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
  <title>Checkout — Fix&amp;Go</title>
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
      <img src="/assets/images/logo.png" alt="Fix&Go" style="height:40px;width:auto;object-fit:contain;"
           onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
    </a>
    <div style="display:flex;align-items:center;gap:0.75rem;">
      <span id="navRoleBadge" class="role-badge owner">🏪 Owner</span>
      <span id="navUserName" style="font-size:0.88rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <button class="btn btn-sm" id="logoutBtn" style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
    </div>
  </nav>

  <div class="container-fluid px-4 py-4" style="max-width:1300px;margin:0 auto;">

    <!-- Header -->
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
      <a href="cart.php" class="btn-back"><i class="bi bi-arrow-left"></i> Back to Cart</a>
      <h3 style="margin:0;font-weight:800;color:var(--fg-text);display:flex;align-items:center;gap:0.5rem;">
        <i class="bi bi-bag-check-fill" style="color:var(--fg-primary);"></i> Checkout
      </h3>
    </div>

    <!-- Steps indicator -->
    <div style="display:flex;align-items:center;gap:0;margin-bottom:2rem;background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1rem 1.5rem;overflow-x:auto;">
      <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
        <div style="width:28px;height:28px;border-radius:50%;background:var(--fg-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;">1</div>
        <span style="font-size:0.85rem;font-weight:700;color:var(--fg-primary);">Cart</span>
      </div>
      <div style="flex:1;min-width:30px;height:2px;background:var(--fg-primary);margin:0 0.75rem;"></div>
      <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
        <div style="width:28px;height:28px;border-radius:50%;background:var(--fg-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;">2</div>
        <span style="font-size:0.85rem;font-weight:700;color:var(--fg-primary);">Checkout</span>
      </div>
      <div style="flex:1;min-width:30px;height:2px;background:var(--fg-border);margin:0 0.75rem;"></div>
      <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
        <div style="width:28px;height:28px;border-radius:50%;background:var(--fg-border);color:var(--fg-muted);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;">3</div>
        <span style="font-size:0.85rem;font-weight:600;color:var(--fg-muted);">Payment</span>
      </div>
    </div>

    <!-- Main checkout grid -->
    <div id="checkoutContent">
      <div style="text-align:center;padding:3rem;color:var(--fg-muted);">
        <div style="width:32px;height:32px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 1rem;"></div>
        Loading checkout…
      </div>
    </div>

  </div>

  <!-- Payment loading overlay -->
  <div id="paymentOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(6px);z-index:9999;flex-direction:column;align-items:center;justify-content:center;color:#fff;gap:1.25rem;">
    <div style="width:56px;height:56px;border:4px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;"></div>
    <div style="font-size:1.1rem;font-weight:700;">Creating payment session…</div>
    <div style="font-size:0.85rem;opacity:0.7;">You will be redirected to PayMongo</div>
  </div>

  <style>
    @keyframes spin { to { transform: rotate(360deg); } }

    .checkout-grid {
      display: grid;
      grid-template-columns: 1fr 400px;
      gap: 1.5rem;
      align-items: start;
    }
    @media (max-width: 992px) {
      .checkout-grid { grid-template-columns: 1fr; }
    }

    .section-card {
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 14px;
      margin-bottom: 1.25rem;
      overflow: hidden;
    }
    .section-card-header {
      padding: 1rem 1.5rem;
      border-bottom: 1px solid var(--fg-border);
      display: flex;
      align-items: center;
      gap: 0.6rem;
      background: var(--fg-bg);
    }
    .section-card-header h6 {
      margin: 0;
      font-weight: 700;
      font-size: 0.95rem;
      color: var(--fg-text);
    }
    .section-card-body { padding: 1.5rem; }

    .form-group { margin-bottom: 1.25rem; }
    .form-label {
      display: block;
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--fg-text);
      margin-bottom: 0.4rem;
    }
    .form-label .req { color: #dc3545; }
    .form-control {
      width: 100%;
      padding: 0.65rem 0.9rem;
      border: 1.5px solid var(--fg-border);
      border-radius: 10px;
      background: var(--fg-bg);
      color: var(--fg-text);
      font-size: 0.9rem;
      outline: none;
      transition: border-color 0.2s;
      box-sizing: border-box;
    }
    .form-control:focus { border-color: var(--fg-primary); }
    .form-control::placeholder { color: var(--fg-muted); }
    textarea.form-control { resize: vertical; min-height: 80px; }

    .order-item {
      display: flex;
      gap: 0.75rem;
      align-items: center;
      padding: 0.75rem 0;
      border-bottom: 1px solid var(--fg-border);
    }
    .order-item:last-child { border-bottom: none; }
    .order-item-img {
      width: 56px; height: 56px;
      border-radius: 8px;
      object-fit: cover;
      background: var(--fg-bg);
      flex-shrink: 0;
    }
    .order-item-img-ph {
      width: 56px; height: 56px;
      border-radius: 8px;
      background: var(--fg-bg);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem; color: var(--fg-muted);
      flex-shrink: 0;
    }
    .order-item-info { flex: 1; min-width: 0; }
    .order-item-name {
      font-size: 0.85rem; font-weight: 700;
      color: var(--fg-text); line-height: 1.3;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .order-item-meta { font-size: 0.75rem; color: var(--fg-muted); margin-top: 0.15rem; }
    .order-item-price { font-size: 0.9rem; font-weight: 800; color: var(--fg-primary); flex-shrink: 0; }

    .summary-row {
      display: flex; justify-content: space-between;
      font-size: 0.88rem; margin-bottom: 0.6rem;
    }
    .summary-row-label { color: var(--fg-muted); }
    .summary-row-value { font-weight: 700; color: var(--fg-text); }
    .summary-divider { height: 1px; background: var(--fg-border); margin: 0.75rem 0; }
    .summary-total {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 1.25rem;
    }
    .summary-total-label { font-size: 1rem; font-weight: 700; color: var(--fg-text); }
    .summary-total-value { font-size: 1.5rem; font-weight: 800; color: var(--fg-primary); }

    .btn-pay {
      width: 100%; padding: 0.9rem;
      border-radius: 10px; background: var(--fg-primary);
      color: #fff; border: none;
      font-size: 1rem; font-weight: 700;
      cursor: pointer; transition: all 0.2s;
      display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    }
    .btn-pay:hover {
      background: var(--fg-primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(230,168,0,0.35);
    }
    .btn-pay:disabled {
      opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none;
    }

    .payment-methods {
      display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.75rem;
    }
    .pm-badge {
      padding: 0.3rem 0.75rem;
      border: 1.5px solid var(--fg-border);
      border-radius: 8px;
      font-size: 0.75rem; font-weight: 700;
      color: var(--fg-muted);
      background: var(--fg-bg);
    }

    .secure-note {
      display: flex; align-items: center; gap: 0.4rem;
      font-size: 0.75rem; color: var(--fg-muted);
      margin-top: 0.75rem; justify-content: center;
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="/assets/js/session-timeout.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const user = FGAuth.UserStore.get();
      if (!user || user.role !== 'owner') {
        window.location.href = '/login.html';
        return;
      }

      const navName = document.getElementById('navUserName');
      if (navName) navName.textContent = user.firstName + ' ' + user.lastName;

      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
          e.preventDefault();
          fetch('/api/logout', { method: 'POST' })
            .finally(() => { FGAuth.UserStore.clear(); window.location.href = '/index.php?logout=true'; });
        });
      }

      const cart = FGCart.getCart();
      if (!cart || cart.length === 0) {
        window.location.href = 'cart.php';
        return;
      }

      renderCheckout(user, cart);
    });

    function renderCheckout(user, cart) {
      const grouped = FGCart.getCartBySupplier();
      const itemCount = FGCart.getItemCount();
      const total = FGCart.getTotal();

      // Build order items HTML
      let orderItemsHtml = '';
      cart.forEach(item => {
        const img = item.image_path
          ? `<img class="order-item-img" src="${esc(item.image_path)}" alt="" loading="lazy" onerror="this.outerHTML='<div class=\\'order-item-img-ph\\'><i class=\\'bi bi-image\\'></i></div>'">`
          : `<div class="order-item-img-ph"><i class="bi bi-image"></i></div>`;
        orderItemsHtml += `
          <div class="order-item">
            ${img}
            <div class="order-item-info">
              <div class="order-item-name">${esc(item.item_description)}</div>
              <div class="order-item-meta">${esc(item.category)} · Qty: ${item.quantity}</div>
            </div>
            <div class="order-item-price">₱${(item.srp * item.quantity).toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
          </div>`;
      });

      document.getElementById('checkoutContent').innerHTML = `
        <div class="checkout-grid">

          <!-- LEFT: Delivery details form -->
          <div>
            <div class="section-card">
              <div class="section-card-header">
                <i class="bi bi-geo-alt-fill" style="color:var(--fg-primary);font-size:1rem;"></i>
                <h6>Delivery / Pickup Details</h6>
              </div>
              <div class="section-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                  <div class="form-group">
                    <label class="form-label">First Name <span class="req">*</span></label>
                    <input type="text" class="form-control" id="firstName" value="${esc(user.firstName || '')}" placeholder="First name">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Last Name <span class="req">*</span></label>
                    <input type="text" class="form-control" id="lastName" value="${esc(user.lastName || '')}" placeholder="Last name">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">Email Address <span class="req">*</span></label>
                  <input type="email" class="form-control" id="email" value="${esc(user.email || '')}" placeholder="your@email.com">
                </div>
                <div class="form-group">
                  <label class="form-label">Phone Number <span class="req">*</span></label>
                  <input type="tel" class="form-control" id="phone" placeholder="+63 9XX XXX XXXX">
                </div>
                <div class="form-group">
                  <label class="form-label">Delivery Address <span class="req">*</span></label>
                  <input type="text" class="form-control" id="address" placeholder="Street, Barangay">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                  <div class="form-group">
                    <label class="form-label">City / Municipality <span class="req">*</span></label>
                    <input type="text" class="form-control" id="city" placeholder="City">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Province</label>
                    <input type="text" class="form-control" id="province" placeholder="Province">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">Order Notes <span style="color:var(--fg-muted);font-weight:400;">(optional)</span></label>
                  <textarea class="form-control" id="notes" placeholder="Special instructions, preferred delivery time, etc."></textarea>
                </div>
              </div>
            </div>

            <div class="section-card">
              <div class="section-card-header">
                <i class="bi bi-truck" style="color:#3b82f6;font-size:1rem;"></i>
                <h6>Delivery Method</h6>
              </div>
              <div class="section-card-body">
                <label style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;border:2px solid var(--fg-primary);border-radius:10px;cursor:pointer;margin-bottom:0.75rem;background:rgba(230,168,0,0.04);">
                  <input type="radio" name="delivery" value="pickup" checked style="accent-color:var(--fg-primary);width:16px;height:16px;">
                  <div>
                    <div style="font-weight:700;font-size:0.9rem;color:var(--fg-text);">🏪 Shop Pickup</div>
                    <div style="font-size:0.78rem;color:var(--fg-muted);">Pick up at the supplier's location — Free</div>
                  </div>
                </label>
                <label style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;border:1.5px solid var(--fg-border);border-radius:10px;cursor:pointer;background:var(--fg-bg);">
                  <input type="radio" name="delivery" value="delivery" style="accent-color:var(--fg-primary);width:16px;height:16px;">
                  <div>
                    <div style="font-weight:700;font-size:0.9rem;color:var(--fg-text);">🚚 Delivery</div>
                    <div style="font-size:0.78rem;color:var(--fg-muted);">Delivered to your address — Fee may apply</div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- RIGHT: Order summary -->
          <div>
            <div class="section-card" style="position:sticky;top:80px;">
              <div class="section-card-header">
                <i class="bi bi-receipt" style="color:var(--fg-primary);font-size:1rem;"></i>
                <h6>Order Summary</h6>
                <span style="margin-left:auto;font-size:0.75rem;color:var(--fg-muted);">${itemCount} item${itemCount !== 1 ? 's' : ''}</span>
              </div>
              <div class="section-card-body">
                <!-- Items list -->
                <div style="max-height:280px;overflow-y:auto;margin-bottom:1rem;">
                  ${orderItemsHtml}
                </div>

                <!-- Totals -->
                <div class="summary-row">
                  <span class="summary-row-label">Subtotal</span>
                  <span class="summary-row-value">₱${total.toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
                </div>
                <div class="summary-row">
                  <span class="summary-row-label">Delivery Fee</span>
                  <span class="summary-row-value" id="deliveryFeeDisplay">Free</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-total">
                  <span class="summary-total-label">Total</span>
                  <span class="summary-total-value" id="grandTotalDisplay">₱${total.toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
                </div>

                <button class="btn-pay" id="btnProceedPayment" onclick="proceedToPayment()">
                  <i class="bi bi-lock-fill"></i> Pay ₱${total.toLocaleString('en-PH',{minimumFractionDigits:2})}
                </button>

                <div class="payment-methods">
                  <span class="pm-badge">💳 Card</span>
                  <span class="pm-badge">📱 GCash</span>
                  <span class="pm-badge">💜 Maya</span>
                  <span class="pm-badge">🟢 GrabPay</span>
                </div>

                <div class="secure-note">
                  <i class="bi bi-shield-lock-fill" style="color:#28A745;"></i>
                  Secured by PayMongo
                </div>
              </div>
            </div>
          </div>

        </div>`;

      // Listen for delivery method change
      document.querySelectorAll('input[name="delivery"]').forEach(radio => {
        radio.addEventListener('change', function() {
          const isDelivery = this.value === 'delivery';
          document.getElementById('deliveryFeeDisplay').textContent = isDelivery ? '₱50.00' : 'Free';
          const newTotal = isDelivery ? total + 50 : total;
          document.getElementById('grandTotalDisplay').textContent = '₱' + newTotal.toLocaleString('en-PH',{minimumFractionDigits:2});
          document.getElementById('btnProceedPayment').innerHTML = `<i class="bi bi-lock-fill"></i> Pay ₱${newTotal.toLocaleString('en-PH',{minimumFractionDigits:2})}`;
          // Style the selected radio card
          document.querySelectorAll('input[name="delivery"]').forEach(r => {
            const card = r.closest('label');
            if (r.checked) {
              card.style.border = '2px solid var(--fg-primary)';
              card.style.background = 'rgba(230,168,0,0.04)';
            } else {
              card.style.border = '1.5px solid var(--fg-border)';
              card.style.background = 'var(--fg-bg)';
            }
          });
        });
      });
    }

    function proceedToPayment() {
      // Validate form
      const required = ['firstName','lastName','email','phone','address','city'];
      let valid = true;
      required.forEach(id => {
        const el = document.getElementById(id);
        if (!el || !el.value.trim()) {
          el && (el.style.borderColor = '#dc3545');
          valid = false;
        } else {
          el.style.borderColor = '';
        }
      });

      if (!valid) {
        showError('Please fill in all required fields.');
        return;
      }

      const cart = FGCart.getCart();
      if (!cart || cart.length === 0) {
        showError('Your cart is empty.');
        return;
      }

      // Build product_ids and quantities
      const productIds = cart.map(i => i.id);
      const quantities = {};
      cart.forEach(i => { quantities[i.id] = i.quantity; });

      // Show loading overlay
      const overlay = document.getElementById('paymentOverlay');
      overlay.style.display = 'flex';

      fetch('/api/paymongo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'create_checkout',
          product_ids: productIds,
          quantities: quantities
        })
      })
      .then(r => r.json())
      .then(data => {
        overlay.style.display = 'none';
        if (!data.success) {
          showError('Payment error: ' + (data.message || 'Unknown error'));
          return;
        }
        // Clear cart after successful checkout session creation
        FGCart.clearCart();
        // Redirect to PayMongo
        window.location.href = data.checkout_url;
      })
      .catch(err => {
        overlay.style.display = 'none';
        showError('Could not connect to payment server. Please try again.');
        console.error(err);
      });
    }

    function showError(msg) {
      let el = document.getElementById('checkoutError');
      if (!el) {
        el = document.createElement('div');
        el.id = 'checkoutError';
        el.style.cssText = 'position:fixed;top:80px;right:20px;background:#dc3545;color:#fff;padding:1rem 1.5rem;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.2);z-index:10000;font-weight:600;font-size:0.9rem;display:flex;align-items:center;gap:0.75rem;';
        document.body.appendChild(el);
      }
      el.innerHTML = `<i class="bi bi-exclamation-circle-fill"></i> ${esc(msg)}`;
      el.style.display = 'flex';
      setTimeout(() => { if (el) el.style.display = 'none'; }, 5000);
    }

    function esc(str) {
      return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
  </script>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>

