<?php
/**
 * Fix&Go — Supervisor Reports API
 * Generate reports for products received from owner
 *
 * GET ?action=monthly&year=2026&month=5  → monthly report
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'supervisor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Supervisor access required.']);
    exit;
}

$supervisorId = (int) $_SESSION['user_id'];
$pdo = require __DIR__ . '/db.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'monthly';

    if ($action === 'monthly') {
        $year = (int) ($_GET['year'] ?? date('Y'));
        $month = (int) ($_GET['month'] ?? date('n'));

        // Validate year and month
        if ($year < 2020 || $year > 2100 || $month < 1 || $month > 12) {
            echo json_encode(['success' => false, 'message' => 'Invalid year or month.']);
            exit;
        }

        // Get first and last day of the month
        $firstDay = sprintf('%04d-%02d-01', $year, $month);
        $lastDay = date('Y-m-t', strtotime($firstDay));

        // Get products received in this month
        $stmt = $pdo->prepare(
            "SELECT 
                sp.id,
                sp.category,
                sp.brand,
                sp.item_description,
                sp.qty,
                sp.srp,
                sp.image_path,
                sp.updated_at
             FROM supplier_products sp
             INNER JOIN users u ON sp.supplier_id = u.id
             WHERE u.role = 'owner'
               AND sp.status = 'sent_to_supervisor'
               AND DATE(sp.updated_at) BETWEEN ? AND ?
             ORDER BY sp.updated_at DESC"
        );
        $stmt->execute([$firstDay, $lastDay]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate statistics
        $totalProducts = count($products);
        $totalQuantity = 0;
        $totalValue = 0;
        $categories = [];

        foreach ($products as $p) {
            $totalQuantity += (int) $p['qty'];
            $totalValue += (float) $p['srp'] * (int) $p['qty'];
            if (!empty($p['category']) && !in_array($p['category'], $categories)) {
                $categories[] = $p['category'];
            }
        }

        $stats = [
            'total_products' => $totalProducts,
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'unique_categories' => count($categories)
        ];

        echo json_encode([
            'success' => true,
            'products' => $products,
            'stats' => $stats,
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $firstDay,
                'end_date' => $lastDay
            ]
        ]);
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
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'application/json') !== false) {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $body['action'] ?? '';

        if ($action === 'send_to_owner') {
            $year = (int) ($body['year'] ?? date('Y'));
            $month = (int) ($body['month'] ?? date('n'));

            // Validate year and month
            if ($year < 2020 || $year > 2100 || $month < 1 || $month > 12) {
                echo json_encode(['success' => false, 'message' => 'Invalid year or month.']);
                exit;
            }

            // Get first and last day of the month
            $firstDay = sprintf('%04d-%02d-01', $year, $month);
            $lastDay = date('Y-m-t', strtotime($firstDay));

            // Get owner ID (first owner in the system)
            $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'owner' LIMIT 1");
            $stmt->execute();
            $owner = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$owner) {
                echo json_encode(['success' => false, 'message' => 'No owner found in the system.']);
                exit;
            }

            $ownerId = $owner['id'];

            // Get products for this month
            $stmt = $pdo->prepare(
                "SELECT 
                    sp.id,
                    sp.category,
                    sp.brand,
                    sp.item_description,
                    sp.qty,
                    sp.srp,
                    sp.updated_at
                 FROM supplier_products sp
                 INNER JOIN users u ON sp.supplier_id = u.id
                 WHERE u.role = 'owner'
                   AND sp.status = 'sent_to_supervisor'
                   AND DATE(sp.updated_at) BETWEEN ? AND ?
                 ORDER BY sp.updated_at DESC"
            );
            $stmt->execute([$firstDay, $lastDay]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($products)) {
                echo json_encode(['success' => false, 'message' => 'No products found for this period.']);
                exit;
            }

            // Calculate statistics
            $totalProducts = count($products);
            $totalQuantity = 0;
            $totalValue = 0;

            foreach ($products as $p) {
                $totalQuantity += (int) $p['qty'];
                $totalValue += (float) $p['srp'] * (int) $p['qty'];
            }

            // Check if report already exists for this period
            $stmt = $pdo->prepare(
                "SELECT id FROM supervisor_reports 
                 WHERE supervisor_id = ? AND owner_id = ? AND report_year = ? AND report_month = ?
                 LIMIT 1"
            );
            $stmt->execute([$supervisorId, $ownerId, $year, $month]);
            $existingReport = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingReport) {
                // Update existing report
                $stmt = $pdo->prepare(
                    "UPDATE supervisor_reports 
                     SET total_products = ?, total_quantity = ?, total_value = ?,
                         report_data = ?, sent_at = NOW(), updated_at = NOW()
                     WHERE id = ?"
                );
                $stmt->execute([
                    $totalProducts,
                    $totalQuantity,
                    $totalValue,
                    json_encode($products),
                    $existingReport['id']
                ]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Report updated and sent to owner successfully!',
                    'report_id' => $existingReport['id']
                ]);
            } else {
                // Create new report
                $stmt = $pdo->prepare(
                    "INSERT INTO supervisor_reports
                     (supervisor_id, owner_id, report_year, report_month,
                      total_products, total_quantity, total_value, report_data,
                      sent_at, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
                );
                $stmt->execute([
                    $supervisorId,
                    $ownerId,
                    $year,
                    $month,
                    $totalProducts,
                    $totalQuantity,
                    $totalValue,
                    json_encode($products)
                ]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Report sent to owner successfully!',
                    'report_id' => $pdo->lastInsertId()
                ]);
            }
            exit;
        }

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request format.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
