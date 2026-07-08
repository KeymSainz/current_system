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
  <title>Fix&amp;Go — Technician Orders</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body{background:var(--fg-bg);}
    .supplier-layout{display:flex;min-height:calc(100vh - 65px);}
    .supplier-sidebar{width:240px;background:var(--fg-card-bg);border-right:1px solid var(--fg-border);padding:1.5rem 0;flex-shrink:0;position:sticky;top:65px;height:calc(100vh - 65px);overflow-y:auto;}
    .sidebar-label{font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);padding:0 1.25rem;margin-bottom:0.5rem;}
    .sidebar-nav{list-style:none;padding:0;margin:0;}
    .sidebar-nav a{display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1.25rem;color:var(--fg-muted);text-decoration:none;font-size:0.88rem;font-weight:500;border-left:3px solid transparent;transition:all 0.2s;}
    .sidebar-nav a:hover{color:var(--fg-primary);background:rgba(230,168,0,0.07);border-left-color:var(--fg-primary);}
    .sidebar-nav a.active{color:var(--fg-primary);background:rgba(230,168,0,0.1);border-left-color:var(--fg-primary);font-weight:700;}
    .sidebar-nav a i{font-size:1rem;width:20px;text-align:center;}
    .supplier-main{flex:1;padding:2rem;min-width:0;}
    .section-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;margin-bottom:1.5rem;}
    .section-head{padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;}
    .section-head h6{margin:0;font-weight:700;font-size:0.95rem;color:var(--fg-text);}
    .mini-table{width:100%;border-collapse:collapse;font-size:0.83rem;}
    .mini-table th{background:var(--fg-bg);padding:0.6rem 1rem;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);}
    .mini-table td{padding:0.65rem 1rem;border-bottom:1px solid var(--fg-border);color:var(--fg-text);vertical-align:middle;}
    .mini-table tr:last-child td{border-bottom:none;}
    .mini-table tr:hover td{background:rgba(230,168,0,0.03);}
    .badge-status{display:inline-flex;align-items:center;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.7rem;font-weight:700;text-transform:uppercase;}
    .badge-pending{background:rgba(230,168,0,0.12);color:#c98f00;}
    .badge-confirmed{background:rgba(59,130,246,0.12);color:#3b82f6;}
    .badge-preparing{background:rgba(139,92,246,0.12);color:#8b5cf6;}
    .badge-ready{background:rgba(16,185,129,0.12);color:#10b981;}
    .badge-shipped{background:rgba(99,102,241,0.12);color:#6366f1;}
    .badge-delivered{background:rgba(40,167,69,0.12);color:#28A745;}
    .badge-cancelled{background:rgba(107,114,128,0.12);color:#6c757d;}
    .badge-paid{background:rgba(40,167,69,0.12);color:#28A745;}
    .stats-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1rem;margin-bottom:1.75rem;}
    .stat-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.1rem 1rem;text-align:center;}
    .stat-value{font-size:1.8rem;font-weight:800;line-height:1;}
    .stat-label{font-size:0.72rem;color:var(--fg-muted);font-weight:600;margin-top:0.2rem;}
    .filter-bar{display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;}
    .filter-input{padding:0.4rem 0.8rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.82rem;outline:none;transition:border-color 0.2s;}
    .filter-input:focus{border-color:var(--fg-primary);}
    .alert-bar{padding:0.75rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;}
    .alert-success{background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);}
    .alert-danger{background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @keyframes spin{to{transform:rotate(360deg);}}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .supplier-sidebar{position:fixed;top:65px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 65px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .supplier-sidebar.open{transform:translateX(0);}
      .supplier-main{padding:1.25rem;}
    }
  </style>
</head>
<body>
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;" onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge supplier">📦 Supplier</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="../../../dashboard.php" class="btn btn-sm" style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
  </nav>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="supplier-layout">
    <aside class="supplier-sidebar" id="supplierSidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
        <li><a href="owner-purchases.php"><i class="bi bi-cart-check"></i> Owner Purchases</a></li>
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Orders</a></li>
        <li><a href="deliveries.php"><i class="bi bi-truck"></i> Deliveries</a></li>
        <li><a href="tech-requests.php"><i class="bi bi-tools"></i> Tech Requests</a></li>
        <li><a href="tech-orders.php" class="active"><i class="bi bi-bag-check"></i> Tech Orders</a></li>
        <li><a href="sales-report.php"><i class="bi bi-bar-chart-line"></i> Sales Report</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>
    <main class="supplier-main">
      <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;"><i class="bi bi-bag-check" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Technician Orders</h2>
        <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">Orders placed by phone technicians — confirm, prepare, and fulfill them</p>
      </div>
      <div id="alertBox" style="display:none;"></div>
      <div class="stats-row">
        <div class="stat-card"><div class="stat-value" style="color:var(--fg-primary);" id="statTotal">—</div><div class="stat-label">Total</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#c98f00;" id="statPending">—</div><div class="stat-label">Pending</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#3b82f6;" id="statConfirmed">—</div><div class="stat-label">Confirmed</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#28A745;" id="statDelivered">—</div><div class="stat-label">Delivered</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#10b981;" id="statRevenue">—</div><div class="stat-label">Revenue</div></div>
      </div>
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-bag-check" style="color:var(--fg-primary);"></i> Orders from Technicians</h6>
          <div class="filter-bar">
            <input type="text" class="filter-input" id="searchInput" placeholder="Search technician or ref…" oninput="applyFilters()" style="min-width:180px;">
            <select class="filter-input" id="statusFilter" onchange="applyFilters()">
              <option value="all">All Status</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="preparing">Preparing</option>
              <option value="ready">Ready</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        <div style="overflow-x:auto;">
          <table class="mini-table">
            <thead><tr><th>#</th><th>Technician</th><th>Items</th><th>Total</th><th>Payment</th><th>Fulfillment</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody id="ordersBody"><tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--fg-muted);"><div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Status Update Modal -->
  <div id="statusModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:16px;width:100%;max-width:420px;padding:1.75rem;">
      <h6 style="font-weight:800;margin:0 0 1rem;color:var(--fg-text);" id="modalTitle">Update Order Status</h6>
      <div style="margin-bottom:1rem;">
        <label style="font-size:0.82rem;font-weight:700;color:var(--fg-text);display:block;margin-bottom:0.4rem;">New Status</label>
        <select id="modalStatus" class="filter-input" style="width:100%;">
          <option value="confirmed">Confirmed</option>
          <option value="preparing">Preparing</option>
          <option value="ready">Ready for Pickup/Delivery</option>
          <option value="shipped">Shipped</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
      <div style="margin-bottom:1.25rem;">
        <label style="font-size:0.82rem;font-weight:700;color:var(--fg-text);display:block;margin-bottom:0.4rem;">Note for Technician (optional)</label>
        <textarea id="modalNotes" rows="2" class="filter-input" style="width:100%;resize:vertical;" placeholder="e.g. Ready for pickup at 2pm…"></textarea>
      </div>
      <div style="display:flex;gap:0.75rem;justify-content:flex-end;">
        <button onclick="closeStatusModal()" style="padding:0.5rem 1.1rem;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.85rem;font-weight:600;cursor:pointer;">Cancel</button>
        <button id="modalConfirmBtn" onclick="confirmStatusUpdate()" style="padding:0.5rem 1.25rem;border-radius:8px;border:none;font-size:0.85rem;font-weight:700;cursor:pointer;color:#fff;background:var(--fg-primary);">Update</button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script src="../../assets/js/session-timeout.js"></script>
  <script>
  'use strict';
  const API = '../../../backend/seller_tech_orders.php';
  let allOrders = [], pendingOrderId = null;

  function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
  function peso(n){ return '₱'+parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:2}); }
  function fmtDate(d){ return d?new Date(d).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'}):'—'; }

  document.addEventListener('DOMContentLoaded', function(){
    const user = FGAuth.UserStore.get();
    if(!user||user.role!=='supplier'){ window.location.href='../../../login.html'; return; }
    document.getElementById('navUserName').textContent = ((user.firstName||'')+' '+(user.lastName||'')).trim()||user.email;
    const sidebar=document.getElementById('supplierSidebar'), overlay=document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click',()=>{ sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click',()=>{ sidebar.classList.remove('open'); overlay.classList.remove('open'); });
    loadStats(); loadOrders();
  });

  function loadStats(){
    fetch(API+'?action=stats',{credentials:'include'}).then(r=>r.json()).then(d=>{
      if(!d.success) return;
      const s=d.stats||{};
      document.getElementById('statTotal').textContent    = s.total||0;
      document.getElementById('statPending').textContent  = s.pending||0;
      document.getElementById('statConfirmed').textContent= s.confirmed||0;
      document.getElementById('statDelivered').textContent= s.delivered||0;
      document.getElementById('statRevenue').textContent  = peso(s.total_revenue||0);
    }).catch(()=>{});
  }

  function loadOrders(){
    fetch(API+'?action=list',{credentials:'include'}).then(r=>r.json()).then(d=>{
      if(!d.success){ renderOrders([]); return; }
      allOrders = d.orders||[];
      renderOrders(allOrders);
    }).catch(()=>{ document.getElementById('ordersBody').innerHTML='<tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--fg-muted);">Network error.</td></tr>'; });
  }

  function applyFilters(){
    const q = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    let items = allOrders;
    if(status!=='all') items=items.filter(o=>o.order_status===status);
    if(q) items=items.filter(o=>(o.technician_name||'').toLowerCase().includes(q)||(o.reference||'').toLowerCase().includes(q));
    renderOrders(items);
  }

  const statusMap={pending:'badge-pending',confirmed:'badge-confirmed',preparing:'badge-preparing',ready:'badge-ready',shipped:'badge-shipped',delivered:'badge-delivered',cancelled:'badge-cancelled'};
  const payMap={pending:'badge-pending',paid:'badge-paid',failed:'badge-cancelled'};

  function renderOrders(orders){
    const tbody=document.getElementById('ordersBody');
    if(!orders.length){ tbody.innerHTML='<tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--fg-muted);"><i class="bi bi-bag" style="font-size:2rem;display:block;margin-bottom:0.75rem;opacity:0.3;"></i>No technician orders yet.</td></tr>'; return; }
    tbody.innerHTML = orders.map(o=>{
      const items=(o.items||[]).map(i=>`${i.quantity}× ${esc(i.product_name)}`).join(', ');
      const canUpdate = !['delivered','cancelled'].includes(o.order_status);
      const actions = canUpdate
        ? `<button onclick="openStatusModal(${o.id},'${o.order_status}')" style="padding:0.25rem 0.65rem;border-radius:6px;font-size:0.72rem;font-weight:700;cursor:pointer;border:1.5px solid var(--fg-primary);color:var(--fg-primary);background:transparent;transition:all 0.2s;" onmouseenter="this.style.background='var(--fg-primary)';this.style.color='#000'" onmouseleave="this.style.background='transparent';this.style.color='var(--fg-primary)'">Update</button>`
        : '<span style="font-size:0.75rem;color:var(--fg-muted);">—</span>';
      return `<tr>
        <td style="font-weight:700;color:var(--fg-primary);">#${o.id}</td>
        <td><div style="font-weight:600;">${esc(o.technician_name||'—')}</div><div style="font-size:0.7rem;color:var(--fg-muted);">${esc(o.technician_email||'')}</div></td>
        <td style="max-width:160px;font-size:0.78rem;color:var(--fg-muted);">${items||'—'}</td>
        <td style="font-weight:700;">${peso(o.total_amount)}</td>
        <td><span class="badge-status ${payMap[o.payment_status]||'badge-pending'}">${o.payment_method.toUpperCase()} · ${o.payment_status}</span></td>
        <td><span class="badge-status ${o.fulfillment_type==='pickup'?'badge-confirmed':'badge-shipped'}">${o.fulfillment_type==='pickup'?'🏪 Pickup':'🚚 Delivery'}</span>
          ${o.delivery_address?`<div style="font-size:0.68rem;color:var(--fg-muted);max-width:120px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${esc(o.delivery_address)}">${esc(o.delivery_address)}</div>`:''}
        </td>
        <td><span class="badge-status ${statusMap[o.order_status]||'badge-pending'}">${o.order_status}</span></td>
        <td style="color:var(--fg-muted);font-size:0.78rem;">${fmtDate(o.created_at)}</td>
        <td>${actions}</td>
      </tr>`;
    }).join('');
  }

  function openStatusModal(orderId, currentStatus){
    pendingOrderId = orderId;
    document.getElementById('modalTitle').textContent = 'Update Order #'+orderId;
    document.getElementById('modalNotes').value = '';
    const sel = document.getElementById('modalStatus');
    // Set next logical status
    const flow = {pending:'confirmed',confirmed:'preparing',preparing:'ready',ready:'shipped',shipped:'delivered'};
    sel.value = flow[currentStatus] || 'confirmed';
    document.getElementById('statusModal').style.display='flex';
  }
  function closeStatusModal(){ document.getElementById('statusModal').style.display='none'; pendingOrderId=null; }
  document.getElementById('statusModal').addEventListener('click',function(e){ if(e.target===this) closeStatusModal(); });

  function confirmStatusUpdate(){
    if(!pendingOrderId) return;
    const status = document.getElementById('modalStatus').value;
    const notes  = document.getElementById('modalNotes').value.trim();
    const btn    = document.getElementById('modalConfirmBtn');
    btn.disabled=true; btn.textContent='Updating…';
    fetch(API,{method:'POST',credentials:'include',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({action:'update_status',order_id:pendingOrderId,status,notes})})
      .then(r=>r.json()).then(d=>{
        if(!d.success) throw new Error(d.message||'Failed.');
        closeStatusModal();
        showAlert('success','Order updated successfully.');
        loadStats(); loadOrders();
      }).catch(err=>showAlert('danger',err.message))
      .finally(()=>{ btn.disabled=false; btn.textContent='Update'; });
  }

  function showAlert(type,msg){
    const box=document.getElementById('alertBox');
    box.style.display='flex'; box.className='alert-bar alert-'+type;
    box.innerHTML='<i class="bi bi-'+(type==='success'?'check-circle-fill':'exclamation-triangle-fill')+'"></i> '+esc(msg);
    setTimeout(()=>{ box.style.display='none'; },5000);
  }
  </script>

</body>
</html>




