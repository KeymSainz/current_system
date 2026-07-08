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
  <title>Fix&amp;Go — Seller Centre</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8.1" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5.1" />
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
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay-bg.open{display:block;}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .cu-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .cu-sidebar.open{transform:translateX(0);}
      .cu-main{padding:1.25rem;}
      .form-row{grid-template-columns:1fr !important;}
    }
    /* Role picker cards */
    .role-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;margin-bottom:2rem;}
    .role-card{background:var(--fg-card-bg);border:2px solid var(--fg-border);border-radius:18px;padding:2rem;display:flex;flex-direction:column;gap:1rem;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s,border-color 0.2s;}
    .role-card:hover{transform:translateY(-4px);box-shadow:0 14px 40px rgba(0,0,0,0.12);border-color:var(--fg-primary);}
    .role-card.selected{border-color:var(--fg-primary);background:rgba(230,168,0,0.04);}
    .role-icon{width:64px;height:64px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;}
    .role-title{font-size:1.1rem;font-weight:800;color:var(--fg-text);}
    .role-desc{font-size:0.85rem;color:var(--fg-muted);line-height:1.6;flex:1;}
    .role-perks{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.4rem;}
    .role-perks li{font-size:0.82rem;color:var(--fg-text);display:flex;align-items:center;gap:0.5rem;}
    .role-perks li i{color:#28A745;font-size:0.85rem;flex-shrink:0;}
    /* Wizard */
    .wizard-wrap{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;overflow:hidden;}
    .wizard-stepper{display:flex;align-items:center;padding:1.5rem 2rem;border-bottom:1px solid var(--fg-border);gap:0;background:var(--fg-bg);}
    .wz-step{display:flex;align-items:center;gap:0.6rem;flex:1;position:relative;}
    .wz-step:not(:last-child)::after{content:'';flex:1;height:2px;background:var(--fg-border);margin:0 0.5rem;transition:background 0.3s;}
    .wz-step.done:not(:last-child)::after{background:var(--fg-primary);}
    .wz-dot{width:32px;height:32px;border-radius:50%;border:2px solid var(--fg-border);background:var(--fg-card-bg);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;color:var(--fg-muted);flex-shrink:0;transition:all 0.3s;}
    .wz-step.active .wz-dot{border-color:var(--fg-primary);background:var(--fg-primary);color:#fff;}
    .wz-step.done .wz-dot{border-color:var(--fg-primary);background:var(--fg-primary);color:#fff;}
    .wz-label{font-size:0.78rem;font-weight:700;color:var(--fg-muted);white-space:nowrap;transition:color 0.3s;}
    .wz-step.active .wz-label,.wz-step.done .wz-label{color:var(--fg-primary);}
    .wizard-body{padding:2rem;}
    .wz-panel{display:none;}
    .wz-panel.active{display:block;}
    .wizard-foot{padding:1.25rem 2rem;border-top:1px solid var(--fg-border);display:flex;justify-content:space-between;align-items:center;gap:1rem;}
    /* Form */
    .form-section{margin-bottom:1.75rem;}
    .form-section-title{font-size:0.95rem;font-weight:800;color:var(--fg-text);margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;gap:0.5rem;}
    .form-group{margin-bottom:1rem;}
    .form-group label{display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.4rem;}
    .form-group label span{color:#dc3545;margin-left:2px;}
    .form-input{width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;font-family:inherit;}
    .form-input:focus{border-color:var(--fg-primary);box-shadow:0 0 0 3px rgba(230,168,0,0.15);}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
    /* Doc dropzone */
    .doc-dropzone{border:2px dashed var(--fg-border);border-radius:10px;padding:1rem;text-align:center;cursor:pointer;transition:border-color 0.2s,background 0.2s;background:var(--fg-bg);}
    .doc-dropzone:hover{border-color:var(--fg-primary);background:rgba(230,168,0,0.04);}
    .doc-dropzone.has-file{border-color:#28A745;background:rgba(40,167,69,0.04);}
    .doc-dropzone-icon{font-size:1.5rem;color:var(--fg-muted);margin-bottom:0.35rem;}
    .doc-dropzone-label{font-size:0.8rem;font-weight:700;color:var(--fg-text);}
    .doc-dropzone-sub{font-size:0.72rem;color:var(--fg-muted);}
    /* Review */
    .review-section{background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;padding:1.25rem;margin-bottom:1.25rem;}
    .review-section-title{font-size:0.82rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.85rem;}
    .review-row{display:flex;gap:1rem;margin-bottom:0.55rem;font-size:0.85rem;}
    .review-label{font-weight:700;color:var(--fg-text);min-width:160px;flex-shrink:0;}
    .review-value{color:var(--fg-muted);}
    .doc-badge{display:inline-flex;align-items:center;gap:0.35rem;font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:6px;}
    .doc-badge.ok{background:rgba(40,167,69,0.12);color:#28A745;}
    .doc-badge.na{background:rgba(107,114,128,0.1);color:var(--fg-muted);}
    /* Buttons */
    .btn-wz-next{padding:0.65rem 1.75rem;border-radius:10px;background:var(--fg-primary);color:#fff;border:none;font-weight:700;font-size:0.9rem;cursor:pointer;transition:all 0.2s;}
    .btn-wz-next:hover{background:var(--fg-primary-dark,#c98f00);transform:translateY(-1px);}
    .btn-wz-back{padding:0.65rem 1.5rem;border-radius:10px;background:transparent;color:var(--fg-muted);border:1.5px solid var(--fg-border);font-weight:700;font-size:0.9rem;cursor:pointer;transition:all 0.2s;}
    .btn-wz-back:hover{border-color:var(--fg-text);color:var(--fg-text);}
    .btn-submit{padding:0.65rem 2rem;border-radius:10px;background:#28A745;color:#fff;border:none;font-weight:700;font-size:0.9rem;cursor:pointer;transition:all 0.2s;}
    .btn-submit:hover{background:#1e8035;transform:translateY(-1px);}
    .btn-submit:disabled{opacity:0.6;cursor:not-allowed;transform:none;}
    .alert-bar{padding:0.75rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;}
    .alert-success{background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);}
    .alert-danger{background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);}
    .alert-info{background:rgba(59,130,246,0.08);color:#3b82f6;border:1px solid rgba(59,130,246,0.2);}
    @keyframes spin{to{transform:rotate(360deg);}}
    @keyframes scaleIn{from{transform:scale(0.5);opacity:0;}to{transform:scale(1);opacity:1;}}
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge customer">👤 Customer</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="../../../index.php?browse=1" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;">
        <i class="bi bi-shop"></i> Browse Shop
      </a>
      <button onclick="customerLogout()" class="btn btn-sm"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
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

  <div class="sidebar-overlay-bg" id="sidebarOverlay"></div>

  <div class="cu-layout">
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
        <li><a href="repairs.php"><i class="bi bi-tools"></i> My Repairs</a></li>
        <li><a href="wishlist.php"><i class="bi bi-heart-fill"></i> Wishlist</a></li>
        <li><a href="vouchers.php"><i class="bi bi-ticket-perforated-fill"></i> My Vouchers</a></li>
      </ul>
      <div class="sidebar-section-label">Fix&amp;Go</div>
      <ul class="sidebar-nav">
        <li><a href="seller-centre.php" class="active"><i class="bi bi-shop-window"></i> Seller Centre</a></li>
        <li><a href="become-technician.php"><i class="bi bi-wrench-adjustable-circle-fill"></i> Become a Technician</a></li>
      </ul>
    </aside>

    <main class="cu-main">
      <div class="page-header">
        <h2><i class="bi bi-shop-window" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Fix&amp;Go Seller Centre</h2>
        <p>Grow your business on Fix&amp;Go — register as a Supplier or Shop Owner</p>
      </div>

      <div id="alertBox" style="display:none;margin-bottom:1rem;"></div>
      <div id="statusArea"></div>

      <!-- ── Role picker (shown when no application) ── -->
      <div id="rolePickerSection">
        <div class="alert-bar alert-info" style="margin-bottom:1.75rem;">
          <i class="bi bi-info-circle-fill"></i>
          You are currently a <strong>Customer</strong>. Applying creates a separate seller account with a different email.
        </div>
        <div class="role-grid">
          <div class="role-card" id="cardSupplier" onclick="selectRole('supplier')">
            <div class="role-icon" style="background:rgba(16,185,129,0.12);color:#10b981;"><i class="bi bi-box-seam-fill"></i></div>
            <div>
              <div class="role-title">Supplier</div>
              <div class="role-desc">Supply phone parts, accessories, and products to repair shops on Fix&amp;Go.</div>
            </div>
            <ul class="role-perks">
              <li><i class="bi bi-check-circle-fill"></i> List and manage your product catalog</li>
              <li><i class="bi bi-check-circle-fill"></i> Receive orders from shop owners</li>
              <li><i class="bi bi-check-circle-fill"></i> Track deliveries and sales revenue</li>
            </ul>
          </div>
          <div class="role-card" id="cardOwner" onclick="selectRole('owner')">
            <div class="role-icon" style="background:rgba(230,168,0,0.12);color:var(--fg-primary);"><i class="bi bi-shop-window"></i></div>
            <div>
              <div class="role-title">Shop Owner</div>
              <div class="role-desc">Open and manage your own phone repair shop on Fix&amp;Go.</div>
            </div>
            <ul class="role-perks">
              <li><i class="bi bi-check-circle-fill"></i> Manage staff, technicians &amp; supervisors</li>
              <li><i class="bi bi-check-circle-fill"></i> Accept bookings and track repairs</li>
              <li><i class="bi bi-check-circle-fill"></i> Purchase parts from suppliers</li>
            </ul>
          </div>
        </div>
        <div style="text-align:center;margin-top:0.5rem;">
          <button id="btnStartWizard" onclick="startWizard()"
            style="display:inline-flex;align-items:center;gap:0.6rem;padding:0.75rem 2.5rem;border-radius:12px;background:var(--fg-primary);color:#fff;border:none;font-weight:700;font-size:1rem;cursor:pointer;opacity:0.4;pointer-events:none;transition:all 0.2s;">
            <i class="bi bi-arrow-right-circle-fill"></i> Continue
          </button>
          <p style="font-size:0.78rem;color:var(--fg-muted);margin-top:0.75rem;">Select a role above to continue</p>
        </div>
      </div>

      <!-- ── Wizard (shown after role selected) ── -->
      <div class="wizard-wrap" id="wizardWrap" style="display:none;">
        <div class="wizard-stepper">
          <div class="wz-step active" id="step-dot-1">
            <div class="wz-dot" id="dot1">1</div>
            <span class="wz-label">Account Info</span>
          </div>
          <div class="wz-step" id="step-dot-2">
            <div class="wz-dot" id="dot2">2</div>
            <span class="wz-label">Documents</span>
          </div>
          <div class="wz-step" id="step-dot-3">
            <div class="wz-dot" id="dot3">3</div>
            <span class="wz-label">Review &amp; Submit</span>
          </div>
        </div>

        <div class="wizard-body">
          <!-- Step 1: Account Info -->
          <div class="wz-panel active" id="panel1">
            <div class="alert-bar alert-info" style="margin-bottom:1.5rem;" id="roleInfoBanner">
              <i class="bi bi-info-circle-fill"></i>
              <span id="roleInfoText">Fill in your seller account details.</span>
            </div>

            <div class="form-section">
              <div class="form-section-title"><i class="bi bi-person-badge-fill" style="color:var(--fg-primary);"></i> Personal Information</div>
              <div class="form-row">
                <div class="form-group">
                  <label>First Name <span>*</span></label>
                  <input type="text" class="form-input" id="sellerFirstName" placeholder="e.g. Juan" maxlength="80">
                </div>
                <div class="form-group">
                  <label>Last Name <span>*</span></label>
                  <input type="text" class="form-input" id="sellerLastName" placeholder="e.g. Santos" maxlength="80">
                </div>
              </div>
              <div class="form-group">
                <label>Company / Business Name <span>*</span></label>
                <input type="text" class="form-input" id="sellerCompany" placeholder="e.g. Santos Trading Co." maxlength="150">
              </div>
              <div class="form-group" id="shopNameGroup" style="display:none;">
                <label>Shop Name <span>*</span></label>
                <input type="text" class="form-input" id="sellerShopName" placeholder="e.g. QuickFix Manila" maxlength="150">
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Seller Email <span>*</span></label>
                  <input type="email" class="form-input" id="sellerEmail" placeholder="Different from your customer email">
                </div>
                <div class="form-group">
                  <label>Phone Number <span>*</span></label>
                  <input type="tel" class="form-input" id="sellerPhone" placeholder="+63 9XX XXX XXXX">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Password <span>*</span></label>
                  <input type="password" class="form-input" id="sellerPassword" placeholder="Min. 8 characters">
                </div>
                <div class="form-group">
                  <label>Confirm Password <span>*</span></label>
                  <input type="password" class="form-input" id="sellerConfirmPassword" placeholder="Repeat password">
                </div>
              </div>
            </div>
          </div><!-- /panel1 -->

          <!-- Step 2: Documents -->
          <div class="wz-panel" id="panel2">
            <div class="alert-bar alert-info" style="margin-bottom:1.5rem;">
              <i class="bi bi-shield-check-fill"></i>
              Upload clear photos or scans. Accepted: JPG, PNG, PDF · Max 5MB each.
            </div>

            <div class="form-section">
              <div class="form-section-title"><i class="bi bi-file-earmark-text-fill" style="color:var(--fg-primary);"></i> Required Documents</div>
              <div class="form-row">
                <div class="form-group">
                  <label>Government-Issued ID <span>*</span></label>
                  <div class="doc-dropzone" id="dz-govId" onclick="document.getElementById('govIdFile').click()">
                    <div class="doc-dropzone-icon"><i class="bi bi-card-heading"></i></div>
                    <div class="doc-dropzone-label">Upload Gov't ID</div>
                    <div class="doc-dropzone-sub">Driver's License · Passport · PhilSys · Max 5MB</div>
                  </div>
                  <input type="file" id="govIdFile" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none">
                </div>
                <div class="form-group">
                  <label>Bank Account Proof <span>*</span></label>
                  <div class="doc-dropzone" id="dz-bank" onclick="document.getElementById('bankFile').click()">
                    <div class="doc-dropzone-icon"><i class="bi bi-bank"></i></div>
                    <div class="doc-dropzone-label">Upload Bank Proof</div>
                    <div class="doc-dropzone-sub">Bank statement · Passbook · Account screenshot</div>
                  </div>
                  <input type="file" id="bankFile" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none">
                </div>
              </div>
            </div>

            <div class="form-section">
              <div class="form-section-title"><i class="bi bi-file-earmark-check-fill" style="color:var(--fg-primary);"></i> Business Documents</div>
              <div class="form-row">
                <div class="form-group">
                  <label>BIR Certificate of Registration <small style="color:var(--fg-muted);font-weight:400;">(recommended)</small></label>
                  <div class="doc-dropzone" id="dz-bir" onclick="document.getElementById('birFile').click()">
                    <div class="doc-dropzone-icon"><i class="bi bi-receipt"></i></div>
                    <div class="doc-dropzone-label">Upload BIR COR</div>
                    <div class="doc-dropzone-sub">BIR Form 2303 · Optional</div>
                  </div>
                  <input type="file" id="birFile" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none">
                </div>
                <div class="form-group" id="dtiGroup" style="display:none;">
                  <label>DTI / SEC Registration <span id="dtiRequired">*</span></label>
                  <div class="doc-dropzone" id="dz-dti" onclick="document.getElementById('dtiFile').click()">
                    <div class="doc-dropzone-icon"><i class="bi bi-building"></i></div>
                    <div class="doc-dropzone-label">Upload DTI / SEC</div>
                    <div class="doc-dropzone-sub">DTI Business Name Certificate or SEC Registration</div>
                  </div>
                  <input type="file" id="dtiFile" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none">
                </div>
              </div>
            </div>
          </div><!-- /panel2 -->

          <!-- Step 3: Review & Submit -->
          <div class="wz-panel" id="panel3">
            <div class="alert-bar alert-info" style="margin-bottom:1.5rem;">
              <i class="bi bi-eye-fill"></i>
              Review your information before submitting. Go back to make changes.
            </div>

            <div class="review-section">
              <div class="review-section-title">Account Information</div>
              <div class="review-row"><span class="review-label">Role</span><span class="review-value" id="rv-role">—</span></div>
              <div class="review-row"><span class="review-label">Full Name</span><span class="review-value" id="rv-name">—</span></div>
              <div class="review-row"><span class="review-label">Company</span><span class="review-value" id="rv-company">—</span></div>
              <div class="review-row" id="rv-shopRow" style="display:none;"><span class="review-label">Shop Name</span><span class="review-value" id="rv-shop">—</span></div>
              <div class="review-row"><span class="review-label">Seller Email</span><span class="review-value" id="rv-email">—</span></div>
              <div class="review-row"><span class="review-label">Phone</span><span class="review-value" id="rv-phone">—</span></div>
            </div>

            <div class="review-section">
              <div class="review-section-title">Documents Submitted</div>
              <div class="review-row" style="flex-direction:column;gap:0.4rem;"><span class="review-label">Government ID</span><span class="review-value" id="rv-govid">—</span></div>
              <div class="review-row" style="flex-direction:column;gap:0.4rem;"><span class="review-label">Bank Proof</span><span class="review-value" id="rv-bank">—</span></div>
              <div class="review-row" style="flex-direction:column;gap:0.4rem;"><span class="review-label">BIR Certificate</span><span class="review-value" id="rv-bir">—</span></div>
              <div class="review-row" id="rv-dtiRow" style="flex-direction:column;gap:0.4rem;display:none;"><span class="review-label">DTI / SEC</span><span class="review-value" id="rv-dti">—</span></div>
            </div>

            <div class="alert-bar alert-info">
              <i class="bi bi-shield-check-fill"></i>
              By submitting, you confirm all information is accurate and agree to Fix&amp;Go's Terms of Service.
            </div>
          </div><!-- /panel3 -->
        </div><!-- /wizard-body -->

        <div class="wizard-foot">
          <button class="btn-wz-back" id="btnBack" style="display:none;" onclick="wizardBack()">
            <i class="bi bi-arrow-left"></i> Back
          </button>
          <div style="flex:1;"></div>
          <button class="btn-wz-next" id="btnNext" onclick="wizardNext()">
            Next <i class="bi bi-arrow-right"></i>
          </button>
          <button class="btn-submit" id="btnSubmit" style="display:none;" onclick="submitApplication()">
            <i class="bi bi-send-fill"></i> Submit Application
          </button>
        </div>
      </div><!-- /wizardWrap -->
    </main>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script src="../../assets/js/session-timeout.js"></script>
  <script>
  'use strict';
  function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

  var selectedRole = '';
  var currentStep  = 1;

  /* ── Role selection ── */
  function selectRole(role) {
    selectedRole = role;
    document.getElementById('cardSupplier').classList.toggle('selected', role === 'supplier');
    document.getElementById('cardOwner').classList.toggle('selected', role === 'owner');
    var btn = document.getElementById('btnStartWizard');
    btn.style.opacity = '1';
    btn.style.pointerEvents = 'auto';
    document.querySelector('#btnStartWizard + p').textContent =
      role === 'supplier' ? 'Registering as Supplier' : 'Registering as Shop Owner';
  }

  function startWizard() {
    if (!selectedRole) return;
    document.getElementById('rolePickerSection').style.display = 'none';
    document.getElementById('wizardWrap').style.display = 'block';
    // Show/hide owner-specific fields
    var isOwner = selectedRole === 'owner';
    document.getElementById('shopNameGroup').style.display = isOwner ? 'block' : 'none';
    document.getElementById('dtiGroup').style.display = isOwner ? 'block' : 'none';
    document.getElementById('roleInfoText').textContent =
      isOwner ? 'Registering as Shop Owner — fill in your business details below.'
               : 'Registering as Supplier — fill in your business details below.';
    goToStep(1);
  }

  /* ── Wizard navigation ── */
  function wizardNext() {
    if (!validateStep(currentStep)) return;
    if (currentStep === 2) buildReview();
    goToStep(currentStep + 1);
  }
  function wizardBack() {
    goToStep(currentStep - 1);
  }
  function goToStep(n) {
    document.getElementById('panel' + currentStep).classList.remove('active');
    document.getElementById('step-dot-' + currentStep).classList.remove('active');
    if (n > currentStep) document.getElementById('step-dot-' + currentStep).classList.add('done');
    else document.getElementById('step-dot-' + currentStep).classList.remove('done');
    currentStep = n;
    document.getElementById('panel' + currentStep).classList.add('active');
    document.getElementById('step-dot-' + currentStep).classList.add('active');
    document.getElementById('btnBack').style.display   = currentStep > 1 ? 'inline-flex' : 'none';
    document.getElementById('btnNext').style.display   = currentStep < 3 ? 'inline-flex' : 'none';
    document.getElementById('btnSubmit').style.display = currentStep === 3 ? 'inline-flex' : 'none';
    window.scrollTo({top:0,behavior:'smooth'});
  }

  /* ── Validation ── */
  function validateStep(step) {
    var show = function(msg){ showAlert('danger', msg); return false; };
    if (step === 1) {
      if (!document.getElementById('sellerFirstName').value.trim()) return show('First name is required.');
      if (!document.getElementById('sellerLastName').value.trim())  return show('Last name is required.');
      if (!document.getElementById('sellerCompany').value.trim())   return show('Company name is required.');
      if (selectedRole === 'owner' && !document.getElementById('sellerShopName').value.trim())
        return show('Shop name is required for Shop Owner.');
      if (!document.getElementById('sellerEmail').value.trim())     return show('Seller email is required.');
      if (!document.getElementById('sellerPhone').value.trim())     return show('Phone number is required.');
      var pw = document.getElementById('sellerPassword').value;
      if (pw.length < 8) return show('Password must be at least 8 characters.');
      if (pw !== document.getElementById('sellerConfirmPassword').value) return show('Passwords do not match.');
    }
    if (step === 2) {
      if (!document.getElementById('govIdFile').files[0]) return show('Government-issued ID is required.');
      if (!document.getElementById('bankFile').files[0])  return show('Bank account proof is required.');
      if (selectedRole === 'owner' && !document.getElementById('dtiFile').files[0])
        return show('DTI / SEC registration is required for Shop Owners.');
    }
    hideAlert();
    return true;
  }

  /* ── Review ── */
  function buildReview() {
    var fn = document.getElementById('sellerFirstName').value.trim();
    var ln = document.getElementById('sellerLastName').value.trim();
    document.getElementById('rv-role').textContent    = selectedRole === 'owner' ? '🏪 Shop Owner' : '📦 Supplier';
    document.getElementById('rv-name').textContent    = fn + ' ' + ln;
    document.getElementById('rv-company').textContent = document.getElementById('sellerCompany').value.trim();
    document.getElementById('rv-email').textContent   = document.getElementById('sellerEmail').value.trim();
    document.getElementById('rv-phone').textContent   = document.getElementById('sellerPhone').value.trim();
    var shopRow = document.getElementById('rv-shopRow');
    if (selectedRole === 'owner') {
      shopRow.style.display = 'flex';
      document.getElementById('rv-shop').textContent = document.getElementById('sellerShopName').value.trim();
    } else {
      shopRow.style.display = 'none';
    }
    document.getElementById('rv-govid').innerHTML = docBadge('govIdFile');
    document.getElementById('rv-bank').innerHTML  = docBadge('bankFile');
    document.getElementById('rv-bir').innerHTML   = docBadge('birFile');
    var dtiRow = document.getElementById('rv-dtiRow');
    if (selectedRole === 'owner') {
      dtiRow.style.display = 'flex';
      document.getElementById('rv-dti').innerHTML = docBadge('dtiFile');
    } else {
      dtiRow.style.display = 'none';
    }
  }

  function docBadge(fileId) {
    var f = document.getElementById(fileId) && document.getElementById(fileId).files[0];
    if (!f) return '<span class="doc-badge na">Not provided</span>';
    var isImg = f.type.startsWith('image/');
    var url   = URL.createObjectURL(f);
    return '<div style="display:flex;flex-direction:column;gap:0.5rem;">'
      + '<span class="doc-badge ok"><i class="bi bi-check-circle-fill"></i> ' + esc(f.name) + '</span>'
      + (isImg
          ? '<img src="' + url + '" alt="preview" style="max-width:220px;max-height:140px;border-radius:8px;border:1px solid var(--fg-border);object-fit:contain;display:block;">'
          : '<a href="' + url + '" target="_blank" style="font-size:0.75rem;color:var(--fg-primary);display:inline-flex;align-items:center;gap:0.3rem;"><i class="bi bi-file-earmark-text"></i> View PDF</a>')
      + '</div>';
  }

  /* ── Submit ── */
  function submitApplication() {
    var btn  = document.getElementById('btnSubmit');
    var user = FGAuth.UserStore.get();
    btn.disabled = true;
    btn.innerHTML = '<span style="display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,0.4);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;margin-right:0.5rem;"></span> Submitting…';

    var fd = new FormData();
    fd.append('role',            selectedRole);
    fd.append('firstName',       document.getElementById('sellerFirstName').value.trim());
    fd.append('lastName',        document.getElementById('sellerLastName').value.trim());
    fd.append('companyName',     document.getElementById('sellerCompany').value.trim());
    fd.append('shopName',        document.getElementById('sellerShopName').value.trim());
    fd.append('email',           document.getElementById('sellerEmail').value.trim());
    fd.append('phone',           document.getElementById('sellerPhone').value.trim());
    fd.append('password',        document.getElementById('sellerPassword').value);
    fd.append('confirmPassword', document.getElementById('sellerConfirmPassword').value);
    if (user && user.id) fd.append('customer_id', user.id);

    var govId = document.getElementById('govIdFile').files[0];
    var bank  = document.getElementById('bankFile').files[0];
    var bir   = document.getElementById('birFile').files[0];
    var dti   = document.getElementById('dtiFile').files[0];
    if (govId) fd.append('govIdFile', govId);
    if (bank)  fd.append('bankFile',  bank);
    if (bir)   fd.append('birFile',   bir);
    if (dti)   fd.append('dtiFile',   dti);

    fetch('../../../backend/seller_apply.php', {method:'POST', body:fd, credentials:'include'})
      .then(function(r){return r.json();})
      .then(function(d) {
        if (d.success) {
          document.getElementById('wizardWrap').style.display = 'none';
          document.getElementById('statusArea').innerHTML =
            '<div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;padding:2.5rem;text-align:center;max-width:520px;margin:0 auto;">'
            + '<div style="width:72px;height:72px;border-radius:50%;background:rgba(40,167,69,0.12);border:3px solid #28A745;display:flex;align-items:center;justify-content:center;font-size:2rem;color:#28A745;margin:0 auto 1.25rem;animation:scaleIn 0.4s ease;"><i class="bi bi-check-circle-fill"></i></div>'
            + '<h4 style="font-weight:800;color:var(--fg-text);margin-bottom:0.5rem;">Application Submitted!</h4>'
            + '<p style="color:var(--fg-muted);font-size:0.88rem;margin-bottom:1.25rem;">' + esc(d.message) + '</p>'
            + '<a href="dashboard.php" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.5rem;border-radius:10px;background:var(--fg-primary);color:#fff;font-weight:700;font-size:0.88rem;text-decoration:none;"><i class="bi bi-house-fill"></i> Back to Dashboard</a>'
            + '</div>';
        } else {
          showAlert('danger', d.message || 'Submission failed. Please try again.');
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-send-fill"></i> Submit Application';
        }
      })
      .catch(function() {
        showAlert('danger', 'Network error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Submit Application';
      });
  }

  /* ── Doc dropzone init ── */
  function initDropzones() {
    [
      {fileId:'govIdFile', dzId:'dz-govId'},
      {fileId:'bankFile',  dzId:'dz-bank'},
      {fileId:'birFile',   dzId:'dz-bir'},
      {fileId:'dtiFile',   dzId:'dz-dti'},
    ].forEach(function(item) {
      var input = document.getElementById(item.fileId);
      var dz    = document.getElementById(item.dzId);
      if (!input || !dz) return;
      input.addEventListener('change', function() {
        if (this.files[0]) {
          var f = this.files[0];
          if (f.size > 5 * 1024 * 1024) {
            showAlert('danger', 'File "' + f.name + '" exceeds 5MB limit.');
            this.value = '';
            return;
          }
          dz.classList.add('has-file');
          dz.querySelector('.doc-dropzone-label').textContent = f.name;
          dz.querySelector('.doc-dropzone-sub').textContent   = (f.size/1024/1024).toFixed(2) + ' MB';
          dz.querySelector('.doc-dropzone-icon').innerHTML    = '<i class="bi bi-check-circle-fill" style="color:#28A745;"></i>';
        }
      });
    });
  }

  /* ── Alert helpers ── */
  function showAlert(type, msg) {
    var el = document.getElementById('alertBox');
    el.className = 'alert-bar alert-' + type;
    el.innerHTML = '<i class="bi bi-' + (type==='danger'?'exclamation-circle-fill':'check-circle-fill') + '"></i> ' + esc(msg);
    el.style.display = 'flex';
    el.scrollIntoView({behavior:'smooth',block:'nearest'});
  }
  function hideAlert() { document.getElementById('alertBox').style.display = 'none'; }

  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('../../../backend/logout.php').finally(function(){ window.location.href = '../../../login.html'; });
    });
  }
  function toggleNotifDropdown(){}
  function markAllRead(){}

  /* ── Application status rendering (same as before) ── */
  function checkApplicationStatus(user) {
    fetch('../../../backend/check_application.php?customer_id=' + (user.id || ''), {credentials:'include'})
      .then(function(r){return r.json();})
      .then(function(data) {
        var app = data.application;
        if (!app) return; // no application — show role picker
        // Hide role picker and wizard, show status
        document.getElementById('rolePickerSection').style.display = 'none';
        document.getElementById('wizardWrap').style.display = 'none';
        if      (app.status === 'approved') renderApprovedUI(app, user);
        else if (app.status === 'pending')  renderPendingUI(app, user);
        else if (app.status === 'rejected') renderRejectedUI(app, user);
      })
      .catch(function(){});
  }

  function renderApprovedUI(app, user) {
    var roleLabel = app.role === 'owner' ? 'Shop Owner' : 'Supplier';
    var roleIcon  = app.role === 'owner' ? 'shop-window' : 'box-seam-fill';
    document.getElementById('statusArea').innerHTML =
      '<div style="background:linear-gradient(135deg,rgba(40,167,69,0.12),rgba(40,167,69,0.04));border:2px solid rgba(40,167,69,0.35);border-radius:18px;padding:2rem;margin-bottom:1.75rem;text-align:center;">'
      + '<div style="width:80px;height:80px;border-radius:50%;background:rgba(40,167,69,0.15);border:3px solid #28A745;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;animation:scaleIn 0.4s ease;"><i class="bi bi-check-lg" style="font-size:2.5rem;color:#28A745;"></i></div>'
      + '<div style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(40,167,69,0.15);border:1px solid rgba(40,167,69,0.3);border-radius:50px;padding:0.3rem 1rem;margin-bottom:1rem;"><span style="width:8px;height:8px;border-radius:50%;background:#28A745;display:inline-block;"></span><span style="font-size:0.75rem;font-weight:700;color:#28A745;text-transform:uppercase;letter-spacing:1px;">Application Approved</span></div>'
      + '<h3 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin-bottom:0.5rem;">🎉 Congratulations, ' + esc(user.firstName || 'there') + '!</h3>'
      + '<p style="font-size:0.92rem;color:var(--fg-muted);margin-bottom:1.5rem;line-height:1.6;">Your <strong>' + roleLabel + '</strong> application has been approved.</p>'
      + (app.admin_notes ? '<div style="background:rgba(230,168,0,0.08);border:1px solid rgba(230,168,0,0.25);border-radius:10px;padding:0.85rem 1.1rem;margin-bottom:1.5rem;text-align:left;font-size:0.83rem;"><i class="bi bi-chat-quote-fill" style="color:var(--fg-primary);margin-right:0.4rem;"></i><strong>Admin note:</strong> ' + esc(app.admin_notes) + '</div>' : '')
      + '<button id="switchToSellerBtn" onclick="switchToSeller(\'' + app.role + '\',' + (user.id||0) + ')" style="display:inline-flex;align-items:center;gap:0.5rem;background:#28A745;color:#fff;padding:0.75rem 1.75rem;border-radius:10px;font-weight:700;font-size:0.95rem;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(40,167,69,0.3);"><i class="bi bi-' + roleIcon + '"></i> Switch to ' + roleLabel + ' Dashboard</button>'
      + '</div>';
  }

  function switchToSeller(role, customerId) {
    var btn = document.getElementById('switchToSellerBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span style="width:16px;height:16px;border:2px solid rgba(255,255,255,0.4);border-top-color:#fff;border-radius:50%;display:inline-block;animation:spin 0.7s linear infinite;"></span> Switching…'; }
    fetch('../../../backend/switch_to_seller.php', {method:'POST',credentials:'include',headers:{'Content-Type':'application/json'},body:JSON.stringify({customer_id:customerId,role:role})})
      .then(function(r){return r.json();})
      .then(function(d) {
        if (!d.success) throw new Error(d.message || 'Switch failed.');
        FGAuth.UserStore.save(d.user);
        document.body.insertAdjacentHTML('beforeend','<div id="switchOverlay" style="position:fixed;inset:0;background:var(--fg-bg);z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1rem;"><div style="width:64px;height:64px;border-radius:50%;background:rgba(40,167,69,0.15);border:3px solid #28A745;display:flex;align-items:center;justify-content:center;animation:scaleIn 0.3s ease;"><i class="bi bi-check-lg" style="font-size:2rem;color:#28A745;"></i></div><div style="font-size:1.1rem;font-weight:800;color:var(--fg-text);">Switching to ' + (role==='owner'?'Shop Owner':'Supplier') + ' account…</div></div>');
        setTimeout(function(){ window.location.href = '../../../' + d.redirect; }, 1200);
      })
      .catch(function(err) {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-shop-window"></i> Switch to Dashboard'; }
        showAlert('danger', err.message);
      });
  }

  function renderPendingUI(app, user) {
    var roleLabel = app.role === 'owner' ? 'Shop Owner' : 'Supplier';
    var submitted = app.submitted_at ? new Date(app.submitted_at).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}) : '—';
    fetch('../../../backend/document_approvals.php?action=my_documents&customer_id=' + (user.id||''), {credentials:'include'})
      .then(function(r){return r.json();})
      .then(function(data) {
        var docs = (data.success && data.documents) ? data.documents : [];
        var hasRejected = docs.some(function(d){return d.status==='rejected';});
        var docCards = docs.filter(function(d){return d.path;}).map(function(doc) {
          var statusIcon  = {pending:'<i class="bi bi-hourglass-split" style="color:#f59e0b;font-size:1.2rem;"></i>',approved:'<i class="bi bi-check-circle-fill" style="color:#28A745;font-size:1.2rem;"></i>',rejected:'<i class="bi bi-x-circle-fill" style="color:#dc3545;font-size:1.2rem;"></i>'}[doc.status];
          var statusBadge = {pending:'<span style="background:rgba(245,158,11,0.15);color:#f59e0b;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.7rem;font-weight:700;">⏳ PENDING</span>',approved:'<span style="background:rgba(40,167,69,0.15);color:#28A745;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.7rem;font-weight:700;">✅ APPROVED</span>',rejected:'<span style="background:rgba(220,53,69,0.15);color:#dc3545;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.7rem;font-weight:700;">❌ REJECTED</span>'}[doc.status];
          var ext = (doc.path||'').split('.').pop().toLowerCase();
          var isImg = ['jpg','jpeg','png','webp','gif'].includes(ext);
          var imgPreview = (isImg && doc.path) ? '<img src="../../../' + doc.path + '" alt="' + esc(doc.label) + '" style="max-width:100%;max-height:160px;border-radius:8px;border:1px solid var(--fg-border);object-fit:contain;display:block;margin-top:0.5rem;" onerror="this.style.display=\'none\'">' : '';
          var pdfLink = (!isImg && doc.path) ? '<a href="../../../' + doc.path + '" target="_blank" style="font-size:0.78rem;color:var(--fg-primary);display:inline-flex;align-items:center;gap:0.3rem;margin-top:0.4rem;"><i class="bi bi-file-earmark-text"></i> View PDF</a>' : '';
          var rejReason = (doc.status==='rejected' && doc.rejection_reason) ? '<div style="background:rgba(220,53,69,0.08);border-left:3px solid #dc3545;border-radius:6px;padding:0.65rem;margin-top:0.5rem;"><div style="font-size:0.7rem;color:#dc3545;font-weight:700;text-transform:uppercase;margin-bottom:0.25rem;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:0.3rem;"></i>Rejection Reason</div><div style="font-size:0.8rem;color:var(--fg-text);">' + esc(doc.rejection_reason) + '</div></div>' : '';
          var resubmitBtn = doc.status==='rejected' ? '<button onclick="openResubmitModal(\'' + doc.type + '\',\'' + esc(doc.label) + '\',' + app.id + ')" style="display:inline-flex;align-items:center;gap:0.4rem;background:var(--fg-primary);color:#fff;padding:0.4rem 0.85rem;border-radius:8px;font-size:0.78rem;font-weight:600;border:none;cursor:pointer;margin-top:0.5rem;"><i class="bi bi-upload"></i> Resubmit Document</button>' : '';
          return '<div style="background:var(--fg-card-bg);border:1px solid ' + (doc.status==='rejected'?'rgba(220,53,69,0.3)':'var(--fg-border)') + ';border-radius:10px;padding:1rem;">'
            + '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;"><div style="display:flex;align-items:center;gap:0.75rem;">' + statusIcon + '<div><div style="font-weight:700;color:var(--fg-text);font-size:0.88rem;">' + esc(doc.label) + '</div><div style="font-size:0.75rem;color:var(--fg-muted);margin-top:0.15rem;">' + statusBadge + '</div></div></div></div>'
            + imgPreview + pdfLink + rejReason + resubmitBtn + '</div>';
        }).join('');
        document.getElementById('statusArea').innerHTML =
          '<div style="background:' + (hasRejected?'rgba(220,53,69,0.07)':'rgba(245,158,11,0.07)') + ';border:2px solid ' + (hasRejected?'rgba(220,53,69,0.3)':'rgba(245,158,11,0.3)') + ';border-radius:18px;padding:2rem;margin-bottom:1.75rem;">'
          + '<div style="text-align:center;margin-bottom:1.5rem;">'
          + '<div style="width:72px;height:72px;border-radius:50%;background:' + (hasRejected?'rgba(220,53,69,0.12)':'rgba(245,158,11,0.15)') + ';border:3px solid ' + (hasRejected?'#dc3545':'#f59e0b') + ';display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;"><i class="bi bi-' + (hasRejected?'exclamation-triangle':'hourglass-split') + '" style="font-size:2rem;color:' + (hasRejected?'#dc3545':'#f59e0b') + ';"></i></div>'
          + '<h3 style="font-size:1.3rem;font-weight:800;color:var(--fg-text);margin-bottom:0.5rem;">' + (hasRejected?'Document Resubmission Required':'Application Pending Review') + '</h3>'
          + '<p style="font-size:0.9rem;color:var(--fg-muted);margin-bottom:0;line-height:1.6;">' + (hasRejected?'Some documents were rejected. Review the reasons below and resubmit.':'Your <strong>' + roleLabel + '</strong> application was submitted on <strong>' + submitted + '</strong>. Our team will review within <strong>1–2 business days</strong>.') + '</p>'
          + '</div>'
          + (docCards ? '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;margin-bottom:1rem;">' + docCards + '</div>' : '')
          + '</div>';
      })
      .catch(function(){ document.getElementById('statusArea').innerHTML = '<div class="alert-bar alert-info"><i class="bi bi-hourglass-split"></i> Your application is under review. We\'ll notify you within 1–2 business days.</div>'; });
  }

  function renderRejectedUI(app, user) {
    var roleLabel = app.role === 'owner' ? 'Shop Owner' : 'Supplier';
    document.getElementById('statusArea').innerHTML =
      '<div style="background:rgba(220,53,69,0.07);border:2px solid rgba(220,53,69,0.3);border-radius:18px;padding:2rem;margin-bottom:1.75rem;text-align:center;">'
      + '<div style="width:72px;height:72px;border-radius:50%;background:rgba(220,53,69,0.12);border:3px solid #dc3545;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;"><i class="bi bi-x-lg" style="font-size:2rem;color:#dc3545;"></i></div>'
      + '<h3 style="font-size:1.3rem;font-weight:800;color:var(--fg-text);margin-bottom:0.5rem;">Application Not Approved</h3>'
      + '<p style="font-size:0.9rem;color:var(--fg-muted);margin-bottom:1rem;line-height:1.6;">Your <strong>' + roleLabel + '</strong> application was not approved at this time.</p>'
      + (app.admin_notes ? '<div style="background:rgba(220,53,69,0.07);border:1px solid rgba(220,53,69,0.2);border-radius:10px;padding:0.85rem 1.1rem;margin-bottom:1.25rem;text-align:left;font-size:0.83rem;"><i class="bi bi-info-circle-fill" style="color:#dc3545;margin-right:0.4rem;"></i><strong>Reason:</strong> ' + esc(app.admin_notes) + '</div>' : '')
      + '<button onclick="document.getElementById(\'statusArea\').innerHTML=\'\';document.getElementById(\'rolePickerSection\').style.display=\'block\';" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.5rem;border-radius:10px;background:var(--fg-primary);color:#fff;border:none;font-weight:700;font-size:0.88rem;cursor:pointer;"><i class="bi bi-arrow-repeat"></i> Apply Again</button>'
      + '</div>';
  }

  /* ── Resubmit modal ── */
  var currentResubmit = {type:'',label:'',appId:0};
  function openResubmitModal(docType, docLabel, appId) {
    currentResubmit = {type:docType, label:docLabel, appId:appId};
    document.getElementById('resubmitModalTitle').textContent = 'Resubmit ' + docLabel;
    document.getElementById('resubmitDocLabel').textContent   = docLabel;
    document.getElementById('resubmitFileInput').value = '';
    document.getElementById('resubmitFileName').style.display = 'none';
    document.getElementById('resubmitAlertBox').style.display = 'none';
    document.getElementById('resubmitModal').classList.add('open');
  }
  function closeResubmitModal() { document.getElementById('resubmitModal').classList.remove('open'); }
  function previewResubmitDoc(input) {
    var nameEl = document.getElementById('resubmitFileName');
    if (input.files && input.files[0]) {
      var f = input.files[0];
      if (f.size > 5*1024*1024) { showResubmitAlert('danger','File exceeds 5MB limit.'); input.value=''; return; }
      nameEl.textContent = '✓ ' + f.name; nameEl.style.display = 'block';
    } else { nameEl.style.display = 'none'; }
  }
  function submitResubmission() {
    var fileInput = document.getElementById('resubmitFileInput');
    if (!fileInput.files || !fileInput.files[0]) { showResubmitAlert('danger','Please select a file.'); return; }
    var btn = document.getElementById('btnResubmit');
    btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading…';
    var fd = new FormData();
    fd.append('action','resubmit_document');
    fd.append('application_id', currentResubmit.appId);
    fd.append('document_type',  currentResubmit.type);
    fd.append(currentResubmit.type + 'File', fileInput.files[0]);
    fetch('../../../backend/document_approvals.php', {method:'POST',body:fd,credentials:'include'})
      .then(function(r){return r.json();})
      .then(function(d) {
        if (!d.success) throw new Error(d.message || 'Resubmission failed.');
        showResubmitAlert('success', d.message || 'Document resubmitted successfully!');
        setTimeout(function() {
          closeResubmitModal();
          var user = FGAuth.UserStore.get();
          if (user) checkApplicationStatus(user);
        }, 1500);
      })
      .catch(function(err) { showResubmitAlert('danger', err.message || 'Failed to resubmit.'); })
      .finally(function() { btn.disabled=false; btn.innerHTML='<i class="bi bi-upload"></i> Upload & Resubmit'; });
  }
  function showResubmitAlert(type, msg) {
    var box = document.getElementById('resubmitAlertBox');
    box.style.display = 'flex'; box.className = 'alert-bar alert-' + type;
    box.innerHTML = '<i class="bi bi-' + (type==='success'?'check-circle-fill':'exclamation-triangle-fill') + '"></i> ' + msg;
  }

  /* ── DOMContentLoaded ── */
  document.addEventListener('DOMContentLoaded', function() {
    var user = FGAuth.UserStore.get();
    if (!user || user.role !== 'customer') { window.location.href = '../../../login.html'; return; }

    var fullName = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email;
    document.getElementById('sidebarName').textContent = fullName || user.email;
    var initials = ((user.firstName||'')[0]||'') + ((user.lastName||'')[0]||'');
    (function renderAvatar(url) {
      var el = document.getElementById('sidebarAvatarInitials');
      if (!el) return;
      if (url) el.innerHTML = '<img src="' + url + '" alt="avatar" onerror="this.parentElement.textContent=\'' + initials.toUpperCase() + '\'">';
      else el.textContent = initials.toUpperCase() || '?';
    })(user.avatar_url || null);

    fetch('../../../backend/session-user.php',{credentials:'include'}).then(function(r){return r.json();}).then(function(d){
      if (d.loggedIn && d.user) { FGAuth.UserStore.save(d.user); var el=document.getElementById('sidebarAvatarInitials'); if(el&&d.user.avatar_url) el.innerHTML='<img src="'+d.user.avatar_url+'" alt="avatar" onerror="this.parentElement.textContent=\''+initials.toUpperCase()+'\'">'; }
    }).catch(function(){});

    // Pre-fill name
    document.getElementById('sellerFirstName').value = user.firstName || '';
    document.getElementById('sellerLastName').value  = user.lastName  || '';

    // Sidebar toggle
    var sidebar = document.getElementById('cuSidebar'), overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', function(){ sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', function(){ sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    initDropzones();
    checkApplicationStatus(user);
  });
  </script>

  <!-- Resubmit Document Modal -->
  <div class="modal-overlay" id="resubmitModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;box-shadow:0 24px 64px rgba(0,0,0,0.4);width:100%;max-width:480px;max-height:90vh;overflow-y:auto;">
      <div style="padding:1.5rem 1.75rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;">
        <h5 id="resubmitModalTitle" style="margin:0;font-weight:800;font-size:1.1rem;color:var(--fg-text);"><i class="bi bi-upload" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Resubmit Document</h5>
        <button onclick="closeResubmitModal()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:1rem;"><i class="bi bi-x-lg"></i></button>
      </div>
      <div style="padding:1.5rem 1.75rem;">
        <div id="resubmitAlertBox" style="display:none;"></div>
        <p style="font-size:0.85rem;color:var(--fg-muted);margin-bottom:1rem;">Upload a new version of <strong id="resubmitDocLabel"></strong> to replace the rejected document.</p>
        <div class="form-group">
          <label>Select New Document <span style="color:#dc3545;">*</span></label>
          <div class="doc-dropzone" onclick="document.getElementById('resubmitFileInput').click()">
            <div class="doc-dropzone-icon"><i class="bi bi-cloud-upload"></i></div>
            <div class="doc-dropzone-label">Click to select file</div>
            <div class="doc-dropzone-sub">JPG, PNG, or PDF · Max 5MB</div>
            <div id="resubmitFileName" style="display:none;margin-top:0.5rem;font-size:0.82rem;color:#28A745;font-weight:600;"></div>
          </div>
          <input type="file" id="resubmitFileInput" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" onchange="previewResubmitDoc(this)">
        </div>
        <div style="background:rgba(230,168,0,0.08);border:1px solid rgba(230,168,0,0.25);border-radius:10px;padding:0.85rem;font-size:0.78rem;color:var(--fg-muted);line-height:1.6;">
          <i class="bi bi-info-circle" style="color:var(--fg-primary);margin-right:0.4rem;"></i>
          After resubmission, your document will be reviewed within 1–2 business days.
        </div>
      </div>
      <div style="padding:1.25rem 1.75rem;border-top:1px solid var(--fg-border);display:flex;gap:0.75rem;justify-content:flex-end;">
        <button onclick="closeResubmitModal()" style="padding:0.55rem 1.1rem;border-radius:9px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-weight:600;cursor:pointer;">Cancel</button>
        <button id="btnResubmit" onclick="submitResubmission()" class="btn-submit" style="padding:0.55rem 1.25rem;">
          <i class="bi bi-upload"></i> Upload &amp; Resubmit
        </button>
      </div>
    </div>
  </div>

  <script>
  // Make modal-overlay open/close work
  document.getElementById('resubmitModal').addEventListener('click', function(e){ if(e.target===this) closeResubmitModal(); });
  document.getElementById('resubmitModal').style.cssText += ';display:none;';
  // Override classList.add/remove for the resubmit modal since it uses inline style
  var _origAdd = document.getElementById('resubmitModal').classList.add.bind(document.getElementById('resubmitModal').classList);
  var _origRem = document.getElementById('resubmitModal').classList.remove.bind(document.getElementById('resubmitModal').classList);
  document.getElementById('resubmitModal').classList.add = function(cls) {
    if (cls === 'open') this.style.display = 'flex';
    _origAdd(cls);
  };
  document.getElementById('resubmitModal').classList.remove = function(cls) {
    if (cls === 'open') this.style.display = 'none';
    _origRem(cls);
  };
  </script>

</body>
</html>




