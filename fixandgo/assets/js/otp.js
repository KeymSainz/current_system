/**
 * Fix&Go — OTP Verification Page Logic
 *
 * Submits to the real PHP backend: POST /backend/verify-otp.php
 * Resend calls: POST /backend/resend-otp.php
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const verifyBtn = document.getElementById('verifyBtn');
  const resendBtn = document.getElementById('resendBtn');
  const otpForm   = document.getElementById('otpForm');

  const pendingEmail = sessionStorage.getItem('fg_pending_email');
  // Purpose is set by login.php ('login') or register.php ('verify')
  const pendingPurpose = sessionStorage.getItem('fg_pending_purpose') || 'verify';

  if (!pendingEmail) {
    window.location.href = 'register.php';
    return;
  }

  // Display email
  const emailDisplay = document.getElementById('otpEmail');
  if (emailDisplay) emailDisplay.textContent = pendingEmail;

  let attempts = 0;
  const MAX_ATTEMPTS = 3;

  // ── Fetch real CSRF token ──────────────────────────────────────────────
  let csrfToken = '';
  fetch('api/session/csrf')
    .then(function (r) { return r.json(); })
    .then(function (data) {
      csrfToken = data.token;
      document.querySelectorAll('[name="_csrf"]').forEach(function (el) {
        el.value = data.token;
      });
    });

  /* ------------------------------------------------------------------ */
  /* OTP Input Wiring                                                    */
  /* ------------------------------------------------------------------ */
  FGAuth.initOTPInputs('.otp-group', function () {
    if (verifyBtn) verifyBtn.disabled = false;
  });

  document.querySelectorAll('.otp-group .otp-input').forEach(function (input) {
    input.addEventListener('input', function () {
      const code = FGAuth.getOTPValue('.otp-group');
      if (verifyBtn) verifyBtn.disabled = code.length < 6;
    });
  });

  /* ------------------------------------------------------------------ */
  /* Countdown Timer (10 minutes)                                        */
  /* ------------------------------------------------------------------ */
  FGAuth.startCountdown(10 * 60, 'otpTimer', function () {
    FGAuth.showAlert('otpAlert',
      'Your OTP has expired. Please request a new one.', 'warning');
    if (verifyBtn) verifyBtn.disabled = true;
  });

  /* ------------------------------------------------------------------ */
  /* Resend OTP                                                          */
  /* ------------------------------------------------------------------ */
  let resendSeconds = 60;

  function startResendCooldown() {
    resendBtn.disabled = true;
    const countdownEl = document.getElementById('resendCountdown');
    let remaining = resendSeconds;
    const interval = setInterval(function () {
      remaining--;
      if (countdownEl) countdownEl.textContent = '(' + remaining + 's)';
      if (remaining <= 0) {
        clearInterval(interval);
        resendBtn.disabled = false;
        if (countdownEl) countdownEl.textContent = '';
      }
    }, 1000);
  }

  startResendCooldown();

  resendBtn.addEventListener('click', function () {
    FGAuth.hideAlert('otpAlert');
    FGAuth.clearOTPInputs('.otp-group');
    if (verifyBtn) verifyBtn.disabled = true;
    attempts = 0;

    const fd = new FormData();
    fd.append('_csrf',   csrfToken);
    fd.append('email',   pendingEmail);
    fd.append('purpose', pendingPurpose);

    fetch('api/otp/resend', { method: 'POST', body: fd, credentials: 'include' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        FGAuth.showAlert('otpAlert', data.message,
          data.success ? 'success' : 'danger');
        if (data.success) {
          startResendCooldown();
          FGAuth.startCountdown(10 * 60, 'otpTimer', function () {
            FGAuth.showAlert('otpAlert',
              'Your OTP has expired. Please request a new one.', 'warning');
            if (verifyBtn) verifyBtn.disabled = true;
          });
        }
      })
      .catch(function () {
        FGAuth.showAlert('otpAlert',
          'Could not resend code. Please try again.', 'danger');
      });
  });

  /* ------------------------------------------------------------------ */
  /* Form Submit → POST to backend/verify-otp.php                       */
  /* ------------------------------------------------------------------ */
  otpForm.addEventListener('submit', function (e) {
    e.preventDefault();
    FGAuth.hideAlert('otpAlert');

    if (attempts >= MAX_ATTEMPTS) {
      FGAuth.showAlert('otpAlert',
        'Too many incorrect attempts. Please request a new code.', 'danger');
      if (verifyBtn) verifyBtn.disabled = true;
      return;
    }

    const enteredCode = FGAuth.getOTPValue('.otp-group');
    if (enteredCode.length < 6) {
      FGAuth.showAlert('otpAlert', 'Please enter all 6 digits.', 'warning');
      return;
    }

    FGAuth.setButtonLoading(verifyBtn, true);

    const fd = new FormData();
    fd.append('_csrf',   csrfToken);
    fd.append('email',   pendingEmail);
    fd.append('otp',     enteredCode);
    fd.append('purpose', pendingPurpose);

    fetch('api/otp/verify', { method: 'POST', body: fd, credentials: 'include' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        FGAuth.setButtonLoading(verifyBtn, false);

        if (!data.success) {
          attempts++;
          const remaining = MAX_ATTEMPTS - attempts;
          const suffix = remaining > 0
            ? ' (' + remaining + ' attempt' + (remaining === 1 ? '' : 's') + ' remaining)'
            : ' No more attempts — please request a new code.';
          FGAuth.showAlert('otpAlert', data.message + suffix, 'danger');
          FGAuth.clearOTPInputs('.otp-group');
          if (verifyBtn) verifyBtn.disabled = true;
          return;
        }

        // Save user returned from server
        if (data.user) FGAuth.UserStore.save(data.user);

        FGAuth.showAlert('otpAlert',
          'Email verified! Redirecting…', 'success');
        sessionStorage.removeItem('fg_pending_email');
        sessionStorage.removeItem('fg_pending_purpose');

        // Honour post-login redirect if set (e.g. from technician card click),
        // otherwise trust the server's role-based redirect
        const postRedirect = sessionStorage.getItem('fg_post_login_redirect') || '';
        sessionStorage.removeItem('fg_post_login_redirect');

        const redirectUrl = postRedirect || data.redirect || 'index.php';

        setTimeout(function () {
          window.location.href = redirectUrl;
        }, 1200);
      })
      .catch(function () {
        FGAuth.setButtonLoading(verifyBtn, false);
        FGAuth.showAlert('otpAlert',
          'Could not connect to the server. Please try again.', 'danger');
      });
  });
});
