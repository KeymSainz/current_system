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
  /* Logout Confirmation Modal                                           */
  /* Auto-intercepts any #logoutBtn or onclick="customerLogout()"       */
  /* ------------------------------------------------------------------ */
  function injectLogoutModal() {
    // Inject modal HTML once
    if (document.getElementById('fgLogoutModal')) return;

    const modal = document.createElement('div');
    modal.id = 'fgLogoutModal';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-labelledby', 'fgLogoutTitle');
    modal.style.cssText = [
      'position:fixed', 'inset:0', 'z-index:99999',
      'display:none', 'align-items:center', 'justify-content:center',
      'padding:1rem', 'background:rgba(0,0,0,0.55)',
      'backdrop-filter:blur(6px)', '-webkit-backdrop-filter:blur(6px)',
    ].join(';');

    modal.innerHTML = `
      <div style="
        background:var(--fg-card-bg,#fff);
        border:1px solid var(--fg-border,#e5e7eb);
        border-radius:20px;
        box-shadow:0 32px 80px rgba(0,0,0,0.35);
        width:100%;max-width:420px;
        animation:fgModalIn 0.25s cubic-bezier(0.16,1,0.3,1);
        overflow:hidden;
      ">
        <!-- Header -->
        <div style="
          padding:1.5rem 1.75rem 1.25rem;
          border-bottom:1px solid var(--fg-border,#e5e7eb);
          display:flex;align-items:center;gap:0.85rem;
        ">
          <div style="
            width:44px;height:44px;border-radius:12px;flex-shrink:0;
            background:rgba(220,53,69,0.1);
            display:flex;align-items:center;justify-content:center;
            font-size:1.3rem;
          ">🚪</div>
          <div>
            <h5 id="fgLogoutTitle" style="margin:0;font-weight:800;font-size:1.05rem;color:var(--fg-text,#111);">
              Sign Out
            </h5>
            <p style="margin:0;font-size:0.8rem;color:var(--fg-muted,#6b7280);">
              Fix&amp;Go
            </p>
          </div>
          <button id="fgLogoutClose" aria-label="Cancel" style="
            margin-left:auto;width:32px;height:32px;border-radius:8px;
            border:1.5px solid var(--fg-border,#e5e7eb);background:transparent;
            cursor:pointer;display:flex;align-items:center;justify-content:center;
            color:var(--fg-muted,#6b7280);font-size:1rem;transition:all 0.2s;
          ">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
              <path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div style="padding:1.75rem 1.75rem 1.25rem;text-align:center;">
          <div style="
            width:72px;height:72px;border-radius:50%;margin:0 auto 1.25rem;
            background:rgba(220,53,69,0.08);
            display:flex;align-items:center;justify-content:center;
            font-size:2rem;
          ">👋</div>
          <h6 style="font-size:1.1rem;font-weight:800;color:var(--fg-text,#111);margin:0 0 0.5rem;">
            Are you sure you want to sign out?
          </h6>
          <p style="font-size:0.88rem;color:var(--fg-muted,#6b7280);margin:0;line-height:1.6;">
            You'll need to sign in again to access your dashboard and account.
          </p>
        </div>

        <!-- Footer -->
        <div style="
          padding:1rem 1.75rem 1.5rem;
          display:flex;gap:0.75rem;
        ">
          <button id="fgLogoutCancel" style="
            flex:1;padding:0.75rem;border-radius:10px;
            background:transparent;
            color:var(--fg-muted,#6b7280);
            border:1.5px solid var(--fg-border,#e5e7eb);
            font-weight:600;font-size:0.9rem;cursor:pointer;
            transition:all 0.2s;
          ">
            Cancel
          </button>
          <button id="fgLogoutConfirm" style="
            flex:1;padding:0.75rem;border-radius:10px;
            background:#dc3545;color:#fff;border:none;
            font-weight:700;font-size:0.9rem;cursor:pointer;
            transition:all 0.2s;
            display:flex;align-items:center;justify-content:center;gap:0.5rem;
          ">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Yes, Sign Out
          </button>
        </div>
      </div>
      <style>
        @keyframes fgModalIn {
          from { opacity:0; transform:scale(0.9) translateY(12px); }
          to   { opacity:1; transform:scale(1) translateY(0); }
        }
        #fgLogoutConfirm:hover { background:#b02a37 !important; transform:translateY(-1px); box-shadow:0 6px 18px rgba(220,53,69,0.35); }
        #fgLogoutCancel:hover  { border-color:var(--fg-text,#111) !important; color:var(--fg-text,#111) !important; }
        #fgLogoutClose:hover   { border-color:#dc3545 !important; color:#dc3545 !important; background:rgba(220,53,69,0.06) !important; }
      </style>
    `;

    document.body.appendChild(modal);

    // Close handlers
    function closeModal() {
      modal.style.display = 'none';
    }

    document.getElementById('fgLogoutClose').addEventListener('click', closeModal);
    document.getElementById('fgLogoutCancel').addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
      if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && modal.style.display !== 'none') closeModal();
    });

    // Confirm handler — resolve the pending logout callback
    document.getElementById('fgLogoutConfirm').addEventListener('click', function() {
      const btn = this;
      btn.disabled = true;
      btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 0.7s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Signing out…';
      closeModal();
      if (typeof modal._logoutCallback === 'function') {
        modal._logoutCallback();
      }
    });
  }

  /**
   * Show the logout confirmation modal.
   * @param {Function} onConfirm  Called when user clicks "Yes, Sign Out"
   */
  function showLogoutModal(onConfirm) {
    injectLogoutModal();
    const modal = document.getElementById('fgLogoutModal');
    // Reset confirm button state
    const confirmBtn = document.getElementById('fgLogoutConfirm');
    if (confirmBtn) {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Yes, Sign Out`;
    }
    modal._logoutCallback = onConfirm;
    modal.style.display = 'flex';
    document.getElementById('fgLogoutCancel').focus();
  }

  /**
   * Auto-wire any #logoutBtn on the page to show the modal.
   * Also patches the global customerLogout() function if present.
   */
  function autoWireLogout() {
    // Wire #logoutBtn
    const btn = document.getElementById('logoutBtn');
    if (btn) {
      // Remove existing listeners by cloning
      const fresh = btn.cloneNode(true);
      btn.parentNode.replaceChild(fresh, btn);
      fresh.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        showLogoutModal(function() {
          // Detect backend path based on depth
          const depth = (window.location.pathname.match(/\//g) || []).length;
          let backendPath = 'api/logout';
          if (depth >= 5) backendPath = '../../../backend/logout.php';
          else if (depth >= 4) backendPath = '../../backend/logout.php';

          fetch(backendPath, { method: 'POST' })
            .catch(function() {})
            .finally(function() {
              FGAuth.UserStore.clear();
              // Redirect to root index
              const parts = window.location.pathname.split('/');
              const fgIdx = parts.indexOf('fixandgo');
              const base = fgIdx >= 0 ? parts.slice(0, fgIdx + 1).join('/') : '';
              window.location.href = base + '/index.php?logout=true';
            });
        });
      });
    }

    // Patch global customerLogout() if it exists
    if (typeof window.customerLogout === 'function') {
      const original = window.customerLogout;
      window.customerLogout = function() {
        showLogoutModal(original);
      };
    }
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
    showLogoutModal,
    autoWireLogout,
  };

  // Auto-inject CSRF on DOM ready
  document.addEventListener('DOMContentLoaded', function () {
    FGAuth.injectCSRF();
    FGAuth.initPasswordToggles();
    FGAuth.autoWireLogout();
  });

})(window);
