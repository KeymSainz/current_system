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
  <title>Fix&amp;Go — Company Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=5" />
  <link rel="stylesheet" href="/assets/css/supplier.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body{background:var(--fg-bg);}
    .sp-layout{display:flex;min-height:calc(100vh - 68px);}
    .sp-sidebar{width:240px;flex-shrink:0;background:var(--fg-card-bg);border-right:1px solid var(--fg-border);padding:1.5rem 0;position:sticky;top:68px;height:calc(100vh - 68px);overflow-y:auto;}
    .sidebar-label{font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);padding:0 1.25rem;margin-bottom:0.5rem;}
    .sidebar-nav{list-style:none;padding:0;margin:0;}
    .sidebar-nav a{display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1.25rem;color:var(--fg-muted);text-decoration:none;font-size:0.88rem;font-weight:500;border-left:3px solid transparent;transition:all 0.2s;}
    .sidebar-nav a:hover{color:var(--fg-primary);background:rgba(230,168,0,0.07);border-left-color:var(--fg-primary);}
    .sidebar-nav a.active{color:var(--fg-primary);background:rgba(230,168,0,0.1);border-left-color:var(--fg-primary);font-weight:700;}
    .sidebar-nav a i{font-size:1rem;width:20px;text-align:center;}
    .sp-main{flex:1;padding:2rem;min-width:0;}
    .profile-grid{display:grid;grid-template-columns:300px 1fr;gap:1.5rem;align-items:start;}
    .section-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.5rem;margin-bottom:1.5rem;}
    .section-card h6{font-weight:800;font-size:1rem;color:var(--fg-text);margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;}
    .company-avatar{width:88px;height:88px;border-radius:16px;background:linear-gradient(135deg,var(--fg-primary),#c98f00);display:flex;align-items:center;justify-content:center;font-size:2.2rem;color:#fff;font-weight:800;margin:0 auto 1rem;overflow:hidden;}
    .company-avatar img{width:100%;height:100%;object-fit:cover;border-radius:16px;}
    .company-name{text-align:center;font-size:1.2rem;font-weight:800;color:var(--fg-text);margin-bottom:0.25rem;}
    .company-email{text-align:center;font-size:0.85rem;color:var(--fg-muted);margin-bottom:1rem;}
    .info-row{display:flex;align-items:flex-start;gap:0.75rem;padding:0.65rem 0;border-bottom:1px solid var(--fg-border);}
    .info-row:last-child{border-bottom:none;}
    .info-icon{width:36px;height:36px;border-radius:8px;background:rgba(230,168,0,0.1);color:var(--fg-primary);display:flex;align-items:center;justify-content:center;font-size:0.95rem;flex-shrink:0;}
    .info-label{font-size:0.72rem;color:var(--fg-muted);font-weight:700;text-transform:uppercase;}
    .info-value{font-size:0.88rem;color:var(--fg-text);font-weight:600;line-height:1.5;}
    .form-group{margin-bottom:1.1rem;}
    .form-group label{display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.4rem;}
    .form-group label span{color:#dc3545;margin-left:2px;}
    .form-input{width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;font-family:inherit;}
    .form-input:focus{border-color:var(--fg-primary);box-shadow:0 0 0 3px rgba(230,168,0,0.15);}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
    .alert-bar{padding:0.75rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;}
    .alert-success{background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);}
    .alert-danger{background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @media(max-width:900px){.profile-grid{grid-template-columns:1fr;}}
    @media(max-width:768px){.sidebar-toggle{display:flex;align-items:center;}.sp-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}.sp-sidebar.open{transform:translateX(0);}.sp-main{padding:1.25rem;}.form-row{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;" onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge sales_person">&#x1F4BC; Sales Person</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="messages.php" style="position:relative;text-decoration:none;" title="Messages">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);transition:all 0.2s;" onmouseenter="this.style.borderColor='var(--fg-primary)';this.style.color='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-text)'"><i class="bi bi-chat-dots-fill"></i></div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:var(--fg-primary);color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <a href="/dashboard.php" class="btn btn-sm" style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
  </nav>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="sp-layout">
    <aside class="sp-sidebar" id="spSidebar">
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="orders.php"><i class="bi bi-cart3"></i> Customer Orders</a></li>
        <li><a href="inventory.php"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="profile.php" class="active"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>
    <main class="sp-main">
      <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;"><i class="bi bi-building" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Company Profile</h2>
        <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">Manage your company information and shop address</p>
      </div>
      <div id="alertBox" style="display:none;"></div>
      <div class="profile-grid">
        <!-- Left: Company card -->
        <div>
          <div class="section-card" style="text-align:center;">
            <div style="position:relative;display:inline-block;margin-bottom:1rem;">
              <div class="company-avatar" id="avatarEl">🏪</div>
              <label for="avatarInput" style="position:absolute;bottom:2px;right:2px;width:28px;height:28px;border-radius:50%;background:var(--fg-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.7rem;cursor:pointer;border:2px solid var(--fg-card-bg);" title="Change logo"><i class="bi bi-camera-fill"></i></label>
              <input type="file" id="avatarInput" accept="image/*" style="display:none;">
            </div>
            <div class="company-name" id="profileCompanyName">Loading…</div>
            <div class="company-email" id="profileEmail">—</div>
            <span class="role-badge sales_person" style="margin:0 auto;display:inline-flex;">&#x1F4BC; Sales Person</span>
            <hr style="border-color:var(--fg-border);margin:1rem 0;">
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
            <div class="info-row" id="addrVerifiedRow" style="display:none;">
              <div class="info-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;"><i class="bi bi-geo-alt-fill"></i></div>
              <div>
                <div class="info-label">Shop Address</div>
                <div class="info-value" id="profileAddressDisplay" style="font-size:0.82rem;line-height:1.5;">—</div>
                <span id="addrVerifiedBadge" style="display:none;background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);padding:0.1rem 0.5rem;border-radius:20px;font-size:0.68rem;font-weight:700;margin-top:0.25rem;display:inline-flex;align-items:center;gap:0.25rem;"><i class="bi bi-patch-check-fill"></i> Verified</span>
              </div>
            </div>
          </div>
          <div class="section-card" style="text-align:center;padding:1rem;">
            <a href="settings.php" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.55rem 1.25rem;border-radius:10px;background:rgba(220,53,69,0.08);border:1.5px solid rgba(220,53,69,0.3);color:#dc3545;text-decoration:none;font-size:0.85rem;font-weight:700;transition:all 0.2s;width:100%;justify-content:center;" onmouseenter="this.style.background='rgba(220,53,69,0.15)'" onmouseleave="this.style.background='rgba(220,53,69,0.08)'">
              <i class="bi bi-lock-fill"></i> Change Password → Settings
            </a>
          </div>
        </div>
        <!-- Right: Edit forms -->
        <div>
          <!-- Company Info -->
          <div class="section-card" style="margin-bottom:1.5rem;">
            <h6><i class="bi bi-building" style="color:var(--fg-primary);"></i> Company Information</h6>
            <form id="profileForm">
              <div class="form-group">
                <label>Company / Shop Name <span>*</span></label>
                <input type="text" class="form-input" id="editCompanyName" placeholder="e.g. QuickFix Mobile Repair" required>
              </div>
              <div class="form-group">
                <label>Email <span>*</span></label>
                <input type="email" class="form-input" id="editEmail" required>
              </div>
              <div class="form-group" style="margin-bottom:0;">
                <label>Phone</label>
                <input type="tel" class="form-input" id="editPhone" placeholder="+63 9XX XXX XXXX">
              </div>
              <button type="submit" class="btn-primary-custom" style="margin-top:1.25rem;" id="btnSaveProfile">
                <i class="bi bi-check-circle-fill"></i> Save Company Info
              </button>
            </form>
          </div>
          <!-- Shop Address -->
          <div class="section-card">
            <h6 style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;">
              <span><i class="bi bi-geo-alt-fill" style="color:#3b82f6;"></i> Shop Address</span>
              <span id="addrVerifiedTag" style="display:none;background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:700;align-items:center;gap:0.3rem;"><i class="bi bi-patch-check-fill"></i> Address Verified</span>
            </h6>
            <p style="font-size:0.82rem;color:var(--fg-muted);margin:0 0 1.1rem;line-height:1.6;">
              This address is used as the <strong>seller origin</strong> when customers track their delivery on the map.
              Fill in all fields to get a <strong style="color:#28A745;">Verified</strong> badge.
              <span style="color:#3b82f6;font-weight:600;">🇵🇭 Philippines addresses only.</span>
            </p>
            <form id="addressForm">
              <div class="form-group">
                <label>House / Unit / Street <span>*</span></label>
                <input type="text" class="form-input" id="editAddressLine" placeholder="e.g. 123 Rizal St., Purok 5" required>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Barangay <span>*</span></label>
                  <input type="text" class="form-input" id="editBarangay" placeholder="e.g. Barangay Poblacion" required>
                </div>
                <div class="form-group">
                  <label>ZIP Code</label>
                  <input type="text" class="form-input" id="editZipCode" placeholder="e.g. 8000" maxlength="10">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>City / Municipality <span>*</span></label>
                  <input type="text" class="form-input" id="editCity" placeholder="e.g. Davao City" required>
                </div>
                <div class="form-group">
                  <label>Province <span>*</span></label>
                  <input type="text" class="form-input" id="editProvince" placeholder="e.g. Davao del Sur" required>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:0;">
                <label>Region</label>
                <select class="form-input" id="editRegion">
                  <option value="">— Select Region —</option>
                  <option value="NCR">NCR — National Capital Region</option>
                  <option value="CAR">CAR — Cordillera Administrative Region</option>
                  <option value="Region I">Region I — Ilocos Region</option>
                  <option value="Region II">Region II — Cagayan Valley</option>
                  <option value="Region III">Region III — Central Luzon</option>
                  <option value="Region IV-A">Region IV-A — CALABARZON</option>
                  <option value="Region IV-B">Region IV-B — MIMAROPA</option>
                  <option value="Region V">Region V — Bicol Region</option>
                  <option value="Region VI">Region VI — Western Visayas</option>
                  <option value="Region VII">Region VII — Central Visayas</option>
                  <option value="Region VIII">Region VIII — Eastern Visayas</option>
                  <option value="Region IX">Region IX — Zamboanga Peninsula</option>
                  <option value="Region X">Region X — Northern Mindanao</option>
                  <option value="Region XI">Region XI — Davao Region</option>
                  <option value="Region XII">Region XII — SOCCSKSARGEN</option>
                  <option value="Region XIII">Region XIII — Caraga</option>
                  <option value="BARMM">BARMM — Bangsamoro</option>
                </select>
              </div>
              <div id="addrAlert" style="display:none;margin-top:1rem;"></div>
              <button type="submit" class="btn-primary-custom" style="margin-top:1.25rem;background:#3b82f6;border-color:#3b82f6;" id="btnSaveAddress">
                <i class="bi bi-geo-alt-fill"></i> Save Address
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="/assets/js/session-timeout.js"></script>
  <script>
  'use strict';
  function escHtml(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

  document.addEventListener('DOMContentLoaded', function(){
    const user = FGAuth.UserStore.get();
    if(!user||user.role!=='sales_person'){window.location.href='/login.html';return;}
    const sidebar=document.getElementById('spSidebar'),overlay=document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click',()=>{sidebar.classList.toggle('open');overlay.classList.toggle('open');});
    overlay.addEventListener('click',()=>{sidebar.classList.remove('open');overlay.classList.remove('open');});
    loadProfile();
    loadUnreadMessageCount();

    // Avatar upload
    document.getElementById('avatarInput').addEventListener('change',function(){
      const file=this.files[0];
      if(!file)return;
      if(file.size>3*1024*1024){showAlert('danger','Image must be under 3MB.');return;}
      const reader=new FileReader();
      reader.onload=e=>{const av=document.getElementById('avatarEl');av.innerHTML=`<img src="${e.target.result}" alt="logo" style="width:100%;height:100%;object-fit:cover;border-radius:16px;">`;};
      reader.readAsDataURL(file);
      const fd=new FormData();fd.append('action','upload_avatar');fd.append('avatar',file);
      fetch('/api/profile',{method:'POST',body:fd,credentials:'include'})
        .then(r=>r.json()).then(d=>{if(d.success){showAlert('success','Logo updated!');const upd=Object.assign({},FGAuth.UserStore.get(),{avatar_url:d.avatar_url});FGAuth.UserStore.save(upd);}else showAlert('danger',d.message||'Upload failed.');}).catch(()=>showAlert('danger','Upload failed.'));
    });

    // Company info form
    document.getElementById('profileForm').addEventListener('submit',function(e){
      e.preventDefault();
      const company=document.getElementById('editCompanyName').value.trim();
      const em=document.getElementById('editEmail').value.trim();
      const ph=document.getElementById('editPhone').value.trim();
      if(!company||!em){showAlert('danger','Company name and email are required.');return;}
      const btn=document.getElementById('btnSaveProfile');
      btn.disabled=true;btn.innerHTML='<i class="bi bi-hourglass-split"></i> Saving…';
      fetch('/api/profile',{
        method:'POST',headers:{'Content-Type':'application/json'},credentials:'include',
        body:JSON.stringify({action:'update_company_profile',company_name:company,email:em,phone:ph})
      }).then(r=>r.json()).then(d=>{
        if(!d.success)throw new Error(d.message||'Update failed.');
        const upd=Object.assign({},FGAuth.UserStore.get(),{shopName:company,email:em,phone:ph});
        FGAuth.UserStore.save(upd);
        document.getElementById('profileCompanyName').textContent=company;
        document.getElementById('profileEmail').textContent=em;
        document.getElementById('profilePhone').textContent=ph||'Not set';
        document.getElementById('navUserName').textContent=company;
        const av=document.getElementById('avatarEl');
        if(!av.querySelector('img'))av.textContent=company[0]||'🏪';
        showAlert('success','Company info updated!');
      }).catch(err=>showAlert('danger',err.message))
      .finally(()=>{btn.disabled=false;btn.innerHTML='<i class="bi bi-check-circle-fill"></i> Save Company Info';});
    });

    // Address form
    document.getElementById('addressForm').addEventListener('submit',function(e){
      e.preventDefault();
      const addressLine=document.getElementById('editAddressLine').value.trim();
      const barangay=document.getElementById('editBarangay').value.trim();
      const city=document.getElementById('editCity').value.trim();
      const province=document.getElementById('editProvince').value.trim();
      const region=document.getElementById('editRegion').value.trim();
      const zipCode=document.getElementById('editZipCode').value.trim();
      if(!addressLine||!barangay||!city||!province){showAddrAlert('danger','Please fill in Address Line, Barangay, City, and Province.');return;}
      const btn=document.getElementById('btnSaveAddress');
      btn.disabled=true;btn.innerHTML='<i class="bi bi-hourglass-split"></i> Saving…';
      fetch('/api/profile',{
        method:'POST',headers:{'Content-Type':'application/json'},credentials:'include',
        body:JSON.stringify({action:'update_address',address_line:addressLine,barangay,city,province,region,zip_code:zipCode})
      }).then(r=>r.json()).then(d=>{
        if(!d.success)throw new Error(d.message||'Failed to save address.');
        const addrParts=[addressLine,barangay,city,province,zipCode].filter(Boolean);
        document.getElementById('addrVerifiedRow').style.display='flex';
        document.getElementById('profileAddressDisplay').textContent=addrParts.join(', ');
        if(d.address_verified===1){
          document.getElementById('addrVerifiedBadge').style.display='inline-flex';
          document.getElementById('addrVerifiedTag').style.display='inline-flex';
          showAddrAlert('success','✅ Address saved and verified! Customers can now track deliveries from your location.');
        }else{
          document.getElementById('addrVerifiedBadge').style.display='none';
          document.getElementById('addrVerifiedTag').style.display='none';
          showAddrAlert('success','Address saved. Fill in all fields including ZIP code to get verified.');
        }
      }).catch(err=>showAddrAlert('danger',err.message))
      .finally(()=>{btn.disabled=false;btn.innerHTML='<i class="bi bi-geo-alt-fill"></i> Save Address';});
    });
  });

  function loadProfile(){
    fetch('/api/profile?action=get',{credentials:'include'})
      .then(r=>r.json()).then(d=>{
        if(!d.success)return;
        const u=d.user;
        const companyName=u.shop_name||((u.first_name||'')+' '+(u.last_name||'')).trim()||'My Company';
        document.getElementById('navUserName').textContent=companyName;
        document.getElementById('profileCompanyName').textContent=companyName;
        document.getElementById('profileEmail').textContent=u.email||'—';
        document.getElementById('profilePhone').textContent=u.phone||'Not set';
        document.getElementById('profileJoined').textContent=u.created_at?new Date(u.created_at).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}):'N/A';
        const av=document.getElementById('avatarEl');
        if(u.avatar_url){av.innerHTML=`<img src="../../../${escHtml(u.avatar_url)}" alt="logo" style="width:100%;height:100%;object-fit:cover;border-radius:16px;">`;}
        else{av.textContent=companyName[0]||'🏪';}
        document.getElementById('editCompanyName').value=u.shop_name||'';
        document.getElementById('editEmail').value=u.email||'';
        document.getElementById('editPhone').value=u.phone||'';
        document.getElementById('editAddressLine').value=u.address_line||'';
        document.getElementById('editBarangay').value=u.barangay||'';
        document.getElementById('editCity').value=u.city||'';
        document.getElementById('editProvince').value=u.province||'';
        document.getElementById('editRegion').value=u.region||'';
        document.getElementById('editZipCode').value=u.zip_code||'';
        const addrParts=[u.address_line,u.barangay,u.city,u.province,u.zip_code].filter(Boolean);
        if(addrParts.length){document.getElementById('addrVerifiedRow').style.display='flex';document.getElementById('profileAddressDisplay').textContent=addrParts.join(', ');}
        if(parseInt(u.address_verified)===1){document.getElementById('addrVerifiedBadge').style.display='inline-flex';document.getElementById('addrVerifiedTag').style.display='inline-flex';}
        const upd=Object.assign({},FGAuth.UserStore.get(),{shopName:u.shop_name,email:u.email,phone:u.phone,avatar_url:u.avatar_url});
        FGAuth.UserStore.save(upd);
      }).catch(()=>{});
  }

  function showAlert(type,msg){const box=document.getElementById('alertBox');box.style.display='flex';box.className='alert-bar alert-'+type;box.innerHTML='<i class="bi bi-'+(type==='success'?'check-circle-fill':'exclamation-triangle-fill')+'"></i> '+escHtml(msg);setTimeout(()=>{box.style.display='none';},5000);}
  function showAddrAlert(type,msg){const el=document.getElementById('addrAlert');el.style.display='flex';el.className='alert-bar alert-'+type;el.innerHTML='<i class="bi bi-'+(type==='success'?'check-circle-fill':'exclamation-triangle-fill')+'"></i> '+escHtml(msg);setTimeout(()=>{el.style.display='none';},6000);}
  function loadUnreadMessageCount(){fetch('/api/messages?action=unread_count',{credentials:'include'}).then(r=>r.json()).then(d=>{if(d.success&&d.count>0){const b=document.getElementById('navMsgBadge');if(b){b.textContent=d.count>99?'99+':d.count;b.style.display='inline-block';}}}).catch(()=>{});setTimeout(loadUnreadMessageCount,10000);}
  </script>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>

