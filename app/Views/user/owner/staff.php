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
  <title>Fix&Go — Manage Staff</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=5" />
  <link rel="stylesheet" href="/assets/css/supplier.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); margin: 0; }
    .supplier-layout { display: flex; min-height: calc(100vh - 68px); }
    .supplier-sidebar {
      width: 240px; flex-shrink: 0; background: var(--fg-card-bg);
      border-right: 1px solid var(--fg-border); padding: 1.5rem 0;
      position: sticky; top: 68px; height: calc(100vh - 68px); overflow-y: auto;
    }
    .sidebar-label {
      font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: 1px; color: var(--fg-muted); padding: 0 1.25rem; margin-bottom: 0.5rem;
    }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a {
      display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.25rem;
      color: var(--fg-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;
      border-left: 3px solid transparent; transition: all 0.2s;
    }
    .sidebar-nav a:hover { color: var(--fg-primary); background: rgba(230,168,0,0.07); border-left-color: var(--fg-primary); }
    .sidebar-nav a.active { color: var(--fg-primary); background: rgba(230,168,0,0.1); border-left-color: var(--fg-primary); font-weight: 700; }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }
    .supplier-main { flex: 1; padding: 2rem; min-width: 0; }
    .page-header {
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;
    }
    .page-header h2 { font-size: 1.6rem; font-weight: 800; color: var(--fg-text); margin: 0; }
    .page-header p  { color: var(--fg-muted); margin: 0; font-size: 0.88rem; }
    .stats-grid {
      display: grid; grid-template-columns: repeat(4, 1fr);
      gap: 1rem; margin-bottom: 1.5rem;
    }
    .stat-card {
      background: var(--fg-card-bg); border: 1px solid var(--fg-border);
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
    .tabs {
      display: flex; gap: 0.5rem; margin-bottom: 1.5rem;
      border-bottom: 2px solid var(--fg-border);
    }
    .tab {
      padding: 0.75rem 1.5rem; border: none; background: none;
      color: var(--fg-muted); font-weight: 600; font-size: 0.9rem;
      cursor: pointer; border-bottom: 3px solid transparent;
      transition: all 0.2s; position: relative; bottom: -2px;
    }
    .tab:hover { color: var(--fg-primary); }
    .tab.active {
      color: var(--fg-primary); border-bottom-color: var(--fg-primary);
    }
    .tab-badge {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 20px; height: 20px; border-radius: 10px;
      background: var(--fg-primary); color: #fff;
      font-size: 0.7rem; font-weight: 700;
      padding: 0 0.4rem; margin-left: 0.5rem;
    }
    .table-card {
      background: var(--fg-card-bg); border: 1px solid var(--fg-border);
      border-radius: 14px; overflow: hidden;
    }
    .staff-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .staff-table thead th {
      background: var(--fg-primary); color: #fff;
      padding: 0.75rem 1rem; text-align: left;
      font-weight: 700; font-size: 0.72rem;
      text-transform: uppercase; letter-spacing: 0.6px;
    }
    .staff-table tbody td {
      padding: 0.75rem 1rem; border-bottom: 1px solid var(--fg-border);
      color: var(--fg-text); vertical-align: middle;
    }
    .staff-table tbody tr:last-child td { border-bottom: none; }
    .staff-table tbody tr:hover { background: rgba(230,168,0,0.03); }
    .role-badge {
      display: inline-flex; align-items: center; gap: 0.3rem;
      padding: 0.25rem 0.75rem; border-radius: 20px;
      font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
    }
    .role-sales { background: rgba(59,130,246,0.12); color: #3b82f6; }
    .role-supervisor { background: rgba(230,168,0,0.12); color: #c98f00; }
    .role-technician { background: rgba(16,185,129,0.12); color: #10b981; }
    .btn-action {
      padding: 0.4rem 0.9rem; border-radius: 8px;
      border: 1.5px solid var(--fg-border);
      background: transparent; cursor: pointer;
      font-size: 0.8rem; font-weight: 600;
      transition: all 0.2s; margin: 0 0.25rem;
    }
    .btn-action.approve { border-color: #28A745; color: #28A745; }
    .btn-action.approve:hover { background: rgba(40,167,69,0.1); }
    .btn-action.reject { border-color: #dc3545; color: #dc3545; }
    .btn-action.reject:hover { background: rgba(220,53,69,0.1); }
    .btn-action.deactivate { border-color: #6C757D; color: #6C757D; }
    .btn-action.deactivate:hover { background: rgba(108,117,125,0.1); }
    .empty-state {
      text-align: center; padding: 4rem 2rem; color: var(--fg-muted);
    }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.4; }
    .alert-bar {
      padding: 0.75rem 1.25rem; border-radius: 10px;
      font-size: 0.85rem; font-weight: 600;
      display: flex; align-items: center; gap: 0.6rem;
      margin-bottom: 1rem; animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
    .alert-success { background: rgba(40,167,69,0.12); color: #28A745; border: 1px solid rgba(40,167,69,0.25); }
    .alert-danger  { background: rgba(220,53,69,0.12);  color: #dc3545; border: 1px solid rgba(220,53,69,0.25); }
    .btn-primary-custom {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.65rem 1.5rem; border-radius: 10px;
      background: var(--fg-primary); color: #fff;
      border: none; font-weight: 700; font-size: 0.9rem;
      cursor: pointer; transition: all 0.2s;
    }
    .btn-primary-custom:hover {
      background: var(--fg-primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(230,168,0,0.35);
    }
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
      width: 100%; max-width: 500px;
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
    .form-group { margin-bottom: 1.1rem; }
    .form-group label {
      display: block; font-size: 0.82rem; font-weight: 700;
      color: var(--fg-text); margin-bottom: 0.4rem;
    }
    .form-group label span { color: #dc3545; margin-left: 2px; }
    .form-input, .form-select {
      width: 100%; padding: 0.65rem 0.9rem;
      border: 1.5px solid var(--fg-border);
      border-radius: 10px; background: var(--fg-bg);
      color: var(--fg-text); font-size: 0.88rem;
      outline: none; transition: border-color 0.2s, box-shadow 0.2s;
      font-family: inherit;
    }
    .form-input:focus, .form-select:focus {
      border-color: var(--fg-primary);
      box-shadow: 0 0 0 3px rgba(230,168,0,0.15);
    }
    .form-select { cursor: pointer; }
    .btn-cancel {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.65rem 1.25rem; border-radius: 10px;
      background: transparent; color: var(--fg-muted);
      border: 1.5px solid var(--fg-border); font-weight: 600;
      font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
    }
    .btn-cancel:hover { border-color: var(--fg-text); color: var(--fg-text); }
    @media (max-width: 992px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
  </style>
</head>
<body>
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&Go"
             style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge owner">🏪 Owner</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="/dashboard.php" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
  </nav>

  <div class="supplier-layout">
    <aside class="supplier-sidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> Manage Products</a></li>
        <li><a href="inventory.php"><i class="bi bi-receipt"></i> Purchase History</a></li>
        <li><a href="staff.php" class="active"><i class="bi bi-people"></i> Manage Staff</a></li>
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Bookings</a></li>
        <li><a href="deliveries.php"><i class="bi bi-truck"></i> Deliveries</a></li>
        <li><a href="tech-orders.php"><i class="bi bi-bag-check"></i> Tech Orders</a></li>
        <li><a href="sales-report.php"><i class="bi bi-bar-chart-line"></i> Revenue Report</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li><a href="profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>

    <main class="supplier-main">
      <div class="page-header">
        <div>
          <h2>Manage Staff</h2>
          <p>Review applications and manage your team</p>
        </div>
        <button class="btn-primary-custom" id="btnRegisterStaff">
          <i class="bi bi-person-plus"></i>
          Register Staff
        </button>
      </div>

      <div id="alertBox" style="display:none;"></div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981;"><i class="bi bi-people"></i></div>
          <div>
            <div class="stat-value" style="color:#10b981;" id="statTotal">0</div>
            <div class="stat-label">Total Staff</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(40,167,69,0.12);color:#28A745;"><i class="bi bi-check-circle"></i></div>
          <div>
            <div class="stat-value" style="color:#28A745;" id="statActive">0</div>
            <div class="stat-label">Active</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(230,168,0,0.12);color:#e6a800;"><i class="bi bi-hourglass-split"></i></div>
          <div>
            <div class="stat-value" style="color:#e6a800;" id="statPending">0</div>
            <div class="stat-label">Pending</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="bi bi-tools"></i></div>
          <div>
            <div class="stat-value" style="color:#3b82f6;" id="statTechnicians">0</div>
            <div class="stat-label">Technicians</div>
          </div>
        </div>
      </div>

      <div class="tabs">
        <button class="tab active" data-tab="pending">
          Pending Applications
          <span class="tab-badge" id="pendingBadge">0</span>
        </button>
        <button class="tab" data-tab="active">
          Active Staff
          <span class="tab-badge" id="activeBadge" style="background:#28A745;">0</span>
        </button>
      </div>

      <div class="tab-content" id="pendingTab">
        <div class="table-card">
          <table class="staff-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Position</th>
                <th>Applied</th>
                <th style="text-align:center;">Actions</th>
              </tr>
            </thead>
            <tbody id="pendingTableBody">
              <tr>
                <td colspan="6">
                  <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No pending applications</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-content" id="activeTab" style="display:none;">
        <div class="table-card">
          <table class="staff-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Position</th>
                <th>Status</th>
                <th>Joined</th>
                <th style="text-align:center;">Actions</th>
              </tr>
            </thead>
            <tbody id="activeTableBody">
              <tr>
                <td colspan="7">
                  <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No staff members yet</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Register Staff Modal -->
  <div class="modal-overlay" id="registerModal">
    <div class="modal-box">
      <div class="modal-head">
        <h5><i class="bi bi-person-plus"></i> Register New Staff</h5>
        <button class="btn-close-modal" id="btnCloseModal">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <form id="registerStaffForm">
        <div class="modal-body">
          <div class="form-group">
            <label>Position <span>*</span></label>
            <select class="form-select" name="role" required>
              <option value="">Select Position</option>
              <option value="sales_person">Sales Person</option>
              <option value="supervisor">Supervisor</option>
            </select>
          </div>
          <div class="form-group">
            <label>First Name <span>*</span></label>
            <input type="text" class="form-input" name="first_name" required placeholder="Enter first name" minlength="2" maxlength="50">
          </div>
          <div class="form-group">
            <label>Last Name <span>*</span></label>
            <input type="text" class="form-input" name="last_name" required placeholder="Enter last name" minlength="2" maxlength="50">
          </div>
          <div class="form-group">
            <label>Email <span>*</span></label>
            <input type="email" class="form-input" name="email" required placeholder="Enter email address">
          </div>
          <div class="form-group">
            <label>Phone Number <span>*</span></label>
            <input type="tel" class="form-input" name="phone" required placeholder="e.g. +63 912 345 6789" pattern="[\+]?[0-9\s\-\(\)]+">
          </div>
          <div class="form-group">
            <label>Password <span>*</span></label>
            <input type="password" class="form-input" name="password" id="staffPassword" required placeholder="Enter password" minlength="6">
            <small style="color:var(--fg-muted);font-size:0.75rem;display:block;margin-top:0.3rem;">
              Minimum 6 characters
            </small>
          </div>
          <div class="form-group">
            <label>Confirm Password <span>*</span></label>
            <input type="password" class="form-input" name="confirm_password" id="staffConfirmPassword" required placeholder="Re-enter password" minlength="6">
            <small id="passwordMatchError" style="color:#dc3545;font-size:0.75rem;display:none;margin-top:0.3rem;">
              <i class="bi bi-exclamation-circle"></i> Passwords do not match
            </small>
          </div>
        </div>
        <div class="modal-foot">
          <button type="button" class="btn-cancel" id="btnCancelModal">
            <i class="bi bi-x-circle"></i> Cancel
          </button>
          <button type="submit" class="btn-primary-custom">
            <i class="bi bi-check-circle"></i> Register Staff
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="/assets/js/session-timeout.js"></script>
  <script src="staff.js"></script>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>


