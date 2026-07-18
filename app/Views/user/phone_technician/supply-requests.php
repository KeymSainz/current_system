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
  <link rel="manifest" href="/manifest.json">
  <link rel="apple-touch-icon" href="/assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="/assets/css/mobile.css">
  <title>Fix&amp;Go — Supply Requests</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8.1" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5.1" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body{background:var(--fg-bg);}
    .tc-layout{display:flex;min-height:calc(100vh - 68px);}
    .tc-sidebar{width:240px;flex-shrink:0;background:var(--fg-card-bg);border-right:1px solid var(--fg-border);padding:1.5rem 0 2rem;position:sticky;top:68px;height:calc(100vh - 68px);overflow-y:auto;}
    .sidebar-label{font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);padding:0.75rem 1.25rem 0.35rem;}
    .sidebar-nav{list-style:none;padding:0;margin:0;}
    .sidebar-nav a{display:flex;align-items:center;gap:0.75rem;padding:0.6rem 1.25rem;color:var(--fg-muted);text-decoration:none;font-size:0.88rem;font-weight:500;border-left:3px solid transparent;transition:all 0.2s;}
    .sidebar-nav a:hover{color:#8b5cf6;background:rgba(139,92,246,0.07);border-left-color:#8b5cf6;}
    .sidebar-nav a.active{color:#8b5cf6;background:rgba(139,92,246,0.1);border-left-color:#8b5cf6;font-weight:700;}
    .sidebar-nav a i{font-size:1rem;width:20px;text-align:center;}
    .tc-main{flex:1;padding:2rem;min-width:0;}
    .tab-bar{display:flex;gap:0.5rem;margin-bottom:1.5rem;border-bottom:2px solid var(--fg-border);padding-bottom:0;}
    .tab-btn{padding:0.55rem 1.1rem;border:none;background:transparent;color:var(--fg-muted);font-size:0.88rem;font-weight:600;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all 0.2s;border-radius:8px 8px 0 0;}
    .tab-btn.active{color:#8b5cf6;border-bottom-color:#8b5cf6;background:rgba(139,92,246,0.06);}
    .tab-btn:hover:not(.active){color:#8b5cf6;background:rgba(139,92,246,0.04);}
    .tab-pane{display:none;}.tab-pane.active{display:block;}
    .shop-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.25rem;}
    .shop-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:16px;overflow:hidden;transition:box-shadow 0.2s,border-color 0.2s;}
    .shop-card:hover{box-shadow:0 8px 32px rgba(139,92,246,0.12);border-color:rgba(139,92,246,0.3);}
    .shop-header{padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;gap:0.85rem;}
    .shop-avatar{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,rgba(139,92,246,0.2),rgba(139,92,246,0.06));border:2px solid rgba(139,92,246,0.25);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;overflow:hidden;}
    .shop-avatar img{width:100%;height:100%;object-fit:cover;border-radius:10px;}
    .shop-name{font-weight:800;font-size:0.95rem;color:var(--fg-text);}
    .shop-meta{font-size:0.72rem;color:var(--fg-muted);}
    .shop-actions{display:flex;gap:0.5rem;margin-left:auto;}
    .product-list{padding:0.75rem;}
    .product-row{display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0.5rem;border-radius:10px;transition:background 0.15s;}
    .product-row:hover{background:rgba(139,92,246,0.04);}
    .product-img{width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid var(--fg-border);flex-shrink:0;background:var(--fg-bg);}
    .product-img-ph{width:40px;height:40px;border-radius:8px;background:var(--fg-bg);border:1px solid var(--fg-border);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--fg-muted);flex-shrink:0;}
    .product-info{flex:1;min-width:0;}
    .product-name{font-size:0.83rem;font-weight:700;color:var(--fg-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .product-cat{font-size:0.68rem;color:var(--fg-muted);}
    .product-price{font-size:0.9rem;font-weight:800;color:#8b5cf6;white-space:nowrap;}
    .product-stock{font-size:0.68rem;color:var(--fg-muted);}
    .add-cart-btn{padding:0.3rem 0.7rem;border-radius:8px;background:#8b5cf6;color:#fff;border:none;font-size:0.75rem;font-weight:700;cursor:pointer;transition:all 0.2s;white-space:nowrap;}
    .add-cart-btn:hover{background:#7c3aed;}
    .section-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;margin-bottom:1.5rem;}
    .section-head{padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;}
    .section-head h6{margin:0;font-weight:700;font-size:0.95rem;color:var(--fg-text);}
    .mini-table{width:100%;border-collapse:collapse;font-size:0.83rem;}
    .mini-table th{background:var(--fg-bg);padding:0.6rem 1rem;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);}
    .mini-table td{padding:0.65rem 1rem;border-bottom:1px solid var(--fg-border);color:var(--fg-text);vertical-align:middle;}
    .mini-table tr:last-child td{border-bottom:none;}
    .mini-table tr:hover td{background:rgba(139,92,246,0.03);}
    .badge-status{display:inline-flex;align-items:center;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.7rem;font-weight:700;text-transform:uppercase;}
    .badge-pending{background:rgba(230,168,0,0.12);color:#c98f00;}
    .badge-confirmed{background:rgba(59,130,246,0.12);color:#3b82f6;}
    .badge-preparing{background:rgba(139,92,246,0.12);color:#8b5cf6;}
    .badge-ready{background:rgba(16,185,129,0.12);color:#10b981;}
    .badge-shipped{background:rgba(99,102,241,0.12);color:#6366f1;}
    .badge-delivered{background:rgba(40,167,69,0.12);color:#28A745;}
    .badge-cancelled{background:rgba(107,114,128,0.12);color:#6c757d;}
    .badge-paid{background:rgba(40,167,69,0.12);color:#28A745;}
    .badge-failed{background:rgba(220,53,69,0.12);color:#dc3545;}
    .stats-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:1rem;margin-bottom:1.5rem;}
    .stat-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1rem;text-align:center;}
    .stat-value{font-size:1.7rem;font-weight:800;line-height:1;}
    .stat-label{font-size:0.7rem;color:var(--fg-muted);font-weight:600;margin-top:0.2rem;}
    .alert-bar{padding:0.75rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;}
    .alert-success{background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);}
    .alert-danger{background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);}
    .alert-info{background:rgba(59,130,246,0.12);color:#3b82f6;border:1px solid rgba(59,130,246,0.25);}
    .cart-badge{background:#8b5cf6;color:#fff;font-size:0.65rem;font-weight:800;padding:0.1rem 0.4rem;border-radius:10px;min-width:18px;text-align:center;display:inline-block;margin-left:0.3rem;}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @keyframes spin{to{transform:rotate(360deg);}}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .tc-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .tc-sidebar.open{transform:translateX(0);}
      .tc-main{padding:1.25rem;}
      .shop-grid{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>

  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;" onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <a href="/index.php?browse=1" class="btn btn-sm" style="border:1.5px solid rgba(139,92,246,0.4);border-radius:8px;color:#8b5cf6;background:rgba(139,92,246,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:0.35rem;"><i class="bi bi-house-door"></i> Browse Shop</a>
      <button id="cartNavBtn" onclick="openCartModal()" style="position:relative;background:rgba(139,92,246,0.08);border:1.5px solid rgba(139,92,246,0.3);border-radius:8px;padding:0.3rem 0.75rem;color:#8b5cf6;font-size:0.85rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.35rem;">
        <i class="bi bi-cart3"></i> Cart <span id="cartNavCount" class="cart-badge" style="display:none;">0</span>
      </button>
      <span style="background:rgba(139,92,246,0.12);color:#8b5cf6;border:1px solid rgba(139,92,246,0.25);padding:0.25rem 0.75rem;border-radius:50px;font-size:0.75rem;font-weight:700;">🔧 Technician</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <button id="logoutBtn" class="btn btn-sm" style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="tc-layout">
    <aside class="tc-sidebar" id="tcSidebar">
      <div class="sidebar-label">Main</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="repairs.php"><i class="bi bi-tools"></i> Repair Bookings</a></li>
        <li><a href="inventory.php"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="supply-requests.php" class="active"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
      </ul>
      <div class="sidebar-label">Account</div>
      <ul class="sidebar-nav">
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>

    <main class="tc-main">
      <div style="margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
        <div>
          <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;"><i class="bi bi-send" style="color:#8b5cf6;margin-right:0.5rem;"></i>Supply Requests</h2>
          <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">Browse supplier &amp; owner shops, add to cart, and order products</p>
        </div>
        <button onclick="openCartModal()" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.55rem 1.1rem;border-radius:10px;background:#8b5cf6;color:#fff;border:none;font-weight:700;font-size:0.85rem;cursor:pointer;transition:all 0.2s;" onmouseenter="this.style.background='#7c3aed'" onmouseleave="this.style.background='#8b5cf6'">
          <i class="bi bi-cart3"></i> View Cart <span id="cartBtnCount" class="cart-badge" style="display:none;">0</span>
        </button>
      </div>

      <div id="alertBox" style="display:none;"></div>

      <!-- Tabs -->
      <div class="tab-bar">
        <button class="tab-btn active" onclick="switchTab('shops')" id="tabShops"><i class="bi bi-shop"></i> Shops &amp; Products</button>
        <button class="tab-btn" onclick="switchTab('orders')" id="tabOrders"><i class="bi bi-bag-check"></i> My Orders</button>
        <button class="tab-btn" onclick="switchTab('requests')" id="tabRequests"><i class="bi bi-send"></i> Supply Requests</button>
      </div>

      <!-- Tab: Shops -->
      <div class="tab-pane active" id="paneShops">
        <div id="shopsGrid" class="shop-grid">
          <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--fg-muted);">
            <div style="width:28px;height:28px;border:3px solid var(--fg-border);border-top-color:#8b5cf6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.75rem;"></div>Loading shops…
          </div>
        </div>
      </div>

      <!-- Tab: My Orders -->
      <div class="tab-pane" id="paneOrders">
        <div class="stats-row">
          <div class="stat-card"><div class="stat-value" style="color:#8b5cf6;" id="oStatTotal">—</div><div class="stat-label">Total</div></div>
          <div class="stat-card"><div class="stat-value" style="color:#c98f00;" id="oStatPending">—</div><div class="stat-label">Pending</div></div>
          <div class="stat-card"><div class="stat-value" style="color:#3b82f6;" id="oStatConfirmed">—</div><div class="stat-label">Confirmed</div></div>
          <div class="stat-card"><div class="stat-value" style="color:#28A745;" id="oStatDelivered">—</div><div class="stat-label">Delivered</div></div>
        </div>
        <div class="section-card">
          <div class="section-head"><h6><i class="bi bi-bag-check" style="color:#8b5cf6;margin-right:0.4rem;"></i>Order History</h6></div>
          <div style="overflow-x:auto;">
            <table class="mini-table">
              <thead><tr><th>#</th><th>Shop</th><th>Items</th><th>Total</th><th>Payment</th><th>Fulfillment</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
              <tbody id="ordersBody"><tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--fg-muted);">Loading…</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Tab: Supply Requests -->
      <div class="tab-pane" id="paneRequests">
        <div class="stats-row">
          <div class="stat-card"><div class="stat-value" style="color:#8b5cf6;" id="rStatTotal">—</div><div class="stat-label">Total</div></div>
          <div class="stat-card"><div class="stat-value" style="color:#c98f00;" id="rStatPending">—</div><div class="stat-label">Pending</div></div>
          <div class="stat-card"><div class="stat-value" style="color:#28A745;" id="rStatApproved">—</div><div class="stat-label">Approved</div></div>
          <div class="stat-card"><div class="stat-value" style="color:#dc3545;" id="rStatRejected">—</div><div class="stat-label">Rejected</div></div>
        </div>
        <div class="section-card">
          <div class="section-head"><h6><i class="bi bi-send" style="color:#8b5cf6;margin-right:0.4rem;"></i>My Supply Requests</h6></div>
          <div style="overflow-x:auto;">
            <table class="mini-table">
              <thead><tr><th>#</th><th>Product</th><th>Supplier</th><th>Qty</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
              <tbody id="requestsBody"><tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--fg-muted);">Loading…</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- ===== CART MODAL ===== -->
  <div id="cartModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:flex-start;justify-content:flex-end;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;width:100%;max-width:480px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 64px rgba(0,0,0,0.4);margin-top:1rem;">
      <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <h5 style="margin:0;font-weight:800;font-size:1rem;color:var(--fg-text);"><i class="bi bi-cart3" style="color:#8b5cf6;margin-right:0.5rem;"></i>Cart <span id="cartModalCount" class="cart-badge">0</span></h5>
        <button onclick="closeCartModal()" style="width:30px;height:30px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:0.9rem;"><i class="bi bi-x-lg"></i></button>
      </div>
      <div id="cartItems" style="flex:1;overflow-y:auto;padding:1rem;"></div>
      <div style="padding:1.25rem 1.5rem;border-top:1px solid var(--fg-border);flex-shrink:0;">
        <div style="display:flex;justify-content:space-between;font-size:0.9rem;font-weight:700;color:var(--fg-text);margin-bottom:1rem;">
          <span>Total:</span><span id="cartTotal" style="color:#8b5cf6;">₱0.00</span>
        </div>
        <button onclick="proceedCheckout()" id="checkoutBtn" style="width:100%;padding:0.75rem;border-radius:12px;background:#8b5cf6;color:#fff;border:none;font-weight:800;font-size:0.95rem;cursor:pointer;transition:all 0.2s;" onmouseenter="this.style.background='#7c3aed'" onmouseleave="this.style.background='#8b5cf6'">
          <i class="bi bi-bag-check-fill"></i> Proceed to Checkout
        </button>
      </div>
    </div>
  </div>

  <!-- ===== CHECKOUT MODAL ===== -->
  <div id="checkoutModal" style="display:none;position:fixed;inset:0;z-index:9100;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;overflow-y:auto;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;width:100%;max-width:520px;box-shadow:0 24px 64px rgba(0,0,0,0.4);">
      <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;">
        <h5 style="margin:0;font-weight:800;font-size:1rem;color:var(--fg-text);"><i class="bi bi-bag-check-fill" style="color:#8b5cf6;margin-right:0.5rem;"></i>Checkout</h5>
        <button onclick="closeCheckoutModal()" style="width:30px;height:30px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:0.9rem;"><i class="bi bi-x-lg"></i></button>
      </div>
      <div style="padding:1.5rem;">
        <!-- Fulfillment -->
        <div style="margin-bottom:1.25rem;">
          <div style="font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.6rem;">Fulfillment Type</div>
          <div style="display:flex;gap:0.75rem;">
            <label style="flex:1;display:flex;align-items:center;gap:0.6rem;padding:0.75rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;" id="lblPickup">
              <input type="radio" name="fulfillment" value="pickup" id="rdPickup" onchange="onFulfillmentChange()"> <i class="bi bi-shop" style="color:#8b5cf6;"></i> <span style="font-weight:700;font-size:0.88rem;">Pickup</span>
            </label>
            <label style="flex:1;display:flex;align-items:center;gap:0.6rem;padding:0.75rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;" id="lblDelivery">
              <input type="radio" name="fulfillment" value="delivery" id="rdDelivery" checked onchange="onFulfillmentChange()"> <i class="bi bi-truck" style="color:#8b5cf6;"></i> <span style="font-weight:700;font-size:0.88rem;">Delivery (+₱50)</span>
            </label>
          </div>
        </div>
        <!-- Shop address (for pickup) -->
        <div id="shopAddressBox" style="display:none;background:rgba(139,92,246,0.06);border:1px solid rgba(139,92,246,0.2);border-radius:10px;padding:0.85rem;margin-bottom:1.25rem;font-size:0.83rem;color:var(--fg-text);">
          <div style="font-weight:700;margin-bottom:0.25rem;"><i class="bi bi-geo-alt-fill" style="color:#8b5cf6;"></i> Shop Address</div>
          <div id="shopAddressText" style="color:var(--fg-muted);">—</div>
        </div>
        <!-- Delivery address -->
        <div id="deliveryAddressBox" style="margin-bottom:1.25rem;">
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.4rem;">Delivery Address <span style="color:#dc3545;">*</span></label>
          <textarea id="deliveryAddress" rows="3" style="width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;resize:vertical;font-family:inherit;transition:border-color 0.2s;" placeholder="House/Unit No., Street, Barangay, City, Province, ZIP" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='var(--fg-border)'"></textarea>
        </div>
        <!-- Payment method -->
        <div style="margin-bottom:1.25rem;">
          <div style="font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.6rem;">Payment Method</div>
          <div style="display:flex;flex-direction:column;gap:0.5rem;">
            <label style="display:flex;align-items:center;gap:0.6rem;padding:0.65rem 0.9rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;" id="lblCod">
              <input type="radio" name="payment" value="cod" id="rdCod" checked onchange="onPaymentChange()"> <i class="bi bi-cash-coin" style="color:#28A745;"></i> <span style="font-weight:700;font-size:0.88rem;">Cash on Delivery / Pickup</span>
            </label>
            <label style="display:flex;align-items:center;gap:0.6rem;padding:0.65rem 0.9rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;" id="lblGcash">
              <input type="radio" name="payment" value="gcash" id="rdGcash" onchange="onPaymentChange()"> <i class="bi bi-phone-fill" style="color:#0070ba;"></i> <span style="font-weight:700;font-size:0.88rem;">GCash</span> <span style="font-size:0.72rem;color:var(--fg-muted);margin-left:auto;">via PayMongo</span>
            </label>
            <label style="display:flex;align-items:center;gap:0.6rem;padding:0.65rem 0.9rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;" id="lblCard">
              <input type="radio" name="payment" value="card" id="rdCard" onchange="onPaymentChange()"> <i class="bi bi-credit-card-fill" style="color:#6366f1;"></i> <span style="font-weight:700;font-size:0.88rem;">Credit/Debit Card</span> <span style="font-size:0.72rem;color:var(--fg-muted);margin-left:auto;">via PayMongo</span>
            </label>
          </div>
        </div>
        <!-- Notes -->
        <div style="margin-bottom:1.25rem;">
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.4rem;">Notes (optional)</label>
          <input type="text" id="orderNotes" style="width:100%;padding:0.6rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;font-family:inherit;transition:border-color 0.2s;" placeholder="Any special instructions…" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='var(--fg-border)'">
        </div>
        <!-- Summary -->
        <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;padding:0.85rem;margin-bottom:1.25rem;font-size:0.85rem;">
          <div style="display:flex;justify-content:space-between;margin-bottom:0.35rem;"><span style="color:var(--fg-muted);">Subtotal</span><span id="coSubtotal" style="font-weight:600;">₱0.00</span></div>
          <div style="display:flex;justify-content:space-between;margin-bottom:0.35rem;"><span style="color:var(--fg-muted);">Shipping</span><span id="coShipping" style="font-weight:600;">₱50.00</span></div>
          <div style="display:flex;justify-content:space-between;padding-top:0.5rem;border-top:1px solid var(--fg-border);"><span style="font-weight:800;">Total</span><span id="coTotal" style="font-weight:800;color:#8b5cf6;">₱0.00</span></div>
        </div>
        <div id="coAlert" style="display:none;margin-bottom:1rem;"></div>
        <button id="placeOrderBtn" onclick="placeOrder()" style="width:100%;padding:0.75rem;border-radius:12px;background:#8b5cf6;color:#fff;border:none;font-weight:800;font-size:0.95rem;cursor:pointer;transition:all 0.2s;" onmouseenter="this.style.background='#7c3aed'" onmouseleave="this.style.background='#8b5cf6'">
          <i class="bi bi-bag-check-fill"></i> Place Order
        </button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script>
  'use strict';
  const ORDERS_API = '/api/technician/orders';
  const MKT_API    = '../../../api/technician/marketplace';
  const MSG_API    = '../../../api/messages';

  // ── Cart (sessionStorage) ─────────────────────────────────
  const CART_KEY = 'fg_tech_cart';
  let cartSellerId = null, cartSellerRole = null, cartSellerName = '', cartShopAddress = '';

  function getCart(){ try{ return JSON.parse(sessionStorage.getItem(CART_KEY)||'[]'); }catch(e){ return []; } }
  function saveCart(c){ sessionStorage.setItem(CART_KEY, JSON.stringify(c)); updateCartBadge(); }
  function clearCart(){ sessionStorage.removeItem(CART_KEY); cartSellerId=null; cartSellerRole=null; cartSellerName=''; cartShopAddress=''; updateCartBadge(); }

  function addToCart(product, sellerId, sellerRole, sellerName, shopAddress){
    if(cartSellerId && cartSellerId !== sellerId){
      if(!confirm('Your cart has items from another shop. Clear cart and add this item?')) return;
      clearCart();
    }
    cartSellerId = sellerId; cartSellerRole = sellerRole; cartSellerName = sellerName; cartShopAddress = shopAddress||'';
    const cart = getCart();
    const idx = cart.findIndex(i => i.id === product.id);
    if(idx >= 0){ cart[idx].quantity = Math.min(cart[idx].quantity + 1, parseInt(product.qty)||99); }
    else { cart.push({ id:product.id, name:product.name||product.item_description, category:product.category, price:parseFloat(product.price||product.srp), qty:parseInt(product.qty), quantity:1, image_path:product.image_path||'' }); }
    saveCart(cart);
    showAlert('success', `<i class="bi bi-cart-check-fill"></i> Added to cart! <button onclick="openCartModal()" style="background:none;border:none;color:#28A745;font-weight:700;cursor:pointer;text-decoration:underline;">View Cart</button>`);
  }

  function updateCartBadge(){
    const cart = getCart();
    const count = cart.reduce((s,i)=>s+i.quantity,0);
    ['cartNavCount','cartBtnCount','cartModalCount'].forEach(id=>{
      const el = document.getElementById(id);
      if(!el) return;
      el.textContent = count;
      el.style.display = count > 0 ? 'inline-block' : 'none';
    });
  }

  function peso(n){ return '₱' + parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:2}); }
  function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
  function fmtDate(d){ return d?new Date(d).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'}):'—'; }

  // ── Init ──────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function(){
    const user = FGAuth.UserStore.get();
    if(!user||user.role!=='phone_technician'){ window.location.href='/login.html'; return; }
    document.getElementById('navUserName').textContent = ((user.firstName||'')+' '+(user.lastName||'')).trim()||user.email;
    const sidebar=document.getElementById('tcSidebar'), overlay=document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click',()=>{ sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click',()=>{ sidebar.classList.remove('open'); overlay.classList.remove('open'); });
    updateCartBadge();
    loadShops();

    // Check payment return
    const params = new URLSearchParams(window.location.search);
    if(params.get('payment')==='success') showAlert('success','Payment successful! Your order has been confirmed.');
    if(params.get('payment')==='cancel') showAlert('danger','Payment was cancelled. Your order is still pending.');
  });

  // ── Tab switching ─────────────────────────────────────────
  function switchTab(tab){
    ['shops','orders','requests'].forEach(t=>{
      document.getElementById('pane'+t.charAt(0).toUpperCase()+t.slice(1)).classList.toggle('active', t===tab);
      document.getElementById('tab'+t.charAt(0).toUpperCase()+t.slice(1)).classList.toggle('active', t===tab);
    });
    if(tab==='orders') loadOrders();
    if(tab==='requests') loadRequests();
  }

  // ── Load shops ────────────────────────────────────────────
  function loadShops(){
    fetch(ORDERS_API+'?action=shops',{credentials:'include'})
      .then(r=>r.json())
      .then(d=>{
        const grid = document.getElementById('shopsGrid');
        if(!d.success||!d.shops||!d.shops.length){
          grid.innerHTML='<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--fg-muted);"><i class="bi bi-shop" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.3;"></i>No shops available yet.</div>';
          return;
        }
        grid.innerHTML = d.shops.map(shop => renderShopCard(shop)).join('');
      }).catch(()=>{
        document.getElementById('shopsGrid').innerHTML='<div style="grid-column:1/-1;text-align:center;padding:2rem;color:var(--fg-muted);">Could not load shops.</div>';
      });
  }

  function renderShopCard(shop){
    const roleLabel = shop.seller_role==='owner' ? '🏪 Owner' : '📦 Supplier';
    const location = [shop.shop_city, shop.shop_province].filter(Boolean).join(', ');
    const address = shop.shop_address || (location ? location : 'Address not set');
    const logoHtml = shop.shop_logo
      ? `<img src="../../../${esc(shop.shop_logo)}" alt="" onerror="this.parentElement.innerHTML='📦'">`
      : (shop.seller_role==='owner'?'🏪':'📦');

    const products = (shop.products||[]).map(p=>{
      const imgHtml = p.image_path
        ? `<img src="../../../${esc(p.image_path)}" class="product-img" alt="" onerror="this.outerHTML='<div class=\\'product-img-ph\\'>📦</div>'">`
        : `<div class="product-img-ph">📦</div>`;
      return `<div class="product-row">
        ${imgHtml}
        <div class="product-info">
          <div class="product-name" title="${esc(p.name)}">${esc(p.name)}</div>
          <div class="product-cat">${esc(p.category||'—')} · <span class="product-stock">Stock: ${p.qty}</span></div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
          <div class="product-price">${peso(p.price)}</div>
          <button class="add-cart-btn" onclick="addToCart(${JSON.stringify(p).replace(/"/g,'&quot;')},${shop.seller_id},'${shop.seller_role}','${esc(shop.shop_name)}','${esc(address)}')">
            <i class="bi bi-cart-plus"></i> Add
          </button>
        </div>
      </div>`;
    }).join('');

    return `<div class="shop-card">
      <div class="shop-header">
        <div class="shop-avatar">${logoHtml}</div>
        <div style="flex:1;min-width:0;">
          <div class="shop-name">${esc(shop.shop_name)}</div>
          <div class="shop-meta">${roleLabel} · ${esc(location||'—')}</div>
        </div>
        <div class="shop-actions">
          <button onclick="chatWith(${shop.seller_id},'${esc(shop.seller_name)}')" title="Chat" style="padding:0.3rem 0.6rem;border-radius:8px;background:rgba(139,92,246,0.1);border:1.5px solid rgba(139,92,246,0.3);color:#8b5cf6;cursor:pointer;font-size:0.8rem;font-weight:700;transition:all 0.2s;" onmouseenter="this.style.background='#8b5cf6';this.style.color='#fff'" onmouseleave="this.style.background='rgba(139,92,246,0.1)';this.style.color='#8b5cf6'">
            <i class="bi bi-chat-dots-fill"></i> Chat
          </button>
        </div>
      </div>
      <div class="product-list">${products || '<div style="padding:1rem;text-align:center;color:var(--fg-muted);font-size:0.83rem;">No products available</div>'}</div>
    </div>`;
  }

  function chatWith(userId, userName){
    fetch(MSG_API+'?action=get_or_create&other_id='+userId,{credentials:'include'})
      .then(r=>r.json())
      .then(d=>{
        if(d.success) window.location.href='messages.php?conv_id='+d.conv_id;
        else showAlert('danger','Could not open chat: '+(d.message||'Error'));
      }).catch(()=>showAlert('danger','Network error.'));
  }
  </script>

  <script>
  // ── Cart Modal ────────────────────────────────────────────
  function openCartModal(){
    const cart = getCart();
    const modal = document.getElementById('cartModal');
    if(!cart.length){
      showAlert('info','Your cart is empty. Add products from the shops above.');
      return;
    }
    renderCartItems(cart);
    modal.style.display='flex';
  }
  function closeCartModal(){ document.getElementById('cartModal').style.display='none'; }
  document.getElementById('cartModal').addEventListener('click',function(e){ if(e.target===this) closeCartModal(); });

  function renderCartItems(cart){
    const subtotal = cart.reduce((s,i)=>s+(i.price*i.quantity),0);
    document.getElementById('cartTotal').textContent = peso(subtotal);
    document.getElementById('cartItems').innerHTML = cart.length ? cart.map(item=>`
      <div style="display:flex;align-items:center;gap:0.75rem;padding:0.65rem 0;border-bottom:1px solid var(--fg-border);">
        ${item.image_path?`<img src="../../../${esc(item.image_path)}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid var(--fg-border);" onerror="this.outerHTML='<div style=\\'width:40px;height:40px;border-radius:8px;background:var(--fg-bg);border:1px solid var(--fg-border);display:flex;align-items:center;justify-content:center;font-size:1.1rem;\\'>📦</div>'">`:'<div style="width:40px;height:40px;border-radius:8px;background:var(--fg-bg);border:1px solid var(--fg-border);display:flex;align-items:center;justify-content:center;font-size:1.1rem;">📦</div>'}
        <div style="flex:1;min-width:0;">
          <div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(item.name)}</div>
          <div style="font-size:0.72rem;color:var(--fg-muted);">${esc(item.category||'—')} · ${peso(item.price)} each</div>
        </div>
        <div style="display:flex;align-items:center;gap:0.4rem;flex-shrink:0;">
          <button onclick="changeQty(${item.id},-1)" style="width:26px;height:26px;border-radius:6px;border:1.5px solid var(--fg-border);background:var(--fg-bg);color:var(--fg-text);cursor:pointer;font-weight:700;font-size:0.9rem;display:flex;align-items:center;justify-content:center;">−</button>
          <span style="font-weight:700;font-size:0.88rem;min-width:20px;text-align:center;">${item.quantity}</span>
          <button onclick="changeQty(${item.id},1)" style="width:26px;height:26px;border-radius:6px;border:1.5px solid var(--fg-border);background:var(--fg-bg);color:var(--fg-text);cursor:pointer;font-weight:700;font-size:0.9rem;display:flex;align-items:center;justify-content:center;">+</button>
        </div>
        <div style="font-weight:800;color:#8b5cf6;font-size:0.88rem;min-width:60px;text-align:right;">${peso(item.price*item.quantity)}</div>
        <button onclick="removeFromCart(${item.id})" style="width:26px;height:26px;border-radius:6px;border:1.5px solid rgba(220,53,69,0.3);background:transparent;color:#dc3545;cursor:pointer;font-size:0.8rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-trash3"></i></button>
      </div>`).join('')
      : '<div style="text-align:center;padding:2rem;color:var(--fg-muted);">Cart is empty.</div>';
  }

  function changeQty(productId, delta){
    const cart = getCart();
    const item = cart.find(i=>i.id===productId);
    if(!item) return;
    item.quantity = Math.max(1, Math.min(item.quantity+delta, item.qty||99));
    saveCart(cart);
    renderCartItems(cart);
  }
  function removeFromCart(productId){
    const cart = getCart().filter(i=>i.id!==productId);
    saveCart(cart);
    if(!cart.length){ closeCartModal(); return; }
    renderCartItems(cart);
  }

  // ── Checkout Modal ────────────────────────────────────────
  function proceedCheckout(){
    const cart = getCart();
    if(!cart.length){ showAlert('danger','Cart is empty.'); return; }
    closeCartModal();
    const subtotal = cart.reduce((s,i)=>s+(i.price*i.quantity),0);
    document.getElementById('coSubtotal').textContent = peso(subtotal);
    document.getElementById('coShipping').textContent = peso(50);
    document.getElementById('coTotal').textContent    = peso(subtotal+50);
    document.getElementById('shopAddressText').textContent = cartShopAddress || 'Address not provided by seller';
    document.getElementById('deliveryAddress').value = '';
    document.getElementById('orderNotes').value = '';
    document.getElementById('rdDelivery').checked = true;
    onFulfillmentChange();
    document.getElementById('rdCod').checked = true;
    onPaymentChange();
    document.getElementById('coAlert').style.display='none';
    document.getElementById('checkoutModal').style.display='flex';
  }
  function closeCheckoutModal(){ document.getElementById('checkoutModal').style.display='none'; }
  document.getElementById('checkoutModal').addEventListener('click',function(e){ if(e.target===this) closeCheckoutModal(); });

  function onFulfillmentChange(){
    const isPickup = document.getElementById('rdPickup').checked;
    document.getElementById('shopAddressBox').style.display    = isPickup  ? 'block' : 'none';
    document.getElementById('deliveryAddressBox').style.display = isPickup ? 'none'  : 'block';
    const subtotal = getCart().reduce((s,i)=>s+(i.price*i.quantity),0);
    const shipping = isPickup ? 0 : 50;
    document.getElementById('coShipping').textContent = peso(shipping);
    document.getElementById('coTotal').textContent    = peso(subtotal+shipping);
    ['lblPickup','lblDelivery'].forEach(id=>{
      const lbl = document.getElementById(id);
      const inp = lbl.querySelector('input');
      lbl.style.borderColor = inp.checked ? '#8b5cf6' : 'var(--fg-border)';
    });
  }
  function onPaymentChange(){
    ['lblCod','lblGcash','lblCard'].forEach(id=>{
      const lbl = document.getElementById(id);
      const inp = lbl.querySelector('input');
      lbl.style.borderColor = inp.checked ? '#8b5cf6' : 'var(--fg-border)';
    });
  }

  function placeOrder(){
    const cart = getCart();
    if(!cart.length){ coAlert('danger','Cart is empty.'); return; }
    const isPickup   = document.getElementById('rdPickup').checked;
    const delivAddr  = document.getElementById('deliveryAddress').value.trim();
    const payMethod  = document.querySelector('input[name="payment"]:checked')?.value || 'cod';
    const notes      = document.getElementById('orderNotes').value.trim();
    if(!isPickup && !delivAddr){ coAlert('danger','Please enter your delivery address.'); return; }

    const subtotal   = cart.reduce((s,i)=>s+(i.price*i.quantity),0);
    const shipping   = isPickup ? 0 : 50;

    const payload = {
      action:           payMethod==='cod' ? 'place_cod' : 'create_checkout',
      seller_id:        cartSellerId,
      seller_role:      cartSellerRole,
      fulfillment_type: isPickup ? 'pickup' : 'delivery',
      delivery_address: isPickup ? '' : delivAddr,
      payment_method:   payMethod,
      notes:            notes,
      cart:             cart.map(i=>({ id:i.id, quantity:i.quantity })),
    };

    const btn = document.getElementById('placeOrderBtn');
    btn.disabled=true; btn.innerHTML='<i class="bi bi-hourglass-split"></i> Processing…';

    fetch(ORDERS_API,{
      method:'POST', credentials:'include',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    })
      .then(r=>r.json())
      .then(d=>{
        if(!d.success) throw new Error(d.message||'Order failed.');
        if(d.checkout_url){
          clearCart();
          closeCheckoutModal();
          window.location.href = d.checkout_url;
        } else {
          clearCart();
          closeCheckoutModal();
          showAlert('success','Order placed! Reference: '+d.reference);
          switchTab('orders');
        }
      })
      .catch(err=>coAlert('danger',err.message))
      .finally(()=>{ btn.disabled=false; btn.innerHTML='<i class="bi bi-bag-check-fill"></i> Place Order'; });
  }

  function coAlert(type, msg){
    const el = document.getElementById('coAlert');
    el.style.display='flex';
    el.className='alert-bar alert-'+type;
    el.innerHTML='<i class="bi bi-'+(type==='success'?'check-circle-fill':'exclamation-triangle-fill')+'"></i> '+esc(msg);
  }

  // ── Orders tab ────────────────────────────────────────────
  function loadOrders(){
    fetch(ORDERS_API+'?action=order_stats',{credentials:'include'})
      .then(r=>r.json()).then(d=>{
        if(!d.success) return;
        const s=d.stats||{};
        document.getElementById('oStatTotal').textContent    = s.total||0;
        document.getElementById('oStatPending').textContent  = s.pending||0;
        document.getElementById('oStatConfirmed').textContent= s.confirmed||0;
        document.getElementById('oStatDelivered').textContent= s.delivered||0;
      }).catch(()=>{});

    fetch(ORDERS_API+'?action=my_orders',{credentials:'include'})
      .then(r=>r.json()).then(d=>{
        const tbody = document.getElementById('ordersBody');
        if(!d.success||!d.orders||!d.orders.length){
          tbody.innerHTML='<tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--fg-muted);">No orders yet.</td></tr>';
          return;
        }
        const statusMap={pending:'badge-pending',confirmed:'badge-confirmed',preparing:'badge-preparing',ready:'badge-ready',shipped:'badge-shipped',delivered:'badge-delivered',cancelled:'badge-cancelled'};
        const payMap={pending:'badge-pending',paid:'badge-paid',failed:'badge-failed'};
        tbody.innerHTML = d.orders.map(o=>{
          const items = (o.items||[]).map(i=>`${i.quantity}× ${esc(i.product_name)}`).join(', ');
          const cancelBtn = o.order_status==='pending'
            ? `<button onclick="cancelOrder(${o.id})" style="padding:0.2rem 0.55rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid #dc3545;color:#dc3545;background:transparent;" onmouseenter="this.style.background='#dc3545';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#dc3545'">Cancel</button>`
            : '—';
          return `<tr>
            <td style="font-weight:700;color:#8b5cf6;">#${o.id}</td>
            <td><div style="font-weight:600;">${esc(o.shop_name||o.seller_name||'—')}</div><div style="font-size:0.7rem;color:var(--fg-muted);">${o.seller_role==='owner'?'🏪 Owner':'📦 Supplier'}</div></td>
            <td style="max-width:160px;font-size:0.78rem;color:var(--fg-muted);">${items||'—'}</td>
            <td style="font-weight:700;">${peso(o.total_amount)}</td>
            <td><span class="badge-status ${payMap[o.payment_status]||'badge-pending'}">${o.payment_method.toUpperCase()} · ${o.payment_status}</span></td>
            <td><span class="badge-status ${o.fulfillment_type==='pickup'?'badge-confirmed':'badge-shipped'}">${o.fulfillment_type==='pickup'?'🏪 Pickup':'🚚 Delivery'}</span></td>
            <td><span class="badge-status ${statusMap[o.order_status]||'badge-pending'}">${o.order_status}</span></td>
            <td style="color:var(--fg-muted);font-size:0.78rem;">${fmtDate(o.created_at)}</td>
            <td>${cancelBtn}</td>
          </tr>`;
        }).join('');
      }).catch(()=>{});
  }

  function cancelOrder(id){
    if(!confirm('Cancel order #'+id+'?')) return;
    fetch(ORDERS_API,{method:'POST',credentials:'include',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'cancel_order',order_id:id})})
      .then(r=>r.json()).then(d=>{ if(d.success){ showAlert('success','Order cancelled.'); loadOrders(); } else showAlert('danger',d.message||'Failed.'); })
      .catch(()=>showAlert('danger','Network error.'));
  }

  // ── Supply Requests tab ───────────────────────────────────
  function loadRequests(){
    fetch(MKT_API+'?action=request_stats',{credentials:'include'})
      .then(r=>r.json()).then(d=>{
        if(!d.success) return;
        const s=d.stats||{};
        document.getElementById('rStatTotal').textContent   = s.total||0;
        document.getElementById('rStatPending').textContent = s.pending||0;
        document.getElementById('rStatApproved').textContent= s.approved||0;
        document.getElementById('rStatRejected').textContent= s.rejected||0;
      }).catch(()=>{});

    fetch(MKT_API+'?action=my_requests',{credentials:'include'})
      .then(r=>r.json()).then(d=>{
        const tbody = document.getElementById('requestsBody');
        if(!d.success||!d.requests||!d.requests.length){
          tbody.innerHTML='<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--fg-muted);">No supply requests yet.</td></tr>';
          return;
        }
        const sMap={pending:'badge-pending',approved:'badge-confirmed',rejected:'badge-cancelled',fulfilled:'badge-delivered',cancelled:'badge-cancelled'};
        tbody.innerHTML = d.requests.map(r=>{
          const cancelBtn = r.status==='pending'
            ? `<button onclick="cancelRequest(${r.id})" style="padding:0.2rem 0.55rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid #dc3545;color:#dc3545;background:transparent;" onmouseenter="this.style.background='#dc3545';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#dc3545'">Cancel</button>`
            : '—';
          return `<tr>
            <td style="font-weight:700;color:#8b5cf6;">#${r.id}</td>
            <td><div style="font-weight:600;">${esc(r.product_name||'—')}</div><div style="font-size:0.7rem;color:var(--fg-muted);">${esc(r.product_category||'')}</div></td>
            <td style="color:var(--fg-muted);">${esc(r.supplier_name||'—')}</td>
            <td style="font-weight:700;text-align:center;">${r.quantity_requested||1}</td>
            <td><span class="badge-status ${sMap[r.status]||'badge-pending'}">${r.status}</span></td>
            <td style="color:var(--fg-muted);font-size:0.78rem;">${fmtDate(r.created_at)}</td>
            <td>${cancelBtn}</td>
          </tr>`;
        }).join('');
      }).catch(()=>{});
  }

  function cancelRequest(id){
    if(!confirm('Cancel supply request #'+id+'?')) return;
    fetch(MKT_API,{method:'POST',credentials:'include',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'cancel_request',request_id:id})})
      .then(r=>r.json()).then(d=>{ if(d.success){ showAlert('success','Request cancelled.'); loadRequests(); } else showAlert('danger',d.message||'Failed.'); })
      .catch(()=>showAlert('danger','Network error.'));
  }

  function showAlert(type, msg){
    const box = document.getElementById('alertBox');
    box.style.display='flex';
    box.className='alert-bar alert-'+type;
    box.innerHTML='<i class="bi bi-'+(type==='success'?'check-circle-fill':type==='info'?'info-circle-fill':'exclamation-triangle-fill')+'"></i> '+msg;
    setTimeout(()=>{ box.style.display='none'; },6000);
  }
  </script>

</body>
</html>




