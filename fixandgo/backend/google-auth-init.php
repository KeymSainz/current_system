<?php
/**
 * Fix&Go — Initiate Google OAuth Flow
 * GET /backend/google-auth-init.php
 *
 * Generates a state token (CSRF protection for OAuth),
 * then redirects the user to Google's consent screen.
 */

require_once __DIR__ . '/helpers.php';

startSecureSession();

$config = require __DIR__ . '/config.php';

// Generate and store state token
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$params = http_build_query([
    'client_id'     => $config['google_client_id'],
    'redirect_uri'  => $config['google_redirect_uri'],
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $state,
    'access_type'   => 'online',
    'prompt'        => 'select_account',
]);

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
exit;
