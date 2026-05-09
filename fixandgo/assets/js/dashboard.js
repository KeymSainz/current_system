/**
 * Fix&Go — Dashboard Logic
 * Roles: owner | supervisor | supplier | sales_person | customer
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  fetch('backend/session-user.php')
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

  /* ── Render ─────────────────────────────────────────────────────── */
  function renderDashboard(user) {
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
      browseShopBtn.href = 'index.html?browse=1';
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

    // Navbar name
    const navName = document.getElementById('navUserName');
    if (navName) navName.textContent = user.firstName + ' ' + user.lastName;

    // Role badge
    const navBadge = document.getElementById('navRoleBadge');
    if (navBadge) {
      navBadge.className = 'role-badge ' + user.role;
      const labels = {
        owner:       '🏪 Owner',
        supervisor:  '🛡️ Supervisor',
        supplier:    '📦 Supplier',
        sales_person:'💼 Sales Person',
        customer:    '👤 Customer',
      };
      navBadge.textContent = labels[user.role] || user.role;
    }

    // Welcome banner
    const wTitle = document.getElementById('welcomeTitle');
    if (wTitle) wTitle.textContent = 'Welcome back, ' + user.firstName + '!';

    const wSub = document.getElementById('welcomeSubtitle');
    if (wSub) {
      const subs = {
        owner:       'Manage your repair shop, staff, inventory, and performance.',
        supervisor:  'Monitor operations, staff activity, and service quality.',
        supplier:    'Manage your product listings, orders, and deliveries.',
        sales_person:'Track your leads, sales targets, and customer deals.',
        customer:    'Book repairs, track your devices, and view your history.',
      };
      wSub.textContent = subs[user.role] || 'Manage your Fix&Go account.';
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
        console.log('Logout button clicked');
        
        fetch('backend/logout.php', { method: 'POST' })
          .catch(function(error) {
            console.log('Logout error:', error);
          })
          .finally(function () {
            console.log('Clearing user store and redirecting to index.html?logout=true');
            FGAuth.UserStore.clear();
            window.location.href = 'index.html?logout=true';
          });
      });
      console.log('Logout event listener attached');
    } else {
      console.error('Logout button not found!');
    }
  }

  /* ── Route by role ──────────────────────────────────────────────── */
  function getRoleContent(role, user) {
    switch (role) {
      case 'owner':        return ownerDashboard(user);
      case 'supervisor':   return supervisorDashboard(user);
      case 'supplier':     return supplierDashboard(user);
      case 'sales_person': return salesDashboard(user);
      default:             return customerDashboard(user);
    }
  }

  /* ================================================================
     OWNER DASHBOARD
  ================================================================ */
  function ownerDashboard(user) {
    // Load pending submissions async
    setTimeout(() => loadOwnerSubmissions(), 100);

    return `
      ${sectionTitle('🏪', 'Shop Overview', 'var(--fg-primary)')}
      ${statsGrid(`
        ${stat('📋', 'Total Bookings',  '128', 'rgba(230,168,0,0.12)',   'var(--fg-primary)')}
        ${stat('✅', 'Completed',       '94',  'rgba(40,167,69,0.12)',   '#28A745')}
        ${stat('⏳', 'Pending',         '21',  'rgba(255,193,7,0.15)',   '#856404')}
        ${stat('📦', 'Pending Products','—',   'rgba(59,130,246,0.12)',  '#3b82f6', 'statPendingProducts')}
      `)}
      ${sectionTitle('💰', 'Revenue Summary', '#28A745')}
      ${statsGrid(`
        ${stat('💵', 'Today',     '₱320',  'rgba(40,167,69,0.12)',  '#28A745')}
        ${stat('📅', 'This Week', '₱1,840','rgba(40,167,69,0.12)',  '#28A745')}
        ${stat('🗓️', 'This Month','₱7,200','rgba(40,167,69,0.12)',  '#28A745')}
        ${stat('📈', 'Growth',    '+12%',  'rgba(99,102,241,0.12)', '#6366f1')}
      `)}
      ${sectionTitle('⚡', 'Quick Actions', '#6366f1')}
      ${actionsGrid(`
        ${action('➕', 'Add Staff',       'Invite technicians, supervisors, or sales staff.', 'var(--fg-primary)', 'views/user/owner/staff.html')}
        ${action('📊', 'View Reports',    'Revenue, performance, and booking analytics.',     '#28A745',           'views/user/owner/sales-report.html')}
        ${action('📋', 'Supervisor Reports', 'View monthly reports from your supervisor.',    '#3b82f6',           'views/user/owner/supervisor-reports.html')}
        ${action('🛒', 'Manage Inventory','Track parts, accessories, and stock levels.',      '#6366f1',           'views/user/owner/products.html')}
        ${action('📋', 'Bookings',        'View and manage all customer repair bookings.',    '#2a9d8f',           'views/user/owner/orders.html')}
        ${action('📣', 'Promotions',      'Create discount offers and loyalty rewards.',      '#856404')}
        ${action('💬', 'Messages',        'Chat with customers and technicians.',             '#3b82f6',           'views/user/owner/messages.html')}
        ${action('🧪', 'Test Payment',    'Run a dummy PayMongo checkout to verify your API keys.', '#dc3545', 'paymongo-test.html')}
      `)}

      <!-- ── Pending Supplier Submissions ── -->
      <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;margin-top:0.5rem;">
        <span style="font-size:1.2rem;line-height:1;">📬</span>
        <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">Supplier Product Submissions</h5>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <span id="submissionCountBadge" style="display:none;background:rgba(59,130,246,0.12);color:#3b82f6;font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:20px;"></span>
      </div>
      <div id="ownerSubmissionsGrid" style="margin-bottom:2rem;">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading submissions…
        </div>
      </div>

      <!-- ── Received Products ── -->
      <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;margin-top:0.5rem;">
        <span style="font-size:1.2rem;line-height:1;">✅</span>
        <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">Accepted Products (In Shop)</h5>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <a href="views/user/owner/products.html" style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All →</a>
      </div>
      <div id="ownerReceivedGrid" style="margin-bottom:2rem;">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading…
        </div>
      </div>

      ${sectionTitle('📋', 'Recent Bookings', '#2a9d8f')}
      ${bookingTable([
        { id:'#1042', customer:'Maria Santos',   device:'iPhone 14',     service:'Screen Repair',     status:'In Progress', amount:'₱89' },
        { id:'#1041', customer:'Juan Dela Cruz', device:'Samsung S23',   service:'Battery Replace',   status:'Completed',   amount:'₱45' },
        { id:'#1040', customer:'Ana Reyes',      device:'Xiaomi 12',     service:'Water Damage',      status:'Pending',     amount:'₱120' },
        { id:'#1039', customer:'Pedro Lim',      device:'iPhone 13 Pro', service:'Charging Port Fix', status:'Completed',   amount:'₱60' },
      ])}

      <style>
        @keyframes spin { to { transform: rotate(360deg); } }
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

  /* ── Load owner submissions ─────────────────────────────────── */
  function loadOwnerSubmissions() {
    // Load pending submissions
    fetch('backend/owner_products.php?action=submissions')
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
    fetch('backend/owner_products.php?action=received')
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
              <span class="sub-price">₱${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
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
                onclick="ownerBuyProducts(${JSON.stringify(availableIds).replace(/"/g,'&quot;')}, '${escHtml(supplierName)}')"
                style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 1rem;border-radius:8px;background:var(--fg-primary);color:#fff;border:none;font-size:0.8rem;font-weight:700;cursor:pointer;transition:all 0.2s;">
                <i class="bi bi-cart-fill"></i> Buy All from ${escHtml(supplierName)}
                <span style="background:rgba(255,255,255,0.25);padding:0.1rem 0.45rem;border-radius:6px;font-size:0.72rem;">
                  ₱${availableTotal.toLocaleString('en-PH',{minimumFractionDigits:2})}
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
                      <span class="sub-price">₱${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
                      <span class="sub-qty" style="${isOutOfStock ? 'color:#dc3545;font-weight:700;' : ''}">${p.qty > 0 ? p.qty + ' in stock' : 'Out of stock'}</span>
                    </div>
                    <button ${isOutOfStock ? 'disabled' : `onclick="ownerBuyProducts([${p.id}], '${escHtml(supplierName)}')"`}
                      style="margin-top:0.6rem;width:100%;padding:0.4rem;border-radius:8px;background:${isOutOfStock ? '#e0e0e0' : 'rgba(230,168,0,0.1)'};color:${isOutOfStock ? '#999' : 'var(--fg-primary)'};border:1.5px solid ${isOutOfStock ? '#e0e0e0' : 'rgba(230,168,0,0.3)'};font-size:0.78rem;font-weight:700;cursor:${isOutOfStock ? 'not-allowed' : 'pointer'};transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:0.35rem;opacity:${isOutOfStock ? '0.6' : '1'};"
                      ${isOutOfStock ? '' : `onmouseenter="this.style.background='var(--fg-primary)';this.style.color='#fff'" onmouseleave="this.style.background='rgba(230,168,0,0.1)';this.style.color='var(--fg-primary)'"`}>
                      <i class="bi bi-${isOutOfStock ? 'ban' : 'cart-fill'}"></i> ${isOutOfStock ? 'Out of Stock' : 'Buy This'}
                    </button>
                  </div>
                </div>`;
            }).join('')}
          </div>
        </div>`;
    });

    el.innerHTML = html;
  }

  // ── Buy products via PayMongo ────────────────────────────────
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
              <span style="font-weight:600;color:var(--fg-text);">₱${unitPrice.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
              <span style="color:var(--fg-muted);font-size:0.85rem;">Quantity:</span>
              <span style="font-weight:600;color:var(--fg-text);" id="qtyDisplay">1</span>
            </div>
            <div style="height:1px;background:var(--fg-border);margin:0.75rem 0;"></div>
            <div style="display:flex;justify-content:space-between;">
              <span style="font-weight:700;color:var(--fg-text);">Total:</span>
              <span style="font-size:1.3rem;font-weight:800;color:var(--fg-primary);" id="totalDisplay">₱${unitPrice.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
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
    document.getElementById('totalDisplay').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits:2});
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
      <div style="font-size:1rem;font-weight:600;">Creating payment session…</div>
    `;
    document.body.appendChild(loadingEl);

    fetch('backend/paymongo.php', {
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
      <div style="font-size:1rem;font-weight:600;">Creating payment session…</div>
    `;
    document.body.appendChild(loadingEl);

    fetch('backend/paymongo.php', {
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
    fetch('backend/owner_products.php', {
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
      ${sectionTitle('🛡️', 'Operations Overview', '#6366f1')}
      ${statsGrid(`
        ${stat('🔧', 'Active Repairs',  '14', 'rgba(230,168,0,0.12)',  'var(--fg-primary)')}
        ${stat('👥', 'Staff On Duty',   '6',  'rgba(99,102,241,0.12)', '#6366f1')}
        ${stat('⚠️', 'Issues Flagged', '2',  'rgba(220,53,69,0.12)',  '#dc3545')}
        ${stat('✅', 'Resolved Today',  '9',  'rgba(40,167,69,0.12)',  '#28A745')}
      `)}
      ${sectionTitle('👨‍💼', 'Staff Performance', '#2a9d8f')}
      ${statsGrid(`
        ${stat('⭐', 'Avg Rating',       '4.7', 'rgba(78,205,196,0.15)', '#2a9d8f')}
        ${stat('⏱️', 'Avg Repair Time', '1.4h','rgba(255,193,7,0.15)',  '#856404')}
        ${stat('📦', 'Parts Used Today', '23',  'rgba(99,102,241,0.12)', '#6366f1')}
        ${stat('🔁', 'Rework Rate',      '3%',  'rgba(220,53,69,0.12)',  '#dc3545')}
      `)}
      ${sectionTitle('⚡', 'Quick Actions', '#6366f1')}
      ${actionsGrid(`
        ${action('📦', 'Product Supply Inventory', 'Manage all products and stock levels.',              'var(--fg-primary)', 'views/user/supervisor/inventory.html')}
        ${action('⚠️', 'Flagged Issues',           'Review and resolve escalated complaints.',           '#dc3545')}
        ${action('📊', 'Daily Report',             'Generate and export today\'s operations report.',    '#28A745', 'views/user/supervisor/reports.html')}
        ${action('📦', 'Parts Request',            'Approve or reject parts requests from staff.',       '#6366f1')}
      `)}
      ${sectionTitle('📋', 'Active Jobs', '#2a9d8f')}
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
    // Kick off async load of shop + my products
    setTimeout(() => loadSupplierShopView(), 100);

    return `
      ${sectionTitle('📦', 'Supply Overview', '#10b981')}
      ${statsGrid(`
        ${stat('📦', 'Products Listed', '—',  'rgba(16,185,129,0.12)', '#10b981', 'statMyTotal')}
        ${stat('🛒', 'Pending Orders',  '12',  'rgba(230,168,0,0.12)',  'var(--fg-primary)')}
        ${stat('✅', 'Delivered',       '230', 'rgba(40,167,69,0.12)',  '#28A745')}
        ${stat('🏪', 'In Shop',        '—',   'rgba(59,130,246,0.12)', '#3b82f6', 'statShopTotal')}
      `)}
      ${sectionTitle('⚡', 'Quick Actions', '#10b981')}
      ${actionsGrid(`
        ${action('➕', 'Add Product',      'List a new part or accessory for sale.',        '#10b981',           'views/user/supplier/products.html')}
        ${action('📦', 'Manage Inventory', 'Update stock levels and product details.',      'var(--fg-primary)', 'views/user/supplier/products.html')}
        ${action('🛒', 'View Orders',      'See all incoming orders from shops.',           '#6366f1',           'views/user/supplier/orders.html')}
        ${action('🚚', 'Deliveries',       'Track and update delivery statuses.',           '#2a9d8f',           'views/user/supplier/deliveries.html')}
        ${action('📊', 'Sales Report',     'View revenue, top products, and trends.',       '#28A745',           'views/user/supplier/sales-report.html')}
        ${action('💬', 'Shop Messages',    'Communicate with repair shops directly.',       '#856404',           'views/user/supplier/messages.html')}
      `)}

      <!-- ── Shop Products View ── -->
      <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;margin-top:0.5rem;">
        <span style="font-size:1.2rem;line-height:1;">🏪</span>
        <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">All Products Currently in Shop</h5>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <span style="font-size:0.78rem;color:var(--fg-muted);" id="shopProductCount">Loading…</span>
      </div>
      <div id="shopProductsGrid" style="margin-bottom:2rem;">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading shop products…
        </div>
      </div>

      <!-- ── My Products View ── -->
      <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;margin-top:0.5rem;">
        <span style="font-size:1.2rem;line-height:1;">📋</span>
        <h5 style="margin:0;font-weight:700;color:var(--fg-text);font-size:1rem;">My Products</h5>
        <div style="flex:1;height:1px;background:var(--fg-border);"></div>
        <a href="views/user/supplier/products.html"
           style="font-size:0.8rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">
          Manage All →
        </a>
      </div>
      <div id="myProductsGrid" style="margin-bottom:2rem;">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
          <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading your products…
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

  /* ── Load supplier shop view data ──────────────────────────── */
  function loadSupplierShopView() {
    fetch('backend/supplier_shop_view.php?action=both')
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
        ${isShopView ? 'No products in the shop yet.' : 'You have no products yet. <a href="views/user/supplier/products.html" style="color:var(--fg-primary);font-weight:600;">Add one →</a>'}
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
              <span class="sp-price">₱${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
              <span class="sp-qty">${p.qty > 0 ? p.qty + ' in stock' : 'Out of stock'}</span>
            </div>
            ${statusHtml}
          </div>
        </div>`;
    }).join('');

    el.innerHTML = `<div class="sp-grid">${cards}</div>`;
  }

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
      ${sectionTitle('💼', 'Sales Overview', '#3b82f6')}
      ${statsGrid(`
        ${stat('🛒', 'Orders Today',     '—', 'rgba(59,130,246,0.12)',  '#3b82f6',       'spStatOrders')}
        ${stat('📦', 'Products Listed',  '—', 'rgba(16,185,129,0.12)', '#10b981',        'spStatProducts')}
        ${stat('📤', 'Pending Requests', '—', 'rgba(230,168,0,0.12)',  'var(--fg-primary)', 'spStatRequests')}
        ${stat('💰', 'Total Revenue',    '—', 'rgba(40,167,69,0.12)',  '#28A745',        'spStatRevenue')}
      `)}
      ${sectionTitle('⚡', 'Quick Actions', '#3b82f6')}
      ${actionsGrid(`
        ${action('📦', 'My Products',     'Upload and manage products visible to customers.',  '#10b981',           'views/user/sales_person/products.html')}
        ${action('🛒', 'Customer Orders', 'View and track customer orders.',                   '#3b82f6',           'views/user/sales_person/orders.html')}
        ${action('📋', 'Inventory',       'View products available from supervisor.',          '#6366f1',           'views/user/sales_person/inventory.html')}
        ${action('📤', 'Supply Requests', 'Request additional product supply from supervisor.','var(--fg-primary)', 'views/user/sales_person/supply-requests.html')}
        ${action('👤', 'My Profile',      'View and update your account information.',         '#2a9d8f',           'views/user/sales_person/profile.html')}
        ${action('🏠', 'Full Dashboard',  'Go to your complete sales person dashboard.',       '#856404',           'views/user/sales_person/dashboard.html')}
      `)}
      ${sectionTitle('📋', 'Recent Orders', '#2a9d8f')}
      <div id="spRecentOrders" style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.5rem;text-align:center;color:var(--fg-muted);">
        <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
        Loading recent orders…
      </div>
      <style>@keyframes spin { to { transform: rotate(360deg); } }</style>`;
  }

  function loadSalesStats() {
    // Products
    fetch('backend/sales_products.php?action=stats')
      .then(r => r.json())
      .then(d => {
        if (d.success && d.stats) {
          const el = document.getElementById('spStatProducts');
          if (el) el.textContent = d.stats.total || 0;
        }
      }).catch(() => {});

    // Orders stats
    fetch('backend/sales_orders.php?action=stats')
      .then(r => r.json())
      .then(d => {
        if (d.success && d.stats) {
          const elO = document.getElementById('spStatOrders');
          if (elO) elO.textContent = d.stats.orders_today || 0;
          const elR = document.getElementById('spStatRevenue');
          if (elR) {
            const rev = parseFloat(d.stats.total_revenue || 0);
            elR.textContent = '₱' + rev.toLocaleString('en-PH', {minimumFractionDigits: 0});
          }
        }
      }).catch(() => {});

    // Supply requests
    fetch('backend/sales_supply_requests.php?action=stats')
      .then(r => r.json())
      .then(d => {
        if (d.success && d.stats) {
          const el = document.getElementById('spStatRequests');
          if (el) el.textContent = d.stats.pending || 0;
        }
      }).catch(() => {});

    // Recent orders
    fetch('backend/sales_orders.php?action=list')
      .then(r => r.json())
      .then(d => {
        const el = document.getElementById('spRecentOrders');
        if (!el) return;
        if (!d.success || !d.orders || !d.orders.length) {
          el.innerHTML = '<p style="margin:0;font-size:0.88rem;">No orders yet. <a href="views/user/sales_person/orders.html" style="color:var(--fg-primary);font-weight:600;">View Orders →</a></p>';
          return;
        }
        const rows = d.orders.slice(0, 5).map(o => {
          const date = new Date(o.created_at).toLocaleDateString('en-PH');
          const total = parseFloat(o.total_amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});
          const statusColor = o.status === 'completed' ? '#28A745' : o.status === 'pending' ? '#c98f00' : '#6C757D';
          return `<tr style="border-bottom:1px solid var(--fg-border);">
            <td style="padding:0.65rem 0.75rem;font-weight:700;color:#3b82f6;">#${o.id}</td>
            <td style="padding:0.65rem 0.75rem;font-size:0.85rem;">${escHtml(o.product_name || '—')}</td>
            <td style="padding:0.65rem 0.75rem;font-weight:700;">₱${total}</td>
            <td style="padding:0.65rem 0.75rem;"><span style="background:${statusColor}22;color:${statusColor};padding:0.2rem 0.6rem;border-radius:20px;font-size:0.72rem;font-weight:700;">${o.status || 'pending'}</span></td>
            <td style="padding:0.65rem 0.75rem;color:var(--fg-muted);font-size:0.82rem;">${date}</td>
          </tr>`;
        }).join('');
        el.innerHTML = `<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
          <thead><tr style="background:var(--fg-bg);">
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Order</th>
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Product</th>
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Total</th>
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Status</th>
            <th style="padding:0.6rem 0.75rem;font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;border-bottom:1px solid var(--fg-border);">Date</th>
          </tr></thead>
          <tbody>${rows}</tbody>
        </table></div>
        <div style="text-align:right;margin-top:0.75rem;"><a href="views/user/sales_person/orders.html" style="font-size:0.82rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">View All Orders →</a></div>`;
      }).catch(() => {
        const el = document.getElementById('spRecentOrders');
        if (el) el.innerHTML = '<p style="margin:0;font-size:0.88rem;color:var(--fg-muted);">Could not load orders.</p>';
      });
  }

  /* ================================================================
     CUSTOMER DASHBOARD
  ================================================================ */
  function customerDashboard(user) {
    // Redirect to the full customer dashboard
    setTimeout(() => {
      window.location.href = 'views/user/customer/dashboard.html';
    }, 50);
    return `
      <div style="text-align:center;padding:3rem;color:var(--fg-muted);">
        <div style="width:32px;height:32px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 1rem;"></div>
        Redirecting to your dashboard…
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

