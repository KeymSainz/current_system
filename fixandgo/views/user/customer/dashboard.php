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
  <title>Fix&amp;Go — My Account</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <!-- Mobile navbar fix — after all CSS so it wins -->
  <style>
    @media (max-width: 991px) {
      /* Hide desktop-only nav items */
      #cusMobileMenuHide, #cusMobileMenuHide2, #cusMobileMenuHide3,
      #cusMobileMenuHide4, #cusMobileMenuHide5, #cusMobileMenuHide6,
      .fg-navbar > div:last-child > .role-badge,
      .fg-navbar > div:last-child > span[id="navUserName"] { display: none !important; }
      /* Show mobile hamburger */
      #cusMobileMenu { display: flex !important; }
      /* Show mobile search bar */
      #cusMobileSearchBar { display: block !important; }
      /* Prevent wrapping */
      .fg-navbar > div:last-child { flex-wrap: nowrap !important; gap: 0.4rem !important; }
      .fg-navbar { flex-wrap: nowrap !important; }
      /* Hide left sidebar toggle — right hamburger handles it */
      #sidebarToggle { display: none !important; }
      /* Add bottom padding so content clears the bottom nav */
      .cu-main { padding-bottom: 75px !important; }
    }
    @media (min-width: 992px) {
      #cusMobileMenu { display: none !important; }
      #cusDrawer, #cusDrawerOverlay { display: none !important; }
      #cusMobileSearchBar { display: none !important; }
      #cusDashBottomNav { display: none !important; }
    }
    /* Drawer */
    #cusDrawer a, #cusDrawer button { transition: background 0.15s; }
    #cusDrawer a:hover, #cusDrawer button:hover {
      background: rgba(230,168,0,0.09) !important; color: var(--fg-primary,#e6a800) !important;
    }
  </style>
  <!-- Inline styles for immediate effect -->
  <style>
    /* CACHE BUSTER TEST - If you see this comment in browser inspector, styles are loading */
    body { background: var(--fg-bg); }
    /* ── Layout ── */
    .cu-layout { display: flex; min-height: calc(100vh - 60px); }
    /* ── Sidebar ── */
    .cu-sidebar {
      width: 260px; flex-shrink: 0;
      background: var(--fg-card-bg);
      border-right: 1px solid var(--fg-border);
      padding: 1.25rem 0 2rem;
      position: sticky; top: 60px;
      height: calc(100vh - 60px);
      overflow-y: auto;
    }
    .sidebar-profile {
      display: flex; align-items: center; gap: 0.85rem;
      padding: 0 1.25rem 1rem;
      border-bottom: 1px solid var(--fg-border);
      margin-bottom: 0.75rem;
    }
    .sidebar-avatar {
      width: 44px; height: 44px; border-radius: 12px;
      background: linear-gradient(135deg, var(--fg-primary), #c98f00);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem; color: #fff; font-weight: 800;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(230,168,0,0.25);
      overflow: hidden;
    }
    .sidebar-avatar img {
      width: 100%; height: 100%; object-fit: cover; border-radius: 12px; display: block;
    }
    .sidebar-profile-name { font-size: 0.88rem; font-weight: 700; color: var(--fg-text); line-height: 1.3; }
    .sidebar-profile-edit { 
      font-size: 0.72rem; color: var(--fg-primary); text-decoration: none; font-weight: 600;
      display: inline-flex; align-items: center; gap: 0.25rem;
      transition: all 0.2s;
    }
    .sidebar-profile-edit:hover { text-decoration: none; opacity: 0.8; }
    .sidebar-section-label {
      font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: 1.2px; color: var(--fg-muted);
      padding: 0.75rem 1.25rem 0.4rem; margin-top: 0.5rem;
    }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav li a, .sidebar-nav li button {
      display: flex; align-items: center; gap: 0.7rem;
      padding: 0.65rem 1.25rem;
      color: var(--fg-muted); text-decoration: none;
      font-size: 0.86rem; font-weight: 500;
      border-left: 3px solid transparent;
      transition: all 0.2s; width: 100%;
      background: none; border-top: none; border-right: none; border-bottom: none;
      cursor: pointer; text-align: left;
      position: relative;
    }
    .sidebar-nav li a:hover, .sidebar-nav li button:hover {
      color: var(--fg-primary); background: rgba(230,168,0,0.08);
      border-left-color: var(--fg-primary);
    }
    .sidebar-nav li a.active {
      color: var(--fg-primary); background: rgba(230,168,0,0.12);
      border-left-color: var(--fg-primary); font-weight: 700;
    }
    .sidebar-nav li a i, .sidebar-nav li button i { font-size: 1.05rem; width: 20px; text-align: center; }
    .sidebar-badge {
      margin-left: auto; background: #dc3545; color: #fff;
      font-size: 0.65rem; font-weight: 700;
      padding: 0.15rem 0.5rem; border-radius: 12px; min-width: 20px; text-align: center;
    }
    /* ── Main ── */
    .cu-main { flex: 1; padding: 1.75rem 2rem; min-width: 0; background: var(--fg-bg); }
    /* ── Welcome banner ── */
    .welcome-banner {
      background: linear-gradient(135deg, var(--fg-primary) 0%, #c98f00 100%);
      border-radius: 16px; padding: 1.5rem 1.75rem;
      color: #fff; margin-bottom: 1.5rem;
      position: relative; overflow: hidden;
      box-shadow: 0 4px 20px rgba(230,168,0,0.25);
    }
    .welcome-banner::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -10%;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
      pointer-events: none;
    }
    .welcome-banner::after { 
      content: '🛒'; 
      position: absolute; 
      right: 1.5rem; 
      top: 50%; 
      transform: translateY(-50%); 
      font-size: 3.5rem; 
      opacity: 0.2; 
    }
    .welcome-banner h2 { 
      font-weight: 800; 
      margin: 0 0 0.3rem; 
      font-size: 1.3rem; 
      position: relative;
      z-index: 1;
    }
    .welcome-banner p { 
      margin: 0; 
      opacity: 0.9; 
      font-size: 0.88rem; 
      position: relative;
      z-index: 1;
    }
    /* ── Stats ── */
    .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-card {
      background: var(--fg-card-bg); border: 1px solid var(--fg-border);
      border-radius: 14px; padding: 1.25rem 1rem;
      display: flex; align-items: center; gap: 1rem;
      transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    }
    .stat-card:hover { 
      transform: translateY(-4px); 
      box-shadow: 0 12px 32px rgba(0,0,0,0.12); 
      border-color: rgba(230,168,0,0.3);
    }
    .stat-icon { 
      width: 48px; 
      height: 48px; 
      border-radius: 12px; 
      display: flex; 
      align-items: center; 
      justify-content: center; 
      font-size: 1.3rem; 
      flex-shrink: 0; 
    }
    .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.72rem; color: var(--fg-muted); font-weight: 600; margin-top: 0.2rem; text-transform: uppercase; letter-spacing: 0.5px; }
    /* ── Quick actions ── */
    .quick-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px,1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .quick-card {
      background: var(--fg-card-bg); border: 1px solid var(--fg-border);
      border-radius: 14px; padding: 1.25rem 1rem;
      text-align: center; text-decoration: none; color: var(--fg-text);
      display: block; transition: all 0.2s;
      position: relative;
      overflow: hidden;
    }
    .quick-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--fg-primary), #c98f00);
      transform: scaleX(0);
      transition: transform 0.3s;
    }
    .quick-card:hover::before {
      transform: scaleX(1);
    }
    .quick-card:hover { 
      transform: translateY(-5px); 
      box-shadow: 0 12px 36px rgba(0,0,0,0.14); 
      border-color: rgba(230,168,0,0.4); 
      color: var(--fg-text); 
    }
    .quick-card .qc-icon { 
      font-size: 2rem; 
      margin-bottom: 0.6rem; 
      display: block; 
      transition: transform 0.2s;
    }
    .quick-card:hover .qc-icon {
      transform: scale(1.1);
    }
    .quick-card .qc-label { font-size: 0.82rem; font-weight: 700; }
    /* ── Section card ── */
    .section-card { 
      background: var(--fg-card-bg); 
      border: 1px solid var(--fg-border); 
      border-radius: 14px; 
      overflow: hidden; 
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .section-head { 
      padding: 1rem 1.25rem; 
      border-bottom: 1px solid var(--fg-border); 
      display: flex; 
      align-items: center; 
      justify-content: space-between;
      background: linear-gradient(to bottom, var(--fg-bg), var(--fg-card-bg));
    }
    .section-head h6 { margin: 0; font-weight: 700; font-size: 0.95rem; color: var(--fg-text); display: flex; align-items: center; gap: 0.5rem; }
      display: block; transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    }
    .quick-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,0.12); border-color: var(--fg-primary); color: var(--fg-text); }
    .quick-card .qc-icon { font-size: 2rem; margin-bottom: 0.6rem; display: block; }
    .quick-card .qc-label { font-size: 0.82rem; font-weight: 700; }
    /* ── Section card ── */
    .section-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; overflow: hidden; margin-bottom: 1.5rem; }
    .section-head { padding: 1rem 1.25rem; border-bottom: 1px solid var(--fg-border); display: flex; align-items: center; justify-content: space-between; }
    .section-head h6 { margin: 0; font-weight: 700; font-size: 0.95rem; color: var(--fg-text); }
    /* ── Mini table ── */
    .mini-table { width: 100%; border-collapse: collapse; font-size: 0.83rem; }
    .mini-table th { background: var(--fg-bg); padding: 0.6rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--fg-muted); border-bottom: 1px solid var(--fg-border); }
    .mini-table td { padding: 0.65rem 1rem; border-bottom: 1px solid var(--fg-border); color: var(--fg-text); vertical-align: middle; }
    .mini-table tr:last-child td { border-bottom: none; }
    /* ── Badges ── */
    .badge-status { display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .badge-pending    { background: rgba(230,168,0,0.12); color: #c98f00; }
    .badge-progress   { background: rgba(59,130,246,0.12); color: #3b82f6; }
    .badge-completed  { background: rgba(40,167,69,0.12);  color: #28A745; }
    .badge-cancelled  { background: rgba(220,53,69,0.12);  color: #dc3545; }
    /* ── Sidebar toggle (mobile) ── */
    .sidebar-toggle { display: none; background: none; border: 1.5px solid var(--fg-border); border-radius: 8px; padding: 0.3rem 0.6rem; color: var(--fg-text); cursor: pointer; font-size: 1.1rem; }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-overlay.open { display: block; }
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }
      .cu-sidebar { position: fixed; top: 68px; left: 0; z-index: 200; transform: translateX(-100%); height: calc(100vh - 68px); box-shadow: 4px 0 20px rgba(0,0,0,0.15); transition: transform 0.3s; }
      .cu-sidebar.open { transform: translateX(0); }
      .cu-main { padding: 1.25rem; }
    }
    @media (max-width: 575px) {
      html,body{overflow-x:hidden;}
      .cu-main { padding: 0.75rem; }
      .stats-grid { grid-template-columns: repeat(2,1fr); gap: 0.65rem; }
      .stat-icon { width:38px;height:38px;font-size:1rem; }
      .stat-value { font-size: 1.3rem; }
      .quick-grid { grid-template-columns: repeat(3,1fr); gap: 0.65rem; }
      .quick-card { padding: 0.9rem 0.5rem; }
      .quick-card .qc-icon { font-size: 1.5rem; margin-bottom: 0.4rem; }
      .quick-card .qc-label { font-size: 0.72rem; }
      .welcome-banner { padding: 1.1rem 1.25rem; }
      .welcome-banner h2 { font-size: 1.05rem; }
      .welcome-banner p { font-size: 0.8rem; }
      /* Mini table: hide Issue and Amount columns */
      .mini-table th:nth-child(3), .mini-table td:nth-child(3),
      .mini-table th:nth-child(4), .mini-table td:nth-child(4) { display: none; }
      .mini-table th, .mini-table td { padding: 0.5rem 0.6rem!important; font-size: 0.77rem!important; }
      /* Navbar: hide username on tiny phones */
      #navUserName { display: none!important; }
      /* Section card padding */
      .section-card .section-head { padding: 0.75rem 1rem; }
    }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-2">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:42px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.1rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-2" id="navDesktopRight">
      <span class="role-badge customer" id="cusMobileMenuHide">👤 Customer</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);" id="cusMobileMenuHide2"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="../../../index.php?browse=1" class="btn btn-sm" id="cusMobileMenuHide3"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;">
        <i class="bi bi-shop"></i> Browse Shop
      </a>
      <!-- Cart Icon -->
      <a href="../../../index.php?browse=1#shop" style="position:relative;text-decoration:none;" id="cusMobileMenuHide4">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);transition:all 0.2s;">
          <i class="bi bi-cart-fill"></i>
        </div>
        <span id="navCartBadge" style="position:absolute;top:-4px;right:-4px;background:#dc3545;color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <!-- Message Icon -->
      <a href="messages.php" style="position:relative;text-decoration:none;" title="Messages" id="cusMobileMenuHide5">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);transition:all 0.2s;">
          <i class="bi bi-chat-dots-fill"></i>
        </div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:var(--fg-primary);color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <!-- Logout — desktop only -->
      <button onclick="customerLogout()" class="btn btn-sm" id="cusMobileMenuHide6"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
      <!-- Notification Bell + Mobile Hamburger — always inline on one row -->
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
        <button id="cusMobileMenu" onclick="toggleCusDrawer()" aria-label="Menu"
          style="display:none;background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:8px;width:34px;height:34px;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);flex-shrink:0;">
          <i class="bi bi-list"></i>
        </button>
      </div>
    </div>
  </nav>

  <!-- Mobile search bar — sticky below navbar -->
  <div id="cusMobileSearchBar" style="display:none;position:sticky;top:58px;z-index:99;background:var(--fg-card-bg);border-bottom:1px solid var(--fg-border);padding:0.5rem 1rem;">
    <div style="position:relative;display:flex;align-items:center;">
      <i class="bi bi-search" style="position:absolute;left:0.75rem;color:var(--fg-muted);font-size:0.85rem;pointer-events:none;"></i>
      <input type="text" id="cusDashSearch" placeholder="Search orders, repairs…"
             style="width:100%;background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50px;padding:0.45rem 2rem 0.45rem 2.25rem;color:var(--fg-text);font-size:0.85rem;outline:none;transition:border-color 0.2s;"
             onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'">
    </div>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="cu-layout">

    <!-- Sidebar -->
    <aside class="cu-sidebar" id="cuSidebar">

      <!-- Profile snippet -->
      <div class="sidebar-profile">
        <div class="sidebar-avatar" id="sidebarAvatarInitials">?</div>
        <div>
          <div class="sidebar-profile-name" id="sidebarName">Loading…</div>
          <a href="profile.php" class="sidebar-profile-edit"><i class="bi bi-pencil-fill" style="font-size:0.65rem;"></i> Edit Profile</a>
        </div>
      </div>

      <!-- My Account -->
      <div class="sidebar-section-label">My Account</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php" class="active"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li>
          <a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a>
        </li>
        <li><a href="notifications.php"><i class="bi bi-bell-fill"></i> Notifications <span class="sidebar-badge" id="notifBadge" style="display:none;">0</span></a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages <span class="sidebar-badge" id="msgBadge" style="display:none;">0</span></a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>

      <!-- Shopping -->
      <div class="sidebar-section-label">Shopping</div>
      <ul class="sidebar-nav">
        <li><a href="orders.php"><i class="bi bi-bag-heart-fill"></i> My Purchases</a></li>
        <li><a href="repairs.php"><i class="bi bi-tools"></i> My Repairs</a></li>
        <li><a href="wishlist.php"><i class="bi bi-heart-fill"></i> Wishlist</a></li>
        <li><a href="vouchers.php"><i class="bi bi-ticket-perforated-fill"></i> My Vouchers</a></li>
      </ul>

      <!-- Fix&Go Seller Centre -->
      <div class="sidebar-section-label">Fix&amp;Go</div>
      <ul class="sidebar-nav">
        <li>
          <a href="seller-centre.php">
            <i class="bi bi-shop-window"></i> Seller Centre
          </a>
        </li>
      </ul>

    </aside>

    <!-- Main content -->
    <main class="cu-main">

      <!-- Welcome banner -->
      <div class="welcome-banner">
        <h2>Welcome back, <span id="firstName">there</span>! 👋</h2>
        <p>Here's a summary of your Fix&amp;Go activity.</p>
        <span class="role-badge customer" style="background:rgba(255,255,255,0.2);color:#fff;margin-top:0.5rem;display:inline-flex;">👤 Customer</span>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="bi bi-bag-fill"></i></div>
          <div><div class="stat-value" style="color:#3b82f6;" id="statOrders">—</div><div class="stat-label">Total Orders</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(230,168,0,0.12);color:#c98f00;"><i class="bi bi-tools"></i></div>
          <div><div class="stat-value" style="color:#c98f00;" id="statRepairs">—</div><div class="stat-label">Repairs</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-check-circle-fill"></i></div>
          <div><div class="stat-value" style="color:#28A745;" id="statCompleted">—</div><div class="stat-label">Completed</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(220,53,69,0.12);color:#dc3545;"><i class="bi bi-clock-history"></i></div>
          <div><div class="stat-value" style="color:#dc3545;" id="statPending">—</div><div class="stat-label">Pending</div></div>
        </div>
      </div>

      <!-- Quick Actions -->
      <h6 style="font-weight:700;color:var(--fg-text);margin-bottom:0.75rem;">Quick Actions</h6>
      <div class="quick-grid">
        <a href="repairs.php" class="quick-card"><span class="qc-icon">🔧</span><span class="qc-label">Book Repair</span></a>
        <a href="orders.php"  class="quick-card"><span class="qc-icon">📦</span><span class="qc-label">My Purchases</span></a>
        <a href="repairs.php" class="quick-card"><span class="qc-icon">📍</span><span class="qc-label">Track Repair</span></a>
        <a href="messages.php" class="quick-card"><span class="qc-icon">💬</span><span class="qc-label">Messages</span></a>
        <a href="../../../index.php?browse=1" class="quick-card"><span class="qc-icon">🛒</span><span class="qc-label">Browse Shop</span></a>
      </div>

      <!-- Recent Purchases -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-bag-heart-fill" style="color:var(--fg-primary);margin-right:0.4rem;"></i>Recent Purchases</h6>
          <a href="orders.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All →</a>
        </div>
        <div id="recentOrdersBody">
          <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
            <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
            Loading…
          </div>
        </div>
      </div>

      <!-- Recent Repairs -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-tools" style="color:#c98f00;margin-right:0.4rem;"></i>Recent Repairs</h6>
          <a href="repairs.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All →</a>
        </div>
        <div style="overflow-x:auto;">
          <table class="mini-table">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Device / Technician</th>
                <th>Issue</th>
                <th style="text-align:right;">Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="recentRepairsBody">
              <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#c98f00;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
                Loading…
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'customer') { window.location.href = '../../../login.html'; return; }

    const fullName = ((user.firstName || '') + ' ' + (user.lastName || '')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email;
    document.getElementById('sidebarName').textContent = fullName || user.email;
    document.getElementById('firstName').textContent = user.firstName || fullName || 'there';

    // Render avatar — show photo if available, else initials
    function renderSidebarAvatar(avatarUrl) {
      const el = document.getElementById('sidebarAvatarInitials');
      if (!el) return;
      if (avatarUrl) {
        el.innerHTML = '<img src="' + avatarUrl + '" alt="avatar" onerror="this.parentElement.innerHTML=\'' +
          (((user.firstName||'')[0]||'') + ((user.lastName||'')[0]||'')).toUpperCase() + '\'">';
      } else {
        const initials = ((user.firstName || '')[0] || '') + ((user.lastName || '')[0] || '');
        el.textContent = initials.toUpperCase() || '?';
      }
    }

    // First render from cached session
    renderSidebarAvatar(user.avatar_url || null);

    // Then fetch fresh from server to get latest avatar_url
    fetch('../../../api/session/user', { credentials: 'include' })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.loggedIn && data.user) {
          // Update sessionStorage with fresh data
          FGAuth.UserStore.save(data.user);
          // Re-render avatar with fresh data
          renderSidebarAvatar(data.user.avatar_url || null);
          // Update name in case it changed
          const freshName = ((data.user.firstName || '') + ' ' + (data.user.lastName || '')).trim();
          document.getElementById('sidebarName').textContent = freshName || data.user.email;
        }
      })
      .catch(function() {}); // silent fail — keep initials

    // Sidebar toggle
    const sidebar = document.getElementById('cuSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    // Load stats & recent data
    loadStats();
    loadRecentOrders();
    loadRecentRepairs();
    loadUnreadMessageCount();
  });

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

  function loadStats() {
    fetch('../../../backend/customer_orders.php?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        const orders = d.orders || [];
        document.getElementById('statOrders').textContent    = orders.length;
        document.getElementById('statCompleted').textContent = orders.filter(o => o.status === 'completed').length;
        document.getElementById('statPending').textContent   = orders.filter(o => o.status === 'pending').length;
      })
      .catch(() => {});

    // Repair stats from bookings
    fetch('../../../backend/repair_bookings.php?action=my_bookings', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        const bookings = d.bookings || [];
        document.getElementById('statRepairs').textContent = bookings.length;
      })
      .catch(() => {});
  }

  function loadRecentOrders() {
    fetch('../../../backend/customer_orders.php?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const body = document.getElementById('recentOrdersBody');
        if (!d.success || !d.orders || d.orders.length === 0) {
          body.innerHTML = `<div style="text-align:center;padding:2.5rem 1rem;color:var(--fg-muted);">
            <i class="bi bi-bag-heart" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.25;"></i>
            No purchases yet.
            <a href="../../../index.php?browse=1" style="display:block;margin-top:0.5rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">Browse the shop →</a>
          </div>`;
          return;
        }
        const statusMap   = { pending:'badge-pending', processing:'badge-progress', completed:'badge-completed', cancelled:'badge-cancelled' };
        const statusLabel = { pending:'To Pay', processing:'To Ship', completed:'Completed', cancelled:'Cancelled' };
        const recent = d.orders.slice(0, 5);
        body.innerHTML = recent.map(o => {
          const total = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
          const date  = new Date(o.created_at).toLocaleDateString('en-PH', { month:'short', day:'numeric', year:'numeric' });
          const img   = o.image_path
            ? `<img src="../../../${esc(o.image_path)}" style="width:60px;height:60px;border-radius:8px;object-fit:cover;border:1px solid var(--fg-border);flex-shrink:0;" onerror="this.outerHTML='<div style=\\'width:60px;height:60px;border-radius:8px;background:var(--fg-bg);border:1px solid var(--fg-border);display:flex;align-items:center;justify-content:center;flex-shrink:0;\\'><i class=\\'bi bi-box-seam\\'style=\\'color:var(--fg-muted);font-size:1.3rem;\\'></i></div>'">`
            : `<div style="width:60px;height:60px;border-radius:8px;background:var(--fg-bg);border:1px solid var(--fg-border);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-box-seam" style="color:var(--fg-muted);font-size:1.3rem;"></i></div>`;
          return `<div style="display:flex;gap:0.85rem;padding:0.85rem 1.25rem;border-bottom:1px solid var(--fg-border);align-items:center;">
            ${img}
            <div style="flex:1;min-width:0;">
              <div style="font-weight:600;font-size:0.85rem;color:var(--fg-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(o.product_name || '—')}</div>
              <div style="font-size:0.75rem;color:var(--fg-muted);margin:0.15rem 0;">x${o.quantity} · ${date}</div>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:0.25rem;">
                <span class="badge-status ${statusMap[o.status] || ''}">${statusLabel[o.status] || o.status}</span>
                <span style="font-weight:800;color:var(--fg-primary);font-size:0.88rem;">₱${total}</span>
              </div>
            </div>
          </div>`;
        }).join('') + `<div style="text-align:center;padding:0.75rem;">
          <a href="orders.php" style="font-size:0.82rem;color:var(--fg-primary);font-weight:700;text-decoration:none;">View all purchases →</a>
        </div>`;
      })
      .catch(() => {
        document.getElementById('recentOrdersBody').innerHTML =
          '<div style="text-align:center;padding:1.5rem;color:var(--fg-muted);">Could not load purchases.</div>';
      });
  }

  function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  // Module-level bookings cache so openDashPayModal can look up data safely
  var _dashBookings = [];

  function loadRecentRepairs() {
    fetch('../../../backend/repair_bookings.php?action=my_bookings', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const tbody = document.getElementById('recentRepairsBody');
        if (!d.success || !d.bookings || d.bookings.length === 0) {
          tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:2.5rem;color:var(--fg-muted);">
            <i class="bi bi-tools" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.2;"></i>
            No repairs booked yet. <a href="repairs.php" style="color:var(--fg-primary);font-weight:600;text-decoration:none;">Book a repair →</a>
          </td></tr>`;
          return;
        }

        // Update stat count
        const statEl = document.getElementById('statRepairs');
        if (statEl) statEl.textContent = d.bookings.length;

        // Cache for modal lookup
        _dashBookings = d.bookings;

        const statusConfig = {
          pending:     { cls:'badge-pending',   label:'Pending',     icon:'⏳' },
          confirmed:   { cls:'badge-progress',  label:'Confirmed',   icon:'✅' },
          in_progress: { cls:'badge-progress',  label:'In Progress', icon:'🔧' },
          completed:   { cls:'badge-completed', label:'Completed',   icon:'🎉' },
          cancelled:   { cls:'badge-cancelled', label:'Cancelled',   icon:'✕'  },
        };

        tbody.innerHTML = d.bookings.slice(0, 6).map(b => {
          const sc      = statusConfig[b.status] || { cls:'badge-pending', label:b.status, icon:'•' };
          const device  = esc(b.device_name || '—');
          const issue   = esc(b.fault_description || b.problem_desc || '—');
          const laborFee = parseFloat(b.labor_fee || 0);
          const partsFee = parseFloat(b.parts_fee || 0);
          const fee     = parseFloat(b.repair_fee || b.total_amount || b.total_price || 0);
          const amount  = fee > 0 ? '₱' + fee.toLocaleString('en-PH', {minimumFractionDigits:0}) : '—';
          const date    = new Date(b.created_at).toLocaleDateString('en-PH', {month:'short', day:'numeric', year:'numeric'});
          const techName = esc(b.technician_name || '—');

          // Message button for active repairs
          const msgBtn = (b.status === 'confirmed' || b.status === 'in_progress')
            ? `<a href="messages.php?with=${b.technician_id || ''}"
                style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.25rem 0.65rem;border-radius:6px;background:rgba(59,130,246,0.1);color:#3b82f6;border:1.5px solid rgba(59,130,246,0.25);font-size:0.7rem;font-weight:700;text-decoration:none;white-space:nowrap;"
                onmouseenter="this.style.background='#3b82f6';this.style.color='#fff'"
                onmouseleave="this.style.background='rgba(59,130,246,0.1)';this.style.color='#3b82f6'">
                💬 Message
              </a>`
            : '';

          // Pay Now button for completed, unpaid repairs
          const alreadyPaid = b.customer_payment_status === 'paid';
          const payBtn = b.status === 'completed'
            ? alreadyPaid
              ? `<span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.25rem 0.65rem;border-radius:6px;background:rgba(40,167,69,0.1);color:#28A745;font-size:0.7rem;font-weight:700;white-space:nowrap;">✅ Paid</span>`
              : `<button onclick="openDashPayModal(${b.id})"
                  id="dashPayBtn_${b.id}"
                  style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.25rem 0.65rem;border-radius:6px;background:rgba(40,167,69,0.1);color:#28A745;border:1.5px solid rgba(40,167,69,0.3);font-size:0.7rem;font-weight:700;cursor:pointer;white-space:nowrap;"
                  onmouseenter="this.style.background='#28A745';this.style.color='#fff'"
                  onmouseleave="this.style.background='rgba(40,167,69,0.1)';this.style.color='#28A745'">
                  💳 Pay Now
                </button>`
            : '';

          return `<tr>
            <td style="font-weight:700;color:var(--fg-primary);">#${b.id}</td>
            <td>
              <div style="font-weight:600;font-size:0.85rem;">${device}</div>
              <div style="font-size:0.73rem;color:var(--fg-muted);margin-top:0.1rem;">${techName}</div>
            </td>
            <td style="max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--fg-muted);" title="${issue}">${issue}</td>
            <td style="text-align:right;font-weight:700;white-space:nowrap;">${amount}</td>
            <td><span class="badge-status ${sc.cls}">${sc.icon} ${sc.label}</span></td>
            <td style="color:var(--fg-muted);font-size:0.8rem;white-space:nowrap;">${date}</td>
            <td style="white-space:nowrap;">${msgBtn}${payBtn}</td>
          </tr>`;
        }).join('');
      })
      .catch(() => {
        document.getElementById('recentRepairsBody').innerHTML =
          '<tr><td colspan="7" style="text-align:center;padding:1.5rem;color:var(--fg-muted);">Could not load repairs.</td></tr>';
      });
  }

  // ── Dashboard Payment Modal ───────────────────────────────────
  let _dashPayBookingId = null;

  function openDashPayModal(bookingId) {
    var b = (_dashBookings || []).find(function(x){ return x.id == bookingId; }) || {};
    _dashPayBookingId = bookingId;

    var fee      = parseFloat(b.repair_fee || b.total_amount || b.total_price || 0);
    var laborFee = parseFloat(b.labor_fee  || 0);
    var partsFee = parseFloat(b.parts_fee  || 0);
    var techMethod = b.payment_method || '';
    var P = String.fromCharCode(8369);

    document.getElementById('dpmBookingId').textContent = '#' + bookingId;
    document.getElementById('dpmDevice').textContent    = b.device_name || '—';
    document.getElementById('dpmTechName').textContent  = b.technician_name || 'Technician';
    document.getElementById('dpmFee').textContent = fee > 0 ? P + fee.toLocaleString('en-PH',{minimumFractionDigits:2}) : 'To be collected';

    // Breakdown rows
    var hasBreakdown = laborFee > 0 || partsFee > 0;
    var breakdown = document.getElementById('dpmBreakdown');
    var laborRow  = document.getElementById('dpmLaborRow');
    var partsRow  = document.getElementById('dpmPartsRow');
    if (breakdown) {
      breakdown.style.display = hasBreakdown ? 'block' : 'none';
      if (laborRow) { laborRow.style.display = laborFee > 0 ? 'flex' : 'none'; var la=document.getElementById('dpmLaborAmt'); if(la) la.textContent=P+laborFee.toLocaleString('en-PH',{minimumFractionDigits:2}); }
      if (partsRow) { partsRow.style.display = partsFee > 0 ? 'flex' : 'none'; var pa=document.getElementById('dpmPartsAmt'); if(pa) pa.textContent=P+partsFee.toLocaleString('en-PH',{minimumFractionDigits:2}); }
    }

    // Parts replaced tags
    var partsList = [];
    try { partsList = JSON.parse(b.parts_replaced || '[]'); } catch(e) {}
    var prWrap = document.getElementById('dpmPartsReplacedWrap');
    var prList = document.getElementById('dpmPartsReplacedList');
    if (prWrap && prList) {
      if (partsList.length > 0) {
        prWrap.style.display = 'block';
        prList.innerHTML = partsList.map(function(p){
          var qBadge = p.qty > 1 ? '<span style="background:#8b5cf6;color:#fff;font-size:0.6rem;font-weight:800;padding:0.05rem 0.3rem;border-radius:8px;">x'+p.qty+'</span> ' : '';
          return '<span style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.2rem 0.6rem;border-radius:20px;background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.25);font-size:0.73rem;font-weight:600;color:#8b5cf6;">' + qBadge + esc(p.name) + '</span>';
        }).join('');
      } else {
        prWrap.style.display = 'none';
      }
    }

    // Tech hint
    var pmLabels = {cash:'Cash',bank_transfer:'Bank Transfer',gcash:'GCash',maya:'Maya',other:'Other'};
    var hint = document.getElementById('dpmTechHint');
    hint.textContent = techMethod ? 'Technician expects: ' + (pmLabels[techMethod] || techMethod) : '';
    hint.style.display = techMethod ? 'block' : 'none';

    // Online pay button
    var onlineBtn = document.getElementById('dpmOnlineBtn');
    if (onlineBtn) { onlineBtn.disabled = fee <= 0; onlineBtn.style.opacity = fee > 0 ? '1' : '0.4'; }

    // Reset form
    document.getElementById('dpmMethod').value = '';
    document.getElementById('dpmNote').value   = '';
    document.getElementById('dpmNoteWrap').style.display = 'none';
    document.getElementById('dpmAlert').style.display    = 'none';
    document.getElementById('dpmBtn').disabled  = false;
    document.getElementById('dpmBtn').innerHTML = '<i class="bi bi-check-circle-fill"></i> Confirm — I\'ve Paid Directly';
    ['dpmCash','dpmBank','dpmGcash','dpmMaya','dpmOther'].forEach(function(id){
      var el = document.getElementById(id);
      if (el) { el.style.border = '2px solid var(--fg-border)'; el.style.background = 'var(--fg-card-bg)'; }
    });
    document.getElementById('dashPayModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closeDashPayModal() {
    document.getElementById('dashPayModal').style.display = 'none';
    document.body.style.overflow = '';
  }

  function pickDashPM(method) {
    document.getElementById('dpmMethod').value = method;
    const map = {cash:'dpmCash', bank_transfer:'dpmBank', gcash:'dpmGcash', maya:'dpmMaya', other:'dpmOther'};
    ['dpmCash','dpmBank','dpmGcash','dpmMaya','dpmOther'].forEach(id => {
      const el = document.getElementById(id);
      if (el) { el.style.border = '2px solid var(--fg-border)'; el.style.background = 'var(--fg-card-bg)'; }
    });
    const sel = document.getElementById(map[method]);
    if (sel) { sel.style.border = '2px solid var(--fg-primary)'; sel.style.background = 'rgba(230,168,0,0.08)'; }
    document.getElementById('dpmNoteWrap').style.display = method !== 'cash' ? 'block' : 'none';
    const ni = document.getElementById('dpmNote');
    if (ni) {
      const ph = {bank_transfer:'Account name / number / bank',gcash:'GCash number e.g. 0917-123-4567',maya:'Maya number or reference',other:'Payment reference'};
      ni.placeholder = ph[method] || 'Reference';
    }
  }

  function submitDashPayment() {
    const method = document.getElementById('dpmMethod').value;
    const note   = document.getElementById('dpmNote').value.trim();
    const btn    = document.getElementById('dpmBtn');
    const alEl   = document.getElementById('dpmAlert');

    alEl.style.display = 'none';
    if (!method) {
      alEl.style.display = 'flex';
      alEl.style.background = 'rgba(220,53,69,0.1)'; alEl.style.color = '#dc3545';
      alEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>&nbsp; Please select a payment method.';
      return;
    }
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing…';

    fetch('../../../api/repair/bookings', {
      method: 'POST', credentials: 'include',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        action: 'submit_payment',
        booking_id: _dashPayBookingId,
        customer_payment_method: method,
        customer_payment_note:   note || null,
      })
    }).then(r => r.json()).then(d => {
      if (!d.success) throw new Error(d.message || 'Payment failed.');
      alEl.style.display = 'flex';
      alEl.style.background = 'rgba(40,167,69,0.1)'; alEl.style.color = '#28A745';
      alEl.innerHTML = '<i class="bi bi-check-circle-fill"></i>&nbsp; Payment confirmed! Thank you.';
      btn.innerHTML  = '<i class="bi bi-check-circle-fill"></i> Done!';
      const payBtn = document.getElementById('dashPayBtn_' + _dashPayBookingId);
      if (payBtn) {
        payBtn.outerHTML = `<span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.25rem 0.65rem;border-radius:6px;background:rgba(40,167,69,0.1);color:#28A745;font-size:0.7rem;font-weight:700;">✅ Paid</span>`;
      }
      setTimeout(closeDashPayModal, 2000);
    }).catch(err => {
      alEl.style.display = 'flex';
      alEl.style.background = 'rgba(220,53,69,0.1)'; alEl.style.color = '#dc3545';
      alEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>&nbsp; ' + esc(err.message);
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Confirm — I\'ve Paid Directly';
    });
  }

  // ── Pay online via PayMongo from dashboard ────────────────────
  function dashPayOnline() {
    const btn  = document.getElementById('dpmOnlineBtn');
    const alEl = document.getElementById('dpmAlert');
    alEl.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating payment link…';

    fetch('../../../backend/repair_payment.php', {
      method: 'POST', credentials: 'include',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ booking_id: _dashPayBookingId })
    }).then(r => r.json()).then(d => {
      if (!d.success) throw new Error(d.message || 'Could not create payment link.');
      window.location.href = d.checkout_url;
    }).catch(err => {
      alEl.style.display = 'flex';
      alEl.style.background = 'rgba(220,53,69,0.1)'; alEl.style.color = '#dc3545';
      alEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>&nbsp; ' + esc(err.message);
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-credit-card-2-front-fill"></i> Pay via GCash / Card / Maya / Bank';
    });
  }

  // ── Handle PayMongo return on dashboard ───────────────────────
  (function checkDashPayReturn() {
    const params = new URLSearchParams(location.search);
    if (params.get('payment') === 'success') {
      const toast = document.createElement('div');
      toast.style.cssText = 'position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:9999;background:#28A745;color:#fff;padding:0.9rem 1.5rem;border-radius:12px;font-weight:700;font-size:0.9rem;box-shadow:0 8px 24px rgba(0,0,0,0.25);display:flex;align-items:center;gap:0.5rem;white-space:nowrap;';
      toast.innerHTML = '<i class="bi bi-check-circle-fill"></i> Payment successful! Your repair is fully paid.';
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 5000);
      history.replaceState({}, '', location.pathname);
    } else if (params.get('payment') === 'cancel') {
      history.replaceState({}, '', location.pathname);
    }
  })();

  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('../../../backend/logout.php').finally(() => {
        window.location.href = '../../../login.html';
      });
    });
  }

  // Cart badge from sessionStorage
  (function() {
    try {
      const cart = JSON.parse(sessionStorage.getItem('fg_customer_cart') || '[]');
      const n = cart.reduce((s,i) => s + i.quantity, 0);
      const badge = document.getElementById('navCartBadge');
      if (badge && n > 0) { badge.textContent = n; badge.style.display = 'inline-block'; }
    } catch(e) {}
  })();

  // Notification dropdown toggle
  function toggleNotifDropdown() {
    const dropdown = document.getElementById('notifDropdown');
    if (dropdown.style.display === 'none' || !dropdown.style.display) {
      dropdown.style.display = 'block';
      // Close when clicking outside
      setTimeout(() => {
        document.addEventListener('click', closeNotifOnClickOutside);
      }, 0);
    } else {
      dropdown.style.display = 'none';
      document.removeEventListener('click', closeNotifOnClickOutside);
    }
  }

  function closeNotifOnClickOutside(e) {
    const notifWrap = document.getElementById('notifWrap');
    const dropdown = document.getElementById('notifDropdown');
    if (!notifWrap.contains(e.target)) {
      dropdown.style.display = 'none';
      document.removeEventListener('click', closeNotifOnClickOutside);
    }
  }

  function markAllRead() {
    // Placeholder for mark all as read functionality
    console.log('Mark all notifications as read');
  }
  </script>

<!-- ══ Pay Now Modal ══════════════════════════════════════════ -->
<div id="dashPayModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
  <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:480px;max-height:96vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">
    <!-- Header -->
    <div style="background:linear-gradient(135deg,#28A745,#1a8a35);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
      <div>
        <div style="color:#fff;font-weight:800;font-size:1rem;">💳 Confirm Your Payment</div>
        <div style="color:rgba(255,255,255,0.8);font-size:0.75rem;margin-top:0.15rem;">
          Repair <strong id="dpmBookingId">#—</strong> · <span id="dpmDevice"></span> · <span id="dpmTechName"></span>
        </div>
      </div>
      <button onclick="closeDashPayModal()" style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;"
        onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">✕</button>
    </div>
    <!-- Body -->
    <div style="padding:1.35rem;overflow-y:auto;flex:1;">
      <!-- Cost breakdown + total -->
      <div style="background:rgba(40,167,69,0.07);border:1.5px solid rgba(40,167,69,0.2);border-radius:12px;padding:1rem 1.1rem;margin-bottom:1.1rem;">
        <!-- breakdown rows -->
        <div id="dpmBreakdown" style="display:none;margin-bottom:0.75rem;">
          <div id="dpmLaborRow" style="display:none;justify-content:space-between;font-size:0.83rem;padding:0.2rem 0;">
            <span style="color:var(--fg-muted);">🔧 Labor / Service Fee</span>
            <span id="dpmLaborAmt" style="font-weight:700;color:var(--fg-text);"></span>
          </div>
          <div id="dpmPartsRow" style="display:none;justify-content:space-between;font-size:0.83rem;padding:0.2rem 0;">
            <span style="color:var(--fg-muted);">🔩 Parts / Replacement</span>
            <span id="dpmPartsAmt" style="font-weight:700;color:var(--fg-text);"></span>
          </div>
          <div style="border-top:1px dashed var(--fg-border);margin:0.5rem 0 0.35rem;"></div>
        </div>
        <!-- Parts replaced tags -->
        <div id="dpmPartsReplacedWrap" style="display:none;margin-bottom:0.75rem;">
          <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.4rem;">🔩 Parts / Products Replaced</div>
          <div id="dpmPartsReplacedList" style="display:flex;flex-wrap:wrap;gap:0.35rem;"></div>
        </div>
        <!-- Total -->
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <div>
            <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.2rem;">Total Amount Due</div>
            <div id="dpmFee" style="font-size:1.65rem;font-weight:800;color:#28A745;line-height:1;"></div>
            <div id="dpmTechHint" style="font-size:0.74rem;color:var(--fg-muted);margin-top:0.25rem;display:none;"></div>
          </div>
          <i class="bi bi-receipt" style="font-size:2.2rem;color:rgba(40,167,69,0.22);flex-shrink:0;"></i>
        </div>
      </div>

      <!-- ── Pay Online via PayMongo ── -->
      <div style="border:1.5px solid rgba(59,130,246,0.3);border-radius:12px;padding:0.9rem 1rem;margin-bottom:0.85rem;background:rgba(59,130,246,0.04);">
        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.7px;color:#3b82f6;margin-bottom:0.4rem;">🌐 Pay Online — Secure Checkout</div>
        <div style="font-size:0.8rem;color:var(--fg-muted);line-height:1.5;margin-bottom:0.7rem;">
          Supports <strong style="color:var(--fg-text);">GCash</strong>, <strong style="color:var(--fg-text);">Maya</strong>, <strong style="color:var(--fg-text);">Credit/Debit Card</strong> &amp; <strong style="color:var(--fg-text);">Bank Transfer</strong>.
        </div>
        <button id="dpmOnlineBtn" onclick="dashPayOnline()"
          style="width:100%;padding:0.7rem;border-radius:9px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff;border:none;font-weight:800;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:opacity 0.2s;"
          onmouseenter="this.style.opacity='0.86'" onmouseleave="this.style.opacity='1'">
          <i class="bi bi-credit-card-2-front-fill"></i> Pay via GCash / Card / Maya / Bank
        </button>
      </div>

      <!-- Divider -->
      <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.85rem;">
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <span style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);white-space:nowrap;">OR PAID DIRECTLY</span>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
      </div>

      <!-- ── Direct payment method ── -->
      <div style="margin-bottom:0.85rem;">
        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.7px;color:var(--fg-muted);margin-bottom:0.5rem;">💵 I Already Paid the Technician</div>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0.4rem;">
          <div id="dpmCash" onclick="pickDashPM('cash')" style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.6rem 0.35rem;border:2px solid var(--fg-border);border-radius:9px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('dpmMethod').value!=='cash')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('dpmMethod').value!=='cash')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.2rem;">💵</span><span style="font-size:0.62rem;font-weight:700;color:var(--fg-text);">Cash</span>
          </div>
          <div id="dpmBank" onclick="pickDashPM('bank_transfer')" style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.6rem 0.35rem;border:2px solid var(--fg-border);border-radius:9px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('dpmMethod').value!=='bank_transfer')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('dpmMethod').value!=='bank_transfer')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.2rem;">🏦</span><span style="font-size:0.62rem;font-weight:700;color:var(--fg-text);">Bank</span>
          </div>
          <div id="dpmGcash" onclick="pickDashPM('gcash')" style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.6rem 0.35rem;border:2px solid var(--fg-border);border-radius:9px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('dpmMethod').value!=='gcash')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('dpmMethod').value!=='gcash')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.2rem;">📱</span><span style="font-size:0.62rem;font-weight:700;color:var(--fg-text);">GCash</span>
          </div>
          <div id="dpmMaya" onclick="pickDashPM('maya')" style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.6rem 0.35rem;border:2px solid var(--fg-border);border-radius:9px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('dpmMethod').value!=='maya')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('dpmMethod').value!=='maya')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.2rem;">💳</span><span style="font-size:0.62rem;font-weight:700;color:var(--fg-text);">Maya</span>
          </div>
          <div id="dpmOther" onclick="pickDashPM('other')" style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.6rem 0.35rem;border:2px solid var(--fg-border);border-radius:9px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('dpmMethod').value!=='other')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('dpmMethod').value!=='other')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.2rem;">💰</span><span style="font-size:0.62rem;font-weight:700;color:var(--fg-text);">Other</span>
          </div>
        </div>
        <input type="hidden" id="dpmMethod" value="">
      </div>

      <!-- Reference (non-cash) -->
      <div id="dpmNoteWrap" style="display:none;margin-bottom:0.85rem;">
        <label style="display:block;font-size:0.72rem;font-weight:700;color:var(--fg-muted);margin-bottom:0.3rem;">Account / Reference Number <span style="font-weight:400;">(optional)</span></label>
        <input type="text" id="dpmNote" placeholder="e.g. 0917-123-4567 / Ref: 123456"
          style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;box-sizing:border-box;"
          onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'">
      </div>

      <div id="dpmAlert" style="display:none;padding:0.65rem 0.9rem;border-radius:8px;font-size:0.83rem;font-weight:600;align-items:center;gap:0.4rem;margin-bottom:0.75rem;"></div>

      <button id="dpmBtn" onclick="submitDashPayment()"
        style="width:100%;padding:0.85rem;border-radius:12px;background:linear-gradient(135deg,var(--fg-primary),#c98f00);color:#000;border:none;font-weight:800;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:opacity 0.2s;"
        onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
        <i class="bi bi-check-circle-fill"></i> Confirm — I've Paid Directly
      </button>
    </div>
  </div>
</div>
<script>
  document.getElementById('dashPayModal').addEventListener('click', function(e){ if(e.target===this) closeDashPayModal(); });
</script>

<!-- ══ MOBILE DRAWER ══════════════════════════════════════════ -->
<div id="cusDrawerOverlay" onclick="toggleCusDrawer()"
  style="display:none;position:fixed;inset:0;z-index:1099;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);"></div>
<div id="cusDrawer"
  style="position:fixed;top:0;right:0;z-index:1100;height:100%;width:75vw;max-width:300px;
         background:var(--fg-card-bg);border-left:1px solid var(--fg-border);
         display:flex;flex-direction:column;transform:translateX(100%);
         transition:transform 0.3s cubic-bezier(0.4,0,0.2,1);
         box-shadow:-8px 0 32px rgba(0,0,0,0.4);">
  <!-- Drawer header with user info -->
  <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);">
    <div style="display:flex;align-items:center;gap:0.75rem;">
      <div id="drawerAvatar" style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,var(--fg-primary),#c98f00);display:flex;align-items:center;justify-content:center;font-size:0.95rem;color:#fff;font-weight:800;flex-shrink:0;">?</div>
      <div>
        <div id="drawerName" style="font-size:0.88rem;font-weight:700;color:var(--fg-text);">Loading…</div>
        <div style="font-size:0.68rem;color:var(--fg-muted);">👤 Customer</div>
      </div>
    </div>
    <button onclick="toggleCusDrawer()" style="background:none;border:1px solid var(--fg-border);color:var(--fg-text);width:30px;height:30px;border-radius:8px;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
  </div>
  <!-- Nav links -->
  <nav style="flex:1;overflow-y:auto;padding:0.5rem 0;">
    <a href="dashboard.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);">
      <i class="bi bi-house-fill" style="width:18px;color:var(--fg-primary);"></i> Dashboard
    </a>
    <a href="orders.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);">
      <i class="bi bi-bag-fill" style="width:18px;color:var(--fg-primary);"></i> My Purchases
    </a>
    <a href="repairs.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);">
      <i class="bi bi-tools" style="width:18px;color:var(--fg-primary);"></i> My Repairs
    </a>
    <a href="messages.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);">
      <i class="bi bi-chat-dots-fill" style="width:18px;color:var(--fg-primary);"></i> Messages
      <span id="drawerMsgBadge" style="display:none;margin-left:auto;background:var(--fg-primary);color:#fff;font-size:0.65rem;font-weight:800;padding:0.1rem 0.4rem;border-radius:10px;">0</span>
    </a>
    <a href="notifications.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);">
      <i class="bi bi-bell-fill" style="width:18px;color:var(--fg-primary);"></i> Notifications
    </a>
    <a href="../../../index.php?browse=1" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);">
      <i class="bi bi-shop" style="width:18px;color:var(--fg-primary);"></i> Browse Shop
    </a>
    <a href="settings.php" style="display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1.25rem;color:var(--fg-text);text-decoration:none;font-weight:600;font-size:0.9rem;border-bottom:1px solid rgba(255,255,255,0.04);">
      <i class="bi bi-gear-fill" style="width:18px;color:var(--fg-primary);"></i> Settings
    </a>
  </nav>
  <!-- Logout at bottom of drawer -->
  <div style="padding:1rem 1.25rem;border-top:1px solid var(--fg-border);">
    <button onclick="customerLogout()" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;width:100%;padding:0.7rem;border-radius:10px;background:rgba(220,53,69,0.08);border:1.5px solid rgba(220,53,69,0.3);color:#dc3545;font-weight:700;font-size:0.88rem;cursor:pointer;">
      <i class="bi bi-box-arrow-right"></i> Logout
    </button>
  </div>
</div>

<!-- ══ MOBILE BOTTOM NAV ══════════════════════════════════════ -->
<nav id="cusDashBottomNav" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;background:var(--fg-card-bg);border-top:1px solid var(--fg-border);padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));box-shadow:0 -4px 20px rgba(0,0,0,0.15);">
  <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
    <li>
      <a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-primary);text-decoration:none;font-size:0.6rem;font-weight:700;">
        <i class="bi bi-house-fill" style="font-size:1.25rem;"></i>Home
      </a>
    </li>
    <li>
      <a href="../../../index.php#shop" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;">
        <i class="bi bi-shop" style="font-size:1.25rem;"></i>Shop
      </a>
    </li>
    <li>
      <a href="../../../index.php#technicians" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;" onclick="window.location.href='../../../index.php#technicians';return false;">
        <i class="bi bi-person-workspace" style="font-size:1.25rem;"></i>Technicians
      </a>
    </li>
    <li style="position:relative;">
      <a href="notifications.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;">
        <span style="position:relative;display:inline-block;">
          <i class="bi bi-bell-fill" style="font-size:1.25rem;"></i>
          <span id="bnNotifBadge" style="display:none;position:absolute;top:-5px;right:-6px;background:#dc3545;color:#fff;font-size:0.5rem;font-weight:800;padding:0.05rem 0.3rem;border-radius:10px;min-width:14px;text-align:center;">0</span>
        </span>Inbox
      </a>
    </li>
    <li>
      <a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;">
        <i class="bi bi-person-fill" style="font-size:1.25rem;"></i>Me
      </a>
    </li>
  </ul>
</nav>

<script>
  // Show bottom nav + search bar on mobile only
  (function() {
    function checkMobile() {
      var isMob = window.innerWidth <= 991;
      var bn = document.getElementById('cusDashBottomNav');
      var sb = document.getElementById('cusMobileSearchBar');
      if (bn) bn.style.display = isMob ? 'block' : 'none';
      if (sb) sb.style.display = isMob ? 'block' : 'none';
    }
    checkMobile();
    window.addEventListener('resize', checkMobile);
  })();

  // Drawer toggle
  function toggleCusDrawer() {
    var drawer = document.getElementById('cusDrawer');
    var overlay = document.getElementById('cusDrawerOverlay');
    var isOpen = drawer.style.transform === 'translateX(0%)' || drawer.style.transform === 'translateX(0px)' || drawer.style.transform === 'translateX(0)';
    drawer.style.transform = isOpen ? 'translateX(100%)' : 'translateX(0%)';
    overlay.style.display = isOpen ? 'none' : 'block';
    document.body.style.overflow = isOpen ? '' : 'hidden';
    // Sync drawer name/avatar
    if (!isOpen) {
      try {
        var u = FGAuth.UserStore.get();
        if (u) {
          var name = ((u.firstName||'') + ' ' + (u.lastName||'')).trim() || u.email;
          var initials = ((u.firstName||'')[0]||'') + ((u.lastName||'')[0]||'');
          var dn = document.getElementById('drawerName');
          var da = document.getElementById('drawerAvatar');
          if (dn) dn.textContent = name;
          if (da && initials) da.textContent = initials.toUpperCase();
        }
      } catch(e) {}
    }
  }
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') { var d=document.getElementById('cusDrawer'); if(d && d.style.transform==='translateX(0%)') toggleCusDrawer(); } });
</script>

</body>
</html>




