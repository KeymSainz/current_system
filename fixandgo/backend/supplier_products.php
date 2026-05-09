<?php
/**
 * Fix&Go — Supplier Products API
 * Handles CRUD + image upload + verify + send_to_owner
 *
 * Actions (GET):  list
 * Actions (POST): create | update | delete | update_status
 *
 * Accepts both multipart/form-data (with image) and application/json.
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

// ── Auth check ───────────────────────────────────────────────
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'supplier') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$supplierId = (int) $_SESSION['user_id'];
$pdo        = require __DIR__ . '/db.php';
$method     = $_SERVER['REQUEST_METHOD'];

// ── Upload directory ─────────────────────────────────────────
define('UPLOAD_DIR',  __DIR__ . '/../uploads/products/');
define('UPLOAD_URL',  'uploads/products/');
define('MAX_SIZE',    5 * 1024 * 1024); // 5 MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// ── Helper: handle image upload ──────────────────────────────
function handleImageUpload(string $fieldName): ?string {
    if (empty($_FILES[$fieldName]['tmp_name'])) return null;

    $file     = $_FILES[$fieldName];
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, ALLOWED_TYPES, true)) {
        throw new RuntimeException('Invalid image type. Allowed: JPG, PNG, WEBP, GIF.');
    }

    if ($file['size'] > MAX_SIZE) {
        throw new RuntimeException('Image too large. Maximum size is 5MB.');
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('prod_', true) . '.' . strtolower($ext);
    $dest     = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Failed to save image.');
    }

    return UPLOAD_URL . $filename;
}

// ── Helper: delete old image ─────────────────────────────────
function deleteOldImage(?string $path): void {
    if (!$path) return;
    $full = __DIR__ . '/../' . $path;
    if (file_exists($full)) @unlink($full);
}

// ── GET: list products / owners ─────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    if ($action === 'owners') {
        // Return all active owner users so supplier can pick one
        $stmt = $pdo->query(
            "SELECT u.id,
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                    u.email,
                    s.name  AS shop_name,
                    s.city  AS shop_city
             FROM users u
             LEFT JOIN shops s ON s.owner_id = u.id AND s.is_active = 1
             WHERE u.role = 'owner' AND u.is_active = 1
             ORDER BY u.first_name ASC"
        );
        echo json_encode(['success' => true, 'owners' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    $stmt = $pdo->prepare(
        'SELECT id, category, brand, item_description, qty, srp, image_path,
                status, notes, created_at, updated_at
         FROM supplier_products
         WHERE supplier_id = ?
         ORDER BY created_at DESC'
    );
    $stmt->execute([$supplierId]);
    echo json_encode(['success' => true, 'products' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

// ── POST: actions ────────────────────────────────────────────
if ($method === 'POST') {

    // Detect if multipart (file upload) or JSON
    $isMultipart = !empty($_FILES) || !empty($_POST);

    if ($isMultipart) {
        $body   = $_POST;
        $action = $body['action'] ?? '';
    } else {
        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $body['action'] ?? '';
    }

    switch ($action) {

        // ── CREATE ───────────────────────────────────────────
        case 'create':
            $category = trim($body['category'] ?? '');
            $brand    = trim($body['brand'] ?? '');
            $desc     = trim($body['item_description'] ?? '');
            $qty      = max(0, (int)($body['qty'] ?? 0));
            $srp      = max(0, (float)($body['srp'] ?? 0));
            $notes    = trim($body['notes'] ?? '');

            if (!$category || !$desc) {
                echo json_encode(['success' => false, 'message' => 'Category and description are required.']);
                exit;
            }

            // Handle image upload
            $imagePath = null;
            try {
                $imagePath = handleImageUpload('product_image');
            } catch (RuntimeException $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }

            $stmt = $pdo->prepare(
                'INSERT INTO supplier_products
                 (supplier_id, category, brand, item_description, qty, srp, image_path, notes, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, "draft")'
            );
            $stmt->execute([$supplierId, $category, $brand, $desc, $qty, $srp, $imagePath, $notes]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId(), 'image_path' => $imagePath]);
            break;

        // ── UPDATE ───────────────────────────────────────────
        case 'update':
            $id       = (int)($body['id'] ?? 0);
            $category = trim($body['category'] ?? '');
            $brand    = trim($body['brand'] ?? '');
            $desc     = trim($body['item_description'] ?? '');
            $qty      = max(0, (int)($body['qty'] ?? 0));
            $srp      = max(0, (float)($body['srp'] ?? 0));
            $notes    = trim($body['notes'] ?? '');
            $existing = trim($body['existing_image'] ?? '');

            if (!$id || !$category || !$desc) {
                echo json_encode(['success' => false, 'message' => 'Invalid data.']);
                exit;
            }

            // Handle new image upload
            $imagePath = $existing ?: null;
            try {
                $newImage = handleImageUpload('product_image');
                if ($newImage) {
                    // Delete old image if replaced
                    deleteOldImage($existing);
                    $imagePath = $newImage;
                }
            } catch (RuntimeException $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }

            $stmt = $pdo->prepare(
                'UPDATE supplier_products
                 SET category=?, brand=?, item_description=?, qty=?, srp=?, image_path=?, notes=?
                 WHERE id=? AND supplier_id=?'
            );
            $stmt->execute([$category, $brand, $desc, $qty, $srp, $imagePath, $notes, $id, $supplierId]);
            echo json_encode(['success' => true, 'image_path' => $imagePath]);
            break;

        // ── DELETE ───────────────────────────────────────────
        case 'delete':
            $id = (int)($body['id'] ?? 0);
            if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID.']); exit; }

            // Get image path before deleting
            $row = $pdo->prepare('SELECT image_path FROM supplier_products WHERE id=? AND supplier_id=?');
            $row->execute([$id, $supplierId]);
            $product = $row->fetch();
            if ($product) deleteOldImage($product['image_path']);

            $stmt = $pdo->prepare('DELETE FROM supplier_products WHERE id=? AND supplier_id=?');
            $stmt->execute([$id, $supplierId]);
            echo json_encode(['success' => true]);
            break;

        // ── UPDATE STATUS ────────────────────────────────────
        case 'update_status':
            $ids    = array_map('intval', $body['ids'] ?? []);
            $status = $body['status'] ?? '';
            $allowed = ['verified', 'sent_to_owner', 'draft'];

            if (empty($ids) || !in_array($status, $allowed, true)) {
                echo json_encode(['success' => false, 'message' => 'Invalid request.']);
                exit;
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $params       = array_merge($ids, [$supplierId]);

            if ($status === 'sent_to_owner') {
                // Use the owner_id passed from the frontend (supplier chose a specific owner)
                $requestedOwnerId = (int)($body['owner_id'] ?? 0);

                if ($requestedOwnerId) {
                    // Verify the requested owner exists and is active
                    $ownerCheck = $pdo->prepare("SELECT id FROM users WHERE id=? AND role='owner' AND is_active=1 LIMIT 1");
                    $ownerCheck->execute([$requestedOwnerId]);
                    $owner = $ownerCheck->fetch();
                    $ownerId = $owner ? $owner['id'] : null;
                } else {
                    // Fallback: shop link or first active owner
                    $ownerStmt = $pdo->prepare(
                        "SELECT DISTINCT s.owner_id AS id
                         FROM shop_members sm
                         JOIN shops s ON s.id = sm.shop_id AND s.is_active = 1
                         WHERE sm.user_id = ? LIMIT 1"
                    );
                    $ownerStmt->execute([$supplierId]);
                    $owner = $ownerStmt->fetch();
                    if (!$owner) {
                        $ownerStmt = $pdo->query("SELECT id FROM users WHERE role='owner' AND is_active=1 LIMIT 1");
                        $owner = $ownerStmt->fetch();
                    }
                    $ownerId = $owner ? $owner['id'] : null;
                }

                if (!$ownerId) {
                    echo json_encode(['success' => false, 'message' => 'No owner found. Please select a valid owner.']);
                    exit;
                }

                $subStmt = $pdo->prepare(
                    'INSERT INTO product_submissions (supplier_id, owner_id, status) VALUES (?, ?, "pending")'
                );
                $subStmt->execute([$supplierId, $ownerId]);
                $submissionId = $pdo->lastInsertId();

                $itemStmt = $pdo->prepare(
                    'INSERT INTO submission_items (submission_id, product_id) VALUES (?, ?)'
                );
                foreach ($ids as $pid) {
                    $itemStmt->execute([$submissionId, $pid]);
                }

                $updateStmt = $pdo->prepare(
                    "UPDATE supplier_products SET status='sent_to_owner', sent_at=NOW()
                     WHERE id IN ($placeholders) AND supplier_id=?"
                );
                $updateResult = $updateStmt->execute($params);
                $rowsAffected = $updateStmt->rowCount();
                
                if ($rowsAffected === 0) {
                    error_log("WARNING: No products were updated. IDs: " . implode(',', $ids) . ", Supplier: $supplierId");
                }
            } else {
                $pdo->prepare(
                    "UPDATE supplier_products SET status=?, verified_at=NOW()
                     WHERE id IN ($placeholders) AND supplier_id=?"
                )->execute(array_merge([$status], $ids, [$supplierId]));
            }

            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
