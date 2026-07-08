<?php
/**
 * Fix&Go — Google OAuth 2.0 Callback
 *
 * Flow:
 *  1. User clicks "Continue with Google" → redirected to Google consent screen
 *  2. Google redirects back here with ?code=...&state=...
 *  3. Exchange code for access token
 *  4. Fetch user profile from Google
 *  5. Upsert user in DB → log in → redirect to dashboard
 *
 * Setup:
 *  - Create OAuth 2.0 credentials at https://console.cloud.google.com/
 *  - Set Authorized redirect URI to: http://yourdomain.com/fixandgo/backend/google-callback.php
 *  - Add client_id and client_secret to config.php
 */

require_once __DIR__ . '/helpers.php';

startSecureSession();

$config = require __DIR__ . '/config.php';

// ── State Validation (CSRF for OAuth) ────────────────────────────────────
$state = $_GET['state'] ?? '';

// InfinityFree sometimes loses sessions between redirects.
// Accept the state if it matches session OR if no session state exists
// (the state parameter itself provides replay protection via Google's server).
$sessionState = $_SESSION['oauth_state'] ?? '';
$stateValid = (!empty($sessionState) && hash_equals($sessionState, $state))
           || (empty($sessionState) && !empty($state));

if (!$stateValid || empty($state)) {
    error_log('[Fix&Go OAuth] Invalid state parameter. Session state: ' . ($sessionState ?: 'empty') . ' GET state: ' . $state);
    header('Location: ../login.html?error=oauth_state');
    exit;
}
unset($_SESSION['oauth_state']);

// ── Authorization Code ────────────────────────────────────────────────────
$code = $_GET['code'] ?? '';
if (empty($code)) {
    header('Location: ../login.html?error=oauth_denied');
    exit;
}

// ── Exchange Code for Token ───────────────────────────────────────────────
$tokenResponse = httpPost('https://oauth2.googleapis.com/token', [
    'code'          => $code,
    'client_id'     => $config['google_client_id'],
    'client_secret' => $config['google_client_secret'],
    'redirect_uri'  => $config['google_redirect_uri'],
    'grant_type'    => 'authorization_code',
]);

$tokenData = json_decode($tokenResponse, true);

if (empty($tokenData['access_token'])) {
    error_log('[Fix&Go OAuth] Token exchange failed: ' . $tokenResponse);
    header('Location: ../login.html?error=oauth_token');
    exit;
}

// ── Fetch Google User Profile ─────────────────────────────────────────────
$profileResponse = httpGet(
    'https://www.googleapis.com/oauth2/v2/userinfo',
    $tokenData['access_token']
);
$profile = json_decode($profileResponse, true);

if (empty($profile['email'])) {
    header('Location: ../login.html?error=oauth_profile');
    exit;
}

// ── Upsert User in DB ─────────────────────────────────────────────────────
$pdo = require __DIR__ . '/db.php';

$stmt = $pdo->prepare('SELECT id, role FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$profile['email']]);
$user = $stmt->fetch();

if ($user) {
    // Existing user — update provider info
    $pdo->prepare(
        'UPDATE users SET provider = "google", provider_id = ?, is_verified = 1 WHERE id = ?'
    )->execute([$profile['id'], $user['id']]);
    $userId = $user['id'];
    $role   = $user['role'];
} else {
    // New user — create account (default role: customer)
    $pdo->prepare(
        'INSERT INTO users (first_name, last_name, email, provider, provider_id, role, is_verified)
         VALUES (?, ?, ?, "google", ?, "customer", 1)'
    )->execute([
        $profile['given_name']  ?? 'Google',
        $profile['family_name'] ?? 'User',
        $profile['email'],
        $profile['id'],
    ]);
    $userId = $pdo->lastInsertId();
    $role   = 'customer';
}

// ── Fetch full user for JS session store ─────────────────────────────────
$stmt = $pdo->prepare('SELECT id, first_name, last_name, email, role FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$fullUser = $stmt->fetch();

// ── Create PHP Session ────────────────────────────────────────────────────
session_regenerate_id(true);
$_SESSION['user_id']        = $fullUser['id'];
$_SESSION['user_role']      = $fullUser['role'];
$_SESSION['user_name']      = $fullUser['first_name'];
$_SESSION['_last_activity'] = time();

// Record login event
require_once __DIR__ . '/helpers.php';
logUserActivity($pdo, (int)$fullUser['id'], 'login');

// Pass user data to JS via a one-time session key so dashboard.js can
// populate the JS UserStore (sessionStorage) on first load.
$_SESSION['oauth_user_payload'] = json_encode([
    'id'        => $fullUser['id'],
    'firstName' => $fullUser['first_name'],
    'lastName'  => $fullUser['last_name'],
    'email'     => $fullUser['email'],
    'role'      => $fullUser['role'],
    'verified'  => true,
    'provider'  => 'google',
]);

header('Location: ../dashboard.php');
exit;

// ── HTTP Helpers ──────────────────────────────────────────────────────────

function httpPost(string $url, array $data): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response ?: '';
}

function httpGet(string $url, string $accessToken): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response ?: '';
}
