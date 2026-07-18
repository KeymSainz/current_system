<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- PWA -->
  <meta name="theme-color" content="#e6a800">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Fix&amp;Go">
  <link rel="manifest" href="/manifest.json">
  <link rel="apple-touch-icon" href="/assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="/assets/css/mobile.css">
  <title>Fix&amp;Go — Settings</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body{background:var(--fg-bg);}
    .cu-layout{display:flex;min-height:calc(100vh - 68px);}
    .cu-sidebar{width:260px;flex-shrink:0;background:var(--fg-card-bg);border-right:1px solid var(--fg-border);padding:1.5rem 0 2rem;position:sticky;top:68px;height:calc(100vh - 68px);overflow-y:auto;}
    .sidebar-profile{display:flex;align-items:center;gap:0.85rem;padding:0 1.25rem 1.25rem;border-bottom:1px solid var(--fg-border);margin-bottom:0.75rem;}
    .sidebar-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,rgba(230,168,0,0.25),rgba(230,168,0,0.08));border:2px solid rgba(230,168,0,0.35);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--fg-primary);font-weight:800;flex-shrink:0;overflow:hidden;}
    .sidebar-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;}
    .sidebar-profile-name{font-size:0.9rem;font-weight:700;color:var(--fg-text);}
    .sidebar-profile-edit{font-size:0.75rem;color:var(--fg-primary);text-decoration:none;font-weight:600;}
    .sidebar-section-label{font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);padding:0.75rem 1.25rem 0.35rem;}
    .sidebar-nav{list-style:none;padding:0;margin:0;}
    .sidebar-nav li a{display:flex;align-items:center;gap:0.75rem;padding:0.6rem 1.25rem;color:var(--fg-muted);text-decoration:none;font-size:0.88rem;font-weight:500;border-left:3px solid transparent;transition:all 0.2s;}
    .sidebar-nav li a:hover{color:var(--fg-primary);background:rgba(230,168,0,0.07);border-left-color:var(--fg-primary);}
    .sidebar-nav li a.active{color:var(--fg-primary);background:rgba(230,168,0,0.1);border-left-color:var(--fg-primary);font-weight:700;}
    .sidebar-nav li a i{font-size:1rem;width:20px;text-align:center;}
    .cu-main{flex:1;padding:2rem;min-width:0;}
    .page-header{margin-bottom:1.75rem;}
    .page-header h2{font-size:1.5rem;font-weight:800;color:var(--fg-text);margin:0 0 0.25rem;}
    .page-header p{color:var(--fg-muted);margin:0;font-size:0.88rem;}
    .section-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;margin-bottom:1.5rem;}
    .section-head{padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;}
    .section-head h6{margin:0;font-weight:700;font-size:0.95rem;color:var(--fg-text);}
    .section-body{padding:1.25rem;}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    /* Toggle switch */
    .toggle-row{display:flex;align-items:center;justify-content:space-between;padding:0.65rem 0;border-bottom:1px solid var(--fg-border);}
    .toggle-row:last-child{border-bottom:none;}
    .toggle-label{font-size:0.88rem;color:var(--fg-text);font-weight:500;}
    .toggle-label small{display:block;font-size:0.75rem;color:var(--fg-muted);font-weight:400;margin-top:0.1rem;}
    .fg-switch{position:relative;display:inline-block;width:44px;height:24px;}
    .fg-switch input{opacity:0;width:0;height:0;}
    .fg-switch .slider{position:absolute;cursor:pointer;inset:0;background:var(--fg-border);border-radius:24px;transition:0.3s;}
    .fg-switch .slider:before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:0.3s;}
    .fg-switch input:checked + .slider{background:var(--fg-primary);}
    .fg-switch input:checked + .slider:before{transform:translateX(20px);}
    /* Danger zone */
    .danger-card{background:rgba(220,53,69,0.04);border:1px solid rgba(220,53,69,0.25);border-radius:14px;overflow:hidden;margin-bottom:1.5rem;}
    .danger-head{padding:1rem 1.25rem;border-bottom:1px solid rgba(220,53,69,0.2);display:flex;align-items:center;gap:0.5rem;}
    .danger-head h6{margin:0;font-weight:700;font-size:0.95rem;color:#dc3545;}
    .danger-body{padding:1.25rem;}
    /* Form */
    .fg-label{font-size:0.82rem;font-weight:600;color:var(--fg-muted);margin-bottom:0.35rem;display:block;}
    .fg-input{width:100%;padding:0.55rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:9px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;}
    .fg-input:focus{border-color:var(--fg-primary);}
    .btn-save{background:var(--fg-primary);color:#fff;border:none;border-radius:9px;padding:0.55rem 1.5rem;font-size:0.88rem;font-weight:700;cursor:pointer;transition:opacity 0.2s;}
    .btn-save:hover{opacity:0.88;}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .cu-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .cu-sidebar.open{transform:translateX(0);}
      .cu-main{padding:1.25rem;}
    }
    /* Mobile navbar clean-up */
    @media (max-width: 991px) {
      .set-desktop-hide { display: none !important; }
      #settingsMobileMenu { display: flex !important; }
      .fg-navbar { flex-wrap: nowrap !important; }
      .fg-navbar > div:last-child { flex-wrap: nowrap !important; gap: 0.4rem !important; }
      #sidebarToggle { display: none !important; }
      .cu-main { padding-bottom: 75px !important; }
    }
    @media (min-width: 992px) {
      #settingsMobileMenu { display: none !important; }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-2">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:42px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.1rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="role-badge customer set-desktop-hide-not">👤 Customer</span>
      <span id="navUserName" class="set-desktop-hide" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="/index.php?browse=1" class="btn btn-sm set-desktop-hide"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;">
        <i class="bi bi-shop"></i> Browse Shop
      </a>
      <a href="messages.php" class="set-desktop-hide" style="position:relative;text-decoration:none;" title="Messages">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);transition:all 0.2s;">
          <i class="bi bi-chat-dots-fill"></i>
        </div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:var(--fg-primary);color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <!-- Logout — visible on desktop; in drawer on mobile -->
      <button onclick="customerLogout()" class="btn btn-sm set-desktop-hide"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
      <!-- Mobile hamburger -->
      <button id="settingsMobileMenu" onclick="toggleSettingsDrawer()" aria-label="Menu"
        style="display:none;background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:8px;width:36px;height:36px;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="cu-layout">

    <!-- Sidebar -->
    <aside class="cu-sidebar" id="cuSidebar">
      <div class="sidebar-profile">
        <div class="sidebar-avatar" id="sidebarAvatarInitials">?</div>
        <div>
          <div class="sidebar-profile-name" id="sidebarName">Loading…</div>
          <a href="profile.php" class="sidebar-profile-edit"><i class="bi bi-pencil-fill" style="font-size:0.65rem;"></i> Edit Profile</a>
        </div>
      </div>
      <div class="sidebar-section-label">My Account</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
        <li><a href="notifications.php"><i class="bi bi-bell-fill"></i> Notifications</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
        <li><a href="settings.php" class="active"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
      <div class="sidebar-section-label">Shopping</div>
      <ul class="sidebar-nav">
        <li><a href="orders.php"><i class="bi bi-bag-fill"></i> My Orders</a></li>
        <li><a href="repairs.php"><i class="bi bi-tools"></i> My Repairs</a></li>
        <li><a href="wishlist.php"><i class="bi bi-heart-fill"></i> Wishlist</a></li>
        <li><a href="vouchers.php"><i class="bi bi-ticket-perforated-fill"></i> My Vouchers</a></li>
      </ul>
      <div class="sidebar-section-label">Fix&amp;Go</div>
      <ul class="sidebar-nav">
        <li><a href="/index.php?browse=1"><i class="bi bi-shop-window"></i> Browse Shop</a></li>
        <li><a href="seller-centre.php"><i class="bi bi-shop-window"></i> Seller Centre</a></li>
        <li><a href="become-technician.php"><i class="bi bi-wrench-adjustable-circle-fill"></i> Become a Technician</a></li>
      </ul>
    </aside>

    <!-- Main content -->
    <main class="cu-main">

      <div class="page-header">
        <h2><i class="bi bi-gear-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Settings</h2>
        <p>Manage your account preferences</p>
      </div>

      <!-- Account Security -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-shield-lock-fill" style="color:var(--fg-primary);margin-right:0.4rem;"></i>Account Security</h6>
        </div>
        <div class="section-body">
          <form id="passwordForm" onsubmit="handlePasswordChange(event)">
            <div style="display:grid;gap:1rem;max-width:480px;">
              <div>
                <label class="fg-label" for="currentPassword">Current Password</label>
                <input type="password" id="currentPassword" class="fg-input" placeholder="Enter current password" autocomplete="current-password" />
              </div>
              <div>
                <label class="fg-label" for="newPassword">New Password</label>
                <input type="password" id="newPassword" class="fg-input" placeholder="Enter new password" autocomplete="new-password" />
              </div>
              <div>
                <label class="fg-label" for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" class="fg-input" placeholder="Confirm new password" autocomplete="new-password" />
              </div>
              <div id="passwordMsg" style="font-size:0.82rem;display:none;"></div>
              <div>
                <button type="submit" class="btn-save"><i class="bi bi-check-lg" style="margin-right:0.35rem;"></i>Save Password</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Notification Preferences -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-bell-fill" style="color:#3b82f6;margin-right:0.4rem;"></i>Notification Preferences</h6>
        </div>
        <div class="section-body" style="padding-top:0.5rem;padding-bottom:0.5rem;">
          <div class="toggle-row">
            <div class="toggle-label">Order Updates<small>Get notified when your order status changes</small></div>
            <label class="fg-switch"><input type="checkbox" id="notifOrders" checked /><span class="slider"></span></label>
          </div>
          <div class="toggle-row">
            <div class="toggle-label">Repair Status<small>Updates on your repair bookings</small></div>
            <label class="fg-switch"><input type="checkbox" id="notifRepairs" checked /><span class="slider"></span></label>
          </div>
          <div class="toggle-row">
            <div class="toggle-label">Promotions<small>Deals, vouchers, and special offers</small></div>
            <label class="fg-switch"><input type="checkbox" id="notifPromos" /><span class="slider"></span></label>
          </div>
          <div class="toggle-row">
            <div class="toggle-label">Messages<small>New messages from shops and technicians</small></div>
            <label class="fg-switch"><input type="checkbox" id="notifMessages" checked /><span class="slider"></span></label>
          </div>
        </div>
      </div>

      <!-- Privacy -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-eye-fill" style="color:#6f42c1;margin-right:0.4rem;"></i>Privacy</h6>
        </div>
        <div class="section-body" style="padding-top:0.5rem;padding-bottom:0.5rem;">
          <div class="toggle-row">
            <div class="toggle-label">Show profile to technicians<small>Technicians can see your name and contact info</small></div>
            <label class="fg-switch"><input type="checkbox" id="privacyProfile" checked /><span class="slider"></span></label>
          </div>
          <div class="toggle-row">
            <div class="toggle-label">Allow location access<small>Used to find nearby shops and technicians</small></div>
            <label class="fg-switch"><input type="checkbox" id="privacyLocation" /><span class="slider"></span></label>
          </div>
        </div>
      </div>

      <!-- Danger Zone -->
      <div class="danger-card">
        <div class="danger-head">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <h6>Danger Zone</h6>
        </div>
        <div class="danger-body">
          <p style="font-size:0.88rem;color:var(--fg-muted);margin-bottom:1rem;">Permanently delete your account and all associated data. This action cannot be undone.</p>
          <button
            class="btn btn-sm"
            style="background:rgba(220,53,69,0.08);border:1.5px solid rgba(220,53,69,0.3);color:#dc3545;border-radius:9px;font-weight:700;font-size:0.85rem;padding:0.5rem 1.25rem;cursor:not-allowed;opacity:0.7;"
            disabled
            title="Contact support to delete your account"
            data-bs-toggle="tooltip"
            data-bs-placement="right"
            data-bs-title="Contact support to delete your account">
            <i class="bi bi-trash3-fill" style="margin-right:0.35rem;"></i>Delete Account
          </button>
          <p style="font-size:0.75rem;color:var(--fg-muted);margin-top:0.6rem;margin-bottom:0;">
            To delete your account, please <a href="messages.php" style="color:#dc3545;font-weight:600;">contact support</a>.
          </p>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="/assets/js/session-timeout.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'customer') { window.location.href = '/login.html'; return; }
    const fullName = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email;
    document.getElementById('sidebarName').textContent = fullName || user.email;
    const initials = ((user.firstName||'')[0]||'') + ((user.lastName||'')[0]||'');
    (function renderAvatar(url) {
      var el = document.getElementById('sidebarAvatarInitials');
      if (!el) return;
      if (url) { el.innerHTML = '<img src="' + url + '" alt="avatar" onerror="this.parentElement.textContent=\'' + initials.toUpperCase() + '\'">' ; }
      else { el.textContent = initials.toUpperCase() || '?'; }
    })(user.avatar_url || null);
    fetch('../../../api/session/user', { credentials: 'include' })
      .then(function(r){return r.json();}).then(function(d){
        if (d.loggedIn && d.user) {
          FGAuth.UserStore.save(d.user);
          var el = document.getElementById('sidebarAvatarInitials');
          if (el && d.user.avatar_url) el.innerHTML = '<img src="' + d.user.avatar_url + '" alt="avatar" onerror="this.parentElement.textContent=\'' + initials.toUpperCase() + '\'">';
        }
      }).catch(function(){});
    const sidebar = document.getElementById('cuSidebar'), overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });
    loadUnreadMessageCount();
    // Init Bootstrap tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
  });

  function handlePasswordChange(e) {
    e.preventDefault();
    const cur = document.getElementById('currentPassword').value;
    const nw  = document.getElementById('newPassword').value;
    const cf  = document.getElementById('confirmPassword').value;
    const msg = document.getElementById('passwordMsg');
    msg.style.display = 'block';
    if (!cur || !nw || !cf) {
      msg.style.color = '#dc3545'; msg.textContent = 'Please fill in all fields.'; return;
    }
    if (nw !== cf) {
      msg.style.color = '#dc3545'; msg.textContent = 'New passwords do not match.'; return;
    }
    if (nw.length < 8) {
      msg.style.color = '#dc3545'; msg.textContent = 'Password must be at least 8 characters.'; return;
    }
    msg.style.color = '#28A745'; msg.textContent = 'Password updated successfully!';
    document.getElementById('passwordForm').reset();
    setTimeout(() => { msg.style.display = 'none'; }, 3000);
  }

  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('/api/logout').finally(() => {
        window.location.href = '/login.html';
      });
    });
  }

  function loadUnreadMessageCount() {
    fetch('/api/messages?action=unread_count', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success && d.count > 0) {
          const badge = document.getElementById('navMsgBadge');
          if (badge) { badge.textContent = d.count > 99 ? '99+' : d.count; badge.style.display = 'inline-block'; }
        }
      }).catch(() => {});
    setTimeout(loadUnreadMessageCount, 10000);
  }
  </script>

<!-- ══ MOBILE DRAWER (Settings) ══════════════════════════════ -->
<div id="settingsDrawerOverlay" onclick="toggleSettingsDrawer()"
  style="display:none;position:fixed;inset:0;z-index:1099;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);"></div>
<div id="settingsDrawer"
  style="position:fixed;top:0;right:0;z-index:1100;height:100%;width:75vw;max-width:300px;
         background:var(--fg-card-bg);border-left:1px solid var(--fg-border);
         display:flex;flex-direction:column;transform:translateX(100%);
         transition:transform 0.3s cubic-bezier(0.4,0,0.2,1);
         box-shadow:-8px 0 32px rgba(0,0,0,0.4);">
  <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);">
    <span style="font-size:0.95rem;font-weight:800;color:var(--fg-text);">Fix<span style="color:var(--fg-primary);">&amp;Go</span></span>
    <button onclick="toggleSettingsDrawer()" style="background:none;border:1px solid var(--fg-border);color:var(--fg-text);width:30px;height:30px;border-radius:8px;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
  </div>
  <nav style="flex:1;overflow-y:auto;padding:0.5rem 0;">
    <a href="dashboard.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-house-fill" style="width:18px;color:var(--fg-primary);"></i> Dashboard</a>
    <a href="orders.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-bag-fill" style="width:18px;color:var(--fg-primary);"></i> My Purchases</a>
    <a href="repairs.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-tools" style="width:18px;color:var(--fg-primary);"></i> My Repairs</a>
    <a href="messages.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-chat-dots-fill" style="width:18px;color:var(--fg-primary);"></i> Messages</a>
    <a href="notifications.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-bell-fill" style="width:18px;color:var(--fg-primary);"></i> Notifications</a>
    <a href="settings.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-primary);text-decoration:none;font-weight:700;font-size:0.9rem;border-left:3px solid var(--fg-primary);background:rgba(230,168,0,0.07);"><i class="bi bi-gear-fill" style="width:18px;color:var(--fg-primary);"></i> Settings</a>
  </nav>
  <div style="padding:1rem 1.25rem;border-top:1px solid var(--fg-border);">
    <button onclick="customerLogout()" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;width:100%;padding:0.7rem;border-radius:10px;background:rgba(220,53,69,0.08);border:1.5px solid rgba(220,53,69,0.3);color:#dc3545;font-weight:700;font-size:0.88rem;cursor:pointer;">
      <i class="bi bi-box-arrow-right"></i> Logout
    </button>
  </div>
</div>

<!-- ══ MOBILE BOTTOM NAV (Settings) ══════════════════════════ -->
<nav id="settingsBottomNav" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;background:var(--fg-card-bg);border-top:1px solid var(--fg-border);padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));box-shadow:0 -4px 20px rgba(0,0,0,0.15);">
  <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
    <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-house-fill" style="font-size:1.25rem;"></i>Home</a></li>
    <li><a href="/index.php#shop" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-shop" style="font-size:1.25rem;"></i>Shop</a></li>
    <li><a href="/index.php#technicians" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-workspace" style="font-size:1.25rem;"></i>Technicians</a></li>
    <li><a href="notifications.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-bell-fill" style="font-size:1.25rem;"></i>Inbox</a></li>
    <li><a href="settings.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-primary);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-gear-fill" style="font-size:1.25rem;"></i>Settings</a></li>
  </ul>
</nav>

<script>
  (function() {
    function checkMob() {
      var isMob = window.innerWidth <= 991;
      var bn = document.getElementById('settingsBottomNav');
      if (bn) bn.style.display = isMob ? 'block' : 'none';
    }
    checkMob(); window.addEventListener('resize', checkMob);
  })();
  function toggleSettingsDrawer() {
    var d = document.getElementById('settingsDrawer');
    var o = document.getElementById('settingsDrawerOverlay');
    var open = d.style.transform === 'translateX(0%)';
    d.style.transform = open ? 'translateX(100%)' : 'translateX(0%)';
    o.style.display = open ? 'none' : 'block';
    document.body.style.overflow = open ? '' : 'hidden';
  }
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') { var d=document.getElementById('settingsDrawer'); if(d && d.style.transform==='translateX(0%)') toggleSettingsDrawer(); } });
</script>

</body>
</html>




