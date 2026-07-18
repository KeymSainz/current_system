<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <!-- PWA -->
  <meta name="theme-color" content="#e6a800">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Fix&amp;Go">
  <link rel="manifest" href="/manifest.json">
  <link rel="apple-touch-icon" href="/assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="/assets/css/mobile.css">
  <title>Fix&amp;Go — Repair Bookings</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="/assets/css/auth.css?v=8.1"/>
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5.1"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
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
    .badge-in_progress{background:rgba(139,92,246,0.12);color:#8b5cf6;}
    .badge-completed{background:rgba(40,167,69,0.12);color:#28A745;}
    .badge-cancelled{background:rgba(220,53,69,0.12);color:#dc3545;}
    .filter-bar{display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;}
    .filter-input{padding:0.4rem 0.8rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.82rem;outline:none;transition:border-color 0.2s;}
    .filter-input:focus{border-color:#8b5cf6;}
    .stats-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1rem;margin-bottom:1.75rem;}
    .stat-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.1rem 1rem;text-align:center;}
    .stat-value{font-size:1.8rem;font-weight:800;line-height:1;}
    .stat-label{font-size:0.72rem;color:var(--fg-muted);font-weight:600;margin-top:0.2rem;}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    /* Payment method cards */
    .pm-card{display:flex;flex-direction:column;align-items:center;gap:0.35rem;padding:0.7rem 0.5rem;border:2px solid var(--fg-border);border-radius:10px;cursor:pointer;transition:all 0.2s;background:var(--fg-bg);text-align:center;user-select:none;}
    .pm-card:hover{border-color:#28A745;background:rgba(40,167,69,0.05);}
    .pm-card.selected{border-color:#28A745!important;background:rgba(40,167,69,0.08)!important;}
    .pm-card span.pm-icon{font-size:1.5rem;}
    .pm-card span.pm-name{font-size:0.72rem;font-weight:700;color:var(--fg-text);}
    @keyframes spin{to{transform:rotate(360deg);}}
    @media(max-width:992px){.tc-main{padding:1.5rem;}.mini-table th:nth-child(3),.mini-table td:nth-child(3),.mini-table th:nth-child(6),.mini-table td:nth-child(6){display:none;}}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .tc-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .tc-sidebar.open{transform:translateX(0);}
      .tc-main{padding:1rem;}
      .stats-row{grid-template-columns:repeat(3,1fr);}
      .filter-bar{flex-direction:column;align-items:stretch;}
      .filter-input{width:100%!important;min-width:unset!important;}
    }
    @media(max-width:575px){
      html,body{overflow-x:hidden;}
      .stats-row{grid-template-columns:repeat(2,1fr);}
      .stat-value{font-size:1.4rem;}
      .mini-table th:nth-child(2),.mini-table td:nth-child(2),.mini-table th:nth-child(4),.mini-table td:nth-child(4),.mini-table th:nth-child(7),.mini-table td:nth-child(7){display:none;}
      .mini-table th,.mini-table td{padding:0.45rem 0.5rem!important;font-size:0.75rem!important;}
      #navUserName{display:none!important;}
      /* Fix Complete Repair modal on small screens */
      #completeProofModal { padding:0.25rem !important; align-items:flex-end !important; }
      #completeProofModal > div { border-radius:18px 18px 0 0 !important; max-height:94vh !important; margin:0 !important; }
      #cpmBody { padding:0.9rem !important; }
      #cpmBody input, #cpmBody select, #cpmBody textarea { font-size:0.82rem !important; }
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
        <li><a href="repairs.php" class="active"><i class="bi bi-tools"></i> Repair Bookings</a></li>
        <li><a href="inventory.php"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
      </ul>
      <div class="sidebar-label">Account</div>
      <ul class="sidebar-nav"><li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li></ul>
    </aside>
    <main class="tc-main">
      <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;"><i class="bi bi-tools" style="color:#8b5cf6;margin-right:0.5rem;"></i>Repair Bookings</h2>
        <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">Manage customer repair requests and update their status.</p>
      </div>
      <!-- Stats -->
      <div class="stats-row">
        <div class="stat-card"><div class="stat-value" style="color:#8b5cf6;" id="rTotal">—</div><div class="stat-label">Total Repairs</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#c98f00;" id="rPending">—</div><div class="stat-label">Pending</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#3b82f6;" id="rConfirmed">—</div><div class="stat-label">Confirmed</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#8b5cf6;" id="rInProgress">—</div><div class="stat-label">In Progress</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#28A745;" id="rCompleted">—</div><div class="stat-label">Completed</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#10b981;" id="rRevenue">—</div><div class="stat-label">Revenue</div></div>
      </div>
      <!-- Tabs -->
      <div style="display:flex;gap:0.25rem;margin-bottom:1.25rem;background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:12px;padding:0.3rem;width:fit-content;">
        <button id="tabBookings" onclick="switchTab('bookings')" style="padding:0.45rem 1.1rem;border-radius:9px;border:none;font-size:0.83rem;font-weight:700;cursor:pointer;transition:all 0.2s;background:rgba(139,92,246,0.12);color:#8b5cf6;">
          <i class="bi bi-tools"></i> Bookings
        </button>
        <button id="tabPayments" onclick="switchTab('payments')" style="padding:0.45rem 1.1rem;border-radius:9px;border:none;font-size:0.83rem;font-weight:700;cursor:pointer;transition:all 0.2s;background:transparent;color:var(--fg-muted);">
          <i class="bi bi-credit-card-2-front"></i> Payment History
        </button>
      </div>
      <!-- Bookings Panel -->
      <div id="panelBookings">
        <div class="section-card">
          <div class="section-head">
            <h6><i class="bi bi-tools" style="color:#8b5cf6;margin-right:0.4rem;"></i>All Repair Bookings</h6>
            <div class="filter-bar">
              <input type="text" class="filter-input" id="searchInput" placeholder="Search customer or device…" oninput="applyFilters()" style="min-width:200px;">
              <select class="filter-input" id="statusFilter" onchange="applyFilters()">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
          <div style="overflow-x:auto;">
            <table class="mini-table">
              <thead><tr><th>#</th><th>Customer</th><th>Device</th><th>Issue</th><th>Status</th><th>Scheduled</th><th>Amount</th><th>Actions</th></tr></thead>
              <tbody id="repairsBody">
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                  <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#8b5cf6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>Loading…
                </td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- Payment History Panel -->
      <div id="panelPayments" style="display:none;">
        <div class="stats-row" id="payStatRow" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));"></div>
        <div class="section-card">
          <div class="section-head">
            <h6><i class="bi bi-credit-card-2-front" style="color:#28A745;margin-right:0.4rem;"></i>Completed Repair Payments</h6>
            <div class="filter-bar">
              <input type="text" class="filter-input" id="paySearchInput" placeholder="Search customer or device…" oninput="applyPayFilters()" style="min-width:200px;">
              <select class="filter-input" id="payMethodFilter" onchange="applyPayFilters()">
                <option value="all">All Methods</option>
                <option value="cash">💵 Cash</option>
                <option value="gcash">📱 GCash</option>
                <option value="maya">💳 Maya</option>
                <option value="bank_transfer">🏦 Bank Transfer</option>
                <option value="other">💰 Other</option>
              </select>
            </div>
          </div>
          <div style="overflow-x:auto;">
            <table class="mini-table">
              <thead><tr><th>#</th><th>Customer</th><th>Device</th><th>Method</th><th>Amount</th><th>Pay Status</th><th>Receipt</th><th>Date Completed</th></tr></thead>
              <tbody id="payHistBody">
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);">
                  <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#28A745;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>Loading…
                </td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
  <!-- ══ Booking Detail Modal ════════════════════════════════════ -->
  <div id="bookingDetailModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;overflow-y:auto;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;width:100%;max-width:600px;box-shadow:0 24px 64px rgba(0,0,0,0.4);">
      <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;">
        <h5 style="margin:0;font-weight:800;font-size:1rem;color:var(--fg-text);">📋 Booking Details</h5>
        <button onclick="document.getElementById('bookingDetailModal').style.display='none'" style="width:30px;height:30px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:0.9rem;"><i class="bi bi-x-lg"></i></button>
      </div>
      <div id="bdmContent" style="padding:1.5rem;"></div>
    </div>
  </div>

  <!-- ══ Complete Repair Modal ══════════════════════════════════ -->
  <div id="completeProofModal" style="display:none;position:fixed;inset:0;z-index:9200;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:0.75rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:540px;max-height:96vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);">
      <!-- Header -->
      <div style="background:linear-gradient(135deg,#28A745,#1a8a35);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div>
          <div id="cpmTitle" style="color:#fff;font-weight:800;font-size:1rem;">✅ Complete Repair</div>
          <div style="color:rgba(255,255,255,0.8);font-size:0.75rem;margin-top:0.15rem;">
            Repair <strong id="cpmRepairId">#—</strong> · <span id="cpmDevice"></span> · <span id="cpmCustomer"></span>
          </div>
        </div>
        <button onclick="closeCPM()" style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;" onmouseenter="this.style.background='rgba(255,255,255,0.3)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">✕</button>
      </div>
      <!-- Scrollable Body -->
      <div id="cpmBody" style="padding:1.25rem;overflow-y:auto;flex:1;">

        <!-- ── COST BREAKDOWN SECTION ── -->
        <div style="background:rgba(230,168,0,0.07);border:1.5px solid rgba(230,168,0,0.25);border-radius:12px;padding:1rem 1.1rem;margin-bottom:1.1rem;">
          <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-primary);margin-bottom:0.85rem;">💰 Cost Breakdown</div>

          <!-- Labor + Parts row -->
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:0.75rem;margin-bottom:0.75rem;">
            <div>
              <label style="display:block;font-size:0.72rem;font-weight:700;color:var(--fg-muted);margin-bottom:0.3rem;">Labor / Service Fee (₱)</label>
              <input type="number" id="cpmLaborFee" min="0" step="0.01" placeholder="e.g. 300"
                style="width:100%;padding:0.55rem 0.75rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;box-sizing:border-box;"
                onfocus="this.style.borderColor='#28A745'" onblur="this.style.borderColor='var(--fg-border)'"
                oninput="recalcTotal()">
            </div>
            <div>
              <label style="display:block;font-size:0.72rem;font-weight:700;color:var(--fg-muted);margin-bottom:0.3rem;">Parts / Replacement Cost (₱)</label>
              <input type="number" id="cpmPartsFee" min="0" step="0.01" placeholder="Auto-calculated from parts"
                style="width:100%;padding:0.55rem 0.75rem;border:1.5px solid var(--fg-border);border-radius:8px;background:rgba(139,92,246,0.05);color:#8b5cf6;font-size:0.88rem;outline:none;box-sizing:border-box;font-weight:700;"
                readonly title="Auto-calculated from inventory parts added below">
            </div>
          </div>

          <!-- Total display -->
          <div style="display:flex;align-items:center;justify-content:space-between;background:rgba(40,167,69,0.1);border:1.5px solid rgba(40,167,69,0.35);border-radius:10px;padding:0.75rem 1rem;margin-bottom:0.85rem;">
            <div>
              <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;color:var(--fg-muted);margin-bottom:0.15rem;">Total Amount to Charge</div>
              <span id="cpmTotalDisplay" style="font-size:1.5rem;font-weight:900;color:#28A745;line-height:1;">₱0.00</span>
            </div>
            <i class="bi bi-calculator-fill" style="font-size:1.8rem;color:rgba(40,167,69,0.25);"></i>
            <input type="hidden" id="cpmFee" value="0">
          </div>

          <!-- Product price photo/video upload -->
          <div>
            <div style="font-size:0.72rem;font-weight:700;color:var(--fg-muted);margin-bottom:0.45rem;">📸 Product/Parts Price Photo <span style="font-weight:400;">(optional — helps customer trust the cost)</span></div>
            <label for="cpmPricePhoto" style="display:flex;align-items:center;gap:0.65rem;padding:0.65rem 0.85rem;border:2px dashed rgba(230,168,0,0.35);border-radius:9px;cursor:pointer;background:rgba(230,168,0,0.02);"
              onmouseenter="this.style.borderColor='var(--fg-primary)'" onmouseleave="this.style.borderColor='rgba(230,168,0,0.35)'">
              <i class="bi bi-camera-fill" style="color:var(--fg-primary);font-size:1.2rem;flex-shrink:0;"></i>
              <div>
                <div style="font-size:0.82rem;font-weight:700;color:var(--fg-text);">Attach price tag / receipt photo</div>
                <div style="font-size:0.7rem;color:var(--fg-muted);">JPG, PNG, WebP or Video · Max 20 MB</div>
              </div>
            </label>
            <input type="file" id="cpmPricePhoto" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm" style="display:none;" onchange="handlePricePhoto(this)">
            <div id="cpmPricePhotoPreview" style="display:none;margin-top:0.55rem;padding:0.45rem 0.8rem;background:rgba(230,168,0,0.07);border:1px solid rgba(230,168,0,0.2);border-radius:8px;align-items:center;gap:0.6rem;">
              <div id="cpmPricePhotoThumb" style="flex:1;display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;color:var(--fg-text);overflow:hidden;"></div>
              <button type="button" onclick="clearPricePhoto()" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:0.9rem;flex-shrink:0;">✕</button>
            </div>
          </div>
        </div>

        <!-- ── PARTS REPLACED SECTION ── -->
        <div style="background:rgba(139,92,246,0.05);border:1.5px solid rgba(139,92,246,0.2);border-radius:12px;padding:1rem 1.1rem;margin-bottom:1.1rem;">
          <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#8b5cf6;margin-bottom:0.75rem;">🔩 Parts / Products Replaced</div>

          <!-- Dynamic parts list -->
          <div id="partsListWrap" style="margin-bottom:0.65rem;display:flex;flex-direction:column;gap:0.45rem;"></div>

          <!-- Add part row — inventory picker -->
          <div style="display:flex;flex-wrap:wrap;gap:0.45rem;align-items:center;">
            <select id="cpmPartSelect"
              style="flex:1;min-width:0;padding:0.5rem 0.75rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.82rem;outline:none;box-sizing:border-box;"
              onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='var(--fg-border)'">
              <option value="">— Select from inventory —</option>
            </select>
            <input type="number" id="cpmPartQty" placeholder="Qty" min="1" value="1"
              style="width:58px;flex-shrink:0;padding:0.5rem 0.4rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;text-align:center;"
              onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='var(--fg-border)'">
            <button type="button" onclick="addPartFromInventory()"
              style="flex-shrink:0;padding:0.5rem 0.85rem;border-radius:8px;background:#8b5cf6;color:#fff;border:none;font-weight:700;font-size:0.82rem;cursor:pointer;white-space:nowrap;"
              onmouseenter="this.style.background='#7c3aed'" onmouseleave="this.style.background='#8b5cf6'">
              + Add
            </button>
          </div>
          <div style="font-size:0.7rem;color:var(--fg-muted);margin-top:0.4rem;">Select parts from your inventory. Price is fixed from your inventory price list.</div>
          <input type="hidden" id="cpmPartsReplaced" value="[]">
        </div>

        <!-- ── PAYMENT SECTION ── -->
        <div style="background:rgba(59,130,246,0.05);border:1.5px solid rgba(59,130,246,0.2);border-radius:12px;padding:1rem 1.1rem;margin-bottom:1.1rem;">
          <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#3b82f6;margin-bottom:0.85rem;">💳 Payment Collection</div>

          <!-- Payment Status -->
          <div style="margin-bottom:0.75rem;">
            <label style="display:block;font-size:0.72rem;font-weight:700;color:var(--fg-muted);margin-bottom:0.3rem;">Collection Status</label>
            <select id="cpmPayStatus" style="width:100%;padding:0.55rem 0.75rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;box-sizing:border-box;">
              <option value="pending_collection">⏳ Customer will pay via Pay Now</option>
              <option value="paid">✅ Already collected (cash on-site)</option>
            </select>
          </div>

          <!-- Mode of Payment (only when already collected) -->
          <div id="cpmMethodWrap" style="display:none;">
            <label style="display:block;font-size:0.72rem;font-weight:700;color:var(--fg-muted);margin-bottom:0.5rem;">Mode Collected</label>
            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0.45rem;">
              <div class="pm-card" id="pmCash" onclick="pickPM('cash')"><span class="pm-icon">💵</span><span class="pm-name">Cash</span></div>
              <div class="pm-card" id="pmBank" onclick="pickPM('bank_transfer')"><span class="pm-icon">🏦</span><span class="pm-name">Bank</span></div>
              <div class="pm-card" id="pmGcash" onclick="pickPM('gcash')"><span class="pm-icon">📱</span><span class="pm-name">GCash</span></div>
              <div class="pm-card" id="pmMaya" onclick="pickPM('maya')"><span class="pm-icon">💳</span><span class="pm-name">Maya</span></div>
              <div class="pm-card" id="pmOther" onclick="pickPM('other')"><span class="pm-icon">💰</span><span class="pm-name">Other</span></div>
            </div>
            <input type="hidden" id="cpmMethod" value="">
          </div>

          <!-- Reference / Account (shown for non-cash) -->
          <div id="cpmNoteWrap" style="display:none;margin-top:0.65rem;">
            <label style="display:block;font-size:0.72rem;font-weight:700;color:var(--fg-muted);margin-bottom:0.3rem;">Account / Reference <span style="font-weight:400;">(optional)</span></label>
            <input type="text" id="cpmNote" placeholder="e.g. 0917-123-4567 / Ref: 123456"
              style="width:100%;padding:0.55rem 0.75rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;box-sizing:border-box;"
              onfocus="this.style.borderColor='#28A745'" onblur="this.style.borderColor='var(--fg-border)'">
          </div>
        </div>

        <!-- ── RECEIPT UPLOAD ── -->
        <div style="margin-bottom:1rem;">
          <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.5rem;">🧾 Upload Receipt <span style="font-weight:400;text-transform:none;">(optional)</span></div>
          <label for="cpmReceiptFile" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border:2px dashed rgba(230,168,0,0.4);border-radius:10px;cursor:pointer;background:rgba(230,168,0,0.03);"
            onmouseenter="this.style.borderColor='var(--fg-primary)'" onmouseleave="this.style.borderColor='rgba(230,168,0,0.4)'">
            <i class="bi bi-file-earmark-image" style="color:var(--fg-primary);font-size:1.4rem;flex-shrink:0;"></i>
            <div><div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);">Click to attach receipt</div>
            <div style="font-size:0.72rem;color:var(--fg-muted);">JPG, PNG, WebP or PDF · Max 10 MB</div></div>
          </label>
          <input type="file" id="cpmReceiptFile" accept="image/jpeg,image/png,image/webp,image/gif,application/pdf" style="display:none;" onchange="handleReceiptFile(this)">
          <div id="cpmReceiptPreview" style="display:none;margin-top:0.6rem;padding:0.5rem 0.85rem;background:rgba(230,168,0,0.07);border:1px solid rgba(230,168,0,0.2);border-radius:8px;align-items:center;gap:0.75rem;">
            <div id="cpmReceiptThumb" style="flex:1;display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;color:var(--fg-text);overflow:hidden;"></div>
            <button type="button" onclick="clearReceiptFile()" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:0.9rem;flex-shrink:0;">✕</button>
          </div>
        </div>

        <!-- ── PROOF PHOTO/VIDEO ── -->
        <div style="margin-bottom:1rem;">
          <div id="cpmProofLabel" style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#28A745;margin-bottom:0.5rem;">📎 Repair Proof Photo/Video <span style="font-weight:400;text-transform:none;">(recommended)</span></div>
          <label for="cpmProofFile" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border:2px dashed rgba(40,167,69,0.35);border-radius:10px;cursor:pointer;background:rgba(40,167,69,0.03);"
            onmouseenter="this.style.borderColor='#28A745'" onmouseleave="this.style.borderColor='rgba(40,167,69,0.35)'">
            <i class="bi bi-camera-video-fill" style="color:#28A745;font-size:1.4rem;flex-shrink:0;"></i>
            <div><div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);">Click to attach proof</div>
            <div style="font-size:0.72rem;color:var(--fg-muted);">Photo or Video · Max 50 MB</div></div>
          </label>
          <input type="file" id="cpmProofFile" accept="image/*,video/mp4,video/webm,video/quicktime" style="display:none;" onchange="handleProofFile(this)">
          <div id="cpmProofPreview" style="display:none;margin-top:0.6rem;border-radius:10px;overflow:hidden;border:1px solid var(--fg-border);">
            <div style="padding:0.6rem 0.85rem;background:var(--fg-bg);display:flex;align-items:center;justify-content:space-between;">
              <div id="cpmProofThumb"></div>
              <button type="button" onclick="clearProofFile()" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:1rem;flex-shrink:0;">✕</button>
            </div>
          </div>
        </div>

        <!-- ── MESSAGE ── -->
        <div style="margin-bottom:0.75rem;">
          <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.4rem;">Message to Customer</div>
          <textarea id="cpmMessage" rows="3"
            style="width:100%;padding:0.75rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;resize:vertical;outline:none;font-family:inherit;line-height:1.55;box-sizing:border-box;"
            onfocus="this.style.borderColor='#28A745'" onblur="this.style.borderColor='var(--fg-border)'"></textarea>
        </div>

        <div id="cpmAlert" style="display:none;margin-bottom:0.75rem;padding:0.65rem 0.9rem;border-radius:8px;font-size:0.83rem;font-weight:600;align-items:center;gap:0.5rem;"></div>

        <button id="cpmBtn" onclick="submitComplete()"
          style="width:100%;padding:0.9rem;border-radius:12px;background:linear-gradient(135deg,#28A745,#1a8a35);color:#fff;border:none;font-weight:800;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;"
          onmouseenter="this.style.opacity='0.88'" onmouseleave="this.style.opacity='1'">
          <i class="bi bi-check-circle-fill"></i> Mark Complete &amp; Send
        </button>
      </div>
    </div>
  </div>

  <!-- ══ Status Update Modal ════════════════════════════════════ -->
  <div id="statusUpdateModal" style="display:none;position:fixed;inset:0;z-index:9100;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:540px;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);">
      <div style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div>
          <div style="color:#fff;font-weight:800;font-size:1rem;">📢 Send Repair Update</div>
          <div style="color:rgba(255,255,255,0.75);font-size:0.75rem;margin-top:0.15rem;">
            Repair <strong id="suRepairId">#—</strong> · <span id="suDevice"></span> · <span id="suCustomer"></span>
          </div>
        </div>
        <button onclick="closeStatusUpdate()" style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;" onmouseenter="this.style.background='rgba(255,255,255,0.3)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">✕</button>
      </div>
      <div style="padding:1.25rem;overflow-y:auto;flex:1;">
        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#8b5cf6;margin-bottom:0.6rem;">Quick Updates</div>
        <div id="suQuickPicks" style="display:flex;flex-wrap:wrap;gap:0.4rem;margin-bottom:1.1rem;"></div>
        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.4rem;">📎 Attach Photo/Video <span style="font-weight:400;text-transform:none;">(optional)</span></div>
        <label for="suFileInput" style="display:flex;align-items:center;gap:0.6rem;padding:0.6rem 0.85rem;border:1.5px dashed var(--fg-border);border-radius:10px;cursor:pointer;background:var(--fg-bg);margin-bottom:0.75rem;" onmouseenter="this.style.borderColor='#8b5cf6'" onmouseleave="this.style.borderColor='var(--fg-border)'">
          <i class="bi bi-paperclip" style="color:#8b5cf6;font-size:1rem;"></i>
          <span style="font-size:0.82rem;color:var(--fg-muted);">Click to attach a photo or video</span>
        </label>
        <input type="file" id="suFileInput" accept="image/*,video/mp4,video/webm,video/quicktime" style="display:none;" onchange="handleSUFile(this)">
        <div id="suFilePreview" style="display:none;padding:0.4rem 0.75rem;background:rgba(139,92,246,0.07);border:1px solid rgba(139,92,246,0.2);border-radius:8px;margin-bottom:0.75rem;align-items:center;gap:0.6rem;font-size:0.82rem;color:var(--fg-text);">
          <div id="suFileThumb" style="flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;"></div>
          <button type="button" onclick="clearSUFile()" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:0.9rem;flex-shrink:0;">✕</button>
        </div>
        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.4rem;">Message to Customer</div>
        <textarea id="suMessage" rows="5" placeholder="Write your repair update message here…"
          style="width:100%;padding:0.75rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;resize:vertical;outline:none;font-family:inherit;line-height:1.55;box-sizing:border-box;"
          onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='var(--fg-border)'"></textarea>
        <div id="suAlert" style="display:none;margin-top:0.75rem;padding:0.6rem 0.9rem;border-radius:8px;font-size:0.82rem;font-weight:600;align-items:center;gap:0.5rem;"></div>
        <button id="suSendBtn" onclick="sendStatusUpdate()"
          style="margin-top:1rem;width:100%;padding:0.8rem;border-radius:12px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff;border:none;font-weight:800;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;"
          onmouseenter="this.style.opacity='0.88'" onmouseleave="this.style.opacity='1'">
          <i class="bi bi-send-fill"></i> Send Update
        </button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="/assets/js/session-timeout.js"></script>
  <script>
  'use strict';
  const API = '../../../backend/technician_dashboard.php';
  const BASE_URL = 'http://' + location.hostname + '/';
  let allRepairs = [];
  let allPayments = [];

  function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
  function peso(n){ return '₱' + parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:0}); }
  function fmtDate(d){ return d ? new Date(d).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'}) : '—'; }
  function fmtDT(d){ return d ? new Date(d).toLocaleString('en-PH',{month:'short',day:'numeric',year:'numeric',hour:'numeric',minute:'2-digit'}) : '—'; }
  function showAlert(elId, msg, ok){ const el=document.getElementById(elId); el.style.display='flex'; el.style.background=ok?'rgba(40,167,69,0.1)':'rgba(220,53,69,0.1)'; el.style.color=ok?'#28A745':'#dc3545'; el.innerHTML=(ok?'<i class="bi bi-check-circle-fill"></i> ':'<i class="bi bi-exclamation-triangle-fill"></i> ')+esc(msg); }

  /* ── Tab switching ───────────────────────────────────────── */
  function switchTab(tab){
    const isBk = tab==='bookings';
    document.getElementById('panelBookings').style.display = isBk ? '' : 'none';
    document.getElementById('panelPayments').style.display = isBk ? 'none' : '';
    const tbB=document.getElementById('tabBookings'), tbP=document.getElementById('tabPayments');
    tbB.style.background = isBk ? 'rgba(139,92,246,0.12)' : 'transparent';
    tbB.style.color      = isBk ? '#8b5cf6' : 'var(--fg-muted)';
    tbP.style.background = !isBk ? 'rgba(40,167,69,0.12)' : 'transparent';
    tbP.style.color      = !isBk ? '#28A745' : 'var(--fg-muted)';
    if(!isBk && allPayments.length===0) loadPaymentHistory();
  }

  /* ── Init ────────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function(){
    const user = FGAuth.UserStore.get();
    if(!user || user.role!=='phone_technician'){ window.location.href='/login.html'; return; }
    document.getElementById('navUserName').textContent = ((user.firstName||'')+' '+(user.lastName||'')).trim()||user.email;
    const sb=document.getElementById('tcSidebar'), ov=document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click',()=>{ sb.classList.toggle('open'); ov.classList.toggle('open'); });
    ov.addEventListener('click',()=>{ sb.classList.remove('open'); ov.classList.remove('open'); });
    loadStats(); loadRepairs();
    setupPayStatusToggle();
  });

  /* ── Stats ───────────────────────────────────────────────── */
  function loadStats(){
    fetch(API+'?action=stats',{credentials:'include'}).then(r=>r.json()).then(d=>{
      if(!d.success) return;
      const s=d.stats;
      document.getElementById('rTotal').textContent     = s.total_repairs||0;
      document.getElementById('rPending').textContent   = s.pending_repairs||0;
      document.getElementById('rCompleted').textContent = s.completed_repairs||0;
      document.getElementById('rRevenue').textContent   = peso(s.total_revenue);
    }).catch(()=>{});
    fetch(API+'?action=repairs',{credentials:'include'}).then(r=>r.json()).then(d=>{
      if(!d.success) return;
      const rr=d.repairs||[];
      document.getElementById('rConfirmed').textContent  = rr.filter(r=>r.status==='confirmed').length;
      document.getElementById('rInProgress').textContent = rr.filter(r=>r.status==='in_progress').length;
    }).catch(()=>{});
  }

  /* ── Load Repairs ────────────────────────────────────────── */
  function loadRepairs(){
    fetch(API+'?action=repairs',{credentials:'include'}).then(r=>r.json()).then(d=>{
      if(!d.success){ document.getElementById('repairsBody').innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);">Could not load repairs.</td></tr>'; return; }
      allRepairs = d.repairs||[];
      window._allRepairs = allRepairs;
      renderRepairs(allRepairs);
    }).catch(()=>{ document.getElementById('repairsBody').innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);">Network error.</td></tr>'; });
  }

  const statusMap = {
    pending:     {cls:'badge-pending',    label:'Pending'},
    confirmed:   {cls:'badge-confirmed',  label:'Confirmed'},
    in_progress: {cls:'badge-in_progress',label:'In Progress'},
    completed:   {cls:'badge-completed',  label:'Completed'},
    cancelled:   {cls:'badge-cancelled',  label:'Cancelled'},
  };

  function applyFilters(){
    const q=document.getElementById('searchInput').value.toLowerCase();
    const st=document.getElementById('statusFilter').value;
    let items=allRepairs;
    if(st!=='all') items=items.filter(r=>r.status===st);
    if(q) items=items.filter(r=>
      (r.first_name+' '+r.last_name).toLowerCase().includes(q)||
      (r.device_name||r.device_model||'').toLowerCase().includes(q)||
      (r.fault_description||r.issue_description||'').toLowerCase().includes(q)
    );
    renderRepairs(items);
  }

  function renderRepairs(repairs){
    const tbody=document.getElementById('repairsBody');
    if(!repairs.length){ tbody.innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);">No repairs found.</td></tr>'; return; }
    tbody.innerHTML=repairs.map(r=>{
      const cust=esc(((r.first_name||'')+' '+(r.last_name||'')).trim()||'N/A');
      const s=statusMap[r.status]||{cls:'badge-pending',label:r.status};
      const device=esc(r.device_name||r.device_model||'—');
      const issue =esc(r.fault_description||r.issue_description||'—');
      return `<tr>
        <td style="font-weight:700;color:#8b5cf6;">#${r.id}</td>
        <td>
          <div style="font-weight:600;">${cust}</div>
          <div style="font-size:0.75rem;color:var(--fg-muted);">${esc(r.customer_email||'')}</div>
          ${r.contact_number?`<div style="font-size:0.72rem;color:var(--fg-muted);">📞 ${esc(r.contact_number)}</div>`:''}
        </td>
        <td style="color:var(--fg-muted);">${device}</td>
        <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${issue}">${issue}</td>
        <td>
          <span class="badge-status ${s.cls}">${s.label}</span>
          ${r.status==='completed'?(r.customer_payment_status==='paid'
            ?'<br><span style="font-size:0.65rem;font-weight:700;color:#28A745;">💰 Customer Paid</span>'
            :'<br><span style="font-size:0.65rem;font-weight:700;color:#c98f00;">⏳ Awaiting Payment</span>'):''}
        </td>
        <td style="color:var(--fg-muted);font-size:0.8rem;">${fmtDate(r.scheduled_at||r.created_at)}</td>
        <td style="font-weight:700;">${r.total_amount>0?peso(r.total_amount):r.repair_fee>0?peso(r.repair_fee):'—'}</td>
        <td>
          <button onclick="viewDetails(${r.id})" style="padding:0.2rem 0.55rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid #8b5cf6;color:#8b5cf6;background:transparent;margin-right:0.2rem;" onmouseenter="this.style.background='#8b5cf6';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#8b5cf6'">📋</button>
          ${getActions(r.id,r.status,r.customer_id)}
        </td>
      </tr>`;
    }).join('');
    window._allRepairs = repairs;
  }

  function getActions(id, status, customerId){
    const btnStyle=(color)=>`padding:0.2rem 0.55rem;border-radius:6px;font-size:0.7rem;font-weight:700;cursor:pointer;border:1.5px solid ${color};color:${color};background:transparent;margin-right:0.2rem;`;
    const btn=(label,ns,color)=>`<button onclick="quickUpdate(${id},'${ns}')" style="${btnStyle(color)}" onmouseenter="this.style.background='${color}';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='${color}'">${label}</button>`;
    const msgBtn=`<a href="messages.php?other_id=${customerId}" style="${btnStyle('#8b5cf6')}text-decoration:none;display:inline-block;margin-right:0.2rem;" onmouseenter="this.style.background='#8b5cf6';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#8b5cf6'">💬</a>`;
    const updBtn=`<button onclick="openStatusUpdate(${id})" style="${btnStyle('var(--fg-primary)')}" onmouseenter="this.style.background='var(--fg-primary)';this.style.color='#000'" onmouseleave="this.style.background='transparent';this.style.color='var(--fg-primary)'">📢</button>`;
    const completeBtn=`<button onclick="openCPM(${id})" style="${btnStyle('#28A745')}" onmouseenter="this.style.background='#28A745';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#28A745'">✅ Complete</button>`;
    let a = msgBtn;
    if(status==='pending')      a += btn('✔ Confirm','confirmed','#3b82f6') + cancelBtn(id);
    else if(status==='confirmed') a += btn('▶ Start','in_progress','#8b5cf6') + cancelBtn(id);
    else if(status==='in_progress') a += updBtn + completeBtn;
    return a;
  }

  function cancelBtn(id){
    return `<button onclick="openCancelReasonModal(${id})" style="${btnStyle('#dc3545')}" onmouseenter="this.style.background='#dc3545';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#dc3545'">✕ Cancel</button>`;
  }

  // ── Cancel Reason Modal ──────────────────────────────────────
  let _cancelRepairId = null;
  function openCancelReasonModal(id) {
    _cancelRepairId = id;
    document.getElementById('cancelReasonInput').value = '';
    document.getElementById('cancelReasonError').style.display = 'none';
    document.getElementById('cancelReasonModal').style.display = 'flex';
  }
  function closeCancelReasonModal() {
    document.getElementById('cancelReasonModal').style.display = 'none';
    _cancelRepairId = null;
  }
  function submitCancelReason() {
    const reason = document.getElementById('cancelReasonInput').value.trim();
    if (!reason) {
      document.getElementById('cancelReasonError').style.display = 'block';
      document.getElementById('cancelReasonInput').focus();
      return;
    }
    closeCancelReasonModal();
    quickUpdate(_cancelRepairId, 'cancelled', reason);
  }

  function quickUpdate(id, status, cancelReason){
    if(status === 'cancelled' && cancelReason === undefined) { openCancelReasonModal(id); return; }
    if(status !== 'cancelled' && !confirm('Update repair #'+id+' to "'+status+'"?')) return;
    const payload = {action:'update_repair',repair_id:id,status:status};
    if(status === 'cancelled' && cancelReason) payload.cancel_reason = cancelReason;
    fetch(API,{method:'POST',credentials:'include',headers:{'Content-Type':'application/json'},
      body:JSON.stringify(payload)
    }).then(r=>r.json()).then(d=>{
      if(d.success){ loadRepairs(); loadStats(); }
      else alert(d.message||'Failed.');
    }).catch(()=>alert('Network error.'));
  }

  function viewDetails(id){
    const r=(window._allRepairs||[]).find(x=>x.id==id)||allRepairs.find(x=>x.id==id);
    if(!r) return;
    const s=statusMap[r.status]||{cls:'badge-pending',label:r.status};
    document.getElementById('bdmContent').innerHTML=`
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:0.85rem;">
        <div><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Customer</div><div style="font-weight:700;">${esc(((r.first_name||'')+' '+(r.last_name||'')).trim()||'N/A')}</div></div>
        <div><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Contact</div><div style="font-weight:700;">${esc(r.contact_number||r.customer_phone||'—')}</div></div>
        <div style="grid-column:1/-1;"><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Address</div><div>${esc(r.address||r.address_line||'—')}</div></div>
        <div><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Device</div><div style="font-weight:700;">${esc(r.device_name||r.device_model||'—')}</div></div>
        <div><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Status</div><span class="badge-status ${s.cls}">${s.label}</span></div>
        <div style="grid-column:1/-1;"><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Fault Description</div><div style="line-height:1.6;">${esc(r.fault_description||r.issue_description||'—')}</div></div>
        <div style="grid-column:1/-1;"><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Phone History</div><div style="line-height:1.6;">${esc(r.phone_history||'—')}</div></div>
        <div style="grid-column:1/-1;"><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Expected Fix</div><div style="line-height:1.6;">${esc(r.expected_fix||'—')}</div></div>
        <div><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Scheduled</div><div>${fmtDate(r.scheduled_at)||'Not set'}</div></div>
        <div><div style="font-size:0.7rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;margin-bottom:0.2rem;">Booked On</div><div>${fmtDate(r.created_at)}</div></div>
      </div>
      <div style="margin-top:1.25rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
        ${getActions(r.id,r.status,r.customer_id)}
        <a href="messages.php?other_id=${r.customer_id}" style="padding:0.35rem 0.85rem;border-radius:8px;font-size:0.78rem;font-weight:700;border:1.5px solid #8b5cf6;color:#8b5cf6;background:transparent;text-decoration:none;display:inline-flex;align-items:center;gap:0.3rem;" onmouseenter="this.style.background='#8b5cf6';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#8b5cf6'">💬 Message</a>
      </div>`;
    document.getElementById('bookingDetailModal').style.display='flex';
  }

  /* ═══════════════════════════════════════════════════════════
     COMPLETE REPAIR MODAL  (openCPM / submitComplete)
  ═══════════════════════════════════════════════════════════ */
  let _cpmRepairId = null, _cpmCustomerId = null;

  function pickPM(method){
    document.getElementById('cpmMethod').value = method;
    ['pmCash','pmBank','pmGcash','pmMaya','pmOther'].forEach(function(pid){
      const el=document.getElementById(pid);
      if(el) el.classList.remove('selected');
    });
    const map={cash:'pmCash',bank_transfer:'pmBank',gcash:'pmGcash',maya:'pmMaya',other:'pmOther'};
    const target=document.getElementById(map[method]);
    if(target) target.classList.add('selected');
    const wrap=document.getElementById('cpmNoteWrap');
    if(wrap) wrap.style.display=(method==='cash')?'none':'block';
    const note=document.getElementById('cpmNote');
    if(note){
      const ph={bank_transfer:'Account name / number / bank',gcash:'GCash number e.g. 0917-123-4567',maya:'Maya number or reference',other:'Payment reference / details'};
      note.placeholder=ph[method]||'Reference';
    }
  }

  /* ── Cost recalculation ────────────────────────────────── */
  function recalcTotal(){
    const labor = parseFloat(document.getElementById('cpmLaborFee').value)||0;
    const parts = parseFloat(document.getElementById('cpmPartsFee').value)||0;
    const total = labor + parts;
    document.getElementById('cpmFee').value = total.toFixed(2);
    document.getElementById('cpmTotalDisplay').textContent = '₱' + total.toLocaleString('en-PH',{minimumFractionDigits:2});
  }

  function recalcPartsTotal(){
    // Auto-sum parts fee from inventory items in _partsList
    const partsCost = _partsList.reduce((sum, p) => sum + (p.price * p.qty), 0);
    document.getElementById('cpmPartsFee').value = partsCost.toFixed(2);
    recalcTotal();
  }

  /* ── Payment status toggle ──────────────────────────────── */
  function setupPayStatusToggle(){
    const payStatusSel = document.getElementById('cpmPayStatus');
    if(payStatusSel){
      payStatusSel.addEventListener('change', function(){
        const isAlreadyPaid = this.value === 'paid';
        const mw = document.getElementById('cpmMethodWrap');
        if(mw) mw.style.display = isAlreadyPaid ? 'block' : 'none';
        if(!isAlreadyPaid){
          document.getElementById('cpmMethod').value = '';
          ['pmCash','pmBank','pmGcash','pmMaya','pmOther'].forEach(id=>{
            const el=document.getElementById(id); if(el) el.classList.remove('selected');
          });
          const nw=document.getElementById('cpmNoteWrap'); if(nw) nw.style.display='none';
        }
      });
    }
  }

  /* ── Parts replaced list ────────────────────────────────── */
  let _partsList = [];
  let _techInventory = []; // cache of technician's inventory

  function loadTechInventoryForCPM(){
    fetch('../../../backend/technician_dashboard.php?action=inventory', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        _techInventory = d.items || [];
        const sel = document.getElementById('cpmPartSelect');
        if (!sel) return;
        sel.innerHTML = '<option value="">— Select from inventory —</option>' +
          _techInventory.filter(i => parseInt(i.quantity) > 0).map(i =>
            `<option value="${i.id}" data-price="${i.price}" data-name="${i.name.replace(/"/g,'&quot;')}">${i.name} — ₱${parseFloat(i.price).toLocaleString('en-PH',{minimumFractionDigits:2})} (Qty: ${i.quantity})</option>`
          ).join('');
      }).catch(() => {});
  }

  function renderPartsList(){
    const wrap = document.getElementById('partsListWrap');
    if(!_partsList.length){
      wrap.innerHTML = '<div style="font-size:0.78rem;color:var(--fg-muted);font-style:italic;">No parts added yet.</div>';
      document.getElementById('cpmPartsReplaced').value = '[]';
      return;
    }
    wrap.innerHTML = _partsList.map((p,i) =>
      `<div style="display:flex;align-items:center;justify-content:space-between;background:rgba(139,92,246,0.07);border:1px solid rgba(139,92,246,0.2);border-radius:8px;padding:0.4rem 0.75rem;">
        <div style="display:flex;align-items:center;gap:0.5rem;flex:1;min-width:0;">
          <span style="background:#8b5cf6;color:#fff;font-size:0.65rem;font-weight:800;padding:0.15rem 0.5rem;border-radius:20px;flex-shrink:0;">×${p.qty}</span>
          <span style="font-size:0.83rem;font-weight:600;color:var(--fg-text);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(p.name)}</span>
        </div>
        <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
          <span style="font-size:0.8rem;font-weight:700;color:#8b5cf6;">₱${(p.price * p.qty).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>
          <button type="button" onclick="removePart(${i})" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:0.85rem;line-height:1;padding:0.1rem 0.25rem;" title="Remove">✕</button>
        </div>
      </div>`
    ).join('');
    document.getElementById('cpmPartsReplaced').value = JSON.stringify(_partsList);
  }

  function addPartFromInventory(){
    const sel = document.getElementById('cpmPartSelect');
    const qtyEl = document.getElementById('cpmPartQty');
    if (!sel || !sel.value) { sel && sel.focus(); return; }
    const opt   = sel.options[sel.selectedIndex];
    const name  = opt.dataset.name;
    const price = parseFloat(opt.dataset.price) || 0;
    const qty   = Math.max(1, parseInt(qtyEl.value) || 1);
    const invItem = _techInventory.find(i => i.id == sel.value);
    const maxQty  = invItem ? parseInt(invItem.quantity) : 999;

    // Check if already added — if so, just increment qty
    const existing = _partsList.find(p => p.inventory_id == sel.value);
    if (existing) {
      const newQty = existing.qty + qty;
      if (newQty > maxQty) {
        alert('Not enough stock. Available: ' + maxQty);
        return;
      }
      existing.qty = newQty;
    } else {
      if (qty > maxQty) {
        alert('Not enough stock. Available: ' + maxQty);
        return;
      }
      _partsList.push({ inventory_id: parseInt(sel.value), name, price, qty });
    }

    sel.value = '';
    qtyEl.value = '1';
    renderPartsList();
    recalcPartsTotal();
  }

  // Keep old addPart as fallback (not used in UI now)
  function addPart(){
    addPartFromInventory();
  }

  function removePart(idx){
    _partsList.splice(idx,1);
    renderPartsList();
    recalcPartsTotal();
  }

  function resetPartsList(){
    _partsList = [];
    renderPartsList();
    // Reset parts fee (read-only — auto-calculated)
    var pf = document.getElementById('cpmPartsFee');
    if (pf) { pf.value = '0.00'; }
    recalcTotal();
  }

  /* ── Price photo ────────────────────────────────────────── */
  function handlePricePhoto(input){
    const file=input.files[0]; if(!file) return;
    const ok=['image/jpeg','image/png','image/webp','image/gif','video/mp4','video/webm'];
    if(!ok.includes(file.type)){ alert('Only images or video allowed.'); input.value=''; return; }
    if(file.size>20*1024*1024){ alert('File too large. Max 20 MB.'); input.value=''; return; }
    const pv=document.getElementById('cpmPricePhotoPreview'); pv.style.display='flex';
    const thumb=document.getElementById('cpmPricePhotoThumb');
    if(file.type.startsWith('video/')){
      thumb.innerHTML=`<i class="bi bi-camera-video-fill" style="color:var(--fg-primary);"></i> <span>${esc(file.name)}</span>`;
    } else {
      thumb.innerHTML=`<img src="${URL.createObjectURL(file)}" style="height:36px;border-radius:4px;object-fit:cover;"> <span>${esc(file.name)}</span>`;
    }
  }
  function clearPricePhoto(){
    const fi=document.getElementById('cpmPricePhoto'); if(fi) fi.value='';
    const pv=document.getElementById('cpmPricePhotoPreview'); if(pv){ pv.style.display='none'; document.getElementById('cpmPricePhotoThumb').innerHTML=''; }
  }

  function handleProofFile(input){
    const file=input.files[0]; if(!file) return;
    if(!file.type.startsWith('image/')&&!file.type.startsWith('video/')){ alert('Only images and videos allowed.'); input.value=''; return; }
    document.getElementById('cpmProofPreview').style.display='block';
    const thumb=document.getElementById('cpmProofThumb');
    if(file.type.startsWith('image/')){
      thumb.innerHTML=`<img src="${URL.createObjectURL(file)}" style="max-width:100%;max-height:160px;border-radius:8px;object-fit:contain;display:block;"><div style="font-size:0.72rem;color:var(--fg-muted);margin-top:0.25rem;">${esc(file.name)}</div>`;
    } else {
      thumb.innerHTML=`<video src="${URL.createObjectURL(file)}" controls style="max-width:100%;max-height:160px;border-radius:8px;display:block;"></video><div style="font-size:0.72rem;color:var(--fg-muted);margin-top:0.25rem;">${esc(file.name)}</div>`;
    }
  }
  function clearProofFile(){ const fi=document.getElementById('cpmProofFile'); if(fi) fi.value=''; const pv=document.getElementById('cpmProofPreview'); if(pv){ pv.style.display='none'; document.getElementById('cpmProofThumb').innerHTML=''; } }

  function handleReceiptFile(input){
    const file=input.files[0]; if(!file) return;
    const ok=['image/jpeg','image/png','image/webp','image/gif','application/pdf'];
    if(!ok.includes(file.type)){ alert('Only JPG, PNG, WebP or PDF allowed for receipts.'); input.value=''; return; }
    if(file.size>10*1024*1024){ alert('Receipt too large. Max 10 MB.'); input.value=''; return; }
    const pv=document.getElementById('cpmReceiptPreview'); pv.style.display='flex';
    const thumb=document.getElementById('cpmReceiptThumb');
    if(file.type==='application/pdf'){
      thumb.innerHTML=`<i class="bi bi-file-earmark-pdf-fill" style="color:#dc3545;"></i> ${esc(file.name)}`;
    } else {
      thumb.innerHTML=`<img src="${URL.createObjectURL(file)}" style="height:36px;border-radius:4px;object-fit:cover;"> <span>${esc(file.name)}</span>`;
    }
  }
  function clearReceiptFile(){ const fi=document.getElementById('cpmReceiptFile'); if(fi) fi.value=''; const pv=document.getElementById('cpmReceiptPreview'); if(pv){ pv.style.display='none'; document.getElementById('cpmReceiptThumb').innerHTML=''; } }

  window.openCPM = function(repairId){
    let r=(window._allRepairs||[]).find(x=>x.id==repairId)||allRepairs.find(x=>x.id==repairId);
    if(!r) r={id:repairId,customer_id:null,service_type:'shop_fix',device_name:'',first_name:'',last_name:''};
    _cpmRepairId   = repairId;
    _cpmCustomerId = r.customer_id;
    const isHome   = r.service_type==='home_service';

    document.getElementById('cpmRepairId').textContent = '#'+repairId;
    document.getElementById('cpmDevice').textContent   = r.device_name||r.device_model||'—';
    document.getElementById('cpmCustomer').textContent = ((r.first_name||'')+' '+(r.last_name||'')).trim()||'Customer';
    document.getElementById('cpmTitle').textContent    = isHome ? '✅ Complete Home Service' : '✅ Complete Repair & Send Proof';
    document.getElementById('cpmProofLabel').textContent = isHome
      ? '📎 Repair Proof (optional)'
      : '📎 Repair Proof Photo/Video (recommended)';
    document.getElementById('cpmMessage').value = isHome
      ? '✅ Your device has been fully repaired at your location! Thank you for choosing Fix&Go.'
      : '✅ Great news! Your phone is ready for pickup. See attached photo/video as proof it\'s fully fixed!';

    /* reset everything */
    document.getElementById('cpmLaborFee').value  = '';
    document.getElementById('cpmPartsFee').value  = '';
    document.getElementById('cpmFee').value        = '0';
    document.getElementById('cpmTotalDisplay').textContent = '₱0.00';
    document.getElementById('cpmPayStatus').value = 'pending_collection';
    document.getElementById('cpmMethod').value    = '';
    document.getElementById('cpmNote').value      = '';

    // Load technician inventory into the parts dropdown
    loadTechInventoryForCPM();
    document.getElementById('cpmNoteWrap').style.display    = 'none';
    document.getElementById('cpmMethodWrap').style.display  = 'none';
    document.getElementById('cpmAlert').style.display       = 'none';
    document.getElementById('cpmBtn').disabled  = false;
    document.getElementById('cpmBtn').innerHTML = '<i class="bi bi-check-circle-fill"></i> Mark Complete &amp; Send';
    ['pmCash','pmBank','pmGcash','pmMaya','pmOther'].forEach(function(pid){
      const el=document.getElementById(pid); if(el) el.classList.remove('selected');
    });
    clearProofFile(); clearReceiptFile(); clearPricePhoto();
    resetPartsList();

    /* open */
    const modal=document.getElementById('completeProofModal');
    modal.style.display='flex';
    document.body.style.overflow='hidden';
    /* scroll body to top so payment section is first thing visible */
    setTimeout(function(){ const b=document.getElementById('cpmBody'); if(b) b.scrollTop=0; }, 30);
  };

  function closeCPM(){
    document.getElementById('completeProofModal').style.display='none';
    document.body.style.overflow='';
  }

  window.submitComplete = function(){
    const laborFee    = parseFloat(document.getElementById('cpmLaborFee').value)||0;
    const partsFee    = parseFloat(document.getElementById('cpmPartsFee').value)||0;
    const totalFee    = laborFee + partsFee;
    const method      = document.getElementById('cpmMethod').value;
    const payStatus   = document.getElementById('cpmPayStatus').value;
    const note        = document.getElementById('cpmNote').value.trim();
    const msg         = document.getElementById('cpmMessage').value.trim();
    const proofInput  = document.getElementById('cpmProofFile');
    const rcptInput   = document.getElementById('cpmReceiptFile');
    const priceInput  = document.getElementById('cpmPricePhoto');
    const hasProof    = proofInput && proofInput.files && proofInput.files[0];
    const hasReceipt  = rcptInput  && rcptInput.files  && rcptInput.files[0];
    const hasPricePhoto = priceInput && priceInput.files && priceInput.files[0];
    const btn         = document.getElementById('cpmBtn');
    const alEl        = document.getElementById('cpmAlert');

    alEl.style.display = 'none';

    /* If already collected, require a payment method */
    if(payStatus === 'paid' && !method){
      showAlert('cpmAlert','Please select the payment method you collected.',false);
      document.getElementById('cpmBody').scrollTop = 0;
      return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing…';

    /* Build payment summary for message */
    const pmLabels = {cash:'Cash 💵',bank_transfer:'Bank Transfer 🏦',gcash:'GCash 📱',maya:'Maya 💳',other:'Other 💰'};
    let summary = '\n\n💰 Repair Cost Breakdown:';
    if(laborFee > 0) summary += `\n• Labor / Service Fee: ₱${laborFee.toLocaleString('en-PH',{minimumFractionDigits:2})}`;
    if(partsFee > 0) summary += `\n• Parts / Replacement Cost: ₱${partsFee.toLocaleString('en-PH',{minimumFractionDigits:2})}`;
    if(_partsList.length > 0){
      summary += '\n\n🔩 Parts / Products Replaced:';
      _partsList.forEach(p => { summary += `\n• ${p.qty > 1 ? p.qty + '× ' : ''}${p.name}`; });
    }
    if(totalFee > 0) summary += `\n\n💵 Total Amount Due: ₱${totalFee.toLocaleString('en-PH',{minimumFractionDigits:2})}`;
    if(payStatus === 'paid' && method) summary += `\n• Payment: ${pmLabels[method]||method} ✅ Collected`;
    else summary += '\n• Payment: Please tap the 💳 Pay Now button in your dashboard.';
    if(note) summary += `\n• Reference: ${note}`;
    if(hasPricePhoto) summary += '\n• Parts price photo attached below.';
    const fullMsg = (msg||'') + summary;

    /* Step 1 — send message + proof + price photo to customer */
    const sendMsg = () => {
      if(!_cpmCustomerId) return Promise.resolve();
      const fd = new FormData();
      fd.append('action','send');
      fd.append('other_id', _cpmCustomerId);
      fd.append('body', fullMsg);
      if(hasProof)      fd.append('attachment',  proofInput.files[0]);
      if(hasPricePhoto) fd.append('attachment2', priceInput.files[0]);
      return fetch('../../../api/messages',{method:'POST',credentials:'include',body:fd})
        .then(r=>r.json())
        .then(d=>{ if(!d.success) throw new Error(d.message||'Message failed.'); });
    };

    /* Step 2 — mark repair complete with cost breakdown */
    const markDone = () => {
      const fd = new FormData();
      fd.append('action',          'update_repair');
      fd.append('repair_id',       _cpmRepairId);
      fd.append('status',          'completed');
      fd.append('labor_fee',       laborFee);
      fd.append('parts_fee',       partsFee);
      fd.append('repair_fee',      totalFee);
      fd.append('payment_status',  payStatus);
      fd.append('parts_replaced',  JSON.stringify(_partsList));
      if(payStatus==='paid' && method) fd.append('payment_method', method);
      if(note)          fd.append('payment_note', note);
      if(hasReceipt)    fd.append('receipt',      rcptInput.files[0]);
      if(hasPricePhoto) fd.append('price_photo',  priceInput.files[0]);
      return fetch(API,{method:'POST',credentials:'include',body:fd})
        .then(r=>r.json())
        .then(d=>{ if(!d.success) throw new Error(d.message||'Failed to mark complete.'); });
    };

    sendMsg()
      .then(()=>markDone())
      .then(()=>{
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Done!';
        showAlert('cpmAlert','Repair marked complete! Cost saved and customer notified.',true);
        allPayments = [];
        setTimeout(()=>{ closeCPM(); loadRepairs(); loadStats(); }, 2000);
      })
      .catch(err=>{
        showAlert('cpmAlert', err.message, false);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Mark Complete &amp; Send';
      });
  };

  /* close modal on backdrop click */
  document.getElementById('completeProofModal').addEventListener('click',function(e){ if(e.target===this) closeCPM(); });
  document.getElementById('bookingDetailModal').addEventListener('click', function(e){ if(e.target===this) this.style.display='none'; });

  /* ═══════════════════════════════════════════════════════════
     STATUS UPDATE MODAL
  ═══════════════════════════════════════════════════════════ */
  let _suRepairId=null, _suCustomerId=null;

  const STATUS_UPDATES=[
    {label:'📥 Device Received',   msg:'Hi! I\'ve received your device and started the inspection. I\'ll update you once I know the exact issue. 🔍'},
    {label:'🔍 Diagnosis Done',    msg:'Diagnosis complete! I\'ve identified the issue and will begin the repair shortly.'},
    {label:'🔧 Repair In Progress',msg:'I\'m currently working on your device. I\'ll keep you posted! 🔧'},
    {label:'⚙️ Replacing Parts',   msg:'Replacing the faulty parts now. I\'ll update you once done!'},
    {label:'🧪 Testing Device',    msg:'Repair done! Testing your device to make sure everything works before returning it. ✅'},
    {label:'✅ Almost Ready',      msg:'Your device is almost ready — just doing final checks. See you soon! 🎉'},
    {label:'Custom Message',       msg:''},
  ];

  window.openStatusUpdate = function(repairId){
    const r=(window._allRepairs||[]).find(x=>x.id==repairId)||allRepairs.find(x=>x.id==repairId);
    if(!r) return;
    _suRepairId=repairId; _suCustomerId=r.customer_id;
    document.getElementById('suRepairId').textContent  = '#'+repairId;
    document.getElementById('suDevice').textContent    = r.device_name||r.device_model||'—';
    document.getElementById('suCustomer').textContent  = ((r.first_name||'')+' '+(r.last_name||'')).trim()||'Customer';
    document.getElementById('suMessage').value = '';
    document.getElementById('suAlert').style.display   = 'none';
    document.getElementById('suSendBtn').disabled      = false;
    document.getElementById('suSendBtn').innerHTML     = '<i class="bi bi-send-fill"></i> Send Update';
    clearSUFile();
    document.getElementById('suQuickPicks').innerHTML = STATUS_UPDATES.map((u,i)=>
      `<button type="button" onclick="pickSU(${i})" id="suQ${i}"
        style="padding:0.4rem 0.75rem;border-radius:8px;font-size:0.78rem;font-weight:600;cursor:pointer;border:1.5px solid var(--fg-border);color:var(--fg-text);background:var(--fg-bg);"
        onmouseenter="this.style.borderColor='#8b5cf6';this.style.color='#8b5cf6'"
        onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-text)'">${esc(u.label)}</button>`
    ).join('');
    document.getElementById('statusUpdateModal').style.display='flex';
    document.body.style.overflow='hidden';
  };

  window.pickSU = function(idx){
    document.getElementById('suMessage').value=STATUS_UPDATES[idx].msg;
    STATUS_UPDATES.forEach((_,i)=>{
      const b=document.getElementById('suQ'+i); if(!b) return;
      b.style.borderColor=i===idx?'#8b5cf6':'var(--fg-border)';
      b.style.background =i===idx?'rgba(139,92,246,0.1)':'var(--fg-bg)';
      b.style.color      =i===idx?'#8b5cf6':'var(--fg-text)';
    });
    document.getElementById('suMessage').focus();
  };

  function handleSUFile(input){
    const file=input.files[0]; if(!file) return;
    if(!file.type.startsWith('image/')&&!file.type.startsWith('video/')){ alert('Only images and videos allowed.'); input.value=''; return; }
    const pv=document.getElementById('suFilePreview'); pv.style.display='flex';
    document.getElementById('suFileThumb').innerHTML=file.type.startsWith('image/')
      ?`<i class="bi bi-image-fill" style="color:#8b5cf6;"></i> ${esc(file.name)}`
      :`<i class="bi bi-camera-video-fill" style="color:#8b5cf6;"></i> ${esc(file.name)}`;
  }
  function clearSUFile(){ const fi=document.getElementById('suFileInput'); if(fi) fi.value=''; const pv=document.getElementById('suFilePreview'); if(pv) pv.style.display='none'; }

  window.sendStatusUpdate = function(){
    const msg=document.getElementById('suMessage').value.trim();
    const fi=document.getElementById('suFileInput');
    const hasFile=fi&&fi.files&&fi.files[0];
    const btn=document.getElementById('suSendBtn');
    if(!msg&&!hasFile){ showAlert('suAlert','Please write a message or attach a photo/video.',false); return; }
    btn.disabled=true; btn.innerHTML='<i class="bi bi-hourglass-split"></i> Sending…';
    document.getElementById('suAlert').style.display='none';
    const fd=new FormData();
    fd.append('action','send'); fd.append('other_id',_suCustomerId);
    if(msg) fd.append('body',msg);
    if(hasFile) fd.append('attachment',fi.files[0]);
    fetch('../../../api/messages',{method:'POST',credentials:'include',body:fd})
      .then(r=>r.json()).then(d=>{
        if(!d.success) throw new Error(d.message||'Failed.');
        showAlert('suAlert','Update sent to customer!',true);
        document.getElementById('suMessage').value=''; clearSUFile();
        btn.innerHTML='<i class="bi bi-check-circle-fill"></i> Sent!';
        setTimeout(()=>{ btn.disabled=false; btn.innerHTML='<i class="bi bi-send-fill"></i> Send Update'; },2000);
      }).catch(err=>{ showAlert('suAlert',err.message,false); btn.disabled=false; btn.innerHTML='<i class="bi bi-send-fill"></i> Send Update'; });
  };

  function closeStatusUpdate(){ document.getElementById('statusUpdateModal').style.display='none'; document.body.style.overflow=''; }
  document.getElementById('statusUpdateModal').addEventListener('click',function(e){ if(e.target===this) closeStatusUpdate(); });

  /* ═══════════════════════════════════════════════════════════
     PAYMENT HISTORY TAB
  ═══════════════════════════════════════════════════════════ */
  const PM_LABEL={cash:'💵 Cash',gcash:'📱 GCash',maya:'💳 Maya',bank_transfer:'🏦 Bank Transfer',other:'💰 Other'};
  const PS_LABEL={paid:'✅ Paid',pending_collection:'⏳ To Collect',unpaid:'❌ Unpaid'};
  const PS_COLOR={paid:'#28A745',pending_collection:'#c98f00',unpaid:'#dc3545'};

  function loadPaymentHistory(){
    document.getElementById('payHistBody').innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);"><div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#28A745;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>Loading…</td></tr>';
    fetch(API+'?action=payment_history',{credentials:'include'})
      .then(r=>r.json()).then(d=>{
        if(!d.success){ document.getElementById('payHistBody').innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);">Could not load payment history.</td></tr>'; return; }
        allPayments=d.payments||[];
        renderPaySummary(d.summary||{});
        renderPayHistory(allPayments);
      }).catch(()=>{ document.getElementById('payHistBody').innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);">Network error.</td></tr>'; });
  }

  function renderPaySummary(s){
    const by=s.by_method||{};
    const cards=Object.entries(by).map(([m,v])=>`<div class="stat-card"><div class="stat-value" style="color:#28A745;font-size:1.3rem;">${peso(v.amount)}</div><div class="stat-label">${PM_LABEL[m]||m} (${v.count})</div></div>`).join('');
    document.getElementById('payStatRow').innerHTML=`
      <div class="stat-card"><div class="stat-value" style="color:#28A745;">${s.total_transactions||0}</div><div class="stat-label">Total Paid</div></div>
      <div class="stat-card"><div class="stat-value" style="color:#10b981;font-size:1.4rem;">${peso(s.total_revenue||0)}</div><div class="stat-label">Total Revenue</div></div>
      ${cards}`;
  }

  function applyPayFilters(){
    const q=document.getElementById('paySearchInput').value.toLowerCase();
    const m=document.getElementById('payMethodFilter').value;
    let items=allPayments;
    if(m!=='all') items=items.filter(p=>p.payment_method===m);
    if(q) items=items.filter(p=>(p.customer_name||'').toLowerCase().includes(q)||(p.device_name||'').toLowerCase().includes(q));
    renderPayHistory(items);
  }

  function renderPayHistory(payments){
    const tbody=document.getElementById('payHistBody');
    if(!payments.length){ tbody.innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--fg-muted);">No payment records found.</td></tr>'; return; }
    tbody.innerHTML=payments.map(p=>{
      const method=PM_LABEL[p.payment_method]||(p.payment_method?esc(p.payment_method):'<span style="color:var(--fg-muted);">—</span>');
      const ps=p.payment_status||'paid';
      const rcpt=p.receipt_path
        ?`<a href="${BASE_URL}${esc(p.receipt_path)}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.2rem 0.6rem;border-radius:6px;font-size:0.72rem;font-weight:700;border:1.5px solid #28A745;color:#28A745;background:transparent;text-decoration:none;" onmouseenter="this.style.background='#28A745';this.style.color='#fff'" onmouseleave="this.style.background='transparent';this.style.color='#28A745'"><i class="bi bi-file-earmark-check"></i> View</a>`
        :'<span style="color:var(--fg-muted);font-size:0.78rem;">—</span>';
      return `<tr>
        <td style="font-weight:700;color:#28A745;">#${p.id}</td>
        <td><div style="font-weight:600;">${esc(p.customer_name||'—')}</div><div style="font-size:0.73rem;color:var(--fg-muted);">${esc(p.customer_email||'')}</div></td>
        <td style="color:var(--fg-muted);">${esc(p.device_name||'—')}</td>
        <td>${method}</td>
        <td style="font-weight:700;color:#28A745;">${p.total_amount>0?peso(p.total_amount):'—'}</td>
        <td><span style="font-size:0.78rem;font-weight:700;color:${PS_COLOR[ps]||'#28A745'};">${PS_LABEL[ps]||ps}</span></td>
        <td>${rcpt}</td>
        <td style="color:var(--fg-muted);font-size:0.8rem;">${fmtDT(p.paid_at)}</td>
      </tr>`;
    }).join('');
  }

  </script>

  <!-- ══ Cancel Reason Modal ════════════════════════════════════ -->
  <div id="cancelReasonModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:9200;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:16px;width:100%;max-width:420px;box-shadow:0 24px 60px rgba(0,0,0,0.45);overflow:hidden;">
      <div style="background:linear-gradient(135deg,#dc3545,#b02a37);padding:1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;">
        <div>
          <div style="color:#fff;font-weight:800;font-size:0.95rem;">✕ Cancel Booking</div>
          <div style="color:rgba(255,255,255,0.75);font-size:0.75rem;margin-top:0.1rem;">Please provide a reason for cancellation</div>
        </div>
        <button onclick="closeCancelReasonModal()" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;font-size:0.95rem;cursor:pointer;font-weight:700;flex-shrink:0;" onmouseenter="this.style.background='rgba(255,255,255,0.3)'" onmouseleave="this.style.background='rgba(255,255,255,0.15)'">✕</button>
      </div>
      <div style="padding:1.35rem;">
        <label style="font-size:0.82rem;font-weight:700;color:var(--fg-text);display:block;margin-bottom:0.5rem;">Reason for Cancellation <span style="color:#dc3545;">*</span></label>
        <textarea id="cancelReasonInput" rows="3" placeholder="e.g. Customer is no longer available, part is out of stock, schedule conflict…"
          style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;resize:vertical;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
          onfocus="this.style.borderColor='#dc3545'" onblur="this.style.borderColor='var(--fg-border)'"></textarea>
        <div id="cancelReasonError" style="display:none;color:#dc3545;font-size:0.78rem;font-weight:600;margin-top:0.35rem;">⚠ Please enter a reason before cancelling.</div>
        <div style="display:flex;gap:0.75rem;margin-top:1rem;justify-content:flex-end;">
          <button onclick="closeCancelReasonModal()" style="padding:0.5rem 1.1rem;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.85rem;font-weight:600;cursor:pointer;">Go Back</button>
          <button onclick="submitCancelReason()" style="padding:0.5rem 1.25rem;border-radius:8px;border:none;background:#dc3545;color:#fff;font-size:0.85rem;font-weight:700;cursor:pointer;transition:opacity 0.2s;" onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">✕ Confirm Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.getElementById('cancelReasonModal').addEventListener('click', function(e){ if(e.target===this) closeCancelReasonModal(); });
  </script>

</body>
</html>




