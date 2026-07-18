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
  <title>Fix&amp;Go — Supplier Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .supplier-layout { display: flex; min-height: calc(100vh - 65px); }
    .supplier-sidebar { width: 240px; background: var(--fg-card-bg); border-right: 1px solid var(--fg-border); padding: 1.5rem 0; flex-shrink: 0; position: sticky; top: 65px; height: calc(100vh - 65px); overflow-y: auto; }
    .sidebar-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--fg-muted); padding: 0 1.25rem; margin-bottom: 0.5rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.25rem; color: var(--fg-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.2s ease; }
    .sidebar-nav a:hover { color: var(--fg-primary); background: rgba(230,168,0,0.07); border-left-color: var(--fg-primary); }
    .sidebar-nav a.active { color: var(--fg-primary); background: rgba(230,168,0,0.1); border-left-color: var(--fg-primary); font-weight: 700; }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }
    .supplier-main { flex: 1; padding: 2rem; min-width: 0; }
    /* Welcome banner */
    .welcome-banner { background: linear-gradient(135deg, var(--fg-primary) 0%, #c98f00 100%); border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.75rem; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(230,168,0,0.25); }
    .welcome-banner::after { content: '📦'; position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); font-size: 4rem; opacity: 0.2; }
    .welcome-banner h2 { font-weight: 800; margin: 0 0 0.25rem; font-size: 1.4rem; }
    .welcome-banner p { margin: 0; opacity: 0.85; font-size: 0.9rem; }
    /* Stats */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .stat-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; padding: 1.25rem 1rem; display: flex; align-items: center; gap: 1rem; transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
    .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
    .stat-value { font-size: 1.8rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .stat-label { font-size: 0.72rem; color: var(--fg-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    /* Quick actions */
    .quick-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
    .quick-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; padding: 1.25rem 1rem; text-align: center; text-decoration: none; color: var(--fg-text); display: block; transition: all 0.2s; }
    .quick-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.14); border-color: var(--fg-primary); color: var(--fg-text); }
    .quick-card .qc-icon { font-size: 2rem; margin-bottom: 0.6rem; display: block; }
    .quick-card .qc-label { font-size: 0.85rem; font-weight: 700; }
    /* Section card */
    .section-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; overflow: hidden; margin-bottom: 1.5rem; }
    .section-head { padding: 1rem 1.25rem; border-bottom: 1px solid var(--fg-border); display: flex; align-items: center; justify-content: space-between; }
    .section-head h6 { margin: 0; font-weight: 700; font-size: 0.95rem; color: var(--fg-text); display: flex; align-items: center; gap: 0.5rem; }
    /* Mini table */
    .mini-table { width: 100%; border-collapse: collapse; font-size: 0.83rem; }
    .mini-table th { background: var(--fg-bg); padding: 0.6rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--fg-muted); border-bottom: 1px solid var(--fg-border); }
    .mini-table td { padding: 0.65rem 1rem; border-bottom: 1px solid var(--fg-border); color: var(--fg-text); vertical-align: middle; }
    .mini-table tr:last-child td { border-bottom: none; }
    /* Badges */
    .badge-status { display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .badge-draft      { background: rgba(108,117,125,0.12); color: #6C757D; }
    .badge-pending    { background: rgba(230,168,0,0.12);   color: #c98f00; }
    .badge-verified   { background: rgba(59,130,246,0.12);  color: #3b82f6; }
    .badge-accepted   { background: rgba(40,167,69,0.12);   color: #28A745; }
    .badge-rejected   { background: rgba(220,53,69,0.12);   color: #dc3545; }
    .badge-sent       { background: rgba(99,102,241,0.12);  color: #6366f1; }
    /* Product image */
    .prod-img { width: 44px; height: 44px; border-radius: 8px; object-fit: cover; border: 1px solid var(--fg-border); flex-shrink: 0; }
    .prod-img-ph { width: 44px; height: 44px; border-radius: 8px; background: var(--fg-bg); border: 1px solid var(--fg-border); display: flex; align-items: center; justify-content: center; color: var(--fg-muted); font-size: 1.1rem; flex-shrink: 0; }
    /* Sidebar toggle */
    .sidebar-toggle { display: none; background: none; border: 1.5px solid var(--fg-border); border-radius: 8px; padding: 0.3rem 0.6rem; color: var(--fg-text); cursor: pointer; font-size: 1.1rem; }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-overlay.open { display: block; }
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }
      .supplier-sidebar { position: fixed; top: 65px; left: 0; z-index: 200; transform: translateX(-100%); height: calc(100vh - 65px); box-shadow: 4px 0 20px rgba(0,0,0,0.15); transition: transform 0.3s ease; }
      .supplier-sidebar.open { transform: translateX(0); }
      .supplier-main { padding: 1.25rem; }
    }
    @keyframes spin { to { transform: rotate(360deg); } }
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
      <span class="role-badge supplier sp-desk-hide">📦 Supplier</span>
      <span id="navUserName" class="sp-desk-hide" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <!-- Bell + mobile hamburger always inline -->
      <div style="display:flex;align-items:center;gap:0.35rem;flex-shrink:0;">
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
        <a href="/dashboard.php" class="btn btn-sm sp-desk-hide"
           style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;">
          <i class="bi bi-arrow-left"></i> Back
        </a>
        <button id="spMobileMenu" onclick="toggleSpDrawer()" aria-label="Menu"
          style="display:none;background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:8px;width:34px;height:34px;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);flex-shrink:0;">
          <i class="bi bi-list"></i>
        </button>
      </div>
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="supplier-layout">
    <!-- Sidebar -->
    <aside class="supplier-sidebar" id="supplierSidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php" class="active"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
        <li><a href="owner-purchases.php"><i class="bi bi-cart-check"></i> Owner Purchases</a></li>
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Orders</a></li>
        <li><a href="deliveries.php"><i class="bi bi-truck"></i> Deliveries</a></li>
        <li><a href="tech-requests.php"><i class="bi bi-tools"></i> Tech Requests</a></li>
        <li><a href="tech-orders.php"><i class="bi bi-bag-check"></i> Tech Orders</a></li>
        <li><a href="sales-report.php"><i class="bi bi-bar-chart-line"></i> Sales Report</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>

    <!-- Main -->
    <main class="supplier-main">

      <!-- Welcome banner -->
      <div class="welcome-banner">
        <h2>Welcome back, <span id="supplierName">Supplier</span>! 👋</h2>
        <p>Here's what's happening with your store today.</p>
        <span class="role-badge supplier" style="background:rgba(255,255,255,0.2);color:#fff;margin-top:0.5rem;display:inline-flex;">📦 Supplier</span>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981;"><i class="bi bi-box-seam"></i></div>
          <div>
            <div class="stat-value" style="color:#10b981;" id="statTotalProducts">—</div>
            <div class="stat-label">Total Products</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(230,168,0,0.12);color:#c98f00;"><i class="bi bi-hourglass-split"></i></div>
          <div>
            <div class="stat-value" style="color:#c98f00;" id="statPendingSubmissions">—</div>
            <div class="stat-label">Pending Review</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-check-circle-fill"></i></div>
          <div>
            <div class="stat-value" style="color:#28A745;" id="statAccepted">—</div>
            <div class="stat-label">Accepted</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="bi bi-currency-exchange"></i></div>
          <div>
            <div class="stat-value" style="color:#3b82f6;" id="statRevenue">—</div>
            <div class="stat-label">Total Revenue</div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <h6 style="font-weight:700;color:var(--fg-text);margin-bottom:0.75rem;">Quick Actions</h6>
      <div class="quick-grid">
        <a href="products.php"       class="quick-card"><span class="qc-icon">📦</span><span class="qc-label">My Products</span></a>
        <a href="owner-purchases.php" class="quick-card"><span class="qc-icon">🛒</span><span class="qc-label">Owner Purchases</span></a>
        <a href="orders.php"         class="quick-card"><span class="qc-icon">📋</span><span class="qc-label">Orders</span></a>
        <a href="deliveries.php"     class="quick-card"><span class="qc-icon">🚚</span><span class="qc-label">Deliveries</span></a>
        <a href="tech-requests.php"  class="quick-card"><span class="qc-icon">🔧</span><span class="qc-label">Tech Requests</span></a>
        <a href="tech-orders.php"    class="quick-card"><span class="qc-icon">📋</span><span class="qc-label">Tech Orders</span></a>
        <a href="sales-report.php"   class="quick-card"><span class="qc-icon">📊</span><span class="qc-label">Sales Report</span></a>
        <a href="profile.php"        class="quick-card"><span class="qc-icon">👤</span><span class="qc-label">Profile</span></a>
      </div>

      <!-- Recent Products -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-box-seam" style="color:#10b981;"></i> Recent Products</h6>
          <a href="products.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All →</a>
        </div>
        <div id="recentProductsBody">
          <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
            <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#10b981;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
            Loading…
          </div>
        </div>
      </div>

      <!-- Recent Owner Purchases -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-cart-check" style="color:var(--fg-primary);"></i> Recent Owner Purchases</h6>
          <a href="owner-purchases.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All →</a>
        </div>
        <div style="overflow-x:auto;">
          <table class="mini-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Owner</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">Total</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody id="recentPurchasesBody">
              <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
                Loading…
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
  <script src="/assets/js/session-timeout.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'supplier') { window.location.href = '/login.html'; return; }

    const fullName = ((user.firstName || '') + ' ' + (user.lastName || '')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email || 'Supplier';
    document.getElementById('supplierName').textContent = user.firstName || fullName || 'Supplier';

    // Sidebar toggle
    const sidebar = document.getElementById('supplierSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    loadStats();
    loadRecentProducts();
    loadRecentPurchases();
  });

  function loadStats() {
    // Product count
    fetch('/api/supplier/products?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        document.getElementById('statTotalProducts').textContent = (d.products || []).length;
      }).catch(() => { document.getElementById('statTotalProducts').textContent = '—'; });

    // Submission stats (pending / accepted)
    fetch('/api/supplier/orders?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        document.getElementById('statPendingSubmissions').textContent = d.stats.pending || 0;
        document.getElementById('statAccepted').textContent           = d.stats.acknowledged || 0;
      }).catch(() => {
        document.getElementById('statPendingSubmissions').textContent = '—';
        document.getElementById('statAccepted').textContent = '—';
      });

    // Revenue from owner purchases
    fetch('/api/supplier/sales?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success && d.stats) {
          const rev = parseFloat(d.stats.total_revenue || 0);
          document.getElementById('statRevenue').textContent = '₱' + rev.toLocaleString('en-PH', { minimumFractionDigits: 0 });
        } else {
          document.getElementById('statRevenue').textContent = '₱0';
        }
      }).catch(() => {
        document.getElementById('statRevenue').textContent = '₱0';
      });
  }

  function loadRecentProducts() {
    fetch('/api/supplier/products?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const body = document.getElementById('recentProductsBody');
        if (!d.success || !d.products || !d.products.length) {
          body.innerHTML = `<div style="text-align:center;padding:2.5rem 1rem;color:var(--fg-muted);">
            <i class="bi bi-box-seam" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.25;"></i>
            No products yet. <a href="products.php" style="color:var(--fg-primary);font-weight:600;text-decoration:none;">Add your first product →</a>
          </div>`;
          return;
        }

        const statusMap = {
          draft:                { cls: 'badge-draft',    label: 'Draft' },
          verified:             { cls: 'badge-verified', label: 'Verified' },
          sent_to_owner:        { cls: 'badge-pending',  label: 'Sent to Owner' },
          sent_to_sales_person: { cls: 'badge-accepted', label: 'In Shop' },
          accepted:             { cls: 'badge-accepted', label: 'Accepted' },
          rejected:             { cls: 'badge-rejected', label: 'Rejected' },
        };

        const recent = d.products.slice(0, 6);
        body.innerHTML = recent.map(p => {
          const s = statusMap[p.status] || { cls: 'badge-draft', label: p.status };
          const img = p.image_path
            ? `<img src="../../../${esc(p.image_path)}" class="prod-img" onerror="this.outerHTML='<div class=\\'prod-img-ph\\'><i class=\\'bi bi-box-seam\\'></i></div>'">`
            : `<div class="prod-img-ph"><i class="bi bi-box-seam"></i></div>`;
          const price = parseFloat(p.srp || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
          return `<div style="display:flex;gap:0.85rem;padding:0.85rem 1.25rem;border-bottom:1px solid var(--fg-border);align-items:center;">
            ${img}
            <div style="flex:1;min-width:0;">
              <div style="font-weight:600;font-size:0.85rem;color:var(--fg-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(p.item_description)}</div>
              <div style="font-size:0.75rem;color:var(--fg-muted);margin:0.1rem 0;">${esc(p.category)}${p.brand ? ' · ' + esc(p.brand) : ''} · ${p.qty} pcs</div>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:0.2rem;">
                <span class="badge-status ${s.cls}">${s.label}</span>
                <span style="font-weight:800;color:var(--fg-primary);font-size:0.85rem;">₱${price}</span>
              </div>
            </div>
          </div>`;
        }).join('') + `<div style="text-align:center;padding:0.75rem;">
          <a href="products.php" style="font-size:0.82rem;color:var(--fg-primary);font-weight:700;text-decoration:none;">View all products →</a>
        </div>`;
      })
      .catch(() => {
        document.getElementById('recentProductsBody').innerHTML =
          '<div style="text-align:center;padding:1.5rem;color:var(--fg-muted);">Could not load products.</div>';
      });
  }

  function loadRecentPurchases() {
    fetch('/api/supplier/sales?action=purchases', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const tbody = document.getElementById('recentPurchasesBody');
        if (!d.success || !d.purchases || !d.purchases.length) {
          tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--fg-muted);">No owner purchases yet.</td></tr>';
          return;
        }
        tbody.innerHTML = d.purchases.slice(0, 5).map(p => {
          const total = parseFloat(p.total_price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
          const date  = new Date(p.purchased_at).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
          const name  = p.brand ? esc(p.brand) + ' — ' + esc(p.item_description) : esc(p.item_description);
          return `<tr>
            <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:600;">${name}</td>
            <td>
              <div style="font-weight:600;font-size:0.83rem;">${esc(p.owner_name)}</div>
              <div style="font-size:0.72rem;color:var(--fg-muted);">${esc(p.owner_email)}</div>
            </td>
            <td style="text-align:center;font-weight:700;">${p.qty}</td>
            <td style="text-align:right;font-weight:800;color:var(--fg-primary);">₱${total}</td>
            <td style="font-size:0.8rem;color:var(--fg-muted);">${date}</td>
          </tr>`;
        }).join('');
      })
      .catch(() => {
        document.getElementById('recentPurchasesBody').innerHTML =
          '<tr><td colspan="5" style="text-align:center;padding:1.5rem;color:var(--fg-muted);">Could not load purchases.</td></tr>';
      });
  }

  function esc(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  // Notification dropdown
  function toggleNotifDropdown() {
    const dropdown = document.getElementById('notifDropdown');
    if (dropdown.style.display === 'none' || !dropdown.style.display) {
      dropdown.style.display = 'block';
      setTimeout(() => { document.addEventListener('click', closeNotifOnClickOutside); }, 0);
    } else {
      dropdown.style.display = 'none';
      document.removeEventListener('click', closeNotifOnClickOutside);
    }
  }
  function closeNotifOnClickOutside(e) {
    const wrap = document.getElementById('notifWrap');
    if (!wrap.contains(e.target)) {
      document.getElementById('notifDropdown').style.display = 'none';
      document.removeEventListener('click', closeNotifOnClickOutside);
    }
  }
  function markAllRead() {}
  </script>

  <!-- ── Mobile CSS ── -->
  <style>
    @media (max-width: 991px) {
      .sp-desk-hide { display: none !important; }
      #spMobileMenu { display: flex !important; }
      .fg-navbar { flex-wrap: nowrap !important; }
      .fg-navbar > div:last-child { flex-wrap: nowrap !important; gap: 0.35rem !important; }
      #sidebarToggle { display: none !important; }
      .supplier-main { padding-bottom: 75px !important; }
    }
    @media (min-width: 992px) {
      #spMobileMenu, #spDrawer, #spDrawerOverlay, #spBottomNav { display: none !important; }
    }
  </style>

  <!-- ── Mobile Drawer ── -->
  <div id="spDrawerOverlay" onclick="toggleSpDrawer()" style="display:none;position:fixed;inset:0;z-index:1099;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);"></div>
  <div id="spDrawer" style="position:fixed;top:0;right:0;z-index:1100;height:100%;width:75vw;max-width:300px;background:var(--fg-card-bg);border-left:1px solid var(--fg-border);display:flex;flex-direction:column;transform:translateX(100%);transition:transform 0.3s cubic-bezier(0.4,0,0.2,1);box-shadow:-8px 0 32px rgba(0,0,0,0.4);">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);">
      <span style="font-size:0.95rem;font-weight:800;color:var(--fg-text);">📦 Supplier Menu</span>
      <button onclick="toggleSpDrawer()" style="background:none;border:1px solid var(--fg-border);color:var(--fg-text);width:30px;height:30px;border-radius:8px;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>
    <nav style="flex:1;overflow-y:auto;padding:0.5rem 0;">
      <a href="dashboard.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-house-fill" style="width:18px;color:var(--fg-primary);"></i>Dashboard</a>
      <a href="products.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-box-seam" style="width:18px;color:var(--fg-primary);"></i>Products</a>
      <a href="orders.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-cart3" style="width:18px;color:var(--fg-primary);"></i>Orders</a>
      <a href="deliveries.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-truck" style="width:18px;color:var(--fg-primary);"></i>Deliveries</a>
      <a href="messages.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-chat-dots" style="width:18px;color:var(--fg-primary);"></i>Messages</a>
      <a href="sales-report.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-bar-chart-line" style="width:18px;color:var(--fg-primary);"></i>Sales Report</a>
      <a href="profile.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;"><i class="bi bi-person-circle" style="width:18px;color:var(--fg-primary);"></i>Profile</a>
    </nav>
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--fg-border);">
      <a href="/dashboard.php" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;width:100%;padding:0.7rem;border-radius:10px;background:rgba(230,168,0,0.08);border:1.5px solid rgba(230,168,0,0.3);color:var(--fg-primary);font-weight:700;font-size:0.88rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Back to Main
      </a>
    </div>
  </div>

  <!-- ── Mobile Bottom Nav ── -->
  <nav id="spBottomNav" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;background:var(--fg-card-bg);border-top:1px solid var(--fg-border);padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));box-shadow:0 -4px 20px rgba(0,0,0,0.15);">
    <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:var(--fg-primary);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-house-fill" style="font-size:1.2rem;"></i>Home</a></li>
      <li><a href="products.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-box-seam" style="font-size:1.2rem;"></i>Products</a></li>
      <li><a href="orders.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-cart3" style="font-size:1.2rem;"></i>Orders</a></li>
      <li><a href="messages.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-chat-dots" style="font-size:1.2rem;"></i>Messages</a></li>
      <li><a href="profile.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-fill" style="font-size:1.2rem;"></i>Me</a></li>
    </ul>
  </nav>

  <script>
    (function() {
      function checkMob() {
        var bn = document.getElementById('spBottomNav');
        if (bn) bn.style.display = window.innerWidth <= 991 ? 'block' : 'none';
      }
      checkMob(); window.addEventListener('resize', checkMob);
    })();
    function toggleSpDrawer() {
      var d = document.getElementById('spDrawer'), o = document.getElementById('spDrawerOverlay');
      var open = d.style.transform === 'translateX(0%)';
      d.style.transform = open ? 'translateX(100%)' : 'translateX(0%)';
      o.style.display = open ? 'none' : 'block';
      document.body.style.overflow = open ? '' : 'hidden';
    }
    document.addEventListener('keydown', function(e){ if(e.key==='Escape'){var d=document.getElementById('spDrawer');if(d&&d.style.transform==='translateX(0%)') toggleSpDrawer();}});
  </script>

</body>
</html>




