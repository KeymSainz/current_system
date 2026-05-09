<?php
/**
 * Auto-Fix Product Holders
 * Sets current_holder_id for products that don't have it
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
require_once __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Fix Product Holders</h1>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h1, h2 { color: #333; }
pre { background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 8px; overflow: auto; }
.success { color: #28a745; font-weight: bold; }
.error { color: #dc3545; font-weight: bold; }
.info { color: #17a2b8; font-weight: bold; }
</style>";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<p class='error'>❌ This script must be run via POST request</p>";
    echo "<p><a href='debug_product_transfer_error.php'>← Back to Debug</a></p>";
    exit;
}

// Check if user is logged in
if (empty($_SESSION['user_id'])) {
    echo "<p class='error'>❌ Not logged in!</p>";
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? '';

echo "<h2>Running Fix...</h2>";

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Fix 1: Set current_holder_id from product_submissions
    echo "<h3>Fix 1: Setting current_holder_id from product_submissions</h3>";
    
    $stmt = $pdo->prepare("
        UPDATE supplier_products sp
        JOIN submission_items si ON si.product_id = sp.id
        JOIN product_submissions ps ON ps.id = si.submission_id
        SET 
          sp.current_holder_id = ps.owner_id,
          sp.holder_type = 'owner'
        WHERE sp.status IN ('owner_received', 'sent_to_owner', 'verified')
          AND ps.owner_id IS NOT NULL
          AND sp.current_holder_id IS NULL
    ");
    $stmt->execute();
    $updated1 = $stmt->rowCount();
    
    echo "<p class='success'>✅ Updated $updated1 product(s) from product_submissions</p>";
    
    // Fix 2: For products without submission link, set to current user if they're owner
    if ($userRole === 'owner') {
        echo "<h3>Fix 2: Setting remaining products to current owner</h3>";
        
        $stmt = $pdo->prepare("
            UPDATE supplier_products sp
            SET 
              sp.current_holder_id = ?,
              sp.holder_type = 'owner'
            WHERE sp.status IN ('owner_received', 'sent_to_owner', 'verified', 'draft')
              AND sp.current_holder_id IS NULL
        ");
        $stmt->execute([$userId]);
        $updated2 = $stmt->rowCount();
        
        echo "<p class='success'>✅ Updated $updated2 product(s) to current owner (you)</p>";
    } else {
        $updated2 = 0;
        echo "<p class='info'>ℹ️ Skipped Fix 2 (only runs for owner role)</p>";
    }
    
    // Commit transaction
    $pdo->commit();
    
    $totalUpdated = $updated1 + $updated2;
    
    echo "<hr>";
    echo "<h2>Summary</h2>";
    echo "<p class='success'>✅ Successfully updated <strong>$totalUpdated</strong> product(s)</p>";
    
    // Show updated products
    if ($totalUpdated > 0) {
        echo "<h3>Updated Products:</h3>";
        $stmt = $pdo->prepare("
            SELECT 
                sp.id,
                sp.category,
                sp.brand,
                sp.item_description,
                sp.qty,
                sp.current_holder_id,
                sp.holder_type,
                u.email as holder_email
            FROM supplier_products sp
            LEFT JOIN users u ON u.id = sp.current_holder_id
            WHERE sp.current_holder_id IS NOT NULL
            ORDER BY sp.id DESC
            LIMIT 20
        ");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table style='border-collapse:collapse;width:100%;background:white;'>";
        echo "<tr style='background:#e6a800;color:white;'>";
        echo "<th style='border:1px solid #ddd;padding:8px;'>ID</th>";
        echo "<th style='border:1px solid #ddd;padding:8px;'>Description</th>";
        echo "<th style='border:1px solid #ddd;padding:8px;'>Qty</th>";
        echo "<th style='border:1px solid #ddd;padding:8px;'>Holder ID</th>";
        echo "<th style='border:1px solid #ddd;padding:8px;'>Holder Type</th>";
        echo "<th style='border:1px solid #ddd;padding:8px;'>Holder Email</th>";
        echo "</tr>";
        
        foreach ($products as $p) {
            echo "<tr>";
            echo "<td style='border:1px solid #ddd;padding:8px;'>{$p['id']}</td>";
            echo "<td style='border:1px solid #ddd;padding:8px;'>{$p['category']} - {$p['brand']}</td>";
            echo "<td style='border:1px solid #ddd;padding:8px;'>{$p['qty']}</td>";
            echo "<td style='border:1px solid #ddd;padding:8px;'>{$p['current_holder_id']}</td>";
            echo "<td style='border:1px solid #ddd;padding:8px;'>{$p['holder_type']}</td>";
            echo "<td style='border:1px solid #ddd;padding:8px;'>{$p['holder_email']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go back to <strong>Manage Products</strong> page</li>";
    echo "<li>Hard refresh (Ctrl+F5)</li>";
    echo "<li>Select products and click <strong>Send to Supervisor</strong></li>";
    echo "<li>It should work now! ✅</li>";
    echo "</ol>";
    
    echo "<p><a href='../views/user/owner/products.html' style='display:inline-block;padding:10px 20px;background:#e6a800;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>→ Go to Manage Products</a></p>";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr style='margin:30px 0;'>";
echo "<p style='text-align:center;color:#666;'>Fix completed at " . date('Y-m-d H:i:s') . "</p>";
echo "<p style='text-align:center;'><a href='debug_product_transfer_error.php'>← Back to Debug</a></p>";
