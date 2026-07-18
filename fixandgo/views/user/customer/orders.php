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
  <link rel="manifest" href="../../../manifest.json">
  <link rel="apple-touch-icon" href="../../../assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="../../../assets/css/mobile.css">
  <title>Fix&amp;Go — My Purchases</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5" />
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
    .page-header{margin-bottom:1.5rem;}
    .page-header h2{font-size:1.5rem;font-weight:800;color:var(--fg-text);margin:0 0 0.25rem;}
    .page-header p{color:var(--fg-muted);margin:0;font-size:0.88rem;}
    /* ── Shopee-style tabs ── */
    .purchase-tabs-wrap{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px 14px 0 0;overflow:hidden;}
    .purchase-tabs{display:flex;border-bottom:1px solid var(--fg-border);overflow-x:auto;scrollbar-width:none;}
    .purchase-tabs::-webkit-scrollbar{display:none;}
    .purchase-tab{padding:0.9rem 1.25rem;font-size:0.85rem;font-weight:600;color:var(--fg-muted);cursor:pointer;border-bottom:2.5px solid transparent;white-space:nowrap;text-decoration:none;transition:all 0.2s;background:none;border-top:none;border-left:none;border-right:none;outline:none;}
    .purchase-tab:hover{color:var(--fg-text);}
    .purchase-tab.active{color:var(--fg-primary);border-bottom-color:var(--fg-primary);font-weight:700;}
    /* ── Purchase cards ── */
    .purchase-list{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-top:none;border-radius:0 0 14px 14px;overflow:hidden;}
    .purchase-card{border-bottom:1px solid var(--fg-border);}
    .purchase-card:last-child{border-bottom:none;}
    .purchase-card-shop{display:flex;align-items:center;justify-content:space-between;padding:0.75rem 1.25rem;border-bottom:1px solid var(--fg-border);background:rgba(230,168,0,0.03);}
    .purchase-card-shop-name{font-size:0.85rem;font-weight:700;color:var(--fg-text);display:flex;align-items:center;gap:0.5rem;}
    .purchase-card-item{display:flex;gap:1rem;padding:1rem 1.25rem;align-items:flex-start;}
    .purchase-card-img{width:80px;height:80px;border-radius:8px;object-fit:cover;border:1px solid var(--fg-border);flex-shrink:0;}
    .purchase-card-img-ph{width:80px;height:80px;border-radius:8px;background:var(--fg-bg);border:1px solid var(--fg-border);display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:1.8rem;flex-shrink:0;}
    .purchase-card-info{flex:1;min-width:0;}
    .purchase-card-name{font-size:0.9rem;font-weight:600;color:var(--fg-text);margin-bottom:0.2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .purchase-card-meta{font-size:0.78rem;color:var(--fg-muted);margin-bottom:0.5rem;}
    .purchase-card-qty-price{display:flex;justify-content:space-between;align-items:center;}
    .purchase-card-qty{font-size:0.82rem;color:var(--fg-muted);}
    .purchase-card-price{font-size:0.9rem;font-weight:700;color:var(--fg-text);}
    .purchase-card-footer{display:flex;align-items:center;justify-content:space-between;padding:0.75rem 1.25rem;border-top:1px solid var(--fg-border);flex-wrap:wrap;gap:0.5rem;}
    .purchase-card-total{font-size:0.85rem;color:var(--fg-muted);}
    .purchase-card-total strong{font-size:1rem;font-weight:800;color:var(--fg-primary);margin-left:0.35rem;}
    .purchase-card-actions{display:flex;gap:0.5rem;flex-wrap:wrap;}
    .btn-action{padding:0.45rem 1rem;border-radius:8px;font-size:0.8rem;font-weight:700;cursor:pointer;border:1.5px solid;transition:all 0.15s;text-decoration:none;display:inline-flex;align-items:center;gap:0.3rem;}
    .btn-cancel{border-color:rgba(220,53,69,0.4);color:#dc3545;background:rgba(220,53,69,0.07);}
    .btn-cancel:hover{background:rgba(220,53,69,0.15);}
    .btn-rate{border-color:rgba(230,168,0,0.5);color:var(--fg-primary);background:rgba(230,168,0,0.08);}
    .btn-rate:hover{background:rgba(230,168,0,0.18);}
    .btn-again{border-color:var(--fg-border);color:var(--fg-muted);background:transparent;}
    .btn-again:hover{border-color:var(--fg-primary);color:var(--fg-primary);}
    /* ── Status badges ── */
    .badge-status{display:inline-flex;align-items:center;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.7rem;font-weight:700;text-transform:uppercase;}
    .badge-pending{background:rgba(230,168,0,0.12);color:#c98f00;}
    .badge-progress{background:rgba(59,130,246,0.12);color:#3b82f6;}
    .badge-completed{background:rgba(40,167,69,0.12);color:#28A745;}
    .badge-cancelled{background:rgba(220,53,69,0.12);color:#dc3545;}
    /* ── Empty state ── */
    .purchase-empty{text-align:center;padding:4rem 2rem;}
    .purchase-empty p{margin:0.75rem 0 0;font-size:0.95rem;color:var(--fg-muted);font-weight:500;}
    /* ── Sidebar toggle ── */
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .cu-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .cu-sidebar.open{transform:translateX(0);}
      .cu-main{padding:1.25rem;}
      .purchase-card-item{flex-wrap:wrap;}
    }
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    @keyframes spin{to{transform:rotate(360deg)}}
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation" style="flex-wrap:nowrap !important;">
    <div class="d-flex align-items-center gap-2">
      <a href="dashboard.php" style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);text-decoration:none;flex-shrink:0;" title="Back to Dashboard">
        <i class="bi bi-arrow-left"></i>
      </a>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:38px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-2" style="flex-wrap:nowrap;">
      <!-- Desktop only items -->
      <span class="role-badge customer ord-desk" style="display:none;">👤 Customer</span>
      <span id="navUserName" class="ord-desk" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);display:none;"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="../../../index.php?browse=1" class="btn btn-sm ord-desk" style="display:none;border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;">
        <i class="bi bi-shop"></i> Browse Shop
      </a>
      <button onclick="customerLogout()" class="btn btn-sm ord-desk" style="display:none;border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
      <!-- Bell always visible -->
      <div class="notif-wrap" id="notifWrap" style="position:relative;">
        <button class="notif-bell" id="notifBellBtn" onclick="toggleNotifDropdown()" aria-label="Notifications">
          <i class="bi bi-bell-fill"></i>
          <span class="notif-count" id="notifCount" style="display:none;">0</span>
        </button>
        <div class="notif-dropdown" id="notifDropdown" style="display:none;">
          <div class="notif-header">
            <span>Notifications</span>
            <button onclick="markAllRead()" style="background:none;border:none;color:var(--fg-primary);font-size:0.75rem;font-weight:700;cursor:pointer;">Mark all read</button>
          </div>
          <div id="notifList"><div style="padding:1.5rem;text-align:center;color:var(--fg-muted);font-size:0.83rem;">Loading…</div></div>
        </div>
      </div>
    </div>
  </nav>
  <style>
    @media (min-width: 992px) { .ord-desk { display: flex !important; } }
    @media (max-width: 991px) { .cu-sidebar { display: none !important; } body { padding-bottom: 70px; } }
  </style>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="cu-layout">
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
        <li><a href="orders.php" class="active"><i class="bi bi-bag-heart-fill"></i> My Purchases</a></li>
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
      <div class="page-header">
        <h2><i class="bi bi-bag-heart-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>My Purchases</h2>
        <p>Track and manage your product orders</p>
      </div>

      <!-- Shopee-style tabs + list -->
      <div class="purchase-tabs-wrap">
        <div class="purchase-tabs" id="purchaseTabs">
          <button class="purchase-tab active" data-tab="all">All</button>
          <button class="purchase-tab" data-tab="to-pay">To Pay</button>
          <button class="purchase-tab" data-tab="to-ship">To Ship</button>
          <button class="purchase-tab" data-tab="to-receive">To Receive</button>
          <button class="purchase-tab" data-tab="completed">Completed</button>
          <button class="purchase-tab" data-tab="cancelled">Cancelled</button>
          <button class="purchase-tab" data-tab="return-refund">Return/Refund</button>
          <button class="purchase-tab" data-tab="history" style="margin-left:auto;color:var(--fg-primary);font-weight:700;">🧾 Purchase History</button>
        </div>
      </div>
      <div class="purchase-list" id="purchaseList">
        <div class="purchase-empty">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto;"></div>
          <p>Loading…</p>
        </div>
      </div>

      <!-- Purchase History Panel (hidden until tab clicked) -->
      <div id="purchaseHistoryPanel" style="display:none;margin-top:0.5rem;">
        <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;">

          <!-- Filter bar -->
          <div style="padding:0.85rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
              <i class="bi bi-receipt" style="color:var(--fg-primary);font-size:1.1rem;"></i>
              <span style="font-weight:800;font-size:0.95rem;color:var(--fg-text);">Purchase History</span>
              <span id="historyCount" style="background:rgba(230,168,0,0.12);color:#c98f00;font-size:0.7rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:20px;"></span>
            </div>
            <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
              <select id="historyPayFilter"
                style="padding:0.35rem 0.7rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.8rem;cursor:pointer;outline:none;"
                onchange="renderHistory()">
                <option value="all">All Payments</option>
                <option value="cod">Cash on Delivery</option>
                <option value="gcash">GCash</option>
                <option value="paymongo">Card / Online</option>
                <option value="online">Online</option>
              </select>
              <select id="historyStatusFilter"
                style="padding:0.35rem 0.7rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.8rem;cursor:pointer;outline:none;"
                onchange="renderHistory()">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="processing">Shipped</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>

          <!-- History table -->
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.83rem;" id="historyTable">
              <thead>
                <tr style="background:var(--fg-bg);">
                  <th style="padding:0.65rem 1rem;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Order #</th>
                  <th style="padding:0.65rem 1rem;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Date</th>
                  <th style="padding:0.65rem 1rem;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Product</th>
                  <th style="padding:0.65rem 1rem;text-align:center;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Qty</th>
                  <th style="padding:0.65rem 1rem;text-align:right;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Total</th>
                  <th style="padding:0.65rem 1rem;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Payment</th>
                  <th style="padding:0.65rem 1rem;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Status</th>
                  <th style="padding:0.65rem 1rem;text-align:center;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Receipt</th>
                </tr>
              </thead>
              <tbody id="historyBody">
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                  <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
                  Loading…
                </td></tr>
              </tbody>
            </table>
          </div>

          <!-- Summary footer -->
          <div id="historySummary" style="padding:0.85rem 1.25rem;border-top:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;background:var(--fg-bg);"></div>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'customer') { window.location.href = '../../../login.html'; return; }
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
    var st = document.getElementById('sidebarToggle');
    if (st && sidebar && overlay) {
      st.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
      overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });
    }

    // Tab clicks
    document.querySelectorAll('.purchase-tab').forEach(tab => {
      tab.addEventListener('click', function() {
        document.querySelectorAll('.purchase-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const tabName = this.dataset.tab;
        if (tabName === 'history') {
          document.getElementById('purchaseList').style.display = 'none';
          document.getElementById('purchaseHistoryPanel').style.display = 'block';
          renderHistory();
        } else {
          document.getElementById('purchaseHistoryPanel').style.display = 'none';
          document.getElementById('purchaseList').style.display = 'block';
          renderPurchases(tabName);
        }
      });
    });

    loadOrders();

    // Load unread message count
    loadUnreadMessageCount();

    // PayMongo success toast
    const params = new URLSearchParams(window.location.search);
    if (params.get('payment') === 'success') {
      const toast = document.createElement('div');
      toast.style.cssText = 'position:fixed;bottom:2rem;left:50%;transform:translateX(-50%);background:#28a745;color:#fff;padding:1rem 2rem;border-radius:12px;font-weight:700;font-size:0.9rem;z-index:99999;box-shadow:0 8px 30px rgba(40,167,69,0.4);display:flex;align-items:center;gap:0.6rem;';
      toast.innerHTML = '<i class="bi bi-check-circle-fill"></i> Payment successful! Your order has been placed.';
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 5000);
    }
  });

  let _allOrders = [];
  let _currentTab = 'all';

  // Tab → status mapping
  const TAB_STATUS = {
    'all':           null,
    'to-pay':        'pending',
    'to-ship':       'processing',
    'to-receive':    'processing',
    'completed':     'completed',
    'cancelled':     'cancelled',
    'return-refund': '__none__',
  };

  function loadOrders() {
    document.getElementById('purchaseList').innerHTML =
      '<div class="purchase-empty"><div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto;"></div><p>Loading…</p></div>';
    fetch('../../../backend/customer_orders.php?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) throw new Error(d.message);
        _allOrders = d.orders || [];
        renderPurchases(_currentTab);
      })
      .catch(() => {
        document.getElementById('purchaseList').innerHTML =
          '<div class="purchase-empty"><p>Could not load purchases. Please try again.</p></div>';
      });
  }

  function renderPurchases(tab) {
    _currentTab = tab;
    const list = document.getElementById('purchaseList');
    const statusFilter = TAB_STATUS[tab];

    let filtered;
    if (statusFilter === '__none__') {
      filtered = [];
    } else if (statusFilter === null) {
      filtered = _allOrders;
    } else {
      filtered = _allOrders.filter(o => o.status === statusFilter);
    }

    if (filtered.length === 0) {
      list.innerHTML = `
        <div class="purchase-empty">
          <svg width="110" height="110" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="60" cy="65" r="42" fill="var(--fg-bg)"/>
            <rect x="38" y="28" width="44" height="56" rx="4" fill="var(--fg-card-bg)" stroke="var(--fg-border)" stroke-width="1.5"/>
            <rect x="46" y="22" width="28" height="12" rx="6" fill="var(--fg-card-bg)" stroke="var(--fg-border)" stroke-width="1.5"/>
            <rect x="46" y="48" width="28" height="3" rx="1.5" fill="var(--fg-border)"/>
            <rect x="46" y="56" width="20" height="3" rx="1.5" fill="var(--fg-border)"/>
            <rect x="46" y="64" width="24" height="3" rx="1.5" fill="var(--fg-border)"/>
            <circle cx="88" cy="32" r="5" fill="#e6a800" opacity="0.5"/>
            <circle cx="30" cy="50" r="4" fill="#26aa99" opacity="0.5"/>
            <circle cx="92" cy="72" r="3" fill="#e6a800" opacity="0.3"/>
            <line x1="72" y1="72" x2="82" y2="82" stroke="#26aa99" stroke-width="3" stroke-linecap="round"/>
            <circle cx="82" cy="82" r="8" fill="#26aa99" opacity="0.15"/>
          </svg>
          <p>No orders yet</p>
          <a href="../../../index.php?browse=1"
             style="display:inline-flex;align-items:center;gap:0.4rem;background:var(--fg-primary);color:#fff;padding:0.55rem 1.4rem;border-radius:9px;font-size:0.85rem;font-weight:700;text-decoration:none;margin-top:0.75rem;">
            <i class="bi bi-shop"></i> Browse Shop
          </a>
        </div>`;
      return;
    }

    const statusMap = { pending:'badge-pending', processing:'badge-progress', completed:'badge-completed', cancelled:'badge-cancelled' };
    const statusLabel = { pending:'To Pay', processing:'To Ship', completed:'Completed', cancelled:'Cancelled' };

    list.innerHTML = filtered.map(o => {
      const date  = new Date(o.created_at).toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' });
      const total = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
      const price = parseFloat(o.unit_price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });

      const imgHtml = o.image_path
        ? `<img src="../../../${esc(o.image_path)}" class="purchase-card-img" onerror="this.outerHTML='<div class=\\'purchase-card-img-ph\\'><i class=\\'bi bi-box-seam\\'></i></div>'">`
        : `<div class="purchase-card-img-ph"><i class="bi bi-box-seam"></i></div>`;

      // Action buttons per status
      const contactBtn = o.sales_person_id
        ? `<a href="messages.php?with=${o.sales_person_id}" class="btn-action btn-again"><i class="bi bi-chat-dots"></i> Contact Seller</a>`
        : '';
      let actions = '';
      if (o.status === 'pending') {
        actions = `<button class="btn-action btn-cancel" onclick="cancelOrder(${o.id})"><i class="bi bi-x-circle"></i> Cancel</button>${contactBtn}`;
      } else if (o.status === 'completed') {
        actions = `
          <button class="btn-action btn-rate" onclick="openReviewModal(${o.id},${o.product_id},'${esc(o.product_name)}')"><i class="bi bi-star-fill"></i> Rate</button>
          ${contactBtn}
          <a href="../../../index.php?browse=1" class="btn-action btn-again"><i class="bi bi-arrow-repeat"></i> Buy Again</a>`;
      } else if (o.status === 'processing') {
        // Build seller address — prefer verified address_line fields, fallback to shop address
        const sellerVerified = parseInt(o.seller_address_verified) === 1;
        let sellerAddr = '';
        if (o.seller_address_line) {
          // Use the full verified address from profile
          sellerAddr = [o.seller_address_line, o.seller_barangay, o.seller_city||o.shop_city, o.seller_province, o.seller_zip_code]
            .filter(Boolean).join(', ');
        } else if (o.shop_address || o.shop_city) {
          sellerAddr = [o.shop_address, o.shop_city].filter(Boolean).join(', ');
        }
        const custAddr = [o.customer_address_line, o.customer_barangay, o.customer_city, o.customer_province].filter(Boolean).join(', ') || '';
        const shopName = esc(o.seller_shop_name || 'Fix&Go Shop');
        const verifiedIcon = sellerVerified ? ' <i class="bi bi-patch-check-fill" style="color:#28A745;font-size:0.7rem;" title="Verified address"></i>' : '';
        const trackBtn = `<button class="btn-action" onclick="openTrackModal(${o.id},'${esc(o.product_name)}','${sellerAddr.replace(/'/g,"\'")}','${custAddr.replace(/'/g,"\'")}','${shopName}')" style="border-color:rgba(59,130,246,0.5);color:#3b82f6;background:rgba(59,130,246,0.08);" onmouseenter="this.style.background='rgba(59,130,246,0.18)'" onmouseleave="this.style.background='rgba(59,130,246,0.08)'"><i class="bi bi-geo-alt-fill"></i> Track Order${verifiedIcon}</button>`;
        actions = `${trackBtn}${contactBtn}<a href="../../../index.php?browse=1" class="btn-action btn-again"><i class="bi bi-arrow-repeat"></i> Buy Again</a>`;
      } else if (o.status === 'cancelled') {
        actions = `${contactBtn}<a href="../../../index.php?browse=1" class="btn-action btn-again"><i class="bi bi-arrow-repeat"></i> Buy Again</a>`;
      }

      return `
        <div class="purchase-card">
          <div class="purchase-card-shop">
            <div class="purchase-card-shop-name">
              <i class="bi bi-shop-window" style="color:var(--fg-primary);"></i>
              Fix&amp;Go Shop
            </div>
            <span class="badge-status ${statusMap[o.status] || ''}">${statusLabel[o.status] || o.status}</span>
          </div>
          <div class="purchase-card-item">
            ${imgHtml}
            <div class="purchase-card-info">
              <div class="purchase-card-name">${esc(o.product_name || '—')}</div>
              <div class="purchase-card-meta">${esc(o.category || '')}${o.brand ? ' · ' + esc(o.brand) : ''}</div>
              <div class="purchase-card-qty-price">
                <span class="purchase-card-qty">x${o.quantity}</span>
                <span class="purchase-card-price">₱${price}</span>
              </div>
            </div>
          </div>
          <div class="purchase-card-footer">
            <div class="purchase-card-total">
              Order Total: <strong>₱${total}</strong>
              <span style="font-size:0.72rem;color:var(--fg-muted);margin-left:0.5rem;">${date}</span>
            </div>
            <div class="purchase-card-actions">${actions}</div>
          </div>
        </div>`;
    }).join('');
  }

  // ── Cancel order ─────────────────────────────────────────────
  let _cancelOrderId = null;

  window.cancelOrder = function(orderId) {
    _cancelOrderId = orderId;
    document.getElementById('cancelReasonSelect').value = '';
    document.getElementById('cancelReasonOther').value = '';
    document.getElementById('cancelNotes').value = '';
    document.getElementById('cancelReasonOtherWrap').style.display = 'none';
    document.getElementById('cancelAlert').style.display = 'none';
    document.getElementById('cancelConfirmBtn').disabled = false;
    document.getElementById('cancelConfirmBtn').innerHTML = '<i class="bi bi-x-circle-fill"></i> Cancel Order';
    document.getElementById('cancelModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  };

  window.closeCancelModal = function() {
    document.getElementById('cancelModal').style.display = 'none';
    document.body.style.overflow = '';
  };

  function onCancelReasonChange() {
    const val = document.getElementById('cancelReasonSelect').value;
    document.getElementById('cancelReasonOtherWrap').style.display = val === 'other' ? 'block' : 'none';
  }

  window.confirmCancelOrder = function() {
    const select = document.getElementById('cancelReasonSelect');
    const other  = document.getElementById('cancelReasonOther').value.trim();
    const reason = select.value === 'other' ? (other || 'Other') : select.value;
    if (!reason) {
      const a = document.getElementById('cancelAlert');
      a.style.display = 'flex';
      a.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Please select a reason for cancellation.';
      return;
    }
    const btn = document.getElementById('cancelConfirmBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Cancelling…';
    document.getElementById('cancelAlert').style.display = 'none';
    fetch('../../../api/customer/orders', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'include',
      body: JSON.stringify({ action: 'cancel', order_id: _cancelOrderId, reason, notes: document.getElementById('cancelNotes').value.trim() })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) { closeCancelModal(); loadOrders(); }
        else {
          const a = document.getElementById('cancelAlert');
          a.style.display = 'flex';
          a.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + esc(d.message || 'Could not cancel order.');
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-x-circle-fill"></i> Cancel Order';
        }
      })
      .catch(() => {
        const a = document.getElementById('cancelAlert');
        a.style.display = 'flex';
        a.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Network error. Please try again.';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-x-circle-fill"></i> Cancel Order';
      });
  };

  // ── Review modal ─────────────────────────────────────────────
  let _reviewOrderId = null, _reviewProductId = null, _reviewRating = 5;

  window.openReviewModal = function(orderId, productId, productName) {
    _reviewOrderId = orderId; _reviewProductId = productId; _reviewRating = 5;
    document.getElementById('reviewProductName').textContent = productName;
    document.getElementById('reviewText').value = '';
    document.getElementById('reviewAlert').style.display = 'none';
    document.getElementById('reviewSubmitBtn').disabled = false;
    document.getElementById('reviewSubmitBtn').innerHTML = '<i class="bi bi-send-fill"></i> Submit Review';
    updateStars(5);
    document.getElementById('reviewModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  };

  window.closeReviewModal = function() {
    document.getElementById('reviewModal').style.display = 'none';
    document.body.style.overflow = '';
  };

  function updateStars(val) {
    _reviewRating = val;
    document.querySelectorAll('.rev-star').forEach(s => {
      s.style.color = parseInt(s.dataset.val) <= val ? '#f5a623' : 'var(--fg-border)';
    });
  }

  window.submitReview = function() {
    const text = document.getElementById('reviewText').value.trim();
    const btn = document.getElementById('reviewSubmitBtn');
    const alertEl = document.getElementById('reviewAlert');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting…';
    alertEl.style.display = 'none';
    fetch('../../../api/customer/orders', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'include',
      body: JSON.stringify({ action: 'review', product_id: _reviewProductId, order_id: _reviewOrderId, rating: _reviewRating, review_text: text })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          alertEl.style.cssText = 'display:flex;padding:0.65rem 1rem;border-radius:8px;font-size:0.83rem;font-weight:600;margin-bottom:1rem;background:rgba(40,167,69,0.1);color:#28A745;border:1px solid rgba(40,167,69,0.3);align-items:center;gap:0.5rem;';
          alertEl.innerHTML = '<i class="bi bi-check-circle-fill"></i> Review submitted! Thank you.';
          setTimeout(() => { closeReviewModal(); loadOrders(); }, 1800);
        } else {
          alertEl.style.cssText = 'display:flex;padding:0.65rem 1rem;border-radius:8px;font-size:0.83rem;font-weight:600;margin-bottom:1rem;background:rgba(220,53,69,0.1);color:#dc3545;border:1px solid rgba(220,53,69,0.3);align-items:center;gap:0.5rem;';
          alertEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + esc(d.message || 'Could not submit review.');
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-send-fill"></i> Submit Review';
        }
      })
      .catch(() => {
        alertEl.style.cssText = 'display:flex;padding:0.65rem 1rem;border-radius:8px;font-size:0.83rem;font-weight:600;margin-bottom:1rem;background:rgba(220,53,69,0.1);color:#dc3545;border:1px solid rgba(220,53,69,0.3);align-items:center;gap:0.5rem;';
        alertEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Network error. Please try again.';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Submit Review';
      });
  };

  function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  // ── Purchase History ─────────────────────────────────────────
  const PAY_LABELS = { cod:'Cash on Delivery', gcash:'GCash', paymongo:'Card / Online', online:'Online', card:'Card' };
  const PAY_COLORS = { cod:'#c98f00', gcash:'#00b14f', paymongo:'#6366f1', online:'#3b82f6', card:'#6366f1' };
  const PAY_BG    = { cod:'rgba(201,143,0,0.1)', gcash:'rgba(0,177,79,0.1)', paymongo:'rgba(99,102,241,0.1)', online:'rgba(59,130,246,0.1)', card:'rgba(99,102,241,0.1)' };

  function renderHistory() {
    const payF    = document.getElementById('historyPayFilter')?.value    || 'all';
    const statusF = document.getElementById('historyStatusFilter')?.value || 'all';
    const tbody   = document.getElementById('historyBody');
    const summary = document.getElementById('historySummary');
    const countEl = document.getElementById('historyCount');

    let orders = _allOrders;
    if (payF    !== 'all') orders = orders.filter(o => o.payment_method === payF);
    if (statusF !== 'all') orders = orders.filter(o => o.status === statusF);

    if (countEl) countEl.textContent = orders.length + ' order' + (orders.length !== 1 ? 's' : '');

    const statusCfg = {
      pending:    { cls:'badge-pending',   label:'Pending'   },
      processing: { cls:'badge-progress',  label:'Shipped'   },
      completed:  { cls:'badge-completed', label:'Completed' },
      cancelled:  { cls:'badge-cancelled', label:'Cancelled' },
    };

    if (!orders.length) {
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--fg-muted);">
        <i class="bi bi-receipt" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.2;"></i>
        No orders found for the selected filters.
      </td></tr>`;
      if (summary) summary.innerHTML = '';
      return;
    }

    tbody.innerHTML = orders.map(o => {
      const sc      = statusCfg[o.status] || { cls:'badge-pending', label:o.status };
      const date    = new Date(o.created_at).toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' });
      const total   = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
      const pm      = o.payment_method || 'cod';
      const pmLabel = PAY_LABELS[pm] || pm;
      const pmColor = PAY_COLORS[pm] || '#6c757d';
      const pmBg    = PAY_BG[pm]    || 'rgba(108,117,125,0.1)';
      const pmIcon  = pm === 'cod' ? '💵' : pm === 'gcash' ? '📱' : '💳';
      return `<tr style="border-bottom:1px solid var(--fg-border);transition:background 0.15s;" onmouseenter="this.style.background='var(--fg-bg)'" onmouseleave="this.style.background='transparent'">
        <td style="padding:0.7rem 1rem;font-weight:800;color:var(--fg-primary);white-space:nowrap;">#${o.id}</td>
        <td style="padding:0.7rem 1rem;color:var(--fg-muted);font-size:0.8rem;white-space:nowrap;">${date}</td>
        <td style="padding:0.7rem 1rem;max-width:180px;">
          <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(o.product_name||'—')}</div>
          ${o.brand ? `<div style="font-size:0.73rem;color:var(--fg-muted);">${esc(o.brand)}</div>` : ''}
        </td>
        <td style="padding:0.7rem 1rem;text-align:center;color:var(--fg-muted);">×${o.quantity||1}</td>
        <td style="padding:0.7rem 1rem;text-align:right;font-weight:800;color:var(--fg-text);white-space:nowrap;">₱${total}</td>
        <td style="padding:0.7rem 1rem;">
          <span style="display:inline-flex;align-items:center;gap:0.3rem;background:${pmBg};color:${pmColor};padding:0.2rem 0.6rem;border-radius:20px;font-size:0.72rem;font-weight:700;white-space:nowrap;">${pmIcon} ${pmLabel}</span>
        </td>
        <td style="padding:0.7rem 1rem;"><span class="badge-status ${sc.cls}">${sc.label}</span></td>
        <td style="padding:0.7rem 1rem;text-align:center;">
          <button onclick="openHistoryReceipt(${o.id})"
            style="background:linear-gradient(135deg,var(--fg-primary),#c98f00);color:#fff;border:none;border-radius:8px;padding:0.28rem 0.7rem;font-size:0.72rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.3rem;transition:opacity 0.2s;white-space:nowrap;"
            onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
            🧾 Receipt
          </button>
        </td>
      </tr>`;
    }).join('');

    // Summary footer
    const totalSpend = orders.filter(o => o.status !== 'cancelled').reduce((s, o) => s + parseFloat(o.total_amount || 0), 0);
    const byMethod = {};
    orders.forEach(o => {
      if (o.status === 'cancelled') return;
      const pm = o.payment_method || 'cod';
      if (!byMethod[pm]) byMethod[pm] = 0;
      byMethod[pm] += parseFloat(o.total_amount || 0);
    });
    const methodBreakdown = Object.entries(byMethod).map(([pm, amt]) =>
      `<span style="font-size:0.78rem;color:var(--fg-muted);">${PAY_LABELS[pm]||pm}: <strong style="color:var(--fg-text);">₱${amt.toLocaleString('en-PH',{minimumFractionDigits:2})}</strong></span>`
    ).join('<span style="color:var(--fg-border);margin:0 0.35rem;">|</span>');
    if (summary) summary.innerHTML = `
      <div style="font-size:0.82rem;color:var(--fg-muted);">${methodBreakdown || 'No completed orders'}</div>
      <div style="font-size:0.9rem;font-weight:800;color:var(--fg-primary);">Total Spent: ₱${totalSpend.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>`;
  }

  // ── Receipt Modal ─────────────────────────────────────────────
  window.openHistoryReceipt = function(orderId) {
    const o = _allOrders.find(x => x.id == orderId);
    if (!o) return;
    const modal = document.getElementById('historyReceiptModal');
    if (!modal) return;

    const pm      = o.payment_method || 'cod';
    const pmLabel = PAY_LABELS[pm] || pm;
    const pmColor = PAY_COLORS[pm] || '#6c757d';
    const pmBg    = PAY_BG[pm]    || 'rgba(108,117,125,0.1)';
    const pmIcon  = pm === 'cod' ? '💵' : pm === 'gcash' ? '📱' : '💳';
    const date    = new Date(o.created_at).toLocaleString('en-PH', { dateStyle:'long', timeStyle:'short' });
    const total   = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    const unit    = parseFloat(o.unit_price   || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    const qty     = parseInt(o.quantity || 1);
    const statusCfg = {
      pending:    { color:'#c98f00', bg:'rgba(201,143,0,0.12)',   label:'Pending'   },
      processing: { color:'#3b82f6', bg:'rgba(59,130,246,0.12)',  label:'Shipped'   },
      completed:  { color:'#28A745', bg:'rgba(40,167,69,0.12)',   label:'Completed' },
      cancelled:  { color:'#dc3545', bg:'rgba(220,53,69,0.12)',   label:'Cancelled' },
    };
    const sc = statusCfg[o.status] || { color:'#6c757d', bg:'rgba(108,117,125,0.12)', label:o.status };
    const shopName = esc(o.seller_shop_name || 'Fix&Go Shop');

    document.getElementById('hrModalDate').textContent = date;
    document.getElementById('hrModalBody').innerHTML = `
      <div id="hrPrintContent">
        <!-- Brand -->
        <div style="text-align:center;padding-bottom:1rem;margin-bottom:1rem;border-bottom:2px dashed var(--fg-border);">
          <div style="display:inline-flex;align-items:center;gap:0.5rem;margin-bottom:0.3rem;">
            <span style="font-size:1.5rem;">🔧</span>
            <span style="font-size:1.25rem;font-weight:900;color:var(--fg-primary);">Fix&amp;Go</span>
          </div>
          <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--fg-muted);">Official Purchase Receipt</div>
        </div>

        <!-- Order + status -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.1rem;gap:0.75rem;flex-wrap:wrap;">
          <div>
            <div style="font-size:0.62rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);margin-bottom:0.15rem;">Order</div>
            <div style="font-size:1.2rem;font-weight:900;color:var(--fg-text);">#${o.id}</div>
            <div style="font-size:0.75rem;color:var(--fg-muted);">${date}</div>
          </div>
          <span style="background:${sc.bg};color:${sc.color};padding:0.35rem 1rem;border-radius:50px;font-weight:800;font-size:0.8rem;border:1.5px solid ${sc.color}44;white-space:nowrap;">${sc.label}</span>
        </div>

        <!-- Shop -->
        <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;padding:0.85rem 1rem;margin-bottom:1rem;">
          <div style="font-size:0.62rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--fg-primary);margin-bottom:0.45rem;">🏪 Seller</div>
          <div style="font-weight:700;font-size:0.92rem;color:var(--fg-text);">${shopName}</div>
          ${o.seller_city ? `<div style="font-size:0.78rem;color:var(--fg-muted);margin-top:0.15rem;">📍 ${esc(o.seller_city)}${o.seller_province ? ', ' + esc(o.seller_province) : ''}</div>` : ''}
        </div>

        <!-- Product table -->
        <div style="margin-bottom:1rem;">
          <div style="font-size:0.62rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--fg-primary);margin-bottom:0.5rem;">📦 Order Details</div>
          <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:0.83rem;">
              <thead>
                <tr>
                  <th style="padding:0.55rem 0.85rem;text-align:left;font-size:0.62rem;font-weight:800;text-transform:uppercase;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Product</th>
                  <th style="padding:0.55rem 0.85rem;text-align:center;font-size:0.62rem;font-weight:800;text-transform:uppercase;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Qty</th>
                  <th style="padding:0.55rem 0.85rem;text-align:right;font-size:0.62rem;font-weight:800;text-transform:uppercase;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Unit</th>
                  <th style="padding:0.55rem 0.85rem;text-align:right;font-size:0.62rem;font-weight:800;text-transform:uppercase;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="padding:0.7rem 0.85rem;font-weight:700;color:var(--fg-text);">${esc(o.product_name||'—')}${o.brand ? `<div style="font-size:0.72rem;color:var(--fg-muted);">${esc(o.brand)}</div>` : ''}</td>
                  <td style="padding:0.7rem 0.85rem;text-align:center;color:var(--fg-text);">${qty}</td>
                  <td style="padding:0.7rem 0.85rem;text-align:right;color:var(--fg-muted);">₱${unit}</td>
                  <td style="padding:0.7rem 0.85rem;text-align:right;font-weight:800;color:var(--fg-text);">₱${total}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Totals + payment -->
        <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;padding:0.85rem 1rem;margin-bottom:1rem;">
          <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.85rem;margin-bottom:0.45rem;">
            <span style="color:var(--fg-muted);">Subtotal</span>
            <span style="font-weight:600;color:var(--fg-text);">₱${total}</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:0.45rem;border-bottom:1px solid var(--fg-border);margin-bottom:0.45rem;">
            <span style="font-size:0.85rem;color:var(--fg-muted);">Payment Method</span>
            <span style="display:inline-flex;align-items:center;gap:0.3rem;background:${pmBg};color:${pmColor};padding:0.2rem 0.65rem;border-radius:20px;font-size:0.75rem;font-weight:700;">${pmIcon} ${pmLabel}</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:0.92rem;font-weight:800;color:var(--fg-text);">Total</span>
            <span style="font-size:1.2rem;font-weight:900;color:var(--fg-primary);">₱${total}</span>
          </div>
        </div>

        <!-- Footer -->
        <div style="text-align:center;padding-top:0.85rem;border-top:2px dashed var(--fg-border);">
          <div style="font-size:1rem;margin-bottom:0.25rem;">🙏</div>
          <div style="font-size:0.78rem;font-weight:700;color:var(--fg-text);">Thank you for shopping with Fix&amp;Go!</div>
          <div style="font-size:0.7rem;color:var(--fg-muted);margin-top:0.15rem;">Your trusted repair & parts shop</div>
        </div>
      </div>`;

    modal.style.display = 'flex';
  };

  window.closeHistoryReceipt = function() {
    document.getElementById('historyReceiptModal').style.display = 'none';
  };

  window.printHistoryReceipt = function() {
    var el = document.getElementById('hrPrintContent');
    if (!el) return;
    var win = window.open('', '_blank', 'width=540,height=760');
    var css = '*{box-sizing:border-box;margin:0;padding:0}body{font-family:"Segoe UI",Arial,sans-serif;background:#fff;color:#111;padding:24px}table{width:100%;border-collapse:collapse}th,td{padding:8px 10px}@media print{body{padding:12px}}';
    win.document.write('<!DOCTYPE html><html><head><title>Receipt</title><style>' + css + '</style></head><body>' + el.innerHTML + '</body></html>');
    win.document.close();
    win.focus();
    setTimeout(function() { win.print(); win.close(); }, 500);
  };

  // Cart badge
  (function() {
    try {
      const cart = JSON.parse(sessionStorage.getItem('fg_customer_cart') || '[]');
      const n = cart.reduce((s,i) => s + i.quantity, 0);
      const badge = document.getElementById('navCartBadge');
      if (badge && n > 0) { badge.textContent = n; badge.style.display = 'inline-block'; }
    } catch(e) {}
  })();

  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('../../../backend/logout.php').finally(() => { window.location.href = '../../../login.html'; });
    });
  }

  function toggleNotifDropdown() {
    const d = document.getElementById('notifDropdown');
    d.style.display = (d.style.display === 'none' || !d.style.display) ? 'block' : 'none';
  }
  function markAllRead() {}

  // Load unread message count
  function loadUnreadMessageCount() {
    fetch('../../../backend/messages.php?action=unread_count', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success && d.count > 0) {
          const badge = document.getElementById('navMsgBadge');
          if (badge) {
            badge.textContent = d.count > 99 ? '99+' : d.count;
            badge.style.display = 'inline-block';
          }
        }
      }).catch(() => {});
    setTimeout(loadUnreadMessageCount, 10000);
  }
  </script>

    <!-- Track Order Modal â€” MapLibre GL (same engine as mapcn-main) -->
  <link href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css" rel="stylesheet"/>
  <style>
    #trackModal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(6px);z-index:9100;align-items:center;justify-content:center;padding:0.75rem;}
    #trackBox{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;width:100%;max-width:680px;max-height:94vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);}
    #trackMapEl{height:320px;flex-shrink:0;position:relative;}
    .track-header-bar{background:linear-gradient(135deg,#00b14f,#026d32);padding:0.9rem 1.2rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
    .track-header-bar h5{color:#fff;font-weight:800;font-size:1rem;margin:0;}
    .track-close-btn{background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:0.9rem;}
    .track-close-btn:hover{background:rgba(255,255,255,0.35);}
    .track-info-bar{padding:0.9rem 1.2rem;border-top:1px solid var(--fg-border);flex-shrink:0;display:flex;flex-direction:column;gap:0.65rem;}
    .track-status-steps{display:flex;align-items:center;}
    .track-step{display:flex;flex-direction:column;align-items:center;gap:0.25rem;}
    .track-step-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;flex-shrink:0;}
    .track-step-dot.done{background:#00b14f;color:#fff;}
    .track-step-dot.active{background:#3b82f6;color:#fff;box-shadow:0 0 0 3px rgba(59,130,246,0.25);}
    .track-step-dot.pending{background:var(--fg-bg);border:2px solid var(--fg-border);color:var(--fg-muted);}
    .track-step-label{font-size:0.62rem;font-weight:700;color:var(--fg-muted);white-space:nowrap;}
    .track-step-label.active{color:var(--fg-text);}
    .track-step-line{flex:1;height:3px;border-radius:2px;margin:0 4px;background:var(--fg-border);}
    .track-step-line.done{background:#00b14f;}
    .track-current-loc{display:flex;align-items:center;gap:0.75rem;background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;padding:0.6rem 0.85rem;}
    .loc-text{font-size:0.72rem;color:var(--fg-muted);}
    .loc-city{font-size:0.9rem;font-weight:800;color:var(--fg-text);}
    .track-delivery-date{display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;color:var(--fg-muted);}
    .track-replay-btn{margin:0 1.2rem 0.9rem;padding:0.45rem;border-radius:9px;background:rgba(59,130,246,0.1);color:#3b82f6;border:1.5px solid rgba(59,130,246,0.25);font-weight:700;font-size:0.82rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;transition:all 0.2s;}
    .track-replay-btn:hover{background:#3b82f6;color:#fff;}
    .maplibregl-ctrl-attrib{font-size:0.6rem!important;}
  </style>
  <div id="trackModal">
    <div id="trackBox">
      <div class="track-header-bar">
        <h5>&#128230; Track Package</h5>
        <button class="track-close-btn" onclick="closeTrackModal()"><i class="bi bi-x-lg"></i></button>
      </div>
      <div id="trackMapEl"></div>
      <div class="track-info-bar">
        <div class="track-status-steps">
          <div class="track-step"><div class="track-step-dot done">&#10003;</div><div class="track-step-label active">Ordered</div></div>
          <div class="track-step-line done"></div>
          <div class="track-step"><div class="track-step-dot done">&#10003;</div><div class="track-step-label active">Packed</div></div>
          <div class="track-step-line done"></div>
          <div class="track-step"><div class="track-step-dot active" id="stepShipped">&#x1F69A;</div><div class="track-step-label active">Shipped</div></div>
          <div class="track-step-line" id="lineDelivered"></div>
          <div class="track-step"><div class="track-step-dot pending" id="stepDelivered">&#x1F3E0;</div><div class="track-step-label" id="labelDelivered">Delivered</div></div>
        </div>
        <div class="track-current-loc">
          <span style="font-size:1.4rem;">&#x1F69A;</span>
          <div><div class="loc-text">Currently in</div><div class="loc-city" id="trackLocCity">Loading&hellip;</div></div>
        </div>
        <div class="track-delivery-date">
          <i class="bi bi-calendar-check-fill" style="color:#00b14f;"></i>
          <span>Estimated delivery: <strong id="trackDeliveryDate">&mdash;</strong></span>
        </div>
      </div>

    </div>
  </div>
  <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
  <script>
  'use strict';
  var _tMap=null,_tTruck=null,_tAnimPoints=[],_tAnimStep=0,_tAnimTimer=null;
  var _tOriginCoord=null,_tDestCoord=null;
  var OSRM='https://router.project-osrm.org/route/v1/driving/';
  var MAP_LIGHT='https://basemaps.cartocdn.com/gl/positron-gl-style/style.json';
  var MAP_DARK='https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json';
  var PH=[
    ['manila',14.5995,120.9842],['quezon city',14.6760,121.0437],['makati',14.5547,121.0244],
    ['pasig',14.5764,121.0851],['taguig',14.5243,121.0792],['marikina',14.6507,121.1029],
    ['caloocan',14.6500,120.9667],['las pinas',14.4500,120.9833],['muntinlupa',14.4081,121.0415],
    ['paranaque',14.4793,121.0198],['pasay',14.5378,121.0014],['mandaluyong',14.5794,121.0359],
    ['valenzuela',14.7011,120.9830],['malabon',14.6625,120.9572],
    ['angeles',15.1450,120.5887],['antipolo',14.5860,121.1760],['bacoor',14.4580,120.9340],
    ['baguio',16.4023,120.5960],['batangas',13.7565,121.0583],['calamba',14.2113,121.1653],
    ['cavite',14.4791,120.8970],['dagupan',16.0430,120.3330],['imus',14.4297,120.9367],
    ['legazpi',13.1391,123.7438],['lucena',13.9373,121.6170],['malolos',14.8433,120.8114],
    ['naga',13.6192,123.1814],['olongapo',14.8295,120.2828],['santa rosa',14.3122,121.1114],
    ['tarlac',15.4755,120.5963],['tuguegarao',17.6132,121.7270],['bacolod',10.6770,122.9560],
    ['cebu',10.3157,123.8854],['dumaguete',9.3068,123.3054],['iloilo',10.7202,122.5621],
    ['lapu-lapu',10.3119,123.9494],['mandaue',10.3236,123.9223],['tacloban',11.2543,125.0000],
    ['cagayan de oro',8.4820,124.6472],['davao',7.1907,125.4553],['general santos',6.1131,125.1716],
    ['zamboanga',6.9214,122.0790],['cotabato',7.2047,124.2310],['iligan',8.2280,124.2452],
    ['toril',7.0480,125.5720],['sirawan',7.1100,125.5300],['matina',7.1670,125.4800],
    ['buhangin',7.2170,125.5070],['agdao',7.2090,125.4720],['tugbok',7.1210,125.5490],
    ['mintal',6.9960,125.5020],['marilog',7.3600,125.3600]
  ];
  function lookupCity(t){
    if(!t)return null;
    var tl=t.toLowerCase(),b=null,bs=0;
    for(var i=0;i<PH.length;i++){if(tl.indexOf(PH[i][0])>=0&&PH[i][0].length>bs){bs=PH[i][0].length;b=PH[i];}}
    return b;
  }
  function nearestCity(lat,lng){
    var b='Philippines',bd=Infinity;
    for(var i=0;i<PH.length;i++){var d=Math.pow(lat-PH[i][1],2)+Math.pow(lng-PH[i][2],2);if(d<bd){bd=d;b=PH[i][0].replace(/\b\w/g,function(c){return c.toUpperCase();});}}
    return b;
  }
  function cap(s){return s.replace(/\b\w/g,function(c){return c.toUpperCase();});}
  function dotEl(color){var e=document.createElement('div');e.style.cssText='width:18px;height:18px;border-radius:50%;border:2.5px solid #fff;background:'+color+';box-shadow:0 2px 8px rgba(0,0,0,0.4);';return e;}
  function truckEl(){var e=document.createElement('div');e.style.cssText='width:38px;height:38px;border-radius:50%;background:#fff;border:2.5px solid #00b14f;display:flex;align-items:center;justify-content:center;font-size:19px;box-shadow:0 3px 12px rgba(0,0,0,0.4);';e.textContent='\uD83D\uDE9A';return e;}
  function getStyle(){var d=document.documentElement.getAttribute('data-theme')==='dark'||document.documentElement.classList.contains('dark');return d?MAP_DARK:MAP_LIGHT;}
  function bezier(p1,p2,n){var m=[(p1[0]+p2[0])/2+0.6,(p1[1]+p2[1])/2],pts=[];for(var i=0;i<=n;i++){var t=i/n;pts.push([(1-t)*(1-t)*p1[0]+2*(1-t)*t*m[0]+t*t*p2[0],(1-t)*(1-t)*p1[1]+2*(1-t)*t*m[1]+t*t*p2[1]]);}return pts;}

  // Track order state (module-level, not inside function)
var _tMapReady=false, _tGeoDone=false;
var _tPendingRender=null;

function _tRenderIfReady(){
  if(!_tMapReady||!_tGeoDone||!_tPendingRender)return;
  var oLng=_tPendingRender[0],oLat=_tPendingRender[1],dLng=_tPendingRender[2],dLat=_tPendingRender[3],shopName=_tPendingRender[4];
  _tPendingRender=null;

  // Fit map to show both markers
  var sw=[Math.min(oLng,dLng)-0.05,Math.min(oLat,dLat)-0.05];
  var ne=[Math.max(oLng,dLng)+0.05,Math.max(oLat,dLat)+0.05];
  _tMap.fitBounds([sw,ne],{padding:60,maxZoom:14,duration:500});

  // Clean up old layers/sources
  ['l-full','l-prog'].forEach(function(id){try{_tMap.removeLayer(id);}catch(e){}});
  ['s-full','s-prog'].forEach(function(id){try{_tMap.removeSource(id);}catch(e){}});

  // Fetch real road route via OSRM
  fetch(OSRM+oLng+','+oLat+';'+dLng+','+dLat+'?overview=full&geometries=geojson')
    .then(function(r){return r.json();})
    .then(function(data){
      var coords=data&&data.routes&&data.routes[0]&&data.routes[0].geometry&&data.routes[0].geometry.coordinates;
      if(coords&&coords.length>1){
        _tAnimPoints=coords.map(function(c){return[c[1],c[0]];});
        // Full route - gray background
        _tMap.addSource('s-full',{type:'geojson',data:{type:'Feature',geometry:{type:'LineString',coordinates:coords}}});
        _tMap.addLayer({id:'l-full',type:'line',source:'s-full',paint:{'line-color':'#aaaaaa','line-width':5,'line-opacity':0.5}});
        // Progress portion - blue
        var progCoords=coords.slice(0,Math.max(2,Math.floor(coords.length*0.01)));
        _tMap.addSource('s-prog',{type:'geojson',data:{type:'Feature',geometry:{type:'LineString',coordinates:progCoords}}});
        _tMap.addLayer({id:'l-prog',type:'line',source:'s-prog',paint:{'line-color':'#3b82f6','line-width':6,'line-opacity':0.95}});
      } else {
        // Bezier fallback
        var pts=bezier([oLat,oLng],[dLat,dLng],120);
        _tAnimPoints=pts;
        var lnglat=pts.map(function(p){return[p[1],p[0]];});
        _tMap.addSource('s-full',{type:'geojson',data:{type:'Feature',geometry:{type:'LineString',coordinates:lnglat}}});
        _tMap.addLayer({id:'l-full',type:'line',source:'s-full',paint:{'line-color':'#aaaaaa','line-width':5,'line-opacity':0.5}});
      }
    }).catch(function(){
      // Bezier fallback on error
      var pts=bezier([oLat,oLng],[dLat,dLng],120);
      _tAnimPoints=pts;
      try{
        var lnglat=pts.map(function(p){return[p[1],p[0]];});
        _tMap.addSource('s-full',{type:'geojson',data:{type:'Feature',geometry:{type:'LineString',coordinates:lnglat}}});
        _tMap.addLayer({id:'l-full',type:'line',source:'s-full',paint:{'line-color':'#aaaaaa','line-width':5,'line-opacity':0.5}});
      }catch(e){}
    }).finally(function(){
      // Always add markers and start animation
      if(_tTruck){try{_tTruck.remove();}catch(e){}_tTruck=null;}
      // Remove old markers
      document.querySelectorAll('.track-dot-marker').forEach(function(e){e.parentNode&&e.parentNode.removeChild(e);});
      // Shop marker (green)
      var shopEl=dotEl('#00b14f');shopEl.className='track-dot-marker';
      new maplibregl.Marker({element:shopEl,anchor:'center'})
        .setLngLat([oLng,oLat])
        .setPopup(new maplibregl.Popup({offset:14}).setHTML('<strong>'+(shopName||'Seller')+'</strong><br><small>'+nearestCity(oLat,oLng)+'</small>'))
        .addTo(_tMap);
      // Customer marker (blue)
      var custEl=dotEl('#3b82f6');custEl.className='track-dot-marker';
      new maplibregl.Marker({element:custEl,anchor:'center'})
        .setLngLat([dLng,dLat])
        .setPopup(new maplibregl.Popup({offset:14}).setHTML('<strong>Your Location</strong><br><small>'+nearestCity(dLat,dLng)+'</small>'))
        .addTo(_tMap);
      // Truck starts at seller
      _tTruck=new maplibregl.Marker({element:truckEl(),anchor:'center'}).setLngLat([oLng,oLat]).addTo(_tMap);
      if(!_tAnimPoints.length)_tAnimPoints=bezier([oLat,oLng],[dLat,dLng],120);
      setTimeout(startAnim,600);
    });
}

window.openTrackModal=function(orderId,productName,sellerAddr,custAddr,shopName){
    document.getElementById('trackModal').style.display='flex';
    document.body.style.overflow='hidden';
    var dd=new Date();dd.setDate(dd.getDate()+3);
    document.getElementById('trackDeliveryDate').textContent=dd.toLocaleDateString('en-PH',{weekday:'short',month:'short',day:'numeric'});
    if(_tAnimTimer){clearInterval(_tAnimTimer);_tAnimTimer=null;}
    _tAnimStep=0;_tAnimPoints=[];_tMapReady=false;_tGeoDone=false;_tPendingRender=null;
    document.getElementById('stepDelivered').className='track-step-dot pending';
    document.getElementById('stepDelivered').textContent='\uD83C\uDFE0';
    document.getElementById('lineDelivered').className='track-step-line';
    document.getElementById('labelDelivered').className='track-step-label';
    document.getElementById('trackLocCity').textContent='Locating\u2026';

    var el=document.getElementById('trackMapEl');
    if(_tMap){_tMap.remove();_tMap=null;}

    // Create map
    _tMap=new maplibregl.Map({
      container:el,
      style:getStyle(),
      center:[122.5,11.5],
      zoom:5,
      attributionControl:{compact:true}
    });

    // Register load handler FIRST
    _tMap.on('load',function(){
      _tMapReady=true;
      _tRenderIfReady();
    });

    // Geocode in parallel
    function geo(addr){
      if(!addr||!addr.trim())return Promise.resolve(null);
      return fetch('https://nominatim.openstreetmap.org/search?q='+encodeURIComponent(addr.trim()+', Philippines')+'&format=json&limit=1&countrycodes=ph&accept-language=en')
        .then(function(r){return r.json();})
        .then(function(d){return(d&&d.length>0)?[parseFloat(d[0].lat),parseFloat(d[0].lon)]:null;})
        .catch(function(){return null;});
    }

    Promise.all([geo(sellerAddr),geo(custAddr)]).then(function(res){
      var oc=res[0],dc=res[1];
      if(!oc){var fb=lookupCity(sellerAddr);if(fb)oc=[fb[1],fb[2]];}
      if(!dc){var fb2=lookupCity(custAddr)||lookupCity(sellerAddr);if(fb2)dc=[fb2[1],fb2[2]];}
      if(!oc)oc=[7.1907,125.4553];
      if(!dc)dc=[7.0480,125.5720];
      _tOriginCoord=[oc[0],oc[1]];
      _tDestCoord=[dc[0],dc[1]];
      document.getElementById('trackLocCity').textContent=nearestCity(oc[0],oc[1]);
      _tPendingRender=[oc[1],oc[0],dc[1],dc[0],shopName];
      _tGeoDone=true;
      _tRenderIfReady();
    });
  };

function startAnim(){
    if(_tAnimTimer)clearInterval(_tAnimTimer);
    _tAnimStep=0;var n=_tAnimPoints.length;
    if(n===0)return;
    _tAnimTimer=setInterval(function(){
      if(!_tTruck||_tAnimStep>=n-1){
        clearInterval(_tAnimTimer);
        if(_tAnimStep>=n-1){
          document.getElementById('stepDelivered').className='track-step-dot done';
          document.getElementById('stepDelivered').textContent='\u2713';
          document.getElementById('lineDelivered').className='track-step-line done';
          document.getElementById('labelDelivered').className='track-step-label active';
          document.getElementById('trackLocCity').textContent=nearestCity(_tDestCoord[0],_tDestCoord[1]);
          // Update progress route to full
          try{
            var src=_tMap.getSource('s-prog');
            if(src){
              var fullCoords=_tAnimPoints.map(function(p){return[p[1],p[0]];});
              src.setData({type:'Feature',geometry:{type:'LineString',coordinates:fullCoords}});
            }
          }catch(e){}
        }
        return;
      }
      _tAnimStep++;
      var p=_tAnimPoints[_tAnimStep];
      _tTruck.setLngLat([p[1],p[0]]);
      // Update progress route incrementally
      if(_tAnimStep%10===0){
        try{
          var src=_tMap.getSource('s-prog');
          if(src){
            var progCoords=_tAnimPoints.slice(0,_tAnimStep+1).map(function(q){return[q[1],q[0]];});
            src.setData({type:'Feature',geometry:{type:'LineString',coordinates:progCoords}});
          }
        }catch(e){}
        var pct=_tAnimStep/(n-1);
        document.getElementById('trackLocCity').textContent=nearestCity(
          _tOriginCoord[0]+(_tDestCoord[0]-_tOriginCoord[0])*pct,
          _tOriginCoord[1]+(_tDestCoord[1]-_tOriginCoord[1])*pct
        );
      }
    },55);
  }
window.closeTrackModal=function(){
    if(_tAnimTimer){clearInterval(_tAnimTimer);_tAnimTimer=null;}
    document.getElementById('trackModal').style.display='none';
    document.body.style.overflow='';
  };
  document.getElementById('trackModal').addEventListener('click',function(e){if(e.target===this)closeTrackModal();});
  </script>

<!-- Purchase History Receipt Modal -->
  <div id="historyReceiptModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:9200;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:500px;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);">
      <div style="background:linear-gradient(135deg,var(--fg-primary) 0%,#c98f00 100%);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div>
          <div style="color:#fff;font-weight:800;font-size:1rem;">&#x1F9FE; Purchase Receipt</div>
          <div style="color:rgba(255,255,255,0.75);font-size:0.75rem;margin-top:0.15rem;" id="hrModalDate"></div>
        </div>
        <div style="display:flex;gap:0.4rem;align-items:center;">
          <button onclick="printHistoryReceipt()"
            style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.35);border-radius:8px;padding:0.35rem 0.9rem;font-size:0.78rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.3rem;transition:background 0.2s;"
            onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">
            &#x1F5A8;&#xFE0F; Print
          </button>
          <button onclick="closeHistoryReceipt()"
            style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.35);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;font-weight:700;transition:background 0.2s;flex-shrink:0;"
            onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">&#x2715;</button>
        </div>
      </div>
      <div id="hrModalBody" style="padding:1.35rem;overflow-y:auto;flex:1;"></div>
    </div>
  </div>
  <script>
    document.getElementById('historyReceiptModal').addEventListener('click', function(e) {
      if (e.target === this) closeHistoryReceipt();
    });
  </script>

  <!-- ── Mobile Bottom Nav ── -->
  <nav id="ordersBottomNav" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;background:var(--fg-card-bg);border-top:1px solid var(--fg-border);padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));box-shadow:0 -4px 20px rgba(0,0,0,0.15);">
    <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-house-fill" style="font-size:1.25rem;"></i>Home</a></li>
      <li><a href="../../../index.php#shop" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-primary);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-bag-fill" style="font-size:1.25rem;"></i>Orders</a></li>
      <li><a href="../../../index.php#technicians" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-workspace" style="font-size:1.25rem;"></i>Technicians</a></li>
      <li><a href="notifications.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-bell-fill" style="font-size:1.25rem;"></i>Inbox</a></li>
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-fill" style="font-size:1.25rem;"></i>Me</a></li>
    </ul>
  </nav>
  <script>(function(){var nb=document.getElementById('ordersBottomNav');function c(){nb.style.display=window.innerWidth<=991?'block':'none';}c();window.addEventListener('resize',c);})();</script>

</body>
</html>



