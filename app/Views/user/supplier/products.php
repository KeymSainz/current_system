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
  <title>Fix&Go — Product Inventory</title>
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
    .btn-toolbar {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.45rem 1rem; border-radius: 8px;
      border: 1.5px solid var(--fg-border);
      background: transparent; color: var(--fg-text);
      font-size: 0.82rem; font-weight: 600; cursor: pointer;
      transition: all 0.2s;
    }
    .btn-toolbar:hover { border-color: var(--fg-primary); color: var(--fg-primary); background: rgba(230,168,0,0.06); }
    .btn-toolbar.danger:hover { border-color: #dc3545; color: #dc3545; background: rgba(220,53,69,0.06); }
    .btn-toolbar.success:hover { border-color: #28A745; color: #28A745; background: rgba(40,167,69,0.06); }
    .btn-toolbar.primary:hover { border-color: #3b82f6; color: #3b82f6; background: rgba(59,130,246,0.06); }
    .btn-toolbar.muted:hover  { border-color: #6C757D; color: #6C757D; background: rgba(108,117,125,0.06); }

    /* ── Filter tabs ── */
    .btn-filter {
      display: inline-flex; align-items: center; gap: 0.3rem;
      padding: 0.35rem 0.85rem; border-radius: 20px;
      border: 1.5px solid var(--fg-border);
      background: transparent; color: var(--fg-muted);
      font-size: 0.78rem; font-weight: 600; cursor: pointer;
      transition: all 0.2s;
    }
    .btn-filter:hover { border-color: var(--fg-primary); color: var(--fg-primary); background: rgba(230,168,0,0.06); }
    .btn-filter.active { background: var(--fg-primary); border-color: var(--fg-primary); color: #fff; }
    .btn-filter.rejected-filter { border-color: rgba(220,53,69,0.35); color: #dc3545; }
    .btn-filter.rejected-filter:hover,
    .btn-filter.rejected-filter.active { background: #dc3545; border-color: #dc3545; color: #fff; }
    .rejected-count {
      background: #dc3545; color: #fff;
      border-radius: 50%; width: 16px; height: 16px;
      font-size: 0.65rem; font-weight: 800;
      display: inline-flex; align-items: center; justify-content: center;
    }
    .btn-filter.rejected-filter.active .rejected-count { background: rgba(255,255,255,0.3); }
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
    .product-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .product-table thead th {
      background: var(--fg-primary); color: #fff;
      padding: 0.75rem 1rem; text-align: left;
      font-weight: 700; font-size: 0.72rem;
      text-transform: uppercase; letter-spacing: 0.6px;
      white-space: nowrap;
    }
    .product-table thead th:first-child { width: 40px; text-align: center; }
    .product-table tbody td {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid var(--fg-border);
      color: var(--fg-text); vertical-align: middle;
    }
    .product-table tbody tr:last-child td { border-bottom: none; }
    .product-table tbody tr:hover { background: rgba(230,168,0,0.03); }
    .product-table tbody td:first-child { text-align: center; }

    /* ── Status badges ── */
    .badge-status {
      display: inline-flex; align-items: center; gap: 0.3rem;
      padding: 0.25rem 0.75rem; border-radius: 20px;
      font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
      white-space: nowrap;
    }
    .badge-draft         { background: rgba(108,117,125,0.12); color: #6C757D; }
    .badge-verified      { background: rgba(40,167,69,0.12);   color: #28A745; }
    .badge-sent_to_owner { background: rgba(59,130,246,0.12);  color: #3b82f6; }
    .badge-owner_received{ background: rgba(16,185,129,0.12);  color: #10b981; }
    .badge-rejected      { background: rgba(220,53,69,0.12);   color: #dc3545; }

    /* ── Action buttons ── */
    .btn-act {
      width: 32px; height: 32px; border-radius: 8px;
      border: 1.5px solid var(--fg-border);
      background: transparent; cursor: pointer;
      display: inline-flex; align-items: center; justify-content: center;
      font-size: 0.9rem; color: var(--fg-muted);
      transition: all 0.2s; margin: 0 1px;
    }
    .btn-act:hover { transform: scale(1.1); }
    .btn-act.edit:hover   { border-color: var(--fg-primary); color: var(--fg-primary); background: rgba(230,168,0,0.08); }
    .btn-act.verify:hover { border-color: #28A745; color: #28A745; background: rgba(40,167,69,0.08); }
    .btn-act.send:hover   { border-color: #3b82f6; color: #3b82f6; background: rgba(59,130,246,0.08); }
    .btn-act.del:hover    { border-color: #dc3545; color: #dc3545; background: rgba(220,53,69,0.08); }

    /* ── Empty state ── */
    .empty-state {
      text-align: center; padding: 4rem 2rem; color: var(--fg-muted);
    }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.4; }
    .empty-state p { font-size: 0.9rem; margin: 0; }

    /* ── Modal ── */
    .modal-overlay {
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.55);
      backdrop-filter: blur(4px);
      z-index: 1000; display: none;
      align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 18px;
      box-shadow: 0 24px 64px rgba(0,0,0,0.4);
      width: 100%; max-width: 560px;
      max-height: 90vh; overflow-y: auto;
      animation: modalIn 0.25s cubic-bezier(0.16,1,0.3,1);
    }
    @keyframes modalIn {
      from { opacity: 0; transform: scale(0.95) translateY(10px); }
      to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-head {
      padding: 1.5rem 1.75rem 1.25rem;
      border-bottom: 1px solid var(--fg-border);
      display: flex; align-items: center; justify-content: space-between;
    }
    .modal-head h5 { margin: 0; font-weight: 800; font-size: 1.1rem; color: var(--fg-text); }
    .modal-body { padding: 1.5rem 1.75rem; }
    .modal-foot {
      padding: 1.25rem 1.75rem;
      border-top: 1px solid var(--fg-border);
      display: flex; gap: 0.75rem; justify-content: flex-end;
    }
    .btn-close-modal {
      width: 32px; height: 32px; border-radius: 8px;
      border: 1.5px solid var(--fg-border);
      background: transparent; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      color: var(--fg-muted); font-size: 1rem; transition: all 0.2s;
    }
    .btn-close-modal:hover { border-color: #dc3545; color: #dc3545; background: rgba(220,53,69,0.08); }

    /* ── Form ── */
    .form-group { margin-bottom: 1.1rem; }
    .form-group label {
      display: block; font-size: 0.82rem; font-weight: 700;
      color: var(--fg-text); margin-bottom: 0.4rem;
    }
    .form-group label span { color: #dc3545; margin-left: 2px; }
    .form-input {
      width: 100%; padding: 0.65rem 0.9rem;
      border: 1.5px solid var(--fg-border);
      border-radius: 10px; background: var(--fg-bg);
      color: var(--fg-text); font-size: 0.88rem;
      outline: none; transition: border-color 0.2s, box-shadow 0.2s;
      font-family: inherit;
    }
    .form-input:focus { border-color: var(--fg-primary); box-shadow: 0 0 0 3px rgba(230,168,0,0.15); }
    .form-input::placeholder { color: var(--fg-muted); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .input-prefix {
      position: relative;
    }
    .input-prefix span {
      position: absolute; left: 0.9rem; top: 50%;
      transform: translateY(-50%);
      color: var(--fg-muted); font-size: 0.88rem; pointer-events: none;
    }
    .input-prefix .form-input { padding-left: 1.8rem; }

    /* ── Description Combobox ── */
    .desc-combo-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }
    .desc-combo-wrap .form-input {
      padding-right: 2.5rem;
      width: 100%;
    }
    .desc-combo-arrow {
      position: absolute; right: 0.7rem; top: 50%;
      transform: translateY(-50%);
      background: none; border: none; cursor: pointer;
      color: var(--fg-muted); font-size: 0.85rem;
      padding: 0; line-height: 1;
      transition: color 0.2s, transform 0.2s;
      z-index: 2;
    }
    .desc-combo-arrow:hover { color: var(--fg-primary); }
    .desc-combo-arrow.open i { transform: rotate(180deg); display: inline-block; }

    .desc-combo-dropdown {
      position: absolute;
      top: calc(100% + 4px); left: 0; right: 0;
      background: var(--fg-card-bg);
      border: 1.5px solid var(--fg-primary);
      border-radius: 10px;
      box-shadow: 0 8px 28px rgba(0,0,0,0.25);
      z-index: 500;
      max-height: 220px;
      overflow: hidden;
      display: flex; flex-direction: column;
      animation: comboIn 0.15s ease;
    }
    @keyframes comboIn {
      from { opacity: 0; transform: translateY(-4px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .desc-combo-list {
      overflow-y: auto;
      flex: 1;
    }
    .desc-combo-item {
      padding: 0.6rem 1rem;
      font-size: 0.85rem;
      color: var(--fg-text);
      cursor: pointer;
      transition: background 0.15s;
      display: flex; align-items: center; gap: 0.5rem;
    }
    .desc-combo-item:hover,
    .desc-combo-item.active {
      background: rgba(230,168,0,0.1);
      color: var(--fg-primary);
    }
    .desc-combo-item i { color: var(--fg-primary); font-size: 0.8rem; flex-shrink: 0; }
    .desc-combo-item mark {
      background: rgba(230,168,0,0.3);
      color: inherit; border-radius: 2px; padding: 0 1px;
    }
    .desc-combo-empty {
      padding: 0.75rem 1rem;
      font-size: 0.82rem;
      color: var(--fg-muted);
      text-align: center;
    }
    .desc-combo-add {
      padding: 0.6rem 1rem;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--fg-primary);
      cursor: pointer;
      border-top: 1px solid var(--fg-border);
      display: flex; align-items: center; gap: 0.5rem;
      transition: background 0.15s;
      flex-shrink: 0;
    }
    .desc-combo-add:hover { background: rgba(230,168,0,0.1); }
    .desc-combo-add i { font-size: 0.9rem; }

    /* ── Buttons ── */
    .btn-primary-custom {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.65rem 1.5rem; border-radius: 10px;
      background: var(--fg-primary); color: #fff;
      border: none; font-weight: 700; font-size: 0.9rem;
      cursor: pointer; transition: all 0.2s;
    }
    .btn-primary-custom:hover { background: var(--fg-primary-dark); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(230,168,0,0.35); }
    .btn-cancel {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.65rem 1.25rem; border-radius: 10px;
      background: transparent; color: var(--fg-muted);
      border: 1.5px solid var(--fg-border); font-weight: 600;
      font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
    }
    .btn-cancel:hover { border-color: var(--fg-text); color: var(--fg-text); }

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
    .alert-warning { background: rgba(230,168,0,0.12);  color: #c98f00; border: 1px solid rgba(230,168,0,0.25); }

    /* ── Drop zone ── */
    #dropZone {
      border: 2px dashed var(--fg-border);
      border-radius: 14px;
      padding: 1.5rem 1rem;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
      background: var(--fg-bg);
      position: relative;
      user-select: none;
    }
    #dropZone:hover,
    #dropZone.dz-hover {
      border-color: var(--fg-primary);
      background: rgba(230,168,0,0.04);
      box-shadow: 0 0 0 3px rgba(230,168,0,0.1);
    }
    .dz-placeholder { pointer-events: none; }
    .dz-hint-main {
      margin: 0; font-size: 0.88rem; font-weight: 600;
      color: var(--fg-muted);
    }
    .dz-hint-main i { color: var(--fg-primary); margin-right: 0.3rem; }
    .dz-hint-or { font-weight: 400; }
    .dz-hint-sub {
      margin: 0.25rem 0 0; font-size: 0.75rem; color: var(--fg-muted);
    }
    .dz-preview-wrap { pointer-events: none; }
    .dz-preview-img {
      max-height: 160px; max-width: 100%;
      border-radius: 10px; object-fit: contain;
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
      display: block; margin: 0 auto 0.6rem;
    }
    .dz-preview-hint {
      margin: 0; font-size: 0.78rem; color: var(--fg-muted);
    }
    .dz-preview-hint i { margin-right: 0.25rem; }
    .btn-remove-img {
      background: transparent;
      border: 1.5px solid #dc3545;
      color: #dc3545;
      border-radius: 8px;
      padding: 0.3rem 0.9rem;
      font-size: 0.8rem; font-weight: 600;
      cursor: pointer;
      display: inline-flex; align-items: center; gap: 0.35rem;
      transition: all 0.2s;
    }
    .btn-remove-img:hover {
      background: rgba(220,53,69,0.08);
    }

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
      .form-row { grid-template-columns: 1fr; }
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
        <li><a href="products.php" class="active"><i class="bi bi-box-seam"></i> Products</a></li>
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

    <!-- ── Main Content ── -->
    <main class="supplier-main">

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h2>Product Inventory</h2>
          <p>Manage your accessories and parts catalog</p>
        </div>
        <button class="btn-primary-custom" id="btnAddProduct">
          <i class="bi bi-plus-circle-fill"></i> Add Product
        </button>
      </div>

      <!-- Alert -->
      <div id="alertBox" style="display:none;"></div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981;"><i class="bi bi-box-seam"></i></div>
          <div>
            <div class="stat-value" style="color:#10b981;" id="statTotal">0</div>
            <div class="stat-label">Total Products</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-check-circle"></i></div>
          <div>
            <div class="stat-value" style="color:#28A745;" id="statVerified">0</div>
            <div class="stat-label">Verified</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="bi bi-send"></i></div>
          <div>
            <div class="stat-value" style="color:#3b82f6;" id="statSent">0</div>
            <div class="stat-label">Sent to Owner</div>
          </div>
        </div>
        <div class="stat-card" id="statRejectedCard" style="cursor:pointer;" title="Click to view rejected products">
          <div class="stat-icon" style="background:rgba(220,53,69,0.12);color:#dc3545;"><i class="bi bi-x-circle-fill"></i></div>
          <div>
            <div class="stat-value" style="color:#dc3545;" id="statRejected">0</div>
            <div class="stat-label">Rejected</div>
          </div>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="toolbar">
        <div class="toolbar-left">
          <button class="btn-toolbar success" id="btnVerifySelected">
            <i class="bi bi-check-circle"></i> Verify Selected
          </button>
          <button class="btn-toolbar primary" id="btnSendToOwner">
            <i class="bi bi-send"></i> Send to Owner
          </button>
          <button class="btn-toolbar" id="btnDraftSelected" style="border-color:rgba(108,117,125,0.5);color:#6C757D;">
            <i class="bi bi-file-earmark"></i> Move to Draft
          </button>
          <button class="btn-toolbar danger" id="btnDeleteSelected">
            <i class="bi bi-trash"></i> Delete Selected
          </button>
        </div>
        <div class="toolbar-right">
          <!-- Status filter tabs -->
          <div style="display:flex;gap:0.35rem;flex-wrap:wrap;">
            <button class="btn-filter active" data-filter="all">All</button>
            <button class="btn-filter" data-filter="draft">Draft</button>
            <button class="btn-filter" data-filter="verified">Verified</button>
            <button class="btn-filter" data-filter="sent_to_owner">Sent</button>
            <button class="btn-filter rejected-filter" data-filter="rejected">
              <i class="bi bi-x-circle-fill"></i> Rejected
              <span class="rejected-count" id="rejectedCount" style="display:none;"></span>
            </button>
          </div>
          <input type="text" class="search-input" id="searchInput" placeholder="🔍  Search products…">
        </div>
      </div>

      <!-- Table -->
      <div class="table-card">
        <div style="overflow-x:auto;">
          <table class="product-table">
            <thead>
              <tr>
                <th><input type="checkbox" id="checkAll" style="cursor:pointer;"></th>
                <th>Image</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Item Description / Model</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">SRP (₱)</th>
                <th>Status</th>
                <th style="text-align:center;">Actions</th>
              </tr>
            </thead>
            <tbody id="productTableBody">
              <tr>
                <td colspan="9">
                  <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No products yet. Click <strong>Add Product</strong> to get started.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <!-- ── Add / Edit Modal ── -->
  <div class="modal-overlay" id="modalOverlay">
    <div class="modal-box" id="productModal">
      <div class="modal-head">
        <h5 id="modalTitle"><i class="bi bi-plus-circle-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Add Product</h5>
        <button class="btn-close-modal" id="btnCloseModal"><i class="bi bi-x-lg"></i></button>
      </div>
      <form id="productForm">
        <div class="modal-body">
          <input type="hidden" id="productId">

          <!-- Category & Brand -->
          <div class="form-row">
            <div class="form-group" style="position:relative;">
              <label>Category <span>*</span></label>
              <div class="desc-combo-wrap">
                <input type="text" class="form-input" id="category"
                       placeholder="Select or type a category" autocomplete="off" required>
                <button type="button" class="desc-combo-arrow" id="catComboArrow" tabindex="-1" aria-label="Show categories">
                  <i class="bi bi-chevron-down"></i>
                </button>
              </div>
              <div class="desc-combo-dropdown" id="catComboDropdown" style="display:none;">
                <div class="desc-combo-list" id="catComboList"></div>
                <div class="desc-combo-add" id="catComboAdd" style="display:none;">
                  <i class="bi bi-plus-circle"></i>
                  Add "<span id="catComboAddText"></span>"
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Brand</label>
              <input type="text" class="form-input" id="brand"
                     placeholder="e.g. ONE GUARD, SAMSUNG">
            </div>
          </div>

          <!-- Description -->
          <div class="form-group" style="position:relative;">
            <label>Item Description / Model <span>*</span></label>
            <div class="desc-combo-wrap">
              <input type="text" class="form-input" id="itemDescription"
                     placeholder="e.g. SAMSUNG TEMPERED GLASS A10 2.5D FULL 0.33MM - BLACK"
                     autocomplete="off" required>
              <button type="button" class="desc-combo-arrow" id="descComboArrow" tabindex="-1" aria-label="Show suggestions">
                <i class="bi bi-chevron-down"></i>
              </button>
            </div>
            <!-- Dropdown list -->
            <div class="desc-combo-dropdown" id="descComboDropdown" style="display:none;">
              <div class="desc-combo-list" id="descComboList"></div>
              <div class="desc-combo-add" id="descComboAdd" style="display:none;">
                <i class="bi bi-plus-circle"></i>
                Add "<span id="descComboAddText"></span>"
              </div>
            </div>
          </div>

          <!-- Qty & SRP -->
          <div class="form-row">
            <div class="form-group">
              <label>Quantity <span>*</span></label>
              <input type="number" class="form-input" id="qty" min="0" value="0" required>
            </div>
            <div class="form-group">
              <label>SRP (₱) <span>*</span></label>
              <div class="input-prefix">
                <span>₱</span>
                <input type="number" class="form-input" id="srp" min="0" step="0.01" value="0.00" required>
              </div>
            </div>
          </div>

          <!-- Total Price -->
          <div class="form-group">
            <label>Total Price <span style="color:var(--fg-muted);font-weight:500;">(Auto-calculated)</span></label>
            <div class="input-prefix">
              <span>₱</span>
              <input type="text" class="form-input" id="totalPrice" value="0.00" readonly
                     style="padding-left:1.8rem;background:var(--fg-bg);cursor:default;font-weight:700;color:var(--fg-primary);">
            </div>
          </div>

          <!-- Notes -->
          <div class="form-group" style="margin-bottom:0;">
            <label>Notes <span style="color:var(--fg-muted);font-weight:500;">(Optional)</span></label>
            <textarea class="form-input" id="notes" rows="2"
                      placeholder="Additional notes about this product…"
                      style="resize:vertical;"></textarea>
          </div>

          <!-- Image Upload -->
          <div class="form-group" style="margin-top:1.1rem;margin-bottom:0;">
            <label>Product Image <span style="color:var(--fg-muted);font-weight:500;">(Optional)</span></label>

            <!-- Drop zone -->
            <div id="dropZone"
              onclick="document.getElementById('productImage').click()"
              ondragover="event.preventDefault();this.classList.add('dz-hover')"
              ondragleave="this.classList.remove('dz-hover')"
              ondrop="handleDrop(event)">

              <!-- Placeholder shown when no image selected -->
              <div id="imagePlaceholder" class="dz-placeholder">
                <img src="/assets/images/product-placeholder.svg"
                     alt="Upload placeholder"
                     style="width:120px;height:120px;object-fit:contain;opacity:0.6;display:block;margin:0 auto 0.75rem;">
                <p class="dz-hint-main"><i class="bi bi-cloud-arrow-up-fill"></i> Click to upload <span class="dz-hint-or">or drag &amp; drop</span></p>
                <p class="dz-hint-sub">PNG, JPG, WEBP — max 5MB</p>
              </div>

              <!-- Preview shown after image is selected -->
              <div id="imagePreviewWrap" style="display:none;" class="dz-preview-wrap">
                <img id="imagePreview" src="" alt="Preview" class="dz-preview-img">
                <p class="dz-preview-hint"><i class="bi bi-arrow-repeat"></i> Click to change image</p>
              </div>

              <input type="file" id="productImage" accept="image/png,image/jpeg,image/webp,image/gif"
                     style="display:none;" onchange="handleImageSelect(this)">
            </div>

            <!-- Remove button -->
            <button type="button" id="btnRemoveImage"
                    style="display:none;margin-top:0.6rem;"
                    class="btn-remove-img"
                    onclick="removeImage()">
              <i class="bi bi-trash"></i> Remove Image
            </button>
            <input type="hidden" id="existingImagePath">
          </div>
        </div>

        <div class="modal-foot">
          <button type="button" class="btn-cancel" id="btnCancelModal">
            <i class="bi bi-x"></i> Cancel
          </button>
          <button type="submit" class="btn-primary-custom">
            <i class="bi bi-floppy-fill"></i> Save Product
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="products.js?v=4"></script>
  <script>
    // ── Image upload helpers ──────────────────────────────────
    function handleImageSelect(input) {
      if (input.files && input.files[0]) processImageFile(input.files[0]);
    }

    function handleDrop(e) {
      e.preventDefault();
      const zone = document.getElementById('dropZone');
      zone.classList.remove('dz-hover');
      const file = e.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) processImageFile(file);
    }

    function processImageFile(file) {
      if (file.size > 5 * 1024 * 1024) {
        alert('Image must be under 5MB.');
        return;
      }
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById('imagePreview').src            = e.target.result;
        document.getElementById('imagePreviewWrap').style.display = 'block';
        document.getElementById('imagePlaceholder').style.display = 'none';
        document.getElementById('btnRemoveImage').style.display   = 'inline-flex';
        document.getElementById('dropZone').classList.add('dz-hover');
      };
      reader.readAsDataURL(file);
    }

    function removeImage() {
      document.getElementById('productImage').value             = '';
      document.getElementById('imagePreview').src               = '';
      document.getElementById('imagePreviewWrap').style.display = 'none';
      document.getElementById('imagePlaceholder').style.display = 'block';
      document.getElementById('btnRemoveImage').style.display   = 'none';
      document.getElementById('dropZone').classList.remove('dz-hover');
      document.getElementById('existingImagePath').value        = '';
    }

    // ── Description Combobox ──────────────────────────────────
    (function () {
      // Predefined product descriptions — grows as user adds new ones
      const STORAGE_KEY = 'fg_item_descriptions';

      const DEFAULTS = [
        // Tempered Glass
        'SAMSUNG TEMPERED GLASS A10 2.5D FULL 0.33MM - BLACK',
        'SAMSUNG TEMPERED GLASS A20 2.5D FULL 0.33MM - BLACK',
        'SAMSUNG TEMPERED GLASS A30 2.5D FULL 0.33MM - BLACK',
        'SAMSUNG TEMPERED GLASS A50 2.5D FULL 0.33MM - BLACK',
        'SAMSUNG TEMPERED GLASS A51 2.5D FULL 0.33MM - BLACK',
        'SAMSUNG TEMPERED GLASS A52 2.5D FULL 0.33MM - BLACK',
        'IPHONE 11 TEMPERED GLASS 2.5D FULL COVER 0.33MM',
        'IPHONE 12 TEMPERED GLASS 2.5D FULL COVER 0.33MM',
        'IPHONE 13 TEMPERED GLASS 2.5D FULL COVER 0.33MM',
        'IPHONE 14 TEMPERED GLASS 2.5D FULL COVER 0.33MM',
        'OPPO A5S TEMPERED GLASS 2.5D FULL 0.33MM',
        'VIVO Y20 TEMPERED GLASS 2.5D FULL 0.33MM',
        // Batteries
        'SAMSUNG GALAXY S8 BATTERY 3000MAH EB-BG950ABA',
        'SAMSUNG GALAXY A50 BATTERY 4000MAH EB-BA505ABU',
        'IPHONE 11 BATTERY 3110MAH A2111',
        'IPHONE 12 BATTERY 2815MAH A2172',
        'IPHONE 13 BATTERY 3227MAH A2656',
        'OPPO A5S BATTERY 4230MAH BLP673',
        'VIVO Y20 BATTERY 5000MAH B-N9',
        // Screens / LCD
        'SAMSUNG GALAXY A32 AMOLED LCD DISPLAY + DIGITIZER - BLACK',
        'SAMSUNG GALAXY A52 LCD DISPLAY + TOUCH SCREEN - BLACK',
        'OPPO A5S LCD DISPLAY + TOUCH SCREEN DIGITIZER - BLACK',
        'IPHONE 11 LCD DISPLAY + TOUCH SCREEN ASSEMBLY - BLACK',
        'IPHONE 12 OLED DISPLAY + TOUCH SCREEN ASSEMBLY - BLACK',
        'IPHONE 13 OLED DISPLAY + TOUCH SCREEN ASSEMBLY - BLACK',
        'VIVO Y20 LCD DISPLAY + DIGITIZER ASSEMBLY - BLACK',
        // Chargers
        'APPLE 20W USB-C POWER ADAPTER WITH LIGHTNING CABLE',
        'ANKER 65W GAN USB-C WALL CHARGER 3-PORT',
        'SAMSUNG 25W SUPER FAST CHARGER EP-TA800',
        // Cables
        'BASEUS 100W USB-C TO USB-C BRAIDED CABLE 2M - BLACK',
        'UGREEN MFI LIGHTNING TO USB-A CABLE 1.5M - WHITE',
        'SAMSUNG USB-C TO USB-C CABLE 1M EP-DG977',
        // Cases
        'SPIGEN TOUGH ARMOR CASE - SAMSUNG GALAXY S23 ULTRA - GUNMETAL',
        'OTTERBOX COMMUTER SERIES CASE - IPHONE 14 PRO - BLACK',
        'IPHONE 13 SILICONE CASE - MIDNIGHT',
        // Power Banks
        'ANKER POWERCORE 20000MAH PORTABLE CHARGER - BLACK',
        'BASEUS BLADE 100W 20000MAH POWER BANK - BLACK',
        // Earphones
        'SAMSUNG GALAXY BUDS2 PRO TRUE WIRELESS EARBUDS - GRAPHITE',
        'APPLE AIRPODS PRO 2ND GEN WITH MAGSAFE CHARGING CASE',
      ];

      function getSaved() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
        catch { return []; }
      }

      function getAllDescriptions() {
        const saved = getSaved();
        // Merge defaults + saved, deduplicate, sort
        const all = [...new Set([...DEFAULTS, ...saved])];
        all.sort((a, b) => a.localeCompare(b));
        return all;
      }

      function saveNew(desc) {
        const saved = getSaved();
        const upper = desc.trim().toUpperCase();
        if (!saved.includes(upper) && !DEFAULTS.includes(upper)) {
          saved.push(upper);
          localStorage.setItem(STORAGE_KEY, JSON.stringify(saved));
        }
      }

      const input    = document.getElementById('itemDescription');
      const arrow    = document.getElementById('descComboArrow');
      const dropdown = document.getElementById('descComboDropdown');
      const list     = document.getElementById('descComboList');
      const addRow   = document.getElementById('descComboAdd');
      const addText  = document.getElementById('descComboAddText');

      if (!input) return;

      let activeIdx = -1;

      function esc(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
      }

      function highlight(text, q) {
        if (!q) return esc(text);
        const re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');
        return esc(text).replace(re, '<mark>$1</mark>');
      }

      function renderList(q) {
        const all = getAllDescriptions();
        const filtered = q
          ? all.filter(d => d.toLowerCase().includes(q.toLowerCase()))
          : all;

        activeIdx = -1;

        if (!filtered.length) {
          list.innerHTML = `<div class="desc-combo-empty">No matches found</div>`;
        } else {
          list.innerHTML = filtered.map((d, i) =>
            `<div class="desc-combo-item" data-val="${esc(d)}" data-idx="${i}">
               <i class="bi bi-tag"></i>
               <span>${highlight(d, q)}</span>
             </div>`
          ).join('');

          list.querySelectorAll('.desc-combo-item').forEach(item => {
            item.addEventListener('mousedown', function (e) {
              e.preventDefault();
              selectItem(this.dataset.val);
            });
          });
        }

        // Show "Add new" row if typed value doesn't exactly match any option
        const trimmed = (q || '').trim().toUpperCase();
        const exactMatch = trimmed && getAllDescriptions().some(d => d.toUpperCase() === trimmed);
        if (trimmed && !exactMatch) {
          addText.textContent = q.trim();
          addRow.style.display = 'flex';
        } else {
          addRow.style.display = 'none';
        }
      }

      function selectItem(val) {
        input.value = val;
        closeDropdown();
        input.dispatchEvent(new Event('input'));
      }

      function openDropdown() {
        renderList(input.value.trim());
        dropdown.style.display = 'flex';
        arrow.classList.add('open');
      }

      function closeDropdown() {
        dropdown.style.display = 'none';
        arrow.classList.remove('open');
        activeIdx = -1;
      }

      function isOpen() {
        return dropdown.style.display !== 'none';
      }

      // Input events
      input.addEventListener('input', function () {
        openDropdown();
      });

      input.addEventListener('focus', function () {
        openDropdown();
      });

      // Arrow button toggles
      arrow.addEventListener('mousedown', function (e) {
        e.preventDefault();
        isOpen() ? closeDropdown() : openDropdown();
        input.focus();
      });

      // Keyboard navigation
      input.addEventListener('keydown', function (e) {
        if (!isOpen()) return;
        const items = list.querySelectorAll('.desc-combo-item');
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          activeIdx = Math.min(activeIdx + 1, items.length - 1);
          updateActive(items);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          activeIdx = Math.max(activeIdx - 1, -1);
          updateActive(items);
        } else if (e.key === 'Enter') {
          if (activeIdx >= 0 && items[activeIdx]) {
            e.preventDefault();
            selectItem(items[activeIdx].dataset.val);
          } else if (addRow.style.display !== 'none') {
            e.preventDefault();
            addNewItem();
          }
        } else if (e.key === 'Escape') {
          closeDropdown();
        }
      });

      function updateActive(items) {
        items.forEach((el, i) => {
          el.classList.toggle('active', i === activeIdx);
          if (i === activeIdx) el.scrollIntoView({ block: 'nearest' });
        });
      }

      // "Add new" row click
      addRow.addEventListener('mousedown', function (e) {
        e.preventDefault();
        addNewItem();
      });

      function addNewItem() {
        const val = input.value.trim();
        if (!val) return;
        saveNew(val);
        input.value = val;
        closeDropdown();
        input.dispatchEvent(new Event('input'));
      }

      // Close on outside click
      document.addEventListener('click', function (e) {
        if (!input.closest('.form-group').contains(e.target)) {
          closeDropdown();
        }
      });
    })();
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
    });
  </script>

</body>
</html>




