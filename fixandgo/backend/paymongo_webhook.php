<?php
/**
 * Fix&Go — PayMongo Webhook Handler
 * Register this URL in your PayMongo dashboard:
 *   https://yourdomain.com/fixandgo/backend/paymongo_webhook.php
 *
 * Events handled:
 *   payment.paid    → mark payment as paid
 *   payment.failed  → mark payment as failed
 */

require_once __DIR__ . '/helpers.php';
header('Content-Type: application/json');

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

// Read raw body
$rawBody   = file_get_contents('php://input');
$signature = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';

// Verify webhook signature if secret is configured
if (!empty($config['paymongo_webhook_secret']) && $signature) {
    $parts     = [];
    foreach (explode(',', $signature) as $part) {
        [$k, $v] = explode('=', $part, 2);
        $parts[$k] = $v;
    }
    $timestamp = $parts['t'] ?? '';
    $hmac      = $parts['te'] ?? '';
    $expected  = hash_hmac('sha256', $timestamp . '.' . $rawBody, $config['paymongo_webhook_secret']);

    if (!hash_equals($expected, $hmac)) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid signature.']);
        exit;
    }
}

$event = json_decode($rawBody, true);
$type  = $event['data']['attributes']['type'] ?? '';
$data  = $event['data']['attributes']['data'] ?? [];

switch ($type) {
    case 'payment.paid':
        $pmId = $data['id'] ?? '';
        // Find payment by paymongo checkout session — match via metadata or reference
        $ref = $data['attributes']['external_reference_number'] ?? '';
        if ($ref) {
            $stmt = $pdo->prepare(
                "SELECT id, owner_id FROM owner_payments WHERE reference=? AND status='pending' LIMIT 1"
            );
            $stmt->execute([$ref]);
            $payment = $stmt->fetch();
            
            if ($payment) {
                $pdo->prepare(
                    "UPDATE owner_payments SET status='paid', paid_at=NOW() WHERE reference=? AND status='pending'"
                )->execute([$ref]);
                
                // Add purchased products to owner inventory
                addProductsToOwnerInventory($pdo, $payment['id'], $payment['owner_id']);
            }
        }
        break;

    case 'payment.failed':
        $ref = $data['attributes']['external_reference_number'] ?? '';
        if ($ref) {
            $pdo->prepare(
                "UPDATE owner_payments SET status='failed' WHERE reference=? AND status='pending'"
            )->execute([$ref]);
        }
        break;
}

http_response_code(200);
echo json_encode(['received' => true]);
