<?php
/**
 * Fix&Go — Become a Technician Application Endpoint
 * POST multipart/form-data
 *
 * Fields:
 *   firstName, lastName, middleName, suffix
 *   email, password, confirmPassword
 *   entityType (sole_proprietorship|corporation|one_person_corp)
 *   businessName, generalLocation, shopAddress, zipCode
 *   addressLat, addressLng
 *   specializations (comma-separated)
 *   experienceYrs
 *   businessEmail
 *   Files: govIdFile, certFile, dtiFile, birFile
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

// ── Auth ──────────────────────────────────────────────────────────────────
$sessionUserId   = $_SESSION['user_id']   ?? null;
$sessionUserRole = $_SESSION['user_role'] ?? null;

if (!$sessionUserId && !empty($_POST['customer_id'])) {
    $cid  = (int)$_POST['customer_id'];
    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = ? AND role = 'customer' AND is_active = 1 LIMIT 1");
    $stmt->execute([$cid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) { $sessionUserId = $row['id']; $sessionUserRole = $row['role']; }
}

if (!$sessionUserId || $sessionUserRole !== 'customer') {
    jsonResponse(false, 'You must be logged in as a customer to apply.', [], 403);
}
$customerId = (int)$sessionUserId;

// ── Input ─────────────────────────────────────────────────────────────────
$firstName       = sanitizeString($_POST['firstName']       ?? '');
$lastName        = sanitizeString($_POST['lastName']        ?? '');
$middleName      = sanitizeString($_POST['middleName']      ?? '');
$suffix          = sanitizeString($_POST['suffix']          ?? '');
$email           = trim($_POST['email']                     ?? '');
$password        = $_POST['password']                       ?? '';
$confirmPw       = $_POST['confirmPassword']                ?? '';
$entityType      = trim($_POST['entityType']                ?? 'sole_proprietorship');
$businessName    = sanitizeString($_POST['businessName']    ?? '');
$generalLocation = sanitizeString($_POST['generalLocation'] ?? '');
$shopAddress     = sanitizeString($_POST['shopAddress']     ?? '');
$zipCode         = trim($_POST['zipCode']                   ?? '');
$addressLat      = (float)($_POST['addressLat']             ?? 0);
$addressLng      = (float)($_POST['addressLng']             ?? 0);
$specializations = sanitizeString($_POST['specializations'] ?? '');
$experienceYrs   = (int)($_POST['experienceYrs']            ?? 0);
$businessEmail   = trim($_POST['businessEmail']             ?? '');

// ── Validation ────────────────────────────────────────────────────────────
$errors = [];
if (strlen($firstName) < 2)  $errors[] = 'First name is required.';
if (strlen($lastName)  < 2)  $errors[] = 'Last name is required.';
if (!validateEmail($email))  $errors[] = 'Valid email address is required.';
if (strlen($password)  < 8)  $errors[] = 'Password must be at least 8 characters.';
if ($password !== $confirmPw) $errors[] = 'Passwords do not match.';
if (empty($businessName))    $errors[] = 'Business/Trade name is required.';
if (empty($shopAddress))     $errors[] = 'Business address is required.';
if (empty($zipCode))         $errors[] = 'ZIP code is required.';
if (empty($specializations)) $errors[] = 'Please select at least one specialization.';
if (!in_array($entityType, ['sole_proprietorship','corporation','one_person_corp'], true))
    $errors[] = 'Invalid entity type.';

if (empty($_FILES['govIdFile']['name'])) $errors[] = 'Government-issued ID is required.';

if (!empty($errors)) {
    jsonResponse(false, implode(' ', $errors), [], 422);
}

// ── Duplicate check ───────────────────────────────────────────────────────
$dup = $pdo->prepare(
    "SELECT id FROM seller_applications
     WHERE user_id = ? AND role = 'phone_technician' AND status = 'pending' LIMIT 1"
);
$dup->execute([$customerId]);
if ($dup->fetch()) {
    jsonResponse(false, 'You already have a pending technician application. Please wait for admin review.', [], 409);
}

// ── Email uniqueness ──────────────────────────────────────────────────────
$emailCheck = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$emailCheck->execute([$email]);
if ($emailCheck->fetch()) {
    jsonResponse(false, 'This email is already registered. Use a different email for your technician account.', [], 409);
}

// ── Upload documents ──────────────────────────────────────────────────────
$uploadDir = __DIR__ . '/../uploads/applications/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

function uploadTechDoc(string $fieldName, string $uploadDir): ?string {
    if (empty($_FILES[$fieldName]['name'])) return null;
    $file    = $_FILES[$fieldName];
    $allowed = ['image/jpeg','image/jpg','image/png','image/webp','application/pdf'];
    $max     = 5 * 1024 * 1024;
    if (!in_array($file['type'], $allowed, true) || $file['size'] > $max) return null;
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $fieldName . '_' . uniqid() . '.' . $ext;
    $dest     = $uploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return null;
    return 'uploads/applications/' . $filename;
}

$docGovId = uploadTechDoc('govIdFile', $uploadDir);
$docCert  = uploadTechDoc('certFile',  $uploadDir);
$docDti   = uploadTechDoc('dtiFile',   $uploadDir);
$docBir   = uploadTechDoc('birFile',   $uploadDir);

if (!$docGovId) jsonResponse(false, 'Failed to upload Government ID. Check file type and size (max 5MB).', [], 422);

// ── Save application ──────────────────────────────────────────────────────
$stmt = $pdo->prepare(
    "INSERT INTO seller_applications
     (user_id, role, first_name, last_name, middle_name, suffix,
      email, company_name, shop_name, shop_address, address_lat, address_lng,
      specializations, experience_yrs, entity_type, business_name,
      general_location, zip_code, business_email,
      doc_gov_id, doc_cert, doc_dti, doc_bir,
      status, submitted_at)
     VALUES (?, 'phone_technician', ?, ?, ?, ?,
             ?, ?, ?, ?, ?, ?,
             ?, ?, ?, ?,
             ?, ?, ?,
             ?, ?, ?, ?,
             'pending', NOW())"
);
$stmt->execute([
    $customerId,
    $firstName, $lastName, $middleName ?: null, $suffix ?: null,
    $email,
    $firstName . ' ' . $lastName,  // company_name fallback
    null,                           // shop_name
    $shopAddress,
    $addressLat ?: null, $addressLng ?: null,
    $specializations, $experienceYrs,
    $entityType, $businessName,
    $generalLocation, $zipCode, $businessEmail ?: null,
    $docGovId, $docCert ?: null, $docDti ?: null, $docBir ?: null,
]);
$applicationId = $pdo->lastInsertId();

// ── Create inactive user account ──────────────────────────────────────────
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => $config['bcrypt_cost']]);
$pdo->prepare(
    "INSERT INTO users
     (first_name, last_name, email, password_hash, role, is_verified, is_active,
      application_status, created_at)
     VALUES (?, ?, ?, ?, 'phone_technician', 1, 0, 'pending', NOW())"
)->execute([$firstName, $lastName, $email, $passwordHash]);

// ── Notify admin ──────────────────────────────────────────────────────────
try {
    require_once __DIR__ . '/mailer.php';
    $adminEmail = $config['smtp_user'];
    $subject    = "New Technician Application — Fix&Go #$applicationId";
    $body = "
<h2>New Technician Application</h2>
<p>A customer has applied to become a phone technician.</p>
<table style='border-collapse:collapse;width:100%;'>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Application ID</td><td style='padding:6px 12px;'>#{$applicationId}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Name</td><td style='padding:6px 12px;'>{$firstName} {$lastName}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Email</td><td style='padding:6px 12px;'>{$email}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Business</td><td style='padding:6px 12px;'>{$businessName}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Address</td><td style='padding:6px 12px;'>{$shopAddress}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Specializations</td><td style='padding:6px 12px;'>{$specializations}</td></tr>
  <tr><td style='padding:6px 12px;font-weight:bold;'>Experience</td><td style='padding:6px 12px;'>{$experienceYrs} year(s)</td></tr>
</table>
<p>Please log in to the Admin Dashboard to review this application.</p>
";
    sendEmail($adminEmail, 'Fix&Go Admin', $subject, $body);
} catch (Exception $e) { /* mail failure is non-fatal */ }

jsonResponse(true, 'Application submitted! Our team will review it within 1–2 business days.', [
    'application_id' => $applicationId,
]);
