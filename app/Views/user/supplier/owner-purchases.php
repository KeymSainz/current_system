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
  <title>Fix&Go — Owner Purchases</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=5" />
  <link rel="stylesheet" href="/assets/css/supplier.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    /* ── Layout ── */
    body { background: var(--fg-bg); margin: 0; }
    .supplier-layout { display: flex; min-height: calc(100vh - 68px); }

    /* ── Sidebar ── */
    .supplier-sidebar {
      width: 240px; flex-shrink: 0;
      background: var(--fg-card-bg);
      border-right: 1px solid var(--fg-border);
      padding: 1.5rem 0;
      position: sticky; top: 68px;
      height: calc(100vh - 68px);
      overflow-y: auto;
    }
    .sidebar-label {
      font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: 1px; color: var(--fg-muted);
      padding: 0 1.25rem; margin-bottom: 0.5rem;
    }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a {
      display: flex; align-items: center; gap: 0.75rem;
      padding: 0.65rem 1.25rem; color: var(--fg-muted);
      text-decoration: none; font-size: 0.88rem; font-weight: 500;
      border-left: 3px solid transparent; transition: all 0.2s;
    }
    .sidebar-nav a:hover { color: var(--fg-primary); background: rgba(230,168,0,0.07); border-left-color: var(--fg-primary); }
    .sidebar-nav a.active { color: var(--fg-primary); background: rgba(230,168,0,0.1); border-left-color: var(--fg-primary); font-weight: 700; }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }

    /* ── Main ── */
    .supplier-main { flex: 1; padding: 2rem; min-width: 0; }

    /* ── Page header ── */
    .page-header {
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;
    }
    .page-header h2 { font-size: 1.6rem; font-weight: 800; color: var(--fg-text); margin: 0; }
    .page-header p  { color: var(--fg-muted); margin: 0; font-size: 0.88rem; }

    /* ── Stat cards ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem; margin-bottom: 1.5rem;
    }
    .stat-card {
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 14px; padding: 1.25rem 1rem;
      display: flex; align-items: center; gap: 1rem;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
    .stat-icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.3rem; flex-shrink: 0;
    }
    .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.72rem; color: var(--fg-muted); font-weight: 600; margin-top: 0.2rem; }

    /* ── Toolbar ── */
    .toolbar {
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 12px; padding: 0.85rem 1.25rem;
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;
    }
    .toolbar-left { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .toolbar-right { display: flex; gap: 0.5rem; align-items: center; }
    .search-input {
      padding: 0.45rem 0.9rem; border-radius: 8px;
      border: 1.5px solid var(--fg-border);
      background: var(--fg-bg); color: var(--fg-text);
      font-size: 0.82rem; outline: none; transition: border-color 0.2s;
      min-width: 200px;
    }
    .search-input:focus { border-color: var(--fg-primary); }

    /* ── Table ── */
    .table-card {
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 14px; overflow: hidden;
    }
    .purchase-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .purchase-table thead th {
      background: var(--fg-primary); color: #fff;
      padding: 0.75rem 1rem; text-align: left;
      font-weight: 700; font-size: 0.72rem;
      text-transform: uppercase; letter-spacing: 0.6px;
      white-space: nowrap;
    }
    .purchase-table tbody td {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid var(--fg-border);
      color: var(--fg-text); vertical-align: middle;
    }
    .purchase-table tbody tr:last-child td { border-bottom: none; }
    .purchase-table tbody tr:hover { background: rgba(230,168,0,0.03); }

    /* ── Product image ── */
    .product-img {
      width: 50px; height: 50px; object-fit: cover;
      border-radius: 8px; border: 1px solid var(--fg-border);
    }

    /* ── Empty state ── */
    .empty-state {
      text-align: center; padding: 4rem 2rem; color: var(--fg-muted);
    }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.4; }
    .empty-state p { font-size: 0.9rem; margin: 0; }

    /* ── Alert ── */
    .alert-bar {
      padding: 0.75rem 1.25rem; border-radius: 10px;
      font-size: 0.85rem; font-weight: 600;
      display: flex; align-items: center; gap: 0.6rem;
      margin-bottom: 1rem; animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
    .alert-success { background: rgba(40,167,69,0.12); color: #28A745; border: 1px solid rgba(40,167,69,0.25); }
    .alert-danger  { background: rgba(220,53,69,0.12);  color: #dc3545; border: 1px solid rgba(220,53,69,0.25); }

    /* ── Sidebar toggle (mobile) ── */
    .sidebar-toggle {
      display: none; background: none;
      border: 1.5px solid var(--fg-border); border-radius: 8px;
      padding: 0.3rem 0.6rem; color: var(--fg-text);
      cursor: pointer; font-size: 1.1rem;
    }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-overlay.open { display: block; }

    @media (max-width: 992px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }
      .supplier-sidebar {
        position: fixed; top: 68px; left: 0; z-index: 200;
        transform: translateX(-100%); height: calc(100vh - 68px);
        box-shadow: 4px 0 20px rgba(0,0,0,0.15); transition: transform 0.3s;
      }
      .supplier-sidebar.open { transform: translateX(0); }
      .supplier-main { padding: 1.25rem; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
      .stats-grid { grid-template-columns: 1fr 1fr; }
      .page-header { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

  <!-- ── Navbar ── -->
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&Go"
             style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge supplier">📦 Supplier</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
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
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="supplier-layout">

    <!-- ── Sidebar ── -->
    <aside class="supplier-sidebar" id="supplierSidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
        <li><a href="owner-purchases.php" class="active"><i class="bi bi-cart-check"></i> Owner Purchases</a></li>
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Orders</a></li>
        <li><a href="deliveries.php"><i class="bi bi-truck"></i> Deliveries</a></li>
        <li><a href="tech-requests.php"><i class="bi bi-tools"></i> Tech Requests</a></li>
        <li><a href="tech-orders.php"><i class="bi bi-bag-check"></i> Tech Orders</a></li>
        <li><a href="sales-report.php"><i class="bi bi-bar-chart-line"></i> Sales Report</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>

    <!-- ── Main Content ── -->
    <main class="supplier-main">

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h2>Owner Purchases</h2>
          <p>Products purchased by shop owners from your inventory</p>
        </div>
      </div>

      <!-- Alert -->
      <div id="alertBox" style="display:none;"></div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981;"><i class="bi bi-cart-check"></i></div>
          <div>
            <div class="stat-value" style="color:#10b981;" id="statTotalSales">0</div>
            <div class="stat-label">Total Sales</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="bi bi-people"></i></div>
          <div>
            <div class="stat-value" style="color:#3b82f6;" id="statUniqueOwners">0</div>
            <div class="stat-label">Unique Owners</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(230,168,0,0.12);color:#e6a800;"><i class="bi bi-currency-exchange"></i></div>
          <div>
            <div class="stat-value" style="color:#e6a800;" id="statTotalRevenue">₱0</div>
            <div class="stat-label">Total Revenue</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(108,117,125,0.12);color:#6C757D;"><i class="bi bi-box"></i></div>
          <div>
            <div class="stat-value" style="color:#6C757D;" id="statItemsSold">0</div>
            <div class="stat-label">Items Sold</div>
          </div>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="toolbar">
        <div class="toolbar-left"></div>
        <div class="toolbar-right">
          <input type="text" class="search-input" id="searchInput" placeholder="🔍  Search purchases…">
        </div>
      </div>

      <!-- Table -->
      <div class="table-card">
        <div style="overflow-x:auto;">
          <table class="purchase-table">
            <thead>
              <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Category</th>
                <th>Owner</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">Unit Price</th>
                <th style="text-align:right;">Total</th>
                <th>Payment Ref</th>
                <th>Purchased</th>
              </tr>
            </thead>
            <tbody id="purchaseTableBody">
              <tr>
                <td colspan="9">
                  <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No purchases yet. Owners will see your products once you submit them.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script>
    // ── Auth guard ──
    document.addEventListener('DOMContentLoaded', function() {
      const user = FGAuth.UserStore.get();
      if (!user || user.role !== 'supplier') {
        window.location.href = '/login.html';
        return;
      }

      const fullName = (user.firstName || '') + ' ' + (user.lastName || '');
      document.getElementById('navUserName').textContent = fullName.trim() || user.email || 'Supplier';

      // Sidebar toggle
      const sidebar   = document.getElementById('supplierSidebar');
      const overlay   = document.getElementById('sidebarOverlay');
      const toggleBtn = document.getElementById('sidebarToggle');
      if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
          sidebar.classList.toggle('open');
          overlay.classList.toggle('open');
        });
        overlay.addEventListener('click', function () {
          sidebar.classList.remove('open');
          overlay.classList.remove('open');
        });
      }

      // Load data
      loadStats();
      loadPurchases();
    });

    // ── Load statistics ──
    function loadStats() {
      fetch('/api/supplier/sales?action=stats', {
        method: 'GET',
        credentials: 'include',
      })
      .then(r => r.json())
      .then(data => {
        if (data.success && data.stats) {
          document.getElementById('statTotalSales').textContent = data.stats.total_sales || 0;
          document.getElementById('statUniqueOwners').textContent = data.stats.unique_owners || 0;
          document.getElementById('statTotalRevenue').textContent = '₱' + parseFloat(data.stats.total_revenue || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
          document.getElementById('statItemsSold').textContent = data.stats.total_items_sold || 0;
        }
      })
      .catch(err => console.error('Failed to load stats:', err));
    }

    // ── Load purchases ──
    let allPurchases = [];

    function loadPurchases() {
      fetch('/api/supplier/sales?action=purchases', {
        method: 'GET',
        credentials: 'include',
      })
      .then(r => r.json())
      .then(data => {
        if (data.setup_required) {
          // Show setup message
          const tbody = document.getElementById('purchaseTableBody');
          tbody.innerHTML = `
            <tr>
              <td colspan="9">
                <div class="empty-state">
                  <i class="bi bi-exclamation-triangle" style="color: #e6a800;"></i>
                  <p style="margin-bottom: 1rem;"><strong>Setup Required</strong></p>
                  <p>${data.message}</p>
                  <p style="margin-top: 1rem;">
                    <a href="../../../setup-inventory.php" style="color: var(--fg-primary); font-weight: 600; text-decoration: underline;">
                      Click here to complete setup
                    </a>
                  </p>
                </div>
              </td>
            </tr>`;
          return;
        }
        
        if (data.success) {
          allPurchases = data.purchases || [];
          renderPurchases(allPurchases);
        } else {
          showAlert(data.message || 'Failed to load purchases.', 'danger');
        }
      })
      .catch(err => {
        console.error('Failed to load purchases:', err);
        showAlert('Network error. Please check if the database table exists.', 'danger');
        const tbody = document.getElementById('purchaseTableBody');
        tbody.innerHTML = `
          <tr>
            <td colspan="9">
              <div class="empty-state">
                <i class="bi bi-exclamation-triangle" style="color: #dc3545;"></i>
                <p style="margin-bottom: 1rem;"><strong>Network Error</strong></p>
                <p>Unable to load purchases. The database table may not exist yet.</p>
                <p style="margin-top: 1rem;">
                  <a href="../../../setup-inventory.php" style="color: var(--fg-primary); font-weight: 600; text-decoration: underline;">
                    Click here to complete setup
                  </a>
                </p>
              </div>
            </td>
          </tr>`;
      });
    }

    function renderPurchases(purchases) {
      const tbody = document.getElementById('purchaseTableBody');
      
      if (!purchases || purchases.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="9">
              <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>No purchases yet. Owners will see your products once you submit them.</p>
              </div>
            </td>
          </tr>`;
        return;
      }

      tbody.innerHTML = purchases.map(p => {
        const imgSrc = p.image_path 
          ? `../../../${p.image_path}` 
          : '/assets/images/product-placeholder.svg';
        
        const productName = p.brand ? `${p.brand} — ${p.item_description}` : p.item_description;
        const purchasedDate = new Date(p.purchased_at).toLocaleDateString('en-PH', {
          year: 'numeric', month: 'short', day: 'numeric'
        });

        return `
          <tr>
            <td><img src="${imgSrc}" alt="Product" class="product-img" onerror="this.src='/assets/images/product-placeholder.svg'"></td>
            <td style="max-width:250px;"><strong>${escapeHtml(productName)}</strong></td>
            <td>${escapeHtml(p.category)}</td>
            <td>
              <div style="font-weight:600;">${escapeHtml(p.owner_name)}</div>
              <div style="font-size:0.75rem;color:var(--fg-muted);">${escapeHtml(p.owner_email)}</div>
            </td>
            <td style="text-align:center;font-weight:700;">${p.qty}</td>
            <td style="text-align:right;font-weight:600;">₱${parseFloat(p.unit_price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
            <td style="text-align:right;font-weight:700;color:var(--fg-primary);">₱${parseFloat(p.total_price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
            <td style="font-family:monospace;font-size:0.8rem;">${escapeHtml(p.payment_reference)}</td>
            <td style="font-size:0.8rem;">${purchasedDate}</td>
          </tr>
        `;
      }).join('');
    }

    // ── Search ──
    document.getElementById('searchInput').addEventListener('input', function(e) {
      const query = e.target.value.toLowerCase().trim();
      if (!query) {
        renderPurchases(allPurchases);
        return;
      }

      const filtered = allPurchases.filter(p => {
        const productName = (p.brand ? p.brand + ' ' : '') + p.item_description;
        return productName.toLowerCase().includes(query) ||
               p.category.toLowerCase().includes(query) ||
               p.owner_name.toLowerCase().includes(query) ||
               p.owner_email.toLowerCase().includes(query) ||
               p.payment_reference.toLowerCase().includes(query);
      });

      renderPurchases(filtered);
    });

    // ── Helpers ──
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    function showAlert(message, type = 'success') {
      const alertBox = document.getElementById('alertBox');
      const icon = type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill';
      alertBox.innerHTML = `
        <div class="alert-${type}">
          <i class="bi bi-${icon}"></i>
          <span>${message}</span>
        </div>
      `;
      alertBox.style.display = 'block';
      setTimeout(() => { alertBox.style.display = 'none'; }, 5000);
    }
  </script>

</body>
</html>




