<?php
/**
 * Fix&Go — Owner: View Supervisor Reports API
 * Allows owners to view reports sent by supervisors
 *
 * GET ?action=list  → list all reports sent to this owner
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Owner access required.']);
    exit;
}

$ownerId = (int) $_SESSION['user_id'];
$pdo = require __DIR__ . '/db.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    if ($action === 'list') {
        // Get all reports sent to this owner
        $stmt = $pdo->prepare(
            "SELECT 
                sr.id,
                sr.supervisor_id,
                sr.report_year,
                sr.report_month,
                sr.total_products,
                sr.total_quantity,
                sr.total_value,
                sr.report_data,
                sr.sent_at,
                sr.created_at,
                CONCAT(u.first_name, ' ', u.last_name) AS supervisor_name,
                u.email AS supervisor_email
             FROM supervisor_reports sr
             INNER JOIN users u ON sr.supervisor_id = u.id
             WHERE sr.owner_id = ?
             ORDER BY sr.report_year DESC, sr.report_month DESC, sr.sent_at DESC"
        );
        $stmt->execute([$ownerId]);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'reports' => $reports,
            'total' => count($reports)
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
