<?php
/**
 * Fix&Go — Email Service
 * Uses PHP's built-in mail() function — no external dependencies.
 */

$config = require __DIR__ . '/config.php';

/**
 * Send an OTP email.
 *
 * @param string $toEmail   Recipient email
 * @param string $toName    Recipient display name
 * @param string $otp       6-digit OTP code
 * @param string $purpose   'verify' | 'login' | 'reset'
 * @return bool
 */
function sendOTPEmail(string $toEmail, string $toName, string $otp, string $purpose = 'verify'): bool
{
    global $config;

    $fromEmail = $config['smtp_from_email'] ?? $config['smtp_user'] ?? 'noreply@fixandgo.com';
    $fromName  = $config['smtp_from_name']  ?? 'Fix&Go';
    $appName   = $config['app_name']        ?? 'Fix&Go';

    if ($purpose === 'reset') {
        $subject = $appName . ' — Password Reset Code';
        $body    = buildResetEmailHTML($toName, $otp);
    } else {
        $subject = $appName . ' — Verify Your Email';
        $body    = buildVerifyEmailHTML($toName, $otp);
    }

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
    $headers .= "Reply-To: {$fromEmail}\r\n";
    $headers .= "X-Mailer: Fix&Go\r\n";

    $toHeader = $toName ? "=?UTF-8?B?" . base64_encode($toName) . "?= <{$toEmail}>" : $toEmail;

    try {
        $result = mail($toHeader, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
        if (!$result) {
            error_log("[Fix&Go Mailer] mail() returned false for: {$toEmail}");
        }
        return (bool) $result;
    } catch (\Throwable $e) {
        error_log("[Fix&Go Mailer] Exception: " . $e->getMessage());
        return false;
    }
}

/**
 * Send a generic HTML email.
 */
function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
{
    global $config;

    $fromEmail = $config['smtp_from_email'] ?? $config['smtp_user'] ?? 'noreply@fixandgo.com';
    $fromName  = $config['smtp_from_name']  ?? 'Fix&Go';

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
    $headers .= "Reply-To: {$fromEmail}\r\n";

    $toHeader = $toName ? "=?UTF-8?B?" . base64_encode($toName) . "?= <{$toEmail}>" : $toEmail;

    try {
        return (bool) mail($toHeader, '=?UTF-8?B?' . base64_encode($subject) . '?=', $htmlBody, $headers);
    } catch (\Throwable $e) {
        error_log('[Fix&Go Mailer] sendEmail failed: ' . $e->getMessage());
        return false;
    }
}

// ── Email Templates ───────────────────────────────────────────────────────

function buildVerifyEmailHTML(string $name, string $otp): string
{
    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"/></head>
<body style="font-family:Segoe UI,sans-serif;background:#F4F6FB;margin:0;padding:2rem;">
  <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:16px;padding:2rem;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div style="text-align:center;margin-bottom:1.5rem;">
      <span style="background:linear-gradient(135deg,#e6a800,#c98f00);color:#000;border-radius:12px;padding:0.5rem 1.2rem;font-size:1.3rem;font-weight:800;">🔧 Fix&amp;Go</span>
    </div>
    <h2 style="color:#1A1A2E;font-size:1.3rem;margin-bottom:0.5rem;">Verify your email address</h2>
    <p style="color:#6C757D;font-size:0.9rem;">Hi <strong>{$name}</strong>, thanks for joining Fix&amp;Go! Enter the code below to activate your account.</p>
    <div style="text-align:center;margin:1.5rem 0;">
      <span style="display:inline-block;background:#FFF8E1;border:2px dashed #e6a800;border-radius:12px;padding:1rem 2rem;font-size:2.2rem;font-weight:800;letter-spacing:0.5rem;color:#e6a800;">{$otp}</span>
    </div>
    <p style="color:#6C757D;font-size:0.82rem;text-align:center;">This code expires in <strong>10 minutes</strong>. Do not share it with anyone.</p>
    <hr style="border:none;border-top:1px solid #DEE2E6;margin:1.5rem 0;"/>
    <p style="color:#aaa;font-size:0.75rem;text-align:center;">If you didn't create a Fix&amp;Go account, you can safely ignore this email.</p>
  </div>
</body>
</html>
HTML;
}

function buildResetEmailHTML(string $name, string $otp): string
{
    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"/></head>
<body style="font-family:Segoe UI,sans-serif;background:#F4F6FB;margin:0;padding:2rem;">
  <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:16px;padding:2rem;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div style="text-align:center;margin-bottom:1.5rem;">
      <span style="background:linear-gradient(135deg,#e6a800,#c98f00);color:#000;border-radius:12px;padding:0.5rem 1.2rem;font-size:1.3rem;font-weight:800;">🔧 Fix&amp;Go</span>
    </div>
    <h2 style="color:#1A1A2E;font-size:1.3rem;margin-bottom:0.5rem;">Password Reset Request</h2>
    <p style="color:#6C757D;font-size:0.9rem;">Hi <strong>{$name}</strong>, we received a request to reset your Fix&amp;Go password. Use the code below:</p>
    <div style="text-align:center;margin:1.5rem 0;">
      <span style="display:inline-block;background:#FFF8E1;border:2px dashed #e6a800;border-radius:12px;padding:1rem 2rem;font-size:2.2rem;font-weight:800;letter-spacing:0.5rem;color:#e6a800;">{$otp}</span>
    </div>
    <p style="color:#6C757D;font-size:0.82rem;text-align:center;">This code expires in <strong>10 minutes</strong>.</p>
    <hr style="border:none;border-top:1px solid #DEE2E6;margin:1.5rem 0;"/>
    <p style="color:#aaa;font-size:0.75rem;text-align:center;">If you didn't request a password reset, please ignore this email.</p>
  </div>
</body>
</html>
HTML;
}
