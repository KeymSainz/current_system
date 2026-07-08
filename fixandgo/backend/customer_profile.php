<?php
/**
 * Fix&Go — Customer Profile API
 *
 * GET  ?action=get           → get profile + address
 * POST action=update_profile → update name/phone/gender/dob
 * POST action=update_address → update delivery address
 * POST action=upload_avatar  → upload profile photo (multipart)
 * POST action=change_password → change password
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Cache-Control: no-store');

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Customer login required.']);
    exit;
}

$customerId = (int)$_SESSION['user_id'];
$pdo        = require __DIR__ . '/db.php';
$method     = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    header('Content-Type: application/json');
    $action = $_GET['action'] ?? 'get';

    if ($action === 'get') {
        $stmt = $pdo->prepare(
            "SELECT id, first_name, last_name, email, phone,
                    address_line, barangay, city, province, region, zip_code,
                    address_verified, created_at,
                    avatar_url,
                    gender, date_of_birth
             FROM users WHERE id = ?"
        );
        $stmt->execute([$customerId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        // Check if address is complete
        $addressComplete = !empty($user['address_line']) && !empty($user['barangay'])
                        && !empty($user['city'])         && !empty($user['province'])
                        && !empty($user['zip_code'])     && !empty($user['phone']);

        echo json_encode([
            'success'          => true,
            'user'             => $user,
            'address_complete' => $addressComplete,
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

if ($method === 'POST') {
    // Avatar upload uses multipart — check content type
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isMultipart = str_contains($contentType, 'multipart/form-data');

    if ($isMultipart) {
        $action = $_POST['action'] ?? '';
    } else {
        header('Content-Type: application/json');
        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $body['action'] ?? '';
    }

    // ── Upload avatar ─────────────────────────────────────────
    if ($action === 'upload_avatar') {
        header('Content-Type: application/json');
        if (empty($_FILES['avatar'])) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
            exit;
        }
        $file = $_FILES['avatar'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Upload error.']);
            exit;
        }
        // Validate type
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mime    = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, GIF, WEBP allowed.']);
            exit;
        }
        if ($file['size'] > 3 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File too large (max 3MB).']);
            exit;
        }
        // Save file
        $ext     = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'][$mime];
        $dir     = __DIR__ . '/../uploads/avatars/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        // Delete old avatar
        $oldStmt = $pdo->prepare("SELECT avatar_url FROM users WHERE id = ?");
        $oldStmt->execute([$customerId]);
        $oldRow  = $oldStmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($oldRow['avatar_url'])) {
            $oldPath = __DIR__ . '/../' . ltrim($oldRow['avatar_url'], '/');
            if (file_exists($oldPath)) @unlink($oldPath);
        }
        $filename = 'avatar_' . $customerId . '_' . time() . '.' . $ext;
        $dest     = $dir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            echo json_encode(['success' => false, 'message' => 'Could not save file.']);
            exit;
        }
        $avatarUrl = 'uploads/avatars/' . $filename;
        $pdo->prepare("UPDATE users SET avatar_url = ?, updated_at = NOW() WHERE id = ?")
            ->execute([$avatarUrl, $customerId]);
        echo json_encode(['success' => true, 'avatar_url' => $avatarUrl]);
        exit;
    }

    // ── Update basic profile ──────────────────────────────────
    if ($action === 'update_profile') {
        $firstName = trim($body['first_name'] ?? '');
        $lastName  = trim($body['last_name']  ?? '');
        $phone     = trim($body['phone']      ?? '');
        $gender    = in_array($body['gender'] ?? '', ['male','female','other','']) ? ($body['gender'] ?? '') : '';
        $dob       = !empty($body['dob']) ? $body['dob'] : null;

        if (!$firstName || !$lastName) {
            echo json_encode(['success' => false, 'message' => 'First and last name are required.']);
            exit;
        }

        // Check if gender/dob columns exist
        try {
            $pdo->prepare(
                "UPDATE users SET first_name=?, last_name=?, phone=?, gender=?, date_of_birth=?, updated_at=NOW() WHERE id=?"
            )->execute([$firstName, $lastName, $phone, $gender ?: null, $dob, $customerId]);
        } catch (Exception $e) {
            // Fallback without gender/dob if columns don't exist
            $pdo->prepare(
                "UPDATE users SET first_name=?, last_name=?, phone=?, updated_at=NOW() WHERE id=?"
            )->execute([$firstName, $lastName, $phone, $customerId]);
        }

        echo json_encode(['success' => true, 'message' => 'Profile updated.']);
        exit;
    }

    // ── Update delivery address ───────────────────────────────
    if ($action === 'update_address') {
        $addressLine = trim($body['address_line'] ?? '');
        $barangay    = trim($body['barangay']     ?? '');
        $city        = trim($body['city']         ?? '');
        $province    = trim($body['province']     ?? '');
        $region      = trim($body['region']       ?? '');
        $zipCode     = trim($body['zip_code']     ?? '');
        $phone       = trim($body['phone']        ?? '');

        $errors = [];
        if (!$addressLine) $errors[] = 'Street address is required.';
        if (!$barangay)    $errors[] = 'Barangay is required.';
        if (!$city)        $errors[] = 'City/Municipality is required.';
        if (!$province)    $errors[] = 'Province is required.';
        if (!$zipCode)     $errors[] = 'ZIP code is required.';
        if (!$phone)       $errors[] = 'Phone number is required.';

        if ($errors) {
            echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
            exit;
        }

        $pdo->prepare(
            "UPDATE users SET
                address_line=?, barangay=?, city=?, province=?, region=?,
                zip_code=?, phone=?, address_verified=1, updated_at=NOW()
             WHERE id=?"
        )->execute([$addressLine, $barangay, $city, $province, $region, $zipCode, $phone, $customerId]);

        echo json_encode(['success' => true, 'message' => 'Address saved successfully.', 'address_verified' => true]);
        exit;
    }

    // ── Change password ───────────────────────────────────────
    if ($action === 'change_password') {
        $current = $body['current_password'] ?? '';
        $newPass = $body['new_password']     ?? '';
        $confirm = $body['confirm_password'] ?? '';

        if (!$current || !$newPass) {
            echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
            exit;
        }
        if (strlen($newPass) < 8) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters.']);
            exit;
        }
        if ($newPass !== $confirm) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
            exit;
        }

        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$customerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($current, $row['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit;
        }

        $pdo->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?')
            ->execute([password_hash($newPass, PASSWORD_BCRYPT), $customerId]);

        echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
