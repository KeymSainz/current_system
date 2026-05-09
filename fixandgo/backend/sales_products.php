<?php
/**
 * Fix&Go — Sales Person Products API
 *
 * GET  ?action=list   → list this sales person's products
 * GET  ?action=stats  → product statistics
 * POST action=add     → add product (multipart with optional image)
 * POST action=update  → update product
 * POST action=delete  → delete product (JSON)
 * POST action=toggle  → toggle active/inactive (JSON)
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
            "SELECT id, name, description, category, price, stock, image_path, is_active, created_at, updated_at
             FROM sales_products
             WHERE sales_person_id = ?
             ORDER BY created_at DESC"
        );
        $stmt->execute([$salesPersonId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'products' => $products]);
        exit;
    }

    if ($action === 'stats') {
        $stmt = $pdo->prepare(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS inactive,
                SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) AS out_of_stock,
                SUM(stock) AS total_stock
             FROM sales_products
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
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    // ── Multipart (add / update with optional image) ──────────
    if (strpos($contentType, 'multipart/form-data') !== false) {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $name        = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category    = trim($_POST['category'] ?? '');
            $price       = floatval($_POST['price'] ?? 0);
            $stock       = intval($_POST['stock'] ?? 0);

            if (empty($name) || empty($category) || $price < 0) {
                echo json_encode(['success' => false, 'message' => 'Name, category, and a valid price are required.']);
                exit;
            }

            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload = handleImageUpload($_FILES['image']);
                if (!$upload['success']) {
                    echo json_encode(['success' => false, 'message' => $upload['message']]);
                    exit;
                }
                $imagePath = $upload['path'];
            }

            $stmt = $pdo->prepare(
                "INSERT INTO sales_products
                 (sales_person_id, name, description, category, price, stock, image_path, is_active, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())"
            );
            $stmt->execute([$salesPersonId, $name, $description, $category, $price, $stock, $imagePath]);

            echo json_encode([
                'success'    => true,
                'message'    => 'Product added successfully.',
                'product_id' => (int) $pdo->lastInsertId(),
            ]);
            exit;
        }

        if ($action === 'update') {
            $productId   = intval($_POST['product_id'] ?? 0);
            $name        = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category    = trim($_POST['category'] ?? '');
            $price       = floatval($_POST['price'] ?? 0);
            $stock       = intval($_POST['stock'] ?? 0);

            if (!$productId || empty($name) || empty($category) || $price < 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product data.']);
                exit;
            }

            // Verify ownership
            $stmt = $pdo->prepare("SELECT id, image_path FROM sales_products WHERE id = ? AND sales_person_id = ?");
            $stmt->execute([$productId, $salesPersonId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found.']);
                exit;
            }

            $imagePath = $product['image_path'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload = handleImageUpload($_FILES['image']);
                if (!$upload['success']) {
                    echo json_encode(['success' => false, 'message' => $upload['message']]);
                    exit;
                }
                // Remove old image
                if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
                    unlink(__DIR__ . '/../' . $imagePath);
                }
                $imagePath = $upload['path'];
            }

            $stmt = $pdo->prepare(
                "UPDATE sales_products
                 SET name = ?, description = ?, category = ?, price = ?, stock = ?, image_path = ?, updated_at = NOW()
                 WHERE id = ? AND sales_person_id = ?"
            );
            $stmt->execute([$name, $description, $category, $price, $stock, $imagePath, $productId, $salesPersonId]);

            echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
            exit;
        }
    }

    // ── JSON body (delete / toggle) ───────────────────────────
    if (strpos($contentType, 'application/json') !== false) {
        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $body['action'] ?? '';

        if ($action === 'delete') {
            $productId = intval($body['product_id'] ?? 0);
            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT id, image_path FROM sales_products WHERE id = ? AND sales_person_id = ?");
            $stmt->execute([$productId, $salesPersonId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found.']);
                exit;
            }

            if ($product['image_path'] && file_exists(__DIR__ . '/../' . $product['image_path'])) {
                unlink(__DIR__ . '/../' . $product['image_path']);
            }

            $stmt = $pdo->prepare("DELETE FROM sales_products WHERE id = ? AND sales_person_id = ?");
            $stmt->execute([$productId, $salesPersonId]);

            echo json_encode(['success' => true, 'message' => 'Product deleted.']);
            exit;
        }

        if ($action === 'toggle') {
            $productId = intval($body['product_id'] ?? 0);
            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT id, is_active FROM sales_products WHERE id = ? AND sales_person_id = ?");
            $stmt->execute([$productId, $salesPersonId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found.']);
                exit;
            }

            $newStatus = $product['is_active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE sales_products SET is_active = ?, updated_at = NOW() WHERE id = ? AND sales_person_id = ?");
            $stmt->execute([$newStatus, $productId, $salesPersonId]);

            echo json_encode([
                'success'    => true,
                'message'    => $newStatus ? 'Product activated.' : 'Product deactivated.',
                'is_active'  => $newStatus,
            ]);
            exit;
        }
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action or invalid request.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);

// ============================================================
// HELPERS
// ============================================================
function handleImageUpload(array $file): array
{
    $allowed  = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $maxBytes = 5 * 1024 * 1024; // 5 MB

    if (!in_array($file['type'], $allowed, true)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and WEBP are allowed.'];
    }
    if ($file['size'] > $maxBytes) {
        return ['success' => false, 'message' => 'File too large. Maximum size is 5 MB.'];
    }

    $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename  = 'sp_' . uniqid() . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/products/';
    $destPath  = $uploadDir . $filename;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['success' => false, 'message' => 'Failed to save uploaded file.'];
    }

    return ['success' => true, 'path' => 'uploads/products/' . $filename];
}
