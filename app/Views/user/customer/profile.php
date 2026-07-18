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
  <title>Fix&amp;Go — My Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8.1" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5.1" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .cu-layout { display: flex; min-height: calc(100vh - 68px); }
    .cu-sidebar { width: 260px; flex-shrink: 0; background: var(--fg-card-bg); border-right: 1px solid var(--fg-border); padding: 1.5rem 0 2rem; position: sticky; top: 68px; height: calc(100vh - 68px); overflow-y: auto; }
    .sidebar-profile { display: flex; align-items: center; gap: 0.85rem; padding: 0 1.25rem 1.25rem; border-bottom: 1px solid var(--fg-border); margin-bottom: 0.75rem; }
    .sidebar-avatar { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, rgba(230,168,0,0.25), rgba(230,168,0,0.08)); border: 2px solid rgba(230,168,0,0.35); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--fg-primary); font-weight: 800; flex-shrink: 0; overflow: hidden; }
    .sidebar-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block; }
    .sidebar-profile-name { font-size: 0.9rem; font-weight: 700; color: var(--fg-text); }
    .sidebar-profile-edit { font-size: 0.75rem; color: var(--fg-primary); text-decoration: none; font-weight: 600; }
    .sidebar-section-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--fg-muted); padding: 0.75rem 1.25rem 0.35rem; margin-top: 0.25rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav li a { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 1.25rem; color: var(--fg-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.2s; }
    .sidebar-nav li a:hover { color: var(--fg-primary); background: rgba(230,168,0,0.07); border-left-color: var(--fg-primary); }
    .sidebar-nav li a.active { color: var(--fg-primary); background: rgba(230,168,0,0.1); border-left-color: var(--fg-primary); font-weight: 700; }
    .sidebar-nav li a i { font-size: 1rem; width: 20px; text-align: center; }
    .sidebar-badge { margin-left: auto; background: #dc3545; color: #fff; font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.45rem; border-radius: 20px; min-width: 18px; text-align: center; }
    .cu-main { flex: 1; padding: 2rem; min-width: 0; }
    .page-header { margin-bottom: 1.75rem; }
    .page-header h2 { font-size: 1.5rem; font-weight: 800; color: var(--fg-text); margin: 0 0 0.25rem; }
    .page-header p { color: var(--fg-muted); margin: 0; font-size: 0.88rem; }
    .profile-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start; }
    .profile-card { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 16px; padding: 2rem 1.5rem; text-align: center; }
    .profile-avatar-wrap { position: relative; display: inline-block; margin-bottom: 1rem; }
    .profile-avatar { width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, rgba(230,168,0,0.25), rgba(230,168,0,0.08)); border: 3px solid rgba(230,168,0,0.35); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--fg-primary); font-weight: 800; margin: 0 auto; overflow: hidden; }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .profile-avatar-edit { position: absolute; bottom: 4px; right: 4px; width: 28px; height: 28px; border-radius: 50%; background: var(--fg-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; cursor: pointer; border: 2px solid var(--fg-card-bg); transition: all 0.2s; }
    .profile-avatar-edit:hover { background: var(--fg-primary-dark); transform: scale(1.1); }
    .profile-name { font-size: 1.15rem; font-weight: 800; color: var(--fg-text); margin-bottom: 0.25rem; }
    .profile-email { font-size: 0.82rem; color: var(--fg-muted); margin-bottom: 1rem; }
    .profile-info-row { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0; border-bottom: 1px solid var(--fg-border); text-align: left; }
    .profile-info-row:last-child { border-bottom: none; }
    .profile-info-icon { width: 34px; height: 34px; border-radius: 8px; background: rgba(230,168,0,0.1); color: var(--fg-primary); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0; }
    .profile-info-label { font-size: 0.7rem; color: var(--fg-muted); font-weight: 700; text-transform: uppercase; }
    .profile-info-value { font-size: 0.88rem; color: var(--fg-text); font-weight: 600; }
    .form-section { background: var(--fg-card-bg); border: 1px solid var(--fg-border); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.25rem; }
    .form-section h6 { font-weight: 800; font-size: 1rem; color: var(--fg-text); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label { display: block; font-size: 0.82rem; font-weight: 700; color: var(--fg-text); margin-bottom: 0.4rem; }
    .form-group label span { color: #dc3545; margin-left: 2px; }
    .form-input, .form-select { width: 100%; padding: 0.65rem 0.9rem; border: 1.5px solid var(--fg-border); border-radius: 10px; background: var(--fg-bg); color: var(--fg-text); font-size: 0.88rem; outline: none; transition: border-color 0.2s; font-family: inherit; }
    .form-input:focus, .form-select:focus { border-color: var(--fg-primary); box-shadow: 0 0 0 3px rgba(230,168,0,0.15); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .alert-bar { padding: 0.75rem 1.25rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem; }
    .alert-success { background: rgba(40,167,69,0.12); color: #28A745; border: 1px solid rgba(40,167,69,0.25); }
    .alert-danger  { background: rgba(220,53,69,0.12);  color: #dc3545; border: 1px solid rgba(220,53,69,0.25); }
    .sidebar-toggle { display: none; background: none; border: 1.5px solid var(--fg-border); border-radius: 8px; padding: 0.3rem 0.6rem; color: var(--fg-text); cursor: pointer; font-size: 1.1rem; }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-overlay.open { display: block; }
    @media (max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } }
    @media (max-width: 768px) {
      .sidebar-toggle { display: flex; align-items: center; }
      .cu-sidebar { position: fixed; top: 68px; left: 0; z-index: 200; transform: translateX(-100%); height: calc(100vh - 68px); box-shadow: 4px 0 20px rgba(0,0,0,0.15); transition: transform 0.3s; }
      .cu-sidebar.open { transform: translateX(0); }
      .cu-main { padding: 1.25rem; }
      .form-row { grid-template-columns: 1fr; }
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
      <span class="role-badge customer">👤 Customer</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="/index.php?browse=1" class="btn btn-sm" style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;">
        <i class="bi bi-shop"></i> Browse Shop
      </a>
      <a href="messages.php" style="position:relative;text-decoration:none;" title="Messages">
        <div style="background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-text);transition:all 0.2s;" onmouseenter="this.style.borderColor='var(--fg-primary)';this.style.color='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-text)'">
          <i class="bi bi-chat-dots-fill"></i>
        </div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:var(--fg-primary);color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <button onclick="customerLogout()" class="btn btn-sm"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
      <!-- Notification Bell -->
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

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

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
        <li><a href="profile.php" class="active"><i class="bi bi-person-circle"></i> Profile</a></li>
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
        <li><a href="/index.php?browse=1"><i class="bi bi-shop-window"></i> Browse Shop</a></li>
        <li><a href="seller-centre.php"><i class="bi bi-shop-window"></i> Seller Centre</a></li>
        <li><a href="become-technician.php"><i class="bi bi-wrench-adjustable-circle-fill"></i> Become a Technician</a></li>
      </ul>
    </aside>

    <main class="cu-main">
      <div class="page-header">
        <h2><i class="bi bi-person-circle" style="color:var(--fg-primary);margin-right:0.5rem;"></i>My Profile</h2>
        <p>Manage and protect your account</p>
      </div>

      <div id="alertBox" style="display:none;"></div>

      <div class="profile-grid">
        <!-- Left: avatar + info card -->
        <div class="profile-card">
          <div class="profile-avatar-wrap">
            <div class="profile-avatar" id="profileAvatar">?</div>
            <label for="avatarInput" class="profile-avatar-edit" title="Change photo">
              <i class="bi bi-camera-fill"></i>
            </label>
            <input type="file" id="avatarInput" accept="image/*" style="display:none;">
          </div>
          <div class="profile-name" id="profileFullName">—</div>
          <div class="profile-email" id="profileEmailDisplay">—</div>
          <span class="role-badge customer" style="margin:0 auto 1.25rem;display:inline-flex;">👤 Customer</span>
          <hr style="border-color:var(--fg-border);margin:0 0 1rem;">
          <div class="profile-info-row">
            <div class="profile-info-icon"><i class="bi bi-telephone-fill"></i></div>
            <div><div class="profile-info-label">Phone</div><div class="profile-info-value" id="profilePhoneDisplay">Not set</div></div>
          </div>
          <div class="profile-info-row">
            <div class="profile-info-icon"><i class="bi bi-calendar-fill"></i></div>
            <div><div class="profile-info-label">Member Since</div><div class="profile-info-value" id="profileJoined">—</div></div>
          </div>
          <div class="profile-info-row">
            <div class="profile-info-icon" style="background:rgba(40,167,69,0.1);color:#28A745;"><i class="bi bi-shield-check-fill"></i></div>
            <div><div class="profile-info-label">Account Status</div><div class="profile-info-value" style="color:#28A745;">Active</div></div>
          </div>
        </div>

        <!-- Right: edit forms -->
        <div>
          <!-- Edit Profile -->
          <div class="form-section">
            <h6><i class="bi bi-pencil-fill" style="color:var(--fg-primary);"></i> Edit Profile</h6>
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
                <label>Email Address <span>*</span></label>
                <input type="email" class="form-input" id="editEmail" required>
              </div>
              <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" class="form-input" id="editPhone" placeholder="+63 9XX XXX XXXX">
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Gender</label>
                  <select class="form-select" id="editGender">
                    <option value="">Prefer not to say</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Date of Birth</label>
                  <input type="date" class="form-input" id="editDob">
                </div>
              </div>
              <button type="submit" class="btn-primary-custom" id="btnSaveProfile">
                <i class="bi bi-check-circle-fill"></i> Save Changes
              </button>
            </form>
          </div>

          <!-- Delivery Address -->
          <div class="form-section" id="addressSection">
            <h6><i class="bi bi-geo-alt-fill" style="color:#dc3545;"></i> Delivery Address
              <span id="addressVerifiedBadge" style="display:none;background:rgba(40,167,69,0.12);color:#28A745;font-size:0.7rem;padding:0.2rem 0.6rem;border-radius:20px;font-weight:700;margin-left:0.5rem;"><i class="bi bi-check-circle-fill"></i> Verified</span>
              <span id="addressMissingBadge" style="display:none;background:rgba(220,53,69,0.12);color:#dc3545;font-size:0.7rem;padding:0.2rem 0.6rem;border-radius:20px;font-weight:700;margin-left:0.5rem;"><i class="bi bi-exclamation-circle-fill"></i> Required for checkout</span>
            </h6>
            <div id="addressAlert" style="display:none;margin-bottom:1rem;"></div>
            <form id="addressForm">
              <div class="form-group">
                <label>Phone Number <span>*</span></label>
                <input type="tel" class="form-input" id="addrPhone" placeholder="+63 9XX XXX XXXX" required>
              </div>
              <div class="form-group">
                <label>Street / House No. / Building <span>*</span></label>
                <input type="text" class="form-input" id="addrLine" placeholder="e.g. Purok 2, Sirawan Toril" required>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Barangay <span>*</span></label>
                  <input type="text" class="form-input" id="addrBarangay" required>
                </div>
                <div class="form-group">
                  <label>City / Municipality <span>*</span></label>
                  <input type="text" class="form-input" id="addrCity" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Province <span>*</span></label>
                  <input type="text" class="form-input" id="addrProvince" required>
                </div>
                <div class="form-group">
                  <label>Region</label>
                  <input type="text" class="form-input" id="addrRegion" placeholder="e.g. Mindanao">
                </div>
              </div>
              <div class="form-group">
                <label>ZIP Code <span>*</span></label>
                <input type="text" class="form-input" id="addrZip" placeholder="e.g. 8000" maxlength="10" required>
              </div>
              <button type="submit" class="btn-primary-custom" id="btnSaveAddress" style="background:#dc3545;">
                <i class="bi bi-geo-alt-fill"></i> Save Address
              </button>
            </form>
          </div>

          <!-- Change Password -->
          <div class="form-section">
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
              <button type="submit" class="btn-primary-custom" style="margin-top:1.25rem;background:#dc3545;" id="btnChangePass">
                <i class="bi bi-lock-fill"></i> Change Password
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
  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'customer') { window.location.href = '/login.html'; return; }

    // Sidebar toggle
    const sidebar = document.getElementById('cuSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    // Load full profile from database
    loadProfile();

    // Avatar upload
    document.getElementById('avatarInput').addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;
      if (file.size > 3 * 1024 * 1024) { showAlert('danger', 'Image must be under 3MB.'); return; }

      // Preview immediately
      const reader = new FileReader();
      reader.onload = e => {
        const av = document.getElementById('profileAvatar');
        av.innerHTML = `<img src="${e.target.result}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
        const sidebarAv = document.getElementById('sidebarAvatarInitials');
        sidebarAv.innerHTML = `<img src="${e.target.result}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
      };
      reader.readAsDataURL(file);

      // Upload to server
      const fd = new FormData();
      fd.append('action', 'upload_avatar');
      fd.append('avatar', file);
      fetch('/api/customer/profile', { method: 'POST', body: fd, credentials: 'include' })
        .then(r => r.json())
        .then(d => {
          if (d.success) {
            showAlert('success', 'Profile photo updated!');
            const updated = Object.assign({}, FGAuth.UserStore.get(), { avatar_url: d.avatar_url });
            FGAuth.UserStore.save(updated);
          } else {
            showAlert('danger', d.message || 'Upload failed.');
          }
        }).catch(() => showAlert('danger', 'Upload failed. Please try again.'));
    });

    // Save profile
    document.getElementById('profileForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const fn = document.getElementById('editFirstName').value.trim();
      const ln = document.getElementById('editLastName').value.trim();
      const ph = document.getElementById('editPhone').value.trim();
      const gn = document.getElementById('editGender').value;
      const db = document.getElementById('editDob').value;
      if (!fn || !ln) { showAlert('danger', 'First and last name are required.'); return; }

      const btn = document.getElementById('btnSaveProfile');
      btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';

      fetch('/api/customer/profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ action: 'update_profile', first_name: fn, last_name: ln, phone: ph, gender: gn, dob: db })
      })
        .then(r => r.json())
        .then(d => {
          if (!d.success) { showAlert('danger', d.message || 'Update failed.'); return; }
          const updated = Object.assign({}, FGAuth.UserStore.get(), { firstName: fn, lastName: ln, phone: ph });
          FGAuth.UserStore.save(updated);
          document.getElementById('profileFullName').textContent = (fn + ' ' + ln).trim();
          document.getElementById('profilePhoneDisplay').textContent = ph || 'Not set';
          document.getElementById('navUserName').textContent = (fn + ' ' + ln).trim();
          document.getElementById('sidebarName').textContent = (fn + ' ' + ln).trim();
          // Update initials if no avatar
          const av = document.getElementById('profileAvatar');
          if (!av.querySelector('img')) {
            const ni = ((fn[0]||'') + (ln[0]||'')).toUpperCase();
            av.textContent = ni;
            document.getElementById('sidebarAvatarInitials').textContent = ni;
          }
          showAlert('success', 'Profile updated successfully!');
        })
        .catch(() => showAlert('danger', 'Network error. Please try again.'))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Save Changes'; });
    });

    // Save address
    document.getElementById('addressForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const btn = document.getElementById('btnSaveAddress');
      btn.disabled = true;
      btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';

      fetch('/api/customer/profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          action:       'update_address',
          phone:        document.getElementById('addrPhone').value.trim(),
          address_line: document.getElementById('addrLine').value.trim(),
          barangay:     document.getElementById('addrBarangay').value.trim(),
          city:         document.getElementById('addrCity').value.trim(),
          province:     document.getElementById('addrProvince').value.trim(),
          region:       document.getElementById('addrRegion').value.trim(),
          zip_code:     document.getElementById('addrZip').value.trim(),
        })
      })
        .then(r => r.json())
        .then(d => {
          if (d.success) {
            showAlert('success', 'Address saved! You can now checkout.');
            document.getElementById('addressVerifiedBadge').style.display = 'inline-flex';
            document.getElementById('addressMissingBadge').style.display  = 'none';
          } else {
            const addrAlert = document.getElementById('addressAlert');
            addrAlert.style.display = 'flex';
            addrAlert.className = 'alert-bar alert-danger';
            addrAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + esc(d.message);
          }
        })
        .catch(() => showAlert('danger', 'Network error. Please try again.'))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Save Address'; });
    });

    // Change password — real backend call
    document.getElementById('passwordForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const cur = document.getElementById('currentPassword').value;
      const np  = document.getElementById('newPassword').value;
      const cp  = document.getElementById('confirmPassword').value;
      if (np.length < 8) { showAlert('danger', 'New password must be at least 8 characters.'); return; }
      if (np !== cp)     { showAlert('danger', 'Passwords do not match.'); return; }

      const btn = document.getElementById('btnChangePass');
      btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Changing…';

      fetch('/api/customer/profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ action: 'change_password', current_password: cur, new_password: np, confirm_password: cp })
      })
        .then(r => r.json())
        .then(d => {
          if (d.success) {
            showAlert('success', 'Password changed successfully!');
            document.getElementById('passwordForm').reset();
          } else {
            showAlert('danger', d.message || 'Password change failed.');
          }
        })
        .catch(() => showAlert('danger', 'Network error. Please try again.'))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-lock-fill"></i> Change Password'; });
    });

    loadUnreadMessageCount();
  });

  function loadProfile() {
    fetch('/api/customer/profile?action=get', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        const u = d.user;

        // Names & basic info
        const fullName = ((u.first_name||'') + ' ' + (u.last_name||'')).trim();
        document.getElementById('navUserName').textContent = fullName || u.email;
        document.getElementById('sidebarName').textContent = fullName || u.email;
        document.getElementById('profileFullName').textContent = fullName || '—';
        document.getElementById('profileEmailDisplay').textContent = u.email || '—';
        document.getElementById('profilePhoneDisplay').textContent = u.phone || 'Not set';
        document.getElementById('profileJoined').textContent = u.created_at
          ? new Date(u.created_at).toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' })
          : 'N/A';

        // Avatar
        const initials = ((u.first_name||'')[0]||'') + ((u.last_name||'')[0]||'');
        if (u.avatar_url) {
          const imgHtml = `<img src="../../../${esc(u.avatar_url)}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
          document.getElementById('profileAvatar').innerHTML = imgHtml;
          document.getElementById('sidebarAvatarInitials').innerHTML = `<img src="../../../${esc(u.avatar_url)}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
        } else {
          const ini = initials.toUpperCase() || '?';
          document.getElementById('profileAvatar').textContent = ini;
          document.getElementById('sidebarAvatarInitials').textContent = ini;
        }

        // Form fields
        document.getElementById('editFirstName').value = u.first_name || '';
        document.getElementById('editLastName').value  = u.last_name  || '';
        document.getElementById('editEmail').value     = u.email      || '';
        document.getElementById('editPhone').value     = u.phone      || '';
        if (u.gender)         document.getElementById('editGender').value = u.gender;
        if (u.date_of_birth)  document.getElementById('editDob').value    = u.date_of_birth;

        // Address
        if (u.address_line) document.getElementById('addrLine').value     = u.address_line;
        if (u.barangay)     document.getElementById('addrBarangay').value = u.barangay;
        if (u.city)         document.getElementById('addrCity').value     = u.city;
        if (u.province)     document.getElementById('addrProvince').value = u.province;
        if (u.region)       document.getElementById('addrRegion').value   = u.region;
        if (u.zip_code)     document.getElementById('addrZip').value      = u.zip_code;
        if (u.phone)        document.getElementById('addrPhone').value    = u.phone;

        if (d.address_complete) {
          document.getElementById('addressVerifiedBadge').style.display = 'inline-flex';
          document.getElementById('addressMissingBadge').style.display  = 'none';
        } else {
          document.getElementById('addressVerifiedBadge').style.display = 'none';
          document.getElementById('addressMissingBadge').style.display  = 'inline-flex';
        }

        // Update session store
        const stored = FGAuth.UserStore.get() || {};
        FGAuth.UserStore.save(Object.assign(stored, {
          firstName: u.first_name, lastName: u.last_name,
          email: u.email, phone: u.phone, avatar_url: u.avatar_url
        }));
      }).catch(() => {});
  }

  function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  function showAlert(type, msg) {
    const box = document.getElementById('alertBox');
    box.style.display = 'flex';
    box.className = 'alert-bar alert-' + type;
    box.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill') + '"></i> ' + msg;
    setTimeout(() => { box.style.display = 'none'; }, 5000);
  }

  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('/api/logout').finally(() => {
        window.location.href = '/login.html';
      });
    });
  }

  function loadUnreadMessageCount() {
    fetch('/api/messages?action=unread_count', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success && d.count > 0) {
          const badge = document.getElementById('navMsgBadge');
          if (badge) { badge.textContent = d.count > 99 ? '99+' : d.count; badge.style.display = 'inline-block'; }
        }
      }).catch(() => {});
    setTimeout(loadUnreadMessageCount, 10000);
  }
  </script>

</body>
</html>




