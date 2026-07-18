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
  <title>Fix&amp;Go — My Vouchers</title>
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
    .empty-state{text-align:center;padding:4rem 2rem;color:var(--fg-muted);}
    .empty-state i{font-size:3rem;display:block;margin-bottom:1rem;opacity:0.3;}
    .empty-state p{font-size:0.9rem;margin:0 0 1rem;}
    /* Tabs */
    .voucher-tabs{display:flex;gap:0;margin-bottom:1.25rem;border:1.5px solid var(--fg-border);border-radius:10px;overflow:hidden;width:fit-content;}
    .voucher-tab{padding:0.5rem 1.25rem;background:var(--fg-card-bg);color:var(--fg-muted);font-size:0.85rem;font-weight:600;cursor:pointer;border:none;transition:all 0.2s;}
    .voucher-tab.active{background:var(--fg-primary);color:#fff;}
    .voucher-tab:not(:last-child){border-right:1.5px solid var(--fg-border);}
    /* Voucher cards grid */
    .voucher-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.1rem;padding:1.25rem;}
    .voucher-card{border:2px dashed var(--fg-border);border-radius:12px;padding:1.1rem 1.25rem;background:var(--fg-card-bg);position:relative;transition:border-color 0.2s,box-shadow 0.2s;}
    .voucher-card:hover{border-color:var(--fg-primary);box-shadow:0 6px 20px rgba(230,168,0,0.1);}
    .voucher-card.used{opacity:0.55;filter:grayscale(0.4);}
    .voucher-discount{font-size:1.6rem;font-weight:800;color:#28A745;line-height:1;margin-bottom:0.2rem;}
    .voucher-desc{font-size:0.82rem;color:var(--fg-muted);margin-bottom:0.6rem;}
    .voucher-code-row{display:flex;align-items:center;gap:0.5rem;background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:8px;padding:0.4rem 0.75rem;margin-bottom:0.75rem;}
    .voucher-code{font-family:monospace;font-size:0.9rem;font-weight:700;color:var(--fg-text);flex:1;letter-spacing:1px;}
    .btn-copy{background:none;border:none;color:var(--fg-primary);font-size:0.8rem;font-weight:700;cursor:pointer;padding:0;white-space:nowrap;transition:opacity 0.2s;}
    .btn-copy:hover{opacity:0.75;}
    .voucher-expiry{font-size:0.75rem;color:var(--fg-muted);}
    .voucher-expiry span{font-weight:700;color:var(--fg-text);}
    .voucher-badge{position:absolute;top:0.75rem;right:0.75rem;font-size:0.65rem;font-weight:700;text-transform:uppercase;padding:0.15rem 0.5rem;border-radius:20px;}
    .voucher-badge.available{background:rgba(40,167,69,0.12);color:#28A745;}
    .voucher-badge.used{background:rgba(108,117,125,0.12);color:#6c757d;}
    .voucher-badge.expired{background:rgba(220,53,69,0.12);color:#dc3545;}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .cu-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .cu-sidebar.open{transform:translateX(0);}
      .cu-main{padding:1.25rem;}
      .voucher-grid{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge customer">👤 Customer</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="/index.php?browse=1" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;">
        <i class="bi bi-shop"></i> Browse Shop
      </a>
      <a href="messages.php" style="position:relative;text-decoration:none;" title="Messages">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);transition:all 0.2s;" onmouseenter="this.style.borderColor='var(--fg-primary)';this.style.color='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-text)'">
          <i class="bi bi-chat-dots-fill"></i>
        </div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:var(--fg-primary);color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <button onclick="customerLogout()" class="btn btn-sm"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
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
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
      <div class="sidebar-section-label">Shopping</div>
      <ul class="sidebar-nav">
        <li><a href="orders.php"><i class="bi bi-bag-fill"></i> My Orders</a></li>
        <li><a href="repairs.php"><i class="bi bi-tools"></i> My Repairs</a></li>
        <li><a href="wishlist.php"><i class="bi bi-heart-fill"></i> Wishlist</a></li>
        <li><a href="vouchers.php" class="active"><i class="bi bi-ticket-perforated-fill"></i> My Vouchers</a></li>
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
        <h2><i class="bi bi-ticket-perforated-fill" style="color:#28A745;margin-right:0.5rem;"></i>My Vouchers</h2>
        <p>Your discount codes and promotions</p>
      </div>

      <!-- Tabs -->
      <div class="voucher-tabs" id="voucherTabs">
        <button class="voucher-tab active" data-tab="available">Available</button>
        <button class="voucher-tab" data-tab="used">Used / Expired</button>
      </div>

      <div class="section-card">
        <div class="section-head">
          <h6 id="voucherTabTitle"><i class="bi bi-ticket-perforated-fill" style="color:#28A745;margin-right:0.4rem;"></i>Available Vouchers</h6>
          <span id="voucherCount" style="font-size:0.8rem;color:var(--fg-muted);font-weight:600;">0 vouchers</span>
        </div>
        <div id="voucherContent"></div>
      </div>

    </main>
  </div>

  <!-- Copy toast -->
  <div id="copyToast" style="position:fixed;bottom:1.5rem;right:1.5rem;background:#28A745;color:#fff;padding:0.6rem 1.1rem;border-radius:9px;font-size:0.85rem;font-weight:700;display:none;z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,0.15);">
    <i class="bi bi-check-lg" style="margin-right:0.35rem;"></i>Code copied!
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="/assets/js/session-timeout.js"></script>
  <script>
  // Sample vouchers — replace with real API call when backend is ready
  const vouchersData = [
    // { code: 'WELCOME10', discount: '10% OFF', description: 'Welcome discount on your first order', expiry: '2025-12-31', status: 'available' },
  ];

  let activeTab = 'available';

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

    // Tab switching
    document.querySelectorAll('.voucher-tab').forEach(tab => {
      tab.addEventListener('click', function() {
        document.querySelectorAll('.voucher-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        activeTab = this.dataset.tab;
        renderVouchers(activeTab);
      });
    });

    renderVouchers('available');
  });

  function renderVouchers(tab) {
    const content = document.getElementById('voucherContent');
    const title = document.getElementById('voucherTabTitle');
    const isAvailable = tab === 'available';

    title.innerHTML = isAvailable
      ? '<i class="bi bi-ticket-perforated-fill" style="color:#28A745;margin-right:0.4rem;"></i>Available Vouchers'
      : '<i class="bi bi-ticket-perforated" style="color:var(--fg-muted);margin-right:0.4rem;"></i>Used / Expired Vouchers';

    const filtered = vouchersData.filter(v => isAvailable ? v.status === 'available' : v.status !== 'available');
    document.getElementById('voucherCount').textContent = filtered.length + ' voucher' + (filtered.length !== 1 ? 's' : '');

    if (filtered.length === 0) {
      content.innerHTML = `<div class="empty-state">
        <i class="bi bi-ticket-perforated"></i>
        <p>${isAvailable ? 'No vouchers available' : 'No used or expired vouchers'}</p>
        ${isAvailable ? `<a href="/index.php?browse=1" style="display:inline-flex;align-items:center;gap:0.4rem;color:var(--fg-primary);font-weight:700;text-decoration:none;font-size:0.88rem;">
          <i class="bi bi-shop"></i> Browse promotions
        </a>` : ''}
      </div>`;
      return;
    }

    const cards = filtered.map(v => `
      <div class="voucher-card ${v.status !== 'available' ? 'used' : ''}">
        <span class="voucher-badge ${v.status}">${v.status}</span>
        <div class="voucher-discount">${v.discount}</div>
        <div class="voucher-desc">${v.description}</div>
        <div class="voucher-code-row">
          <span class="voucher-code">${v.code}</span>
          ${v.status === 'available' ? `<button class="btn-copy" onclick="copyCode('${v.code}')"><i class="bi bi-clipboard"></i> Copy</button>` : ''}
        </div>
        <div class="voucher-expiry">Expires: <span>${v.expiry}</span></div>
      </div>`).join('');

    content.innerHTML = `<div class="voucher-grid">${cards}</div>`;
  }

  function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
      const toast = document.getElementById('copyToast');
      toast.style.display = 'block';
      setTimeout(() => { toast.style.display = 'none'; }, 2000);
    }).catch(() => {
      // Fallback for older browsers
      const el = document.createElement('textarea');
      el.value = code; document.body.appendChild(el);
      el.select(); document.execCommand('copy');
      document.body.removeChild(el);
      const toast = document.getElementById('copyToast');
      toast.style.display = 'block';
      setTimeout(() => { toast.style.display = 'none'; }, 2000);
    });
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

</body>
</html>




