<?php
/**
 * Fix&Go — Technician Reviews API
 *
 * GET  ?action=can_review&booking_id=N     → check if current customer can review this booking
 * GET  ?action=technician_reviews&tech_id=N → list all reviews for a technician (public)
 * POST action=submit (multipart)            → submit a review with optional media (up to 3 files)
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

$pdo    = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method = $_SERVER['REQUEST_METHOD'];
$me     = (int)($_SESSION['user_id'] ?? 0);
$myRole = $_SESSION['user_role'] ?? '';

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    // ── Can this customer review this booking? ────────────────
    if ($action === 'can_review') {
        if (!$me) { echo json_encode(['can_review' => false, 'reason' => 'Not logged in.']); exit; }
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        if (!$bookingId) { echo json_encode(['can_review' => false, 'reason' => 'booking_id required.']); exit; }

        // Booking must belong to this customer and be completed
        $bStmt = $pdo->prepare(
            "SELECT b.id, b.technician_id, b.status,
                    (SELECT COUNT(*) FROM technician_reviews r WHERE r.booking_id = b.id) AS already_reviewed
             FROM bookings b
             WHERE b.id = ? AND b.customer_id = ?"
        );
        $bStmt->execute([$bookingId, $me]);
        $booking = $bStmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            echo json_encode(['can_review' => false, 'reason' => 'Booking not found.']);
            exit;
        }
        if ($booking['status'] !== 'completed') {
            echo json_encode(['can_review' => false, 'reason' => 'Repair not yet completed.']);
            exit;
        }
        if ((int)$booking['already_reviewed'] > 0) {
            echo json_encode(['can_review' => false, 'reason' => 'Already reviewed.', 'already_reviewed' => true]);
            exit;
        }

        echo json_encode([
            'can_review'    => true,
            'technician_id' => (int)$booking['technician_id'],
        ]);
        exit;
    }

    // ── List reviews for a technician (public) ────────────────
    if ($action === 'technician_reviews') {
        $techId = (int)($_GET['tech_id'] ?? 0);
        $limit  = min((int)($_GET['limit'] ?? 10), 50);
        $offset = (int)($_GET['offset'] ?? 0);

        if (!$techId) { echo json_encode(['success' => false, 'message' => 'tech_id required.']); exit; }

        $stmt = $pdo->prepare(
            "SELECT
                r.id,
                r.booking_id,
                r.rating,
                r.comment,
                r.media_1_url, r.media_1_type,
                r.media_2_url, r.media_2_type,
                r.media_3_url, r.media_3_type,
                r.created_at,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                COALESCE(u.profile_image, u.avatar_url, '') AS customer_avatar,
                COALESCE(b.device_name, b.fault_description, b.problem_desc, '') AS repair_desc,
                b.service_type
             FROM technician_reviews r
             JOIN users u   ON u.id = r.customer_id
             JOIN bookings b ON b.id = r.booking_id
             WHERE r.technician_id = ?
             ORDER BY r.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$techId, $limit, $offset]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Count total
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM technician_reviews WHERE technician_id = ?");
        $countStmt->execute([$techId]);
        $total = (int)$countStmt->fetchColumn();

        // Average rating
        $avgStmt = $pdo->prepare("SELECT AVG(rating), COUNT(*) FROM technician_reviews WHERE technician_id = ?");
        $avgStmt->execute([$techId]);
        [$avg, $cnt] = $avgStmt->fetch(PDO::FETCH_NUM);

        // Resolve avatar URLs
        $cfg  = require __DIR__ . '/config.php';
        $base = rtrim($cfg['app_url'], '/') . '/';
        foreach ($reviews as &$rev) {
            if (!empty($rev['customer_avatar']) && !str_starts_with($rev['customer_avatar'], 'http')) {
                $rev['customer_avatar'] = $base . ltrim($rev['customer_avatar'], '/');
            }
            foreach (['media_1_url','media_2_url','media_3_url'] as $mKey) {
                if (!empty($rev[$mKey]) && !str_starts_with($rev[$mKey], 'http')) {
                    $rev[$mKey] = $base . ltrim($rev[$mKey], '/');
                }
            }
        }
        unset($rev);

        echo json_encode([
            'success'    => true,
            'reviews'    => $reviews,
            'total'      => $total,
            'avg_rating' => $avg ? round((float)$avg, 2) : 0,
            'count'      => (int)$cnt,
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── POST: submit review ───────────────────────────────────────
if ($method === 'POST') {
    if (!$me || $myRole !== 'customer') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Customer login required.']);
        exit;
    }

    $bookingId    = (int)($_POST['booking_id'] ?? 0);
    $rating       = (int)($_POST['rating'] ?? 0);
    $comment      = trim($_POST['comment'] ?? '');

    if (!$bookingId || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'booking_id and rating (1–5) are required.']);
        exit;
    }
    if ($comment && mb_strlen($comment) > 2000) {
        echo json_encode(['success' => false, 'message' => 'Comment too long (max 2000 characters).']);
        exit;
    }

    // Verify booking belongs to this customer and is completed
    $bStmt = $pdo->prepare(
        "SELECT b.id, b.technician_id, b.status,
                (SELECT COUNT(*) FROM technician_reviews r WHERE r.booking_id = b.id) AS already_reviewed
         FROM bookings b
         WHERE b.id = ? AND b.customer_id = ?"
    );
    $bStmt->execute([$bookingId, $me]);
    $booking = $bStmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found.']);
        exit;
    }
    if ($booking['status'] !== 'completed') {
        echo json_encode(['success' => false, 'message' => 'You can only review completed repairs.']);
        exit;
    }
    if ((int)$booking['already_reviewed'] > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already reviewed this repair.']);
        exit;
    }

    $techId = (int)$booking['technician_id'];

    // ── Handle media uploads (up to 3 files) ─────────────────
    $allowedImages = ['jpg','jpeg','png','gif','webp'];
    $allowedVideos = ['mp4','webm','mov'];
    $allowedMimes  = [
        'image/jpeg','image/png','image/gif','image/webp',
        'video/mp4','video/webm','video/quicktime'
    ];

    $uploadDir = __DIR__ . '/../uploads/reviews/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $mediaData = []; // up to 3 items: ['url', 'type']

    for ($i = 1; $i <= 3; $i++) {
        $fileKey = 'media_' . $i;
        if (empty($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) continue;

        $file    = $_FILES[$fileKey];
        $origName = basename($file['name']);
        $ext     = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $mime    = mime_content_type($file['tmp_name']);

        if (!in_array($ext, array_merge($allowedImages, $allowedVideos)) || !in_array($mime, $allowedMimes)) {
            echo json_encode(['success' => false, 'message' => "File {$i}: invalid type. Only JPG, PNG, WebP, MP4, WebM, MOV allowed."]);
            exit;
        }

        $maxBytes = in_array($ext, $allowedVideos) ? 50 * 1024 * 1024 : 10 * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            $limit = in_array($ext, $allowedVideos) ? '50 MB' : '10 MB';
            echo json_encode(['success' => false, 'message' => "File {$i} too large. Max {$limit}."]);
            exit;
        }

        $safeExt  = preg_replace('/[^a-z0-9]/', '', $ext);
        $newName  = 'rev_' . $me . '_' . $bookingId . '_' . $i . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $safeExt;
        $destPath = $uploadDir . $newName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            echo json_encode(['success' => false, 'message' => "Failed to upload file {$i}."]);
            exit;
        }

        $mediaData[] = [
            'url'  => 'uploads/reviews/' . $newName,
            'type' => in_array($ext, $allowedVideos) ? 'video' : 'image',
        ];
    }

    // Pad to 3 slots
    while (count($mediaData) < 3) $mediaData[] = ['url' => null, 'type' => null];

    // Insert review
    $pdo->prepare(
        "INSERT INTO technician_reviews
            (booking_id, technician_id, customer_id, rating, comment,
             media_1_url, media_1_type, media_2_url, media_2_type, media_3_url, media_3_type)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    )->execute([
        $bookingId, $techId, $me, $rating, $comment ?: null,
        $mediaData[0]['url'], $mediaData[0]['type'],
        $mediaData[1]['url'], $mediaData[1]['type'],
        $mediaData[2]['url'], $mediaData[2]['type'],
    ]);

    // Update technician_profiles rating cache
    try {
        $avgStmt = $pdo->prepare(
            "SELECT AVG(rating), COUNT(*) FROM technician_reviews WHERE technician_id = ?"
        );
        $avgStmt->execute([$techId]);
        [$newAvg, $newCnt] = $avgStmt->fetch(PDO::FETCH_NUM);

        $pdo->prepare(
            "INSERT INTO technician_profiles (user_id, rating_avg, rating_count)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE rating_avg = ?, rating_count = ?"
        )->execute([$techId, round($newAvg, 2), $newCnt, round($newAvg, 2), $newCnt]);
    } catch (Exception $e) {
        error_log('[reviews] Failed to update rating cache: ' . $e->getMessage());
    }

    // Send notification to technician
    try {
        require_once __DIR__ . '/notification_helper.php';
        $custStmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
        $custStmt->execute([$me]);
        $cust = $custStmt->fetch(PDO::FETCH_ASSOC);
        $custName = trim(($cust['first_name'] ?? '') . ' ' . ($cust['last_name'] ?? '')) ?: 'A customer';
        $stars = str_repeat('⭐', $rating);
        sendNotification(
            $techId,
            'review',
            "New {$stars} Review from {$custName}",
            $comment ? mb_substr($comment, 0, 80) . (mb_strlen($comment) > 80 ? '…' : '') : 'No comment left.'
        );
    } catch (Exception $ne) {
        error_log('[reviews notify] ' . $ne->getMessage());
    }

    echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
