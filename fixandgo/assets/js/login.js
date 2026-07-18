/**
 * Fix&Go — Login Page Logic
 *
 * Submits to the real PHP backend: POST /backend/login.php
 * Google button redirects to: /backend/google-auth-init.php (real OAuth)
 *
 * Security:
 *  - Real CSRF token fetched from server
 *  - Client-side rate limiting (backup to server-side)
 *  - Input sanitization
 *  - Role-based redirect after login
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const form       = document.getElementById('loginForm');
  const emailInput = document.getElementById('loginEmail');
  const passInput  = document.getElementById('loginPassword');
  const loginBtn   = document.getElementById('loginBtn');
  const googleBtn  = document.getElementById('googleLoginBtn');

  // Client-side rate limiter (backup — server enforces too)
  const rateLimiter = new FGAuth.RateLimiter('login', 5, 15 * 60 * 1000);

  // If already logged in (session cookie exists), server will redirect anyway.
  // Check local session store for JS-side redirect.
  if (FGAuth.UserStore.isLoggedIn()) {
    const user = FGAuth.UserStore.get();
    if (user && user.role === 'supervisor') {
      window.location.href = 'views/user/supervisor/dashboard.php';
    } else {
      window.location.href = 'dashboard.php';
    }
    return;
  }

  // ── Fetch real CSRF token from server on load ──────────────────────────
  var backendBase = window.FG_BACKEND || 'backend/';
  fetch('api/session/csrf', { credentials: 'include' })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      document.querySelectorAll('[name="_csrf"]').forEach(function (el) {
        el.value = data.token;
      });
    })
    .catch(function () { /* keep simulated token */ });

  // Show OAuth error messages passed via URL query string
  const urlParams = new URLSearchParams(window.location.search);
  const oauthError = urlParams.get('error');
  if (oauthError) {
    const messages = {
      oauth_state:   'Google sign-in failed (security check). Please try again.',
      oauth_denied:  'Google sign-in was cancelled.',
      oauth_token:   'Could not complete Google sign-in. Please try again.',
      oauth_profile: 'Could not retrieve your Google profile. Please try again.',
    };
    FGAuth.showAlert('loginAlert', messages[oauthError] || 'Google sign-in failed.', 'danger');
  }

  /* ------------------------------------------------------------------ */
  /* Real-time inline validation                                         */
  /* ------------------------------------------------------------------ */
  emailInput.addEventListener('blur', function () {
    if (!emailInput.value.trim()) {
      setFieldState(emailInput, false, 'Email is required.');
    } else if (!FGAuth.isValidEmail(emailInput.value)) {
      setFieldState(emailInput, false, 'Please enter a valid email address.');
    } else {
      setFieldState(emailInput, true);
    }
  });

  passInput.addEventListener('blur', function () {
    if (!passInput.value) {
      setFieldState(passInput, false, 'Password is required.');
    } else {
      setFieldState(passInput, true);
    }
  });

  /* ------------------------------------------------------------------ */
  /* Form Submit → POST to backend/login.php                            */
  /* ------------------------------------------------------------------ */
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    FGAuth.hideAlert('loginAlert');

    const email    = emailInput.value.trim();
    const password = passInput.value;

    let valid = true;
    if (!email || !FGAuth.isValidEmail(email)) {
      setFieldState(emailInput, false, 'Please enter a valid email address.');
      valid = false;
    }
    if (!password) {
      setFieldState(passInput, false, 'Password is required.');
      valid = false;
    }
    if (!valid) return;

    if (!rateLimiter.check()) {
      FGAuth.showAlert('loginAlert',
        'Too many login attempts. Please wait 1 minute before trying again.', 'danger');
      loginBtn.disabled = true;
      setTimeout(function () {
        loginBtn.disabled = false;
        rateLimiter.reset();
      }, 60 * 1000);
      return;
    }

    FGAuth.setButtonLoading(loginBtn, true);

    const formData = new FormData(form);

    fetch('api/login', {
      method: 'POST',
      body:   formData,
      credentials: 'include',
    })
      .then(function (r) {
        // InfinityFree security check returns HTML — detect and retry once
        var ct = r.headers.get('content-type') || '';
        if (!ct.includes('application/json')) {
          throw new Error('non-json');
        }
        return r.json();
      })
      .then(function (data) {
        FGAuth.setButtonLoading(loginBtn, false);

        if (!data.success) {
          // Account locked
          if (data.locked) {
            showLockoutBanner(data.seconds_left || 900);
            loginBtn.disabled = true;
            return;
          }
          // Failed attempt — show remaining count
          if (data.remaining !== undefined) {
            showAttemptWarning(data.remaining, data.max_attempts || 5);
          } else {
            FGAuth.showAlert('loginAlert', data.message, 'danger');
          }
          return;
        }

        rateLimiter.reset();
        clearLockoutBanner();

        // Save user to JS session store
        if (data.user) {
          FGAuth.UserStore.save(data.user);
        }

        // Redirect directly — no OTP step
        const redirectParam = new URLSearchParams(window.location.search).get('redirect') || '';
        const dest = redirectParam || data.redirect || '/';
        window.location.href = dest;
      })
      .catch(function () {
        FGAuth.setButtonLoading(loginBtn, false);
        FGAuth.showAlert('loginAlert',
          'Server busy — please wait a moment and try again.', 'danger');
      });
  });

  /* ── Attempt warning ── */
  function showAttemptWarning(remaining, max) {
    var alertEl = document.getElementById('loginAlert');
    if (!alertEl) return;

    var dots = '';
    for (var i = 0; i < max; i++) {
      var filled = i < (max - remaining);
      dots += '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;margin:0 3px;background:' +
        (filled ? '#ef4444' : 'rgba(239,68,68,0.25)') + ';border:2px solid ' +
        (filled ? '#ef4444' : 'rgba(239,68,68,0.5)') + ';"></span>';
    }

    var msg = remaining === 1
      ? '<strong>⚠️ Last attempt!</strong> Your account will be locked after one more failed login.'
      : '<strong>Incorrect password.</strong> ' + remaining + ' attempt' + (remaining !== 1 ? 's' : '') + ' remaining before lockout.';

    alertEl.className = 'auth-alert alert alert-danger mb-3';
    alertEl.innerHTML =
      '<div>' + msg + '</div>' +
      '<div style="margin-top:0.5rem;">' + dots + '</div>';
    alertEl.classList.remove('d-none');
  }

  /* ── Lockout banner with countdown ── */
  var lockoutInterval = null;

  function showLockoutBanner(secondsLeft) {
    clearLockoutBanner();

    var alertEl = document.getElementById('loginAlert');
    if (!alertEl) return;

    function render(secs) {
      var m = Math.floor(secs / 60);
      var s = secs % 60;
      var time = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
      alertEl.className = 'auth-alert alert alert-danger mb-3';
      alertEl.innerHTML =
        '<div style="display:flex;align-items:center;gap:0.6rem;">' +
          '<i class="bi bi-lock-fill" style="font-size:1.2rem;flex-shrink:0;"></i>' +
          '<div>' +
            '<strong>Account Locked</strong><br>' +
            '<span style="font-size:0.85rem;">Too many failed attempts. Try again in ' +
            '<strong style="font-variant-numeric:tabular-nums;">' + time + '</strong></span>' +
          '</div>' +
        '</div>';
      alertEl.classList.remove('d-none');
    }

    render(secondsLeft);
    loginBtn.disabled = true;

    lockoutInterval = setInterval(function () {
      secondsLeft--;
      if (secondsLeft <= 0) {
        clearLockoutBanner();
        loginBtn.disabled = false;
        FGAuth.showAlert('loginAlert', 'Your account has been unlocked. You may try again.', 'success');
      } else {
        render(secondsLeft);
      }
    }, 1000);
  }

  function clearLockoutBanner() {
    if (lockoutInterval) { clearInterval(lockoutInterval); lockoutInterval = null; }
  }

  /* ------------------------------------------------------------------ */
  /* Google Login → redirect to real OAuth flow                         */
  /* ------------------------------------------------------------------ */
  googleBtn.addEventListener('click', function () {
    googleBtn.disabled = true;
    googleBtn.innerHTML =
      '<span class="btn-spinner" style="display:inline-block;border:2px solid rgba(0,0,0,0.2);border-top-color:#333;width:1rem;height:1rem;border-radius:50%;animation:spin 0.7s linear infinite;"></span> Connecting…';

    window.location.href = 'api/auth/google';
  });

  /* ------------------------------------------------------------------ */
  /* Field State Helper                                                  */
  /* ------------------------------------------------------------------ */
  function setFieldState(input, isValid, message) {
    input.classList.toggle('is-valid',   isValid);
    input.classList.toggle('is-invalid', !isValid);
    if (!isValid && message) {
      let fb = input.parentElement.querySelector('.invalid-feedback');
      if (!fb) fb = input.nextElementSibling;
      if (fb) fb.textContent = message;
    }
  }
});
