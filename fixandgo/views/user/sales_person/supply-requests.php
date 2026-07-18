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
  <title>Fix&amp;Go - Supply Requests</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=5" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .sp-layout { display: flex; min-height: calc(100vh - 68px); }
    .sp-sidebar { width: 240px; flex-shrink: 0; background: var(--fg-card-bg); border-right: 1px solid var(--fg-border); padding: 1.5rem 0; position: sticky; top: 68px; height: calc(100vh - 68px); overflow-y: auto; }
    .sidebar-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--fg-muted); padding: 0 1.25rem; margin-bottom: 0.5rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.25rem; color: var(--fg-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.2s; }
    .sidebar-nav a:hover { color: var(--fg-primary); background: rgba(230,168,0,0.07); border-left-color: var(--fg-primary); }
    .sidebar-nav a.active { color: var(--fg-primary); background: rgba(230,168,0,0.1); border-left-color: var(--fg-primary); font-weight: 700; }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }
    .sp-main { flex: 1; padding: 2rem; min-width: 0; }
    .page-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem; }
    .page-header h2 { font-size: 1.6rem; font-weight: 800; color: var(--fg-text); margin: 0; }
    .page-header p { color: var(--fg-muted); margin: 0; font-size: 0.88rem; }
    .section-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 14px; overflow: hidden; margin-bottom: 1.5rem; }
    .section-head { padding: 1rem 1.25rem; border-bottom: 1px solid var(--fg-border); display: flex; align-items: center; justify-content: space-between; }
    .section-head h6 { margin: 0; font-weight: 700; font-size: 0.95rem; color: var(--fg-text); }
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.84rem; }
    .data-table thead th { background: var(--fg-primary); color: #fff; padding: 0.7rem 1rem; text-align: left; font-weight: 700; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.6px; white-space: nowrap; }
    .data-table tbody td { padding: 0.7rem 1rem; border-bottom: 1px solid var(--fg-border); color: var(--fg-text); vertical-align: middle; }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(230,168,0,0.03); }
    .badge-status { display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .badge-pending  { background: rgba(230,168,0,0.12); color: #c98f00; }
    .badge-approved, .badge-active, .badge-completed { background: rgba(40,167,69,0.12); color: #28A745; }
    .badge-rejected, .badge-cancelled { background: rgba(220,53,69,0.12); color: #dc3545; }
    .badge-inactive { background: rgba(108,117,125,0.12); color: #6C757D; }
    .btn-act { width: 32px; height: 32px; border-radius: 8px; border: 1.5px solid var(--fg-border); background: transparent; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem; color: var(--fg-muted); transition: all 0.2s; margin: 0 1px; }
    .btn-act:hover { transform: scale(1.1); }
    .btn-act.edit:hover { border-color: var(--fg-primary); color: var(--fg-primary); background: rgba(230,168,0,0.08); }
    .btn-act.del:hover { border-color: #dc3545; color: #dc3545; background: rgba(220,53,69,0.08); }
    .btn-act.toggle:hover { border-color: #3b82f6; color: #3b82f6; background: rgba(59,130,246,0.08); }
    .empty-state { text-align: center; padding: 3rem 2rem; color: var(--fg-muted); }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; opacity: 0.4; }
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.55); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 18px; box-shadow: 0 24px 64px rgba(0,0,0,0.4); width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; animation: modalIn 0.25s cubic-bezier(0.16,1,0.3,1); }
    @keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    .modal-head { padding: 1.5rem 1.75rem 1.25rem; border-bottom: 1px solid var(--fg-border); display: flex; align-items: center; justify-content: space-between; }
    .modal-head h5 { margin: 0; font-weight: 800; font-size: 1.1rem; color: var(--fg-text); }
    .modal-body { padding: 1.5rem 1.75rem; }
    .modal-foot { padding: 1.25rem 1.75rem; border-top: 1px solid var(--fg-border); display: flex; gap: 0.75rem; justify-content: flex-end; }
    .btn-close-modal { width: 32px; height: 32px; border-radius: 8px; border: 1.5px solid var(--fg-border); background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--fg-muted); font-size: 1rem; transition: all 0.2s; }
    .btn-close-modal:hover { border-color: #dc3545; color: #dc3545; }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label { display: block; font-size: 0.82rem; font-weight: 700; color: var(--fg-text); margin-bottom: 0.4rem; }
    .form-group label span { color: #dc3545; margin-left: 2px; }
    .form-input { width: 100%; padding: 0.65rem 0.9rem; border: 1.5px solid var(--fg-border); border-radius: 10px; background: var(--fg-bg); color: var(--fg-text); font-size: 0.88rem; outline: none; transition: border-color 0.2s; font-family: inherit; }
    .form-input:focus { border-color: var(--fg-primary); box-shadow: 0 0 0 3px rgba(230,168,0,0.15); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .alert-bar { padding: 0.75rem 1.25rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem; }
    .alert-success { background: rgba(40,167,69,0.12); color: #28A745; border: 1px solid rgba(40,167,69,0.25); }
    .alert-danger { background: rgba(220,53,69,0.12); color: #dc3545; border: 1px solid rgba(220,53,69,0.25); }
    .sidebar-toggle { display: none; background: none; border: 1.5px solid var(--fg-border); border-radius: 8px; padding: 0.3rem 0.6rem; color: var(--fg-text); cursor: pointer; font-size: 1.1rem; }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-overlay.open { display: block; }
    @media (max-width: 768px) { .sidebar-toggle { display: flex; align-items: center; } .sp-sidebar { position: fixed; top: 68px; left: 0; z-index: 200; transform: translateX(-100%); height: calc(100vh - 68px); box-shadow: 4px 0 20px rgba(0,0,0,0.15); transition: transform 0.3s; } .sp-sidebar.open { transform: translateX(0); } .sp-main { padding: 1.25rem; } .form-row { grid-template-columns: 1fr; } }
    @keyframes spin { to { transform: rotate(360deg); } }
    .two-col { display:grid; grid-template-columns:1fr 1.6fr; gap:1.5rem; align-items:start; }
    .form-card { background:var(--fg-card-bg); border:1px solid var(--fg-border); border-radius:14px; padding:1.5rem; }
    .form-card h6 { font-weight:800; font-size:1rem; color:var(--fg-text); margin-bottom:1.25rem; }
    @media (max-width:900px) { .two-col { grid-template-columns:1fr; } }
  </style>
</head>
<body>
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='&lt;span style=&apos;font-size:1.2rem;font-weight:800;color:var(--fg-primary);&apos;&gt;Fix&amp;Go&lt;/span&gt;'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge sales_person">&#x1F4BC; Sales Person</span>
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
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Customer Orders</a></li>
        <li><a href="inventory.php"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="supply-requests.php" class="active"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>
    <main class="sp-main">
      <div class="page-header">
        <div>
          <h2><i class="bi bi-send" style="color:#c98f00;margin-right:0.5rem;"></i>Supply Requests</h2>
          <p>Request additional product supply from supervisor</p>
        </div>
      </div>

      <div id="alertBox" style="display:none;"></div>

      <div class="two-col">
        <!-- New Request Form -->
        <div class="form-card">
          <h6><i class="bi bi-plus-circle-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>New Supply Request</h6>
          <form id="requestForm">
            <div class="form-group">
              <label>Product Name <span>*</span></label>
              <input type="text" class="form-input" id="reqProduct" placeholder="e.g. iPhone 14 Screen" required>
            </div>
            <div class="form-group">
              <label>Category</label>
              <input type="text" class="form-input" id="reqCategory" placeholder="e.g. Accessories">
            </div>
            <div class="form-group">
              <label>Quantity Requested <span>*</span></label>
              <input type="number" class="form-input" id="reqQty" min="1" value="1" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label>Reason / Notes</label>
              <textarea class="form-input" id="reqReason" rows="3" placeholder="Why do you need this product?"></textarea>
            </div>
            <button type="submit" class="btn-primary-custom" style="margin-top:1.25rem;width:100%;" id="btnSubmitReq">
              <i class="bi bi-send-fill"></i> Submit Request
            </button>
          </form>
        </div>

        <!-- Requests Table -->
        <div>
          <div class="section-card">
            <div class="section-head">
              <h6><i class="bi bi-list-ul" style="color:#c98f00;margin-right:0.4rem;"></i>My Requests</h6>
              <div style="display:flex;gap:0.4rem;">
                <button class="btn-filter active" data-filter="all" style="font-size:0.72rem;padding:0.25rem 0.65rem;">All</button>
                <button class="btn-filter" data-filter="pending" style="font-size:0.72rem;padding:0.25rem 0.65rem;">Pending</button>
                <button class="btn-filter" data-filter="approved" style="font-size:0.72rem;padding:0.25rem 0.65rem;">Approved</button>
                <button class="btn-filter" data-filter="rejected" style="font-size:0.72rem;padding:0.25rem 0.65rem;">Rejected</button>
              </div>
            </div>
            <div style="overflow-x:auto;">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th style="text-align:center;">Qty</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align:center;">Del</th>
                  </tr>
                </thead>
                <tbody id="requestsTableBody">
                  <tr><td colspan="6"><div class="empty-state"><i class="bi bi-hourglass-split"></i><p>Loading...</p></div></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
        const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'sales_person') { window.location.href = '../../../login.html'; return; }
    const fullName = ((user.firstName || '') + ' ' + (user.lastName || '')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email || 'Sales Person';
        const sidebar = document.getElementById('spSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', function() { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', function() { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    let allRequests = [];
    let currentFilter = 'all';

    // Pre-fill from URL params (from inventory page)
    const params = new URLSearchParams(window.location.search);
    if (params.get('product')) document.getElementById('reqProduct').value = decodeURIComponent(params.get('product'));
    if (params.get('category')) document.getElementById('reqCategory').value = decodeURIComponent(params.get('category'));

    loadRequests();
    loadUnreadMessageCount();

    function loadRequests() {
      fetch('../../../backend/sales_supply_requests.php?action=list')
        .then(r => r.json())
        .then(d => {
          if (!d.success) throw new Error(d.message);
          allRequests = d.requests || [];
          renderTable();
        })
        .catch(() => {
          document.getElementById('requestsTableBody').innerHTML =
            '<tr><td colspan="6"><div class="empty-state"><i class="bi bi-inbox"></i><p>No requests yet.</p></div></td></tr>';
        });
    }

    function renderTable() {
      let filtered = allRequests;
      if (currentFilter !== 'all') filtered = filtered.filter(r => r.status === currentFilter);

      const tbody = document.getElementById('requestsTableBody');
      if (!filtered.length) {
        tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state"><i class="bi bi-inbox"></i><p>No requests found.</p></div></td></tr>';
        return;
      }
      tbody.innerHTML = filtered.map(r => {
        const date = new Date(r.created_at).toLocaleDateString('en-PH');
        const cls = r.status === 'approved' ? 'badge-approved' : r.status === 'rejected' ? 'badge-rejected' : 'badge-pending';
        const delBtn = r.status === 'pending'
          ? '<button class="btn-act del" title="Delete" onclick="deleteRequest(' + r.id + ')"><i class="bi bi-trash-fill"></i></button>'
          : '<span style="color:var(--fg-muted);font-size:0.75rem;">â€”</span>';
        return '<tr>' +
          '<td style="font-weight:600;">' + escHtml(r.product_name) + (r.supervisor_notes ? '<br><small style="color:var(--fg-muted);font-size:0.72rem;font-weight:400;">' + escHtml(r.supervisor_notes) + '</small>' : '') + '</td>' +
          '<td style="color:var(--fg-muted);">' + escHtml(r.category || 'â€”') + '</td>' +
          '<td style="text-align:center;font-weight:700;">' + r.quantity_requested + '</td>' +
          '<td><span class="badge-status ' + cls + '">' + r.status + '</span></td>' +
          '<td style="color:var(--fg-muted);">' + date + '</td>' +
          '<td style="text-align:center;">' + delBtn + '</td>' +
          '</tr>';
      }).join('');
    }

    document.querySelectorAll('.btn-filter').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        renderTable();
      });
    });

    // Submit form
    document.getElementById('requestForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const product = document.getElementById('reqProduct').value.trim();
      const category = document.getElementById('reqCategory').value.trim();
      const qty = parseInt(document.getElementById('reqQty').value);
      const reason = document.getElementById('reqReason').value.trim();

      if (!product || qty < 1) {
        showAlert('danger', 'Product name and quantity are required.');
        return;
      }

      const btn = document.getElementById('btnSubmitReq');
      btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';

      fetch('../../../backend/sales_supply_requests.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: 'create', product_name: product, category, quantity_requested: qty, reason })
      })
        .then(r => r.json())
        .then(d => {
          if (!d.success) throw new Error(d.message);
          showAlert('success', 'Supply request submitted successfully!');
          document.getElementById('requestForm').reset();
          loadRequests();
        })
        .catch(err => showAlert('danger', err.message))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i> Submit Request'; });
    });

    window.deleteRequest = function(id) {
      if (!confirm('Delete this pending request?')) return;
      fetch('../../../backend/sales_supply_requests.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: 'delete', request_id: id })
      })
        .then(r => r.json())
        .then(d => {
          if (!d.success) throw new Error(d.message);
          showAlert('success', 'Request deleted.');
          loadRequests();
        })
        .catch(err => showAlert('danger', err.message));
    };

    function showAlert(type, msg) {
      const box = document.getElementById('alertBox');
      box.style.display = 'flex';
      box.className = 'alert-bar alert-' + type;
      box.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill') + '"></i>' + escHtml(msg);
      setTimeout(() => { box.style.display = 'none'; }, 4000);
    }
  });

    function escHtml(s) { if (!s) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

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

