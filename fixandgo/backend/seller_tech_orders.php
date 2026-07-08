<?php
/**
 * Fix&Go — Seller (Supplier/Owner) Technician Orders API
 *
 * GET  ?action=list          → orders placed by technicians to this seller
 * GET  ?action=stats         → counts by status
 * POST action=update_status  → update order status (confirmed/preparing/ready/shipped/delivered/cancelled)
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

$myRole = $_SESSION['user_role'] ?? '';
if (empty($_SESSION['user_id']) || !in_array($myRole, ['supplier', 'owner'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Supplier or Owner access required.']);
    exit;
}

$sellerId = (int) $_SESSION['user_id'];
$pdo      = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method   = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    if ($action === 'stats') {
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    COUNT(*)                                                              AS total,
                    SUM(CASE WHEN order_status='pending'   THEN 1 ELSE 0 END)            AS pending,
                    SUM(CASE WHEN order_status='confirmed' THEN 1 ELSE 0 END)            AS confirmed,
                    SUM(CASE WHEN order_status='delivered' THEN 1 ELSE 0 END)            AS delivered,
                    SUM(CASE WHEN order_status='cancelled' THEN 1 ELSE 0 END)            AS cancelled,
                    SUM(CASE WHEN payment_status='paid'    THEN total_amount ELSE 0 END) AS total_revenue
                 FROM technician_orders WHERE seller_id = ?"
            );
            $stmt->execute([$sellerId]);
            echo json_encode(['success' => true, 'stats' => $stmt->fetch(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'list') {
        $statusFilter = $_GET['status'] ?? 'all';
        try {
            $sql = "SELECT
                        o.id, o.seller_role, o.fulfillment_type, o.delivery_address,
                        o.payment_method, o.payment_status, o.order_status,
                        o.subtotal, o.shipping_fee, o.total_amount,
                        o.reference, o.notes, o.seller_notes,
                        o.created_at, o.updated_at,
                        CONCAT(u.first_name,' ',u.last_name) AS technician_name,
                        u.email                              AS technician_email,
                        COALESCE(u.phone,'')                 AS technician_phone
                    FROM technician_orders o
                    JOIN users u ON u.id = o.technician_id
                    WHERE o.seller_id = ?";
            $params = [$sellerId];
            if ($statusFilter !== 'all') { $sql .= " AND o.order_status = ?"; $params[] = $statusFilter; }
            $sql .= " ORDER BY o.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($orders as &$order) {
                $iStmt = $pdo->prepare(
                    "SELECT product_name, category, unit_price, quantity, subtotal
                     FROM technician_order_items WHERE order_id = ?"
                );
                $iStmt->execute([$order['id']]);
                $order['items'] = $iStmt->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($order);

            echo json_encode(['success' => true, 'orders' => $orders]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

if ($method === 'POST') {
    $body      = json_decode(file_get_contents('php://input'), true) ?? [];
    $action    = $body['action']    ?? '';
    $orderId   = (int)($body['order_id'] ?? 0);
    $newStatus = trim($body['status']    ?? '');
    $notes     = trim($body['notes']     ?? '');

    if ($action === 'update_status') {
        $allowed = ['confirmed','preparing','ready','shipped','delivered','cancelled'];
        if (!$orderId || !in_array($newStatus, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid order ID or status.']);
            exit;
        }
        try {
            // Verify this order belongs to this seller
            $check = $pdo->prepare("SELECT id, order_status, technician_id FROM technician_orders WHERE id = ? AND seller_id = ?");
            $check->execute([$orderId, $sellerId]);
            $order = $check->fetch(PDO::FETCH_ASSOC);
            if (!$order) { echo json_encode(['success' => false, 'message' => 'Order not found.']); exit; }

            $upd = $pdo->prepare(
                "UPDATE technician_orders
                 SET order_status = ?, seller_notes = ?, updated_at = NOW()
                 WHERE id = ? AND seller_id = ?"
            );
            $upd->execute([$newStatus, $notes ?: null, $orderId, $sellerId]);

            // Notify technician
            try {
                require_once __DIR__ . '/notification_helper.php';
                $statusLabels = ['confirmed'=>'Confirmed','preparing'=>'Being Prepared','ready'=>'Ready for Pickup','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'];
                sendNotification(
                    $order['technician_id'],
                    'order_update',
                    'Order #' . $orderId . ' ' . ($statusLabels[$newStatus] ?? $newStatus),
                    'Your order #' . $orderId . ' status has been updated to: ' . ($statusLabels[$newStatus] ?? $newStatus) . ($notes ? '. Note: ' . $notes : '')
                );
            } catch (Exception $ne) { error_log('[seller_tech_orders notify] ' . $ne->getMessage()); }

            echo json_encode(['success' => true, 'message' => 'Order status updated.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
