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
  <title>Fix&amp;Go — Technician Messages</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8.1" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5.1" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .tc-layout { display:flex;min-height:calc(100vh - 68px); }
    .tc-sidebar { width:240px;flex-shrink:0;background:var(--fg-card-bg);border-right:1px solid var(--fg-border);padding:1.5rem 0 2rem;position:sticky;top:68px;height:calc(100vh - 68px);overflow-y:auto; }
    .sidebar-profile { display:flex;align-items:center;gap:0.85rem;padding:0 1.25rem 1.25rem;border-bottom:1px solid var(--fg-border);margin-bottom:0.75rem; }
    .sidebar-avatar { width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,rgba(139,92,246,0.25),rgba(139,92,246,0.08));border:2px solid rgba(139,92,246,0.35);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#8b5cf6;font-weight:800;flex-shrink:0;overflow:hidden; }
    .sidebar-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
    .sidebar-profile-name { font-size:0.88rem;font-weight:700;color:var(--fg-text); }
    .sidebar-profile-role { font-size:0.72rem;color:var(--fg-muted); }
    .sidebar-label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);padding:0.75rem 1.25rem 0.35rem; }
    .sidebar-nav { list-style:none;padding:0;margin:0; }
    .sidebar-nav a { display:flex;align-items:center;gap:0.75rem;padding:0.6rem 1.25rem;color:var(--fg-muted);text-decoration:none;font-size:0.88rem;font-weight:500;border-left:3px solid transparent;transition:all 0.2s; }
    .sidebar-nav a:hover { color:#8b5cf6;background:rgba(139,92,246,0.07);border-left-color:#8b5cf6; }
    .sidebar-nav a.active { color:#8b5cf6;background:rgba(139,92,246,0.1);border-left-color:#8b5cf6;font-weight:700; }
    .sidebar-nav a i { font-size:1rem;width:20px;text-align:center; }
    .tc-main { flex:1;padding:2rem;min-width:0; }
    /* Messages layout */
    .msg-layout { display:grid;grid-template-columns:320px 1fr;gap:1.25rem;height:calc(100vh - 180px);min-height:500px; }
    .conv-panel { background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column; }
    .conv-head { padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between; }
    .conv-head h6 { margin:0;font-weight:700;font-size:0.95rem;color:var(--fg-text); }
    .conv-search { padding:0.75rem 1rem;border-bottom:1px solid var(--fg-border); }
    .conv-search input { width:100%;padding:0.45rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.83rem;outline:none;transition:border-color 0.2s; }
    .conv-search input:focus { border-color:#8b5cf6; }
    .conv-list { flex:1;overflow-y:auto; }
    .conv-item { display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;cursor:pointer;border-bottom:1px solid var(--fg-border);transition:background 0.15s; }
    .conv-item:hover { background:rgba(139,92,246,0.05); }
    .conv-item.active { background:rgba(139,92,246,0.1); }
    .conv-avatar { width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,rgba(139,92,246,0.2),rgba(139,92,246,0.06));border:2px solid rgba(139,92,246,0.25);display:flex;align-items:center;justify-content:center;font-size:1rem;color:#8b5cf6;font-weight:800;flex-shrink:0; }
    .conv-info { flex:1;min-width:0; }
    .conv-name { font-size:0.85rem;font-weight:700;color:var(--fg-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .conv-preview { font-size:0.75rem;color:var(--fg-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:0.1rem; }
    .conv-meta { display:flex;flex-direction:column;align-items:flex-end;gap:0.25rem;flex-shrink:0; }
    .conv-time { font-size:0.68rem;color:var(--fg-muted); }
    .conv-unread { background:#8b5cf6;color:#fff;font-size:0.65rem;font-weight:800;padding:0.1rem 0.4rem;border-radius:10px;min-width:18px;text-align:center; }
    /* Chat panel */
    .chat-panel { background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column; }
    .chat-head { padding:1rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;gap:0.75rem; }
    .chat-head-avatar { width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,rgba(139,92,246,0.2),rgba(139,92,246,0.06));border:2px solid rgba(139,92,246,0.25);display:flex;align-items:center;justify-content:center;font-size:0.9rem;color:#8b5cf6;font-weight:800;flex-shrink:0; }
    .chat-head-name { font-weight:700;font-size:0.95rem;color:var(--fg-text); }
    .chat-head-role { font-size:0.72rem;color:var(--fg-muted); }
    .chat-messages { flex:1;overflow-y:auto;padding:1.25rem;display:flex;flex-direction:column;gap:0.75rem; }
    .msg-bubble { max-width:70%;padding:0.65rem 0.9rem;border-radius:14px;font-size:0.85rem;line-height:1.5;word-break:break-word; }
    .msg-bubble.mine { background:#8b5cf6;color:#fff;border-bottom-right-radius:4px;align-self:flex-end; }
    .msg-bubble.theirs { background:var(--fg-bg);border:1px solid var(--fg-border);color:var(--fg-text);border-bottom-left-radius:4px;align-self:flex-start; }
    .msg-time { font-size:0.65rem;opacity:0.65;margin-top:0.2rem;display:block; }
    .msg-row { display:flex;flex-direction:column; }
    .msg-row.mine { align-items:flex-end; }
    .msg-row.theirs { align-items:flex-start; }
    .chat-input-area { padding:1rem 1.25rem;border-top:1px solid var(--fg-border);display:flex;gap:0.75rem;align-items:flex-end; }
    .chat-input { flex:1;padding:0.65rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;resize:none;max-height:120px;font-family:inherit;transition:border-color 0.2s; }
    .chat-input:focus { border-color:#8b5cf6; }
    .chat-send-btn { padding:0.65rem 1.25rem;border-radius:10px;background:#8b5cf6;color:#fff;border:none;font-weight:700;font-size:0.88rem;cursor:pointer;transition:all 0.2s;flex-shrink:0; }
    .chat-send-btn:hover { background:#7c3aed;transform:translateY(-1px); }
    .chat-send-btn:disabled { opacity:0.5;cursor:not-allowed;transform:none; }
    .chat-empty { flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--fg-muted);text-align:center;padding:2rem; }
    .chat-empty i { font-size:3rem;margin-bottom:1rem;opacity:0.3; }
    .sidebar-toggle { display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem; }
    .sidebar-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199; }
    .sidebar-overlay.open { display:block; }
    @keyframes spin { to { transform:rotate(360deg); } }
    @media(max-width:992px){
      .msg-layout{grid-template-columns:260px 1fr;}
      .chat-panel{height:500px;}
    }
    @media (max-width:768px) {
      .sidebar-toggle { display:flex;align-items:center; }
      .tc-sidebar { position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s; }
      .tc-sidebar.open { transform:translateX(0); }
      .tc-main { padding:1.25rem; }
      .msg-layout { grid-template-columns:1fr;height:auto; }
      .chat-panel { height:calc(100dvh - 280px);min-height:320px; }
      .conv-panel { max-height:220px; }
    }
    @media (max-width:480px) {
      .tc-main { padding:0.75rem; }
      .chat-send-btn { padding:0.55rem 0.85rem !important; font-size:0.82rem !important; }
      .chat-input { font-size:0.85rem !important; padding:0.5rem 0.75rem !important; }
    }
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
      <a href="../../../index.php?browse=1" class="btn btn-sm" style="border:1.5px solid rgba(139,92,246,0.4);border-radius:8px;color:#8b5cf6;background:rgba(139,92,246,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:0.35rem;"><i class="bi bi-house-door"></i> Browse Shop</a>
      <span class="role-badge" style="background:rgba(139,92,246,0.12);color:#8b5cf6;border:1px solid rgba(139,92,246,0.25);padding:0.25rem 0.75rem;border-radius:50px;font-size:0.75rem;font-weight:700;">🔧 Technician</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <button id="logoutBtn" class="btn btn-sm"
         style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
    </div>
  </nav>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="tc-layout">
    <aside class="tc-sidebar" id="tcSidebar">
      <div class="sidebar-profile">
        <div class="sidebar-avatar" id="sidebarAvatar">🔧</div>
        <div>
          <div class="sidebar-profile-name" id="sidebarName">Technician</div>
          <div class="sidebar-profile-role">🔧 Phone Technician</div>
        </div>
      </div>
      <div class="sidebar-label">Main</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="repairs.php"><i class="bi bi-tools"></i> Repair Bookings</a></li>
        <li><a href="inventory.php"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="marketplace.php"><i class="bi bi-shop"></i> Marketplace</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="messages.php" class="active"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
      </ul>
      <div class="sidebar-label">Account</div>
      <ul class="sidebar-nav">
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>

    <main class="tc-main">
      <div style="margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
        <div>
          <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;">
            <i class="bi bi-chat-dots-fill" style="color:#8b5cf6;margin-right:0.5rem;"></i>Messages
          </h2>
          <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">Chat with your customers</p>
        </div>
        <button onclick="openNewConvModal()" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.55rem 1.1rem;border-radius:10px;background:#8b5cf6;color:#fff;border:none;font-weight:700;font-size:0.85rem;cursor:pointer;transition:all 0.2s;" onmouseenter="this.style.background='#7c3aed'" onmouseleave="this.style.background='#8b5cf6'">
          <i class="bi bi-plus-lg"></i> New Message
        </button>      </div>

      <div class="msg-layout">
        <!-- Conversations list -->
        <div class="conv-panel">
          <div class="conv-head">
            <h6><i class="bi bi-chat-left-dots" style="color:#8b5cf6;margin-right:0.4rem;"></i>Conversations</h6>
            <span id="convCount" style="font-size:0.75rem;color:var(--fg-muted);"></span>
          </div>
          <div class="conv-search">
            <input type="text" id="convSearchInput" placeholder="Search conversations…" oninput="filterConvs(this.value)">
          </div>
          <div class="conv-list" id="convList">
            <div style="text-align:center;padding:2rem;color:var(--fg-muted);">
              <div style="width:24px;height:24px;border:3px solid var(--fg-border);border-top-color:#8b5cf6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
              Loading…
            </div>
          </div>
        </div>

        <!-- Chat area -->
        <div class="chat-panel" id="chatPanel">
          <div class="chat-empty" id="chatEmpty">
            <i class="bi bi-chat-dots"></i>
            <h6 style="font-weight:700;color:var(--fg-text);margin-bottom:0.5rem;">Select a conversation</h6>
            <p style="font-size:0.85rem;margin:0;">Choose a conversation from the left or start a new one.</p>
          </div>
          <div id="chatActive" style="display:none;flex:1;display:none;flex-direction:column;height:100%;">
            <div class="chat-head" id="chatHead">
              <div class="chat-head-avatar" id="chatHeadAvatar">?</div>
              <div>
                <div class="chat-head-name" id="chatHeadName">—</div>
                <div class="chat-head-role" id="chatHeadRole">—</div>
              </div>
            </div>
            <div class="chat-messages" id="chatMessages"></div>
            <div id="techChatFilePreview" style="display:none;padding:0.5rem 1.25rem 0;border-top:1px solid var(--fg-border);align-items:center;gap:0.75rem;background:var(--fg-bg);">
              <div id="techChatFileThumb" style="font-size:0.82rem;color:var(--fg-text);flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;"></div>
              <button onclick="clearTechChatFile()" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:1rem;flex-shrink:0;" title="Remove">✕</button>
            </div>
            <div class="chat-input-area">
              <label for="techChatFileInput" title="Send photo or video" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:1.5px solid var(--fg-border);cursor:pointer;flex-shrink:0;color:var(--fg-muted);font-size:1rem;transition:all 0.2s;background:var(--fg-bg);" onmouseenter="this.style.borderColor='#8b5cf6';this.style.color='#8b5cf6'" onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-muted)'">
                <i class="bi bi-paperclip"></i>
              </label>
              <input type="file" id="techChatFileInput" accept="image/*,video/mp4,video/webm,video/quicktime" style="display:none;" onchange="handleTechChatFile(this)">
              <textarea class="chat-input" id="chatInput" rows="1" placeholder="Type a message…"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"
                oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,120)+'px'"></textarea>
              <button class="chat-send-btn" id="chatSendBtn" onclick="sendMessage()">
                <i class="bi bi-send-fill"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- New Conversation Modal -->
  <div id="newConvModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;box-shadow:0 24px 64px rgba(0,0,0,0.4);width:100%;max-width:440px;">
      <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;">
        <h5 style="margin:0;font-weight:800;font-size:1rem;color:var(--fg-text);"><i class="bi bi-person-plus-fill" style="color:#8b5cf6;margin-right:0.5rem;"></i>New Message</h5>
        <button onclick="closeNewConvModal()" style="width:30px;height:30px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:0.9rem;"><i class="bi bi-x-lg"></i></button>
      </div>
      <div style="padding:1.25rem 1.5rem;">
        <div style="margin-bottom:1rem;">
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.4rem;">Search Customer, Supplier or Owner</label>
          <input type="text" id="userSearchInput" placeholder="Type name or email…"
            style="width:100%;padding:0.6rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;transition:border-color 0.2s;"
            onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='var(--fg-border)'"
            oninput="searchUsers(this.value)">
        </div>
        <div id="userSearchResults" style="max-height:220px;overflow-y:auto;border:1px solid var(--fg-border);border-radius:10px;display:none;"></div>
        <p style="font-size:0.78rem;color:var(--fg-muted);margin:0.75rem 0 0;">You can message customers, suppliers, and owners.</p>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script>
  'use strict';
  const MSG_API = '../../../api/messages';
  let myId = 0;
  let activeConvId = null;
  let allConvs = [];
  let pollTimer = null;

  function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
  function fmtTime(d) {
    if (!d) return '';
    const dt = new Date(d);
    const now = new Date();
    const diff = now - dt;
    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return Math.floor(diff/60000) + 'm ago';
    if (diff < 86400000) return dt.toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'});
    return dt.toLocaleDateString('en-PH',{month:'short',day:'numeric'});
  }

  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'phone_technician') {
      window.location.href = '../../../login.html';
      return;
    }
    myId = user.id || 0;
    const fullName = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
    document.getElementById('navUserName').textContent = fullName || user.email;
    document.getElementById('sidebarName').textContent = fullName || user.email;
    const initials = ((user.firstName||'')[0]||'') + ((user.lastName||'')[0]||'');
    const av = document.getElementById('sidebarAvatar');
    if (initials) av.textContent = initials.toUpperCase();

    const sidebar = document.getElementById('tcSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', function () {
      sidebar.classList.toggle('open'); overlay.classList.toggle('open');
    });
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open'); overlay.classList.remove('open');
    });

    // Check URL for pre-selected conversation
    const params = new URLSearchParams(window.location.search);
    const preConvId  = parseInt(params.get('conv_id')   || '0');
    const preOtherId = parseInt(params.get('other_id')  || params.get('with') || '0');

    loadConversations().then(() => {
      if (preConvId)   openConversation(preConvId, null, null);
      else if (preOtherId) openConvWithUser(preOtherId);
    });

    // Poll for new messages every 8 seconds
    pollTimer = setInterval(function () {
      loadConversations(true);
      if (activeConvId) loadMessages(activeConvId, true);
    }, 8000);
  });

  function loadConversations(silent) {
    return fetch(MSG_API + '?action=conversations', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        allConvs = d.conversations || [];
        renderConvList(allConvs);
        document.getElementById('convCount').textContent = allConvs.length + ' conversation' + (allConvs.length !== 1 ? 's' : '');
      }).catch(() => {});
  }

  function renderConvList(convs) {
    const el = document.getElementById('convList');
    if (!convs.length) {
      el.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--fg-muted);font-size:0.85rem;">No conversations yet.<br>Start one with a customer.</div>';
      return;
    }
    el.innerHTML = convs.map(c => {
      const initials = (c.other_name||'?').split(' ').map(w=>w[0]||'').join('').toUpperCase().slice(0,2);
      const isActive = c.id == activeConvId;
      const unread = parseInt(c.unread_count||0);
      return `<div class="conv-item${isActive?' active':''}" onclick="openConversation(${c.id},'${esc(c.other_name||'User')}','${esc(c.other_role||'')}')">
        <div class="conv-avatar">${esc(initials)}</div>
        <div class="conv-info">
          <div class="conv-name">${esc(c.other_name||'User')}</div>
          <div class="conv-preview">${esc(c.last_message||'No messages yet')}</div>
        </div>
        <div class="conv-meta">
          <span class="conv-time">${fmtTime(c.last_message_at||c.updated_at)}</span>
          ${unread > 0 ? `<span class="conv-unread">${unread}</span>` : ''}
        </div>
      </div>`;
    }).join('');
  }

  function filterConvs(q) {
    const filtered = q.trim()
      ? allConvs.filter(c => (c.other_name||'').toLowerCase().includes(q.toLowerCase()))
      : allConvs;
    renderConvList(filtered);
  }

  function openConversation(convId, otherName, otherRole) {
    activeConvId = convId;
    // Find conv data if not passed
    if (!otherName) {
      const c = allConvs.find(x => x.id == convId);
      if (c) { otherName = c.other_name; otherRole = c.other_role; }
    }
    // Update active state in list
    document.querySelectorAll('.conv-item').forEach(el => el.classList.remove('active'));
    const items = document.querySelectorAll('.conv-item');
    items.forEach(el => { if (el.onclick && el.onclick.toString().includes('(' + convId + ',')) el.classList.add('active'); });

    // Show chat panel
    document.getElementById('chatEmpty').style.display = 'none';
    const chatActive = document.getElementById('chatActive');
    chatActive.style.display = 'flex';
    chatActive.style.flexDirection = 'column';
    chatActive.style.height = '100%';

    const initials = (otherName||'?').split(' ').map(w=>w[0]||'').join('').toUpperCase().slice(0,2);
    document.getElementById('chatHeadAvatar').textContent = initials;
    document.getElementById('chatHeadName').textContent = otherName || 'User';
    const roleLabels = { customer:'👤 Customer', phone_technician:'🔧 Technician', sales_person:'💼 Sales Person', owner:'🏪 Owner', supplier:'📦 Supplier' };
    document.getElementById('chatHeadRole').textContent = roleLabels[otherRole] || otherRole || '';

    loadMessages(convId);
    document.getElementById('chatInput').focus();
  }

  function openConvWithUser(otherId) {
    fetch(MSG_API + '?action=get_or_create&other_id=' + otherId, { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          loadConversations().then(() => openConversation(d.conv_id, d.other_name, d.other_role));
        }
      }).catch(() => {});
  }

  function loadMessages(convId, silent) {
    fetch(MSG_API + '?action=messages&conv_id=' + convId, { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        myId = d.my_id || myId;
        const el = document.getElementById('chatMessages');
        if (!d.messages || !d.messages.length) {
          el.innerHTML = '<div style="text-align:center;color:var(--fg-muted);font-size:0.85rem;padding:1rem;">No messages yet. Say hello!</div>';
          return;
        }
        const wasAtBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 60;
        el.innerHTML = d.messages.map(m => {
          const mine = m.sender_id == myId;
          const time = fmtTime(m.created_at);

          // Build media content
          let mediaHtml = '';
          if (m.file_url) {
            const src = '../../../' + m.file_url;
            if (m.file_type === 'image') {
              mediaHtml = `<div style="margin-bottom:${m.body?'0.4rem':'0'};">
                <img src="${esc(src)}" alt="${esc(m.file_name||'Photo')}"
                  style="max-width:220px;max-height:220px;border-radius:10px;cursor:pointer;display:block;object-fit:cover;"
                  onclick="openTechMediaViewer('${esc(src)}','image')"
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

          const bodyText = m.body ? esc(m.body) : '';
          return `<div class="msg-row ${mine?'mine':'theirs'}">
            <div class="msg-bubble ${mine?'mine':'theirs'}">${mediaHtml}${bodyText}<span class="msg-time">${time}</span></div>
          </div>`;
        }).join('');
        if (!silent || wasAtBottom) el.scrollTop = el.scrollHeight;
        loadConversations(true);
      }).catch(() => {});
  }

  function sendMessage() {
    const input = document.getElementById('chatInput');
    const body  = input.value.trim();
    const fileInput = document.getElementById('techChatFileInput');
    const hasFile = fileInput && fileInput.files && fileInput.files[0];

    if ((!body && !hasFile) || !activeConvId) return;

    const conv = allConvs.find(c => c.id == activeConvId);
    if (!conv) return;

    const btn = document.getElementById('chatSendBtn');
    btn.disabled = true;
    input.value = '';
    input.style.height = 'auto';

    if (hasFile) {
      const fd = new FormData();
      fd.append('action',   'send');
      fd.append('other_id', conv.other_id);
      if (body) fd.append('body', body);
      fd.append('attachment', fileInput.files[0]);

      fetch(MSG_API, { method: 'POST', credentials: 'include', body: fd })
        .then(r => r.json())
        .then(d => {
          if (d.success) { clearTechChatFile(); loadMessages(activeConvId); }
          else { input.value = body; alert(d.message || 'Failed to send.'); }
          btn.disabled = false;
        }).catch(() => { btn.disabled = false; });
    } else {
      fetch(MSG_API, {
        method: 'POST', credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'send', other_id: conv.other_id, body })
      })
        .then(r => r.json())
        .then(d => {
          if (d.success) loadMessages(activeConvId);
          else { input.value = body; alert(d.message || 'Failed to send.'); }
          btn.disabled = false;
        }).catch(() => { input.value = body; btn.disabled = false; });
    }
  }

  function handleTechChatFile(input) {
    const file = input.files[0];
    if (!file) return;
    const previewEl = document.getElementById('techChatFilePreview');
    const thumbEl   = document.getElementById('techChatFileThumb');
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
      thumbEl.innerHTML = `<i class="bi bi-camera-video-fill" style="color:#8b5cf6;margin-right:0.4rem;"></i> ${esc(file.name)}`;
    }
  }

  function clearTechChatFile() {
    const fi = document.getElementById('techChatFileInput');
    const pv = document.getElementById('techChatFilePreview');
    if (fi) fi.value = '';
    if (pv) pv.style.display = 'none';
  }

  function openTechMediaViewer(src, type) {
    const overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.92);z-index:99999;display:flex;align-items:center;justify-content:center;cursor:pointer;';
    overlay.onclick = () => document.body.removeChild(overlay);
    if (type === 'image') {
      overlay.innerHTML = `<img src="${src}" style="max-width:94vw;max-height:92vh;border-radius:10px;object-fit:contain;">`;
    }
    document.body.appendChild(overlay);
  }

  // New conversation modal
  let searchTimer = null;
  function openNewConvModal() {
    document.getElementById('newConvModal').style.display = 'flex';
    document.getElementById('userSearchInput').value = '';
    document.getElementById('userSearchResults').style.display = 'none';
    document.getElementById('userSearchResults').innerHTML = '';
    setTimeout(() => document.getElementById('userSearchInput').focus(), 100);
  }
  function closeNewConvModal() {
    document.getElementById('newConvModal').style.display = 'none';
  }
  document.addEventListener('click', function(e) {
    const modal = document.getElementById('newConvModal');
    if (e.target === modal) closeNewConvModal();
  });

  function searchUsers(q) {
    clearTimeout(searchTimer);
    const resultsEl = document.getElementById('userSearchResults');
    if (q.trim().length < 2) { resultsEl.style.display = 'none'; return; }
    searchTimer = setTimeout(() => {
      fetch(MSG_API + '?action=search_users&q=' + encodeURIComponent(q), { credentials: 'include' })
        .then(r => r.json())
        .then(d => {
          if (!d.success || !d.users || !d.users.length) {
            resultsEl.style.display = 'block';
            resultsEl.innerHTML = '<div style="padding:1rem;text-align:center;color:var(--fg-muted);font-size:0.83rem;">No customers found.</div>';
            return;
          }
          resultsEl.style.display = 'block';
          resultsEl.innerHTML = d.users.map(u => {
            const name = esc(((u.first_name||'') + ' ' + (u.last_name||'')).trim() || u.email);
            const initials = name.split(' ').map(w=>w[0]||'').join('').toUpperCase().slice(0,2);
            return `<div onclick="startConvWith(${u.id},'${name}')" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;cursor:pointer;border-bottom:1px solid var(--fg-border);transition:background 0.15s;" onmouseenter="this.style.background='rgba(139,92,246,0.07)'" onmouseleave="this.style.background='transparent'">
              <div style="width:36px;height:36px;border-radius:50%;background:rgba(139,92,246,0.12);border:2px solid rgba(139,92,246,0.25);display:flex;align-items:center;justify-content:center;font-size:0.85rem;color:#8b5cf6;font-weight:800;flex-shrink:0;">${esc(initials)}</div>
              <div>
                <div style="font-size:0.85rem;font-weight:700;color:var(--fg-text);">${name}</div>
                <div style="font-size:0.72rem;color:var(--fg-muted);">${esc(u.email||'')} · ${({customer:'👤 Customer',supplier:'📦 Supplier',owner:'🏪 Owner',phone_technician:'🔧 Technician',sales_person:'💼 Sales'})[u.role]||u.role}</div>
              </div>
            </div>`;
          }).join('');
        }).catch(() => {});
    }, 300);
  }

  function startConvWith(otherId, name) {
    closeNewConvModal();
    fetch(MSG_API + '?action=get_or_create&other_id=' + otherId, { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          loadConversations().then(() => openConversation(d.conv_id, d.other_name || name, d.other_role));
        }
      }).catch(() => {});
  }
  </script>

</body>
</html>




