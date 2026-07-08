<?php
/**
 * Fix&Go — PayMongo Return Handler
 * PayMongo redirects here after payment success or cancel.
 * Updates the payment record and redirects to the owner dashboard.
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();

$config = require __DIR__ . '/config.php';
$pdo    = require __DIR__ . '/db.php';

/**
 * Add purchased products to owner's product inventory
 * Products are added to supplier_products table with owner as the supplier_id
 */
function addProductsToOwnerInventory($pdo, $paymentId, $ownerId) {
    // Get payment details including custom quantities
    $stmt = $pdo->prepare(
        'SELECT product_ids, purchase_quantities FROM owner_payments WHERE id = ? LIMIT 1'
    );
    $stmt->execute([$paymentId]);
    $payment = $stmt->fetch();
    
    if (!$payment || !$payment['product_ids']) {
        return;
    }
    
    $productIds = json_decode($payment['product_ids'], true);
    $purchaseQuantities = json_decode($payment['purchase_quantities'], true) ?? [];
    
    if (empty($productIds)) {
        return;
    }
    
    // Get product details from supplier's products
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare(
        "SELECT sp.id, sp.supplier_id, sp.category, sp.brand, sp.item_description,
                sp.qty, sp.srp, sp.image_path, sp.notes
         FROM supplier_products sp
         WHERE sp.id IN ($placeholders)"
    );
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Insert each product into owner's inventory (supplier_products table)
    // The owner becomes the new "supplier" for these products
    $insertStmt = $pdo->prepare(
        'INSERT INTO supplier_products
         (supplier_id, category, brand, item_description, qty, srp,
          image_path, notes, status, current_holder_id, holder_type, verified_at, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, "verified", ?, "owner", NOW(), NOW())'
    );
    
    // Prepare statement to reduce supplier's stock
    $reduceStockStmt = $pdo->prepare(
        'UPDATE supplier_products SET qty = GREATEST(0, qty - ?) WHERE id = ?'
    );
    
    foreach ($products as $product) {
        $productId = (int)$product['id'];
        
        // Use custom quantity if available, otherwise use full stock
        $purchasedQty = isset($purchaseQuantities[$productId]) 
            ? max(1, (int)$purchaseQuantities[$productId])
            : max(1, (int)$product['qty']);
        
        $srp = (float)$product['srp'];
        
        // Add note about purchase source
        $purchaseNote = "Purchased from supplier (Payment ID: $paymentId, Qty: $purchasedQty)";
        $notes = $product['notes'] ? $product['notes'] . "\n\n" . $purchaseNote : $purchaseNote;
        
        $insertStmt->execute([
            $ownerId,                      // owner becomes the supplier_id
            $product['category'],
            $product['brand'],
            $product['item_description'],
            $purchasedQty,                 // use purchased quantity
            $srp,
            $product['image_path'],
            $notes,
            $ownerId,                      // current_holder_id = owner
        ]);
        
        // Reduce supplier's stock by purchased quantity
        $reduceStockStmt->execute([$purchasedQty, $productId]);
    }
    
    // Mark original supplier products as "sold" if stock is now 0
    $updateStmt = $pdo->prepare(
        "UPDATE supplier_products 
         SET status = 'owner_received', 
             notes = CONCAT(COALESCE(notes, ''), '\n\nSold to owner (Payment ID: $paymentId)')
         WHERE id IN ($placeholders) AND qty = 0"
    );
    $updateStmt->execute($productIds);
    
    // Also keep a record in owner_inventory for purchase tracking (if table exists)
    try {
        $checkTable = $pdo->query("SHOW TABLES LIKE 'owner_inventory'");
        if ($checkTable->rowCount() > 0) {
            $trackingStmt = $pdo->prepare(
                'INSERT INTO owner_inventory
                 (owner_id, supplier_id, supplier_product_id, payment_id,
                  category, brand, item_description, qty, unit_price, total_price,
                  image_path, notes, purchased_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
            );
            
            foreach ($products as $product) {
                $productId = (int)$product['id'];
                
                // Use custom quantity if available
                $purchasedQty = isset($purchaseQuantities[$productId]) 
                    ? max(1, (int)$purchaseQuantities[$productId])
                    : max(1, (int)$product['qty']);
                
                $unitPrice = (float)$product['srp'];
                $totalPrice = $purchasedQty * $unitPrice;
                
                $trackingStmt->execute([
                    $ownerId,
                    $product['supplier_id'],
                    $product['id'],
                    $paymentId,
                    $product['category'],
                    $product['brand'],
                    $product['item_description'],
                    $purchasedQty,
                    $unitPrice,
                    $totalPrice,
                    $product['image_path'],
                    $product['notes']
                ]);
            }
        }
    } catch (Exception $e) {
        // Silently fail if owner_inventory doesn't exist - not critical
        error_log('Could not track purchase in owner_inventory: ' . $e->getMessage());
    }
}

$status = $_GET['status'] ?? 'cancel';
$ref    = trim($_GET['ref'] ?? '');

$appUrl    = rtrim($config['app_url'], '/');
$dashUrl   = $appUrl . '/dashboard.php';
$successUrl = $appUrl . '/payment-success.php';

if ($ref) {
    if ($status === 'success') {
        // Verify with PayMongo before marking paid
        $stmt = $pdo->prepare('SELECT id, paymongo_id, owner_id FROM owner_payments WHERE reference = ? LIMIT 1');
        $stmt->execute([$ref]);
        $payment = $stmt->fetch();

        if ($payment) {
            $auth = base64_encode($config['paymongo_secret_key'] . ':');
            $ch   = curl_init('https://api.paymongo.com/v1/checkout_sessions/' . $payment['paymongo_id']);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    'Accept: application/json',
                    'Authorization: Basic ' . $auth,
                ],
            ]);
            $res     = curl_exec($ch);
            $decoded = json_decode($res, true);
            curl_close($ch);

            $pmStatus = $decoded['data']['attributes']['payment_intent']['attributes']['status'] ?? '';

            if ($pmStatus === 'succeeded') {
                $pdo->prepare(
                    "UPDATE owner_payments SET status='paid', paid_at=NOW() WHERE reference=?"
                )->execute([$ref]);
                
                // Add purchased products to owner inventory
                try {
                    addProductsToOwnerInventory($pdo, $payment['id'], $payment['owner_id']);
                } catch (Exception $e) {
                    // Log error but don't break the flow
                    error_log('Failed to add products to inventory: ' . $e->getMessage());
                }
            }
        }

        header('Location: ' . $successUrl . '?ref=' . urlencode($ref));
    } else {
        $pdo->prepare(
            "UPDATE owner_payments SET status='cancelled' WHERE reference=? AND status='pending'"
        )->execute([$ref]);
        header('Location: ' . $dashUrl . '?payment=cancelled&ref=' . urlencode($ref));
    }
} else {
    header('Location: ' . $dashUrl);
}
exit;
