/**
 * Fix&Go — Supervisor Inventory Management
 */

(function() {
  'use strict';

  let allProducts = [];
  let filteredProducts = [];
  let editingProductId = null;
  let selectedProductIds = new Set();

  // Auth guard
  document.addEventListener('DOMContentLoaded', function() {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'supervisor') {
      window.location.href = '../../../login.html';
      return;
    }

    const fullName = (user.firstName || '') + ' ' + (user.lastName || '');
    document.getElementById('navUserName').textContent = fullName.trim() || user.email || 'Supervisor';

    // Load pending transfers first
    loadPendingTransfers();
    
    // Load products
    loadProducts();

    // Modal handlers
    const productModal = document.getElementById('productModal');
    const btnAddProduct = document.getElementById('btnAddProduct');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const btnCancelModal = document.getElementById('btnCancelModal');
    const productForm = document.getElementById('productForm');

    btnAddProduct.addEventListener('click', () => openAddModal());
    btnCloseModal.addEventListener('click', () => closeModal());
    btnCancelModal.addEventListener('click', () => closeModal());
    
    productModal.addEventListener('click', (e) => {
      if (e.target === productModal) closeModal();
    });

    productForm.addEventListener('submit', handleSubmit);

    // Search and filter
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.getElementById('categoryFilter').addEventListener('change', applyFilters);
    document.getElementById('stockFilter').addEventListener('change', applyFilters);

    // Selection handlers
    document.getElementById('selectAllCheckbox').addEventListener('change', handleSelectAll);
    document.getElementById('btnSendToSalesPerson').addEventListener('click', handleSendToSalesPerson);
  });

  // ============================================================
  // PENDING TRANSFERS
  // ============================================================
  
  function loadPendingTransfers() {
    fetch('../../../backend/product_transfers.php?action=get_pending_transfers', {
      method: 'GET',
      credentials: 'include',
    })
    .then(r => r.json())
    .then(data => {
      console.log('Pending transfers response:', data);
      
      if (data.success) {
        renderPendingTransfers(data.transfers || []);
      } else {
        console.error('Failed to load pending transfers:', data.message);
      }
    })
    .catch(err => {
      console.error('Failed to load pending transfers:', err);
    });
  }
  
  function renderPendingTransfers(transfers) {
    const section = document.getElementById('pendingTransfersSection');
    const list = document.getElementById('pendingTransfersList');
    const countBadge = document.getElementById('pendingCount');
    
    if (!transfers || transfers.length === 0) {
      section.style.display = 'none';
      return;
    }
    
    section.style.display = 'block';
    countBadge.textContent = transfers.length;
    
    list.innerHTML = transfers.map(t => `
      <div style="background:var(--fg-bg);border:1px solid var(--fg-border);border-radius:10px;padding:1rem;margin-bottom:0.75rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="flex:1;min-width:200px;">
          <div style="font-weight:700;font-size:0.95rem;color:var(--fg-text);margin-bottom:0.3rem;">
            ${escapeHtml(t.category)} - ${escapeHtml(t.brand)}
          </div>
          <div style="font-size:0.85rem;color:var(--fg-muted);margin-bottom:0.5rem;">
            ${escapeHtml(t.item_description)}
          </div>
          <div style="display:flex;gap:1rem;flex-wrap:wrap;font-size:0.8rem;">
            <span style="color:var(--fg-muted);">
              <i class="bi bi-box"></i> Quantity: <strong>${t.quantity}</strong>
            </span>
            <span style="color:var(--fg-muted);">
              <i class="bi bi-currency-dollar"></i> Price: <strong>₱${parseFloat(t.srp).toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong>
            </span>
            <span style="color:var(--fg-muted);">
              <i class="bi bi-person"></i> From: <strong>${escapeHtml(t.from_first_name)} ${escapeHtml(t.from_last_name)}</strong>
            </span>
          </div>
          ${t.notes ? `<div style="margin-top:0.5rem;font-size:0.8rem;color:var(--fg-muted);font-style:italic;">
            <i class="bi bi-chat-left-text"></i> ${escapeHtml(t.notes)}
          </div>` : ''}
        </div>
        <div style="display:flex;gap:0.5rem;">
          <button onclick="acceptTransfer(${t.id})" style="padding:0.5rem 1rem;border-radius:8px;border:none;background:#10b981;color:white;font-weight:600;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:0.3rem;transition:all 0.2s;">
            <i class="bi bi-check-circle"></i> Accept
          </button>
          <button onclick="rejectTransfer(${t.id})" style="padding:0.5rem 1rem;border-radius:8px;border:1.5px solid #ef4444;background:transparent;color:#ef4444;font-weight:600;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:0.3rem;transition:all 0.2s;">
            <i class="bi bi-x-circle"></i> Reject
          </button>
        </div>
      </div>
    `).join('');
  }
  
  window.acceptTransfer = function(transferId) {
    if (!confirm('Accept this product transfer?')) {
      return;
    }
    
    fetch('../../../backend/product_transfers.php', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'accept_transfer',
        transfer_id: transferId
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert('Transfer accepted successfully!', 'success');
        loadPendingTransfers();
        loadProducts(); // Reload products to show the new one
      } else {
        showAlert(data.message || 'Failed to accept transfer.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to accept transfer:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  };
  
  window.rejectTransfer = function(transferId) {
    const reason = prompt('Reason for rejection (optional):');
    if (reason === null) {
      return; // User cancelled
    }
    
    fetch('../../../backend/product_transfers.php', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'reject_transfer',
        transfer_id: transferId,
        reason: reason
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert('Transfer rejected.', 'success');
        loadPendingTransfers();
      } else {
        showAlert(data.message || 'Failed to reject transfer.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to reject transfer:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  };
  
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // ============================================================
  // PRODUCTS
  // ============================================================

  function loadProducts() {
    console.log('=== LOADING SUPERVISOR PRODUCTS ===');
    fetch('../../../backend/supervisor_inventory.php?action=list', {
      method: 'GET',
      credentials: 'include',
    })
    .then(r => r.json())
    .then(data => {
      console.log('Supervisor products response:', data);
      
      if (data.success) {
        allProducts = data.products || [];
        filteredProducts = [...allProducts];
        console.log('Total products loaded:', allProducts.length);
        renderProducts(filteredProducts);
      } else {
        console.error('Failed to load products:', data.message);
        showAlert(data.message || 'Failed to load products.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to load products:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  }

  function renderProducts(products) {
    const grid = document.getElementById('productsGrid');
    
    if (!products || products.length === 0) {
      grid.innerHTML = `
        <div class="empty-state" style="grid-column: 1 / -1;">
          <i class="bi bi-box-seam"></i>
          <p>No products found</p>
        </div>
      `;
      document.getElementById('selectionToolbar').style.display = 'none';
      return;
    }

    // Show selection toolbar
    document.getElementById('selectionToolbar').style.display = 'flex';

    grid.innerHTML = products.map(p => {
      const stockStatus = getStockStatus(p.stock_quantity);
      const imagePath = p.image_path 
        ? `../../../${p.image_path}`
        : '../../../assets/images/product-placeholder.svg';
      
      const isSelected = selectedProductIds.has(p.id);

      return `
        <div class="product-card ${isSelected ? 'selected' : ''}" data-product-id="${p.id}">
          <input type="checkbox" class="product-checkbox" data-product-id="${p.id}" ${isSelected ? 'checked' : ''} onchange="handleProductCheckbox(${p.id}, this.checked)">
          <img src="${escapeHtml(imagePath)}" alt="${escapeHtml(p.name)}" class="product-image"
               onerror="this.src='../../../assets/images/product-placeholder.svg'">
          <div class="product-body">
            <div class="product-category">${escapeHtml(p.category || 'Uncategorized')}</div>
            <div class="product-name">${escapeHtml(p.name)}</div>
            <div class="product-price">₱${parseFloat(p.price).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
            <div class="product-stock">
              <span class="stock-badge ${stockStatus.class}">${stockStatus.label}</span>
              <span style="color:var(--fg-muted);font-size:0.85rem;">${p.stock_quantity} units</span>
            </div>
            <div class="product-actions">
              <button class="btn-action edit" onclick="editProduct(${p.id})">
                <i class="bi bi-pencil"></i> Edit
              </button>
              <button class="btn-action delete" onclick="deleteProduct(${p.id}, '${escapeHtml(p.name)}')">
                <i class="bi bi-trash"></i> Delete
              </button>
            </div>
          </div>
        </div>
      `;
    }).join('');

    updateSelectionUI();
  }

  function getStockStatus(quantity) {
    if (quantity === 0) {
      return { class: 'stock-out', label: 'Out of Stock' };
    } else if (quantity < 10) {
      return { class: 'stock-low', label: 'Low Stock' };
    } else {
      return { class: 'stock-in', label: 'In Stock' };
    }
  }

  function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const stockFilter = document.getElementById('stockFilter').value;

    filteredProducts = allProducts.filter(p => {
      const matchesSearch = p.name.toLowerCase().includes(searchTerm) ||
                           (p.description && p.description.toLowerCase().includes(searchTerm));
      const matchesCategory = !categoryFilter || p.category === categoryFilter;
      
      let matchesStock = true;
      if (stockFilter === 'in_stock') {
        matchesStock = p.stock_quantity >= 10;
      } else if (stockFilter === 'low_stock') {
        matchesStock = p.stock_quantity > 0 && p.stock_quantity < 10;
      } else if (stockFilter === 'out_of_stock') {
        matchesStock = p.stock_quantity === 0;
      }

      return matchesSearch && matchesCategory && matchesStock;
    });

    renderProducts(filteredProducts);
  }

  function openAddModal() {
    editingProductId = null;
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Add Product';
    document.getElementById('submitBtnText').textContent = 'Add Product';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('productModal').classList.add('open');
  }

  window.editProduct = function(id) {
    const product = allProducts.find(p => p.id === id);
    if (!product) return;

    editingProductId = id;
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil"></i> Edit Product';
    document.getElementById('submitBtnText').textContent = 'Update Product';
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productCategory').value = product.category || '';
    document.getElementById('productDescription').value = product.description || '';
    document.getElementById('productPrice').value = product.price;
    document.getElementById('productStock').value = product.stock_quantity;
    document.getElementById('productModal').classList.add('open');
  };

  function closeModal() {
    document.getElementById('productModal').classList.remove('open');
    document.getElementById('productForm').reset();
    editingProductId = null;
  }

  function handleSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const action = editingProductId ? 'update' : 'add';
    formData.append('action', action);

    if (editingProductId) {
      formData.set('product_id', editingProductId);
    }

    // Disable submit button
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

    fetch('../../../backend/supervisor_inventory.php', {
      method: 'POST',
      credentials: 'include',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert(data.message || 'Product saved successfully!', 'success');
        closeModal();
        loadProducts();
      } else {
        showAlert(data.message || 'Failed to save product.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to save product:', err);
      showAlert('Network error. Please try again.', 'danger');
    })
    .finally(() => {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
  }

  window.deleteProduct = function(id, name) {
    if (!confirm(`Are you sure you want to delete "${name}"?\n\nThis action cannot be undone.`)) {
      return;
    }

    fetch('../../../backend/supervisor_inventory.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'delete', product_id: id })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert('Product deleted successfully.', 'success');
        loadProducts();
      } else {
        showAlert(data.message || 'Failed to delete product.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to delete product:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  };

  function showAlert(message, type = 'success') {
    const alertBox = document.getElementById('alertBox');
    const icon = type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill';
    alertBox.innerHTML = `
      <div class="alert-${type}">
        <i class="bi bi-${icon}"></i>
        <span>${message}</span>
      </div>
    `;
    alertBox.style.display = 'block';
    setTimeout(() => { alertBox.style.display = 'none'; }, 5000);
    
    // Scroll to top to show alert
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // ============================================================
  // SELECTION HANDLERS
  // ============================================================

  window.handleProductCheckbox = function(productId, isChecked) {
    if (isChecked) {
      selectedProductIds.add(productId);
    } else {
      selectedProductIds.delete(productId);
    }
    updateSelectionUI();
  };

  function handleSelectAll(e) {
    const isChecked = e.target.checked;
    
    if (isChecked) {
      // Select all filtered products
      filteredProducts.forEach(p => selectedProductIds.add(p.id));
    } else {
      // Deselect all
      selectedProductIds.clear();
    }
    
    renderProducts(filteredProducts);
  }

  function updateSelectionUI() {
    const count = selectedProductIds.size;
    const countEl = document.getElementById('selectedCount');
    const sendBtn = document.getElementById('btnSendToSalesPerson');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');

    countEl.textContent = `${count} selected`;
    sendBtn.disabled = count === 0;

    // Update select all checkbox state
    if (count === 0) {
      selectAllCheckbox.checked = false;
      selectAllCheckbox.indeterminate = false;
    } else if (count === filteredProducts.length) {
      selectAllCheckbox.checked = true;
      selectAllCheckbox.indeterminate = false;
    } else {
      selectAllCheckbox.checked = false;
      selectAllCheckbox.indeterminate = true;
    }

    // Update card visual state
    document.querySelectorAll('.product-card').forEach(card => {
      const productId = parseInt(card.dataset.productId);
      if (selectedProductIds.has(productId)) {
        card.classList.add('selected');
      } else {
        card.classList.remove('selected');
      }
    });
  }

  function handleSendToSalesPerson() {
    if (selectedProductIds.size === 0) {
      showAlert('Please select at least one product.', 'danger');
      return;
    }

    const count = selectedProductIds.size;
    
    // Open modal to select sales person
    openSendToSalesPersonModal(Array.from(selectedProductIds));
  }
  
  // ============================================================
  // SEND TO SALES PERSON MODAL
  // ============================================================
  
  let selectedProductsForTransfer = [];
  
  function openSendToSalesPersonModal(productIds) {
    selectedProductsForTransfer = productIds;
    
    // Fetch list of sales persons
    fetch('../../../backend/product_transfers.php?action=get_staff_list&role=sales_person', {
      credentials: 'include'
    })
    .then(r => r.json())
    .then(data => {
      console.log('Sales Person API response:', data);
      
      if (!data.success) {
        showAlert(data.message || 'Failed to load sales persons.', 'danger');
        return;
      }
      
      if (data.count === 0) {
        showAlert('No sales persons available. Please register sales persons in the Staff Management page first.', 'warning');
        return;
      }
      
      // Build sales person list HTML
      const salesPersonOptions = data.staff.map(salesPerson => `
        <option value="${salesPerson.id}">
          ${escapeHtml(salesPerson.first_name)} ${escapeHtml(salesPerson.last_name)} (${escapeHtml(salesPerson.email)})
        </option>
      `).join('');
      
      // Create modal HTML
      const modalHTML = `
        <div class="modal-overlay open" id="sendToSalesPersonModal" style="z-index:2000;">
          <div class="modal-box" style="max-width:480px;">
            <div class="modal-head">
              <h5><i class="bi bi-send" style="color:#3b82f6;margin-right:0.5rem;"></i>Send to Sales Person</h5>
              <button class="btn-close-modal" onclick="closeSendToSalesPersonModal()">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <div class="modal-body">
              <p style="font-size:0.9rem;color:var(--fg-muted);margin-bottom:1.25rem;">
                Select a sales person to send <strong>${productIds.length} product(s)</strong> to:
              </p>
              
              <div class="form-group">
                <label>Sales Person <span style="color:#dc3545;">*</span></label>
                <select class="form-input" id="salesPersonSelect" required>
                  <option value="">-- Select Sales Person --</option>
                  ${salesPersonOptions}
                </select>
              </div>
              
              <div class="form-group">
                <label>Quantity per Product <span style="color:#dc3545;">*</span></label>
                <input type="number" class="form-input" id="transferQuantity" min="1" value="1" required>
                <small style="font-size:0.75rem;color:var(--fg-muted);display:block;margin-top:0.3rem;">
                  Number of units to send for each selected product
                </small>
              </div>
              
              <div class="form-group" style="margin-bottom:0;">
                <label>Notes <span style="color:var(--fg-muted);font-weight:500;">(Optional)</span></label>
                <textarea class="form-input" id="transferNotes" rows="3" 
                          placeholder="Add any notes about this transfer..."></textarea>
              </div>
            </div>
            <div class="modal-foot">
              <button type="button" class="btn-cancel" onclick="closeSendToSalesPersonModal()">
                <i class="bi bi-x"></i> Cancel
              </button>
              <button type="button" class="btn-primary-custom" style="background:#3b82f6;" onclick="confirmSendToSalesPerson()">
                <i class="bi bi-send-fill"></i> Send Products
              </button>
            </div>
          </div>
        </div>
      `;
      
      // Add modal to page
      document.body.insertAdjacentHTML('beforeend', modalHTML);
    })
    .catch(err => {
      console.error('Failed to load sales persons:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  }
  
  window.closeSendToSalesPersonModal = function() {
    const modal = document.getElementById('sendToSalesPersonModal');
    if (modal) {
      modal.remove();
    }
    selectedProductsForTransfer = [];
  };
  
  window.confirmSendToSalesPerson = function() {
    const salesPersonId = document.getElementById('salesPersonSelect').value;
    const quantity = parseInt(document.getElementById('transferQuantity').value);
    const notes = document.getElementById('transferNotes').value.trim();
    
    if (!salesPersonId) {
      showAlert('Please select a sales person.', 'danger');
      return;
    }
    
    if (!quantity || quantity < 1) {
      showAlert('Please enter a valid quantity.', 'danger');
      return;
    }
    
    // Disable button to prevent double-click
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';
    
    // Send each product to the sales person
    const promises = selectedProductsForTransfer.map(productId => {
      const payload = {
        action: 'send_to_staff',
        product_id: productId,
        to_user_id: parseInt(salesPersonId),
        quantity: quantity,
        notes: notes
      };
      
      console.log('Sending payload:', payload);
      
      return fetch('../../../backend/product_transfers.php', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(r => {
        console.log('Response status:', r.status);
        return r.json();
      })
      .then(data => {
        console.log('Response data:', data);
        return data;
      });
    });
    
    Promise.all(promises)
      .then(results => {
        console.log('=== TRANSFER RESULTS ===');
        console.log('All results:', results);
        
        const successCount = results.filter(r => r.success).length;
        const failCount = results.length - successCount;
        
        // Log failed transfers
        results.forEach((r, idx) => {
          if (!r.success) {
            console.error(`Transfer ${idx} failed:`, r);
          }
        });
        
        if (successCount > 0) {
          showAlert(`${successCount} product(s) sent to sales person successfully!`, 'success');
          selectedProductIds.clear();
          loadProducts();
        }
        
        if (failCount > 0) {
          const failedReasons = results.filter(r => !r.success).map(r => r.message).join(', ');
          showAlert(`${failCount} product(s) failed to send. Reasons: ${failedReasons}`, 'danger');
        }
        
        closeSendToSalesPersonModal();
      })
      .catch(err => {
        console.error('Failed to send products:', err);
        showAlert('Network error. Please try again.', 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Send Products';
      });
  };

})();
