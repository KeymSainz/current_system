<?php
/**
 * Fix&Go — Supervisor Inventory Management API
 * Supervisors can view, add, edit, and delete products from owner's inventory
 *
 * GET  ?action=list      → list all products
 * GET  ?action=stats     → inventory statistics
 * POST action=add        → add new product
 * POST action=update     → update existing product
 * POST action=delete     → delete product
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

// ============================================================
// GET REQUESTS
// ============================================================
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    if ($action === 'list') {
        // Get all products held by this supervisor
        $stmt = $pdo->prepare(
            "SELECT 
                sp.id,
                sp.category,
                sp.brand,
                sp.item_description AS name,
                sp.item_description AS description,
                sp.srp              AS price,
                sp.qty              AS stock_quantity,
                sp.image_path,
                sp.status,
                sp.created_at       AS submitted_at,
                sp.updated_at,
                COALESCE(u.first_name, 'Unknown') AS first_name,
                COALESCE(u.last_name, 'Supplier') AS last_name,
                COALESCE(u.email, 'N/A') AS email
             FROM supplier_products sp
             LEFT JOIN users u ON sp.supplier_id = u.id
             WHERE sp.current_holder_id = ? AND sp.holder_type = 'supervisor'
             ORDER BY sp.updated_at DESC"
        );
        $stmt->execute([$supervisorId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debug logging
        error_log("Supervisor ID: " . $supervisorId);
        error_log("Products found: " . count($products));
        error_log("Products: " . json_encode($products));

        echo json_encode([
            'success'  => true,
            'products' => $products,
            'debug' => [
                'supervisor_id' => $supervisorId,
                'count' => count($products),
                'query' => 'WHERE sp.current_holder_id = ? AND sp.holder_type = \'supervisor\''
            ]
        ]);
        exit;
    }

    if ($action === 'stats') {
        $stmt = $pdo->query(
            "SELECT 
                COUNT(*) AS total_products,
                SUM(CASE WHEN qty > 0 THEN 1 ELSE 0 END) AS in_stock,
                SUM(CASE WHEN qty > 0 AND qty < 10 THEN 1 ELSE 0 END) AS low_stock,
                SUM(CASE WHEN qty = 0 THEN 1 ELSE 0 END) AS out_of_stock,
                SUM(srp * qty) AS total_value
             FROM supplier_products
             WHERE status = 'sent_to_supervisor'"
        );
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
    
    // Handle multipart/form-data (file upload)
    if (strpos($contentType, 'multipart/form-data') !== false) {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add') {
            // Add new product to supervisor inventory
            $name          = trim($_POST['name'] ?? '');
            $description   = trim($_POST['description'] ?? '');
            $category      = trim($_POST['category'] ?? '');
            $price         = floatval($_POST['price'] ?? 0);
            $stockQuantity = intval($_POST['stock_quantity'] ?? 0);

            if (empty($name) || empty($category) || $price <= 0) {
                echo json_encode(['success' => false, 'message' => 'Name, category, and price are required.']);
                exit;
            }

            // Handle image upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = handleImageUpload($_FILES['image']);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['path'];
                } else {
                    echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                    exit;
                }
            }

            // Insert product — supervisor is the supplier_id, status = sent_to_supervisor
            $stmt = $pdo->prepare(
                "INSERT INTO supplier_products 
                 (supplier_id, category, brand, item_description, qty, srp, image_path, status, created_at, updated_at)
                 VALUES (?, ?, '', ?, ?, ?, ?, 'sent_to_supervisor', NOW(), NOW())"
            );
            $stmt->execute([$supervisorId, $category, $name, $stockQuantity, $price, $imagePath]);

            echo json_encode([
                'success'    => true,
                'message'    => 'Product added successfully.',
                'product_id' => $pdo->lastInsertId(),
            ]);
            exit;
        }

        if ($action === 'update') {
            // Update existing product
            $productId = intval($_POST['product_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $stockQuantity = intval($_POST['stock_quantity'] ?? 0);

            if (!$productId || empty($name) || empty($category) || $price <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product data.']);
                exit;
            }

            // Check if product exists
            $stmt = $pdo->prepare(
                "SELECT sp.id, sp.image_path 
                 FROM supplier_products sp
                 WHERE sp.id = ?"
            );
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found.']);
                exit;
            }

            // Handle image upload
            $imagePath = $product['image_path'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = handleImageUpload($_FILES['image']);
                if ($uploadResult['success']) {
                    // Delete old image if exists
                    if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
                        unlink(__DIR__ . '/../' . $imagePath);
                    }
                    $imagePath = $uploadResult['path'];
                } else {
                    echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                    exit;
                }
            }

            // Update product using correct column names
            $stmt = $pdo->prepare(
                "UPDATE supplier_products 
                 SET category = ?, item_description = ?, qty = ?, srp = ?, 
                     image_path = ?, updated_at = NOW()
                 WHERE id = ?"
            );
            $stmt->execute([$category, $name, $stockQuantity, $price, $imagePath, $productId]);

            echo json_encode([
                'success' => true,
                'message' => 'Product updated successfully.'
            ]);
            exit;
        }
    }

    // Handle JSON requests (delete)
    if (strpos($contentType, 'application/json') !== false) {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $body['action'] ?? '';

        if ($action === 'send_to_sales_person') {
            $productIds = array_map('intval', $body['product_ids'] ?? []);

            if (empty($productIds)) {
                echo json_encode(['success' => false, 'message' => 'No products selected.']);
                exit;
            }

            // Update product status to 'sent_to_sales_person'
            // Works for any product currently in 'sent_to_supervisor' status
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $stmt = $pdo->prepare(
                "UPDATE supplier_products
                 SET status = 'sent_to_sales_person', updated_at = NOW()
                 WHERE id IN ($placeholders)
                   AND status = 'sent_to_supervisor'"
            );
            $stmt->execute($productIds);
            $updatedCount = $stmt->rowCount();

            if ($updatedCount > 0) {
                echo json_encode([
                    'success'       => true,
                    'message'       => "$updatedCount product(s) sent to Sales Person successfully.",
                    'updated_count' => $updatedCount,
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No products were updated. They may have already been sent or do not exist.',
                ]);
            }
            exit;
        }

        if ($action === 'delete') {
            $productId = intval($body['product_id'] ?? 0);

            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
                exit;
            }

            // Check if product exists
            $stmt = $pdo->prepare(
                "SELECT sp.id, sp.image_path 
                 FROM supplier_products sp
                 WHERE sp.id = ? AND sp.status = 'sent_to_supervisor'"
            );
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found.']);
                exit;
            }

            // Delete image file if exists
            if ($product['image_path'] && file_exists(__DIR__ . '/../' . $product['image_path'])) {
                unlink(__DIR__ . '/../' . $product['image_path']);
            }

            // Delete product
            $stmt = $pdo->prepare("DELETE FROM supplier_products WHERE id = ?");
            $stmt->execute([$productId]);

            echo json_encode([
                'success' => true,
                'message' => 'Product deleted successfully.'
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
// HELPER FUNCTIONS
// ============================================================

function handleImageUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and WEBP are allowed.'];
    }

    // Validate file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large. Maximum size is 5MB.'];
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'prod_' . uniqid() . '.' . $extension;
    $uploadDir = __DIR__ . '/../uploads/products/';
    $uploadPath = $uploadDir . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return [
            'success' => true,
            'path' => 'uploads/products/' . $filename
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to upload file.'];
    }
}
