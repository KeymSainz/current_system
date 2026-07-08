<?php
/**
 * Fix&Go — Phone Technician Dashboard API
 *
 * GET  ?action=stats          → key metrics (repairs, revenue, inventory, messages)
 * GET  ?action=inventory      → products in technician's inventory
 * GET  ?action=inventory_stats→ inventory summary counts
 * GET  ?action=repairs        → repair bookings list
 * GET  ?action=products       → displayed products (public catalog)
 * POST action=toggle_display  → toggle product visibility to customers
 * POST action=update_repair   → update repair booking status
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (
    empty($_SESSION['user_id']) ||
    empty($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'phone_technician'
) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Technician access required.']);
    exit;
}

$techId = (int) $_SESSION['user_id'];
$pdo    = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method = $_SERVER['REQUEST_METHOD'];

/* ── Auto-migrate payment columns on every request ──────────── */
try {
    $pdo->exec("ALTER TABLE bookings
        ADD COLUMN IF NOT EXISTS repair_fee          DECIMAL(10,2)  NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS labor_fee           DECIMAL(10,2)  NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS parts_fee           DECIMAL(10,2)  NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS payment_method      ENUM('cash','bank_transfer','gcash','maya','other') NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS payment_status      ENUM('unpaid','paid','pending_collection') NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS payment_note        VARCHAR(255)   NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS receipt_path        VARCHAR(500)   NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS price_photo_path    VARCHAR(500)   NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS total_amount        DECIMAL(10,2)  NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS customer_payment_method ENUM('cash','gcash','maya','bank_transfer','card','online','other') NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS customer_payment_status ENUM('pending','paid') NOT NULL DEFAULT 'pending',
        ADD COLUMN IF NOT EXISTS customer_paid_at    DATETIME       NULL DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS parts_replaced      TEXT           NULL DEFAULT NULL");
} catch (\Exception $me) { /* columns already exist */ }

/* ── helper: check if a column/table exists ─────────────────── */
function techColExists(PDO $pdo, string $table, string $col): bool {
    $r = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS
                        WHERE TABLE_SCHEMA = DATABASE()
                          AND TABLE_NAME   = ?
                          AND COLUMN_NAME  = ?");
    $r->execute([$table, $col]);
    return (bool) $r->fetchColumn();
}

function techTableExists(PDO $pdo, string $table): bool {
    $r = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES
                        WHERE TABLE_SCHEMA = DATABASE()
                          AND TABLE_NAME   = ?");
    $r->execute([$table]);
    return (bool) $r->fetchColumn();
}

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'stats';

    // ── Dashboard stats ───────────────────────────────────────
    if ($action === 'stats') {
        $stats = [
            'total_repairs'     => 0,
            'repairs_today'     => 0,
            'pending_repairs'   => 0,
            'completed_repairs' => 0,
            'total_revenue'     => 0,
            'inventory_items'   => 0,
            'low_stock'         => 0,
            'displayed_products'=> 0,
            'unread_messages'   => 0,
        ];

        // Repair/booking stats
        if (techTableExists($pdo, 'bookings')) {
            try {
                $stmt = $pdo->prepare(
                    "SELECT
                        COUNT(*)                                                              AS total_repairs,
                        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END)        AS repairs_today,
                        SUM(CASE WHEN status IN ('pending','confirmed') THEN 1 ELSE 0 END)   AS pending_repairs,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END)                AS completed_repairs,
                        COALESCE(SUM(CASE WHEN status = 'completed' THEN COALESCE(total_amount,0) ELSE 0 END), 0) AS total_revenue
                     FROM bookings
                     WHERE technician_id = ?"
                );
                $stmt->execute([$techId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $stats['total_repairs']     = (int)$row['total_repairs'];
                    $stats['repairs_today']     = (int)$row['repairs_today'];
                    $stats['pending_repairs']   = (int)$row['pending_repairs'];
                    $stats['completed_repairs'] = (int)$row['completed_repairs'];
                    $stats['total_revenue']     = (float)$row['total_revenue'];
                }
            } catch (Exception $e) {
                error_log('[tech stats bookings] ' . $e->getMessage());
            }
        }

        // Inventory stats
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    COUNT(*)                                                   AS total_items,
                    SUM(CASE WHEN qty > 0 AND qty < 10 THEN 1 ELSE 0 END)     AS low_stock,
                    SUM(CASE WHEN is_displayed = 1 THEN 1 ELSE 0 END)         AS displayed
                 FROM supplier_products
                 WHERE current_holder_id = ? AND holder_type = 'phone_technician'"
            );
            $stmt->execute([$techId]);
            $inv = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($inv) {
                $stats['inventory_items']    = (int)$inv['total_items'];
                $stats['low_stock']          = (int)$inv['low_stock'];
                $stats['displayed_products'] = (int)$inv['displayed'];
            }
        } catch (Exception $e) {
            error_log('[tech stats inventory] ' . $e->getMessage());
        }

        // Unread messages
        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM messages m
                 JOIN conversations c ON c.id = m.conversation_id
                 WHERE (c.user_a_id = ? OR c.user_b_id = ?)
                   AND m.sender_id != ?
                   AND m.is_read = 0"
            );
            $stmt->execute([$techId, $techId, $techId]);
            $stats['unread_messages'] = (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            error_log('[tech stats messages] ' . $e->getMessage());
        }

        echo json_encode(['success' => true, 'stats' => $stats]);
        exit;
    }

    // ── Inventory list ────────────────────────────────────────
    if ($action === 'inventory') {
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    sp.id,
                    sp.category,
                    COALESCE(sp.brand, '') AS brand,
                    sp.item_description   AS name,
                    sp.qty                AS quantity,
                    sp.srp                AS price,
                    COALESCE(sp.image_path, '') AS image_path,
                    sp.status,
                    COALESCE(sp.is_displayed, 0) AS is_displayed,
                    sp.updated_at         AS last_updated
                 FROM supplier_products sp
                 WHERE sp.current_holder_id = ? AND sp.holder_type = 'phone_technician'
                 ORDER BY sp.updated_at DESC"
            );
            $stmt->execute([$techId]);
            echo json_encode(['success' => true, 'items' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            error_log('[tech inventory] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Inventory stats ───────────────────────────────────────
    if ($action === 'inventory_stats') {
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    COUNT(*)                                                    AS total_items,
                    SUM(CASE WHEN qty > 0  THEN 1 ELSE 0 END)                  AS in_stock,
                    SUM(CASE WHEN qty > 0 AND qty < 10 THEN 1 ELSE 0 END)      AS low_stock,
                    SUM(CASE WHEN qty = 0  THEN 1 ELSE 0 END)                  AS out_of_stock,
                    SUM(CASE WHEN is_displayed = 1 THEN 1 ELSE 0 END)          AS displayed,
                    SUM(qty)                                                    AS total_units
                 FROM supplier_products
                 WHERE current_holder_id = ? AND holder_type = 'phone_technician'"
            );
            $stmt->execute([$techId]);
            echo json_encode(['success' => true, 'stats' => $stmt->fetch(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Repair bookings ───────────────────────────────────────
    if ($action === 'repairs') {
        if (!techTableExists($pdo, 'bookings')) {
            echo json_encode(['success' => true, 'repairs' => []]);
            exit;
        }
        try {
            $status = $_GET['status'] ?? 'all';

            $addrLine = techColExists($pdo, 'users', 'address_line') ? 'u.address_line' : "'' AS address_line";
            $city     = techColExists($pdo, 'users', 'city')         ? 'u.city'         : "'' AS city";
            $phone    = techColExists($pdo, 'users', 'phone')        ? 'u.phone'        : "'' AS phone";
            $amount   = techColExists($pdo, 'bookings', 'total_amount') ? 'b.total_amount' : "0 AS total_amount";
            $notes    = techColExists($pdo, 'bookings', 'notes')     ? 'b.notes'        : "'' AS notes";
            $device   = techColExists($pdo, 'bookings', 'device_model') ? 'b.device_model' : "'' AS device_model";
            $issue    = techColExists($pdo, 'bookings', 'issue_description') ? 'b.issue_description' : "'' AS issue_description";
            $contact  = techColExists($pdo, 'bookings', 'contact_number')    ? 'b.contact_number'    : "'' AS contact_number";
            $addr     = techColExists($pdo, 'bookings', 'address')           ? 'b.address'           : "'' AS address";
            $devName  = techColExists($pdo, 'bookings', 'device_name')       ? 'b.device_name'       : "'' AS device_name";
            $fault    = techColExists($pdo, 'bookings', 'fault_description') ? 'b.fault_description' : "'' AS fault_description";
            $history  = techColExists($pdo, 'bookings', 'phone_history')     ? 'b.phone_history'     : "'' AS phone_history";
            $expected = techColExists($pdo, 'bookings', 'expected_fix')      ? 'b.expected_fix'      : "'' AS expected_fix";
            $svcType  = techColExists($pdo, 'bookings', 'service_type')      ? 'COALESCE(b.service_type, \'shop_fix\') AS service_type' : "'shop_fix' AS service_type";

            $sql = "SELECT
                        b.id,
                        b.status,
                        b.created_at,
                        b.updated_at,
                        b.scheduled_at,
                        $amount,
                        $notes,
                        $device,
                        $issue,
                        $contact,
                        $addr,
                        $devName,
                        $fault,
                        $history,
                        $expected,
                        $svcType,
                        COALESCE(b.repair_fee, 0)         AS repair_fee,
                        COALESCE(b.labor_fee, 0)          AS labor_fee,
                        COALESCE(b.parts_fee, 0)          AS parts_fee,
                        b.payment_method,
                        b.payment_status,
                        b.receipt_path,
                        b.price_photo_path,
                        b.parts_replaced,
                        b.customer_payment_status,
                        u.id         AS customer_id,
                        u.first_name,
                        u.last_name,
                        u.email      AS customer_email,
                        $phone       AS customer_phone,
                        $addrLine,
                        $city
                    FROM bookings b
                    JOIN users u ON u.id = b.customer_id
                    WHERE b.technician_id = ?";
            $params = [$techId];

            if ($status !== 'all') {
                $sql     .= " AND b.status = ?";
                $params[] = $status;
            }
            $sql .= " ORDER BY b.created_at DESC LIMIT 200";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['success' => true, 'repairs' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            error_log('[tech repairs] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Payment history ───────────────────────────────────────
    if ($action === 'payment_history') {
        if (!techTableExists($pdo, 'bookings')) {
            echo json_encode(['success' => true, 'payments' => [], 'summary' => []]);
            exit;
        }
        try {
            // Columns are guaranteed by auto-migrate at top of file
            $hasPhone = techColExists($pdo, 'users', 'phone');
            $phoneCol = $hasPhone ? 'u.phone AS customer_phone' : "NULL AS customer_phone";

            $stmt = $pdo->prepare(
                "SELECT
                    b.id,
                    b.status,
                    b.updated_at                                          AS paid_at,
                    b.created_at,
                    COALESCE(b.total_amount, b.repair_fee, 0)             AS total_amount,
                    b.repair_fee,
                    b.labor_fee,
                    b.parts_fee,
                    b.payment_method,
                    b.payment_note,
                    b.payment_status,
                    b.receipt_path,
                    b.price_photo_path,
                    b.customer_payment_method,
                    b.customer_payment_status,
                    CONCAT(u.first_name,' ',u.last_name)                  AS customer_name,
                    u.email                                               AS customer_email,
                    $phoneCol,
                    COALESCE(b.device_name,'')                            AS device_name,
                    COALESCE(b.fault_description,'')                      AS fault_description
                 FROM bookings b
                 JOIN users u ON u.id = b.customer_id
                 WHERE b.technician_id = ?
                   AND b.status = 'completed'
                 ORDER BY b.updated_at DESC
                 LIMIT 500"
            );
            $stmt->execute([$techId]);
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total   = count($payments);
            $revenue = array_sum(array_column($payments, 'total_amount'));
            $byMethod = [];
            foreach ($payments as $p) {
                $m = $p['payment_method'] ?? ($p['customer_payment_method'] ?? 'pending');
                if (!isset($byMethod[$m])) $byMethod[$m] = ['count' => 0, 'amount' => 0];
                $byMethod[$m]['count']++;
                $byMethod[$m]['amount'] += (float)($p['total_amount'] ?? 0);
            }

            echo json_encode([
                'success'  => true,
                'payments' => $payments,
                'summary'  => [
                    'total_transactions' => $total,
                    'total_revenue'      => $revenue,
                    'by_method'          => $byMethod,
                ],
            ]);
        } catch (Exception $e) {
            error_log('[tech payment_history] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Displayed products (public catalog) ───────────────────
    if ($action === 'products') {
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    sp.id,
                    sp.category,
                    COALESCE(sp.brand, '') AS brand,
                    sp.item_description   AS name,
                    sp.qty                AS quantity,
                    sp.srp                AS price,
                    COALESCE(sp.image_path, '') AS image_path,
                    sp.is_displayed,
                    sp.updated_at
                 FROM supplier_products sp
                 WHERE sp.current_holder_id = ?
                   AND sp.holder_type = 'phone_technician'
                   AND sp.is_displayed = 1
                 ORDER BY sp.updated_at DESC"
            );
            $stmt->execute([$techId]);
            echo json_encode(['success' => true, 'products' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Profile ───────────────────────────────────────────────
    if ($action === 'profile') {
        try {
            $stmt = $pdo->prepare(
                "SELECT u.id, u.first_name, u.last_name, u.email, u.phone,
                        COALESCE(u.profile_image, u.avatar_url, '') AS avatar_url,
                        COALESCE(u.bio, '') AS bio,
                        COALESCE(u.specializations, '') AS specializations,
                        COALESCE(u.shop_name, '') AS shop_name,
                        COALESCE(u.address_line, '') AS address_line,
                        COALESCE(u.barangay, '') AS barangay,
                        COALESCE(u.city, '') AS city,
                        COALESCE(u.province, '') AS province,
                        COALESCE(u.zip_code, '') AS zip_code,
                        u.created_at
                 FROM users u
                 WHERE u.id = ? AND u.role = 'phone_technician'"
            );
            $stmt->execute([$techId]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$profile) {
                echo json_encode(['success' => false, 'message' => 'Profile not found.']);
                exit;
            }
            echo json_encode(['success' => true, 'profile' => $profile]);
        } catch (Exception $e) {
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
    // Detect multipart vs JSON
    $isMultipart = !empty($_FILES) || !empty($_POST);
    if ($isMultipart) {
        $body   = $_POST;
        $action = $body['action'] ?? '';
    } else {
        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $body['action'] ?? '';
    }

    // ── Upload shop image ─────────────────────────────────────
    if ($action === 'upload_shop_image') {
        if (empty($_FILES['shop_image']['tmp_name'])) {
            echo json_encode(['success'=>false,'message'=>'No image uploaded.']);
            exit;
        }
        $file     = $_FILES['shop_image'];
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed  = ['image/jpeg','image/png','image/webp','image/gif'];
        if (!in_array($mimeType, $allowed)) {
            echo json_encode(['success'=>false,'message'=>'Invalid image type.']);
            exit;
        }
        if ($file['size'] > 5*1024*1024) {
            echo json_encode(['success'=>false,'message'=>'Image too large. Max 5MB.']);
            exit;
        }
        $uploadDir = __DIR__ . '/../uploads/shop_images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'shop_' . $techId . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            echo json_encode(['success'=>false,'message'=>'Failed to save image.']);
            exit;
        }
        $relativePath = 'uploads/shop_images/' . $filename;
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS shop_image VARCHAR(500) NULL");
        } catch (Exception $e) { /* already exists */ }
        $pdo->prepare("UPDATE users SET shop_image=? WHERE id=?")->execute([$relativePath, $techId]);
        echo json_encode(['success'=>true,'message'=>'Shop image updated.','shop_image'=>$relativePath]);
        exit;
    }

    // ── Toggle product display ────────────────────────────────
    if ($action === 'toggle_display') {
        $productId = (int)($body['product_id'] ?? 0);
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID required.']);
            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT id, is_displayed FROM supplier_products
             WHERE id = ? AND current_holder_id = ? AND holder_type = 'phone_technician'"
        );
        $stmt->execute([$productId, $techId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found in your inventory.']);
            exit;
        }

        $newVal = $product['is_displayed'] ? 0 : 1;
        $pdo->prepare(
            "UPDATE supplier_products SET is_displayed = ?, updated_at = NOW() WHERE id = ?"
        )->execute([$newVal, $productId]);

        echo json_encode([
            'success'      => true,
            'is_displayed' => $newVal,
            'message'      => $newVal ? 'Product is now visible to customers.' : 'Product hidden from customers.',
        ]);
        exit;
    }

    // ── Update product (price, quantity, image) ───────────────
    if ($action === 'update_product') {
        $productId = (int)($body['product_id'] ?? 0);
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID required.']);
            exit;
        }

        // Verify ownership
        $stmt = $pdo->prepare(
            "SELECT id, image_path FROM supplier_products
             WHERE id = ? AND current_holder_id = ? AND holder_type = 'phone_technician'"
        );
        $stmt->execute([$productId, $techId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            echo json_encode(['success' => false, 'message' => 'Product not found in your inventory.']);
            exit;
        }

        $setClauses = "updated_at = NOW()";
        $params     = [];

        // Price
        if (isset($body['price']) && $body['price'] !== '') {
            $price = (float)$body['price'];
            if ($price < 0) { echo json_encode(['success' => false, 'message' => 'Price cannot be negative.']); exit; }
            $setClauses .= ", srp = ?";
            $params[]    = $price;
        }

        // Quantity
        if (isset($body['quantity']) && $body['quantity'] !== '') {
            $qty = (int)$body['quantity'];
            if ($qty < 0) { echo json_encode(['success' => false, 'message' => 'Quantity cannot be negative.']); exit; }
            $setClauses .= ", qty = ?";
            $params[]    = $qty;
        }

        // Image upload
        $imagePath = null;
        if (!empty($_FILES['image']['tmp_name'])) {
            $file     = $_FILES['image'];
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($mimeType, $allowed)) {
                echo json_encode(['success' => false, 'message' => 'Invalid image type. Use JPG, PNG, or WebP.']);
                exit;
            }
            if ($file['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'Image too large. Max 5MB.']);
                exit;
            }
            $uploadDir = __DIR__ . '/../uploads/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'product_' . $techId . '_' . $productId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $imagePath   = 'uploads/products/' . $filename;
                $setClauses .= ", image_path = ?";
                $params[]    = $imagePath;
            }
        }

        $params[] = $productId;
        $pdo->prepare("UPDATE supplier_products SET $setClauses WHERE id = ?")->execute($params);

        // Return updated product row
        $updated = $pdo->prepare(
            "SELECT id, item_description AS name, category, brand, qty AS quantity, srp AS price, image_path, is_displayed
             FROM supplier_products WHERE id = ?"
        );
        $updated->execute([$productId]);
        $row = $updated->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'message' => 'Product updated successfully.', 'product' => $row]);
        exit;
    }

    // ── Update repair status ──────────────────────────────────
    if ($action === 'update_repair') {
        if (!techTableExists($pdo, 'bookings')) {
            echo json_encode(['success' => false, 'message' => 'Bookings table not found.']);
            exit;
        }

        $repairId      = (int)($body['repair_id']      ?? 0);
        $newStatus     = trim($body['status']          ?? '');
        $paymentMethod = trim($body['payment_method']  ?? '');
        $repairFee     = isset($body['repair_fee']) && $body['repair_fee'] !== '' ? (float)$body['repair_fee'] : null;
        $paymentNote   = trim($body['payment_note']    ?? '');
        $paymentStatus = trim($body['payment_status']  ?? '');
        $laborFee      = isset($body['labor_fee'])  && $body['labor_fee']  !== '' ? (float)$body['labor_fee']  : null;
        $partsFee      = isset($body['parts_fee'])  && $body['parts_fee']  !== '' ? (float)$body['parts_fee']  : null;
        $partsReplaced = trim($body['parts_replaced'] ?? '');
        $cancelReason  = trim($body['cancel_reason']  ?? '');
        $allowed       = ['confirmed', 'in_progress', 'completed', 'cancelled'];

        $allowedPayment = ['cash', 'bank_transfer', 'gcash', 'maya', 'other', ''];
        $allowedPaymentStatus = ['paid', 'pending_collection', 'unpaid', ''];

        if (!$repairId || !in_array($newStatus, $allowed, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid repair ID or status.']);
            exit;
        }
        if ($paymentMethod && !in_array($paymentMethod, $allowedPayment, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid payment method.']);
            exit;
        }
        if ($paymentStatus && !in_array($paymentStatus, $allowedPaymentStatus, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid payment status.']);
            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT b.id, b.status, b.customer_id FROM bookings b
             WHERE b.id = ? AND b.technician_id = ?"
        );
        $stmt->execute([$repairId, $techId]);
        $repair = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$repair) {
            echo json_encode(['success' => false, 'message' => 'Repair not found.']);
            exit;
        }

        // ── Handle receipt upload ─────────────────────────────
        $receiptPath = null;
        if (!empty($_FILES['receipt']['tmp_name'])) {
            $file     = $_FILES['receipt'];
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'];
            if (!in_array($mimeType, $allowed_mimes)) {
                echo json_encode(['success' => false, 'message' => 'Invalid receipt file type. Use JPG, PNG, WebP, or PDF.']);
                exit;
            }
            if ($file['size'] > 10 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'Receipt file too large. Max 10MB.']);
                exit;
            }
            $uploadDir = __DIR__ . '/../uploads/receipts/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'receipt_' . $techId . '_' . $repairId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $receiptPath = 'uploads/receipts/' . $filename;
            }
        }

        // ── Handle price photo upload ─────────────────────────
        $pricePhotoPath = null;
        if (!empty($_FILES['price_photo']['tmp_name'])) {
            $pf       = $_FILES['price_photo'];
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($pf['tmp_name']);
            $allowed_mimes = ['image/jpeg','image/png','image/webp','image/gif','video/mp4','video/webm'];
            if (in_array($mimeType, $allowed_mimes) && $pf['size'] <= 20 * 1024 * 1024) {
                $uploadDir = __DIR__ . '/../uploads/price_photos/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext      = strtolower(pathinfo($pf['name'], PATHINFO_EXTENSION));
                $filename = 'price_' . $techId . '_' . $repairId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($pf['tmp_name'], $uploadDir . $filename)) {
                    $pricePhotoPath = 'uploads/price_photos/' . $filename;
                }
            }
        }

        // ── Build UPDATE — columns are guaranteed by auto-migrate at top ──
        try {
            $setClauses = "status = ?, updated_at = NOW()";
            $params     = [$newStatus];

            if ($paymentMethod)    { $setClauses .= ", payment_method = ?";           $params[] = $paymentMethod; }
            if ($laborFee !== null){ $setClauses .= ", labor_fee = ?";                $params[] = $laborFee; }
            if ($partsFee !== null){ $setClauses .= ", parts_fee = ?";                $params[] = $partsFee; }
            if ($repairFee !== null){
                $setClauses .= ", repair_fee = ?, total_amount = ?";
                $params[] = $repairFee;
                $params[] = $repairFee;
            }
            if ($paymentNote)      { $setClauses .= ", payment_note = ?";             $params[] = $paymentNote; }
            if ($paymentStatus)    { $setClauses .= ", payment_status = ?";           $params[] = $paymentStatus; }
            if ($receiptPath)      { $setClauses .= ", receipt_path = ?";             $params[] = $receiptPath; }
            if ($pricePhotoPath)   { $setClauses .= ", price_photo_path = ?";         $params[] = $pricePhotoPath; }
            if ($partsReplaced)    { $setClauses .= ", parts_replaced = ?";            $params[] = $partsReplaced; }
            if ($newStatus === 'cancelled' && $cancelReason !== '') {
                // Store cancel reason — add column if missing
                try { $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS cancel_reason VARCHAR(500) NULL"); } catch (\Exception $e) {}
                $setClauses .= ", cancel_reason = ?";
                $params[] = $cancelReason;
            }

            $params[] = $repairId;
            $pdo->prepare("UPDATE bookings SET $setClauses WHERE id = ?")->execute($params);

        } catch (\Exception $upEx) {
            error_log('[tech update_repair] ' . $upEx->getMessage());
            $pdo->prepare("UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?")->execute([$newStatus, $repairId]);
        }

        // ── Deduct inventory stock when repair is completed ────
        if ($newStatus === 'completed' && $partsReplaced) {
            try {
                $parts = json_decode($partsReplaced, true);
                if (is_array($parts)) {
                    foreach ($parts as $part) {
                        $invId  = isset($part['inventory_id']) ? (int)$part['inventory_id'] : 0;
                        $usedQty = isset($part['qty'])         ? (int)$part['qty']          : 1;
                        if ($invId > 0 && $usedQty > 0) {
                            // Only deduct from this technician's own inventory
                            $pdo->prepare(
                                "UPDATE supplier_products
                                 SET qty = GREATEST(0, qty - ?), updated_at = NOW()
                                 WHERE id = ? AND current_holder_id = ? AND holder_type = 'phone_technician'"
                            )->execute([$usedQty, $invId, $techId]);
                        }
                    }
                }
            } catch (\Exception $invEx) {
                error_log('[tech inventory deduct] ' . $invEx->getMessage());
                // Non-fatal — repair still marked complete
            }
        }

        // ── Auto-create conversation on confirmation ───────────
        if ($newStatus === 'confirmed') {
            try {
                // Get booking details for the message
                $bStmt = $pdo->prepare(
                    "SELECT b.device_name, b.fault_description, b.scheduled_at,
                            COALESCE(b.service_type, 'shop_fix') AS service_type,
                            COALESCE(b.contact_number, '') AS contact_number,
                            CONCAT(u.first_name,' ',u.last_name) AS tech_name,
                            COALESCE(u.shop_name, CONCAT(u.first_name,' ',u.last_name)) AS shop_name,
                            COALESCE(u.phone, '') AS tech_phone,
                            COALESCE(u.address_line, '') AS tech_address_line,
                            COALESCE(u.barangay, '') AS tech_barangay,
                            COALESCE(u.city, '') AS tech_city,
                            COALESCE(u.province, '') AS tech_province,
                            COALESCE(u.zip_code, '') AS tech_zip
                     FROM bookings b
                     JOIN users u ON u.id = b.technician_id
                     WHERE b.id = ?"
                );
                $bStmt->execute([$repairId]);
                $bookingInfo = $bStmt->fetch(PDO::FETCH_ASSOC);

                $customerId = $repair['customer_id'];
                $a    = min((int)$customerId, (int)$techId);
                $b_id = max((int)$customerId, (int)$techId);

                // Get or create conversation
                $convCheck = $pdo->prepare(
                    "SELECT id FROM conversations WHERE user_a_id = ? AND user_b_id = ?"
                );
                $convCheck->execute([$a, $b_id]);
                $conv = $convCheck->fetch(PDO::FETCH_ASSOC);

                if (!$conv) {
                    $pdo->prepare(
                        "INSERT INTO conversations (user_a_id, user_b_id) VALUES (?, ?)"
                    )->execute([$a, $b_id]);
                    $convId = (int)$pdo->lastInsertId();
                } else {
                    $convId = (int)$conv['id'];
                }

                // Build service-type-aware confirmation message
                $device      = $bookingInfo['device_name'] ?? 'your device';
                $techName    = $bookingInfo['shop_name']   ?? 'Your Technician';
                $serviceType = $bookingInfo['service_type'] ?? 'shop_fix';
                $schedMsg    = $bookingInfo['scheduled_at']
                    ? "\n📅 Scheduled: " . date('M j, Y g:i A', strtotime($bookingInfo['scheduled_at']))
                    : '';

                if ($serviceType === 'home_service') {
                    // Home service — technician goes to customer
                    $autoMsg = "Hi! Great news — I've confirmed your repair booking #{$repairId} for your {$device}! 🎉"
                             . "\n\n🏠 Service Type: **Home Service**"
                             . "\nI will visit you at your address to perform the repair."
                             . $schedMsg
                             . "\n\nPlease make sure you're available at home at the scheduled time. I'll message you before I head over."
                             . "\n\nFeel free to message me here if you have any questions. Looking forward to fixing your device!";
                } else {
                    // In-shop fix — customer brings device
                    $addrParts = array_filter([
                        $bookingInfo['tech_address_line'] ?? '',
                        $bookingInfo['tech_barangay']     ?? '',
                        $bookingInfo['tech_city']         ?? '',
                        $bookingInfo['tech_province']     ?? '',
                        $bookingInfo['tech_zip']          ?? '',
                    ]);
                    $shopAddr  = !empty($addrParts) ? implode(', ', $addrParts) : 'our shop';
                    $techPhone = $bookingInfo['tech_phone'] ?? '';

                    $autoMsg = "Hi! Great news — I've confirmed your repair booking #{$repairId} for your {$device}! 🎉"
                             . "\n\n🏪 Service Type: **In-Shop Fix**"
                             . "\nPlease bring your device to our shop:"
                             . "\n📍 {$shopAddr}"
                             . ($techPhone ? "\n📞 {$techPhone}" : '')
                             . $schedMsg
                             . "\n\nKindly visit during our service hours. I'll have your device fixed as soon as possible!"
                             . "\n\nFeel free to message me here if you have any questions. See you soon!";
                }

                try {
                    $pdo->prepare(
                        "INSERT INTO messages (conversation_id, sender_id, body) VALUES (?, ?, ?)"
                    )->execute([$convId, $techId, $autoMsg]);
                } catch (Exception $colEx) {
                    $pdo->prepare(
                        "INSERT INTO messages (conversation_id, sender_id, body, sent_at) VALUES (?, ?, ?, NOW())"
                    )->execute([$convId, $techId, $autoMsg]);
                }
                $pdo->prepare(
                    "UPDATE conversations SET updated_at = NOW() WHERE id = ?"
                )->execute([$convId]);

            } catch (Exception $me) {
                error_log('[tech confirm auto-message] ' . $me->getMessage());
            }
        }

        // Notify customer
        try {
            require_once __DIR__ . '/notification_helper.php';
            $labels = [
                'confirmed'   => 'Repair Confirmed',
                'in_progress' => 'Repair In Progress',
                'completed'   => 'Repair Completed',
                'cancelled'   => 'Repair Cancelled',
            ];
            $bodies = [
                'confirmed'   => "Your repair booking #{$repairId} has been confirmed by the technician.",
                'in_progress' => "Your device repair #{$repairId} is now in progress.",
                'completed'   => "Your device repair #{$repairId} has been completed. Thank you!",
                'cancelled'   => "Your repair booking #{$repairId} has been cancelled.",
            ];
            sendNotification(
                $repair['customer_id'],
                'repair_update',
                $labels[$newStatus] ?? 'Repair Update',
                $bodies[$newStatus] ?? "Your repair #{$repairId} status has been updated to {$newStatus}."
            );
        } catch (Exception $ne) {
            error_log('[tech update_repair notify] ' . $ne->getMessage());
        }

        echo json_encode(['success' => true, 'message' => 'Repair status updated.']);
        exit;
    }

    // ── Update extended profile ───────────────────────────────
    if ($action === 'update_profile') {
        $firstName       = trim($body['first_name']      ?? '');
        $lastName        = trim($body['last_name']       ?? '');
        $email           = trim($body['email']           ?? '');
        $phone           = trim($body['phone']           ?? '');
        $bio             = trim($body['bio']             ?? '');
        $description     = trim($body['description']     ?? '');
        $specializations = trim($body['specializations'] ?? '');
        $shopName        = trim($body['shop_name']       ?? '');
        $addressLine     = trim($body['address_line']    ?? '');
        $barangay        = trim($body['barangay']        ?? '');
        $city            = trim($body['city']            ?? '');
        $province        = trim($body['province']        ?? '');
        $zipCode         = trim($body['zip_code']        ?? '');
        $experienceYears = max(0, min(99, (int)($body['experience_years'] ?? 0)));

        if (!$firstName || !$lastName || !$email) {
            echo json_encode(['success' => false, 'message' => 'First name, last name, and email are required.']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit;
        }

        try {
            // Check email uniqueness
            $dup = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $dup->execute([$email, $techId]);
            if ($dup->fetch()) {
                echo json_encode(['success' => false, 'message' => 'That email is already in use by another account.']);
                exit;
            }

            // Update users table — handle description column gracefully
            try {
                $pdo->prepare(
                    "UPDATE users
                     SET first_name = ?, last_name = ?, email = ?, phone = ?,
                         bio = ?, description = ?, specializations = ?, shop_name = ?,
                         address_line = ?, barangay = ?, city = ?, province = ?, zip_code = ?,
                         updated_at = NOW()
                     WHERE id = ?"
                )->execute([
                    $firstName, $lastName, $email, $phone ?: null,
                    $bio ?: null, $description ?: null,
                    $specializations ?: null, $shopName ?: null,
                    $addressLine ?: null, $barangay ?: null, $city ?: null,
                    $province ?: null, $zipCode ?: null, $techId
                ]);
            } catch (\Exception $colEx) {
                // Fallback without description column
                $pdo->prepare(
                    "UPDATE users
                     SET first_name = ?, last_name = ?, email = ?, phone = ?,
                         bio = ?, specializations = ?, shop_name = ?,
                         address_line = ?, barangay = ?, city = ?, province = ?, zip_code = ?,
                         updated_at = NOW()
                     WHERE id = ?"
                )->execute([
                    $firstName, $lastName, $email, $phone ?: null,
                    ($description ?: $bio) ?: null,
                    $specializations ?: null, $shopName ?: null,
                    $addressLine ?: null, $barangay ?: null, $city ?: null,
                    $province ?: null, $zipCode ?: null, $techId
                ]);
            }

            // Upsert experience_years into technician_profiles
            try {
                $pdo->prepare(
                    "INSERT INTO technician_profiles (user_id, experience_years)
                     VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE experience_years = ?"
                )->execute([$techId, $experienceYears, $experienceYears]);
            } catch (\Exception $tpEx) {
                error_log('[tech update_profile tp] ' . $tpEx->getMessage());
            }

            echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
        } catch (Exception $e) {
            error_log('[tech update_profile] ' . $e->getMessage());
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
