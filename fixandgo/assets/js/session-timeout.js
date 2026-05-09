/**
 * Fix&Go — Session Timeout Manager
 *
 * - Tracks user activity (mouse, keyboard, touch, scroll)
 * - Pings the server every 4 minutes while active to keep session alive
 * - Shows a warning modal 2 minutes before expiry
 * - Auto-logs out when the session expires
 * - Works for all roles: customer, supplier, owner, sales_person, supervisor, admin
 *
 * Include this script on every protected page:
 *   <script src="path/to/session-timeout.js"></script>
 */

(function () {
  'use strict';

  /* ── Config ─────────────────────────────────────────────────────────── */
  const TIMEOUT_MS       = 10 * 60 * 1000;   // 10 minutes (must match PHP)
  const WARNING_BEFORE   = 2  * 60 * 1000;   // show warning 2 min before expiry
  const PING_INTERVAL    = 4  * 60 * 1000;   // ping server every 4 min when active
  const CHECK_INTERVAL   = 10 * 1000;        // check countdown every 10 seconds
  const ACTIVITY_DEBOUNCE = 30 * 1000;       // don't ping more than once per 30s

  /* ── Resolve the backend path relative to any page depth ────────────── */
  function resolveBackendPath(file) {
    // Walk up from current page to find the fixandgo root
    const path = window.location.pathname;
    const parts = path.split('/').filter(Boolean);
    const fixandgoIdx = parts.indexOf('fixandgo');
    if (fixandgoIdx === -1) return 'backend/' + file;

    const depth = parts.length - fixandgoIdx - 1; // levels below fixandgo/
    const prefix = depth > 0 ? '../'.repeat(depth) : '';
    return prefix + 'backend/' + file;
  }

  const PING_URL   = resolveBackendPath('session-ping.php');
  const LOGOUT_URL = resolveBackendPath('logout.php');

  /* ── State ───────────────────────────────────────────────────────────── */
  let lastActivityTime = Date.now();
  let warningShown     = false;
  let countdownTimer   = null;
  let pingTimer        = null;
  let checkTimer       = null;
  let lastPingTime     = 0;

  /* ── Activity tracking ───────────────────────────────────────────────── */
  function onActivity() {
    lastActivityTime = Date.now();

    // If warning is showing, dismiss it and reset
    if (warningShown) {
      hideWarning();
      pingServer(); // immediately reset server-side timer
    }

    // Debounced ping
    const now = Date.now();
    if (now - lastPingTime > ACTIVITY_DEBOUNCE) {
      pingServer();
    }
  }

  ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click']
    .forEach(evt => document.addEventListener(evt, onActivity, { passive: true }));

  /* ── Server ping ─────────────────────────────────────────────────────── */
  function pingServer() {
    lastPingTime = Date.now();
    fetch(PING_URL, {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
    })
      .then(r => r.json())
      .then(d => {
        if (d.expired || !d.loggedIn) {
          forceLogout('expired');
        }
      })
      .catch(() => {}); // silent — don't disrupt UX on network hiccup
  }

  /* ── Periodic server check ───────────────────────────────────────────── */
  function checkSession() {
    fetch(PING_URL + '?check=1', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.expired || !d.loggedIn) {
          forceLogout('expired');
          return;
        }

        const remaining = d.secondsRemaining * 1000;

        if (remaining <= WARNING_BEFORE && !warningShown) {
          showWarning(Math.ceil(remaining / 1000));
        } else if (remaining > WARNING_BEFORE && warningShown) {
          hideWarning();
        } else if (warningShown) {
          updateCountdown(Math.ceil(remaining / 1000));
        }
      })
      .catch(() => {});
  }

  /* ── Periodic ping while active ──────────────────────────────────────── */
  function startPingLoop() {
    pingTimer = setInterval(() => {
      const idle = Date.now() - lastActivityTime;
      if (idle < PING_INTERVAL) {
        pingServer();
      }
    }, PING_INTERVAL);
  }

  /* ── Warning modal ───────────────────────────────────────────────────── */
  function showWarning(secondsLeft) {
    if (warningShown) return;
    warningShown = true;

    // Inject modal HTML
    const overlay = document.createElement('div');
    overlay.id = 'fg-session-overlay';
    overlay.innerHTML = `
      <div id="fg-session-modal">
        <div class="fg-st-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <h3 class="fg-st-title">Session Expiring Soon</h3>
        <p class="fg-st-body">
          You've been inactive for a while. Your session will expire in
        </p>
        <div class="fg-st-countdown" id="fg-st-countdown">${formatTime(secondsLeft)}</div>
        <p class="fg-st-body" style="margin-top:0.5rem;font-size:0.82rem;">
          Click <strong>Stay Logged In</strong> to continue your session.
        </p>
        <div class="fg-st-actions">
          <button class="fg-st-btn fg-st-btn-primary" id="fg-st-stay">
            Stay Logged In
          </button>
          <button class="fg-st-btn fg-st-btn-secondary" id="fg-st-logout">
            Log Out Now
          </button>
        </div>
      </div>
    `;

    // Inject styles
    if (!document.getElementById('fg-session-styles')) {
      const style = document.createElement('style');
      style.id = 'fg-session-styles';
      style.textContent = `
        #fg-session-overlay {
          position: fixed; inset: 0; z-index: 99999;
          background: rgba(0,0,0,0.65);
          backdrop-filter: blur(6px);
          display: flex; align-items: center; justify-content: center;
          padding: 1rem;
          animation: fg-fade-in 0.25s ease;
        }
        @keyframes fg-fade-in {
          from { opacity: 0; }
          to   { opacity: 1; }
        }
        #fg-session-modal {
          background: var(--fg-card-bg, #1a1d27);
          border: 1px solid var(--fg-border, #2a2d3a);
          border-radius: 18px;
          padding: 2rem 2rem 1.75rem;
          max-width: 400px; width: 100%;
          text-align: center;
          box-shadow: 0 24px 64px rgba(0,0,0,0.5);
          animation: fg-slide-up 0.3s cubic-bezier(0.16,1,0.3,1);
        }
        @keyframes fg-slide-up {
          from { opacity: 0; transform: translateY(20px) scale(0.97); }
          to   { opacity: 1; transform: translateY(0)   scale(1);    }
        }
        .fg-st-icon {
          width: 64px; height: 64px; border-radius: 50%;
          background: rgba(230,168,0,0.15);
          border: 2px solid rgba(230,168,0,0.4);
          display: flex; align-items: center; justify-content: center;
          margin: 0 auto 1.25rem;
          color: #e6a800;
        }
        .fg-st-icon svg { width: 30px; height: 30px; }
        .fg-st-title {
          font-size: 1.15rem; font-weight: 800;
          color: var(--fg-text, #f1f5f9);
          margin: 0 0 0.6rem;
        }
        .fg-st-body {
          font-size: 0.88rem;
          color: var(--fg-muted, #64748b);
          margin: 0 0 0.5rem; line-height: 1.5;
        }
        .fg-st-countdown {
          font-size: 2.5rem; font-weight: 800;
          color: #e6a800;
          letter-spacing: 2px;
          margin: 0.5rem 0 1rem;
          font-variant-numeric: tabular-nums;
        }
        .fg-st-countdown.urgent { color: #ef4444; animation: fg-pulse 1s infinite; }
        @keyframes fg-pulse {
          0%,100% { opacity: 1; }
          50%      { opacity: 0.6; }
        }
        .fg-st-actions {
          display: flex; gap: 0.75rem; margin-top: 1.25rem;
        }
        .fg-st-btn {
          flex: 1; padding: 0.65rem 1rem;
          border-radius: 10px; font-size: 0.88rem;
          font-weight: 700; cursor: pointer;
          border: none; transition: all 0.2s;
        }
        .fg-st-btn-primary {
          background: #e6a800; color: #000;
        }
        .fg-st-btn-primary:hover { background: #d4970a; }
        .fg-st-btn-secondary {
          background: rgba(239,68,68,0.12); color: #ef4444;
          border: 1.5px solid rgba(239,68,68,0.3);
        }
        .fg-st-btn-secondary:hover { background: rgba(239,68,68,0.22); }
      `;
      document.head.appendChild(style);
    }

    document.body.appendChild(overlay);

    // Button handlers
    document.getElementById('fg-st-stay').addEventListener('click', function () {
      hideWarning();
      pingServer();
      lastActivityTime = Date.now();
    });

    document.getElementById('fg-st-logout').addEventListener('click', function () {
      forceLogout('manual');
    });

    // Start countdown display
    let secs = secondsLeft;
    countdownTimer = setInterval(() => {
      secs--;
      if (secs <= 0) {
        clearInterval(countdownTimer);
        forceLogout('expired');
      } else {
        updateCountdown(secs);
      }
    }, 1000);
  }

  function updateCountdown(secs) {
    const el = document.getElementById('fg-st-countdown');
    if (!el) return;
    el.textContent = formatTime(secs);
    if (secs <= 30) {
      el.classList.add('urgent');
    }
  }

  function hideWarning() {
    warningShown = false;
    clearInterval(countdownTimer);
    const overlay = document.getElementById('fg-session-overlay');
    if (overlay) overlay.remove();
  }

  /* ── Force logout ────────────────────────────────────────────────────── */
  function forceLogout(reason) {
    clearInterval(pingTimer);
    clearInterval(checkTimer);
    clearInterval(countdownTimer);

    // Clear client-side session
    try { sessionStorage.removeItem('fg_user'); } catch (e) {}

    // Show expired message if auto-expired
    if (reason === 'expired') {
      const overlay = document.getElementById('fg-session-overlay');
      if (overlay) overlay.remove();

      const expiredOverlay = document.createElement('div');
      expiredOverlay.id = 'fg-session-overlay';
      expiredOverlay.innerHTML = `
        <div id="fg-session-modal">
          <div class="fg-st-icon" style="background:rgba(239,68,68,0.12);border-color:rgba(239,68,68,0.4);color:#ef4444;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <h3 class="fg-st-title">Session Expired</h3>
          <p class="fg-st-body">
            Your session has expired due to inactivity.<br>
            Please log in again to continue.
          </p>
          <div class="fg-st-actions" style="justify-content:center;">
            <button class="fg-st-btn fg-st-btn-primary" style="max-width:200px;" id="fg-st-relogin">
              Log In Again
            </button>
          </div>
        </div>
      `;
      document.body.appendChild(expiredOverlay);

      document.getElementById('fg-st-relogin').addEventListener('click', doLogout);

      // Auto-redirect after 4 seconds
      setTimeout(doLogout, 4000);
    } else {
      doLogout();
    }
  }

  function doLogout() {
    fetch(LOGOUT_URL, {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ reason: 'timeout' }),
    })
      .catch(() => {})
      .finally(() => {
        try { sessionStorage.removeItem('fg_user'); } catch (e) {}
        const loginUrl = resolveBackendPath('').replace('backend/', '') + 'login.html?reason=timeout';
        window.location.href = loginUrl;
      });
  }

  /* ── Helpers ─────────────────────────────────────────────────────────── */
  function formatTime(totalSeconds) {
    const m = Math.floor(totalSeconds / 60);
    const s = totalSeconds % 60;
    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
  }

  /* ── Init ────────────────────────────────────────────────────────────── */
  function init() {
    // Only run on pages where a user is logged in
    const user = (function () {
      try { return JSON.parse(sessionStorage.getItem('fg_user') || 'null'); }
      catch (e) { return null; }
    })();

    if (!user) return; // not logged in — nothing to do

    // Set initial activity time
    lastActivityTime = Date.now();

    // Start loops
    startPingLoop();
    checkTimer = setInterval(checkSession, CHECK_INTERVAL);

    // Do an immediate check to sync with server state
    setTimeout(checkSession, 2000);
  }

  // Run after DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
