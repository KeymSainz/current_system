/**
 * Fix&Go — Owner Staff Management
 */

(function() {
  'use strict';

  let pendingStaff = [];
  let activeStaff = [];

  // Auth guard
  document.addEventListener('DOMContentLoaded', function() {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'owner') {
      window.location.href = '../../../login.html';
      return;
    }

    const fullName = (user.firstName || '') + ' ' + (user.lastName || '');
    document.getElementById('navUserName').textContent = fullName.trim() || user.email || 'Owner';

    // Load data
    loadStats();
    loadPending();
    loadActive();

    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        switchTab(tabName);
      });
    });

    // Register Staff Modal
    const registerModal = document.getElementById('registerModal');
    const btnRegisterStaff = document.getElementById('btnRegisterStaff');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const btnCancelModal = document.getElementById('btnCancelModal');
    const registerStaffForm = document.getElementById('registerStaffForm');

    btnRegisterStaff.addEventListener('click', () => {
      registerModal.classList.add('open');
    });

    btnCloseModal.addEventListener('click', () => {
      registerModal.classList.remove('open');
      registerStaffForm.reset();
    });

    btnCancelModal.addEventListener('click', () => {
      registerModal.classList.remove('open');
      registerStaffForm.reset();
    });

    registerModal.addEventListener('click', (e) => {
      if (e.target === registerModal) {
        registerModal.classList.remove('open');
        registerStaffForm.reset();
      }
    });

    registerStaffForm.addEventListener('submit', handleRegisterStaff);

    // Real-time password match validation
    const passwordInput = document.getElementById('staffPassword');
    const confirmPasswordInput = document.getElementById('staffConfirmPassword');
    const passwordMatchError = document.getElementById('passwordMatchError');

    confirmPasswordInput.addEventListener('input', function() {
      if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
        passwordMatchError.style.display = 'block';
        confirmPasswordInput.style.borderColor = '#dc3545';
      } else {
        passwordMatchError.style.display = 'none';
        confirmPasswordInput.style.borderColor = '';
      }
    });

    passwordInput.addEventListener('input', function() {
      if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
        passwordMatchError.style.display = 'block';
        confirmPasswordInput.style.borderColor = '#dc3545';
      } else {
        passwordMatchError.style.display = 'none';
        confirmPasswordInput.style.borderColor = '';
      }
    });
  });

  function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

    // Update tab content
    document.getElementById('pendingTab').style.display = tabName === 'pending' ? 'block' : 'none';
    document.getElementById('activeTab').style.display = tabName === 'active' ? 'block' : 'none';
  }

  function loadStats() {
    fetch('../../../backend/owner_staff.php?action=stats', {
      method: 'GET',
      credentials: 'include',
    })
    .then(r => r.json())
    .then(data => {
      if (data.success && data.stats) {
        document.getElementById('statTotal').textContent = data.stats.total_staff || 0;
        document.getElementById('statActive').textContent = data.stats.active_staff || 0;
        document.getElementById('statPending').textContent = data.stats.pending_staff || 0;
        document.getElementById('statTechnicians').textContent = data.stats.technician_count || 0;
        
        document.getElementById('pendingBadge').textContent = data.stats.pending_staff || 0;
        document.getElementById('activeBadge').textContent = data.stats.active_staff || 0;
      }
    })
    .catch(err => console.error('Failed to load stats:', err));
  }

  function loadPending() {
    fetch('../../../backend/owner_staff.php?action=pending', {
      method: 'GET',
      credentials: 'include',
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        pendingStaff = data.pending || [];
        renderPending(pendingStaff);
      }
    })
    .catch(err => console.error('Failed to load pending:', err));
  }

  function loadActive() {
    fetch('../../../backend/owner_staff.php?action=active', {
      method: 'GET',
      credentials: 'include',
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        activeStaff = data.staff || [];
        renderActive(activeStaff);
      }
    })
    .catch(err => console.error('Failed to load active:', err));
  }

  function renderPending(staff) {
    const tbody = document.getElementById('pendingTableBody');
    
    if (!staff || staff.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <i class="bi bi-inbox"></i>
              <p>No pending applications</p>
            </div>
          </td>
        </tr>`;
      return;
    }

    tbody.innerHTML = staff.map(s => {
      const roleBadge = getRoleBadge(s.role);
      const appliedDate = new Date(s.created_at).toLocaleDateString('en-PH', {
        year: 'numeric', month: 'short', day: 'numeric'
      });

      return `
        <tr>
          <td><strong>${escapeHtml(s.first_name + ' ' + s.last_name)}</strong></td>
          <td>${escapeHtml(s.email)}</td>
          <td>${escapeHtml(s.phone || 'N/A')}</td>
          <td>${roleBadge}</td>
          <td style="font-size:0.8rem;">${appliedDate}</td>
          <td style="text-align:center;">
            <button class="btn-action approve" onclick="approveStaff(${s.id})">
              <i class="bi bi-check-lg"></i> Approve
            </button>
            <button class="btn-action reject" onclick="rejectStaff(${s.id})">
              <i class="bi bi-x-lg"></i> Reject
            </button>
          </td>
        </tr>
      `;
    }).join('');
  }

  function renderActive(staff) {
    const tbody = document.getElementById('activeTableBody');
    
    if (!staff || staff.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7">
            <div class="empty-state">
              <i class="bi bi-inbox"></i>
              <p>No staff members yet</p>
            </div>
          </td>
        </tr>`;
      return;
    }

    tbody.innerHTML = staff.map(s => {
      const roleBadge = getRoleBadge(s.role);
      const statusBadge = s.is_active 
        ? '<span style="color:#28A745;font-weight:600;">● Active</span>'
        : '<span style="color:#6C757D;font-weight:600;">● Inactive</span>';
      const joinedDate = new Date(s.created_at).toLocaleDateString('en-PH', {
        year: 'numeric', month: 'short', day: 'numeric'
      });

      return `
        <tr>
          <td><strong>${escapeHtml(s.first_name + ' ' + s.last_name)}</strong></td>
          <td>${escapeHtml(s.email)}</td>
          <td>${escapeHtml(s.phone || 'N/A')}</td>
          <td>${roleBadge}</td>
          <td>${statusBadge}</td>
          <td style="font-size:0.8rem;">${joinedDate}</td>
          <td style="text-align:center;">
            ${s.is_active 
              ? `<button class="btn-action deactivate" onclick="deactivateStaff(${s.id})">
                   <i class="bi bi-pause-circle"></i> Deactivate
                 </button>`
              : `<button class="btn-action approve" onclick="activateStaff(${s.id})">
                   <i class="bi bi-play-circle"></i> Activate
                 </button>`
            }
          </td>
        </tr>
      `;
    }).join('');
  }

  function getRoleBadge(role) {
    const badges = {
      'sales_person': '<span class="role-badge role-sales"><i class="bi bi-person-badge"></i> Sales Person</span>',
      'supervisor': '<span class="role-badge role-supervisor"><i class="bi bi-person-check"></i> Supervisor</span>',
      'phone_technician': '<span class="role-badge role-technician"><i class="bi bi-tools"></i> Technician</span>'
    };
    return badges[role] || role;
  }

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

  // Global functions
  window.approveStaff = function(id) {
    if (!confirm('Approve this staff application?')) return;

    fetch('../../../backend/owner_staff.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'approve', ids: [id] })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert('Staff approved successfully!', 'success');
        loadStats();
        loadPending();
        loadActive();
      } else {
        showAlert(data.message || 'Failed to approve staff.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to approve:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  };

  window.rejectStaff = function(id) {
    if (!confirm('Reject this staff application? This will delete the account.')) return;

    fetch('../../../backend/owner_staff.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'reject', ids: [id] })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert('Staff application rejected.', 'success');
        loadStats();
        loadPending();
      } else {
        showAlert(data.message || 'Failed to reject staff.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to reject:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  };

  window.deactivateStaff = function(id) {
    if (!confirm('Deactivate this staff member? They will not be able to login.')) return;

    fetch('../../../backend/owner_staff.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'deactivate', ids: [id] })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert('Staff member deactivated.', 'success');
        loadStats();
        loadActive();
      } else {
        showAlert(data.message || 'Failed to deactivate staff.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to deactivate:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  };

  window.activateStaff = function(id) {
    if (!confirm('Reactivate this staff member?')) return;

    fetch('../../../backend/owner_staff.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ action: 'activate', ids: [id] })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert('Staff member reactivated.', 'success');
        loadStats();
        loadActive();
      } else {
        showAlert(data.message || 'Failed to reactivate staff.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to reactivate:', err);
      showAlert('Network error. Please try again.', 'danger');
    });
  };

  function handleRegisterStaff(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');

    // Validate password match
    if (password !== confirmPassword) {
      showAlert('Passwords do not match. Please try again.', 'danger');
      document.getElementById('staffConfirmPassword').focus();
      return;
    }

    // Validate password strength
    if (password.length < 6) {
      showAlert('Password must be at least 6 characters long.', 'danger');
      return;
    }

    const data = {
      action: 'register',
      role: formData.get('role'),
      first_name: formData.get('first_name').trim(),
      last_name: formData.get('last_name').trim(),
      email: formData.get('email').trim().toLowerCase(),
      phone: formData.get('phone').trim(),
      password: password
    };

    // Validate role
    if (!['sales_person', 'supervisor'].includes(data.role)) {
      showAlert('Invalid position selected. Only Sales Person and Supervisor can be registered directly.', 'danger');
      return;
    }

    // Validate required fields
    if (!data.first_name || !data.last_name || !data.email || !data.phone) {
      showAlert('All fields are required.', 'danger');
      return;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(data.email)) {
      showAlert('Please enter a valid email address.', 'danger');
      return;
    }

    // Validate phone format
    const phoneRegex = /^[\+]?[0-9\s\-\(\)]+$/;
    if (!phoneRegex.test(data.phone)) {
      showAlert('Please enter a valid phone number.', 'danger');
      return;
    }

    // Disable submit button
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Registering...';

    fetch('../../../backend/owner_staff.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert('Staff registered successfully!', 'success');
        document.getElementById('registerModal').classList.remove('open');
        document.getElementById('registerStaffForm').reset();
        document.getElementById('passwordMatchError').style.display = 'none';
        document.getElementById('staffConfirmPassword').style.borderColor = '';
        loadStats();
        loadActive();
      } else {
        showAlert(data.message || 'Failed to register staff.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to register staff:', err);
      showAlert('Network error. Please try again.', 'danger');
    })
    .finally(() => {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
  }

})();
