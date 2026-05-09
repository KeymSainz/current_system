<?php
/**
 * Fix&Go — Sales Person Orders API
 *
 * GET ?action=list&status=all  → list customer orders/bookings
 * GET ?action=stats            → order statistics
 * GET ?action=detail&id=X      → single order detail
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (
    empty($_SESSION['user_id']) ||
    empty($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'sales_person'
) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Sales person access required.']);
    exit;
}

$salesPersonId = (int) $_SESSION['user_id'];
$pdo    = require __DIR__ . '/db.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    // ── List orders ───────────────────────────────────────────
    if ($action === 'list') {
        $status = $_GET['status'] ?? 'all';

        // Try to pull from supplier_products purchase records (owner_purchases table)
        // Fall back to a simple query if the table doesn't exist yet.
        try {
            // Check if owner_purchases table exists
            $check = $pdo->query("SHOW TABLES LIKE 'owner_purchases'");
            if ($check->rowCount() > 0) {
                $sql = "SELECT
                            op.id,
                            op.product_id,
                            sp.item_description AS product_name,
                            sp.category,
                            op.quantity,
                            op.total_amount,
                            op.status,
                            op.created_at,
                            u.first_name,
                            u.last_name,
                            u.email AS customer_email
                        FROM owner_purchases op
                        LEFT JOIN supplier_products sp ON op.product_id = sp.id
                        LEFT JOIN users u ON op.buyer_id = u.id
                        WHERE 1=1";

                $params = [];
                if ($status !== 'all') {
                    $sql    .= " AND op.status = ?";
                    $params[] = $status;
                }
                $sql .= " ORDER BY op.created_at DESC LIMIT 100";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // No purchases table yet — return empty list
                $orders = [];
            }
        } catch (PDOException $e) {
            error_log('[sales_orders] ' . $e->getMessage());
            $orders = [];
        }

        echo json_encode(['success' => true, 'orders' => $orders]);
        exit;
    }

    // ── Stats ─────────────────────────────────────────────────
    if ($action === 'stats') {
        $stats = [
            'orders_today'   => 0,
            'pending'        => 0,
            'completed'      => 0,
            'total_revenue'  => 0,
        ];

        try {
            $check = $pdo->query("SHOW TABLES LIKE 'owner_purchases'");
            if ($check->rowCount() > 0) {
                $stmt = $pdo->query(
                    "SELECT
                        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS orders_today,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                        SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) AS total_revenue
                     FROM owner_purchases"
                );
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $stats = $row;
                }
            }
        } catch (PDOException $e) {
            error_log('[sales_orders stats] ' . $e->getMessage());
        }

        echo json_encode(['success' => true, 'stats' => $stats]);
        exit;
    }

    // ── Single order detail ───────────────────────────────────
    if ($action === 'detail') {
        $orderId = intval($_GET['id'] ?? 0);
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID required.']);
            exit;
        }

        try {
            $check = $pdo->query("SHOW TABLES LIKE 'owner_purchases'");
            if ($check->rowCount() > 0) {
                $stmt = $pdo->prepare(
                    "SELECT
                        op.*,
                        sp.item_description AS product_name,
                        sp.category,
                        sp.image_path,
                        u.first_name,
                        u.last_name,
                        u.email AS customer_email,
                        u.phone AS customer_phone
                     FROM owner_purchases op
                     LEFT JOIN supplier_products sp ON op.product_id = sp.id
                     LEFT JOIN users u ON op.buyer_id = u.id
                     WHERE op.id = ?"
                );
                $stmt->execute([$orderId]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$order) {
                    echo json_encode(['success' => false, 'message' => 'Order not found.']);
                    exit;
                }

                echo json_encode(['success' => true, 'order' => $order]);
                exit;
            }
        } catch (PDOException $e) {
            error_log('[sales_orders detail] ' . $e->getMessage());
        }

        echo json_encode(['success' => false, 'message' => 'Orders table not available.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
