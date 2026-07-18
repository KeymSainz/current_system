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
  <title>Fix&Go — Inventory Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=8" />
  <link rel="stylesheet" href="/assets/css/supplier.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); margin: 0; }
    .supervisor-layout { display: block; min-height: calc(100vh - 68px); }
    .supervisor-main { padding: 2rem; min-width: 0; }
    .page-header {
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;
    }
    .page-header h2 { font-size: 1.6rem; font-weight: 800; color: var(--fg-text); margin: 0; }
    .page-header p { color: var(--fg-muted); margin: 0; font-size: 0.88rem; }
    .btn-primary-custom {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.65rem 1.5rem; border-radius: 10px;
      background: var(--fg-primary); color: #fff;
      border: none; font-weight: 700; font-size: 0.9rem;
      cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .btn-primary-custom:hover {
      background: var(--fg-primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(230,168,0,0.35);
      color: #fff;
    }
    .search-filter-bar {
      background: var(--fg-card-bg); border: 1px solid var(--fg-border);
      border-radius: 14px; padding: 1.25rem; margin-bottom: 1.5rem;
      display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;
    }
    .search-box {
      flex: 1; min-width: 250px; position: relative;
    }
    .search-box input {
      width: 100%; padding: 0.65rem 0.9rem 0.65rem 2.5rem;
      border: 1.5px solid var(--fg-border); border-radius: 10px;
      background: var(--fg-bg); color: var(--fg-text);
      font-size: 0.88rem; outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-box input:focus {
      border-color: var(--fg-primary);
      box-shadow: 0 0 0 3px rgba(230,168,0,0.15);
    }
    .search-box i {
      position: absolute; left: 0.9rem; top: 50%;
      transform: translateY(-50%); color: var(--fg-muted);
    }
    .filter-select {
      padding: 0.65rem 0.9rem; border: 1.5px solid var(--fg-border);
      border-radius: 10px; background: var(--fg-bg);
      color: var(--fg-text); font-size: 0.88rem;
      outline: none; cursor: pointer;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .filter-select:focus {
      border-color: var(--fg-primary);
      box-shadow: 0 0 0 3px rgba(230,168,0,0.15);
    }
    .products-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.25rem;
    }
    .product-card {
      background: var(--fg-card-bg); border: 1px solid var(--fg-border);
      border-radius: 14px; overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
      position: relative;
    }
    .product-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }
    .product-card.selected {
      border-color: var(--fg-primary);
      box-shadow: 0 0 0 2px rgba(230,168,0,0.2);
    }
    .product-checkbox {
      position: absolute;
      top: 0.75rem;
      left: 0.75rem;
      width: 24px;
      height: 24px;
      cursor: pointer;
      z-index: 10;
      accent-color: var(--fg-primary);
    }
    .product-image {
      width: 100%; height: 200px; object-fit: cover;
      background: var(--fg-bg);
    }
    .product-body {
      padding: 1.25rem;
    }
    .product-name {
      font-size: 1rem; font-weight: 700; color: var(--fg-text);
      margin-bottom: 0.5rem; line-height: 1.3;
    }
    .product-category {
      font-size: 0.75rem; color: var(--fg-muted);
      text-transform: uppercase; letter-spacing: 0.5px;
      margin-bottom: 0.75rem;
    }
    .product-price {
      font-size: 1.3rem; font-weight: 800; color: var(--fg-primary);
      margin-bottom: 0.75rem;
    }
    .product-stock {
      display: flex; align-items: center; gap: 0.5rem;
      font-size: 0.85rem; margin-bottom: 1rem;
    }
    .stock-badge {
      padding: 0.25rem 0.75rem; border-radius: 20px;
      font-size: 0.7rem; font-weight: 700;
    }
    .stock-in { background: rgba(16,185,129,0.12); color: #10b981; }
    .stock-low { background: rgba(239,68,68,0.12); color: #ef4444; }
    .stock-out { background: rgba(107,114,128,0.12); color: #6b7280; }
    .product-actions {
      display: flex; gap: 0.5rem;
    }
    .btn-action {
      flex: 1; padding: 0.5rem; border-radius: 8px;
      border: 1.5px solid var(--fg-border);
      background: transparent; cursor: pointer;
      font-size: 0.8rem; font-weight: 600;
      transition: all 0.2s; display: flex;
      align-items: center; justify-content: center; gap: 0.3rem;
    }
    .btn-action.edit { border-color: #3b82f6; color: #3b82f6; }
    .btn-action.edit:hover { background: rgba(59,130,246,0.1); }
    .btn-action.delete { border-color: #ef4444; color: #ef4444; }
    .btn-action.delete:hover { background: rgba(239,68,68,0.1); }
    .empty-state {
      text-align: center; padding: 4rem 2rem; color: var(--fg-muted);
    }
    .empty-state i { font-size: 4rem; display: block; margin-bottom: 1rem; opacity: 0.3; }
    .alert-bar {
      padding: 0.75rem 1.25rem; border-radius: 10px;
      font-size: 0.85rem; font-weight: 600;
      display: flex; align-items: center; gap: 0.6rem;
      margin-bottom: 1rem; animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
    .alert-success { background: rgba(16,185,129,0.12); color: #10b981; border: 1px solid rgba(16,185,129,0.25); }
    .alert-danger { background: rgba(239,68,68,0.12); color: #ef4444; border: 1px solid rgba(239,68,68,0.25); }
    .role-badge.supervisor {
      background: rgba(230,168,0,0.12); color: #c98f00;
      padding: 0.35rem 0.9rem; border-radius: 20px;
      font-size: 0.75rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.5px;
      display: inline-flex; align-items: center; gap: 0.4rem;
    }
    .modal-overlay {
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.55);
      backdrop-filter: blur(4px);
      z-index: 1000; display: none;
      align-items: center; justify-content: center;
      padding: 1rem;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
      background: var(--fg-card-bg);
      border: 1px solid var(--fg-border);
      border-radius: 18px;
      box-shadow: 0 24px 64px rgba(0,0,0,0.4);
      width: 100%; max-width: 600px;
      max-height: 90vh; overflow-y: auto;
      animation: modalIn 0.25s cubic-bezier(0.16,1,0.3,1);
    }
    @keyframes modalIn {
      from { opacity: 0; transform: scale(0.95) translateY(10px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-head {
      padding: 1.5rem 1.75rem 1.25rem;
      border-bottom: 1px solid var(--fg-border);
      display: flex; align-items: center; justify-content: space-between;
    }
    .modal-head h5 { margin: 0; font-weight: 800; font-size: 1.1rem; color: var(--fg-text); }
    .modal-body { padding: 1.5rem 1.75rem; }
    .modal-foot {
      padding: 1.25rem 1.75rem;
      border-top: 1px solid var(--fg-border);
      display: flex; gap: 0.75rem; justify-content: flex-end;
    }
    .btn-close-modal {
      width: 32px; height: 32px; border-radius: 8px;
      border: 1.5px solid var(--fg-border);
      background: transparent; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      color: var(--fg-muted); font-size: 1rem; transition: all 0.2s;
    }
    .btn-close-modal:hover { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,0.08); }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label {
      display: block; font-size: 0.82rem; font-weight: 700;
      color: var(--fg-text); margin-bottom: 0.4rem;
    }
    .form-group label span { color: #ef4444; margin-left: 2px; }
    .form-input, .form-select, .form-textarea {
      width: 100%; padding: 0.65rem 0.9rem;
      border: 1.5px solid var(--fg-border);
      border-radius: 10px; background: var(--fg-bg);
      color: var(--fg-text); font-size: 0.88rem;
      outline: none; transition: border-color 0.2s, box-shadow 0.2s;
      font-family: inherit;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
      border-color: var(--fg-primary);
      box-shadow: 0 0 0 3px rgba(230,168,0,0.15);
    }
    .form-textarea { resize: vertical; min-height: 80px; }
    .form-select { cursor: pointer; }
    .btn-cancel {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.65rem 1.25rem; border-radius: 10px;
      background: transparent; color: var(--fg-muted);
      border: 1.5px solid var(--fg-border); font-weight: 600;
      font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
    }
    .btn-cancel:hover { border-color: var(--fg-text); color: var(--fg-text); }
    @media (max-width: 768px) {
      .products-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <nav class="fg-navbar" role="navigation">
    <div class="d-flex align-items-center gap-3">
      <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
        <img src="/assets/images/logo.png" alt="Fix&Go"
             style="height:40px;width:auto;object-fit:contain;"
             onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="role-badge supervisor"><i class="bi bi-person-check"></i> Supervisor</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="/dashboard.php" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-primary);background:rgba(230,168,0,0.08);font-size:0.85rem;text-decoration:none;font-weight:600;">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
      </a>
      <button id="logoutBtn" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
    </div>
  </nav>

  <div class="supervisor-layout">
    <main class="supervisor-main" style="max-width:1400px;margin:0 auto;">
      <div class="page-header">
        <div>
          <h2>Inventory Management</h2>
          <p>Manage all products in the inventory</p>
        </div>
        <button class="btn-primary-custom" id="btnAddProduct">
          <i class="bi bi-plus-circle"></i>
          Add Product
        </button>
      </div>

      <div id="alertBox" style="display:none;"></div>

      <!-- Pending Transfers Section -->
      <div id="pendingTransfersSection" style="display:none;background:var(--fg-card-bg);border:2px solid #3b82f6;border-radius:14px;padding:1.5rem;margin-bottom:1.5rem;">
        <h3 style="font-size:1.1rem;font-weight:800;color:var(--fg-text);margin:0 0 1rem 0;display:flex;align-items:center;gap:0.5rem;">
          <i class="bi bi-inbox" style="color:#3b82f6;"></i>
          Pending Transfers
          <span id="pendingCount" style="background:#3b82f6;color:white;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.75rem;"></span>
        </h3>
        <div id="pendingTransfersList"></div>
      </div>

      <div class="search-filter-bar">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" id="searchInput" placeholder="Search products by name...">
        </div>
        <select class="filter-select" id="categoryFilter">
          <option value="">All Categories</option>
          <option value="Screens">Screens</option>
          <option value="Batteries">Batteries</option>
          <option value="Accessories">Accessories</option>
          <option value="Chargers">Chargers</option>
          <option value="Cases">Cases</option>
          <option value="Other">Other</option>
        </select>
        <select class="filter-select" id="stockFilter">
          <option value="">All Stock Levels</option>
          <option value="in_stock">In Stock</option>
          <option value="low_stock">Low Stock</option>
          <option value="out_of_stock">Out of Stock</option>
        </select>
      </div>

      <!-- Selection Toolbar -->
      <div id="selectionToolbar" style="display:none;background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
          <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.9rem;font-weight:600;color:var(--fg-text);">
            <input type="checkbox" id="selectAllCheckbox" style="width:18px;height:18px;cursor:pointer;">
            <span>Select All</span>
          </label>
          <span id="selectedCount" style="font-size:0.85rem;color:var(--fg-muted);padding:0.25rem 0.75rem;background:rgba(230,168,0,0.1);border-radius:20px;font-weight:600;">0 selected</span>
        </div>
        <div style="display:flex;gap:0.75rem;">
          <button id="btnSendToSalesPerson" class="btn-primary-custom" style="background:#3b82f6;" disabled>
            <i class="bi bi-send"></i> Send to Sales Person
          </button>
        </div>
      </div>

      <div class="products-grid" id="productsGrid">
        <div class="empty-state">
          <i class="bi bi-box-seam"></i>
          <p>Loading products...</p>
        </div>
      </div>
    </main>
  </div>

  <!-- Add/Edit Product Modal -->
  <div class="modal-overlay" id="productModal">
    <div class="modal-box">
      <div class="modal-head">
        <h5 id="modalTitle"><i class="bi bi-plus-circle"></i> Add Product</h5>
        <button class="btn-close-modal" id="btnCloseModal">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <form id="productForm" enctype="multipart/form-data">
        <input type="hidden" id="productId" name="product_id">
        <div class="modal-body">
          <div class="form-group">
            <label>Product Name <span>*</span></label>
            <input type="text" class="form-input" name="name" id="productName" required placeholder="Enter product name" maxlength="150">
          </div>
          <div class="form-group">
            <label>Category <span>*</span></label>
            <select class="form-select" name="category" id="productCategory" required>
              <option value="">Select Category</option>
              <option value="Screens">Screens</option>
              <option value="Batteries">Batteries</option>
              <option value="Accessories">Accessories</option>
              <option value="Chargers">Chargers</option>
              <option value="Cases">Cases</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea class="form-textarea" name="description" id="productDescription" placeholder="Enter product description"></textarea>
          </div>
          <div class="form-group">
            <label>Price (₱) <span>*</span></label>
            <input type="number" class="form-input" name="price" id="productPrice" required placeholder="0.00" min="0" step="0.01">
          </div>
          <div class="form-group">
            <label>Product Image</label>
            <input type="file" class="form-input" name="image" id="productImage" accept="image/*">
            <small style="color:var(--fg-muted);font-size:0.75rem;display:block;margin-top:0.3rem;">
              Max 5MB. Formats: JPG, PNG, WEBP
            </small>
          </div>
        </div>
        <div class="modal-foot">
          <button type="button" class="btn-cancel" id="btnCancelModal">
            <i class="bi bi-x-circle"></i> Cancel
          </button>
          <button type="submit" class="btn-primary-custom">
            <i class="bi bi-check-circle"></i> <span id="submitBtnText">Add Product</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script>
    // Logout handler with modal
    document.addEventListener('DOMContentLoaded', function() {
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
          FGAuth.showLogoutModal(function() {
            fetch('/api/logout', { method: 'POST' })
              .finally(function() {
                FGAuth.UserStore.clear();
                window.location.href = '/index.php?logout=true';
              });
          });
        });
      }
    });
  </script>
  <script src="inventory.js"></script>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>

