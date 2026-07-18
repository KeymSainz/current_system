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
  <title>Fix&amp;Go — Messages</title>
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
    .cu-main{flex:1;padding:1.5rem;min-width:0;display:flex;flex-direction:column;}
    /* Chat layout */
    .chat-wrap{display:grid;grid-template-columns:300px 1fr;border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;background:var(--fg-card-bg);flex:1;min-height:0;height:calc(100vh - 68px - 3rem);transition:grid-template-columns 0.3s;}
    .chat-wrap.with-profile{grid-template-columns:300px 1fr 280px;}
    .conv-panel{border-right:1px solid var(--fg-border);display:flex;flex-direction:column;min-height:0;}
    .conv-head{padding:0.85rem 1.25rem;border-bottom:1px solid var(--fg-border);font-weight:700;font-size:0.9rem;color:var(--fg-text);flex-shrink:0;}
    .conv-search{padding:0.6rem 1rem;border-bottom:1px solid var(--fg-border);flex-shrink:0;}
    .conv-search input{width:100%;padding:0.4rem 0.75rem;border-radius:8px;border:1.5px solid var(--fg-border);background:var(--fg-bg);color:var(--fg-text);font-size:0.82rem;outline:none;}
    .conv-search input:focus{border-color:var(--fg-primary);}
    .conv-list{flex:1;overflow-y:auto;}
    .conv-item{display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1.25rem;cursor:pointer;border-bottom:1px solid var(--fg-border);transition:background 0.15s;}
    .conv-item:hover{background:rgba(230,168,0,0.06);}
    .conv-item.active{background:rgba(230,168,0,0.1);border-left:3px solid var(--fg-primary);}
    .conv-avatar{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;flex-shrink:0;color:#fff;}
    .conv-info{flex:1;min-width:0;}
    .conv-name{font-size:0.88rem;font-weight:700;color:var(--fg-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .conv-preview{font-size:0.75rem;color:var(--fg-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:0.1rem;}
    .conv-meta{display:flex;flex-direction:column;align-items:flex-end;gap:0.25rem;flex-shrink:0;}
    .conv-time{font-size:0.7rem;color:var(--fg-muted);}
    .unread-dot{width:8px;height:8px;border-radius:50%;background:var(--fg-primary);}
    .chat-panel{display:flex;flex-direction:column;min-height:0;}
    .chat-head{padding:0.85rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;gap:0.75rem;flex-shrink:0;}
    .chat-head-name{font-weight:700;font-size:0.92rem;color:var(--fg-text);}
    .chat-head-sub{font-size:0.75rem;color:var(--fg-muted);}
    .chat-messages{flex:1;overflow-y:auto;padding:1.25rem 1.5rem;display:flex;flex-direction:column;gap:0.75rem;}
    .msg-wrap{display:flex;flex-direction:column;}
    .msg-wrap.out{align-items:flex-end;}
    .msg-wrap.in{align-items:flex-start;}
    .msg-bubble{max-width:68%;padding:0.6rem 1rem;border-radius:16px;font-size:0.88rem;line-height:1.55;word-break:break-word;}
    .msg-bubble.out{background:var(--fg-primary);color:#fff;border-bottom-right-radius:4px;}
    .msg-bubble.in{background:var(--fg-bg);border:1px solid var(--fg-border);color:var(--fg-text);border-bottom-left-radius:4px;}
    .msg-time{font-size:0.68rem;color:var(--fg-muted);margin-top:0.2rem;}
    .chat-input-area{padding:0.85rem 1.25rem;border-top:1px solid var(--fg-border);display:flex;gap:0.6rem;align-items:flex-end;flex-shrink:0;}
    .chat-input{flex:1;background:var(--fg-bg);border:1.5px solid var(--fg-border);border-radius:20px;padding:0.55rem 1rem;font-size:0.88rem;color:var(--fg-text);resize:none;outline:none;transition:border-color 0.2s;font-family:inherit;max-height:100px;line-height:1.4;}
    .chat-input:focus{border-color:var(--fg-primary);}
    .btn-send{background:var(--fg-primary);border:none;border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.95rem;cursor:pointer;flex-shrink:0;transition:all 0.2s;}
    .btn-send:hover{opacity:0.85;transform:scale(1.08);}
    .btn-send:disabled{opacity:0.5;cursor:not-allowed;transform:none;}
    .chat-empty{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--fg-muted);gap:0.5rem;}
    /* Tech profile panel */
    .tech-profile-panel{border-left:1px solid var(--fg-border);display:flex;flex-direction:column;overflow-y:auto;background:var(--fg-card-bg);}
    .tech-profile-panel .tp-avatar{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,rgba(139,92,246,0.25),rgba(139,92,246,0.08));border:3px solid rgba(139,92,246,0.35);display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:#8b5cf6;font-weight:800;overflow:hidden;flex-shrink:0;}
    .tech-profile-panel .tp-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%;}
    .tp-spec-pill{display:inline-flex;align-items:center;background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.2);color:#8b5cf6;font-size:0.68rem;font-weight:700;padding:0.15rem 0.5rem;border-radius:50px;}
    .date-divider{text-align:center;font-size:0.72rem;color:var(--fg-muted);margin:0.5rem 0;position:relative;}
    .date-divider::before,.date-divider::after{content:'';position:absolute;top:50%;width:30%;height:1px;background:var(--fg-border);}
    .date-divider::before{left:0;}.date-divider::after{right:0;}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @keyframes spin{to{transform:rotate(360deg)}}
    @media(max-width:992px){.chat-wrap{grid-template-columns:240px 1fr;} .chat-wrap.with-profile{grid-template-columns:240px 1fr;} .tech-profile-panel{display:none;}}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .cu-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .cu-sidebar.open{transform:translateX(0);}
      .cu-main{padding:0.75rem;}
      .chat-wrap{grid-template-columns:1fr;height:auto;}
      .conv-panel{height:200px;border-right:none;border-bottom:1px solid var(--fg-border);}
      .chat-panel{height:calc(100dvh - 68px - 200px - 1.5rem);min-height:300px;}
    }
    @media(max-width:575px){
      html,body{overflow-x:hidden;}
      .cu-main{padding:0.4rem;}
      .chat-wrap{border-radius:10px;}
      .conv-panel{height:180px;}
      .chat-panel{height:calc(100dvh - 68px - 180px - 0.8rem);min-height:260px;}
      .msg-bubble{max-width:85%!important;font-size:0.83rem!important;}
      .chat-input{font-size:0.85rem!important;}
      /* Booking modal fills full screen on small phones */
      #msgBookingModal > div{border-radius:14px!important;margin:0.4rem!important;}
      /* Agreement modal full screen */
      #agreementModal > div{border-radius:14px!important;margin:0.4rem!important;}
      /* Navbar username hidden */
      #navUserName{display:none!important;}
    }
  </style>
<link href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css" rel="stylesheet"/>
  <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
</head>
<body>
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&amp;Go" style="height:42px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-2" style="flex-wrap:nowrap;">
      <span class="role-badge customer msg-desk-only">👤 Customer</span>
      <span id="navUserName" class="msg-desk-only" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <button onclick="customerLogout()" class="btn btn-sm msg-desk-only"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;display:none;">
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
  <style>
    .msg-desk-only { display: none !important; }
    @media (min-width: 992px) { .msg-desk-only { display: flex !important; } }
    /* Full-screen mobile chat: show only conv list first, then chat panel */
    @media (max-width: 768px) {
      .cu-sidebar { display: none !important; }
      .cu-main { padding: 0.5rem !important; padding-bottom: 0 !important; }
      .chat-wrap { grid-template-columns: 1fr !important; height: calc(100dvh - 58px - 70px) !important; border-radius: 10px; }
      /* Show conv panel full width by default */
      .conv-panel { height: 100% !important; border-right: none !important; border-bottom: none !important; display: flex !important; }
      /* Hide chat panel until conversation selected */
      .chat-panel { display: none !important; }
      .chat-wrap.chat-active .conv-panel { display: none !important; }
      .chat-wrap.chat-active .chat-panel { display: flex !important; height: 100% !important; }
    }
  </style>

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
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
        <li><a href="notifications.php"><i class="bi bi-bell-fill"></i> Notifications</a></li>
        <li><a href="messages.php" class="active"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
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
        <li><a href="/index.php?browse=1"><i class="bi bi-shop-window"></i> Browse Shop</a></li>
        <li><a href="seller-centre.php"><i class="bi bi-shop-window"></i> Seller Centre</a></li>
        <li><a href="become-technician.php"><i class="bi bi-wrench-adjustable-circle-fill"></i> Become a Technician</a></li>
      </ul>
    </aside>

    <main class="cu-main">
      <div class="chat-wrap">
        <!-- Conversation list -->
        <div class="conv-panel">
          <div class="conv-head">
            <i class="bi bi-chat-dots-fill" style="color:#3b82f6;margin-right:0.4rem;"></i>Messages
          </div>
          <div class="conv-search">
            <input type="text" id="convSearch" placeholder="Search conversations…">
          </div>
          <div class="conv-list" id="convList">
            <div style="text-align:center;padding:2rem;color:var(--fg-muted);font-size:0.85rem;">
              <div style="width:22px;height:22px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
              Loading…
            </div>
          </div>
        </div>

        <!-- Chat area -->
        <div class="chat-panel" id="chatPanel">
          <div class="chat-empty" id="chatEmpty">
            <i class="bi bi-chat-square-dots" style="font-size:3rem;opacity:0.25;"></i>
            <span style="font-size:0.9rem;font-weight:600;">No conversation selected</span>
            <span style="font-size:0.82rem;">Your messages with sellers will appear here</span>
          </div>
        </div>

        <!-- Technician profile side panel (only shown when chatting with a technician) -->
        <div class="tech-profile-panel" id="techProfilePanel" style="display:none;">
          <div style="padding:1.25rem 1rem;text-align:center;border-bottom:1px solid var(--fg-border);background:linear-gradient(135deg,rgba(139,92,246,0.06),transparent);">
            <div class="tp-avatar" id="tpAvatar" style="margin:0 auto 0.75rem;">🔧</div>
            <div style="font-weight:800;font-size:0.95rem;color:var(--fg-text);" id="tpName">Loading…</div>
            <div id="tpAvailability" style="margin-top:0.3rem;"></div>
            <div id="tpSpecializations" style="display:flex;flex-wrap:wrap;gap:0.3rem;justify-content:center;margin-top:0.5rem;"></div>
          </div>

          <!-- Stats row -->
          <div style="display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid var(--fg-border);" id="tpStats"></div>

          <!-- Shop image -->
          <div id="tpShopImgWrap" style="display:none;">
            <img id="tpShopImg" style="width:100%;max-height:130px;object-fit:cover;" alt="Shop">
          </div>

          <!-- Info -->
          <div style="padding:0.85rem 1rem;" id="tpInfo"></div>

          <!-- Book Now button -->
          <div style="padding:0 1rem 1rem;" id="tpBookBtnWrap"></div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="/assets/js/session-timeout.js"></script>
  <script>
  const API = '../../../api/messages';
  let myId = null;
  let activeConvId = null;
  let allConvs = [];
  let lastMsgCount = 0;

  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'customer') { window.location.href = '/login.html'; return; }
    myId = user.id || null;

    const fullName = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email;
    document.getElementById('sidebarName').textContent = fullName || user.email;
    const initials = ((user.firstName||'')[0]||'') + ((user.lastName||'')[0]||'');
    // Show photo if available, else initials
    (function renderAvatar(url) {
      var el = document.getElementById('sidebarAvatarInitials');
      if (!el) return;
      if (url) { el.innerHTML = '<img src="' + url + '" alt="avatar" onerror="this.parentElement.textContent=\'' + initials.toUpperCase() + '\'">' ; }
      else { el.textContent = initials.toUpperCase() || '?'; }
    })(user.avatar_url || null);
    // Refresh from server
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

    document.getElementById('convSearch').addEventListener('input', function () { renderConvList(this.value); });

    // Check URL param ?with=userId (from "Contact Shop" button in orders)
    const params = new URLSearchParams(window.location.search);
    const withId = parseInt(params.get('with'));

    loadConversations().then(() => {
      if (withId) openOrCreateConv(withId);
    });

    loadUnreadMessageCount();

    // Poll every 5s
    setInterval(() => {
      if (activeConvId) loadMessages(activeConvId, false);
      else loadConversations();
    }, 5000);
  });

  function loadConversations() {
    return fetch(API + '?action=conversations', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        allConvs = d.conversations || [];
        renderConvList('');
      }).catch(() => {});
  }

  function renderConvList(q) {
    const list = document.getElementById('convList');
    let convs = allConvs;
    if (q) convs = convs.filter(c => c.other_name.toLowerCase().includes(q.toLowerCase()));

    if (!convs.length) {
      list.innerHTML = `<div style="text-align:center;padding:2.5rem 1rem;color:var(--fg-muted);font-size:0.85rem;">
        <i class="bi bi-chat-square" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.3;"></i>
        ${q ? 'No results.' : 'No messages yet.<br>Order a product and contact the seller.'}
      </div>`;
      return;
    }

    list.innerHTML = convs.map(c => {
      const initials = c.other_name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2) || '?';
      const color    = stringToColor(c.other_name);
      const preview  = c.last_message ? esc(c.last_message.slice(0, 50)) : '<em>No messages yet</em>';
      const time     = c.last_message_at ? relativeTime(c.last_message_at) : '';
      const active   = c.id === activeConvId ? 'active' : '';
      return `<div class="conv-item ${active}" onclick="openConv(${c.id}, '${esc(c.other_name)}', ${c.other_id})">
        <div class="conv-avatar" style="background:${color};">${initials}</div>
        <div class="conv-info">
          <div class="conv-name">${esc(c.other_name)}</div>
          <div class="conv-preview">${preview}</div>
        </div>
        <div class="conv-meta">
          <span class="conv-time">${time}</span>
          ${parseInt(c.unread_count) > 0 ? '<span class="unread-dot"></span>' : ''}
        </div>
      </div>`;
    }).join('');
  }

  function openConv(convId, otherName, otherId) {
    activeConvId = convId;
    lastMsgCount = 0;
    renderConvList(document.getElementById('convSearch').value);

    // Check if the other person is a phone_technician (for Book Now button)
    const conv = allConvs.find(c => c.id === convId);
    const isTechnician = conv && conv.other_role === 'phone_technician';
    const activeOtherId = otherId;

    const panel = document.getElementById('chatPanel');
    const initials = otherName.split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2) || '?';
    const color = stringToColor(otherName);

    const roleLabel = conv
      ? ({ phone_technician:'🔧 Phone Technician', sales_person:'💼 Sales Person', owner:'🏪 Owner', supplier:'📦 Supplier', customer:'👤 Customer' }[conv.other_role] || conv.other_role)
      : '💼 Sales Person';

    const bookNowBtn = isTechnician
      ? `<button onclick="openBookingForm(${otherId})" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.35rem 0.85rem;border-radius:8px;background:var(--fg-primary);color:#000;border:none;font-size:0.78rem;font-weight:800;cursor:pointer;transition:opacity 0.2s;margin-left:auto;" onmouseenter="this.style.opacity='0.85'" onmouseleave="this.style.opacity='1'">
          <i class="bi bi-calendar-check-fill"></i> Book Now
        </button>`
      : '';

    panel.innerHTML = `
      <div class="chat-head">
        <div class="conv-avatar" style="background:${color};width:36px;height:36px;font-size:0.85rem;">${initials}</div>
        <div style="flex:1;min-width:0;">
          <div class="chat-head-name">${esc(otherName)}</div>
          <div class="chat-head-sub">${roleLabel}</div>
        </div>
        ${bookNowBtn}
      </div>
      ${isTechnician ? `<div style="background:rgba(230,168,0,0.08);border-bottom:1px solid var(--fg-border);padding:0.6rem 1.25rem;display:flex;align-items:center;gap:0.6rem;">
        <i class="bi bi-info-circle-fill" style="color:var(--fg-primary);font-size:0.85rem;flex-shrink:0;"></i>
        <span style="font-size:0.78rem;color:var(--fg-muted);">Chat with the technician about your repair, then click <strong style="color:var(--fg-primary);">Book Now</strong> when ready to submit a booking.</span>
      </div>` : ''}
      <div class="chat-messages" id="chatMessages">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);font-size:0.85rem;">
          <div style="width:20px;height:20px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading messages…
        </div>
      </div>
      <div class="chat-input-area">
        <label for="chatFileInput" title="Send photo or video" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:1.5px solid var(--fg-border);cursor:pointer;flex-shrink:0;color:var(--fg-muted);font-size:1rem;transition:all 0.2s;background:var(--fg-bg);" onmouseenter="this.style.borderColor='var(--fg-primary)';this.style.color='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-muted)'">
          <i class="bi bi-paperclip"></i>
        </label>
        <input type="file" id="chatFileInput" accept="image/*,video/mp4,video/webm,video/quicktime" style="display:none;" onchange="handleChatFile(this, ${activeOtherId})">
        <textarea class="chat-input" id="chatInput" rows="1" placeholder="Type a message…"></textarea>
        <button class="btn-send" id="btnSend"><i class="bi bi-send-fill"></i></button>
      </div>
      <div id="chatFilePreview" style="display:none;padding:0.5rem 1.25rem 0;border-top:1px solid var(--fg-border);align-items:center;gap:0.75rem;background:var(--fg-bg);">
        <div id="chatFilePreviewThumb" style="font-size:0.82rem;color:var(--fg-text);flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;"></div>
        <button onclick="clearChatFile()" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:1rem;flex-shrink:0;" title="Remove">✕</button>
      </div>`;

    const input = document.getElementById('chatInput');
    input.addEventListener('input', function () {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(activeOtherId); }
    });
    document.getElementById('btnSend').addEventListener('click', () => sendMessage(activeOtherId));

    loadMessages(convId, true);
    // Load tech profile panel if chatting with a technician
    if (isTechnician) {
      loadTechProfile(activeOtherId, otherName, activeOtherId);
    } else {
      hideTechProfile();
    }
  }

  function loadMessages(convId, scrollToBottom) {
    fetch(`${API}?action=messages&conv_id=${convId}`, { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) {
          if (d.expired) { window.location.href = '/login.html'; return; }
          const container = document.getElementById('chatMessages');
          if (container) container.innerHTML = `<div style="text-align:center;padding:3rem;color:var(--fg-muted);font-size:0.85rem;">${esc(d.message || 'Could not load messages.')}</div>`;
          return;
        }
        const msgs = d.messages || [];
        if (!scrollToBottom && msgs.length === lastMsgCount) return;
        lastMsgCount = msgs.length;
        if (d.my_id) myId = parseInt(d.my_id);
        renderMessages(msgs, scrollToBottom);
        loadConversations();
      }).catch(() => {
        const container = document.getElementById('chatMessages');
        if (container && scrollToBottom) container.innerHTML = `<div style="text-align:center;padding:3rem;color:var(--fg-muted);font-size:0.85rem;">Could not load messages. <a href="" onclick="location.reload();return false;" style="color:var(--fg-primary);">Retry</a></div>`;
      });
  }

  function renderMessages(msgs, scrollToBottom) {
    const container = document.getElementById('chatMessages');
    if (!container) return;

    if (!msgs.length) {
      container.innerHTML = `<div style="text-align:center;padding:3rem;color:var(--fg-muted);font-size:0.85rem;">
        <i class="bi bi-chat-square" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.3;"></i>
        No messages yet. Say hello!
      </div>`;
      return;
    }

    let html = '', lastDate = '';
    msgs.forEach(m => {
      const d = new Date(m.created_at);
      const dateStr = d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
      if (dateStr !== lastDate) {
        html += `<div class="date-divider">${dateStr}</div>`;
        lastDate = dateStr;
      }
      const isMe = parseInt(m.sender_id) === parseInt(myId);
      const time = d.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' });

      // Build media content
      let mediaHtml = '';
      if (m.file_url) {
        const src = '../../../' + m.file_url;
        if (m.file_type === 'image') {
          mediaHtml = `<div style="margin-bottom:${m.body?'0.4rem':'0'};">
            <img src="${esc(src)}" alt="${esc(m.file_name||'Photo')}"
              style="max-width:220px;max-height:220px;border-radius:10px;cursor:pointer;display:block;object-fit:cover;"
              onclick="openMediaViewer('${esc(src)}','image')"
              onerror="this.style.display='none'">
          </div>`;
        } else if (m.file_type === 'video') {
          mediaHtml = `<div style="margin-bottom:${m.body?'0.4rem':'0'};">
            <video src="${esc(src)}" controls
              style="max-width:240px;max-height:200px;border-radius:10px;display:block;background:#000;"
              onerror="this.outerHTML='<div style=\\'color:#dc3545;font-size:0.78rem;\\'>Video unavailable</div>'">
            </video>
          </div>`;
        }
      }

      html += `<div class="msg-wrap ${isMe ? 'out' : 'in'}">
        <div class="msg-bubble ${isMe ? 'out' : 'in'}">${mediaHtml}${m.body ? esc(m.body) : ''}</div>
        <div class="msg-time">${time}</div>
      </div>`;
    });
    container.innerHTML = html;
    if (scrollToBottom) container.scrollTop = container.scrollHeight;
  }

  function sendMessage(otherId) {
    const input = document.getElementById('chatInput');
    const btn   = document.getElementById('btnSend');
    const body  = input.value.trim();
    const fileInput = document.getElementById('chatFileInput');
    const hasFile = fileInput && fileInput.files && fileInput.files[0];

    if (!body && !hasFile) return;
    btn.disabled = true;

    if (hasFile) {
      // Send as multipart with file
      const fd = new FormData();
      fd.append('action', 'send');
      fd.append('other_id', otherId);
      if (body) fd.append('body', body);
      fd.append('attachment', fileInput.files[0]);

      fetch(API, { method: 'POST', credentials: 'include', body: fd })
        .then(r => r.json())
        .then(d => {
          if (d.expired) { window.location.href = '/login.html'; return; }
          if (d.success) {
            input.value = '';
            input.style.height = 'auto';
            clearChatFile();
            loadMessages(activeConvId, true);
          }
        })
        .catch(() => {})
        .finally(() => { btn.disabled = false; });
    } else {
      // Plain text
      fetch(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ action: 'send', other_id: otherId, body })
      })
        .then(r => r.json())
        .then(d => {
          if (d.expired) { window.location.href = '/login.html'; return; }
          if (d.success) {
            input.value = '';
            input.style.height = 'auto';
            loadMessages(activeConvId, true);
          }
        })
        .catch(() => {})
        .finally(() => { btn.disabled = false; });
    }
  }

  function handleChatFile(input, otherId) {
    const file = input.files[0];
    if (!file) return;
    const previewEl = document.getElementById('chatFilePreview');
    const thumbEl   = document.getElementById('chatFilePreviewThumb');
    if (!previewEl || !thumbEl) return;

    const isImage = file.type.startsWith('image/');
    const isVideo = file.type.startsWith('video/');
    if (!isImage && !isVideo) {
      alert('Only images and videos are allowed.');
      input.value = '';
      return;
    }

    previewEl.style.display = 'flex';
    if (isImage) {
      const url = URL.createObjectURL(file);
      thumbEl.innerHTML = `<img src="${url}" style="height:40px;border-radius:6px;margin-right:0.4rem;vertical-align:middle;object-fit:cover;"> ${esc(file.name)}`;
    } else {
      thumbEl.innerHTML = `<i class="bi bi-camera-video-fill" style="color:var(--fg-primary);margin-right:0.4rem;"></i> ${esc(file.name)}`;
    }
  }

  function clearChatFile() {
    const fi = document.getElementById('chatFileInput');
    const pv = document.getElementById('chatFilePreview');
    if (fi) fi.value = '';
    if (pv) pv.style.display = 'none';
  }

  function openMediaViewer(src, type) {
    const overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.92);z-index:99999;display:flex;align-items:center;justify-content:center;cursor:pointer;';
    overlay.onclick = () => document.body.removeChild(overlay);
    if (type === 'image') {
      overlay.innerHTML = `<img src="${src}" style="max-width:94vw;max-height:92vh;border-radius:10px;object-fit:contain;">`;
    }
    document.body.appendChild(overlay);
  }

  function openOrCreateConv(otherId) {
    fetch(`${API}?action=get_or_create&other_id=${otherId}`, { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        if (!allConvs.find(c => c.id === d.conv_id)) {
          allConvs.unshift({
            id: d.conv_id, other_id: d.other_id,
            other_name: d.other_name, other_role: d.other_role,
            last_message: null, last_message_at: null, unread_count: 0
          });
        } else {
          // Ensure role is populated if missing
          const existing = allConvs.find(c => c.id === d.conv_id);
          if (existing && !existing.other_role && d.other_role) {
            existing.other_role = d.other_role;
          }
        }
        renderConvList('');
        openConv(d.conv_id, d.other_name, d.other_id);
      }).catch(() => {});
  }

  function esc(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
  function stringToColor(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
    const colors = ['#3b82f6','#10b981','#7c3aed','#ea580c','#0891b2','#be185d','#16a34a','#d97706'];
    return colors[Math.abs(hash) % colors.length];
  }
  function relativeTime(dateStr) {
    const d = new Date(dateStr), now = new Date();
    const diff = (now - d) / 1000;
    if (diff < 60) return 'just now';
    if (diff < 3600) return Math.floor(diff/60) + 'm ago';
    if (diff < 86400) return d.toLocaleTimeString('en-PH', {hour:'2-digit',minute:'2-digit'});
    if (diff < 604800) return d.toLocaleDateString('en-PH', {weekday:'short'});
    return d.toLocaleDateString('en-PH', {month:'short',day:'numeric'});
  }
  // ── Booking Form ──────────────────────────────────────────────
  let _bookingTechId = null;
  let _bookingTechProfile = null;
  let _bookingMiniMap = null;

  function openBookingForm(techId) {
    _bookingTechId = techId;
    const user = FGAuth.UserStore.get();
    if (user) {
      document.getElementById('msgBfName').value    = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
      document.getElementById('msgBfContact').value = user.phone || '';
    }
    fetch('../../../api/session/user', { credentials: 'include' })
      .then(function(r){return r.json();})
      .then(function(d) {
        if (d.loggedIn && d.user) {
          const u = d.user;
          const addr = [u.address_line, u.barangay, u.city, u.province].filter(Boolean).join(', ');
          if (addr) {
            document.getElementById('msgBfAddress').value = addr;
            if (document.getElementById('msgBfServiceType').value === 'home_service') {
              updateServiceMap('home_service');
            }
          }
        }
      }).catch(function(){});

    fetch('/api/repair/bookings?action=technician_profile&id=' + techId)
      .then(function(r){return r.json();})
      .then(function(d) {
        if (d.success && d.profile) {
          _bookingTechProfile = d.profile;
          if (document.getElementById('msgBfServiceType').value === 'shop_fix') {
            updateServiceMap('shop_fix');
          }
        }
      }).catch(function(){});

    ['msgBfDevice','msgBfFault','msgBfHistory','msgBfExpected','msgBfSchedule'].forEach(function(id) {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
    document.getElementById('msgBookingAlert').style.display = 'none';
    document.getElementById('msgBookingModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    selectMsgServiceType('shop_fix');
  }

  function updateServiceMap(type) {
    const mapEl  = document.getElementById('msgSvcMapEl');
    const infoEl = document.getElementById('msgSvcMapInfo');
    if (!mapEl) return;
    if (_bookingMiniMap) { try { _bookingMiniMap.remove(); } catch(e){} _bookingMiniMap = null; }

    if (type === 'shop_fix') {
      const p        = _bookingTechProfile;
      const shopAddr = p ? (p.shop_address || [p.general_location, p.city].filter(Boolean).join(', ') || '') : '';
      const shopName = p ? (p.shop_name || ((p.first_name||'') + ' ' + (p.last_name||'')).trim()) : 'Technician Shop';
      if (infoEl) {
        infoEl.innerHTML = '<i class="bi bi-shop-window" style="color:var(--fg-primary);"></i> <strong>' + esc(shopName) + '</strong>'
          + (shopAddr ? '<br><span style="font-size:0.75rem;color:var(--fg-muted);">&#128205; ' + esc(shopAddr) + '</span>' : '<br><span style="font-size:0.75rem;color:var(--fg-muted);">Address not set</span>');
      }
      if (shopAddr) showMiniMap(mapEl, shopAddr, shopName + ' (Shop)', '#00b14f');
      else if (mapEl) mapEl.style.display = 'none';
    } else {
      const custAddr = document.getElementById('msgBfAddress').value.trim();
      if (infoEl) {
        infoEl.innerHTML = '<i class="bi bi-house-fill" style="color:#3b82f6;"></i> <strong>Your Location</strong>'
          + (custAddr ? '<br><span style="font-size:0.75rem;color:var(--fg-muted);">&#128205; ' + esc(custAddr) + '</span>' : '<br><span style="font-size:0.75rem;color:var(--fg-muted);">Fill in your address above</span>');
      }
      if (custAddr) showMiniMap(mapEl, custAddr, 'Your Location (Home Service)', '#3b82f6');
      else if (mapEl) mapEl.style.display = 'none';
    }
  }

  function showMiniMap(container, address, label, pinColor) {
    if (typeof maplibregl === 'undefined') { return; }
    if (_bookingMiniMap) { try { _bookingMiniMap.remove(); } catch(e){} _bookingMiniMap = null; }
    container.style.display = 'block';
    fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(address + ', Philippines') + '&format=json&limit=1&countrycodes=ph&accept-language=en')
      .then(function(r){return r.json();})
      .then(function(data) {
        if (!data || !data[0]) return;
        const lat  = parseFloat(data[0].lat);
        const lng  = parseFloat(data[0].lon);
        const dark = document.documentElement.getAttribute('data-theme') === 'dark';
        const tileStyle = dark
          ? 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json'
          : 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json';
        _bookingMiniMap = new maplibregl.Map({
          container: container,
          style: tileStyle,
          center: [lng, lat],
          zoom: 15,
          attributionControl: { compact: true },
          interactive: false
        });
        _bookingMiniMap.on('load', function() {
          const el = document.createElement('div');
          el.style.cssText = 'width:20px;height:20px;border-radius:50%;border:3px solid #fff;background:' + pinColor + ';box-shadow:0 2px 8px rgba(0,0,0,0.4);';
          new maplibregl.Marker({ element: el, anchor: 'center' })
            .setLngLat([lng, lat])
            .setPopup(new maplibregl.Popup({ offset: 14, closeButton: false }).setHTML('<strong>' + esc(label) + '</strong>'))
            .addTo(_bookingMiniMap);
        });
      }).catch(function(){});
  }

  function closeBookingForm() {
    document.getElementById('msgBookingModal').style.display = 'none';
    document.body.style.overflow = '';
    if (_bookingMiniMap) { try { _bookingMiniMap.remove(); } catch(e){} _bookingMiniMap = null; }
  }

  function submitMsgBooking() {
    const name    = document.getElementById('msgBfName').value.trim();
    const contact = document.getElementById('msgBfContact').value.trim();
    const address = document.getElementById('msgBfAddress').value.trim();
    const device  = document.getElementById('msgBfDevice').value.trim();
    const fault   = document.getElementById('msgBfFault').value.trim();
    const history = document.getElementById('msgBfHistory').value.trim();
    const expected= document.getElementById('msgBfExpected').value.trim();
    const schedule= document.getElementById('msgBfSchedule').value;
    const photoInput = document.getElementById('msgBfPhoto');
    if (!name || !contact || !address || !device || !fault) {
      showMsgBookingAlert('error', 'Please fill in all required fields: Name, Contact, Address, Device, and Fault Description.');
      return;
    }
    if (!_bookingTechId) { showMsgBookingAlert('error', 'Technician not identified.'); return; }
    const btn = document.getElementById('msgSubmitBookingBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting&#8230;';
    document.getElementById('msgBookingAlert').style.display = 'none';
    const fd = new FormData();
    fd.append('action',          'book');
    fd.append('technician_id',   _bookingTechId);
    fd.append('name',            name);
    fd.append('contact_number',  contact);
    fd.append('address',         address);
    fd.append('device_name',     device);
    fd.append('fault_description', fault);
    fd.append('phone_history',   history);
    fd.append('expected_fix',    expected);
    if (schedule) fd.append('scheduled_at', schedule);
    if (photoInput && photoInput.files[0]) fd.append('phone_photo', photoInput.files[0]);
    fd.append('service_type', document.getElementById('msgBfServiceType').value || 'shop_fix');
    fetch('../../../api/repair/bookings', { method: 'POST', credentials: 'include', body: fd })
      .then(function(r){return r.json();})
      .then(function(d) {
        if (!d.success) throw new Error(d.message || 'Booking failed.');
        showMsgBookingAlert('success', '&#9989; Booking #' + d.booking_id + ' submitted! The technician will confirm shortly.');
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Booking Submitted!';
        setTimeout(function() { closeBookingForm(); btn.disabled = false; btn.innerHTML = '<i class="bi bi-calendar-check-fill"></i> Submit Booking'; }, 3500);
      })
      .catch(function(err) {
        showMsgBookingAlert('error', err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-calendar-check-fill"></i> Submit Booking';
      });
  }

  function showMsgBookingAlert(type, msg) {
    const el = document.getElementById('msgBookingAlert');
    el.style.display = 'flex';
    el.style.background = type === 'success' ? 'rgba(40,167,69,0.1)' : 'rgba(220,53,69,0.1)';
    el.style.color = type === 'success' ? '#28A745' : '#dc3545';
    el.style.border = '1px solid ' + (type === 'success' ? 'rgba(40,167,69,0.25)' : 'rgba(220,53,69,0.25)');
    el.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill') + '"></i> ' + msg;
  }

  // ── Agreement Modal — shown BEFORE booking form ───────────────
  let _pendingBookingTechId = null;

  // Intercept openBookingForm — show agreement first
  var _originalOpenBookingForm = openBookingForm;
  window.openBookingForm = function(techId) {
    _pendingBookingTechId = techId;
    showAgreementModal('shop_fix'); // default; will be updated when service type known
  };

  function showAgreementModal(serviceType) {
    const isHome = serviceType === 'home_service';
    const modal  = document.getElementById('agreementModal');
    const body   = document.getElementById('agreementBody');
    const agreeBtn = document.getElementById('agreementAgreeBtn');

    agreeBtn.disabled = true;
    agreeBtn.style.opacity = '0.5';
    agreeBtn.style.cursor  = 'not-allowed';
    document.getElementById('agreementScrollHint').style.display = 'flex';

    const commonTerms = `
      <div style="font-size:0.88rem;line-height:1.7;color:var(--fg-text);">
        <div style="text-align:center;margin-bottom:1.25rem;">
          <div style="font-size:1.6rem;margin-bottom:0.4rem;">📋</div>
          <div style="font-size:1rem;font-weight:800;color:var(--fg-text);">Repair Service Agreement</div>
          <div style="font-size:0.75rem;color:var(--fg-muted);">Please read carefully before proceeding</div>
        </div>

        ${isHome ? `
        <div style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.25);border-radius:10px;padding:0.85rem 1rem;margin-bottom:1rem;">
          <div style="font-weight:800;color:#3b82f6;margin-bottom:0.4rem;">🏠 Home Service — Additional Notice</div>
          <p style="margin:0 0 0.5rem;">By selecting <strong>Home Service</strong>, you acknowledge the following:</p>
          <ul style="margin:0;padding-left:1.25rem;">
            <li>A <strong>home service fee</strong> will be charged in addition to the repair cost. The technician will inform you of the exact amount before proceeding.</li>
            <li>You agree to provide a safe and accessible workspace for the technician to perform the repair at your location.</li>
            <li>The technician reserves the right to decline or reschedule if the environment is deemed unsafe or unsuitable for repair work.</li>
            <li>Travel time and distance may affect the availability of the home service option.</li>
          </ul>
        </div>` : `
        <div style="background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.25);border-radius:10px;padding:0.85rem 1rem;margin-bottom:1rem;">
          <div style="font-weight:800;color:#8b5cf6;margin-bottom:0.4rem;">🏪 In-Shop Fix — Important Reminders</div>
          <ul style="margin:0;padding-left:1.25rem;">
            <li>Please bring your device to the technician's shop at the scheduled time.</li>
            <li>Ensure your device is backed up before dropping it off, as data loss may occur during repair.</li>
            <li>The technician will provide a cost estimate before performing any paid service.</li>
          </ul>
        </div>`}

        <div style="font-weight:800;color:var(--fg-text);margin-bottom:0.5rem;margin-top:0.5rem;">⚖️ Legal Disclaimer — Parts Replacement</div>
        <p>Under <strong>Republic Act No. 7394</strong> (Consumer Act of the Philippines) and applicable consumer protection laws, the following terms apply to all device repair services:</p>

        <ol style="padding-left:1.25rem;margin-bottom:0.75rem;">
          <li style="margin-bottom:0.5rem;"><strong>Risk of Parts Replacement:</strong> The replacement of components (including but not limited to screens, batteries, charging ports, cameras, and motherboard parts) carries inherent risks. Replacement parts — whether OEM, aftermarket, or refurbished — may affect the original performance, durability, or warranty status of your device.</li>
          <li style="margin-bottom:0.5rem;"><strong>Device Warranty Voiding:</strong> Repair services performed outside of the manufacturer's authorized service centers may void any remaining manufacturer warranty on your device. Fix&Go technicians are independent service providers and are not affiliated with any device manufacturer.</li>
          <li style="margin-bottom:0.5rem;"><strong>Data Loss:</strong> The customer acknowledges that repair work may result in partial or complete data loss. It is the customer's sole responsibility to back up all data prior to submitting the device for repair. Fix&Go and its technicians shall not be held liable for any data loss.</li>
          <li style="margin-bottom:0.5rem;"><strong>Liability Limitation:</strong> Fix&Go technicians exercise reasonable care in performing all repairs. However, in cases where pre-existing damage, undisclosed conditions, or inherent device defects cause additional damage during repair, the technician's liability shall be limited to the cost of the repair service rendered.</li>
          <li style="margin-bottom:0.5rem;"><strong>Technician Protection:</strong> The customer agrees not to hold the technician liable for damages resulting from pre-existing conditions, device age, or prior unauthorized repairs. Any dispute shall first be resolved through amicable settlement before escalation under applicable Philippine law.</li>
          <li style="margin-bottom:0.5rem;"><strong>Consent to Repair:</strong> By submitting this booking, the customer expressly consents to the technician performing diagnostic inspection and, if agreed upon, repair services on their device under the terms stated herein.</li>
          <li style="margin-bottom:0.5rem;"><strong>Privacy:</strong> Personal information provided in this booking form is collected solely for the purpose of repair service coordination and will not be shared with third parties without the customer's consent, in accordance with the <strong>Data Privacy Act of 2012 (RA 10173)</strong>.</li>
        </ol>

        <div style="background:rgba(220,53,69,0.08);border:1px solid rgba(220,53,69,0.2);border-radius:10px;padding:0.75rem 1rem;margin-top:0.75rem;">
          <div style="font-weight:800;color:#dc3545;margin-bottom:0.3rem;">⚠️ Important</div>
          <p style="margin:0;font-size:0.83rem;">By clicking <strong>"I Agree & Proceed"</strong>, you confirm that you have read, understood, and agree to all of the terms and conditions stated in this agreement. This agreement is legally binding under Philippine law.</p>
        </div>
      </div>`;

    body.innerHTML = commonTerms;
    document.getElementById('agreementModalTitle').textContent = isHome ? '🏠 Home Service Agreement' : '🏪 In-Shop Repair Agreement';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Listen for scroll to enable agree button
    const scrollEl = document.getElementById('agreementScrollArea');
    scrollEl.scrollTop = 0;
    function onScroll() {
      const atBottom = scrollEl.scrollHeight - scrollEl.scrollTop - scrollEl.clientHeight < 40;
      if (atBottom) {
        agreeBtn.disabled = false;
        agreeBtn.style.opacity = '1';
        agreeBtn.style.cursor = 'pointer';
        document.getElementById('agreementScrollHint').style.display = 'none';
        scrollEl.removeEventListener('scroll', onScroll);
      }
    }
    scrollEl.addEventListener('scroll', onScroll);
    // Also check immediately if content is short enough
    setTimeout(function() {
      if (scrollEl.scrollHeight <= scrollEl.clientHeight + 40) {
        agreeBtn.disabled = false;
        agreeBtn.style.opacity = '1';
        agreeBtn.style.cursor = 'pointer';
        document.getElementById('agreementScrollHint').style.display = 'none';
      }
    }, 200);
  }

  function closeAgreementModal() {
    document.getElementById('agreementModal').style.display = 'none';
    document.body.style.overflow = '';
    _pendingBookingTechId = null;
    _msgAgreementContext = 'open';
  }

  var _msgAgreementContext = 'open'; // 'open' = opening booking form, 'serviceSwitch' = switching service type

  function agreeAndProceed() {
    document.getElementById('agreementModal').style.display = 'none';
    document.body.style.overflow = '';
    if (_msgAgreementContext === 'serviceSwitch') {
      // The customer agreed to home service terms while inside the booking form
      selectMsgServiceType('home_service');
      _msgAgreementContext = 'open';
      _pendingBookingTechId = null;
    } else {
      // Now open the actual booking form
      _originalOpenBookingForm(_pendingBookingTechId);
      _pendingBookingTechId = null;
      _msgAgreementContext = 'open';
    }
  }

  // Update agreement when service type is changed in booking form
  var _origSelectMsgServiceType = selectMsgServiceType;
  window.selectMsgServiceType = function(type) {
    _origSelectMsgServiceType(type);
  };

  // Called when customer clicks a service type option inside the booking form
  window.msgPickServiceType = function(type) {
    if (type === 'home_service') {
      // Store current context: we are inside the booking form (not initial open)
      _pendingBookingTechId = _pendingBookingTechId || '__inForm__';
      _msgAgreementContext = 'serviceSwitch';
      showAgreementModal('home_service');
    } else {
      selectMsgServiceType('shop_fix');
    }
  };

  // When agreement modal is shown, detect service type from the button that was clicked
  window.openBookingFormForService = function(techId, serviceType) {
    _pendingBookingTechId = techId;
    showAgreementModal(serviceType || 'shop_fix');
  };

  function previewMsgPhoto(input) {
    const prev = document.getElementById('msgBfPreview');
    const img  = document.getElementById('msgBfPreviewImg');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) { img.src = e.target.result; prev.style.display = 'block'; };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function clearMsgPhoto() {
    document.getElementById('msgBfPhoto').value = '';
    document.getElementById('msgBfPreview').style.display = 'none';
  }

  function selectMsgServiceType(type) {
    document.getElementById('msgBfServiceType').value = type;
    var homeEl = document.getElementById('msgSvcHome');
    var shopEl = document.getElementById('msgSvcShop');
    if (type === 'home_service') {
      homeEl.style.border = '2px solid var(--fg-primary)';
      homeEl.style.background = 'rgba(230,168,0,0.07)';
      shopEl.style.border = '2px solid var(--fg-border)';
      shopEl.style.background = 'var(--fg-bg)';
    } else {
      shopEl.style.border = '2px solid var(--fg-primary)';
      shopEl.style.background = 'rgba(230,168,0,0.07)';
      homeEl.style.border = '2px solid var(--fg-border)';
      homeEl.style.background = 'var(--fg-bg)';
    }
    updateServiceMap(type);
  }

  function customerLogout() {
    FGAuth.showLogoutModal(function() {
      sessionStorage.removeItem('fg_user');
      fetch('/api/logout').finally(() => { window.location.href = '/login.html'; });
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
  function toggleNotifDropdown() {
    const d = document.getElementById('notifDropdown');
    if (d.style.display === 'none' || !d.style.display) {
      d.style.display = 'block';
      setTimeout(() => document.addEventListener('click', closeNotifOutside), 0);
    } else {
      d.style.display = 'none';
      document.removeEventListener('click', closeNotifOutside);
    }
  }
  function closeNotifOutside(e) {
    if (!document.getElementById('notifWrap').contains(e.target)) {
      document.getElementById('notifDropdown').style.display = 'none';
      document.removeEventListener('click', closeNotifOutside);
    }
  }
  function markAllRead() {}
  </script>

  <!-- Booking Form Modal — includes MapLibre for service location map -->
  
  <div id="msgBookingModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:560px;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.5);">

      <!-- Header -->
      <div style="background:linear-gradient(135deg,var(--fg-primary) 0%,#c98f00 100%);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:0.6rem;">
          <i class="bi bi-calendar-check-fill" style="color:#fff;font-size:1rem;"></i>
          <div>
            <div style="color:#fff;font-weight:800;font-size:1rem;">Repair Booking Form</div>
            <div style="color:rgba(255,255,255,0.75);font-size:0.73rem;margin-top:0.1rem;">Fill in your repair details</div>
          </div>
        </div>
        <button onclick="closeBookingForm()"
          style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;transition:background 0.2s;"
          onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">&#x2715;</button>
      </div>

      <!-- Body -->
      <div style="padding:1.35rem;overflow-y:auto;flex:1;">
        <div id="msgBookingAlert" style="display:none;padding:0.65rem 1rem;border-radius:10px;font-size:0.83rem;font-weight:600;margin-bottom:1rem;align-items:center;gap:0.5rem;"></div>

        <!-- Service Type Toggle -->
        <div style="margin-bottom:1.1rem;">
          <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.5rem;">Service Type <span style="color:#dc3545;">*</span></label>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.6rem;">
            <label id="msgSvcHome" onclick="msgPickServiceType('home_service')" style="display:flex;align-items:center;gap:0.7rem;padding:0.75rem 1rem;border:2px solid var(--fg-border);border-radius:12px;cursor:pointer;transition:all 0.2s;background:var(--fg-bg);">
              <span style="font-size:1.5rem;">🏠</span>
              <div>
                <div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);">Home Service</div>
                <div style="font-size:0.72rem;color:var(--fg-muted);">Technician visits you</div>
              </div>
            </label>
            <label id="msgSvcShop" onclick="msgPickServiceType('shop_fix')" style="display:flex;align-items:center;gap:0.7rem;padding:0.75rem 1rem;border:2px solid var(--fg-primary);border-radius:12px;cursor:pointer;transition:all 0.2s;background:rgba(230,168,0,0.07);">
              <span style="font-size:1.5rem;">🏪</span>
              <div>
                <div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);">In-Shop Fix</div>
                <div style="font-size:0.72rem;color:var(--fg-muted);">Bring to technician shop</div>
              </div>
            </label>
          </div>
          <input type="hidden" id="msgBfServiceType" value="shop_fix">
        </div>

        <!-- Service Location Map -->
        <div style="margin-bottom:1.1rem;">
          <div id="msgSvcMapInfo" style="font-size:0.82rem;padding:0.5rem 0.75rem;background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:8px 8px 0 0;line-height:1.5;"></div>
          <div id="msgSvcMapEl" style="height:160px;border-radius:0 0 10px 10px;overflow:hidden;border:1px solid var(--fg-border);border-top:none;display:none;"></div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.85rem;">

          <!-- Full Name -->
          <div>
            <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.35rem;">Full Name <span style="color:#dc3545;">*</span></label>
            <input type="text" id="msgBfName" placeholder="Your full name"
              style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;font-family:inherit;"
              onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'">
          </div>

          <!-- Contact Number -->
          <div>
            <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.35rem;">Contact Number <span style="color:#dc3545;">*</span></label>
            <input type="tel" id="msgBfContact" placeholder="09XX XXX XXXX"
              style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;font-family:inherit;"
              onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'">
          </div>

          <!-- Address -->
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.35rem;">Address <span style="color:#dc3545;">*</span></label>
            <input type="text" id="msgBfAddress" placeholder="House/Unit, Street, Barangay, City"
              style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;font-family:inherit;"
              onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)';if(document.getElementById('msgBfServiceType').value==='home_service')updateServiceMap('home_service')">
          </div>

          <!-- Device Name -->
          <div>
            <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.35rem;">Device Name <span style="color:#dc3545;">*</span></label>
            <input type="text" id="msgBfDevice" placeholder="e.g. iPhone 14, Samsung S23"
              style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;font-family:inherit;"
              onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'">
          </div>

          <!-- Preferred Schedule -->
          <div>
            <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.35rem;">Preferred Schedule</label>
            <input type="datetime-local" id="msgBfSchedule"
              style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;font-family:inherit;"
              onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'">
          </div>

          <!-- Fault Description -->
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.35rem;">Fault Description <span style="color:#dc3545;">*</span></label>
            <textarea id="msgBfFault" rows="3" placeholder="Describe the problem with your device…"
              style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;resize:vertical;font-family:inherit;"
              onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'"></textarea>
          </div>

          <!-- History of Phone -->
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.35rem;">History of Phone</label>
            <textarea id="msgBfHistory" rows="2" placeholder="Previous repairs, drops, water damage, etc."
              style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;resize:vertical;font-family:inherit;"
              onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'"></textarea>
          </div>

          <!-- Expected Fix -->
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.35rem;">Expected Fix</label>
            <textarea id="msgBfExpected" rows="2" placeholder="What do you expect to be fixed or resolved?"
              style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;resize:vertical;font-family:inherit;"
              onfocus="this.style.borderColor='var(--fg-primary)'" onblur="this.style.borderColor='var(--fg-border)'"></textarea>
          </div>

          <!-- Photo Upload -->
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:0.78rem;font-weight:700;color:var(--fg-text);margin-bottom:0.35rem;">
              <i class="bi bi-camera-fill" style="color:var(--fg-primary);"></i>
              Photo of Phone
              <span style="font-weight:400;color:var(--fg-muted);font-size:0.73rem;">(optional — helps the technician assess the issue)</span>
            </label>
            <label for="msgBfPhoto"
              style="display:flex;align-items:center;gap:0.75rem;padding:0.7rem 1rem;border:2px dashed var(--fg-border);border-radius:10px;cursor:pointer;transition:border-color 0.2s;background:var(--fg-bg);"
              onmouseenter="this.style.borderColor='var(--fg-primary)'" onmouseleave="this.style.borderColor='var(--fg-border)'">
              <i class="bi bi-camera-fill" style="color:var(--fg-primary);font-size:1.1rem;"></i>
              <span style="font-size:0.82rem;color:var(--fg-muted);">Click to upload a photo of your phone</span>
            </label>
            <input type="file" id="msgBfPhoto" accept="image/*" style="display:none;" onchange="previewMsgPhoto(this)">
            <div id="msgBfPreview" style="display:none;margin-top:0.6rem;position:relative;">
              <img id="msgBfPreviewImg" style="width:100%;max-height:180px;object-fit:contain;border-radius:10px;border:1px solid var(--fg-border);">
              <button onclick="clearMsgPhoto()" style="position:absolute;top:0.4rem;right:0.4rem;width:26px;height:26px;border-radius:50%;background:rgba(220,53,69,0.9);border:none;color:#fff;cursor:pointer;font-size:0.78rem;display:flex;align-items:center;justify-content:center;">&#x2715;</button>
            </div>
          </div>

        </div>

        <!-- Submit Button -->
        <button id="msgSubmitBookingBtn" onclick="submitMsgBooking()"
          style="width:100%;margin-top:1.25rem;padding:0.85rem;border-radius:12px;background:var(--fg-primary);color:#000;border:none;font-weight:800;font-size:0.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:opacity 0.2s;"
          onmouseenter="this.style.opacity='0.88'" onmouseleave="this.style.opacity='1'">
          <i class="bi bi-calendar-check-fill"></i> Submit Booking
        </button>
      </div>
    </div>
  </div>
  
  <script>
    document.getElementById('msgBookingModal').addEventListener('click', function(e) {
      if (e.target === this) closeBookingForm();
    });
  </script>

  <!-- ── Agreement / Disclaimer Modal — shown BEFORE booking form ── -->
  <div id="agreementModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.78);backdrop-filter:blur(6px);z-index:10500;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:560px;max-height:94vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.6);" onclick="event.stopPropagation()">

      <!-- Header -->
      <div style="background:linear-gradient(135deg,#dc3545,#b02a37);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:0.6rem;">
          <i class="bi bi-file-earmark-text-fill" style="color:#fff;font-size:1.05rem;"></i>
          <div>
            <div id="agreementModalTitle" style="color:#fff;font-weight:800;font-size:1rem;">Repair Service Agreement</div>
            <div style="color:rgba(255,255,255,0.75);font-size:0.72rem;margin-top:0.1rem;">Read the full agreement before proceeding</div>
          </div>
        </div>
        <button onclick="closeAgreementModal()"
          style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;"
          onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">&#x2715;</button>
      </div>

      <!-- Scroll hint -->
      <div id="agreementScrollHint" style="display:flex;align-items:center;gap:0.5rem;background:rgba(220,53,69,0.1);border-bottom:1px solid rgba(220,53,69,0.2);padding:0.5rem 1.1rem;font-size:0.78rem;color:#dc3545;font-weight:600;flex-shrink:0;">
        <i class="bi bi-arrow-down-circle-fill"></i>
        <span>Please scroll down and read the entire agreement to enable the Agree button</span>
      </div>

      <!-- Scrollable body -->
      <div id="agreementScrollArea" style="overflow-y:auto;flex:1;padding:1.25rem 1.35rem;">
        <div id="agreementBody"></div>
      </div>

      <!-- Footer -->
      <div style="padding:1rem 1.35rem;border-top:1px solid var(--fg-border);background:var(--fg-card-bg);flex-shrink:0;display:flex;gap:0.65rem;">
        <button onclick="closeAgreementModal()"
          style="flex:1;padding:0.7rem;border-radius:10px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-weight:700;font-size:0.88rem;cursor:pointer;"
          onmouseenter="this.style.borderColor='var(--fg-text)';this.style.color='var(--fg-text)'" onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-muted)'">
          Cancel
        </button>
        <button id="agreementAgreeBtn" onclick="agreeAndProceed()"
          style="flex:2;padding:0.7rem;border-radius:10px;border:none;background:#dc3545;color:#fff;font-weight:800;font-size:0.9rem;cursor:not-allowed;opacity:0.5;transition:all 0.2s;"
          onmouseenter="if(!this.disabled)this.style.background='#b02a37'" onmouseleave="if(!this.disabled)this.style.background='#dc3545'">
          <i class="bi bi-check-circle-fill"></i> I Agree &amp; Proceed
        </button>
      </div>

    </div>
  </div>
  <script>
    document.getElementById('agreementModal').addEventListener('click', function(e) {
      if (e.target === this) closeAgreementModal();
    });
  </script>

  <!-- ── Mobile Chat: clicking a conversation switches view ── -->
  <script>
    (function() {
      var chatWrap = document.querySelector('.chat-wrap');
      if (!chatWrap) return;

      // Add back button to chat panel header on mobile
      function addBackBtn() {
        if (window.innerWidth > 768) return;
        var chatHead = chatWrap.querySelector('.chat-head');
        if (!chatHead || chatHead.querySelector('.msg-back-btn')) return;
        var btn = document.createElement('button');
        btn.className = 'msg-back-btn';
        btn.innerHTML = '<i class="bi bi-arrow-left"></i>';
        btn.style.cssText = 'background:none;border:none;color:var(--fg-text);font-size:1.1rem;cursor:pointer;padding:0.2rem 0.4rem 0.2rem 0;flex-shrink:0;';
        btn.addEventListener('click', function() {
          chatWrap.classList.remove('chat-active');
        });
        chatHead.insertBefore(btn, chatHead.firstChild);
      }

      // Intercept conversation item clicks on mobile
      var convList = chatWrap.querySelector('.conv-list');
      if (convList) {
        convList.addEventListener('click', function(e) {
          if (window.innerWidth <= 768) {
            var item = e.target.closest('.conv-item');
            if (item) {
              setTimeout(function() {
                chatWrap.classList.add('chat-active');
                addBackBtn();
              }, 50);
            }
          }
        });
      }

      // If a conversation was pre-selected via URL param, activate chat view
      if (window.innerWidth <= 768) {
        var params = new URLSearchParams(location.search);
        if (params.get('with') || params.get('other_id')) {
          setTimeout(function() {
            chatWrap.classList.add('chat-active');
            addBackBtn();
          }, 300);
        }
      }
    })();
  </script>

  <!-- ── Mobile Bottom Nav ── -->
  <nav id="msgBottomNav" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;background:var(--fg-card-bg);border-top:1px solid var(--fg-border);padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));box-shadow:0 -4px 20px rgba(0,0,0,0.15);">
    <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-house-fill" style="font-size:1.25rem;"></i>Home</a></li>
      <li><a href="/index.php#shop" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-shop" style="font-size:1.25rem;"></i>Shop</a></li>
      <li><a href="/index.php#technicians" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-workspace" style="font-size:1.25rem;"></i>Technicians</a></li>
      <li><a href="notifications.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-bell-fill" style="font-size:1.25rem;"></i>Inbox</a></li>
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-fill" style="font-size:1.25rem;"></i>Me</a></li>
    </ul>
  </nav>
  <script>
    (function(){ var nb=document.getElementById('msgBottomNav'); function c(){ var m=window.innerWidth<=991; nb.style.display=m?'block':'none'; if(m) document.body.style.paddingBottom='70px'; else document.body.style.paddingBottom=''; } c(); window.addEventListener('resize',c); })();
  </script>

</body>
</html>




