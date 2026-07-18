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
  <title>Fix&amp;Go — Become a Technician</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8.1" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5.1" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
    }
    /* ── Wizard stepper ── */
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
    /* ── Form elements ── */
    .form-section{margin-bottom:1.75rem;}
    .form-section-title{font-size:0.95rem;font-weight:800;color:var(--fg-text);margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;gap:0.5rem;}
    .form-group{margin-bottom:1rem;}
    .form-group label{display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.4rem;}
    .form-group label span{color:#dc3545;margin-left:2px;}
    .form-input{width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;font-family:inherit;}
    .form-input:focus{border-color:var(--fg-primary);box-shadow:0 0 0 3px rgba(230,168,0,0.15);}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
    .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;}
    @media(max-width:640px){.form-row,.form-row-3{grid-template-columns:1fr;}}
    /* ── Radio entity type ── */
    .entity-radios{display:flex;flex-wrap:wrap;gap:0.75rem;margin-bottom:1rem;}
    .entity-radio{display:flex;align-items:center;gap:0.5rem;padding:0.55rem 1rem;border:1.5px solid var(--fg-border);border-radius:10px;cursor:pointer;font-size:0.85rem;font-weight:600;color:var(--fg-muted);transition:all 0.2s;}
    .entity-radio input{display:none;}
    .entity-radio.selected{border-color:var(--fg-primary);color:var(--fg-primary);background:rgba(230,168,0,0.07);}
    /* ── Specialization checkboxes ── */
    .spec-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:0.6rem;}
    .spec-chip{display:flex;align-items:center;gap:0.5rem;padding:0.5rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:8px;cursor:pointer;font-size:0.82rem;font-weight:600;color:var(--fg-muted);transition:all 0.2s;user-select:none;background:var(--fg-bg);}
    .spec-chip:hover{border-color:var(--fg-primary);color:var(--fg-primary);}
    .spec-chip.checked{border-color:var(--fg-primary);color:var(--fg-primary);background:rgba(230,168,0,0.08);}
    .spec-chip i{font-size:0.9rem;pointer-events:none;}
    /* ── Doc upload ── */
    .doc-dropzone{border:2px dashed var(--fg-border);border-radius:10px;padding:1rem;text-align:center;cursor:pointer;transition:border-color 0.2s,background 0.2s;background:var(--fg-bg);}
    .doc-dropzone:hover{border-color:var(--fg-primary);background:rgba(230,168,0,0.04);}
    .doc-dropzone.has-file{border-color:#28A745;background:rgba(40,167,69,0.04);}
    .doc-dropzone-icon{font-size:1.5rem;color:var(--fg-muted);margin-bottom:0.35rem;}
    .doc-dropzone-label{font-size:0.8rem;font-weight:700;color:var(--fg-text);}
    .doc-dropzone-sub{font-size:0.72rem;color:var(--fg-muted);}
    /* ── Address modal ── */
    .addr-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center;padding:1rem;}
    .addr-modal-overlay.open{display:flex;}
    .addr-modal-box{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;box-shadow:0 24px 64px rgba(0,0,0,0.4);width:100%;max-width:560px;max-height:92vh;overflow-y:auto;animation:modalIn 0.25s cubic-bezier(0.16,1,0.3,1);}
    @keyframes modalIn{from{opacity:0;transform:scale(0.95) translateY(10px);}to{opacity:1;transform:scale(1) translateY(0);}}
    .addr-modal-head{padding:1.25rem 1.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;}
    .addr-modal-head h5{margin:0;font-weight:800;font-size:1.05rem;color:var(--fg-text);}
    .addr-modal-body{padding:1.5rem;}
    .addr-modal-foot{padding:1rem 1.5rem;border-top:1px solid var(--fg-border);display:flex;gap:0.75rem;justify-content:flex-end;}
    #mapContainer{width:100%;height:260px;border-radius:10px;overflow:hidden;border:1px solid var(--fg-border);margin-top:1rem;}
    /* ── Address display card ── */
    .addr-display{background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:10px;padding:0.85rem 1rem;font-size:0.85rem;color:var(--fg-text);line-height:1.6;}
    .addr-display .addr-name{font-weight:700;margin-bottom:0.15rem;}
    .addr-display .addr-edit-link{color:var(--fg-primary);font-weight:700;cursor:pointer;font-size:0.8rem;text-decoration:none;display:inline-block;margin-top:0.35rem;}
    /* ── Review panel ── */
    .review-section{background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:12px;padding:1.25rem;margin-bottom:1.25rem;}
    .review-section-title{font-size:0.82rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:var(--fg-muted);margin-bottom:0.85rem;}
    .review-row{display:flex;gap:1rem;margin-bottom:0.55rem;font-size:0.85rem;}
    .review-label{font-weight:700;color:var(--fg-text);min-width:160px;flex-shrink:0;}
    .review-value{color:var(--fg-muted);}
    .doc-badge{display:inline-flex;align-items:center;gap:0.35rem;font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:6px;}
    .doc-badge.ok{background:rgba(40,167,69,0.12);color:#28A745;}
    .doc-badge.na{background:rgba(107,114,128,0.1);color:var(--fg-muted);}
    /* ── Buttons ── */
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
    /* ── Leave page modal ── */
    .leave-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);z-index:9999;display:none;align-items:center;justify-content:center;padding:1rem;}
    .leave-modal-overlay.open{display:flex;}
    .leave-modal-box{background:#fff;border-radius:16px;box-shadow:0 24px 64px rgba(0,0,0,0.3);width:100%;max-width:420px;padding:2rem;position:relative;animation:modalIn 0.2s cubic-bezier(0.16,1,0.3,1);}
    [data-theme="dark"] .leave-modal-box{background:#1e1e1e;color:#fff;}
    .leave-modal-close{position:absolute;top:1rem;right:1rem;width:28px;height:28px;border-radius:6px;border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:1rem;}
    .leave-modal-close:hover{color:#333;}
    [data-theme="dark"] .leave-modal-close:hover{color:#fff;}
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge customer">👤 Customer</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="/index.php?browse=1" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;">
        <i class="bi bi-shop"></i> Browse Shop
      </a>
      <button id="logoutBtn" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;font-weight:600;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
    </div>
  </nav>

  <div class="sidebar-overlay-bg" id="sidebarOverlay"></div>

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
        <li><a href="orders.php"><i class="bi bi-bag-heart-fill"></i> My Purchases</a></li>
        <li><a href="repairs.php"><i class="bi bi-tools"></i> My Repairs</a></li>
        <li><a href="wishlist.php"><i class="bi bi-heart-fill"></i> Wishlist</a></li>
        <li><a href="vouchers.php"><i class="bi bi-ticket-perforated-fill"></i> My Vouchers</a></li>
      </ul>
      <div class="sidebar-section-label">Fix&amp;Go</div>
      <ul class="sidebar-nav">
        <li><a href="seller-centre.php"><i class="bi bi-shop-window"></i> Seller Centre</a></li>
        <li><a href="become-technician.php" class="active"><i class="bi bi-wrench-adjustable-circle-fill"></i> Become a Technician</a></li>
      </ul>
    </aside>

    <!-- Main content -->
    <main class="cu-main">
      <div class="page-header">
        <h2><i class="bi bi-wrench-adjustable-circle-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Become a Technician</h2>
        <p>Apply to join Fix&amp;Go as a certified phone repair technician</p>
      </div>

      <div id="alertBox" style="display:none;margin-bottom:1rem;"></div>
      <div id="statusArea"></div>

      <!-- Wizard -->
      <div class="wizard-wrap" id="wizardWrap">
        <!-- Stepper -->
        <div class="wizard-stepper">
          <div class="wz-step active" id="step-dot-1">
            <div class="wz-dot" id="dot1">1</div>
            <span class="wz-label">Technician Info</span>
          </div>
          <div class="wz-step" id="step-dot-2">
            <div class="wz-dot" id="dot2">2</div>
            <span class="wz-label">Business Info</span>
          </div>
          <div class="wz-step" id="step-dot-3">
            <div class="wz-dot" id="dot3">3</div>
            <span class="wz-label">Submit</span>
          </div>
        </div>

        <!-- Step 1: Technician Info -->
        <div class="wizard-body">
          <div class="wz-panel active" id="panel1">
            <!-- Shop / Technician Name -->
            <div class="form-section">
              <div class="form-section-title"><i class="bi bi-person-badge-fill" style="color:var(--fg-primary);"></i> Personal Information</div>
              <div class="form-row">
                <div class="form-group">
                  <label>Last Name <span>*</span></label>
                  <input type="text" class="form-input" id="lastName" placeholder="e.g. Santos" maxlength="80">
                </div>
                <div class="form-group">
                  <label>First Name <span>*</span></label>
                  <input type="text" class="form-input" id="firstName" placeholder="e.g. Juan" maxlength="80">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Middle Name</label>
                  <input type="text" class="form-input" id="middleName" placeholder="Optional" maxlength="80">
                </div>
                <div class="form-group">
                  <label>Suffix</label>
                  <select class="form-input" id="suffix">
                    <option value="">Select</option>
                    <option>Jr.</option><option>Sr.</option><option>II</option><option>III</option><option>IV</option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Account Email <span>*</span></label>
                  <input type="email" class="form-input" id="techEmail" placeholder="New account email">
                </div>
                <div class="form-group">
                  <label>Experience (years) <span>*</span></label>
                  <input type="number" class="form-input" id="experienceYrs" min="0" max="50" value="1">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Password <span>*</span></label>
                  <input type="password" class="form-input" id="techPassword" placeholder="Min. 8 characters">
                </div>
                <div class="form-group">
                  <label>Confirm Password <span>*</span></label>
                  <input type="password" class="form-input" id="techConfirmPassword" placeholder="Repeat password">
                </div>
              </div>
            </div>

            <!-- Specializations -->
            <div class="form-section">
              <div class="form-section-title"><i class="bi bi-tools" style="color:var(--fg-primary);"></i> Specializations <span style="color:#dc3545;font-size:0.75rem;font-weight:600;margin-left:0.25rem;">* select at least one</span></div>
              <div class="spec-grid" id="specGrid">
                <div class="spec-chip" data-value="Screen Repair"><i class="bi bi-phone"></i> Screen Repair</div>
                <div class="spec-chip" data-value="Battery Replacement"><i class="bi bi-battery-charging"></i> Battery Replacement</div>
                <div class="spec-chip" data-value="Water Damage"><i class="bi bi-droplet-fill"></i> Water Damage</div>
                <div class="spec-chip" data-value="Charging Port Repair"><i class="bi bi-plug-fill"></i> Charging Port</div>
                <div class="spec-chip" data-value="Software Troubleshooting"><i class="bi bi-code-slash"></i> Software</div>
                <div class="spec-chip" data-value="Camera Repair"><i class="bi bi-camera-fill"></i> Camera Repair</div>
                <div class="spec-chip" data-value="Speaker/Mic Repair"><i class="bi bi-speaker-fill"></i> Speaker/Mic</div>
                <div class="spec-chip" data-value="Motherboard Repair"><i class="bi bi-cpu-fill"></i> Motherboard</div>
                <div class="spec-chip" data-value="General Repair"><i class="bi bi-wrench-adjustable"></i> General Repair</div>
              </div>
            </div>

            <!-- Documents -->
            <div class="form-section">
              <div class="form-section-title"><i class="bi bi-file-earmark-text-fill" style="color:var(--fg-primary);"></i> Documents</div>
              <div class="form-row">
                <div class="form-group">
                  <label>Government ID <span>*</span></label>
                  <div class="doc-dropzone" id="dz-govId" onclick="document.getElementById('govIdFile').click()">
                    <div class="doc-dropzone-icon"><i class="bi bi-card-image"></i></div>
                    <div class="doc-dropzone-label">Upload Government ID</div>
                    <div class="doc-dropzone-sub">JPG, PNG, PDF · Max 5MB</div>
                  </div>
                  <input type="file" id="govIdFile" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none">
                </div>
                <div class="form-group">
                  <label>Certification / Training Certificate</label>
                  <div class="doc-dropzone" id="dz-cert" onclick="document.getElementById('certFile').click()">
                    <div class="doc-dropzone-icon"><i class="bi bi-award-fill"></i></div>
                    <div class="doc-dropzone-label">Upload Certificate</div>
                    <div class="doc-dropzone-sub">JPG, PNG, PDF · Max 5MB · Optional</div>
                  </div>
                  <input type="file" id="certFile" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none">
                </div>
              </div>
            </div>
          </div><!-- /panel1 -->

          <!-- Step 2: Business Information -->
          <div class="wz-panel" id="panel2">
            <div class="alert-bar alert-info" style="margin-bottom:1.5rem;">
              <i class="bi bi-info-circle-fill"></i>
              This information will be used to verify your compliance and for invoicing purposes. Please provide accurate information.
            </div>

            <!-- Entity Information -->
            <div class="form-section">
              <div class="form-section-title"><i class="bi bi-building" style="color:var(--fg-primary);"></i> Entity Information</div>

              <div class="form-group">
                <label>Technician Type <span>*</span></label>
                <div class="entity-radios" id="entityRadios">
                  <label class="entity-radio selected" data-val="sole_proprietorship">
                    <input type="radio" name="entityType" value="sole_proprietorship" checked>
                    <i class="bi bi-person-fill"></i> Sole Proprietorship
                  </label>
                  <label class="entity-radio" data-val="corporation">
                    <input type="radio" name="entityType" value="corporation">
                    <i class="bi bi-building"></i> Corporation / Partnership
                  </label>
                  <label class="entity-radio" data-val="one_person_corp">
                    <input type="radio" name="entityType" value="one_person_corp">
                    <i class="bi bi-person-badge"></i> One Person Corporation
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label>Business Name / Trade Name <span>*</span></label>
                <input type="text" class="form-input" id="businessName" placeholder="e.g. Juan's Phone Repair" maxlength="255">
                <div style="font-size:0.75rem;color:var(--fg-muted);margin-top:0.3rem;">If not applicable, enter your full name as on your government-issued ID.</div>
              </div>

              <div class="form-group">
                <label>General Location <span>*</span></label>
                <div class="form-row" style="margin-bottom:0.6rem;">
                  <div class="form-group" style="margin-bottom:0;">
                    <label style="font-size:0.75rem;color:var(--fg-muted);font-weight:600;">Region</label>
                    <select class="form-input" id="locRegion" onchange="onRegionChange()">
                      <option value="">Select Region</option>
                      <option value="NCR">NCR – National Capital Region</option>
                      <option value="CAR">CAR – Cordillera Administrative Region</option>
                      <option value="I">Region I – Ilocos Region</option>
                      <option value="II">Region II – Cagayan Valley</option>
                      <option value="III">Region III – Central Luzon</option>
                      <option value="IVA">Region IV-A – CALABARZON</option>
                      <option value="IVB">Region IV-B – MIMAROPA</option>
                      <option value="V">Region V – Bicol Region</option>
                      <option value="VI">Region VI – Western Visayas</option>
                      <option value="VII">Region VII – Central Visayas</option>
                      <option value="VIII">Region VIII – Eastern Visayas</option>
                      <option value="IX">Region IX – Zamboanga Peninsula</option>
                      <option value="X">Region X – Northern Mindanao</option>
                      <option value="XI">Region XI – Davao Region</option>
                      <option value="XII">Region XII – SOCCSKSARGEN</option>
                      <option value="XIII">Region XIII – Caraga</option>
                      <option value="BARMM">BARMM – Bangsamoro</option>
                    </select>
                  </div>
                  <div class="form-group" style="margin-bottom:0;">
                    <label style="font-size:0.75rem;color:var(--fg-muted);font-weight:600;">Province</label>
                    <select class="form-input" id="locProvince" onchange="onProvinceChange()" disabled>
                      <option value="">Select Province</option>
                    </select>
                  </div>
                </div>
                <div class="form-row" style="margin-bottom:0;">
                  <div class="form-group" style="margin-bottom:0;">
                    <label style="font-size:0.75rem;color:var(--fg-muted);font-weight:600;">City / Municipality</label>
                    <select class="form-input" id="locCity" onchange="onCityChange()" disabled>
                      <option value="">Select City / Municipality</option>
                    </select>
                  </div>
                  <div class="form-group" style="margin-bottom:0;">
                    <label style="font-size:0.75rem;color:var(--fg-muted);font-weight:600;">Barangay</label>
                    <select class="form-input" id="locBarangay" onchange="onBarangayChange()" disabled>
                      <option value="">Select Barangay</option>
                    </select>
                  </div>
                </div>
                <input type="hidden" id="generalLocation">
              </div>

              <div class="form-group">
                <label>ZIP Code <span>*</span></label>
                <input type="text" class="form-input" id="zipCode" placeholder="e.g. 8000" maxlength="10">
              </div>

              <!-- Business / Pickup Address with map -->
              <div class="form-group">
                <label>Business Address / Service Area <span>*</span></label>
                <div id="addrDisplay" class="addr-display" style="display:none;">
                  <div class="addr-name" id="addrDisplayName"></div>
                  <div id="addrDisplayText" style="color:var(--fg-muted);font-size:0.82rem;"></div>
                  <a class="addr-edit-link" onclick="openAddrModal()"><i class="bi bi-pencil-fill" style="font-size:0.7rem;"></i> Edit</a>
                </div>
                <button type="button" id="addrAddBtn" onclick="openAddrModal()"
                  style="display:flex;align-items:center;gap:0.5rem;padding:0.65rem 1.1rem;border:1.5px dashed var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-primary);font-weight:700;font-size:0.85rem;cursor:pointer;width:100%;transition:all 0.2s;"
                  onmouseenter="this.style.borderColor='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)'">
                  <i class="bi bi-plus-circle-fill"></i> Add Business Address
                </button>
                <input type="hidden" id="shopAddress">
                <input type="hidden" id="addressLat">
                <input type="hidden" id="addressLng">
              </div>

              <div class="form-group">
                <label>Business Email</label>
                <input type="email" class="form-input" id="businessEmail" placeholder="Optional — for business correspondence">
              </div>
            </div>

            <!-- Business Documents -->
            <div class="form-section">
              <div class="form-section-title"><i class="bi bi-file-earmark-check-fill" style="color:var(--fg-primary);"></i> Business Documents</div>
              <div class="form-row">
                <div class="form-group">
                  <label>DTI / SEC Registration</label>
                  <div class="doc-dropzone" id="dz-dti" onclick="document.getElementById('dtiFile').click()">
                    <div class="doc-dropzone-icon"><i class="bi bi-file-earmark-text"></i></div>
                    <div class="doc-dropzone-label">Upload DTI / SEC</div>
                    <div class="doc-dropzone-sub">JPG, PNG, PDF · Max 5MB · Optional</div>
                  </div>
                  <input type="file" id="dtiFile" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none">
                </div>
                <div class="form-group">
                  <label>BIR Certificate of Registration</label>
                  <div class="doc-dropzone" id="dz-bir" onclick="document.getElementById('birFile').click()">
                    <div class="doc-dropzone-icon"><i class="bi bi-receipt"></i></div>
                    <div class="doc-dropzone-label">Upload BIR Certificate</div>
                    <div class="doc-dropzone-sub">JPG, PNG, PDF · Max 5MB · Optional</div>
                  </div>
                  <input type="file" id="birFile" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none">
                </div>
              </div>
            </div>
          </div><!-- /panel2 -->

          <!-- Step 3: Review & Submit -->
          <div class="wz-panel" id="panel3">
            <div class="alert-bar alert-info" style="margin-bottom:1.5rem;">
              <i class="bi bi-eye-fill"></i>
              Please review your information before submitting. You can go back to make changes.
            </div>

            <!-- Review: Technician Info -->
            <div class="review-section">
              <div class="review-section-title">Technician Information</div>
              <div class="review-row"><span class="review-label">Full Name</span><span class="review-value" id="rv-name">—</span></div>
              <div class="review-row"><span class="review-label">Account Email</span><span class="review-value" id="rv-email">—</span></div>
              <div class="review-row"><span class="review-label">Experience</span><span class="review-value" id="rv-exp">—</span></div>
              <div class="review-row"><span class="review-label">Specializations</span><span class="review-value" id="rv-specs">—</span></div>
            </div>

            <!-- Review: Business Info -->
            <div class="review-section">
              <div class="review-section-title">Business Information</div>
              <div class="review-row"><span class="review-label">Entity Type</span><span class="review-value" id="rv-entity">—</span></div>
              <div class="review-row"><span class="review-label">Business Name</span><span class="review-value" id="rv-bizname">—</span></div>
              <div class="review-row"><span class="review-label">General Location</span><span class="review-value" id="rv-location">—</span></div>
              <div class="review-row"><span class="review-label">Business Address</span><span class="review-value" id="rv-address">—</span></div>
              <div class="review-row"><span class="review-label">ZIP Code</span><span class="review-value" id="rv-zip">—</span></div>
              <div class="review-row"><span class="review-label">Business Email</span><span class="review-value" id="rv-bizemail">—</span></div>
            </div>

            <!-- Review: Documents -->
            <div class="review-section">
              <div class="review-section-title">Documents Submitted</div>
              <div class="review-row" style="flex-direction:column;gap:0.4rem;"><span class="review-label">Government ID</span><span class="review-value" id="rv-govid">—</span></div>
              <div class="review-row" style="flex-direction:column;gap:0.4rem;"><span class="review-label">Certification</span><span class="review-value" id="rv-cert">—</span></div>
              <div class="review-row" style="flex-direction:column;gap:0.4rem;"><span class="review-label">DTI / SEC</span><span class="review-value" id="rv-dti">—</span></div>
              <div class="review-row" style="flex-direction:column;gap:0.4rem;"><span class="review-label">BIR Certificate</span><span class="review-value" id="rv-bir">—</span></div>
            </div>

            <div class="alert-bar alert-info">
              <i class="bi bi-shield-check-fill"></i>
              By submitting, you confirm that all information provided is accurate and that you agree to Fix&amp;Go's Terms of Service and Technician Guidelines.
            </div>
          </div><!-- /panel3 -->
        </div><!-- /wizard-body -->

        <!-- Wizard footer -->
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
      </div><!-- /wizard-wrap -->
    </main>
  </div>

  <!-- Address Modal -->
  <div class="addr-modal-overlay" id="addrModal">
    <div class="addr-modal-box">
      <div class="addr-modal-head">
        <h5 id="addrModalTitle"><i class="bi bi-geo-alt-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Add Business Address</h5>
        <button onclick="closeAddrModal()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:1rem;">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="addr-modal-body">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" class="form-input" id="addrFullName" placeholder="Your full name">
        </div>

        <!-- Cascading location selects -->
        <div class="form-group">
          <label>Region <span>*</span></label>
          <select class="form-input" id="addrRegion" onchange="addrOnRegion()">
            <option value="">Select Region</option>
            <option value="NCR">NCR – National Capital Region</option>
            <option value="CAR">CAR – Cordillera Administrative Region</option>
            <option value="I">Region I – Ilocos Region</option>
            <option value="II">Region II – Cagayan Valley</option>
            <option value="III">Region III – Central Luzon</option>
            <option value="IVA">Region IV-A – CALABARZON</option>
            <option value="IVB">Region IV-B – MIMAROPA</option>
            <option value="V">Region V – Bicol Region</option>
            <option value="VI">Region VI – Western Visayas</option>
            <option value="VII">Region VII – Central Visayas</option>
            <option value="VIII">Region VIII – Eastern Visayas</option>
            <option value="IX">Region IX – Zamboanga Peninsula</option>
            <option value="X">Region X – Northern Mindanao</option>
            <option value="XI">Region XI – Davao Region</option>
            <option value="XII">Region XII – SOCCSKSARGEN</option>
            <option value="XIII">Region XIII – Caraga</option>
            <option value="BARMM">BARMM – Bangsamoro</option>
          </select>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Province <span>*</span></label>
            <select class="form-input" id="addrProvince" onchange="addrOnProvince()" disabled>
              <option value="">Select Province</option>
            </select>
          </div>
          <div class="form-group">
            <label>City / Municipality <span>*</span></label>
            <select class="form-input" id="addrCity" onchange="addrOnCity()" disabled>
              <option value="">Select City / Municipality</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Barangay <span>*</span></label>
            <select class="form-input" id="addrBarangay" onchange="addrOnBarangay()" disabled>
              <option value="">Select Barangay</option>
            </select>
          </div>
          <div class="form-group">
            <label>Postal Code</label>
            <input type="text" class="form-input" id="addrPostal" placeholder="e.g. 8000" maxlength="10">
          </div>
        </div>

        <div class="form-group">
          <label>Detail Address <span>*</span></label>
          <textarea class="form-input" id="addrDetail" rows="2" placeholder="Street No., Purok, Building, Landmark…" style="resize:vertical;" oninput="updateMapFromDetail()"></textarea>
        </div>

        <!-- Leaflet / OpenStreetMap -->
        <div id="mapContainer" style="width:100%;height:260px;border-radius:10px;overflow:hidden;border:1px solid var(--fg-border);margin-top:0.5rem;"></div>
      </div>
      <div class="addr-modal-foot">
        <button onclick="closeAddrModal()" style="padding:0.6rem 1.5rem;border-radius:10px;background:transparent;border:1.5px solid var(--fg-border);color:var(--fg-muted);font-weight:700;font-size:0.88rem;cursor:pointer;">Cancel</button>
        <button onclick="saveAddress()" style="padding:0.6rem 1.75rem;border-radius:10px;background:#dc3545;color:#fff;border:none;font-weight:700;font-size:0.88rem;cursor:pointer;">Save</button>
      </div>
    </div>
  </div>

  <!-- Leave Page Modal -->
  <div class="leave-modal-overlay" id="leaveModal">
    <div class="leave-modal-box">
      <button class="leave-modal-close" onclick="cancelLeave()"><i class="bi bi-x-lg"></i></button>
      <h5 style="font-size:1.2rem;font-weight:800;margin-bottom:0.75rem;color:inherit;">Leave this page?</h5>
      <p style="font-size:0.9rem;color:#666;margin-bottom:1.75rem;line-height:1.6;">Please confirm that your information has been saved, unsaved information will be lost.</p>
      <div style="display:flex;gap:0.75rem;justify-content:flex-end;">
        <button onclick="cancelLeave()" style="padding:0.6rem 1.5rem;border-radius:10px;background:#fff;border:1.5px solid #ddd;font-weight:700;font-size:0.9rem;cursor:pointer;color:#333;">Cancel</button>
        <button onclick="confirmLeave()" style="padding:0.6rem 1.75rem;border-radius:10px;background:#dc3545;color:#fff;border:none;font-weight:700;font-size:0.9rem;cursor:pointer;">Leave</button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="/assets/js/ph-location.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script>
  // ── Auth & sidebar ────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
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
    document.getElementById('sidebarToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    // Pre-fill name from customer account
    document.getElementById('firstName').value = user.firstName || '';
    document.getElementById('lastName').value  = user.lastName  || '';
    document.getElementById('addrFullName').value = fullName;

    // Init spec chips — plain div chips, no nested input
    document.querySelectorAll('#specGrid .spec-chip').forEach(function(chip) {
      chip.addEventListener('click', function() {
        chip.classList.toggle('checked');
        markDirty();
      });
    });

    // Init entity radios
    document.querySelectorAll('.entity-radio').forEach(label => {
      label.addEventListener('click', () => {
        document.querySelectorAll('.entity-radio').forEach(l => l.classList.remove('selected'));
        label.classList.add('selected');
        markDirty();
      });
    });

    // Mark dirty on any input change
    document.querySelectorAll('.form-input').forEach(el => {
      el.addEventListener('input', markDirty);
      el.addEventListener('change', markDirty);
    });

    // Init doc dropzones
    ['govIdFile','certFile','dtiFile','birFile'].forEach(id => {
      const input = document.getElementById(id);
      const dzId  = { govIdFile:'dz-govId', certFile:'dz-cert', dtiFile:'dz-dti', birFile:'dz-bir' }[id];
      const dz    = document.getElementById(dzId);
      if (!input || !dz) return;
      input.addEventListener('change', function() {
        if (this.files[0]) {
          dz.classList.add('has-file');
          dz.querySelector('.doc-dropzone-label').textContent = this.files[0].name;
          dz.querySelector('.doc-dropzone-sub').textContent   = (this.files[0].size/1024/1024).toFixed(2) + ' MB';
          dz.querySelector('.doc-dropzone-icon').innerHTML    = '<i class="bi bi-check-circle-fill" style="color:#28A745;"></i>';
          markDirty();
        }
      });
    });

    // Check existing application
    checkExistingApplication();

    // ── Leave-page guard: intercept all sidebar/nav links ──────────────────
    document.querySelectorAll('a[href]').forEach(function(link) {
      var href = link.getAttribute('href');
      if (!href || href.startsWith('#') || href.startsWith('javascript')) return;
      link.addEventListener('click', function(e) {
        if (!formIsDirty) return;
        e.preventDefault();
        pendingNavUrl = href;
        document.getElementById('leaveModal').classList.add('open');
      });
    });

    // Native browser back/forward/close guard
    window.addEventListener('beforeunload', function(e) {
      if (formIsDirty) { e.preventDefault(); e.returnValue = ''; }
    });
  });

  // ── Dirty-form / leave-page guard ────────────────────────────────────────
  var formIsDirty  = false;
  var pendingNavUrl = null;

  function markDirty() { formIsDirty = true; }
  function clearDirty() { formIsDirty = false; }

  function cancelLeave() {
    pendingNavUrl = null;
    document.getElementById('leaveModal').classList.remove('open');
  }

  function confirmLeave() {
    clearDirty();
    document.getElementById('leaveModal').classList.remove('open');
    if (pendingNavUrl) window.location.href = pendingNavUrl;
  }

  // ── Check existing application ────────────────────────────────────────────
  function checkExistingApplication() {
    const user = FGAuth.UserStore.get();
    fetch('../../../backend/check_application.php?customer_id=' + (user?.id||0) + '&role=phone_technician', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.application) return; // no application — show wizard
        const app = d.application;
        document.getElementById('wizardWrap').style.display = 'none';

        if (app.status === 'pending' || app.status === 'rejected') {
          // Fetch document statuses to show images
          fetch('/api/document-approvals?action=my_documents&customer_id=' + (user?.id||0), { credentials: 'include' })
            .then(r => r.json())
            .then(docData => {
              renderTechnicianStatus(app, docData.success ? docData.documents : []);
            })
            .catch(() => renderTechnicianStatus(app, []));
        } else {
          renderTechnicianStatus(app, []);
        }
      }).catch(() => {});
  }

  function renderTechnicianStatus(app, docs) {
    const statusColors = { pending:'#e6a800', approved:'#28A745', rejected:'#dc3545' };
    const statusIcons  = { pending:'bi-hourglass-split', approved:'bi-check-circle-fill', rejected:'bi-x-circle-fill' };
    const color = statusColors[app.status] || '#aaa';
    const icon  = statusIcons[app.status]  || 'bi-info-circle';
    const submitted = app.submitted_at ? new Date(app.submitted_at).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}) : '—';

    // Build document cards with image previews
    const docCards = docs.filter(d => d.path).map(doc => {
      const ext = (doc.path||'').split('.').pop().toLowerCase();
      const isImg = ['jpg','jpeg','png','webp','gif'].includes(ext);
      const statusBadge = {
        pending:  '<span style="background:rgba(245,158,11,0.15);color:#f59e0b;padding:0.15rem 0.5rem;border-radius:20px;font-size:0.68rem;font-weight:700;">⏳ PENDING</span>',
        approved: '<span style="background:rgba(40,167,69,0.15);color:#28A745;padding:0.15rem 0.5rem;border-radius:20px;font-size:0.68rem;font-weight:700;">✅ APPROVED</span>',
        rejected: '<span style="background:rgba(220,53,69,0.15);color:#dc3545;padding:0.15rem 0.5rem;border-radius:20px;font-size:0.68rem;font-weight:700;">❌ REJECTED</span>',
      }[doc.status] || '';
      const imgTag = isImg ? `<img src="../../../${doc.path}" alt="${esc(doc.label)}" style="max-width:100%;max-height:140px;border-radius:8px;border:1px solid var(--fg-border);object-fit:contain;display:block;margin-top:0.5rem;" onerror="this.style.display='none'">` : '';
      const pdfLink = !isImg ? `<a href="../../../${doc.path}" target="_blank" style="font-size:0.75rem;color:var(--fg-primary);display:inline-flex;align-items:center;gap:0.3rem;margin-top:0.4rem;"><i class="bi bi-file-earmark-text"></i> View PDF</a>` : '';
      const rejReason = (doc.status === 'rejected' && doc.rejection_reason)
        ? `<div style="background:rgba(220,53,69,0.08);border-left:3px solid #dc3545;border-radius:6px;padding:0.6rem;margin-top:0.5rem;font-size:0.78rem;color:var(--fg-text);"><strong style="color:#dc3545;">Reason:</strong> ${esc(doc.rejection_reason)}</div>` : '';
      return `<div style="background:var(--fg-card-bg);border:1px solid ${doc.status==='rejected'?'rgba(220,53,69,0.3)':'var(--fg-border)'};border-radius:10px;padding:0.85rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.35rem;">
          <span style="font-size:0.85rem;font-weight:700;color:var(--fg-text);">${esc(doc.label)}</span>
          ${statusBadge}
        </div>
        ${imgTag}${pdfLink}${rejReason}
      </div>`;
    }).join('');

    const hasRejected = docs.some(d => d.status === 'rejected');

    document.getElementById('statusArea').innerHTML = `
      <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;padding:2rem;max-width:640px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:1.5rem;">
          <div style="width:72px;height:72px;border-radius:50%;background:${color}22;border:3px solid ${color};display:flex;align-items:center;justify-content:center;font-size:2rem;color:${color};margin:0 auto 1rem;">
            <i class="bi ${icon}"></i>
          </div>
          <h4 style="font-weight:800;color:var(--fg-text);margin-bottom:0.5rem;">Application ${app.status.charAt(0).toUpperCase()+app.status.slice(1)}</h4>
          <p style="color:var(--fg-muted);font-size:0.88rem;margin-bottom:0.5rem;">
            ${app.status === 'pending'
              ? (hasRejected ? 'Some documents need resubmission. Review the reasons below.' : "Your technician application is under review. We'll notify you within 1–2 business days.")
              : app.status === 'approved' ? 'Congratulations! Your application has been approved. You can now log in with your technician account.'
              : 'Your application was not approved. ' + (app.admin_notes ? 'Reason: ' + esc(app.admin_notes) : 'Please contact support for details.')}
          </p>
          <div style="font-size:0.75rem;color:var(--fg-muted);">Submitted: ${submitted}</div>
        </div>
        ${docCards ? `<div style="margin-bottom:1.25rem;"><div style="font-size:0.82rem;font-weight:700;color:var(--fg-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.75rem;">Documents Submitted</div><div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:0.75rem;">${docCards}</div></div>` : ''}
        ${app.status === 'rejected' ? `<div style="text-align:center;"><button onclick="document.getElementById('wizardWrap').style.display='';document.getElementById('statusArea').innerHTML=''" style="padding:0.6rem 1.5rem;border-radius:10px;background:var(--fg-primary);color:#fff;border:none;font-weight:700;cursor:pointer;"><i class="bi bi-arrow-repeat"></i> Re-apply</button></div>` : ''}
      </div>`;
  }

  // ── Leaflet / OpenStreetMap ───────────────────────────────────────────────
  var leafletMap    = null;
  var leafletMarker = null;

  function initLeafletMap(lat, lng) {
    var container = document.getElementById('mapContainer');
    if (!container) return;
    var clat = lat || 12.8797, clng = lng || 121.7740; // PH center default
    if (leafletMap) {
      leafletMap.setView([clat, clng], 13);
      if (leafletMarker) leafletMarker.setLatLng([clat, clng]);
      leafletMap.invalidateSize();
      return;
    }
    leafletMap = L.map('mapContainer').setView([clat, clng], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19
    }).addTo(leafletMap);
    leafletMarker = L.marker([clat, clng], { draggable: true }).addTo(leafletMap);
    leafletMarker.on('dragend', function(e) {
      var pos = e.target.getLatLng();
      document.getElementById('addressLat').value = pos.lat.toFixed(7);
      document.getElementById('addressLng').value = pos.lng.toFixed(7);
      reverseGeocode(pos.lat, pos.lng);
    });
    leafletMap.on('click', function(e) {
      leafletMarker.setLatLng(e.latlng);
      document.getElementById('addressLat').value = e.latlng.lat.toFixed(7);
      document.getElementById('addressLng').value = e.latlng.lng.toFixed(7);
      reverseGeocode(e.latlng.lat, e.latlng.lng);
    });
  }

  function geocodeAddress(address) {
    if (!address) return;
    var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(address + ', Philippines');
    fetch(url, { headers: { 'Accept-Language': 'en' } })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data && data[0]) {
          var lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
          if (leafletMap) {
            leafletMap.setView([lat, lng], 15);
            leafletMarker.setLatLng([lat, lng]);
          }
          document.getElementById('addressLat').value = lat.toFixed(7);
          document.getElementById('addressLng').value = lng.toFixed(7);
        }
      }).catch(function() {});
  }

  function reverseGeocode(lat, lng) {
    var url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng;
    fetch(url, { headers: { 'Accept-Language': 'en' } })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data && data.display_name) {
          var detail = document.getElementById('addrDetail');
          if (detail && !detail.value) detail.value = data.display_name;
        }
      }).catch(function() {});
  }

  function updateMapFromDetail() {
    var detail = document.getElementById('addrDetail').value.trim();
    var brgy   = document.getElementById('addrBarangay')?.value || '';
    var city   = document.getElementById('addrCity')?.value || '';
    var prov   = document.getElementById('addrProvince')?.value || '';
    if (detail.length > 5) {
      geocodeAddress([detail, brgy, city, prov].filter(Boolean).join(', '));
    }
  }

  // ── Address modal ─────────────────────────────────────────────────────────
  function openAddrModal() {
    document.getElementById('addrModal').classList.add('open');
    document.getElementById('addrModalTitle').innerHTML =
      '<i class="bi bi-geo-alt-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>' +
      (document.getElementById('shopAddress').value ? 'Edit Address' : 'Add a new Address');
    setTimeout(function() { initLeafletMap(12.8797, 121.7740); }, 150);
  }

  function closeAddrModal() {
    document.getElementById('addrModal').classList.remove('open');
  }

  function saveAddress() {
    var detail = document.getElementById('addrDetail').value.trim();
    var brgy   = document.getElementById('addrBarangay').value;
    var city   = document.getElementById('addrCity').value;
    var prov   = document.getElementById('addrProvince').value;
    var postal = document.getElementById('addrPostal').value.trim();
    var region = document.getElementById('addrRegion').options[document.getElementById('addrRegion').selectedIndex]?.text || '';
    var name   = document.getElementById('addrFullName').value.trim();
    if (!detail) { alert('Please enter a detail address.'); return; }
    if (!city)   { alert('Please select a City / Municipality.'); return; }

    var fullAddr = [detail, brgy, city, prov, postal, 'Philippines'].filter(Boolean).join(', ');
    var genLoc   = [region, prov, city].filter(Boolean).join(' / ');

    document.getElementById('shopAddress').value    = fullAddr;
    document.getElementById('generalLocation').value = genLoc;
    if (postal) document.getElementById('zipCode').value = postal;

    // Sync the Step 2 cascading selects to match
    syncStep2Location(region, prov, city);

    document.getElementById('addrDisplayName').textContent = name || city;
    document.getElementById('addrDisplayText').textContent = fullAddr;
    document.getElementById('addrDisplay').style.display   = 'block';
    document.getElementById('addrAddBtn').style.display    = 'none';
    closeAddrModal();
    markDirty();
  }

  function syncStep2Location(regionText, prov, city) {
    // Match region select by text
    var regSel = document.getElementById('locRegion');
    for (var i = 0; i < regSel.options.length; i++) {
      if (regSel.options[i].text.indexOf(regionText.split('–')[0].trim()) !== -1 ||
          regionText.indexOf(regSel.options[i].value) !== -1) {
        regSel.selectedIndex = i;
        onRegionChange();
        break;
      }
    }
  }

  // ── Wizard navigation ─────────────────────────────────────────────────────
  var currentStep = 1;

  function wizardNext() {
    if (!validateStep(currentStep)) return;
    if (currentStep === 2) { buildReview(); }
    goToStep(currentStep + 1);
  }

  function wizardBack() {
    goToStep(currentStep - 1);
  }

  function goToStep(n) {
    document.getElementById('panel' + currentStep).classList.remove('active');
    document.getElementById('step-dot-' + currentStep).classList.remove('active');
    if (n > currentStep) document.getElementById('step-dot-' + currentStep).classList.add('done');
    else document.getElementById('step-dot-' + (currentStep)).classList.remove('done');

    currentStep = n;
    document.getElementById('panel' + currentStep).classList.add('active');
    document.getElementById('step-dot-' + currentStep).classList.add('active');

    document.getElementById('btnBack').style.display   = currentStep > 1 ? 'inline-flex' : 'none';
    document.getElementById('btnNext').style.display   = currentStep < 3 ? 'inline-flex' : 'none';
    document.getElementById('btnSubmit').style.display = currentStep === 3 ? 'inline-flex' : 'none';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function validateStep(step) {
    const show = (msg) => { showAlert('danger', msg); return false; };
    if (step === 1) {
      if (!document.getElementById('lastName').value.trim())  return show('Last name is required.');
      if (!document.getElementById('firstName').value.trim()) return show('First name is required.');
      if (!document.getElementById('techEmail').value.trim()) return show('Account email is required.');
      const pw = document.getElementById('techPassword').value;
      if (pw.length < 8) return show('Password must be at least 8 characters.');
      if (pw !== document.getElementById('techConfirmPassword').value) return show('Passwords do not match.');
      const specs = [...document.querySelectorAll('#specGrid .spec-chip.checked')];
      if (!specs.length) return show('Please select at least one specialization.');
      if (!document.getElementById('govIdFile').files[0]) return show('Government ID is required.');
    }
    if (step === 2) {
      if (!document.getElementById('businessName').value.trim())  return show('Business name is required.');
      if (!document.getElementById('generalLocation').value)      return show('Please select a general location.');
      if (!document.getElementById('shopAddress').value.trim())   return show('Please add a business address.');
      if (!document.getElementById('zipCode').value.trim())       return show('ZIP code is required.');
    }
    hideAlert();
    return true;
  }

  function buildReview() {
    const fn = document.getElementById('firstName').value.trim();
    const ln = document.getElementById('lastName').value.trim();
    const mn = document.getElementById('middleName').value.trim();
    const sx = document.getElementById('suffix').value;
    const fullName = [ln, fn, mn, sx].filter(Boolean).join(' ');

    const entityMap = { sole_proprietorship:'Sole Proprietorship', corporation:'Corporation / Partnership', one_person_corp:'One Person Corporation' };
    const entityVal = document.querySelector('input[name="entityType"]:checked')?.value || 'sole_proprietorship';
    const specs     = [...document.querySelectorAll('#specGrid .spec-chip.checked')].map(c => c.dataset.value).join(', ');

    const docBadge = (fileId) => {
      const f = document.getElementById(fileId)?.files[0];
      if (!f) return '<span class="doc-badge na">Not provided</span>';
      const isImg = f.type.startsWith('image/');
      const url   = URL.createObjectURL(f);
      return `<div style="display:flex;flex-direction:column;gap:0.5rem;">
        <span class="doc-badge ok"><i class="bi bi-check-circle-fill"></i> ${esc(f.name)}</span>
        ${isImg ? `<img src="${url}" alt="preview" style="max-width:220px;max-height:140px;border-radius:8px;border:1px solid var(--fg-border);object-fit:contain;display:block;">` : `<a href="${url}" target="_blank" style="font-size:0.75rem;color:var(--fg-primary);display:inline-flex;align-items:center;gap:0.3rem;"><i class="bi bi-file-earmark-text"></i> View PDF</a>`}
      </div>`;
    };

    document.getElementById('rv-name').textContent    = fullName;
    document.getElementById('rv-email').textContent   = document.getElementById('techEmail').value;
    document.getElementById('rv-exp').textContent     = document.getElementById('experienceYrs').value + ' year(s)';
    document.getElementById('rv-specs').textContent   = specs;
    document.getElementById('rv-entity').textContent  = entityMap[entityVal] || entityVal;
    document.getElementById('rv-bizname').textContent = document.getElementById('businessName').value;
    document.getElementById('rv-location').textContent= document.getElementById('generalLocation').value;
    document.getElementById('rv-address').textContent = document.getElementById('shopAddress').value;
    document.getElementById('rv-zip').textContent     = document.getElementById('zipCode').value;
    document.getElementById('rv-bizemail').textContent= document.getElementById('businessEmail').value || '—';
    document.getElementById('rv-govid').innerHTML     = docBadge('govIdFile');
    document.getElementById('rv-cert').innerHTML      = docBadge('certFile');
    document.getElementById('rv-dti').innerHTML       = docBadge('dtiFile');
    document.getElementById('rv-bir').innerHTML       = docBadge('birFile');
  }

  // ── Submit ────────────────────────────────────────────────────────────────
  function submitApplication() {
    const btn  = document.getElementById('btnSubmit');
    const user = FGAuth.UserStore.get();
    btn.disabled = true;
    btn.innerHTML = '<span style="display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,0.4);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;margin-right:0.5rem;"></span> Submitting…';

    const fd = new FormData();
    fd.append('customer_id',     user?.id || '');
    fd.append('firstName',       document.getElementById('firstName').value.trim());
    fd.append('lastName',        document.getElementById('lastName').value.trim());
    fd.append('middleName',      document.getElementById('middleName').value.trim());
    fd.append('suffix',          document.getElementById('suffix').value);
    fd.append('email',           document.getElementById('techEmail').value.trim());
    fd.append('password',        document.getElementById('techPassword').value);
    fd.append('confirmPassword', document.getElementById('techConfirmPassword').value);
    fd.append('experienceYrs',   document.getElementById('experienceYrs').value);
    fd.append('specializations', [...document.querySelectorAll('#specGrid .spec-chip.checked')].map(c => c.dataset.value).join(', '));
    fd.append('entityType',      document.querySelector('input[name="entityType"]:checked')?.value || 'sole_proprietorship');
    fd.append('businessName',    document.getElementById('businessName').value.trim());
    fd.append('generalLocation', document.getElementById('generalLocation').value);
    fd.append('shopAddress',     document.getElementById('shopAddress').value.trim());
    fd.append('zipCode',         document.getElementById('zipCode').value.trim());
    fd.append('addressLat',      document.getElementById('addressLat').value || '0');
    fd.append('addressLng',      document.getElementById('addressLng').value || '0');
    fd.append('businessEmail',   document.getElementById('businessEmail').value.trim());

    const govId = document.getElementById('govIdFile').files[0];
    const cert  = document.getElementById('certFile').files[0];
    const dti   = document.getElementById('dtiFile').files[0];
    const bir   = document.getElementById('birFile').files[0];
    if (govId) fd.append('govIdFile', govId);
    if (cert)  fd.append('certFile',  cert);
    if (dti)   fd.append('dtiFile',   dti);
    if (bir)   fd.append('birFile',   bir);

    fetch('../../../backend/technician_apply.php', { method: 'POST', body: fd, credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          clearDirty(); // allow redirect without leave-page prompt
          document.getElementById('wizardWrap').style.display = 'none';
          document.getElementById('statusArea').innerHTML = `
            <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;padding:2.5rem;text-align:center;max-width:520px;margin:0 auto;">
              <div style="width:72px;height:72px;border-radius:50%;background:rgba(40,167,69,0.12);display:flex;align-items:center;justify-content:center;font-size:2rem;color:#28A745;margin:0 auto 1.25rem;">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <h4 style="font-weight:800;color:var(--fg-text);margin-bottom:0.5rem;">Application Submitted!</h4>
              <p style="color:var(--fg-muted);font-size:0.88rem;margin-bottom:1.25rem;">${esc(d.message)}</p>
              <a href="dashboard.php" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.5rem;border-radius:10px;background:var(--fg-primary);color:#fff;font-weight:700;font-size:0.88rem;text-decoration:none;">
                <i class="bi bi-house-fill"></i> Back to Dashboard
              </a>
            </div>`;
        } else {
          showAlert('danger', d.message || 'Submission failed. Please try again.');
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-send-fill"></i> Submit Application';
        }
      })
      .catch(() => {
        showAlert('danger', 'Network error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Submit Application';
      });
  }

  // ── Helpers ───────────────────────────────────────────────────────────────
  function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  function showAlert(type, msg) {
    const el = document.getElementById('alertBox');
    el.className = 'alert-bar alert-' + type;
    el.innerHTML = '<i class="bi bi-' + (type==='danger'?'exclamation-circle-fill':'check-circle-fill') + '"></i> ' + esc(msg);
    el.style.display = 'flex';
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function hideAlert() {
    document.getElementById('alertBox').style.display = 'none';
  }

  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      fetch('/api/logout').finally(() => {
        FGAuth.UserStore.clear();
        window.location.href = '/login.html';
      });
    });
  }
  </script>

</body>
</html>




