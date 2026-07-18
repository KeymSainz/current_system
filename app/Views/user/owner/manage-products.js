/**
 * Fix&Go — Owner Product Management
 * Manages the owner's own product inventory
 */

(function() {
  'use strict';

  let allProducts = [];

  // Load products on page load
  document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadProducts();
  });

  // Load statistics
  function loadStats() {
    fetch('../../../backend/owner_shop_products.php?action=stats', {
      method: 'GET',
      credentials: 'include',
    })
    .then(r => r.json())
    .then(data => {
      if (data.success && data.stats) {
        document.getElementById('statTotal').textContent = data.stats.total_products || 0;
        document.getElementById('statVerified').textContent = data.stats.verified_count || 0;
        document.getElementById('statDraft').textContent = data.stats.draft_count || 0;
        document.getElementById('statValue').textContent = '₱' + parseFloat(data.stats.total_value || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});
      }
    })
    .catch(err => console.error('Failed to load stats:', err));
  }

  // Load products
  function loadProducts() {
    fetch('../../../backend/owner_shop_products.php?action=list', {
      method: 'GET',
      credentials: 'include',
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        allProducts = data.products || [];
        renderProducts(allProducts);
      } else {
        showAlert(data.message || 'Failed to load products.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to load products:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  }

  // Render products table
  function renderProducts(products) {
    const tbody = document.getElementById('productTableBody');
    
    if (!products || products.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="9">
            <div class="empty-state">
              <i class="bi bi-inbox"></i>
              <p>No products yet. Add products or purchase from suppliers.</p>
            </div>
          </td>
        </tr>`;
      return;
    }

    tbody.innerHTML = products.map(p => {
      const imgSrc = p.image_path 
        ? `../../../${p.image_path}` 
        : '../../../assets/images/product-placeholder.svg';
      
      const statusBadge = getStatusBadge(p.status);
      const isPurchased = p.notes && p.notes.includes('Purchased from supplier');

      return `
        <tr>
          <td><input type="checkbox" class="product-checkbox" data-id="${p.id}"></td>
          <td><img src="${imgSrc}" alt="Product" style="width:50px;height:50px;object-fit:cover;border-radius:8px;" onerror="this.src='../../../assets/images/product-placeholder.svg'"></td>
          <td>${escapeHtml(p.category)}</td>
          <td>${escapeHtml(p.brand)}</td>
          <td style="max-width:250px;">
            <strong>${escapeHtml(p.item_description)}</strong>
            ${isPurchased ? '<br><span style="font-size:0.75rem;color:var(--fg-primary);"><i class="bi bi-cart-check"></i> Purchased</span>' : ''}
          </td>
          <td style="text-align:center;font-weight:700;">${p.qty}</td>
          <td style="text-align:right;font-weight:600;">₱${parseFloat(p.srp).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
          <td>${statusBadge}</td>
          <td style="text-align:center;">
            <button class="btn-act edit" onclick="editProduct(${p.id})" title="Edit">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn-act del" onclick="deleteProduct(${p.id})" title="Delete">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
      `;
    }).join('');
  }

  // Get status badge HTML
  function getStatusBadge(status) {
    const badges = {
      'draft': '<span class="badge-status badge-draft"><i class="bi bi-file-earmark"></i> Draft</span>',
      'verified': '<span class="badge-status badge-verified"><i class="bi bi-check-circle"></i> Verified</span>',
      'sent_to_supervisor': '<span class="badge-status badge-sent"><i class="bi bi-send"></i> Sent to Supervisor</span>',
      'owner_received': '<span class="badge-status badge-owner_received">Received</span>',
      'rejected': '<span class="badge-status badge-rejected">Rejected</span>'
    };
    return badges[status] || status;
  }

  // Search functionality
  document.getElementById('searchInput').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase().trim();
    if (!query) {
      renderProducts(allProducts);
      return;
    }

    const filtered = allProducts.filter(p => {
      return p.category.toLowerCase().includes(query) ||
             p.brand.toLowerCase().includes(query) ||
             p.item_description.toLowerCase().includes(query);
    });

    renderProducts(filtered);
  });

  // Helper functions
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

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
  }

  // Checkbox selection handling
  document.getElementById('checkAll').addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
    updateToolbarButtons();
  });

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-checkbox')) {
      updateToolbarButtons();
    }
  });

  function updateToolbarButtons() {
    const selected = getSelectedProductIds();
    const btnSend = document.getElementById('btnSendToSupervisor');
    const btnDelete = document.getElementById('btnDeleteSelected');
    
    console.log('=== UPDATE TOOLBAR BUTTONS ===');
    console.log('Selected products:', selected.length);
    console.log('Button exists:', !!btnSend);
    
    if (selected.length > 0) {
      btnSend.style.display = 'inline-flex';
      btnDelete.style.display = 'inline-flex';
      console.log('Buttons shown');
    } else {
      btnSend.style.display = 'none';
      btnDelete.style.display = 'none';
      console.log('Buttons hidden');
    }
  }

  function getSelectedProductIds() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    return Array.from(checkboxes).map(cb => parseInt(cb.dataset.id));
  }

  // Send to Supervisor button
  document.getElementById('btnSendToSupervisor').addEventListener('click', function() {
    console.log('=== SEND TO SUPERVISOR CLICKED ===');
    const selectedIds = getSelectedProductIds();
    console.log('Selected product IDs:', selectedIds);
    
    if (selectedIds.length === 0) {
      showAlert('Please select products to send.', 'danger');
      return;
    }

    // Open modal to select supervisor
    console.log('Opening supervisor selection modal...');
    openSendToSupervisorModal(selectedIds);
  });

  // Delete Selected button
  document.getElementById('btnDeleteSelected').addEventListener('click', function() {
    const selectedIds = getSelectedProductIds();
    
    if (selectedIds.length === 0) {
      showAlert('Please select products to delete.', 'danger');
      return;
    }

    if (!confirm(`Are you sure you want to delete ${selectedIds.length} product(s)? This action cannot be undone.`)) {
      return;
    }

    fetch('../../../backend/owner_shop_products.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'delete', ids: selectedIds })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert(`${data.deleted} product(s) deleted successfully.`, 'success');
        document.getElementById('checkAll').checked = false;
        loadStats();
        loadProducts();
      } else {
        showAlert(data.message || 'Failed to delete products.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to delete products:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  });

  // Make functions global
  window.editProduct = function(id) {
    const product = allProducts.find(p => p.id === id);
    if (product) {
      // TODO: Open edit modal
      alert('Edit product: ' + product.item_description);
    }
  };

  window.deleteProduct = function(id) {
    if (!confirm('Are you sure you want to delete this product?')) {
      return;
    }

    fetch('../../../backend/owner_shop_products.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'delete', ids: [id] })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert('Product deleted successfully.', 'success');
        loadStats();
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

  // ============================================================
  // SEND TO SUPERVISOR MODAL
  // ============================================================
  
  let selectedProductsForTransfer = [];
  
  function openSendToSupervisorModal(productIds) {
    selectedProductsForTransfer = productIds;
    
    // Fetch list of supervisors
    fetch('../../../backend/product_transfers.php?action=get_staff_list&role=supervisor', {
      credentials: 'include'
    })
    .then(r => r.json())
    .then(data => {
      console.log('Supervisor API response:', data);
      
      if (!data.success) {
        showAlert(data.message || 'Failed to load supervisors.', 'danger');
        return;
      }
      
      if (data.count === 0) {
        showAlert('No supervisors assigned to you. Please assign supervisors in the Staff Management page first.', 'warning');
        return;
      }
      
      // Build supervisor list HTML
      const supervisorOptions = data.staff.map(supervisor => `
        <option value="${supervisor.id}">
          ${escapeHtml(supervisor.first_name)} ${escapeHtml(supervisor.last_name)} (${escapeHtml(supervisor.email)})
        </option>
      `).join('');
      
      // Create modal HTML
      const modalHTML = `
        <div class="modal-overlay open" id="sendToSupervisorModal" style="z-index:2000;">
          <div class="modal-box" style="max-width:480px;">
            <div class="modal-head">
              <h5><i class="bi bi-send" style="color:var(--fg-primary);margin-right:0.5rem;"></i>Send to Supervisor</h5>
              <button class="btn-close-modal" onclick="closeSendToSupervisorModal()">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <div class="modal-body">
              <p style="font-size:0.9rem;color:var(--fg-muted);margin-bottom:1.25rem;">
                Select a supervisor to send <strong>${productIds.length} product(s)</strong> to:
              </p>
              
              <div class="form-group">
                <label>Supervisor <span style="color:#dc3545;">*</span></label>
                <select class="form-input" id="supervisorSelect" required>
                  <option value="">-- Select Supervisor --</option>
                  ${supervisorOptions}
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
              <button type="button" class="btn-cancel" onclick="closeSendToSupervisorModal()">
                <i class="bi bi-x"></i> Cancel
              </button>
              <button type="button" class="btn-primary-custom" onclick="confirmSendToSupervisor()">
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
      console.error('Failed to load supervisors:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  }
  
  window.closeSendToSupervisorModal = function() {
    const modal = document.getElementById('sendToSupervisorModal');
    if (modal) {
      modal.remove();
    }
    selectedProductsForTransfer = [];
  };
  
  window.confirmSendToSupervisor = function() {
    const supervisorId = document.getElementById('supervisorSelect').value;
    const quantity = parseInt(document.getElementById('transferQuantity').value);
    const notes = document.getElementById('transferNotes').value.trim();
    
    if (!supervisorId) {
      showAlert('Please select a supervisor.', 'danger');
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
    
    // Send each product to the supervisor
    const promises = selectedProductsForTransfer.map(productId => {
      const payload = {
        action: 'send_to_staff',
        product_id: productId,
        to_user_id: parseInt(supervisorId),
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
          showAlert(`${successCount} product(s) sent to supervisor successfully!`, 'success');
          document.getElementById('checkAll').checked = false;
          loadStats();
          loadProducts();
        }
        
        if (failCount > 0) {
          const failedReasons = results.filter(r => !r.success).map(r => r.message).join(', ');
          showAlert(`${failCount} product(s) failed to send. Reasons: ${failedReasons}`, 'danger');
        }
        
        closeSendToSupervisorModal();
      })
      .catch(err => {
        console.error('Failed to send products:', err);
        showAlert('Network error. Please try again.', 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Send Products';
      });
  };

})();
