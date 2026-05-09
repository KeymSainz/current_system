<?php
/**
 * Setup Product Transfers
 * Creates tables and sets up everything needed for transfers
 */

require_once __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Setup Product Transfers</h1>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h1, h2 { color: #333; }
.success { color: #28a745; font-weight: bold; }
.error { color: #dc3545; font-weight: bold; }
.info { color: #17a2b8; font-weight: bold; }
pre { background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 8px; overflow: auto; }
</style>";

try {
    echo "<h2>Step 1: Check product_transfers table</h2>";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'product_transfers'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p class='success'>✅ product_transfers table exists</p>";
    } else {
        echo "<p class='error'>❌ product_transfers table does NOT exist</p>";
        echo "<p class='info'>Creating table...</p>";
        
        $pdo->exec("
            CREATE TABLE product_transfers (
              id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
              product_id      INT UNSIGNED  NOT NULL,
              from_user_id    INT UNSIGNED  NOT NULL,
              to_user_id      INT UNSIGNED  NOT NULL,
              transfer_type   ENUM('owner_to_supervisor', 'supervisor_to_sales') NOT NULL,
              quantity        INT UNSIGNED  NOT NULL DEFAULT 1,
              status          ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
              notes           TEXT          NULL,
              transferred_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
              responded_at    DATETIME      NULL,
              
              PRIMARY KEY (id),
              INDEX idx_product       (product_id),
              INDEX idx_from_user     (from_user_id),
              INDEX idx_to_user       (to_user_id),
              INDEX idx_transfer_type (transfer_type),
              INDEX idx_status        (status),
              
              CONSTRAINT fk_transfer_product
                FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
              CONSTRAINT fk_transfer_from
                FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
              CONSTRAINT fk_transfer_to
                FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        echo "<p class='success'>✅ Created product_transfers table</p>";
    }
    
    echo "<h2>Step 2: Check product_transfer_history table</h2>";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'product_transfer_history'");
    $historyExists = $stmt->rowCount() > 0;
    
    if ($historyExists) {
        echo "<p class='success'>✅ product_transfer_history table exists</p>";
    } else {
        echo "<p class='error'>❌ product_transfer_history table does NOT exist</p>";
        echo "<p class='info'>Creating table...</p>";
        
        $pdo->exec("
            CREATE TABLE product_transfer_history (
              id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
              product_id      INT UNSIGNED  NOT NULL,
              from_user_id    INT UNSIGNED  NULL,
              to_user_id      INT UNSIGNED  NOT NULL,
              action          VARCHAR(50)   NOT NULL,
              quantity        INT UNSIGNED  NOT NULL DEFAULT 1,
              notes           TEXT          NULL,
              created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
              
              PRIMARY KEY (id),
              INDEX idx_product (product_id),
              INDEX idx_from_user (from_user_id),
              INDEX idx_to_user (to_user_id),
              
              CONSTRAINT fk_history_product
                FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
              CONSTRAINT fk_history_from
                FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE SET NULL,
              CONSTRAINT fk_history_to
                FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        echo "<p class='success'>✅ Created product_transfer_history table</p>";
    }
    
    echo "<h2>Step 3: Check supplier_products columns</h2>";
    
    $stmt = $pdo->query("DESCRIBE supplier_products");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $hasCurrentHolder = in_array('current_holder_id', $columns);
    $hasHolderType = in_array('holder_type', $columns);
    
    if ($hasCurrentHolder) {
        echo "<p class='success'>✅ current_holder_id column exists</p>";
    } else {
        echo "<p class='error'>❌ current_holder_id column missing</p>";
        echo "<p class='info'>Adding column...</p>";
        $pdo->exec("ALTER TABLE supplier_products ADD COLUMN current_holder_id INT UNSIGNED NULL AFTER status");
        echo "<p class='success'>✅ Added current_holder_id</p>";
    }
    
    if ($hasHolderType) {
        echo "<p class='success'>✅ holder_type column exists</p>";
    } else {
        echo "<p class='error'>❌ holder_type column missing</p>";
        echo "<p class='info'>Adding column...</p>";
        $pdo->exec("ALTER TABLE supplier_products ADD COLUMN holder_type ENUM('owner', 'supervisor', 'sales_person') NULL AFTER current_holder_id");
        echo "<p class='success'>✅ Added holder_type</p>";
    }
    
    echo "<h2>Step 4: Set current_holder_id for products</h2>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM supplier_products WHERE current_holder_id IS NULL");
    $nullCount = $stmt->fetch()['count'];
    
    if ($nullCount > 0) {
        echo "<p class='info'>Found $nullCount products without holder. Setting...</p>";
        
        // Try from product_submissions first
        $stmt = $pdo->exec("
            UPDATE supplier_products sp
            JOIN submission_items si ON si.product_id = sp.id
            JOIN product_submissions ps ON ps.id = si.submission_id
            SET sp.current_holder_id = ps.owner_id, sp.holder_type = 'owner'
            WHERE sp.current_holder_id IS NULL AND ps.owner_id IS NOT NULL
        ");
        echo "<p class='success'>✅ Updated products from submissions</p>";
        
        // Fallback to supplier_id
        $stmt = $pdo->exec("
            UPDATE supplier_products
            SET current_holder_id = supplier_id, holder_type = 'owner'
            WHERE current_holder_id IS NULL AND supplier_id IS NOT NULL
        ");
        echo "<p class='success'>✅ Updated remaining products</p>";
    } else {
        echo "<p class='success'>✅ All products have current_holder_id set</p>";
    }
    
    echo "<hr>";
    echo "<h2>✅ Setup Complete!</h2>";
    echo "<p class='success'>All tables and columns are ready for product transfers!</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <strong>Manage Products</strong> page</li>";
    echo "<li>Hard refresh (Ctrl+F5)</li>";
    echo "<li>Select products and click <strong>Send to Supervisor</strong></li>";
    echo "<li>It should work now! ✅</li>";
    echo "</ol>";
    echo "<p><a href='../views/user/owner/products.html' style='display:inline-block;padding:10px 20px;background:#e6a800;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>→ Go to Manage Products</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr style='margin:30px 0;'>";
echo "<p style='text-align:center;color:#666;'>Setup completed at " . date('Y-m-d H:i:s') . "</p>";
