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
  <title>Fix&amp;Go — Technician Profile</title>
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
    .profile-grid{display:grid;grid-template-columns:1fr 1.6fr;gap:1.5rem;align-items:start;}
    .section-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.5rem;margin-bottom:1.5rem;}
    .section-card:last-child{margin-bottom:0;}
    .section-card h6{font-weight:800;font-size:1rem;color:var(--fg-text);margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;}
    .avatar{width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,rgba(139,92,246,0.35),rgba(139,92,246,0.12));border:3px solid rgba(139,92,246,0.4);display:flex;align-items:center;justify-content:center;font-size:2.2rem;color:#8b5cf6;font-weight:800;margin:0 auto 1rem;overflow:hidden;}
    .avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%;}
    .profile-name{text-align:center;font-size:1.2rem;font-weight:800;color:var(--fg-text);margin-bottom:0.25rem;}
    .profile-email{text-align:center;font-size:0.85rem;color:var(--fg-muted);margin-bottom:1rem;}
    .info-row{display:flex;align-items:center;gap:0.75rem;padding:0.65rem 0;border-bottom:1px solid var(--fg-border);}
    .info-row:last-child{border-bottom:none;}
    .info-icon{width:36px;height:36px;border-radius:8px;background:rgba(139,92,246,0.1);color:#8b5cf6;display:flex;align-items:center;justify-content:center;font-size:0.95rem;flex-shrink:0;}
    .info-label{font-size:0.72rem;color:var(--fg-muted);font-weight:700;text-transform:uppercase;}
    .info-value{font-size:0.9rem;color:var(--fg-text);font-weight:600;}
    .form-group{margin-bottom:1.1rem;}
    .form-group label{display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.4rem;}
    .form-group label span{color:#dc3545;margin-left:2px;}
    .form-input{width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;font-family:inherit;}
    .form-input:focus{border-color:#8b5cf6;box-shadow:0 0 0 3px rgba(139,92,246,0.12);}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
    .alert-bar{padding:0.75rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;}
    .alert-success{background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);}
    .alert-danger{background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @keyframes spin{to{transform:rotate(360deg);}}
    @media(max-width:992px){.profile-grid{grid-template-columns:1fr;}}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .tc-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .tc-sidebar.open{transform:translateX(0);}
      .tc-main{padding:1.25rem;}
      .form-row{grid-template-columns:1fr;}
    }
    @media(max-width:575px){
      html,body{overflow-x:hidden;}
      .tc-main{padding:0.85rem;}
      /* Credentials shop photo grid: 3 cols on phones instead of 5 */
      #shopPhotoGrid{grid-template-columns:repeat(3,1fr)!important;}
      /* Experience years input: full width */
      #editExperienceYears{max-width:100%!important;width:100%!important;}
      /* Video player in credentials shrinks */
      #videoList video{width:100px!important;min-width:100px!important;height:70px!important;}
      /* Cred-tab buttons wrap nicely */
      .cred-tab{padding:0.3rem 0.65rem!important;font-size:0.75rem!important;}
      /* Avatar card: make content full width */
      .section-card{padding:1rem!important;}
      .profile-name{font-size:1rem!important;}
      /* Navbar username hidden */
      #navUserName{display:none!important;}
    }
  </style>
</head>
<body>

  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <a href="/index.php?browse=1" class="btn btn-sm" style="border:1.5px solid rgba(139,92,246,0.4);border-radius:8px;color:#8b5cf6;background:rgba(139,92,246,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:0.35rem;"><i class="bi bi-house-door"></i> Browse Shop</a>
      <span style="background:rgba(139,92,246,0.12);color:#8b5cf6;border:1px solid rgba(139,92,246,0.25);padding:0.25rem 0.75rem;border-radius:50px;font-size:0.75rem;font-weight:700;">🔧 Technician</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="messages.php" style="position:relative;text-decoration:none;" title="Messages">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);transition:all 0.2s;"
             onmouseenter="this.style.borderColor='#8b5cf6';this.style.color='#8b5cf6'"
             onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-text)'">
          <i class="bi bi-chat-dots-fill"></i>
        </div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:#8b5cf6;color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <button id="logoutBtn" class="btn btn-sm"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
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
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
      </ul>
      <div class="sidebar-label">Account</div>
      <ul class="sidebar-nav">
        <li><a href="profile.php" class="active"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>

    <main class="tc-main">
      <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;"><i class="bi bi-person-circle" style="color:#8b5cf6;margin-right:0.5rem;"></i>My Profile</h2>
        <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">View and update your account information</p>
      </div>

      <div id="alertBox" style="display:none;"></div>

      <div class="profile-grid">
        <!-- Left: Avatar card -->
        <div class="section-card" style="text-align:center;">
          <div style="position:relative;display:inline-block;margin-bottom:1rem;">
            <div class="avatar" id="avatarEl">🔧</div>
            <label for="avatarInput" style="position:absolute;bottom:2px;right:2px;width:28px;height:28px;border-radius:50%;background:#8b5cf6;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.7rem;cursor:pointer;border:2px solid var(--fg-card-bg);" title="Change photo">
              <i class="bi bi-camera-fill"></i>
            </label>
            <input type="file" id="avatarInput" accept="image/*" style="display:none;">
          </div>
          <div class="profile-name" id="profileFullName">Loading…</div>
          <div class="profile-email" id="profileEmail">—</div>
          <span style="background:rgba(139,92,246,0.12);color:#8b5cf6;border:1px solid rgba(139,92,246,0.25);padding:0.25rem 0.85rem;border-radius:50px;font-size:0.75rem;font-weight:700;display:inline-flex;align-items:center;gap:0.35rem;">🔧 Phone Technician</span>
          <hr style="border-color:var(--fg-border);margin:1.1rem 0;">
          <div class="info-row">
            <div class="info-icon"><i class="bi bi-telephone-fill"></i></div>
            <div><div class="info-label">Phone</div><div class="info-value" id="profilePhone">—</div></div>
          </div>
          <div class="info-row">
            <div class="info-icon"><i class="bi bi-calendar-fill"></i></div>
            <div><div class="info-label">Member Since</div><div class="info-value" id="profileJoined">—</div></div>
          </div>
          <div class="info-row">
            <div class="info-icon" style="background:rgba(40,167,69,0.1);color:#28A745;"><i class="bi bi-shield-check-fill"></i></div>
            <div><div class="info-label">Account Status</div><div class="info-value" style="color:#28A745;">Active &amp; Verified</div></div>
          </div>
          <div class="info-row" id="profileSpecRow" style="display:none;">
            <div class="info-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="bi bi-tools"></i></div>
            <div><div class="info-label">Specializations</div><div class="info-value" id="profileSpec">—</div></div>
          </div>
          <div class="info-row" id="profileShopRow" style="display:none;">
            <div class="info-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="bi bi-shop"></i></div>
            <div><div class="info-label">Shop Name</div><div class="info-value" id="profileShop">—</div></div>
          </div>
          <div class="info-row" id="profileAddrRow" style="display:none;">
            <div class="info-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="bi bi-geo-alt-fill"></i></div>
            <div><div class="info-label">Shop Address</div><div class="info-value" id="profileAddr" style="font-size:0.82rem;line-height:1.4;">—</div></div>
          </div>

          <!-- Shop Image -->
          <hr style="border-color:var(--fg-border);margin:1.1rem 0;">
          <div style="text-align:left;">
            <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--fg-muted);margin-bottom:0.6rem;">Shop Photo</div>
            <div id="shopImgPreview" style="margin-bottom:0.6rem;display:none;">
              <img id="shopImgEl" style="width:100%;max-height:160px;object-fit:cover;border-radius:10px;border:1px solid var(--fg-border);">
            </div>
            <label for="shopImageInput" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.45rem 1rem;border-radius:8px;background:rgba(139,92,246,0.1);border:1.5px solid rgba(139,92,246,0.3);color:#8b5cf6;font-size:0.82rem;font-weight:700;cursor:pointer;transition:all 0.2s;" onmouseenter="this.style.background='rgba(139,92,246,0.2)'" onmouseleave="this.style.background='rgba(139,92,246,0.1)'">
              <i class="bi bi-camera-fill"></i> Upload Shop Photo
            </label>
            <input type="file" id="shopImageInput" accept="image/*" style="display:none;">
            <div id="shopImgStatus" style="font-size:0.75rem;color:var(--fg-muted);margin-top:0.4rem;"></div>
          </div>
        </div>

        <!-- Right: Edit forms -->
        <div>
          <!-- Edit Profile -->
          <div class="section-card">
            <h6><i class="bi bi-pencil-fill" style="color:#8b5cf6;"></i> Edit Profile</h6>
            <form id="profileForm">
              <div class="form-row">
                <div class="form-group">
                  <label>First Name <span>*</span></label>
                  <input type="text" class="form-input" id="editFirstName" required>
                </div>
                <div class="form-group">
                  <label>Last Name <span>*</span></label>
                  <input type="text" class="form-input" id="editLastName" required>
                </div>
              </div>
              <div class="form-group">
                <label>Email <span>*</span></label>
                <input type="email" class="form-input" id="editEmail" required>
              </div>
              <div class="form-group">
                <label>Phone</label>
                <input type="tel" class="form-input" id="editPhone" placeholder="+63 9XX XXX XXXX">
              </div>
              <div class="form-group">
                <label>Bio <span style="font-weight:400;color:var(--fg-muted);">(short tagline shown on your profile)</span></label>
                <input type="text" class="form-input" id="editBio" placeholder="e.g. Certified phone technician with 5+ years experience">
              </div>
              <div class="form-group">
                <label>Shop / Service Description <span style="font-weight:400;color:var(--fg-muted);">(detailed description visible to customers)</span></label>
                <textarea class="form-input" id="editDescription" rows="4" placeholder="Describe your services, expertise, turnaround time, and what makes your shop stand out…" style="resize:vertical;"></textarea>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Specializations</label>
                  <input type="text" class="form-input" id="editSpecializations" placeholder="e.g. Screen Repair, Battery Replacement">
                </div>
                <div class="form-group">
                  <label>Years of Experience</label>
                  <input type="number" class="form-input" id="editExperienceYears" placeholder="e.g. 5" min="0" max="99" style="max-width:120px;">
                </div>
              </div>
              <div class="form-group">
                <label>Shop Name</label>
                <input type="text" class="form-input" id="editShopName" placeholder="Your shop or business name">
              </div>

              <!-- Shop Address Section -->
              <div style="border-top:1px solid var(--fg-border);margin:1.25rem 0 1.1rem;padding-top:1.1rem;">
                <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#8b5cf6;margin-bottom:0.85rem;display:flex;align-items:center;gap:0.4rem;"><i class="bi bi-geo-alt-fill"></i> Shop / Service Address</div>
                <div class="form-group">
                  <label>Address Line</label>
                  <input type="text" class="form-input" id="editAddressLine" placeholder="House/Unit No., Street, Subdivision">
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label>Barangay</label>
                    <input type="text" class="form-input" id="editBarangay" placeholder="Barangay">
                  </div>
                  <div class="form-group">
                    <label>City / Municipality</label>
                    <input type="text" class="form-input" id="editCity" placeholder="City or Municipality">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label>Province</label>
                    <input type="text" class="form-input" id="editProvince" placeholder="Province">
                  </div>
                  <div class="form-group">
                    <label>ZIP Code</label>
                    <input type="text" class="form-input" id="editZipCode" placeholder="ZIP Code" maxlength="10">
                  </div>
                </div>
              </div>
              <button type="submit" class="btn-primary-custom" style="margin-top:0.5rem;background:#8b5cf6;border-color:#8b5cf6;" id="btnSaveProfile">
                <i class="bi bi-check-circle-fill"></i> Save Changes
              </button>
            </form>
          </div>

          <!-- Change Password -->
          <div class="section-card">
            <h6><i class="bi bi-lock-fill" style="color:#dc3545;"></i> Change Password</h6>
            <form id="passwordForm">
              <div class="form-group">
                <label>Current Password <span>*</span></label>
                <input type="password" class="form-input" id="currentPassword" required>
              </div>
              <div class="form-group">
                <label>New Password <span>*</span></label>
                <input type="password" class="form-input" id="newPassword" placeholder="Min. 8 characters" required>
              </div>
              <div class="form-group" style="margin-bottom:0;">
                <label>Confirm New Password <span>*</span></label>
                <input type="password" class="form-input" id="confirmPassword" required>
              </div>
              <button type="submit" class="btn-primary-custom" style="margin-top:1.25rem;background:#dc3545;border-color:#dc3545;" id="btnChangePass">
                <i class="bi bi-lock-fill"></i> Change Password
              </button>
            </form>
          </div>

          <!-- Credentials & Documents Upload -->
          <div class="section-card" id="credentialsCard">
            <h6><i class="bi bi-patch-check-fill" style="color:#8b5cf6;"></i> Credentials, Shop Photos &amp; Work Videos</h6>
            <p style="font-size:0.83rem;color:var(--fg-muted);margin-bottom:1.25rem;line-height:1.55;">
              Showcase your qualifications and shop to customers. Upload certificates, gov ID, up to <strong>5 shop photos</strong>, and up to <strong>3 work videos</strong> showing you fixing phones.
            </p>

            <!-- Tab switcher -->
            <div style="display:flex;gap:0.4rem;margin-bottom:1.25rem;flex-wrap:wrap;">
              <button class="cred-tab active" data-tab="docs" onclick="switchCredTab('docs')"
                style="padding:0.35rem 0.9rem;border-radius:8px;font-size:0.8rem;font-weight:700;cursor:pointer;border:1.5px solid #8b5cf6;background:rgba(139,92,246,0.1);color:#8b5cf6;transition:all 0.2s;">
                📋 Documents
              </button>
              <button class="cred-tab" data-tab="shop" onclick="switchCredTab('shop')"
                style="padding:0.35rem 0.9rem;border-radius:8px;font-size:0.8rem;font-weight:700;cursor:pointer;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);transition:all 0.2s;">
                🏪 Shop Photos
              </button>
              <button class="cred-tab" data-tab="video" onclick="switchCredTab('video')"
                style="padding:0.35rem 0.9rem;border-radius:8px;font-size:0.8rem;font-weight:700;cursor:pointer;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);transition:all 0.2s;">
                🎬 Work Videos
              </button>
            </div>

            <!-- Existing items list -->
            <div id="credList" style="display:flex;flex-direction:column;gap:0.65rem;margin-bottom:1.25rem;"></div>

            <!-- Upload form -->
            <div style="background:var(--fg-bg);border:1.5px dashed var(--fg-border);border-radius:12px;padding:1.1rem;" id="credUploadForm">

              <!-- Tab: Documents -->
              <div id="credTab_docs">
                <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#8b5cf6;margin-bottom:0.85rem;">Upload Credential / Document</div>
                <div class="form-row" style="margin-bottom:0.85rem;">
                  <div class="form-group" style="margin-bottom:0;">
                    <label>Document Type <span>*</span></label>
                    <select class="form-input" id="credDocType">
                      <option value="gov_id">🪪 Government ID</option>
                      <option value="bir">📄 BIR Certificate</option>
                      <option value="dti">📋 DTI Permit</option>
                      <option value="tech_cert" selected>🏅 Technician Certification</option>
                      <option value="tesda">🎓 TESDA Certificate</option>
                      <option value="nstp">📜 NSTP Certificate</option>
                      <option value="bank">🏦 Bank Document</option>
                      <option value="skill_cert">⚙️ Skill Certificate</option>
                      <option value="custom">📎 Other Document</option>
                    </select>
                  </div>
                  <div class="form-group" style="margin-bottom:0;">
                    <label>Label <span style="font-weight:400;color:var(--fg-muted);">(optional)</span></label>
                    <input type="text" class="form-input" id="credLabel" placeholder="e.g. Samsung Certified Technician">
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:0.85rem;">
                  <label>File <span>*</span> <span style="font-weight:400;color:var(--fg-muted);">JPG, PNG, WebP, PDF — max 10 MB</span></label>
                  <label for="credFileInput"
                    style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border:2px dashed rgba(139,92,246,0.35);border-radius:10px;cursor:pointer;transition:border-color 0.2s;background:var(--fg-card-bg);"
                    onmouseenter="this.style.borderColor='#8b5cf6'" onmouseleave="this.style.borderColor='rgba(139,92,246,0.35)'">
                    <i class="bi bi-cloud-upload-fill" style="color:#8b5cf6;font-size:1.3rem;flex-shrink:0;"></i>
                    <span style="font-size:0.85rem;color:var(--fg-muted);">Click to choose file</span>
                  </label>
                  <input type="file" id="credFileInput" accept="image/*,.pdf" style="display:none;" onchange="onCredFileChange(this,'docs')">
                  <div id="credFilePreview" style="display:none;margin-top:0.6rem;"></div>
                </div>
              </div>

              <!-- Tab: Shop Photos -->
              <div id="credTab_shop" style="display:none;">
                <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#8b5cf6;margin-bottom:0.5rem;">Upload Shop Photo <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--fg-muted);">(up to 5 photos, max 10 MB each)</span></div>
                <p style="font-size:0.8rem;color:var(--fg-muted);margin-bottom:0.85rem;">Show customers your shop interior, equipment, workbench, or storefront.</p>
                <div id="shopPhotoGrid" style="display:grid;grid-template-columns:repeat(5,1fr);gap:0.5rem;margin-bottom:0.85rem;"></div>
                <label for="shopPhotoInput"
                  style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border:2px dashed rgba(139,92,246,0.35);border-radius:10px;cursor:pointer;transition:border-color 0.2s;background:var(--fg-card-bg);"
                  onmouseenter="this.style.borderColor='#8b5cf6'" onmouseleave="this.style.borderColor='rgba(139,92,246,0.35)'">
                  <i class="bi bi-image-fill" style="color:#8b5cf6;font-size:1.3rem;flex-shrink:0;"></i>
                  <div>
                    <div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);">Add Shop Photo</div>
                    <div style="font-size:0.72rem;color:var(--fg-muted);">JPG, PNG, WebP · Max 10 MB</div>
                  </div>
                </label>
                <input type="file" id="shopPhotoInput" accept="image/*" style="display:none;" onchange="uploadCredentialFile(this,'shop_image','🏪 Shop Photo')">
              </div>

              <!-- Tab: Work Videos -->
              <div id="credTab_video" style="display:none;">
                <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;color:#8b5cf6;margin-bottom:0.5rem;">Upload Work Video <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--fg-muted);">(up to 3 videos, max 100 MB each)</span></div>
                <p style="font-size:0.8rem;color:var(--fg-muted);margin-bottom:0.85rem;">Upload a short video of yourself repairing a phone. This builds trust with customers.</p>
                <div id="videoList" style="display:flex;flex-direction:column;gap:0.65rem;margin-bottom:0.85rem;"></div>
                <div class="form-group" style="margin-bottom:0.85rem;">
                  <label>Video Label <span style="font-weight:400;color:var(--fg-muted);">(optional)</span></label>
                  <input type="text" class="form-input" id="videoLabel" placeholder="e.g. Screen Replacement — iPhone 13">
                </div>
                <label for="workVideoInput"
                  style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border:2px dashed rgba(139,92,246,0.35);border-radius:10px;cursor:pointer;transition:border-color 0.2s;background:var(--fg-card-bg);"
                  onmouseenter="this.style.borderColor='#8b5cf6'" onmouseleave="this.style.borderColor='rgba(139,92,246,0.35)'">
                  <i class="bi bi-camera-video-fill" style="color:#8b5cf6;font-size:1.3rem;flex-shrink:0;"></i>
                  <div>
                    <div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);">Add Work Video</div>
                    <div style="font-size:0.72rem;color:var(--fg-muted);">MP4, WebM, MOV · Max 100 MB</div>
                  </div>
                </label>
                <input type="file" id="workVideoInput" accept="video/mp4,video/webm,video/quicktime" style="display:none;"
                  onchange="uploadVideoFile(this)">
                <div id="videoUploadProgress" style="display:none;margin-top:0.6rem;"></div>
              </div>

              <div id="credUploadAlert" style="display:none;padding:0.5rem 0.85rem;border-radius:8px;font-size:0.82rem;font-weight:600;margin-top:0.75rem;"></div>

              <!-- Submit button — only for docs tab -->
              <button id="credUploadBtn" onclick="uploadCredential()"
                style="width:100%;padding:0.75rem;border-radius:10px;background:#8b5cf6;color:#fff;border:none;font-weight:800;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:opacity 0.2s;margin-top:0.85rem;"
                onmouseenter="this.style.opacity='0.88'" onmouseleave="this.style.opacity='1'">
                <i class="bi bi-upload"></i> Upload Document
              </button>
            </div>
          </div>

        </div><!-- /right column -->
      </div><!-- /profile-grid -->
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script>
  'use strict';
  const TECH_API    = '../../../backend/technician_dashboard.php';
  const PROFILE_API = '/api/profile';

  function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

  document.addEventListener('DOMContentLoaded', function(){
    const user = FGAuth.UserStore.get();
    if(!user || user.role !== 'phone_technician'){window.location.href='/login.html';return;}

    const fullName = ((user.firstName||'')+' '+(user.lastName||'')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email;

    const sidebar = document.getElementById('tcSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', function(){sidebar.classList.toggle('open');overlay.classList.toggle('open');});
    overlay.addEventListener('click', function(){sidebar.classList.remove('open');overlay.classList.remove('open');});

    loadProfile();
    loadUnreadCount();
    loadCredentials();

    // Avatar upload
    document.getElementById('avatarInput').addEventListener('change', function(){
      const file = this.files[0];
      if(!file) return;
      if(file.size > 3*1024*1024){showAlert('danger','Image must be under 3MB.');return;}
      const reader = new FileReader();
      reader.onload = e => {
        const av = document.getElementById('avatarEl');
        av.innerHTML = `<img src="${e.target.result}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
      };
      reader.readAsDataURL(file);
      const fd = new FormData();
      fd.append('action','upload_avatar');
      fd.append('avatar',file);
      fetch(PROFILE_API,{method:'POST',body:fd,credentials:'include'})
        .then(r=>r.json())
        .then(d=>{
          if(d.success){
            showAlert('success','Profile photo updated!');
            const upd = Object.assign({},FGAuth.UserStore.get(),{avatar_url:d.avatar_url});
            FGAuth.UserStore.save(upd);
          } else showAlert('danger',d.message||'Upload failed.');
        }).catch(()=>showAlert('danger','Upload failed.'));
    });

    // Save extended profile via technician_dashboard.php
    document.getElementById('profileForm').addEventListener('submit', function(e){
      e.preventDefault();
      const fn = document.getElementById('editFirstName').value.trim();
      const ln = document.getElementById('editLastName').value.trim();
      const em = document.getElementById('editEmail').value.trim();
      const ph = document.getElementById('editPhone').value.trim();
      const bio = document.getElementById('editBio').value.trim();
      const desc = document.getElementById('editDescription').value.trim();
      const spec = document.getElementById('editSpecializations').value.trim();
      const expYrs = parseInt(document.getElementById('editExperienceYears').value) || 0;
      const shop = document.getElementById('editShopName').value.trim();
      if(!fn||!ln||!em){showAlert('danger','First name, last name, and email are required.');return;}

      const btn = document.getElementById('btnSaveProfile');
      btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';

      fetch(TECH_API,{
        method:'POST',headers:{'Content-Type':'application/json'},credentials:'include',
        body:JSON.stringify({
          action:'update_profile',
          first_name:fn, last_name:ln, email:em, phone:ph,
          bio:bio, description:desc,
          specializations:spec, experience_years:expYrs, shop_name:shop,
          address_line:document.getElementById('editAddressLine').value.trim(),
          barangay:document.getElementById('editBarangay').value.trim(),
          city:document.getElementById('editCity').value.trim(),
          province:document.getElementById('editProvince').value.trim(),
          zip_code:document.getElementById('editZipCode').value.trim()
        })
      })
        .then(r=>r.json())
        .then(d=>{
          if(!d.success) throw new Error(d.message||'Update failed.');
          const upd = Object.assign({},FGAuth.UserStore.get(),{firstName:fn,lastName:ln,email:em,phone:ph});
          FGAuth.UserStore.save(upd);
          const av = document.getElementById('avatarEl');
          if(!av.querySelector('img')) av.textContent = ((fn[0]||'')+(ln[0]||'')).toUpperCase();
          document.getElementById('profileFullName').textContent = (fn+' '+ln).trim();
          document.getElementById('profileEmail').textContent = em;
          document.getElementById('profilePhone').textContent = ph||'Not set';
          document.getElementById('navUserName').textContent = (fn+' '+ln).trim();
          if(spec){document.getElementById('profileSpec').textContent=spec;document.getElementById('profileSpecRow').style.display='flex';}
          if(shop){document.getElementById('profileShop').textContent=shop;document.getElementById('profileShopRow').style.display='flex';}
          var addrParts=[document.getElementById('editAddressLine').value.trim(),document.getElementById('editBarangay').value.trim(),document.getElementById('editCity').value.trim(),document.getElementById('editProvince').value.trim(),document.getElementById('editZipCode').value.trim()].filter(Boolean);
          if(addrParts.length){document.getElementById('profileAddr').textContent=addrParts.join(', ');document.getElementById('profileAddrRow').style.display='flex';}
          showAlert('success','Profile updated successfully!');
        })
        .catch(err=>showAlert('danger',err.message))
        .finally(()=>{btn.disabled=false;btn.innerHTML='<i class="bi bi-check-circle-fill"></i> Save Changes';});
    });

    // Change password via profile.php
    document.getElementById('passwordForm').addEventListener('submit', function(e){
      e.preventDefault();
      const current = document.getElementById('currentPassword').value;
      const newPass = document.getElementById('newPassword').value;
      const confirm = document.getElementById('confirmPassword').value;
      if(newPass.length < 8){showAlert('danger','New password must be at least 8 characters.');return;}
      if(newPass !== confirm){showAlert('danger','Passwords do not match.');return;}

      const btn = document.getElementById('btnChangePass');
      btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Changing…';

      fetch(PROFILE_API,{
        method:'POST',headers:{'Content-Type':'application/json'},credentials:'include',
        body:JSON.stringify({action:'change_password',current_password:current,new_password:newPass,confirm_password:confirm})
      })
        .then(r=>r.json())
        .then(d=>{
          if(!d.success) throw new Error(d.message||'Password change failed.');
          showAlert('success','Password changed successfully!');
          document.getElementById('passwordForm').reset();
        })
        .catch(err=>showAlert('danger',err.message))
        .finally(()=>{btn.disabled=false;btn.innerHTML='<i class="bi bi-lock-fill"></i> Change Password';});
    });
  });

  function loadProfile(){
    fetch(TECH_API+'?action=profile',{credentials:'include'})
      .then(r=>r.json())
      .then(d=>{
        if(!d.success) return;
        const u = d.profile;
        const fullName = ((u.first_name||'')+' '+(u.last_name||'')).trim();
        document.getElementById('navUserName').textContent = fullName || u.email || 'Technician';
        document.getElementById('profileFullName').textContent = fullName || 'Technician';
        document.getElementById('profileEmail').textContent = u.email || '—';
        document.getElementById('profilePhone').textContent = u.phone || 'Not set';
        document.getElementById('profileJoined').textContent = u.created_at
          ? new Date(u.created_at).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})
          : 'N/A';
        const av = document.getElementById('avatarEl');
        if(u.avatar_url){
          av.innerHTML = `<img src="../../../${esc(u.avatar_url)}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
        } else {
          const initials = (((u.first_name||'')[0]||'')+((u.last_name||'')[0]||'')).toUpperCase();
          av.textContent = initials || '🔧';
        }
        if(u.specializations){document.getElementById('profileSpec').textContent=u.specializations;document.getElementById('profileSpecRow').style.display='flex';}
        if(u.shop_name){document.getElementById('profileShop').textContent=u.shop_name;document.getElementById('profileShopRow').style.display='flex';}
        // Show existing shop image if any
        if(u.shop_image){
          const base = '../../../';
          document.getElementById('shopImgEl').src = base + u.shop_image;
          document.getElementById('shopImgPreview').style.display = 'block';
        }
        document.getElementById('editFirstName').value = u.first_name || '';
        document.getElementById('editLastName').value  = u.last_name  || '';
        document.getElementById('editEmail').value     = u.email      || '';
        document.getElementById('editPhone').value     = u.phone      || '';
        document.getElementById('editBio').value       = u.bio        || '';
        document.getElementById('editDescription').value = u.description || '';
        document.getElementById('editSpecializations').value = u.specializations || '';
        document.getElementById('editExperienceYears').value = u.experience_years > 0 ? u.experience_years : '';
        document.getElementById('editShopName').value  = u.shop_name  || '';
        document.getElementById('editAddressLine').value = u.address_line || '';
        document.getElementById('editBarangay').value     = u.barangay     || '';
        document.getElementById('editCity').value          = u.city          || '';
        document.getElementById('editProvince').value      = u.province      || '';
        document.getElementById('editZipCode').value        = u.zip_code      || '';
        var addrParts=[u.address_line,u.barangay,u.city,u.province,u.zip_code].filter(Boolean);
        if(addrParts.length){document.getElementById('profileAddr').textContent=addrParts.join(', ');document.getElementById('profileAddrRow').style.display='flex';}
        const upd = Object.assign({},FGAuth.UserStore.get(),{firstName:u.first_name,lastName:u.last_name,email:u.email,phone:u.phone,avatar_url:u.avatar_url});
        FGAuth.UserStore.save(upd);
      }).catch(()=>{});
  }

    // Shop image upload
    document.getElementById('shopImageInput').addEventListener('change', function(){
      const file = this.files[0];
      if (!file) return;
      if (file.size > 5*1024*1024) { showAlert('danger','Image must be under 5MB.'); return; }
      // Preview immediately
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('shopImgEl').src = e.target.result;
        document.getElementById('shopImgPreview').style.display = 'block';
      };
      reader.readAsDataURL(file);
      // Upload
      const fd = new FormData();
      fd.append('action', 'upload_shop_image');
      fd.append('shop_image', file);
      document.getElementById('shopImgStatus').textContent = 'Uploading…';
      fetch(TECH_API, { method:'POST', body:fd, credentials:'include' })
        .then(r=>r.json())
        .then(d=>{
          if (d.success) {
            document.getElementById('shopImgStatus').textContent = '✅ Shop photo updated!';
            setTimeout(()=>{ document.getElementById('shopImgStatus').textContent=''; }, 3000);
          } else {
            document.getElementById('shopImgStatus').textContent = '❌ ' + (d.message||'Upload failed.');
          }
        }).catch(()=>{ document.getElementById('shopImgStatus').textContent = '❌ Upload failed.'; });
    });

  function loadUnreadCount(){
    fetch('/api/messages?action=unread_count',{credentials:'include'})
      .then(r=>r.json())
      .then(d=>{
        if(d.success && d.count > 0){
          const b = document.getElementById('navMsgBadge');
          if(b){b.textContent=d.count>99?'99+':d.count;b.style.display='inline-block';}
        }
      }).catch(()=>{});
    setTimeout(loadUnreadCount,15000);
  }

  function showAlert(type, msg){
    const box = document.getElementById('alertBox');
    box.style.display = 'flex';
    box.className = 'alert-bar alert-'+type;
    box.innerHTML = '<i class="bi bi-'+(type==='success'?'check-circle-fill':'exclamation-triangle-fill')+'"></i> '+esc(msg);
    setTimeout(()=>{box.style.display='none';},5000);
  }

  // ── Credentials & Documents ──────────────────────────────────
  const CRED_API = '/api/technician/credentials';

  const CRED_TYPE_ICONS = {
    gov_id:    'bi-person-vcard-fill',
    bir:       'bi-file-earmark-text-fill',
    dti:       'bi-file-earmark-ruled-fill',
    tech_cert: 'bi-patch-check-fill',
    tesda:     'bi-mortarboard-fill',
    nstp:      'bi-journal-bookmark-fill',
    bank:      'bi-bank2',
    skill_cert:'bi-tools',
    shop_image:'bi-image-fill',
    work_video:'bi-camera-video-fill',
    custom:    'bi-paperclip',
  };

  let _activeCredTab = 'docs';

  function switchCredTab(tab) {
    _activeCredTab = tab;
    document.querySelectorAll('.cred-tab').forEach(b => {
      const isActive = b.dataset.tab === tab;
      b.style.background    = isActive ? 'rgba(139,92,246,0.1)' : 'transparent';
      b.style.borderColor   = isActive ? '#8b5cf6' : 'var(--fg-border)';
      b.style.color         = isActive ? '#8b5cf6' : 'var(--fg-muted)';
    });
    ['docs','shop','video'].forEach(t => {
      const el = document.getElementById('credTab_' + t);
      if (el) el.style.display = t === tab ? 'block' : 'none';
    });
    // Show/hide the Upload Document button (only for docs tab)
    const btn = document.getElementById('credUploadBtn');
    if (btn) btn.style.display = tab === 'docs' ? 'flex' : 'none';
    renderCredentials(window._allCreds || []);
  }

  function loadCredentials() {
    fetch(CRED_API + '?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        window._allCreds = d.credentials || [];
        renderCredentials(window._allCreds);
      }).catch(() => {});
  }

  function renderCredentials(creds) {
    // Filter by active tab
    const tabFilter = { docs: c => c.doc_type !== 'shop_image' && c.doc_type !== 'work_video',
                        shop: c => c.doc_type === 'shop_image',
                        video: c => c.doc_type === 'work_video' };
    const filtered = creds.filter(tabFilter[_activeCredTab] || (() => true));

    const listEl = document.getElementById('credList');

    if (_activeCredTab === 'shop') {
      // Render shop photos as a grid
      const grid = document.getElementById('shopPhotoGrid');
      if (grid) {
        if (!filtered.length) {
          grid.innerHTML = '<div style="grid-column:1/-1;font-size:0.82rem;color:var(--fg-muted);font-style:italic;padding:0.5rem 0;">No shop photos yet.</div>';
        } else {
          grid.innerHTML = filtered.map(c => {
            const url = c.file_url_full || ('../../../' + c.file_url);
            return `<div style="position:relative;aspect-ratio:1;border-radius:8px;overflow:hidden;border:1px solid var(--fg-border);">
              <img src="${esc(url)}" style="width:100%;height:100%;object-fit:cover;">
              <button onclick="deleteCredential(${c.id})"
                style="position:absolute;top:3px;right:3px;width:22px;height:22px;border-radius:50%;background:rgba(220,53,69,0.9);border:none;color:#fff;cursor:pointer;font-size:0.65rem;display:flex;align-items:center;justify-content:center;line-height:1;">✕</button>
            </div>`;
          }).join('');
        }
      }
      listEl.innerHTML = '';
      return;
    }

    if (_activeCredTab === 'video') {
      // Render videos
      const videoList = document.getElementById('videoList');
      if (videoList) {
        if (!filtered.length) {
          videoList.innerHTML = '<div style="font-size:0.82rem;color:var(--fg-muted);font-style:italic;padding:0.5rem 0;">No work videos yet.</div>';
        } else {
          videoList.innerHTML = filtered.map(c => {
            const url = c.file_url_full || ('../../../' + c.file_url);
            return `<div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;padding:0.75rem;display:flex;gap:0.75rem;align-items:flex-start;">
              <video src="${esc(url)}" controls style="width:140px;min-width:140px;height:90px;object-fit:cover;border-radius:6px;background:#000;"></video>
              <div style="flex:1;min-width:0;">
                <div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);margin-bottom:0.3rem;">
                  <i class="bi bi-camera-video-fill" style="color:#8b5cf6;margin-right:0.3rem;"></i>${esc(c.label)}
                </div>
                <div style="font-size:0.72rem;color:var(--fg-muted);margin-bottom:0.6rem;">${esc(c.file_name)}</div>
                <button onclick="deleteCredential(${c.id})"
                  style="padding:0.25rem 0.65rem;border-radius:6px;font-size:0.72rem;font-weight:700;border:1.5px solid rgba(220,53,69,0.35);color:#dc3545;background:transparent;cursor:pointer;"
                  onmouseenter="this.style.background='rgba(220,53,69,0.1)'" onmouseleave="this.style.background='transparent'">
                  <i class="bi bi-trash-fill"></i> Delete
                </button>
              </div>
            </div>`;
          }).join('');
        }
      }
      listEl.innerHTML = '';
      return;
    }

    // Docs tab
    if (!filtered.length) {
      listEl.innerHTML = '<div style="font-size:0.83rem;color:var(--fg-muted);font-style:italic;text-align:center;padding:0.5rem 0;">No documents yet. Upload your first credential below.</div>';
      return;
    }
    listEl.innerHTML = filtered.map(c => {
      const isPdf  = c.file_ext === 'pdf';
      const icon   = CRED_TYPE_ICONS[c.doc_type] || 'bi-file-earmark-fill';
      const base   = '../../../';
      const url    = c.file_url_full || (base + c.file_url);
      const thumb  = c.is_image
        ? `<img src="${esc(url)}" alt="${esc(c.label)}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid var(--fg-border);flex-shrink:0;">`
        : `<div style="width:48px;height:48px;border-radius:6px;border:1px solid var(--fg-border);background:rgba(139,92,246,0.07);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi ${isPdf?'bi-file-earmark-pdf-fill':'bi-file-earmark-fill'}" style="color:#8b5cf6;font-size:1.3rem;"></i></div>`;
      return `<div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;">
        ${thumb}
        <div style="flex:1;min-width:0;">
          <div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);margin-bottom:0.15rem;">
            <i class="bi ${icon}" style="color:#8b5cf6;margin-right:0.3rem;"></i>${esc(c.label)}
          </div>
          <div style="font-size:0.72rem;color:var(--fg-muted);">${esc(c.file_name)} · ${c.file_ext.toUpperCase()}</div>
        </div>
        <div style="display:flex;gap:0.4rem;flex-shrink:0;">
          <a href="${esc(url)}" target="_blank" rel="noopener noreferrer"
            style="padding:0.3rem 0.7rem;border-radius:7px;font-size:0.72rem;font-weight:700;border:1.5px solid rgba(139,92,246,0.35);color:#8b5cf6;background:transparent;text-decoration:none;"
            onmouseenter="this.style.background='rgba(139,92,246,0.12)'" onmouseleave="this.style.background='transparent'">
            <i class="bi bi-eye-fill"></i>
          </a>
          <button onclick="deleteCredential(${c.id})"
            style="padding:0.3rem 0.7rem;border-radius:7px;font-size:0.72rem;font-weight:700;border:1.5px solid rgba(220,53,69,0.35);color:#dc3545;background:transparent;cursor:pointer;"
            onmouseenter="this.style.background='rgba(220,53,69,0.1)'" onmouseleave="this.style.background='transparent'">
            <i class="bi bi-trash-fill"></i>
          </button>
        </div>
      </div>`;
    }).join('');
  }

  function onCredFileChange(input, tab) {
    const file = input.files[0];
    const prevEl = document.getElementById('credFilePreview');
    if (!file) { if(prevEl) prevEl.style.display = 'none'; return; }
    if (prevEl) {
      prevEl.style.display = 'block';
      if (file.type.startsWith('image/')) {
        const url = URL.createObjectURL(file);
        prevEl.innerHTML = `<img src="${url}" style="max-width:100%;max-height:140px;object-fit:contain;border-radius:8px;border:1px solid var(--fg-border);">
          <div style="font-size:0.75rem;color:var(--fg-muted);margin-top:0.3rem;">${esc(file.name)}</div>`;
      } else {
        prevEl.innerHTML = `<div style="display:flex;align-items:center;gap:0.6rem;padding:0.55rem 0.85rem;background:rgba(139,92,246,0.07);border-radius:8px;border:1px solid rgba(139,92,246,0.2);">
          <i class="bi bi-file-earmark-pdf-fill" style="color:#dc3545;font-size:1.3rem;"></i>
          <div><div style="font-size:0.84rem;font-weight:700;color:var(--fg-text);">${esc(file.name)}</div>
          <div style="font-size:0.72rem;color:var(--fg-muted);">${(file.size/1024).toFixed(1)} KB</div></div>
        </div>`;
      }
    }
    const labelEl = document.getElementById('credLabel');
    if (labelEl && !labelEl.value) {
      labelEl.value = file.name.replace(/\.[^.]+$/, '').replace(/[_\-]+/g, ' ');
    }
  }

  // Upload a file directly (shop photos / no extra type selector)
  function uploadCredentialFile(input, docType, defaultLabel) {
    const file = input.files[0];
    if (!file) return;
    const labelVal = docType === 'shop_image'
      ? (defaultLabel || '🏪 Shop Photo')
      : (document.getElementById('videoLabel')?.value.trim() || defaultLabel || '🎬 Work Video');

    showCredAlert('info', '<i class="bi bi-hourglass-split"></i> Uploading…');
    const fd = new FormData();
    fd.append('action',   'upload');
    fd.append('doc_type', docType);
    fd.append('label',    labelVal);
    fd.append('file',     file);

    fetch(CRED_API, { method: 'POST', credentials: 'include', body: fd })
      .then(r => r.json())
      .then(d => {
        if (!d.success) throw new Error(d.message || 'Upload failed.');
        showCredAlert('success', '✅ Uploaded successfully!');
        input.value = '';
        loadCredentials();
      })
      .catch(err => showCredAlert('danger', err.message));
  }

  function uploadVideoFile(input) {
    uploadCredentialFile(input, 'work_video', document.getElementById('videoLabel')?.value.trim() || '🎬 Work Video');
    if (document.getElementById('videoLabel')) document.getElementById('videoLabel').value = '';
  }

  function uploadCredential() {
    const fileInput = document.getElementById('credFileInput');
    const docType   = document.getElementById('credDocType').value;
    const labelEl   = document.getElementById('credLabel');
    const btn       = document.getElementById('credUploadBtn');

    if (!fileInput.files || !fileInput.files[0]) {
      showCredAlert('danger', 'Please select a file to upload.'); return;
    }
    const file = fileInput.files[0];
    const maxMB = file.type === 'application/pdf' ? 10 : 5;
    if (file.size > maxMB * 1024 * 1024) {
      showCredAlert('danger', 'File too large. Max ' + maxMB + ' MB.'); return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading…';

    const fd = new FormData();
    fd.append('action',   'upload');
    fd.append('doc_type', docType);
    if (labelEl.value.trim()) fd.append('label', labelEl.value.trim());
    fd.append('file', file);

    fetch(CRED_API, { method: 'POST', credentials: 'include', body: fd })
      .then(r => r.json())
      .then(d => {
        if (!d.success) throw new Error(d.message || 'Upload failed.');
        showCredAlert('success', '✅ Document uploaded!');
        fileInput.value = '';
        if (labelEl) labelEl.value = '';
        const pv = document.getElementById('credFilePreview');
        if (pv) pv.style.display = 'none';
        btn.innerHTML = '<i class="bi bi-upload"></i> Upload Document';
        loadCredentials();
      })
      .catch(err => {
        showCredAlert('danger', err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-upload"></i> Upload Document';
      });
  }

  function deleteCredential(id) {
    if (!confirm('Delete this item? This cannot be undone.')) return;
    fetch(CRED_API, {
      method: 'POST', credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete', id })
    })
      .then(r => r.json())
      .then(d => {
        if (d.success) { showAlert('success', 'Deleted.'); loadCredentials(); }
        else showAlert('danger', d.message || 'Delete failed.');
      }).catch(() => showAlert('danger', 'Network error.'));
  }

  function showCredAlert(type, msg) {
    const el = document.getElementById('credUploadAlert');
    if (!el) return;
    const colors = { success:['rgba(40,167,69,0.1)','#28A745','rgba(40,167,69,0.25)'],
                     danger: ['rgba(220,53,69,0.1)', '#dc3545','rgba(220,53,69,0.25)'],
                     info:   ['rgba(59,130,246,0.08)','#3b82f6','rgba(59,130,246,0.2)'] };
    const [bg, color, border] = colors[type] || colors.info;
    el.style.cssText = `display:flex;align-items:center;gap:0.4rem;background:${bg};color:${color};border:1px solid ${border};padding:0.5rem 0.85rem;border-radius:8px;font-size:0.82rem;font-weight:600;margin-top:0.75rem;`;
    el.innerHTML = msg;
    if (type !== 'info') setTimeout(() => { el.style.display = 'none'; }, 4000);
  }

  // Credentials are loaded in DOMContentLoaded above via loadCredentials()
  </script>

</body>
</html>




