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
  <title>Fix&amp;Go — My Wishlist</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5" />
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
    .section-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;margin-bottom:1.5rem;}
    .section-head{padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;}
    .section-head h6{margin:0;font-weight:700;font-size:0.95rem;color:var(--fg-text);}
    .empty-state{text-align:center;padding:4rem 2rem;color:var(--fg-muted);}
    .empty-state i{font-size:3rem;display:block;margin-bottom:1rem;opacity:0.3;}
    .empty-state p{font-size:0.9rem;margin:0 0 1rem;}
    /* Product grid */
    .product-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;padding:1.25rem;}
    .product-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:12px;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;}
    .product-card:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(0,0,0,0.1);}
    .product-img{width:100%;aspect-ratio:1;background:var(--fg-bg);display:flex;align-items:center;justify-content:center;font-size:3rem;color:var(--fg-muted);border-bottom:1px solid var(--fg-border);}
    .product-img img{width:100%;height:100%;object-fit:cover;}
    .product-body{padding:0.9rem;}
    .product-name{font-size:0.88rem;font-weight:700;color:var(--fg-text);margin-bottom:0.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .product-price{font-size:1rem;font-weight:800;color:var(--fg-primary);margin-bottom:0.75rem;}
    .product-actions{display:flex;gap:0.5rem;}
    .btn-remove{flex:1;padding:0.45rem;border:1.5px solid rgba(220,53,69,0.3);border-radius:8px;background:rgba(220,53,69,0.06);color:#dc3545;font-size:0.78rem;font-weight:700;cursor:pointer;transition:all 0.2s;}
    .btn-remove:hover{background:rgba(220,53,69,0.12);border-color:#dc3545;}
    .btn-buy{flex:2;padding:0.45rem;border:none;border-radius:8px;background:var(--fg-primary);color:#fff;font-size:0.78rem;font-weight:700;cursor:pointer;transition:opacity 0.2s;}
    .btn-buy:hover{opacity:0.88;}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @media(max-width:992px){.product-grid{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .cu-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .cu-sidebar.open{transform:translateX(0);}
      .cu-main{padding:1.25rem;}
      .product-grid{grid-template-columns:1fr;}
    }
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
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

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
        <li><a href="orders.php"><i class="bi bi-bag-fill"></i> My Orders</a></li>
        <li><a href="repairs.php"><i class="bi bi-tools"></i> My Repairs</a></li>
        <li><a href="wishlist.php" class="active"><i class="bi bi-heart-fill"></i> Wishlist</a></li>
        <li><a href="vouchers.php"><i class="bi bi-ticket-perforated-fill"></i> My Vouchers</a></li>
      </ul>
      <div class="sidebar-section-label">Fix&amp;Go</div>
      <ul class="sidebar-nav">
        <li><a href="/index.php?browse=1"><i class="bi bi-shop-window"></i> Browse Shop</a></li>
        <li><a href="seller-centre.php"><i class="bi bi-shop-window"></i> Seller Centre</a></li>
        <li><a href="become-technician.php"><i class="bi bi-wrench-adjustable-circle-fill"></i> Become a Technician</a></li>
      </ul>
    </aside>

    <!-- Main content -->
    <main class="cu-main">

      <div class="page-header">
        <h2><i class="bi bi-heart-fill" style="color:#dc3545;margin-right:0.5rem;"></i>My Wishlist</h2>
        <p>Products you've saved for later</p>
      </div>

      <div class="section-card">
        <div class="section-head">
          <h6><i class="bi bi-heart-fill" style="color:#dc3545;margin-right:0.4rem;"></i>Saved Products</h6>
          <span id="wishlistCount" style="font-size:0.8rem;color:var(--fg-muted);font-weight:600;">0 items</span>
        </div>
        <div id="wishlistContent">
          <div class="empty-state">
            <i class="bi bi-heart"></i>
            <p>Your wishlist is empty</p>
            <a href="/index.php?browse=1"
               style="display:inline-flex;align-items:center;gap:0.4rem;background:var(--fg-primary);color:#fff;padding:0.55rem 1.25rem;border-radius:9px;font-size:0.85rem;font-weight:700;text-decoration:none;">
              <i class="bi bi-shop"></i> Browse Shop
            </a>
          </div>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script>
  // Wishlist stored in localStorage — replace with API when backend is ready
  let wishlist = JSON.parse(localStorage.getItem('fg_wishlist') || '[]');

  document.addEventListener('DOMContentLoaded', function() {
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
    renderWishlist();
    loadUnreadMessageCount();
  });

  function renderWishlist() {
    const content = document.getElementById('wishlistContent');
    document.getElementById('wishlistCount').textContent = wishlist.length + ' item' + (wishlist.length !== 1 ? 's' : '');

    if (wishlist.length === 0) {
      content.innerHTML = `<div class="empty-state">
        <i class="bi bi-heart"></i>
        <p>Your wishlist is empty</p>
        <a href="/index.php?browse=1" style="display:inline-flex;align-items:center;gap:0.4rem;background:var(--fg-primary);color:#fff;padding:0.55rem 1.25rem;border-radius:9px;font-size:0.85rem;font-weight:700;text-decoration:none;">
          <i class="bi bi-shop"></i> Browse Shop
        </a>
      </div>`;
      return;
    }

    const cards = wishlist.map((item, idx) => `
      <div class="product-card">
        <div class="product-img">
          ${item.image
            ? `<img src="${item.image}" alt="${item.name}" onerror="this.parentElement.innerHTML='<i class=\\'bi bi-box-seam\\'></i>'" />`
            : '<i class="bi bi-box-seam"></i>'}
        </div>
        <div class="product-body">
          <div class="product-name" title="${item.name}">${item.name}</div>
          <div class="product-price">₱${parseFloat(item.price||0).toFixed(2)}</div>
          <div class="product-actions">
            <button class="btn-remove" onclick="removeFromWishlist(${idx})"><i class="bi bi-trash3"></i> Remove</button>
            <button class="btn-buy" onclick="window.location.href='/index.php?browse=1'"><i class="bi bi-cart-plus"></i> Buy Now</button>
          </div>
        </div>
      </div>`).join('');

    content.innerHTML = `<div class="product-grid">${cards}</div>`;
  }

  function removeFromWishlist(idx) {
    wishlist.splice(idx, 1);
    localStorage.setItem('fg_wishlist', JSON.stringify(wishlist));
    renderWishlist();
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




