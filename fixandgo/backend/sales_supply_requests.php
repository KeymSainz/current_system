<?php
/**
 * Fix&Go — Sales Person Supply Requests API
 *
 * GET  ?action=list    → list this sales person's supply requests
 * GET  ?action=stats   → request statistics
 * POST action=create   → create a new supply request (JSON)
 * POST action=delete   → delete a pending request (JSON)
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

// ============================================================
// GET REQUESTS
// ============================================================
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    if ($action === 'list') {
        $stmt = $pdo->prepare(
            "SELECT id, product_name, category, quantity_requested, reason,
                    status, supervisor_notes, created_at, updated_at
             FROM supply_requests
             WHERE sales_person_id = ?
             ORDER BY created_at DESC"
        );
        $stmt->execute([$salesPersonId]);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'requests' => $requests]);
        exit;
    }

    if ($action === 'stats') {
        $stmt = $pdo->prepare(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected
             FROM supply_requests
             WHERE sales_person_id = ?"
        );
        $stmt->execute([$salesPersonId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'stats' => $stats]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ============================================================
// POST REQUESTS
// ============================================================
if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    if ($action === 'create') {
        $productName = trim($body['product_name'] ?? '');
        $category    = trim($body['category'] ?? '');
        $quantity    = intval($body['quantity_requested'] ?? 0);
        $reason      = trim($body['reason'] ?? '');

        if (empty($productName) || $quantity < 1) {
            echo json_encode(['success' => false, 'message' => 'Product name and quantity (≥ 1) are required.']);
            exit;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO supply_requests
             (sales_person_id, product_name, category, quantity_requested, reason, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())"
        );
        $stmt->execute([$salesPersonId, $productName, $category, $quantity, $reason]);

        echo json_encode([
            'success'    => true,
            'message'    => 'Supply request submitted successfully.',
            'request_id' => (int) $pdo->lastInsertId(),
        ]);
        exit;
    }

    if ($action === 'delete') {
        $requestId = intval($body['request_id'] ?? 0);
        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID is required.']);
            exit;
        }

        // Only allow deleting own pending requests
        $stmt = $pdo->prepare(
            "DELETE FROM supply_requests
             WHERE id = ? AND sales_person_id = ? AND status = 'pending'"
        );
        $stmt->execute([$requestId, $salesPersonId]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Request not found or cannot be deleted (only pending requests can be deleted).']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Request deleted.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
