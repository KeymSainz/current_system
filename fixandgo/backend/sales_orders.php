<?php
/**
 * Fix&Go — Sales Person Orders API
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
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$salesPersonId = (int) $_SESSION['user_id'];
$pdo    = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method = $_SERVER['REQUEST_METHOD'];

/* ── helper: check if a column exists ─────────────────────── */
function colExists(PDO $pdo, string $table, string $col): bool {
    $r = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS
                        WHERE TABLE_SCHEMA = DATABASE()
                          AND TABLE_NAME   = ?
                          AND COLUMN_NAME  = ?");
    $r->execute([$table, $col]);
    return (bool) $r->fetchColumn();
}

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    // ── List orders ───────────────────────────────────────────
    if ($action === 'list') {
        $status = $_GET['status'] ?? 'all';

        // Build optional columns safely
        $cancelCol  = colExists($pdo, 'customer_orders', 'cancel_reason')
                      ? "co.cancel_reason" : "'' AS cancel_reason";
        $addrLine   = colExists($pdo, 'users', 'address_line')
                      ? "u.address_line"   : "'' AS address_line";
        $barangay   = colExists($pdo, 'users', 'barangay')
                      ? "u.barangay"       : "'' AS barangay";
        $city       = colExists($pdo, 'users', 'city')
                      ? "u.city"           : "'' AS city";
        $province   = colExists($pdo, 'users', 'province')
                      ? "u.province"       : "'' AS province";
        $zipCode    = colExists($pdo, 'users', 'zip_code')
                      ? "u.zip_code"       : "'' AS zip_code";
        $addrVerif  = colExists($pdo, 'users', 'address_verified')
                      ? "u.address_verified" : "0 AS address_verified";
        $phone      = colExists($pdo, 'users', 'phone')
                      ? "u.phone"          : "'' AS phone";

        $sql = "SELECT
                    co.id,
                    co.product_id,
                    co.quantity,
                    co.unit_price,
                    co.total_amount,
                    co.status,
                    co.payment_method,
                    COALESCE(co.notes,'') AS notes,
                    $cancelCol,
                    co.created_at,
                    co.updated_at,
                    sp.item_description  AS product_name,
                    sp.category,
                    COALESCE(sp.brand,'')      AS brand,
                    COALESCE(sp.image_path,'') AS image_path,
                    u.id                 AS customer_id,
                    u.first_name,
                    u.last_name,
                    u.email              AS customer_email,
                    $phone               AS customer_phone,
                    $addrLine,
                    $barangay,
                    $city,
                    $province,
                    $zipCode,
                    $addrVerif
                FROM customer_orders co
                JOIN supplier_products sp ON sp.id = co.product_id
                JOIN users u ON u.id = co.customer_id
                WHERE sp.current_holder_id = ?";

        $params = [$salesPersonId];

        if ($status !== 'all') {
            $sql     .= " AND co.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY co.created_at DESC LIMIT 200";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'orders' => $orders]);
        } catch (Exception $e) {
            error_log('[sales_orders list] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Stats ─────────────────────────────────────────────────
    if ($action === 'stats') {
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    COUNT(*)                                                          AS total_orders,
                    SUM(CASE WHEN DATE(co.created_at) = CURDATE() THEN 1 ELSE 0 END) AS orders_today,
                    SUM(CASE WHEN co.status = 'pending'    THEN 1 ELSE 0 END)        AS pending,
                    SUM(CASE WHEN co.status = 'processing' THEN 1 ELSE 0 END)        AS processing,
                    SUM(CASE WHEN co.status = 'completed'  THEN 1 ELSE 0 END)        AS completed,
                    SUM(CASE WHEN co.status = 'cancelled'  THEN 1 ELSE 0 END)        AS cancelled,
                    SUM(CASE WHEN co.status IN ('pending','processing','completed')
                             THEN co.total_amount ELSE 0 END)                        AS total_revenue
                 FROM customer_orders co
                 JOIN supplier_products sp ON sp.id = co.product_id
                 WHERE sp.current_holder_id = ?"
            );
            $stmt->execute([$salesPersonId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'total_orders' => 0, 'orders_today' => 0,
                'pending' => 0, 'processing' => 0,
                'completed' => 0, 'cancelled' => 0, 'total_revenue' => 0,
            ];
            echo json_encode(['success' => true, 'stats' => $stats]);
        } catch (Exception $e) {
            error_log('[sales_orders stats] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Single order detail ───────────────────────────────────
    if ($action === 'detail') {
        $orderId = (int)($_GET['id'] ?? 0);
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID required.']);
            exit;
        }

        try {
            $cancelCol = colExists($pdo, 'customer_orders', 'cancel_reason')
                         ? "co.cancel_reason" : "'' AS cancel_reason";
            $addrLine  = colExists($pdo, 'users', 'address_line')
                         ? "u.address_line"   : "'' AS address_line";
            $barangay  = colExists($pdo, 'users', 'barangay')
                         ? "u.barangay"       : "'' AS barangay";
            $city      = colExists($pdo, 'users', 'city')
                         ? "u.city"           : "'' AS city";
            $province  = colExists($pdo, 'users', 'province')
                         ? "u.province"       : "'' AS province";
            $zipCode   = colExists($pdo, 'users', 'zip_code')
                         ? "u.zip_code"       : "'' AS zip_code";
            $addrVerif = colExists($pdo, 'users', 'address_verified')
                         ? "u.address_verified" : "0 AS address_verified";
            $phone     = colExists($pdo, 'users', 'phone')
                         ? "u.phone"          : "'' AS phone";

            $stmt = $pdo->prepare(
                "SELECT co.*,
                    $cancelCol,
                    sp.item_description AS product_name,
                    sp.category,
                    COALESCE(sp.brand,'')      AS brand,
                    COALESCE(sp.image_path,'') AS image_path,
                    u.first_name,
                    u.last_name,
                    u.email              AS customer_email,
                    $phone               AS customer_phone,
                    $addrLine,
                    $barangay,
                    $city,
                    $province,
                    $zipCode,
                    $addrVerif
                 FROM customer_orders co
                 JOIN supplier_products sp ON sp.id = co.product_id
                 JOIN users u ON u.id = co.customer_id
                 WHERE co.id = ?
                   AND sp.current_holder_id = ?"
            );
            $stmt->execute([$orderId, $salesPersonId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Order not found.']);
                exit;
            }
            echo json_encode(['success' => true, 'order' => $order]);
        } catch (Exception $e) {
            error_log('[sales_orders detail] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── POST ──────────────────────────────────────────────────────
if ($method === 'POST') {
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $postAction = $body['action'] ?? '';

    // ── Mark as Shipped ───────────────────────────────────────
    if ($postAction === 'ship') {
        $orderId = (int)($body['order_id'] ?? 0);
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID required.']);
            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT co.id, co.status, co.customer_id, sp.item_description
             FROM customer_orders co
             JOIN supplier_products sp ON sp.id = co.product_id
             WHERE co.id = ? AND sp.current_holder_id = ?"
        );
        $stmt->execute([$orderId, $salesPersonId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found.']);
            exit;
        }
        if ($order['status'] !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Only pending orders can be shipped.']);
            exit;
        }

        $pdo->prepare(
            "UPDATE customer_orders SET status = 'processing', updated_at = NOW() WHERE id = ?"
        )->execute([$orderId]);

        // Notify customer
        try {
            require_once __DIR__ . '/notification_helper.php';
            sendNotification(
                $order['customer_id'],
                'order_shipped',
                'Your Order Has Been Shipped!',
                "Your order #{$orderId} ({$order['item_description']}) is on its way. You'll receive it soon."
            );
        } catch (Exception $ne) { error_log('[ship notify] ' . $ne->getMessage()); }

        echo json_encode(['success' => true, 'message' => 'Order marked as shipped.']);
        exit;
    }

    // ── Mark as Completed ─────────────────────────────────────
    if ($postAction === 'complete') {
        $orderId = (int)($body['order_id'] ?? 0);
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID required.']);
            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT co.id, co.status, co.customer_id, sp.item_description
             FROM customer_orders co
             JOIN supplier_products sp ON sp.id = co.product_id
             WHERE co.id = ? AND sp.current_holder_id = ?"
        );
        $stmt->execute([$orderId, $salesPersonId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found.']);
            exit;
        }
        if ($order['status'] !== 'processing') {
            echo json_encode(['success' => false, 'message' => 'Only shipped orders can be marked as completed.']);
            exit;
        }

        $pdo->prepare(
            "UPDATE customer_orders SET status = 'completed', updated_at = NOW() WHERE id = ?"
        )->execute([$orderId]);

        // Notify customer
        try {
            require_once __DIR__ . '/notification_helper.php';
            sendNotification(
                $order['customer_id'],
                'order_delivered',
                'Order Completed!',
                "Your order #{$orderId} ({$order['item_description']}) has been completed. Thank you for shopping with Fix&Go!"
            );
        } catch (Exception $ne) { error_log('[complete notify] ' . $ne->getMessage()); }

        echo json_encode(['success' => true, 'message' => 'Order marked as completed.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
