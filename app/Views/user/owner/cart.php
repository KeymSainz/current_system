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
  <title>Shopping Cart — Fix&amp;Go</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="/assets/css/dashboard.css?v=6" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar">
    <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
      <img src="/assets/images/logo.png" alt="Fix&Go" style="height:40px;width:auto;object-fit:contain;">
    </a>
    <div style="display:flex;align-items:center;gap:0.75rem;">
      <span id="navRoleBadge" class="role-badge owner">🏪 Owner</span>
      <span id="navUserName" style="font-size:0.88rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
      </button>
      <button class="btn btn-sm" id="logoutBtn">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
    </div>
  </nav>

  <div class="container-fluid px-4 py-4" style="max-width:1400px;margin:0 auto;">
    
    <!-- Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
      <div>
        <a href="/dashboard.php" class="btn-back">
          <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
      </div>
      <h3 style="margin:0;font-weight:800;color:var(--fg-text);display:flex;align-items:center;gap:0.5rem;">
        <i class="bi bi-cart-fill" style="color:var(--fg-primary);"></i>
        Shopping Cart
      </h3>
      <div style="width:140px;"></div>
    </div>

    <!-- Cart Content -->
    <div id="cartContent">
      <div style="text-align:center;padding:3rem;color:var(--fg-muted);">
        <div style="width:32px;height:32px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 1rem;"></div>
        Loading cart…
      </div>
    </div>

  </div>

  <style>
    @keyframes spin { to { transform: rotate(360deg); } }
    
    .cart-grid {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 1.5rem;
      align-items: start;
    }
    
    @media (max-width: 992px) {
      .cart-grid {
        grid-template-columns: 1fr;
      }
    }
    
    .cart-item {
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 14px;
      padding: 1.25rem;
      display: flex;
      gap: 1rem;
      align-items: center;
      transition: all 0.2s;
      margin-bottom: 1rem;
    }
    
    .cart-item:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      border-color: var(--fg-primary);
    }
    
    .cart-item-img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 10px;
      background: var(--fg-bg);
      flex-shrink: 0;
    }
    
    .cart-item-img-ph {
      width: 100px;
      height: 100px;
      border-radius: 10px;
      background: var(--fg-bg);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: var(--fg-muted);
      flex-shrink: 0;
    }
    
    .cart-item-details {
      flex: 1;
      min-width: 0;
    }
    
    .cart-item-category {
      font-size: 0.7rem;
      font-weight: 700;
      color: var(--fg-primary);
      background: rgba(230,168,0,0.1);
      border: 1px solid rgba(230,168,0,0.2);
      padding: 0.15rem 0.6rem;
      border-radius: 50px;
      display: inline-block;
      margin-bottom: 0.4rem;
    }
    
    .cart-item-name {
      font-size: 1rem;
      font-weight: 700;
      color: var(--fg-text);
      margin-bottom: 0.3rem;
      line-height: 1.3;
    }
    
    .cart-item-brand {
      font-size: 0.82rem;
      color: var(--fg-muted);
      margin-bottom: 0.5rem;
    }
    
    .cart-item-price {
      font-size: 1.1rem;
      font-weight: 800;
      color: var(--fg-primary);
    }
    
    .cart-item-actions {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      align-items: flex-end;
    }
    
    .qty-control {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      background: var(--fg-bg);
      border: 1.5px solid var(--fg-border);
      border-radius: 10px;
      padding: 0.3rem;
    }
    
    .qty-btn {
      width: 32px;
      height: 32px;
      border-radius: 6px;
      border: none;
      background: var(--fg-card-bg);
      color: var(--fg-text);
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .qty-btn:hover {
      background: var(--fg-primary);
      color: #fff;
    }
    
    .qty-input {
      width: 60px;
      text-align: center;
      border: 1.5px solid var(--fg-border);
      background: var(--fg-bg);
      border-radius: 6px;
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--fg-text);
      outline: none;
      padding: 0.25rem 0.3rem;
      cursor: text;
      transition: border-color 0.2s;
      -moz-appearance: textfield;
    }

    .qty-input:focus {
      border-color: var(--fg-primary);
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
      -webkit-appearance: none;
    }
    
    .btn-remove {
      padding: 0.4rem 0.9rem;
      border-radius: 8px;
      background: rgba(220,53,69,0.1);
      color: #dc3545;
      border: 1.5px solid rgba(220,53,69,0.25);
      font-size: 0.8rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 0.35rem;
    }
    
    .btn-remove:hover {
      background: #dc3545;
      color: #fff;
      border-color: #dc3545;
    }
    
    .cart-summary {
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 14px;
      padding: 1.5rem;
      position: sticky;
      top: 80px;
    }
    
    .summary-title {
      font-size: 1.1rem;
      font-weight: 800;
      color: var(--fg-text);
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.75rem;
      font-size: 0.9rem;
    }
    
    .summary-row-label {
      color: var(--fg-muted);
    }
    
    .summary-row-value {
      font-weight: 700;
      color: var(--fg-text);
    }
    
    .summary-divider {
      height: 1px;
      background: var(--fg-border);
      margin: 1rem 0;
    }
    
    .summary-total {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    
    .summary-total-label {
      font-size: 1rem;
      font-weight: 700;
      color: var(--fg-text);
    }
    
    .summary-total-value {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--fg-primary);
    }
    
    .btn-checkout {
      width: 100%;
      padding: 0.85rem;
      border-radius: 10px;
      background: var(--fg-primary);
      color: #fff;
      border: none;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }
    
    .btn-checkout:hover {
      background: var(--fg-primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(230,168,0,0.35);
    }
    
    .btn-continue {
      width: 100%;
      padding: 0.75rem;
      border-radius: 10px;
      background: transparent;
      color: var(--fg-muted);
      border: 1.5px solid var(--fg-border);
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 0.75rem;
    }
    
    .btn-continue:hover {
      border-color: var(--fg-text);
      color: var(--fg-text);
    }
    
    .empty-cart {
      text-align: center;
      padding: 4rem 2rem;
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 14px;
    }
    
    .empty-cart-icon {
      font-size: 4rem;
      color: var(--fg-muted);
      opacity: 0.4;
      margin-bottom: 1rem;
    }
    
    .empty-cart-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--fg-text);
      margin-bottom: 0.5rem;
    }
    
    .empty-cart-text {
      font-size: 0.95rem;
      color: var(--fg-muted);
      margin-bottom: 1.5rem;
    }
    
    .supplier-group {
      margin-bottom: 2rem;
    }
    
    .supplier-header {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1rem;
      padding-bottom: 0.75rem;
      border-bottom: 2px solid var(--fg-border);
    }
    
    .supplier-name {
      font-size: 1rem;
      font-weight: 700;
      color: var(--fg-text);
    }
    
    .supplier-icon {
      color: #3b82f6;
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="/assets/js/session-timeout.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Auth check
      const user = FGAuth.UserStore.get();
      if (!user || user.role !== 'owner') {
        window.location.href = '/login.html';
        return;
      }

      // Update navbar
      const navName = document.getElementById('navUserName');
      if (navName) navName.textContent = user.firstName + ' ' + user.lastName;

      // Logout handler
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
          e.preventDefault();
          fetch('/api/logout', { method: 'POST' })
            .finally(() => {
              FGAuth.UserStore.clear();
              window.location.href = '/index.php?logout=true';
            });
        });
      }

      // Render cart
      renderCart();
    });

    function renderCart() {
      const cart = FGCart.getCart();
      const container = document.getElementById('cartContent');

      if (!cart || cart.length === 0) {
        container.innerHTML = `
          <div class="empty-cart">
            <div class="empty-cart-icon"><i class="bi bi-cart-x"></i></div>
            <div class="empty-cart-title">Your cart is empty</div>
            <div class="empty-cart-text">Add products from your dashboard to get started</div>
            <a href="/dashboard.php" class="btn-primary-fg" style="display:inline-flex;text-decoration:none;">
              <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
          </div>`;
        return;
      }

      // Group by supplier
      const grouped = FGCart.getCartBySupplier();
      const itemCount = FGCart.getItemCount();
      const total = FGCart.getTotal();

      let itemsHtml = '';
      Object.entries(grouped).forEach(([supplier, items]) => {
        itemsHtml += `
          <div class="supplier-group">
            <div class="supplier-header">
              <i class="bi bi-person-fill supplier-icon"></i>
              <span class="supplier-name">${escHtml(supplier)}</span>
            </div>`;
        
        items.forEach(item => {
          const img = item.image_path
            ? `<img class="cart-item-img" src="${escHtml(item.image_path)}" alt="" loading="lazy" onerror="this.outerHTML='<div class=\\'cart-item-img-ph\\'><i class=\\'bi bi-image\\'></i></div>'">`
            : `<div class="cart-item-img-ph"><i class="bi bi-image"></i></div>`;
          
          const subtotal = item.srp * item.quantity;
          
          itemsHtml += `
            <div class="cart-item">
              ${img}
              <div class="cart-item-details">
                <span class="cart-item-category">${escHtml(item.category)}</span>
                <div class="cart-item-name">${escHtml(item.item_description)}</div>
                ${item.brand ? `<div class="cart-item-brand">${escHtml(item.brand)}</div>` : ''}
                <div class="cart-item-price">₱${item.srp.toLocaleString('en-PH', {minimumFractionDigits:2})} × ${item.quantity} = ₱${subtotal.toLocaleString('en-PH', {minimumFractionDigits:2})}</div>
              </div>
              <div class="cart-item-actions">
                <div class="qty-control">
                  <button class="qty-btn" onclick="updateQty(${item.id}, -1)">
                    <i class="bi bi-dash"></i>
                  </button>
                  <input type="number" class="qty-input" value="${item.quantity}" min="1" max="${item.maxQty}" 
                         onchange="setQty(${item.id}, this.value)"
                         onclick="this.select()"
                         style="width:60px;-moz-appearance:textfield;">
                  <style>.qty-input::-webkit-outer-spin-button,.qty-input::-webkit-inner-spin-button{-webkit-appearance:none;}</style>
                  <button class="qty-btn" onclick="updateQty(${item.id}, 1)">
                    <i class="bi bi-plus"></i>
                  </button>
                </div>
                <button class="btn-remove" onclick="removeFromCart(${item.id})">
                  <i class="bi bi-trash"></i> Remove
                </button>
              </div>
            </div>`;
        });
        
        itemsHtml += `</div>`;
      });

      container.innerHTML = `
        <div class="cart-grid">
          <div>
            ${itemsHtml}
          </div>
          <div class="cart-summary">
            <div class="summary-title">
              <i class="bi bi-receipt"></i> Order Summary
            </div>
            <div class="summary-row">
              <span class="summary-row-label">Items (${itemCount})</span>
              <span class="summary-row-value">₱${total.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-total">
              <span class="summary-total-label">Total</span>
              <span class="summary-total-value">₱${total.toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
            </div>
            <button class="btn-checkout" onclick="proceedToCheckout()">
              <i class="bi bi-cart-check"></i> Proceed to Checkout
            </button>
            <button class="btn-continue" onclick="window.location.href='/dashboard.php'">
              <i class="bi bi-arrow-left"></i> Continue Shopping
            </button>
          </div>
        </div>`;
    }

    function updateQty(productId, delta) {
      const cart = FGCart.getCart();
      const item = cart.find(i => i.id === productId);
      if (!item) return;
      
      const newQty = item.quantity + delta;
      if (newQty >= 1 && newQty <= item.maxQty) {
        FGCart.updateQuantity(productId, newQty);
        renderCart();
      }
    }

    function setQty(productId, value) {
      const cart = FGCart.getCart();
      const item = cart.find(i => i.id === productId);
      if (!item) return;
      let qty = parseInt(value) || 1;
      // Clamp between 1 and available stock
      qty = Math.max(1, Math.min(qty, item.maxQty || qty));
      FGCart.updateQuantity(productId, qty);
      renderCart();
    }

    function removeFromCart(productId) {
      if (confirm('Remove this item from cart?')) {
        FGCart.removeItem(productId);
        renderCart();
      }
    }

    function proceedToCheckout() {
      window.location.href = 'checkout.php';
    }

    function escHtml(str) {
      return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
  </script>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>

