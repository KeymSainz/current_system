<?php
/**
 * Fix&Go — Repair Bookings API
 *
 * PUBLIC (no auth):
 *   GET  ?action=technician_profile&id=N  → full technician profile + docs + ratings
 *
 * AUTH REQUIRED (customer):
 *   POST action=book (multipart)           → submit repair booking form (with optional phone photo)
 *   GET  ?action=my_bookings               → customer's booking history
 *   POST action=cancel                     → cancel a pending booking
 *
 * AUTH REQUIRED (technician):
 *   POST action=upload_shop_image (multipart) → upload shop image
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');
error_reporting(0);

$pdo    = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method = $_SERVER['REQUEST_METHOD'];

// Detect action from GET or POST body/form
$action = $_GET['action'] ?? '';
if (!$action && $method === 'POST') {
    $action = $_POST['action'] ?? (json_decode(file_get_contents('php://input'), true)['action'] ?? '');
}

// ── Helper: resolve file path to full URL ─────────────────────
function resolveDocUrl(string $path): string {
    if (empty($path)) return '';
    // Already a full URL
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
    // Relative path — prepend app base URL
    $cfg  = require __DIR__ . '/config.php';
    $base = rtrim($cfg['app_url'], '/') . '/';
    return $base . ltrim($path, '/');
}

// ── PUBLIC: Technician profile ────────────────────────────────
if ($method === 'GET' && $action === 'technician_profile') {
    $techId = (int)($_GET['id'] ?? 0);
    if (!$techId) { echo json_encode(['success'=>false,'message'=>'ID required.']); exit; }

    try {
        // Check if shop_image column exists
        $hasShopImg = (bool)$pdo->query(
            "SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='shop_image'"
        )->fetchColumn();

        $shopImgCol = $hasShopImg ? "COALESCE(u.shop_image,'')" : "''";

        $stmt = $pdo->prepare(
            "SELECT
                u.id,
                u.first_name,
                u.last_name,
                COALESCE(u.profile_image, u.avatar_url, '') AS avatar_url,
                {$shopImgCol}                               AS shop_image,
                COALESCE(u.bio, tp.bio, '')                 AS bio,
                COALESCE(u.specializations, tp.specialization, '') AS specializations,
                COALESCE(u.shop_name, '')                   AS shop_name,
                COALESCE(u.phone, '')                       AS phone,
                COALESCE(u.address_line, '')                AS address_line,
                COALESCE(u.barangay, '')                    AS barangay,
                COALESCE(u.city, '')                        AS city,
                COALESCE(u.province, '')                    AS province,
                COALESCE(u.zip_code, '')                    AS zip_code,
                COALESCE(tp.experience_years, 0)            AS experience_years,
                COALESCE(tp.availability, 'available')      AS availability,
                COALESCE(tp.rating_avg, 0.00)               AS rating_avg,
                COALESCE(tp.rating_count, 0)                AS rating_count,
                COALESCE(tp.certifications, '')             AS certifications,
                u.created_at
             FROM users u
             LEFT JOIN technician_profiles tp ON tp.user_id = u.id
             WHERE u.id = ? AND u.role = 'phone_technician' AND u.is_active = 1"
        );
        $stmt->execute([$techId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$profile) { echo json_encode(['success'=>false,'message'=>'Technician not found.']); exit; }

        // Fetch description column safely — it may not exist on all installs
        try {
            $descStmt = $pdo->prepare("SELECT description FROM users WHERE id = ?");
            $descStmt->execute([$techId]);
            $descRow = $descStmt->fetch(PDO::FETCH_ASSOC);
            $profile['description'] = $descRow['description'] ?? '';
        } catch (Exception $e) {
            $profile['description'] = '';
        }
        // Ensure experience_years_direct is set (same value as experience_years for JS compat)
        $profile['experience_years_direct'] = $profile['experience_years'];

        // Resolve avatar URL
        if (!empty($profile['avatar_url'])) {
            $profile['avatar_url'] = resolveDocUrl($profile['avatar_url']);
        }
        if (!empty($profile['shop_image'])) {
            $profile['shop_image'] = resolveDocUrl($profile['shop_image']);
        }

        // Application documents — most recent for this technician
        $docStmt = $pdo->prepare(
            "SELECT shop_name, shop_address, specializations, experience_yrs,
                    doc_gov_id, doc_bir, doc_cert, doc_bank, doc_dti,
                    business_name, general_location, zip_code
             FROM seller_applications
             WHERE user_id = ? AND role = 'phone_technician'
             ORDER BY submitted_at DESC LIMIT 1"
        );
        $docStmt->execute([$techId]);
        $docs = $docStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // Merge: application data fills gaps in users table
        if (empty($profile['shop_name']) && !empty($docs['shop_name'])) {
            $profile['shop_name'] = $docs['shop_name'];
        }

        // Build full address — prefer application shop_address, fallback to users fields
        $shopAddr = trim($docs['shop_address'] ?? '');
        if (empty($shopAddr)) {
            $parts = array_filter([
                $profile['address_line'],
                $profile['barangay'] ?? '',
                $docs['general_location'] ?? '',
                $profile['city'],
                $profile['province'],
                $docs['zip_code'] ?? $profile['zip_code'] ?? '',
            ]);
            $shopAddr = implode(', ', $parts);
        }
        $profile['shop_address']     = $shopAddr;
        $profile['general_location'] = $docs['general_location'] ?? $profile['city'];
        $profile['zip_code']         = $docs['zip_code'] ?? '';
        $profile['business_name']    = $docs['business_name'] ?? '';

        // Resolve document URLs — convert relative paths to full URLs
        foreach (['doc_gov_id','doc_bir','doc_cert','doc_bank','doc_dti'] as $docKey) {
            $raw = $docs[$docKey] ?? '';
            $profile[$docKey] = $raw ? resolveDocUrl($raw) : '';
        }

        // Also fetch from technician_credentials table (self-uploaded docs)
        $credentials = [];
        try {
            $credStmt = $pdo->prepare(
                "SELECT id, doc_type, label, file_url, file_name, file_ext, is_image
                 FROM technician_credentials
                 WHERE technician_id = ?
                 ORDER BY display_order ASC, created_at ASC"
            );
            $credStmt->execute([$techId]);
            $credentials = $credStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($credentials as &$c) {
                $c['file_url_full'] = resolveDocUrl($c['file_url']);
            }
            unset($c);
        } catch (Exception $e) { /* table may not exist yet */ }
        $profile['credentials'] = $credentials;

        // Completed repairs count
        $repairStmt = $pdo->prepare(
            "SELECT COUNT(*) FROM bookings WHERE technician_id = ? AND status = 'completed'"
        );
        $repairStmt->execute([$techId]);
        $profile['repairs_done'] = (int)$repairStmt->fetchColumn();

        // Reviews from technician_reviews table (with ratings + media)
        $reviews = [];
        try {
            $revStmt = $pdo->prepare(
                "SELECT
                    r.id, r.booking_id, r.rating, r.comment,
                    r.media_1_url, r.media_1_type,
                    r.media_2_url, r.media_2_type,
                    r.media_3_url, r.media_3_type,
                    r.created_at,
                    CONCAT(u.first_name,' ',u.last_name) AS customer_name,
                    COALESCE(u.profile_image, u.avatar_url,'') AS customer_avatar,
                    COALESCE(b.device_name, b.fault_description, b.problem_desc,'') AS repair_desc,
                    b.service_type
                 FROM technician_reviews r
                 JOIN users u    ON u.id = r.customer_id
                 JOIN bookings b ON b.id = r.booking_id
                 WHERE r.technician_id = ?
                 ORDER BY r.created_at DESC
                 LIMIT 10"
            );
            $revStmt->execute([$techId]);
            $reviews = $revStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($reviews as &$rev) {
                if (!empty($rev['customer_avatar'])) {
                    $rev['customer_avatar'] = resolveDocUrl($rev['customer_avatar']);
                }
                foreach (['media_1_url','media_2_url','media_3_url'] as $mKey) {
                    if (!empty($rev[$mKey])) $rev[$mKey] = resolveDocUrl($rev[$mKey]);
                }
            }
            unset($rev);
        } catch (Exception $e) {
            // Fall back to completed repairs if reviews table doesn't exist yet
            try {
                $revStmt = $pdo->prepare(
                    "SELECT b.id, b.created_at, b.problem_desc, b.fault_description,
                            CONCAT(u.first_name,' ',u.last_name) AS customer_name,
                            COALESCE(u.profile_image, u.avatar_url,'') AS customer_avatar
                     FROM bookings b
                     JOIN users u ON u.id = b.customer_id
                     WHERE b.technician_id = ? AND b.status = 'completed'
                     ORDER BY b.updated_at DESC LIMIT 5"
                );
                $revStmt->execute([$techId]);
                $reviews = $revStmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($reviews as &$rev) {
                    if (!empty($rev['customer_avatar'])) $rev['customer_avatar'] = resolveDocUrl($rev['customer_avatar']);
                    if (empty($rev['problem_desc']) && !empty($rev['fault_description'])) $rev['problem_desc'] = $rev['fault_description'];
                    $rev['rating']   = null; // no rating in fallback
                    $rev['repair_desc'] = $rev['problem_desc'] ?? '';
                }
                unset($rev);
            } catch (Exception $e2) { /* ignore */ }
        }

        // Also update rating_avg/count from the reviews table if available
        try {
            $rAvgStmt = $pdo->prepare(
                "SELECT AVG(rating), COUNT(*) FROM technician_reviews WHERE technician_id = ?"
            );
            $rAvgStmt->execute([$techId]);
            [$rAvg, $rCnt] = $rAvgStmt->fetch(PDO::FETCH_NUM);
            if ($rCnt > 0) {
                $profile['rating_avg']   = round((float)$rAvg, 2);
                $profile['rating_count'] = (int)$rCnt;
            }
        } catch (Exception $e) { /* ignore */ }

        echo json_encode([
            'success' => true,
            'profile' => $profile,
            'reviews' => $reviews,
        ]);
    } catch (Exception $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
    exit;
}

// ── AUTH REQUIRED from here ───────────────────────────────────
$userId   = (int)($_SESSION['user_id'] ?? 0);
$userRole = $_SESSION['user_role'] ?? '';

// ── Technician: upload shop image ─────────────────────────────
if ($method === 'POST' && $action === 'upload_shop_image') {
    if (!$userId || $userRole !== 'phone_technician') {
        http_response_code(403);
        echo json_encode(['success'=>false,'message'=>'Technician login required.']);
        exit;
    }

    if (empty($_FILES['shop_image']['tmp_name'])) {
        echo json_encode(['success'=>false,'message'=>'No image uploaded.']);
        exit;
    }

    $file     = $_FILES['shop_image'];
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowed  = ['image/jpeg','image/png','image/webp','image/gif'];

    if (!in_array($mimeType, $allowed)) {
        echo json_encode(['success'=>false,'message'=>'Invalid image type. Use JPG, PNG, WEBP or GIF.']);
        exit;
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success'=>false,'message'=>'Image too large. Max 5MB.']);
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/shop_images/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'shop_' . $userId . '_' . time() . '.' . $ext;
    $dest     = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        echo json_encode(['success'=>false,'message'=>'Failed to save image.']);
        exit;
    }

    $relativePath = 'uploads/shop_images/' . $filename;

    // Ensure shop_image column exists
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS shop_image VARCHAR(500) NULL");
    } catch (Exception $e) { /* column may already exist */ }

    $pdo->prepare("UPDATE users SET shop_image=? WHERE id=?")->execute([$relativePath, $userId]);

    echo json_encode([
        'success'    => true,
        'shop_image' => resolveDocUrl($relativePath),
        'message'    => 'Shop image updated.',
    ]);
    exit;
}

// ── Customer: submit booking (multipart with optional phone photo) ──
if ($method === 'POST' && $action === 'book') {
    if (!$userId || $userRole !== 'customer') {
        http_response_code(403);
        echo json_encode(['success'=>false,'message'=>'Customer login required.']);
        exit;
    }

    // Support both JSON and multipart
    $isMultipart = !empty($_POST) || !empty($_FILES);
    if ($isMultipart) {
        $body = $_POST;
    } else {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    $techId           = (int)($body['technician_id']    ?? 0);
    $name             = trim($body['name']              ?? '');
    $contactNumber    = trim($body['contact_number']    ?? '');
    $address          = trim($body['address']           ?? '');
    $deviceName       = trim($body['device_name']       ?? '');
    $faultDescription = trim($body['fault_description'] ?? '');
    $phoneHistory     = trim($body['phone_history']     ?? '');
    $expectedFix      = trim($body['expected_fix']      ?? '');
    $scheduledAt      = trim($body['scheduled_at']      ?? '');

    if (!$techId || !$name || !$contactNumber || !$address || !$deviceName || !$faultDescription) {
        echo json_encode(['success'=>false,'message'=>'Please fill in all required fields.']);
        exit;
    }

    // Verify technician
    $tStmt = $pdo->prepare("SELECT id FROM users WHERE id=? AND role='phone_technician' AND is_active=1");
    $tStmt->execute([$techId]);
    if (!$tStmt->fetch()) {
        echo json_encode(['success'=>false,'message'=>'Technician not found or unavailable.']);
        exit;
    }

    // Handle optional phone photo upload
    $phonePhotoPath = null;
    if (!empty($_FILES['phone_photo']['tmp_name'])) {
        $file     = $_FILES['phone_photo'];
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed  = ['image/jpeg','image/png','image/webp','image/gif'];
        if (in_array($mimeType, $allowed) && $file['size'] <= 5 * 1024 * 1024) {
            $uploadDir = __DIR__ . '/../uploads/repair_photos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'phone_' . $userId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $phonePhotoPath = 'uploads/repair_photos/' . $filename;
            }
        }
    }

    try {
        // Ensure phone_photo column exists
        try {
            $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS phone_photo VARCHAR(500) NULL");
        } catch (Exception $e) { /* ignore */ }

        // Ensure service_type column exists
        try {
            $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS service_type ENUM('home_service','shop_fix') DEFAULT 'shop_fix' NULL");
        } catch (Exception $e) { /* ignore */ }

        $serviceType = in_array(($body['service_type'] ?? ''), ['home_service','shop_fix'])
                       ? $body['service_type'] : 'shop_fix';

        $ins = $pdo->prepare(
            "INSERT INTO bookings
                (customer_id, technician_id, contact_number, address, device_name,
                 problem_desc, fault_description, phone_history, expected_fix,
                 phone_photo, scheduled_at, service_type, status, created_at, updated_at)
             VALUES (?,?,?,?,?,  ?,?,?,?,  ?,  ?,  ?,  'pending', NOW(), NOW())"
        );
        $ins->execute([
            $userId, $techId, $contactNumber, $address, $deviceName,
            $faultDescription, $faultDescription, $phoneHistory, $expectedFix,
            $phonePhotoPath,
            $scheduledAt ?: null,
            $serviceType,
        ]);
        $bookingId = (int)$pdo->lastInsertId();

        // Notify technician
        try {
            require_once __DIR__ . '/notification_helper.php';
            $custStmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id=?");
            $custStmt->execute([$userId]);
            $cust = $custStmt->fetch(PDO::FETCH_ASSOC);
            $custName = trim(($cust['first_name']??'') . ' ' . ($cust['last_name']??''));
            sendNotification(
                $techId,
                'new_booking',
                'New Repair Booking',
                "{$custName} booked a repair for their {$deviceName}. Booking #{$bookingId}."
            );
        } catch (Exception $ne) { error_log('[repair_bookings notify] '.$ne->getMessage()); }

        echo json_encode([
            'success'    => true,
            'booking_id' => $bookingId,
            'message'    => 'Booking submitted! The technician will confirm shortly.',
        ]);
    } catch (Exception $e) {
        error_log('[repair_bookings book] '.$e->getMessage());
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
    exit;
}

// ── Customer: cancel booking ──────────────────────────────────
if ($method === 'POST' && $action === 'cancel') {
    if (!$userId || $userRole !== 'customer') {
        http_response_code(403); echo json_encode(['success'=>false,'message'=>'Unauthorized.']); exit;
    }
    $body      = json_decode(file_get_contents('php://input'), true) ?? [];
    $bookingId = (int)($body['booking_id'] ?? 0);
    if (!$bookingId) { echo json_encode(['success'=>false,'message'=>'Booking ID required.']); exit; }
    try {
        $upd = $pdo->prepare(
            "UPDATE bookings SET status='cancelled', updated_at=NOW()
             WHERE id=? AND customer_id=? AND status='pending'"
        );
        $upd->execute([$bookingId, $userId]);
        if ($upd->rowCount() === 0) {
            echo json_encode(['success'=>false,'message'=>'Booking not found or cannot be cancelled.']);
            exit;
        }
        echo json_encode(['success'=>true,'message'=>'Booking cancelled.']);
    } catch (Exception $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
    exit;
}

// ── Customer: my bookings ─────────────────────────────────────
if ($method === 'GET' && $action === 'my_bookings') {
    if (!$userId || $userRole !== 'customer') {
        http_response_code(403); echo json_encode(['success'=>false,'message'=>'Unauthorized.']); exit;
    }
    try {
        // Auto-add all payment columns so old DBs are upgraded on first request
        try {
            $pdo->exec("ALTER TABLE bookings
                ADD COLUMN IF NOT EXISTS payment_method         ENUM('cash','bank_transfer','gcash','maya','other') NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS repair_fee             DECIMAL(10,2) NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS labor_fee              DECIMAL(10,2) NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS parts_fee              DECIMAL(10,2) NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS payment_note           VARCHAR(255)  NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS payment_status         ENUM('unpaid','paid','pending_collection') NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS receipt_path           VARCHAR(500)  NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS price_photo_path       VARCHAR(500)  NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS customer_payment_method ENUM('cash','gcash','maya','bank_transfer','card','online','other') NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS customer_payment_status ENUM('pending','paid') NOT NULL DEFAULT 'pending',
                ADD COLUMN IF NOT EXISTS customer_payment_note  VARCHAR(255)  NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS customer_paid_at       DATETIME      NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS parts_replaced         TEXT          NULL DEFAULT NULL");
        } catch (Exception $ae) { /* columns already exist — ignore */ }

        // Also ensure total_amount exists (older schema may only have total_price)
        try {
            $pdo->exec("ALTER TABLE bookings
                ADD COLUMN IF NOT EXISTS total_amount DECIMAL(10,2) NULL DEFAULT NULL");
        } catch (Exception $ae2) {}

        $stmt = $pdo->prepare(
            "SELECT
                b.id,
                b.status,
                b.contact_number,
                b.address,
                b.device_name,
                b.problem_desc,
                b.fault_description,
                b.phone_history,
                b.expected_fix,
                b.scheduled_at,
                b.total_price,
                b.technician_notes,
                b.created_at,
                b.updated_at,
                b.technician_id,
                COALESCE(b.service_type,'shop_fix')                                AS service_type,
                b.payment_method,
                b.repair_fee,
                b.labor_fee,
                b.parts_fee,
                b.payment_note,
                b.payment_status,
                b.receipt_path,
                b.price_photo_path,
                COALESCE(b.total_amount, b.repair_fee, b.total_price, 0)          AS total_amount,
                b.customer_payment_method,
                b.customer_payment_status,
                b.customer_payment_note,
                b.customer_paid_at,
                b.parts_replaced,
                CONCAT(u.first_name,' ',u.last_name)                               AS technician_name,
                COALESCE(u.shop_name,'')                                           AS shop_name,
                COALESCE(u.profile_image, u.avatar_url,'')                        AS technician_avatar,
                COALESCE(u.phone,'')                                               AS technician_phone,
                COALESCE(u.city,'')                                                AS technician_city
             FROM bookings b
             LEFT JOIN users u ON u.id = b.technician_id
             WHERE b.customer_id = ?
             ORDER BY b.created_at DESC"
        );
        $stmt->execute([$userId]);
        echo json_encode(['success'=>true,'bookings'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (Exception $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
    exit;
}

// ── Customer: submit payment for completed repair ──────────────
if ($method === 'POST' && $action === 'submit_payment') {
    if (!$userId || $userRole !== 'customer') {
        http_response_code(403); echo json_encode(['success'=>false,'message'=>'Unauthorized.']); exit;
    }
    $body      = json_decode(file_get_contents('php://input'), true) ?? [];
    $bookingId = (int)($body['booking_id'] ?? 0);
    $custPM    = trim($body['customer_payment_method'] ?? '');
    $custNote  = trim($body['customer_payment_note']   ?? '');

    $allowedMethods = ['cash','gcash','maya','bank_transfer','other'];
    if (!$bookingId || !in_array($custPM, $allowedMethods, true)) {
        echo json_encode(['success'=>false,'message'=>'Invalid booking or payment method.']); exit;
    }

    try {
        // Verify booking belongs to this customer and is completed
        $check = $pdo->prepare(
            "SELECT id, status FROM bookings WHERE id=? AND customer_id=? AND status='completed'"
        );
        $check->execute([$bookingId, $userId]);
        if (!$check->fetch()) {
            echo json_encode(['success'=>false,'message'=>'Booking not found or not yet completed.']); exit;
        }

        // Add columns if they don't exist yet
        try {
            $pdo->exec("ALTER TABLE bookings
                ADD COLUMN IF NOT EXISTS customer_payment_method
                    ENUM('cash','gcash','maya','bank_transfer','card','online','other') NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS customer_payment_status
                    ENUM('pending','paid') NOT NULL DEFAULT 'pending',
                ADD COLUMN IF NOT EXISTS customer_payment_note
                    VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS customer_paid_at
                    DATETIME NULL DEFAULT NULL");
        } catch (Exception $ae) { /* columns already exist */ }

        $pdo->prepare(
            "UPDATE bookings
             SET customer_payment_method = ?,
                 customer_payment_status = 'paid',
                 customer_payment_note   = ?,
                 customer_paid_at        = NOW(),
                 updated_at              = NOW()
             WHERE id = ? AND customer_id = ?"
        )->execute([$custPM, $custNote ?: null, $bookingId, $userId]);

        // Notify technician
        try {
            require_once __DIR__ . '/notification_helper.php';
            $bRow = $pdo->prepare("SELECT technician_id, device_name FROM bookings WHERE id=?");
            $bRow->execute([$bookingId]);
            $bData = $bRow->fetch(PDO::FETCH_ASSOC);
            if ($bData && $bData['technician_id']) {
                $methodLabels = ['cash'=>'Cash','gcash'=>'GCash','maya'=>'Maya',
                                 'bank_transfer'=>'Bank Transfer','other'=>'Other'];
                sendNotification(
                    $bData['technician_id'],
                    'payment_received',
                    'Payment Received',
                    "Customer has confirmed payment via " . ($methodLabels[$custPM]??$custPM) .
                    " for repair #{$bookingId} ({$bData['device_name']})."
                );
            }
        } catch (Exception $ne) { error_log('[submit_payment notify] '.$ne->getMessage()); }

        echo json_encode(['success'=>true,'message'=>'Payment confirmed successfully!']);
    } catch (Exception $e) {
        error_log('[submit_payment] '.$e->getMessage());
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
    exit;
}

http_response_code(400);
echo json_encode(['success'=>false,'message'=>'Unknown action.']);
