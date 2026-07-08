<?php
/**
 * Fix&Go — Supplier: Technician Supply Requests API
 *
 * Allows suppliers to view and respond to supply requests from technicians.
 *
 * GET  ?action=list          → all requests sent to this supplier
 * GET  ?action=stats         → counts by status
 * POST action=approve        → approve a pending request
 * POST action=reject         → reject a pending request
 * POST action=fulfill        → mark an approved request as fulfilled
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (
    empty($_SESSION['user_id']) ||
    empty($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'supplier'
) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Supplier access required.']);
    exit;
}

$supplierId = (int) $_SESSION['user_id'];
$pdo        = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method     = $_SERVER['REQUEST_METHOD'];

function techReqTableExists(PDO $pdo): bool {
    $r = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES
                        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'technician_supply_requests'");
    $r->execute();
    return (bool) $r->fetchColumn();
}

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    if (!techReqTableExists($pdo)) {
        echo json_encode(['success' => true, 'requests' => [], 'stats' => [
            'total'=>0,'pending'=>0,'approved'=>0,'rejected'=>0,'fulfilled'=>0,'cancelled'=>0
        ]]);
        exit;
    }

    // ── Stats ─────────────────────────────────────────────────
    if ($action === 'stats') {
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    COUNT(*)                                                          AS total,
                    SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END)            AS pending,
                    SUM(CASE WHEN status = 'approved'  THEN 1 ELSE 0 END)            AS approved,
                    SUM(CASE WHEN status = 'rejected'  THEN 1 ELSE 0 END)            AS rejected,
                    SUM(CASE WHEN status = 'fulfilled' THEN 1 ELSE 0 END)            AS fulfilled,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END)            AS cancelled
                 FROM technician_supply_requests
                 WHERE supplier_id = ?"
            );
            $stmt->execute([$supplierId]);
            echo json_encode(['success' => true, 'stats' => $stmt->fetch(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── List requests ─────────────────────────────────────────
    if ($action === 'list') {
        $statusFilter = $_GET['status'] ?? 'all';
        try {
            $sql = "SELECT
                        tsr.id,
                        tsr.quantity_requested,
                        tsr.note,
                        tsr.status,
                        tsr.supplier_notes,
                        tsr.created_at,
                        tsr.updated_at,
                        sp.item_description             AS product_name,
                        sp.category                     AS product_category,
                        sp.srp                          AS product_price,
                        sp.qty                          AS product_stock,
                        COALESCE(sp.image_path, '')     AS product_image,
                        CONCAT(u.first_name, ' ', u.last_name) AS technician_name,
                        u.email                         AS technician_email,
                        COALESCE(u.phone, '')           AS technician_phone
                    FROM technician_supply_requests tsr
                    JOIN supplier_products sp ON sp.id = tsr.product_id
                    JOIN users u ON u.id = tsr.technician_id
                    WHERE tsr.supplier_id = ?";
            $params = [$supplierId];
            if ($statusFilter !== 'all') {
                $sql .= " AND tsr.status = ?";
                $params[] = $statusFilter;
            }
            $sql .= " ORDER BY tsr.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['success' => true, 'requests' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            error_log('[supplier_tech_requests list] ' . $e->getMessage());
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
    $body      = json_decode(file_get_contents('php://input'), true) ?? [];
    $action    = $body['action']    ?? '';
    $requestId = (int)($body['request_id'] ?? 0);
    $notes     = trim($body['notes'] ?? '');

    if (!$requestId) {
        echo json_encode(['success' => false, 'message' => 'Request ID required.']);
        exit;
    }

    if (!techReqTableExists($pdo)) {
        echo json_encode(['success' => false, 'message' => 'Supply requests table not found.']);
        exit;
    }

    // Verify this request belongs to this supplier
    $check = $pdo->prepare("SELECT id, status, technician_id FROM technician_supply_requests WHERE id = ? AND supplier_id = ?");
    $check->execute([$requestId, $supplierId]);
    $req = $check->fetch(PDO::FETCH_ASSOC);
    if (!$req) {
        echo json_encode(['success' => false, 'message' => 'Request not found.']);
        exit;
    }

    // ── Approve ───────────────────────────────────────────────
    if ($action === 'approve') {
        if ($req['status'] !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Only pending requests can be approved.']);
            exit;
        }
        try {
            $upd = $pdo->prepare(
                "UPDATE technician_supply_requests
                 SET status = 'approved', supplier_notes = ?, updated_at = NOW()
                 WHERE id = ? AND supplier_id = ?"
            );
            $upd->execute([$notes ?: null, $requestId, $supplierId]);

            // Notify technician
            try {
                require_once __DIR__ . '/notification_helper.php';
                sendNotification(
                    $req['technician_id'],
                    'supply_request_approved',
                    'Supply Request Approved',
                    "Your supply request #{$requestId} has been approved by the supplier." . ($notes ? " Note: {$notes}" : '')
                );
            } catch (Exception $ne) { error_log('[notify approve] ' . $ne->getMessage()); }

            echo json_encode(['success' => true, 'message' => 'Request approved.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Reject ────────────────────────────────────────────────
    if ($action === 'reject') {
        if ($req['status'] !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Only pending requests can be rejected.']);
            exit;
        }
        try {
            $upd = $pdo->prepare(
                "UPDATE technician_supply_requests
                 SET status = 'rejected', supplier_notes = ?, updated_at = NOW()
                 WHERE id = ? AND supplier_id = ?"
            );
            $upd->execute([$notes ?: null, $requestId, $supplierId]);

            // Notify technician
            try {
                require_once __DIR__ . '/notification_helper.php';
                sendNotification(
                    $req['technician_id'],
                    'supply_request_rejected',
                    'Supply Request Rejected',
                    "Your supply request #{$requestId} has been rejected." . ($notes ? " Reason: {$notes}" : '')
                );
            } catch (Exception $ne) { error_log('[notify reject] ' . $ne->getMessage()); }

            echo json_encode(['success' => true, 'message' => 'Request rejected.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Fulfill ───────────────────────────────────────────────
    if ($action === 'fulfill') {
        if ($req['status'] !== 'approved') {
            echo json_encode(['success' => false, 'message' => 'Only approved requests can be marked as fulfilled.']);
            exit;
        }
        try {
            $upd = $pdo->prepare(
                "UPDATE technician_supply_requests
                 SET status = 'fulfilled', supplier_notes = ?, updated_at = NOW()
                 WHERE id = ? AND supplier_id = ?"
            );
            $upd->execute([$notes ?: null, $requestId, $supplierId]);

            // Notify technician
            try {
                require_once __DIR__ . '/notification_helper.php';
                sendNotification(
                    $req['technician_id'],
                    'supply_request_fulfilled',
                    'Supply Request Fulfilled',
                    "Your supply request #{$requestId} has been fulfilled. The product is on its way!"
                );
            } catch (Exception $ne) { error_log('[notify fulfill] ' . $ne->getMessage()); }

            echo json_encode(['success' => true, 'message' => 'Request marked as fulfilled.']);
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
