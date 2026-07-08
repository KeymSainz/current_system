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
  <title>Fix&amp;Go — My Products</title>
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
    .product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.25rem;}
    .product-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s,border-color 0.2s;}
    .product-card:hover{transform:translateY(-4px);box-shadow:0 10px 28px rgba(139,92,246,0.14);border-color:#8b5cf6;}
    .product-img{width:100%;aspect-ratio:1/1;object-fit:cover;background:var(--fg-bg);}
    .product-img-ph{width:100%;aspect-ratio:1/1;background:linear-gradient(135deg,rgba(139,92,246,0.08),rgba(139,92,246,0.03));display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--fg-muted);}
    .product-body{padding:0.85rem;}
    .product-cat{font-size:0.65rem;font-weight:700;color:#8b5cf6;background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.2);padding:0.1rem 0.45rem;border-radius:50px;display:inline-block;margin-bottom:0.3rem;}
    .product-name{font-size:0.82rem;font-weight:700;color:var(--fg-text);line-height:1.3;margin-bottom:0.25rem;}
    .product-brand{font-size:0.72rem;color:var(--fg-muted);margin-bottom:0.4rem;}
    .product-footer{display:flex;align-items:center;justify-content:space-between;margin-top:0.4rem;}
    .product-price{font-size:0.95rem;font-weight:800;color:#8b5cf6;}
    .product-qty{font-size:0.7rem;color:var(--fg-muted);background:var(--fg-bg);border:1px solid var(--fg-border);padding:0.1rem 0.45rem;border-radius:6px;}
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
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <a href="../../../dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="../../../assets/images/logo.png" alt="Fix&amp;Go" style="height:48px;width:auto;object-fit:contain;" onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <a href="../../../index.php?browse=1" class="btn btn-sm" style="border:1.5px solid rgba(139,92,246,0.4);border-radius:8px;color:#8b5cf6;background:rgba(139,92,246,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:0.35rem;"><i class="bi bi-house-door"></i> Browse Shop</a>
      <span style="background:rgba(139,92,246,0.12);color:#8b5cf6;border:1px solid rgba(139,92,246,0.25);padding:0.25rem 0.75rem;border-radius:50px;font-size:0.75rem;font-weight:700;">🔧 Technician</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <button id="logoutBtn" class="btn btn-sm" style="border:1.5px solid rgba(220,53,69,0.4);border-radius:8px;color:#dc3545;background:rgba(220,53,69,0.07);font-size:0.85rem;font-weight:600;cursor:pointer;"><i class="bi bi-box-arrow-right"></i> Logout</button>
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
        <li><a href="products.php" class="active"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
      </ul>
      <div class="sidebar-label">Account</div>
      <ul class="sidebar-nav"><li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li></ul>
    </aside>
    <main class="tc-main">
      <div style="margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
        <div>
          <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;"><i class="bi bi-box-seam" style="color:#8b5cf6;margin-right:0.5rem;"></i>My Products</h2>
          <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">Products currently visible to customers. Manage visibility in <a href="inventory.php" style="color:#8b5cf6;font-weight:600;">Inventory</a>.</p>
        </div>
        <span id="productCount" style="font-size:0.85rem;color:var(--fg-muted);font-weight:600;"></span>
      </div>
      <!-- Filters -->
      <div class="filter-bar">
        <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Search products…" oninput="applyFilters()" style="min-width:220px;">
        <div id="catTabs" style="display:flex;gap:0.5rem;flex-wrap:wrap;"></div>
      </div>
      <!-- Grid -->
      <div id="productGrid">
        <div style="text-align:center;padding:3rem;color:var(--fg-muted);">
          <div style="width:32px;height:32px;border:3px solid var(--fg-border);border-top-color:#8b5cf6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.75rem;"></div>
          Loading products…
        </div>
      </div>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script src="../../assets/js/session-timeout.js"></script>
  <script>
  'use strict';
  const API='../../../backend/technician_dashboard.php';
  let allProducts=[];
  function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
  function peso(n){return '₱'+parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:0});}

  document.addEventListener('DOMContentLoaded',function(){
    const user=FGAuth.UserStore.get();
    if(!user||user.role!=='phone_technician'){window.location.href='../../../login.html';return;}
    document.getElementById('navUserName').textContent=((user.firstName||'')+' '+(user.lastName||'')).trim()||user.email;
    const sidebar=document.getElementById('tcSidebar'),overlay=document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click',function(){sidebar.classList.toggle('open');overlay.classList.toggle('open');});
    overlay.addEventListener('click',function(){sidebar.classList.remove('open');overlay.classList.remove('open');});
    loadProducts();
  });

  function loadProducts(){
    fetch(API+'?action=products',{credentials:'include'}).then(r=>r.json()).then(d=>{
      if(!d.success){document.getElementById('productGrid').innerHTML='<div style="text-align:center;padding:3rem;color:var(--fg-muted);">Could not load products.</div>';return;}
      allProducts=d.products||[];
      document.getElementById('productCount').textContent=allProducts.length+' product'+(allProducts.length!==1?'s':'')+' displayed';
      buildCatTabs();
      renderGrid(allProducts);
    }).catch(()=>{document.getElementById('productGrid').innerHTML='<div style="text-align:center;padding:3rem;color:var(--fg-muted);">Network error.</div>';});
  }

  function buildCatTabs(){
    const cats=['All',...new Set(allProducts.map(i=>i.category).filter(Boolean))];
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
    let items=allProducts;
    if(activeCat!=='All') items=items.filter(i=>i.category===activeCat);
    if(q) items=items.filter(i=>(i.name||'').toLowerCase().includes(q)||(i.brand||'').toLowerCase().includes(q));
    renderGrid(items);
  }

  function renderGrid(items){
    const el=document.getElementById('productGrid');
    if(!items.length){
      el.innerHTML='<div style="text-align:center;padding:3rem;color:var(--fg-muted);"><i class="bi bi-box-seam" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.3;"></i>'+(allProducts.length?'No products match your search.':'No products displayed yet. Go to <a href="inventory.php" style="color:#8b5cf6;font-weight:600;">Inventory</a> to make products visible.')+'</div>';
      return;
    }
    el.innerHTML='<div class="product-grid">'+items.map(item=>{
      const imgHtml=item.image_path?`<img src="../../../${esc(item.image_path)}" class="product-img" alt="${esc(item.name)}" onerror="this.parentElement.innerHTML='<div class=\\'product-img-ph\\'>📦</div>'">`:'<div class="product-img-ph">📦</div>';
      return `<div class="product-card">
        ${imgHtml}
        <div class="product-body">
          <span class="product-cat">${esc(item.category||'—')}</span>
          <div class="product-name">${esc(item.name)}</div>
          ${item.brand?`<div class="product-brand">${esc(item.brand)}</div>`:''}
          <div class="product-footer">
            <span class="product-price">${peso(item.price)}</span>
            <span class="product-qty">Qty: ${item.quantity}</span>
          </div>
        </div>
      </div>`;
    }).join('')+'</div>';
  }
  </script>

</body>
</html>




