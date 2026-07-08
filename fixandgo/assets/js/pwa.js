/**
 * Fix&Go — PWA Registration & Install Prompt
 */
(function () {
  'use strict';

  // ── Register Service Worker ──────────────────────────────────
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker
        .register('/fixandgo/sw.js', { scope: '/' })
        .then(function (reg) {
          console.log('[PWA] Service worker registered. Scope:', reg.scope);

          // Check for updates every 60 s when the page is focused
          setInterval(function () {
            if (document.visibilityState === 'visible') reg.update();
          }, 60000);

          // Notify user when a new version is available
          reg.addEventListener('updatefound', function () {
            var newWorker = reg.installing;
            newWorker.addEventListener('statechange', function () {
              if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                showUpdateBanner();
              }
            });
          });
        })
        .catch(function (err) {
          console.warn('[PWA] Service worker registration failed:', err);
        });
    });
  }

  // ── Install Prompt Banner ────────────────────────────────────
  var deferredPrompt = null;

  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;

    // Only show if not already installed / not dismissed in this session
    if (!sessionStorage.getItem('pwa-install-dismissed')) {
      setTimeout(showInstallBanner, 3000); // slight delay — not intrusive
    }
  });

  window.addEventListener('appinstalled', function () {
    hideBanner('pwa-install-banner');
    deferredPrompt = null;
    sessionStorage.setItem('pwa-install-dismissed', '1');
    console.log('[PWA] App installed.');
  });

  function showInstallBanner() {
    if (document.getElementById('pwa-install-banner')) return;

    var banner = document.createElement('div');
    banner.id = 'pwa-install-banner';
    banner.innerHTML = [
      '<div style="display:flex;align-items:center;gap:0.75rem;flex:1;">',
        '<span style="font-size:1.5rem;flex-shrink:0;">🔧</span>',
        '<div>',
          '<div style="font-weight:800;font-size:0.88rem;color:#fff;">Install Fix&amp;Go</div>',
          '<div style="font-size:0.75rem;color:rgba(255,255,255,0.7);margin-top:0.1rem;">Add to your home screen for quick access</div>',
        '</div>',
      '</div>',
      '<div style="display:flex;gap:0.5rem;flex-shrink:0;">',
        '<button id="pwa-install-btn" style="padding:0.45rem 1rem;border-radius:9px;background:linear-gradient(135deg,#e6a800,#c98f00);color:#000;border:none;font-weight:800;font-size:0.8rem;cursor:pointer;">Install</button>',
        '<button id="pwa-dismiss-btn" style="padding:0.45rem 0.75rem;border-radius:9px;background:rgba(255,255,255,0.15);color:#fff;border:none;font-size:0.8rem;cursor:pointer;">✕</button>',
      '</div>',
    ].join('');

    Object.assign(banner.style, {
      position: 'fixed',
      bottom: '0',
      left: '0',
      right: '0',
      zIndex: '99999',
      display: 'flex',
      alignItems: 'center',
      gap: '0.75rem',
      padding: '0.9rem 1rem',
      background: 'linear-gradient(135deg,#1a1d2e,#0f1117)',
      borderTop: '1px solid rgba(230,168,0,0.3)',
      boxShadow: '0 -4px 24px rgba(0,0,0,0.4)',
      flexWrap: 'nowrap',
    });

    document.body.appendChild(banner);

    document.getElementById('pwa-install-btn').addEventListener('click', function () {
      if (!deferredPrompt) return;
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then(function (result) {
        if (result.outcome === 'accepted') {
          hideBanner('pwa-install-banner');
          sessionStorage.setItem('pwa-install-dismissed', '1');
        }
        deferredPrompt = null;
      });
    });

    document.getElementById('pwa-dismiss-btn').addEventListener('click', function () {
      hideBanner('pwa-install-banner');
      sessionStorage.setItem('pwa-install-dismissed', '1');
    });
  }

  function showUpdateBanner() {
    if (document.getElementById('pwa-update-banner')) return;

    var banner = document.createElement('div');
    banner.id = 'pwa-update-banner';
    banner.innerHTML = [
      '<span style="flex:1;font-size:0.86rem;color:#fff;">🎉 A new version of Fix&amp;Go is ready!</span>',
      '<button id="pwa-update-btn" style="padding:0.4rem 0.9rem;border-radius:8px;background:#e6a800;color:#000;border:none;font-weight:800;font-size:0.8rem;cursor:pointer;white-space:nowrap;">Update Now</button>',
      '<button onclick="document.getElementById(\'pwa-update-banner\').remove()" style="padding:0.4rem 0.6rem;border-radius:8px;background:rgba(255,255,255,0.12);color:#fff;border:none;cursor:pointer;font-size:0.8rem;">✕</button>',
    ].join('');

    Object.assign(banner.style, {
      position: 'fixed',
      top: '0',
      left: '0',
      right: '0',
      zIndex: '99999',
      display: 'flex',
      alignItems: 'center',
      gap: '0.6rem',
      padding: '0.75rem 1rem',
      background: 'linear-gradient(135deg,#1a3a2e,#0f2a1e)',
      borderBottom: '1px solid rgba(40,167,69,0.4)',
      boxShadow: '0 4px 20px rgba(0,0,0,0.3)',
    });

    document.body.appendChild(banner);

    document.getElementById('pwa-update-btn').addEventListener('click', function () {
      window.location.reload();
    });
  }

  function hideBanner(id) {
    var el = document.getElementById(id);
    if (el) el.remove();
  }
})();
