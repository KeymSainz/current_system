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
  <title>Fix&amp;Go — Sales Report</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=4" />
    <link rel="stylesheet" href="/assets/css/supplier.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }

    .supplier-layout { display: flex; min-height: calc(100vh - 65px); }

    .supplier-sidebar {
      width: 240px;
      background: var(--fg-card-bg);
      border-right: 1px solid var(--fg-border);
      padding: 1.5rem 0;
      flex-shrink: 0;
      position: sticky;
      top: 65px;
      height: calc(100vh - 65px);
      overflow-y: auto;
    }

    .sidebar-label {
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--fg-muted);
      padding: 0 1.25rem;
      margin-bottom: 0.5rem;
    }

    .sidebar-nav { list-style: none; padding: 0; margin: 0; }

    .sidebar-nav a {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.65rem 1.25rem;
      color: var(--fg-muted);
      text-decoration: none;
      font-size: 0.88rem;
      font-weight: 500;
      border-left: 3px solid transparent;
      transition: all 0.2s ease;
    }

    .sidebar-nav a:hover {
      color: var(--fg-primary);
      background: rgba(230,168,0,0.07);
      border-left-color: var(--fg-primary);
    }

    .sidebar-nav a.active {
      color: var(--fg-primary);
      background: rgba(230,168,0,0.1);
      border-left-color: var(--fg-primary);
      font-weight: 700;
    }

    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }

    .supplier-main { flex: 1; padding: 2rem; min-width: 0; }

    /* Date range filter */
    .range-tabs {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    .range-tab {
      padding: 0.4rem 1.1rem;
      border-radius: 20px;
      border: 1.5px solid var(--fg-border);
      background: transparent;
      color: var(--fg-muted);
      font-size: 0.82rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .range-tab:hover { border-color: var(--fg-primary); color: var(--fg-primary); }

    .range-tab.active {
      background: var(--fg-primary);
      border-color: var(--fg-primary);
      color: #fff;
    }

    /* KPI cards */
    .kpi-card {
      background: var(--fg-card-bg);
      border-radius: var(--fg-radius);
      border: 1px solid var(--fg-border);
      padding: 1.25rem 1.5rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(26,26,46,0.14); }

    .kpi-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      margin-bottom: 0.75rem;
    }

    .kpi-value { font-size: 1.6rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .kpi-label { font-size: 0.78rem; color: var(--fg-muted); font-weight: 600; }

    /* Tables */
    .report-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }

    .report-table th {
      background: var(--fg-primary);
      color: #fff;
      padding: 0.65rem 0.9rem;
      text-align: left;
      font-weight: 700;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      white-space: nowrap;
    }

    .report-table td {
      padding: 0.7rem 0.9rem;
      border-bottom: 1px solid var(--fg-border);
      color: var(--fg-text);
      vertical-align: middle;
    }

    .report-table tbody tr:hover { background: rgba(230,168,0,0.04); }

    .rank-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 26px;
      height: 26px;
      border-radius: 50%;
      font-size: 0.75rem;
      font-weight: 800;
    }

    .growth-positive { color: #10b981; font-weight: 700; }
    .growth-negative { color: #dc3545; font-weight: 700; }

    /* Progress bar */
    .pct-bar {
      height: 6px;
      border-radius: 3px;
      background: var(--fg-border);
      overflow: hidden;
      margin-top: 4px;
    }

    .pct-fill {
      height: 100%;
      border-radius: 3px;
      background: var(--fg-primary);
    }

    /* Section title */
    .section-title {
      font-size: 1rem;
      font-weight: 700;
      color: var(--fg-text);
      margin-bottom: 1rem;
    }

    /* Sidebar toggle */
    .sidebar-toggle {
      display: none;
      background: none;
      border: 1.5px solid var(--fg-border);
      border-radius: 8px;
      padding: 0.3rem 0.6rem;
      color: var(--fg-text);
      cursor: pointer;
      font-size: 1.1rem;
    }

    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      z-index: 199;
    }

    .sidebar-overlay.open { display: block; }

    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }

      .supplier-sidebar {
        position: fixed;
        top: 65px;
        left: 0;
        z-index: 200;
        transform: translateX(-100%);
        height: calc(100vh - 65px);
        box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        transition: transform 0.3s ease;
      }

      .supplier-sidebar.open { transform: translateX(0); }
      .supplier-main { padding: 1.25rem; }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation" aria-label="Main navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge supplier">📦 Supplier</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark/light mode">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
      </button>
      <!-- Notification Bell -->
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
      <a href="/dashboard.php" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Back
      </a>

    <!-- Sidebar -->
    <aside class="supplier-sidebar" id="supplierSidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Orders</a></li>
        <li><a href="deliveries.php"><i class="bi bi-truck"></i> Deliveries</a></li>
        <li><a href="tech-requests.php"><i class="bi bi-tools"></i> Tech Requests</a></li>
        <li><a href="tech-orders.php"><i class="bi bi-bag-check"></i> Tech Orders</a></li>
        <li><a href="sales-report.php" class="active"><i class="bi bi-bar-chart-line"></i> Sales Report</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>

    <!-- Main -->
    <main class="supplier-main">

      <!-- Page header + date filter -->
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
          <h2 style="font-weight:800;color:var(--fg-text);margin:0;">Sales Report</h2>
          <p style="color:var(--fg-muted);margin:0;font-size:0.9rem;">Analytics and performance overview</p>
        </div>
        <div class="range-tabs">
          <button class="range-tab" data-range="week">This Week</button>
          <button class="range-tab active" data-range="month">This Month</button>
          <button class="range-tab" data-range="year">This Year</button>
        </div>
      </div>

      <!-- KPI Cards -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="kpi-card">
            <div class="kpi-icon" style="background:rgba(16,185,129,0.12);color:#059669;">
              <i class="bi bi-currency-exchange"></i>
            </div>
            <div class="kpi-value" style="color:#059669;" id="kpiRevenue">₱4,320</div>
            <div class="kpi-label">Total Revenue</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="kpi-card">
            <div class="kpi-icon" style="background:rgba(59,130,246,0.12);color:#2563eb;">
              <i class="bi bi-bag-check"></i>
            </div>
            <div class="kpi-value" style="color:#2563eb;" id="kpiOrders">18</div>
            <div class="kpi-label">Orders Completed</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="kpi-card">
            <div class="kpi-icon" style="background:rgba(139,92,246,0.12);color:#7c3aed;">
              <i class="bi bi-calculator"></i>
            </div>
            <div class="kpi-value" style="color:#7c3aed;" id="kpiAvg">₱240</div>
            <div class="kpi-label">Avg Order Value</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="kpi-card">
            <div class="kpi-icon" style="background:rgba(230,168,0,0.12);color:#c98f00;">
              <i class="bi bi-trophy"></i>
            </div>
            <div class="kpi-value" style="color:#c98f00;font-size:1.1rem;" id="kpiTop">iPhone LCD</div>
            <div class="kpi-label">Top Product</div>
          </div>
        </div>
      </div>

      <!-- Top Products Table -->
      <div class="section-title">Top Products</div>
      <div class="dashboard-card mb-4" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="report-table">
            <thead>
              <tr>
                <th>Rank</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Units Sold</th>
                <th>Revenue</th>
                <th>% of Total</th>
              </tr>
            </thead>
            <tbody id="topProductsBody"></tbody>
          </table>
        </div>
      </div>

      <!-- Monthly Summary Table -->
      <div class="section-title">Monthly Summary</div>
      <div class="dashboard-card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="report-table">
            <thead>
              <tr>
                <th>Month</th>
                <th>Orders</th>
                <th>Revenue</th>
                <th>Growth</th>
              </tr>
            </thead>
            <tbody id="monthlySummaryBody"></tbody>
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
    // ── Data sets per range ──
    const DATA = {
      week: {
        revenue: '₱980', orders: 4, avg: '₱245', top: 'iPhone LCD',
        products: [
          { rank:1, name:'iPhone 13 LCD Assembly', cat:'LCD Screens',   units:3,  rev:'₱540', pct:55 },
          { rank:2, name:'Samsung A54 Battery',    cat:'Batteries',     units:5,  rev:'₱250', pct:26 },
          { rank:3, name:'USB-C Charging Cable',   cat:'Accessories',   units:8,  rev:'₱120', pct:12 },
          { rank:4, name:'Tempered Glass Bundle',  cat:'Screen Protect',units:4,  rev:'₱50',  pct:5  },
          { rank:5, name:'iPhone 12 Back Cover',   cat:'Cases',         units:1,  rev:'₱20',  pct:2  },
        ],
      },
      month: {
        revenue: '₱4,320', orders: 18, avg: '₱240', top: 'iPhone LCD',
        products: [
          { rank:1, name:'iPhone 13 LCD Assembly', cat:'LCD Screens',   units:12, rev:'₱2,160', pct:50 },
          { rank:2, name:'Samsung A54 Battery',    cat:'Batteries',     units:20, rev:'₱1,000', pct:23 },
          { rank:3, name:'USB-C Charging Cable',   cat:'Accessories',   units:35, rev:'₱525',   pct:12 },
          { rank:4, name:'Tempered Glass Bundle',  cat:'Screen Protect',units:40, rev:'₱400',   pct:9  },
          { rank:5, name:'iPhone 12 Back Cover',   cat:'Cases',         units:9,  rev:'₱235',   pct:6  },
        ],
      },
      year: {
        revenue: '₱52,400', orders: 218, avg: '₱240', top: 'iPhone LCD',
        products: [
          { rank:1, name:'iPhone 13 LCD Assembly', cat:'LCD Screens',   units:140, rev:'₱25,200', pct:48 },
          { rank:2, name:'Samsung A54 Battery',    cat:'Batteries',     units:240, rev:'₱12,000', pct:23 },
          { rank:3, name:'USB-C Charging Cable',   cat:'Accessories',   units:420, rev:'₱6,300',  pct:12 },
          { rank:4, name:'Tempered Glass Bundle',  cat:'Screen Protect',units:480, rev:'₱4,800',  pct:9  },
          { rank:5, name:'iPhone 12 Back Cover',   cat:'Cases',         units:108, rev:'₱4,100',  pct:8  },
        ],
      },
    };

    const MONTHLY = [
      { month:'January',  orders:14, revenue:'₱3,360', growth:'+8%',  pos:true  },
      { month:'February', orders:11, revenue:'₱2,640', growth:'-5%',  pos:false },
      { month:'March',    orders:16, revenue:'₱3,840', growth:'+45%', pos:true  },
      { month:'April',    orders:19, revenue:'₱4,560', growth:'+19%', pos:true  },
      { month:'May',      orders:17, revenue:'₱4,080', growth:'-11%', pos:false },
      { month:'June',     orders:18, revenue:'₱4,320', growth:'+6%',  pos:true  },
    ];

    const rankColors = ['#e6a800','#9ca3af','#c97c3a','#6366f1','#10b981'];

    function renderKPIs(range) {
      const d = DATA[range];
      document.getElementById('kpiRevenue').textContent = d.revenue;
      document.getElementById('kpiOrders').textContent  = d.orders;
      document.getElementById('kpiAvg').textContent     = d.avg;
      document.getElementById('kpiTop').textContent     = d.top;
    }

    function renderTopProducts(range) {
      const products = DATA[range].products;
      document.getElementById('topProductsBody').innerHTML = products.map(p => `
        <tr>
          <td>
            <span class="rank-badge" style="background:${rankColors[p.rank-1]}22;color:${rankColors[p.rank-1]};">
              ${p.rank}
            </span>
          </td>
          <td><strong>${p.name}</strong></td>
          <td style="color:var(--fg-muted);font-size:0.82rem;">${p.cat}</td>
          <td>${p.units}</td>
          <td><strong>${p.rev}</strong></td>
          <td style="min-width:100px;">
            <div style="font-size:0.82rem;font-weight:700;color:var(--fg-primary);">${p.pct}%</div>
            <div class="pct-bar"><div class="pct-fill" style="width:${p.pct}%;"></div></div>
          </td>
        </tr>
      `).join('');
    }

    function renderMonthlySummary() {
      document.getElementById('monthlySummaryBody').innerHTML = MONTHLY.map(m => `
        <tr>
          <td><strong>${m.month}</strong></td>
          <td>${m.orders}</td>
          <td><strong>${m.revenue}</strong></td>
          <td class="${m.pos ? 'growth-positive' : 'growth-negative'}">
            <i class="bi bi-arrow-${m.pos ? 'up' : 'down'}-short"></i>${m.growth}
          </td>
        </tr>
      `).join('');
    }

    document.addEventListener('DOMContentLoaded', function () {
      // Auth guard
      const user = FGAuth.UserStore.get();
      if (!user || user.role !== 'supplier') {
        window.location.href = '/login.html';
        return;
      }

      const fullName = (user.firstName || '') + ' ' + (user.lastName || '');
      document.getElementById('navUserName').textContent = fullName.trim() || user.email || 'Supplier';

      let currentRange = 'month';
      renderKPIs(currentRange);
      renderTopProducts(currentRange);
      renderMonthlySummary();

      // Range tabs
      document.querySelectorAll('.range-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
          document.querySelectorAll('.range-tab').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          currentRange = btn.dataset.range;
          renderKPIs(currentRange);
          renderTopProducts(currentRange);
        });
      });

      // Sidebar toggle
      const sidebar   = document.getElementById('supplierSidebar');
      const overlay   = document.getElementById('sidebarOverlay');
      const toggleBtn = document.getElementById('sidebarToggle');

      toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
      });

      overlay.addEventListener('click', function () {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
      });
    });
  </script>

</body>
</html>




