<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- FG_BACKEND must be first -->
  <script>window.FG_BACKEND = 'api/session/user'';</script>
  <!-- PWA -->
  <meta name="theme-color" content="#e6a800">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Fix&amp;Go">
  <link rel="manifest" href="fixandgo/manifest.json">
  <link rel="apple-touch-icon" href="fixandgo/assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="fixandgo/assets/css/mobile.css">
  <title>Fix&amp;Go — Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="fixandgo/assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="fixandgo/assets/css/dashboard.css?v=6" />
  <link rel="stylesheet" href="fixandgo/assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <!-- Mobile navbar override — must come AFTER auth.css to win specificity -->
  <style>
    @media (max-width: 991px) {
      #navDesktopControls { display: none !important; }
      #navMobileControls  { display: flex !important; }
      /* Prevent auth.css from wrapping the navbar controls */
      .fg-navbar,
      .fg-navbar > div,
      .fg-navbar > div:last-child { flex-wrap: nowrap !important; }
      /* Hide theme toggle + logout on mobile — in drawer instead */
      .fg-navbar .theme-toggle,
      .fg-navbar #themeToggle,
      .fg-navbar #logoutBtn { display: none !important; }
    }
    @media (min-width: 992px) {
      #navMobileControls  { display: none !important; }
      #dashDrawer,
      #dashDrawerOverlay  { display: none !important; }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation" aria-label="Main navigation">
    <a href="index.php" id="navLogoLink" style="text-decoration:none;display:flex;align-items:center;">
      <img src="fixandgo/assets/images/logo.png" alt="Fix&Go" id="navLogo"
           style="height:40px;width:auto;object-fit:contain;"
           onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
    </a>
    <!-- Desktop controls -->
    <div class="d-flex align-items-center gap-3" id="navDesktopControls" style="flex-wrap:nowrap;">
      <a href="index.php?browse=1" class="btn btn-sm" id="backToLandingBtn"
        style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;display:none;">
        <i class="bi bi-house-door"></i> Browse Shop
      </a>
      <button class="btn btn-sm" id="toggleViewBtn"
        style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-text);background:transparent;font-size:0.85rem;font-weight:600;display:none;">
        <i class="bi bi-grid-3x3-gap"></i> <span id="toggleViewText">Dashboard</span>
      </button>
      <a href="views/user/owner/cart.php" id="cartIcon" style="position:relative;display:none;text-decoration:none;">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1.1rem;color:var(--fg-text);">
          <i class="bi bi-cart-fill"></i>
        </div>
        <span id="cartBadge" style="position:absolute;top:-4px;right:-4px;background:#dc3545;color:#fff;font-size:0.65rem;font-weight:700;padding:0.15rem 0.4rem;border-radius:10px;min-width:18px;text-align:center;line-height:1;display:none;"></span>
      </a>
      <span id="navRoleBadge" class="role-badge"></span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark/light mode">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
      </button>
      <button class="btn btn-sm" id="logoutBtn"
        style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
    </div>
    <!-- Mobile controls — only logo + essential icons -->
    <div class="d-flex align-items-center gap-2" id="navMobileControls" style="display:none!important;">
      <a href="views/user/owner/cart.php" id="cartIconMob" style="position:relative;display:none;text-decoration:none;">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:1rem;color:var(--fg-text);">
          <i class="bi bi-cart-fill"></i>
        </div>
        <span id="cartBadgeMob" style="position:absolute;top:-4px;right:-4px;background:#dc3545;color:#fff;font-size:0.55rem;font-weight:700;padding:0.1rem 0.3rem;border-radius:10px;min-width:14px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <button id="dashMobileMenuBtn" aria-label="Menu"
        style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);"
        onclick="toggleDashDrawer()">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </nav>

  <!-- Mobile drawer for dashboard -->
  <div id="dashDrawerOverlay" onclick="toggleDashDrawer()"
    style="display:none;position:fixed;inset:0;z-index:1099;background:rgba(0,0,0,0.55);"></div>
  <div id="dashDrawer"
    style="position:fixed;top:0;right:0;z-index:1100;height:100%;width:72vw;max-width:280px;
           background:var(--fg-card-bg,#1a1a2e);border-left:1px solid var(--fg-border,#2a2a2a);
           display:flex;flex-direction:column;transform:translateX(100%);
           transition:transform 0.3s cubic-bezier(0.4,0,0.2,1);
           box-shadow:-8px 0 32px rgba(0,0,0,0.4);padding:1.5rem 1rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
      <span id="dashDrawerName" style="font-weight:700;font-size:0.95rem;color:var(--fg-text,#fff);"></span>
      <button onclick="toggleDashDrawer()" style="background:none;border:none;cursor:pointer;color:var(--fg-muted,#888);font-size:1.2rem;"><i class="bi bi-x-lg"></i></button>
    </div>
    <nav style="display:flex;flex-direction:column;gap:0.25rem;flex:1;">
      <a href="index.php?browse=1" id="drawerBrowseShop" style="display:none;padding:0.75rem 1rem;border-radius:10px;color:var(--fg-text,#fff);text-decoration:none;font-weight:600;font-size:0.9rem;display:flex;align-items:center;gap:0.75rem;">
        <i class="bi bi-house-door"></i> Browse Shop
      </a>
      <button id="drawerToggleView" onclick="if(document.getElementById('toggleViewBtn'))document.getElementById('toggleViewBtn').click();toggleDashDrawer();"
        style="display:none;text-align:left;padding:0.75rem 1rem;border-radius:10px;background:none;border:none;cursor:pointer;color:var(--fg-text,#fff);font-weight:600;font-size:0.9rem;display:flex;align-items:center;gap:0.75rem;">
        <i class="bi bi-grid-3x3-gap"></i> <span id="drawerToggleText">Dashboard</span>
      </button>
      <button onclick="document.getElementById('themeToggle').click()"
        style="text-align:left;padding:0.75rem 1rem;border-radius:10px;background:none;border:none;cursor:pointer;color:var(--fg-text,#fff);font-weight:600;font-size:0.9rem;display:flex;align-items:center;gap:0.75rem;">
        <i class="bi bi-moon-fill" id="drawerThemeIcon"></i> <span id="drawerThemeText">Dark Mode</span>
      </button>
    </nav>
    <button id="drawerLogoutBtn"
      style="margin-top:auto;padding:0.75rem 1rem;border-radius:10px;background:rgba(220,53,69,0.1);border:1.5px solid rgba(220,53,69,0.3);cursor:pointer;color:#dc3545;font-weight:700;font-size:0.9rem;display:flex;align-items:center;gap:0.75rem;">
      <i class="bi bi-box-arrow-right"></i> Logout
    </button>
  </div>

  <div class="container-fluid px-4 py-4" id="dashboardContent" style="max-width:1300px;margin:0 auto;">

    <!-- Welcome banner -->
    <div class="dashboard-card mb-4" id="welcomeBanner">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <!-- Profile avatar — filled by dashboard.js -->
        <div id="welcomeAvatar" style="width:52px;height:52px;border-radius:50%;flex-shrink:0;overflow:hidden;border:2px solid rgba(230,168,0,0.3);background:rgba(230,168,0,0.12);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:var(--fg-primary);">
          <i class="bi bi-person-circle"></i>
        </div>
        <div>
          <h2 style="font-size:1.3rem;font-weight:700;margin:0;" id="welcomeTitle">Welcome back!</h2>
          <p style="margin:0;color:var(--fg-muted);font-size:0.9rem;" id="welcomeSubtitle"></p>
        </div>
      </div>
    </div>

    <!-- Role-specific content injected by JS -->
    <div id="roleContent"></div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="fixandgo/assets/js/theme.js"></script>
  <script src="fixandgo/assets/js/auth-utils.js"></script>
  <script src="fixandgo/assets/js/cart.js"></script>
  <script>
    window.FG_BACKEND = (function() {
      var parts = window.location.pathname.split('/').filter(Boolean);
      return parts.length <= 1 ? 'fixandgo/backend/' : 'api/session/user'';
    })();
    // Show Browse Shop button and Cart icon immediately from cached session (no waiting for fetch)
    (function() {
      try {
        const user = JSON.parse(sessionStorage.getItem('fg_user') || 'null');
        if (user && (user.role === 'owner' || user.role === 'supplier' || user.role === 'sales_person' || user.role === 'phone_technician')) {
          const btn = document.getElementById('backToLandingBtn');
          if (btn) {
            btn.href = 'index.php?browse=1';
            btn.style.display = 'inline-flex';
            btn.style.alignItems = 'center';
            btn.style.gap = '0.35rem';
          }
        }
        // Show cart icon for owner only
        if (user && user.role === 'owner') {
          const cartIcon = document.getElementById('cartIcon');
          if (cartIcon) {
            cartIcon.style.display = 'block';
          }
        }
      } catch(e) {}
    })();
  </script>
  <script src="fixandgo/assets/js/dashboard.js?v=25"></script>
  <script src="fixandgo/assets/js/pwa.js" defer></script>

<!-- ═══ DASHBOARD MOBILE NAVBAR + DRAWER ════════════════════ -->
<style>
  /* Mobile navbar override handled in <head> */
  #dashDrawer a:hover, #dashDrawer button:hover {
    background: rgba(230,168,0,0.08) !important;
    color: var(--fg-primary, #e6a800) !important;
  }
</style>
<script>
  // ── Dashboard mobile drawer ──────────────────────────────────
  function toggleDashDrawer() {
    var drawer  = document.getElementById('dashDrawer');
    var overlay = document.getElementById('dashDrawerOverlay');
    var open = drawer.style.transform === 'translateX(0%)';
    if (open) {
      drawer.style.transform  = 'translateX(100%)';
      overlay.style.display   = 'none';
      document.body.style.overflow = '';
    } else {
      drawer.style.transform  = 'translateX(0%)';
      overlay.style.display   = 'block';
      document.body.style.overflow = 'hidden';
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    // Sync drawer user name
    var nameEl = document.getElementById('navUserName');
    var drawerNameEl = document.getElementById('dashDrawerName');
    if (nameEl && drawerNameEl) {
      var syncName = function() { drawerNameEl.textContent = nameEl.textContent; };
      syncName();
      new MutationObserver(syncName).observe(nameEl, { childList: true, characterData: true, subtree: true });
    }

    // Sync mobile cart badge
    var cartBadge    = document.getElementById('cartBadge');
    var cartBadgeMob = document.getElementById('cartBadgeMob');
    var cartIcon     = document.getElementById('cartIcon');
    var cartIconMob  = document.getElementById('cartIconMob');
    if (cartBadge && cartBadgeMob) {
      new MutationObserver(function() {
        cartBadgeMob.textContent   = cartBadge.textContent;
        cartBadgeMob.style.display = cartBadge.style.display;
        if (cartIcon && cartIconMob) cartIconMob.style.display = cartIcon.style.display;
      }).observe(cartBadge, { attributes: true, childList: true });
    }

    // Sync Browse Shop visibility
    var browseBtn      = document.getElementById('backToLandingBtn');
    var drawerBrowse   = document.getElementById('drawerBrowseShop');
    var toggleViewBtn  = document.getElementById('toggleViewBtn');
    var drawerToggle   = document.getElementById('drawerToggleView');
    if (browseBtn && drawerBrowse) {
      new MutationObserver(function() {
        drawerBrowse.style.display = browseBtn.style.display !== 'none' ? 'flex' : 'none';
      }).observe(browseBtn, { attributes: true });
    }
    if (toggleViewBtn && drawerToggle) {
      new MutationObserver(function() {
        drawerToggle.style.display = toggleViewBtn.style.display !== 'none' ? 'flex' : 'none';
        var drawerText = document.getElementById('drawerToggleText');
        var toggleText = document.getElementById('toggleViewText');
        if (drawerText && toggleText) drawerText.textContent = toggleText.textContent;
      }).observe(toggleViewBtn, { attributes: true, childList: true, subtree: true });
    }

    // Theme icon sync
    var themeIcon = document.getElementById('themeIcon');
    var drawerThemeIcon = document.getElementById('drawerThemeIcon');
    var drawerThemeText = document.getElementById('drawerThemeText');
    if (themeIcon && drawerThemeIcon) {
      new MutationObserver(function() {
        var isDark = document.documentElement.getAttribute('data-theme') !== 'light';
        drawerThemeIcon.className = isDark ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
        if (drawerThemeText) drawerThemeText.textContent = isDark ? 'Dark Mode' : 'Light Mode';
      }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
    }

    // Drawer logout wires to main logout button
    var drawerLogout = document.getElementById('drawerLogoutBtn');
    var mainLogout   = document.getElementById('logoutBtn');
    if (drawerLogout && mainLogout) {
      drawerLogout.addEventListener('click', function() { mainLogout.click(); });
    }
  });
</script>

<!-- ═══ MOBILE BOTTOM NAV (dashboard) ════════════════════════ -->
<link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css" rel="stylesheet">
<style>
  /* Always block on mobile — JS also enforces this */
  #dashBottomNav {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    z-index: 9999;
    background: var(--fg-bg, #0F0F1A);
    border-top: 1px solid var(--fg-border, #2a2a2a);
    padding: 0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom, 0px));
    box-shadow: 0 -4px 20px rgba(0,0,0,0.3);
  }
  #dashBottomNav ul {
    list-style: none; margin: 0; padding: 0;
    display: flex; justify-content: space-around; align-items: center;
  }
  #dashBottomNav a {
    display: flex; flex-direction: column; align-items: center; gap: 0.15rem;
    padding: 0.3rem 0.75rem;
    color: #888;
    text-decoration: none;
    font-size: 0.62rem; font-weight: 700;
  }
  #dashBottomNav a.dbn-active,
  #dashBottomNav a:hover { color: #e6a800; }
  #dashBottomNav i { font-size: 1.2rem; }
</style>
<nav id="dashBottomNav" style="display:none;">
  <ul>
    <li>
      <a href="index.php" id="dbnHome">
        <i class="fa-solid fa-house"></i>Home
      </a>
    </li>
    <li>
      <a href="index.php#shop" id="dbnShop">
        <i class="fa-solid fa-shop"></i>Shop
      </a>
    </li>
    <li>
      <a href="index.php#technicians" id="dbnRepairs">
        <i class="fa-solid fa-wrench"></i>Repairs
      </a>
    </li>
    <li style="position:relative;">
      <a href="#" id="dbnAlerts">
        <span style="position:relative;display:inline-block;">
          <i class="fa-solid fa-bell"></i>
          <span id="dbnAlertsBadge" style="display:none;position:absolute;top:-5px;right:-6px;
                background:#dc3545;color:#fff;font-size:0.5rem;font-weight:800;
                padding:0.05rem 0.3rem;border-radius:10px;min-width:14px;text-align:center;">0</span>
        </span>Alerts
      </a>
    </li>
    <li>
      <a href="dashboard.php" id="dbnMe">
        <i class="fa-solid fa-user"></i>Me
      </a>
    </li>
  </ul>
</nav>
<script>
  (function() {
    var nav = document.getElementById('dashBottomNav');
    if (!nav) return;

    // Show on mobile, hide on desktop — checked by JS (bypasses all CSS specificity issues)
    function checkSize() {
      if (window.innerWidth <= 991) {
        nav.style.display = 'block';
        document.body.style.paddingBottom = 'calc(60px + env(safe-area-inset-bottom, 0px))';
      } else {
        nav.style.display = 'none';
        document.body.style.paddingBottom = '';
      }
    }
    checkSize();
    window.addEventListener('resize', checkSize);

    // Mark Me tab as active
    var me = document.getElementById('dbnMe');
    if (me) me.classList.add('dbn-active');

    // Wire alerts bell
    var alertBtn = document.getElementById('dbnAlerts');
    if (alertBtn) {
      alertBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (window.toggleNotifDropdown) window.toggleNotifDropdown(e);
      });
    }
  })();
</script>
</body>
</html>

