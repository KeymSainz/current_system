<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- PWA -->
  <meta name="theme-color" content="#e6a800">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Fix&amp;Go">
  <link rel="manifest" href="fixandgo/manifest.json">
  <link rel="apple-touch-icon" href="fixandgo/assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="fixandgo/assets/css/mobile.css">
  <title>Fix&amp;Go — Payment Successful</title>
  <link rel="stylesheet" href="fixandgo/assets/css/auth.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body {
      background: var(--fg-bg);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .success-wrap {
      width: 100%;
      max-width: 460px;
      text-align: center;
    }

    /* Logo */
    .success-logo {
      margin-bottom: 2rem;
    }
    .success-logo img {
      height: 52px;
      object-fit: contain;
    }

    /* Card */
    .success-card {
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.35);
      padding: 2.5rem 2rem 2rem;
      position: relative;
      overflow: hidden;
    }

    /* Green top bar */
    .success-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, #28A745, #10b981, #28A745);
    }

    /* Checkmark circle */
    .check-circle {
      width: 88px;
      height: 88px;
      border-radius: 50%;
      background: rgba(40,167,69,0.12);
      border: 3px solid rgba(40,167,69,0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      animation: popIn 0.5s cubic-bezier(0.16,1,0.3,1) both;
    }
    .check-circle i {
      font-size: 2.5rem;
      color: #28A745;
      animation: checkIn 0.4s 0.2s cubic-bezier(0.16,1,0.3,1) both;
    }
    @keyframes popIn {
      from { transform: scale(0.5); opacity: 0; }
      to   { transform: scale(1);   opacity: 1; }
    }
    @keyframes checkIn {
      from { transform: scale(0) rotate(-20deg); opacity: 0; }
      to   { transform: scale(1) rotate(0);      opacity: 1; }
    }

    .success-title {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--fg-text);
      margin: 0 0 0.4rem;
    }
    .success-subtitle {
      font-size: 0.9rem;
      color: var(--fg-muted);
      margin: 0 0 1.75rem;
      line-height: 1.6;
    }

    /* Receipt box */
    .receipt {
      background: var(--fg-bg);
      border: 1px solid var(--fg-border);
      border-radius: 14px;
      padding: 1.25rem;
      margin-bottom: 1.75rem;
      text-align: left;
    }
    .receipt-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.45rem 0;
      border-bottom: 1px solid var(--fg-border);
      font-size: 0.85rem;
    }
    .receipt-row:last-child { border-bottom: none; }
    .receipt-label { color: var(--fg-muted); font-weight: 500; }
    .receipt-value { color: var(--fg-text); font-weight: 700; }
    .receipt-value.green { color: #28A745; }
    .receipt-value.gold  { color: var(--fg-primary); font-size: 1rem; }

    /* Divider */
    .receipt-divider {
      border: none;
      border-top: 1.5px dashed var(--fg-border);
      margin: 0.5rem 0;
    }

    /* Buttons */
    .btn-dashboard {
      width: 100%;
      padding: 0.85rem;
      border-radius: 12px;
      border: none;
      background: var(--fg-primary);
      color: #fff;
      font-size: 0.95rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      text-decoration: none;
      box-shadow: 0 4px 16px rgba(230,168,0,0.3);
      margin-bottom: 0.75rem;
    }
    .btn-dashboard:hover {
      background: var(--fg-primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 6px 24px rgba(230,168,0,0.45);
      color: #fff;
    }

    .btn-another {
      width: 100%;
      padding: 0.7rem;
      border-radius: 10px;
      border: 1.5px solid var(--fg-border);
      background: transparent;
      color: var(--fg-muted);
      font-size: 0.88rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.4rem;
      text-decoration: none;
    }
    .btn-another:hover { border-color: var(--fg-text); color: var(--fg-text); }

    /* Confetti dots (CSS only) */
    .confetti-wrap {
      position: fixed;
      inset: 0;
      pointer-events: none;
      overflow: hidden;
      z-index: 0;
    }
    .dot {
      position: absolute;
      width: 8px; height: 8px;
      border-radius: 50%;
      animation: fall linear infinite;
      opacity: 0;
    }
    @keyframes fall {
      0%   { transform: translateY(-20px) rotate(0deg);   opacity: 1; }
      100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
    }
  </style>
</head>
<body>

  <!-- Confetti -->
  <div class="confetti-wrap" id="confetti"></div>

  <div class="success-wrap" style="position:relative;z-index:1;">

    <!-- Logo -->
    <div class="success-logo">
      <img src="assets/images/logo.png" alt="Fix&Go"
           onerror="this.outerHTML='<div style=\'font-size:1.4rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</div>'">
    </div>

    <div class="success-card">

      <!-- Check icon -->
      <div class="check-circle">
        <i class="bi bi-check-lg"></i>
      </div>

      <h1 class="success-title">Payment Successful!</h1>
      <p class="success-subtitle">
        Your test payment has been processed.<br>
        Products are now confirmed in your inventory.
      </p>

      <!-- Receipt -->
      <div class="receipt">
        <div class="receipt-row">
          <span class="receipt-label">Reference No.</span>
          <span class="receipt-value" id="refNo">—</span>
        </div>
        <div class="receipt-row">
          <span class="receipt-label">Status</span>
          <span class="receipt-value green"><i class="bi bi-check-circle-fill"></i> Paid</span>
        </div>
        <div class="receipt-row">
          <span class="receipt-label">Payment Method</span>
          <span class="receipt-value">Test Card (Visa)</span>
        </div>
        <hr class="receipt-divider">
        <div class="receipt-row">
          <span class="receipt-label">iPhone 14 Tempered Glass × 2</span>
          <span class="receipt-value">₱598.00</span>
        </div>
        <div class="receipt-row">
          <span class="receipt-label">Samsung S8 Battery × 1</span>
          <span class="receipt-value">₱450.00</span>
        </div>
        <hr class="receipt-divider">
        <div class="receipt-row">
          <span class="receipt-label" style="font-weight:700;">Total Paid</span>
          <span class="receipt-value gold">₱1,048.00</span>
        </div>
      </div>

      <!-- Actions -->
      <a href="dashboard.php" class="btn-dashboard">
        <i class="bi bi-house-fill"></i> Go to Dashboard
      </a>
      <a href="payment-test.php" class="btn-another">
        <i class="bi bi-arrow-repeat"></i> Make Another Test Payment
      </a>

    </div>
  </div>

  <script src="assets/js/theme.js"></script>
  <script src="assets/js/auth-utils.js"></script>
  <script>
    // Auth guard
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'owner') {
      window.location.href = 'login.html';
    }

    // Read reference from URL
    const params = new URLSearchParams(window.location.search);
    const ref    = params.get('ref');
    if (ref) {
      document.getElementById('refNo').textContent = ref;
    }

    // Confetti
    const colors  = ['#e6a800','#28A745','#3b82f6','#f0c040','#10b981','#fff'];
    const confWrap = document.getElementById('confetti');
    for (let i = 0; i < 40; i++) {
      const dot = document.createElement('div');
      dot.className = 'dot';
      dot.style.left       = Math.random() * 100 + 'vw';
      dot.style.background = colors[Math.floor(Math.random() * colors.length)];
      dot.style.width      = (Math.random() * 6 + 5) + 'px';
      dot.style.height     = dot.style.width;
      dot.style.animationDuration  = (Math.random() * 3 + 2) + 's';
      dot.style.animationDelay     = (Math.random() * 2) + 's';
      dot.style.borderRadius       = Math.random() > 0.5 ? '50%' : '2px';
      confWrap.appendChild(dot);
    }
  </script>

<script src="fixandgo/assets/js/pwa.js" defer></script>
</body>
</html>

