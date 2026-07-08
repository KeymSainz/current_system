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
  <title>Fix&amp;Go — Orders</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .supplier-layout { display: flex; min-height: calc(100vh - 68px); }
    .supplier-sidebar { width: 240px; background: var(--fg-card-bg); border-right: 1px solid var(--fg-border); padding: 1.5rem 0; flex-shrink: 0; position: sticky; top: 68px; height: calc(100vh - 68px); overflow-y: auto; }
    .sidebar-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--fg-muted); padding: 0 1.25rem; margin-bottom: 0.5rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.25rem; color: var(--fg-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.2s; }
    .sidebar-nav a:hover { color: var(--fg-primary); background: rgba(230,168,0,0.07); border-left-color: var(--fg-primary); }
    .sidebar-nav a.active { color: var(--fg-primary); background: rgba(230,168,0,0.1); border-left-color: var(--fg-primary); font-weight: 700; }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }
    .supplier-main { flex: 1; padding: 2rem; min-width: 0; }
    /* Page header */
    .page-header { margin-bottom: 1.5rem; }
    .page-header h2 { font-size: 1.5rem; font-weight: 800; color: var(--fg-text); margin: 0 0 0.25rem; }
    .page-header p { color: var(--fg-muted); margin: 0; font-size: 0.88rem; }
    /* Stats */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-mini { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 12px; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; }
    .stat-mini-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .stat-mini-val { font-size: 1.4rem; font-weight: 800; line-height: 1; }
    .stat-mini-label { font-size: 0.7rem; color: var(--fg-muted); font-weight: 600; margin-top: 0.15rem; }
    /* Toolbar */
    .toolbar { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 12px; padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; }
    .btn-filter { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.85rem; border-radius: 20px; border: 1.5px solid var(--fg-border); background: transparent; color: var(--fg-muted); font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .btn-filter:hover { border-color: var(--fg-primary); color: var(--fg-primary); }
    .btn-filter.active { background: var(--fg-primary); border-color: var(--fg-primary); color: #fff; }
    .search-input { padding: 0.45rem 0.9rem; border-radius: 8px; border: 1.5px solid var(--fg-border); background: var(--fg-bg); color: var(--fg-text); font-size: 0.82rem; outline: none; transition: border-color 0.2s; min-width: 200px; }
    .search-input:focus { border-color: var(--fg-primary); }
    /* Submission cards */
    .sub-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; margin-bottom: 1rem; overflow: hidden; transition: box-shadow 0.2s; }
    .sub-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
    .sub-card-head { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--fg-border); background: rgba(230,168,0,0.03); flex-wrap: wrap; gap: 0.5rem; }
    .sub-card-id { font-family: monospace; font-size: 0.85rem; font-weight: 800; color: var(--fg-primary); }
    .sub-card-date { font-size: 0.75rem; color: var(--fg-muted); }
    .sub-card-body { padding: 1rem 1.25rem; }
    .sub-items-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem; }
    .sub-item { display: flex; gap: 0.75rem; align-items: center; background: var(--fg-bg); border: 1px solid var(--fg-border); border-radius: 10px; padding: 0.65rem 0.85rem; }
    .sub-item-img { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid var(--fg-border); flex-shrink: 0; }
    .sub-item-img-ph { width: 48px; height: 48px; border-radius: 8px; background: var(--fg-card-bg); border: 1px solid var(--fg-border); display: flex; align-items: center; justify-content: center; color: var(--fg-muted); font-size: 1.1rem; flex-shrink: 0; }
    .sub-item-name { font-size: 0.82rem; font-weight: 700; color: var(--fg-text); line-height: 1.3; }
    .sub-item-meta { font-size: 0.72rem; color: var(--fg-muted); margin-top: 0.1rem; }
    .sub-card-foot { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1.25rem; border-top: 1px solid var(--fg-border); flex-wrap: wrap; gap: 0.5rem; }
    .owner-info { display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: var(--fg-muted); }
    .owner-info strong { color: var(--fg-text); }
    /* Badges */
    .badge-status { display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .badge-pending      { background: rgba(230,168,0,0.12);   color: #c98f00; }
    .badge-acknowledged { background: rgba(40,167,69,0.12);   color: #28A745; }
    .badge-rejected     { background: rgba(220,53,69,0.12);   color: #dc3545; }
    /* Empty */
    .empty-state { text-align: center; padding: 4rem 2rem; color: var(--fg-muted); }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.3; }
    /* Sidebar toggle */
    .sidebar-toggle { display: none; background: none; border: 1.5px solid var(--fg-border); border-radius: 8px; padding: 0.3rem 0.6rem; color: var(--fg-text); cursor: pointer; font-size: 1.1rem; }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-overlay.open { display: block; }
    @media (max-width: 992px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }
      .supplier-sidebar { position: fixed; top: 68px; left: 0; z-index: 200; transform: translateX(-100%); height: calc(100vh - 68px); box-shadow: 4px 0 20px rgba(0,0,0,0.15); transition: transform 0.3s; }
      .supplier-sidebar.open { transform: translateX(0); }
      .supplier-main { padding: 1.25rem; }
      .sub-items-grid { grid-template-columns: 1fr; }
    }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge supplier">📦 Supplier</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
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
      <a href="../../../dashboard.php" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="supplier-layout">
    <!-- Sidebar -->
    <aside class="supplier-sidebar" id="supplierSidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
        <li><a href="owner-purchases.php"><i class="bi bi-cart-check"></i> Owner Purchases</a></li>
        <li><a href="orders.php" class="active"><i class="bi bi-cart3"></i> Orders</a></li>
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
      <div class="page-header">
        <h2><i class="bi bi-cart3" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Product Submissions</h2>
        <p>Track all product batches you've submitted to shop owners</p>
      </div>

      <!-- Stats -->
      <div class="stats-row">
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(230,168,0,0.12);color:#c98f00;"><i class="bi bi-hourglass-split"></i></div>
          <div><div class="stat-mini-val" style="color:#c98f00;" id="statPending">—</div><div class="stat-mini-label">Pending</div></div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-check-circle-fill"></i></div>
          <div><div class="stat-mini-val" style="color:#28A745;" id="statAccepted">—</div><div class="stat-mini-label">Accepted</div></div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(220,53,69,0.12);color:#dc3545;"><i class="bi bi-x-circle-fill"></i></div>
          <div><div class="stat-mini-val" style="color:#dc3545;" id="statRejected">—</div><div class="stat-mini-label">Rejected</div></div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="bi bi-layers-fill"></i></div>
          <div><div class="stat-mini-val" style="color:#3b82f6;" id="statTotal">—</div><div class="stat-mini-label">Total Batches</div></div>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="toolbar">
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
          <button class="btn-filter active" data-filter="all">All</button>
          <button class="btn-filter" data-filter="pending">Pending</button>
          <button class="btn-filter" data-filter="acknowledged">Accepted</button>
          <button class="btn-filter" data-filter="rejected">Rejected</button>
        </div>
        <input type="text" class="search-input" id="searchInput" placeholder="Search by product or owner…">
      </div>

      <div id="alertBox" style="display:none;margin-bottom:1rem;"></div>

      <!-- Submissions list -->
      <div id="submissionsList">
        <div class="empty-state">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto;"></div>
          <p style="margin-top:1rem;">Loading orders…</p>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script src="../../../assets/js/session-timeout.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'supplier') { window.location.href = '../../../login.html'; return; }
    document.getElementById('navUserName').textContent = ((user.firstName||'') + ' ' + (user.lastName||'')).trim() || user.email;

    const sidebar = document.getElementById('supplierSidebar'), overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    document.querySelectorAll('.btn-filter').forEach(btn => {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        renderSubmissions();
      });
    });
    document.getElementById('searchInput').addEventListener('input', renderSubmissions);

    loadSubmissions();
  });

  let allSubmissions = [];
  let currentFilter = 'all';

  function loadSubmissions() {
    document.getElementById('submissionsList').innerHTML =
      '<div class="empty-state"><div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto;"></div><p style="margin-top:1rem;">Loading…</p></div>';

    fetch('../../../backend/supplier_orders.php?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) throw new Error(d.message);
        allSubmissions = d.submissions || [];
        updateStats();
        renderSubmissions();
      })
      .catch(() => {
        document.getElementById('submissionsList').innerHTML =
          '<div class="empty-state"><i class="bi bi-inbox"></i><p>Could not load orders.</p></div>';
      });
  }

  function updateStats() {
    const pending  = allSubmissions.filter(s => s.status === 'pending').length;
    const accepted = allSubmissions.filter(s => s.status === 'acknowledged').length;
    const rejected = allSubmissions.filter(s => s.status === 'rejected').length;
    document.getElementById('statPending').textContent  = pending;
    document.getElementById('statAccepted').textContent = accepted;
    document.getElementById('statRejected').textContent = rejected;
    document.getElementById('statTotal').textContent    = allSubmissions.length;
  }

  function renderSubmissions() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    let filtered = allSubmissions;

    if (currentFilter !== 'all') {
      filtered = filtered.filter(s => s.status === currentFilter);
    }
    if (q) {
      filtered = filtered.filter(s =>
        (s.owner_name + s.owner_email + (s.items || []).map(i => i.item_description + i.category).join(' ')).toLowerCase().includes(q)
      );
    }

    const list = document.getElementById('submissionsList');
    if (!filtered.length) {
      list.innerHTML = '<div class="empty-state"><i class="bi bi-inbox"></i><p>No submissions found.</p></div>';
      return;
    }

    const badgeMap = {
      pending:      'badge-pending',
      acknowledged: 'badge-acknowledged',
      rejected:     'badge-rejected',
    };
    const labelMap = {
      pending:      'Pending Review',
      acknowledged: 'Accepted',
      rejected:     'Rejected',
    };

    list.innerHTML = filtered.map(s => {
      const date  = new Date(s.created_at).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
      const items = s.items || [];
      const cls   = badgeMap[s.status] || 'badge-pending';
      const lbl   = labelMap[s.status] || s.status;

      const itemsHtml = items.length
        ? `<div class="sub-items-grid">${items.map(i => {
            const img = i.image_path
              ? `<img src="../../../${esc(i.image_path)}" class="sub-item-img" onerror="this.outerHTML='<div class=\\'sub-item-img-ph\\'><i class=\\'bi bi-box-seam\\'></i></div>'">`
              : `<div class="sub-item-img-ph"><i class="bi bi-box-seam"></i></div>`;
            const price = parseFloat(i.srp || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
            return `<div class="sub-item">
              ${img}
              <div>
                <div class="sub-item-name">${esc(i.item_description)}</div>
                <div class="sub-item-meta">${esc(i.category)}${i.brand ? ' · ' + esc(i.brand) : ''}</div>
                <div class="sub-item-meta" style="color:var(--fg-primary);font-weight:700;">₱${price} · ${i.qty} pcs</div>
              </div>
            </div>`;
          }).join('')}</div>`
        : `<p style="color:var(--fg-muted);font-size:0.85rem;margin:0;">No items in this batch.</p>`;

      return `
        <div class="sub-card">
          <div class="sub-card-head">
            <div style="display:flex;align-items:center;gap:0.75rem;">
              <span class="sub-card-id">Batch #${s.id}</span>
              <span class="badge-status ${cls}">${lbl}</span>
              <span style="font-size:0.75rem;color:var(--fg-muted);">${items.length} item${items.length !== 1 ? 's' : ''}</span>
            </div>
            <span class="sub-card-date">${date}</span>
          </div>
          <div class="sub-card-body">${itemsHtml}</div>
          <div class="sub-card-foot">
            <div class="owner-info">
              <i class="bi bi-shop-window" style="color:var(--fg-primary);"></i>
              Sent to: <strong>${esc(s.owner_name || 'Owner')}</strong>
              <span style="font-size:0.75rem;">(${esc(s.owner_email || '')})</span>
            </div>
            ${s.acknowledged_at
              ? `<span style="font-size:0.75rem;color:var(--fg-muted);"><i class="bi bi-clock"></i> Reviewed: ${new Date(s.acknowledged_at).toLocaleDateString('en-PH')}</span>`
              : ''}
          </div>
        </div>`;
    }).join('');
  }

  function esc(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  function toggleNotifDropdown() {
    const d = document.getElementById('notifDropdown');
    if (d.style.display === 'none' || !d.style.display) {
      d.style.display = 'block';
      setTimeout(() => document.addEventListener('click', closeNotifOutside), 0);
    } else {
      d.style.display = 'none';
      document.removeEventListener('click', closeNotifOutside);
    }
  }
  function closeNotifOutside(e) {
    if (!document.getElementById('notifWrap').contains(e.target)) {
      document.getElementById('notifDropdown').style.display = 'none';
      document.removeEventListener('click', closeNotifOutside);
    }
  }
  function markAllRead() {}
  </script>

</body>
</html>




