/**
 * Fix&Go — Auth Utilities
 * Shared helpers: CSRF simulation, rate limiting, sanitization,
 * password strength, OTP wiring, show/hide password, alerts.
 */
(function (global) {
  'use strict';

  /* ------------------------------------------------------------------ */
  /* CSRF Token (simulated — in production, fetch from server)           */
  /* ------------------------------------------------------------------ */
  function generateCSRF() {
    const arr = new Uint8Array(24);
    crypto.getRandomValues(arr);
    return Array.from(arr, b => b.toString(16).padStart(2, '0')).join('');
  }

  function injectCSRF() {
    document.querySelectorAll('[name="_csrf"]').forEach(function (el) {
      el.value = generateCSRF();
    });
  }

  /* ------------------------------------------------------------------ */
  /* Rate Limiter (client-side simulation)                               */
  /* Key: action string, max: attempts, window: ms                      */
  /* ------------------------------------------------------------------ */
  function RateLimiter(key, max, windowMs) {
    this.key = 'fg_rl_' + key;
    this.max = max;
    this.window = windowMs;
  }

  RateLimiter.prototype.check = function () {
    const now = Date.now();
    let data = JSON.parse(sessionStorage.getItem(this.key) || '{"count":0,"start":0}');
    if (now - data.start > this.window) {
      data = { count: 0, start: now };
    }
    data.count++;
    sessionStorage.setItem(this.key, JSON.stringify(data));
    return data.count <= this.max;
  };

  RateLimiter.prototype.remaining = function () {
    const now = Date.now();
    const data = JSON.parse(sessionStorage.getItem(this.key) || '{"count":0,"start":0}');
    if (now - data.start > this.window) return this.max;
    return Math.max(0, this.max - data.count);
  };

  RateLimiter.prototype.reset = function () {
    sessionStorage.removeItem(this.key);
  };

  /* ------------------------------------------------------------------ */
  /* Input Sanitization                                                  */
  /* ------------------------------------------------------------------ */
  function sanitize(str) {
    if (typeof str !== 'string') return '';
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#x27;')
      .trim();
  }

  function isValidEmail(email) {
    // RFC 5322-ish, practical regex
    return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.trim());
  }

  /* ------------------------------------------------------------------ */
  /* Password Strength                                                   */
  /* Returns { score: 0-4, label, color }                               */
  /* ------------------------------------------------------------------ */
  function checkPasswordStrength(pw) {
    let score = 0;
    if (pw.length >= 8)  score++;
    if (pw.length >= 12) score++;
    if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
    if (/\d/.test(pw))   score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;

    const levels = [
      { label: '',         color: '',          pct: 0   },
      { label: 'Weak',     color: '#DC3545',   pct: 25  },
      { label: 'Fair',     color: '#FFC107',   pct: 50  },
      { label: 'Good',     color: '#17A2B8',   pct: 75  },
      { label: 'Strong',   color: '#28A745',   pct: 100 },
    ];

    const idx = Math.min(score, 4);
    return { score: idx, ...levels[idx] };
  }

  /* ------------------------------------------------------------------ */
  /* Password Rules Checker                                              */
  /* ------------------------------------------------------------------ */
  function checkPasswordRules(pw, prefix) {
    prefix = prefix || '';
    const rules = {
      length: pw.length >= 8,
      upper:  /[A-Z]/.test(pw),
      number: /\d/.test(pw),
    };

    // Only update DOM elements if they exist (checklist may have been removed)
    Object.keys(rules).forEach(function (key) {
      const el = document.getElementById((prefix ? prefix + '-' : '') + 'rule-' + key);
      if (!el) return;
      const icon = el.querySelector('.rule-icon');
      if (rules[key]) {
        el.className = 'valid';
        if (icon) icon.className = 'bi bi-check-circle-fill rule-icon';
      } else {
        el.className = 'invalid';
        if (icon) icon.className = 'bi bi-x-circle rule-icon';
      }
    });

    return Object.values(rules).every(Boolean);
  }

  /* ------------------------------------------------------------------ */
  /* Strength Bar Updater                                                */
  /* ------------------------------------------------------------------ */
  function updateStrengthBar(pw, fillId, textId) {
    const fill = document.getElementById(fillId);
    const text = document.getElementById(textId);
    if (!fill || !text) return;
    const s = checkPasswordStrength(pw);
    fill.style.width = s.pct + '%';
    fill.style.background = s.color;
    text.textContent = s.label;
    text.style.color = s.color;
  }

  /* ------------------------------------------------------------------ */
  /* Show/Hide Password Toggle                                           */
  /* ------------------------------------------------------------------ */
  function initPasswordToggles() {
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const targetId = btn.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (!input) return;
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        const icon = btn.querySelector('i');
        if (icon) icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
      });
    });
  }

  /* ------------------------------------------------------------------ */
  /* Alert Helper                                                        */
  /* ------------------------------------------------------------------ */
  function showAlert(id, message, type) {
    // type: 'danger' | 'success' | 'warning' | 'info'
    const el = document.getElementById(id);
    if (!el) return;
    el.className = 'auth-alert alert alert-' + (type || 'danger');
    el.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message;
    el.classList.remove('d-none');
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function hideAlert(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('d-none');
  }

  /* ------------------------------------------------------------------ */
  /* Button Loading State                                                */
  /* ------------------------------------------------------------------ */
  function setButtonLoading(btn, loading) {
    if (!btn) return;
    if (loading) {
      btn.classList.add('loading');
      btn.disabled = true;
    } else {
      btn.classList.remove('loading');
      btn.disabled = false;
    }
  }

  /* ------------------------------------------------------------------ */
  /* OTP Input Wiring                                                    */
  /* Auto-advance, backspace, paste support                             */
  /* ------------------------------------------------------------------ */
  function initOTPInputs(containerSelector, onComplete) {
    const inputs = document.querySelectorAll(containerSelector + ' .otp-input');
    if (!inputs.length) return;

    inputs.forEach(function (input, idx) {
      input.addEventListener('input', function (e) {
        // Allow only digits
        input.value = input.value.replace(/\D/g, '').slice(-1);
        if (input.value) {
          input.classList.add('filled');
          if (idx < inputs.length - 1) inputs[idx + 1].focus();
        } else {
          input.classList.remove('filled');
        }
        checkComplete();
      });

      input.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && !input.value && idx > 0) {
          inputs[idx - 1].focus();
          inputs[idx - 1].value = '';
          inputs[idx - 1].classList.remove('filled');
        }
      });

      input.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        pasted.split('').forEach(function (char, i) {
          if (inputs[idx + i]) {
            inputs[idx + i].value = char;
            inputs[idx + i].classList.add('filled');
          }
        });
        const next = Math.min(idx + pasted.length, inputs.length - 1);
        inputs[next].focus();
        checkComplete();
      });
    });

    function checkComplete() {
      const code = Array.from(inputs).map(function (i) { return i.value; }).join('');
      if (code.length === inputs.length && typeof onComplete === 'function') {
        onComplete(code);
      }
    }
  }

  function getOTPValue(containerSelector) {
    return Array.from(document.querySelectorAll(containerSelector + ' .otp-input'))
      .map(function (i) { return i.value; })
      .join('');
  }

  function clearOTPInputs(containerSelector) {
    document.querySelectorAll(containerSelector + ' .otp-input').forEach(function (i) {
      i.value = '';
      i.classList.remove('filled');
    });
    const first = document.querySelector(containerSelector + ' .otp-input');
    if (first) first.focus();
  }

  /* ------------------------------------------------------------------ */
  /* Countdown Timer                                                     */
  /* ------------------------------------------------------------------ */
  function startCountdown(seconds, displayId, onExpire) {
    const el = document.getElementById(displayId);
    let remaining = seconds;

    function tick() {
      if (!el) return;
      const m = Math.floor(remaining / 60);
      const s = remaining % 60;
      el.textContent = m + ':' + String(s).padStart(2, '0');
      if (remaining <= 0) {
        if (typeof onExpire === 'function') onExpire();
        return;
      }
      remaining--;
      setTimeout(tick, 1000);
    }
    tick();
  }

  /* ------------------------------------------------------------------ */
  /* Session / User Store (simulated — replaces real JWT/session)       */
  /* ------------------------------------------------------------------ */
  var UserStore = {
    save: function (user) {
      // In production: store JWT in httpOnly cookie via server
      sessionStorage.setItem('fg_user', JSON.stringify(user));
    },
    get: function () {
      try {
        return JSON.parse(sessionStorage.getItem('fg_user'));
      } catch (e) {
        return null;
      }
    },
    clear: function () {
      sessionStorage.removeItem('fg_user');
      localStorage.removeItem('fg_remember');
    },
    isLoggedIn: function () {
      return !!this.get();
    },
  };

  /* ------------------------------------------------------------------ */
  /* Google OAuth Simulation                                             */
  /* In production: use Google Identity Services SDK                    */
  /* ------------------------------------------------------------------ */
  function simulateGoogleAuth(callback) {
    // Simulates the Google OAuth popup flow
    const fakeGoogleUser = {
      id: 'google_' + Math.random().toString(36).slice(2),
      firstName: 'Google',
      lastName: 'User',
      email: 'googleuser@gmail.com',
      role: 'customer',
      provider: 'google',
      verified: true,
    };
    setTimeout(function () {
      callback(null, fakeGoogleUser);
    }, 1200);
  }

  /* ------------------------------------------------------------------ */
  /* OTP Generation (client-side simulation)                             */
  /* In production: generated server-side, sent via email               */
  /* ------------------------------------------------------------------ */
  function generateOTP() {
    return String(Math.floor(100000 + Math.random() * 900000));
  }

  /* ------------------------------------------------------------------ */
  /* Expose to global scope                                              */
  /* ------------------------------------------------------------------ */
  global.FGAuth = {
    injectCSRF,
    RateLimiter,
    sanitize,
    isValidEmail,
    checkPasswordStrength,
    checkPasswordRules,
    updateStrengthBar,
    initPasswordToggles,
    showAlert,
    hideAlert,
    setButtonLoading,
    initOTPInputs,
    getOTPValue,
    clearOTPInputs,
    startCountdown,
    UserStore,
    simulateGoogleAuth,
    generateOTP,
  };

  // Auto-inject CSRF on DOM ready
  document.addEventListener('DOMContentLoaded', function () {
    FGAuth.injectCSRF();
    FGAuth.initPasswordToggles();
  });

})(window);
