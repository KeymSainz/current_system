/**
 * Fix&Go — Supplier Products Manager
 * Handles: Add, Edit, Delete, Verify, Send to Owner
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const API = '../../../backend/supplier_products.php';

  // ── Auth check ──────────────────────────────────────────────
  const user = FGAuth.UserStore.get();
  if (!user || user.role !== 'supplier') {
    window.location.href = '../../../login.php';
    return;
  }
  const navName = document.getElementById('navUserName');
  if (navName) navName.textContent = user.firstName + ' ' + user.lastName;

  // ── State ───────────────────────────────────────────────────
  let products = [];
  let editingId = null;
  let activeFilter = 'all'; // current status filter

  // ── DOM refs ────────────────────────────────────────────────
  const tbody         = document.getElementById('productTableBody');
  const alertBox      = document.getElementById('alertBox');
  const checkAll      = document.getElementById('checkAll');
  const modal         = document.getElementById('modalOverlay');
  const backdrop      = null;
  const modalTitle    = document.getElementById('modalTitle');
  const productForm   = document.getElementById('productForm');

  // ── Load products ───────────────────────────────────────────
  function loadProducts() {
    fetch(API + '?action=list')
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          products = data.products;
          renderTable();
          updateStats();
        } else {
          showAlert(data.message || 'Failed to load products.', 'danger');
        }
      })
      .catch(() => {
        // Demo mode — use localStorage
        products = JSON.parse(localStorage.getItem('fg_supplier_products') || '[]');
        renderTable();
        updateStats();
      });
  }

  // ── Render table ────────────────────────────────────────────
  function renderTable() {
    // Apply status filter
    const filtered = activeFilter === 'all'
      ? products
      : products.filter(p => p.status === activeFilter);

    if (filtered.length === 0) {
      const msg = activeFilter === 'rejected'
        ? 'No rejected products. All your submissions are in good standing! 🎉'
        : 'No products yet. Click "Add Product" to get started.';
      tbody.innerHTML = `
        <tr>
          <td colspan="9">
            <div class="empty-state">
              <i class="bi bi-${activeFilter === 'rejected' ? 'check-circle' : 'inbox'}"></i>
              <p>${msg}</p>
            </div>
          </td>
        </tr>`;
      return;
    }

    tbody.innerHTML = filtered.map(p => `
      <tr data-id="${p.id}">
        <td><input type="checkbox" class="row-check" value="${p.id}" style="cursor:pointer;"></td>
        <td>
          ${p.image_path
            ? `<img src="${p.image_path.startsWith('data:') ? p.image_path : '../../../' + p.image_path}"
                    alt="product" style="width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid var(--fg-border);">`
            : `<div style="width:44px;height:44px;border-radius:8px;background:var(--fg-bg);border:1px solid var(--fg-border);display:flex;align-items:center;justify-content:center;color:var(--fg-muted);font-size:1.2rem;"><i class="bi bi-image"></i></div>`
          }
        </td>
        <td><span style="font-size:0.78rem;background:rgba(230,168,0,0.1);color:var(--fg-primary);padding:0.2rem 0.6rem;border-radius:6px;font-weight:600;">${esc(p.category)}</span></td>
        <td style="font-weight:600;">${esc(p.brand) || '<span style="color:var(--fg-muted);">—</span>'}</td>
        <td style="max-width:240px;font-size:0.83rem;">${esc(p.item_description)}</td>
        <td style="text-align:center;font-weight:700;">${p.qty}</td>
        <td style="text-align:right;font-weight:700;">₱${parseFloat(p.srp).toLocaleString('en-PH', {minimumFractionDigits:2})}</td>
        <td><span class="badge-status badge-${p.status}">${statusLabel(p.status)}</span></td>
        <td style="text-align:center;white-space:nowrap;">
          <button class="btn-act edit" title="Edit" onclick="editProduct(${p.id})"><i class="bi bi-pencil"></i></button>
          ${p.status === 'draft' ? `<button class="btn-act verify" title="Verify" onclick="verifyProduct(${p.id})"><i class="bi bi-check-lg"></i></button>` : ''}
          ${p.status === 'verified' ? `<button class="btn-act send" title="Send to Owner" onclick="sendProduct(${p.id})"><i class="bi bi-send"></i></button>` : ''}
          ${p.status === 'rejected' ? `<button class="btn-act verify" title="Re-verify to resubmit" onclick="resubmitProduct(${p.id})" style="border-color:rgba(230,168,0,0.4);" title="Re-submit"><i class="bi bi-arrow-repeat"></i></button>` : ''}
          <button class="btn-act del" title="Delete" onclick="deleteProduct(${p.id})"><i class="bi bi-trash"></i></button>
        </td>
      </tr>`).join('');
  }

  // ── Stats ───────────────────────────────────────────────────
  function updateStats() {
    const rejectedCount = products.filter(p => p.status === 'rejected').length;
    document.getElementById('statTotal').textContent    = products.length;
    document.getElementById('statVerified').textContent = products.filter(p => p.status === 'verified').length;
    document.getElementById('statSent').textContent     = products.filter(p => p.status === 'sent_to_owner' || p.status === 'owner_received').length;
    document.getElementById('statRejected').textContent = rejectedCount;

    // Show/hide rejected count badge on filter button
    const badge = document.getElementById('rejectedCount');
    if (badge) {
      badge.textContent = rejectedCount;
      badge.style.display = rejectedCount > 0 ? 'inline-flex' : 'none';
    }

    // Pulse the rejected stat card if there are rejections
    const rejectedCard = document.getElementById('statRejectedCard');
    if (rejectedCard) {
      rejectedCard.style.borderColor = rejectedCount > 0 ? 'rgba(220,53,69,0.4)' : '';
    }
  }

  // ── Modal open/close ────────────────────────────────────────
  function openModal(title, product = null) {
    const icon = product ? 'bi-pencil-square' : 'bi-plus-circle-fill';
    modalTitle.innerHTML = `<i class="bi ${icon}" style="color:var(--fg-primary);margin-right:0.5rem;"></i>${title}`;
    editingId = product ? product.id : null;
    document.getElementById('productId').value          = product ? product.id : '';
    document.getElementById('category').value           = product ? product.category : '';
    document.getElementById('brand').value              = product ? product.brand : '';
    document.getElementById('itemDescription').value    = product ? product.item_description : '';
    document.getElementById('qty').value                = product ? product.qty : 0;
    document.getElementById('srp').value                = product ? product.srp : '0.00';
    document.getElementById('notes').value              = product ? (product.notes || '') : '';

    // Handle existing image
    const existingPath = document.getElementById('existingImagePath');
    const preview      = document.getElementById('imagePreview');
    const previewWrap  = document.getElementById('imagePreviewWrap');
    const placeholder  = document.getElementById('imagePlaceholder');
    const removeBtn    = document.getElementById('btnRemoveImage');
    const dropZone     = document.getElementById('dropZone');

    if (product && product.image_path) {
      existingPath.value          = product.image_path;
      preview.src                 = '../../../' + product.image_path;
      previewWrap.style.display   = 'block';
      placeholder.style.display   = 'none';
      removeBtn.style.display     = 'inline-flex';
      dropZone.classList.add('dz-hover');
    } else {
      if (typeof removeImage === 'function') removeImage();
    }

    modal.classList.add('open');
    document.getElementById('category').focus();
    updateTotalPrice();
  }

  function updateTotalPrice() {
    const qty = parseFloat(document.getElementById('qty').value) || 0;
    const srp = parseFloat(document.getElementById('srp').value) || 0;
    const total = qty * srp;
    document.getElementById('totalPrice').value = total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function closeModal() {
    modal.classList.remove('open');
    productForm.reset();
    editingId = null;
  }

  document.getElementById('btnAddProduct').addEventListener('click', () => openModal('Add Product'));
  document.getElementById('btnCloseModal').addEventListener('click', closeModal);
  document.getElementById('btnCancelModal').addEventListener('click', closeModal);
  document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });

  // ── Filter tabs ─────────────────────────────────────────────
  document.querySelectorAll('.btn-filter').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      activeFilter = this.dataset.filter;
      renderTable();
    });
  });

  // ── Rejected stat card click → filter to rejected ──────────
  const rejectedCard = document.getElementById('statRejectedCard');
  if (rejectedCard) {
    rejectedCard.addEventListener('click', function () {
      document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
      const rejBtn = document.querySelector('.btn-filter[data-filter="rejected"]');
      if (rejBtn) rejBtn.classList.add('active');
      activeFilter = 'rejected';
      renderTable();
    });
  }

  // ── Re-submit rejected product ──────────────────────────────
  window.resubmitProduct = function (id) {
    if (!confirm('Re-verify this product so you can send it again?')) return;
    // Reset status back to "verified" so it can be re-sent
    fetch(API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'update_status', ids: [id], status: 'verified' }),
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          showAlert('Product re-verified! You can now send it to an owner again.', 'success');
          loadProducts();
        } else {
          showAlert(data.message || 'Error re-verifying product.', 'danger');
        }
      })
      .catch(() => {
        // Demo mode
        const p = products.find(x => x.id === id);
        if (p) { p.status = 'verified'; }
        localStorage.setItem('fg_supplier_products', JSON.stringify(products));
        showAlert('Product re-verified!', 'success');
        renderTable();
        updateStats();
      });
  };

  // ── Total price auto-calc ───────────────────────────────────
  document.getElementById('qty').addEventListener('input', updateTotalPrice);
  document.getElementById('srp').addEventListener('input', updateTotalPrice);

  // ── Save product ────────────────────────────────────────────
  productForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const fileInput = document.getElementById('productImage');
    const hasFile   = fileInput && fileInput.files && fileInput.files[0];

    // Build FormData so we can send the image file
    const fd = new FormData();
    fd.append('action',           editingId ? 'update' : 'create');
    fd.append('id',               editingId || '');
    fd.append('category',         document.getElementById('category').value.trim());
    fd.append('brand',            document.getElementById('brand').value.trim());
    fd.append('item_description', document.getElementById('itemDescription').value.trim());
    fd.append('qty',              document.getElementById('qty').value);
    fd.append('srp',              document.getElementById('srp').value);
    fd.append('notes',            document.getElementById('notes').value.trim());
    fd.append('existing_image',   document.getElementById('existingImagePath').value || '');
    if (hasFile) fd.append('product_image', fileInput.files[0]);

    fetch(API, { method: 'POST', body: fd })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          showAlert(editingId ? 'Product updated!' : 'Product added!', 'success');
          closeModal();
          loadProducts();
        } else {
          showAlert(data.message || 'Error saving product.', 'danger');
        }
      })
      .catch(() => {
        // Demo mode — save to localStorage with base64 image
        const saveProduct = (imagePath) => {
          const payload = {
            action:           editingId ? 'update' : 'create',
            id:               editingId,
            category:         document.getElementById('category').value.trim(),
            brand:            document.getElementById('brand').value.trim(),
            item_description: document.getElementById('itemDescription').value.trim(),
            qty:              parseInt(document.getElementById('qty').value),
            srp:              parseFloat(document.getElementById('srp').value),
            notes:            document.getElementById('notes').value.trim(),
            image_path:       imagePath || document.getElementById('existingImagePath').value || null,
          };
          if (editingId) {
            const idx = products.findIndex(p => p.id === editingId);
            if (idx !== -1) products[idx] = { ...products[idx], ...payload, updated_at: new Date().toISOString() };
          } else {
            payload.id         = Date.now();
            payload.status     = 'draft';
            payload.created_at = new Date().toISOString();
            products.push(payload);
          }
          localStorage.setItem('fg_supplier_products', JSON.stringify(products));
          showAlert(editingId ? 'Product updated!' : 'Product added!', 'success');
          closeModal();
          renderTable();
          updateStats();
        };

        if (hasFile) {
          const reader = new FileReader();
          reader.onload = e => saveProduct(e.target.result); // store base64 in demo mode
          reader.readAsDataURL(fileInput.files[0]);
        } else {
          saveProduct(null);
        }
      });
  });

  // ── Edit ────────────────────────────────────────────────────
  window.editProduct = function (id) {
    const p = products.find(x => x.id === id);
    if (p) openModal('Edit Product', p);
  };

  // ── Verify single ───────────────────────────────────────────
  window.verifyProduct = function (id) {
    updateStatus([id], 'verified', 'Product verified!');
  };

  // ── Owner list (loaded once) ────────────────────────────────
  let ownersList = [];

  function loadOwners() {
    return fetch(API + '?action=owners')
      .then(r => r.json())
      .then(data => { if (data.success) ownersList = data.owners || []; })
      .catch(() => {});
  }

  // ── Owner picker modal ──────────────────────────────────────
  function showOwnerPicker(ids, onConfirm) {
    // Build modal HTML
    const existing = document.getElementById('ownerPickerOverlay');
    if (existing) existing.remove();

    const ownerOptions = ownersList.length
      ? ownersList.map(o => {
          const shopInfo = o.shop_name ? ` — ${o.shop_name}${o.shop_city ? ', ' + o.shop_city : ''}` : '';
          return `<label class="owner-option" style="
            display:flex;align-items:center;gap:0.75rem;
            padding:0.75rem 1rem;border-radius:10px;
            border:1.5px solid var(--fg-border);
            cursor:pointer;transition:all 0.2s;margin-bottom:0.5rem;
          ">
            <input type="radio" name="ownerPick" value="${o.id}" style="accent-color:var(--fg-primary);width:16px;height:16px;flex-shrink:0;">
            <div>
              <div style="font-weight:700;font-size:0.9rem;color:var(--fg-text);">
                <i class="bi bi-shop" style="color:var(--fg-primary);margin-right:0.3rem;"></i>${esc(o.full_name)}
              </div>
              <div style="font-size:0.78rem;color:var(--fg-muted);">${esc(o.email)}${shopInfo ? ' · ' + esc(shopInfo) : ''}</div>
            </div>
          </label>`;
        }).join('')
      : `<p style="color:var(--fg-muted);text-align:center;padding:1rem 0;">No owners registered yet.</p>`;

    const overlay = document.createElement('div');
    overlay.id = 'ownerPickerOverlay';
    overlay.style.cssText = `
      position:fixed;inset:0;background:rgba(0,0,0,0.6);
      backdrop-filter:blur(4px);z-index:2000;
      display:flex;align-items:center;justify-content:center;
    `;
    overlay.innerHTML = `
      <div style="
        background:var(--fg-card-bg);border:1px solid var(--fg-border);
        border-radius:18px;box-shadow:0 24px 64px rgba(0,0,0,0.4);
        width:100%;max-width:480px;max-height:85vh;overflow-y:auto;
        animation:modalIn 0.25s cubic-bezier(0.16,1,0.3,1);
      ">
        <div style="padding:1.5rem 1.75rem 1.25rem;border-bottom:1px solid var(--fg-border);display:flex;align-items:center;justify-content:space-between;">
          <h5 style="margin:0;font-weight:800;font-size:1.05rem;color:var(--fg-text);">
            <i class="bi bi-send-fill" style="color:var(--fg-primary);margin-right:0.5rem;"></i>
            Select Owner to Send To
          </h5>
          <button id="ownerPickerClose" style="background:none;border:1.5px solid var(--fg-border);border-radius:8px;width:32px;height:32px;cursor:pointer;color:var(--fg-muted);font-size:1rem;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div style="padding:1.25rem 1.75rem;">
          <p style="font-size:0.85rem;color:var(--fg-muted);margin-bottom:1rem;">
            Sending <strong style="color:var(--fg-text);">${ids.length} product(s)</strong>. Choose which owner to send them to:
          </p>
          <div id="ownerPickerList">${ownerOptions}</div>
        </div>
        <div style="padding:1rem 1.75rem;border-top:1px solid var(--fg-border);display:flex;gap:0.75rem;justify-content:flex-end;">
          <button id="ownerPickerCancel" style="
            display:inline-flex;align-items:center;gap:0.4rem;
            padding:0.6rem 1.25rem;border-radius:10px;
            background:transparent;color:var(--fg-muted);
            border:1.5px solid var(--fg-border);font-weight:600;font-size:0.88rem;cursor:pointer;
          "><i class="bi bi-x"></i> Cancel</button>
          <button id="ownerPickerConfirm" style="
            display:inline-flex;align-items:center;gap:0.4rem;
            padding:0.6rem 1.5rem;border-radius:10px;
            background:var(--fg-primary);color:#fff;
            border:none;font-weight:700;font-size:0.88rem;cursor:pointer;
          "><i class="bi bi-send-fill"></i> Send Products</button>
        </div>
      </div>`;

    document.body.appendChild(overlay);

    // Hover effect on owner options
    overlay.querySelectorAll('.owner-option').forEach(el => {
      el.addEventListener('mouseenter', () => { el.style.borderColor = 'var(--fg-primary)'; el.style.background = 'rgba(230,168,0,0.05)'; });
      el.addEventListener('mouseleave', () => {
        const radio = el.querySelector('input[type=radio]');
        if (!radio.checked) { el.style.borderColor = 'var(--fg-border)'; el.style.background = ''; }
      });
      el.querySelector('input[type=radio]').addEventListener('change', () => {
        overlay.querySelectorAll('.owner-option').forEach(o => { o.style.borderColor = 'var(--fg-border)'; o.style.background = ''; });
        el.style.borderColor = 'var(--fg-primary)';
        el.style.background  = 'rgba(230,168,0,0.07)';
      });
    });

    const close = () => overlay.remove();
    document.getElementById('ownerPickerClose').addEventListener('click', close);
    document.getElementById('ownerPickerCancel').addEventListener('click', close);
    overlay.addEventListener('click', e => { if (e.target === overlay) close(); });

    document.getElementById('ownerPickerConfirm').addEventListener('click', () => {
      const selected = overlay.querySelector('input[name=ownerPick]:checked');
      if (!selected) { showAlert('Please select an owner.', 'warning'); return; }
      close();
      onConfirm(parseInt(selected.value));
    });
  }

  // ── Send single ─────────────────────────────────────────────
  window.sendProduct = function (id) {
    if (!ownersList.length) {
      loadOwners().then(() => showOwnerPicker([id], ownerId => sendToOwner([id], ownerId)));
    } else {
      showOwnerPicker([id], ownerId => sendToOwner([id], ownerId));
    }
  };

  function sendToOwner(ids, ownerId) {
    fetch(API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'update_status', ids, status: 'sent_to_owner', owner_id: ownerId }),
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) { showAlert(`${ids.length} product(s) sent to owner!`, 'success'); loadProducts(); }
        else showAlert(data.message || 'Error sending products.', 'danger');
      })
      .catch(() => showAlert('Could not connect to server.', 'danger'));
  }

  // ── Delete single ───────────────────────────────────────────
  window.deleteProduct = function (id) {
    if (!confirm('Delete this product?')) return;
    fetch(API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete', id }),
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) { showAlert('Product deleted.', 'success'); loadProducts(); }
        else showAlert(data.message, 'danger');
      })
      .catch(() => {
        products = products.filter(p => p.id !== id);
        localStorage.setItem('fg_supplier_products', JSON.stringify(products));
        showAlert('Product deleted.', 'success');
        renderTable();
        updateStats();
      });
  };

  // ── Bulk actions ────────────────────────────────────────────
  checkAll.addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
  });

  function getCheckedIds() {
    return [...document.querySelectorAll('.row-check:checked')].map(c => parseInt(c.value));
  }

  document.getElementById('btnVerifySelected').addEventListener('click', () => {
    const ids = getCheckedIds();
    if (!ids.length) { showAlert('Select at least one product.', 'warning'); return; }
    updateStatus(ids, 'verified', `${ids.length} product(s) verified!`);
  });

  document.getElementById('btnSendToOwner').addEventListener('click', () => {
    const ids = getCheckedIds().filter(id => {
      const p = products.find(x => x.id === id);
      return p && p.status === 'verified';
    });
    if (!ids.length) { showAlert('Select verified products to send.', 'warning'); return; }
    if (!ownersList.length) {
      loadOwners().then(() => showOwnerPicker(ids, ownerId => sendToOwner(ids, ownerId)));
    } else {
      showOwnerPicker(ids, ownerId => sendToOwner(ids, ownerId));
    }
  });

  document.getElementById('btnDeleteSelected').addEventListener('click', () => {
    const ids = getCheckedIds();
    if (!ids.length) { showAlert('Select at least one product.', 'warning'); return; }
    if (!confirm(`Delete ${ids.length} product(s)?`)) return;
    ids.forEach(id => {
      fetch(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id }),
      }).catch(() => {
        products = products.filter(p => p.id !== id);
      });
    });
    products = products.filter(p => !ids.includes(p.id));
    localStorage.setItem('fg_supplier_products', JSON.stringify(products));
    showAlert(`${ids.length} product(s) deleted.`, 'success');
    renderTable();
    updateStats();
  });

  // ── Move to Draft ───────────────────────────────────────────
  document.getElementById('btnDraftSelected').addEventListener('click', () => {
    const ids = getCheckedIds().filter(id => {
      const p = products.find(x => x.id === id);
      // Only allow moving back to draft if not already sent/received
      return p && (p.status === 'verified' || p.status === 'rejected');
    });
    if (!ids.length) {
      showAlert('Select verified or rejected products to move back to draft.', 'warning');
      return;
    }
    fetch(API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'update_status', ids, status: 'draft' }),
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          showAlert(`${ids.length} product(s) moved to Draft.`, 'success');
          loadProducts();
        } else {
          showAlert(data.message || 'Error moving to draft.', 'danger');
        }
      })
      .catch(() => {
        ids.forEach(id => {
          const p = products.find(x => x.id === id);
          if (p) p.status = 'draft';
        });
        localStorage.setItem('fg_supplier_products', JSON.stringify(products));
        showAlert(`${ids.length} product(s) moved to Draft.`, 'success');
        renderTable();
        updateStats();
      });
  });

  // ── Update status helper ────────────────────────────────────
  function updateStatus(ids, status, successMsg) {
    fetch(API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'update_status', ids, status }),
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) { showAlert(successMsg, 'success'); loadProducts(); }
        else showAlert(data.message, 'danger');
      })
      .catch(() => {
        ids.forEach(id => {
          const p = products.find(x => x.id === id);
          if (p) p.status = status;
        });
        localStorage.setItem('fg_supplier_products', JSON.stringify(products));
        showAlert(successMsg, 'success');
        renderTable();
        updateStats();
      });
  }

  // ── Helpers ─────────────────────────────────────────────────
  function showAlert(msg, type) {
    const icons = { success: 'bi-check-circle-fill', danger: 'bi-exclamation-circle-fill', warning: 'bi-exclamation-triangle-fill' };
    alertBox.className = `alert-bar alert-${type}`;
    alertBox.innerHTML = `<i class="bi ${icons[type] || 'bi-info-circle-fill'}"></i> ${msg}`;
    alertBox.style.display = 'flex';
    setTimeout(() => { alertBox.style.display = 'none'; }, 4000);
  }

  function esc(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  function statusLabel(s) {
    const map = {
      draft:          'Draft',
      verified:       'Verified',
      sent_to_owner:  'Sent to Owner',
      owner_received: 'Received',
      rejected:       'Rejected',
    };
    return map[s] || s;
  }

  // ── Search filter ────────────────────────────────────────────
  document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#productTableBody tr[data-id]').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // ── Init ────────────────────────────────────────────────────
  loadProducts();
  loadOwners(); // pre-fetch owners for the picker

  // ── Category Combobox ────────────────────────────────────────
  const DEFAULT_CATEGORIES = [
    'Battery',
    'Tempered Glass',
    'LCD / Screen',
    'Charger',
    'Cable',
    'Earphones',
    'Power Bank',
    'Phone Case',
    'Wireless Charger',
    'Car Holder',
  ];

  // Load saved custom categories from localStorage
  function getSavedCategories() {
    try {
      return JSON.parse(localStorage.getItem('fg_custom_categories') || '[]');
    } catch { return []; }
  }

  function saveCustomCategory(cat) {
    const saved = getSavedCategories();
    if (!saved.includes(cat)) {
      saved.push(cat);
      localStorage.setItem('fg_custom_categories', JSON.stringify(saved));
    }
  }

  function getAllCategories() {
    const custom = getSavedCategories();
    const all = [...DEFAULT_CATEGORIES];
    custom.forEach(c => { if (!all.includes(c)) all.push(c); });
    return all;
  }

  const catInput    = document.getElementById('category');
  const catDropdown = document.getElementById('catComboDropdown');
  const catList     = document.getElementById('catComboList');
  const catAdd      = document.getElementById('catComboAdd');
  const catAddText  = document.getElementById('catComboAddText');
  const catArrow    = document.getElementById('catComboArrow');

  function renderCatDropdown(query) {
    const all = getAllCategories();
    const q   = (query || '').toLowerCase().trim();
    const filtered = q ? all.filter(c => c.toLowerCase().includes(q)) : all;

    catList.innerHTML = filtered.length
      ? filtered.map(c => {
          const highlighted = q
            ? c.replace(new RegExp(`(${q})`, 'gi'), '<mark>$1</mark>')
            : c;
          return `<div class="desc-combo-item" data-value="${esc(c)}">
            <i class="bi bi-tag-fill"></i>${highlighted}
          </div>`;
        }).join('')
      : `<div class="desc-combo-empty">No match — add it below</div>`;

    // Show "Add" option if typed value isn't an exact match
    const exactMatch = all.some(c => c.toLowerCase() === q);
    if (q && !exactMatch) {
      catAddText.textContent = query;
      catAdd.style.display = 'flex';
    } else {
      catAdd.style.display = 'none';
    }

    // Bind click on items
    catList.querySelectorAll('.desc-combo-item').forEach(item => {
      item.addEventListener('mousedown', e => {
        e.preventDefault();
        catInput.value = item.dataset.value;
        closeCatDropdown();
      });
    });
  }

  function openCatDropdown() {
    renderCatDropdown(catInput.value);
    catDropdown.style.display = 'flex';
    catArrow.classList.add('open');
  }

  function closeCatDropdown() {
    catDropdown.style.display = 'none';
    catArrow.classList.remove('open');
  }

  catInput.addEventListener('focus', openCatDropdown);
  catInput.addEventListener('input', () => renderCatDropdown(catInput.value));
  catInput.addEventListener('blur', () => setTimeout(closeCatDropdown, 150));

  catArrow.addEventListener('mousedown', e => {
    e.preventDefault();
    if (catDropdown.style.display === 'none') {
      catInput.focus();
      openCatDropdown();
    } else {
      closeCatDropdown();
    }
  });

  catAdd.addEventListener('mousedown', e => {
    e.preventDefault();
    const newCat = catInput.value.trim();
    if (newCat) {
      saveCustomCategory(newCat);
      catInput.value = newCat;
    }
    closeCatDropdown();
  });
});
