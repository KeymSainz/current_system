<?php
/**
 * Fix&Go — Switch to Seller Account
 *
 * Called when an approved customer clicks "Go to Supplier/Owner Dashboard".
 * Verifies the customer has an approved application, then switches the
 * PHP session to the new seller account and returns the seller user object.
 *
 * POST { customer_id, role }
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
setCORSHeaders();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

$pdo  = require __DIR__ . '/db.php';
$body = json_decode(file_get_contents('php://input'), true) ?? [];

$customerId = (int)($body['customer_id'] ?? $_SESSION['user_id'] ?? 0);
$role       = trim($body['role'] ?? '');

if (!$customerId || !in_array($role, ['supplier', 'owner'], true)) {
    jsonResponse(false, 'Invalid request.', [], 400);
}

// 1. Verify the customer has an approved application for this role
$stmt = $pdo->prepare(
    "SELECT id, email FROM seller_applications
     WHERE user_id = ? AND role = ? AND status = 'approved'
     ORDER BY reviewed_at DESC LIMIT 1"
);
$stmt->execute([$customerId, $role]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    jsonResponse(false, 'No approved application found for this role.', [], 403);
}

// 2. Find the seller user account (created during application, activated on approval)
$stmt = $pdo->prepare(
    "SELECT id, first_name, last_name, email, phone, role, is_verified
     FROM users
     WHERE email = ? AND role = ? AND is_active = 1
     LIMIT 1"
);
$stmt->execute([$app['email'], $role]);
$seller = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$seller) {
    jsonResponse(false, 'Seller account not found or not yet activated.', [], 404);
}

// 3. Switch the PHP session to the seller account
session_regenerate_id(true);
$_SESSION['user_id']        = (int)$seller['id'];
$_SESSION['user_role']      = $seller['role'];
$_SESSION['user_name']      = $seller['first_name'];
$_SESSION['_last_activity'] = time();

// 4. Return the seller user object so the frontend can update sessionStorage
jsonResponse(true, 'Switched to seller account.', [
    'user' => [
        'id'        => (int)$seller['id'],
        'firstName' => $seller['first_name'],
        'lastName'  => $seller['last_name'],
        'email'     => $seller['email'],
        'phone'     => $seller['phone'] ?? '',
        'role'      => $seller['role'],
        'verified'  => (bool)$seller['is_verified'],
    ],
    'redirect' => $role === 'owner'
        ? 'dashboard.html'
        : 'views/user/supplier/dashboard.html',
]);
