<?php
/**
 * Fix&Go — Backend Configuration EXAMPLE
 *
 * Copy this file to config.php and fill in your real values.
 * NEVER commit config.php — it is in .gitignore.
 */

// ── Database ──────────────────────────────────────────────────────────────
$config['db_host']    = 'sql110.infinityfree.com';   // from cPanel → MySQL Databases
$config['db_name']    = 'if0_XXXXXXXX_fixandgo';     // format: <username>_<dbname>
$config['db_user']    = 'if0_XXXXXXXX';
$config['db_pass']    = 'YOUR_DB_PASSWORD';
$config['db_charset'] = 'utf8mb4';

// ── App ───────────────────────────────────────────────────────────────────
$config['app_name']    = 'Fix&Go';
$config['app_url']     = 'yourdomain.freedev.app';   // your live domain
$config['app_env']     = 'production';               // 'development' or 'production'

// ── Session ───────────────────────────────────────────────────────────────
$config['session_lifetime']  = 600;      // 10 minutes
$config['remember_lifetime'] = 2592000;  // 30 days

// ── OTP ───────────────────────────────────────────────────────────────────
$config['otp_expiry']       = 600;
$config['otp_max_attempts'] = 3;

// ── Rate Limiting ─────────────────────────────────────────────────────────
$config['login_max_attempts']      = 3;
$config['login_lockout_seconds']   = 900;
$config['register_max_attempts']   = 3;
$config['register_window_seconds'] = 3600;

// ── Email (PHPMailer / SMTP) ───────────────────────────────────────────────
$config['smtp_host']       = 'smtp.gmail.com';
$config['smtp_port']       = 587;
$config['smtp_secure']     = 'tls';
$config['smtp_user']       = 'your_email@gmail.com';
$config['smtp_pass']       = 'YOUR_GMAIL_APP_PASSWORD';  // Google App Password
$config['smtp_from_email'] = 'your_email@gmail.com';
$config['smtp_from_name']  = 'Fix&Go';

// ── Google OAuth ──────────────────────────────────────────────────────────
// Get from: https://console.cloud.google.com/ → APIs & Services → Credentials
$config['google_client_id']     = 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com';
$config['google_client_secret'] = 'YOUR_GOOGLE_CLIENT_SECRET';
$config['google_redirect_uri']  = 'https://yourdomain.freedev.app/backend/google-callback.php';

// ── PayMongo ──────────────────────────────────────────────────────────────
$config['paymongo_mode']           = 'test';   // 'test' only
$config['paymongo_secret_key']     = 'sk_test_YOUR_SECRET_KEY';
$config['paymongo_public_key']     = 'pk_test_YOUR_PUBLIC_KEY';
$config['paymongo_webhook_secret'] = '';
$config['paymongo_api_url']        = 'https://api.paymongo.com/v1';

// ── Security ──────────────────────────────────────────────────────────────
$config['csrf_token_len'] = 32;
$config['bcrypt_cost']    = 12;

return $config;
