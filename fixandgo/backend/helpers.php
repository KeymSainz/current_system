<?php
/**
 * Fix&Go — Backend Helpers
 *
 * Covers: CSRF, rate limiting, input sanitization, JSON responses,
 * session management, and secure cookie helpers.
 */

// ── JSON Response ─────────────────────────────────────────────────────────

function jsonResponse(bool $success, string $message, array $data = [], int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    header('X-Content-Type-Options: nosniff');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

// ── CORS for localhost development ────────────────────────────────────────
// Allows fetch() calls from the HTML pages served by Apache on the same host.
function setCORSHeaders(): void
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    // Allow same-origin requests from localhost
    if (preg_match('#^https?://localhost(:\d+)?$#', $origin)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
    }
    // Handle preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

// ── Session ───────────────────────────────────────────────────────────────

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                   || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    // ── Enforce 10-minute idle timeout ───────────────────────────────────
    $timeout = 600; // 10 minutes

    if (!empty($_SESSION['user_id'])) {
        $lastActivity = $_SESSION['_last_activity'] ?? time();

        if ((time() - $lastActivity) > $timeout) {
            // Session expired — destroy it and signal the client
            session_unset();
            session_destroy();

            // Only send JSON if this is an API request (not a page load)
            $isApi = (
                isset($_SERVER['HTTP_ACCEPT']) &&
                str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
            ) || (
                isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            );

            if ($isApi) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'success'   => false,
                    'message'   => 'Session expired. Please log in again.',
                    'expired'   => true,
                ]);
                exit;
            }
            return; // Let the calling script handle the redirect
        }

        // Refresh the last-activity timestamp on every valid request
        $_SESSION['_last_activity'] = time();
    }
}

// ── CSRF ──────────────────────────────────────────────────────────────────

/**
 * Generate and store a CSRF token in the session.
 */
function generateCSRFToken(): string
{
    startSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate the CSRF token from the request.
 * Accepts token from POST body or X-CSRF-Token header.
 * Skipped in development mode to avoid localhost session issues.
 */
function validateCSRF(): void
{
    startSecureSession();

    // Load config to check environment
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }

    // Skip CSRF check in development (localhost)
    if (($config['app_env'] ?? 'development') === 'development') {
        return;
    }

    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        jsonResponse(false, 'Invalid or missing CSRF token.', [], 403);
    }
}

// ── Rate Limiting (DB-backed) ─────────────────────────────────────────────

/**
 * Check and record a rate-limited action.
 *
 * @param PDO    $pdo
 * @param string $identifier  IP address or user ID
 * @param string $action      e.g. 'login', 'register', 'otp'
 * @param int    $maxAttempts
 * @param int    $windowSecs
 * @return bool  true = allowed, false = rate limited
 */
function checkRateLimit(PDO $pdo, string $identifier, string $action, int $maxAttempts, int $windowSecs): bool
{
    $windowStart = date('Y-m-d H:i:s', time() - $windowSecs);

    // Count recent attempts
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM rate_limits
         WHERE identifier = ? AND action = ? AND attempted_at > ?'
    );
    $stmt->execute([$identifier, $action, $windowStart]);
    $count = (int) $stmt->fetchColumn();

    if ($count >= $maxAttempts) {
        return false;
    }

    // Record this attempt
    $stmt = $pdo->prepare(
        'INSERT INTO rate_limits (identifier, action, attempted_at) VALUES (?, ?, NOW())'
    );
    $stmt->execute([$identifier, $action]);

    return true;
}

function resetRateLimit(PDO $pdo, string $identifier, string $action): void
{
    $stmt = $pdo->prepare('DELETE FROM rate_limits WHERE identifier = ? AND action = ?');
    $stmt->execute([$identifier, $action]);
}

// ── Input Sanitization ────────────────────────────────────────────────────

/**
 * Sanitize a string for safe output/storage.
 * Note: Use parameterized queries for DB — this is for display safety.
 */
function sanitizeString(string $input): string
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function validateEmail(string $email): bool
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password meets Fix&Go requirements.
 */
function validatePassword(string $password): bool
{
    return strlen($password) >= 8
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[0-9]/', $password);
}

// ── OTP ───────────────────────────────────────────────────────────────────

/**
 * Generate a cryptographically secure 6-digit OTP.
 */
function generateOTP(): string
{
    return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

// ── Secure Cookie ─────────────────────────────────────────────────────────

function setRememberMeCookie(string $token, int $lifetime): void
{
    setcookie('fg_remember', $token, [
        'expires'  => time() + $lifetime,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
}

function clearRememberMeCookie(): void
{
    setcookie('fg_remember', '', time() - 3600, '/');
}

// ── Client IP ─────────────────────────────────────────────────────────────

function getClientIP(): string
{
    // Prefer real IP; be cautious with X-Forwarded-For in production
    // (validate against trusted proxy list)
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// ── Activity Logging ──────────────────────────────────────────────────────

/**
 * Record a login or logout event to user_activity_logs.
 * Also updates last_login_at / last_logout_at on the users row.
 *
 * @param PDO    $pdo
 * @param int    $userId
 * @param string $action  'login' | 'logout' | 'session_expired'
 */
function logUserActivity(PDO $pdo, int $userId, string $action): void
{
    $ip        = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512);

    try {
        // Insert activity log row
        $pdo->prepare(
            "INSERT INTO user_activity_logs (user_id, action, ip_address, user_agent, created_at)
             VALUES (?, ?, ?, ?, NOW())"
        )->execute([$userId, $action, $ip, $userAgent]);

        // Update the shortcut column on users
        if ($action === 'login') {
            $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?")
                ->execute([$userId]);
        } elseif ($action === 'logout' || $action === 'session_expired') {
            $pdo->prepare("UPDATE users SET last_logout_at = NOW() WHERE id = ?")
                ->execute([$userId]);
        }
    } catch (\Throwable $e) {
        // Never let logging break the main flow
        error_log('logUserActivity error: ' . $e->getMessage());
    }
}
