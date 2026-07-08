<?php
/**
 * Fix&Go — Messages API
 *
 * GET  ?action=conversations          → list all conversations for current user
 * GET  ?action=messages&conv_id=X    → messages in a conversation
 * GET  ?action=unread_count           → total unread count
 * POST action=send                    → send a message (creates conversation if needed)
 * POST action=mark_read&conv_id=X    → mark all messages in conv as read
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Login required.', 'expired' => true]);
    exit;
}

$me     = (int) $_SESSION['user_id'];
$myRole = $_SESSION['user_role'] ?? '';
$pdo    = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'conversations';

    // ── List conversations ────────────────────────────────────
    if ($action === 'conversations') {
        // Detect which timestamp column the messages table uses
        $tsCol = 'created_at';
        try {
            $colCheck = $pdo->query("SHOW COLUMNS FROM messages LIKE 'created_at'");
            if ($colCheck->rowCount() === 0) $tsCol = 'sent_at';
        } catch (Exception $e) { $tsCol = 'sent_at'; }

        $stmt = $pdo->prepare(
            "SELECT
                c.id,
                c.updated_at,
                CASE WHEN c.user_a_id = ? THEN c.user_b_id ELSE c.user_a_id END AS other_id,
                (SELECT COALESCE(m.body, CASE m.file_type WHEN 'video' THEN '🎥 Video' WHEN 'image' THEN '📷 Photo' ELSE '📎 Attachment' END)
                 FROM messages m
                 WHERE m.conversation_id = c.id
                 ORDER BY m.{$tsCol} DESC LIMIT 1)                               AS last_message,
                (SELECT m.{$tsCol} FROM messages m
                 WHERE m.conversation_id = c.id
                 ORDER BY m.{$tsCol} DESC LIMIT 1)                               AS last_message_at,
                (SELECT COUNT(*) FROM messages m
                 WHERE m.conversation_id = c.id
                   AND m.sender_id != ?
                   AND m.is_read = 0)                                            AS unread_count
             FROM conversations c
             WHERE c.user_a_id = ? OR c.user_b_id = ?
             ORDER BY c.updated_at DESC"
        );
        $stmt->execute([$me, $me, $me, $me]);
        $convs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch other user info
        if ($convs) {
            $otherIds = array_unique(array_column($convs, 'other_id'));
            $ph       = implode(',', array_fill(0, count($otherIds), '?'));
            $uStmt    = $pdo->prepare(
                "SELECT id, first_name, last_name, role FROM users WHERE id IN ($ph)"
            );
            $uStmt->execute($otherIds);
            $users = [];
            foreach ($uStmt->fetchAll(PDO::FETCH_ASSOC) as $u) {
                $users[$u['id']] = $u;
            }
            foreach ($convs as &$c) {
                $u = $users[$c['other_id']] ?? null;
                $c['other_name'] = $u
                    ? trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))
                    : 'Unknown';
                $c['other_role'] = $u['role'] ?? '';
            }
            unset($c);
        }

        echo json_encode(['success' => true, 'conversations' => $convs]);
        exit;
    }

    // ── Messages in a conversation ────────────────────────────
    if ($action === 'messages') {
        $convId = (int)($_GET['conv_id'] ?? 0);
        if (!$convId) {
            echo json_encode(['success' => false, 'message' => 'conv_id required.']);
            exit;
        }

        // Verify user is part of this conversation
        $check = $pdo->prepare(
            "SELECT id FROM conversations WHERE id = ? AND (user_a_id = ? OR user_b_id = ?)"
        );
        $check->execute([$convId, $me, $me]);
        if (!$check->fetch()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }

        // Mark messages as read
        $pdo->prepare(
            "UPDATE messages SET is_read = 1
             WHERE conversation_id = ? AND sender_id != ? AND is_read = 0"
        )->execute([$convId, $me]);

        // Detect timestamp column
        $tsCol = 'created_at';
        try {
            $colCheck = $pdo->query("SHOW COLUMNS FROM messages LIKE 'created_at'");
            if ($colCheck->rowCount() === 0) $tsCol = 'sent_at';
        } catch (Exception $e) { $tsCol = 'sent_at'; }

        // Fetch messages
        $stmt = $pdo->prepare(
            "SELECT m.id, m.sender_id, m.body, m.is_read, m.{$tsCol} AS created_at,
                    m.file_url, m.file_type, m.file_name,
                    u.first_name, u.last_name
             FROM messages m
             JOIN users u ON u.id = m.sender_id
             WHERE m.conversation_id = ?
             ORDER BY m.{$tsCol} ASC
             LIMIT 200"
        );
        $stmt->execute([$convId]);
        $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'messages' => $msgs, 'my_id' => $me]);
        exit;
    }

    // ── Unread count ──────────────────────────────────────────
    if ($action === 'unread_count') {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM messages m
             JOIN conversations c ON c.id = m.conversation_id
             WHERE (c.user_a_id = ? OR c.user_b_id = ?)
               AND m.sender_id != ?
               AND m.is_read = 0"
        );
        $stmt->execute([$me, $me, $me]);
        echo json_encode(['success' => true, 'count' => (int) $stmt->fetchColumn()]);
        exit;
    }

    // ── Get or create conversation with a specific user ───────
    if ($action === 'get_or_create') {
        $otherId = (int)($_GET['other_id'] ?? 0);
        if (!$otherId || $otherId === $me) {
            echo json_encode(['success' => false, 'message' => 'Invalid user.']);
            exit;
        }

        $a = min($me, $otherId);
        $b = max($me, $otherId);

        $stmt = $pdo->prepare(
            "SELECT id FROM conversations WHERE user_a_id = ? AND user_b_id = ?"
        );
        $stmt->execute([$a, $b]);
        $conv = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$conv) {
            $pdo->prepare(
                "INSERT INTO conversations (user_a_id, user_b_id) VALUES (?, ?)"
            )->execute([$a, $b]);
            $convId = (int) $pdo->lastInsertId();
        } else {
            $convId = (int) $conv['id'];
        }

        // Get other user info
        $uStmt = $pdo->prepare("SELECT id, first_name, last_name, role FROM users WHERE id = ?");
        $uStmt->execute([$otherId]);
        $other = $uStmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success'     => true,
            'conv_id'     => $convId,
            'other_id'    => $otherId,
            'other_name'  => $other ? trim($other['first_name'] . ' ' . $other['last_name']) : 'Unknown',
            'other_role'  => $other['role'] ?? '',
        ]);
        exit;
    }

    // ── Search users for new conversation ────────────────────
    if ($action === 'search_users') {
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            echo json_encode(['success' => true, 'users' => []]);
            exit;
        }

        // Role-based contact permissions
        $roleMap = [
            'owner'            => ['supervisor', 'sales_person', 'supplier'],
            'supplier'         => ['owner', 'phone_technician'],
            'supervisor'       => ['owner', 'sales_person'],
            'sales_person'     => ['customer'],
            'phone_technician' => ['customer', 'supplier', 'owner'],
            'customer'         => ['sales_person', 'phone_technician'],
        ];
        $allowedRoles = $roleMap[$myRole] ?? [];
        if (empty($allowedRoles)) {
            echo json_encode(['success' => true, 'users' => []]);
            exit;
        }

        $ph     = implode(',', array_fill(0, count($allowedRoles), '?'));
        $params = array_merge([$me, "%{$q}%", "%{$q}%"], $allowedRoles);
        $stmt   = $pdo->prepare(
            "SELECT id, first_name, last_name, role, email
             FROM users
             WHERE id != ?
               AND (CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ?)
               AND role IN ($ph)
               AND status = 'active'
             LIMIT 10"
        );
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'users' => $users]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── POST ──────────────────────────────────────────────────────
if ($method === 'POST') {
    // Support both JSON (text-only) and multipart (with file attachment)
    $isMultipart = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart') !== false;

    if ($isMultipart) {
        $body       = $_POST;
        $postAction = $body['action'] ?? '';
    } else {
        $body       = json_decode(file_get_contents('php://input'), true) ?? [];
        $postAction = $body['action'] ?? '';
    }

    // ── Send message (text or with file attachment) ───────────
    if ($postAction === 'send') {
        $otherId = (int)($body['other_id'] ?? 0);
        $msgBody = trim($body['body'] ?? '');

        // Handle file upload
        $fileUrl  = null;
        $fileType = null;
        $fileName = null;

        if ($isMultipart && !empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $file    = $_FILES['attachment'];
            $origName = basename($file['name']);
            $mime    = mime_content_type($file['tmp_name']);
            $ext     = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            // Allowed types
            $allowedImages = ['jpg','jpeg','png','gif','webp'];
            $allowedVideos = ['mp4','webm','mov'];
            $allowedMimes  = [
                'image/jpeg','image/png','image/gif','image/webp',
                'video/mp4','video/webm','video/quicktime'
            ];

            if (!in_array($ext, array_merge($allowedImages, $allowedVideos))
                || !in_array($mime, $allowedMimes)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only images (JPG, PNG, GIF, WebP) and videos (MP4, WebM, MOV) are allowed.']);
                exit;
            }

            // 50 MB max for video, 10 MB for images
            $maxBytes = in_array($ext, $allowedVideos) ? 50 * 1024 * 1024 : 10 * 1024 * 1024;
            if ($file['size'] > $maxBytes) {
                $limit = in_array($ext, $allowedVideos) ? '50 MB' : '10 MB';
                echo json_encode(['success' => false, 'message' => "File too large. Max size is {$limit}."]);
                exit;
            }

            // Build upload directory
            $uploadDir = __DIR__ . '/../uploads/messages/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $safeExt  = preg_replace('/[^a-z0-9]/', '', $ext);
            $newName  = 'msg_' . $me . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $safeExt;
            $destPath = $uploadDir . $newName;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                echo json_encode(['success' => false, 'message' => 'File upload failed.']);
                exit;
            }

            $fileUrl  = 'uploads/messages/' . $newName;
            $fileType = in_array($ext, $allowedVideos) ? 'video' : 'image';
            $fileName = $origName;
        }

        // Must have body text OR a file
        if (!$msgBody && !$fileUrl) {
            echo json_encode(['success' => false, 'message' => 'Message text or file attachment required.']);
            exit;
        }
        if ($msgBody && mb_strlen($msgBody) > 2000) {
            echo json_encode(['success' => false, 'message' => 'Message too long (max 2000 chars).']);
            exit;
        }
        if (!$otherId) {
            echo json_encode(['success' => false, 'message' => 'other_id required.']);
            exit;
        }

        // Get or create conversation
        $a = min($me, $otherId);
        $b = max($me, $otherId);

        $stmt = $pdo->prepare(
            "SELECT id FROM conversations WHERE user_a_id = ? AND user_b_id = ?"
        );
        $stmt->execute([$a, $b]);
        $conv = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$conv) {
            $pdo->prepare(
                "INSERT INTO conversations (user_a_id, user_b_id) VALUES (?, ?)"
            )->execute([$a, $b]);
            $convId = (int) $pdo->lastInsertId();
        } else {
            $convId = (int) $conv['id'];
        }

        // Insert message — with optional file columns
        $bodyText = $msgBody ?: null;
        try {
            $ins = $pdo->prepare(
                "INSERT INTO messages (conversation_id, sender_id, body, file_url, file_type, file_name)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $ins->execute([$convId, $me, $bodyText, $fileUrl, $fileType, $fileName]);
        } catch (Exception $colEx) {
            // Fallback: old schema without file columns
            $ins = $pdo->prepare(
                "INSERT INTO messages (conversation_id, sender_id, body) VALUES (?, ?, ?)"
            );
            $ins->execute([$convId, $me, $bodyText ?: '']);
        }
        $msgId = (int) $pdo->lastInsertId();

        // Touch conversation updated_at
        $pdo->prepare(
            "UPDATE conversations SET updated_at = NOW() WHERE id = ?"
        )->execute([$convId]);

        // Send notification to recipient
        try {
            require_once __DIR__ . '/notification_helper.php';
            $senderStmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
            $senderStmt->execute([$me]);
            $sender = $senderStmt->fetch(PDO::FETCH_ASSOC);
            $senderName = trim(($sender['first_name'] ?? '') . ' ' . ($sender['last_name'] ?? '')) ?: 'Someone';
            if ($fileUrl) {
                $preview = $fileType === 'video' ? '🎥 Sent a video' : '📷 Sent a photo';
                if ($msgBody) $preview .= ': ' . (mb_strlen($msgBody) > 40 ? mb_substr($msgBody, 0, 40) . '…' : $msgBody);
            } else {
                $preview = mb_strlen($msgBody) > 60 ? mb_substr($msgBody, 0, 60) . '…' : $msgBody;
            }
            sendNotification(
                $otherId,
                'message',
                "New message from {$senderName}",
                $preview
            );
        } catch (Exception $ne) {
            error_log('[messages send notify] ' . $ne->getMessage());
        }

        echo json_encode([
            'success'    => true,
            'message_id' => $msgId,
            'conv_id'    => $convId,
        ]);
        exit;
    }

    // ── Mark read ─────────────────────────────────────────────
    if ($postAction === 'mark_read') {
        $convId = (int)($body['conv_id'] ?? 0);
        if (!$convId) {
            echo json_encode(['success' => false, 'message' => 'conv_id required.']);
            exit;
        }
        $pdo->prepare(
            "UPDATE messages SET is_read = 1
             WHERE conversation_id = ? AND sender_id != ? AND is_read = 0"
        )->execute([$convId, $me]);
        echo json_encode(['success' => true]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
