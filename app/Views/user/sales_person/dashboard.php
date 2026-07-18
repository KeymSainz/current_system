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
  <title>Fix&amp;Go â€” Sales Person Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .sp-layout { display: flex; min-height: calc(100vh - 68px); }
    .sp-sidebar {
      width: 240px; flex-shrink: 0;
      background: var(--fg-card-bg);
      border-right: 1px solid var(--fg-border);
      padding: 1.5rem 0;
      position: sticky; top: 68px;
      height: calc(100vh - 68px);
      overflow-y: auto;
    }
    .sidebar-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--fg-muted); padding: 0 1.25rem; margin-bottom: 0.5rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.25rem; color: var(--fg-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.2s; }
    .sidebar-nav a:hover { color: var(--fg-primary); background: rgba(230,168,0,0.07); border-left-color: var(--fg-primary); }
    .sidebar-nav a.active { color: var(--fg-primary); background: rgba(230,168,0,0.1); border-left-color: var(--fg-primary); font-weight: 700; }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }
    .sp-main { flex: 1; padding: 2rem; min-width: 0; }
    .welcome-banner { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: var(--fg-radius); padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.75rem; position: relative; overflow: hidden; }
    .welcome-banner::after { content: 'ðŸ’¼'; position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); font-size: 4rem; opacity: 0.2; }
    .welcome-banner h2 { font-weight: 800; margin: 0 0 0.25rem; font-size: 1.4rem; }
    .welcome-banner p { margin: 0; opacity: 0.85; font-size: 0.9rem; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .stat-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; padding: 1.25rem 1rem; display: flex; align-items: center; gap: 1rem; transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
    .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.72rem; color: var(--fg-muted); font-weight: 600; margin-top: 0.2rem; }
    .quick-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
    .quick-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; padding: 1.25rem; text-align: center; text-decoration: none; color: var(--fg-text); display: block; transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s; }
    .quick-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.14); border-color: var(--fg-primary); color: var(--fg-text); }
    .quick-card .qc-icon { font-size: 2rem; margin-bottom: 0.6rem; display: block; }
    .quick-card .qc-label { font-size: 0.85rem; font-weight: 700; }
    .section-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; overflow: hidden; margin-bottom: 1.5rem; }
    .section-head { padding: 1rem 1.25rem; border-bottom: 1px solid var(--fg-border); display: flex; align-items: center; justify-content: space-between; }
    .section-head h6 { margin: 0; font-weight: 700; font-size: 0.95rem; color: var(--fg-text); }
    .mini-table { width: 100%; border-collapse: collapse; font-size: 0.83rem; }
    .mini-table th { background: var(--fg-bg); padding: 0.6rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--fg-muted); border-bottom: 1px solid var(--fg-border); }
    .mini-table td { padding: 0.65rem 1rem; border-bottom: 1px solid var(--fg-border); color: var(--fg-text); vertical-align: middle; }
    .mini-table tr:last-child td { border-bottom: none; }
    .badge-status { display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .badge-pending  { background: rgba(230,168,0,0.12); color: #c98f00; }
    .badge-approved { background: rgba(40,167,69,0.12); color: #28A745; }
    .badge-rejected { background: rgba(220,53,69,0.12); color: #dc3545; }
    .badge-active   { background: rgba(40,167,69,0.12); color: #28A745; }
    .sidebar-toggle { display: none; background: none; border: 1.5px solid var(--fg-border); border-radius: 8px; padding: 0.3rem 0.6rem; color: var(--fg-text); cursor: pointer; font-size: 1.1rem; }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-overlay.open { display: block; }
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }
      .sp-sidebar { position: fixed; top: 68px; left: 0; z-index: 200; transform: translateX(-100%); height: calc(100vh - 68px); box-shadow: 4px 0 20px rgba(0,0,0,0.15); transition: transform 0.3s; }
      .sp-sidebar.open { transform: translateX(0); }
      .sp-main { padding: 1.25rem; }
    }
    @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr 1fr; } }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>ðŸ”§ Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge sales_person">ðŸ’¼ Sales Person</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="messages.php" style="position:relative;text-decoration:none;" title="Messages">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);transition:all 0.2s;" onmouseenter="this.style.borderColor='var(--fg-primary)';this.style.color='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-text)'">
          <i class="bi bi-chat-dots-fill"></i>
        </div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:var(--fg-primary);color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <a href="/dashboard.php" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="sp-layout">
    <!-- Sidebar -->
    <aside class="sp-sidebar" id="spSidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php" class="active"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Customer Orders</a></li>
        <li><a href="inventory.php"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>

    <!-- Main -->
    <main class="sp-main">

      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <h2>Welcome back, <span id="spName">Sales Person</span>! ðŸ‘‹</h2>
        <p>Here's your sales overview for today.</p>
        <span class="role-badge sales_person" style="background:rgba(255,255,255,0.2);color:#fff;margin-top:0.5rem;display:inline-flex;">ðŸ’¼ Sales Person</span>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="bi bi-cart3"></i></div>
          <div>
            <div class="stat-value" style="color:#3b82f6;" id="statOrdersToday">â€”</div>
            <div class="stat-label">Orders Today</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981;"><i class="bi bi-box-seam"></i></div>
          <div>
            <div class="stat-value" style="color:#10b981;" id="statProducts">â€”</div>
            <div class="stat-label">Products Listed</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(230,168,0,0.12);color:#c98f00;"><i class="bi bi-send"></i></div>
          <div>
            <div class="stat-value" style="color:#c98f00;" id="statPendingReqs">â€”</div>
            <div class="stat-label">Pending Requests</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-currency-exchange"></i></div>
          <div>
            <div class="stat-value" style="color:#28A745;" id="statRevenue">â€”</div>
            <div class="stat-label">Total Revenue</div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <h6 style="font-weight:700;color:var(--fg-text);margin-bottom:0.75rem;">Quick Actions</h6>
      <div class="quick-grid">
        <a href="products.php" class="quick-card"><span class="qc-icon">ðŸ“¦</span><span class="qc-label">My Products</span></a>
        <a href="orders.php"   class="quick-card"><span class="qc-icon">ðŸ›’</span><span class="qc-label">Orders</span></a>
        <a href="inventory.php" class="quick-card"><span class="qc-icon">ðŸ“‹</span><span class="qc-label">Inventory</span></a>
        <a href="supply-requests.php" class="quick-card"><span class="qc-icon">ðŸ“¤</span><span class="qc-label">Supply Requests</span></a>
        <a href="profile.php"  class="quick-card"><span class="qc-icon">ðŸ‘¤</span><span class="qc-label">Profile</span></a>
      </div>

      <!-- Recent Orders -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-cart3" style="color:#3b82f6;margin-right:0.4rem;"></i>Recent Orders</h6>
          <a href="orders.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All â†’</a>
        </div>
        <div style="overflow-x:auto;">
          <table class="mini-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Customer</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="recentOrdersBody">
              <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
                Loadingâ€¦
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Purchase History -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-receipt" style="color:#10b981;margin-right:0.4rem;"></i>Purchase History</h6>
          <a href="orders.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All &#x2192;</a>
        </div>
        <div style="overflow-x:auto;">
          <table class="mini-table">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th style="text-align:center;">Receipt</th>
              </tr>
            </thead>
            <tbody id="purchaseHistoryBody">
              <tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#10b981;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
                Loading&hellip;
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Recent Supply Requests -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-send" style="color:#c98f00;margin-right:0.4rem;"></i>Recent Supply Requests</h6>
          <a href="supply-requests.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All â†’</a>
        </div>
        <div style="overflow-x:auto;">
          <table class="mini-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody id="recentRequestsBody">
              <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#c98f00;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
                Loadingâ€¦
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'sales_person') {
      window.location.href = '/login.html';
      return;
    }

    const fullName = ((user.firstName || '') + ' ' + (user.lastName || '')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email || 'Sales Person';
    document.getElementById('spName').textContent = user.firstName || fullName || 'Sales Person';

    // Sidebar toggle
    const sidebar = document.getElementById('spSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', function () {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('open');
    });
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      overlay.classList.remove('open');
    });

    // Load stats
    loadStats();
    loadRecentOrders();
    loadRecentRequests();
    loadPurchaseHistory();
    loadUnreadMessageCount();
  });

  function loadStats() {
    // Products count
    fetch('/api/sales/products?action=stats')
      .then(r => r.json())
      .then(d => {
        if (d.success && d.stats) {
          document.getElementById('statProducts').textContent = d.stats.total || 0;
        }
      }).catch(() => {});

    // Orders stats â€” from customer_orders via sales_orders.php
    fetch('/api/sales/orders?action=stats')
      .then(r => r.json())
      .then(d => {
        if (d.success && d.stats) {
          document.getElementById('statOrdersToday').textContent = d.stats.orders_today || 0;
          const rev = parseFloat(d.stats.total_revenue || 0);
          document.getElementById('statRevenue').textContent = 'â‚±' + rev.toLocaleString('en-PH', {minimumFractionDigits: 0});
          document.getElementById('statPendingReqs').textContent = d.stats.pending || 0;
        }
      }).catch(() => {});
  }

  function loadRecentOrders() {
    fetch('/api/sales/orders?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const tbody = document.getElementById('recentOrdersBody');
        if (!d.success || !d.orders || d.orders.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--fg-muted);">No customer orders yet.</td></tr>';
          return;
        }
        const statusMap = { pending:'badge-pending', processing:'badge-status', completed:'badge-approved', cancelled:'badge-rejected' };
        const statusLabel = { pending:'Pending', processing:'Shipped', completed:'Completed', cancelled:'Cancelled' };
        tbody.innerHTML = d.orders.slice(0, 5).map(o => {
          const total = parseFloat(o.total_amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});
          const customer = escHtml((o.first_name||'') + ' ' + (o.last_name||'')).trim() || 'N/A';
          const cls = statusMap[o.status] || 'badge-status';
          return `<tr>
            <td style="font-weight:700;color:#3b82f6;">#${o.id}</td>
            <td>${escHtml(o.product_name || 'â€”')}</td>
            <td>${customer}</td>
            <td style="text-align:center;">${o.quantity || 1}</td>
            <td style="font-weight:700;">â‚±${total}</td>
            <td><span class="badge-status ${cls}">${statusLabel[o.status] || o.status}</span></td>
          </tr>`;
        }).join('');
      })
      .catch(() => {
        document.getElementById('recentOrdersBody').innerHTML =
          '<tr><td colspan="6" style="text-align:center;padding:1.5rem;color:var(--fg-muted);">Could not load orders.</td></tr>';
      });
  }

  function loadRecentRequests() {
    fetch('/api/sales/supply-requests?action=list')
      .then(r => r.json())
      .then(d => {
        const tbody = document.getElementById('recentRequestsBody');
        if (!d.success || !d.requests || d.requests.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--fg-muted);">No supply requests yet.</td></tr>';
          return;
        }
        const rows = d.requests.slice(0, 5).map(r => {
          const date = new Date(r.created_at).toLocaleDateString('en-PH');
          const cls = r.status === 'approved' ? 'badge-approved' : r.status === 'rejected' ? 'badge-rejected' : 'badge-pending';
          return `<tr>
            <td style="font-weight:600;">${escHtml(r.product_name)}</td>
            <td style="color:var(--fg-muted);">${escHtml(r.category || 'â€”')}</td>
            <td style="text-align:center;">${r.quantity_requested}</td>
            <td><span class="badge-status ${cls}">${r.status}</span></td>
            <td style="color:var(--fg-muted);">${date}</td>
          </tr>`;
        }).join('');
        tbody.innerHTML = rows;
      })
      .catch(() => {
        document.getElementById('recentRequestsBody').innerHTML =
          '<tr><td colspan="5" style="text-align:center;padding:1.5rem;color:var(--fg-muted);">Could not load requests.</td></tr>';
      });
  }

  function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
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

  // â”€â”€ Purchase History â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
  let allOrdersCache = [];

  function loadPurchaseHistory() {
    fetch('/api/sales/orders?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const tbody = document.getElementById('purchaseHistoryBody');
        if (!d.success || !d.orders || d.orders.length === 0) {
          tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--fg-muted);">No purchase history yet.</td></tr>';
          return;
        }
        allOrdersCache = d.orders;
        const statusMap   = { pending:'badge-pending', processing:'badge-status', completed:'badge-approved', cancelled:'badge-rejected' };
        const statusLabel = { pending:'Pending', processing:'Shipped', completed:'Completed', cancelled:'Cancelled' };
        const payMap      = { cod:'COD', gcash:'GCash', paymongo:'Card/Online', online:'Online' };
        tbody.innerHTML = d.orders.slice(0, 10).map(o => {
          const total    = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
          const customer = escHtml((o.first_name || '') + ' ' + (o.last_name || '')).trim() || 'N/A';
          const date     = new Date(o.created_at).toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' });
          const cls      = statusMap[o.status] || 'badge-status';
          const pay      = payMap[o.payment_method] || escHtml(o.payment_method || 'N/A');
          return `<tr>
            <td style="font-weight:700;color:#10b981;">#${o.id}</td>
            <td style="color:var(--fg-muted);font-size:0.8rem;">${date}</td>
            <td>
              <div style="font-weight:600;">${customer}</div>
              <div style="font-size:0.75rem;color:var(--fg-muted);">${escHtml(o.customer_email||'')}</div>
            </td>
            <td>${escHtml(o.product_name || '\u2014')}</td>
            <td style="text-align:center;">${o.quantity || 1}</td>
            <td style="font-weight:700;">&#8369;${total}</td>
            <td><span style="font-size:0.75rem;background:rgba(59,130,246,0.1);color:#3b82f6;padding:0.15rem 0.5rem;border-radius:20px;font-weight:600;">${pay}</span></td>
            <td><span class="badge-status ${cls}">${statusLabel[o.status] || o.status}</span></td>
            <td style="text-align:center;">
              <button onclick="openReceipt(${o.id})"
                style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:8px;padding:0.3rem 0.75rem;font-size:0.78rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.3rem;transition:opacity 0.2s;"
                onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
                <i class="bi bi-receipt"></i> View
              </button>
            </td>
          </tr>`;
        }).join('');
      })
      .catch(() => {
        document.getElementById('purchaseHistoryBody').innerHTML =
          '<tr><td colspan="9" style="text-align:center;padding:1.5rem;color:var(--fg-muted);">Could not load purchase history.</td></tr>';
      });
  }

  function openReceipt(orderId) {
    const o = allOrdersCache.find(x => x.id == orderId);
    if (!o) return;
    const customer  = escHtml(((o.first_name||'') + ' ' + (o.last_name||'')).trim() || 'N/A');
    const dateStr   = new Date(o.created_at).toLocaleString('en-PH', { dateStyle:'long', timeStyle:'short' });
    const total     = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    const unitPrice = parseFloat(o.unit_price   || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    const qty       = parseInt(o.quantity || 1);
    const payMap    = { cod:'Cash on Delivery', gcash:'GCash', paymongo:'Card / Online', online:'Online' };
    const payLabel  = payMap[o.payment_method] || escHtml(o.payment_method || 'N/A');
    const statusLabel = { pending:'Pending', processing:'Shipped', completed:'Completed', cancelled:'Cancelled' };
    const statusCls   = { pending:'#c98f00', processing:'#3b82f6', completed:'#28A745', cancelled:'#dc3545' };
    const addrParts = [o.address_line, o.barangay, o.city, o.province, o.zip_code].filter(Boolean);
    const address   = addrParts.length ? escHtml(addrParts.join(', ')) : '\u2014';
    document.getElementById('receiptOrderDate').textContent = dateStr;
    document.getElementById('receiptBody').innerHTML = `
      <div id="printReceiptContent" style="font-family:'Segoe UI',sans-serif;color:var(--fg-text);">
        <div style="text-align:center;margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:2px dashed var(--fg-border);">
          <div style="font-size:1.5rem;font-weight:900;color:#10b981;">&#128295; Fix&amp;Go</div>
          <div style="font-size:0.75rem;color:var(--fg-muted);margin-top:0.2rem;">Official Order Receipt</div>
          <div style="font-size:1.1rem;font-weight:800;margin-top:0.5rem;color:var(--fg-text);">Order #${o.id}</div>
          <div style="font-size:0.78rem;color:var(--fg-muted);">${dateStr}</div>
        </div>
        <div style="text-align:center;margin-bottom:1.25rem;">
          <span style="background:${statusCls[o.status]||'#6c757d'}22;color:${statusCls[o.status]||'#6c757d'};padding:0.35rem 1.2rem;border-radius:20px;font-weight:800;font-size:0.85rem;border:1.5px solid ${statusCls[o.status]||'#6c757d'}44;">
            ${statusLabel[o.status] || o.status}
          </span>
        </div>
        <div style="background:var(--fg-bg);border-radius:10px;padding:0.85rem 1rem;margin-bottom:1rem;">
          <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);margin-bottom:0.5rem;">Customer</div>
          <div style="font-weight:700;font-size:0.95rem;">${customer}</div>
          ${o.customer_email ? `<div style="font-size:0.8rem;color:var(--fg-muted);">${escHtml(o.customer_email)}</div>` : ''}
          ${o.customer_phone ? `<div style="font-size:0.8rem;color:var(--fg-muted);"><i class="bi bi-telephone-fill" style="font-size:0.7rem;"></i> ${escHtml(o.customer_phone)}</div>` : ''}
          <div style="font-size:0.8rem;color:var(--fg-muted);margin-top:0.25rem;"><i class="bi bi-geo-alt-fill" style="font-size:0.7rem;"></i> ${address}</div>
        </div>
        <div style="margin-bottom:1rem;">
          <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);margin-bottom:0.5rem;">Order Details</div>
          <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
            <thead>
              <tr style="background:var(--fg-bg);">
                <th style="padding:0.5rem 0.75rem;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);">Product</th>
                <th style="padding:0.5rem 0.75rem;text-align:center;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);">Qty</th>
                <th style="padding:0.5rem 0.75rem;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);">Unit Price</th>
                <th style="padding:0.5rem 0.75rem;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="padding:0.65rem 0.75rem;font-weight:600;">${escHtml(o.product_name||'\u2014')}</td>
                <td style="padding:0.65rem 0.75rem;text-align:center;">${qty}</td>
                <td style="padding:0.65rem 0.75rem;text-align:right;">&#8369;${unitPrice}</td>
                <td style="padding:0.65rem 0.75rem;text-align:right;font-weight:700;">&#8369;${total}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="border-top:2px dashed var(--fg-border);padding-top:0.85rem;margin-bottom:1rem;">
          <div style="display:flex;justify-content:space-between;font-size:0.83rem;margin-bottom:0.3rem;">
            <span style="color:var(--fg-muted);">Subtotal</span><span>&#8369;${total}</span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:0.83rem;margin-bottom:0.3rem;">
            <span style="color:var(--fg-muted);">Payment Method</span><span style="font-weight:600;">${payLabel}</span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:800;margin-top:0.5rem;padding-top:0.5rem;border-top:1px solid var(--fg-border);">
            <span>Total</span><span style="color:#10b981;">&#8369;${total}</span>
          </div>
        </div>
        ${o.notes ? `<div style="background:rgba(59,130,246,0.07);border-left:3px solid #3b82f6;border-radius:0 8px 8px 0;padding:0.6rem 0.85rem;font-size:0.82rem;"><strong>Note:</strong> ${escHtml(o.notes)}</div>` : ''}
        <div style="text-align:center;margin-top:1.25rem;padding-top:0.85rem;border-top:1px solid var(--fg-border);font-size:0.75rem;color:var(--fg-muted);">
          Thank you for your purchase! &#128293;
        </div>
      </div>
    `;
    const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
    modal.show();
  }

  function printReceipt() {
    const content = document.getElementById('printReceiptContent');
    if (!content) return;
    const win = window.open('', '_blank', 'width=520,height=700');
    win.document.write('<!DOCTYPE html><html><head><title>Receipt</title>' +
      '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">' +
      '<style>body{font-family:"Segoe UI",sans-serif;margin:20px;color:#111;}table{width:100%;border-collapse:collapse;}th,td{padding:6px 10px;}@media print{body{margin:0;}}</style>' +
      '</head><body>' + content.innerHTML + '<script src="/assets/js/pwa.js" defer></script>
</body></html>');
    win.document.close();
    win.focus();
    setTimeout(function() { win.print(); win.close(); }, 400);
  }
  </script>

  <!-- Receipt Modal -->
  <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
      <div class="modal-content" style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:16px;overflow:hidden;">
        <div class="modal-header" style="background:linear-gradient(135deg,#10b981,#059669);padding:1.25rem 1.5rem;border:none;">
          <div>
            <h5 class="modal-title" id="receiptModalLabel" style="color:#fff;font-weight:800;margin:0;font-size:1.1rem;">
              <i class="bi bi-receipt me-2"></i>Order Receipt
            </h5>
            <p style="color:rgba(255,255,255,0.8);margin:0;font-size:0.78rem;" id="receiptOrderDate"></p>
          </div>
          <div class="d-flex gap-2">
            <button onclick="printReceipt()" class="btn btn-sm" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;font-size:0.8rem;">
              <i class="bi bi-printer me-1"></i>Print
            </button>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
        </div>
        <div class="modal-body" id="receiptBody" style="padding:1.5rem;">
          <!-- filled by JS -->
        </div>
      </div>
    </div>
  </div>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>


