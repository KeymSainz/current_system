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
  <title>Fix&amp;Go — Marketplace</title>
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
    .filter-bar{display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;align-items:center;}
    .filter-input{padding:0.45rem 0.85rem;border:1.5px solid var(--fg-border);border-radius:8px;background:var(--fg-bg);color:var(--fg-text);font-size:0.83rem;outline:none;transition:border-color 0.2s;}
    .filter-input:focus{border-color:#8b5cf6;}
    .product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:1.25rem;}
    .product-card{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s,border-color 0.2s;display:flex;flex-direction:column;}
    .product-card:hover{transform:translateY(-4px);box-shadow:0 10px 28px rgba(139,92,246,0.14);border-color:#8b5cf6;}
    .product-img{width:100%;aspect-ratio:1/1;object-fit:cover;background:var(--fg-bg);}
    .product-img-ph{width:100%;aspect-ratio:1/1;background:linear-gradient(135deg,rgba(139,92,246,0.08),rgba(139,92,246,0.03));display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--fg-muted);}
    .product-body{padding:0.85rem;flex:1;display:flex;flex-direction:column;}
    .product-cat{font-size:0.65rem;font-weight:700;color:#8b5cf6;background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.2);padding:0.1rem 0.45rem;border-radius:50px;display:inline-block;margin-bottom:0.3rem;}
    .product-name{font-size:0.82rem;font-weight:700;color:var(--fg-text);line-height:1.3;margin-bottom:0.2rem;}
    .product-brand{font-size:0.72rem;color:var(--fg-muted);margin-bottom:0.35rem;}
    .product-supplier{font-size:0.72rem;color:var(--fg-muted);margin-bottom:0.4rem;display:flex;align-items:center;gap:0.3rem;}
    .product-footer{display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:0.5rem;}
    .product-price{font-size:0.95rem;font-weight:800;color:#8b5cf6;}
    .btn-request{width:100%;margin-top:0.6rem;padding:0.42rem;border-radius:8px;font-size:0.78rem;font-weight:700;cursor:pointer;border:1.5px solid #8b5cf6;color:#8b5cf6;background:rgba(139,92,246,0.08);transition:all 0.2s;}
    .btn-request:hover{background:#8b5cf6;color:#fff;}
    /* Modal */
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center;padding:1rem;}
    .modal-overlay.open{display:flex;}
    .modal-box{background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:18px;box-shadow:0 24px 64px rgba(0,0,0,0.4);width:100%;max-width:500px;max-height:90vh;overflow-y:auto;animation:modalIn 0.25s cubic-bezier(0.16,1,0.3,1);}
    @keyframes modalIn{from{opacity:0;transform:scale(0.95) translateY(10px);}to{opacity:1;transform:scale(1) translateY(0);}}
    .modal-head{padding:1.5rem 1.75rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;}
    .modal-head h5{margin:0;font-weight:800;font-size:1.1rem;color:var(--fg-text);}
    .modal-body{padding:1.5rem 1.75rem;}
    .modal-foot{padding:1.25rem 1.75rem;border-top:1px solid var(--fg-border);display:flex;gap:0.75rem;justify-content:flex-end;}
    .form-group{margin-bottom:1.1rem;}
    .form-group label{display:block;font-size:0.82rem;font-weight:700;color:var(--fg-text);margin-bottom:0.4rem;}
    .form-group label span{color:#dc3545;margin-left:2px;}
    .form-input{width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--fg-border);border-radius:10px;background:var(--fg-bg);color:var(--fg-text);font-size:0.88rem;outline:none;transition:border-color 0.2s;font-family:inherit;}
    .form-input:focus{border-color:#8b5cf6;box-shadow:0 0 0 3px rgba(139,92,246,0.12);}
    .alert-bar{padding:0.75rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;}
    .alert-success{background:rgba(40,167,69,0.12);color:#28A745;border:1px solid rgba(40,167,69,0.25);}
    .alert-danger{background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);}
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
        <li><a href="products.php"><i class="bi bi-box-seam"></i> My Products</a></li>
        <li><a href="marketplace.php" class="active"><i class="bi bi-shop"></i> Marketplace</a></li>
        <li><a href="supply-requests.php"><i class="bi bi-send"></i> Supply Requests</a></li>
        <li><a href="messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
      </ul>
      <div class="sidebar-label">Account</div>
      <ul class="sidebar-nav">
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
      </ul>
    </aside>

    <main class="tc-main">
      <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1.4rem;font-weight:800;color:var(--fg-text);margin:0 0 0.2rem;"><i class="bi bi-shop" style="color:#8b5cf6;margin-right:0.5rem;"></i>Marketplace</h2>
        <p style="color:var(--fg-muted);margin:0;font-size:0.85rem;">Browse available products from suppliers</p>
      </div>

      <div id="alertBox" style="display:none;"></div>

      <!-- Stats -->
      <div class="stats-row">
        <div class="stat-card"><div class="stat-value" style="color:#8b5cf6;" id="statTotal">—</div><div class="stat-label">Total Products</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#3b82f6;" id="statCats">—</div><div class="stat-label">Categories</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#10b981;" id="statSuppliers">—</div><div class="stat-label">Sellers</div></div>
      </div>

      <!-- Filters -->
      <div class="filter-bar">
        <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Search products…" oninput="applyFilters()" style="min-width:220px;">
        <select class="filter-input" id="catFilter" onchange="applyFilters()">
          <option value="all">All Categories</option>
        </select>
        <select class="filter-input" id="supplierFilter" onchange="applyFilters()">
          <option value="all">All Sellers</option>
        </select>
      </div>

      <!-- Product Grid -->
      <div id="productGrid">
        <div style="text-align:center;padding:3rem;color:var(--fg-muted);">
          <div style="width:32px;height:32px;border:3px solid var(--fg-border);border-top-color:#8b5cf6;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 0.75rem;"></div>
          Loading marketplace…
        </div>
      </div>
    </main>
  </div>

  <!-- Request Modal -->
  <div class="modal-overlay" id="requestModal">
    <div class="modal-box">
      <div class="modal-head">
        <h5><i class="bi bi-send-fill" style="color:#8b5cf6;margin-right:0.5rem;"></i>Request Product</h5>
        <button onclick="closeModal()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--fg-border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:1rem;transition:all 0.2s;" onmouseenter="this.style.borderColor='#dc3545';this.style.color='#dc3545'" onmouseleave="this.style.borderColor='var(--fg-border)';this.style.color='var(--fg-muted)'"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <!-- Product info preview -->
        <div id="modalProductInfo" style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;padding:1rem;margin-bottom:1.25rem;display:flex;gap:0.85rem;align-items:center;">
          <div id="modalProductImg" style="width:56px;height:56px;border-radius:8px;overflow:hidden;flex-shrink:0;background:rgba(139,92,246,0.08);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--fg-muted);">📦</div>
          <div>
            <div id="modalProductName" style="font-weight:700;font-size:0.9rem;color:var(--fg-text);"></div>
            <div id="modalProductBrand" style="font-size:0.75rem;color:var(--fg-muted);"></div>
            <div id="modalProductPrice" style="font-size:0.9rem;font-weight:800;color:#8b5cf6;margin-top:0.2rem;"></div>
          </div>
        </div>
        <div class="form-group">
          <label>Supplier</label>
          <input type="text" class="form-input" id="modalSupplierName" readonly style="background:var(--fg-bg);opacity:0.75;cursor:not-allowed;">
        </div>
        <div class="form-group">
          <label>Quantity <span>*</span></label>
          <input type="number" class="form-input" id="modalQty" min="1" value="1" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label>Note <span style="color:var(--fg-muted);font-weight:400;">(optional)</span></label>
          <textarea class="form-input" id="modalNote" rows="3" placeholder="Any specific requirements or notes for the supplier…" style="resize:vertical;"></textarea>
        </div>
      </div>
      <div class="modal-foot">
        <button onclick="closeModal()" style="padding:0.55rem 1.25rem;border-radius:10px;border:1.5px solid var(--fg-border);background:transparent;color:var(--fg-muted);font-size:0.88rem;font-weight:600;cursor:pointer;">Cancel</button>
        <button id="btnSubmitRequest" onclick="submitRequest()" style="padding:0.55rem 1.5rem;border-radius:10px;background:#8b5cf6;color:#fff;border:none;font-size:0.88rem;font-weight:700;cursor:pointer;transition:all 0.2s;" onmouseenter="this.style.background='#7c3aed'" onmouseleave="this.style.background='#8b5cf6'">
          <i class="bi bi-send-fill"></i> Submit Request
        </button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../assets/js/theme.js"></script>
  <script src="../../../assets/js/auth-utils.js"></script>
  <script src="../../assets/js/session-timeout.js"></script>
  <script>
  'use strict';
  const MKT_API = '../../../api/technician/marketplace';
  let allProducts = [];
  let selectedProduct = null;

  function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
  function peso(n){return '₱'+parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:0});}

  document.addEventListener('DOMContentLoaded', function(){
    const user = FGAuth.UserStore.get();
    if(!user||user.role!=='phone_technician'){window.location.href='../../../login.html';return;}
    document.getElementById('navUserName').textContent = ((user.firstName||'')+' '+(user.lastName||'')).trim()||user.email;
    const sidebar=document.getElementById('tcSidebar'),overlay=document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle').addEventListener('click',function(){sidebar.classList.toggle('open');overlay.classList.toggle('open');});
    overlay.addEventListener('click',function(){sidebar.classList.remove('open');overlay.classList.remove('open');});
    loadProducts();
    // Sellers are populated from product data, no separate call needed
  });

  function loadProducts(){
    fetch(MKT_API+'?action=browse',{credentials:'include'})
      .then(r=>r.json())
      .then(d=>{
        if(!d.success){showGrid('<div style="text-align:center;padding:3rem;color:var(--fg-muted);">Could not load products.</div>');return;}
        allProducts = d.products||[];
        // Stats
        const cats = new Set(allProducts.map(p=>p.category).filter(Boolean));
        const sellers = new Map();
        allProducts.forEach(p=>{
          if(p.current_holder_id) sellers.set(p.current_holder_id, p.seller_name||p.supplier_name||'Unknown');
        });
        document.getElementById('statTotal').textContent = allProducts.length;
        document.getElementById('statCats').textContent  = cats.size;
        document.getElementById('statSuppliers').textContent = sellers.size;
        // Populate category filter
        const catSel = document.getElementById('catFilter');
        catSel.innerHTML = '<option value="all">All Categories</option>';
        [...cats].sort().forEach(c=>{
          const o = document.createElement('option');
          o.value = c; o.textContent = c;
          catSel.appendChild(o);
        });
        // Populate seller filter
        const supSel = document.getElementById('supplierFilter');
        supSel.innerHTML = '<option value="all">All Sellers</option>';
        sellers.forEach((name, id)=>{
          const o = document.createElement('option');
          o.value = id; o.textContent = name;
          supSel.appendChild(o);
        });
        renderGrid(allProducts);
      }).catch(()=>showGrid('<div style="text-align:center;padding:3rem;color:var(--fg-muted);">Network error.</div>'));
  }

  function loadSuppliers(){
    fetch(MKT_API+'?action=suppliers',{credentials:'include'})
      .then(r=>r.json())
      .then(d=>{
        if(!d.success) return;
        const sel = document.getElementById('supplierFilter');
        sel.innerHTML = '<option value="all">All Suppliers</option>';
        (d.suppliers||[]).forEach(s=>{
          const o = document.createElement('option');
          o.value = s.id;
          o.textContent = ((s.first_name||'')+' '+(s.last_name||'')).trim()||s.email;
          sel.appendChild(o);
        });
      }).catch(()=>{});
  }

  function applyFilters(){
    const q = document.getElementById('searchInput').value.toLowerCase();
    const cat = document.getElementById('catFilter').value;
    const sup = document.getElementById('supplierFilter').value;
    let items = allProducts;
    if(cat!=='all') items=items.filter(p=>p.category===cat);
    if(sup!=='all') items=items.filter(p=>String(p.current_holder_id)===sup||String(p.supplier_id)===sup);
    if(q) items=items.filter(p=>(p.name||'').toLowerCase().includes(q)||(p.brand||'').toLowerCase().includes(q)||(p.category||'').toLowerCase().includes(q)||(p.supplier_name||'').toLowerCase().includes(q)||(p.seller_name||'').toLowerCase().includes(q));
    renderGrid(items);
  }

  function renderGrid(items){
    if(!items.length){
      showGrid('<div style="text-align:center;padding:3rem;color:var(--fg-muted);"><i class="bi bi-shop" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.3;"></i>No products found.</div>');
      return;
    }
    const cards = items.map(p=>{
      const imgHtml = p.image_path
        ? `<img src="../../../${esc(p.image_path)}" class="product-img" alt="${esc(p.name)}" onerror="this.parentElement.innerHTML='<div class=\\'product-img-ph\\'>📦</div>'">`
        : '<div class="product-img-ph">📦</div>';
      return `<div class="product-card">
        ${imgHtml}
        <div class="product-body">
          <span class="product-cat">${esc(p.category||'—')}</span>
          <div class="product-name">${esc(p.name)}</div>
          ${p.brand?`<div class="product-brand">${esc(p.brand)}</div>`:''}
          <div class="product-supplier"><i class="bi bi-shop" style="color:#8b5cf6;"></i>${esc(p.seller_name||p.supplier_name||'Unknown Seller')}</div>
          <div class="product-footer">
            <span class="product-price">${peso(p.price)}</span>
            <span style="font-size:0.7rem;color:var(--fg-muted);background:var(--fg-bg);border:1px solid var(--fg-border);padding:0.1rem 0.45rem;border-radius:6px;">Qty: ${p.qty}</span>
          </div>
          <button class="btn-request" onclick="openRequestModal(${JSON.stringify(p).replace(/"/g,'&quot;')})">
            <i class="bi bi-send"></i> Request
          </button>
        </div>
      </div>`;
    }).join('');
    showGrid('<div class="product-grid">'+cards+'</div>');
  }

  function showGrid(html){document.getElementById('productGrid').innerHTML=html;}

  function openRequestModal(product){
    selectedProduct = product;
    document.getElementById('modalProductName').textContent = product.name||'—';
    document.getElementById('modalProductBrand').textContent = product.brand||'';
    document.getElementById('modalProductPrice').textContent = peso(product.price);
    document.getElementById('modalSupplierName').value = (product.seller_name||product.supplier_name||'—') + (product.supplier_name && product.seller_name !== product.supplier_name ? ' (via ' + product.supplier_name + ')' : '');
    document.getElementById('modalQty').value = 1;
    document.getElementById('modalNote').value = '';
    const imgEl = document.getElementById('modalProductImg');
    if(product.image_path){
      imgEl.innerHTML = `<img src="../../../${esc(product.image_path)}" alt="${esc(product.name)}" style="width:100%;height:100%;object-fit:cover;">`;
    } else {
      imgEl.textContent = '📦';
    }
    document.getElementById('requestModal').classList.add('open');
  }

  function closeModal(){
    document.getElementById('requestModal').classList.remove('open');
    selectedProduct = null;
  }

  document.addEventListener('click', function(e){
    if(e.target === document.getElementById('requestModal')) closeModal();
  });

  function submitRequest(){
    if(!selectedProduct) return;
    const qty = parseInt(document.getElementById('modalQty').value)||0;
    if(qty < 1){showAlert('danger','Quantity must be at least 1.');return;}
    const note = document.getElementById('modalNote').value.trim();
    const btn = document.getElementById('btnSubmitRequest');
    btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting…';

    fetch(MKT_API,{
      method:'POST',credentials:'include',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({action:'request_product',product_id:selectedProduct.id,supplier_id:selectedProduct.supplier_id,quantity:qty,note:note})
    })
      .then(r=>r.json())
      .then(d=>{
        if(!d.success) throw new Error(d.message||'Request failed.');
        closeModal();
        showAlert('success','Request submitted successfully! The supplier will be notified.');
      })
      .catch(err=>showAlert('danger',err.message))
      .finally(()=>{btn.disabled=false;btn.innerHTML='<i class="bi bi-send-fill"></i> Submit Request';});
  }

  function showAlert(type, msg){
    const box = document.getElementById('alertBox');
    box.style.display = 'flex';
    box.className = 'alert-bar alert-'+type;
    box.innerHTML = '<i class="bi bi-'+(type==='success'?'check-circle-fill':'exclamation-triangle-fill')+'"></i> '+esc(msg);
    setTimeout(()=>{box.style.display='none';},6000);
  }
  </script>

</body>
</html>




