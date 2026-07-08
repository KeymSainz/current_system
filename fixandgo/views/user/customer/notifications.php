<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- PWA -->
  <meta name="theme-color" content="#e6a800">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Fix&amp;Go">
  <link rel="manifest" href="../../../manifest.json">
  <link rel="apple-touch-icon" href="../../../assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="../../../assets/css/mobile.css">
  <title>Fix&amp;Go — Notifications</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body{background:var(--fg-bg);}
    .cu-layout{display:flex;min-height:calc(100vh - 68px);}
    .cu-sidebar{width:260px;flex-shrink:0;background:var(--fg-card-bg);border-right:1px solid var(--fg-border);padding:1.5rem 0 2rem;position:sticky;top:68px;height:calc(100vh - 68px);overflow-y:auto;}
    /* Hide sidebar on mobile — bottom nav handles navigation */
    @media(max-width:991px){ .cu-sidebar { display:none !important; } }
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
    @keyframes spin { to { transform: rotate(360deg); } }
    .page-header{margin-bottom:1.75rem;}
    .page-header h2{font-size:1.5rem;font-weight:800;color:var(--fg-text);margin:0 0 0.25rem;}
    .page-header p{color:var(--fg-muted);margin:0;font-size:0.88rem;}
    .notif-list{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;}
    .notif-item{display:flex;align-items:flex-start;gap:1rem;padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);transition:background 0.15s;}
    .notif-item:last-child{border-bottom:none;}
    .notif-item:hover{background:rgba(230,168,0,0.03);}
    .notif-item.unread{background:rgba(59,130,246,0.04);}
    .notif-icon{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
    .notif-body{flex:1;}
    .notif-title{font-size:0.88rem;font-weight:700;color:var(--fg-text);margin-bottom:0.2rem;}
    .notif-desc{font-size:0.8rem;color:var(--fg-muted);}
    .notif-time{font-size:0.72rem;color:var(--fg-muted);white-space:nowrap;margin-top:0.2rem;}
    .notif-dot{width:8px;height:8px;border-radius:50%;background:#3b82f6;flex-shrink:0;margin-top:0.4rem;}
    .empty-state{text-align:center;padding:4rem 2rem;color:var(--fg-muted);}
    .empty-state i{font-size:3rem;display:block;margin-bottom:1rem;opacity:0.3;}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @media(max-width:768px){.sidebar-toggle{display:flex;align-items:center;}.cu-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}.cu-sidebar.open{transform:translateX(0);}.cu-main{padding:1.25rem;}}
  </style>
</head>
<body>
  <!-- Clean mobile-first navbar -->
  <nav class="fg-navbar" role="navigation" style="flex-wrap:nowrap !important;">
    <div class="d-flex align-items-center gap-2">
      <a href="dashboard.php" style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);text-decoration:none;flex-shrink:0;">
        <i class="bi bi-arrow-left"></i>
      </a>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:38px;width:auto;object-fit:contain;" onerror="this.outerHTML='<span style=\'font-size:1rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-2" style="flex-wrap:nowrap;">
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="messages.php" style="position:relative;text-decoration:none;" title="Messages">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-size:0.95rem;color:var(--fg-text);">
          <i class="bi bi-chat-dots-fill"></i>
        </div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:var(--fg-primary);color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <span id="navUserName" style="font-size:0.82rem;font-weight:600;color:var(--fg-text);display:none;" class="notif-desk-only"></span>
      <button onclick="customerLogout()" class="btn btn-sm notif-desk-only"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;display:none;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
    </div>
  </nav>
  <style>
    @media (min-width: 768px) {
      .notif-desk-only { display: flex !important; }
    }
    body { padding-bottom: 75px; }
  </style>
  <div class="sidebar-overlay" id="sidebarOverlay" style="display:none;"></div>

  <div class="cu-layout">
    <aside class="cu-sidebar" id="cuSidebar">
      <div class="sidebar-profile">
        <div class="sidebar-avatar" id="sidebarAvatarInitials">?</div>
        <div><div class="sidebar-profile-name" id="sidebarName">Loading…</div><a href="profile.php" class="sidebar-profile-edit"><i class="bi bi-pencil-fill" style="font-size:0.65rem;"></i> Edit Profile</a></div>
      </div>
      <div class="sidebar-section-label">My Account</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
        <li><a href="notifications.php" class="active"><i class="bi bi-bell-fill"></i> Notifications</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
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
        <li><a href="../../../index.php?browse=1"><i class="bi bi-shop-window"></i> Browse Shop</a></li>
        <li><a href="seller-centre.php"><i class="bi bi-shop-window"></i> Seller Centre</a></li>
        <li><a href="become-technician.php"><i class="bi bi-wrench-adjustable-circle-fill"></i> Become a Technician</a></li>
      </ul>
    </aside>
    <main class="cu-main">
      <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;">
        <div>
          <h2><i class="bi bi-bell-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Notifications</h2>
          <p>Stay updated with your orders, repairs, and messages</p>
        </div>
        <button onclick="markAllReadFull()" style="padding:0.4rem 1rem;border-radius:8px;background:transparent;border:1.5px solid var(--fg-border);color:var(--fg-primary);font-size:0.8rem;font-weight:700;cursor:pointer;">Mark all read</button>
      </div>
      <div id="notifListFull" style="display:flex;flex-direction:column;gap:0.5rem;">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
          <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading notifications…
        </div>
      </div>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script src="../../assets/js/session-timeout.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'customer') { window.location.href = '../../../login.html'; return; }
    const fullName = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
    var navUN = document.getElementById('navUserName');
    if (navUN) navUN.textContent = fullName || user.email;
    const initials = ((user.firstName||'')[0]||'') + ((user.lastName||'')[0]||'');
    loadUnreadMessageCount();
    loadNotifications();
  });

  function timeAgo(dateStr) {
    var d = new Date(dateStr), now = new Date();
    var diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff/60) + 'm ago';
    if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
    if (diff < 604800) return Math.floor(diff/86400) + 'd ago';
    return d.toLocaleDateString('en-PH', {month:'short',day:'numeric'});
  }

  function iconFor(type) {
    var map = {
      order: 'bi bi-bag-fill',
      repair: 'bi bi-tools',
      message: 'bi bi-chat-dots-fill',
      payment: 'bi bi-credit-card-fill',
      system: 'bi bi-info-circle-fill'
    };
    return map[type] || 'bi bi-bell-fill';
  }
  function colorFor(type) {
    var map = {
      order: 'rgba(59,130,246,0.15)',
      repair: 'rgba(230,168,0,0.15)',
      message: 'rgba(139,92,246,0.15)',
      payment: 'rgba(40,167,69,0.15)',
      system: 'rgba(107,114,128,0.15)'
    };
    return map[type] || 'rgba(230,168,0,0.12)';
  }
  function textColorFor(type) {
    var map = {
      order: '#3b82f6', repair: '#c98f00', message: '#8b5cf6',
      payment: '#28A745', system: '#6b7280'
    };
    return map[type] || 'var(--fg-primary)';
  }

  function loadNotifications() {
    var el = document.getElementById('notifListFull');
    fetch('../../../backend/notifications.php?action=list&limit=50', { credentials: 'include' })
      .then(function(r){ return r.json(); })
      .then(function(data) {
        if (!data.success || !data.notifications || !data.notifications.length) {
          el.innerHTML = '<div style="text-align:center;padding:3rem 1rem;color:var(--fg-muted);"><i class="bi bi-bell" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.3;"></i>No notifications yet.</div>';
          return;
        }
        el.innerHTML = data.notifications.map(function(n) {
          var unread = !n.is_read ? 'border-left:3px solid var(--fg-primary);' : '';
          return '<div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:12px;padding:0.9rem 1rem;display:flex;align-items:flex-start;gap:0.85rem;cursor:pointer;' + unread + '" onclick="markOneRead(' + n.id + ', this)">'
            + '<div style="width:40px;height:40px;border-radius:50%;background:' + colorFor(n.type) + ';display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;color:' + textColorFor(n.type) + ';">'
            + '<i class="' + iconFor(n.type) + '"></i></div>'
            + '<div style="flex:1;min-width:0;">'
            + '<div style="font-size:0.88rem;font-weight:' + (n.is_read ? '500' : '800') + ';color:var(--fg-text);margin-bottom:0.2rem;">' + (n.title || '') + '</div>'
            + '<div style="font-size:0.8rem;color:var(--fg-muted);line-height:1.5;">' + (n.message || '') + '</div>'
            + '<div style="font-size:0.72rem;color:var(--fg-muted);margin-top:0.3rem;">' + timeAgo(n.created_at) + '</div>'
            + '</div>'
            + (!n.is_read ? '<div style="width:8px;height:8px;border-radius:50%;background:var(--fg-primary);flex-shrink:0;margin-top:0.35rem;"></div>' : '')
            + '</div>';
        }).join('');
      })
      .catch(function() {
        el.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--fg-muted);">Could not load notifications.</div>';
      });
  }

  function markOneRead(id, el) {
    fetch('../../../backend/notifications.php?action=mark_read', {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ids: [id] })
    }).then(function() {
      el.style.borderLeft = '';
      var dot = el.querySelector('div[style*="background:var(--fg-primary)"]');
      if (dot) dot.remove();
      var title = el.querySelector('div[style*="font-weight:800"]');
      if (title) title.style.fontWeight = '500';
    }).catch(function(){});
  }

  function markAllReadFull() {
    fetch('../../../backend/notifications.php?action=mark_all_read', {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({})
    }).then(function() { loadNotifications(); }).catch(function(){});
  }

  function loadUnreadMessageCount() {
    fetch('../../../backend/messages.php?action=unread_count', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success && d.count > 0) {
          const badge = document.getElementById('navMsgBadge');
          if (badge) { badge.textContent = d.count > 99 ? '99+' : d.count; badge.style.display = 'inline-block'; }
        }
      }).catch(() => {});
    setTimeout(loadUnreadMessageCount, 10000);
  }

  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('../../../backend/logout.php').finally(() => {
        window.location.href = '../../../login.html';
      });
    });
  }
  </script>

  <!-- ── Mobile Bottom Nav ── -->
  <nav style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;background:var(--fg-card-bg);border-top:1px solid var(--fg-border);padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));box-shadow:0 -4px 20px rgba(0,0,0,0.15);" id="notifPageBottomNav">
    <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-house-fill" style="font-size:1.25rem;"></i>Home</a></li>
      <li><a href="../../../index.php#shop" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-shop" style="font-size:1.25rem;"></i>Shop</a></li>
      <li><a href="../../../index.php#technicians" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-workspace" style="font-size:1.25rem;"></i>Technicians</a></li>
      <li><a href="notifications.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-primary);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-bell-fill" style="font-size:1.25rem;"></i>Inbox</a></li>
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-fill" style="font-size:1.25rem;"></i>Me</a></li>
    </ul>
  </nav>
  <script>
    (function(){ var nb=document.getElementById('notifPageBottomNav'); function c(){ nb.style.display=window.innerWidth<=991?'block':'none'; } c(); window.addEventListener('resize',c); })();
  </script>

</body>
</html>




