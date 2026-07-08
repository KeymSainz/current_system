<?php
/**
 * Fix&Go — Universal Profile API (all roles except customer)
 *
 * GET  ?action=get           → get profile from DB
 * POST action=update_profile → update name/email/phone
 * POST action=change_password → change password
 * POST action=upload_avatar  → upload profile photo (multipart)
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Cache-Control: no-store');

if (empty($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Login required.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$pdo    = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    header('Content-Type: application/json');
    $action = $_GET['action'] ?? 'get';

    if ($action === 'get') {
        $stmt = $pdo->prepare(
            "SELECT id, first_name, last_name, email, phone, role,
                    avatar_url, created_at, status,
                    COALESCE(shop_name,'')         AS shop_name,
                    COALESCE(address_line,'')      AS address_line,
                    COALESCE(barangay,'')          AS barangay,
                    COALESCE(city,'')              AS city,
                    COALESCE(province,'')          AS province,
                    COALESCE(region,'')            AS region,
                    COALESCE(zip_code,'')          AS zip_code,
                    COALESCE(address_verified,0)   AS address_verified
             FROM users WHERE id = ?"
        );
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'user' => $user]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── POST ──────────────────────────────────────────────────────
if ($method === 'POST') {
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
            echo json_encode(['success' => false, 'message' => 'Upload error code: ' . $file['error']]);
            exit;
        }
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
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
        $ext  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'][$mime];
        $dir  = __DIR__ . '/../uploads/avatars/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        // Delete old avatar
        $oldStmt = $pdo->prepare("SELECT avatar_url FROM users WHERE id = ?");
        $oldStmt->execute([$userId]);
        $oldRow  = $oldStmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($oldRow['avatar_url'])) {
            $oldPath = __DIR__ . '/../' . ltrim($oldRow['avatar_url'], '/');
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $dest     = $dir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            echo json_encode(['success' => false, 'message' => 'Could not save file.']);
            exit;
        }
        $avatarUrl = 'uploads/avatars/' . $filename;
        $pdo->prepare("UPDATE users SET avatar_url = ?, updated_at = NOW() WHERE id = ?")
            ->execute([$avatarUrl, $userId]);

        echo json_encode(['success' => true, 'avatar_url' => $avatarUrl]);
        exit;
    }

    // ── Update company/shop profile (sales_person, owner, supplier) ──────────
    if ($action === 'update_company_profile') {
        $companyName = trim($body['company_name'] ?? '');
        $email       = trim($body['email']        ?? '');
        $phone       = trim($body['phone']        ?? '');

        if (!$companyName) {
            echo json_encode(['success' => false, 'message' => 'Company/Shop name is required.']);
            exit;
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit;
        }
        try {
            // Store company name in shop_name column
            $pdo->prepare(
                "UPDATE users SET shop_name=?, phone=?, updated_at=NOW() WHERE id=?"
            )->execute([$companyName, $phone ?: null, $userId]);

            echo json_encode(['success' => true, 'message' => 'Company profile updated successfully.']);
        } catch (Exception $e) {
            error_log('[profile update_company_profile] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Update address ────────────────────────────────────────
    if ($action === 'update_address') {
        $addressLine = trim($body['address_line'] ?? '');
        $barangay    = trim($body['barangay']     ?? '');
        $city        = trim($body['city']         ?? '');
        $province    = trim($body['province']     ?? '');
        $region      = trim($body['region']       ?? '');
        $zipCode     = trim($body['zip_code']     ?? '');

        if (!$addressLine || !$barangay || !$city || !$province) {
            echo json_encode(['success' => false, 'message' => 'Address line, barangay, city, and province are required.']);
            exit;
        }

        // Mark as verified if all key fields are filled
        $verified = ($addressLine && $barangay && $city && $province && $zipCode) ? 1 : 0;

        try {
            $pdo->prepare(
                "UPDATE users
                 SET address_line=?, barangay=?, city=?, province=?, region=?, zip_code=?,
                     address_verified=?, updated_at=NOW()
                 WHERE id=?"
            )->execute([$addressLine, $barangay, $city, $province, $region, $zipCode, $verified, $userId]);

            echo json_encode([
                'success'          => true,
                'message'          => 'Address saved successfully.',
                'address_verified' => $verified,
            ]);
        } catch (Exception $e) {
            error_log('[profile update_address] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to save address.']);
        }
        exit;
    }

    // ── Update profile ────────────────────────────────────────
    if ($action === 'update_profile') {        $firstName = trim($body['first_name'] ?? '');
        $lastName  = trim($body['last_name']  ?? '');
        $email     = trim($body['email']      ?? '');
        $phone     = trim($body['phone']      ?? '');

        if (!$firstName || !$lastName || !$email) {
            echo json_encode(['success' => false, 'message' => 'First name, last name, and email are required.']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit;
        }
        // Check email uniqueness
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->execute([$email, $userId]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email is already in use by another account.']);
            exit;
        }

        $pdo->prepare(
            "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, updated_at=NOW() WHERE id=?"
        )->execute([$firstName, $lastName, $email, $phone, $userId]);

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
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

        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($current, $row['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit;
        }

        $pdo->prepare("UPDATE users SET password_hash=?, updated_at=NOW() WHERE id=?")
            ->execute([password_hash($newPass, PASSWORD_BCRYPT), $userId]);

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
