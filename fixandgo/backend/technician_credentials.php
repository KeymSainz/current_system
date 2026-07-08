<?php
/**
 * Fix&Go — Technician Credentials, Shop Images & Work Videos API
 *
 * GET  ?action=list                   → own credentials (technician only)
 * GET  ?action=public&tech_id=N       → all credentials for a technician (public)
 * POST action=upload (multipart)      → upload credential / shop image / work video
 * POST action=delete                  → delete one item by id
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

// ── Document type catalogue ───────────────────────────────────
const DOC_TYPES = [
    // Credentials / documents
    'gov_id'      => '🪪 Government ID',
    'bir'         => '📄 BIR Certificate',
    'dti'         => '📋 DTI Permit',
    'tech_cert'   => '🏅 Technician Certification',
    'tesda'       => '🎓 TESDA Certificate',
    'nstp'        => '📜 NSTP Certificate',
    'bank'        => '🏦 Bank Document',
    'skill_cert'  => '⚙️ Skill Certificate',
    // Shop media
    'shop_image'  => '🏪 Shop Photo',
    'work_video'  => '🎬 Work Video',
    // Generic
    'custom'      => '📎 Other Document',
];

// Per-type limits
const TYPE_LIMITS = [
    'shop_image' => 5,   // up to 5 shop photos
    'work_video' => 3,   // up to 3 work videos
    'default'    => 10,  // all other types combined
];

// ── Resolve relative path to full URL ─────────────────────────
function resolveUrl(string $path): string {
    if (empty($path)) return '';
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
    $cfg  = require __DIR__ . '/config.php';
    $base = rtrim($cfg['app_url'], '/') . '/';
    return $base . ltrim($path, '/');
}

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    // ── Own credentials (technician) ─────────────────────────
    if ($action === 'list') {
        if (!$me || $myRole !== 'phone_technician') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Technician login required.']);
            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT id, doc_type, label, file_url, file_name, file_ext, is_image, is_video, display_order, created_at
             FROM technician_credentials
             WHERE technician_id = ?
             ORDER BY display_order ASC, created_at ASC"
        );
        $stmt->execute([$me]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$r) {
            $r['file_url_full'] = resolveUrl($r['file_url']);
            $r['is_image'] = (bool)$r['is_image'];
            $r['is_video'] = (bool)$r['is_video'];
        }
        unset($r);

        // Count by type for the UI to show limits
        $counts = [];
        foreach ($rows as $r) {
            $counts[$r['doc_type']] = ($counts[$r['doc_type']] ?? 0) + 1;
        }

        echo json_encode([
            'success'     => true,
            'credentials' => $rows,
            'counts'      => $counts,
            'doc_types'   => DOC_TYPES,
            'limits'      => TYPE_LIMITS,
        ]);
        exit;
    }

    // ── Public listing ────────────────────────────────────────
    if ($action === 'public') {
        $techId = (int)($_GET['tech_id'] ?? 0);
        if (!$techId) { echo json_encode(['success' => false, 'message' => 'tech_id required.']); exit; }

        $stmt = $pdo->prepare(
            "SELECT id, doc_type, label, file_url, file_name, file_ext, is_image, is_video, display_order
             FROM technician_credentials
             WHERE technician_id = ?
             ORDER BY display_order ASC, created_at ASC"
        );
        $stmt->execute([$techId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$r) {
            $r['file_url_full'] = resolveUrl($r['file_url']);
            $r['is_image'] = (bool)$r['is_image'];
            $r['is_video'] = (bool)$r['is_video'];
        }
        unset($r);

        echo json_encode(['success' => true, 'credentials' => $rows]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── POST ──────────────────────────────────────────────────────
if ($method === 'POST') {
    if (!$me || $myRole !== 'phone_technician') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Technician login required.']);
        exit;
    }

    $isMultipart = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart') !== false;
    $body        = $isMultipart ? $_POST : (json_decode(file_get_contents('php://input'), true) ?? []);
    $postAction  = $body['action'] ?? '';

    // ── Upload ────────────────────────────────────────────────
    if ($postAction === 'upload') {
        $docType = trim($body['doc_type'] ?? 'custom');
        $label   = trim($body['label']    ?? (DOC_TYPES[$docType] ?? 'Document'));

        if (!array_key_exists($docType, DOC_TYPES)) {
            echo json_encode(['success' => false, 'message' => 'Invalid document type.']);
            exit;
        }
        if (mb_strlen($label) > 120) {
            echo json_encode(['success' => false, 'message' => 'Label too long (max 120 chars).']);
            exit;
        }

        // File required
        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
            exit;
        }

        $file     = $_FILES['file'];
        $origName = basename($file['name']);
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $mime     = mime_content_type($file['tmp_name']);

        // Separate allowed lists by doc type
        $isVideoType = ($docType === 'work_video');

        $allowedImageExts  = ['jpg','jpeg','png','webp','gif'];
        $allowedImageMimes = ['image/jpeg','image/png','image/webp','image/gif'];
        $allowedVideoExts  = ['mp4','webm','mov'];
        $allowedVideoMimes = ['video/mp4','video/webm','video/quicktime'];
        $allowedDocExts    = ['pdf'];
        $allowedDocMimes   = ['application/pdf'];

        $isImage = false;
        $isVideo = false;

        if ($docType === 'work_video') {
            if (!in_array($ext, $allowedVideoExts) || !in_array($mime, $allowedVideoMimes)) {
                echo json_encode(['success' => false, 'message' => 'Work video must be MP4, WebM, or MOV.']);
                exit;
            }
            $isVideo  = true;
            $maxBytes = 100 * 1024 * 1024; // 100 MB for videos
        } elseif ($docType === 'shop_image') {
            if (!in_array($ext, $allowedImageExts) || !in_array($mime, $allowedImageMimes)) {
                echo json_encode(['success' => false, 'message' => 'Shop photo must be JPG, PNG, or WebP.']);
                exit;
            }
            $isImage  = true;
            $maxBytes = 10 * 1024 * 1024; // 10 MB for shop images
        } else {
            // Credentials: image or PDF
            $allExts  = array_merge($allowedImageExts, $allowedDocExts);
            $allMimes = array_merge($allowedImageMimes, $allowedDocMimes);
            if (!in_array($ext, $allExts) || !in_array($mime, $allMimes)) {
                echo json_encode(['success' => false, 'message' => 'Credentials: use JPG, PNG, WebP, or PDF.']);
                exit;
            }
            $isImage  = in_array($ext, $allowedImageExts);
            $maxBytes = ($mime === 'application/pdf') ? 10 * 1024 * 1024 : 5 * 1024 * 1024;
        }

        if ($file['size'] > $maxBytes) {
            $mbLimit = round($maxBytes / 1024 / 1024);
            echo json_encode(['success' => false, 'message' => "File too large. Max {$mbLimit} MB for this type."]);
            exit;
        }

        // Per-type limits
        $typeLimit = TYPE_LIMITS[$docType] ?? TYPE_LIMITS['default'];
        $cntStmt   = $pdo->prepare("SELECT COUNT(*) FROM technician_credentials WHERE technician_id = ? AND doc_type = ?");
        $cntStmt->execute([$me, $docType]);
        if ((int)$cntStmt->fetchColumn() >= $typeLimit) {
            echo json_encode(['success' => false, 'message' => "Maximum {$typeLimit} files allowed for this type."]);
            exit;
        }

        // Overall cap: no more than 25 items total
        $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM technician_credentials WHERE technician_id = ?");
        $totalStmt->execute([$me]);
        if ((int)$totalStmt->fetchColumn() >= 25) {
            echo json_encode(['success' => false, 'message' => 'Maximum 25 total items allowed.']);
            exit;
        }

        // Save file
        $uploadDir = __DIR__ . '/../uploads/credentials/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $safeExt  = preg_replace('/[^a-z0-9]/', '', $ext);
        $newName  = 'cred_' . $me . '_' . $docType . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $safeExt;
        $destPath = $uploadDir . $newName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save file.']);
            exit;
        }

        $relPath   = 'uploads/credentials/' . $newName;
        $isImageI  = $isImage ? 1 : 0;
        $isVideoI  = $isVideo ? 1 : 0;

        // Next display order
        $ordStmt = $pdo->prepare("SELECT COALESCE(MAX(display_order),0)+1 FROM technician_credentials WHERE technician_id = ?");
        $ordStmt->execute([$me]);
        $nextOrder = (int)$ordStmt->fetchColumn();

        // Handle is_video column gracefully (may not exist in older installs)
        try {
            $pdo->prepare(
                "INSERT INTO technician_credentials
                    (technician_id, doc_type, label, file_url, file_name, file_ext, is_image, is_video, display_order)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            )->execute([$me, $docType, $label, $relPath, $origName, $safeExt, $isImageI, $isVideoI, $nextOrder]);
        } catch (\Exception $e) {
            // Fallback: insert without is_video
            $pdo->prepare(
                "INSERT INTO technician_credentials
                    (technician_id, doc_type, label, file_url, file_name, file_ext, is_image, display_order)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            )->execute([$me, $docType, $label, $relPath, $origName, $safeExt, $isImageI, $nextOrder]);
        }

        $newId = (int)$pdo->lastInsertId();

        echo json_encode([
            'success'       => true,
            'message'       => 'Uploaded successfully.',
            'id'            => $newId,
            'file_url'      => $relPath,
            'file_url_full' => resolveUrl($relPath),
            'is_image'      => $isImage,
            'is_video'      => $isVideo,
        ]);
        exit;
    }

    // ── Delete ────────────────────────────────────────────────
    if ($postAction === 'delete') {
        $credId = (int)($body['id'] ?? 0);
        if (!$credId) { echo json_encode(['success' => false, 'message' => 'id required.']); exit; }

        $sel = $pdo->prepare("SELECT file_url FROM technician_credentials WHERE id = ? AND technician_id = ?");
        $sel->execute([$credId, $me]);
        $row = $sel->fetch(PDO::FETCH_ASSOC);

        if (!$row) { echo json_encode(['success' => false, 'message' => 'Not found.']); exit; }

        $filePath = __DIR__ . '/../' . $row['file_url'];
        if (file_exists($filePath)) @unlink($filePath);

        $pdo->prepare("DELETE FROM technician_credentials WHERE id = ? AND technician_id = ?")->execute([$credId, $me]);

        echo json_encode(['success' => true, 'message' => 'Deleted.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
