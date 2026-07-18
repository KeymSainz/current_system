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
  <link rel="manifest" href="../../../manifest.json">
  <link rel="apple-touch-icon" href="../../../assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="../../../assets/css/mobile.css">
  <title>Fix&amp;Go — Technician Inventory</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../assets/css/auth.css?v=8.1" />
  <link rel="stylesheet" href="../../../assets/css/supplier.css?v=5.1" />
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
    .stats-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:1.75rem;}
    .stat-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1.1rem 1rem;text-align:center;}
    .stat-value{font-size:1.8rem;font-weight:800;line-height:1;}
    .stat-label{font-size:0.72rem;color:var(--fg-muted);font-weight:600;margin-top:0.2rem;}
    .product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.25rem;}
    /* Mobile: 2-column grid like the shop */
    @media(max-width:768px){
      .product-grid{grid-template-columns:repeat(2,1fr)!important;gap:0.75rem!important;}
      .stats-row{grid-template-columns:repeat(2,1fr)!important;gap:0.65rem!important;}
      .filter-bar{gap:0.5rem!important;}
      .filter-input{font-size:0.8rem!important;padding:0.4rem 0.7rem!important;}
    }
    .product-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s,border-color 0.2s;}
    .product-card:hover{transform:translateY(-4px);box-shadow:0 10px 28px rgba(139,92,246,0.14);border-color:#8b5cf6;}
    .product-img{width:100%;aspect-ratio:1/1;object-fit:cover;background:var(--fg-bg);}
    .product-img-ph{width:100%;aspect-ratio:1/1;background:linear-gradient(135deg,rgba(139,92,246,0.08),rgba(139,92,246,0.03));display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--fg-muted);}
    .product-body{padding:0.85rem;}
    @media(max-width:768px){
      .product-body{padding:0.6rem 0.65rem!important;}
      .product-name{font-size:0.75rem!important;}
      .product-price{font-size:0.85rem!important;}
      .product-qty{font-size:0.65rem!important;}
      .toggle-btn{font-size:0.65rem!important;padding:0.3rem!important;}
    }
    .product-cat{font-size:0.65rem;font-weight:700;color:#8b5cf6;background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.2);padding:0.1rem 0.45rem;border-radius:50px;display:inline-block;margin-bottom:0.3rem;}
    .product-name{font-size:0.82rem;font-weight:700;color:var(--fg-text);line-height:1.3;margin-bottom:0.25rem;}
    .product-brand{font-size:0.72rem;color:var(--fg-muted);margin-bottom:0.4rem;}
    .product-footer{display:flex;align-items:center;justify-content:space-between;margin-top:0.4rem;}
    .product-price{font-size:0.95rem;font-weight:800;color:#8b5cf6;}
    .product-qty{font-size:0.7rem;color:var(--fg-muted);background:var(--fg-bg);border:1px solid var(--fg-border);padding:0.1rem 0.45rem;border-radius:6px;}
    .toggle-btn{width:100%;margin-top:0.6rem;padding:0.38rem;border-radius:8px;font-size:0.72rem;font-weight:700;cursor:pointer;border:1.5px solid;transition:all 0.2s;}
    .toggle-btn.shown{background:rgba(139,92,246,0.1);border-color:rgba(139,92,246,0.3);color:#8b5cf6;}
    .toggle-btn.shown:hover{background:#8b5cf6;color:#fff;}
    .toggle-btn.hidden{background:rgba(107,114,128,0.08);border-color:var(--fg-border);color:var(--fg-muted);}
    .toggle-btn.hidden:hover{background:rgba(139,92,246,0.1);border-color:#8b5cf6;color:#8b5cf6;}
    .low-stock-badge{display:inline-block;font-size:0.65rem;font-weight:700;background:rgba(245,158,11,0.12);color:#f59e0b;border:1px solid rgba(245,158,11,0.25);padding:0.1rem 0.45rem;border-radius:50px;margin-left:0.35rem;}
    .out-stock-badge{display:inline-block;font-size:0.65rem;font-weight:700;background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);padding:0.1rem 0.45rem;border-radius:50px;margin-left:0.35rem;}
    .filter-bar{display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;align-items:center;}
    .filter-input{padding:0.45rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.83rem;outline:none;transition:border-color 0.2s;}
    .filter-input:focus{border-color:#8b5cf6;}
    .cat-tab{padding:0.38rem 0.9rem;border-radius:50px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.2s;}
    .cat-tab:hover,.cat-tab.active{border-color:#8b5cf6;color:#8b5cf6;background:rgba(139,92,246,0.08);}
    .sidebar-toggle{display:none;background:none;border:1.5px solid var(--fg-border);border-radius:8px;padding:0.3rem 0.6rem;color:var(--fg-text);cursor:pointer;font-size:1.1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
    .sidebar-overlay.open{display:block;}
    @keyframes spin{to{transform:rotate(360deg);}}
    @media(max-width:768px){
      .sidebar-toggle{display:flex;align-items:center;}
      .tc-sidebar{position:fixed;top:68px;left:0;z-index:200;transform:translateX(-100%);height:calc(100vh - 68px);box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s;}
      .tc-sidebar.open{transform:translateX(0);}
      .tc-main{padding:1.25rem;}
    }
  </style>
</head>
<body>
  <nav class="fg-navbar" role="navigation" style="flex-wrap:nowrap !important;">
    <div class="d-flex align-items-center gap-2">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:38px;width:auto;object-fit:contain;" onerror="this.outerHTML='<span style=\'font-size:1rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-2" style="flex-wrap:nowrap;">
      <a href="../../../index.php?browse=1" class="btn btn-sm inv-desk" style="display:none;border:1.5px solid rgba(139,92,246,0.4);border-radius:8px;color:#8b5cf6;background:rgba(139,92,246,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;align-items:center;gap:0.35rem;"><i class="bi bi-house-door"></i> Browse Shop</a>
      <span class="inv-desk" style="display:none;background:rgba(139,92,246,0.12);color:#8b5cf6;border:1px solid rgba(139,92,246,0.25);padding:0.25rem 0.75rem;border-radius:50px;font-size:0.75rem;font-weight:700;">🔧 Technician</span>
      <span id="navUserName" class="inv-desk" style="display:none;font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <button id="logoutBtn" class="btn btn-sm inv-desk" style="display:none;border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>
  </nav>
  <style>
    @media(min-width:992px){ .inv-desk{ display:flex !important; } }
    @media(max-width:991px){ .tc-sidebar{ display:none !important; } .tc-main{ padding-bottom:75px !important; } }
  </style>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="tc-layout">
    <aside class="tc-sidebar" id="tcSidebar">
      <div class="sidebar-label">Main</div>
      <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="repairs.php"><i class="bi bi-tools"></i> Repair Bookings</a></li>
        <li><a href="inventory.php" class="active"><i class="bi bi-clipboard-data"></i> Inventory</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
      </ul>
      <div class="sidebar-label">Account</div>
      <ul class="sidebar-nav"><li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li></ul>
    </aside>
    <main class="tc-main">
      <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;"><i class="bi bi-clipboard-data" style="color:#3b82f6;margin-right:0.5rem;"></i>Inventory</h2>
        <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">Manage your product inventory and control what customers see.</p>
      </div>
      <!-- Stats -->
      <div class="stats-row" id="invStatsRow">
        <div class="stat-card"><div class="stat-value" style="color:#3b82f6;" id="invTotal">—</div><div class="stat-label">Total Items</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#28A745;" id="invInStock">—</div><div class="stat-label">In Stock</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#f59e0b;" id="invLow">—</div><div class="stat-label">Low Stock</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#dc3545;" id="invOut">—</div><div class="stat-label">Out of Stock</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#8b5cf6;" id="invDisplayed">—</div><div class="stat-label">Displayed</div></div>
        <div class="stat-card"><div class="stat-value" style="color:var(--fg-text);" id="invUnits">—</div><div class="stat-label">Total Units</div></div>
      </div>
      <!-- Filters -->
      <div class="filter-bar">
        <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Search products…" oninput="applyFilters()" style="min-width:220px;">
        <div id="catTabs" style="display:flex;gap:0.5rem;flex-wrap:wrap;"></div>
        <select class="filter-input" id="stockFilter" onchange="applyFilters()">
          <option value="all">All Stock</option>
          <option value="in_stock">In Stock</option>
          <option value="low_stock">Low Stock (&lt;10)</option>
          <option value="out_of_stock">Out of Stock</option>
        </select>
        <select class="filter-input" id="displayFilter" onchange="applyFilters()">
          <option value="all">All Visibility</option>
          <option value="shown">Visible to Customers</option>
          <option value="hidden">Hidden</option>
        </select>
      </div>
      <!-- Grid -->
      <div id="productGrid">
        <div style="text-align:center;padding:3rem;color:var(--fg-muted);">
          <div style="width:32px;height:32px;border:3px solid var(--fg-border);border-top-color:#3b82f6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.75rem;"></div>
          Loading inventory…
        </div>
      </div>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script>
  'use strict';
  const API = '../../../backend/technician_dashboard.php';
  let allItems = [];
  function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
  function peso(n){return '₱'+parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:0});}

  document.addEventListener('DOMContentLoaded', function(){
    const user = FGAuth.UserStore.get();
    if(!user||user.role!=='phone_technician'){window.location.href='../../../login.html';return;}
    document.getElementById('navUserName').textContent=((user.firstName||'')+' '+(user.lastName||'')).trim()||user.email;
    const sidebar=document.getElementById('tcSidebar'),overlay=document.getElementById('sidebarOverlay');
    var st=document.getElementById('sidebarToggle');
    if(st&&sidebar&&overlay){
      st.addEventListener('click',function(){sidebar.classList.toggle('open');overlay.classList.toggle('open');});
      overlay.addEventListener('click',function(){sidebar.classList.remove('open');overlay.classList.remove('open');});
    }
    loadStats(); loadInventory();
  });

  function loadStats(){
    fetch(API+'?action=inventory_stats',{credentials:'include'}).then(r=>r.json()).then(d=>{
      if(!d.success)return;
      const s=d.stats;
      document.getElementById('invTotal').textContent=s.total_items||0;
      document.getElementById('invInStock').textContent=s.in_stock||0;
      document.getElementById('invLow').textContent=s.low_stock||0;
      document.getElementById('invOut').textContent=s.out_of_stock||0;
      document.getElementById('invDisplayed').textContent=s.displayed||0;
      document.getElementById('invUnits').textContent=s.total_units||0;
    }).catch(()=>{});
  }

  function loadInventory(){
    fetch(API+'?action=inventory',{credentials:'include'}).then(r=>r.json()).then(d=>{
      if(!d.success){document.getElementById('productGrid').innerHTML='<div style="text-align:center;padding:3rem;color:var(--fg-muted);">Could not load inventory.</div>';return;}
      allItems=d.items||[];
      buildCatTabs();
      renderGrid(allItems);
    }).catch(()=>{document.getElementById('productGrid').innerHTML='<div style="text-align:center;padding:3rem;color:var(--fg-muted);">Network error.</div>';});
  }

  function buildCatTabs(){
    const cats=['All',...new Set(allItems.map(i=>i.category).filter(Boolean))];
    document.getElementById('catTabs').innerHTML=cats.map(c=>`<button class="cat-tab${c==='All'?' active':''}" onclick="setCat('${esc(c)}',this)">${esc(c)}</button>`).join('');
  }

  let activeCat='All';
  function setCat(cat,btn){
    activeCat=cat;
    document.querySelectorAll('.cat-tab').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    applyFilters();
  }

  function applyFilters(){
    const q=document.getElementById('searchInput').value.toLowerCase();
    const stock=document.getElementById('stockFilter').value;
    const disp=document.getElementById('displayFilter').value;
    let items=allItems;
    if(activeCat!=='All') items=items.filter(i=>i.category===activeCat);
    if(q) items=items.filter(i=>(i.name||'').toLowerCase().includes(q)||(i.brand||'').toLowerCase().includes(q)||(i.category||'').toLowerCase().includes(q));
    if(stock==='in_stock') items=items.filter(i=>parseInt(i.quantity)>0);
    if(stock==='low_stock') items=items.filter(i=>parseInt(i.quantity)>0&&parseInt(i.quantity)<10);
    if(stock==='out_of_stock') items=items.filter(i=>parseInt(i.quantity)===0);
    if(disp==='shown') items=items.filter(i=>parseInt(i.is_displayed)===1);
    if(disp==='hidden') items=items.filter(i=>parseInt(i.is_displayed)===0);
    renderGrid(items);
  }

  function renderGrid(items){
    const el=document.getElementById('productGrid');
    if(!items.length){el.innerHTML='<div style="text-align:center;padding:3rem;color:var(--fg-muted);"><i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.3;"></i>No items match your filters.</div>';return;}
    el.innerHTML='<div class="product-grid">'+items.map(item=>{
      const qty=parseInt(item.quantity||0);
      const shown=parseInt(item.is_displayed||0)===1;
      const stockBadge=qty===0?'<span class="out-stock-badge">Out of Stock</span>':qty<10?'<span class="low-stock-badge">Low Stock</span>':'';
      const imgHtml=item.image_path?`<img src="../../../${esc(item.image_path)}" class="product-img" alt="${esc(item.name)}" onerror="this.parentElement.innerHTML='<div class=\\'product-img-ph\\'>📦</div>'">`:'<div class="product-img-ph">📦</div>';
      return `<div class="product-card">
        ${imgHtml}
        <div class="product-body">
          <span class="product-cat">${esc(item.category||'—')}</span>
          <div class="product-name">${esc(item.name)}${stockBadge}</div>
          ${item.brand?`<div class="product-brand">${esc(item.brand)}</div>`:''}
          <div class="product-footer">
            <span class="product-price">${peso(item.price)}</span>
            <span class="product-qty">Qty: ${qty}</span>
          </div>
          <button class="toggle-btn ${shown?'shown':'hidden'}" id="tb_${item.id}" onclick="toggleDisplay(${item.id},this)">
            ${shown?'👁 Visible to Customers':'🚫 Hidden from Customers'}
          </button>
          <button onclick="openEditModal(${item.id})" style="width:100%;margin-top:0.5rem;padding:0.38rem;border-radius:8px;font-size:0.72rem;font-weight:700;cursor:pointer;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);transition:all 0.2s;" onmouseenter="this.style.borderColor='#8b5cf6';this.style.color='#8b5cf6'" onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-muted)'">
            ✏️ Edit Price / Image
          </button>
        </div>
      </div>`;
    }).join('')+'</div>';
  }

  function toggleDisplay(productId,btn){
    btn.disabled=true;
    fetch(API,{method:'POST',credentials:'include',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'toggle_display',product_id:productId})})
      .then(r=>r.json()).then(d=>{
        if(d.success){
          const shown=d.is_displayed===1;
          btn.className='toggle-btn '+(shown?'shown':'hidden');
          btn.textContent=shown?'👁 Visible to Customers':'🚫 Hidden from Customers';
          const item=allItems.find(i=>i.id==productId);
          if(item) item.is_displayed=d.is_displayed;
          loadStats();
        } else alert(d.message||'Failed.');
        btn.disabled=false;
      }).catch(()=>{btn.disabled=false;});
  }
  </script>

  <!-- ── Edit Product Modal ── -->
  <div id="editModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;width:100%;max-width:440px;box-shadow:0 24px 64px rgba(0,0,0,0.4);overflow:hidden;">
      <div style="background:linear-gradient(135deg,#6d28d9,#4c1d95);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;">
        <div style="color:#fff;font-weight:800;font-size:0.95rem;">✏️ Edit Product</div>
        <button onclick="closeEditModal()" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;font-size:0.9rem;cursor:pointer;font-weight:700;">✕</button>
      </div>
      <div style="padding:1.35rem;display:flex;flex-direction:column;gap:1rem;">
        <div id="editProductName" style="font-size:0.88rem;font-weight:700;color:var(--fg-text);background:var(--fg-bg);border-radius:8px;padding:0.5rem 0.75rem;border:1px solid var(--fg-border);"></div>

        <!-- Current image preview -->
        <div style="text-align:center;">
          <img id="editCurrentImg" src="" alt="Product" style="width:100px;height:100px;object-fit:cover;border-radius:12px;border:2px solid var(--fg-border);display:none;">
          <div id="editImgPh" style="width:100px;height:100px;border-radius:12px;border:2px dashed var(--fg-border);display:inline-flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--fg-muted);">📦</div>
        </div>

        <!-- Price -->
        <div>
          <label style="font-size:0.8rem;font-weight:700;color:var(--fg-muted);display:block;margin-bottom:0.35rem;">Price (₱) <span style="color:#dc3545;">*</span></label>
          <input type="number" id="editPrice" min="0" step="0.01" placeholder="e.g. 2500"
            style="width:100%;padding:0.55rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:9px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
            onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='var(--fg-border)'">
        </div>

        <!-- Quantity -->
        <div>
          <label style="font-size:0.8rem;font-weight:700;color:var(--fg-muted);display:block;margin-bottom:0.35rem;">Quantity</label>
          <input type="number" id="editQty" min="0" step="1" placeholder="e.g. 10"
            style="width:100%;padding:0.55rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:9px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
            onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='var(--fg-border)'">
        </div>

        <!-- Image upload -->
        <div>
          <label style="font-size:0.8rem;font-weight:700;color:var(--fg-muted);display:block;margin-bottom:0.35rem;">Replace Image (optional)</label>
          <input type="file" id="editImage" accept="image/jpeg,image/png,image/webp"
            style="width:100%;padding:0.45rem 0.75rem;border:1.5px solid var(--fg-border);border-radius:9px;background:var(--fg-bg);color:var(--fg-text);font-size:0.83rem;cursor:pointer;box-sizing:border-box;"
            onchange="previewEditImage(this)">
          <div style="font-size:0.72rem;color:var(--fg-muted);margin-top:0.3rem;">JPG, PNG or WebP · Max 5MB</div>
        </div>

        <div id="editAlert" style="display:none;padding:0.6rem 0.9rem;border-radius:8px;font-size:0.82rem;font-weight:600;"></div>

        <div style="display:flex;gap:0.75rem;justify-content:flex-end;">
          <button onclick="closeEditModal()" style="padding:0.55rem 1.1rem;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.85rem;font-weight:600;cursor:pointer;">Cancel</button>
          <button id="editSaveBtn" onclick="saveProductEdit()" style="padding:0.55rem 1.35rem;border-radius:8px;border:none;background:linear-gradient(135deg,#6d28d9,#4c1d95);color:#fff;font-size:0.85rem;font-weight:700;cursor:pointer;transition:opacity 0.2s;" onmouseenter="this.style.opacity='0.88'" onmouseleave="this.style.opacity='1'">
            <i class="bi bi-check-circle-fill"></i> Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
  let _editProductId = null;

  function openEditModal(id) {
    const item = allItems.find(i => i.id == id);
    if (!item) return;
    _editProductId = id;
    document.getElementById('editProductName').textContent = item.name;
    document.getElementById('editPrice').value  = item.price || '';
    document.getElementById('editQty').value    = item.quantity !== undefined ? item.quantity : '';
    document.getElementById('editImage').value  = '';
    document.getElementById('editAlert').style.display = 'none';

    const img  = document.getElementById('editCurrentImg');
    const ph   = document.getElementById('editImgPh');
    if (item.image_path) {
      img.src = '../../../' + item.image_path;
      img.style.display = 'block';
      ph.style.display  = 'none';
    } else {
      img.style.display = 'none';
      ph.style.display  = 'inline-flex';
    }
    document.getElementById('editModal').style.display = 'flex';
  }

  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    _editProductId = null;
  }

  function previewEditImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = document.getElementById('editCurrentImg');
      const ph  = document.getElementById('editImgPh');
      img.src = e.target.result;
      img.style.display = 'block';
      ph.style.display  = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }

  function saveProductEdit() {
    if (!_editProductId) return;
    const price = document.getElementById('editPrice').value.trim();
    const qty   = document.getElementById('editQty').value.trim();
    const imgFile = document.getElementById('editImage').files[0];
    const alertEl = document.getElementById('editAlert');
    const btn     = document.getElementById('editSaveBtn');

    if (!price) {
      alertEl.style.cssText = 'display:flex;padding:0.6rem 0.9rem;border-radius:8px;font-size:0.82rem;font-weight:600;background:rgba(220,53,69,0.1);color:#dc3545;border:1px solid rgba(220,53,69,0.2);';
      alertEl.textContent = '⚠ Please enter a price.';
      return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';
    alertEl.style.display = 'none';

    const fd = new FormData();
    fd.append('action',     'update_product');
    fd.append('product_id', _editProductId);
    fd.append('price',      price);
    if (qty !== '') fd.append('quantity', qty);
    if (imgFile) fd.append('image', imgFile);

    fetch(API, { method: 'POST', credentials: 'include', body: fd })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          // Update local cache
          const item = allItems.find(i => i.id == _editProductId);
          if (item && d.product) {
            item.price    = d.product.price;
            item.quantity = d.product.quantity;
            if (d.product.image_path) item.image_path = d.product.image_path;
          }
          closeEditModal();
          loadInventory();
          loadStats();
        } else {
          alertEl.style.cssText = 'display:flex;padding:0.6rem 0.9rem;border-radius:8px;font-size:0.82rem;font-weight:600;background:rgba(220,53,69,0.1);color:#dc3545;border:1px solid rgba(220,53,69,0.2);';
          alertEl.textContent = '⚠ ' + (d.message || 'Failed to save.');
        }
      })
      .catch(() => {
        alertEl.style.cssText = 'display:flex;padding:0.6rem 0.9rem;border-radius:8px;font-size:0.82rem;font-weight:600;background:rgba(220,53,69,0.1);color:#dc3545;border:1px solid rgba(220,53,69,0.2);';
        alertEl.textContent = '⚠ Network error.';
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Save Changes';
      });
  }

  document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
  });
  </script>

  <!-- ── Mobile Bottom Nav ── -->
  <nav id="invBottomNav" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;background:var(--fg-card-bg);border-top:1px solid var(--fg-border);padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));box-shadow:0 -4px 20px rgba(0,0,0,0.15);">
    <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
      <li><a href="dashboard.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-house-fill" style="font-size:1.25rem;"></i>Home</a></li>
      <li><a href="repairs.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-tools" style="font-size:1.25rem;"></i>Repairs</a></li>
      <li><a href="inventory.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:#8b5cf6;text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-clipboard-data" style="font-size:1.25rem;"></i>Inventory</a></li>
      <li><a href="messages.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-chat-dots-fill" style="font-size:1.25rem;"></i>Messages</a></li>
      <li><a href="profile.php" style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.5rem;color:var(--fg-muted);text-decoration:none;font-size:0.6rem;font-weight:700;"><i class="bi bi-person-fill" style="font-size:1.25rem;"></i>Me</a></li>
    </ul>
  </nav>
  <script>(function(){var nb=document.getElementById('invBottomNav');function c(){nb.style.display=window.innerWidth<=991?'block':'none';}c();window.addEventListener('resize',c);})();</script>

</body>
</html>




