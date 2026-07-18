/**
 * Fix&Go � Dashboard Logic
 * Roles: owner | supervisor | supplier | sales_person | customer
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Resolve backend path � works on both localhost (/fixandgo/dashboard.php)
  // and live server (/dashboard.php where backend is at /fixandgo/backend/)
  const _B = window.FG_BACKEND || (function() {
    var parts = window.location.pathname.split('/').filter(Boolean);
    return parts.length <= 1 ? 'fixandgo/backend/' : 'backend/';
  })();

  fetch('api/session/user')
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.loggedIn && data.user) {
        FGAuth.UserStore.save(data.user);
        renderDashboard(data.user);
      } else {
        const user = FGAuth.UserStore.get();
        user ? renderDashboard(user) : (window.location.href = 'login.html');
      }
    })
    .catch(function () {
      const user = FGAuth.UserStore.get();
      user ? renderDashboard(user) : (window.location.href = 'login.html');
    });

  /* -- Render ------------------------------------------------------- */
  function renderDashboard(user) {
    // Check if user just logged in - removed auto-redirect to marketplace
    // Users can manually switch to marketplace view using the toggle button
    
    // Hide logo link and back button for supervisors
    if (user.role === 'supervisor') {
      const logoLink = document.getElementById('navLogoLink');
      const backBtn = document.getElementById('backToHomeBtn');
      
      if (logoLink) {
        // Replace link with non-clickable div
        const logoImg = document.getElementById('navLogo');
        const logoParent = logoLink.parentNode;
        const newDiv = document.createElement('div');
        newDiv.style.cssText = 'display:flex;align-items:center;';
        newDiv.appendChild(logoImg.cloneNode(true));
        logoParent.replaceChild(newDiv, logoLink);
      }
      
      if (backBtn) {
        backBtn.style.display = 'none';
      }
    }

    // Show "Browse Shop" button for owner, supplier and sales_person
    const browseShopBtn = document.getElementById('backToLandingBtn');
    if (browseShopBtn && (user.role === 'owner' || user.role === 'supplier' || user.role === 'sales_person')) {
      browseShopBtn.href = 'index.php?browse=1';
      browseShopBtn.style.display = 'inline-flex';
      browseShopBtn.style.alignItems = 'center';
      browseShopBtn.style.gap = '0.35rem';
      console.log('Browse Shop button shown for role:', user.role);
      
      // Add click handler for debugging
      browseShopBtn.addEventListener('click', function(e) {
        console.log('Browse Shop button clicked!');
        console.log('Navigating to:', browseShopBtn.href);
        // Let the default link behavior happen
      });
    }

    // Show toggle view button for roles that can access marketplace
    const urlParams = new URLSearchParams(window.location.search);
    const toggleViewBtn = document.getElementById('toggleViewBtn');
    const toggleViewText = document.getElementById('toggleViewText');
    if (toggleViewBtn && (user.role === 'owner' || user.role === 'supplier' || user.role === 'sales_person' || user.role === 'customer')) {
      toggleViewBtn.style.display = 'inline-flex';
      toggleViewBtn.style.alignItems = 'center';
      toggleViewBtn.style.gap = '0.35rem';
      
      // Update button text based on current view
      const currentView = urlParams.get('view');
      if (currentView === 'marketplace') {
        toggleViewText.textContent = 'Dashboard';
        toggleViewBtn.querySelector('i').className = 'bi bi-speedometer2';
      } else {
        toggleViewText.textContent = 'Marketplace';
        toggleViewBtn.querySelector('i').className = 'bi bi-shop';
      }
      
      // Add click handler to toggle view
      toggleViewBtn.addEventListener('click', function() {
        if (currentView === 'marketplace') {
          window.location.href = 'dashboard.php';
        } else {
          window.location.href = 'dashboard.php?view=marketplace';
        }
      });
    }

    // Navbar name
    const navName = document.getElementById('navUserName');
    if (navName) navName.textContent = user.firstName + ' ' + user.lastName;

    // Welcome banner avatar
    const welcomeAvatar = document.getElementById('welcomeAvatar');
    if (welcomeAvatar) {
      if (user.avatar_url) {
        welcomeAvatar.innerHTML = `<img src="${user.avatar_url}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.outerHTML='<i class=\\'bi bi-person-circle\\'></i>'">`;
        welcomeAvatar.style.padding = '0';
      } else {
        // Show initials
        const initials = ((user.firstName || '')[0] || '') + ((user.lastName || '')[0] || '');
        if (initials) {
          welcomeAvatar.innerHTML = `<span style="font-size:1.1rem;font-weight:800;color:var(--fg-primary);">${initials.toUpperCase()}</span>`;
        }
      }
    }

    // Role badge
    const navBadge = document.getElementById('navRoleBadge');
    if (navBadge) {
      navBadge.className = 'role-badge ' + user.role;
      const labels = {
        owner:       '?? Owner',
        supervisor:  '??? Supervisor',
        supplier:    '?? Supplier',
        sales_person:'?? Sales Person',
        customer:    '?? Customer',
      };
      navBadge.textContent = labels[user.role] || user.role;
    }

    // Show cart icon for owner
    if (user.role === 'owner') {
      const cartIcon = document.getElementById('cartIcon');
      if (cartIcon) {
        cartIcon.style.display = 'block';
      }
    }

    // Welcome banner
    const urlParams2 = new URLSearchParams(window.location.search);
    const wTitle = document.getElementById('welcomeTitle');
    if (wTitle) {
      const currentView = urlParams2.get('view');
      if (currentView === 'marketplace') {
        wTitle.textContent = 'Welcome to the Marketplace, ' + user.firstName + '!';
      } else {
        wTitle.textContent = 'Welcome back, ' + user.firstName + '!';
      }
    }

    const wSub = document.getElementById('welcomeSubtitle');
    if (wSub) {
      const currentView = urlParams2.get('view');
      if (currentView === 'marketplace') {
        wSub.textContent = 'Browse products from suppliers and connect with expert technicians.';
      } else {
        const subs = {
          owner:       'Manage your repair shop, staff, inventory, and performance.',
          supervisor:  'Monitor operations, staff activity, and service quality.',
          supplier:    'Manage your product listings, orders, and deliveries.',
          sales_person:'Track your leads, sales targets, and customer deals.',
          customer:    'Book repairs, track your devices, and view your history.',
        };
        wSub.textContent = subs[user.role] || 'Manage your Fix&Go account.';
      }
    }

    // Role content
    const roleContent = document.getElementById('roleContent');
    if (roleContent) roleContent.innerHTML = getRoleContent(user.role, user);

    // Logout
    const logoutBtn = document.getElementById('logoutBtn');
    console.log('Logout button element:', logoutBtn);
    if (logoutBtn) {
      logoutBtn.addEventListener('click', function (e) {
        e.preventDefault();
        FGAuth.showLogoutModal(function() {
          fetch('api/logout', { method: 'POST' })
            .catch(function(error) { console.log('Logout error:', error); })
            .finally(function () {
              FGAuth.UserStore.clear();
              window.location.href = 'index.php?logout=true';
            });
        });
      });
    }
  }

  /* -- Route by role ------------------------------------------------ */
  function getRoleContent(role, user) {
    // Check if we should show marketplace view
    const urlParams = new URLSearchParams(window.location.search);
    const showMarketplace = urlParams.get('view') === 'marketplace';
    
    if (showMarketplace && (role === 'owner' || role === 'supplier' || role === 'sales_person' || role === 'customer')) {
      return marketplaceDashboard(user);
    }
    
    switch (role) {
      case 'owner':        return ownerDashboard(user);
      case 'supervisor':      return supervisorDashboard(user);
      case 'supplier':       return supplierDashboard(user);
      case 'sales_person':   return salesDashboard(user);
      case 'phone_technician': window.location.href = 'views/user/phone_technician/dashboard.php'; return '';
      default:               return customerDashboard(user);
    }
  }

  /* ================================================================
     OWNER DASHBOARD
  ================================================================ */
  function ownerDashboard(user) {
    // Load pending submissions async
    setTimeout(() => loadOwnerSubmissions(), 100);

    return `
      ${sectionTitle('??', 'Shop Overview', 'var(--fg-primary)')}
      ${statsGrid(`
        ${stat('??', 'Total Bookings',  '128', 'rgba(230,168,0,0.12)',   'var(--fg-primary)')}
        ${stat('?', 'Completed',       '94',  'rgba(40,167,69,0.12)',   '#28A745')}
        ${stat('?', 'Pending',         '21',  'rgba(255,193,7,0.15)',   '#856404')}
        ${stat('??', 'Pending Products','�',   'rgba(59,130,246,0.12)',  '#3b82f6', 'statPendingProducts')}
      `)}
      ${sectionTitle('??', 'Revenue Summary', '#28A745')}
      ${statsGrid(`
        ${stat('??', 'Today',     '?320',  'rgba(40,167,69,0.12)',  '#28A745')}
        ${stat('??', 'This Week', '?1,840','rgba(40,167,69,0.12)',  '#28A745')}
        ${stat('???', 'This Month','?7,200','rgba(40,167,69,0.12)',  '#28A745')}
        ${stat('??', 'Growth',    '+12%',  'rgba(99,102,241,0.12)', '#6366f1')}
      `)}
      ${sectionTitle('?', 'Quick Actions', '#6366f1')}
      ${actionsGrid(`
        ${action('?', 'Add Staff',       'Invite technicians, supervisors, or sales staff.', 'var(--fg-primary)', 'views/user/owner/staff.php')}
        ${action('??', 'View Reports',    'Revenue, performance, and booking analytics.',     '#28A745',           'views/user/owner/sales-report.php')}
        ${action('??', 'Supervisor Reports', 'View monthly reports from your supervisor.',    '#3b82f6',           'views/user/owner/supervisor-reports.php')}
        ${action('??', 'Manage Inventory','Track parts, accessories, and stock levels.',      '#6366f1',           'views/user/owner/products.php')}
        ${action('??', 'Bookings',        'View and manage all customer repair bookings.',    '#2a9d8f',           'views/user/owner/orders.php')}
        ${action('???', 'My Cart',        'View your shopping cart and checkout.',            'var(--fg-primary)', 'views/user/owner/cart.php')}
        ${action('??', 'Messages',        'Chat with customers and technicians.',             '#3b82f6',           'views/user/owner/messages.php')}
        ${action('??', 'Test Payment',    'Run a dummy PayMongo checkout to verify your API keys.', '#dc3545', 'paymongo-test.php')}
      `)}

      <!-- -- Pending Supplier Submissions -- -->
      <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;margin-top:0.5rem;">
        <span style="font-size:1.2rem;line-height:1;">??</span>
        <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">Supplier Product Submissions</h5>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <span id="submissionCountBadge" style="display:none;background:rgba(59,130,246,0.12);color:#3b82f6;font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:20px;"></span>
      </div>
      <div id="ownerSubmissionsGrid" style="margin-bottom:2rem;">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading submissions�
        </div>
      </div>

      <!-- -- Received Products -- -->
      <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;margin-top:0.5rem;">
        <span style="font-size:1.2rem;line-height:1;">?</span>
        <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">Accepted Products (In Shop)</h5>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <a href="views/user/owner/cart.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:0.3rem;margin-right:0.5rem;"><i class="bi bi-cart-fill"></i> View Cart</a>
        <a href="views/user/owner/products.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All ?</a>
      </div>
      <div id="ownerReceivedGrid" style="margin-bottom:2rem;">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading�
        </div>
      </div>

      ${sectionTitle('??', 'Recent Bookings', '#2a9d8f')}
      ${bookingTable([
        { id:'#1042', customer:'Maria Santos',   device:'iPhone 14',     service:'Screen Repair',     status:'In Progress', amount:'?89' },
        { id:'#1041', customer:'Juan Dela Cruz', device:'Samsung S23',   service:'Battery Replace',   status:'Completed',   amount:'?45' },
        { id:'#1040', customer:'Ana Reyes',      device:'Xiaomi 12',     service:'Water Damage',      status:'Pending',     amount:'?120' },
        { id:'#1039', customer:'Pedro Lim',      device:'iPhone 13 Pro', service:'Charging Port Fix', status:'Completed',   amount:'?60' },
      ])}

      <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes slideInRight { from { transform: translateX(120%); opacity:0; } to { transform: translateX(0); opacity:1; } }
        @keyframes slideOutRight { from { transform: translateX(0); opacity:1; } to { transform: translateX(120%); opacity:0; } }
        @keyframes modalIn { from { transform: scale(0.92); opacity:0; } to { transform: scale(1); opacity:1; } }
        .sub-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
          gap: 1rem;
        }
        .sub-card {
          background: var(--fg-card-bg);
          border: 1px solid var(--fg-border);
          border-radius: 14px;
          overflow: hidden;
          transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
          display: flex; flex-direction: column;
        }
        .sub-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); border-color: var(--fg-primary); }
        .sub-card-img { width:100%; aspect-ratio:1/1; object-fit:cover; background:var(--fg-bg); display:block; }
        .sub-card-img-ph { width:100%; aspect-ratio:1/1; background:var(--fg-bg); display:flex; align-items:center; justify-content:center; font-size:2rem; color:var(--fg-muted); }
        .sub-card-body { padding:0.75rem; flex:1; display:flex; flex-direction:column; }
        .sub-supplier-tag {
          display:inline-flex; align-items:center; gap:0.3rem;
          font-size:0.68rem; font-weight:700;
          color:#3b82f6; background:rgba(59,130,246,0.1);
          border:1px solid rgba(59,130,246,0.2);
          padding:0.15rem 0.55rem; border-radius:50px;
          margin-bottom:0.4rem;
        }
        .sub-cat { font-size:0.68rem; font-weight:700; color:var(--fg-primary); background:rgba(230,168,0,0.1); border:1px solid rgba(230,168,0,0.2); padding:0.1rem 0.5rem; border-radius:50px; display:inline-block; margin-bottom:0.35rem; }
        .sub-name { font-size:0.8rem; font-weight:700; color:var(--fg-text); line-height:1.3; margin-bottom:0.2rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; flex:1; }
        .sub-brand { font-size:0.72rem; color:var(--fg-muted); margin-bottom:0.5rem; }
        .sub-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.25rem; margin-bottom:0.6rem; }
        .sub-price { font-size:0.95rem; font-weight:800; color:var(--fg-primary); }
        .sub-qty { font-size:0.68rem; color:var(--fg-muted); background:var(--fg-bg); border:1px solid var(--fg-border); padding:0.1rem 0.45rem; border-radius:6px; }
        .sub-actions { display:flex; gap:0.4rem; margin-top:auto; }
        .btn-accept {
          flex:1; padding:0.4rem 0; border-radius:8px;
          background:rgba(40,167,69,0.12); color:#28A745;
          border:1.5px solid rgba(40,167,69,0.3);
          font-size:0.75rem; font-weight:700; cursor:pointer;
          transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:0.3rem;
        }
        .btn-accept:hover { background:#28A745; color:#fff; border-color:#28A745; }
        .btn-reject {
          flex:1; padding:0.4rem 0; border-radius:8px;
          background:rgba(220,53,69,0.1); color:#dc3545;
          border:1.5px solid rgba(220,53,69,0.25);
          font-size:0.75rem; font-weight:700; cursor:pointer;
          transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:0.3rem;
        }
        .btn-reject:hover { background:#dc3545; color:#fff; border-color:#dc3545; }
        .sub-empty { text-align:center; padding:2.5rem 1rem; color:var(--fg-muted); font-size:0.88rem; background:var(--fg-card-bg); border:1px solid var(--fg-border); border-radius:14px; }
      </style>`;
  }

  /* -- Load owner submissions ----------------------------------- */
  function loadOwnerSubmissions() {
    // Load pending submissions
    fetch(_B + 'owner_products.php?action=submissions', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (!data.success) throw new Error(data.message);

        // Update stat counter
        const statEl = document.getElementById('statPendingProducts');
        if (statEl) statEl.textContent = data.pending || 0;

        // Update badge
        const badge = document.getElementById('submissionCountBadge');
        if (badge && data.pending > 0) {
          badge.textContent = data.pending + ' pending';
          badge.style.display = 'inline-block';
        }

        renderOwnerSubmissions(data.products || []);
      })
      .catch(() => {
        const el = document.getElementById('ownerSubmissionsGrid');
        if (el) el.innerHTML = '<div class="sub-empty">Could not load submissions.</div>';
      });

    // Load received products
    fetch(_B + 'owner_products.php?action=received', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (!data.success) throw new Error(data.message);
        renderOwnerReceived(data.products || []);
      })
      .catch(() => {
        const el = document.getElementById('ownerReceivedGrid');
        if (el) el.innerHTML = '<div class="sub-empty">Could not load received products.</div>';
      });
  }

  function renderOwnerSubmissions(products) {
    const el = document.getElementById('ownerSubmissionsGrid');
    if (!el) return;

    if (!products.length) {
      el.innerHTML = '<div class="sub-empty"><i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>No pending product submissions.</div>';
      return;
    }

    el.innerHTML = `<div class="sub-grid">${products.map(p => {
      const img = p.image_path
        ? `<img class="sub-card-img" src="${escHtml(p.image_path)}" alt="" loading="lazy" onerror="this.outerHTML='<div class=\\'sub-card-img-ph\\'><i class=\\'bi bi-image\\'></i></div>'">`
        : `<div class="sub-card-img-ph"><i class="bi bi-image"></i></div>`;
      return `
        <div class="sub-card" id="sub-card-${p.id}">
          ${img}
          <div class="sub-card-body">
            <span class="sub-supplier-tag"><i class="bi bi-person-fill"></i>${escHtml(p.supplier_name)}</span>
            <span class="sub-cat">${escHtml(p.category)}</span>
            <div class="sub-name">${escHtml(p.item_description)}</div>
            ${p.brand ? `<div class="sub-brand">${escHtml(p.brand)}</div>` : ''}
            <div class="sub-footer">
              <span class="sub-price">?${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
              <span class="sub-qty">${p.qty} pcs</span>
            </div>
            <div class="sub-actions">
              <button class="btn-accept" onclick="ownerAccept(${p.id})"><i class="bi bi-check-lg"></i> Accept</button>
              <button class="btn-reject" onclick="ownerReject(${p.id})"><i class="bi bi-x-lg"></i> Reject</button>
            </div>
          </div>
        </div>`;
    }).join('')}</div>`;
  }

  function renderOwnerReceived(products) {
    const el = document.getElementById('ownerReceivedGrid');
    if (!el) return;

    // Store products globally for quantity modal access
    window.allProducts = products;

    if (!products.length) {
      el.innerHTML = '<div class="sub-empty">No accepted products yet. Accept supplier submissions above.</div>';
      return;
    }

    // Check for payment success/cancel from URL params
    const urlParams = new URLSearchParams(window.location.search);
    const payStatus = urlParams.get('payment');
    const payRef    = urlParams.get('ref');
    if (payStatus === 'success' && payRef) {
      const banner = document.createElement('div');
      banner.style.cssText = 'background:rgba(40,167,69,0.12);border:1px solid rgba(40,167,69,0.3);color:#28A745;padding:0.75rem 1.25rem;border-radius:10px;font-weight:600;font-size:0.88rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;';
      banner.innerHTML = '<i class="bi bi-check-circle-fill"></i> Payment successful! Reference: <strong>' + escHtml(payRef) + '</strong>';
      el.parentNode.insertBefore(banner, el);
      // Clean URL
      window.history.replaceState({}, '', window.location.pathname);
    } else if (payStatus === 'cancelled') {
      const banner = document.createElement('div');
      banner.style.cssText = 'background:rgba(220,53,69,0.1);border:1px solid rgba(220,53,69,0.25);color:#dc3545;padding:0.75rem 1.25rem;border-radius:10px;font-weight:600;font-size:0.88rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;';
      banner.innerHTML = '<i class="bi bi-x-circle-fill"></i> Payment was cancelled.';
      el.parentNode.insertBefore(banner, el);
      window.history.replaceState({}, '', window.location.pathname);
    }

    // Group products by supplier for bulk buy
    const bySupplier = {};
    products.forEach(p => {
      const key = p.supplier_name || 'Unknown';
      if (!bySupplier[key]) bySupplier[key] = [];
      bySupplier[key].push(p);
    });

    let html = '';

    Object.entries(bySupplier).forEach(([supplierName, items]) => {
      const totalAmt = items.reduce((s, p) => s + parseFloat(p.srp) * parseInt(p.qty), 0);
      const ids      = items.map(p => p.id);

      html += `
        <div style="margin-bottom:1.5rem;">
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.75rem;">
            <span style="font-size:0.82rem;font-weight:700;color:var(--fg-muted);">
              <i class="bi bi-person-fill" style="color:#3b82f6;margin-right:0.3rem;"></i>${escHtml(supplierName)}
            </span>
            ${(() => {
              const hasStock = items.some(p => p.qty > 0);
              const availableIds = items.filter(p => p.qty > 0).map(p => p.id);
              const availableTotal = items.filter(p => p.qty > 0).reduce((sum, p) => sum + (parseFloat(p.srp) * p.qty), 0);
              
              if (!hasStock) {
                return `<button disabled
                  style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 1rem;border-radius:8px;background:#e0e0e0;color:#999;border:none;font-size:0.8rem;font-weight:700;cursor:not-allowed;transition:all 0.2s;opacity:0.6;">
                  <i class="bi bi-ban"></i> All Out of Stock
                </button>`;
              }
              
              return `<button class="btn-buy-all"
                onclick="addAllToCart(${JSON.stringify(availableIds).replace(/"/g,'&quot;')}, '${escHtml(supplierName)}')"
                style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 1rem;border-radius:8px;background:var(--fg-primary);color:#fff;border:none;font-size:0.8rem;font-weight:700;cursor:pointer;transition:all 0.2s;">
                <i class="bi bi-cart-plus"></i> Add All to Cart
                <span style="background:rgba(255,255,255,0.25);padding:0.1rem 0.45rem;border-radius:6px;font-size:0.72rem;">
                  ${availableIds.length} items
                </span>
              </button>`;
            })()}
          </div>
          <div class="sub-grid">
            ${items.map(p => {
              const img = p.image_path
                ? `<img class="sub-card-img" src="${escHtml(p.image_path)}" alt="" loading="lazy" onerror="this.outerHTML='<div class=\\'sub-card-img-ph\\'><i class=\\'bi bi-image\\'></i></div>'">`
                : `<div class="sub-card-img-ph"><i class="bi bi-image"></i></div>`;
              
              const isOutOfStock = p.qty <= 0;
              
              return `
                <div class="sub-card">
                  ${img}
                  <div class="sub-card-body">
                    <span class="sub-cat">${escHtml(p.category)}</span>
                    <div class="sub-name">${escHtml(p.item_description)}</div>
                    ${p.brand ? `<div class="sub-brand">${escHtml(p.brand)}</div>` : ''}
                    <div class="sub-footer">
                      <span class="sub-price">?${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
                      <span class="sub-qty" style="${isOutOfStock ? 'color:#dc3545;font-weight:700;' : ''}">${p.qty > 0 ? p.qty + ' in stock' : 'Out of stock'}</span>
                    </div>
                    <button ${isOutOfStock ? 'disabled' : `onclick="addToCart(${p.id})"`}
                      style="margin-top:0.6rem;width:100%;padding:0.4rem;border-radius:8px;background:${isOutOfStock ? '#e0e0e0' : 'rgba(230,168,0,0.1)'};color:${isOutOfStock ? '#999' : 'var(--fg-primary)'};border:1.5px solid ${isOutOfStock ? '#e0e0e0' : 'rgba(230,168,0,0.3)'};font-size:0.78rem;font-weight:700;cursor:${isOutOfStock ? 'not-allowed' : 'pointer'};transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:0.35rem;opacity:${isOutOfStock ? '0.6' : '1'};"
                      ${isOutOfStock ? '' : `onmouseenter="this.style.background='var(--fg-primary)';this.style.color='#fff'" onmouseleave="this.style.background='rgba(230,168,0,0.1)';this.style.color='var(--fg-primary)'"`}>
                      <i class="bi bi-${isOutOfStock ? 'ban' : 'cart-plus'}"></i> ${isOutOfStock ? 'Out of Stock' : 'Add to Cart'}
                    </button>
                  </div>
                </div>`;
            }).join('')}
          </div>
        </div>`;
    });

    el.innerHTML = html;
  }

  // -- Buy products via PayMongo --------------------------------
  window.ownerBuyProducts = function(productIds, supplierName) {
    const ids = Array.isArray(productIds) ? productIds : JSON.parse(productIds);

    // If single product, show quantity selector
    if (ids.length === 1) {
      showQuantityModal(ids[0], supplierName);
    } else {
      // Multiple products - show bulk quantity selector
      showBulkQuantityModal(ids, supplierName);
    }
  };

  // -- Add to cart functions ------------------------------------
  window.addToCart = function(productId) {
    const product = allProducts?.find(p => p.id === productId);
    if (!product) {
      alert('Product not found.');
      return;
    }

    // Show quantity modal for adding to cart
    showAddToCartModal(product);
  };

  window.addAllToCart = function(productIds, supplierName) {
    const ids = Array.isArray(productIds) ? productIds : JSON.parse(productIds);
    
    let addedCount = 0;
    ids.forEach(id => {
      const product = allProducts?.find(p => p.id === id);
      if (product && product.qty > 0) {
        // Add with quantity 1 by default
        FGCart.addItem(product, 1);
        addedCount++;
      }
    });

    if (addedCount > 0) {
      showCartNotification(`Added ${addedCount} item(s) to cart`);
    } else {
      alert('No items available to add.');
    }
  };

  function showAddToCartModal(product) {
    const modal = document.createElement('div');
    modal.id = 'addToCartModal';
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;';
    
    const maxQty = parseInt(product.qty) || 1;
    const unitPrice = parseFloat(product.srp) || 0;
    
    modal.innerHTML = `
      <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;box-shadow:0 24px 64px rgba(0,0,0,0.4);width:100%;max-width:500px;animation:modalIn 0.25s cubic-bezier(0.16,1,0.3,1);">
        <div style="padding:1.5rem 1.75rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;">
          <h5 style="margin:0;font-weight:800;font-size:1.1rem;color:var(--fg-text);">
            <i class="bi bi-cart-plus" style="color:var(--fg-primary);"></i> Add to Cart
          </h5>
          <button onclick="document.getElementById('addToCartModal').remove()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:1rem;transition:all 0.2s;">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div style="padding:1.5rem 1.75rem;">
          <div style="margin-bottom:1.5rem;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--fg-text);margin-bottom:0.5rem;">${escHtml(product.item_description)}</div>
            <div style="font-size:0.8rem;color:var(--fg-muted);">
              <span style="background:rgba(230,168,0,0.1);color:var(--fg-primary);padding:0.2rem 0.6rem;border-radius:6px;font-weight:600;margin-right:0.5rem;">${escHtml(product.category)}</span>
              Available: <strong>${maxQty} units</strong>
            </div>
          </div>
          
          <div style="margin-bottom:1.5rem;">
            <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.5rem;">
              Quantity <span style="color:#dc3545;">*</span>
            </label>
            <div style="display:flex;align-items:center;gap:1rem;">
              <button onclick="adjustCartQty(-1)" style="width:40px;height:40px;border-radius:8px;border:1.5px solid var(--fg-border);background:var(--fg-bg);cursor:pointer;font-size:1.2rem;font-weight:700;color:var(--fg-text);transition:all 0.2s;">
                <i class="bi bi-dash"></i>
              </button>
              <input type="number" id="cartQtyInput" value="1" min="1" max="${maxQty}" style="flex:1;padding:0.65rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:1.2rem;font-weight:700;text-align:center;outline:none;" oninput="updateCartTotal()">
              <button onclick="adjustCartQty(1)" style="width:40px;height:40px;border-radius:8px;border:1.5px solid var(--fg-border);background:var(--fg-bg);cursor:pointer;font-size:1.2rem;font-weight:700;color:var(--fg-text);transition:all 0.2s;">
                <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>
          
          <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;padding:1rem;margin-bottom:1.5rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
              <span style="color:var(--fg-muted);font-size:0.85rem;">Unit Price:</span>
              <span style="font-weight:600;color:var(--fg-text);">?${unitPrice.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
              <span style="color:var(--fg-muted);font-size:0.85rem;">Quantity:</span>
              <span style="font-weight:600;color:var(--fg-text);" id="cartQtyDisplay">1</span>
            </div>
            <div style="height:1px;background:var(--fg-border);margin:0.75rem 0;"></div>
            <div style="display:flex;justify-content:space-between;">
              <span style="font-weight:700;color:var(--fg-text);">Subtotal:</span>
              <span style="font-size:1.3rem;font-weight:800;color:var(--fg-primary);" id="cartTotalDisplay">?${unitPrice.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
            </div>
          </div>
        </div>
        <div style="padding:1.25rem 1.75rem;border-top:1px solid var(--fg-border);display:flex;gap:0.75rem;justify-content:flex-end;">
          <button onclick="document.getElementById('addToCartModal').remove()" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.25rem;border-radius:10px;background:transparent;color:var(--fg-muted);border:1.5px solid var(--fg-border);font-weight:600;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-x-circle"></i> Cancel
          </button>
          <button onclick="confirmAddToCart(${product.id})" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.5rem;border-radius:10px;background:var(--fg-primary);color:#fff;border:none;font-weight:700;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-cart-plus"></i> Add to Cart
          </button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    // Store product data for later use
    window.currentCartProduct = { id: product.id, maxQty, unitPrice, product };
  }

  window.adjustCartQty = function(delta) {
    const input = document.getElementById('cartQtyInput');
    const current = parseInt(input.value) || 1;
    const newVal = Math.max(1, Math.min(window.currentCartProduct.maxQty, current + delta));
    input.value = newVal;
    updateCartTotal();
  };

  window.updateCartTotal = function() {
    const input = document.getElementById('cartQtyInput');
    let qty = parseInt(input.value) || 1;
    
    // Validate bounds
    if (qty < 1) qty = 1;
    if (qty > window.currentCartProduct.maxQty) qty = window.currentCartProduct.maxQty;
    input.value = qty;
    
    const total = window.currentCartProduct.unitPrice * qty;
    document.getElementById('cartQtyDisplay').textContent = qty;
    document.getElementById('cartTotalDisplay').textContent = '?' + total.toLocaleString('en-PH', {minimumFractionDigits:2});
  };

  window.confirmAddToCart = function(productId) {
    const qty = parseInt(document.getElementById('cartQtyInput').value) || 1;
    const product = window.currentCartProduct.product;
    
    // Add to cart
    FGCart.addItem(product, qty);
    
    // Close modal
    document.getElementById('addToCartModal')?.remove();
    
    // Show notification
    showCartNotification(`Added ${qty} � ${product.item_description} to cart`);
  };

  function showCartNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = 'position:fixed;top:80px;right:20px;background:var(--fg-primary);color:#fff;padding:1rem 1.5rem;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.2);z-index:10000;font-weight:600;font-size:0.9rem;display:flex;align-items:center;gap:0.75rem;animation:slideInRight 0.3s ease-out;';
    notification.innerHTML = `
      <i class="bi bi-check-circle-fill" style="font-size:1.2rem;"></i>
      <span>${escHtml(message)}</span>
      <a href="views/user/owner/cart.php" style="color:#fff;text-decoration:underline;font-weight:700;margin-left:0.5rem;">View Cart</a>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.style.animation = 'slideOutRight 0.3s ease-in';
      setTimeout(() => notification.remove(), 300);
    }, 4000);
  }

  function showQuantityModal(productId, supplierName) {
    // Find product data
    const product = allProducts?.find(p => p.id === productId);
    if (!product) {
      alert('Product not found.');
      return;
    }

    // Create modal
    const modal = document.createElement('div');
    modal.id = 'quantityModal';
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;';
    
    const maxQty = parseInt(product.qty) || 1;
    const unitPrice = parseFloat(product.srp) || 0;
    
    modal.innerHTML = `
      <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;box-shadow:0 24px 64px rgba(0,0,0,0.4);width:100%;max-width:500px;animation:modalIn 0.25s cubic-bezier(0.16,1,0.3,1);">
        <div style="padding:1.5rem 1.75rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;">
          <h5 style="margin:0;font-weight:800;font-size:1.1rem;color:var(--fg-text);">
            <i class="bi bi-cart-fill" style="color:var(--fg-primary);"></i> Select Quantity
          </h5>
          <button onclick="document.getElementById('quantityModal').remove()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:1rem;transition:all 0.2s;">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div style="padding:1.5rem 1.75rem;">
          <div style="margin-bottom:1.5rem;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--fg-text);margin-bottom:0.5rem;">${escHtml(product.item_description)}</div>
            <div style="font-size:0.8rem;color:var(--fg-muted);">
              <span style="background:rgba(230,168,0,0.1);color:var(--fg-primary);padding:0.2rem 0.6rem;border-radius:6px;font-weight:600;margin-right:0.5rem;">${escHtml(product.category)}</span>
              Available: <strong>${maxQty} units</strong>
            </div>
          </div>
          
          <div style="margin-bottom:1.5rem;">
            <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.5rem;">
              Quantity <span style="color:#dc3545;">*</span>
            </label>
            <div style="display:flex;align-items:center;gap:1rem;">
              <button onclick="adjustQty(-1)" style="width:40px;height:40px;border-radius:8px;border:1.5px solid var(--fg-border);background:var(--fg-bg);cursor:pointer;font-size:1.2rem;font-weight:700;color:var(--fg-text);transition:all 0.2s;">
                <i class="bi bi-dash"></i>
              </button>
              <input type="number" id="qtyInput" value="1" min="1" max="${maxQty}" style="flex:1;padding:0.65rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:1.2rem;font-weight:700;text-align:center;outline:none;" oninput="updateTotal()">
              <button onclick="adjustQty(1)" style="width:40px;height:40px;border-radius:8px;border:1.5px solid var(--fg-border);background:var(--fg-bg);cursor:pointer;font-size:1.2rem;font-weight:700;color:var(--fg-text);transition:all 0.2s;">
                <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>
          
          <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;padding:1rem;margin-bottom:1.5rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
              <span style="color:var(--fg-muted);font-size:0.85rem;">Unit Price:</span>
              <span style="font-weight:600;color:var(--fg-text);">?${unitPrice.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
              <span style="color:var(--fg-muted);font-size:0.85rem;">Quantity:</span>
              <span style="font-weight:600;color:var(--fg-text);" id="qtyDisplay">1</span>
            </div>
            <div style="height:1px;background:var(--fg-border);margin:0.75rem 0;"></div>
            <div style="display:flex;justify-content:space-between;">
              <span style="font-weight:700;color:var(--fg-text);">Total:</span>
              <span style="font-size:1.3rem;font-weight:800;color:var(--fg-primary);" id="totalDisplay">?${unitPrice.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
            </div>
          </div>
        </div>
        <div style="padding:1.25rem 1.75rem;border-top:1px solid var(--fg-border);display:flex;gap:0.75rem;justify-content:flex-end;">
          <button onclick="document.getElementById('quantityModal').remove()" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.25rem;border-radius:10px;background:transparent;color:var(--fg-muted);border:1.5px solid var(--fg-border);font-weight:600;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-x-circle"></i> Cancel
          </button>
          <button onclick="proceedToPayment(${productId}, '${escHtml(supplierName)}')" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.5rem;border-radius:10px;background:var(--fg-primary);color:#fff;border:none;font-weight:700;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-cart-check"></i> Proceed to Payment
          </button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    // Store product data for later use
    window.currentProduct = { id: productId, maxQty, unitPrice };
  }

  function showBulkQuantityModal(productIds, supplierName) {
    // For multiple products, show confirmation with total
    const total = productIds.length;
    if (!confirm(`Proceed to payment for ${total} product(s) from ${supplierName}?\n\nNote: This will purchase all available stock for each product.`)) return;
    
    proceedToPaymentBulk(productIds);
  }

  window.adjustQty = function(delta) {
    const input = document.getElementById('qtyInput');
    const current = parseInt(input.value) || 1;
    const newVal = Math.max(1, Math.min(window.currentProduct.maxQty, current + delta));
    input.value = newVal;
    updateTotal();
  };

  window.updateTotal = function() {
    const input = document.getElementById('qtyInput');
    let qty = parseInt(input.value) || 1;
    
    // Validate bounds
    if (qty < 1) qty = 1;
    if (qty > window.currentProduct.maxQty) qty = window.currentProduct.maxQty;
    input.value = qty;
    
    const total = window.currentProduct.unitPrice * qty;
    document.getElementById('qtyDisplay').textContent = qty;
    document.getElementById('totalDisplay').textContent = '?' + total.toLocaleString('en-PH', {minimumFractionDigits:2});
  };

  window.proceedToPayment = function(productId, supplierName) {
    const qty = parseInt(document.getElementById('qtyInput').value) || 1;
    
    // Close modal
    document.getElementById('quantityModal')?.remove();
    
    // Show loading state
    const loadingEl = document.createElement('div');
    loadingEl.id = 'paymentLoadingOverlay';
    loadingEl.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;gap:1rem;';
    loadingEl.innerHTML = `
      <div style="width:48px;height:48px;border:4px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;"></div>
      <div style="font-size:1rem;font-weight:600;">Creating payment session�</div>
    `;
    document.body.appendChild(loadingEl);

    fetch('api/paymongo', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
        action: 'create_checkout', 
        product_ids: [productId],
        quantities: { [productId]: qty }
      }),
    })
      .then(r => r.json())
      .then(data => {
        document.getElementById('paymentLoadingOverlay')?.remove();
        if (!data.success) {
          alert('Payment error: ' + (data.message || 'Unknown error'));
          return;
        }
        // Redirect to PayMongo checkout
        window.location.href = data.checkout_url;
      })
      .catch(err => {
        document.getElementById('paymentLoadingOverlay')?.remove();
        alert('Could not connect to payment server. Please try again.');
        console.error(err);
      });
  };

  function proceedToPaymentBulk(productIds) {
    // Show loading state
    const loadingEl = document.createElement('div');
    loadingEl.id = 'paymentLoadingOverlay';
    loadingEl.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;gap:1rem;';
    loadingEl.innerHTML = `
      <div style="width:48px;height:48px;border:4px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;"></div>
      <div style="font-size:1rem;font-weight:600;">Creating payment session�</div>
    `;
    document.body.appendChild(loadingEl);

    fetch('api/paymongo', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'create_checkout', product_ids: productIds }),
    })
      .then(r => r.json())
      .then(data => {
        document.getElementById('paymentLoadingOverlay')?.remove();
        if (!data.success) {
          alert('Payment error: ' + (data.message || 'Unknown error'));
          return;
        }
        // Redirect to PayMongo checkout
        window.location.href = data.checkout_url;
      })
      .catch(err => {
        document.getElementById('paymentLoadingOverlay')?.remove();
        alert('Could not connect to payment server. Please try again.');
        console.error(err);
      });
  }

  // Global accept/reject handlers
  window.ownerAccept = function(id) {
    ownerAction('accept', [id]);
  };
  window.ownerReject = function(id) {
    ownerAction('reject', [id]);
  };

  function ownerAction(action, ids) {
    fetch('api/owner/products', {
      credentials: 'include',
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action, ids }),
    })
      .then(r => r.json())
      .then(data => {
        if (!data.success) { alert(data.message || 'Error'); return; }
        // Reload both sections
        loadOwnerSubmissions();
      })
      .catch(() => alert('Could not connect to server.'));
  }

  /* ================================================================
     SUPERVISOR DASHBOARD
  ================================================================ */
  function supervisorDashboard(user) {
    return `
      ${sectionTitle('???', 'Operations Overview', '#6366f1')}
      ${statsGrid(`
        ${stat('??', 'Active Repairs',  '14', 'rgba(230,168,0,0.12)',  'var(--fg-primary)')}
        ${stat('??', 'Staff On Duty',   '6',  'rgba(99,102,241,0.12)', '#6366f1')}
        ${stat('??', 'Issues Flagged', '2',  'rgba(220,53,69,0.12)',  '#dc3545')}
        ${stat('?', 'Resolved Today',  '9',  'rgba(40,167,69,0.12)',  '#28A745')}
      `)}
      ${sectionTitle('?????', 'Staff Performance', '#2a9d8f')}
      ${statsGrid(`
        ${stat('?', 'Avg Rating',       '4.7', 'rgba(78,205,196,0.15)', '#2a9d8f')}
        ${stat('??', 'Avg Repair Time', '1.4h','rgba(255,193,7,0.15)',  '#856404')}
        ${stat('??', 'Parts Used Today', '23',  'rgba(99,102,241,0.12)', '#6366f1')}
        ${stat('??', 'Rework Rate',      '3%',  'rgba(220,53,69,0.12)',  '#dc3545')}
      `)}
      ${sectionTitle('?', 'Quick Actions', '#6366f1')}
      ${actionsGrid(`
        ${action('??', 'Product Supply Inventory', 'Manage all products and stock levels.',              'var(--fg-primary)', 'views/user/supervisor/inventory.php')}
        ${action('??', 'Flagged Issues',           'Review and resolve escalated complaints.',           '#dc3545')}
        ${action('??', 'Daily Report',             'Generate and export today\'s operations report.',    '#28A745', 'views/user/supervisor/reports.php')}
        ${action('??', 'Parts Request',            'Approve or reject parts requests from staff.',       '#6366f1')}
      `)}
      ${sectionTitle('??', 'Active Jobs', '#2a9d8f')}
      ${bookingTable([
        { id:'#1042', customer:'Maria Santos',  device:'iPhone 14',   service:'Screen Repair', status:'In Progress', amount:'$89' },
        { id:'#1040', customer:'Ana Reyes',     device:'Xiaomi 12',   service:'Water Damage',  status:'Pending',     amount:'$120' },
        { id:'#1038', customer:'Carlo Mendoza', device:'Oppo Reno 8', service:'Speaker Fix',   status:'In Progress', amount:'$35' },
        { id:'#1037', customer:'Liza Tan',      device:'Samsung A54', service:'Back Glass',    status:'Pending',     amount:'$55' },
      ])}`;
  }

  /* ================================================================
     SUPPLIER DASHBOARD
  ================================================================ */
  function supplierDashboard(user) {
    // Kick off async load of shop + my products + stats
    setTimeout(() => loadSupplierShopView(), 100);
    setTimeout(() => loadSupplierStats(), 120);

    return `
      ${sectionTitle('??', 'Supply Overview', '#10b981')}
      ${statsGrid(`
        ${stat('??', 'My Products',     '�', 'rgba(16,185,129,0.12)', '#10b981',           'statMyTotal')}
        ${stat('?', 'Pending Review',  '�', 'rgba(230,168,0,0.12)',  'var(--fg-primary)', 'statSupPending')}
        ${stat('?', 'Accepted',        '�', 'rgba(40,167,69,0.12)',  '#28A745',           'statSupAccepted')}
        ${stat('??', 'In Shop',         '�', 'rgba(59,130,246,0.12)', '#3b82f6',           'statShopTotal')}
      `)}
      ${sectionTitle('?', 'Quick Actions', '#10b981')}
      ${actionsGrid(`
        ${action('?', 'Add Product',      'List a new part or accessory for sale.',        '#10b981',           'views/user/supplier/products.php')}
        ${action('??', 'Manage Inventory', 'Update stock levels and product details.',      'var(--fg-primary)', 'views/user/supplier/products.php')}
        ${action('??', 'View Orders',      'See all incoming orders from shops.',           '#6366f1',           'views/user/supplier/orders.php')}
        ${action('??', 'Deliveries',       'Track and update delivery statuses.',           '#2a9d8f',           'views/user/supplier/deliveries.php')}
        ${action('??', 'Sales Report',     'View revenue, top products, and trends.',       '#28A745',           'views/user/supplier/sales-report.php')}
        ${action('??', 'Shop Messages',    'Communicate with repair shops directly.',       '#856404',           'views/user/supplier/messages.php')}
      `)}

      <!-- -- Shop Products View -- -->
      <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;margin-top:0.5rem;">
        <span style="font-size:1.2rem;line-height:1;">??</span>
        <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">All Products Currently in Shop</h5>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <span style="font-size:0.78rem;color:var(--fg-muted);" id="shopProductCount">Loading�</span>
      </div>
      <div id="shopProductsGrid" style="margin-bottom:2rem;">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading shop products�
        </div>
      </div>

      <!-- -- My Products View -- -->
      <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;margin-top:0.5rem;">
        <span style="font-size:1.2rem;line-height:1;">??</span>
        <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">My Products</h5>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <a href="views/user/supplier/products.php"
           style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">
          Manage All ?
        </a>
      </div>
      <div id="myProductsGrid" style="margin-bottom:2rem;">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading your products�
        </div>
      </div>

      <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        .sp-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
          gap: 1rem;
        }
        .sp-card {
          background: var(--fg-card-bg);
          border: 1px solid var(--fg-border);
          border-radius: 14px;
          overflow: hidden;
          transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        }
        .sp-card:hover {
          transform: translateY(-4px);
          box-shadow: 0 10px 28px rgba(0,0,0,0.15);
          border-color: var(--fg-primary);
        }
        .sp-card-img {
          width: 100%; aspect-ratio: 1/1;
          object-fit: cover; background: var(--fg-bg);
          display: block;
        }
        .sp-card-img-ph {
          width: 100%; aspect-ratio: 1/1;
          background: var(--fg-bg);
          display: flex; align-items: center; justify-content: center;
          font-size: 2rem; color: var(--fg-muted);
        }
        .sp-card-body { padding: 0.75rem; }
        .sp-cat {
          font-size: 0.68rem; font-weight: 700;
          color: var(--fg-primary);
          background: rgba(230,168,0,0.1);
          border: 1px solid rgba(230,168,0,0.2);
          padding: 0.1rem 0.5rem; border-radius: 50px;
          display: inline-block; margin-bottom: 0.35rem;
        }
        .sp-name {
          font-size: 0.8rem; font-weight: 700;
          color: var(--fg-text); line-height: 1.3;
          margin-bottom: 0.2rem;
          display: -webkit-box; -webkit-line-clamp: 2;
          -webkit-box-orient: vertical; overflow: hidden;
        }
        .sp-brand { font-size: 0.72rem; color: var(--fg-muted); margin-bottom: 0.5rem; }
        .sp-footer {
          display: flex; align-items: center;
          justify-content: space-between; flex-wrap: wrap; gap: 0.25rem;
        }
        .sp-price { font-size: 0.95rem; font-weight: 800; color: var(--fg-primary); }
        .sp-qty {
          font-size: 0.68rem; color: var(--fg-muted);
          background: var(--fg-bg); border: 1px solid var(--fg-border);
          padding: 0.1rem 0.45rem; border-radius: 6px;
        }
        .sp-status {
          font-size: 0.68rem; font-weight: 700;
          padding: 0.15rem 0.55rem; border-radius: 50px;
          display: inline-block; margin-top: 0.35rem;
        }
        .sp-status-draft         { background:rgba(108,117,125,0.12); color:#6C757D; }
        .sp-status-verified      { background:rgba(40,167,69,0.12);   color:#28A745; }
        .sp-status-sent_to_owner { background:rgba(59,130,246,0.12);  color:#3b82f6; }
        .sp-status-owner_received{ background:rgba(16,185,129,0.12);  color:#10b981; }
        .sp-status-rejected      { background:rgba(220,53,69,0.12);   color:#dc3545; }
        .sp-empty {
          text-align: center; padding: 2.5rem 1rem;
          color: var(--fg-muted); font-size: 0.88rem;
          background: var(--fg-card-bg);
          border: 1px solid var(--fg-border);
          border-radius: 14px;
        }
      </style>`;
  }

  /* -- Load supplier stats ------------------------------------- */
  function loadSupplierStats() {
    fetch(_B + 'supplier_orders.php?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        const el1 = document.getElementById('statSupPending');
        const el2 = document.getElementById('statSupAccepted');
        if (el1) el1.textContent = d.stats.pending      || 0;
        if (el2) el2.textContent = d.stats.acknowledged || 0;
      }).catch(() => {});
  }

  /* -- Load supplier shop view data ---------------------------- */
  function loadSupplierShopView() {
    fetch(_B + 'supplier_shop_view.php?action=both', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (!data.success) throw new Error(data.message);

        // Update stat counters
        const myTotalEl   = document.getElementById('statMyTotal');
        const shopTotalEl = document.getElementById('statShopTotal');
        if (myTotalEl)   myTotalEl.textContent   = data.my_total   || 0;
        if (shopTotalEl) shopTotalEl.textContent  = data.shop_total || 0;

        // Render shop products
        const shopCountEl = document.getElementById('shopProductCount');
        if (shopCountEl) shopCountEl.textContent = data.shop_total + ' products';
        renderSupplierProductGrid('shopProductsGrid', data.shop_products, true);

        // Render my products
        renderSupplierProductGrid('myProductsGrid', data.my_products, false);
      })
      .catch(() => {
        const shopEl = document.getElementById('shopProductsGrid');
        const myEl   = document.getElementById('myProductsGrid');
        if (shopEl) shopEl.innerHTML = '<div class="sp-empty">Could not load shop products.</div>';
        if (myEl)   myEl.innerHTML   = '<div class="sp-empty">Could not load your products.</div>';
      });
  }

  function renderSupplierProductGrid(containerId, products, isShopView) {
    const el = document.getElementById(containerId);
    if (!el) return;

    if (!products || !products.length) {
      el.innerHTML = `<div class="sp-empty">
        <i class="bi bi-box-seam" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
        ${isShopView ? 'No products in the shop yet.' : 'You have no products yet. <a href="views/user/supplier/products.php" style="color:var(--fg-primary);font-weight:600;">Add one ?</a>'}
      </div>`;
      return;
    }

    const cards = products.map(p => {
      const imgSrc = p.image_path || null;
      const imgHtml = imgSrc
        ? `<img class="sp-card-img" src="${escHtml(imgSrc)}" alt="${escHtml(p.item_description)}" loading="lazy"
               onerror="this.outerHTML='<div class=\\'sp-card-img-ph\\'><i class=\\'bi bi-image\\'></i></div>'">`
        : `<div class="sp-card-img-ph"><i class="bi bi-image"></i></div>`;

      const statusHtml = !isShopView
        ? `<div><span class="sp-status sp-status-${p.status}">${statusLabelMap(p.status)}</span></div>`
        : '';

      const supplierHtml = isShopView && p.supplier_name
        ? `<div style="font-size:0.68rem;color:var(--fg-muted);margin-top:0.2rem;">by ${escHtml(p.supplier_name)}</div>`
        : '';

      return `
        <div class="sp-card">
          ${imgHtml}
          <div class="sp-card-body">
            <span class="sp-cat">${escHtml(p.category)}</span>
            <div class="sp-name">${escHtml(p.item_description)}</div>
            ${p.brand ? `<div class="sp-brand">${escHtml(p.brand)}</div>` : ''}
            ${supplierHtml}
            <div class="sp-footer">
              <span class="sp-price">?${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
              <span class="sp-qty">${p.qty > 0 ? p.qty + ' in stock' : 'Out of stock'}</span>
            </div>
            ${statusHtml}
          </div>
        </div>`;
    }).join('');

    el.innerHTML = `<div class="sp-grid">${cards}</div>`;
  }

  /* ================================================================
     MARKETPLACE DASHBOARD (All Roles)
  ================================================================ */
  function marketplaceDashboard(user) {
    // Load marketplace data async
    setTimeout(() => {
      loadMarketplaceProducts();
      loadMarketplaceTechnicians();
      if (user.role === 'owner' || user.role === 'customer') {
        loadMarketplaceMessages();
      }
    }, 100);

    return `
      ${sectionTitle('???', 'Marketplace', 'var(--fg-primary)')}
      
      <!-- Search and Filter Bar -->
      <div class="dashboard-card mb-4" style="padding:1rem 1.5rem;">
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
          <div style="flex:1;min-width:200px;position:relative;">
            <i class="bi bi-search" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--fg-muted);font-size:0.9rem;"></i>
            <input type="text" id="marketplaceSearch" placeholder="Search products, shops, technicians..." 
              style="width:100%;padding:0.65rem 1rem 0.65rem 2.5rem;border:1.5px solid var(--fg-border);border-radius:50px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;"
              onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'" oninput="filterMarketplace()">
          </div>
          <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <button class="market-tab active" data-tab="all" onclick="switchMarketTab('all')" style="padding:0.5rem 1.2rem;border-radius:50px;border:1.5px solid var(--fg-primary);background:var(--fg-primary);color:#fff;font-size:0.82rem;font-weight:700;cursor:pointer;transition:all 0.2s;">
              <i class="bi bi-grid-fill"></i> All
            </button>
            <button class="market-tab" data-tab="products" onclick="switchMarketTab('products')" style="padding:0.5rem 1.2rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-text);font-size:0.82rem;font-weight:700;cursor:pointer;transition:all 0.2s;">
              <i class="bi bi-box-seam"></i> Products
            </button>
            <button class="market-tab" data-tab="technicians" onclick="switchMarketTab('technicians')" style="padding:0.5rem 1.2rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-text);font-size:0.82rem;font-weight:700;cursor:pointer;transition:all 0.2s;">
              <i class="bi bi-tools"></i> Technicians
            </button>
          </div>
        </div>
        <!-- Category filter row (shown when Products tab active) -->
        <div id="catFilterRow" style="display:flex;gap:0.4rem;flex-wrap:wrap;margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid var(--fg-border);">
          <button class="cat-filter-btn active" data-cat="all" onclick="filterByCategory('all')"
            style="padding:0.3rem 0.85rem;border-radius:50px;border:1.5px solid var(--fg-primary);background:rgba(230,168,0,0.12);color:var(--fg-primary);font-size:0.75rem;font-weight:700;cursor:pointer;transition:all 0.2s;">
            All Categories
          </button>
          <button class="cat-filter-btn" data-cat="LCD / Screen" onclick="filterByCategory('LCD / Screen')"
            style="padding:0.3rem 0.85rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.75rem;font-weight:600;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-phone"></i> Screens
          </button>
          <button class="cat-filter-btn" data-cat="Battery" onclick="filterByCategory('Battery')"
            style="padding:0.3rem 0.85rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.75rem;font-weight:600;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-battery-charging"></i> Batteries
          </button>
          <button class="cat-filter-btn" data-cat="Tempered Glass" onclick="filterByCategory('Tempered Glass')"
            style="padding:0.3rem 0.85rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.75rem;font-weight:600;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-shield-check"></i> Tempered Glass
          </button>
          <button class="cat-filter-btn" data-cat="Charger" onclick="filterByCategory('Charger')"
            style="padding:0.3rem 0.85rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.75rem;font-weight:600;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-plug"></i> Chargers
          </button>
          <button class="cat-filter-btn" data-cat="Earphones" onclick="filterByCategory('Earphones')"
            style="padding:0.3rem 0.85rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.75rem;font-weight:600;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-headphones"></i> Earphones
          </button>
          <button class="cat-filter-btn" data-cat="Back Cover" onclick="filterByCategory('Back Cover')"
            style="padding:0.3rem 0.85rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.75rem;font-weight:600;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-phone-flip"></i> Cases
          </button>
          <button class="cat-filter-btn" data-cat="Tools" onclick="filterByCategory('Tools')"
            style="padding:0.3rem 0.85rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.75rem;font-weight:600;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-tools"></i> Tools
          </button>
        </div>
      </div>

      <!-- Products Section -->
      <div id="marketProductsSection">
        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
          <span style="font-size:1.2rem;line-height:1;">??</span>
          <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">Products & Accessories</h5>
          <div style="flex:1;height:1px;background:var(--fg-border);"></div>
          <span id="productCount" style="background:rgba(230,168,0,0.12);color:var(--fg-primary);font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:20px;"></span>
        </div>
        <div id="marketProductsGrid" style="margin-bottom:2.5rem;">
          <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
            <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
            Loading products�
          </div>
        </div>
      </div>

      <!-- Technicians Section -->
      <div id="marketTechniciansSection">
        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
          <span style="font-size:1.2rem;line-height:1;">??</span>
          <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">Expert Technicians</h5>
          <div style="flex:1;height:1px;background:var(--fg-border);"></div>
          <span id="techCount" style="background:rgba(59,130,246,0.12);color:#3b82f6;font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:20px;"></span>
        </div>
        <div id="marketTechniciansGrid" style="margin-bottom:2rem;">
          <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
            <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
            Loading technicians�
          </div>
        </div>
      </div>

      <!-- Messages Section -->
      <div id="marketMessagesSection" style="display:${(user.role === 'owner' || user.role === 'customer') ? 'block' : 'none'};">
        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
          <span style="font-size:1.2rem;line-height:1;">??</span>
          <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">Recent Messages</h5>
          <div style="flex:1;height:1px;background:var(--fg-border);"></div>
          <a href="views/user/${user.role}/messages.php" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All ?</a>
        </div>
        <div id="marketMessagesGrid" style="margin-bottom:2rem;">
          <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
            <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
            Loading messages�
          </div>
        </div>
      </div>

      <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        
        .market-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
          gap: 1.25rem;
        }
        
        .market-product-card {
          background: var(--fg-card-bg);
          border: 1px solid var(--fg-border);
          border-radius: 14px;
          overflow: hidden;
          transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
          display: flex;
          flex-direction: column;
          cursor: pointer;
        }
        
        .market-product-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 12px 36px rgba(230,168,0,0.14);
          border-color: var(--fg-primary);
        }
        
        .market-product-img {
          width: 100%;
          aspect-ratio: 1/1;
          object-fit: cover;
          background: var(--fg-bg);
        }
        
        .market-product-img-ph {
          width: 100%;
          aspect-ratio: 1/1;
          background: var(--fg-bg);
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 2.5rem;
          color: var(--fg-muted);
        }
        
        .market-product-body {
          padding: 0.9rem 1rem 1rem;
          flex: 1;
          display: flex;
          flex-direction: column;
        }
        
        .market-supplier-badge {
          display: inline-flex;
          align-items: center;
          gap: 0.3rem;
          font-size: 0.68rem;
          font-weight: 700;
          color: #3b82f6;
          background: rgba(59,130,246,0.1);
          border: 1px solid rgba(59,130,246,0.2);
          padding: 0.15rem 0.55rem;
          border-radius: 50px;
          margin-bottom: 0.4rem;
          max-width: fit-content;
        }
        
        .market-cat-badge {
          display: inline-block;
          font-size: 0.68rem;
          font-weight: 700;
          color: var(--fg-primary);
          background: rgba(230,168,0,0.1);
          border: 1px solid rgba(230,168,0,0.2);
          padding: 0.15rem 0.55rem;
          border-radius: 50px;
          margin-bottom: 0.45rem;
        }
        
        .market-product-title {
          font-size: 0.82rem;
          font-weight: 700;
          color: var(--fg-text);
          line-height: 1.35;
          margin-bottom: 0.3rem;
          flex: 1;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }
        
        .market-product-brand {
          font-size: 0.75rem;
          color: var(--fg-muted);
          margin-bottom: 0.6rem;
        }
        
        .market-product-footer {
          display: flex;
          align-items: center;
          justify-content: space-between;
          margin-top: auto;
        }
        
        .market-product-price {
          font-size: 1rem;
          font-weight: 800;
          color: var(--fg-primary);
        }
        
        .market-product-qty {
          font-size: 0.72rem;
          color: var(--fg-muted);
          background: var(--fg-bg);
          border: 1px solid var(--fg-border);
          padding: 0.15rem 0.5rem;
          border-radius: 6px;
        }
        
        .market-tech-card {
          background: var(--fg-card-bg);
          border: 1px solid var(--fg-border);
          border-radius: 18px;
          padding: 1.5rem 1.25rem;
          text-align: center;
          transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
          position: relative;
          overflow: hidden;
          display: flex;
          flex-direction: column;
          cursor: pointer;
        }
        
        .market-tech-card::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 4px;
          background: linear-gradient(90deg, var(--fg-primary), #c98f00);
          opacity: 0;
          transition: opacity 0.25s;
        }
        
        .market-tech-card:hover {
          transform: translateY(-6px);
          box-shadow: 0 14px 40px rgba(230,168,0,0.14);
          border-color: var(--fg-primary);
        }
        
        .market-tech-card:hover::before {
          opacity: 1;
        }
        
        .market-tech-avatar {
          width: 80px;
          height: 80px;
          border-radius: 50%;
          margin: 0 auto 1rem;
          border: 3px solid var(--fg-border);
          object-fit: cover;
          transition: border-color 0.25s;
        }
        
        .market-tech-card:hover .market-tech-avatar {
          border-color: var(--fg-primary);
        }
        
        .market-tech-avatar-ph {
          width: 80px;
          height: 80px;
          border-radius: 50%;
          margin: 0 auto 1rem;
          border: 3px solid var(--fg-border);
          background: linear-gradient(135deg, rgba(230,168,0,0.18), rgba(230,168,0,0.06));
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 2rem;
          color: var(--fg-primary);
          transition: border-color 0.25s;
        }
        
        .market-tech-card:hover .market-tech-avatar-ph {
          border-color: var(--fg-primary);
        }
        
        .market-tech-name {
          font-size: 1rem;
          font-weight: 800;
          color: var(--fg-text);
          margin-bottom: 0.25rem;
        }
        
        .market-tech-role {
          display: inline-block;
          font-size: 0.7rem;
          font-weight: 700;
          color: var(--fg-primary);
          background: rgba(230,168,0,0.1);
          border: 1px solid rgba(230,168,0,0.2);
          padding: 0.15rem 0.6rem;
          border-radius: 50px;
          margin-bottom: 0.75rem;
        }
        
        .market-tech-shop {
          font-size: 0.78rem;
          color: var(--fg-muted);
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 0.3rem;
          margin-bottom: 0.75rem;
        }
        
        .market-tech-shop i {
          color: var(--fg-primary);
          font-size: 0.72rem;
        }
        
        .market-tech-specs {
          display: flex;
          flex-wrap: wrap;
          justify-content: center;
          gap: 0.35rem;
          margin-bottom: 0.75rem;
        }
        
        .market-tech-spec-pill {
          font-size: 0.68rem;
          font-weight: 600;
          color: var(--fg-muted);
          background: var(--fg-bg);
          border: 1px solid var(--fg-border);
          padding: 0.15rem 0.55rem;
          border-radius: 50px;
        }
        
        .market-tech-cta {
          margin-top: auto;
          padding: 0.5rem 1rem;
          border-radius: 8px;
          background: rgba(230,168,0,0.1);
          border: 1.5px solid rgba(230,168,0,0.3);
          color: var(--fg-primary);
          font-size: 0.8rem;
          font-weight: 700;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 0.4rem;
          transition: all 0.2s;
          cursor: pointer;
        }
        
        .market-tech-card:hover .market-tech-cta {
          background: var(--fg-primary);
          border-color: var(--fg-primary);
          color: #fff;
        }
        
        .market-empty {
          text-align: center;
          padding: 3.5rem 1rem;
          color: var(--fg-muted);
          background: var(--fg-card-bg);
          border: 1px solid var(--fg-border);
          border-radius: 14px;
        }
        
        .market-empty i {
          font-size: 2.8rem;
          display: block;
          margin-bottom: 0.75rem;
          opacity: 0.4;
        }
        
        .market-empty p {
          font-size: 0.9rem;
          margin: 0;
        }
        
        .market-message-card {
          background: var(--fg-card-bg);
          border: 1px solid var(--fg-border);
          border-radius: 14px;
          padding: 1rem 1.25rem;
          transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
          cursor: pointer;
          display: flex;
          align-items: center;
          gap: 1rem;
        }
        
        .market-message-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 20px rgba(0,0,0,0.1);
          border-color: var(--fg-primary);
        }
        
        .market-message-avatar {
          width: 48px;
          height: 48px;
          border-radius: 50%;
          background: linear-gradient(135deg, rgba(230,168,0,0.18), rgba(230,168,0,0.06));
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1.2rem;
          color: var(--fg-primary);
          flex-shrink: 0;
        }
        
        .market-message-content {
          flex: 1;
          min-width: 0;
        }
        
        .market-message-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          margin-bottom: 0.3rem;
        }
        
        .market-message-name {
          font-size: 0.88rem;
          font-weight: 700;
          color: var(--fg-text);
        }
        
        .market-message-time {
          font-size: 0.7rem;
          color: var(--fg-muted);
        }
        
        .market-message-preview {
          font-size: 0.8rem;
          color: var(--fg-muted);
          line-height: 1.4;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }
        
        .market-message-unread {
          width: 8px;
          height: 8px;
          border-radius: 50%;
          background: var(--fg-primary);
          flex-shrink: 0;
        }
      </style>`;
  }

  /* -- Load marketplace products -------------------------------- */
  function loadMarketplaceProducts() {
    fetch('api/marketplace/products', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (!data.success) throw new Error(data.message);
        
        const count = document.getElementById('productCount');
        if (count) count.textContent = (data.products?.length || 0) + ' available';
        
        renderMarketplaceProducts(data.products || []);
      })
      .catch(() => {
        const el = document.getElementById('marketProductsGrid');
        if (el) el.innerHTML = '<div class="market-empty"><i class="bi bi-exclamation-circle"></i><p>Could not load products.</p></div>';
      });
  }

  function renderMarketplaceProducts(products) {
    const el = document.getElementById('marketProductsGrid');
    if (!el) return;

    if (!products.length) {
      el.innerHTML = '<div class="market-empty"><i class="bi bi-inbox"></i><p>No products available yet.</p></div>';
      return;
    }

    // Category icon map
    const catIcons = {
      'LCD / Screen':   'bi-phone',
      'Battery':        'bi-battery-charging',
      'Tempered Glass': 'bi-shield-check',
      'Charger':        'bi-plug',
      'Earphones':      'bi-headphones',
      'Back Cover':     'bi-phone-flip',
      'Tools':          'bi-tools',
    };

    el.innerHTML = `<div class="market-grid">${products.map(p => {
      const icon = catIcons[p.category] || 'bi-box-seam';
      const img = p.image_path
        ? `<img class="market-product-img" src="${escHtml(p.image_path)}" alt="" loading="lazy" onerror="this.outerHTML='<div class=\\'market-product-img-ph\\'><i class=\\'bi ${icon}\\'></i></div>'">`
        : `<div class="market-product-img-ph"><i class="bi ${icon}"></i></div>`;
      
      const shopLabel = p.supplier_shop || p.supplier_name;
      
      return `
        <div class="market-product-card" onclick="viewProductDetails(${p.id})">
          ${img}
          <div class="market-product-body">
            <span class="market-supplier-badge"><i class="bi bi-shop"></i>${escHtml(shopLabel)}</span>
            <span class="market-cat-badge">${escHtml(p.category)}</span>
            <div class="market-product-title">${escHtml(p.item_description)}</div>
            ${p.brand ? `<div class="market-product-brand"><i class="bi bi-tag" style="font-size:0.65rem;"></i> ${escHtml(p.brand)}</div>` : ''}
            <div class="market-product-footer">
              <span class="market-product-price">?${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
              <span class="market-product-qty">${p.qty} in stock</span>
            </div>
          </div>
        </div>`;
    }).join('')}</div>`;
  }

  /* -- Load marketplace technicians ------------------------------ */
  function loadMarketplaceTechnicians() {
    fetch('api/marketplace/technicians', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (!data.success) throw new Error(data.message);
        
        const count = document.getElementById('techCount');
        if (count) count.textContent = (data.technicians?.length || 0) + ' available';
        
        renderMarketplaceTechnicians(data.technicians || []);
      })
      .catch(() => {
        const el = document.getElementById('marketTechniciansGrid');
        if (el) el.innerHTML = '<div class="market-empty"><i class="bi bi-exclamation-circle"></i><p>Could not load technicians.</p></div>';
      });
  }

  function renderMarketplaceTechnicians(technicians) {
    const el = document.getElementById('marketTechniciansGrid');
    if (!el) return;

    if (!technicians.length) {
      el.innerHTML = '<div class="market-empty"><i class="bi bi-inbox"></i><p>No technicians available yet.</p></div>';
      return;
    }

    el.innerHTML = `<div class="market-grid">${technicians.map(t => {
      const avatar = t.profile_image
        ? `<img class="market-tech-avatar" src="${escHtml(t.profile_image)}" alt="" loading="lazy" onerror="this.outerHTML='<div class=\\'market-tech-avatar-ph\\'><i class=\\'bi bi-person-fill\\'></i></div>'">`
        : `<div class="market-tech-avatar-ph"><i class="bi bi-person-fill"></i></div>`;
      
      const specs = (t.specializations || 'General Repair').split(',').slice(0, 3);
      
      // Rating stars
      const rating = parseFloat(t.rating_avg) || 0;
      const stars = rating > 0
        ? `<div style="display:flex;align-items:center;justify-content:center;gap:3px;margin-bottom:0.5rem;">
            ${[1,2,3,4,5].map(s => `<i class="bi bi-star${s <= Math.round(rating) ? '-fill' : ''}" style="font-size:0.7rem;color:${s <= Math.round(rating) ? '#e6a800' : 'var(--fg-border)'}"></i>`).join('')}
            <span style="font-size:0.72rem;color:var(--fg-muted);margin-left:3px;">${rating.toFixed(1)} (${t.rating_count})</span>
          </div>`
        : '';

      // Stats row
      const expYears = parseInt(t.experience_years) || 0;
      const svcCount = parseInt(t.services_count) || 0;
      const prdCount = parseInt(t.products_count) || 0;
      const statsRow = (expYears > 0 || svcCount > 0 || prdCount > 0)
        ? `<div style="display:flex;justify-content:center;gap:1.25rem;border-top:1px solid var(--fg-border);padding-top:0.75rem;margin-top:0.5rem;margin-bottom:0.75rem;">
            ${expYears > 0 ? `<div style="text-align:center;"><div style="font-size:1rem;font-weight:800;color:var(--fg-text);line-height:1;">${expYears}</div><div style="font-size:0.62rem;color:var(--fg-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-top:0.15rem;">Yrs Exp</div></div>` : ''}
            ${svcCount > 0 ? `<div style="text-align:center;"><div style="font-size:1rem;font-weight:800;color:var(--fg-text);line-height:1;">${svcCount}</div><div style="font-size:0.62rem;color:var(--fg-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-top:0.15rem;">Services</div></div>` : ''}
            ${prdCount > 0 ? `<div style="text-align:center;"><div style="font-size:1rem;font-weight:800;color:var(--fg-text);line-height:1;">${prdCount}</div><div style="font-size:0.62rem;color:var(--fg-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-top:0.15rem;">Products</div></div>` : ''}
          </div>`
        : '';

      return `
        <div class="market-tech-card" onclick="viewTechnicianDetails(${t.id})">
          ${avatar}
          <div class="market-tech-name">${escHtml(t.first_name + ' ' + t.last_name)}</div>
          <span class="market-tech-role">${escHtml(t.role_label || 'Technician')}</span>
          ${t.shop_name ? `<div class="market-tech-shop"><i class="bi bi-shop"></i>${escHtml(t.shop_name)}</div>` : ''}
          ${stars}
          <div class="market-tech-specs">
            ${specs.map(s => `<span class="market-tech-spec-pill">${escHtml(s.trim())}</span>`).join('')}
          </div>
          ${t.bio ? `<div style="font-size:0.76rem;color:var(--fg-muted);line-height:1.5;margin-bottom:0.5rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${escHtml(t.bio)}</div>` : ''}
          ${statsRow}
          <div class="market-tech-cta">
            <i class="bi bi-chat-dots"></i> Contact
          </div>
        </div>`;
    }).join('')}</div>`;
  }

  /* -- Load marketplace messages ---------------------------------- */
  function loadMarketplaceMessages() {
    fetch('api/messages', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        if (!data.success) throw new Error(data.message);
        renderMarketplaceMessages(data.conversations || []);
      })
      .catch(() => {
        const el = document.getElementById('marketMessagesGrid');
        if (el) el.innerHTML = '<div class="market-empty"><i class="bi bi-exclamation-circle"></i><p>Could not load messages.</p></div>';
      });
  }

  function renderMarketplaceMessages(conversations) {
    const el = document.getElementById('marketMessagesGrid');
    if (!el) return;

    if (!conversations.length) {
      el.innerHTML = '<div class="market-empty"><i class="bi bi-inbox"></i><p>No messages yet. Start a conversation!</p></div>';
      return;
    }

    // Show only the 5 most recent conversations
    const recent = conversations.slice(0, 5);

    el.innerHTML = `<div style="display:flex;flex-direction:column;gap:0.75rem;">${recent.map(c => {
      const initials = (c.other_user_name || 'U').split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
      const timeAgo = formatTimeAgo(c.last_message_time);
      const isUnread = c.unread_count > 0;
      
      return `
        <div class="market-message-card" onclick="window.location.href='views/user/${FGAuth.UserStore.get()?.role}/messages.php?user=${c.other_user_id}'">
          <div class="market-message-avatar">
            ${initials}
          </div>
          <div class="market-message-content">
            <div class="market-message-header">
              <span class="market-message-name">${escHtml(c.other_user_name)}</span>
              <span class="market-message-time">${timeAgo}</span>
            </div>
            <div class="market-message-preview">${escHtml(c.last_message || 'No messages yet')}</div>
          </div>
          ${isUnread ? '<div class="market-message-unread"></div>' : ''}
        </div>`;
    }).join('')}</div>`;
  }

  function formatTimeAgo(timestamp) {
    if (!timestamp) return '';
    const now = new Date();
    const then = new Date(timestamp);
    const diffMs = now - then;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;
    return then.toLocaleDateString();
  }

  /* -- Marketplace tab switching -------------------------------- */
  window.switchMarketTab = function(tab) {
    // Update tab buttons
    document.querySelectorAll('.market-tab').forEach(btn => {
      const isActive = btn.dataset.tab === tab;
      btn.style.background = isActive ? 'var(--fg-primary)' : 'transparent';
      btn.style.color = isActive ? '#fff' : 'var(--fg-text)';
      btn.style.borderColor = isActive ? 'var(--fg-primary)' : 'var(--fg-border)';
      if (isActive) btn.classList.add('active');
      else btn.classList.remove('active');
    });

    // Show/hide sections
    const productsSection = document.getElementById('marketProductsSection');
    const techniciansSection = document.getElementById('marketTechniciansSection');
    const catFilterRow = document.getElementById('catFilterRow');
    
    if (tab === 'all') {
      if (productsSection) productsSection.style.display = 'block';
      if (techniciansSection) techniciansSection.style.display = 'block';
      if (catFilterRow) catFilterRow.style.display = 'flex';
    } else if (tab === 'products') {
      if (productsSection) productsSection.style.display = 'block';
      if (techniciansSection) techniciansSection.style.display = 'none';
      if (catFilterRow) catFilterRow.style.display = 'flex';
    } else if (tab === 'technicians') {
      if (productsSection) productsSection.style.display = 'none';
      if (techniciansSection) techniciansSection.style.display = 'block';
      if (catFilterRow) catFilterRow.style.display = 'none';
    }
  };

  /* -- Category filter ------------------------------------------- */
  window.filterByCategory = function(cat) {
    // Update category buttons
    document.querySelectorAll('.cat-filter-btn').forEach(btn => {
      const isActive = btn.dataset.cat === cat;
      btn.style.background = isActive ? 'rgba(230,168,0,0.12)' : 'transparent';
      btn.style.color = isActive ? 'var(--fg-primary)' : 'var(--fg-muted)';
      btn.style.borderColor = isActive ? 'var(--fg-primary)' : 'var(--fg-border)';
    });

    // Filter product cards
    document.querySelectorAll('.market-product-card').forEach(card => {
      if (cat === 'all') {
        card.style.display = 'flex';
      } else {
        const cardCat = card.querySelector('.market-cat-badge')?.textContent?.trim() || '';
        card.style.display = cardCat === cat ? 'flex' : 'none';
      }
    });
  };

  /* -- Marketplace search/filter -------------------------------- */
  window.filterMarketplace = function() {
    const query = document.getElementById('marketplaceSearch')?.value.toLowerCase() || '';
    
    // Filter products
    document.querySelectorAll('.market-product-card').forEach(card => {
      const text = card.textContent.toLowerCase();
      card.style.display = text.includes(query) ? 'flex' : 'none';
    });
    
    // Filter technicians
    document.querySelectorAll('.market-tech-card').forEach(card => {
      const text = card.textContent.toLowerCase();
      card.style.display = text.includes(query) ? 'flex' : 'none';
    });
  };

  /* -- View product/technician details -------------------------- */
  window.viewProductDetails = function(productId) {
    // TODO: Implement product details modal or navigate to product page
    console.log('View product:', productId);
    alert('Product details coming soon! Product ID: ' + productId);
  };

  window.viewTechnicianDetails = function(techId) {
    // Remove any existing modal
    const existing = document.getElementById('_techViewModal');
    if (existing) existing.remove();

    // Build modal
    const modal = document.createElement('div');
    modal.id = '_techViewModal';
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;';
    modal.innerHTML = `
      <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:500px;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);">
        <div style="background:linear-gradient(135deg,#7c3aed,#4c1d95);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
          <div style="color:#fff;font-weight:800;font-size:1rem;">&#128295; Technician Profile</div>
          <button onclick="document.getElementById('_techViewModal').remove();document.body.style.overflow='';"
            style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;">&#x2715;</button>
        </div>
        <div id="_techViewBody" style="overflow-y:auto;flex:1;padding:1.5rem;text-align:center;">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:#8b5cf6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.75rem;"></div>
          Loading profile�
        </div>
      </div>`;
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    modal.addEventListener('click', function(e) {
      if (e.target === modal) { modal.remove(); document.body.style.overflow = ''; }
    });

    // Fetch profile
    fetch(_B + 'repair_bookings.php?action=technician_profile&id=' + techId, { credentials: 'include' })
      .then(r => r.json())
      .then(function(d) {
        const body = document.getElementById('_techViewBody');
        if (!d.success || !d.profile) {
          body.innerHTML = '<div style="color:#dc3545;padding:1rem;">Could not load profile.</div>';
          return;
        }
        const p = d.profile;
        const name = escHtml((p.shop_name || (p.first_name + ' ' + p.last_name)).trim());
        const avail = p.availability || 'available';
        const availColor = avail === 'available' ? '#28A745' : '#dc3545';
        const rating = parseFloat(p.rating_avg || 0).toFixed(1);
        const specs  = (p.specializations || '').split(',').map(s => s.trim()).filter(Boolean);

        // Avatar
        let avatarHtml;
        if (p.avatar_url) {
          avatarHtml = '<img src="' + escHtml(p.avatar_url) + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.parentElement.textContent=\'??\'">';
        } else {
          const ini = ((p.first_name||'')[0]||'') + ((p.last_name||'')[0]||'');
          avatarHtml = '<span style="font-size:1.8rem;">' + (ini.toUpperCase() || '??') + '</span>';
        }

        // Shop image
        const shopImgHtml = p.shop_image
          ? '<img src="' + escHtml(p.shop_image) + '" style="width:100%;max-height:150px;object-fit:cover;border-radius:12px;margin-bottom:1.25rem;border:1px solid var(--fg-border);" onerror="this.remove()">'
          : '';

        // Specs pills
        const specsPills = specs.map(s =>
          '<span style="display:inline-flex;background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.2);color:#8b5cf6;font-size:0.7rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:50px;">' + escHtml(s) + '</span>'
        ).join(' ');

        // Recent repairs
        let repairsHtml = '';
        if (d.reviews && d.reviews.length) {
          repairsHtml = '<div style="text-align:left;margin-top:1rem;"><div style="font-size:0.68rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#8b5cf6;margin-bottom:0.5rem;">Recent Completed Repairs</div>' +
            d.reviews.slice(0,3).map(function(rv) {
              var date = new Date(rv.created_at).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'});
              var desc = escHtml((rv.problem_desc||'').slice(0,70)) + (rv.problem_desc && rv.problem_desc.length > 70 ? '�' : '');
              return '<div style="padding:0.6rem 0.85rem;background:var(--fg-bg);border-radius:8px;border:1px solid var(--fg-border);margin-bottom:0.4rem;">' +
                '<div style="font-size:0.82rem;font-weight:700;color:var(--fg-text);">' + escHtml(rv.customer_name||'Customer') + '</div>' +
                '<div style="font-size:0.78rem;color:var(--fg-muted);margin-top:0.1rem;">' + desc + '</div>' +
                '<div style="font-size:0.7rem;color:var(--fg-muted);margin-top:0.15rem;">' + date + '</div></div>';
            }).join('') + '</div>';
        }

        // Location
        const locStr = p.shop_address || [p.general_location, p.city].filter(Boolean).join(', ') || '';
        const locHtml = locStr
          ? '<div style="font-size:0.82rem;color:var(--fg-muted);margin-top:0.4rem;display:flex;align-items:flex-start;gap:0.35rem;justify-content:center;"><span>??</span><span>' + escHtml(locStr) + '</span></div>'
          : '';

        body.innerHTML =
          '<div style="text-align:center;margin-bottom:1.25rem;">' +
            shopImgHtml +
            '<div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,rgba(139,92,246,0.2),rgba(139,92,246,0.06));border:3px solid rgba(139,92,246,0.35);display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;overflow:hidden;">' + avatarHtml + '</div>' +
            '<div style="font-size:1.05rem;font-weight:800;color:var(--fg-text);">' + name + '</div>' +
            '<div style="margin-top:0.35rem;"><span style="display:inline-flex;align-items:center;gap:0.3rem;background:' + availColor + '22;color:' + availColor + ';padding:0.15rem 0.65rem;border-radius:50px;font-size:0.7rem;font-weight:700;border:1px solid ' + availColor + '44;"><span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>' + (avail === 'available' ? 'Available' : 'Unavailable') + '</span></div>' +
            locHtml +
            (specs.length ? '<div style="margin-top:0.6rem;display:flex;flex-wrap:wrap;gap:0.3rem;justify-content:center;">' + specsPills + '</div>' : '') +
          '</div>' +

          '<div style="display:grid;grid-template-columns:repeat(3,1fr);background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;overflow:hidden;margin-bottom:1.25rem;">' +
            '<div style="text-align:center;padding:0.75rem 0.5rem;border-right:1px solid var(--fg-border);"><div style="font-size:1.1rem;font-weight:800;color:#8b5cf6;">' + (p.repairs_done || 0) + '</div><div style="font-size:0.65rem;color:var(--fg-muted);text-transform:uppercase;margin-top:0.1rem;">Repairs</div></div>' +
            '<div style="text-align:center;padding:0.75rem 0.5rem;border-right:1px solid var(--fg-border);"><div style="font-size:1.1rem;font-weight:800;color:var(--fg-primary);">' + (p.experience_years || '�') + '</div><div style="font-size:0.65rem;color:var(--fg-muted);text-transform:uppercase;margin-top:0.1rem;">Yrs Exp</div></div>' +
            '<div style="text-align:center;padding:0.75rem 0.5rem;"><div style="font-size:1.1rem;font-weight:800;color:#f59e0b;">' + rating + '</div><div style="font-size:0.65rem;color:var(--fg-muted);text-transform:uppercase;margin-top:0.1rem;">Rating</div></div>' +
          '</div>' +

          (p.bio ? '<div style="font-size:0.83rem;color:var(--fg-muted);line-height:1.55;margin-bottom:1rem;padding:0.7rem 0.85rem;background:var(--fg-bg);border-radius:10px;border:1px solid var(--fg-border);text-align:left;">' + escHtml(p.bio) + '</div>' : '') +

          repairsHtml +

          '<div style="margin-top:1.25rem;display:flex;gap:0.75rem;">' +
            '<button onclick="msgTechFromDash(' + techId + ')" style="flex:1;padding:0.75rem;border-radius:12px;background:var(--fg-primary);color:#000;border:none;font-weight:800;font-size:0.88rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;transition:opacity 0.2s;" onmouseenter="this.style.opacity=\'0.88\'" onmouseleave="this.style.opacity=\'1\'">&#128172; Message Technician</button>' +
          '</div>';
      })
      .catch(function() {
        const body = document.getElementById('_techViewBody');
        if (body) body.innerHTML = '<div style="color:#dc3545;padding:1rem;text-align:center;">Could not load profile. Please try again.</div>';
      });
  };

  window.msgTechFromDash = function(techId) {
    const modal = document.getElementById('_techViewModal');
    if (modal) { modal.remove(); document.body.style.overflow = ''; }
    window.location.href = 'views/user/customer/messages.php?with=' + techId;
  };

  function escHtml(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function statusLabelMap(s) {
    const map = {
      draft: 'Draft', verified: 'Verified',
      sent_to_owner: 'Sent to Owner',
      owner_received: 'In Shop', rejected: 'Rejected',
    };
    return map[s] || s;
  }

  /* ================================================================
     SALES PERSON DASHBOARD
  ================================================================ */
  function salesDashboard(user) {
    // Load live stats async
    setTimeout(() => loadSalesStats(), 100);

    return `
      ${sectionTitle('??', 'Sales Overview', '#3b82f6')}
      ${statsGrid(`
        ${stat('??', 'Orders Today',     '�', 'rgba(59,130,246,0.12)',  '#3b82f6',       'spStatOrders')}
        ${stat('??', 'Products Listed',  '�', 'rgba(16,185,129,0.12)', '#10b981',        'spStatProducts')}
        ${stat('??', 'Pending Requests', '�', 'rgba(230,168,0,0.12)',  'var(--fg-primary)', 'spStatRequests')}
        ${stat('??', 'Total Revenue',    '�', 'rgba(40,167,69,0.12)',  '#28A745',        'spStatRevenue')}
      `)}
      ${sectionTitle('?', 'Quick Actions', '#3b82f6')}
      ${actionsGrid(`
        ${action('??', 'My Products',     'Upload and manage products visible to customers.',  '#10b981',           'views/user/sales_person/products.php')}
        ${action('??', 'Customer Orders', 'View and track customer orders.',                   '#3b82f6',           'views/user/sales_person/orders.php')}
        ${action('??', 'Inventory',       'View products available from supervisor.',          '#6366f1',           'views/user/sales_person/inventory.php')}
        ${action('??', 'Supply Requests', 'Request additional product supply from supervisor.','var(--fg-primary)', 'views/user/sales_person/supply-requests.php')}
        ${action('??', 'My Profile',      'View and update your account information.',         '#2a9d8f',           'views/user/sales_person/profile.php')}
        ${action('??', 'Full Dashboard',  'Go to your complete sales person dashboard.',       '#856404',           'views/user/sales_person/dashboard.php')}
      `)}
      ${sectionTitle('??', 'Recent Orders', '#2a9d8f')}
      <div id="spRecentOrders" style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.5rem;text-align:center;color:var(--fg-muted);">
        <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
        Loading recent orders�
      </div>

      ${sectionTitle('??', 'Purchase History', '#10b981')}
      <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;margin-bottom:1.5rem;">
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:0.84rem;" id="spPurchaseHistoryTable">
            <thead>
              <tr style="background:var(--fg-bg);">
                <th style="padding:0.6rem 0.85rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);white-space:nowrap;">Order #</th>
                <th style="padding:0.6rem 0.85rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Date</th>
                <th style="padding:0.6rem 0.85rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Customer</th>
                <th style="padding:0.6rem 0.85rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Product</th>
                <th style="padding:0.6rem 0.85rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);text-align:center;">Qty</th>
                <th style="padding:0.6rem 0.85rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Total</th>
                <th style="padding:0.6rem 0.85rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Payment</th>
                <th style="padding:0.6rem 0.85rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Status</th>
                <th style="padding:0.6rem 0.85rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);text-align:center;">Receipt</th>
              </tr>
            </thead>
            <tbody id="spPurchaseHistoryBody">
              <tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#10b981;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
                Loading�
              </td></tr>
            </tbody>
          </table>
        </div>
        <div style="text-align:right;padding:0.75rem 1rem;border-top:1px solid var(--fg-border);">
          <a href="views/user/sales_person/orders.php" style="font-size:0.82rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All Orders ?</a>
        </div>
      </div>

      <!-- Receipt Modal (injected into body by JS) -->
      <style>@keyframes spin { to { transform: rotate(360deg); } }</style>`;
  }

  function loadSalesStats() {
    // Products
    fetch(_B + 'sales_products.php?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success && d.stats) {
          const el = document.getElementById('spStatProducts');
          if (el) el.textContent = d.stats.total || 0;
        }
      }).catch(() => {});

    // Orders stats
    fetch(_B + 'sales_orders.php?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success && d.stats) {
          const elO = document.getElementById('spStatOrders');
          if (elO) elO.textContent = d.stats.orders_today || 0;
          const elR = document.getElementById('spStatRevenue');
          if (elR) {
            const rev = parseFloat(d.stats.total_revenue || 0);
            elR.textContent = '?' + rev.toLocaleString('en-PH', {minimumFractionDigits: 0});
          }
        }
      }).catch(() => {});

    // Supply requests
    fetch(_B + 'sales_supply_requests.php?action=stats', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success && d.stats) {
          const el = document.getElementById('spStatRequests');
          if (el) el.textContent = d.stats.pending || 0;
        }
      }).catch(() => {});

    // Recent orders
    fetch(_B + 'sales_orders.php?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const el = document.getElementById('spRecentOrders');
        if (!el) return;
        if (!d.success || !d.orders || !d.orders.length) {
          el.innerHTML = '<p style="margin:0;font-size:0.88rem;">No orders yet. <a href="views/user/sales_person/orders.php" style="color:var(--fg-primary);font-weight:600;">View Orders ?</a></p>';
          return;
        }
        const rows = d.orders.slice(0, 5).map(o => {
          const date     = new Date(o.created_at).toLocaleDateString('en-PH');
          const total    = parseFloat(o.total_amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});
          const customer = escHtml(((o.first_name || '') + ' ' + (o.last_name || '')).trim() || 'Unknown');
          const statusMap = {
            pending:    { bg:'rgba(230,168,0,0.15)',   color:'#c98f00', label:'Pending'   },
            processing: { bg:'rgba(59,130,246,0.15)',  color:'#3b82f6', label:'Shipped'   },
            completed:  { bg:'rgba(40,167,69,0.15)',   color:'#28A745', label:'Completed' },
            cancelled:  { bg:'rgba(220,53,69,0.15)',   color:'#dc3545', label:'Cancelled' },
          };
          const s = statusMap[o.status] || { bg:'rgba(108,117,125,0.15)', color:'#6C757D', label: o.status };
          return `<tr style="border-bottom:1px solid var(--fg-border);">
            <td style="padding:0.65rem 0.75rem;font-weight:700;color:#3b82f6;white-space:nowrap;">#${o.id}</td>
            <td style="padding:0.65rem 0.75rem;font-size:0.85rem;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(o.product_name || '�')}</td>
            <td style="padding:0.65rem 0.75rem;font-size:0.85rem;">${customer}</td>
            <td style="padding:0.65rem 0.75rem;font-weight:700;white-space:nowrap;">?${total}</td>
            <td style="padding:0.65rem 0.75rem;white-space:nowrap;"><span style="background:${s.bg};color:${s.color};padding:0.2rem 0.6rem;border-radius:20px;font-size:0.72rem;font-weight:700;">${s.label}</span></td>
            <td style="padding:0.65rem 0.75rem;color:var(--fg-muted);font-size:0.82rem;white-space:nowrap;">${date}</td>
          </tr>`;
        }).join('');
        el.innerHTML = `<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
          <thead><tr style="background:var(--fg-bg);">
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);white-space:nowrap;">Order</th>
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Product</th>
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Customer</th>
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Total</th>
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Status</th>
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Date</th>
          </tr></thead>
          <tbody>${rows}</tbody>
        </table></div>
        <div style="text-align:right;margin-top:0.75rem;"><a href="views/user/sales_person/orders.php" style="font-size:0.82rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All Orders ?</a></div>`;
      }).catch(() => {
        const el = document.getElementById('spRecentOrders');
        if (el) el.innerHTML = '<p style="margin:0;font-size:0.88rem;color:var(--fg-muted);">Could not load orders.</p>';
      });

    // Purchase History
    fetch(_B + 'sales_orders.php?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        const tbody = document.getElementById('spPurchaseHistoryBody');
        if (!tbody) return;
        if (!d.success || !d.orders || !d.orders.length) {
          tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--fg-muted);">No purchase history yet.</td></tr>';
          return;
        }
        // cache for receipt modal
        window.spOrdersCache = d.orders;
        const statusMap   = { pending:'rgba(230,168,0,0.15)', processing:'rgba(59,130,246,0.15)', completed:'rgba(40,167,69,0.15)', cancelled:'rgba(220,53,69,0.15)' };
        const statusColor = { pending:'#c98f00', processing:'#3b82f6', completed:'#28A745', cancelled:'#dc3545' };
        const statusLabel = { pending:'Pending', processing:'Shipped', completed:'Completed', cancelled:'Cancelled' };
        const payMap      = { cod:'COD', gcash:'GCash', paymongo:'Card/Online', online:'Online' };
        tbody.innerHTML = d.orders.slice(0, 10).map(o => {
          const total    = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
          const customer = escHtml(((o.first_name || '') + ' ' + (o.last_name || '')).trim() || 'N/A');
          const date     = new Date(o.created_at).toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' });
          const sBg      = statusMap[o.status]   || 'rgba(108,117,125,0.15)';
          const sColor   = statusColor[o.status] || '#6c757d';
          const sLabel   = statusLabel[o.status] || o.status;
          const pay      = payMap[o.payment_method] || escHtml(o.payment_method || 'N/A');
          return `<tr style="border-bottom:1px solid var(--fg-border);">
            <td style="padding:0.6rem 0.85rem;font-weight:700;color:#10b981;white-space:nowrap;">#${o.id}</td>
            <td style="padding:0.6rem 0.85rem;font-size:0.8rem;color:var(--fg-muted);white-space:nowrap;">${date}</td>
            <td style="padding:0.6rem 0.85rem;">
              <div style="font-weight:600;font-size:0.85rem;">${customer}</div>
              <div style="font-size:0.73rem;color:var(--fg-muted);">${escHtml(o.customer_email||'')}</div>
            </td>
            <td style="padding:0.6rem 0.85rem;font-size:0.85rem;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(o.product_name||'�')}</td>
            <td style="padding:0.6rem 0.85rem;text-align:center;">${o.quantity||1}</td>
            <td style="padding:0.6rem 0.85rem;font-weight:700;white-space:nowrap;">?${total}</td>
            <td style="padding:0.6rem 0.85rem;"><span style="font-size:0.73rem;background:rgba(59,130,246,0.1);color:#3b82f6;padding:0.15rem 0.5rem;border-radius:20px;font-weight:600;">${pay}</span></td>
            <td style="padding:0.6rem 0.85rem;white-space:nowrap;"><span style="background:${sBg};color:${sColor};padding:0.2rem 0.6rem;border-radius:20px;font-size:0.72rem;font-weight:700;">${sLabel}</span></td>
            <td style="padding:0.6rem 0.85rem;text-align:center;">
              <button onclick="spOpenReceipt(${o.id})"
                style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:8px;padding:0.3rem 0.7rem;font-size:0.75rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.3rem;"
                onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
                ?? View
              </button>
            </td>
          </tr>`;
        }).join('');

        // Inject receipt modal into page if not already there
        if (!document.getElementById('spReceiptModal')) {
          const modalEl = document.createElement('div');
          modalEl.id = 'spReceiptModal';
          modalEl.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;';
          modalEl.innerHTML = `
            <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:500px;overflow:hidden;box-shadow:0 32px 80px rgba(0,0,0,0.5);display:flex;flex-direction:column;max-height:92vh;">
              <div style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                <div>
                  <div style="color:#fff;font-weight:800;font-size:1rem;display:flex;align-items:center;gap:0.4rem;">?? <span>Order Receipt</span></div>
                  <div style="color:rgba(255,255,255,0.75);font-size:0.75rem;margin-top:0.15rem;" id="spReceiptDate"></div>
                </div>
                <div style="display:flex;gap:0.4rem;align-items:center;">
                  <button onclick="spPrintReceipt()"
                    style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.35);border-radius:8px;padding:0.35rem 0.9rem;font-size:0.78rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.3rem;transition:background 0.2s;"
                    onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">
                    ??? Print
                  </button>
                  <button onclick="document.getElementById('spReceiptModal').style.display='none'"
                    style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.35);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;font-weight:700;transition:background 0.2s;flex-shrink:0;"
                    onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">?</button>
                </div>
              </div>
              <div id="spReceiptBody" style="padding:1.35rem;overflow-y:auto;flex:1;"></div>
            </div>`;
          document.body.appendChild(modalEl);
          modalEl.addEventListener('click', function(e) {
            if (e.target === modalEl) modalEl.style.display = 'none';
          });
        }
      }).catch(() => {
        const tbody = document.getElementById('spPurchaseHistoryBody');
        if (tbody) tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:1.5rem;color:var(--fg-muted);">Could not load purchase history.</td></tr>';
      });
  }

  /* ================================================================
     CUSTOMER DASHBOARD
  ================================================================ */
  function customerDashboard(user) {
    // Redirect to the full customer dashboard
    setTimeout(() => {
      window.location.href = 'views/user/customer/dashboard.php';
    }, 50);
    return `
      <div style="text-align:center;padding:3rem;color:var(--fg-muted);">
        <div style="width:32px;height:32px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 1rem;"></div>
        Redirecting to your dashboard�
      </div>
      <style>@keyframes spin{to{transform:rotate(360deg);}}</style>`;
  }

  /* ================================================================
     SHARED COMPONENTS
  ================================================================ */
  function sectionTitle(icon, title, color) {
    return `
      <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;margin-top:0.5rem;">
        <span style="font-size:1.2rem;line-height:1;">${icon}</span>
        <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">${title}</h5>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
      </div>`;
  }

  function stat(icon, label, value, bg, color, id) {
    const idAttr = id ? `id="${id}"` : '';
    return `
      <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.25rem 1rem;text-align:center;box-shadow:var(--fg-shadow);">
        <div style="width:52px;height:52px;border-radius:12px;background:${bg};color:${color};display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 0.6rem;">${icon}</div>
        <div ${idAttr} style="font-size:1.6rem;font-weight:800;color:${color};line-height:1;">${value}</div>
        <div style="font-size:0.75rem;color:var(--fg-muted);font-weight:600;margin-top:0.3rem;">${label}</div>
      </div>`;
  }

  function statsGrid(items) {
    return `<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:0.75rem;margin-bottom:1.5rem;">${items}</div>`;
  }

  function action(icon, title, desc, color, href) {
    const tag   = href && href !== '#' ? 'a' : 'div';
    const extra = href && href !== '#' ? `href="${href}"` : '';
    return `
      <${tag} ${extra} style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.25rem;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s,border-color 0.2s;display:block;text-decoration:none;box-shadow:var(--fg-shadow);"
           role="button" tabindex="0"
           onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 28px rgba(0,0,0,0.2)';this.style.borderColor='${color}'"
           onmouseleave="this.style.transform='';this.style.boxShadow='var(--fg-shadow)';this.style.borderColor='var(--fg-border)'">
        <div style="width:48px;height:48px;border-radius:12px;background:${color}22;color:${color};display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:0.75rem;">${icon}</div>
        <div style="font-weight:700;font-size:0.92rem;color:var(--fg-text);margin-bottom:0.25rem;">${title}</div>
        <div style="font-size:0.8rem;color:var(--fg-muted);line-height:1.5;">${desc}</div>
      </${tag}>`;
  }

  function actionsGrid(items) {
    return `<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.75rem;margin-bottom:1.5rem;">${items}</div>`;
  }

  function statusBadge(status) {
    const map = {
      'Completed':   { bg:'rgba(40,167,69,0.12)',  color:'#28A745' },
      'In Progress': { bg:'rgba(230,168,0,0.12)',  color:'#c98f00' },
      'Pending':     { bg:'rgba(255,193,7,0.15)',  color:'#856404' },
      'Shipped':     { bg:'rgba(59,130,246,0.12)', color:'#3b82f6' },
      'Processing':  { bg:'rgba(99,102,241,0.12)', color:'#6366f1' },
      'Delivered':   { bg:'rgba(40,167,69,0.12)',  color:'#28A745' },
      'Paid':        { bg:'rgba(40,167,69,0.12)',  color:'#28A745' },
    };
    const s = map[status] || { bg:'rgba(108,117,125,0.12)', color:'#6C757D' };
    return `<span style="background:${s.bg};color:${s.color};padding:0.25rem 0.7rem;border-radius:20px;font-size:0.75rem;font-weight:700;">${status}</span>`;
  }

  function bookingTable(rows) {
    const trs = rows.map(r => `
      <tr>
        <td style="font-weight:700;color:var(--fg-primary);">${r.id}</td>
        <td>${r.customer}</td>
        <td style="color:var(--fg-muted);">${r.device}</td>
        <td>${r.service}</td>
        <td>${statusBadge(r.status)}</td>
        <td style="font-weight:700;">${r.amount}</td>
      </tr>`).join('');
    return tableWrapper(['ID','Customer / Device','Device','Service','Status','Amount'], trs);
  }

  function orderTable(rows) {
    const trs = rows.map(r => `
      <tr>
        <td style="font-weight:700;color:var(--fg-primary);">${r.id}</td>
        <td>${r.shop}</td>
        <td>${r.product}</td>
        <td style="text-align:center;">${r.qty}</td>
        <td>${statusBadge(r.status)}</td>
        <td style="font-weight:700;">${r.total}</td>
      </tr>`).join('');
    return tableWrapper(['Order ID','Shop','Product','Qty','Status','Total'], trs);
  }

  function salesTable(rows) {
    const trs = rows.map(r => `
      <tr>
        <td style="font-weight:700;color:var(--fg-primary);">${r.id}</td>
        <td>${r.customer}</td>
        <td>${r.service}</td>
        <td style="font-weight:700;">${r.amount}</td>
        <td>${statusBadge(r.status)}</td>
        <td style="color:var(--fg-muted);">${r.date}</td>
      </tr>`).join('');
    return tableWrapper(['TXN ID','Customer','Service','Amount','Status','Date'], trs);
  }

  function tableWrapper(headers, trs) {
    const ths = headers.map(h => `<th style="font-size:0.78rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;letter-spacing:0.5px;padding:0.75rem 1rem;border-bottom:1px solid var(--fg-border);">${h}</th>`).join('');
    return `
      <div class="dashboard-card mb-4" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;">
            <thead><tr style="background:var(--fg-bg);">${ths}</tr></thead>
            <tbody>
              ${trs.replace(/<tr>/g, '<tr style="border-bottom:1px solid var(--fg-border);">').replace(/<td/g, '<td style="padding:0.75rem 1rem;font-size:0.88rem;color:var(--fg-text);" ')}
            </tbody>
          </table>
        </div>
      </div>`;
  }
});

/* -- Sales Person Receipt Modal (global) -------------------------- */
window.spOpenReceipt = function(orderId) {
  const orders = window.spOrdersCache || [];
  const o = orders.find(x => x.id == orderId);
  if (!o) return;

  const modal = document.getElementById('spReceiptModal');
  if (!modal) return;

  const esc = s => s ? String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') : '';
  const customer  = esc(((o.first_name||'') + ' ' + (o.last_name||'')).trim() || 'N/A');
  const dateStr   = new Date(o.created_at).toLocaleString('en-PH', { dateStyle:'long', timeStyle:'short' });
  const total     = parseFloat(o.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
  const unitPrice = parseFloat(o.unit_price   || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
  const qty       = parseInt(o.quantity || 1);
  const payMap    = { cod:'Cash on Delivery', gcash:'GCash', paymongo:'Card / Online', online:'Online' };
  const payLabel  = payMap[o.payment_method] || esc(o.payment_method || 'N/A');
  const sLabel    = { pending:'Pending', processing:'Shipped', completed:'Completed', cancelled:'Cancelled' };
  const sColor    = { pending:'#c98f00', processing:'#3b82f6', completed:'#28A745', cancelled:'#dc3545' };
  const sBg       = { pending:'rgba(201,143,0,0.15)', processing:'rgba(59,130,246,0.15)', completed:'rgba(40,167,69,0.15)', cancelled:'rgba(220,53,69,0.15)' };
  const addrParts = [o.address_line, o.barangay, o.city, o.province, o.zip_code].filter(Boolean);
  const address   = addrParts.length ? esc(addrParts.join(', ')) : '�';
  const sc        = sColor[o.status] || '#6c757d';
  const sb        = sBg[o.status]    || 'rgba(108,117,125,0.15)';

  document.getElementById('spReceiptDate').textContent = dateStr;
  document.getElementById('spReceiptBody').innerHTML = `
    <div id="spPrintContent">

      <!-- Brand header -->
      <div style="text-align:center;padding-bottom:1.1rem;margin-bottom:1.1rem;border-bottom:2px dashed var(--fg-border);">
        <div style="display:inline-flex;align-items:center;gap:0.5rem;margin-bottom:0.35rem;">
          <span style="font-size:1.6rem;line-height:1;">??</span>
          <span style="font-size:1.3rem;font-weight:900;color:#10b981;letter-spacing:-0.5px;">Fix&amp;Go</span>
        </div>
        <div style="font-size:0.7rem;font-weight:600;color:var(--fg-muted);text-transform:uppercase;letter-spacing:1px;">Official Order Receipt</div>
      </div>

      <!-- Order ID + status -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.1rem;flex-wrap:wrap;gap:0.5rem;">
        <div>
          <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);margin-bottom:0.2rem;">Order</div>
          <div style="font-size:1.25rem;font-weight:900;color:var(--fg-text);">#${o.id}</div>
          <div style="font-size:0.75rem;color:var(--fg-muted);margin-top:0.1rem;">${dateStr}</div>
        </div>
        <span style="background:${sb};color:${sc};padding:0.45rem 1.1rem;border-radius:50px;font-weight:800;font-size:0.82rem;border:1.5px solid ${sc}44;white-space:nowrap;">
          ${sLabel[o.status] || o.status}
        </span>
      </div>

      <!-- Customer card -->
      <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;padding:1rem 1.1rem;margin-bottom:1.1rem;">
        <div style="font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#10b981;margin-bottom:0.6rem;">?? Customer</div>
        <div style="font-weight:800;font-size:1rem;color:var(--fg-text);margin-bottom:0.3rem;">${customer}</div>
        ${o.customer_email ? `
        <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.82rem;color:var(--fg-muted);margin-bottom:0.2rem;">
          <span>??</span><span>${esc(o.customer_email)}</span>
        </div>` : ''}
        ${o.customer_phone ? `
        <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.82rem;color:var(--fg-muted);margin-bottom:0.2rem;">
          <span>??</span><span>${esc(o.customer_phone)}</span>
        </div>` : ''}
        <div style="display:flex;align-items:flex-start;gap:0.4rem;font-size:0.82rem;color:var(--fg-muted);margin-top:0.1rem;">
          <span style="flex-shrink:0;margin-top:1px;">??</span><span>${address}</span>
        </div>
      </div>

      <!-- Product table -->
      <div style="margin-bottom:1.1rem;">
        <div style="font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#10b981;margin-bottom:0.6rem;">?? Order Details</div>
        <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;overflow:hidden;">
          <table style="width:100%;border-collapse:collapse;font-size:0.83rem;">
            <thead>
              <tr>
                <th style="padding:0.6rem 0.85rem;text-align:left;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Product</th>
                <th style="padding:0.6rem 0.85rem;text-align:center;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Qty</th>
                <th style="padding:0.6rem 0.85rem;text-align:right;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Unit Price</th>
                <th style="padding:0.6rem 0.85rem;text-align:right;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="padding:0.75rem 0.85rem;font-weight:700;color:var(--fg-text);">${esc(o.product_name||'�')}</td>
                <td style="padding:0.75rem 0.85rem;text-align:center;color:var(--fg-text);">${qty}</td>
                <td style="padding:0.75rem 0.85rem;text-align:right;color:var(--fg-muted);">?${unitPrice}</td>
                <td style="padding:0.75rem 0.85rem;text-align:right;font-weight:800;color:var(--fg-text);">?${total}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Totals -->
      <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;padding:0.85rem 1.1rem;margin-bottom:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.85rem;margin-bottom:0.55rem;">
          <span style="color:var(--fg-muted);">Subtotal</span>
          <span style="color:var(--fg-text);font-weight:600;">?${total}</span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.85rem;padding-bottom:0.55rem;border-bottom:1px solid var(--fg-border);">
          <span style="color:var(--fg-muted);">Payment Method</span>
          <span style="background:rgba(59,130,246,0.12);color:#3b82f6;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.75rem;font-weight:700;">${payLabel}</span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:0.55rem;">
          <span style="font-size:0.95rem;font-weight:800;color:var(--fg-text);">Total</span>
          <span style="font-size:1.2rem;font-weight:900;color:#10b981;">?${total}</span>
        </div>
      </div>

      ${o.notes ? `
      <div style="background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-left:3px solid #3b82f6;border-radius:0 10px 10px 0;padding:0.65rem 0.9rem;font-size:0.82rem;color:var(--fg-text);margin-bottom:1rem;">
        <span style="font-weight:700;color:#3b82f6;">Note: </span>${esc(o.notes)}
      </div>` : ''}

      <!-- Footer -->
      <div style="text-align:center;padding-top:0.85rem;border-top:2px dashed var(--fg-border);">
        <div style="font-size:1.1rem;margin-bottom:0.25rem;">??</div>
        <div style="font-size:0.8rem;font-weight:700;color:var(--fg-text);">Thank you for your purchase!</div>
        <div style="font-size:0.72rem;color:var(--fg-muted);margin-top:0.15rem;">Fix&amp;Go � Your trusted repair partner</div>
      </div>

    </div>`;

  modal.style.display = 'flex';
};

window.spPrintReceipt = function() {
  const content = document.getElementById('spPrintContent');
  if (!content) return;
  const win = window.open('', '_blank', 'width=560,height=760');
  win.document.write(`<!DOCTYPE html><html><head><title>Order Receipt - Fix&Go</title>
    <style>
      * { box-sizing: border-box; margin: 0; padding: 0; }
      body { font-family: 'Segoe UI', Arial, sans-serif; background: #fff; color: #111; padding: 24px; font-size: 14px; }
      table { width: 100%; border-collapse: collapse; }
      th, td { padding: 8px 10px; }
      @media print { body { padding: 12px; } }
    </style>
  </head><body>${content.innerHTML}</body></html>`);
  win.document.close();
  win.focus();
  setTimeout(function() { win.print(); win.close(); }, 500);
};

