/**
 * Fix&Go — Forgot Password Page Logic
 *
 * Step 1 → POST backend/forgot-password.php  (send OTP to email)
 * Step 2 → POST backend/verify-otp.php       (verify OTP)
 *        → POST backend/reset-password.php   (set new password)
 *
 * No more sessionStorage simulation — all calls go to the real DB.
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const step1     = document.getElementById('step1');
  const step2     = document.getElementById('step2');
  const fpForm    = document.getElementById('fpForm');
  const fpBtn     = document.getElementById('fpBtn');
  const resetForm = document.getElementById('resetForm');
  const resetBtn  = document.getElementById('resetBtn');

  let resetEmail       = '';
  let newPasswordValid = false;
  let csrfToken        = '';

  // ── Fetch CSRF token on load ───────────────────────────────────────────
  const _B = window.FG_BACKEND || (function() {
    var parts = window.location.pathname.split('/').filter(Boolean);
    return parts.length <= 1 ? 'fixandgo/backend/' : 'backend/';
  })();

  fetch(_B + 'csrf-token.php')
    .then(function (r) { return r.json(); })
    .then(function (data) {
      csrfToken = data.token;
      document.querySelectorAll('[name="_csrf"]').forEach(function (el) {
        el.value = data.token;
      });
    });

  /* ------------------------------------------------------------------ */
  /* Step 1 — Send reset OTP via backend/forgot-password.php            */
  /* ------------------------------------------------------------------ */
  fpForm.addEventListener('submit', function (e) {
    e.preventDefault();
    FGAuth.hideAlert('fpAlert');

    const email = document.getElementById('fpEmail').value.trim();

    if (!FGAuth.isValidEmail(email)) {
      FGAuth.showAlert('fpAlert', 'Please enter a valid email address.', 'danger');
      return;
    }

    FGAuth.setButtonLoading(fpBtn, true);

    const fd = new FormData();
    fd.append('_csrf',  csrfToken);
    fd.append('email',  email);

    fetch(_B + 'forgot-password.php', { method: 'POST', body: fd, credentials: 'include' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        FGAuth.setButtonLoading(fpBtn, false);

        if (!data.success) {
          FGAuth.showAlert('fpAlert', data.message, 'danger');
          return;
        }

        // Move to step 2
        resetEmail = email;
        const display = document.getElementById('fpEmailDisplay');
        if (display) display.textContent = email;

        step1.classList.add('d-none');
        step2.classList.remove('d-none');

        // Wire OTP inputs
        FGAuth.initOTPInputs('.otp-group', function () { validateResetForm(); });
        document.querySelectorAll('.otp-group .otp-input').forEach(function (input) {
          input.addEventListener('input', validateResetForm);
        });

        // Start 10-minute countdown
        FGAuth.startCountdown(10 * 60, 'resetTimer', function () {
          FGAuth.showAlert('resetAlert',
            'Code expired. Please go back and request a new one.', 'warning');
          if (resetBtn) resetBtn.disabled = true;
        });

        // Wire new password rules
        const newPwInput = document.getElementById('newPassword');
        if (newPwInput) {
          newPwInput.addEventListener('input', function () {
            newPasswordValid = FGAuth.checkPasswordRules(newPwInput.value, 'reset');
            FGAuth.updateStrengthBar(newPwInput.value, 'resetStrengthFill', 'resetStrengthText');
            validateResetForm();
          });
        }

        const confirmNewPw = document.getElementById('confirmNewPassword');
        if (confirmNewPw) {
          confirmNewPw.addEventListener('input', validateResetForm);
        }
      })
      .catch(function () {
        FGAuth.setButtonLoading(fpBtn, false);
        FGAuth.showAlert('fpAlert',
          'Could not connect to the server. Please try again.', 'danger');
      });
  });

  /* ------------------------------------------------------------------ */
  /* Enable/disable reset button                                         */
  /* ------------------------------------------------------------------ */
  function validateResetForm() {
    const newPw     = (document.getElementById('newPassword')        || {}).value || '';
    const confirmPw = (document.getElementById('confirmNewPassword') || {}).value || '';
    const otpCode   = FGAuth.getOTPValue('.otp-group');
    const match     = newPw === confirmPw && confirmPw.length > 0;

    const confirmInput = document.getElementById('confirmNewPassword');
    if (confirmInput && confirmPw) {
      confirmInput.classList.toggle('is-valid',   match);
      confirmInput.classList.toggle('is-invalid', !match);
    }

    if (resetBtn) {
      resetBtn.disabled = !(otpCode.length === 6 && newPasswordValid && match);
    }
  }

  /* ------------------------------------------------------------------ */
  /* Step 2 — Verify OTP then reset password                            */
  /* ------------------------------------------------------------------ */
  resetForm.addEventListener('submit', function (e) {
    e.preventDefault();
    FGAuth.hideAlert('resetAlert');

    const code      = FGAuth.getOTPValue('.otp-group');
    const newPw     = document.getElementById('newPassword').value;
    const confirmPw = document.getElementById('confirmNewPassword').value;

    if (newPw !== confirmPw) {
      FGAuth.showAlert('resetAlert', 'Passwords do not match.', 'danger');
      return;
    }

    FGAuth.setButtonLoading(resetBtn, true);

    // ── Step 2a: verify OTP ──────────────────────────────────────────
    const verifyFd = new FormData();
    verifyFd.append('_csrf',   csrfToken);
    verifyFd.append('email',   resetEmail);
    verifyFd.append('otp',     code);
    verifyFd.append('purpose', 'reset');

    fetch(_B + 'verify-otp.php', { method: 'POST', body: verifyFd })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.success) {
          FGAuth.setButtonLoading(resetBtn, false);
          FGAuth.showAlert('resetAlert', data.message, 'danger');
          FGAuth.clearOTPInputs('.otp-group');
          resetBtn.disabled = true;
          return;
        }

        // ── Step 2b: set new password ──────────────────────────────
        const resetFd = new FormData();
        resetFd.append('_csrf',       csrfToken);
        resetFd.append('newPassword', newPw);

        return fetch(_B + 'reset-password.php', { method: 'POST', body: resetFd });
      })
      .then(function (r) {
        if (!r) return; // already handled above
        return r.json();
      })
      .then(function (data) {
        if (!data) return;
        FGAuth.setButtonLoading(resetBtn, false);

        if (!data.success) {
          FGAuth.showAlert('resetAlert', data.message, 'danger');
          return;
        }

        FGAuth.showAlert('resetAlert',
          'Password reset successfully! Redirecting to login…', 'success');
        setTimeout(function () {
          window.location.href = data.redirect || 'login.html';
        }, 2000);
      })
      .catch(function () {
        FGAuth.setButtonLoading(resetBtn, false);
        FGAuth.showAlert('resetAlert',
          'Could not connect to the server. Please try again.', 'danger');
      });
  });
});
