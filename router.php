<?php
/**
 * Fix&Go — MVC Front Controller / Router
 *
 * All HTTP requests come here first (via .htaccess rewrite).
 * - /api/* requests → MVC controllers
 * - Everything else → serve static pages as before
 *
 * Deploy this file to htdocs/ alongside login.html, dashboard.php, etc.
 */

// Bootstrap MVC
require_once __DIR__ . '/app/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = '/' . ltrim($uri, '/');

// ── Route API and VIEW requests through MVC ──────────────────────────────
if (strpos($uri, '/api/') === 0 || strpos($uri, '/views/') === 0) {
    $router->dispatch();
    exit;
}

// ── Non-API: let the web server serve files normally ─────────────────────
// This router only intercepts /api/* — all other files (HTML, PHP, CSS, JS)
// are served directly by Apache/the web server.
// If we reach here from an .htaccess rewrite and the file exists, serve it.
$filePath = __DIR__ . $uri;

if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    // Let Apache serve it (return false in PHP built-in server context)
    return false;
}

// Default: serve index.php (landing page)
require __DIR__ . '/index.php';
