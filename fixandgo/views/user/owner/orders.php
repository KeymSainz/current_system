<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- PWA -->
  <meta name="theme-color" content="#e6a800">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Fix&amp;Go">
  <link rel="manifest" href="../../../manifest.json">
  <link rel="apple-touch-icon" href="../../../assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="../../../assets/css/mobile.css">
  <title>Fix&amp;Go — Bookings</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=4" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body{background:var(--fg-bg)}.supplier-layout{display:flex;min-height:calc(100vh - 65px)}.supplier-sidebar{width:240px;background:var(--fg-card-bg);border-right:1px solid var(--fg-border);padding:1.5rem 0;flex-shrink:0;position:sticky;top:65px;height:calc(100vh - 65px);overflow-y:auto}.sidebar-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);padding:0 1.25rem;margin-bottom:.5rem}.sidebar-nav{list-style:none;padding:0;margin:0}.sidebar-nav a{display:flex;align-items:center;gap:.75rem;padding:.65rem 1.25rem;color:var(--fg-muted);text-decoration:none;font-size:.88rem;font-weight:500;border-left:3px solid transparent;transition:all .2s ease}.sidebar-nav a:hover{color:var(--fg-primary);background:rgba(230,168,0,.07);border-left-color:var(--fg-primary)}.sidebar-nav a.active{color:var(--fg-primary);background:rgba(230,168,0,.1);border-left-color:var(--fg-primary);font-weight:700}.sidebar-nav a i{font-size:1rem;width:20px;text-align:center}.supplier-main{flex:1;padding:2rem;min-width:0}
    .filter-tabs{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.25rem}.filter-tab{padding:.4rem 1rem;border-radius:20px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s ease}.filter-tab:hover{border-color:var(--fg-primary);color:var(--fg-primary)}.filter-tab.active{background:var(--fg-primary);border-color:var(--fg-primary);color:#fff}
    .orders-table{width:100%;border-collapse:collapse;font-size:.85rem}.orders-table th{background:var(--fg-primary);color:#fff;padding:.65rem .9rem;text-align:left;font-weight:700;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}.orders-table td{padding:.7rem .9rem;border-bottom:1px solid var(--fg-border);color:var(--fg-text);vertical-align:middle}.orders-table tbody tr:hover{background:rgba(230,168,0,.04)}
    .status-badge{display:inline-block;padding:.25rem .7rem;border-radius:20px;font-size:.7rem;font-weight:700;text-transform:uppercase;white-space:nowrap}
    .status-pending{background:rgba(230,168,0,.12);color:#c98f00}
    .status-confirmed{background:rgba(59,130,246,.12);color:#2563eb}
    .status-inprogress{background:rgba(249,115,22,.12);color:#ea580c}
    .status-completed{background:rgba(16,185,129,.12);color:#059669}
    .status-cancelled{background:rgba(220,53,69,.12);color:#dc3545}
    .btn-icon{background:none;border:1.5px solid var(--fg-border);border-radius:8px;cursor:pointer;padding:.3rem .55rem;font-size:.9rem;color:var(--fg-muted);transition:all .2s}.btn-icon:hover{border-color:var(--fg-primary);color:var(--fg-primary);transform:scale(1.08)}
    .modal-backdrop-custom{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:none}.modal-custom{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:var(--fg-card-bg);border-radius:var(--fg-radius);box-shadow:0 20px 60px rgba(0,0,0,.35);z-index:1001;max-width:560px;width:92%;max-height:90vh;overflow-y:auto;display:none}.modal-header-custom{padding:1.25rem 1.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between}.modal-body-custom{padding:1.5rem}.modal-footer-custom{padding:1rem 1.5rem;border-top:1px solid var(--fg-border);display:flex;gap:.75rem;justify-content:flex-end}
    .detail-row{display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--fg-border);font-size:.88rem}.detail-row:last-child{border-bottom:none}.detail-label{color:var(--fg-muted);font-weight:600}.detail-value{color:var(--fg-text);font-weight:500;text-align:right}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:.3rem .6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem}.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:199}.sidebar-overlay.open{display:block}
    @media(max-width:768px){.sidebar-toggle{display:flex;align-items:center}.supplier-sidebar{position:fixed;top:65px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 65px);box-shadow:4px 0 20px rgba(0,0,0,.15);transition:transform .3s ease}.supplier-sidebar.open{transform:translateX(0)}.supplier-main{padding:1.25rem}}
  </style>
</head>
<body>
  <nav class="fg-navbar">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;"><img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;" onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'"></a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge owner">🏪 Owner</span>
      <span id="navUserName" style="font-size:.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="../../../dashboard.php" class="btn btn-sm" style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:.85rem;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
  </nav>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="supplier-layout">
    <aside class="supplier-sidebar" id="supplierSidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
        <li><a href="orders.php" class="active"><i class="bi bi-cart3"></i> Bookings</a></li>
        <li><a href="deliveries.php"><i class="bi bi-truck"></i> Deliveries</a></li>
        <li><a href="tech-orders.php"><i class="bi bi-bag-check"></i> Tech Orders</a></li>
        <li><a href="sales-report.php"><i class="bi bi-bar-chart-line"></i> Revenue Report</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li><a href="profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>
    <main class="supplier-main">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div><h2 style="font-weight:800;color:var(--fg-text);margin:0;">Bookings</h2><p style="color:var(--fg-muted);margin:0;font-size:.9rem;">Manage customer repair bookings</p></div>
      </div>
      <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">All</button>
        <button class="filter-tab" data-filter="Pending">Pending</button>
        <button class="filter-tab" data-filter="Confirmed">Confirmed</button>
        <button class="filter-tab" data-filter="In Progress">In Progress</button>
        <button class="filter-tab" data-filter="Completed">Completed</button>
        <button class="filter-tab" data-filter="Cancelled">Cancelled</button>
      </div>
      <div class="dashboard-card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="orders-table">
            <thead><tr><th>Booking ID</th><th>Customer</th><th>Device</th><th>Service</th><th>Technician</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody id="ordersTableBody"></tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
  <div class="modal-backdrop-custom" id="modalBackdrop"></div>
  <div class="modal-custom" id="orderModal">
    <div class="modal-header-custom"><h5 style="margin:0;font-weight:700;color:var(--fg-text);">Booking Details</h5><button class="btn-icon" id="btnCloseModal"><i class="bi bi-x-lg"></i></button></div>
    <div class="modal-body-custom" id="orderModalBody"></div>
    <div class="modal-footer-custom">
      <button class="btn btn-sm" style="border:1.5px solid var(--fg-border);border-radius:8px;background:transparent;" id="btnCloseModalFooter">Close</button>
      <button class="btn-primary-fg" style="width:auto;padding:.45rem 1.25rem;font-size:.88rem;" id="btnUpdateStatus">Update Status</button>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script>
    const ORDERS=[
      {id:'BK-1042', customer:'Maria Santos',   device:'iPhone 14',      service:'Screen Repair',    tech:'Carlos Reyes',   status:'In Progress', date:'Jun 10, 2025'},
      {id:'BK-1041', customer:'Juan Dela Cruz', device:'Samsung S23',    service:'Battery Replace',  tech:'Ana Dela Cruz',  status:'Completed',   date:'Jun 9, 2025'},
      {id:'BK-1040', customer:'Ana Reyes',      device:'Xiaomi 12',      service:'Water Damage',     tech:'Marco Santos',   status:'Pending',     date:'Jun 12, 2025'},
      {id:'BK-1039', customer:'Pedro Lim',      device:'iPhone 13 Pro',  service:'Charging Port',    tech:'Carlos Reyes',   status:'Confirmed',   date:'Jun 11, 2025'},
      {id:'BK-1038', customer:'Carlo Mendoza',  device:'Oppo Reno 8',    service:'Speaker Fix',      tech:'Ana Dela Cruz',  status:'In Progress', date:'Jun 10, 2025'},
      {id:'BK-1037', customer:'Liza Tan',       device:'Samsung A54',    service:'Back Glass',       tech:'Marco Santos',   status:'Cancelled',   date:'Jun 8, 2025'},
    ];
    let currentFilter='all', selectedOrderId=null;
    function sc(s){
      return {
        'Pending':     'status-pending',
        'Confirmed':   'status-confirmed',
        'In Progress': 'status-inprogress',
        'Completed':   'status-completed',
        'Cancelled':   'status-cancelled',
      }[s] || '';
    }
    function renderTable(f){
      const tbody=document.getElementById('ordersTableBody');
      const rows=f==='all'?ORDERS:ORDERS.filter(o=>o.status===f);
      if(!rows.length){tbody.innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);"><i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>No bookings found.</td></tr>';return;}
      tbody.innerHTML=rows.map(o=>`<tr>
        <td><strong style="color:var(--fg-primary);">${o.id}</strong></td>
        <td>${o.customer}</td>
        <td>${o.device}</td>
        <td>${o.service}</td>
        <td>${o.tech}</td>
        <td><span class="status-badge ${sc(o.status)}">${o.status}</span></td>
        <td style="color:var(--fg-muted);font-size:.82rem;">${o.date}</td>
        <td><button class="btn-icon me-1" title="View" onclick="viewOrder('${o.id}')"><i class="bi bi-eye"></i></button><button class="btn-icon" title="Update" onclick="openUpdate('${o.id}')"><i class="bi bi-arrow-repeat"></i></button></td>
      </tr>`).join('');
    }
    function viewOrder(id){
      const o=ORDERS.find(x=>x.id===id);if(!o)return;selectedOrderId=id;
      document.getElementById('orderModalBody').innerHTML=`
        <div class="detail-row"><span class="detail-label">Booking ID</span><span class="detail-value"><strong style="color:var(--fg-primary);">${o.id}</strong></span></div>
        <div class="detail-row"><span class="detail-label">Customer</span><span class="detail-value">${o.customer}</span></div>
        <div class="detail-row"><span class="detail-label">Device</span><span class="detail-value">${o.device}</span></div>
        <div class="detail-row"><span class="detail-label">Service</span><span class="detail-value">${o.service}</span></div>
        <div class="detail-row"><span class="detail-label">Technician</span><span class="detail-value">${o.tech}</span></div>
        <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="status-badge ${sc(o.status)}">${o.status}</span></span></div>
        <div class="detail-row"><span class="detail-label">Date</span><span class="detail-value">${o.date}</span></div>`;
      openModal();
    }
    function openUpdate(id){
      const o=ORDERS.find(x=>x.id===id);if(!o)return;selectedOrderId=id;
      const opts=['Pending','Confirmed','In Progress','Completed','Cancelled'].map(s=>`<option value="${s}"${s===o.status?' selected':''}>${s}</option>`).join('');
      document.getElementById('orderModalBody').innerHTML=`
        <div class="detail-row"><span class="detail-label">Booking ID</span><span class="detail-value"><strong style="color:var(--fg-primary);">${o.id}</strong></span></div>
        <div class="detail-row"><span class="detail-label">Customer</span><span class="detail-value">${o.customer}</span></div>
        <div class="mb-3 mt-3"><label class="form-label">Update Status</label><select class="form-select" id="newStatusSelect">${opts}</select></div>`;
      openModal();
    }
    function openModal(){document.getElementById('modalBackdrop').style.display='block';document.getElementById('orderModal').style.display='block';}
    function closeModal(){document.getElementById('modalBackdrop').style.display='none';document.getElementById('orderModal').style.display='none';selectedOrderId=null;}
    document.addEventListener('DOMContentLoaded',function(){
      const user=FGAuth.UserStore.get();if(!user||user.role!=='owner'){window.location.href='../../../login.html';return;}
      document.getElementById('navUserName').textContent=((user.firstName||'')+' '+(user.lastName||'')).trim()||user.email||'Owner';
      renderTable('all');
      document.querySelectorAll('.filter-tab').forEach(btn=>btn.addEventListener('click',function(){document.querySelectorAll('.filter-tab').forEach(b=>b.classList.remove('active'));btn.classList.add('active');currentFilter=btn.dataset.filter;renderTable(currentFilter);}));
      document.getElementById('btnCloseModal').addEventListener('click',closeModal);
      document.getElementById('btnCloseModalFooter').addEventListener('click',closeModal);
      document.getElementById('modalBackdrop').addEventListener('click',closeModal);
      document.getElementById('btnUpdateStatus').addEventListener('click',function(){const sel=document.getElementById('newStatusSelect');if(!sel||!selectedOrderId)return;const o=ORDERS.find(x=>x.id===selectedOrderId);if(o){o.status=sel.value;renderTable(currentFilter);}closeModal();});
      const sidebar=document.getElementById('supplierSidebar'),overlay=document.getElementById('sidebarOverlay'),toggleBtn=document.getElementById('sidebarToggle');
      toggleBtn.addEventListener('click',function(){sidebar.classList.toggle('open');overlay.classList.toggle('open');});
      overlay.addEventListener('click',function(){sidebar.classList.remove('open');overlay.classList.remove('open');});
    });
  </script>
<script src="../../../assets/js/pwa.js" defer></script>
</body>
</html>


