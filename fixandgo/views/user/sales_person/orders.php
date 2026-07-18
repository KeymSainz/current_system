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
  <title>Fix&amp;Go â€” Customer Orders</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body{background:var(--fg-bg);}
    .sp-layout{display:flex;min-height:calc(100vh - 68px);}
    .sp-sidebar{width:240px;flex-shrink:0;background:var(--fg-card-bg);border-right:1px solid var(--fg-border);padding:1.5rem 0;position:sticky;top:68px;height:calc(100vh - 68px);overflow-y:auto;}
    .sidebar-label{font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);padding:0 1.25rem;margin-bottom:0.5rem;}
    .sidebar-nav{list-style:none;padding:0;margin:0;}
    .sidebar-nav a{display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1.25rem;color:var(--fg-muted);text-decoration:none;font-size:0.88rem;font-weight:500;border-left:3px solid transparent;transition:all 0.2s;}
    .sidebar-nav a:hover{color:var(--fg-primary);background:rgba(230,168,0,0.07);border-left-color:var(--fg-primary);}
    .sidebar-nav a.active{color:var(--fg-primary);background:rgba(230,168,0,0.1);border-left-color:var(--fg-primary);font-weight:700;}
    .sidebar-nav a i{font-size:1rem;width:20px;text-align:center;}
    .sp-main{flex:1;padding:2rem;min-width:0;}
    .page-header{margin-bottom:1.5rem;}
    .page-header h2{font-size:1.5rem;font-weight:800;color:var(--fg-text);margin:0 0 0.25rem;}
    .page-header p{color:var(--fg-muted);margin:0;font-size:0.88rem;}
    /* Stats row */
    .stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;}
    .stat-mini{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:12px;padding:1rem;display:flex;align-items:center;gap:0.75rem;}
    .stat-mini-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
    .stat-mini-val{font-size:1.4rem;font-weight:800;line-height:1;}
    .stat-mini-label{font-size:0.7rem;color:var(--fg-muted);font-weight:600;margin-top:0.15rem;}
    /* Toolbar */
    .toolbar{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:12px;padding:0.85rem 1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;margin-bottom:1rem;}
    .btn-filter{display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.85rem;border-radius:20px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.2s;}
    .btn-filter:hover{border-color:var(--fg-primary);color:var(--fg-primary);}
    .btn-filter.active{background:var(--fg-primary);border-color:var(--fg-primary);color:#fff;}
    .search-input{padding:0.45rem 0.9rem;border-radius:8px;border:1.5px solid var(--fg-border);background:var(--fg-bg);color:var(--fg-text);font-size:0.82rem;outline:none;transition:border-color 0.2s;min-width:200px;}
    .search-input:focus{border-color:var(--fg-primary);}
    /* Order cards */
    .order-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;margin-bottom:1rem;overflow:hidden;transition:box-shadow 0.2s;}
    .order-card:hover{box-shadow:0 4px 20px rgba(0,0,0,0.1);}
    .order-card-head{display:flex;align-items:center;justify-content:space-between;padding:0.75rem 1.25rem;border-bottom:1px solid var(--fg-border);background:rgba(230,168,0,0.03);flex-wrap:wrap;gap:0.5rem;}
    .order-card-id{font-family:monospace;font-size:0.85rem;font-weight:800;color:#3b82f6;}
    .order-card-date{font-size:0.75rem;color:var(--fg-muted);}
    .order-card-body{display:flex;gap:1rem;padding:1rem 1.25rem;align-items:flex-start;flex-wrap:wrap;}
    .order-prod-img{width:72px;height:72px;border-radius:8px;object-fit:cover;border:1px solid var(--fg-border);flex-shrink:0;}
    .order-prod-img-ph{width:72px;height:72px;border-radius:8px;background:var(--fg-bg);border:1px solid var(--fg-border);display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:1.6rem;flex-shrink:0;}
    .order-prod-info{flex:1;min-width:180px;}
    .order-prod-name{font-size:0.9rem;font-weight:700;color:var(--fg-text);margin-bottom:0.2rem;}
    .order-prod-meta{font-size:0.78rem;color:var(--fg-muted);margin-bottom:0.4rem;}
    .order-customer-box{flex:1;min-width:200px;background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;padding:0.75rem 1rem;}
    .order-customer-title{font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);margin-bottom:0.5rem;display:flex;align-items:center;gap:0.3rem;}
    .order-customer-name{font-size:0.88rem;font-weight:700;color:var(--fg-text);margin-bottom:0.2rem;}
    .order-customer-contact{font-size:0.78rem;color:var(--fg-muted);margin-bottom:0.3rem;}
    .order-address-line{font-size:0.78rem;color:var(--fg-text);line-height:1.5;}
    .order-card-foot{display:flex;align-items:center;justify-content:space-between;padding:0.75rem 1.25rem;border-top:1px solid var(--fg-border);flex-wrap:wrap;gap:0.5rem;}
    .order-total{font-size:0.85rem;color:var(--fg-muted);}
    .order-total strong{font-size:1rem;font-weight:800;color:var(--fg-primary);margin-left:0.35rem;}
    .order-actions{display:flex;gap:0.5rem;flex-wrap:wrap;}
    .btn-ship{padding:0.45rem 1rem;border-radius:8px;font-size:0.8rem;font-weight:700;cursor:pointer;border:1.5px solid rgba(59,130,246,0.5);color:#3b82f6;background:rgba(59,130,246,0.08);transition:all 0.15s;display:inline-flex;align-items:center;gap:0.3rem;}
    .btn-ship:hover{background:rgba(59,130,246,0.18);}
    .btn-complete{padding:0.45rem 1rem;border-radius:8px;font-size:0.8rem;font-weight:700;cursor:pointer;border:1.5px solid rgba(40,167,69,0.5);color:#28A745;background:rgba(40,167,69,0.08);transition:all 0.15s;display:inline-flex;align-items:center;gap:0.3rem;}
    .btn-complete:hover{background:rgba(40,167,69,0.18);}
    .btn-view{padding:0.45rem 1rem;border-radius:8px;font-size:0.8rem;font-weight:700;cursor:pointer;border:1.5px solid var(--fg-border);color:var(--fg-muted);background:transparent;transition:all 0.15s;display:inline-flex;align-items:center;gap:0.3rem;}
    .btn-view:hover{border-color:var(--fg-primary);color:var(--fg-primary);}
    /* Badges */
    .badge-status{display:inline-flex;align-items:center;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.7rem;font-weight:700;text-transform:uppercase;}
    .badge-pending{background:rgba(230,168,0,0.12);color:#c98f00;}
    .badge-processing{background:rgba(59,130,246,0.12);color:#3b82f6;}
    .badge-completed{background:rgba(40,167,69,0.12);color:#28A745;}
    .badge-cancelled{background:rgba(220,53,69,0.12);color:#dc3545;}
    /* Empty */
    .empty-state{text-align:center;padding:4rem 2rem;color:var(--fg-muted);}
    .empty-state i{font-size:3rem;display:block;margin-bottom:1rem;opacity:0.3;}
    /* Modal */
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center;padding:1rem;}
    .modal-overlay.open{display:flex;}
    .modal-box{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;box-shadow:0 24px 64px rgba(0,0,0,0.4);width:100%;max-width:580px;max-height:90vh;overflow-y:auto;animation:modalIn 0.25s cubic-bezier(0.16,1,0.3,1);}
    @keyframes modalIn{from{opacity:0;transform:scale(0.95) translateY(10px)}to{opacity:1;transform:scale(1) translateY(0)}}
    .modal-head{padding:1.25rem 1.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;}
    .modal-head h5{margin:0;font-weight:800;font-size:1rem;color:var(--fg-text);}
    .modal-body{padding:1.5rem;}
    .modal-foot{padding:1.25rem 1.5rem;border-top:1px solid var(--fg-border);display:flex;gap:0.75rem;justify-content:flex-end;}
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
    .detail-item label{font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);display:block;margin-bottom:0.2rem;}
    .detail-item span{font-size:0.88rem;color:var(--fg-text);font-weight:500;}
    .detail-address-box{background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;padding:1rem;margin-top:1rem;}
    .detail-address-box h6{font-size:0.78rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);margin:0 0 0.6rem;display:flex;align-items:center;gap:0.4rem;}
    /* Sidebar toggle */
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @media(max-width:992px){.stats-row{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .sp-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .sp-sidebar.open{transform:translateX(0);}
      .sp-main{padding:1.25rem;}
      .detail-grid{grid-template-columns:1fr;}
    }
    @keyframes spin{to{transform:rotate(360deg)}}
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
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
      <a href="../../../dashboard.php" class="btn btn-sm"
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
        <li><a href="products.php"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="orders.php" class="active"><i class="bi bi-cart3"></i> Customer Orders</a></li>
        <li><a href="inventory.php"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
        <li><a href="profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>

    <main class="sp-main">
      <div class="page-header">
        <h2><i class="bi bi-cart3" style="color:#3b82f6;margin-right:0.5rem;"></i>Customer Orders</h2>
        <p>Manage and ship customer orders for your products</p>
      </div>

      <!-- Stats row -->
      <div class="stats-row">
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(230,168,0,0.12);color:#c98f00;"><i class="bi bi-hourglass-split"></i></div>
          <div><div class="stat-mini-val" style="color:#c98f00;" id="statPending">â€”</div><div class="stat-mini-label">Pending</div></div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="bi bi-truck"></i></div>
          <div><div class="stat-mini-val" style="color:#3b82f6;" id="statProcessing">â€”</div><div class="stat-mini-label">Shipped</div></div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-check-circle-fill"></i></div>
          <div><div class="stat-mini-val" style="color:#28A745;" id="statCompleted">â€”</div><div class="stat-mini-label">Completed</div></div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-currency-exchange"></i></div>
          <div><div class="stat-mini-val" style="color:#28A745;" id="statRevenue">â€”</div><div class="stat-mini-label">Revenue</div></div>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="toolbar">
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
          <button class="btn-filter active" data-filter="all">All</button>
          <button class="btn-filter" data-filter="pending">Pending</button>
          <button class="btn-filter" data-filter="processing">Shipped</button>
          <button class="btn-filter" data-filter="completed">Completed</button>
          <button class="btn-filter" data-filter="cancelled">Cancelled</button>
        </div>
        <input type="text" class="search-input" id="searchInput" placeholder="Search by customer or productâ€¦">
      </div>

      <div id="alertBox" style="display:none;margin-bottom:1rem;"></div>

      <!-- Orders list -->
      <div id="ordersList">
        <div class="empty-state">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto;"></div>
          <p style="margin-top:1rem;">Loading ordersâ€¦</p>
        </div>
      </div>

    </main>
  </div>

  <!-- Order Detail Modal -->
  <div class="modal-overlay" id="detailOverlay">
    <div class="modal-box">
      <div class="modal-head">
        <h5><i class="bi bi-receipt" style="color:#3b82f6;margin-right:0.5rem;"></i>Order Details</h5>
        <button onclick="closeDetail()" style="width:30px;height:30px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body" id="detailBody">
        <p style="color:var(--fg-muted);">Loadingâ€¦</p>
      </div>
      <div class="modal-foot" id="detailFoot">
        <button onclick="closeDetail()" style="padding:0.6rem 1.25rem;border-radius:9px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-weight:700;cursor:pointer;">Close</button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'sales_person') { window.location.href = '../../../login.html'; return; }
    const fullName = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email;

    const sidebar = document.getElementById('spSidebar'), overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    document.querySelectorAll('.btn-filter').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        renderOrders();
      });
    });
    document.getElementById('searchInput').addEventListener('input', renderOrders);

    loadStats();
    loadOrders();
    loadUnreadMessageCount();
  });

  let allOrders = [];
  let currentFilter = 'all';

  function loadStats() {
    fetch('../../../backend/sales_orders.php?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        const s = d.stats;
        document.getElementById('statPending').textContent    = s.pending    || 0;
        document.getElementById('statProcessing').textContent = s.processing || 0;
        document.getElementById('statCompleted').textContent  = s.completed  || 0;
        const rev = parseFloat(s.total_revenue || 0);
        document.getElementById('statRevenue').textContent = 'â‚±' + rev.toLocaleString('en-PH', { minimumFractionDigits: 0 });
      }).catch(() => {});
  }

  function loadOrders() {
    document.getElementById('ordersList').innerHTML =
      '<div class="empty-state"><div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto;"></div><p style="margin-top:1rem;">Loading ordersâ€¦</p></div>';
    fetch('../../../backend/sales_orders.php?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) throw new Error(d.message);
        allOrders = d.orders || [];
        renderOrders();
      })
      .catch(() => {
        document.getElementById('ordersList').innerHTML =
          '<div class="empty-state"><i class="bi bi-inbox"></i><p>Could not load orders.</p></div>';
      });
  }

  function renderOrders() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    let filtered = allOrders;
    if (currentFilter !== 'all') filtered = filtered.filter(o => o.status === currentFilter);
    if (q) filtered = filtered.filter(o =>
      (o.id + (o.product_name||'') + (o.first_name||'') + (o.last_name||'') + (o.city||'')).toLowerCase().includes(q)
    );

    const list = document.getElementById('ordersList');
    if (!filtered.length) {
      list.innerHTML = '<div class="empty-state"><i class="bi bi-inbox"></i><p>No orders found.</p></div>';
      return;
    }

    const statusBadge = { pending:'badge-pending', processing:'badge-processing', completed:'badge-completed', cancelled:'badge-cancelled' };
    const statusLabel = { pending:'Pending', processing:'Shipped', completed:'Completed', cancelled:'Cancelled' };

    list.innerHTML = filtered.map(o => {
      const date  = new Date(o.created_at).toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' });
      const total = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
      const price = parseFloat(o.unit_price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
      const customer = esc((o.first_name||'') + ' ' + (o.last_name||'')).trim() || 'Unknown';

      const imgHtml = o.image_path
        ? `<img src="../../../${esc(o.image_path)}" class="order-prod-img" onerror="this.outerHTML='<div class=\\'order-prod-img-ph\\'><i class=\\'bi bi-box-seam\\'></i></div>'">`
        : `<div class="order-prod-img-ph"><i class="bi bi-box-seam"></i></div>`;

      // Build address string
      const addrParts = [o.address_line, o.barangay, o.city, o.province, o.zip_code].filter(Boolean);
      const addrStr = addrParts.length ? addrParts.map(esc).join(', ') : '<span style="color:#dc3545;">No address on file</span>';

      // Action buttons
      let actions = `<button class="btn-view" onclick="viewOrder(${o.id})"><i class="bi bi-eye"></i> View</button>`;
      actions += `<a href="messages.php?with=${o.customer_id}" class="btn-view" style="text-decoration:none;"><i class="bi bi-chat-dots"></i> Message</a>`;
      if (o.status === 'pending') {
        actions += `<button class="btn-ship" onclick="shipOrder(${o.id})"><i class="bi bi-truck"></i> Mark as Shipped</button>`;
      }
      if (o.status === 'processing') {
        actions += `<button class="btn-complete" onclick="completeOrder(${o.id})"><i class="bi bi-check-circle"></i> Mark as Completed</button>`;
      }

      return `
        <div class="order-card" id="order-card-${o.id}">
          <div class="order-card-head">
            <div style="display:flex;align-items:center;gap:0.75rem;">
              <span class="order-card-id">#${o.id}</span>
              <span class="badge-status ${statusBadge[o.status] || ''}">${statusLabel[o.status] || o.status}</span>
              <span style="font-size:0.75rem;color:var(--fg-muted);text-transform:uppercase;">${esc(o.payment_method || 'cod')}</span>
            </div>
            <span class="order-card-date">${date}</span>
          </div>
          <div class="order-card-body">
            ${imgHtml}
            <div class="order-prod-info">
              <div class="order-prod-name">${esc(o.product_name || 'â€”')}</div>
              <div class="order-prod-meta">${esc(o.category||'')}${o.brand ? ' Â· ' + esc(o.brand) : ''}</div>
              <div style="font-size:0.82rem;color:var(--fg-muted);">x${o.quantity} @ â‚±${price}</div>
            </div>
            <div class="order-customer-box">
              <div class="order-customer-title"><i class="bi bi-person-fill"></i> Customer</div>
              <div class="order-customer-name">${customer}</div>
              <div class="order-customer-contact"><i class="bi bi-telephone-fill" style="font-size:0.7rem;"></i> ${esc(o.customer_phone || 'No phone')}</div>
              <div class="order-customer-contact"><i class="bi bi-envelope-fill" style="font-size:0.7rem;"></i> ${esc(o.customer_email || 'â€”')}</div>
              <div style="margin-top:0.4rem;padding-top:0.4rem;border-top:1px solid var(--fg-border);">
                <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.2rem;"><i class="bi bi-geo-alt-fill" style="color:#dc3545;"></i> Delivery Address</div>
                <div class="order-address-line">${addrStr}</div>
              </div>
            </div>
          </div>
          <div class="order-card-foot">
            <div class="order-total">Order Total: <strong>â‚±${total}</strong></div>
            <div class="order-actions">${actions}</div>
          </div>
        </div>`;
    }).join('');
  }

  // â”€â”€ Ship order â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
  window.shipOrder = function(orderId) {
    if (!confirm('Mark order #' + orderId + ' as shipped?')) return;
    fetch('../../../backend/sales_orders.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'ship', order_id: orderId })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) { showAlert('success', 'Order #' + orderId + ' marked as shipped.'); loadStats(); loadOrders(); }
        else showAlert('danger', d.message || 'Could not update order.');
      })
      .catch(() => showAlert('danger', 'Network error.'));
  };

  // â”€â”€ Complete order â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
  window.completeOrder = function(orderId) {
    if (!confirm('Mark order #' + orderId + ' as completed?')) return;
    fetch('../../../backend/sales_orders.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'complete', order_id: orderId })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) { showAlert('success', 'Order #' + orderId + ' marked as completed.'); loadStats(); loadOrders(); }
        else showAlert('danger', d.message || 'Could not update order.');
      })
      .catch(() => showAlert('danger', 'Network error.'));
  };

  // â”€â”€ View order detail modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
  window.viewOrder = function(orderId) {
    document.getElementById('detailBody').innerHTML = '<p style="color:var(--fg-muted);">Loadingâ€¦</p>';
    document.getElementById('detailFoot').innerHTML = '<button onclick="closeDetail()" style="padding:0.6rem 1.25rem;border-radius:9px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-weight:700;cursor:pointer;">Close</button>';
    document.getElementById('detailOverlay').classList.add('open');

    fetch('../../../backend/sales_orders.php?action=detail&id=' + orderId, { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) { document.getElementById('detailBody').innerHTML = '<p style="color:#dc3545;">' + esc(d.message) + '</p>'; return; }
        const o = d.order;
        const date  = new Date(o.created_at).toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' });
        const total = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        const price = parseFloat(o.unit_price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        const customer = esc((o.first_name||'') + ' ' + (o.last_name||'')).trim() || 'Unknown';
        const addrParts = [o.address_line, o.barangay, o.city, o.province, o.zip_code].filter(Boolean);
        const addrStr = addrParts.map(esc).join(', ') || 'No address on file';
        const statusBadge = { pending:'badge-pending', processing:'badge-processing', completed:'badge-completed', cancelled:'badge-cancelled' };
        const statusLabel = { pending:'Pending', processing:'Shipped', completed:'Completed', cancelled:'Cancelled' };

        document.getElementById('detailBody').innerHTML = `
          <div class="detail-grid">
            <div class="detail-item"><label>Order ID</label><span style="font-weight:800;color:#3b82f6;">#${o.id}</span></div>
            <div class="detail-item"><label>Status</label><span><span class="badge-status ${statusBadge[o.status]||''}">${statusLabel[o.status]||o.status}</span></span></div>
            <div class="detail-item"><label>Date Ordered</label><span>${date}</span></div>
            <div class="detail-item"><label>Payment</label><span style="text-transform:uppercase;">${esc(o.payment_method||'cod')}</span></div>
            <div class="detail-item"><label>Product</label><span style="font-weight:700;">${esc(o.product_name||'â€”')}</span></div>
            <div class="detail-item"><label>Category</label><span>${esc(o.category||'â€”')}</span></div>
            <div class="detail-item"><label>Quantity</label><span>${o.quantity}</span></div>
            <div class="detail-item"><label>Unit Price</label><span>â‚±${price}</span></div>
            <div class="detail-item"><label>Total Amount</label><span style="font-weight:800;color:var(--fg-primary);font-size:1rem;">â‚±${total}</span></div>
            ${o.notes ? `<div class="detail-item" style="grid-column:1/-1;"><label>Order Notes</label><span>${esc(o.notes)}</span></div>` : ''}
            ${o.cancel_reason ? `<div class="detail-item" style="grid-column:1/-1;"><label>Cancel Reason</label><span style="color:#dc3545;">${esc(o.cancel_reason)}</span></div>` : ''}
          </div>
          <div class="detail-address-box">
            <h6><i class="bi bi-person-fill" style="color:var(--fg-primary);"></i> Customer Information</h6>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">
              <div><div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.15rem;">Name</div><div style="font-weight:700;">${customer}</div></div>
              <div><div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.15rem;">Phone</div><div>${esc(o.customer_phone||'Not provided')}</div></div>
              <div style="grid-column:1/-1;"><div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.15rem;">Email</div><div>${esc(o.customer_email||'â€”')}</div></div>
            </div>
            <div style="border-top:1px solid var(--fg-border);padding-top:0.75rem;">
              <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.35rem;display:flex;align-items:center;gap:0.3rem;"><i class="bi bi-geo-alt-fill" style="color:#dc3545;"></i> Delivery Address</div>
              <div style="font-size:0.88rem;color:var(--fg-text);line-height:1.7;">${addrStr}</div>
              ${o.address_verified ? '<div style="margin-top:0.4rem;"><span style="background:rgba(40,167,69,0.12);color:#28A745;font-size:0.7rem;font-weight:700;padding:0.15rem 0.5rem;border-radius:20px;"><i class="bi bi-check-circle-fill"></i> Address Verified</span></div>' : ''}
            </div>
          </div>`;

        // Add action buttons in footer
        let footBtns = '<button onclick="closeDetail()" style="padding:0.6rem 1.25rem;border-radius:9px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-weight:700;cursor:pointer;">Close</button>';
        // Always show Message Customer button
        const custId = o.customer_id || (o.first_name ? '' : '');
        if (custId) {
          footBtns += `<a href="messages.php?with=${custId}" class="btn-view" style="text-decoration:none;padding:0.6rem 1.25rem;"><i class="bi bi-chat-dots"></i> Message Customer</a>`;
        }
        if (o.status === 'pending') {
          footBtns += `<button class="btn-ship" onclick="closeDetail();shipOrder(${o.id})"><i class="bi bi-truck"></i> Mark as Shipped</button>`;
        }
        if (o.status === 'processing') {
          footBtns += `<button class="btn-complete" onclick="closeDetail();completeOrder(${o.id})"><i class="bi bi-check-circle"></i> Mark as Completed</button>`;
        }
        document.getElementById('detailFoot').innerHTML = footBtns;
      })
      .catch(() => {
        document.getElementById('detailBody').innerHTML = '<p style="color:#dc3545;">Failed to load order details.</p>';
      });
  };

  window.closeDetail = function() {
    document.getElementById('detailOverlay').classList.remove('open');
  };

  function showAlert(type, msg) {
    const box = document.getElementById('alertBox');
    box.style.display = 'flex';
    box.style.cssText = `display:flex;align-items:center;gap:0.6rem;padding:0.75rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;margin-bottom:1rem;${type==='success'?'background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);':'background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);'}`;
    box.innerHTML = `<i class="bi bi-${type==='success'?'check-circle-fill':'exclamation-triangle-fill'}"></i> ${esc(msg)}`;
    setTimeout(() => { box.style.display = 'none'; }, 4000);
  }

  function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

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
  </script>

<script src="../../../assets/js/pwa.js" defer></script>
</body>
</html>


