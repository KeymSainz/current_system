<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <script>window.FG_BACKEND = 'backend/';</script>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- PWA -->
  <meta name="theme-color" content="#e6a800">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Fix&amp;Go">
  <link rel="manifest" href="fixandgo/manifest.json">
  <link rel="apple-touch-icon" href="fixandgo/assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="fixandgo/assets/css/mobile.css">
  <title>Fix&amp;Go — Forgot Password</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="fixandgo/assets/css/auth.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
</head>
<body>

  <div style="position:fixed;top:1rem;right:1rem;z-index:200;">
    <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark/light mode">
      <i class="bi bi-moon-fill" id="themeIcon"></i>
    </button>
  </div>

  <main class="auth-bg">
    <div class="auth-card" style="max-width:440px;">

      <div class="logo-area">
        <a href="login.html" class="logo-placeholder">
          <span class="logo-icon">🔧</span> Fix&amp;Go
        </a>
      </div>

      <!-- Step 1: Enter email -->
      <div id="step1">
        <div style="font-size:2.5rem;text-align:center;margin-bottom:0.75rem;">🔑</div>
        <h1 class="auth-title text-center">Forgot your password?</h1>
        <p class="auth-subtitle text-center">Enter your email and we'll send you a reset code.</p>

        <div id="fpAlert" class="auth-alert alert d-none mb-3" role="alert"></div>

        <form id="fpForm" novalidate>
          <input type="hidden" name="_csrf" id="csrfToken" />
          <div class="mb-3">
            <label for="fpEmail" class="form-label">Email address</label>
            <input type="email" class="form-control" id="fpEmail" name="email"
              placeholder="you@example.com" autocomplete="email" required />
            <div class="invalid-feedback">Please enter a valid email address.</div>
          </div>
          <button type="submit" class="btn btn-primary-fg mb-3" id="fpBtn">
            <span class="btn-spinner" aria-hidden="true"></span>
            <span class="btn-text">Send Reset Code</span>
          </button>
          <p class="text-center fs-sm mb-0" style="color:var(--fg-muted);">
            <a href="login.html" class="auth-link">← Back to login</a>
          </p>
        </form>
      </div>

      <!-- Step 2: OTP + new password -->
      <div id="step2" class="d-none">
        <div style="font-size:2.5rem;text-align:center;margin-bottom:0.75rem;">🔒</div>
        <h1 class="auth-title text-center">Reset your password</h1>
        <p class="auth-subtitle text-center">Enter the code sent to <strong id="fpEmailDisplay"></strong> and choose a new password.</p>

        <div id="resetAlert" class="auth-alert alert d-none mb-3" role="alert"></div>

        <form id="resetForm" novalidate>
          <input type="hidden" name="_csrf" id="csrfToken2" />

          <!-- OTP -->
          <div class="mb-3">
            <label class="form-label">Verification Code</label>
            <div class="otp-group" role="group" aria-label="Reset code input">
              <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 1" />
              <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 2" />
              <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 3" />
              <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 4" />
              <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 5" />
              <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 6" />
            </div>
            <div class="otp-countdown mb-2">
              Code expires in <span class="timer" id="resetTimer">10:00</span>
            </div>
          </div>

          <!-- New password -->
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <div class="password-wrapper">
              <input type="password" class="form-control" id="newPassword" name="newPassword"
                placeholder="Create a new password" autocomplete="new-password" required />
              <button type="button" class="toggle-password" aria-label="Show/hide password" data-target="newPassword">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div class="password-strength mt-2">
              <div class="strength-bar"><div class="strength-fill" id="resetStrengthFill"></div></div>
              <span class="strength-text" id="resetStrengthText"></span>
            </div>
          </div>

          <!-- Confirm new password -->
          <div class="mb-4">
            <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
            <div class="password-wrapper">
              <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword"
                placeholder="Repeat your new password" autocomplete="new-password" required />
              <button type="button" class="toggle-password" aria-label="Show/hide password" data-target="confirmNewPassword">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div class="invalid-feedback" id="resetConfirmFeedback">Passwords do not match.</div>
          </div>

          <button type="submit" class="btn btn-primary-fg mb-3" id="resetBtn" disabled>
            <span class="btn-spinner" aria-hidden="true"></span>
            <span class="btn-text">Reset Password</span>
          </button>
        </form>
      </div>

    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="fixandgo/assets/js/theme.js"></script>
  <script src="fixandgo/assets/js/auth-utils.js"></script>
  <script src="fixandgo/assets/js/forgot-password.js"></script>
  <script src="fixandgo/assets/js/pwa.js" defer></script>
</body>
</html>

