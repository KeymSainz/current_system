<?php
/**
 * Fix&Go — Owner Inventory API
 * View products purchased by the owner
 *
 * GET  ?action=inventory  → all products in owner's inventory
 * GET  ?action=stats      → inventory statistics
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$ownerId = (int) $_SESSION['user_id'];
$pdo     = require __DIR__ . '/db.php';
$method  = $_SERVER['REQUEST_METHOD'];

// Check if owner_inventory table exists
try {
    $checkTable = $pdo->query("SHOW TABLES LIKE 'owner_inventory'");
    $tableExists = $checkTable->rowCount() > 0;
} catch (Exception $e) {
    $tableExists = false;
}

if (!$tableExists) {
    echo json_encode([
        'success' => false,
        'message' => 'Inventory table not found. Please run the database migration first.',
        'setup_required' => true,
        'inventory' => []
    ]);
    exit;
}

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'inventory';

    if ($action === 'inventory') {
        try {
            // Get all products in owner's inventory
            $stmt = $pdo->prepare(
                "SELECT 
                    oi.id,
                    oi.category,
                    oi.brand,
                    oi.item_description,
                    oi.qty,
                    oi.unit_price,
                    oi.total_price,
                    oi.image_path,
                    oi.notes,
                    oi.purchased_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS supplier_name,
                    u.email AS supplier_email,
                    op.reference AS payment_reference,
                    op.paid_at
                 FROM owner_inventory oi
                 JOIN users u ON u.id = oi.supplier_id
                 JOIN owner_payments op ON op.id = oi.payment_id
                 WHERE oi.owner_id = ?
                 ORDER BY oi.purchased_at DESC"
            );
            $stmt->execute([$ownerId]);
            $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success'   => true,
                'inventory' => $inventory,
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'inventory' => []
            ]);
        }
        exit;
    }

    if ($action === 'stats') {
        try {
            // Get inventory statistics
            $stmt = $pdo->prepare(
                "SELECT 
                    COUNT(DISTINCT oi.id) AS total_products,
                    COUNT(DISTINCT oi.supplier_id) AS unique_suppliers,
                    COALESCE(SUM(oi.total_price), 0) AS total_spent,
                    COALESCE(SUM(oi.qty), 0) AS total_items
                 FROM owner_inventory oi
                 WHERE oi.owner_id = ?"
            );
            $stmt->execute([$ownerId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get category breakdown
            $categoryStmt = $pdo->prepare(
                "SELECT 
                    oi.category,
                    COUNT(*) AS product_count,
                    SUM(oi.qty) AS total_qty,
                    SUM(oi.total_price) AS total_spent
                 FROM owner_inventory oi
                 WHERE oi.owner_id = ?
                 GROUP BY oi.category
                 ORDER BY total_spent DESC"
            );
            $categoryStmt->execute([$ownerId]);
            $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get recent purchases
            $recentStmt = $pdo->prepare(
                "SELECT 
                    oi.item_description,
                    oi.brand,
                    oi.category,
                    oi.qty,
                    oi.total_price,
                    oi.purchased_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS supplier_name
                 FROM owner_inventory oi
                 JOIN users u ON u.id = oi.supplier_id
                 WHERE oi.owner_id = ?
                 ORDER BY oi.purchased_at DESC
                 LIMIT 10"
            );
            $recentStmt->execute([$ownerId]);
            $recent = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success'    => true,
                'stats'      => $stats,
                'categories' => $categories,
                'recent'     => $recent,
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'stats' => [
                    'total_products' => 0,
                    'unique_suppliers' => 0,
                    'total_spent' => 0,
                    'total_items' => 0
                ],
                'categories' => [],
                'recent' => []
            ]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
