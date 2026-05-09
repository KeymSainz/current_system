<?php
/**
 * Fix the supplier_products status ENUM — adds 'sent_to_owner' back
 * and fixes any products that got stored with empty status
 */
require_once __DIR__ . '/db.php';
header('Content-Type: text/plain; charset=utf-8');

echo "=== FIXING STATUS ENUM ===\n\n";

try {
    // Step 1: Show current ENUM definition
    $stmt = $pdo->query("SHOW COLUMNS FROM supplier_products LIKE 'status'");
    $col = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Current ENUM: {$col['Type']}\n\n";

    // Step 2: Fix the ENUM to include all needed values
    echo "Updating ENUM to include 'sent_to_owner'...\n";
    $pdo->exec("
        ALTER TABLE supplier_products
        MODIFY COLUMN status
            ENUM('draft','verified','sent_to_owner','owner_received','rejected','pending','sent_to_supervisor','sent_to_sales_person')
            NOT NULL DEFAULT 'draft'
    ");
    echo "✅ ENUM updated.\n\n";

    // Step 3: Show new ENUM
    $stmt = $pdo->query("SHOW COLUMNS FROM supplier_products LIKE 'status'");
    $col = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "New ENUM: {$col['Type']}\n\n";

    // Step 4: Fix products that are in submissions but have empty/wrong status
    echo "Fixing products in submissions with empty/wrong status...\n";
    $stmt = $pdo->query("
        SELECT DISTINCT sp.id, sp.status
        FROM supplier_products sp
        JOIN submission_items si ON si.product_id = sp.id
        JOIN product_submissions ps ON ps.id = si.submission_id
        WHERE sp.status != 'sent_to_owner'
          AND sp.status != 'owner_received'
          AND sp.status != 'rejected'
    ");
    $toFix = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($toFix) . " products to fix.\n";

    $fixed = 0;
    foreach ($toFix as $p) {
        $upd = $pdo->prepare("UPDATE supplier_products SET status='sent_to_owner', sent_at=NOW() WHERE id=?");
        $upd->execute([$p['id']]);
        if ($upd->rowCount() > 0) {
            echo "  ✓ Fixed product #{$p['id']} (was: '{$p['status']}')\n";
            $fixed++;
        }
    }

    echo "\nFixed $fixed products.\n\n";

    // Step 5: Verify
    echo "=== VERIFICATION ===\n";
    $owners = $pdo->query("SELECT id, email FROM users WHERE role='owner' AND is_active=1")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($owners as $owner) {
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT sp.id) as cnt
            FROM supplier_products sp
            JOIN submission_items si ON si.product_id = sp.id
            JOIN product_submissions ps ON ps.id = si.submission_id AND ps.owner_id = ?
            WHERE sp.status = 'sent_to_owner'
        ");
        $stmt->execute([$owner['id']]);
        $cnt = $stmt->fetchColumn();
        echo "Owner {$owner['email']} (ID:{$owner['id']}): $cnt product(s) pending\n";
    }

    echo "\n✅ DONE! Refresh the owner dashboard now.\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
