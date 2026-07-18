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
  <title>Fix&amp;Go — Deliveries</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=4" />
  <link rel="stylesheet" href="/assets/css/supplier.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .supplier-layout { display: flex; min-height: calc(100vh - 65px); }
    .supplier-sidebar { width: 240px; background: var(--fg-card-bg); border-right: 1px solid var(--fg-border); padding: 1.5rem 0; flex-shrink: 0; position: sticky; top: 65px; height: calc(100vh - 65px); overflow-y: auto; }
    .sidebar-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--fg-muted); padding: 0 1.25rem; margin-bottom: 0.5rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.25rem; color: var(--fg-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.2s ease; }
    .sidebar-nav a:hover { color: var(--fg-primary); background: rgba(230,168,0,0.07); border-left-color: var(--fg-primary); }
    .sidebar-nav a.active { color: var(--fg-primary); background: rgba(230,168,0,0.1); border-left-color: var(--fg-primary); font-weight: 700; }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }
    .supplier-main { flex: 1; padding: 2rem; min-width: 0; }
    .stat-card { background: var(--fg-card-bg); border-radius: var(--fg-radius); border: 1px solid var(--fg-border); padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(26,26,46,0.14); }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
    .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1; margin-bottom: 0.15rem; }
    .stat-label { font-size: 0.75rem; color: var(--fg-muted); font-weight: 600; }
    .deliveries-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .deliveries-table th { background: var(--fg-primary); color: #fff; padding: 0.65rem 0.9rem; text-align: left; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .deliveries-table td { padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--fg-border); color: var(--fg-text); vertical-align: middle; }
    .deliveries-table tbody tr:hover { background: rgba(230,168,0,0.04); }
    .status-badge { display: inline-block; padding: 0.25rem 0.7rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; white-space: nowrap; }
    .status-pending-pickup { background: rgba(230,168,0,0.12); color: #c98f00; }
    .status-in-transit     { background: rgba(59,130,246,0.12); color: #2563eb; }
    .status-out-delivery   { background: rgba(249,115,22,0.12); color: #ea580c; }
    .status-delivered      { background: rgba(16,185,129,0.12); color: #059669; }
    .btn-icon { background: none; border: 1.5px solid var(--fg-border); border-radius: 8px; cursor: pointer; padding: 0.3rem 0.55rem; font-size: 0.9rem; color: var(--fg-muted); transition: all 0.2s; }
    .btn-icon:hover { border-color: var(--fg-primary); color: var(--fg-primary); transform: scale(1.08); }
    .modal-backdrop-custom { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: none; }
    .modal-custom { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--fg-card-bg); border-radius: var(--fg-radius); box-shadow: 0 20px 60px rgba(0,0,0,0.35); z-index: 1001; max-width: 480px; width: 92%; max-height: 90vh; overflow-y: auto; display: none; }
    .modal-header-custom { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--fg-border); display: flex; align-items: center; justify-content: space-between; }
    .modal-body-custom { padding: 1.5rem; }
    .modal-footer-custom { padding: 1rem 1.5rem; border-top: 1px solid var(--fg-border); display: flex; gap: 0.75rem; justify-content: flex-end; }
    .sidebar-toggle { display: none; background: none; border: 1.5px solid var(--fg-border); border-radius: 8px; padding: 0.3rem 0.6rem; color: var(--fg-text); cursor: pointer; font-size: 1.1rem; }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-overlay.open { display: block; }
    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }
      .supplier-sidebar { position: fixed; top: 65px; left: 0; z-index: 200; transform: translateX(-100%); height: calc(100vh - 65px); box-shadow: 4px 0 20px rgba(0,0,0,0.15); transition: transform 0.3s ease; }
      .supplier-sidebar.open { transform: translateX(0); }
      .supplier-main { padding: 1.25rem; }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation" aria-label="Main navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge owner">🏪 Owner</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark/light mode"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="/dashboard.php" class="btn btn-sm"
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
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Bookings</a></li>
        <li><a href="deliveries.php" class="active"><i class="bi bi-truck"></i> Deliveries</a></li>
        <li><a href="tech-orders.php"><i class="bi bi-bag-check"></i> Tech Orders</a></li>
        <li><a href="sales-report.php"><i class="bi bi-bar-chart-line"></i> Revenue Report</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li><a href="profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>

    <!-- Main -->
    <main class="supplier-main">

      <!-- Page header -->
      <div class="mb-4">
        <h2 style="font-weight:800;color:var(--fg-text);margin:0;">Deliveries</h2>
        <p style="color:var(--fg-muted);margin:0;font-size:0.9rem;">Track incoming parts and accessories</p>
      </div>

      <!-- Stats -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#2563eb;"><i class="bi bi-truck"></i></div>
            <div><div class="stat-value" style="color:#2563eb;">4</div><div class="stat-label">In Transit</div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#059669;"><i class="bi bi-check-circle"></i></div>
            <div><div class="stat-value" style="color:#059669;">3</div><div class="stat-label">Delivered Today</div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:rgba(230,168,0,0.12);color:#c98f00;"><i class="bi bi-box-seam"></i></div>
            <div><div class="stat-value" style="color:#c98f00;">2</div><div class="stat-label">Pending Pickup</div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:rgba(139,92,246,0.12);color:#7c3aed;"><i class="bi bi-calendar-check"></i></div>
            <div><div class="stat-value" style="color:#7c3aed;">18</div><div class="stat-label">Total This Month</div></div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="dashboard-card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="deliveries-table">
            <thead>
              <tr>
                <th>Delivery ID</th>
                <th>Order ID</th>
                <th>Shop</th>
                <th>Address</th>
                <th>Courier</th>
                <th>Status</th>
                <th>Expected Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="deliveriesTableBody"></tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <!-- Update Status Modal -->
  <div class="modal-backdrop-custom" id="modalBackdrop"></div>
  <div class="modal-custom" id="deliveryModal">
    <div class="modal-header-custom">
      <h5 style="margin:0;font-weight:700;color:var(--fg-text);">Update Delivery Status</h5>
      <button class="btn-icon" id="btnCloseModal"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body-custom" id="deliveryModalBody"></div>
    <div class="modal-footer-custom">
      <button class="btn btn-sm" style="border:1.5px solid var(--fg-border);border-radius:8px;background:transparent;" id="btnCloseModalFooter">Cancel</button>
      <button class="btn-primary-fg" style="width:auto;padding:0.45rem 1.25rem;font-size:0.88rem;" id="btnSaveStatus">Save</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script>
    const DELIVERIES = [
      { id: 'DEL-101', orderId: 'ORD-501', shop: 'QuickFix Manila',  address: '123 Taft Ave, Manila',       courier: 'J&T Express',   status: 'In Transit',       expected: 'Jun 13, 2025' },
      { id: 'DEL-102', orderId: 'ORD-502', shop: 'TechRepair Cebu',  address: '45 Colon St, Cebu City',     courier: 'LBC',           status: 'Pending Pickup',   expected: 'Jun 14, 2025' },
      { id: 'DEL-103', orderId: 'ORD-503', shop: 'GadgetFix BGC',    address: '7th Ave, BGC, Taguig',       courier: 'Ninja Van',     status: 'Delivered',        expected: 'Jun 9, 2025'  },
      { id: 'DEL-104', orderId: 'ORD-504', shop: 'iRepair Davao',    address: '88 Quirino Ave, Davao City', courier: 'Flash Express', status: 'Out for Delivery', expected: 'Jun 12, 2025' },
      { id: 'DEL-105', orderId: 'ORD-505', shop: 'PhoneFix QC',      address: '22 Timog Ave, Quezon City',  courier: 'J&T Express',   status: 'Pending Pickup',   expected: 'Jun 15, 2025' },
    ];

    let selectedDeliveryId = null;

    function statusClass(s) {
      const map = {
        'Pending Pickup':   'status-pending-pickup',
        'In Transit':       'status-in-transit',
        'Out for Delivery': 'status-out-delivery',
        'Delivered':        'status-delivered',
      };
      return map[s] || '';
    }

    function renderTable() {
      const tbody = document.getElementById('deliveriesTableBody');
      tbody.innerHTML = DELIVERIES.map(d => `
        <tr>
          <td><strong style="color:var(--fg-primary);">${d.id}</strong></td>
          <td>${d.orderId}</td>
          <td>${d.shop}</td>
          <td style="font-size:0.82rem;color:var(--fg-muted);max-width:160px;">${d.address}</td>
          <td>${d.courier}</td>
          <td><span class="status-badge ${statusClass(d.status)}">${d.status}</span></td>
          <td style="color:var(--fg-muted);font-size:0.82rem;">${d.expected}</td>
          <td>
            <button class="btn-icon" title="Update Status" onclick="openUpdate('${d.id}')">
              <i class="bi bi-arrow-repeat"></i>
            </button>
          </td>
        </tr>
      `).join('');
    }

    function openUpdate(id) {
      const d = DELIVERIES.find(x => x.id === id);
      if (!d) return;
      selectedDeliveryId = id;

      const statuses = ['Pending Pickup', 'In Transit', 'Out for Delivery', 'Delivered'];
      const options = statuses.map(s =>
        `<option value="${s}" ${s === d.status ? 'selected' : ''}>${s}</option>`
      ).join('');

      document.getElementById('deliveryModalBody').innerHTML = `
        <p style="font-size:0.88rem;color:var(--fg-muted);margin-bottom:1rem;">
          Delivery <strong style="color:var(--fg-primary);">${d.id}</strong> — Order ${d.orderId}
        </p>
        <div class="mb-3">
          <label class="form-label">Shop</label>
          <input class="form-control" value="${d.shop}" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label">Courier</label>
          <input class="form-control" value="${d.courier}" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label">Update Status</label>
          <select class="form-select" id="newDeliveryStatus">${options}</select>
        </div>
      `;

      document.getElementById('modalBackdrop').style.display = 'block';
      document.getElementById('deliveryModal').style.display = 'block';
    }

    function closeModal() {
      document.getElementById('modalBackdrop').style.display = 'none';
      document.getElementById('deliveryModal').style.display = 'none';
      selectedDeliveryId = null;
    }

    document.addEventListener('DOMContentLoaded', function () {
      const user = FGAuth.UserStore.get();
      if (!user || user.role !== 'owner') {
        window.location.href = '/login.html';
        return;
      }

      const fullName = (user.firstName || '') + ' ' + (user.lastName || '');
      document.getElementById('navUserName').textContent = fullName.trim() || user.email || 'Owner';

      renderTable();

      document.getElementById('btnCloseModal').addEventListener('click', closeModal);
      document.getElementById('btnCloseModalFooter').addEventListener('click', closeModal);
      document.getElementById('modalBackdrop').addEventListener('click', closeModal);

      document.getElementById('btnSaveStatus').addEventListener('click', function () {
        const sel = document.getElementById('newDeliveryStatus');
        if (!sel || !selectedDeliveryId) return;
        const d = DELIVERIES.find(x => x.id === selectedDeliveryId);
        if (d) { d.status = sel.value; renderTable(); }
        closeModal();
      });

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
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>


