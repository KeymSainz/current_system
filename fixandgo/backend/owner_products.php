<?php
/**
 * Fix&Go — Owner Products API
 * Handles viewing and actioning supplier product submissions.
 *
 * GET  ?action=submissions          → pending submissions sent to this owner
 * GET  ?action=received             → all owner_received products
 * POST action=accept  ids=[...]     → mark products as owner_received
 * POST action=reject  ids=[...]     → mark products as rejected
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

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'submissions';

    if ($action === 'submissions') {
        // Products with status = 'sent_to_owner' that belong to submissions targeting this owner
        // Use DISTINCT to prevent duplicates when a product appears in multiple submissions
        $stmt = $pdo->prepare(
            "SELECT DISTINCT
                sp.id,
                sp.category,
                sp.brand,
                sp.item_description,
                sp.qty,
                sp.srp,
                sp.image_path,
                sp.notes,
                sp.sent_at,
                sp.status,
                CONCAT(u.first_name, ' ', u.last_name) AS supplier_name,
                u.email AS supplier_email,
                MAX(ps.id)           AS submission_id,
                MAX(ps.submitted_at) AS submitted_at
             FROM supplier_products sp
             JOIN users u ON u.id = sp.supplier_id
             JOIN submission_items si ON si.product_id = sp.id
             JOIN product_submissions ps ON ps.id = si.submission_id
                  AND ps.owner_id = ?
             WHERE sp.status = 'sent_to_owner'
             GROUP BY sp.id, sp.category, sp.brand, sp.item_description,
                      sp.qty, sp.srp, sp.image_path, sp.notes,
                      sp.sent_at, sp.status,
                      supplier_name, supplier_email
             ORDER BY submitted_at DESC, sp.category ASC"
        );
        $stmt->execute([$ownerId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Count pending
        $countStmt = $pdo->prepare(
            "SELECT COUNT(DISTINCT sp.id)
             FROM supplier_products sp
             JOIN submission_items si ON si.product_id = sp.id
             JOIN product_submissions ps ON ps.id = si.submission_id AND ps.owner_id = ?
             WHERE sp.status = 'sent_to_owner'"
        );
        $countStmt->execute([$ownerId]);
        $pending = (int) $countStmt->fetchColumn();

        echo json_encode([
            'success'  => true,
            'products' => $products,
            'pending'  => $pending,
        ]);
        exit;
    }

    if ($action === 'received') {
        // All products already accepted by this owner — DISTINCT to prevent duplicates
        $stmt = $pdo->prepare(
            "SELECT DISTINCT
                sp.id,
                sp.category,
                sp.brand,
                sp.item_description,
                sp.qty,
                sp.srp,
                sp.image_path,
                sp.notes,
                sp.status,
                CONCAT(u.first_name, ' ', u.last_name) AS supplier_name,
                u.email AS supplier_email
             FROM supplier_products sp
             JOIN users u ON u.id = sp.supplier_id
             JOIN submission_items si ON si.product_id = sp.id
             JOIN product_submissions ps ON ps.id = si.submission_id AND ps.owner_id = ?
             WHERE sp.status = 'owner_received'
             GROUP BY sp.id, sp.category, sp.brand, sp.item_description,
                      sp.qty, sp.srp, sp.image_path, sp.notes,
                      sp.status, supplier_name, supplier_email
             ORDER BY sp.category ASC, sp.item_description ASC"
        );
        $stmt->execute([$ownerId]);
        echo json_encode(['success' => true, 'products' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';
    $ids    = array_map('intval', $body['ids'] ?? []);

    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'No product IDs provided.']);
        exit;
    }

    // Verify these products were actually sent to THIS owner
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $verifyStmt = $pdo->prepare(
        "SELECT DISTINCT sp.id
         FROM supplier_products sp
         JOIN submission_items si ON si.product_id = sp.id
         JOIN product_submissions ps ON ps.id = si.submission_id AND ps.owner_id = ?
         WHERE sp.id IN ($placeholders) AND sp.status = 'sent_to_owner'"
    );
    $verifyStmt->execute(array_merge([$ownerId], $ids));
    $validIds = array_column($verifyStmt->fetchAll(PDO::FETCH_ASSOC), 'id');

    if (empty($validIds)) {
        echo json_encode(['success' => false, 'message' => 'No valid products found.']);
        exit;
    }

    $vPlaceholders = implode(',', array_fill(0, count($validIds), '?'));

    if ($action === 'accept') {
        $pdo->prepare(
            "UPDATE supplier_products
             SET status = 'owner_received', updated_at = NOW()
             WHERE id IN ($vPlaceholders)"
        )->execute($validIds);

        // Mark submission as acknowledged
        $pdo->prepare(
            "UPDATE product_submissions ps
             JOIN submission_items si ON si.submission_id = ps.id
             SET ps.status = 'acknowledged', ps.acknowledged_at = NOW()
             WHERE si.product_id IN ($vPlaceholders) AND ps.owner_id = ?"
        )->execute(array_merge($validIds, [$ownerId]));

        echo json_encode(['success' => true, 'accepted' => count($validIds)]);

    } elseif ($action === 'reject') {
        $pdo->prepare(
            "UPDATE supplier_products
             SET status = 'rejected', updated_at = NOW()
             WHERE id IN ($vPlaceholders)"
        )->execute($validIds);

        $pdo->prepare(
            "UPDATE product_submissions ps
             JOIN submission_items si ON si.submission_id = ps.id
             SET ps.status = 'rejected'
             WHERE si.product_id IN ($vPlaceholders) AND ps.owner_id = ?"
        )->execute(array_merge($validIds, [$ownerId]));

        echo json_encode(['success' => true, 'rejected' => count($validIds)]);

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
