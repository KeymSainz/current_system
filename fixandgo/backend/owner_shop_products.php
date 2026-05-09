<?php
/**
 * Fix&Go — Owner Shop Products API
 * Manage the owner's own product inventory (including purchased products)
 *
 * GET  ?action=list     → all products owned by this owner
 * GET  ?action=stats    → product statistics
 * POST action=add       → add new product
 * POST action=update    → update existing product
 * POST action=delete    → delete product(s)
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
    $action = $_GET['action'] ?? 'list';

    if ($action === 'list') {
        // Get all products owned by this owner
        $stmt = $pdo->prepare(
            "SELECT 
                id,
                category,
                brand,
                item_description,
                qty,
                srp,
                image_path,
                status,
                notes,
                created_at,
                updated_at
             FROM supplier_products
             WHERE supplier_id = ?
             ORDER BY created_at DESC"
        );
        $stmt->execute([$ownerId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success'  => true,
            'products' => $products,
        ]);
        exit;
    }

    if ($action === 'stats') {
        // Get product statistics
        $stmt = $pdo->prepare(
            "SELECT 
                COUNT(*) AS total_products,
                SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) AS verified_count,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS draft_count,
                SUM(qty) AS total_items,
                SUM(qty * srp) AS total_value
             FROM supplier_products
             WHERE supplier_id = ?"
        );
        $stmt->execute([$ownerId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'stats'   => $stats,
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    if ($action === 'add') {
        $category = trim($body['category'] ?? '');
        $brand = trim($body['brand'] ?? '');
        $itemDescription = trim($body['item_description'] ?? '');
        $qty = max(0, (int)($body['qty'] ?? 0));
        $srp = max(0, (float)($body['srp'] ?? 0));
        $imagePath = trim($body['image_path'] ?? '');
        $notes = trim($body['notes'] ?? '');
        $status = trim($body['status'] ?? 'draft'); // draft, verified, or sent_to_supervisor

        if (empty($category) || empty($itemDescription)) {
            echo json_encode(['success' => false, 'message' => 'Category and description are required.']);
            exit;
        }

        // Validate status
        if (!in_array($status, ['draft', 'verified', 'sent_to_supervisor'])) {
            $status = 'draft';
        }

        $verifiedAt = ($status === 'verified' || $status === 'sent_to_supervisor') ? 'NOW()' : 'NULL';

        $stmt = $pdo->prepare(
            "INSERT INTO supplier_products
             (supplier_id, category, brand, item_description, qty, srp,
              image_path, notes, status, verified_at, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, $verifiedAt, NOW())"
        );
        $stmt->execute([
            $ownerId,
            $category,
            $brand,
            $itemDescription,
            $qty,
            $srp,
            $imagePath,
            $notes,
            $status
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Product added successfully.',
            'id' => $pdo->lastInsertId()
        ]);
        exit;
    }

    if ($action === 'update') {
        $id = (int)($body['id'] ?? 0);
        $category = trim($body['category'] ?? '');
        $brand = trim($body['brand'] ?? '');
        $itemDescription = trim($body['item_description'] ?? '');
        $qty = max(0, (int)($body['qty'] ?? 0));
        $srp = max(0, (float)($body['srp'] ?? 0));
        $imagePath = trim($body['image_path'] ?? '');
        $notes = trim($body['notes'] ?? '');
        $status = trim($body['status'] ?? '');

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
            exit;
        }

        // Verify ownership
        $checkStmt = $pdo->prepare('SELECT id, status FROM supplier_products WHERE id = ? AND supplier_id = ?');
        $checkStmt->execute([$id, $ownerId]);
        $product = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found or access denied.']);
            exit;
        }

        // If status is being updated, validate it
        if (!empty($status) && !in_array($status, ['draft', 'verified', 'sent_to_supervisor'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status.']);
            exit;
        }

        // Build update query dynamically
        $updates = [
            'category = ?',
            'brand = ?',
            'item_description = ?',
            'qty = ?',
            'srp = ?',
            'image_path = ?',
            'notes = ?',
            'updated_at = NOW()'
        ];
        $params = [$category, $brand, $itemDescription, $qty, $srp, $imagePath, $notes];

        if (!empty($status)) {
            $updates[] = 'status = ?';
            $params[] = $status;
            
            // Set verified_at if changing to verified or sent_to_supervisor
            if (($status === 'verified' || $status === 'sent_to_supervisor') && $product['status'] === 'draft') {
                $updates[] = 'verified_at = NOW()';
            }
        }

        $params[] = $id;
        $params[] = $ownerId;

        $stmt = $pdo->prepare(
            'UPDATE supplier_products
             SET ' . implode(', ', $updates) . '
             WHERE id = ? AND supplier_id = ?'
        );
        $stmt->execute($params);

        echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
        exit;
    }

    if ($action === 'send_to_supervisor') {
        $ids = array_map('intval', $body['ids'] ?? []);

        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'No product IDs provided.']);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare(
            "UPDATE supplier_products
             SET status = 'sent_to_supervisor', verified_at = NOW(), updated_at = NOW()
             WHERE id IN ($placeholders) AND supplier_id = ? AND status IN ('draft', 'verified')"
        );
        $stmt->execute(array_merge($ids, [$ownerId]));

        echo json_encode([
            'success' => true,
            'message' => 'Products sent to supervisor successfully.',
            'updated' => $stmt->rowCount()
        ]);
        exit;
    }

    if ($action === 'delete') {
        $ids = array_map('intval', $body['ids'] ?? []);

        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'No product IDs provided.']);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare(
            "DELETE FROM supplier_products
             WHERE id IN ($placeholders) AND supplier_id = ?"
        );
        $stmt->execute(array_merge($ids, [$ownerId]));

        echo json_encode([
            'success' => true,
            'message' => 'Products deleted successfully.',
            'deleted' => $stmt->rowCount()
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
