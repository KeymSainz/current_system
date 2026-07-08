
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
  <title>Fix&amp;Go — Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=4" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    /* ── Layout ── */
    body { background: var(--fg-bg); }
    .supplier-layout { display: flex; min-height: calc(100vh - 65px); }

    /* ── Sidebar ── */
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
      background: rgba(230,168,0,0.10);
      border-left-color: var(--fg-primary);
      font-weight: 700;
    }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }

    /* ── Main content ── */
    .supplier-main { flex: 1; padding: 2rem; min-width: 0; }
    .profile-content { max-width: 860px; margin: 0 auto; }

    /* ── Profile Header Card ── */
    .profile-header-card {
      background: var(--fg-card-bg);
      border-radius: var(--fg-radius);
      border: 1px solid var(--fg-border);
      box-shadow: var(--fg-shadow);
      padding: 2.25rem 2rem 1.75rem;
      margin-bottom: 1.5rem;
      position: relative;
      overflow: hidden;
    }
    .profile-header-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--fg-primary), var(--fg-primary-dark), #f0c040);
      border-radius: var(--fg-radius) var(--fg-radius) 0 0;
    }
    .profile-header-inner {
      display: flex;
      align-items: center;
      gap: 1.75rem;
      flex-wrap: wrap;
    }

    /* ── Avatar ── */
    .avatar-wrap { position: relative; flex-shrink: 0; }
    .profile-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--fg-primary) 0%, var(--fg-primary-dark) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.8rem;
      font-weight: 800;
      color: #fff;
      letter-spacing: -1px;
      box-shadow: 0 8px 28px rgba(230,168,0,0.35);
      overflow: hidden;
      transition: box-shadow 0.3s ease;
      user-select: none;
    }
    .profile-avatar img {
      width: 100%; height: 100%;
      object-fit: cover; border-radius: 50%;
    }
    .avatar-upload-overlay {
      position: absolute;
      inset: 0;
      border-radius: 50%;
      background: rgba(0,0,0,0.45);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 0.2rem;
      opacity: 0;
      transition: opacity 0.25s ease;
      cursor: pointer;
      color: #fff;
      font-size: 0.7rem;
      font-weight: 600;
    }
    .avatar-upload-overlay i { font-size: 1.4rem; }
    .avatar-wrap:hover .avatar-upload-overlay { opacity: 1; }
    .avatar-wrap:hover .profile-avatar { box-shadow: 0 10px 32px rgba(230,168,0,0.5); }
    #avatarFileInput { display: none; }

    /* ── Profile info ── */
    .profile-info { flex: 1; min-width: 200px; }
    .profile-name {
      font-size: 1.55rem;
      font-weight: 800;
      color: var(--fg-text);
      margin: 0 0 0.2rem;
      line-height: 1.2;
    }
    .profile-email {
      font-size: 0.88rem;
      color: var(--fg-muted);
      margin: 0 0 0.6rem;
      display: flex;
      align-items: center;
      gap: 0.35rem;
    }
    .profile-badges {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    /* ── Stats row ── */
    .profile-stats {
      display: flex;
      gap: 0;
      margin-top: 1.5rem;
      border-top: 1px solid var(--fg-border);
      padding-top: 1.25rem;
    }
    .stat-item {
      flex: 1;
      text-align: center;
      padding: 0 1rem;
      border-right: 1px solid var(--fg-border);
    }
    .stat-item:last-child { border-right: none; }
    .stat-value {
      font-size: 1.4rem;
      font-weight: 800;
      color: var(--fg-primary);
      line-height: 1;
      margin-bottom: 0.25rem;
    }
    .stat-label {
      font-size: 0.72rem;
      font-weight: 600;
      color: var(--fg-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* ── Section Card ── */
    .section-card {
      background: var(--fg-card-bg);
      border-radius: var(--fg-radius);
      border: 1px solid var(--fg-border);
      padding: 1.75rem 2rem;
      margin-bottom: 1.5rem;
      box-shadow: var(--fg-shadow);
    }
    .section-header {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--fg-border);
    }
    .section-header-icon {
      width: 38px; height: 38px;
      border-radius: 10px;
      background: rgba(230,168,0,0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--fg-primary);
      font-size: 1.05rem;
      flex-shrink: 0;
    }
    .section-header-text h3 {
      font-size: 1rem;
      font-weight: 700;
      color: var(--fg-text);
      margin: 0;
      line-height: 1.2;
    }
    .section-header-text p {
      font-size: 0.78rem;
      color: var(--fg-muted);
      margin: 0;
    }

    /* ── Input prefix icons ── */
    .input-prefix-wrap { position: relative; }
    .input-prefix-wrap .prefix-icon {
      position: absolute;
      left: 0.85rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--fg-muted);
      font-size: 0.95rem;
      pointer-events: none;
      z-index: 4;
      transition: color 0.2s;
    }
    .input-prefix-wrap .form-control { padding-left: 2.5rem; }
    .input-prefix-wrap:focus-within .prefix-icon { color: var(--fg-primary); }

    /* ── Readonly email ── */
    .email-field-wrap { position: relative; }
    .email-field-wrap .form-control[readonly] {
      background: var(--fg-bg);
      color: var(--fg-muted);
      cursor: not-allowed;
      padding-left: 2.5rem;
      padding-right: 9.5rem;
    }
    .email-lock-icon {
      position: absolute;
      left: 0.85rem; top: 50%;
      transform: translateY(-50%);
      color: var(--fg-muted);
      font-size: 0.95rem;
      pointer-events: none;
      z-index: 4;
    }
    .email-readonly-badge {
      position: absolute;
      right: 0.75rem; top: 50%;
      transform: translateY(-50%);
      background: rgba(108,117,125,0.12);
      color: var(--fg-muted);
      font-size: 0.68rem;
      font-weight: 700;
      padding: 0.2rem 0.55rem;
      border-radius: 20px;
      white-space: nowrap;
      pointer-events: none;
      z-index: 4;
    }

    /* ── Password wrapper ── */
    .password-wrapper { position: relative; }
    .password-wrapper .form-control { padding-right: 2.8rem; }
    .toggle-password {
      position: absolute;
      right: 0.75rem; top: 50%;
      transform: translateY(-50%);
      background: none; border: none;
      color: var(--fg-muted);
      cursor: pointer; padding: 0;
      font-size: 1rem; line-height: 1;
      transition: color 0.2s; z-index: 5;
    }
    .toggle-password:hover { color: var(--fg-primary); }

    /* ── Password strength bar ── */
    .pw-strength-wrap { margin-top: 0.5rem; }
    .pw-strength-bar {
      height: 5px; border-radius: 3px;
      background: var(--fg-border);
      overflow: hidden; margin-bottom: 0.3rem;
    }
    .pw-strength-fill {
      height: 100%; border-radius: 3px;
      width: 0%;
      transition: width 0.35s ease, background 0.35s ease;
    }
    .pw-strength-text { font-size: 0.75rem; font-weight: 700; }

    /* ── Confirm match icon ── */
    .confirm-match-icon {
      position: absolute;
      right: 2.85rem; top: 50%;
      transform: translateY(-50%);
      font-size: 1rem; z-index: 5;
      transition: opacity 0.2s;
    }
    .confirm-match-icon.hidden { opacity: 0; pointer-events: none; }

    /* ── Alert animation ── */
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-8px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .alert-animated { animation: fadeInDown 0.3s ease both; }

    /* ── Form label always block ── */
    .form-label {
      display: block;
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--fg-text);
      margin-bottom: 0.4rem;
    }

    /* ── Two-column grid responsive ── */
    @media (max-width: 540px) {
      .form-two-col {
        grid-template-columns: 1fr !important;
      }
    }

    /* ── Save button full width ── */
    .btn-save-full {
      width: 100% !important;
      padding: 0.75rem 1.5rem !important;
      font-size: 1rem !important;
      border-radius: 10px !important;
      margin-top: 0.25rem;
      justify-content: center !important;
    }

    /* ── Sidebar toggle / overlay ── */
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

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }
      .supplier-sidebar {
        position: fixed;
        top: 65px; left: 0; z-index: 200;
        transform: translateX(-100%);
        height: calc(100vh - 65px);
        box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        transition: transform 0.3s ease;
      }
      .supplier-sidebar.open { transform: translateX(0); }
      .supplier-main { padding: 1.25rem; }
      .profile-header-inner { flex-direction: column; align-items: flex-start; gap: 1.25rem; }
      .section-card { padding: 1.25rem 1rem; }
    }
    @media (max-width: 480px) {
      .profile-stats { flex-wrap: wrap; }
      .stat-item {
        flex: 0 0 50%;
        border-right: none;
        border-bottom: 1px solid var(--fg-border);
        padding: 0.75rem 0;
      }
      .stat-item:nth-child(odd) { border-right: 1px solid var(--fg-border); }
      .stat-item:last-child { border-bottom: none; }
    }
  </style>
</head>
<body>


  <!-- ═══════════════════════════════════════════════
       NAVBAR
  ═══════════════════════════════════════════════ -->
  <nav class="fg-navbar" role="navigation" aria-label="Main navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go"
             style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=&quot;font-size:1.2rem;font-weight:800;color:var(--fg-primary);&quot;>&#128295; Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge supplier d-none d-sm-inline-flex">
        <i class="bi bi-box-seam"></i> Supplier
      </span>
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
      <a href="../../../dashboard.php" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.3rem;">
        <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
      </a>
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="supplier-layout">

    <!-- ═══════════════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════════════ -->
    <aside class="supplier-sidebar" id="supplierSidebar" aria-label="Supplier navigation">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Orders</a></li>
        <li><a href="deliveries.php"><i class="bi bi-truck"></i> Deliveries</a></li>
        <li><a href="tech-requests.php"><i class="bi bi-tools"></i> Tech Requests</a></li>
        <li><a href="tech-orders.php"><i class="bi bi-bag-check"></i> Tech Orders</a></li>
        <li><a href="sales-report.php"><i class="bi bi-bar-chart-line"></i> Sales Report</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li><a href="profile.php" class="active"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>

    <!-- ═══════════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════════ -->
    <main class="supplier-main">
      <div class="profile-content">

        <!-- Page heading -->
        <div class="mb-4">
          <h2 style="font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;">My Profile</h2>
          <p style="color:var(--fg-muted);margin:0;font-size:0.9rem;">Manage your account information and security settings</p>
        </div>

        <!-- ─────────────────────────────────────────
             PROFILE HEADER CARD
        ───────────────────────────────────────── -->
        <div class="profile-header-card">
          <div class="profile-header-inner">

            <!-- Avatar with upload overlay -->
            <div class="avatar-wrap">
              <div class="profile-avatar" id="profileAvatar">
                <span id="avatarInitials">?</span>
              </div>
              <div class="avatar-upload-overlay" id="avatarUploadOverlay"
                   role="button" tabindex="0"
                   aria-label="Upload profile photo" title="Click to upload photo">
                <i class="bi bi-camera-fill"></i>
                <span>Change</span>
              </div>
              <input type="file" id="avatarFileInput" accept="image/*" aria-label="Upload avatar image">
            </div>

            <!-- Name / email / badges -->
            <div class="profile-info">
              <h1 class="profile-name" id="profileName">Supplier Name</h1>
              <p class="profile-email">
                <i class="bi bi-envelope-fill" style="color:var(--fg-primary);font-size:0.8rem;"></i>
                <span id="profileEmail">supplier@example.com</span>
              </p>
              <div class="profile-badges">
                <span class="role-badge supplier">
                  <i class="bi bi-box-seam"></i> Supplier
                </span>
                <span style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.78rem;color:var(--fg-muted);">
                  <i class="bi bi-calendar-check" style="color:var(--fg-primary);"></i>
                  Member since <strong id="memberSince" style="color:var(--fg-text);margin-left:0.2rem;">—</strong>
                </span>
              </div>
            </div>
          </div>

          <!-- Stats row -->
          <div class="profile-stats">
            <div class="stat-item">
              <div class="stat-value" id="statProducts">—</div>
              <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-item">
              <div class="stat-value" id="statOrders">—</div>
              <div class="stat-label">Orders</div>
            </div>
            <div class="stat-item">
              <div class="stat-value" id="statMonths">—</div>
              <div class="stat-label">Months Active</div>
            </div>
          </div>
        </div>

        <!-- ─────────────────────────────────────────
             EDIT PROFILE FORM
        ───────────────────────────────────────── -->
        <div class="section-card">
          <div class="section-header">
            <div class="section-header-icon">
              <i class="bi bi-person-lines-fill"></i>
            </div>
            <div class="section-header-text">
              <h3>Edit Profile</h3>
              <p>Update your personal and business information</p>
            </div>
          </div>

          <form id="profileForm" novalidate>

            <!-- First / Last Name -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;" class="form-two-col">
              <div>
                <label class="form-label" for="firstName">First Name</label>
                <div class="input-prefix-wrap">
                  <i class="bi bi-person prefix-icon"></i>
                  <input type="text" class="form-control" id="firstName"
                         placeholder="John" autocomplete="given-name" required>
                </div>
              </div>
              <div>
                <label class="form-label" for="lastName">Last Name</label>
                <div class="input-prefix-wrap">
                  <i class="bi bi-person prefix-icon"></i>
                  <input type="text" class="form-control" id="lastName"
                         placeholder="Doe" autocomplete="family-name" required>
                </div>
              </div>
            </div>

            <!-- Email (readonly) -->
            <div style="margin-bottom:1rem;">
              <label class="form-label" for="email">Email Address</label>
              <div class="email-field-wrap">
                <i class="bi bi-envelope email-lock-icon"></i>
                <input type="email" class="form-control" id="email"
                       placeholder="supplier@example.com"
                       readonly autocomplete="email">
                <span class="email-readonly-badge">
                  <i class="bi bi-lock-fill"></i> Cannot be changed
                </span>
              </div>
            </div>

            <!-- Phone -->
            <div style="margin-bottom:1rem;">
              <label class="form-label" for="phone">Phone Number</label>
              <div class="input-prefix-wrap">
                <i class="bi bi-telephone prefix-icon"></i>
                <input type="tel" class="form-control" id="phone"
                       placeholder="+63 912 345 6789" autocomplete="tel">
              </div>
            </div>

            <!-- Company / Tax ID -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;" class="form-two-col">
              <div>
                <label class="form-label" for="companyName">Company Name</label>
                <div class="input-prefix-wrap">
                  <i class="bi bi-building prefix-icon"></i>
                  <input type="text" class="form-control" id="companyName"
                         placeholder="ABC Parts Supply Co." autocomplete="organization">
                </div>
              </div>
              <div>
                <label class="form-label" for="taxId">Tax ID / TIN</label>
                <div class="input-prefix-wrap">
                  <i class="bi bi-file-earmark-text prefix-icon"></i>
                  <input type="text" class="form-control" id="taxId"
                         placeholder="123-456-789-000">
                </div>
              </div>
            </div>

            <!-- Business Address -->
            <div style="margin-bottom:1rem;">
              <label class="form-label" for="businessAddress">Business Address</label>
              <textarea class="form-control" id="businessAddress" rows="2"
                        placeholder="123 Main St, Manila, Philippines"
                        autocomplete="street-address"
                        style="resize:vertical;display:block;width:100%;"></textarea>
            </div>

            <!-- Bio -->
            <div style="margin-bottom:1.5rem;">
              <label class="form-label" for="bio">
                Bio / About
                <span style="font-weight:400;color:var(--fg-muted);font-size:0.78rem;margin-left:0.35rem;">(optional)</span>
              </label>
              <textarea class="form-control" id="bio" rows="3"
                        placeholder="Tell customers a bit about your business..."
                        style="resize:vertical;display:block;width:100%;"></textarea>
            </div>

            <!-- Alert -->
            <div id="profileAlert" class="auth-alert d-none mb-3" role="alert"></div>

            <!-- Save button -->
            <button type="submit" class="btn-primary-fg btn-save-full">
              <span class="btn-spinner"></span>
              <i class="bi bi-check-circle btn-text"></i>
              <span class="btn-text"> Save Profile</span>
            </button>
          </form>
        </div>

        <!-- ─────────────────────────────────────────
             CHANGE PASSWORD CARD
        ───────────────────────────────────────── -->
        <div class="section-card">
          <div class="section-header">
            <div class="section-header-icon" style="background:rgba(99,102,241,0.12);color:#6366f1;">
              <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="section-header-text">
              <h3>Change Password</h3>
              <p>Keep your account secure with a strong password</p>
            </div>
          </div>

          <form id="passwordForm" novalidate>

            <!-- Current password -->
            <div class="mb-3">
              <label class="form-label" for="currentPassword">Current Password</label>
              <div class="password-wrapper">
                <input type="password" class="form-control" id="currentPassword"
                       placeholder="Enter your current password"
                       autocomplete="current-password" required>
                <button type="button" class="toggle-password" data-target="currentPassword"
                        aria-label="Toggle current password visibility">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <!-- New password + strength bar -->
            <div class="mb-3">
              <label class="form-label" for="newPassword">New Password</label>
              <div class="password-wrapper">
                <input type="password" class="form-control" id="newPassword"
                       placeholder="Enter new password (min. 8 characters)"
                       autocomplete="new-password" required>
                <button type="button" class="toggle-password" data-target="newPassword"
                        aria-label="Toggle new password visibility">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <div class="pw-strength-wrap" id="strengthWrap" style="display:none;">
                <div class="pw-strength-bar">
                  <div class="pw-strength-fill" id="strengthFill"></div>
                </div>
                <span class="pw-strength-text" id="strengthText"></span>
              </div>
            </div>

            <!-- Confirm password + match indicator -->
            <div class="mb-4">
              <label class="form-label" for="confirmPassword">Confirm New Password</label>
              <div class="password-wrapper">
                <input type="password" class="form-control" id="confirmPassword"
                       placeholder="Re-enter new password"
                       autocomplete="new-password" required>
                <i class="bi confirm-match-icon hidden" id="confirmMatchIcon" aria-live="polite"></i>
                <button type="button" class="toggle-password" data-target="confirmPassword"
                        aria-label="Toggle confirm password visibility">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <!-- Alert -->
            <div id="passwordAlert" class="auth-alert d-none mb-3" role="alert"></div>

            <!-- Update button -->
            <button type="submit" class="btn-primary-fg btn-save-full">
              <span class="btn-spinner"></span>
              <i class="bi bi-key btn-text"></i>
              <span class="btn-text"> Update Password</span>
            </button>
          </form>
        </div>

      </div><!-- /.profile-content -->
    </main>
  </div><!-- /.supplier-layout -->


  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script src="../../assets/js/session-timeout.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {

      /* ── Auth guard ── */
      var user = FGAuth.UserStore.get();
      if (!user || user.role !== 'supplier') {
        window.location.href = '../../../login.html';
        return;
      }

      /* ── Helper: get initials from name ── */
      function getInitials(first, last) {
        var f = (first || '').trim();
        var l = (last  || '').trim();
        if (f && l) return (f[0] + l[0]).toUpperCase();
        if (f)      return f.slice(0, 2).toUpperCase();
        if (l)      return l.slice(0, 2).toUpperCase();
        return '?';
      }

      /* ── Populate header ── */
      function populateHeader(u) {
        var fullName = ((u.firstName || '') + ' ' + (u.lastName || '')).trim();
        document.getElementById('navUserName').textContent  = fullName || u.email || 'Supplier';
        document.getElementById('profileName').textContent  = fullName || 'Supplier Name';
        document.getElementById('profileEmail').textContent = u.email  || 'supplier@example.com';

        // Initials in avatar (unless a photo is already set)
        var initials = getInitials(u.firstName, u.lastName);
        document.getElementById('avatarInitials').textContent = initials;

        // If user has a saved avatar data-URL, show it
        if (u.avatarDataUrl) {
          showAvatarImage(u.avatarDataUrl);
        }

        // Member since
        var since = u.createdAt ? new Date(u.createdAt) : (function () {
          var d = new Date();
          d.setMonth(d.getMonth() - 6);
          return d;
        }());
        document.getElementById('memberSince').textContent =
          since.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

        // Stats (simulated)
        var monthsActive = Math.max(1, Math.round(
          (Date.now() - since.getTime()) / (1000 * 60 * 60 * 24 * 30)
        ));
        document.getElementById('statProducts').textContent = u.productCount  || '0';
        document.getElementById('statOrders').textContent   = u.orderCount    || '0';
        document.getElementById('statMonths').textContent   = monthsActive;
      }

      /* ── Populate form fields ── */
      function populateForm(u) {
        document.getElementById('firstName').value       = u.firstName       || '';
        document.getElementById('lastName').value        = u.lastName        || '';
        document.getElementById('email').value           = u.email           || '';
        document.getElementById('phone').value           = u.phone           || '';
        document.getElementById('companyName').value     = u.companyName     || '';
        document.getElementById('taxId').value           = u.taxId           || '';
        document.getElementById('businessAddress').value = u.businessAddress || '';
        document.getElementById('bio').value             = u.bio             || '';
      }

      populateHeader(user);
      populateForm(user);

      /* ── Avatar upload ── */
      var avatarOverlay = document.getElementById('avatarUploadOverlay');
      var avatarInput   = document.getElementById('avatarFileInput');

      function showAvatarImage(src) {
        document.getElementById('profileAvatar').innerHTML = '<img src="' + src + '" alt="Profile photo">';
      }

      avatarOverlay.addEventListener('click', function () { avatarInput.click(); });
      avatarOverlay.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); avatarInput.click(); }
      });

      avatarInput.addEventListener('change', function () {
        var file = avatarInput.files[0];
        if (!file) return;
        if (file.size > 3 * 1024 * 1024) { FGAuth.showAlert('profileAlert', 'Image must be under 3MB.', 'danger'); return; }
        var reader = new FileReader();
        reader.onload = function (e) { showAvatarImage(e.target.result); };
        reader.readAsDataURL(file);
        var fd = new FormData();
        fd.append('action', 'upload_avatar');
        fd.append('avatar', file);
        fetch('../../../backend/profile.php', { method: 'POST', body: fd, credentials: 'include' })
          .then(function(r) { return r.json(); })
          .then(function(d) {
            if (d.success) {
              FGAuth.showAlert('profileAlert', 'Profile photo updated!', 'success');
              var upd = Object.assign({}, FGAuth.UserStore.get(), { avatar_url: d.avatar_url });
              FGAuth.UserStore.save(upd);
            } else { FGAuth.showAlert('profileAlert', d.message || 'Upload failed.', 'danger'); }
          }).catch(function() { FGAuth.showAlert('profileAlert', 'Upload failed.', 'danger'); });
      });

      // Load from DB on page load
      fetch('../../../backend/profile.php?action=get', { credentials: 'include' })
        .then(function(r) { return r.json(); })
        .then(function(d) {
          if (!d.success) return;
          var u = d.user;
          var fullName = ((u.first_name||'') + ' ' + (u.last_name||'')).trim();
          document.getElementById('navUserName').textContent  = fullName || u.email || 'Supplier';
          document.getElementById('profileName').textContent  = fullName || 'Supplier';
          document.getElementById('profileEmail').textContent = u.email  || '';
          document.getElementById('memberSince').textContent  = u.created_at
            ? new Date(u.created_at).toLocaleDateString('en-US', { month:'long', year:'numeric' })
            : '—';
          var monthsActive = u.created_at
            ? Math.max(1, Math.round((Date.now() - new Date(u.created_at).getTime()) / (1000*60*60*24*30)))
            : 0;
          document.getElementById('statMonths').textContent = monthsActive;
          if (u.avatar_url) {
            showAvatarImage('../../../' + u.avatar_url);
          } else {
            var ini = (((u.first_name||'')[0]||'') + ((u.last_name||'')[0]||'')).toUpperCase() || '?';
            document.getElementById('avatarInitials').textContent = ini;
          }
          document.getElementById('firstName').value = u.first_name || '';
          document.getElementById('lastName').value  = u.last_name  || '';
          document.getElementById('email').value     = u.email      || '';
          document.getElementById('phone').value     = u.phone      || '';
          var upd = Object.assign({}, FGAuth.UserStore.get(), {
            firstName: u.first_name, lastName: u.last_name, email: u.email, phone: u.phone, avatar_url: u.avatar_url
          });
          FGAuth.UserStore.save(upd);
        }).catch(function() {});

      /* ── Profile form submit ── */
      document.getElementById('profileForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var fn = document.getElementById('firstName').value.trim();
        var ln = document.getElementById('lastName').value.trim();
        var ph = document.getElementById('phone').value.trim();
        if (!fn || !ln) { FGAuth.showAlert('profileAlert', 'First and last name are required.', 'danger'); return; }

        fetch('../../../backend/profile.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({ action: 'update_profile', first_name: fn, last_name: ln, email: document.getElementById('email').value, phone: ph })
        })
          .then(function(r) { return r.json(); })
          .then(function(d) {
            if (!d.success) { FGAuth.showAlert('profileAlert', d.message || 'Update failed.', 'danger'); return; }
            var fullName = (fn + ' ' + ln).trim();
            document.getElementById('navUserName').textContent = fullName;
            document.getElementById('profileName').textContent = fullName;
            var av = document.getElementById('profileAvatar');
            if (!av.querySelector('img')) document.getElementById('avatarInitials').textContent = ((fn[0]||'')+(ln[0]||'')).toUpperCase();
            var upd = Object.assign({}, FGAuth.UserStore.get(), { firstName: fn, lastName: ln, phone: ph });
            FGAuth.UserStore.save(upd);
            FGAuth.showAlert('profileAlert', 'Profile updated successfully!', 'success');
            setTimeout(function() { FGAuth.hideAlert('profileAlert'); }, 3500);
          })
          .catch(function() { FGAuth.showAlert('profileAlert', 'Network error.', 'danger'); });
      });

      /* ── Password strength bar ── */
      var newPwInput   = document.getElementById('newPassword');
      var strengthWrap = document.getElementById('strengthWrap');
      var strengthFill = document.getElementById('strengthFill');
      var strengthText = document.getElementById('strengthText');

      newPwInput.addEventListener('input', function () {
        var val = newPwInput.value;
        if (!val) { strengthWrap.style.display = 'none'; return; }
        strengthWrap.style.display = 'block';
        var s = FGAuth.checkPasswordStrength ? FGAuth.checkPasswordStrength(val) : { pct: Math.min(val.length * 10, 100), color: '#28A745', label: '' };
        strengthFill.style.width = s.pct + '%';
        strengthFill.style.background = s.color;
        strengthText.textContent = s.label;
        strengthText.style.color = s.color;
        checkConfirmMatch();
      });

      var confirmInput = document.getElementById('confirmPassword');
      var matchIcon    = document.getElementById('confirmMatchIcon');
      function checkConfirmMatch() {
        var nv = newPwInput.value, cv = confirmInput.value;
        if (!cv) { matchIcon.className = 'bi confirm-match-icon hidden'; return; }
        if (nv === cv) { matchIcon.className = 'bi bi-check-circle-fill confirm-match-icon'; matchIcon.style.color = '#28A745'; }
        else           { matchIcon.className = 'bi bi-x-circle-fill confirm-match-icon';     matchIcon.style.color = '#DC3545'; }
      }
      confirmInput.addEventListener('input', checkConfirmMatch);

      /* ── Password form submit ── */
      document.getElementById('passwordForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var cur = document.getElementById('currentPassword').value;
        var nw  = document.getElementById('newPassword').value;
        var cf  = document.getElementById('confirmPassword').value;
        if (!cur) { FGAuth.showAlert('passwordAlert', 'Please enter your current password.', 'danger'); return; }
        if (nw.length < 8) { FGAuth.showAlert('passwordAlert', 'New password must be at least 8 characters.', 'danger'); return; }
        if (nw !== cf) { FGAuth.showAlert('passwordAlert', 'New passwords do not match.', 'danger'); return; }

        fetch('../../../backend/profile.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({ action: 'change_password', current_password: cur, new_password: nw, confirm_password: cf })
        })
          .then(function(r) { return r.json(); })
          .then(function(d) {
            if (!d.success) { FGAuth.showAlert('passwordAlert', d.message || 'Password change failed.', 'danger'); return; }
            FGAuth.showAlert('passwordAlert', 'Password updated successfully!', 'success');
            document.getElementById('passwordForm').reset();
            strengthWrap.style.display = 'none';
            matchIcon.className = 'bi confirm-match-icon hidden';
            setTimeout(function() { FGAuth.hideAlert('passwordAlert'); }, 3500);
          })
          .catch(function() { FGAuth.showAlert('passwordAlert', 'Network error.', 'danger'); });
      });

      /* ── Password toggles ── */
      document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var input = document.getElementById(btn.getAttribute('data-target'));
          if (!input) return;
          var hide = input.type === 'password';
          input.type = hide ? 'text' : 'password';
          var icon = btn.querySelector('i');
          if (icon) icon.className = hide ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
      });

      /* ── Sidebar toggle ── */
      var sidebar   = document.getElementById('supplierSidebar');
      var overlay   = document.getElementById('sidebarOverlay');
      var toggleBtn = document.getElementById('sidebarToggle');
      toggleBtn.addEventListener('click', function () { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
      overlay.addEventListener('click', function () { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    }); // end DOMContentLoaded
  </script>

</body>
</html>



