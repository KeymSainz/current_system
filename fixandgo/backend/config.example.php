<?php
/**
 * Fix&Go Configuration Template
 * 
 * Copy this file to config.php and fill in your actual values
 * DO NOT commit config.php to version control!
 */

// ============================================================
// DATABASE CONFIGURATION
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'fixandgo');
define('DB_USER', 'your_database_username');
define('DB_PASS', 'your_database_password');

// ============================================================
// PAYMONGO CONFIGURATION
// ============================================================
// Get your keys from: https://dashboard.paymongo.com/developers
define('PAYMONGO_SECRET_KEY', 'sk_test_your_secret_key_here');
define('PAYMONGO_PUBLIC_KEY', 'pk_test_your_public_key_here');

// ============================================================
// EMAIL CONFIGURATION (PHPMailer)
// ============================================================
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_app_specific_password');
define('SMTP_FROM', 'your_email@gmail.com');
define('SMTP_FROM_NAME', 'Fix&Go');

// ============================================================
// SITE CONFIGURATION
// ============================================================
define('SITE_URL', 'http://localhost/fixandgo');
define('SITE_NAME', 'Fix&Go');

// ============================================================
// SESSION CONFIGURATION
// ============================================================
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds

// ============================================================
// SECURITY CONFIGURATION
// ============================================================
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// ============================================================
// FILE UPLOAD CONFIGURATION
// ============================================================
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);

// ============================================================
// GOOGLE OAUTH (Optional)
// ============================================================
// Get credentials from: https://console.cloud.google.com/
define('GOOGLE_CLIENT_ID', 'your_google_client_id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'your_google_client_secret');
define('GOOGLE_REDIRECT_URI', 'http://localhost/fixandgo/backend/google-callback.php');
