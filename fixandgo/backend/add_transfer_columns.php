<?php
/**
 * Add Transfer Columns to supplier_products
 * Run this once to add current_holder_id and holder_type columns
 */

require_once __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Add Transfer Columns</h1>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h1, h2 { color: #333; }
.success { color: #28a745; font-weight: bold; }
.error { color: #dc3545; font-weight: bold; }
.info { color: #17a2b8; font-weight: bold; }
pre { background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 8px; overflow: auto; }
</style>";

try {
    // Check current columns
    echo "<h2>Step 1: Check Current Columns</h2>";
    $stmt = $pdo->query("DESCRIBE supplier_products");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $hasCurrentHolder = in_array('current_holder_id', $columns);
    $hasHolderType = in_array('holder_type', $columns);
    
    echo "<p>Current columns: " . implode(', ', $columns) . "</p>";
    echo "<p>Has current_holder_id: " . ($hasCurrentHolder ? '<span class="success">✅ YES</span>' : '<span class="error">❌ NO</span>') . "</p>";
    echo "<p>Has holder_type: " . ($hasHolderType ? '<span class="success">✅ YES</span>' : '<span class="error">❌ NO</span>') . "</p>";
    
    // Add columns if missing
    if (!$hasCurrentHolder || !$hasHolderType) {
        echo "<h2>Step 2: Adding Missing Columns</h2>";
        
        if (!$hasCurrentHolder) {
            echo "<p class='info'>Adding current_holder_id column...</p>";
            $pdo->exec("
                ALTER TABLE supplier_products 
                ADD COLUMN current_holder_id INT UNSIGNED NULL AFTER status
            ");
            echo "<p class='success'>✅ Added current_holder_id</p>";
        }
        
        if (!$hasHolderType) {
            echo "<p class='info'>Adding holder_type column...</p>";
            $pdo->exec("
                ALTER TABLE supplier_products 
                ADD COLUMN holder_type ENUM('owner', 'supervisor', 'sales_person') NULL AFTER current_holder_id
            ");
            echo "<p class='success'>✅ Added holder_type</p>";
        }
        
        // Add foreign key
        echo "<p class='info'>Adding foreign key constraint...</p>";
        try {
            $pdo->exec("
                ALTER TABLE supplier_products
                ADD CONSTRAINT fk_sp_current_holder
                  FOREIGN KEY (current_holder_id) REFERENCES users(id) ON DELETE SET NULL
            ");
            echo "<p class='success'>✅ Added foreign key</p>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate key') !== false) {
                echo "<p class='info'>ℹ️ Foreign key already exists</p>";
            } else {
                throw $e;
            }
        }
        
        // Add index
        echo "<p class='info'>Adding index...</p>";
        try {
            $pdo->exec("
                ALTER TABLE supplier_products
                ADD INDEX idx_current_holder (current_holder_id)
            ");
            echo "<p class='success'>✅ Added index</p>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate key') !== false) {
                echo "<p class='info'>ℹ️ Index already exists</p>";
            } else {
                throw $e;
            }
        }
        
        echo "<h2>Step 3: Set current_holder_id for Existing Products</h2>";
        
        // Set current_holder_id from product_submissions
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
        
        // Set remaining products to supplier_id
        $stmt = $pdo->prepare("
            UPDATE supplier_products sp
            SET 
              sp.current_holder_id = sp.supplier_id,
              sp.holder_type = 'owner'
            WHERE sp.status IN ('owner_received', 'sent_to_owner', 'verified', 'draft')
              AND sp.current_holder_id IS NULL
              AND sp.supplier_id IS NOT NULL
        ");
        $stmt->execute();
        $updated2 = $stmt->rowCount();
        echo "<p class='success'>✅ Updated $updated2 product(s) from supplier_id</p>";
        
        $totalUpdated = $updated1 + $updated2;
        echo "<p class='success'><strong>Total: $totalUpdated product(s) updated</strong></p>";
        
    } else {
        echo "<h2>Step 2: Columns Already Exist</h2>";
        echo "<p class='success'>✅ All required columns are present!</p>";
        
        // Check if products have holder set
        $stmt = $pdo->query("
            SELECT COUNT(*) as count
            FROM supplier_products
            WHERE current_holder_id IS NULL
              AND status IN ('owner_received', 'sent_to_owner', 'verified')
        ");
        $nullCount = $stmt->fetch()['count'];
        
        if ($nullCount > 0) {
            echo "<h2>Step 3: Fix Products Without Holder</h2>";
            echo "<p class='error'>⚠️ Found $nullCount product(s) without current_holder_id</p>";
            
            // Set current_holder_id from product_submissions
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
            
            // Set remaining products to supplier_id
            $stmt = $pdo->prepare("
                UPDATE supplier_products sp
                SET 
                  sp.current_holder_id = sp.supplier_id,
                  sp.holder_type = 'owner'
                WHERE sp.status IN ('owner_received', 'sent_to_owner', 'verified', 'draft')
                  AND sp.current_holder_id IS NULL
                  AND sp.supplier_id IS NOT NULL
            ");
            $stmt->execute();
            $updated2 = $stmt->rowCount();
            echo "<p class='success'>✅ Updated $updated2 product(s) from supplier_id</p>";
            
            $totalUpdated = $updated1 + $updated2;
            echo "<p class='success'><strong>Total: $totalUpdated product(s) updated</strong></p>";
        } else {
            echo "<p class='success'>✅ All products have current_holder_id set!</p>";
        }
    }
    
    // Verify final state
    echo "<h2>Step 4: Verification</h2>";
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total,
            COUNT(current_holder_id) as with_holder,
            COUNT(*) - COUNT(current_holder_id) as without_holder
        FROM supplier_products
    ");
    $result = $stmt->fetch();
    
    echo "<p><strong>Total products:</strong> {$result['total']}</p>";
    echo "<p><strong>With holder:</strong> <span class='success'>{$result['with_holder']}</span></p>";
    echo "<p><strong>Without holder:</strong> " . ($result['without_holder'] > 0 ? "<span class='error'>{$result['without_holder']}</span>" : "<span class='success'>0</span>") . "</p>";
    
    if ($result['without_holder'] == 0) {
        echo "<hr>";
        echo "<h2>✅ Success!</h2>";
        echo "<p class='success'>All columns added and products updated successfully!</p>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ol>";
        echo "<li>Go to <strong>Manage Products</strong> page</li>";
        echo "<li>Hard refresh (Ctrl+F5)</li>";
        echo "<li>Select products and click <strong>Send to Supervisor</strong></li>";
        echo "<li>It should work now! ✅</li>";
        echo "</ol>";
        echo "<p><a href='../views/user/owner/products.html' style='display:inline-block;padding:10px 20px;background:#e6a800;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>→ Go to Manage Products</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr style='margin:30px 0;'>";
echo "<p style='text-align:center;color:#666;'>Completed at " . date('Y-m-d H:i:s') . "</p>";
