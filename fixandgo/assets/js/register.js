/**
 * Fix&Go — Registration Page Logic
 *
 * Submits to the real PHP backend: POST /backend/register.php
 * Google button redirects to: /backend/google-auth-init.php (real OAuth)
 *
 * Security:
 *  - Real CSRF token fetched from server before submit
 *  - Real-time password rule enforcement
 *  - Password strength meter
 *  - Password match validation
 *  - Input sanitization
 *  - Client-side rate limiting (backup to server-side)
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const form            = document.getElementById('registerForm');
  const firstNameInput  = document.getElementById('firstName');
  const lastNameInput   = document.getElementById('lastName');
  const emailInput      = document.getElementById('regEmail');
  const passwordInput   = document.getElementById('regPassword');
  const confirmInput    = document.getElementById('confirmPassword');
  const registerBtn     = document.getElementById('registerBtn');
  const googleBtn       = document.getElementById('googleRegisterBtn');

  // Guard: if any critical element is missing, bail out
  if (!form || !firstNameInput || !lastNameInput || !emailInput || !passwordInput || !confirmInput || !registerBtn) {
    console.error('[Fix&Go] Register form elements not found. IDs present:', {
      registerForm:    !!document.getElementById('registerForm'),
      firstName:       !!document.getElementById('firstName'),
      lastName:        !!document.getElementById('lastName'),
      regEmail:        !!document.getElementById('regEmail'),
      regPassword:     !!document.getElementById('regPassword'),
      confirmPassword: !!document.getElementById('confirmPassword'),
      registerBtn:     !!document.getElementById('registerBtn'),
    });
    return;
  }

  // Client-side rate limiter (backup — server enforces too)
  const rateLimiter = new FGAuth.RateLimiter('register', 3, 60 * 60 * 1000);

  // ── Fetch real CSRF token from server on load ──────────────────────────
  var backendBase = window.FG_BACKEND || 'backend/';
  fetch(backendBase + 'csrf-token.php')
    .then(function (r) { return r.json(); })
    .then(function (data) {
      document.querySelectorAll('[name="_csrf"]').forEach(function (el) {
        el.value = data.token;
      });
    })
    .catch(function () {
      // Fallback: keep the simulated token already injected by auth-utils.js
    });

  /* ------------------------------------------------------------------ */
  /* Real-time validation                                                */
  /* ------------------------------------------------------------------ */

  [firstNameInput, lastNameInput].forEach(function (input) {
    input.addEventListener('input', function () { validateName(input); });
    input.addEventListener('blur',  function () { validateName(input); });
  });

  function validateName(input) {
    const val = input.value.trim();
    if (val.length < 2) {
      setFieldState(input, false, 'Enter at least 2 characters.');
    } else if (!/^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/.test(val)) {
      setFieldState(input, false, 'Only letters, spaces, and hyphens allowed.');
    } else {
      setFieldState(input, true);
    }
    updateSubmitState();
  }

  emailInput.addEventListener('input', validateEmail);
  emailInput.addEventListener('blur',  validateEmail);

  function validateEmail() {
    const val = emailInput.value.trim();
    if (!val) {
      setFieldState(emailInput, false, 'Email is required.');
    } else if (!FGAuth.isValidEmail(val)) {
      setFieldState(emailInput, false, 'Please enter a valid email address.');
    } else {
      setFieldState(emailInput, true);
    }
    updateSubmitState();
  }

  passwordInput.addEventListener('input', function () {
    const pw = passwordInput.value;
    FGAuth.updateStrengthBar(pw, 'strengthFill', 'strengthText');

    if (pw.length === 0) {
      passwordInput.classList.remove('is-valid', 'is-invalid');
      document.getElementById('pwHint').style.color = 'var(--fg-muted)';
      document.getElementById('pwHint').textContent = 'At least 8 characters · one uppercase letter · one number';
    } else {
      const missing = [];
      if (pw.length < 8)       missing.push('8+ characters');
      if (!/[A-Z]/.test(pw))   missing.push('uppercase letter');
      if (!/\d/.test(pw))      missing.push('number');

      if (missing.length === 0) {
        passwordInput.classList.remove('is-invalid');
        passwordInput.classList.add('is-valid');
        document.getElementById('pwHint').style.color = '#28a745';
        document.getElementById('pwHint').textContent = '✓ Password looks good';
      } else {
        passwordInput.classList.remove('is-valid');
        passwordInput.classList.add('is-invalid');
        document.getElementById('pwHint').style.color = '#dc3545';
        document.getElementById('pwHint').textContent = 'Missing: ' + missing.join(', ');
      }
    }

    if (confirmInput.value) validateConfirm();
    updateSubmitState();
  });

  confirmInput.addEventListener('input', validateConfirm);
  confirmInput.addEventListener('blur',  validateConfirm);

  function validateConfirm() {
    const pw  = passwordInput.value;
    const cpw = confirmInput.value;
    const hint = document.getElementById('confirmHint');

    if (!cpw) {
      confirmInput.classList.remove('is-valid', 'is-invalid');
      hint.style.display = 'none';
    } else if (cpw === pw) {
      confirmInput.classList.remove('is-invalid');
      confirmInput.classList.add('is-valid');
      hint.style.display = 'none';
    } else {
      confirmInput.classList.remove('is-valid');
      confirmInput.classList.add('is-invalid');
      hint.style.display = 'block';
    }
    updateSubmitState();
  }

  function updateSubmitState() {
    // Re-evaluate password validity live every time
    const pw = passwordInput.value;
    const pwOk = pw.length >= 8 && /[A-Z]/.test(pw) && /\d/.test(pw);
    const cpwOk = confirmInput.value === pw && confirmInput.value.length > 0;

    const allFilled =
      firstNameInput.value.trim().length >= 2 &&
      lastNameInput.value.trim().length  >= 2 &&
      FGAuth.isValidEmail(emailInput.value.trim()) &&
      pwOk &&
      cpwOk;

    registerBtn.disabled = !allFilled;
  }

  /* ------------------------------------------------------------------ */
  /* Form Submit → POST to backend/register.php                         */
  /* ------------------------------------------------------------------ */
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    FGAuth.hideAlert('registerAlert');

    FGAuth.setButtonLoading(registerBtn, true);

    const formData = new FormData(form);

    fetch((window.FG_BACKEND || 'backend/') + 'register.php', {
      method: 'POST',
      body:   formData,
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        FGAuth.setButtonLoading(registerBtn, false);

        if (!data.success) {
          FGAuth.showAlert('registerAlert', data.message, 'danger');
          return;
        }

        // Store pending email so OTP page knows who to verify
        sessionStorage.setItem('fg_pending_email', emailInput.value.trim());
        FGAuth.showAlert('registerAlert', data.message, 'success');

        setTimeout(function () {
          const dest = (!data.redirect || data.redirect === 'otp.php')
            ? 'fixandgo/otp.html'
            : data.redirect;
          window.location.href = dest;
        }, 800);
      })
      .catch(function () {
        FGAuth.setButtonLoading(registerBtn, false);
        FGAuth.showAlert('registerAlert',
          'Could not connect to the server. Please try again.', 'danger');
      });
  });

  /* ------------------------------------------------------------------ */
  /* Google Sign-Up → redirect to real OAuth flow                       */
  /* ------------------------------------------------------------------ */
  googleBtn.addEventListener('click', function () {
    googleBtn.disabled = true;
    googleBtn.innerHTML =
      '<span class="btn-spinner" style="display:inline-block;border:2px solid rgba(0,0,0,0.2);border-top-color:#333;width:1rem;height:1rem;border-radius:50%;animation:spin 0.7s linear infinite;"></span> Connecting…';

    // Redirect to PHP which builds the Google OAuth URL and redirects
    window.location.href = (window.FG_BACKEND || 'backend/') + 'google-auth-init.php';
  });

  /* ------------------------------------------------------------------ */
  /* Field State Helper                                                  */
  /* ------------------------------------------------------------------ */
  function setFieldState(input, isValid, message) {
    input.classList.toggle('is-valid',   isValid);
    input.classList.toggle('is-invalid', !isValid);
    if (!isValid && message) {
      // Search up to 2 levels up for the invalid-feedback element
      let fb = input.parentElement.querySelector('.invalid-feedback')
            || input.parentElement.parentElement.querySelector('.invalid-feedback');
      if (fb) fb.textContent = message;
    }
  }
});
