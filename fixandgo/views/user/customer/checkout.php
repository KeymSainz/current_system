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
  <title>Fix&amp;Go — Checkout</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: #f5f5f5; font-family: 'Segoe UI', sans-serif; }
    [data-theme="dark"] body { background: #0d0d0d; }
    .co-wrap { max-width: 900px; margin: 0 auto; padding: 1.5rem 1rem 4rem; }
    /* Section card */
    .co-card { background: #fff; border-radius: 4px; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
    [data-theme="dark"] .co-card { background: #1a1a1a; }
    .co-card-body { padding: 1.25rem 1.5rem; }
    /* Address section */
    .addr-row { display: flex; align-items: flex-start; gap: 1rem; flex-wrap: wrap; }
    .addr-pin { color: #e6a800; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 0.3rem; white-space: nowrap; }
    .addr-detail { flex: 1; min-width: 0; }
    .addr-name { font-weight: 700; font-size: 0.92rem; color: #333; }
    [data-theme="dark"] .addr-name { color: #eee; }
    .addr-text { font-size: 0.85rem; color: #555; margin-top: 0.2rem; line-height: 1.5; }
    [data-theme="dark"] .addr-text { color: #aaa; }
    .addr-default { border: 1px solid #e6a800; color: #e6a800; font-size: 0.68rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 2px; margin-left: 0.5rem; }
    .addr-change { color: #e6a800; font-size: 0.85rem; font-weight: 600; cursor: pointer; white-space: nowrap; text-decoration: none; }
    .addr-change:hover { text-decoration: underline; }
    /* Products table */
    .prod-table-head { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1rem; padding: 0.75rem 1.5rem; border-bottom: 1px solid #f0f0f0; font-size: 0.8rem; color: #888; }
    [data-theme="dark"] .prod-table-head { border-color: #2a2a2a; color: #666; }
    .prod-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1rem; padding: 1rem 1.5rem; align-items: center; border-bottom: 1px solid #f9f9f9; }
    [data-theme="dark"] .prod-row { border-color: #222; }
    .prod-img { width: 56px; height: 56px; border-radius: 4px; object-fit: cover; border: 1px solid #eee; flex-shrink: 0; }
    .prod-img-ph { width: 56px; height: 56px; border-radius: 4px; background: #f5f5f5; border: 1px solid #eee; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 1.3rem; flex-shrink: 0; }
    .prod-name { font-size: 0.85rem; color: #333; font-weight: 500; }
    [data-theme="dark"] .prod-name { color: #ddd; }
    .prod-price { font-size: 0.9rem; color: #e6a800; font-weight: 600; }
    .prod-qty { font-size: 0.88rem; color: #555; text-align: center; }
    [data-theme="dark"] .prod-qty { color: #aaa; }
    .prod-subtotal { font-size: 0.9rem; color: #e6a800; font-weight: 700; text-align: right; }
    /* Shop row */
    .shop-row { padding: 0.75rem 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 0.5rem; }
    [data-theme="dark"] .shop-row { border-color: #2a2a2a; }
    .shop-badge { background: #e6a800; color: #fff; font-size: 0.65rem; font-weight: 800; padding: 0.15rem 0.4rem; border-radius: 2px; }
    .shop-name { font-size: 0.85rem; font-weight: 700; color: #333; }
    [data-theme="dark"] .shop-name { color: #eee; }
    /* Message row */
    .msg-row { display: flex; align-items: center; gap: 1rem; padding: 0.75rem 1.5rem; border-bottom: 1px solid #f0f0f0; flex-wrap: wrap; }
    [data-theme="dark"] .msg-row { border-color: #2a2a2a; }
    .msg-label { font-size: 0.82rem; color: #888; white-space: nowrap; }
    .msg-input { flex: 1; min-width: 200px; border: 1px solid #e0e0e0; border-radius: 2px; padding: 0.45rem 0.75rem; font-size: 0.82rem; color: #333; outline: none; background: #fff; }
    [data-theme="dark"] .msg-input { background: #111; border-color: #333; color: #ddd; }
    .msg-input:focus { border-color: #e6a800; }
    /* Shipping row */
    .ship-row { display: flex; align-items: center; gap: 1rem; padding: 0.75rem 1.5rem; flex-wrap: wrap; }
    .ship-label { font-size: 0.82rem; color: #888; }
    .ship-option { font-size: 0.85rem; color: #333; font-weight: 600; }
    [data-theme="dark"] .ship-option { color: #ddd; }
    .ship-date { font-size: 0.75rem; color: #26aa99; display: flex; align-items: center; gap: 0.3rem; }
    .ship-price { margin-left: auto; font-size: 0.88rem; font-weight: 700; color: #333; }
    [data-theme="dark"] .ship-price { color: #ddd; }
    /* Order total row */
    .order-total-row { display: flex; justify-content: flex-end; align-items: center; gap: 1rem; padding: 0.75rem 1.5rem; border-top: 1px solid #f0f0f0; }
    [data-theme="dark"] .order-total-row { border-color: #2a2a2a; }
    .order-total-label { font-size: 0.85rem; color: #555; }
    [data-theme="dark"] .order-total-label { color: #aaa; }
    .order-total-val { font-size: 1.1rem; font-weight: 800; color: #e6a800; }
    /* Payment method */
    .pay-section { padding: 1.25rem 1.5rem; }
    .pay-title { font-size: 0.95rem; font-weight: 700; color: #333; margin-bottom: 1rem; }
    [data-theme="dark"] .pay-title { color: #eee; }
    .pay-options { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .pay-opt { display: flex; align-items: center; gap: 0.5rem; padding: 0.55rem 1rem; border: 1.5px solid #e0e0e0; border-radius: 4px; cursor: pointer; font-size: 0.85rem; color: #555; transition: all 0.15s; }
    [data-theme="dark"] .pay-opt { border-color: #333; color: #aaa; }
    .pay-opt.active { border-color: #e6a800; color: #e6a800; background: rgba(230,168,0,0.08); }
    .pay-opt input { display: none; }
    @media (max-width: 640px) {
      .prod-table-head, .prod-row { grid-template-columns: 2fr 1fr 1fr; }
      .prod-table-head > *:nth-child(3), .prod-row > *:nth-child(3) { display: none; }
    }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; font-size: 0.85rem; }
    .summary-label { color: #888; }
    [data-theme="dark"] .summary-label { color: #666; }
    .summary-val { color: #333; font-weight: 500; }
    [data-theme="dark"] .summary-val { color: #ccc; }
    .summary-total-label { font-size: 0.9rem; color: #555; font-weight: 600; }
    [data-theme="dark"] .summary-total-label { color: #aaa; }
    .summary-total-val { font-size: 1.4rem; font-weight: 900; color: #e6a800; }
    /* Place order button */
    .place-order-btn { width: 100%; padding: 0.9rem; background: #e6a800; color: #fff; border: none; border-radius: 4px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background 0.2s; margin-top: 1rem; }
    .place-order-btn:hover { background: #c98f00; }
    .place-order-btn:disabled { background: #ccc; cursor: not-allowed; }
    /* Address warning */
    .addr-warning { background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 1rem 1.25rem; display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.75rem; }
    .addr-warning i { color: #e6a800; font-size: 1.1rem; flex-shrink: 0; margin-top: 0.1rem; }
    .addr-warning-text { font-size: 0.85rem; color: #856404; }
    .addr-warning-text a { color: #e6a800; font-weight: 700; }
    /* Navbar */
    .co-navbar { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
    [data-theme="dark"] .co-navbar { background: #111; border-color: #222; }
    .co-navbar-brand { font-size: 1.2rem; font-weight: 800; color: #e6a800; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; }
    .co-navbar-title { font-size: 1rem; font-weight: 700; color: #555; border-left: 1.5px solid #e0e0e0; padding-left: 1rem; margin-left: 0.5rem; }
    [data-theme="dark"] .co-navbar-title { color: #aaa; border-color: #333; }
    /* Back button */
    .co-navbar-back { display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 50%; border: 1.5px solid #e0e0e0; color: #555; text-decoration: none; font-size: 0.95rem; transition: all 0.15s; flex-shrink: 0; }
    [data-theme="dark"] .co-navbar-back { border-color: #333; color: #aaa; }
    .co-navbar-back:hover { border-color: #e6a800; color: #e6a800; background: rgba(230,168,0,0.08); }
    .co-section-title { font-size: 0.95rem; font-weight: 700; color: #333; padding: 1rem 1.5rem 0; }
    [data-theme="dark"] .co-section-title { color: #eee; }
    /* Card input form */
    .card-form { display:none; margin-top:1rem; padding:1rem 1.25rem; background:#f9f9f9; border-radius:6px; border:1px solid #e8e8e8; }
    [data-theme="dark"] .card-form { background:#111; border-color:#2a2a2a; }
    .card-form.show { display:block; }
    .card-form-title { font-size:0.8rem; font-weight:700; color:#555; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.4rem; }
    [data-theme="dark"] .card-form-title { color:#aaa; }
    .card-input-group { margin-bottom:0.65rem; }
    .card-input-group label { display:block; font-size:0.75rem; font-weight:700; color:#555; margin-bottom:0.3rem; }
    [data-theme="dark"] .card-input-group label { color:#aaa; }
    .card-input { width:100%; padding:0.55rem 0.85rem; border:1.5px solid #e0e0e0; border-radius:4px; font-size:0.85rem; color:#333; background:#fff; outline:none; transition:border-color 0.15s; font-family:inherit; }
    [data-theme="dark"] .card-input { background:#1a1a1a; border-color:#333; color:#ddd; }
    .card-input:focus { border-color:#e6a800; }
    .card-row { display:grid; grid-template-columns:1fr 1fr; gap:0.65rem; }
    .card-brands { display:flex; gap:0.4rem; margin-bottom:0.75rem; }
    .card-brand-badge { font-size:0.7rem; font-weight:700; padding:0.2rem 0.5rem; border-radius:3px; border:1px solid #e0e0e0; color:#555; }
    [data-theme="dark"] .card-brand-badge { border-color:#333; color:#aaa; }
    /* GCash info */
    .gcash-info { display:none; margin-top:1rem; padding:0.85rem 1.1rem; background:#f0f7ff; border:1px solid #bee3f8; border-radius:6px; font-size:0.82rem; color:#2b6cb0; }
    [data-theme="dark"] .gcash-info { background:#0a1929; border-color:#1a3a5c; color:#63b3ed; }
    .gcash-info.show { display:flex; align-items:flex-start; gap:0.6rem; }
    /* Payment alert */
    .pay-alert { display:none; padding:0.75rem 1rem; border-radius:4px; font-size:0.83rem; font-weight:600; margin-top:0.75rem; align-items:center; gap:0.5rem; }
    .pay-alert.show { display:flex; }
    .pay-alert-error { background:rgba(220,53,69,0.1); color:#dc3545; border:1px solid rgba(220,53,69,0.3); }
    .pay-alert-info  { background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.3); }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="co-navbar">
    <div style="display:flex;align-items:center;gap:0.75rem;">
      <a href="../../../index.php" class="co-navbar-back" title="Back to Shop">
        <i class="bi bi-arrow-left"></i>
      </a>
      <a href="../../../index.php" class="co-navbar-brand">
        <i class="bi bi-wrench-adjustable-circle-fill"></i>
        Fix&amp;Go
      </a>
    </div>
    <span class="co-navbar-title">Checkout</span>
  </nav>

  <div class="co-wrap">

    <!-- Address Warning (shown if address not verified) -->
    <div id="addressWarning" class="addr-warning" style="display:none;">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <div class="addr-warning-text">
        Please complete your delivery address before placing an order.
        <a href="profile.php">Go to Profile</a>
      </div>
    </div>

    <!-- Delivery Address Section -->
    <div class="co-card">
      <div class="co-section-title">
        <i class="bi bi-geo-alt-fill" style="color:#e6a800;"></i>
        Delivery Address
      </div>
      <div class="co-card-body">
        <div id="addressDisplay" class="addr-row">
          <div class="addr-pin">
            <i class="bi bi-geo-alt-fill"></i>
            Delivery Address
          </div>
          <div class="addr-detail">
            <div class="addr-name">
              <span id="addrName">Loading...</span>
              <span id="addrPhone"></span>
            </div>
            <div class="addr-text" id="addrFull">Loading address...</div>
          </div>
          <a href="profile.php" class="addr-change">Change</a>
        </div>
      </div>
    </div>

    <!-- Products Section -->
    <div class="co-card">
      <div class="prod-table-head">
        <div>Product</div>
        <div style="text-align:center;">Unit Price</div>
        <div style="text-align:center;">Quantity</div>
        <div style="text-align:right;">Subtotal</div>
      </div>
      <div id="productsContainer">
        <!-- Products will be rendered here -->
      </div>
    </div>

    <!-- Payment Method Section -->
    <div class="co-card">
      <div class="pay-section">
        <div class="pay-title">Payment Method</div>
        <div class="pay-options">
          <label class="pay-opt active" id="optCod">
            <input type="radio" name="payment" value="cod" checked />
            <i class="bi bi-cash-coin"></i>
            Cash on Delivery
          </label>
          <label class="pay-opt" id="optGcash">
            <input type="radio" name="payment" value="gcash" />
            <i class="bi bi-phone-fill" style="color:#0070ba;"></i>
            GCash
          </label>
          <label class="pay-opt" id="optCard">
            <input type="radio" name="payment" value="card" />
            <i class="bi bi-credit-card-fill"></i>
            Credit/Debit Card
          </label>
        </div>

        <!-- GCash info -->
        <div class="gcash-info" id="gcashInfo">
          <i class="bi bi-info-circle-fill" style="flex-shrink:0;margin-top:0.1rem;"></i>
          <div>You'll be redirected to <strong>PayMongo</strong> to complete your GCash payment securely. Make sure your GCash app is ready.</div>
        </div>

        <!-- Card input form -->
        <div class="card-form" id="cardForm">
          <div class="card-form-title">
            <i class="bi bi-lock-fill" style="color:#28a745;"></i>
            Secure Card Details
            <span style="margin-left:auto;font-size:0.7rem;font-weight:400;color:#888;">Powered by PayMongo</span>
          </div>
          <div class="card-brands">
            <span class="card-brand-badge">VISA</span>
            <span class="card-brand-badge">Mastercard</span>
            <span class="card-brand-badge">JCB</span>
          </div>
          <div class="card-input-group">
            <label>Card Number <span style="color:#dc3545;">*</span></label>
            <input type="text" class="card-input" id="cardNumber"
              placeholder="1234 5678 9012 3456" maxlength="19"
              inputmode="numeric" autocomplete="cc-number" />
          </div>
          <div class="card-input-group">
            <label>Cardholder Name <span style="color:#dc3545;">*</span></label>
            <input type="text" class="card-input" id="cardName"
              placeholder="Name as on card" autocomplete="cc-name" />
          </div>
          <div class="card-row">
            <div class="card-input-group">
              <label>Expiry Date <span style="color:#dc3545;">*</span></label>
              <input type="text" class="card-input" id="cardExpiry"
                placeholder="MM / YY" maxlength="7"
                inputmode="numeric" autocomplete="cc-exp" />
            </div>
            <div class="card-input-group">
              <label>CVV <span style="color:#dc3545;">*</span></label>
              <input type="text" class="card-input" id="cardCvv"
                placeholder="123" maxlength="4"
                inputmode="numeric" autocomplete="cc-csc" />
            </div>
          </div>
          <div style="font-size:0.72rem;color:#888;margin-top:0.25rem;">
            <i class="bi bi-shield-lock-fill" style="color:#28a745;"></i>
            Your card details are used to verify your card. Payment is processed securely by PayMongo.
          </div>
        </div>

        <!-- Payment alert -->
        <div class="pay-alert pay-alert-error" id="payAlert"></div>
      </div>
    </div>

    <!-- Order Summary Section -->
    <div class="co-card">
      <div class="co-card-body">
        <div class="summary-row">
          <span class="summary-label">Merchandise Subtotal:</span>
          <span class="summary-val" id="summarySubtotal">₱0.00</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Shipping Fee:</span>
          <span class="summary-val" id="summaryShipping">₱0.00</span>
        </div>
        <div class="summary-row" style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid #f0f0f0;">
          <span class="summary-total-label">Total Payment:</span>
          <span class="summary-total-val" id="summaryTotal">₱0.00</span>
        </div>
        <button id="placeOrderBtn" class="place-order-btn" disabled>
          <i class="bi bi-bag-check-fill"></i> Place Order
        </button>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>

  <script>
  // =====================================================================
  // CUSTOMER CART (sessionStorage)
  // =====================================================================
  const FGCustomerCart = (function () {
    const KEY = 'fg_customer_cart';
    function get() {
      try { return JSON.parse(sessionStorage.getItem(KEY) || '[]'); } catch(e) { return []; }
    }
    function save(cart) {
      sessionStorage.setItem(KEY, JSON.stringify(cart));
    }
    function clear() { sessionStorage.removeItem(KEY); }
    function total() { return get().reduce((s, i) => s + i.srp * i.quantity, 0); }
    return { get, clear, total };
  })();

  // =====================================================================
  // CHECKOUT PAGE LOGIC
  // =====================================================================
  (function() {
    'use strict';

    let addressVerified = false;
    let userAddress = null;
    const cart = FGCustomerCart.get();
    const SHIPPING_FEE = 50;

    // ── Initialize ──────────────────────────────────────────────
    async function init() {
      const user = FGAuth.UserStore.get();
      if (!user || user.role !== 'customer') {
        alert('Please log in to continue.');
        window.location.href = '../../../login.html';
        return;
      }
      if (!cart || cart.length === 0) {
        alert('Your cart is empty.');
        window.location.href = '../../../index.php';
        return;
      }

      // Check for payment return messages
      const params = new URLSearchParams(window.location.search);
      if (params.get('payment') === 'cancelled') showPayAlert('Payment was cancelled. You can try again.', 'info');
      if (params.get('payment') === 'failed')    showPayAlert('Payment could not be verified. Please try again.', 'error');

      await loadAddress();
      renderProducts();
      updateSummary();
      setupPaymentToggle();
      setupCardFormatting();
      document.getElementById('placeOrderBtn').addEventListener('click', placeOrder);
    }

    // ── Load customer address ───────────────────────────────────
    async function loadAddress() {
      try {
        const res  = await fetch('../../../backend/customer_profile.php?action=get', { credentials: 'include' });
        const data = await res.json();
        if (!data.success) { showPayAlert('Failed to load address.', 'error'); return; }

        userAddress    = data.user;
        addressVerified = data.address_complete && userAddress.address_verified;

        document.getElementById('addrName').textContent  = `${userAddress.first_name} ${userAddress.last_name}`;
        document.getElementById('addrPhone').textContent = userAddress.phone ? `(+63) ${userAddress.phone}` : '';

        if (addressVerified) {
          document.getElementById('addrFull').textContent = `${userAddress.address_line}, ${userAddress.barangay}, ${userAddress.city}, ${userAddress.province} ${userAddress.zip_code}`;
          document.getElementById('addressWarning').style.display = 'none';
          document.getElementById('placeOrderBtn').disabled = false;
        } else {
          document.getElementById('addrFull').textContent = 'Address not complete. Please update your profile.';
          document.getElementById('addrFull').style.color = '#e6a800';
          document.getElementById('addressWarning').style.display = 'flex';
          document.getElementById('placeOrderBtn').disabled = true;
        }
      } catch (err) {
        showPayAlert('Failed to load address.', 'error');
      }
    }

    // ── Render products ─────────────────────────────────────────
    function renderProducts() {
      const container = document.getElementById('productsContainer');
      container.innerHTML = '';
      cart.forEach(item => {
        const row = document.createElement('div');
        row.className = 'prod-row';
        const imgHtml = item.image_path
          ? `<img src="../../../${item.image_path}" class="prod-img" alt="${escapeHtml(item.item_description)}" />`
          : `<div class="prod-img-ph"><i class="bi bi-image"></i></div>`;
        row.innerHTML = `
          <div style="display:flex;gap:0.75rem;align-items:center;">
            ${imgHtml}
            <div>
              <div class="prod-name">${escapeHtml(item.item_description)}</div>
              <div style="font-size:0.75rem;color:#888;margin-top:0.2rem;">
                ${escapeHtml(item.category)}${item.brand ? ' • ' + escapeHtml(item.brand) : ''}
              </div>
            </div>
          </div>
          <div class="prod-price">₱${item.srp.toFixed(2)}</div>
          <div class="prod-qty">${item.quantity}</div>
          <div class="prod-subtotal">₱${(item.srp * item.quantity).toFixed(2)}</div>`;
        container.appendChild(row);
      });
    }

    // ── Update summary ──────────────────────────────────────────
    function updateSummary() {
      const subtotal = FGCustomerCart.total();
      const total    = subtotal + SHIPPING_FEE;
      document.getElementById('summarySubtotal').textContent = `₱${subtotal.toFixed(2)}`;
      document.getElementById('summaryShipping').textContent = `₱${SHIPPING_FEE.toFixed(2)}`;
      document.getElementById('summaryTotal').textContent    = `₱${total.toFixed(2)}`;
    }

    // ── Payment method toggle ───────────────────────────────────
    function setupPaymentToggle() {
      const opts      = document.querySelectorAll('.pay-opt');
      const cardForm  = document.getElementById('cardForm');
      const gcashInfo = document.getElementById('gcashInfo');
      const btn       = document.getElementById('placeOrderBtn');

      opts.forEach(opt => {
        opt.addEventListener('click', () => {
          opts.forEach(o => o.classList.remove('active'));
          opt.classList.add('active');
          opt.querySelector('input').checked = true;

          const val = opt.querySelector('input').value;
          cardForm.classList.toggle('show',  val === 'card');
          gcashInfo.classList.toggle('show', val === 'gcash');

          // Update button label
          if (val === 'gcash')       btn.innerHTML = '<i class="bi bi-phone-fill"></i> Pay with GCash';
          else if (val === 'card')   btn.innerHTML = '<i class="bi bi-credit-card-fill"></i> Pay with Card';
          else                       btn.innerHTML = '<i class="bi bi-bag-check-fill"></i> Place Order';

          hidePayAlert();
        });
      });
    }

    // ── Card number / expiry formatting ────────────────────────
    function setupCardFormatting() {
      const numInput = document.getElementById('cardNumber');
      numInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g, '').substring(0, 16);
        this.value = v.replace(/(.{4})/g, '$1 ').trim();
      });

      const expInput = document.getElementById('cardExpiry');
      expInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g, '').substring(0, 4);
        if (v.length >= 3) v = v.substring(0,2) + ' / ' + v.substring(2);
        this.value = v;
      });

      const cvvInput = document.getElementById('cardCvv');
      cvvInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 4);
      });
    }

    // ── Validate card fields ────────────────────────────────────
    function validateCard() {
      const num  = document.getElementById('cardNumber').value.replace(/\s/g, '');
      const name = document.getElementById('cardName').value.trim();
      const exp  = document.getElementById('cardExpiry').value.replace(/\s/g, '');
      const cvv  = document.getElementById('cardCvv').value.trim();

      if (num.length < 13 || num.length > 16) { showPayAlert('Please enter a valid card number.', 'error'); return false; }
      if (!name)                               { showPayAlert('Please enter the cardholder name.', 'error'); return false; }
      if (exp.length < 4)                      { showPayAlert('Please enter a valid expiry date (MM/YY).', 'error'); return false; }
      if (cvv.length < 3)                      { showPayAlert('Please enter a valid CVV.', 'error'); return false; }
      return true;
    }

    // ── Place order ─────────────────────────────────────────────
    async function placeOrder() {
      if (!addressVerified) {
        showPayAlert('Please complete your delivery address before placing an order.', 'error');
        return;
      }

      const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
      const btn = document.getElementById('placeOrderBtn');

      // ── COD: place directly ──────────────────────────────────
      if (paymentMethod === 'cod') {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing…';
        try {
          const promises = cart.map(item =>
            fetch('../../../api/customer/orders', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              credentials: 'include',
              body: JSON.stringify({ action: 'place', product_id: item.id, quantity: item.quantity, payment_method: 'cod', notes: '' })
            }).then(r => r.json())
          );
          const results = await Promise.all(promises);
          const failed  = results.filter(r => !r.success);
          if (failed.length > 0) throw new Error(failed[0].message || 'Some orders failed.');
          FGCustomerCart.clear();
          alert('Order placed successfully! Thank you for your purchase.');
          window.location.href = 'orders.php';
        } catch (err) {
          showPayAlert(err.message || 'Failed to place order. Please try again.', 'error');
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-bag-check-fill"></i> Place Order';
        }
        return;
      }

      // ── GCash / Card: go through PayMongo ───────────────────
      if (paymentMethod === 'card' && !validateCard()) return;

      btn.disabled = true;
      btn.innerHTML = paymentMethod === 'gcash'
        ? '<i class="bi bi-hourglass-split"></i> Redirecting to GCash…'
        : '<i class="bi bi-hourglass-split"></i> Redirecting to PayMongo…';

      try {
        const res  = await fetch('../../../backend/customer_paymongo.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({
            payment_method: paymentMethod,
            cart: cart.map(i => ({ id: i.id, item_description: i.item_description, srp: i.srp, quantity: i.quantity })),
          })
        });
        const data = await res.json();

        if (!data.success) throw new Error(data.message || 'Could not create payment session.');

        // Clear cart before redirect (PayMongo return will place orders)
        FGCustomerCart.clear();

        // Redirect to PayMongo hosted checkout
        window.location.href = data.checkout_url;

      } catch (err) {
        showPayAlert(err.message || 'Payment failed. Please try again.', 'error');
        btn.disabled = false;
        btn.innerHTML = paymentMethod === 'gcash'
          ? '<i class="bi bi-phone-fill"></i> Pay with GCash'
          : '<i class="bi bi-credit-card-fill"></i> Pay with Card';
      }
    }

    // ── Helpers ─────────────────────────────────────────────────
    function showPayAlert(msg, type) {
      const el = document.getElementById('payAlert');
      el.className = 'pay-alert pay-alert-' + (type === 'info' ? 'info' : 'error') + ' show';
      el.innerHTML = `<i class="bi bi-${type === 'info' ? 'info-circle-fill' : 'exclamation-triangle-fill'}"></i> ${msg}`;
    }
    function hidePayAlert() {
      const el = document.getElementById('payAlert');
      if (el) el.className = 'pay-alert pay-alert-error';
    }
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    init();
  })();
  </script>


</body>
</html>



