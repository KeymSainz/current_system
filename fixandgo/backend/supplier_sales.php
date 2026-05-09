<?php
/**
 * Fix&Go — Supplier Sales API
 * View products purchased by owners from this supplier
 *
 * GET  ?action=purchases  → all products purchased by owners from this supplier
 * GET  ?action=stats      → sales statistics
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'supplier') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$supplierId = (int) $_SESSION['user_id'];
$pdo        = require __DIR__ . '/db.php';
$method     = $_SERVER['REQUEST_METHOD'];

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
        'purchases' => []
    ]);
    exit;
}

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'purchases';

    if ($action === 'purchases') {
        try {
            // Get all products purchased by owners from this supplier
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
                    CONCAT(u.first_name, ' ', u.last_name) AS owner_name,
                    u.email AS owner_email,
                    op.reference AS payment_reference,
                    op.amount AS payment_amount,
                    op.paid_at
                 FROM owner_inventory oi
                 JOIN users u ON u.id = oi.owner_id
                 JOIN owner_payments op ON op.id = oi.payment_id
                 WHERE oi.supplier_id = ?
                 ORDER BY oi.purchased_at DESC"
            );
            $stmt->execute([$supplierId]);
            $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success'   => true,
                'purchases' => $purchases,
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'purchases' => []
            ]);
        }
        exit;
    }

    if ($action === 'stats') {
        try {
            // Get sales statistics
            $stmt = $pdo->prepare(
                "SELECT 
                    COUNT(DISTINCT oi.id) AS total_sales,
                    COUNT(DISTINCT oi.owner_id) AS unique_owners,
                    COALESCE(SUM(oi.total_price), 0) AS total_revenue,
                    COALESCE(SUM(oi.qty), 0) AS total_items_sold
                 FROM owner_inventory oi
                 WHERE oi.supplier_id = ?"
            );
            $stmt->execute([$supplierId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get monthly revenue
            $monthlyStmt = $pdo->prepare(
                "SELECT 
                    DATE_FORMAT(oi.purchased_at, '%Y-%m') AS month,
                    SUM(oi.total_price) AS revenue,
                    COUNT(DISTINCT oi.id) AS sales_count
                 FROM owner_inventory oi
                 WHERE oi.supplier_id = ?
                 GROUP BY month
                 ORDER BY month DESC
                 LIMIT 12"
            );
            $monthlyStmt->execute([$supplierId]);
            $monthlyRevenue = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get top selling products
            $topProductsStmt = $pdo->prepare(
                "SELECT 
                    oi.item_description,
                    oi.brand,
                    oi.category,
                    COUNT(*) AS times_sold,
                    SUM(oi.qty) AS total_qty,
                    SUM(oi.total_price) AS total_revenue
                 FROM owner_inventory oi
                 WHERE oi.supplier_id = ?
                 GROUP BY oi.item_description, oi.brand, oi.category
                 ORDER BY times_sold DESC, total_revenue DESC
                 LIMIT 10"
            );
            $topProductsStmt->execute([$supplierId]);
            $topProducts = $topProductsStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success'         => true,
                'stats'           => $stats,
                'monthly_revenue' => $monthlyRevenue,
                'top_products'    => $topProducts,
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'stats' => [
                    'total_sales' => 0,
                    'unique_owners' => 0,
                    'total_revenue' => 0,
                    'total_items_sold' => 0
                ],
                'monthly_revenue' => [],
                'top_products' => []
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
