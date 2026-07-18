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
  <title>Fix&amp;Go â€” Messages</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5" />
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
    .sp-main{flex:1;padding:1.5rem;min-width:0;display:flex;flex-direction:column;}
    /* Chat layout */
    .chat-wrap{display:grid;grid-template-columns:300px 1fr;border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;background:var(--fg-card-bg);flex:1;min-height:0;height:calc(100vh - 68px - 3rem);}
    /* Conv list */
    .conv-panel{border-right:1px solid var(--fg-border);display:flex;flex-direction:column;min-height:0;}
    .conv-head{padding:0.85rem 1.25rem;border-bottom:1px solid var(--fg-border);font-weight:700;font-size:0.9rem;color:var(--fg-text);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
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
    /* Chat panel */
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
    /* New conv button */
    .btn-new-conv{padding:0.3rem 0.7rem;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:0.3rem;}
    .btn-new-conv:hover{border-color:var(--fg-primary);color:var(--fg-primary);}
    /* Date divider */
    .date-divider{text-align:center;font-size:0.72rem;color:var(--fg-muted);margin:0.5rem 0;position:relative;}
    .date-divider::before,.date-divider::after{content:'';position:absolute;top:50%;width:30%;height:1px;background:var(--fg-border);}
    .date-divider::before{left:0;}.date-divider::after{right:0;}
    /* Sidebar toggle */
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    /* New conv modal */
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center;padding:1rem;}
    .modal-overlay.open{display:flex;}
    .modal-box{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.4);width:100%;max-width:440px;animation:modalIn 0.2s ease;}
    @keyframes modalIn{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}}
    .modal-head{padding:1.1rem 1.5rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;}
    .modal-head h5{margin:0;font-weight:800;font-size:0.95rem;}
    .modal-body{padding:1.25rem 1.5rem;}
    .customer-item{display:flex;align-items:center;gap:0.75rem;padding:0.65rem 0.85rem;border-radius:10px;cursor:pointer;transition:background 0.15s;}
    .customer-item:hover{background:rgba(230,168,0,0.08);}
    .customer-item-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--fg-primary),#c98f00);display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:800;color:#fff;flex-shrink:0;}
    .customer-item-name{font-size:0.88rem;font-weight:700;color:var(--fg-text);}
    .customer-item-sub{font-size:0.75rem;color:var(--fg-muted);}
    @keyframes spin{to{transform:rotate(360deg)}}
    @media(max-width:992px){.chat-wrap{grid-template-columns:240px 1fr;}}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .sp-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .sp-sidebar.open{transform:translateX(0);}
      .sp-main{padding:0.75rem;}
      .chat-wrap{grid-template-columns:1fr;height:auto;}
      .conv-panel{height:220px;border-right:none;border-bottom:1px solid var(--fg-border);}
      .chat-panel{height:calc(100vh - 68px - 220px - 1.5rem);}
    }
  </style>
</head>
<body>
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>ðŸ”§ Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge sales_person">ðŸ’¼ Sales Person</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <!-- Messages icon â€” active (you are here) -->
      <a href="messages.php" style="position:relative;text-decoration:none;" title="Messages">
        <div style="background:rgba(230,168,0,0.12);border:1.5px solid var(--fg-primary);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;color:var(--fg-primary);">
          <i class="bi bi-chat-dots-fill"></i>
        </div>
        <span id="navMsgBadge" style="position:absolute;top:-4px;right:-4px;background:var(--fg-primary);color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;display:none;"></span>
      </a>
      <a href="../../../dashboard.php" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Back
      </a>
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
        <li><a href="messages.php" class="active"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
        <li><a href="profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
        <li><a href="settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
      </ul>
    </aside>

    <main class="sp-main">
      <div class="chat-wrap">
        <!-- Conversation list -->
        <div class="conv-panel">
          <div class="conv-head">
            <span><i class="bi bi-chat-dots-fill" style="color:#3b82f6;margin-right:0.4rem;"></i>Chats</span>
            <button class="btn-new-conv" id="btnNewConv" title="Message a customer">
              <i class="bi bi-pencil-square"></i> New
            </button>
          </div>
          <div class="conv-search">
            <input type="text" id="convSearch" placeholder="Search conversationsâ€¦">
          </div>
          <div class="conv-list" id="convList">
            <div style="text-align:center;padding:2rem;color:var(--fg-muted);font-size:0.85rem;">
              <div style="width:22px;height:22px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
              Loadingâ€¦
            </div>
          </div>
        </div>

        <!-- Chat area -->
        <div class="chat-panel" id="chatPanel">
          <div class="chat-empty" id="chatEmpty">
            <i class="bi bi-chat-square-dots" style="font-size:3rem;opacity:0.25;"></i>
            <span style="font-size:0.9rem;font-weight:600;">Select a conversation</span>
            <span style="font-size:0.82rem;">or start a new one with a customer</span>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- New conversation modal -->
  <div class="modal-overlay" id="newConvModal">
    <div class="modal-box">
      <div class="modal-head">
        <h5><i class="bi bi-person-plus-fill" style="color:var(--fg-primary);margin-right:0.4rem;"></i>Message a Customer</h5>
        <button onclick="closeNewConvModal()" style="width:28px;height:28px;border-radius:7px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <input type="text" id="customerSearch" placeholder="Search customers by nameâ€¦"
               style="width:100%;padding:0.5rem 0.85rem;border-radius:9px;border:1.5px solid var(--fg-border);background:var(--fg-bg);color:var(--fg-text);font-size:0.85rem;outline:none;margin-bottom:0.75rem;"
               oninput="filterCustomers(this.value)">
        <div id="customerList" style="max-height:280px;overflow-y:auto;">
          <div style="text-align:center;padding:1.5rem;color:var(--fg-muted);font-size:0.85rem;">
            <div style="width:20px;height:20px;border:3px solid var(--fg-border);border-top-color:var(--fg-primary);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
            Loading customersâ€¦
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script src="../../../../assets/js/session-timeout.js"></script>
  <script>
  const API = '../../../api/messages';
  let myId = null;
  let activeConvId = null;
  let pollTimer = null;
  let allConvs = [];
  let allCustomers = [];

  document.addEventListener('DOMContentLoaded', function () {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'sales_person') { window.location.href = '../../../login.html'; return; }
    myId = user.id || null;
    document.getElementById('navUserName').textContent = ((user.firstName||'') + ' ' + (user.lastName||'')).trim() || user.email;

    const sidebar = document.getElementById('spSidebar'), overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

    document.getElementById('btnNewConv').addEventListener('click', openNewConvModal);
    document.getElementById('convSearch').addEventListener('input', function () { renderConvList(this.value); });

    // Check URL param ?with=userId
    const params = new URLSearchParams(window.location.search);
    const withId = parseInt(params.get('with'));

    loadConversations().then(() => {
      if (withId) openOrCreateConv(withId);
    });

    // Poll for new messages every 5s
    pollTimer = setInterval(() => {
      if (activeConvId) loadMessages(activeConvId, false);
      else loadConversations();
    }, 5000);
  });

  // â”€â”€ Load conversations â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
      list.innerHTML = `<div style="text-align:center;padding:2rem;color:var(--fg-muted);font-size:0.85rem;">
        ${q ? 'No results.' : 'No conversations yet.<br>Click <b>New</b> to message a customer.'}
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

  // â”€â”€ Open conversation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
  function openConv(convId, otherName, otherId) {
    activeConvId = convId;
    renderConvList(document.getElementById('convSearch').value);

    const panel = document.getElementById('chatPanel');
    const initials = otherName.split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2) || '?';
    const color = stringToColor(otherName);

    panel.innerHTML = `
      <div class="chat-head">
        <div class="conv-avatar" style="background:${color};width:36px;height:36px;font-size:0.85rem;">${initials}</div>
        <div>
          <div class="chat-head-name">${esc(otherName)}</div>
          <div class="chat-head-sub">ðŸ‘¤ Customer</div>
        </div>
        <div style="margin-left:auto;">
          <a href="orders.php" style="font-size:0.78rem;color:var(--fg-primary);font-weight:600;text-decoration:none;">
            <i class="bi bi-cart3"></i> View Orders
          </a>
        </div>
      </div>
      <div class="chat-messages" id="chatMessages">
        <div style="text-align:center;padding:2rem;color:var(--fg-muted);font-size:0.85rem;">
          <div style="width:20px;height:20px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.5rem;"></div>
          Loading messagesâ€¦
        </div>
      </div>
      <div class="chat-input-area">
        <textarea class="chat-input" id="chatInput" rows="1" placeholder="Type a messageâ€¦"></textarea>
        <button class="btn-send" id="btnSend"><i class="bi bi-send-fill"></i></button>
      </div>`;

    // Wire up send
    const input = document.getElementById('chatInput');
    input.addEventListener('input', function () {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(otherId); }
    });
    document.getElementById('btnSend').addEventListener('click', () => sendMessage(otherId));

    loadMessages(convId, true);
  }

  // â”€â”€ Load messages â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
  let lastMsgCount = 0;
  function loadMessages(convId, scrollToBottom) {
    fetch(`${API}?action=messages&conv_id=${convId}`, { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) {
          if (d.expired) { window.location.href = '../../../login.html'; return; }
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

    let html = '';
    let lastDate = '';
    msgs.forEach(m => {
      const d = new Date(m.created_at);
      const dateStr = d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
      if (dateStr !== lastDate) {
        html += `<div class="date-divider">${dateStr}</div>`;
        lastDate = dateStr;
      }
      const isMe = parseInt(m.sender_id) === parseInt(myId);
      const time = d.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' });
      html += `<div class="msg-wrap ${isMe ? 'out' : 'in'}">
        <div class="msg-bubble ${isMe ? 'out' : 'in'}">${esc(m.body)}</div>
        <div class="msg-time">${time}</div>
      </div>`;
    });
    container.innerHTML = html;
    if (scrollToBottom) container.scrollTop = container.scrollHeight;
  }

  // â”€â”€ Send message â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
  function sendMessage(otherId) {
    const input = document.getElementById('chatInput');
    const btn   = document.getElementById('btnSend');
    const body  = input.value.trim();
    if (!body) return;

    btn.disabled = true;
    fetch(API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'send', other_id: otherId, body })
    })
      .then(r => r.json())
      .then(d => {
        if (d.expired) { window.location.href = '../../../login.html'; return; }
        if (d.success) {
          input.value = '';
          input.style.height = 'auto';
          loadMessages(activeConvId, true);
        }
      })
      .catch(() => {})
      .finally(() => { btn.disabled = false; });
  }

  // â”€â”€ Open or create conversation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
  function openOrCreateConv(otherId) {
    fetch(`${API}?action=get_or_create&other_id=${otherId}`, { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        // Add to list if not there
        if (!allConvs.find(c => c.id === d.conv_id)) {
          allConvs.unshift({
            id: d.conv_id, other_id: d.other_id,
            other_name: d.other_name, other_role: d.other_role,
            last_message: null, last_message_at: null, unread_count: 0
          });
        }
        renderConvList('');
        openConv(d.conv_id, d.other_name, d.other_id);
        closeNewConvModal();
      }).catch(() => {});
  }

  // â”€â”€ New conversation modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
  function openNewConvModal() {
    document.getElementById('newConvModal').classList.add('open');
    document.getElementById('customerSearch').value = '';
    loadCustomers();
  }
  function closeNewConvModal() {
    document.getElementById('newConvModal').classList.remove('open');
  }
  document.getElementById('newConvModal').addEventListener('click', function (e) {
    if (e.target === this) closeNewConvModal();
  });

  function loadCustomers() {
    fetch('../../../backend/messages.php?action=conversations', { credentials: 'include' })
      .then(() => {}) // already loaded
      .catch(() => {});

    // Load customers who ordered from this sales person
    fetch('../../../backend/sales_orders.php?action=list', { credentials: 'include' })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        // Deduplicate by customer_id
        const seen = new Set();
        allCustomers = (d.orders || []).reduce((acc, o) => {
          if (!seen.has(o.customer_id)) {
            seen.add(o.customer_id);
            acc.push({
              id: o.customer_id,
              name: ((o.first_name||'') + ' ' + (o.last_name||'')).trim() || 'Customer',
              email: o.customer_email || ''
            });
          }
          return acc;
        }, []);
        filterCustomers('');
      })
      .catch(() => {
        document.getElementById('customerList').innerHTML =
          '<div style="text-align:center;padding:1.5rem;color:var(--fg-muted);font-size:0.85rem;">Could not load customers.</div>';
      });
  }

  function filterCustomers(q) {
    const list = document.getElementById('customerList');
    let customers = allCustomers;
    if (q) customers = customers.filter(c => c.name.toLowerCase().includes(q.toLowerCase()) || c.email.toLowerCase().includes(q.toLowerCase()));

    if (!customers.length) {
      list.innerHTML = `<div style="text-align:center;padding:1.5rem;color:var(--fg-muted);font-size:0.85rem;">
        ${q ? 'No customers found.' : 'No customers have ordered yet.'}
      </div>`;
      return;
    }

    list.innerHTML = customers.map(c => {
      const initials = c.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2) || '?';
      return `<div class="customer-item" onclick="openOrCreateConv(${c.id})">
        <div class="customer-item-avatar">${initials}</div>
        <div>
          <div class="customer-item-name">${esc(c.name)}</div>
          <div class="customer-item-sub">${esc(c.email)}</div>
        </div>
      </div>`;
    }).join('');
  }

  // â”€â”€ Helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
  </script>
<script src="../../../assets/js/pwa.js" defer></script>
</body>
</html>


