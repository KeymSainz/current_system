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
  <title>Fix&amp;Go — Settings</title>
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
    .section-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.5rem;margin-bottom:1.5rem;max-width:560px;}
    .section-card h6{font-weight:800;font-size:1rem;color:var(--fg-text);margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;}
    .form-group{margin-bottom:1.1rem;}
    .form-group label{display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.4rem;}
    .form-group label span{color:#dc3545;margin-left:2px;}
    .form-input{width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;font-family:inherit;}
    .form-input:focus{border-color:var(--fg-primary);box-shadow:0 0 0 3px rgba(230,168,0,0.15);}
    .alert-bar{padding:0.75rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;}
    .alert-success{background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);}
    .alert-danger{background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @media(max-width:768px){.sidebar-toggle{display:flex;align-items:center;}.sp-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}.sp-sidebar.open{transform:translateX(0);}.sp-main{padding:1.25rem;}}
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
        <li><a href="profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php" class="active"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>
    <main class="sp-main">
      <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;"><i class="bi bi-gear-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Settings</h2>
        <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">Manage your account security settings</p>
      </div>
      <div id="alertBox" style="display:none;"></div>
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
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script>
  'use strict';
  function escHtml(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
  document.addEventListener('DOMContentLoaded',function(){
    const user=FGAuth.UserStore.get();
    if(!user||user.role!=='sales_person'){window.location.href='/login.html';return;}
    const companyName=user.shopName||((user.firstName||'')+' '+(user.lastName||'')).trim()||'My Company';
    document.getElementById('navUserName').textContent=companyName;
    const sidebar=document.getElementById('spSidebar'),overlay=document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click',()=>{sidebar.classList.toggle('open');overlay.classList.toggle('open');});
    overlay.addEventListener('click',()=>{sidebar.classList.remove('open');overlay.classList.remove('open');});
    document.getElementById('passwordForm').addEventListener('submit',function(e){
      e.preventDefault();
      const current=document.getElementById('currentPassword').value;
      const newPass=document.getElementById('newPassword').value;
      const confirm=document.getElementById('confirmPassword').value;
      if(newPass.length<8){showAlert('danger','New password must be at least 8 characters.');return;}
      if(newPass!==confirm){showAlert('danger','Passwords do not match.');return;}
      const btn=document.getElementById('btnChangePass');
      btn.disabled=true;btn.innerHTML='<i class="bi bi-hourglass-split"></i> Changing…';
      fetch('/api/profile',{
        method:'POST',headers:{'Content-Type':'application/json'},credentials:'include',
        body:JSON.stringify({action:'change_password',current_password:current,new_password:newPass,confirm_password:confirm})
      }).then(r=>r.json()).then(d=>{
        if(!d.success)throw new Error(d.message||'Password change failed.');
        showAlert('success','Password changed successfully!');
        document.getElementById('passwordForm').reset();
      }).catch(err=>showAlert('danger',err.message))
      .finally(()=>{btn.disabled=false;btn.innerHTML='<i class="bi bi-lock-fill"></i> Change Password';});
    });
  });
  function showAlert(type,msg){const box=document.getElementById('alertBox');box.style.display='flex';box.className='alert-bar alert-'+type;box.innerHTML='<i class="bi bi-'+(type==='success'?'check-circle-fill':'exclamation-triangle-fill')+'"></i> '+escHtml(msg);setTimeout(()=>{box.style.display='none';},5000);}
  </script>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>

