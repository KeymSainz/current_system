<?php
/**
 * Fix&Go — Seller Application Endpoint
 * POST multipart/form-data
 *
 * Accepts the full seller application from the Seller Centre modal,
 * stores documents, saves the application, and notifies the admin.
 *
 * Fields:
 *   role, firstName, lastName, email, phone, companyName,
 *   shopName (owner only), password, confirmPassword
 *   Files: govIdFile, birFile, dtiFile (owner), bankFile
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
setCORSHeaders();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

$pdo    = require __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';

// ── Require logged-in customer ────────────────────────────────────────────
// Support both PHP session auth and a fallback lookup by posted user_id
$sessionUserId   = $_SESSION['user_id']   ?? null;
$sessionUserRole = $_SESSION['user_role'] ?? null;

// If no PHP session, try to identify via a posted customer_id field
// (the frontend can pass it as a hidden field for sessionStorage-based auth)
if (!$sessionUserId && !empty($_POST['customer_id'])) {
    $cid  = (int)$_POST['customer_id'];
    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = ? AND role = 'customer' AND is_active = 1 LIMIT 1");
    $stmt->execute([$cid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $sessionUserId   = $row['id'];
        $sessionUserRole = $row['role'];
    }
}

if (!$sessionUserId || $sessionUserRole !== 'customer') {
    jsonResponse(false, 'You must be logged in as a customer to apply.', [], 403);
}
$customerId = (int)$sessionUserId;

// ── Input ─────────────────────────────────────────────────────────────────
$role        = trim($_POST['role']        ?? '');
$firstName   = sanitizeString($_POST['firstName']   ?? '');
$lastName    = sanitizeString($_POST['lastName']    ?? '');
$email       = trim($_POST['email']       ?? '');
$phone       = trim($_POST['phone']       ?? '');
$companyName = sanitizeString($_POST['companyName'] ?? '');
$shopName    = sanitizeString($_POST['shopName']    ?? '');
$password    = $_POST['password']         ?? '';
$confirmPw   = $_POST['confirmPassword']  ?? '';

// ── Validation ────────────────────────────────────────────────────────────
$errors = [];

if (!in_array($role, ['supplier', 'owner'], true)) {
    $errors[] = 'Invalid role selected.';
}
if (strlen($firstName) < 2)  $errors[] = 'First name is required.';
if (strlen($lastName)  < 2)  $errors[] = 'Last name is required.';
if (!validateEmail($email))  $errors[] = 'Valid email address is required.';
if (empty($phone))           $errors[] = 'Phone number is required.';
if (empty($companyName))     $errors[] = 'Company name is required.';
if ($role === 'owner' && empty($shopName)) $errors[] = 'Shop name is required for Shop Owner.';
if (strlen($password) < 8)  $errors[] = 'Password must be at least 8 characters.';
if ($password !== $confirmPw) $errors[] = 'Passwords do not match.';

// Required documents
if (empty($_FILES['govIdFile']['name']))  $errors[] = 'Government-issued ID is required.';
if (empty($_FILES['bankFile']['name']))   $errors[] = 'Bank account proof is required.';
if ($role === 'owner' && empty($_FILES['dtiFile']['name'])) {
    $errors[] = 'DTI/SEC registration is required for Shop Owners.';
}

if (!empty($errors)) {
    jsonResponse(false, implode(' ', $errors), [], 422);
}

// ── Check for duplicate application ──────────────────────────────────────
$dup = $pdo->prepare(
    "SELECT id FROM seller_applications
     WHERE user_id = ? AND role = ? AND status = 'pending' LIMIT 1"
);
$dup->execute([$customerId, $role]);
if ($dup->fetch()) {
    jsonResponse(false, 'You already have a pending ' . $role . ' application. Please wait for admin review.', [], 409);
}

// ── Check seller email not already taken ──────────────────────────────────
$emailCheck = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$emailCheck->execute([$email]);
if ($emailCheck->fetch()) {
    jsonResponse(false, 'This email is already registered. Use a different email for your seller account.', [], 409);
}

// ── Upload documents ──────────────────────────────────────────────────────
$uploadDir = __DIR__ . '/../uploads/applications/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function uploadDoc(string $fieldName, string $uploadDir): ?string {
    if (empty($_FILES[$fieldName]['name'])) return null;

    $file     = $_FILES[$fieldName];
    $allowed  = ['image/jpeg','image/jpg','image/png','image/webp','application/pdf'];
    $maxBytes = 5 * 1024 * 1024;

    if (!in_array($file['type'], $allowed, true)) return null;
    if ($file['size'] > $maxBytes) return null;

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $fieldName . '_' . uniqid() . '.' . $ext;
    $dest     = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) return null;
    return 'uploads/applications/' . $filename;
}

$docGovId = uploadDoc('govIdFile', $uploadDir);
$docBir   = uploadDoc('birFile',   $uploadDir);
$docDti   = uploadDoc('dtiFile',   $uploadDir);
$docBank  = uploadDoc('bankFile',  $uploadDir);

if (!$docGovId) jsonResponse(false, 'Failed to upload Government ID. Check file type and size (max 5MB).', [], 422);
if (!$docBank)  jsonResponse(false, 'Failed to upload Bank proof. Check file type and size (max 5MB).', [], 422);
if ($role === 'owner' && !$docDti) jsonResponse(false, 'Failed to upload DTI/SEC document.', [], 422);

// ── Save application ──────────────────────────────────────────────────────
$stmt = $pdo->prepare(
    "INSERT INTO seller_applications
     (user_id, role, first_name, last_name, email, phone, company_name, shop_name,
      doc_gov_id, doc_bir, doc_dti, doc_bank, status, submitted_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())"
);
$stmt->execute([
    $customerId, $role, $firstName, $lastName, $email, $phone,
    $companyName, $shopName ?: null,
    $docGovId, $docBir ?: null, $docDti ?: null, $docBank
]);
$applicationId = $pdo->lastInsertId();

// ── Also create the user account (inactive until approved) ────────────────
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => $config['bcrypt_cost']]);
$stmt = $pdo->prepare(
    "INSERT INTO users
     (first_name, last_name, email, phone, password_hash, role, is_verified, is_active,
      application_status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, 1, 0, 'pending', NOW())"
);
$stmt->execute([$firstName, $lastName, $email, $phone, $passwordHash, $role]);

// ── Notify admin via email ────────────────────────────────────────────────
require_once __DIR__ . '/mailer.php';
$adminEmail = $config['smtp_user']; // send to the configured SMTP sender (admin)
$roleLabel  = $role === 'owner' ? 'Shop Owner' : 'Supplier';

$subject = "New {$roleLabel} Application — Fix&Go Seller Centre";
$body    = "
<h2>New {$roleLabel} Application</h2>
<p>A new seller application has been submitted and is awaiting your review.</p>
<table style='border-collapse:collapse;width:100%;'>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Application ID</td><td style='padding:6px 12px;'>#{$applicationId}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Role</td><td style='padding:6px 12px;'>{$roleLabel}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Name</td><td style='padding:6px 12px;'>{$firstName} {$lastName}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Email</td><td style='padding:6px 12px;'>{$email}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Phone</td><td style='padding:6px 12px;'>{$phone}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Company</td><td style='padding:6px 12px;'>{$companyName}</td></tr>
  " . ($shopName ? "<tr><td style='padding:6px 12px;font-weight:bold;'>Shop Name</td><td style='padding:6px 12px;'>{$shopName}</td></tr>" : '') . "
</table>
<p style='margin-top:1rem;'>
  <strong>Documents submitted:</strong><br>
  • Government ID: " . ($docGovId ? '✅ Uploaded' : '❌ Missing') . "<br>
  • BIR Certificate: " . ($docBir ? '✅ Uploaded' : '⚠️ Not provided') . "<br>
  • DTI/SEC: " . ($docDti ? '✅ Uploaded' : ($role === 'owner' ? '❌ Missing' : 'N/A')) . "<br>
  • Bank Proof: " . ($docBank ? '✅ Uploaded' : '❌ Missing') . "
</p>
<p>Please log in to the <strong>Admin Dashboard</strong> to review and approve or reject this application.</p>
";

sendEmail($adminEmail, 'Fix&Go Admin', $subject, $body);

jsonResponse(true, 'Application submitted successfully! Our team will review it within 1–2 business days. You will receive an email once approved.', [
    'application_id' => $applicationId,
]);
