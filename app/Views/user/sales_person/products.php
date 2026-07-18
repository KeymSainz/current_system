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
  <title>Fix&amp;Go â€” Manage Products</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=5" />
  <link rel="stylesheet" href="/assets/css/supplier.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .sp-layout { display: flex; min-height: calc(100vh - 68px); }
    .sp-sidebar { width: 240px; flex-shrink: 0; background: var(--fg-card-bg); border-right: 1px solid var(--fg-border); padding: 1.5rem 0; position: sticky; top: 68px; height: calc(100vh - 68px); overflow-y: auto; }
    .sidebar-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--fg-muted); padding: 0 1.25rem; margin-bottom: 0.5rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.25rem; color: var(--fg-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.2s; }
    .sidebar-nav a:hover { color: var(--fg-primary); background: rgba(230,168,0,0.07); border-left-color: var(--fg-primary); }
    .sidebar-nav a.active { color: var(--fg-primary); background: rgba(230,168,0,0.1); border-left-color: var(--fg-primary); font-weight: 700; }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }
    .sp-main { flex: 1; padding: 2rem; min-width: 0; }
    .page-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem; }
    .page-header h2 { font-size: 1.6rem; font-weight: 800; color: var(--fg-text); margin: 0; }
    .page-header p { color: var(--fg-muted); margin: 0; font-size: 0.88rem; }
    .section-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; overflow: hidden; margin-bottom: 1.5rem; }
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.84rem; }
    .data-table thead th { background: var(--fg-primary); color: #fff; padding: 0.7rem 1rem; text-align: left; font-weight: 700; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.6px; white-space: nowrap; }
    .data-table tbody td { padding: 0.7rem 1rem; border-bottom: 1px solid var(--fg-border); color: var(--fg-text); vertical-align: middle; }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(230,168,0,0.03); }
    .badge-status { display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .badge-active { background: rgba(40,167,69,0.12); color: #28A745; }
    .badge-low    { background: rgba(230,168,0,0.12); color: #c98f00; }
    .badge-oos    { background: rgba(220,53,69,0.12); color: #dc3545; }
    .empty-state { text-align: center; padding: 3rem 2rem; color: var(--fg-muted); }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; opacity: 0.4; }
    .alert-bar { padding: 0.75rem 1.25rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem; }
    .alert-success { background: rgba(40,167,69,0.12); color: #28A745; border: 1px solid rgba(40,167,69,0.25); }
    .alert-danger  { background: rgba(220,53,69,0.12); color: #dc3545; border: 1px solid rgba(220,53,69,0.25); }
    .alert-info    { background: rgba(59,130,246,0.08); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
    .sidebar-toggle { display: none; background: none; border: 1.5px solid var(--fg-border); border-radius: 8px; padding: 0.3rem 0.6rem; color: var(--fg-text); cursor: pointer; font-size: 1.1rem; }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-overlay.open { display: block; }
    .stats-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; padding: 1rem; display: flex; align-items: center; gap: 0.85rem; }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.7rem; color: var(--fg-muted); font-weight: 600; margin-top: 0.15rem; }
    .toolbar { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 12px; padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; }
    .search-input { padding: 0.45rem 0.9rem; border-radius: 8px; border: 1.5px solid var(--fg-border); background: var(--fg-bg); color: var(--fg-text); font-size: 0.82rem; outline: none; transition: border-color 0.2s; min-width: 200px; }
    .search-input:focus { border-color: var(--fg-primary); }
    .product-img { width: 52px; height: 52px; border-radius: 10px; object-fit: cover; background: var(--fg-bg); }
    .product-img-ph { width: 52px; height: 52px; border-radius: 10px; background: var(--fg-bg); border: 1px solid var(--fg-border); display: flex; align-items: center; justify-content: center; color: var(--fg-muted); font-size: 1.2rem; }
    .btn-remove { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.85rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer; border: 1.5px solid #dc3545; color: #dc3545; background: rgba(220,53,69,0.08); transition: all 0.2s; }
    .btn-remove:hover { background: #dc3545; color: #fff; }
    @media (max-width: 992px) { .stats-row { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }
      .sp-sidebar { position: fixed; top: 68px; left: 0; z-index: 200; transform: translateX(-100%); height: calc(100vh - 68px); box-shadow: 4px 0 20px rgba(0,0,0,0.15); transition: transform 0.3s; }
      .sp-sidebar.open { transform: translateX(0); }
      .sp-main { padding: 1.25rem; }
    }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>
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
    <aside class="sp-sidebar" id="spSidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php" class="active"><i class="bi bi-shop"></i> Manage Products</a></li>
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Customer Orders</a></li>
        <li><a href="inventory.php"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>

    <main class="sp-main">
      <div class="page-header">
        <div>
          <h2><i class="bi bi-shop" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Manage Products</h2>
          <p>Products currently displayed to customers on the shop</p>
        </div>
        <a href="inventory.php" class="btn-primary-custom" style="text-decoration:none;">
          <i class="bi bi-clipboard-data"></i> Go to Inventory
        </a>
      </div>

      <div id="alertBox" style="display:none;"></div>

      <div class="alert-bar alert-info">
        <i class="bi bi-info-circle-fill"></i>
        These products are <strong>visible to customers</strong> on the shop. Go to <a href="inventory.php" style="color:#3b82f6;font-weight:700;">Inventory</a> to add more products to display.
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-shop"></i></div>
          <div><div class="stat-value" style="color:#28A745;" id="statDisplayed">0</div><div class="stat-label">Displayed to Customers</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(230,168,0,0.12);color:#c98f00;"><i class="bi bi-exclamation-circle"></i></div>
          <div><div class="stat-value" style="color:#c98f00;" id="statLow">0</div><div class="stat-label">Low Stock</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(220,53,69,0.12);color:#dc3545;"><i class="bi bi-x-circle"></i></div>
          <div><div class="stat-value" style="color:#dc3545;" id="statOos">0</div><div class="stat-label">Out of Stock</div></div>
        </div>
      </div>

      <div class="toolbar">
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
          <button class="btn-filter active" data-filter="all">All</button>
          <button class="btn-filter" data-filter="instock">In Stock</button>
          <button class="btn-filter" data-filter="low">Low Stock</button>
          <button class="btn-filter" data-filter="oos">Out of Stock</button>
        </div>
        <input type="text" class="search-input" id="searchInput" placeholder="Search products...">
      </div>

      <div class="section-card">
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th style="text-align:right;">Price</th>
                <th style="text-align:center;">Stock</th>
                <th>Stock Status</th>
                <th style="text-align:center;">Action</th>
              </tr>
            </thead>
            <tbody id="productTableBody">
              <tr><td colspan="8"><div class="empty-state">
                <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.75rem;"></div>
                <p>Loading...</p>
              </div></td></tr>
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
  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'sales_person') { window.location.href = '/login.html'; return; }

    document.getElementById('navUserName').textContent = ((user.firstName || '') + ' ' + (user.lastName || '')).trim() || user.email;

    const sidebar = document.getElementById('spSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    let allItems = [];
    let currentFilter = 'all';

    loadProducts();

    function loadProducts() {
      fetch('/api/sales/inventory?action=displayed')
        .then(r => r.json())
        .then(d => {
          if (!d.success) throw new Error(d.message);
          allItems = d.items || [];
          renderStats();
          renderTable();
        })
        .catch(() => {
          document.getElementById('productTableBody').innerHTML =
            '<tr><td colspan="8"><div class="empty-state"><i class="bi bi-shop"></i><p>No products on display yet.<br><a href="inventory.php" style="color:var(--fg-primary);font-weight:700;">Go to Inventory</a> to add products.</p></div></td></tr>';
        });
    }

    function renderStats() {
      document.getElementById('statDisplayed').textContent = allItems.length;
      document.getElementById('statLow').textContent       = allItems.filter(i => parseInt(i.quantity) > 0 && parseInt(i.quantity) <= 10).length;
      document.getElementById('statOos').textContent       = allItems.filter(i => parseInt(i.quantity) === 0).length;
    }

    function renderTable() {
      const q = document.getElementById('searchInput').value.toLowerCase();
      let filtered = allItems;
      if (currentFilter === 'instock') filtered = filtered.filter(i => parseInt(i.quantity) > 10);
      if (currentFilter === 'low')     filtered = filtered.filter(i => parseInt(i.quantity) > 0 && parseInt(i.quantity) <= 10);
      if (currentFilter === 'oos')     filtered = filtered.filter(i => parseInt(i.quantity) === 0);
      if (q) filtered = filtered.filter(i => (i.name + (i.category || '') + (i.brand || '')).toLowerCase().includes(q));

      const tbody = document.getElementById('productTableBody');
      if (!filtered.length) {
        tbody.innerHTML = '<tr><td colspan="8"><div class="empty-state"><i class="bi bi-shop"></i><p>No displayed products found.<br><a href="inventory.php" style="color:var(--fg-primary);font-weight:700;">Go to Inventory</a> to add products to display.</p></div></td></tr>';
        return;
      }

      tbody.innerHTML = filtered.map(item => {
        const qty = parseInt(item.quantity || 0);
        const img = item.image_path
          ? `<img class="product-img" src="../../../${esc(item.image_path)}" alt="" onerror="this.outerHTML='<div class=\\'product-img-ph\\'><i class=\\'bi bi-image\\'></i></div>'">`
          : `<div class="product-img-ph"><i class="bi bi-image"></i></div>`;
        const price = parseFloat(item.price || 0).toLocaleString('en-PH', {minimumFractionDigits:2});

        let stockBadge, stockColor;
        if (qty === 0)      { stockBadge = 'badge-oos';   stockColor = '#dc3545'; }
        else if (qty <= 10) { stockBadge = 'badge-low';   stockColor = '#c98f00'; }
        else                { stockBadge = 'badge-active'; stockColor = '#28A745'; }
        const stockLabel = qty === 0 ? 'Out of Stock' : qty <= 10 ? 'Low Stock' : 'In Stock';

        return `<tr id="prod-row-${item.id}">
          <td>${img}</td>
          <td style="font-weight:600;">${esc(item.name)}</td>
          <td><span style="background:rgba(230,168,0,0.1);color:var(--fg-primary);padding:0.15rem 0.55rem;border-radius:20px;font-size:0.72rem;font-weight:700;">${esc(item.category || 'â€”')}</span></td>
          <td style="color:var(--fg-muted);">${esc(item.brand || 'â€”')}</td>
          <td style="text-align:right;font-weight:700;">â‚±${price}</td>
          <td style="text-align:center;font-weight:700;color:${stockColor};">${qty}</td>
          <td><span class="badge-status ${stockBadge}">${stockLabel}</span></td>
          <td style="text-align:center;">
            <button class="btn-remove" onclick="removeFromDisplay(${item.id})">
              <i class="bi bi-eye-slash-fill"></i> Remove from Display
            </button>
          </td>
        </tr>`;
      }).join('');
    }

    window.removeFromDisplay = function(productId) {
      fetch('/api/sales/inventory', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'toggle_display', product_id: productId })
      })
        .then(r => r.json())
        .then(d => {
          if (!d.success) throw new Error(d.message);
          allItems = allItems.filter(i => i.id != productId);
          renderStats();
          renderTable();
          showAlert('success', 'Product removed from display.');
        })
        .catch(err => showAlert('danger', err.message));
    };

    document.querySelectorAll('.btn-filter').forEach(btn => {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        renderTable();
      });
    });
    document.getElementById('searchInput').addEventListener('input', renderTable);

    function showAlert(type, msg) {
      const box = document.getElementById('alertBox');
      box.style.display = 'flex';
      box.className = 'alert-bar alert-' + type;
      box.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill') + '"></i>' + esc(msg);
      setTimeout(() => { box.style.display = 'none'; }, 3500);
    }
  });

  function esc(s) { if (!s) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

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
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>


