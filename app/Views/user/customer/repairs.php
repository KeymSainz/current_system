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
  <title>Fix&amp;Go — My Repairs</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body{background:var(--fg-bg);}
    .cu-layout{display:flex;min-height:calc(100vh - 68px);}
    .cu-sidebar{width:260px;flex-shrink:0;background:var(--fg-card-bg);border-right:1px solid var(--fg-border);padding:1.5rem 0 2rem;position:sticky;top:68px;height:calc(100vh - 68px);overflow-y:auto;}
    .sidebar-profile{display:flex;align-items:center;gap:0.85rem;padding:0 1.25rem 1.25rem;border-bottom:1px solid var(--fg-border);margin-bottom:0.75rem;}
    .sidebar-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,rgba(230,168,0,0.25),rgba(230,168,0,0.08));border:2px solid rgba(230,168,0,0.35);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--fg-primary);font-weight:800;flex-shrink:0;overflow:hidden;}
    .sidebar-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;}
    .sidebar-profile-name{font-size:0.9rem;font-weight:700;color:var(--fg-text);}
    .sidebar-profile-edit{font-size:0.75rem;color:var(--fg-primary);text-decoration:none;font-weight:600;}
    .sidebar-section-label{font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);padding:0.75rem 1.25rem 0.35rem;}
    .sidebar-nav{list-style:none;padding:0;margin:0;}
    .sidebar-nav li a{display:flex;align-items:center;gap:0.75rem;padding:0.6rem 1.25rem;color:var(--fg-muted);text-decoration:none;font-size:0.88rem;font-weight:500;border-left:3px solid transparent;transition:all 0.2s;}
    .sidebar-nav li a:hover{color:var(--fg-primary);background:rgba(230,168,0,0.07);border-left-color:var(--fg-primary);}
    .sidebar-nav li a.active{color:var(--fg-primary);background:rgba(230,168,0,0.1);border-left-color:var(--fg-primary);font-weight:700;}
    .sidebar-nav li a i{font-size:1rem;width:20px;text-align:center;}
    .cu-main{flex:1;padding:2rem;min-width:0;}
    .page-header{margin-bottom:1.75rem;}
    .page-header h2{font-size:1.5rem;font-weight:800;color:var(--fg-text);margin:0 0 0.25rem;}
    .page-header p{color:var(--fg-muted);margin:0;font-size:0.88rem;}
    .section-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;margin-bottom:1.5rem;}
    .section-head{padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;}
    .section-head h6{margin:0;font-weight:700;font-size:0.95rem;color:var(--fg-text);}
    .empty-state{text-align:center;padding:4rem 2rem;color:var(--fg-muted);}
    .empty-state i{font-size:3rem;display:block;margin-bottom:1rem;opacity:0.3;}
    .empty-state p{font-size:0.9rem;margin:0 0 1rem;}
    .badge-status{display:inline-flex;align-items:center;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.7rem;font-weight:700;text-transform:uppercase;}
    .badge-pending{background:rgba(230,168,0,0.12);color:#c98f00;}
    .badge-progress{background:rgba(59,130,246,0.12);color:#3b82f6;}
    .badge-completed{background:rgba(40,167,69,0.12);color:#28A745;}
    .badge-cancelled{background:rgba(220,53,69,0.12);color:#dc3545;}
    .data-table{width:100%;border-collapse:collapse;font-size:0.84rem;}
    .data-table thead th{background:var(--fg-primary);color:#fff;padding:0.7rem 1rem;text-align:left;font-weight:700;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.6px;}
    .data-table tbody td{padding:0.7rem 1rem;border-bottom:1px solid var(--fg-border);color:var(--fg-text);vertical-align:middle;}
    .data-table tbody tr:last-child td{border-bottom:none;}
    .data-table tbody tr:hover{background:rgba(230,168,0,0.03);}
    /* Filter tabs */
    .filter-tabs{display:flex;gap:0.4rem;flex-wrap:wrap;margin-bottom:1.25rem;}
    .filter-tab{padding:0.4rem 1rem;border-radius:20px;border:1.5px solid var(--fg-border);background:var(--fg-card-bg);color:var(--fg-muted);font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.2s;text-decoration:none;}
    .filter-tab:hover,.filter-tab.active{background:var(--fg-primary);border-color:var(--fg-primary);color:#fff;}
    /* Info banner */
    .info-banner{display:flex;align-items:center;gap:0.75rem;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:10px;padding:0.85rem 1.1rem;margin-bottom:1.25rem;font-size:0.85rem;color:#3b82f6;font-weight:500;}
    .info-banner i{font-size:1.1rem;flex-shrink:0;}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .cu-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .cu-sidebar.open{transform:translateX(0);}
      .cu-main{padding:1.25rem;}
    }
    @media(max-width:575px){
      html,body{overflow-x:hidden;}
      .cu-main{padding:0.75rem;}
      /* Navbar: hide non-essential labels on tiny screens */
      #navUserName{display:none!important;}
      .role-badge{display:none!important;}
      /* Make table scrollable and readable */
      .data-table thead th{font-size:0.65rem!important;padding:0.5rem 0.6rem!important;}
      .data-table tbody td{font-size:0.78rem!important;padding:0.5rem 0.6rem!important;}
      /* Filter tabs wrap nicely */
      .filter-tabs{gap:0.3rem!important;}
      .filter-tab{font-size:0.75rem!important;padding:0.3rem 0.7rem!important;}
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation" style="flex-wrap:nowrap !important;">
    <div class="d-flex align-items-center gap-2">
      <a href="dashboard.php" style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);text-decoration:none;flex-shrink:0;" title="Back to Dashboard">
        <i class="bi bi-arrow-left"></i>
      </a>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:38px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-2" style="flex-wrap:nowrap;">
      <span class="role-badge customer rep-desk" style="display:none;">👤 Customer</span>
      <span id="navUserName" class="rep-desk" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);display:none;"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="/index.php?browse=1" class="btn btn-sm rep-desk" style="display:none;border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;">
        <i class="bi bi-shop"></i> Browse Shop
      </a>
      <button onclick="customerLogout()" class="btn btn-sm rep-desk" style="display:none;border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
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
    </div>
  </nav>
  <style>
    @media (min-width: 992px) { .rep-desk { display: flex !important; } }
    @media (max-width: 991px) { .cu-sidebar { display: none !important; } body { padding-bottom: 70px; } }
  </style>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="cu-layout">

    <!-- Sidebar -->
    <aside class="cu-sidebar" id="cuSidebar">
      <div class="sidebar-profile">
        <div class="sidebar-avatar" id="sidebarAvatarInitials">?</div>
        <div>
          <div class="sidebar-profile-name" id="sidebarName">Loading…</div>
          <a href="profile.php" class="sidebar-profile-edit"><i class="bi bi-pencil-fill" style="font-size:0.65rem;"></i> Edit Profile</a>
        </div>
      </div>
      <div class="sidebar-section-label">My Account</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
        <li><a href="notifications.php"><i class="bi bi-bell-fill"></i> Notifications</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
      <div class="sidebar-section-label">Shopping</div>
      <ul class="sidebar-nav">
        <li><a href="orders.php"><i class="bi bi-bag-fill"></i> My Orders</a></li>
        <li><a href="repairs.php" class="active"><i class="bi bi-tools"></i> My Repairs</a></li>
        <li><a href="wishlist.php"><i class="bi bi-heart-fill"></i> Wishlist</a></li>
        <li><a href="vouchers.php"><i class="bi bi-ticket-perforated-fill"></i> My Vouchers</a></li>
      </ul>
      <div class="sidebar-section-label">Fix&amp;Go</div>
      <ul class="sidebar-nav">
        <li><a href="/index.php?browse=1"><i class="bi bi-shop-window"></i> Browse Shop</a></li>
        <li><a href="seller-centre.php"><i class="bi bi-shop-window"></i> Seller Centre</a></li>
        <li><a href="become-technician.php"><i class="bi bi-wrench-adjustable-circle-fill"></i> Become a Technician</a></li>
      </ul>
    </aside>

    <!-- Main content -->
    <main class="cu-main">

      <div class="page-header">
        <h2><i class="bi bi-tools" style="color:#c98f00;margin-right:0.5rem;"></i>My Repairs</h2>
        <p>Track your device repair bookings</p>
      </div>

      <!-- Info banner -->
      <div class="info-banner">
        <i class="bi bi-info-circle-fill"></i>
        Find a technician on the landing page to book a repair
        <a href="/index.php?browse=1#technicians" style="margin-left:auto;color:#3b82f6;font-weight:700;text-decoration:none;white-space:nowrap;">Find Technicians →</a>
      </div>

      <!-- Filter tabs -->
      <div class="filter-tabs" id="filterTabs">
        <a class="filter-tab active" data-filter="all" href="#">All</a>
        <a class="filter-tab" data-filter="pending" href="#">Pending</a>
        <a class="filter-tab" data-filter="progress" href="#">In Progress</a>
        <a class="filter-tab" data-filter="completed" href="#">Completed</a>
        <a class="filter-tab" data-filter="cancelled" href="#">Cancelled</a>
      </div>

      <!-- Repairs table -->
      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-tools" style="color:#c98f00;margin-right:0.4rem;"></i>Repair Bookings</h6>
          <span id="repairCount" style="font-size:0.8rem;color:var(--fg-muted);font-weight:600;">0 bookings</span>
        </div>
        <div style="overflow-x:auto;" id="repairsTableWrap">
          <div class="empty-state">
            <i class="bi bi-tools"></i>
            <p>No repairs booked yet.</p>
            <a href="/index.php?browse=1#technicians"
               style="display:inline-flex;align-items:center;gap:0.4rem;background:var(--fg-primary);color:#fff;padding:0.55rem 1.25rem;border-radius:9px;font-size:0.85rem;font-weight:700;text-decoration:none;">
              <i class="bi bi-wrench-adjustable"></i> Book a Repair
            </a>
          </div>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="/assets/js/session-timeout.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'customer') { window.location.href = '/login.html'; return; }
    const fullName = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email;
    document.getElementById('sidebarName').textContent = fullName || user.email;
    const initials = ((user.firstName||'')[0]||'') + ((user.lastName||'')[0]||'');
    (function renderAvatar(url) {
      var el = document.getElementById('sidebarAvatarInitials');
      if (!el) return;
      if (url) { el.innerHTML = '<img src="' + url + '" alt="avatar" onerror="this.parentElement.textContent=\'' + initials.toUpperCase() + '\'">' ; }
      else { el.textContent = initials.toUpperCase() || '?'; }
    })(user.avatar_url || null);
    fetch('../../../api/session/user', { credentials: 'include' })
      .then(function(r){return r.json();}).then(function(d){
        if (d.loggedIn && d.user) {
          FGAuth.UserStore.save(d.user);
          var el = document.getElementById('sidebarAvatarInitials');
          if (el && d.user.avatar_url) el.innerHTML = '<img src="' + d.user.avatar_url + '" alt="avatar" onerror="this.parentElement.textContent=\'' + initials.toUpperCase() + '\'">';
        }
      }).catch(function(){});
    const sidebar = document.getElementById('cuSidebar'), overlay = document.getElementById('sidebarOverlay');
    var st = document.getElementById('sidebarToggle');
    if (st && sidebar && overlay) {
      st.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
      overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });
    }
    loadUnreadMessageCount();

    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
      tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        renderRepairs(this.dataset.filter);
      });
    });

    loadBookings();
  });

  // ── Real bookings from API ─────────────────────────────────────────────
  const repairsData = [];

  function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  function renderRepairs(filter) {
    const wrap = document.getElementById('repairsTableWrap');
    const filtered = filter === 'all' ? repairsData : repairsData.filter(r => {
      if (filter === 'progress') return r.status === 'in_progress';
      return r.status === filter;
    });
    document.getElementById('repairCount').textContent = filtered.length + ' booking' + (filtered.length !== 1 ? 's' : '');

    if (filtered.length === 0) {
      wrap.innerHTML = `<div class="empty-state"><i class="bi bi-tools"></i><p>No repairs booked yet.</p><a href="/index.php#technicians" style="display:inline-flex;align-items:center;gap:0.4rem;background:var(--fg-primary);color:#fff;padding:0.55rem 1.25rem;border-radius:9px;font-size:0.85rem;font-weight:700;text-decoration:none;"><i class="bi bi-wrench-adjustable"></i> Book a Repair</a></div>`;
      return;
    }

    const statusLabel = { pending:'Pending', confirmed:'Confirmed', in_progress:'In Progress', completed:'Completed', cancelled:'Cancelled' };
    const statusClass = { pending:'badge-pending', confirmed:'badge-progress', in_progress:'badge-progress', completed:'badge-completed', cancelled:'badge-cancelled' };

    const rows = filtered.map(r => {
      const techName = r.technician_name
        ? `<div style="font-weight:600;">${esc(r.technician_name)}</div><div style="font-size:0.72rem;color:var(--fg-muted);">${esc(r.shop_name||'')}</div>`
        : '<span style="color:var(--fg-muted);">Unassigned</span>';

      const cancelBtn = r.status === 'pending'
        ? `<button onclick="cancelBooking(${r.id})" style="padding:0.2rem 0.55rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid #dc3545;color:#dc3545;background:transparent;" onmouseenter="this.style.background='#dc3545';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#dc3545'">Cancel</button>`
        : '';

      const reviewBtn = r.status === 'completed' && r.technician_id
        ? `<button onclick="openReviewModal(${r.id},${r.technician_id},'${esc(r.technician_name||'Technician')}')" id="revBtn_${r.id}"
            style="padding:0.2rem 0.65rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid var(--fg-primary);color:var(--fg-primary);background:transparent;margin-left:0.2rem;"
            onmouseenter="this.style.background='var(--fg-primary)';this.style.color='#fff'"
            onmouseleave="this.style.background='transparent';this.style.color='var(--fg-primary)'">
            ⭐ Review
          </button>`
        : '';

      // Payment button — shown on completed repairs where customer hasn't paid yet
      const alreadyPaid = r.customer_payment_status === 'paid';
      const laborFee = parseFloat(r.labor_fee || 0);
      const partsFee = parseFloat(r.parts_fee || 0);
      const fee = parseFloat(r.repair_fee || r.total_amount || r.total_price || 0);
      const payBtn = r.status === 'completed'
        ? alreadyPaid
          ? `<span style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.2rem 0.6rem;border-radius:6px;font-size:0.7rem;font-weight:700;background:rgba(40,167,69,0.1);color:#28A745;margin-left:0.2rem;">✅ Paid</span>`
          : `<button onclick="openPayModal(${r.id})"
              id="payBtn_${r.id}"
              style="padding:0.2rem 0.65rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid #28A745;color:#28A745;background:transparent;margin-left:0.2rem;"
              onmouseenter="this.style.background='#28A745';this.style.color='#fff'"
              onmouseleave="this.style.background='transparent';this.style.color='#28A745'">
              💳 Pay Now
            </button>`
        : '';

      // Show receipt link if technician uploaded one
      const rcptLink = r.receipt_path
        ? `<a href="http://${location.hostname}/${esc(r.receipt_path)}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:0.25rem;font-size:0.7rem;font-weight:700;color:#28A745;text-decoration:none;margin-left:0.2rem;"
             title="View receipt">🧾</a>`
        : '';

      // Amount display
      const amountHtml = fee > 0
        ? `<span style="font-weight:700;color:var(--fg-primary);">₱${fee.toLocaleString('en-PH',{minimumFractionDigits:0})}</span>`
        : '<span style="color:var(--fg-muted);">—</span>';

      return `<tr>
        <td style="font-weight:700;color:var(--fg-primary);">#${r.id}</td>
        <td>${esc(r.device_name||r.problem_desc||'—')}</td>
        <td style="max-width:160px;font-size:0.8rem;color:var(--fg-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(r.fault_description||r.problem_desc||'—')}</td>
        <td>${techName}</td>
        <td><span class="badge-status ${statusClass[r.status]||'badge-pending'}">${statusLabel[r.status]||r.status}</span></td>
        <td>${amountHtml}</td>
        <td style="color:var(--fg-muted);font-size:0.8rem;">${r.created_at ? new Date(r.created_at).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'}) : '—'}</td>
        <td style="white-space:nowrap;">${cancelBtn}${payBtn}${rcptLink}${reviewBtn}</td>
      </tr>`;
    }).join('');

    wrap.innerHTML = `<table class="data-table"><thead><tr><th>Booking #</th><th>Device</th><th>Issue</th><th>Technician</th><th>Status</th><th>Amount</th><th>Date</th><th>Action</th></tr></thead><tbody>${rows}</tbody></table>`;

    // Check which completed bookings already have reviews and disable those buttons
    filtered.filter(r => r.status === 'completed' && r.technician_id).forEach(r => {
      fetch('/api/reviews?action=can_review&booking_id=' + r.id, { credentials: 'include' })
        .then(res => res.json())
        .then(d => {
          const btn = document.getElementById('revBtn_' + r.id);
          if (!btn) return;
          if (d.already_reviewed) {
            btn.textContent = '✅ Reviewed';
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'default';
            btn.onmouseenter = btn.onmouseleave = null;
          }
        }).catch(() => {});
    });
  }

  function cancelBooking(id) {
    if (!confirm('Cancel booking #' + id + '?')) return;
    fetch('../../../api/repair/bookings', {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'cancel', booking_id: id })
    }).then(r => r.json()).then(d => {
      if (d.success) loadBookings();
      else alert(d.message || 'Failed to cancel.');
    }).catch(() => alert('Network error.'));
  }

  function loadBookings() {
    fetch('/api/repair/bookings?action=my_bookings', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          repairsData.length = 0;
          (d.bookings || []).forEach(b => repairsData.push(b));
          const activeFilter = document.querySelector('.filter-tab.active')?.dataset?.filter || 'all';
          renderRepairs(activeFilter);
        }
      }).catch(() => {});
  }


  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('/api/logout').finally(() => {
        window.location.href = '/login.html';
      });
    });
  }

  // ── Payment Modal ────────────────────────────────────────────
  let _payBookingId = null;

  function openPayModal(bookingId) {
    // Look up from repairsData — avoids any inline JSON/quote issues
    const r = repairsData.find(function(x){ return x.id == bookingId; }) || {};
    _payBookingId = bookingId;

    const fee      = parseFloat(r.repair_fee || r.total_amount || r.total_price || 0);
    const laborFee = parseFloat(r.labor_fee  || 0);
    const partsFee = parseFloat(r.parts_fee  || 0);
    const techMethod = r.payment_method || '';

    document.getElementById('pmDevice').textContent    = r.device_name || '—';
    document.getElementById('pmBookingId').textContent = '#' + bookingId;
    document.getElementById('pmTechName').textContent  = r.technician_name || 'Technician';

    // Cost breakdown rows
    const hasBreakdown = laborFee > 0 || partsFee > 0;
    const breakdownEl  = document.getElementById('pmBreakdown');
    const laborEl      = document.getElementById('pmLaborRow');
    const partsEl      = document.getElementById('pmPartsRow');
    if (hasBreakdown) {
      breakdownEl.style.display = 'block';
      laborEl.style.display = laborFee > 0 ? 'flex' : 'none';
      partsEl.style.display = partsFee > 0 ? 'flex' : 'none';
      document.getElementById('pmLaborAmt').textContent = String.fromCharCode(8369) + laborFee.toLocaleString('en-PH',{minimumFractionDigits:2});
      document.getElementById('pmPartsAmt').textContent = String.fromCharCode(8369) + partsFee.toLocaleString('en-PH',{minimumFractionDigits:2});
    } else {
      breakdownEl.style.display = 'none';
    }

    // Parts replaced list
    var partsList = [];
    try { partsList = JSON.parse(r.parts_replaced || '[]'); } catch(e) {}
    var prWrap = document.getElementById('pmPartsReplacedWrap');
    var prList = document.getElementById('pmPartsReplacedList');
    if (prWrap && prList) {
      if (partsList.length > 0) {
        prWrap.style.display = 'block';
        prList.innerHTML = partsList.map(function(p){
          var qtyBadge = p.qty > 1 ? '<span style="background:#8b5cf6;color:#fff;font-size:0.62rem;font-weight:800;padding:0.05rem 0.35rem;border-radius:10px;">x'+p.qty+'</span> ' : '';
          return '<span style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.2rem 0.65rem;border-radius:20px;background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.25);font-size:0.75rem;font-weight:600;color:#8b5cf6;">' + qtyBadge + esc(p.name) + '</span>';
        }).join('');
      } else {
        prWrap.style.display = 'none';
      }
    }

    document.getElementById('pmFeeDisplay').textContent = fee > 0
      ? String.fromCharCode(8369) + fee.toLocaleString('en-PH', {minimumFractionDigits: 2})
      : 'To be collected';

    var pmLabels = {cash:'Cash', bank_transfer:'Bank Transfer', gcash:'GCash', maya:'Maya', other:'Other'};
    var techExpect = document.getElementById('pmTechMethod');
    techExpect.textContent = techMethod ? 'Technician expects: ' + (pmLabels[techMethod] || techMethod) : '';
    techExpect.style.display = techMethod ? 'block' : 'none';

    // Online pay only when fee is set
    document.getElementById('pmOnlineBtn').disabled     = fee <= 0;
    document.getElementById('pmOnlineBtn').style.opacity = fee > 0 ? '1' : '0.4';

    // Reset form
    document.getElementById('pmMethod').value = '';
    document.getElementById('pmNote').value   = '';
    document.getElementById('pmNoteWrap').style.display = 'none';
    document.getElementById('pmAlert').style.display    = 'none';
    document.getElementById('pmSubmitBtn').disabled  = false;
    document.getElementById('pmSubmitBtn').innerHTML = '<i class="bi bi-check-circle-fill"></i> Confirm — I\'ve Paid Directly';
    ['pmCash','pmBank','pmGcash','pmMaya','pmOther'].forEach(function(id){
      var el = document.getElementById(id);
      if (el) { el.style.border = '2px solid var(--fg-border)'; el.style.background = 'var(--fg-card-bg)'; }
    });

    document.getElementById('paymentModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closePayModal() {
    document.getElementById('paymentModal').style.display = 'none';
    document.body.style.overflow = '';
  }

  function pickPayMethod(method) {
    document.getElementById('pmMethod').value = method;
    const map = {cash:'pmCash', bank_transfer:'pmBank', gcash:'pmGcash', maya:'pmMaya', other:'pmOther'};
    ['pmCash','pmBank','pmGcash','pmMaya','pmOther'].forEach(id => {
      const el = document.getElementById(id);
      if (el) { el.style.border = '2px solid var(--fg-border)'; el.style.background = 'var(--fg-card-bg)'; }
    });
    const sel = document.getElementById(map[method]);
    if (sel) { sel.style.border = '2px solid var(--fg-primary)'; sel.style.background = 'rgba(230,168,0,0.08)'; }
    const nw = document.getElementById('pmNoteWrap');
    if (nw) nw.style.display = method !== 'cash' ? 'block' : 'none';
    const ni = document.getElementById('pmNote');
    if (ni) {
      const ph = {bank_transfer:'Account name / number / bank name',gcash:'GCash number e.g. 0917-123-4567',maya:'Maya number or reference',other:'Payment reference or details'};
      ni.placeholder = ph[method] || 'Reference';
    }
  }

  // ── Pay via PayMongo (GCash, Card, Maya, Bank) ───────────────
  window.payOnline = function() {
    const btn = document.getElementById('pmOnlineBtn');
    const alEl = document.getElementById('pmAlert');
    alEl.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating payment link…';

    fetch('/api/repair/payment', {
      method: 'POST', credentials: 'include',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ booking_id: _payBookingId })
    }).then(r => r.json()).then(d => {
      if (!d.success) throw new Error(d.message || 'Could not create payment link.');
      // Redirect to PayMongo checkout
      window.location.href = d.checkout_url;
    }).catch(err => {
      alEl.style.display = 'flex';
      alEl.style.background = 'rgba(220,53,69,0.1)'; alEl.style.color = '#dc3545';
      alEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>&nbsp; ' + esc(err.message);
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-credit-card-2-front-fill"></i> Pay Online (GCash / Card / Maya)';
    });
  };

  // ── Pay with Cash (direct confirm) ──────────────────────────
  function submitPayment() {
    const method = document.getElementById('pmMethod').value;
    const note   = document.getElementById('pmNote').value.trim();
    const btn    = document.getElementById('pmSubmitBtn');
    const alEl   = document.getElementById('pmAlert');

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
        booking_id: _payBookingId,
        customer_payment_method: method,
        customer_payment_note:   note || null,
      })
    }).then(r => r.json()).then(d => {
      if (!d.success) throw new Error(d.message || 'Payment failed.');
      alEl.style.display = 'flex';
      alEl.style.background = 'rgba(40,167,69,0.1)'; alEl.style.color = '#28A745';
      alEl.innerHTML = '<i class="bi bi-check-circle-fill"></i>&nbsp; Payment confirmed! Thank you.';
      btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Done!';
      setTimeout(() => { closePayModal(); loadBookings(); }, 2000);
    }).catch(err => {
      alEl.style.display = 'flex';
      alEl.style.background = 'rgba(220,53,69,0.1)'; alEl.style.color = '#dc3545';
      alEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>&nbsp; ' + esc(err.message);
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Confirm Cash Payment';
    });
  }

  // ── Handle return from PayMongo ───────────────────────────────
  (function checkPaymentReturn(){
    const params = new URLSearchParams(location.search);
    if (params.get('payment') === 'success') {
      const alertDiv = document.createElement('div');
      alertDiv.style.cssText = 'position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:9999;background:#28A745;color:#fff;padding:0.9rem 1.5rem;border-radius:12px;font-weight:700;font-size:0.9rem;box-shadow:0 8px 24px rgba(0,0,0,0.25);display:flex;align-items:center;gap:0.5rem;';
      alertDiv.innerHTML = '<i class="bi bi-check-circle-fill"></i> Payment successful! Your repair is fully paid.';
      document.body.appendChild(alertDiv);
      setTimeout(()=>alertDiv.remove(), 5000);
      history.replaceState({}, '', location.pathname);
    } else if (params.get('payment') === 'cancel') {
      history.replaceState({}, '', location.pathname);
    }
  })();
  let _reviewTechId    = null;
  const _reviewFiles   = [null, null, null]; // up to 3 media files

  function openReviewModal(bookingId, techId, techName) {
    _reviewBookingId = bookingId;
    _reviewTechId    = techId;
    _reviewFiles[0] = _reviewFiles[1] = _reviewFiles[2] = null;

    document.getElementById('rvTechName').textContent = techName;
    document.getElementById('rvBookingId').textContent = '#' + bookingId;
    document.getElementById('rvComment').value = '';
    document.getElementById('rvAlert').style.display = 'none';
    document.getElementById('rvSubmitBtn').disabled = false;
    document.getElementById('rvSubmitBtn').innerHTML = '<i class="bi bi-send-fill"></i> Submit Review';
    setReviewRating(0);
    clearReviewPreviews();
    document.getElementById('reviewModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
    document.body.style.overflow = '';
  }

  function setReviewRating(val) {
    document.getElementById('rvRatingValue').value = val;
    document.querySelectorAll('.rv-star').forEach((s, i) => {
      s.style.color = i < val ? '#e6a800' : 'var(--fg-border)';
    });
    const labels = ['','Terrible','Poor','Okay','Good','Excellent'];
    document.getElementById('rvRatingLabel').textContent = val ? labels[val] + ' — ' + val + ' star' + (val > 1 ? 's' : '') : 'Tap a star to rate';
  }

  function handleReviewMedia(input, slot) {
    const file = input.files[0];
    if (!file) return;
    const isImage = file.type.startsWith('image/');
    const isVideo = file.type.startsWith('video/');
    if (!isImage && !isVideo) {
      alert('Only images and videos are allowed.');
      input.value = '';
      return;
    }
    _reviewFiles[slot] = file;
    const previewEl = document.getElementById('rvPreview_' + slot);
    const url = URL.createObjectURL(file);
    if (isImage) {
      previewEl.innerHTML = `<div style="position:relative;display:inline-block;">
        <img src="${url}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1.5px solid var(--fg-border);">
        <button onclick="clearReviewSlot(${slot})" style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;border-radius:50%;background:#dc3545;border:none;color:#fff;font-size:0.65rem;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">✕</button>
      </div>`;
    } else {
      previewEl.innerHTML = `<div style="position:relative;display:inline-block;">
        <video src="${url}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1.5px solid var(--fg-border);"></video>
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.3);border-radius:8px;font-size:1.1rem;">▶</div>
        <button onclick="clearReviewSlot(${slot})" style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;border-radius:50%;background:#dc3545;border:none;color:#fff;font-size:0.65rem;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">✕</button>
      </div>`;
    }
  }

  function clearReviewSlot(slot) {
    _reviewFiles[slot] = null;
    document.getElementById('rvPreview_' + slot).innerHTML = '';
    document.getElementById('rvMediaInput_' + slot).value = '';
  }

  function clearReviewPreviews() {
    [0,1,2].forEach(i => {
      document.getElementById('rvPreview_' + i).innerHTML = '';
      document.getElementById('rvMediaInput_' + i).value = '';
    });
  }

  function submitReview() {
    const rating  = parseInt(document.getElementById('rvRatingValue').value || '0');
    const comment = document.getElementById('rvComment').value.trim();
    const alEl    = document.getElementById('rvAlert');
    const btn     = document.getElementById('rvSubmitBtn');

    if (!rating) {
      alEl.style.display = 'flex';
      alEl.style.background = 'rgba(220,53,69,0.1)'; alEl.style.color = '#dc3545';
      alEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>&nbsp; Please select a star rating.';
      return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting…';
    alEl.style.display = 'none';

    const fd = new FormData();
    fd.append('booking_id', _reviewBookingId);
    fd.append('rating',     rating);
    if (comment) fd.append('comment', comment);
    [0,1,2].forEach(i => { if (_reviewFiles[i]) fd.append('media_' + (i+1), _reviewFiles[i]); });

    fetch('/api/reviews', { method: 'POST', credentials: 'include', body: fd })
      .then(r => r.json())
      .then(d => {
        if (!d.success) throw new Error(d.message || 'Submission failed.');
        alEl.style.display = 'flex';
        alEl.style.background = 'rgba(40,167,69,0.1)'; alEl.style.color = '#28A745';
        alEl.innerHTML = '<i class="bi bi-check-circle-fill"></i>&nbsp; Review submitted! Thank you for your feedback.';
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Submitted!';
        // Mark button as reviewed in table
        const revBtn = document.getElementById('revBtn_' + _reviewBookingId);
        if (revBtn) {
          revBtn.textContent = '✅ Reviewed';
          revBtn.disabled = true;
          revBtn.style.opacity = '0.5';
          revBtn.style.cursor = 'default';
          revBtn.onmouseenter = revBtn.onmouseleave = null;
        }
        setTimeout(closeReviewModal, 2200);
      })
      .catch(err => {
        alEl.style.display = 'flex';
        alEl.style.background = 'rgba(220,53,69,0.1)'; alEl.style.color = '#dc3545';
        alEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>&nbsp; ' + esc(err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Submit Review';
      });
  }

  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('/api/logout').finally(() => {
        window.location.href = '/login.html';
      });
    });
  }

  function loadUnreadMessageCount() {
    fetch('/api/messages?action=unread_count', { credentials: 'include' })
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

<!-- ── Review Modal ─────────────────────────────────────────── -->
<div id="reviewModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.72);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:520px;max-height:94vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">

    <!-- Header -->
    <div style="background:linear-gradient(135deg,#e6a800,#c98f00);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
      <div style="display:flex;align-items:center;gap:0.6rem;">
        <i class="bi bi-star-fill" style="color:#fff;font-size:1.05rem;"></i>
        <div>
          <div style="color:#fff;font-weight:800;font-size:1rem;">Rate Your Repair</div>
          <div style="color:rgba(255,255,255,0.8);font-size:0.72rem;margin-top:0.1rem;">
            Booking <span id="rvBookingId" style="font-weight:800;"></span> ·
            <span id="rvTechName"></span>
          </div>
        </div>
      </div>
      <button onclick="closeReviewModal()" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;"
        onmouseenter="this.style.background='rgba(255,255,255,0.35)'" onmouseleave="this.style.background='rgba(255,255,255,0.2)'">✕</button>
    </div>

    <!-- Body -->
    <div style="padding:1.35rem;overflow-y:auto;flex:1;">

      <!-- Star Rating -->
      <div style="text-align:center;margin-bottom:1.25rem;">
        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.75rem;">How was the repair?</div>
        <div style="display:flex;justify-content:center;gap:0.4rem;margin-bottom:0.5rem;">
          <i class="bi bi-star-fill rv-star" data-val="1" onclick="setReviewRating(1)" style="font-size:2rem;cursor:pointer;color:var(--fg-border);transition:color 0.15s;"></i>
          <i class="bi bi-star-fill rv-star" data-val="2" onclick="setReviewRating(2)" style="font-size:2rem;cursor:pointer;color:var(--fg-border);transition:color 0.15s;"></i>
          <i class="bi bi-star-fill rv-star" data-val="3" onclick="setReviewRating(3)" style="font-size:2rem;cursor:pointer;color:var(--fg-border);transition:color 0.15s;"></i>
          <i class="bi bi-star-fill rv-star" data-val="4" onclick="setReviewRating(4)" style="font-size:2rem;cursor:pointer;color:var(--fg-border);transition:color 0.15s;"></i>
          <i class="bi bi-star-fill rv-star" data-val="5" onclick="setReviewRating(5)" style="font-size:2rem;cursor:pointer;color:var(--fg-border);transition:color 0.15s;"></i>
        </div>
        <div id="rvRatingLabel" style="font-size:0.82rem;color:var(--fg-muted);font-weight:600;">Tap a star to rate</div>
        <input type="hidden" id="rvRatingValue" value="0">
      </div>

      <!-- Comment -->
      <div style="margin-bottom:1.1rem;">
        <label style="display:block;font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.4rem;">Your Review <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <textarea id="rvComment" rows="3" placeholder="Share your experience — how was the technician and the quality of the repair?"
          style="width:100%;padding:0.7rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;resize:vertical;outline:none;font-family:inherit;line-height:1.55;transition:border-color 0.2s;"
          onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'"></textarea>
      </div>

      <!-- Media Upload (up to 3) -->
      <div style="margin-bottom:1.1rem;">
        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.5rem;">
          📷 Attach Photo/Video of Repaired Phone <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional, max 3)</span>
        </div>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-start;">

          <!-- Slot 0 -->
          <div style="display:flex;flex-direction:column;align-items:center;gap:0.4rem;">
            <label for="rvMediaInput_0" style="width:80px;height:80px;border:2px dashed var(--fg-border);border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:border-color 0.2s;background:var(--fg-bg);font-size:0.65rem;color:var(--fg-muted);text-align:center;gap:0.2rem;"
              onmouseenter="this.style.borderColor='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)'">
              <i class="bi bi-plus-lg" style="font-size:1.2rem;color:var(--fg-muted);"></i>
              Photo/Video
            </label>
            <input type="file" id="rvMediaInput_0" accept="image/*,video/mp4,video/webm,video/quicktime" style="display:none;" onchange="handleReviewMedia(this,0)">
            <div id="rvPreview_0"></div>
          </div>

          <!-- Slot 1 -->
          <div style="display:flex;flex-direction:column;align-items:center;gap:0.4rem;">
            <label for="rvMediaInput_1" style="width:80px;height:80px;border:2px dashed var(--fg-border);border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:border-color 0.2s;background:var(--fg-bg);font-size:0.65rem;color:var(--fg-muted);text-align:center;gap:0.2rem;"
              onmouseenter="this.style.borderColor='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)'">
              <i class="bi bi-plus-lg" style="font-size:1.2rem;color:var(--fg-muted);"></i>
              Photo/Video
            </label>
            <input type="file" id="rvMediaInput_1" accept="image/*,video/mp4,video/webm,video/quicktime" style="display:none;" onchange="handleReviewMedia(this,1)">
            <div id="rvPreview_1"></div>
          </div>

          <!-- Slot 2 -->
          <div style="display:flex;flex-direction:column;align-items:center;gap:0.4rem;">
            <label for="rvMediaInput_2" style="width:80px;height:80px;border:2px dashed var(--fg-border);border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:border-color 0.2s;background:var(--fg-bg);font-size:0.65rem;color:var(--fg-muted);text-align:center;gap:0.2rem;"
              onmouseenter="this.style.borderColor='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)'">
              <i class="bi bi-plus-lg" style="font-size:1.2rem;color:var(--fg-muted);"></i>
              Photo/Video
            </label>
            <input type="file" id="rvMediaInput_2" accept="image/*,video/mp4,video/webm,video/quicktime" style="display:none;" onchange="handleReviewMedia(this,2)">
            <div id="rvPreview_2"></div>
          </div>

        </div>
        <div style="font-size:0.72rem;color:var(--fg-muted);margin-top:0.4rem;">Images up to 10 MB · Videos up to 50 MB (MP4, WebM, MOV)</div>
      </div>

      <div id="rvAlert" style="display:none;padding:0.6rem 0.9rem;border-radius:8px;font-size:0.83rem;font-weight:600;align-items:center;gap:0.4rem;margin-bottom:0.75rem;"></div>

      <button id="rvSubmitBtn" onclick="submitReview()"
        style="width:100%;padding:0.85rem;border-radius:12px;background:var(--fg-primary);color:#000;border:none;font-weight:800;font-size:0.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:opacity 0.2s;"
        onmouseenter="this.style.opacity='0.88'" onmouseleave="this.style.opacity='1'">
        <i class="bi bi-send-fill"></i> Submit Review
      </button>
    </div>
  </div>
</div>
<script>
  document.getElementById('reviewModal').addEventListener('click', function(e) {
    if (e.target === this) closeReviewModal();
  });
</script>


<!-- ══ Payment Modal ════════════════════════════════════════════ -->
<div id="paymentModal" style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
  <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:510px;max-height:96vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">

    <!-- Header -->
    <div style="background:linear-gradient(135deg,#28A745,#1a8a35);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
      <div>
        <div style="color:#fff;font-weight:800;font-size:1rem;">💳 Repair Payment</div>
        <div style="color:rgba(255,255,255,0.8);font-size:0.75rem;margin-top:0.15rem;">
          Repair <strong id="pmBookingId">#—</strong> · <span id="pmDevice"></span> · <span id="pmTechName"></span>
        </div>
      </div>
      <button onclick="closePayModal()" style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;"
        onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">✕</button>
    </div>

    <!-- Body -->
    <div style="padding:1.35rem;overflow-y:auto;flex:1;">

      <!-- Cost Breakdown + Total -->
      <div style="background:rgba(40,167,69,0.07);border:1.5px solid rgba(40,167,69,0.2);border-radius:12px;padding:1rem 1.1rem;margin-bottom:1.1rem;">
        <!-- breakdown rows -->
        <div id="pmBreakdown" style="display:none;margin-bottom:0.75rem;">
          <div id="pmLaborRow" style="display:flex;justify-content:space-between;font-size:0.83rem;padding:0.25rem 0;">
            <span style="color:var(--fg-muted);">🔧 Labor / Service Fee</span>
            <span id="pmLaborAmt" style="font-weight:700;color:var(--fg-text);"></span>
          </div>
          <div id="pmPartsRow" style="display:flex;justify-content:space-between;font-size:0.83rem;padding:0.25rem 0;">
            <span style="color:var(--fg-muted);">🔩 Parts / Replacement</span>
            <span id="pmPartsAmt" style="font-weight:700;color:var(--fg-text);"></span>
          </div>
          <div style="border-top:1px dashed var(--fg-border);margin:0.5rem 0 0.4rem;"></div>
        </div>
        <!-- Parts replaced tags -->
        <div id="pmPartsReplacedWrap" style="display:none;margin-bottom:0.75rem;">
          <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.4rem;">🔩 Parts / Products Replaced</div>
          <div id="pmPartsReplacedList" style="display:flex;flex-wrap:wrap;gap:0.35rem;"></div>
        </div>
        <!-- Total row -->
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <div>
            <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.2rem;">Total Amount Due</div>
            <div id="pmFeeDisplay" style="font-size:1.75rem;font-weight:800;color:#28A745;line-height:1;"></div>
            <div id="pmTechMethod" style="font-size:0.74rem;color:var(--fg-muted);margin-top:0.3rem;display:none;"></div>
          </div>
          <i class="bi bi-receipt" style="font-size:2.5rem;color:rgba(40,167,69,0.22);flex-shrink:0;"></i>
        </div>
      </div>

      <!-- ─── OPTION 1: Pay Online via PayMongo ─── -->
      <div style="border:1.5px solid rgba(59,130,246,0.3);border-radius:12px;padding:1rem 1.1rem;margin-bottom:0.9rem;background:rgba(59,130,246,0.04);">
        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#3b82f6;margin-bottom:0.45rem;">🌐 Pay Online — Secure Checkout</div>
        <div style="font-size:0.81rem;color:var(--fg-muted);line-height:1.5;margin-bottom:0.75rem;">
          Redirects to PayMongo. Supports <strong style="color:var(--fg-text);">GCash</strong>, <strong style="color:var(--fg-text);">Maya</strong>, <strong style="color:var(--fg-text);">Credit/Debit Card</strong>, and <strong style="color:var(--fg-text);">Bank Transfer</strong>.
        </div>
        <button id="pmOnlineBtn" onclick="payOnline()"
          style="width:100%;padding:0.75rem;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff;border:none;font-weight:800;font-size:0.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:opacity 0.2s;"
          onmouseenter="this.style.opacity='0.86'" onmouseleave="this.style.opacity='1'">
          <i class="bi bi-credit-card-2-front-fill"></i> Pay via GCash / Card / Maya / Bank
        </button>
      </div>

      <!-- Divider -->
      <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.9rem;">
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <span style="font-size:0.72rem;font-weight:700;color:var(--fg-muted);white-space:nowrap;">OR PAID DIRECTLY</span>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
      </div>

      <!-- ─── OPTION 2: Cash / Direct Payment ─── -->
      <div style="margin-bottom:0.85rem;">
        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.55rem;">💵 I Already Paid the Technician Directly</div>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0.45rem;margin-bottom:0.55rem;">
          <div id="pmCash" onclick="pickPayMethod('cash')"
            style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.65rem 0.4rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('pmMethod').value!=='cash')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('pmMethod').value!=='cash')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.3rem;">💵</span><span style="font-size:0.65rem;font-weight:700;color:var(--fg-text);">Cash</span>
          </div>
          <div id="pmBank" onclick="pickPayMethod('bank_transfer')"
            style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.65rem 0.4rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('pmMethod').value!=='bank_transfer')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('pmMethod').value!=='bank_transfer')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.3rem;">🏦</span><span style="font-size:0.65rem;font-weight:700;color:var(--fg-text);">Bank</span>
          </div>
          <div id="pmGcash" onclick="pickPayMethod('gcash')"
            style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.65rem 0.4rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('pmMethod').value!=='gcash')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('pmMethod').value!=='gcash')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.3rem;">📱</span><span style="font-size:0.65rem;font-weight:700;color:var(--fg-text);">GCash</span>
          </div>
          <div id="pmMaya" onclick="pickPayMethod('maya')"
            style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.65rem 0.4rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('pmMethod').value!=='maya')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('pmMethod').value!=='maya')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.3rem;">💳</span><span style="font-size:0.65rem;font-weight:700;color:var(--fg-text);">Maya</span>
          </div>
          <div id="pmOther" onclick="pickPayMethod('other')"
            style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.65rem 0.4rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;background:var(--fg-card-bg);text-align:center;user-select:none;"
            onmouseenter="if(document.getElementById('pmMethod').value!=='other')this.style.borderColor='var(--fg-primary)'"
            onmouseleave="if(document.getElementById('pmMethod').value!=='other')this.style.borderColor='var(--fg-border)'">
            <span style="font-size:1.3rem;">💰</span><span style="font-size:0.65rem;font-weight:700;color:var(--fg-text);">Other</span>
          </div>
        </div>
        <input type="hidden" id="pmMethod" value="">
      </div>

      <!-- Reference (non-cash) -->
      <div id="pmNoteWrap" style="display:none;margin-bottom:0.85rem;">
        <label style="display:block;font-size:0.72rem;font-weight:700;color:var(--fg-muted);margin-bottom:0.3rem;">Account / Reference Number <span style="font-weight:400;">(optional)</span></label>
        <input type="text" id="pmNote" placeholder="e.g. 0917-123-4567 / Ref: 123456"
          style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;box-sizing:border-box;"
          onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'">
      </div>

      <div id="pmAlert" style="display:none;padding:0.65rem 0.9rem;border-radius:8px;font-size:0.83rem;font-weight:600;align-items:center;gap:0.4rem;margin-bottom:0.75rem;"></div>

      <button id="pmSubmitBtn" onclick="submitPayment()"
        style="width:100%;padding:0.85rem;border-radius:12px;background:linear-gradient(135deg,var(--fg-primary),#c98f00);color:#000;border:none;font-weight:800;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:opacity 0.2s;"
        onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
        <i class="bi bi-check-circle-fill"></i> Confirm — I've Paid Directly
      </button>
    </div>
  </div>
</div>

<script>
  document.getElementById('paymentModal').addEventListener('click', function(e){ if(e.target===this) closePayModal(); });
</script>

  <!-- ── Mobile Bottom Nav ── -->
  <nav id="repairsBottomNav" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;background:var(--fg-card-bg);border-top:1px solid var(--fg-border);padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));box-shadow:0 -4px 20px rgba(0,0,0,0.15);">
    <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-house-fill" style="font-size:1.25rem;"></i>Home</a></li>
      <li><a href="/index.php#shop" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-shop" style="font-size:1.25rem;"></i>Shop</a></li>
      <li><a href="/index.php#technicians" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-primary);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-workspace" style="font-size:1.25rem;"></i>Technicians</a></li>
      <li><a href="notifications.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-bell-fill" style="font-size:1.25rem;"></i>Inbox</a></li>
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-fill" style="font-size:1.25rem;"></i>Me</a></li>
    </ul>
  </nav>
  <script>(function(){var nb=document.getElementById('repairsBottomNav');function c(){nb.style.display=window.innerWidth<=991?'block':'none';}c();window.addEventListener('resize',c);})();</script>

</body>
</html>




