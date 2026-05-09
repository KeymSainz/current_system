/**
 * Fix&Go — Supervisor Reports
 */

(function() {
  'use strict';

  let currentDate = new Date();
  let reportData = [];

  document.addEventListener('DOMContentLoaded', function() {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'supervisor') {
      window.location.href = '../../../login.html';
      return;
    }

    const fullName = (user.firstName || '') + ' ' + (user.lastName || '');
    document.getElementById('navUserName').textContent = fullName.trim() || user.email || 'Supervisor';

    // Load initial report
    loadMonthlyReport();

    // Month navigation
    document.getElementById('btnPrevMonth').addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      loadMonthlyReport();
    });

    document.getElementById('btnNextMonth').addEventListener('click', () => {
      const now = new Date();
      if (currentDate.getMonth() === now.getMonth() && currentDate.getFullYear() === now.getFullYear()) {
        return; // Can't go beyond current month
      }
      currentDate.setMonth(currentDate.getMonth() + 1);
      loadMonthlyReport();
    });

    // Send to owner
    document.getElementById('btnSendToOwner').addEventListener('click', handleSendToOwner);
  });

  function loadMonthlyReport() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth() + 1; // JavaScript months are 0-indexed

    // Update month display
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('currentMonth').textContent = `${monthNames[currentDate.getMonth()]} ${year}`;

    // Disable next button if current month
    const now = new Date();
    const btnNext = document.getElementById('btnNextMonth');
    if (currentDate.getMonth() === now.getMonth() && currentDate.getFullYear() === now.getFullYear()) {
      btnNext.disabled = true;
      btnNext.style.opacity = '0.5';
      btnNext.style.cursor = 'not-allowed';
    } else {
      btnNext.disabled = false;
      btnNext.style.opacity = '1';
      btnNext.style.cursor = 'pointer';
    }

    // Fetch report data
    fetch(`../../../backend/supervisor_reports.php?action=monthly&year=${year}&month=${month}`, {
      method: 'GET',
      credentials: 'include',
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        reportData = data.products || [];
        renderReport(reportData, data.stats || {});
      } else {
        showError(data.message || 'Failed to load report.');
      }
    })
    .catch(err => {
      console.error('Failed to load report:', err);
      showError('Network error. Please try again.');
    });
  }

  function renderReport(products, stats) {
    // Update statistics
    document.getElementById('statReceived').textContent = stats.total_products || 0;
    document.getElementById('statTotalQty').textContent = stats.total_quantity || 0;
    document.getElementById('statTotalValue').textContent = '₱' + (parseFloat(stats.total_value || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    document.getElementById('statCategories').textContent = stats.unique_categories || 0;

    // Render table
    const tbody = document.getElementById('reportTableBody');
    
    if (!products || products.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7">
            <div class="empty-state">
              <i class="bi bi-inbox"></i>
              <p>No products received this month</p>
            </div>
          </td>
        </tr>
      `;
      return;
    }

    tbody.innerHTML = products.map(p => {
      const receivedDate = new Date(p.updated_at).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
      const totalValue = parseFloat(p.srp) * parseInt(p.qty);

      return `
        <tr>
          <td>${receivedDate}</td>
          <td><strong>${escapeHtml(p.item_description)}</strong></td>
          <td>${escapeHtml(p.category || 'N/A')}</td>
          <td>${escapeHtml(p.brand || 'N/A')}</td>
          <td style="text-align:center;font-weight:600;">${p.qty}</td>
          <td style="text-align:right;">₱${parseFloat(p.srp).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
          <td style="text-align:right;font-weight:700;color:var(--fg-primary);">₱${totalValue.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
        </tr>
      `;
    }).join('');
  }

  function showError(message) {
    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = `
      <tr>
        <td colspan="7">
          <div class="empty-state">
            <i class="bi bi-exclamation-triangle" style="color:#dc3545;"></i>
            <p>${escapeHtml(message)}</p>
          </div>
        </td>
      </tr>
    `;
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function handleSendToOwner() {
    if (!reportData || reportData.length === 0) {
      showAlert('No data to send. Please select a month with products.', 'danger');
      return;
    }

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth() + 1;
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const monthName = monthNames[currentDate.getMonth()];

    if (!confirm(`Send ${monthName} ${year} report to Owner?\n\nThis report contains ${reportData.length} product(s).`)) {
      return;
    }

    const btn = document.getElementById('btnSendToOwner');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';

    fetch('../../../backend/supervisor_reports.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({
        action: 'send_to_owner',
        year: year,
        month: month
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showAlert(data.message || 'Report sent to owner successfully!', 'success');
      } else {
        showAlert(data.message || 'Failed to send report.', 'danger');
      }
    })
    .catch(err => {
      console.error('Failed to send report:', err);
      showAlert('Network error. Please try again.', 'danger');
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = originalHTML;
    });
  }

  function showAlert(message, type = 'success') {
    const alertBox = document.getElementById('alertBox');
    const icon = type === 'success' ? 'check-circle-fill' : 
                 type === 'danger' ? 'exclamation-triangle-fill' : 
                 'info-circle-fill';
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

})();
