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
  <title>Fix&amp;Go — Technician Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8.1" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5.1" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .tc-layout { display: flex; min-height: calc(100vh - 68px); }
    .tc-sidebar {
      width: 240px; flex-shrink: 0;
      background: var(--fg-card-bg);
      border-right: 1px solid var(--fg-border);
      padding: 1.5rem 0 2rem;
      position: sticky; top: 68px;
      height: calc(100vh - 68px);
      overflow-y: auto;
    }
    .sidebar-profile { display:flex;align-items:center;gap:0.85rem;padding:0 1.25rem 1.25rem;border-bottom:1px solid var(--fg-border);margin-bottom:0.75rem; }
    .sidebar-avatar { width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,rgba(139,92,246,0.25),rgba(139,92,246,0.08));border:2px solid rgba(139,92,246,0.35);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#8b5cf6;font-weight:800;flex-shrink:0;overflow:hidden; }
    .sidebar-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
    .sidebar-profile-name { font-size:0.88rem;font-weight:700;color:var(--fg-text); }
    .sidebar-profile-role { font-size:0.72rem;color:var(--fg-muted); }
    .sidebar-label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);padding:0.75rem 1.25rem 0.35rem; }
    .sidebar-nav { list-style:none;padding:0;margin:0; }
    .sidebar-nav a { display:flex;align-items:center;gap:0.75rem;padding:0.6rem 1.25rem;color:var(--fg-muted);text-decoration:none;font-size:0.88rem;font-weight:500;border-left:3px solid transparent;transition:all 0.2s; }
    .sidebar-nav a:hover { color:#8b5cf6;background:rgba(139,92,246,0.07);border-left-color:#8b5cf6; }
    .sidebar-nav a.active { color:#8b5cf6;background:rgba(139,92,246,0.1);border-left-color:#8b5cf6;font-weight:700; }
    .sidebar-nav a i { font-size:1rem;width:20px;text-align:center; }
    .tc-main { flex:1;padding:2rem;min-width:0; }
    .welcome-banner { background:linear-gradient(135deg,#7c3aed 0%,#4c1d95 100%);border-radius:16px;padding:1.75rem 2rem;color:#fff;margin-bottom:1.75rem;position:relative;overflow:hidden; }
    .welcome-banner::after { content:'🔧';position:absolute;right:2rem;top:50%;transform:translateY(-50%);font-size:4.5rem;opacity:0.15; }
    .welcome-banner h2 { font-weight:800;margin:0 0 0.25rem;font-size:1.4rem; }
    .welcome-banner p { margin:0;opacity:0.85;font-size:0.9rem; }
    .stats-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.75rem; }
    .stat-card { background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.25rem 1rem;display:flex;align-items:center;gap:1rem;transition:transform 0.2s,box-shadow 0.2s; }
    .stat-card:hover { transform:translateY(-3px);box-shadow:0 10px 30px rgba(0,0,0,0.12); }
    .stat-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0; }
    .stat-value { font-size:1.7rem;font-weight:800;line-height:1; }
    .stat-label { font-size:0.72rem;color:var(--fg-muted);font-weight:600;margin-top:0.2rem; }
    .section-card { background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;margin-bottom:1.5rem; }
    .section-head { padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between; }
    .section-head h6 { margin:0;font-weight:700;font-size:0.95rem;color:var(--fg-text); }
    .mini-table { width:100%;border-collapse:collapse;font-size:0.83rem; }
    .mini-table th { background:var(--fg-bg);padding:0.6rem 1rem;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border); }
    .mini-table td { padding:0.65rem 1rem;border-bottom:1px solid var(--fg-border);color:var(--fg-text);vertical-align:middle; }
    .mini-table tr:last-child td { border-bottom:none; }
    .mini-table tr:hover td { background:rgba(139,92,246,0.03); }
    .badge-status { display:inline-flex;align-items:center;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.7rem;font-weight:700;text-transform:uppercase; }
    .badge-pending    { background:rgba(230,168,0,0.12);color:#c98f00; }
    .badge-confirmed  { background:rgba(59,130,246,0.12);color:#3b82f6; }
    .badge-in_progress{ background:rgba(139,92,246,0.12);color:#8b5cf6; }
    .badge-completed  { background:rgba(40,167,69,0.12);color:#28A745; }
    .badge-cancelled  { background:rgba(220,53,69,0.12);color:#dc3545; }
    .product-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;padding:1.25rem; }
    .product-card { background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s,border-color 0.2s; }
    .product-card:hover { transform:translateY(-4px);box-shadow:0 10px 28px rgba(139,92,246,0.14);border-color:#8b5cf6; }
    .product-img { width:100%;aspect-ratio:1/1;object-fit:cover;background:var(--fg-card-bg); }
    .product-img-ph { width:100%;aspect-ratio:1/1;background:linear-gradient(135deg,rgba(139,92,246,0.08),rgba(139,92,246,0.03));display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--fg-muted); }
    .product-body { padding:0.75rem; }
    .product-cat { font-size:0.65rem;font-weight:700;color:#8b5cf6;background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.2);padding:0.1rem 0.45rem;border-radius:50px;display:inline-block;margin-bottom:0.3rem; }
    .product-name { font-size:0.8rem;font-weight:700;color:var(--fg-text);line-height:1.3;margin-bottom:0.25rem; }
    .product-price { font-size:0.95rem;font-weight:800;color:#8b5cf6; }
    .product-qty { font-size:0.7rem;color:var(--fg-muted);background:var(--fg-card-bg);border:1px solid var(--fg-border);padding:0.1rem 0.45rem;border-radius:6px; }
    .toggle-display-btn { width:100%;margin-top:0.5rem;padding:0.35rem;border-radius:8px;font-size:0.72rem;font-weight:700;cursor:pointer;border:1.5px solid;transition:all 0.2s; }
    .toggle-display-btn.shown { background:rgba(139,92,246,0.1);border-color:rgba(139,92,246,0.3);color:#8b5cf6; }
    .toggle-display-btn.shown:hover { background:#8b5cf6;color:#fff; }
    .toggle-display-btn.hidden { background:rgba(107,114,128,0.08);border-color:var(--fg-border);color:var(--fg-muted); }
    .toggle-display-btn.hidden:hover { background:rgba(139,92,246,0.1);border-color:#8b5cf6;color:#8b5cf6; }
    .quick-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1rem;margin-bottom:1.75rem; }
    .quick-card { background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.25rem;text-align:center;text-decoration:none;color:var(--fg-text);display:block;transition:transform 0.2s,box-shadow 0.2s,border-color 0.2s; }
    .quick-card:hover { transform:translateY(-4px);box-shadow:0 12px 40px rgba(0,0,0,0.14);border-color:#8b5cf6;color:var(--fg-text); }
    .quick-card .qc-icon { font-size:2rem;margin-bottom:0.6rem;display:block; }
    .quick-card .qc-label { font-size:0.85rem;font-weight:700; }
    .sidebar-toggle { display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem; }
    .sidebar-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199; }
    .sidebar-overlay.open { display:block; }
    @keyframes spin { to { transform:rotate(360deg); } }
    @media (max-width:992px) { .stats-grid { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:768px) {
      .sidebar-toggle { display:flex;align-items:center; }
      .tc-sidebar { position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s; }
      .tc-sidebar.open { transform:translateX(0); }
      .tc-main { padding:1.25rem; }
    }
    @media (max-width:480px) { .stats-grid { grid-template-columns:1fr 1fr; } }
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
      <a href="/index.php?browse=1" class="btn btn-sm tc-desk-hide"
        style="border:1.5px solid rgba(139,92,246,0.4);border-radius:8px;color:#8b5cf6;background:rgba(139,92,246,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:0.35rem;">
        <i class="bi bi-house-door"></i> Browse Shop
      </a>
      <span class="role-badge tc-desk-hide" style="background:rgba(139,92,246,0.12);color:#8b5cf6;border:1px solid rgba(139,92,246,0.25);padding:0.25rem 0.75rem;border-radius:50px;font-size:0.75rem;font-weight:700;">🔧 Technician</span>
      <span id="navUserName" class="tc-desk-hide" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <!-- Bell + messages + logout grouped inline — desktop only -->
      <a href="messages.php" class="tc-desk-hide" style="position:relative;text-decoration:none;" title="Messages">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);transition:all 0.2s;">
          <i class="bi bi-chat-dots-fill"></i>
        </div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:#8b5cf6;color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <button id="logoutBtn" class="btn btn-sm tc-desk-hide"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
      <!-- Mobile hamburger — always last, inline -->
      <button id="tcMobileMenu" onclick="toggleTcDrawer()" aria-label="Menu"
        style="display:none;background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:8px;width:34px;height:34px;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);flex-shrink:0;">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="tc-layout">
    <!-- Sidebar -->
    <aside class="tc-sidebar" id="tcSidebar">
      <div class="sidebar-profile">
        <div class="sidebar-avatar" id="sidebarAvatar">🔧</div>
        <div>
          <div class="sidebar-profile-name" id="sidebarName">Technician</div>
          <div class="sidebar-profile-role">🔧 Phone Technician</div>
        </div>
      </div>
      <div class="sidebar-label">Main</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php" class="active"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="repairs.php"><i class="bi bi-tools"></i> Repair Bookings</a></li>
        <li><a href="inventory.php"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages <span id="sidebarMsgBadge" style="display:none;background:#8b5cf6;color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;margin-left:auto;"></span></a></li>
      </ul>
      <div class="sidebar-label">Account</div>
      <ul class="sidebar-nav">
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>

    <!-- Main -->
    <main class="tc-main">

      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <h2>Welcome back, <span id="tcName">Technician</span>! 👋</h2>
        <p id="tcSubtitle">Here's your repair &amp; inventory overview.</p>
        <span style="background:rgba(255,255,255,0.2);color:#fff;padding:0.25rem 0.75rem;border-radius:50px;font-size:0.75rem;font-weight:700;margin-top:0.5rem;display:inline-flex;align-items:center;gap:0.4rem;">🔧 Phone Technician</span>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid" id="statsGrid">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(139,92,246,0.12);color:#8b5cf6;"><i class="bi bi-tools"></i></div>
          <div><div class="stat-value" style="color:#8b5cf6;" id="statRepairsToday">—</div><div class="stat-label">Repairs Today</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(230,168,0,0.12);color:#c98f00;"><i class="bi bi-hourglass-split"></i></div>
          <div><div class="stat-value" style="color:#c98f00;" id="statPending">—</div><div class="stat-label">Pending Repairs</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-check-circle-fill"></i></div>
          <div><div class="stat-value" style="color:#28A745;" id="statCompleted">—</div><div class="stat-label">Completed</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981;"><i class="bi bi-currency-exchange"></i></div>
          <div><div class="stat-value" style="color:#10b981;" id="statRevenue">—</div><div class="stat-label">Total Revenue</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="bi bi-clipboard-data"></i></div>
          <div><div class="stat-value" style="color:#3b82f6;" id="statInventory">—</div><div class="stat-label">Inventory Items</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(245,158,11,0.12);color:#f59e0b;"><i class="bi bi-exclamation-triangle-fill"></i></div>
          <div><div class="stat-value" style="color:#f59e0b;" id="statLowStock">—</div><div class="stat-label">Low Stock</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(139,92,246,0.12);color:#8b5cf6;"><i class="bi bi-box-seam"></i></div>
          <div><div class="stat-value" style="color:#8b5cf6;" id="statDisplayed">—</div><div class="stat-label">Products Displayed</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(236,72,153,0.12);color:#ec4899;"><i class="bi bi-chat-dots-fill"></i></div>
          <div><div class="stat-value" style="color:#ec4899;" id="statMessages">—</div><div class="stat-label">Unread Messages</div></div>
        </div>
      </div>

      <!-- Quick Actions -->
      <h6 style="font-weight:700;color:var(--fg-text);margin-bottom:0.75rem;">Quick Actions</h6>
      <div class="quick-grid">
        <a href="repairs.php"   class="quick-card"><span class="qc-icon">🔧</span><span class="qc-label">Repair Bookings</span></a>
        <a href="inventory.php" class="quick-card"><span class="qc-icon">📋</span><span class="qc-label">Inventory</span></a>
        <a href="products.php"  class="quick-card"><span class="qc-icon">📦</span><span class="qc-label">My Products</span></a>
        <a href="/index.php?browse=1" class="quick-card"><span class="qc-icon">🏪</span><span class="qc-label">Browse Shop</span></a>
        <a href="supply-requests.php" class="quick-card"><span class="qc-icon">📤</span><span class="qc-label">Supply Requests</span></a>
        <a href="messages.php"  class="quick-card"><span class="qc-icon">💬</span><span class="qc-label">Messages</span></a>
        <a href="profile.php"   class="quick-card"><span class="qc-icon">👤</span><span class="qc-label">Profile</span></a>
      </div>

      <!-- Recent Repairs -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-tools" style="color:#8b5cf6;margin-right:0.4rem;"></i>Recent Repair Bookings</h6>
          <a href="repairs.php" style="font-size:0.8rem;color:#8b5cf6;font-weight:600;text-decoration:none;">View All →</a>
        </div>
        <div style="overflow-x:auto;">
          <table class="mini-table">
            <thead>
              <tr><th>#</th><th>Customer</th><th>Device</th><th>Issue</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody id="recentRepairsBody">
              <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#8b5cf6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
                Loading…
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Inventory Preview -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-clipboard-data" style="color:#3b82f6;margin-right:0.4rem;"></i>Inventory — Display Control</h6>
          <a href="inventory.php" style="font-size:0.8rem;color:#8b5cf6;font-weight:600;text-decoration:none;">Manage All →</a>
        </div>
        <div id="inventoryPreview">
          <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
            <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
            Loading inventory…
          </div>
        </div>
      </div>

    </main>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script>
  'use strict';
  const API = '../../../backend/technician_dashboard.php';

  function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
  function peso(n) { return '₱' + parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:0}); }
  function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'}) : '—'; }

  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'phone_technician') {
      window.location.href = '/login.html';
      return;
    }

    const fullName = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email;
    document.getElementById('tcName').textContent      = user.firstName || fullName || 'Technician';
    document.getElementById('sidebarName').textContent = fullName || user.email;

    // Avatar initials
    const initials = ((user.firstName||'')[0]||'') + ((user.lastName||'')[0]||'');
    const av = document.getElementById('sidebarAvatar');
    if (initials) av.textContent = initials.toUpperCase();

    // Sidebar toggle
    const sidebar = document.getElementById('tcSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', function () {
      sidebar.classList.toggle('open'); overlay.classList.toggle('open');
    });
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open'); overlay.classList.remove('open');
    });

    loadStats();
    loadRecentRepairs();
    loadInventoryPreview();
    loadUnreadCount();
  });

  function loadStats() {
    fetch(API + '?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        const s = d.stats;
        document.getElementById('statRepairsToday').textContent = s.repairs_today ?? '—';
        document.getElementById('statPending').textContent      = s.pending_repairs ?? '—';
        document.getElementById('statCompleted').textContent    = s.completed_repairs ?? '—';
        document.getElementById('statRevenue').textContent      = peso(s.total_revenue);
        document.getElementById('statInventory').textContent    = s.inventory_items ?? '—';
        document.getElementById('statLowStock').textContent     = s.low_stock ?? '—';
        document.getElementById('statDisplayed').textContent    = s.displayed_products ?? '—';
        document.getElementById('statMessages').textContent     = s.unread_messages ?? '0';
        if ((s.unread_messages||0) > 0) {
          const b = document.getElementById('navMsgBadge');
          if (b) { b.textContent = s.unread_messages; b.style.display = 'inline-block'; }
          const sb = document.getElementById('sidebarMsgBadge');
          if (sb) { sb.textContent = s.unread_messages; sb.style.display = 'inline-block'; }
        }
      }).catch(() => {});
  }

  function loadRecentRepairs() {
    fetch(API + '?action=repairs', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const tbody = document.getElementById('recentRepairsBody');
        if (!d.success || !d.repairs || !d.repairs.length) {
          tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--fg-muted);">No repair bookings yet.</td></tr>';
          return;
        }
        repairsCache = d.repairs;
        const statusMap = {
          pending:     { cls:'badge-pending',     label:'Pending'     },
          confirmed:   { cls:'badge-confirmed',   label:'Confirmed'   },
          in_progress: { cls:'badge-in_progress', label:'In Progress' },
          completed:   { cls:'badge-completed',   label:'Completed'   },
          cancelled:   { cls:'badge-cancelled',   label:'Cancelled'   },
        };
        tbody.innerHTML = d.repairs.slice(0, 6).map(r => {
          const customer = esc(((r.first_name||'') + ' ' + (r.last_name||'')).trim() || 'N/A');
          const s = statusMap[r.status] || { cls:'badge-pending', label: r.status };
          const nextActions = getNextActions(r.id, r.status, r.customer_id);
          const device = esc(r.device_name || r.device_model || '—');
          const issue  = esc(r.fault_description || r.issue_description || '—');
          return `<tr>
            <td style="font-weight:700;color:#8b5cf6;">#${r.id}</td>
            <td>${customer}</td>
            <td style="color:var(--fg-muted);">${device}</td>
            <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${issue}">${issue}</td>
            <td><span class="badge-status ${s.cls}">${s.label}</span></td>
            <td style="color:var(--fg-muted);font-size:0.8rem;">${fmtDate(r.created_at)}</td>
            <td>${nextActions}</td>
          </tr>`;
        }).join('');
      }).catch(() => {
        document.getElementById('recentRepairsBody').innerHTML =
          '<tr><td colspan="7" style="text-align:center;padding:1.5rem;color:var(--fg-muted);">Could not load repairs.</td></tr>';
      });
  }

  // Cache for booking review modal
  let repairsCache = [];

  function getNextActions(id, status, customerId) {
    const reviewBtn = `<button onclick="openReviewModal(${id})" style="padding:0.2rem 0.7rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid #8b5cf6;color:#8b5cf6;background:transparent;margin-right:0.25rem;transition:all 0.2s;" onmouseenter="this.style.background='#8b5cf6';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#8b5cf6'">&#128065; Review</button>`;
    const msgBtn = customerId
      ? `<a href="messages.php?with=${customerId}" style="padding:0.2rem 0.6rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid #3b82f6;color:#3b82f6;background:transparent;margin-right:0.25rem;transition:all 0.2s;text-decoration:none;display:inline-flex;align-items:center;gap:0.2rem;" onmouseenter="this.style.background='#3b82f6';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#3b82f6'">&#128172; Message</a>`
      : '';
    const quickBtn = (label, newStatus, color) =>
      `<button onclick="updateRepair(${id},'${newStatus}',false)" style="padding:0.2rem 0.6rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid ${color};color:${color};background:transparent;margin-right:0.25rem;transition:all 0.2s;" onmouseenter="this.style.background='${color}';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='${color}'">${label}</button>`;
    if (status === 'pending')     return reviewBtn + quickBtn('Cancel','cancelled','#dc3545');
    if (status === 'confirmed')   return reviewBtn + msgBtn + quickBtn('Start','in_progress','#8b5cf6') + quickBtn('Cancel','cancelled','#dc3545');
    if (status === 'in_progress') return reviewBtn + msgBtn + quickBtn('Complete','completed','#28A745');
    return reviewBtn + msgBtn;
  }

  // ── Cancel Reason helpers ──────────────────────────────────
  let _cancelPendingId = null, _cancelFromModal = false;
  function openCancelModal(id, fromModal) {
    _cancelPendingId  = id;
    _cancelFromModal  = fromModal;
    document.getElementById('cancelReasonInput').value = '';
    document.getElementById('cancelReasonError').style.display = 'none';
    document.getElementById('cancelReasonModal').style.display = 'flex';
  }
  function closeCancelModal() {
    document.getElementById('cancelReasonModal').style.display = 'none';
    _cancelPendingId = null;
  }
  function submitCancelReason() {
    const reason = document.getElementById('cancelReasonInput').value.trim();
    if (!reason) {
      document.getElementById('cancelReasonError').style.display = 'block';
      document.getElementById('cancelReasonInput').focus();
      return;
    }
    closeCancelModal();
    doUpdateRepair(_cancelPendingId, 'cancelled', _cancelFromModal, reason);
  }

  function updateRepair(id, newStatus, fromModal, cancelReason) {
    if (newStatus === 'cancelled' && cancelReason === undefined) {
      openCancelModal(id, fromModal);
      return;
    }
    doUpdateRepair(id, newStatus, fromModal, cancelReason);
  }

  function doUpdateRepair(id, newStatus, fromModal, cancelReason) {
    const labels = { confirmed:'confirm', in_progress:'start', completed:'mark as completed', cancelled:'cancel' };
    if (newStatus !== 'cancelled' && !confirm('Are you sure you want to ' + (labels[newStatus]||newStatus) + ' repair #' + id + '?')) return;
    const payload = { action: 'update_repair', repair_id: id, status: newStatus };
    if (newStatus === 'cancelled' && cancelReason) payload.cancel_reason = cancelReason;
    fetch(API, {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          if (fromModal) closeReviewModal();
          loadRecentRepairs();
          loadStats();
          // After confirming, show a toast nudging to check messages
          if (newStatus === 'confirmed') {
            showToast('Booking confirmed! A message was sent to the customer. <a href="messages.php" style="color:#fff;font-weight:800;text-decoration:underline;">Open Messages →</a>', '#3b82f6', 6000);
          }
        } else alert(d.message || 'Failed to update.');
      }).catch(() => alert('Network error.'));
  }

  function showToast(html, color, duration) {
    var t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);background:' + color + ';color:#fff;padding:0.85rem 1.5rem;border-radius:12px;font-weight:600;font-size:0.88rem;z-index:99999;box-shadow:0 8px 30px rgba(0,0,0,0.35);display:flex;align-items:center;gap:0.5rem;max-width:90vw;text-align:center;';
    t.innerHTML = html;
    document.body.appendChild(t);
    setTimeout(function() { if (t.parentNode) t.parentNode.removeChild(t); }, duration || 4000);
  }

  function openReviewModal(id) {
    const r = repairsCache.find(x => x.id == id);
    if (!r) return;
    const modal = document.getElementById('bookingReviewModal');
    const statusMap = {
      pending:     { cls:'badge-pending',     label:'Pending'     },
      confirmed:   { cls:'badge-confirmed',   label:'Confirmed'   },
      in_progress: { cls:'badge-in_progress', label:'In Progress' },
      completed:   { cls:'badge-completed',   label:'Completed'   },
      cancelled:   { cls:'badge-cancelled',   label:'Cancelled'   },
    };
    const s        = statusMap[r.status] || { cls:'badge-pending', label: r.status };
    const customer = esc(((r.first_name||'') + ' ' + (r.last_name||'')).trim() || 'N/A');
    const device   = esc(r.device_name || r.device_model || '—');
    const issue    = esc(r.fault_description || r.issue_description || r.problem_desc || '—');
    const address  = esc(r.address || [r.address_line, r.city].filter(Boolean).join(', ') || '—');
    const phone    = esc(r.contact_number || r.customer_phone || '—');
    const history  = esc(r.phone_history || '');
    const expected = esc(r.expected_fix || '');
    const sched    = r.scheduled_at ? new Date(r.scheduled_at).toLocaleString('en-PH',{dateStyle:'medium',timeStyle:'short'}) : 'Not specified';
    const created  = fmtDate(r.created_at);
    const svcType  = r.service_type || 'shop_fix';
    const svcLabel = svcType === 'home_service' ? '🏠 Home Service' : '🏪 In-Shop Fix';
    const svcColor = svcType === 'home_service' ? '#10b981' : '#8b5cf6';
    const svcBg    = svcType === 'home_service' ? 'rgba(16,185,129,0.12)' : 'rgba(139,92,246,0.12)';

    let actionBtns = '';
    if (r.status === 'pending') {
      actionBtns = `
        <button onclick="updateRepair(${r.id},'confirmed',true)" style="flex:1;padding:0.65rem;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border:none;font-weight:700;font-size:0.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;transition:opacity 0.2s;" onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
          ✅ Confirm Booking
        </button>
        <button onclick="updateRepair(${r.id},'cancelled',true)" style="flex:1;padding:0.65rem;border-radius:10px;background:rgba(220,53,69,0.1);color:#dc3545;border:1.5px solid rgba(220,53,69,0.3);font-weight:700;font-size:0.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;transition:all 0.2s;" onmouseenter="this.style.background='#dc3545';this.style.color='#fff'" onmouseleave="this.style.background='rgba(220,53,69,0.1)';this.style.color='#dc3545'">
          ✕ Decline
        </button>`;
    } else if (r.status === 'confirmed') {
      actionBtns = `
        <a href="messages.php?with=${r.customer_id}" style="flex:1;padding:0.65rem;border-radius:10px;background:rgba(59,130,246,0.1);color:#3b82f6;border:1.5px solid rgba(59,130,246,0.3);font-weight:700;font-size:0.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;text-decoration:none;transition:all 0.2s;" onmouseenter="this.style.background='#3b82f6';this.style.color='#fff'" onmouseleave="this.style.background='rgba(59,130,246,0.1)';this.style.color='#3b82f6'">
          &#128172; Message Customer
        </a>
        <button onclick="updateRepair(${r.id},'in_progress',true)" style="flex:1;padding:0.65rem;border-radius:10px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff;border:none;font-weight:700;font-size:0.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;transition:opacity 0.2s;" onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
          &#128296; Start Repair
        </button>
        <button onclick="updateRepair(${r.id},'cancelled',true)" style="flex:1;padding:0.65rem;border-radius:10px;background:rgba(220,53,69,0.1);color:#dc3545;border:1.5px solid rgba(220,53,69,0.3);font-weight:700;font-size:0.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;transition:all 0.2s;" onmouseenter="this.style.background='#dc3545';this.style.color='#fff'" onmouseleave="this.style.background='rgba(220,53,69,0.1)';this.style.color='#dc3545'">
          &#x2715; Cancel
        </button>`;
    } else if (r.status === 'in_progress') {
      actionBtns = `
        <a href="messages.php?with=${r.customer_id}" style="flex:1;padding:0.65rem;border-radius:10px;background:rgba(59,130,246,0.1);color:#3b82f6;border:1.5px solid rgba(59,130,246,0.3);font-weight:700;font-size:0.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;text-decoration:none;transition:all 0.2s;" onmouseenter="this.style.background='#3b82f6';this.style.color='#fff'" onmouseleave="this.style.background='rgba(59,130,246,0.1)';this.style.color='#3b82f6'">
          &#128172; Message Customer
        </a>
        <button onclick="updateRepair(${r.id},'completed',true)" style="flex:1;padding:0.65rem;border-radius:10px;background:linear-gradient(135deg,#28A745,#15803d);color:#fff;border:none;font-weight:700;font-size:0.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;transition:opacity 0.2s;" onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
          &#127881; Mark as Completed
        </button>`;
    }

    document.getElementById('brModalTitle').textContent = 'Booking #' + r.id;
    document.getElementById('brModalBody').innerHTML = `
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.25rem;flex-wrap:wrap;">
        <div>
          <div style="font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#8b5cf6;margin-bottom:0.15rem;">Submitted</div>
          <div style="font-size:0.82rem;color:var(--fg-muted);">${created}</div>
          ${r.scheduled_at ? `<div style="font-size:0.82rem;color:var(--fg-muted);margin-top:0.2rem;">📅 Scheduled: <strong>${sched}</strong></div>` : ''}
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.4rem;">
          <span class="badge-status ${s.cls}">${s.label}</span>
          <span style="display:inline-flex;align-items:center;gap:0.3rem;background:${svcBg};color:${svcColor};padding:0.2rem 0.7rem;border-radius:20px;font-size:0.72rem;font-weight:700;border:1px solid ${svcColor}33;">${svcLabel}</span>
        </div>
      </div>

      <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;padding:1rem 1.1rem;margin-bottom:1rem;">
        <div style="font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#8b5cf6;margin-bottom:0.5rem;">👤 Customer Details</div>
        <div style="font-weight:800;font-size:0.95rem;color:var(--fg-text);margin-bottom:0.4rem;">${customer}</div>
        <div style="display:flex;flex-wrap:wrap;gap:0.75rem;margin-bottom:0.35rem;">
          <span style="font-size:0.82rem;color:var(--fg-muted);">📞 ${phone}</span>
          <span style="font-size:0.82rem;color:var(--fg-muted);">✉️ ${esc(r.customer_email||'N/A')}</span>
        </div>
        <div style="font-size:0.82rem;color:var(--fg-muted);display:flex;align-items:flex-start;gap:0.35rem;"><span style="flex-shrink:0;">📍</span><span>${address}</span></div>
      </div>

      <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;padding:1rem 1.1rem;margin-bottom:1rem;">
        <div style="font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#8b5cf6;margin-bottom:0.5rem;">📱 Device & Repair Info</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">
          <div><div style="font-size:0.65rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Device</div><div style="font-size:0.9rem;font-weight:700;color:var(--fg-text);">${device}</div></div>
          <div><div style="font-size:0.65rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Preferred Schedule</div><div style="font-size:0.82rem;font-weight:600;color:var(--fg-text);">${sched}</div></div>
        </div>
        <div style="margin-bottom:${history ? '0.75rem' : '0'};">
          <div style="font-size:0.65rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.3rem;">Fault / Issue</div>
          <div style="font-size:0.88rem;color:var(--fg-text);line-height:1.55;background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:8px;padding:0.65rem 0.85rem;">${issue}</div>
        </div>
        ${history ? `<div style="margin-bottom:0.75rem;"><div style="font-size:0.65rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.3rem;">Phone History</div><div style="font-size:0.85rem;color:var(--fg-text);line-height:1.55;background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:8px;padding:0.65rem 0.85rem;">${history}</div></div>` : ''}
        ${expected ? `<div><div style="font-size:0.65rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.3rem;">Expected Fix</div><div style="font-size:0.85rem;color:var(--fg-text);line-height:1.55;background:rgba(139,92,246,0.06);border:1px solid rgba(139,92,246,0.2);border-radius:8px;padding:0.65rem 0.85rem;">${expected}</div></div>` : ''}
      </div>

      ${actionBtns ? `<div style="display:flex;gap:0.75rem;flex-wrap:wrap;">${actionBtns}</div>` : `<div style="text-align:center;padding:0.5rem;font-size:0.82rem;color:var(--fg-muted);">No further actions available.</div>`}
    `;

    modal.style.display = 'flex';
  }

  function closeReviewModal() {
    document.getElementById('bookingReviewModal').style.display = 'none';
  }

  function loadInventoryPreview() {
    fetch(API + '?action=inventory', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const el = document.getElementById('inventoryPreview');
        if (!d.success || !d.items || !d.items.length) {
          el.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--fg-muted);font-size:0.88rem;">No inventory items yet. Items will appear here when assigned by your supervisor.</div>';
          return;
        }
        const cards = d.items.slice(0, 8).map(item => {
          const isShown = parseInt(item.is_displayed) === 1;
          const imgHtml = item.image_path
            ? `<img src="../../../${esc(item.image_path)}" class="product-img" alt="${esc(item.name)}" onerror="this.parentElement.innerHTML='<div class=\\'product-img-ph\\'>📦</div>'">`
            : `<div class="product-img-ph">📦</div>`;
          return `<div class="product-card">
            ${imgHtml}
            <div class="product-body">
              <span class="product-cat">${esc(item.category||'—')}</span>
              <div class="product-name">${esc(item.name)}</div>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:0.4rem;">
                <span class="product-price">${peso(item.price)}</span>
                <span class="product-qty">Qty: ${item.quantity}</span>
              </div>
              <button class="toggle-display-btn ${isShown?'shown':'hidden'}" id="dispBtn_${item.id}"
                onclick="toggleDisplay(${item.id}, this)">
                ${isShown ? '👁 Visible to Customers' : '🚫 Hidden'}
              </button>
            </div>
          </div>`;
        }).join('');
        el.innerHTML = `<div class="product-grid">${cards}</div>
          ${d.items.length > 8 ? `<div style="text-align:center;padding:0.75rem;border-top:1px solid var(--fg-border);"><a href="inventory.php" style="font-size:0.82rem;color:#8b5cf6;font-weight:600;text-decoration:none;">View all ${d.items.length} items →</a></div>` : ''}`;
      }).catch(() => {
        document.getElementById('inventoryPreview').innerHTML =
          '<div style="text-align:center;padding:2rem;color:var(--fg-muted);">Could not load inventory.</div>';
      });
  }

  function toggleDisplay(productId, btn) {
    btn.disabled = true;
    fetch(API, {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'toggle_display', product_id: productId })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          const shown = d.is_displayed === 1;
          btn.className = 'toggle-display-btn ' + (shown ? 'shown' : 'hidden');
          btn.textContent = shown ? '👁 Visible to Customers' : '🚫 Hidden';
          loadStats();
        } else { alert(d.message || 'Failed.'); }
        btn.disabled = false;
      }).catch(() => { btn.disabled = false; });
  }

  function loadUnreadCount() {
    fetch('/api/messages?action=unread_count', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success && d.count > 0) {
          const b = document.getElementById('navMsgBadge');
          if (b) { b.textContent = d.count > 99 ? '99+' : d.count; b.style.display = 'inline-block'; }
          const sb = document.getElementById('sidebarMsgBadge');
          if (sb) { sb.textContent = d.count > 99 ? '99+' : d.count; sb.style.display = 'inline-block'; }
        }
      }).catch(() => {});
    setTimeout(loadUnreadCount, 15000);
  }
  </script>

  <!-- ── Cancel Reason Modal ───────────────────────────────── -->
  <div id="cancelReasonModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:10000;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:16px;width:100%;max-width:420px;box-shadow:0 24px 60px rgba(0,0,0,0.45);overflow:hidden;">
      <div style="background:linear-gradient(135deg,#dc3545,#b02a37);padding:1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;">
        <div>
          <div style="color:#fff;font-weight:800;font-size:0.95rem;display:flex;align-items:center;gap:0.4rem;">✕ Cancel Booking</div>
          <div style="color:rgba(255,255,255,0.75);font-size:0.75rem;margin-top:0.1rem;">Please provide a reason for cancellation</div>
        </div>
        <button onclick="closeCancelModal()" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;font-size:0.95rem;cursor:pointer;font-weight:700;flex-shrink:0;" onmouseenter="this.style.background='rgba(255,255,255,0.3)'" onmouseleave="this.style.background='rgba(255,255,255,0.15)'">✕</button>
      </div>
      <div style="padding:1.35rem;">
        <label style="font-size:0.82rem;font-weight:700;color:var(--fg-text);display:block;margin-bottom:0.5rem;">Reason for Cancellation <span style="color:#dc3545;">*</span></label>
        <textarea id="cancelReasonInput" rows="3" placeholder="e.g. Customer is no longer available, part is out of stock, schedule conflict…"
          style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;resize:vertical;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
          onfocus="this.style.borderColor='#dc3545'" onblur="this.style.borderColor='var(--fg-border)'"></textarea>
        <div id="cancelReasonError" style="display:none;color:#dc3545;font-size:0.78rem;font-weight:600;margin-top:0.35rem;">⚠ Please enter a reason before cancelling.</div>
        <div style="display:flex;gap:0.75rem;margin-top:1rem;justify-content:flex-end;">
          <button onclick="closeCancelModal()" style="padding:0.5rem 1.1rem;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.85rem;font-weight:600;cursor:pointer;">Go Back</button>
          <button onclick="submitCancelReason()" style="padding:0.5rem 1.25rem;border-radius:8px;border:none;background:#dc3545;color:#fff;font-size:0.85rem;font-weight:700;cursor:pointer;transition:opacity 0.2s;" onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">✕ Confirm Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Booking Review Modal ───────────────────────────────── -->
  <div id="bookingReviewModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:520px;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);">

      <!-- Modal Header -->
      <div style="background:linear-gradient(135deg,#7c3aed 0%,#4c1d95 100%);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div>
          <div style="color:#fff;font-weight:800;font-size:1rem;display:flex;align-items:center;gap:0.4rem;">
            🔧 <span id="brModalTitle">Booking Review</span>
          </div>
          <div style="color:rgba(255,255,255,0.7);font-size:0.75rem;margin-top:0.1rem;">Review booking details before taking action</div>
        </div>
        <button onclick="closeReviewModal()"
          style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;font-weight:700;transition:background 0.2s;flex-shrink:0;"
          onmouseenter="this.style.background='rgba(255,255,255,0.3)'" onmouseleave="this.style.background='rgba(255,255,255,0.15)'">✕</button>
      </div>

      <!-- Modal Body -->
      <div id="brModalBody" style="padding:1.35rem;overflow-y:auto;flex:1;"></div>

    </div>
  </div>

  <script>
    // Close modal on backdrop click
    document.getElementById('bookingReviewModal').addEventListener('click', function(e) {
      if (e.target === this) closeReviewModal();
    });
    document.getElementById('cancelReasonModal').addEventListener('click', function(e) {
      if (e.target === this) closeCancelModal();
    });
  </script>

  <!-- ── Mobile CSS ── -->
  <style>
    @media (max-width: 991px) {
      .tc-desk-hide { display: none !important; }
      #tcMobileMenu { display: flex !important; }
      .fg-navbar { flex-wrap: nowrap !important; }
      .fg-navbar > div:last-child { flex-wrap: nowrap !important; gap: 0.35rem !important; }
      #sidebarToggle { display: none !important; }
      .tc-main { padding-bottom: 75px !important; }
    }
    @media (min-width: 992px) {
      #tcMobileMenu { display: none !important; }
      #tcDrawer, #tcDrawerOverlay, #tcBottomNav { display: none !important; }
    }
  </style>

  <!-- ── Mobile Drawer ── -->
  <div id="tcDrawerOverlay" onclick="toggleTcDrawer()" style="display:none;position:fixed;inset:0;z-index:1099;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);"></div>
  <div id="tcDrawer" style="position:fixed;top:0;right:0;z-index:1100;height:100%;width:75vw;max-width:300px;background:var(--fg-card-bg);border-left:1px solid var(--fg-border);display:flex;flex-direction:column;transform:translateX(100%);transition:transform 0.3s cubic-bezier(0.4,0,0.2,1);box-shadow:-8px 0 32px rgba(0,0,0,0.4);">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);">
      <div style="display:flex;align-items:center;gap:0.65rem;">
        <div style="width:36px;height:36px;border-radius:10px;background:rgba(139,92,246,0.15);border:1.5px solid rgba(139,92,246,0.3);display:flex;align-items:center;justify-content:center;font-size:1rem;color:#8b5cf6;flex-shrink:0;">🔧</div>
        <div><div id="tcDrawerName" style="font-size:0.88rem;font-weight:700;color:var(--fg-text);">Technician</div><div style="font-size:0.68rem;color:#8b5cf6;">🔧 Phone Technician</div></div>
      </div>
      <button onclick="toggleTcDrawer()" style="background:none;border:1px solid var(--fg-border);color:var(--fg-text);width:30px;height:30px;border-radius:8px;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>
    <nav style="flex:1;overflow-y:auto;padding:0.5rem 0;">
      <a href="dashboard.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-house-fill" style="width:18px;color:#8b5cf6;"></i>Dashboard</a>
      <a href="repairs.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-tools" style="width:18px;color:#8b5cf6;"></i>Repair Bookings</a>
      <a href="inventory.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-clipboard-data" style="width:18px;color:#8b5cf6;"></i>Inventory</a>
      <a href="messages.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-chat-dots-fill" style="width:18px;color:#8b5cf6;"></i>Messages</a>
      <a href="supply-requests.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-send" style="width:18px;color:#8b5cf6;"></i>Supply Requests</a>
      <a href="products.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);"><i class="bi bi-box-seam" style="width:18px;color:#8b5cf6;"></i>My Products</a>
      <a href="profile.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;"><i class="bi bi-person-circle" style="width:18px;color:#8b5cf6;"></i>Profile</a>
    </nav>
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--fg-border);">
      <button id="tcDrawerLogout" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;width:100%;padding:0.7rem;border-radius:10px;background:rgba(220,53,69,0.08);border:1.5px solid rgba(220,53,69,0.3);color:#dc3545;font-weight:700;font-size:0.88rem;cursor:pointer;"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>
  </div>

  <!-- ── Mobile Bottom Nav ── -->
  <nav id="tcBottomNav" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;background:var(--fg-card-bg);border-top:1px solid var(--fg-border);padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));box-shadow:0 -4px 20px rgba(0,0,0,0.15);">
    <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:#8b5cf6;text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-house-fill" style="font-size:1.2rem;"></i>Home</a></li>
      <li><a href="repairs.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-tools" style="font-size:1.2rem;"></i>Technicians</a></li>
      <li><a href="inventory.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-clipboard-data" style="font-size:1.2rem;"></i>Inventory</a></li>
      <li><a href="messages.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-chat-dots-fill" style="font-size:1.2rem;"></i>Messages</a></li>
      <li><a href="profile.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.25rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-fill" style="font-size:1.2rem;"></i>Me</a></li>
    </ul>
  </nav>

  <script>
    (function() {
      function checkMob() {
        var bn = document.getElementById('tcBottomNav');
        if (bn) bn.style.display = window.innerWidth <= 991 ? 'block' : 'none';
      }
      checkMob(); window.addEventListener('resize', checkMob);
    })();
    function toggleTcDrawer() {
      var d = document.getElementById('tcDrawer'), o = document.getElementById('tcDrawerOverlay');
      var open = d.style.transform === 'translateX(0%)';
      d.style.transform = open ? 'translateX(100%)' : 'translateX(0%)';
      o.style.display = open ? 'none' : 'block';
      document.body.style.overflow = open ? '' : 'hidden';
      if (!open) { try { var u=FGAuth.UserStore.get(); if(u){ var dn=document.getElementById('tcDrawerName'); if(dn) dn.textContent=((u.firstName||'')+' '+(u.lastName||'')).trim()||u.email; } } catch(e){} }
    }
    var tcDL = document.getElementById('tcDrawerLogout');
    if (tcDL) tcDL.addEventListener('click', function() { var lb=document.getElementById('logoutBtn'); if(lb) lb.click(); });
    document.addEventListener('keydown', function(e){ if(e.key==='Escape'){var d=document.getElementById('tcDrawer');if(d&&d.style.transform==='translateX(0%)') toggleTcDrawer();}});
  </script>

</body>
</html>




