<?php
/**
 * Fix&Go — Email Service (PHPMailer)
 *
 * Sends OTP and notification emails via SMTP.
 * Uses the PHPMailer library included in the project.
 */

// Adjust path to PHPMailer relative to this file
$phpMailerBase = dirname(__DIR__, 2) . '/PHPMailer-PHPMailer-3cd2a2a/src/';

require $phpMailerBase . 'PHPMailer.php';
require $phpMailerBase . 'SMTP.php';
require $phpMailerBase . 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$config = require __DIR__ . '/config.php';

/**
 * Send an OTP email to the given address.
 *
 * @param string $toEmail   Recipient email
 * @param string $toName    Recipient display name
 * @param string $otp       6-digit OTP code
 * @param string $purpose   'verify' | 'reset'
 * @return bool
 */
function sendOTPEmail(string $toEmail, string $toName, string $otp, string $purpose = 'verify'): bool
{
    global $config;

    $mail = new PHPMailer(true);

    try {
        // ── SMTP Configuration ────────────────────────────────────────
        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp_user'];
        $mail->Password   = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['smtp_port'];

        // Always disable SMTP debug output — it breaks JSON responses
        $mail->SMTPDebug  = SMTP::DEBUG_OFF;

        // ── Sender / Recipient ────────────────────────────────────────
        $mail->setFrom($config['smtp_from_email'], $config['smtp_from_name']);
        $mail->addAddress($toEmail, $toName);
        $mail->addReplyTo($config['smtp_from_email'], $config['smtp_from_name']);

        // ── Content ───────────────────────────────────────────────────
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        if ($purpose === 'reset') {
            $mail->Subject = 'Fix&Go — Password Reset Code';
            $mail->Body    = buildResetEmailHTML($toName, $otp);
            $mail->AltBody = "Hi $toName,\n\nYour Fix&Go password reset code is: $otp\n\nThis code expires in 10 minutes.\n\nIf you did not request this, please ignore this email.";
        } else {
            $mail->Subject = 'Fix&Go — Verify Your Email';
            $mail->Body    = buildVerifyEmailHTML($toName, $otp);
            $mail->AltBody = "Hi $toName,\n\nYour Fix&Go verification code is: $otp\n\nThis code expires in 10 minutes.";
        }

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('[Fix&Go Mailer Error] ' . $mail->ErrorInfo);
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
          <span style="background:linear-gradient(135deg,#FF6B35,#e05a28);color:#fff;border-radius:12px;padding:0.5rem 1.2rem;font-size:1.3rem;font-weight:800;">🔧 Fix&amp;Go</span>
        </div>
        <h2 style="color:#1A1A2E;font-size:1.3rem;margin-bottom:0.5rem;">Verify your email address</h2>
        <p style="color:#6C757D;font-size:0.9rem;">Hi <strong>{$name}</strong>, thanks for joining Fix&amp;Go! Enter the code below to activate your account.</p>
        <div style="text-align:center;margin:1.5rem 0;">
          <span style="display:inline-block;background:#FFF3EE;border:2px dashed #FF6B35;border-radius:12px;padding:1rem 2rem;font-size:2.2rem;font-weight:800;letter-spacing:0.5rem;color:#FF6B35;">{$otp}</span>
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
          <span style="background:linear-gradient(135deg,#FF6B35,#e05a28);color:#fff;border-radius:12px;padding:0.5rem 1.2rem;font-size:1.3rem;font-weight:800;">🔧 Fix&amp;Go</span>
        </div>
        <h2 style="color:#1A1A2E;font-size:1.3rem;margin-bottom:0.5rem;">Password Reset Request</h2>
        <p style="color:#6C757D;font-size:0.9rem;">Hi <strong>{$name}</strong>, we received a request to reset your Fix&amp;Go password. Use the code below:</p>
        <div style="text-align:center;margin:1.5rem 0;">
          <span style="display:inline-block;background:#FFF3EE;border:2px dashed #FF6B35;border-radius:12px;padding:1rem 2rem;font-size:2.2rem;font-weight:800;letter-spacing:0.5rem;color:#FF6B35;">{$otp}</span>
        </div>
        <p style="color:#6C757D;font-size:0.82rem;text-align:center;">This code expires in <strong>10 minutes</strong>.</p>
        <hr style="border:none;border-top:1px solid #DEE2E6;margin:1.5rem 0;"/>
        <p style="color:#aaa;font-size:0.75rem;text-align:center;">If you didn't request a password reset, please ignore this email. Your password will not change.</p>
      </div>
    </body>
    </html>
    HTML;
}

/**
 * Send a generic HTML email.
 * Used for admin notifications (seller applications, etc.)
 */
function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
{
    global $config;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp_user'];
        $mail->Password   = $config['smtp_pass'];
        $mail->SMTPSecure = $config['smtp_secure'];
        $mail->Port       = $config['smtp_port'];

        $mail->setFrom($config['smtp_from_email'], $config['smtp_from_name']);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('[Fix&Go Mailer] sendEmail failed: ' . $mail->ErrorInfo);
        return false;
    }
}
