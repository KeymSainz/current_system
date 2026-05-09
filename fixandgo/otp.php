<?php
/**
 * Fix&Go — OTP Verification Page (pure PHP, no JS fetch)
 *
 * GET  → shows the OTP form
 * POST → verifies the OTP, redirects to dashboard or shows error
 * ?resend=1 → generates a new OTP and sends it
 */

require_once __DIR__ . '/backend/helpers.php';

startSecureSession();

$config = require __DIR__ . '/backend/config.php';
$pdo    = require __DIR__ . '/backend/db.php';

// ── Guard: must have a pending email in session ───────────────────────────
$email   = $_SESSION['pending_email']   ?? '';
$purpose = $_SESSION['pending_purpose'] ?? 'verify';

if (empty($email)) {
    header('Location: login.html');
    exit;
}

// ── Fetch user ────────────────────────────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT id, first_name, last_name, email, role FROM users WHERE email = ? LIMIT 1'
);
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: login.html');
    exit;
}

$error   = '';
$success = '';

// ── Resend OTP ────────────────────────────────────────────────────────────
if (isset($_GET['resend'])) {
    require_once __DIR__ . '/backend/mailer.php';

    $otp       = generateOTP();
    $otpHash   = password_hash($otp, PASSWORD_BCRYPT);
    $expiresAt = date('Y-m-d H:i:s', time() + $config['otp_expiry']);

    $pdo->prepare('DELETE FROM otp_tokens WHERE user_id = ? AND purpose = ?')
        ->execute([$user['id'], $purpose]);

    $pdo->prepare(
        'INSERT INTO otp_tokens (user_id, otp_hash, purpose, expires_at, attempts)
         VALUES (?, ?, ?, ?, 0)'
    )->execute([$user['id'], $otpHash, $purpose, $expiresAt]);

    sendOTPEmail($email, $user['first_name'], $otp, $purpose);

    $success = 'A new code has been sent to ' . htmlspecialchars($email) . '.';
}

// ── POST: verify OTP ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect the 6 individual digit inputs into one string
    $digits = '';
    for ($i = 1; $i <= 6; $i++) {
        $digits .= preg_replace('/\D/', '', $_POST['d' . $i] ?? '');
    }

    if (strlen($digits) !== 6) {
        $error = 'Please enter all 6 digits.';
    } else {
        // Fetch OTP record
        $stmt = $pdo->prepare(
            'SELECT id, otp_hash, expires_at, attempts
             FROM otp_tokens
             WHERE user_id = ? AND purpose = ?
             ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute([$user['id'], $purpose]);
        $record = $stmt->fetch();

        if (!$record) {
            $error = 'No code found. Please request a new one.';
        } elseif (strtotime($record['expires_at']) < time()) {
            $error = 'This code has expired. <a href="otp.php?resend=1" class="auth-link">Send a new code</a>.';
        } elseif ((int) $record['attempts'] >= $config['otp_max_attempts']) {
            $error = 'Too many incorrect attempts. <a href="otp.php?resend=1" class="auth-link">Request a new code</a>.';
        } elseif (!password_verify($digits, $record['otp_hash'])) {
            // Wrong code — increment attempts
            $pdo->prepare('UPDATE otp_tokens SET attempts = attempts + 1 WHERE id = ?')
                ->execute([$record['id']]);

            $remaining = $config['otp_max_attempts'] - ((int) $record['attempts'] + 1);
            $error = $remaining > 0
                ? "Incorrect code. $remaining attempt(s) remaining."
                : 'Incorrect code. No more attempts. <a href="otp.php?resend=1" class="auth-link">Request a new code</a>.';
        } else {
            // ── OTP correct ──────────────────────────────────────────
            $pdo->prepare('DELETE FROM otp_tokens WHERE id = ?')->execute([$record['id']]);

            if ($purpose === 'verify') {
                $pdo->prepare('UPDATE users SET is_verified = 1 WHERE id = ?')
                    ->execute([$user['id']]);
            }

            // Create full authenticated session
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['first_name'];

            // Log successful login activity
            if ($purpose === 'login' || $purpose === 'verify') {
                logUserActivity($pdo, $user['id'], 'login');
            }

            // Handle Remember Me (set during login)
            $remember = $_SESSION['pending_remember'] ?? false;
            if ($remember) {
                $token     = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $expires   = time() + $config['remember_lifetime'];
                $pdo->prepare(
                    'INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
                )->execute([$user['id'], $tokenHash, date('Y-m-d H:i:s', $expires)]);
                setRememberMeCookie($token, $config['remember_lifetime']);
            }

            unset(
                $_SESSION['pending_email'],
                $_SESSION['pending_purpose'],
                $_SESSION['pending_remember']
            );

            // Route to correct dashboard based on role
            $role = $user['role'];
            if ($role === 'admin') {
                header('Location: fixandgo/views/admin/dashboard.html');
            } else {
                header('Location: dashboard.html');
            }
            exit;
        }
    }
}

// ── Fetch expiry time for display ─────────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT expires_at FROM otp_tokens
     WHERE user_id = ? AND purpose = ?
     ORDER BY created_at DESC LIMIT 1'
);
$stmt->execute([$user['id'], $purpose]);
$otpRow    = $stmt->fetch();
$expiresAt = $otpRow ? strtotime($otpRow['expires_at']) : (time() + 600);
$secondsLeft = max(0, $expiresAt - time());
$minutesLeft = floor($secondsLeft / 60);
$secsLeft    = $secondsLeft % 60;
$expiryDisplay = sprintf('%d:%02d', $minutesLeft, $secsLeft);

$maskedEmail = preg_replace('/(?<=.{2}).(?=.*@)/u', '*', $email);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fix&amp;Go — Verify Code</title>
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/css/auth.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
</head>
<body>

  <!-- Theme Toggle -->
  <div style="position:fixed;top:1rem;right:1rem;z-index:200;">
    <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark/light mode">
      <i class="bi bi-moon-fill" id="themeIcon"></i>
    </button>
  </div>

  <main class="auth-bg">
    <div class="auth-card" style="max-width:440px; text-align:center;">

      <!-- Logo -->
      <div class="logo-area">
        <a href="login.html" class="logo-placeholder">
          <span class="logo-icon">🔧</span> Fix&amp;Go
        </a>
      </div>

      <div style="font-size:3rem; margin-bottom:0.75rem;">📧</div>

      <h1 class="auth-title">Check your email</h1>
      <p class="auth-subtitle">
        We sent a 6-digit code to<br/>
        <strong><?= htmlspecialchars($maskedEmail) ?></strong><br/>
        Enter it below to continue.
      </p>

      <!-- Alerts -->
      <?php if ($error): ?>
        <div class="auth-alert alert alert-danger mb-3" role="alert">
          <i class="bi bi-exclamation-circle"></i> <?= $error ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="auth-alert alert alert-success mb-3" role="alert">
          <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <!-- OTP Form — plain POST, no JavaScript required -->
      <form method="POST" action="otp.php">

        <!-- 6 individual digit boxes -->
        <div class="otp-group" role="group" aria-label="One-time password">
          <?php for ($i = 1; $i <= 6; $i++): ?>
            <input
              class="otp-input"
              type="text"
              inputmode="numeric"
              name="d<?= $i ?>"
              maxlength="1"
              pattern="[0-9]"
              autocomplete="<?= $i === 1 ? 'one-time-code' : 'off' ?>"
              aria-label="Digit <?= $i ?>"
              required
            />
          <?php endfor; ?>
        </div>

        <!-- Expiry display -->
        <div class="otp-countdown mb-3">
          Code expires in
          <span class="timer"><?= htmlspecialchars($expiryDisplay) ?></span>
        </div>

        <button type="submit" class="btn btn-primary-fg mb-3">
          Verify Code
        </button>

      </form>

      <!-- Resend — plain GET link, no JavaScript -->
      <p class="fs-sm" style="color:var(--fg-muted);">
        Didn't receive the code?
        <a href="otp.php?resend=1" class="auth-link fw-700">Resend OTP</a>
      </p>

      <p class="fs-sm mt-2 mb-0" style="color:var(--fg-muted);">
        <a href="login.html" class="auth-link">← Back to login</a>
      </p>

    </div>
  </main>

  <!-- Only theme toggle needs JS — OTP form works without it -->
  <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/theme.js"></script>

  <!-- Auto-advance between digit boxes (progressive enhancement — works without it too) -->
  <script>
    document.querySelectorAll('.otp-input').forEach(function(input, idx, all) {
      input.addEventListener('input', function() {
        input.value = input.value.replace(/\D/g, '').slice(-1);
        if (input.value && idx < all.length - 1) all[idx + 1].focus();
      });
      input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !input.value && idx > 0) all[idx - 1].focus();
      });
      input.addEventListener('paste', function(e) {
        e.preventDefault();
        var digits = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        digits.split('').forEach(function(d, i) {
          if (all[idx + i]) all[idx + i].value = d;
        });
        var next = Math.min(idx + digits.length, all.length - 1);
        all[next].focus();
      });
    });
  </script>

</body>
</html>
