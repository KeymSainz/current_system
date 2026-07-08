<?php
/**
 * Fix&Go — Supplier Orders API
 * Returns product submission batches sent by this supplier to owners.
 *
 * GET  ?action=list   → all submissions with their items
 * GET  ?action=stats  → counts by status
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
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$supplierId = (int) $_SESSION['user_id'];
$pdo        = require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$action = $_GET['action'] ?? 'list';

// ── Stats ─────────────────────────────────────────────────────
if ($action === 'stats') {
    $stmt = $pdo->prepare(
        "SELECT
            COUNT(*)                                                        AS total,
            SUM(CASE WHEN ps.status = 'pending'      THEN 1 ELSE 0 END)   AS pending,
            SUM(CASE WHEN ps.status = 'acknowledged' THEN 1 ELSE 0 END)   AS acknowledged,
            SUM(CASE WHEN ps.status = 'rejected'     THEN 1 ELSE 0 END)   AS rejected
         FROM product_submissions ps
         WHERE ps.supplier_id = ?"
    );
    $stmt->execute([$supplierId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
        'total' => 0, 'pending' => 0, 'acknowledged' => 0, 'rejected' => 0,
    ];
    echo json_encode(['success' => true, 'stats' => $stats]);
    exit;
}

// ── List submissions with items ───────────────────────────────
if ($action === 'list') {
    // Fetch all submission batches for this supplier
    $stmt = $pdo->prepare(
        "SELECT
            ps.id,
            ps.status,
            ps.created_at,
            ps.acknowledged_at,
            CONCAT(u.first_name, ' ', u.last_name) AS owner_name,
            u.email                                 AS owner_email
         FROM product_submissions ps
         JOIN users u ON u.id = ps.owner_id
         WHERE ps.supplier_id = ?
         ORDER BY ps.created_at DESC
         LIMIT 200"
    );
    $stmt->execute([$supplierId]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($submissions)) {
        echo json_encode(['success' => true, 'submissions' => []]);
        exit;
    }

    // Collect all submission IDs
    $ids          = array_column($submissions, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Fetch all items for those submissions in one query
    $itemStmt = $pdo->prepare(
        "SELECT
            si.submission_id,
            sp.id           AS product_id,
            sp.item_description,
            sp.category,
            sp.brand,
            sp.qty,
            sp.srp,
            sp.image_path,
            sp.status       AS product_status
         FROM submission_items si
         JOIN supplier_products sp ON sp.id = si.product_id
         WHERE si.submission_id IN ($placeholders)
         ORDER BY sp.item_description ASC"
    );
    $itemStmt->execute($ids);
    $allItems = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    // Group items by submission_id
    $itemsBySubmission = [];
    foreach ($allItems as $item) {
        $itemsBySubmission[$item['submission_id']][] = $item;
    }

    // Attach items to each submission
    foreach ($submissions as &$sub) {
        $sub['items'] = $itemsBySubmission[$sub['id']] ?? [];
    }
    unset($sub);

    echo json_encode(['success' => true, 'submissions' => $submissions]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Unknown action.']);
