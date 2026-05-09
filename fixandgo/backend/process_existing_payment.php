<?php
/**
 * Fix&Go — Process Existing Payment
 * This script processes an existing paid payment and adds products to owner inventory
 * Run this once to fix the payment that was already completed
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();

// Only allow owners to run this
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'owner') {
    die('Unauthorized. Please login as owner.');
}

$pdo = require __DIR__ . '/db.php';
$ownerId = (int) $_SESSION['user_id'];

/**
 * Add purchased products to owner's product inventory
 * Products are added to supplier_products table with owner as the supplier_id
 */
function addProductsToOwnerInventory($pdo, $paymentId, $ownerId) {
    // Get payment details
    $stmt = $pdo->prepare(
        'SELECT product_ids FROM owner_payments WHERE id = ? LIMIT 1'
    );
    $stmt->execute([$paymentId]);
    $payment = $stmt->fetch();
    
    if (!$payment || !$payment['product_ids']) {
        return ['success' => false, 'message' => 'No product IDs found in payment'];
    }
    
    $productIds = json_decode($payment['product_ids'], true);
    if (empty($productIds)) {
        return ['success' => false, 'message' => 'Product IDs array is empty'];
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
    
    if (empty($products)) {
        return ['success' => false, 'message' => 'No products found with those IDs'];
    }
    
    // Check if already processed by looking for products with matching notes
    $checkStmt = $pdo->prepare(
        "SELECT COUNT(*) FROM supplier_products 
         WHERE supplier_id = ? AND notes LIKE ?"
    );
    $checkStmt->execute([$ownerId, "%Payment ID: $paymentId%"]);
    $existing = $checkStmt->fetchColumn();
    
    if ($existing > 0) {
        return ['success' => false, 'message' => 'Payment already processed (' . $existing . ' products in inventory)'];
    }
    
    // Insert each product into owner's inventory (supplier_products table)
    // The owner becomes the new "supplier" for these products
    $insertStmt = $pdo->prepare(
        'INSERT INTO supplier_products
         (supplier_id, category, brand, item_description, qty, srp,
          image_path, notes, status, verified_at, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, "verified", NOW(), NOW())'
    );
    
    $inserted = 0;
    foreach ($products as $product) {
        $qty = max(1, (int)$product['qty']);
        $srp = (float)$product['srp'];
        
        // Add note about purchase source
        $purchaseNote = "Purchased from supplier (Payment ID: $paymentId)";
        $notes = $product['notes'] ? $product['notes'] . "\n\n" . $purchaseNote : $purchaseNote;
        
        $insertStmt->execute([
            $ownerId,                      // owner becomes the supplier_id
            $product['category'],
            $product['brand'],
            $product['item_description'],
            $qty,
            $srp,
            $product['image_path'],
            $notes,
        ]);
        $inserted++;
    }
    
    // Mark original supplier products as "sold" by updating status
    $updateStmt = $pdo->prepare(
        "UPDATE supplier_products 
         SET status = 'owner_received', 
             notes = CONCAT(COALESCE(notes, ''), '\n\nSold to owner (Payment ID: $paymentId)')
         WHERE id IN ($placeholders)"
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
                $qty = max(1, (int)$product['qty']);
                $unitPrice = (float)$product['srp'];
                $totalPrice = $qty * $unitPrice;
                
                $trackingStmt->execute([
                    $ownerId,
                    $product['supplier_id'],
                    $product['id'],
                    $paymentId,
                    $product['category'],
                    $product['brand'],
                    $product['item_description'],
                    $qty,
                    $unitPrice,
                    $totalPrice,
                    $product['image_path'],
                    $product['notes']
                ]);
            }
        }
    } catch (Exception $e) {
        // Not critical if tracking fails
    }
    
    return ['success' => true, 'message' => "Successfully added $inserted products to your shop inventory"];
}

// Get the most recent paid payment for this owner
$stmt = $pdo->prepare(
    "SELECT id, reference, amount, product_ids, paid_at
     FROM owner_payments
     WHERE owner_id = ? AND status = 'paid'
     ORDER BY paid_at DESC
     LIMIT 1"
);
$stmt->execute([$ownerId]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die('No paid payments found for your account.');
}

echo "<h2>Processing Payment</h2>";
echo "<p><strong>Reference:</strong> " . htmlspecialchars($payment['reference']) . "</p>";
echo "<p><strong>Amount:</strong> ₱" . number_format($payment['amount'], 2) . "</p>";
echo "<p><strong>Paid At:</strong> " . $payment['paid_at'] . "</p>";
echo "<hr>";

// Process the payment
$result = addProductsToOwnerInventory($pdo, $payment['id'], $ownerId);

if ($result['success']) {
    echo "<p style='color: green; font-weight: bold;'>✓ " . $result['message'] . "</p>";
    echo "<p><a href='../views/user/owner/products.html'>View My Products</a></p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ " . $result['message'] . "</p>";
}

echo "<hr>";
echo "<p><a href='../dashboard.html'>Back to Dashboard</a></p>";
