<?php
/**
 * Fix Product Status - Update products that are in submissions but have wrong status
 * Access: http://localhost/current_system/fixandgo/backend/fix_product_status.php
 */

require_once __DIR__ . '/helpers.php';
$pdo = require __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fix Product Status</title>
    <style>
        body { font-family: monospace; background: #0f1117; color: #e2e8f0; padding: 20px; max-width: 1200px; margin: 0 auto; }
        .section { background: #1a1d27; border: 1px solid #2a2d3a; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        h2 { color: #e6a800; margin-top: 0; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        .warning { color: #fbbf24; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #2a2d3a; }
        th { color: #e6a800; }
        .btn { background: #e6a800; color: #000; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; margin: 5px; }
    </style>
</head>
<body>
    <h1>🔧 Fix Product Status</h1>
    
    <div class="section">
        <h2>Problem Detection</h2>
        <?php
        // Find products that are in submissions but don't have "sent_to_owner" status
        $stmt = $pdo->query("
            SELECT sp.id, sp.category, sp.item_description, sp.status, sp.supplier_id,
                   si.submission_id, ps.owner_id, ps.status as submission_status
            FROM supplier_products sp
            JOIN submission_items si ON si.product_id = sp.id
            JOIN product_submissions ps ON ps.id = si.submission_id
            WHERE sp.status != 'sent_to_owner' AND ps.status = 'pending'
        ");
        $brokenProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($brokenProducts) > 0) {
            echo '<p class="error">❌ Found ' . count($brokenProducts) . ' product(s) with incorrect status!</p>';
            echo '<p>These products are in submissions but their status is not "sent_to_owner":</p>';
            
            echo '<table>';
            echo '<tr><th>Product ID</th><th>Current Status</th><th>Category</th><th>Description</th><th>Submission ID</th><th>Owner ID</th></tr>';
            foreach ($brokenProducts as $p) {
                echo '<tr>';
                echo '<td>#' . $p['id'] . '</td>';
                echo '<td><strong>' . ($p['status'] ?: '<span class="warning">(empty)</span>') . '</strong></td>';
                echo '<td>' . htmlspecialchars($p['category']) . '</td>';
                echo '<td>' . htmlspecialchars(substr($p['item_description'], 0, 40)) . '...</td>';
                echo '<td>#' . $p['submission_id'] . '</td>';
                echo '<td>#' . $p['owner_id'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="success">✅ All products have correct status!</p>';
        }
        ?>
    </div>
    
    <?php if (count($brokenProducts) > 0): ?>
        <div class="section">
            <h2>Fix Action</h2>
            
            <?php if (isset($_POST['fix_now'])): ?>
                <?php
                try {
                    $fixed = 0;
                    foreach ($brokenProducts as $p) {
                        $stmt = $pdo->prepare("
                            UPDATE supplier_products 
                            SET status = 'sent_to_owner', sent_at = NOW() 
                            WHERE id = ?
                        ");
                        $stmt->execute([$p['id']]);
                        $fixed += $stmt->rowCount();
                    }
                    
                    echo '<p class="success">✅ Successfully fixed ' . $fixed . ' product(s)!</p>';
                    echo '<p>The products should now appear in the owner dashboard.</p>';
                    echo '<p><a href="?" class="btn">Refresh Page</a></p>';
                } catch (Exception $e) {
                    echo '<p class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
                }
                ?>
            <?php else: ?>
                <p>Click the button below to fix all products with incorrect status:</p>
                <form method="POST">
                    <button type="submit" name="fix_now" class="btn">🔧 Fix All Products Now</button>
                </form>
                <p style="color: var(--fg-muted); font-size: 0.9em;">
                    This will update <?php echo count($brokenProducts); ?> product(s) to have status "sent_to_owner"
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="section">
        <h2>All Products in Submissions</h2>
        <?php
        $stmt = $pdo->query("
            SELECT sp.id, sp.category, sp.item_description, sp.status, sp.sent_at,
                   si.submission_id, ps.owner_id, ps.status as submission_status,
                   u.email as supplier_email
            FROM supplier_products sp
            JOIN submission_items si ON si.product_id = sp.id
            JOIN product_submissions ps ON ps.id = si.submission_id
            JOIN users u ON sp.supplier_id = u.id
            ORDER BY si.submission_id DESC, sp.id
        ");
        $allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($allProducts) > 0) {
            echo '<p>Total: ' . count($allProducts) . ' product(s)</p>';
            echo '<table>';
            echo '<tr><th>Product ID</th><th>Status</th><th>Category</th><th>Description</th><th>Sent At</th><th>Submission</th><th>Supplier</th></tr>';
            foreach ($allProducts as $p) {
                $statusClass = $p['status'] === 'sent_to_owner' ? 'success' : 'error';
                echo '<tr>';
                echo '<td>#' . $p['id'] . '</td>';
                echo '<td class="' . $statusClass . '"><strong>' . ($p['status'] ?: '<span class="warning">(empty)</span>') . '</strong></td>';
                echo '<td>' . htmlspecialchars($p['category']) . '</td>';
                echo '<td>' . htmlspecialchars(substr($p['item_description'], 0, 30)) . '...</td>';
                echo '<td>' . ($p['sent_at'] ? htmlspecialchars($p['sent_at']) : '—') . '</td>';
                echo '<td>#' . $p['submission_id'] . ' (' . htmlspecialchars($p['submission_status']) . ')</td>';
                echo '<td>' . htmlspecialchars($p['supplier_email']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="warning">No products in submissions yet.</p>';
        }
        ?>
    </div>
    
</body>
</html>
