<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fix&amp;Go � Admin Dashboard</title>
  <link rel="stylesheet" href="../https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../../fixandgo/assets/css/auth.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: #0f1117; color: #e2e8f0; font-family: 'Segoe UI', sans-serif; margin: 0; }
    :root { --admin-primary: #e6a800; --admin-danger: #ef4444; --admin-success: #22c55e; --admin-info: #3b82f6; --admin-card: #1a1d27; --admin-border: #2a2d3a; --admin-muted: #64748b; }
    /* Navbar */
    .admin-nav { background: #13151f; border-bottom: 1px solid var(--admin-border); padding: 0 1.5rem; height: 64px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
    .admin-nav-brand { display: flex; align-items: center; gap: 0.75rem; font-size: 1.1rem; font-weight: 800; color: var(--admin-primary); }
    .admin-nav-brand span { background: var(--admin-primary); color: #000; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.7rem; font-weight: 800; letter-spacing: 1px; }
    .admin-nav-right { display: flex; align-items: center; gap: 1rem; }
    .admin-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--admin-primary); color: #000; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; }
    /* Layout */
    .admin-layout { display: flex; min-height: calc(100vh - 64px); }
    /* Sidebar */
    .admin-sidebar { width: 240px; flex-shrink: 0; background: #13151f; border-right: 1px solid var(--admin-border); padding: 1.5rem 0; position: sticky; top: 64px; height: calc(100vh - 64px); overflow-y: auto; }
    .sidebar-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--admin-muted); padding: 0.75rem 1.25rem 0.35rem; }
    .sidebar-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 1.25rem; color: var(--admin-muted); text-decoration: none; font-size: 0.875rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.15s; cursor: pointer; background: none; border-top: none; border-right: none; border-bottom: none; width: 100%; text-align: left; }
    .sidebar-link:hover { color: #e2e8f0; background: rgba(255,255,255,0.04); border-left-color: var(--admin-border); }
    .sidebar-link.active { color: var(--admin-primary); background: rgba(230,168,0,0.08); border-left-color: var(--admin-primary); font-weight: 700; }
    .sidebar-link i { width: 18px; text-align: center; font-size: 1rem; }
    .sidebar-badge { margin-left: auto; background: var(--admin-danger); color: #fff; font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.45rem; border-radius: 20px; min-width: 18px; text-align: center; }
    /* Main */
    .admin-main { flex: 1; padding: 2rem; min-width: 0; }
    /* Section panels */
    .admin-section { display: none; }
    .admin-section.active { display: block; }
    /* Stats grid */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-card { background: var(--admin-card); border: 1px solid var(--admin-border); border-radius: 14px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.5rem; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.75rem; color: var(--admin-muted); font-weight: 600; }
    /* Page header */
    .page-hdr { margin-bottom: 1.5rem; }
    .page-hdr h2 { font-size: 1.4rem; font-weight: 800; margin: 0 0 0.25rem; color: #f1f5f9; }
    .page-hdr p { color: var(--admin-muted); margin: 0; font-size: 0.875rem; }
    /* Table card */
    .table-card { background: var(--admin-card); border: 1px solid var(--admin-border); border-radius: 14px; overflow: hidden; }
    .table-head-bar { padding: 1rem 1.25rem; border-bottom: 1px solid var(--admin-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }
    .table-head-bar h6 { margin: 0; font-weight: 700; font-size: 0.95rem; color: #f1f5f9; }
    .admin-table { width: 100%; border-collapse: collapse; font-size: 0.83rem; }
    .admin-table th { background: #0f1117; padding: 0.65rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--admin-muted); border-bottom: 1px solid var(--admin-border); white-space: nowrap; }
    .admin-table td { padding: 0.7rem 1rem; border-bottom: 1px solid var(--admin-border); color: #cbd5e1; vertical-align: middle; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-table tr:hover td { background: rgba(255,255,255,0.02); }
    /* Badges */
    .badge { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; white-space: nowrap; }
    .badge-customer    { background: rgba(59,130,246,0.15);  color: #60a5fa; }
    .badge-supplier    { background: rgba(16,185,129,0.15);  color: #34d399; }
    .badge-owner       { background: rgba(230,168,0,0.15);   color: #fbbf24; }
    .badge-technician  { background: rgba(139,92,246,0.15);  color: #a78bfa; }
    .badge-sales       { background: rgba(236,72,153,0.15);  color: #f472b6; }
    .badge-supervisor  { background: rgba(245,158,11,0.15);  color: #fcd34d; }
    .badge-pending     { background: rgba(245,158,11,0.15);  color: #fcd34d; }
    .badge-approved    { background: rgba(34,197,94,0.15);   color: #4ade80; }
    .badge-rejected    { background: rgba(239,68,68,0.15);   color: #f87171; }
    .badge-banned      { background: rgba(239,68,68,0.2);    color: #ef4444; }
    .badge-active      { background: rgba(34,197,94,0.15);   color: #4ade80; }
    /* Buttons */
    .btn-admin { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: none; transition: all 0.15s; }
    .btn-approve { background: rgba(34,197,94,0.15); color: #4ade80; }
    .btn-approve:hover { background: rgba(34,197,94,0.25); }
    .btn-approve:disabled { background: rgba(108,117,125,0.1); color: #64748b; cursor: not-allowed; opacity: 0.5; }
    .btn-reject  { background: rgba(239,68,68,0.15); color: #f87171; }
    .btn-reject:hover  { background: rgba(239,68,68,0.25); }
    .btn-ban     { background: rgba(239,68,68,0.15); color: #f87171; }
    .btn-ban:hover     { background: rgba(239,68,68,0.25); }
    .btn-unban   { background: rgba(34,197,94,0.15); color: #4ade80; }
    .btn-unban:hover   { background: rgba(34,197,94,0.25); }
    .btn-primary-admin { background: var(--admin-primary); color: #000; font-weight: 700; }
    .btn-primary-admin:hover { background: #d4970a; }
    /* Search/filter bar */
    .filter-bar { display: flex; gap: 0.75rem; flex-wrap: wrap; }
    .admin-input { background: #0f1117; border: 1px solid var(--admin-border); border-radius: 8px; color: #e2e8f0; padding: 0.45rem 0.85rem; font-size: 0.83rem; outline: none; transition: border-color 0.15s; }
    .admin-input:focus { border-color: var(--admin-primary); }
    .admin-select { background: #0f1117; border: 1px solid var(--admin-border); border-radius: 8px; color: #e2e8f0; padding: 0.45rem 0.85rem; font-size: 0.83rem; outline: none; cursor: pointer; }
    /* Modal */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #1a1d27; border: 1px solid var(--admin-border); border-radius: 16px; width: 100%; max-width: 460px; box-shadow: 0 24px 64px rgba(0,0,0,0.6); animation: modalIn 0.2s ease; }
    @keyframes modalIn { from { opacity:0; transform:scale(0.95) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }
    .modal-hdr { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border); display: flex; align-items: center; justify-content: space-between; }
    .modal-hdr h5 { margin: 0; font-weight: 800; color: #f1f5f9; font-size: 1rem; }
    .modal-body { padding: 1.5rem; }
    .modal-foot { padding: 1rem 1.5rem; border-top: 1px solid var(--admin-border); display: flex; gap: 0.75rem; justify-content: flex-end; }
    .modal-close { background: none; border: 1px solid var(--admin-border); border-radius: 6px; color: var(--admin-muted); width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .modal-close:hover { border-color: var(--admin-danger); color: var(--admin-danger); }
    .modal-label { font-size: 0.8rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.4rem; display: block; }
    .modal-textarea { width: 100%; background: #0f1117; border: 1px solid var(--admin-border); border-radius: 8px; color: #e2e8f0; padding: 0.65rem 0.85rem; font-size: 0.83rem; resize: vertical; min-height: 80px; outline: none; font-family: inherit; }
    .modal-textarea:focus { border-color: var(--admin-primary); }
    /* Alert */
    .admin-alert { padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.83rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
    .alert-success { background: rgba(34,197,94,0.1); color: #4ade80; border: 1px solid rgba(34,197,94,0.2); }
    .alert-danger  { background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
    /* Empty state */
    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--admin-muted); }
    .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; display: block; }
    /* Spinner */
    .spin { width: 20px; height: 20px; border: 2px solid var(--admin-border); border-top-color: var(--admin-primary); border-radius: 50%; animation: spin 0.7s linear infinite; margin: 2rem auto; }
    @keyframes spin { to { transform: rotate(360deg); } }
    /* Logout btn */
    .btn-logout { background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.2); border-radius: 8px; padding: 0.4rem 0.85rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; transition: all 0.15s; }
    .btn-logout:hover { background: rgba(239,68,68,0.2); }
    @media(max-width:768px) { .admin-sidebar { display: none; } .stats-grid { grid-template-columns: repeat(2,1fr); } }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="admin-nav">
    <div class="admin-nav-brand">
      <i class="bi bi-shield-fill-check" style="color:var(--admin-primary);font-size:1.3rem;"></i>
      Fix&amp;Go <span>ADMIN</span>
    </div>
    <div class="admin-nav-right">
      <span style="font-size:0.82rem;color:var(--admin-muted);" id="navAdminEmail"></span>
      <div class="admin-avatar" id="navAdminInitial">A</div>
      <button class="btn-logout" onclick="adminLogout()">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
    </div>
  </nav>

  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-label">Overview</div>
      <button class="sidebar-link active" onclick="showSection('dashboard')">
        <i class="bi bi-speedometer2"></i> Dashboard
      </button>

      <div class="sidebar-label">User Management</div>
      <button class="sidebar-link" onclick="showSection('applicants')">
        <i class="bi bi-person-check-fill"></i> Applicants
        <span class="sidebar-badge" id="pendingBadge" style="display:none;">0</span>
      </button>
      <button class="sidebar-link" onclick="showSection('all-users')">
        <i class="bi bi-people-fill"></i> All Users
      </button>
      <button class="sidebar-link" onclick="showSection('customers')">
        <i class="bi bi-person-fill"></i> Customers
      </button>
      <button class="sidebar-link" onclick="showSection('suppliers')">
        <i class="bi bi-box-seam-fill"></i> Suppliers
      </button>
      <button class="sidebar-link" onclick="showSection('owners')">
        <i class="bi bi-shop-window"></i> Shop Owners
      </button>
      <button class="sidebar-link" onclick="showSection('technicians')">
        <i class="bi bi-tools"></i> Technicians
      </button>
      <button class="sidebar-link" onclick="showSection('banned')">
        <i class="bi bi-slash-circle-fill"></i> Banned Users
      </button>

      <div class="sidebar-label">Reports</div>
      <button class="sidebar-link" onclick="showSection('login-logs')">
        <i class="bi bi-clock-history"></i> Login Logs
      </button>
    </aside>

    <!-- Main -->
    <main class="admin-main">
      <div id="globalAlert" style="display:none;"></div>

      <!-- -------------------------------------------
           DASHBOARD SECTION
      ------------------------------------------- -->
      <div class="admin-section active" id="section-dashboard">
        <div class="page-hdr">
          <h2><i class="bi bi-speedometer2" style="color:var(--admin-primary);margin-right:0.5rem;"></i>Admin Dashboard</h2>
          <p>System overview � Fix&amp;Go platform</p>
        </div>

        <div class="stats-grid" id="statsGrid">
          <div class="spin"></div>
        </div>

        <!-- Pending applicants quick view -->
        <div class="table-card">
          <div class="table-head-bar">
            <h6><i class="bi bi-person-check-fill" style="color:var(--admin-primary);margin-right:0.4rem;"></i>Pending Applications</h6>
            <button class="btn-admin btn-primary-admin" onclick="showSection('applicants')">
              <i class="bi bi-arrow-right"></i> View All
            </button>
          </div>
          <div style="overflow-x:auto;">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Applied</th><th>Actions</th>
                </tr>
              </thead>
              <tbody id="dashPendingBody">
                <tr><td colspan="6"><div class="spin"></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- -------------------------------------------
           APPLICANTS SECTION
      ------------------------------------------- -->
      <div class="admin-section" id="section-applicants">
        <div class="page-hdr">
          <h2><i class="bi bi-person-check-fill" style="color:var(--admin-primary);margin-right:0.5rem;"></i>Applicants</h2>
          <p>Review and approve Supplier, Shop Owner &amp; Technician applications</p>
        </div>
        <div class="table-card">
          <div class="table-head-bar">
            <h6>Pending Applications</h6>
            <button class="btn-admin btn-primary-admin" onclick="loadApplicants()">
              <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
          </div>
          <div style="overflow-x:auto;">
            <table class="admin-table">
              <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Applied</th><th>Actions</th></tr>
              </thead>
              <tbody id="applicantsBody">
                <tr><td colspan="7"><div class="spin"></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- -------------------------------------------
           ALL USERS / ROLE-FILTERED SECTIONS
      ------------------------------------------- -->
      <div class="admin-section" id="section-all-users">
        <div class="page-hdr">
          <h2><i class="bi bi-people-fill" style="color:var(--admin-primary);margin-right:0.5rem;"></i>All Users</h2>
          <p>Every registered user on the platform</p>
        </div>
        <div id="usersTableWrap"></div>
      </div>

      <div class="admin-section" id="section-customers">
        <div class="page-hdr">
          <h2><i class="bi bi-person-fill" style="color:#60a5fa;margin-right:0.5rem;"></i>Customers</h2>
          <p>All registered customer accounts</p>
        </div>
        <div id="customersTableWrap"></div>
      </div>

      <div class="admin-section" id="section-suppliers">
        <div class="page-hdr">
          <h2><i class="bi bi-box-seam-fill" style="color:#34d399;margin-right:0.5rem;"></i>Suppliers</h2>
          <p>Approved and pending supplier accounts</p>
        </div>
        <div id="suppliersTableWrap"></div>
      </div>

      <div class="admin-section" id="section-owners">
        <div class="page-hdr">
          <h2><i class="bi bi-shop-window" style="color:var(--admin-primary);margin-right:0.5rem;"></i>Shop Owners</h2>
          <p>Approved and pending shop owner accounts</p>
        </div>
        <div id="ownersTableWrap"></div>
      </div>

      <div class="admin-section" id="section-technicians">
        <div class="page-hdr">
          <h2><i class="bi bi-tools" style="color:#a78bfa;margin-right:0.5rem;"></i>Phone Technicians</h2>
          <p>All registered phone technicians</p>
        </div>
        <div id="techniciansTableWrap"></div>
      </div>

      <div class="admin-section" id="section-banned">
        <div class="page-hdr">
          <h2><i class="bi bi-slash-circle-fill" style="color:#ef4444;margin-right:0.5rem;"></i>Banned Users</h2>
          <p>Users currently banned from the platform</p>
        </div>
        <div id="bannedTableWrap"></div>
      </div>

      <!-- ═══════════════════════════════════════════
           LOGIN LOGS SECTION
      ═══════════════════════════════════════════ -->
      <div class="admin-section" id="section-login-logs">
        <div class="page-hdr">
          <h2><i class="bi bi-clock-history" style="color:var(--admin-primary);margin-right:0.5rem;"></i>Login &amp; Logout Logs</h2>
          <p>Track when users log in, log out, or get timed out</p>
        </div>

        <!-- Filter bar -->
        <div class="table-card" style="margin-bottom:1rem;">
          <div class="table-head-bar" style="flex-wrap:wrap;gap:0.75rem;">
            <h6><i class="bi bi-funnel" style="color:var(--admin-primary);margin-right:0.4rem;"></i>Filters</h6>
            <div class="filter-bar">
              <input type="text"   id="logFilterUser"   class="admin-input" placeholder="Search name or email…" style="min-width:200px;">
              <select id="logFilterAction" class="admin-select">
                <option value="">All Actions</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="session_expired">Session Expired</option>
              </select>
              <input type="date" id="logFilterDate" class="admin-input" title="Filter by date">
              <button class="btn-admin btn-primary-admin" onclick="loadLoginLogs()">
                <i class="bi bi-search"></i> Search
              </button>
              <button class="btn-admin" style="background:rgba(255,255,255,0.05);color:#94a3b8;" onclick="clearLogFilters()">
                <i class="bi bi-x-circle"></i> Clear
              </button>
            </div>
          </div>
        </div>

        <!-- Logs table -->
        <div class="table-card">
          <div class="table-head-bar">
            <h6 id="logTableTitle">Recent Activity</h6>
            <button class="btn-admin btn-primary-admin" onclick="loadLoginLogs()">
              <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
          </div>
          <div style="overflow-x:auto;">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>User</th>
                  <th>Role</th>
                  <th>Action</th>
                  <th>IP Address</th>
                  <th>Browser / Device</th>
                  <th>Date &amp; Time</th>
                </tr>
              </thead>
              <tbody id="loginLogsBody">
                <tr><td colspan="7"><div class="spin"></div></td></tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div id="logPagination" style="padding:0.75rem 1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;border-top:1px solid var(--admin-border);">
            <span id="logPageInfo" style="font-size:0.78rem;color:var(--admin-muted);"></span>
            <div style="display:flex;gap:0.4rem;" id="logPageBtns"></div>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- -------------------------------------------
       APPROVE MODAL
  ------------------------------------------- -->
  <div class="modal-overlay" id="approveModal">
    <div class="modal-box">
      <div class="modal-hdr">
        <h5><i class="bi bi-check-circle-fill" style="color:#4ade80;margin-right:0.5rem;"></i>Approve Application</h5>
        <button class="modal-close" onclick="closeModal('approveModal')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <p style="color:#94a3b8;font-size:0.85rem;margin-bottom:1rem;">
          Approving will activate this account. The user will be able to log in immediately.
        </p>
        <div style="background:#0f1117;border:1px solid var(--admin-border);border-radius:8px;padding:0.75rem;margin-bottom:1rem;">
          <div style="font-size:0.82rem;color:#f1f5f9;font-weight:700;" id="approveUserName"></div>
          <div style="font-size:0.78rem;color:var(--admin-muted);" id="approveUserEmail"></div>
          <div style="margin-top:0.35rem;" id="approveUserRole"></div>
        </div>
        <label class="modal-label">Notes (optional)</label>
        <textarea class="modal-textarea" id="approveNotes" placeholder="Welcome message or notes for the applicant�"></textarea>
      </div>
      <div class="modal-foot">
        <button class="btn-admin" style="background:rgba(255,255,255,0.05);color:#94a3b8;" onclick="closeModal('approveModal')">Cancel</button>
        <button class="btn-admin btn-approve" id="btnConfirmApprove" onclick="confirmApprove()">
          <i class="bi bi-check-lg"></i> Approve
        </button>
      </div>
    </div>
  </div>

  <!-- -------------------------------------------
       REJECT MODAL
  ------------------------------------------- -->
  <div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
      <div class="modal-hdr">
        <h5><i class="bi bi-x-circle-fill" style="color:#f87171;margin-right:0.5rem;"></i>Reject Application</h5>
        <button class="modal-close" onclick="closeModal('rejectModal')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <div style="background:#0f1117;border:1px solid var(--admin-border);border-radius:8px;padding:0.75rem;margin-bottom:1rem;">
          <div style="font-size:0.82rem;color:#f1f5f9;font-weight:700;" id="rejectUserName"></div>
          <div style="font-size:0.78rem;color:var(--admin-muted);" id="rejectUserEmail"></div>
        </div>
        <label class="modal-label">Rejection Reason <span style="color:#ef4444;">*</span></label>
        <textarea class="modal-textarea" id="rejectReason" placeholder="Explain why the application is being rejected�"></textarea>
      </div>
      <div class="modal-foot">
        <button class="btn-admin" style="background:rgba(255,255,255,0.05);color:#94a3b8;" onclick="closeModal('rejectModal')">Cancel</button>
        <button class="btn-admin btn-reject" onclick="confirmReject()">
          <i class="bi bi-x-lg"></i> Reject
        </button>
      </div>
    </div>
  </div>

  <!-- -------------------------------------------
       BAN MODAL
  ------------------------------------------- -->
  <div class="modal-overlay" id="banModal">
    <div class="modal-box">
      <div class="modal-hdr">
        <h5><i class="bi bi-slash-circle-fill" style="color:#ef4444;margin-right:0.5rem;"></i>Ban User</h5>
        <button class="modal-close" onclick="closeModal('banModal')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <div style="background:#0f1117;border:1px solid var(--admin-border);border-radius:8px;padding:0.75rem;margin-bottom:1rem;">
          <div style="font-size:0.82rem;color:#f1f5f9;font-weight:700;" id="banUserName"></div>
          <div style="font-size:0.78rem;color:var(--admin-muted);" id="banUserEmail"></div>
        </div>
        <label class="modal-label">Ban Reason <span style="color:#ef4444;">*</span></label>
        <textarea class="modal-textarea" id="banReason" placeholder="Reason for banning this user�"></textarea>
      </div>
      <div class="modal-foot">
        <button class="btn-admin" style="background:rgba(255,255,255,0.05);color:#94a3b8;" onclick="closeModal('banModal')">Cancel</button>
        <button class="btn-admin btn-ban" onclick="confirmBan()">
          <i class="bi bi-slash-circle"></i> Ban User
        </button>
      </div>
    </div>
  </div>

  <!-- -------------------------------------------
       DOCUMENTS MODAL
  ------------------------------------------- -->
  <div class="modal-overlay" id="docsModal">
    <div class="modal-box" style="max-width:560px;">
      <div class="modal-hdr">
        <h5><i class="bi bi-file-earmark-text" style="color:#60a5fa;margin-right:0.5rem;"></i>Application Documents</h5>
        <button class="modal-close" onclick="closeModal('docsModal')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body" id="docsModalContent" style="max-height:70vh;overflow-y:auto;">
        <div class="spin"></div>
      </div>
      <div class="modal-foot">
        <button class="btn-admin" style="background:rgba(255,255,255,0.05);color:#94a3b8;" onclick="closeModal('docsModal')">Close</button>
      </div>
    </div>
  </div>

  <script src="../https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../../fixandgo/assets/js/auth-utils.js"></script>

  <script>
  'use strict';

  const API = '../../../../fixandgo/backend/admin.php';
  let currentAction = { type: null, userId: null };

  // -- Auth check --------------------------------------------------------
  document.addEventListener('DOMContentLoaded', function () {
    fetch(API + '?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) { window.location.href = '../../../login.php'; return; }
        renderStats(d.stats);
        loadApplicants();
        loadDashPending();
      })
      .catch(() => { window.location.href = '../../../login.php'; });

    // Set admin name from session (via a quick ping)
    const stored = sessionStorage.getItem('fg_user');
    if (stored) {
      try {
        const u = JSON.parse(stored);
        document.getElementById('navAdminEmail').textContent = u.email || '';
        const init = ((u.firstName||'')[0]||'A').toUpperCase();
        document.getElementById('navAdminInitial').textContent = init;
      } catch(e) {}
    }
  });

  // -- Section navigation ------------------------------------------------
  function showSection(name) {
    document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
    document.getElementById('section-' + name).classList.add('active');
    document.querySelectorAll('.sidebar-link').forEach(l => {
      if (l.getAttribute('onclick') && l.getAttribute('onclick').includes("'" + name + "'")) {
        l.classList.add('active');
      }
    });

    // Load data for the section
    const loaders = {
      'all-users':   () => loadUsers('usersTableWrap', ''),
      'customers':   () => loadUsers('customersTableWrap', 'customer'),
      'suppliers':   () => loadUsers('suppliersTableWrap', 'supplier'),
      'owners':      () => loadUsers('ownersTableWrap', 'owner'),
      'technicians': () => loadUsers('techniciansTableWrap', 'phone_technician'),
      'banned':      () => loadUsers('bannedTableWrap', '', 'banned'),
      'applicants':  () => loadApplicants(),
      'login-logs':  () => loadLoginLogs(1),
    };
    if (loaders[name]) loaders[name]();
  }

  // -- Stats -------------------------------------------------------------
  function renderStats(s) {
    const items = [
      { label: 'Customers',    value: s.total_customers,    color: '#3b82f6', icon: 'bi-person-fill' },
      { label: 'Suppliers',    value: s.total_suppliers,    color: '#22c55e', icon: 'bi-box-seam-fill' },
      { label: 'Shop Owners',  value: s.total_owners,       color: '#e6a800', icon: 'bi-shop-window' },
      { label: 'Technicians',  value: s.total_technicians,  color: '#8b5cf6', icon: 'bi-tools' },
      { label: 'Sales Staff',  value: s.total_sales_person, color: '#ec4899', icon: 'bi-briefcase-fill' },
      { label: 'Supervisors',  value: s.total_supervisors,  color: '#f59e0b', icon: 'bi-person-badge-fill' },
      { label: 'Pending',      value: s.pending_applicants, color: '#f59e0b', icon: 'bi-hourglass-split', alert: s.pending_applicants > 0 },
      { label: 'Banned',       value: s.banned_users,       color: '#ef4444', icon: 'bi-slash-circle-fill' },
      { label: 'New Today',    value: s.new_today,          color: '#06b6d4', icon: 'bi-person-plus-fill' },
    ];

    document.getElementById('statsGrid').innerHTML = items.map(i => `
      <div class="stat-card" style="${i.alert ? 'border-color:rgba(245,158,11,0.4);' : ''}">
        <div class="stat-icon" style="background:${i.color}22;color:${i.color};">
          <i class="bi ${i.icon}"></i>
        </div>
        <div class="stat-value" style="color:${i.color};">${i.value}</div>
        <div class="stat-label">${i.label}</div>
      </div>`).join('');

    // Update pending badge
    if (s.pending_applicants > 0) {
      const badge = document.getElementById('pendingBadge');
      badge.textContent = s.pending_applicants;
      badge.style.display = 'inline-block';
    }
  }

  // -- Load applicants ---------------------------------------------------
  function loadApplicants() {
    fetch(API + '?action=applicants', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        renderApplicants(d.applicants || [], 'applicantsBody');
        renderApplicants((d.applicants || []).slice(0, 5), 'dashPendingBody');
      })
      .catch(() => setBodyError('applicantsBody', 7));
  }

  function loadDashPending() {
    fetch(API + '?action=applicants', { credentials: 'include' })
      .then(r => r.json())
      .then(d => renderApplicants((d.applicants || []).slice(0, 5), 'dashPendingBody'))
      .catch(() => setBodyError('dashPendingBody', 6));
  }

  function renderApplicants(list, bodyId) {
    const tbody = document.getElementById(bodyId);
    if (!tbody) return;
    if (!list.length) {
      tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="bi bi-check-circle"></i>No pending applications</div></td></tr>`;
      return;
    }
    tbody.innerHTML = list.map(u => `
      <tr>
        <td style="color:var(--admin-muted);">#${u.id}</td>
        <td>
          <div style="font-weight:600;color:#f1f5f9;">${esc(u.first_name)} ${esc(u.last_name)}</div>
          <div style="font-size:0.75rem;color:var(--admin-muted);">${esc(u.company_name || '')}</div>
        </td>
        <td style="color:#94a3b8;">${esc(u.email)}</td>
        <td>${esc(u.phone || '—')}</td>
        <td>${u.role === 'supplier' ? '<span class="badge badge-supplier">📦 Supplier</span>' : u.role === 'owner' ? '<span class="badge badge-owner">🏪 Shop Owner</span>' : '<span class="badge badge-technician">🔧 Technician</span>'}</td>
        <td style="color:var(--admin-muted);font-size:0.78rem;">${fmtDate(u.submitted_at || u.created_at)}</td>
        <td>
          <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
            <button class="btn-admin" style="background:rgba(59,130,246,0.15);color:#60a5fa;" onclick="viewApplicantDocs(${u.id})">
              <i class="bi bi-file-earmark-text"></i> Docs
            </button>
            <button class="btn-admin btn-approve" id="approveBtn_${u.id}" onclick="openApprove(${u.id},'${esc(u.first_name)} ${esc(u.last_name)}','${esc(u.email)}','${u.role}')" disabled title="Review documents first">
              <i class="bi bi-check-lg"></i> Approve
            </button>
            <button class="btn-admin btn-reject" onclick="openReject(${u.id},'${esc(u.first_name)} ${esc(u.last_name)}','${esc(u.email)}')">
              <i class="bi bi-x-lg"></i> Reject
            </button>
          </div>
        </td>
      </tr>`).join('');
  }

  // -- Load users --------------------------------------------------------
  function loadUsers(wrapId, role, status) {
    const wrap = document.getElementById(wrapId);
    if (!wrap) return;
    wrap.innerHTML = '<div class="spin"></div>';

    let url = API + '?action=users';
    if (role)   url += '&role='   + encodeURIComponent(role);
    if (status) url += '&status=' + encodeURIComponent(status);

    fetch(url, { credentials: 'include' })
      .then(r => r.json())
      .then(d => renderUsersTable(wrap, d.users || [], role, status))
      .catch(() => { wrap.innerHTML = '<div class="empty-state"><i class="bi bi-exclamation-triangle"></i>Failed to load users.</div>'; });
  }

  function renderUsersTable(wrap, users, role, status) {
    const searchId = 'search_' + (role || status || 'all');
    wrap.innerHTML = `
      <div class="table-card">
        <div class="table-head-bar">
          <h6>${users.length} user${users.length !== 1 ? 's' : ''}</h6>
          <div class="filter-bar">
            <input class="admin-input" id="${searchId}" placeholder="?? Search name or email�" oninput="filterTable('${searchId}','usersTbody_${searchId}')" style="width:220px;">
          </div>
        </div>
        <div style="overflow-x:auto;">
          <table class="admin-table">
            <thead>
              <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody id="usersTbody_${searchId}">
              ${users.length ? users.map(u => userRow(u)).join('') : '<tr><td colspan="7"><div class="empty-state"><i class="bi bi-people"></i>No users found.</div></td></tr>'}
            </tbody>
          </table>
        </div>
      </div>`;
  }

  function userRow(u) {
    const roleBadge = {
      customer: '<span class="badge badge-customer">👤 Customer</span>',
      supplier: '<span class="badge badge-supplier">📦 Supplier</span>',
      owner:    '<span class="badge badge-owner">🏪 Owner</span>',
      phone_technician: '<span class="badge badge-technician">🔧 Technician</span>',
      sales_person: '<span class="badge badge-sales">💼 Sales</span>',
      supervisor: '<span class="badge badge-supervisor">👔 Supervisor</span>',
    }[u.role] || `<span class="badge">${u.role}</span>`;

    let statusBadge = u.is_banned
      ? '<span class="badge badge-banned">🚫 Banned</span>'
      : u.is_active
        ? '<span class="badge badge-active">✅ Active</span>'
        : '<span class="badge badge-rejected">⛔ Inactive</span>';

    if (u.application_status === 'pending') {
      statusBadge += ' <span class="badge badge-pending">⏳ Pending</span>';
    }

    // Show locked badge if account is currently locked
    const isLocked = u.locked_until && new Date(u.locked_until) > new Date();
    if (isLocked) {
      statusBadge += ' <span class="badge" style="background:rgba(245,158,11,0.2);color:#fcd34d;">🔒 Locked</span>';
    }

    // Check if user is supplier, owner, or technician (has application documents)
    const hasDocuments = u.role === 'supplier' || u.role === 'owner' || u.role === 'phone_technician';

    const actions = u.is_banned
      ? `<button class="btn-admin btn-unban" onclick="openUnban(${u.id},'${esc(u.first_name)} ${esc(u.last_name)}')"><i class="bi bi-check-circle"></i> Unban</button>`
      : `<div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
           ${hasDocuments ? `<button class="btn-admin" style="background:rgba(59,130,246,0.15);color:#60a5fa;" onclick="viewUserDocs(${u.id})"><i class="bi bi-file-earmark-text"></i> Docs</button>` : ''}
           ${isLocked ? `<button class="btn-admin" style="background:rgba(245,158,11,0.15);color:#fcd34d;" onclick="unlockAccount(${u.id},'${esc(u.first_name)} ${esc(u.last_name)}')"><i class="bi bi-unlock-fill"></i> Unlock</button>` : ''}
           <button class="btn-admin btn-ban" onclick="openBan(${u.id},'${esc(u.first_name)} ${esc(u.last_name)}','${esc(u.email)}')"><i class="bi bi-slash-circle"></i> Ban</button>
         </div>`;

    return `<tr>
      <td style="color:var(--admin-muted);">#${u.id}</td>
      <td style="font-weight:600;color:#f1f5f9;">${esc(u.first_name)} ${esc(u.last_name)}</td>
      <td style="color:#94a3b8;">${esc(u.email)}</td>
      <td>${roleBadge}</td>
      <td>${statusBadge}</td>
      <td style="color:var(--admin-muted);font-size:0.78rem;">${fmtDate(u.created_at)}</td>
      <td>${actions}</td>
    </tr>`;
  }

  // -- Filter table ------------------------------------------------------
  function filterTable(inputId, tbodyId) {
    const q = document.getElementById(inputId).value.toLowerCase();
    const rows = document.getElementById(tbodyId).querySelectorAll('tr');
    rows.forEach(r => {
      r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  // -- Approve modal -----------------------------------------------------
  function openApprove(id, name, email, role) {
    currentAction = { type: 'approve', userId: id };
    document.getElementById('approveUserName').textContent = name;
    document.getElementById('approveUserEmail').textContent = email;
    document.getElementById('approveUserRole').innerHTML =
      role === 'supplier'
        ? '<span class="badge badge-supplier">📦 Supplier</span>'
        : role === 'phone_technician'
        ? '<span class="badge badge-technician">🔧 Technician</span>'
        : '<span class="badge badge-owner">🏪 Shop Owner</span>';
    document.getElementById('approveNotes').value = '';
    document.getElementById('approveModal').classList.add('open');
  }

  function confirmApprove() {
    const btn = document.getElementById('btnConfirmApprove');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Approving�';

    fetch(API, {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'approve',
        user_id: currentAction.userId,
        notes: document.getElementById('approveNotes').value.trim()
      })
    })
    .then(r => r.json())
    .then(d => {
      closeModal('approveModal');
      showAlert(d.success ? 'success' : 'danger', d.message);
      if (d.success) { loadApplicants(); loadStats(); }
    })
    .catch(() => showAlert('danger', 'Request failed.'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg"></i> Approve'; });
  }

  // -- Reject modal ------------------------------------------------------
  function openReject(id, name, email) {
    currentAction = { type: 'reject', userId: id };
    document.getElementById('rejectUserName').textContent = name;
    document.getElementById('rejectUserEmail').textContent = email;
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.add('open');
  }

  function confirmReject() {
    const reason = document.getElementById('rejectReason').value.trim();
    if (!reason) { document.getElementById('rejectReason').style.borderColor = '#ef4444'; return; }

    fetch(API, {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'reject', user_id: currentAction.userId, notes: reason })
    })
    .then(r => r.json())
    .then(d => {
      closeModal('rejectModal');
      showAlert(d.success ? 'success' : 'danger', d.message);
      if (d.success) { loadApplicants(); loadStats(); }
    })
    .catch(() => showAlert('danger', 'Request failed.'));
  }

  // -- Ban modal ---------------------------------------------------------
  function openBan(id, name, email) {
    currentAction = { type: 'ban', userId: id };
    document.getElementById('banUserName').textContent = name;
    document.getElementById('banUserEmail').textContent = email;
    document.getElementById('banReason').value = '';
    document.getElementById('banModal').classList.add('open');
  }

  function confirmBan() {
    const reason = document.getElementById('banReason').value.trim();
    if (!reason) { document.getElementById('banReason').style.borderColor = '#ef4444'; return; }

    fetch(API, {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'ban', user_id: currentAction.userId, reason })
    })
    .then(r => r.json())
    .then(d => {
      closeModal('banModal');
      showAlert(d.success ? 'success' : 'danger', d.message);
      if (d.success) loadStats();
    })
    .catch(() => showAlert('danger', 'Request failed.'));
  }

  // -- Unban -------------------------------------------------------------
  function openUnban(id, name) {
    if (!confirm('Unban ' + name + '? They will regain access to the platform.')) return;
    fetch(API, {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'unban', user_id: id })
    })
    .then(r => r.json())
    .then(d => {
      showAlert(d.success ? 'success' : 'danger', d.message);
      if (d.success) loadStats();
    })
    .catch(() => showAlert('danger', 'Request failed.'));
  }

  // -- View Documents ----------------------------------------------------
  let _docsCache = {};
  function viewDocs(appId) {
    // Find the applicant from the last loaded list
    fetch(API + '?action=applicants', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const app = (d.applicants || []).find(a => a.id == appId);
        if (!app) { showAlert('danger', 'Application not found.'); return; }
        const base = '../../../../fixandgo/';
        const docRow = (label, path, required) => {
          if (!path) return `<div style="display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0;border-bottom:1px solid #2a2d3a;">
            <i class="bi bi-x-circle" style="color:#ef4444;font-size:1.1rem;"></i>
            <span style="color:#94a3b8;font-size:0.83rem;">${label} — ${required ? '<span style="color:#ef4444;">Not uploaded</span>' : '<span style="color:#64748b;">Not provided</span>'}</span>
          </div>`;
          const ext = path.split('.').pop().toLowerCase();
          const isImg = ['jpg','jpeg','png','webp'].includes(ext);
          return `<div style="padding:0.6rem 0;border-bottom:1px solid #2a2d3a;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.4rem;">
              <span style="font-size:0.83rem;font-weight:600;color:#e2e8f0;"><i class="bi bi-check-circle" style="color:#4ade80;margin-right:0.4rem;"></i>${label}</span>
              <a href="${base}${path}" target="_blank" class="btn-admin" style="background:rgba(59,130,246,0.15);color:#60a5fa;font-size:0.75rem;">
                <i class="bi bi-box-arrow-up-right"></i> Open
              </a>
            </div>
            ${isImg ? `<img src="${base}${path}" style="max-width:100%;max-height:180px;border-radius:8px;border:1px solid #2a2d3a;object-fit:contain;" onerror="this.style.display='none'">` : ''}
          </div>`;
        };

        document.getElementById('docsModalContent').innerHTML = `
          <div style="background:#0f1117;border:1px solid #2a2d3a;border-radius:8px;padding:0.85rem;margin-bottom:1rem;">
            <div style="font-weight:700;color:#f1f5f9;margin-bottom:0.25rem;">${esc(app.first_name)} ${esc(app.last_name)}</div>
            <div style="font-size:0.8rem;color:#94a3b8;">${esc(app.email)} — ${esc(app.phone)}</div>
            <div style="font-size:0.8rem;color:#94a3b8;margin-top:0.2rem;">Company: <strong style="color:#e2e8f0;">${esc(app.company_name)}</strong>${app.shop_name ? ' — Shop: <strong style="color:#e2e8f0;">' + esc(app.shop_name) + '</strong>' : ''}</div>
          </div>
          ${docRow('Government-Issued ID', app.doc_gov_id, true)}
          ${docRow('BIR Certificate', app.doc_bir, false)}
          ${docRow('DTI / SEC Registration', app.doc_dti, app.role === 'owner')}
          ${docRow('Bank Account Proof', app.doc_bank, true)}
        `;
        document.getElementById('docsModal').classList.add('open');
      });
  }

  // -- View Applicant Documents with Individual Approval --------------------
  function viewApplicantDocs(appId) {
    // Show modal immediately with loading state
    document.getElementById('docsModalContent').innerHTML = '<div class="spin"></div>';
    document.getElementById('docsModal').classList.add('open');

    // Fetch documents with approval statuses directly from application ID
    fetch('../../../../fixandgo/backend/document_approvals.php?action=get_documents&application_id=' + appId, { credentials: 'include' })
      .then(r => {
        if (!r.ok) {
          return r.text().then(t => { throw new Error('HTTP ' + r.status + ': ' + t.substring(0, 200)); });
        }
        return r.json();
      })
      .then(docData => {
        if (!docData.success) {
          document.getElementById('docsModalContent').innerHTML = `
            <div style="text-align:center;padding:2rem;color:#f87171;">
              <i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;display:block;margin-bottom:0.75rem;"></i>
              <div style="font-weight:700;margin-bottom:0.5rem;">Failed to load documents</div>
              <div style="font-size:0.82rem;color:#94a3b8;">${esc(docData.message || 'Unknown error')}</div>
            </div>`;
          return;
        }
        renderDocumentsModal(docData.application, docData.documents, appId);
      })
      .catch(err => {
        console.error('[viewApplicantDocs]', err);
        document.getElementById('docsModalContent').innerHTML = `
          <div style="text-align:center;padding:2rem;color:#f87171;">
            <i class="bi bi-wifi-off" style="font-size:2rem;display:block;margin-bottom:0.75rem;"></i>
            <div style="font-weight:700;margin-bottom:0.5rem;">Network Error</div>
            <div style="font-size:0.82rem;color:#94a3b8;">${esc(err.message)}</div>
            <div style="font-size:0.75rem;color:#64748b;margin-top:0.5rem;">Check browser console for details. Make sure you are logged in as admin.</div>
          </div>`;
      });
  }

  // -- View User Documents with Individual Approval -------------------------
  function viewUserDocs(userId) {
    // Show modal immediately with loading state
    document.getElementById('docsModalContent').innerHTML = '<div class="spin"></div>';
    document.getElementById('docsModal').classList.add('open');

    // First get the application ID from user
    fetch(API + '?action=user_documents&user_id=' + userId, { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success || !d.application) { 
          document.getElementById('docsModalContent').innerHTML = `
            <div style="text-align:center;padding:2rem;color:#f87171;">
              <i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;display:block;margin-bottom:0.75rem;"></i>
              <div style="font-weight:700;margin-bottom:0.5rem;">No documents found</div>
              <div style="font-size:0.82rem;color:#94a3b8;">${esc(d.message || 'No application documents found for this user.')}</div>
            </div>`;
          return; 
        }
        
        const appId = d.application.id;
        
        // Now fetch documents with approval statuses
        fetch('../../../../fixandgo/backend/document_approvals.php?action=get_documents&application_id=' + appId, { credentials: 'include' })
          .then(r => r.json())
          .then(docData => {
            if (!docData.success) {
              document.getElementById('docsModalContent').innerHTML = `
                <div style="text-align:center;padding:2rem;color:#f87171;">
                  <i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;display:block;margin-bottom:0.75rem;"></i>
                  <div style="font-weight:700;margin-bottom:0.5rem;">Failed to load document statuses</div>
                  <div style="font-size:0.82rem;color:#94a3b8;">${esc(docData.message || 'Unknown error')}</div>
                </div>`;
              return;
            }
            renderDocumentsModal(docData.application, docData.documents, appId);
          })
          .catch(err => {
            console.error('[viewUserDocs inner]', err);
            document.getElementById('docsModalContent').innerHTML = `
              <div style="text-align:center;padding:2rem;color:#f87171;">
                <i class="bi bi-wifi-off" style="font-size:2rem;display:block;margin-bottom:0.75rem;"></i>
                <div style="font-weight:700;margin-bottom:0.5rem;">Network Error</div>
                <div style="font-size:0.82rem;color:#94a3b8;">${esc(err.message)}</div>
              </div>`;
          });
      })
      .catch(err => {
        console.error('[viewUserDocs]', err);
        document.getElementById('docsModalContent').innerHTML = `
          <div style="text-align:center;padding:2rem;color:#f87171;">
            <i class="bi bi-wifi-off" style="font-size:2rem;display:block;margin-bottom:0.75rem;"></i>
            <div style="font-weight:700;margin-bottom:0.5rem;">Network Error</div>
            <div style="font-size:0.82rem;color:#94a3b8;">${esc(err.message)}</div>
          </div>`;
      });
  }

  function renderDocumentsModal(app, documents, appId) {
    const base = '../../../../fixandgo/';
    
    const docCard = (doc) => {
      if (!doc.path) {
        return `<div style="background:#0f1117;border:1px solid #2a2d3a;border-radius:10px;padding:1rem;margin-bottom:0.75rem;">
          <div style="display:flex;align-items:center;gap:0.75rem;">
            <i class="bi bi-x-circle" style="color:#ef4444;font-size:1.3rem;"></i>
            <div style="flex:1;">
              <div style="font-weight:600;color:#94a3b8;font-size:0.85rem;">${doc.label}</div>
              <div style="font-size:0.75rem;color:#64748b;margin-top:0.2rem;">
                ${doc.required ? '<span style="color:#ef4444;">Required — Not uploaded</span>' : '<span>Not provided</span>'}
              </div>
            </div>
          </div>
        </div>`;
      }
      
      const ext = doc.path.split('.').pop().toLowerCase();
      const isImg = ['jpg','jpeg','png','webp','gif'].includes(ext);
      
      const statusBadge = {
        pending: '<span class="badge badge-pending">⏳ Pending Review</span>',
        approved: '<span class="badge badge-approved">✅ Approved</span>',
        rejected: '<span class="badge badge-rejected">❌ Rejected</span>',
      }[doc.status];
      
      const actionButtons = doc.status === 'pending' 
        ? `<div style="display:flex;gap:0.5rem;margin-top:0.75rem;">
             <button class="btn-admin btn-approve" onclick="approveDocument(${app.id}, '${doc.type}', '${esc(doc.label)}')">
               <i class="bi bi-check-lg"></i> Approve
             </button>
             <button class="btn-admin btn-reject" onclick="rejectDocument(${app.id}, '${doc.type}', '${esc(doc.label)}')">
               <i class="bi bi-x-lg"></i> Reject
             </button>
           </div>`
        : doc.status === 'rejected'
        ? `<div style="margin-top:0.75rem;padding:0.65rem;background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;border-radius:6px;">
             <div style="font-size:0.7rem;color:#f87171;font-weight:700;text-transform:uppercase;margin-bottom:0.25rem;">Rejection Reason</div>
             <div style="font-size:0.8rem;color:#cbd5e1;">${esc(doc.rejection_reason || 'No reason provided')}</div>
           </div>
           <button class="btn-admin btn-approve" onclick="approveDocument(${app.id}, '${doc.type}', '${esc(doc.label)}')" style="margin-top:0.5rem;width:100%;">
             <i class="bi bi-check-lg"></i> Approve Now
           </button>`
        : '';
      
      return `<div style="background:#0f1117;border:1px solid ${doc.status === 'approved' ? 'rgba(34,197,94,0.3)' : doc.status === 'rejected' ? 'rgba(239,68,68,0.3)' : '#2a2d3a'};border-radius:10px;padding:1rem;margin-bottom:0.75rem;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:0.75rem;">
          <div style="flex:1;">
            <div style="font-weight:700;color:#e2e8f0;font-size:0.9rem;margin-bottom:0.3rem;">${doc.label}</div>
            ${statusBadge}
          </div>
          <a href="${base}${doc.path}" target="_blank" class="btn-admin" style="background:rgba(59,130,246,0.15);color:#60a5fa;font-size:0.75rem;">
            <i class="bi bi-box-arrow-up-right"></i> Open
          </a>
        </div>
        ${isImg ? `<img src="${base}${doc.path}" style="width:100%;max-height:200px;border-radius:8px;border:1px solid #2a2d3a;object-fit:contain;margin-bottom:0.75rem;" onerror="this.style.display='none'">` : ''}
        ${actionButtons}
      </div>`;
    };
    
    // Check if all required documents are approved
    const requiredDocs = documents.filter(d => d.required && d.path);
    const approvedRequired = requiredDocs.filter(d => d.status === 'approved');
    const rejectedDocs = documents.filter(d => d.status === 'rejected');
    const allDocsApproved = requiredDocs.length > 0 && requiredDocs.length === approvedRequired.length;
    const hasRejections = rejectedDocs.length > 0;
    
    const overallStatus = {
      pending: '<span class="badge badge-pending">⏳ Under Review</span>',
      docs_approved: '<span class="badge badge-approved">✅ Documents Approved</span>',
      approved: '<span class="badge badge-approved">✅ Fully Approved</span>',
      rejected: '<span class="badge badge-rejected">❌ Rejected</span>',
    }[app.overall_status || app.status] || app.status;
    
    document.getElementById('docsModalContent').innerHTML = `
      <div style="background:#0f1117;border:1px solid #2a2d3a;border-radius:10px;padding:1rem;margin-bottom:1.25rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
          <div style="font-weight:700;color:#f1f5f9;font-size:0.95rem;">${esc(app.first_name)} ${esc(app.last_name)}</div>
          ${overallStatus}
        </div>
        <div style="font-size:0.8rem;color:#94a3b8;">${esc(app.email)} — ${esc(app.phone || 'No phone')}</div>
        <div style="font-size:0.8rem;color:#94a3b8;margin-top:0.2rem;">
          Role: <strong style="color:#e2e8f0;">${app.role === 'supplier' ? '📦 Supplier' : app.role === 'phone_technician' ? '🔧 Technician' : '🏪 Shop Owner'}</strong>
        </div>
        ${app.company_name ? `<div style="font-size:0.8rem;color:#94a3b8;margin-top:0.2rem;">Company: <strong style="color:#e2e8f0;">${esc(app.company_name)}</strong></div>` : ''}
        ${app.shop_name ? `<div style="font-size:0.8rem;color:#94a3b8;margin-top:0.2rem;">Shop: <strong style="color:#e2e8f0;">${esc(app.shop_name)}</strong></div>` : ''}
        ${app.business_name ? `<div style="font-size:0.8rem;color:#94a3b8;margin-top:0.2rem;">Business: <strong style="color:#e2e8f0;">${esc(app.business_name)}</strong></div>` : ''}
        ${app.specializations ? `<div style="font-size:0.8rem;color:#94a3b8;margin-top:0.2rem;">Specializations: <strong style="color:#e2e8f0;">${esc(app.specializations)}</strong></div>` : ''}
        ${app.shop_address ? `<div style="font-size:0.8rem;color:#94a3b8;margin-top:0.2rem;">Address: <strong style="color:#e2e8f0;">${esc(app.shop_address)}</strong></div>` : ''}
        ${app.submitted_at ? `<div style="font-size:0.75rem;color:#64748b;margin-top:0.5rem;">Submitted: ${fmtDate(app.submitted_at)}</div>` : ''}
      </div>
      <div style="margin-bottom:1rem;">
        <h6 style="font-size:0.9rem;font-weight:700;color:#e6a800;margin:0 0 0.75rem 0;">
          <i class="bi bi-file-earmark-check" style="margin-right:0.4rem;"></i>Application Documents
        </h6>
        ${documents.map(doc => docCard(doc)).join('')}
      </div>
      ${hasRejections ? `
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:10px;padding:1rem;text-align:center;margin-bottom:1rem;">
          <i class="bi bi-exclamation-triangle-fill" style="color:#f87171;font-size:1.5rem;display:block;margin-bottom:0.5rem;"></i>
          <div style="font-weight:700;color:#f87171;margin-bottom:0.25rem;">${rejectedDocs.length} Document${rejectedDocs.length > 1 ? 's' : ''} Rejected</div>
          <div style="font-size:0.8rem;color:#94a3b8;margin-bottom:0.75rem;">Notify the applicant about rejected documents so they can resubmit.</div>
          <button class="btn-admin" style="background:#ef4444;color:#fff;" onclick="notifyApplicantRejections(${app.id}, '${esc(app.first_name)} ${esc(app.last_name)}', '${esc(app.email)}', '${app.role}')">
            <i class="bi bi-send-fill"></i> Send Rejection Notification
          </button>
        </div>
      ` : ''}
      ${allDocsApproved ? `
        <div style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);border-radius:10px;padding:1rem;text-align:center;">
          <i class="bi bi-check-circle-fill" style="color:#4ade80;font-size:1.5rem;display:block;margin-bottom:0.5rem;"></i>
          <div style="font-weight:700;color:#4ade80;margin-bottom:0.25rem;">All Documents Approved!</div>
          <div style="font-size:0.8rem;color:#94a3b8;margin-bottom:0.75rem;">You can now fully approve this application.</div>
          <button class="btn-admin btn-primary-admin" onclick="finalApproveApplication(${app.id}, '${esc(app.first_name)} ${esc(app.last_name)}', '${esc(app.email)}', '${app.role}')">
            <i class="bi bi-check-circle-fill"></i> Approve Application
          </button>
        </div>
      ` : ''}
    `;
    
    document.getElementById('docsModal').classList.add('open');
    
    // Enable the main Approve button if all docs are approved
    if (allDocsApproved) {
      const approveBtn = document.getElementById('approveBtn_' + appId);
      if (approveBtn) {
        approveBtn.disabled = false;
        approveBtn.title = 'All documents approved - ready to approve';
      }
    }
  }

  // -- Approve single document -----------------------------------------------
  function approveDocument(appId, docType, docLabel) {
    if (!confirm(`Approve ${docLabel}?`)) return;
    
    fetch('../../../../fixandgo/backend/document_approvals.php', {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'approve_document', application_id: appId, document_type: docType })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          showAlert('success', `${docLabel} approved!`);
          setTimeout(() => {
            fetch('../../../../fixandgo/backend/document_approvals.php?action=get_documents&application_id=' + appId, { credentials: 'include' })
              .then(r => r.json())
              .then(docData => {
                if (docData.success) renderDocumentsModal(docData.application, docData.documents, appId);
              });
          }, 500);
        } else {
          showAlert('danger', d.message || 'Failed to approve document');
        }
      })
      .catch(() => showAlert('danger', 'Network error'));
  }

  // -- Reject single document ------------------------------------------------
  function rejectDocument(appId, docType, docLabel) {
    const reason = prompt(`Why is ${docLabel} being rejected?\n\nThis reason will be sent to the applicant:`);
    if (!reason || !reason.trim()) return;
    
    fetch('../../../../fixandgo/backend/document_approvals.php', {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
        action: 'reject_document', 
        application_id: appId, 
        document_type: docType,
        reason: reason.trim()
      })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          showAlert('success', `${docLabel} rejected.`);
          setTimeout(() => {
            fetch('../../../../fixandgo/backend/document_approvals.php?action=get_documents&application_id=' + appId, { credentials: 'include' })
              .then(r => r.json())
              .then(docData => {
                if (docData.success) renderDocumentsModal(docData.application, docData.documents, appId);
              });
          }, 500);
        } else {
          showAlert('danger', d.message || 'Failed to reject document');
        }
      })
      .catch(() => showAlert('danger', 'Network error'));
  }

  // -- Final application approval (after all docs approved) ------------------
  function finalApproveApplication(appId, name, email, role) {
    if (!confirm(`Approve the full application for ${name}?\n\nThey will be able to log in immediately.`)) return;
    
    fetch(API, {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'approve', user_id: appId, notes: 'All documents approved' })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          showAlert('success', 'Application fully approved!');
          closeModal('docsModal');
          loadApplicants();
          loadDashPending();
        } else {
          showAlert('danger', d.message || 'Failed to approve application');
        }
      })
      .catch(() => showAlert('danger', 'Network error'));
  }

  // -- Notify applicant about rejected documents -----------------------------
  function notifyApplicantRejections(appId, name, email, role) {
    if (!confirm(`Send rejection notification to ${name}?\n\nThis will notify them about which documents were rejected and why.`)) return;
    
    const btn = event.target;
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';
    
    fetch('../../../../fixandgo/backend/document_approvals.php', {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
        action: 'notify_rejections', 
        application_id: appId 
      })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          showAlert('success', `Notification sent to ${name}!`);
          btn.innerHTML = '<i class="bi bi-check-lg"></i> Notification Sent';
          setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
          }, 3000);
        } else {
          showAlert('danger', d.message || 'Failed to send notification');
          btn.innerHTML = originalHTML;
          btn.disabled = false;
        }
      })
      .catch(() => {
        showAlert('danger', 'Network error');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
      });
  }

  // -- Load stats --------------------------------------------------------
  function loadStats() {
    fetch(API + '?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => { if (d.success) renderStats(d.stats); });
  }

  // -- Helpers -----------------------------------------------------------
  function closeModal(id) {
    document.getElementById(id).classList.remove('open');
  }

  function showAlert(type, msg) {
    const el = document.getElementById('globalAlert');
    el.style.display = 'flex';
    el.className = 'admin-alert alert-' + type;
    el.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill') + '"></i>' + msg;
    setTimeout(() => { el.style.display = 'none'; }, 4000);
  }

  function setBodyError(id, cols) {
    const el = document.getElementById(id);
    if (el) el.innerHTML = `<tr><td colspan="${cols}"><div class="empty-state"><i class="bi bi-exclamation-triangle"></i>Failed to load.</div></td></tr>`;
  }

  function fmtDate(dt) {
    if (!dt) return '�';
    return new Date(dt).toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' });
  }

  function esc(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }

  function adminLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('../../../../fixandgo/backend/logout.php').finally(() => { 
        window.location.href = '../../../../fixandgo/login.php'; 
      });
    });
  }

  // ── Unlock locked account ─────────────────────────────────────────────
  function unlockAccount(userId, name) {
    if (!confirm('Unlock account for ' + name + '?\nThis will reset their failed login attempts.')) return;

    fetch(API, {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'unlock', user_id: userId }),
    })
      .then(r => r.json())
      .then(d => {
        showAlert(d.success ? 'success' : 'danger', d.message);
        if (d.success) loadUsers('usersTableWrap', '');
      })
      .catch(() => showAlert('danger', 'Request failed.'));
  }

  // ── Login Logs ────────────────────────────────────────────────────────
  let logCurrentPage = 1;
  const LOG_PAGE_SIZE = 25;

  function loadLoginLogs(page) {
    page = page || 1;
    logCurrentPage = page;

    const user   = document.getElementById('logFilterUser').value.trim();
    const action = document.getElementById('logFilterAction').value;
    const date   = document.getElementById('logFilterDate').value;

    const params = new URLSearchParams({
      action: 'login_logs',
      page,
      limit: LOG_PAGE_SIZE,
    });
    if (user)   params.set('search', user);
    if (action) params.set('filter_action', action);
    if (date)   params.set('date', date);

    const tbody = document.getElementById('loginLogsBody');
    tbody.innerHTML = '<tr><td colspan="7"><div class="spin"></div></td></tr>';

    fetch('../../../../fixandgo/backend/admin.php?' + params.toString(), { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) throw new Error(d.message || 'Failed to load logs');
        renderLoginLogs(d.logs || [], d.total || 0, page);
      })
      .catch(err => {
        tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="bi bi-exclamation-triangle"></i>${err.message}</div></td></tr>`;
      });
  }

  function renderLoginLogs(logs, total, page) {
    const tbody = document.getElementById('loginLogsBody');

    if (!logs.length) {
      tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="bi bi-clock-history"></i>No activity logs found</div></td></tr>`;
      renderLogPagination(0, 0, page);
      return;
    }

    const actionBadge = {
      login:           '<span class="badge badge-approved"><i class="bi bi-box-arrow-in-right"></i> Login</span>',
      logout:          '<span class="badge badge-customer"><i class="bi bi-box-arrow-right"></i> Logout</span>',
      session_expired: '<span class="badge badge-pending"><i class="bi bi-clock-history"></i> Timed Out</span>',
      login_failed:    '<span class="badge badge-rejected"><i class="bi bi-x-circle-fill"></i> Failed</span>',
    };

    const roleBadge = {
      customer:     '<span class="badge badge-customer">Customer</span>',
      supplier:     '<span class="badge badge-supplier">Supplier</span>',
      owner:        '<span class="badge badge-owner">Owner</span>',
      phone_technician: '<span class="badge badge-technician">Technician</span>',
      sales_person: '<span class="badge badge-sales">Sales</span>',
      supervisor:   '<span class="badge badge-supervisor">Supervisor</span>',
      admin:        '<span class="badge" style="background:rgba(239,68,68,0.15);color:#f87171;">Admin</span>',
    };

    tbody.innerHTML = logs.map(log => {
      const ua      = parseUA(log.user_agent || '');
      const dt      = fmtDateTime(log.created_at);
      const role    = log.role || '';
      const name    = esc(log.first_name + ' ' + log.last_name);
      const email   = esc(log.email || '');

      return `
        <tr>
          <td style="color:var(--admin-muted);font-size:0.75rem;">#${log.id}</td>
          <td>
            <div style="font-weight:600;color:#f1f5f9;">${name}</div>
            <div style="font-size:0.72rem;color:var(--admin-muted);">${email}</div>
          </td>
          <td>${roleBadge[role] || esc(role)}</td>
          <td>${actionBadge[log.action] || esc(log.action)}</td>
          <td style="font-size:0.78rem;color:#94a3b8;font-family:monospace;">${esc(log.ip_address || '—')}</td>
          <td style="font-size:0.75rem;color:var(--admin-muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${esc(log.user_agent || '')}">
            ${ua}
          </td>
          <td style="font-size:0.78rem;color:var(--admin-muted);white-space:nowrap;">${dt}</td>
        </tr>`;
    }).join('');

    renderLogPagination(total, LOG_PAGE_SIZE, page);

    const totalPages = Math.ceil(total / LOG_PAGE_SIZE);
    const start = (page - 1) * LOG_PAGE_SIZE + 1;
    const end   = Math.min(page * LOG_PAGE_SIZE, total);
    document.getElementById('logTableTitle').textContent =
      `Showing ${start}–${end} of ${total} records`;
  }

  function renderLogPagination(total, pageSize, currentPage) {
    const totalPages = Math.ceil(total / pageSize);
    const info = document.getElementById('logPageInfo');
    const btns = document.getElementById('logPageBtns');

    if (totalPages <= 1) {
      info.textContent = '';
      btns.innerHTML = '';
      return;
    }

    info.textContent = `Page ${currentPage} of ${totalPages}`;

    let html = '';
    // Prev
    html += `<button class="btn-admin" style="background:rgba(255,255,255,0.05);color:#94a3b8;${currentPage === 1 ? 'opacity:0.4;cursor:not-allowed;' : ''}"
      onclick="${currentPage > 1 ? 'loadLoginLogs(' + (currentPage - 1) + ')' : ''}">
      <i class="bi bi-chevron-left"></i>
    </button>`;

    // Page numbers (show up to 5 around current)
    const start = Math.max(1, currentPage - 2);
    const end   = Math.min(totalPages, currentPage + 2);
    for (let p = start; p <= end; p++) {
      const active = p === currentPage;
      html += `<button class="btn-admin ${active ? 'btn-primary-admin' : ''}"
        style="${active ? '' : 'background:rgba(255,255,255,0.05);color:#94a3b8;'}"
        onclick="loadLoginLogs(${p})">${p}</button>`;
    }

    // Next
    html += `<button class="btn-admin" style="background:rgba(255,255,255,0.05);color:#94a3b8;${currentPage === totalPages ? 'opacity:0.4;cursor:not-allowed;' : ''}"
      onclick="${currentPage < totalPages ? 'loadLoginLogs(' + (currentPage + 1) + ')' : ''}">
      <i class="bi bi-chevron-right"></i>
    </button>`;

    btns.innerHTML = html;
  }

  function clearLogFilters() {
    document.getElementById('logFilterUser').value   = '';
    document.getElementById('logFilterAction').value = '';
    document.getElementById('logFilterDate').value   = '';
    loadLoginLogs(1);
  }

  // Parse user-agent into a short readable string
  function parseUA(ua) {
    if (!ua) return '—';
    let browser = 'Unknown';
    let os      = '';

    if (/Edg\//.test(ua))         browser = '<i class="bi bi-browser-edge"></i> Edge';
    else if (/Chrome\//.test(ua)) browser = '<i class="bi bi-browser-chrome"></i> Chrome';
    else if (/Firefox\//.test(ua))browser = '<i class="bi bi-browser-firefox"></i> Firefox';
    else if (/Safari\//.test(ua)) browser = '<i class="bi bi-browser-safari"></i> Safari';

    if (/Windows/.test(ua))      os = 'Windows';
    else if (/Android/.test(ua)) os = 'Android';
    else if (/iPhone|iPad/.test(ua)) os = 'iOS';
    else if (/Mac OS/.test(ua))  os = 'macOS';
    else if (/Linux/.test(ua))   os = 'Linux';

    return browser + (os ? ' · ' + os : '');
  }

  function fmtDateTime(str) {
    if (!str) return '—';
    const d = new Date(str);
    return d.toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' })
      + ' ' + d.toLocaleTimeString('en-PH', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
  }

  // Close modals when clicking outside
  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', function(e) {
      if (e.target === this) this.classList.remove('open');
    });
  });
  </script>
</body>
</html>
