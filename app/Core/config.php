<?php
/**
 * Fix&Go — Config Loader
 * Finds and loads config.php from wherever it lives.
 *
 * Search order:
 *  1. backend/config.php          (flat deploy: htdocs/backend/)
 *  2. fixandgo/backend/config.php (source/nested deploy)
 *  3. Built-in fallback values
 */

$root = dirname(__DIR__, 2); // htdocs root (two levels up from app/Core/)

$locations = [
    $root . '/backend/config.php',
    $root . '/fixandgo/backend/config.php',
];

foreach ($locations as $loc) {
    if (file_exists($loc)) {
        return require $loc;
    }
}

// ── Fallback (fill in manually on server if config file is missing) ────────
$config = [];
$config['db_host']    = 'sql110.infinityfree.com';
$config['db_name']    = 'if0_42189730_fixandgo';
$config['db_user']    = 'if0_42189730';
$config['db_pass']    = '';
$config['db_charset'] = 'utf8mb4';
$config['app_env']    = 'production';
$config['app_url']    = 'fixandgo.freedev.app';
$config['app_name']   = 'Fix&Go';
$config['otp_expiry']            = 600;
$config['otp_max_attempts']      = 3;
$config['login_max_attempts']    = 3;
$config['login_lockout_seconds'] = 900;
$config['remember_lifetime']     = 2592000;
$config['session_lifetime']      = 600;

return $config;
