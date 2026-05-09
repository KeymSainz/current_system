/**
 * Fix&Go — Owner: View Supervisor Reports
 */

(function() {
  'use strict';

  let allReports = [];

  document.addEventListener('DOMContentLoaded', function() {
    const user = FGAuth.UserStore.get();
    if (!user || user.role !== 'owner') {
      window.location.href = '../../../login.html';
      return;
    }

    const fullName = (user.firstName || '') + ' ' + (user.lastName || '');
    document.getElementById('navUserName').textContent = fullName.trim() || user.email || 'Owner';

    // Load reports
    loadReports();

    // Modal handlers
    const reportModal = document.getElementById('reportModal');
    const btnCloseModal = document.getElementById('btnCloseModal');

    btnCloseModal.addEventListener('click', () => closeModal());
    reportModal.addEventListener('click', (e) => {
      if (e.target === reportModal) closeModal();
    });
  });

  function loadReports() {
    fetch('../../../backend/owner_supervisor_reports.php?action=list', {
      method: 'GET',
      credentials: 'include',
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        allReports = data.reports || [];
        renderReports(allReports);
      } else {
        showError(data.message || 'Failed to load reports.');
      }
    })
    .catch(err => {
      console.error('Failed to load reports:', err);
      showError('Network error. Please try again.');
    });
  }

  function renderReports(reports) {
    const grid = document.getElementById('reportsGrid');
    
    if (!reports || reports.length === 0) {
      grid.innerHTML = `
        <div class="empty-state" style="grid-column: 1 / -1;">
          <i class="bi bi-inbox"></i>
          <p>No reports received yet</p>
          <small style="color:var(--fg-muted);">Reports will appear here when your supervisor sends them</small>
        </div>
      `;
      return;
    }

    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    grid.innerHTML = reports.map(r => {
      const monthName = monthNames[r.report_month - 1];
      const sentDate = new Date(r.sent_at).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });

      return `
        <div class="report-card" onclick="viewReport(${r.id})">
          <div class="report-header">
            <div>
              <div class="report-period">${monthName} ${r.report_year}</div>
              <div class="report-date">Sent: ${sentDate}</div>
            </div>
            <i class="bi bi-chevron-right" style="font-size:1.5rem;color:var(--fg-muted);"></i>
          </div>
          <div class="report-stats">
            <div class="stat-item">
              <div class="stat-value" style="color:#3b82f6;">${r.total_products}</div>
              <div class="stat-label">Products</div>
            </div>
            <div class="stat-item">
              <div class="stat-value" style="color:#10b981;">${r.total_quantity}</div>
              <div class="stat-label">Quantity</div>
            </div>
            <div class="stat-item" style="grid-column: 1 / -1;">
              <div class="stat-value" style="color:#e6a800;">₱${parseFloat(r.total_value).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
              <div class="stat-label">Total Value</div>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  window.viewReport = function(reportId) {
    const report = allReports.find(r => r.id === reportId);
    if (!report) return;

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const monthName = monthNames[report.report_month - 1];

    document.getElementById('modalTitle').innerHTML = 
      `<i class="bi bi-file-earmark-bar-graph"></i> ${monthName} ${report.report_year} Report`;

    const products = JSON.parse(report.report_data || '[]');

    let detailsHTML = `
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
        <div class="stat-item">
          <div class="stat-value" style="color:#3b82f6;">${report.total_products}</div>
          <div class="stat-label">Products Received</div>
        </div>
        <div class="stat-item">
          <div class="stat-value" style="color:#10b981;">${report.total_quantity}</div>
          <div class="stat-label">Total Quantity</div>
        </div>
        <div class="stat-item">
          <div class="stat-value" style="color:#e6a800;">₱${parseFloat(report.total_value).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
          <div class="stat-label">Total Value</div>
        </div>
      </div>
    `;

    if (products.length > 0) {
      detailsHTML += `
        <h6 style="font-weight:700;margin-bottom:1rem;color:var(--fg-text);">Product Details</h6>
        <div style="overflow-x:auto;">
          <table class="products-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">Unit Price</th>
                <th style="text-align:right;">Total</th>
              </tr>
            </thead>
            <tbody>
              ${products.map(p => {
                const date = new Date(p.updated_at).toLocaleDateString('en-PH', {
                  month: 'short',
                  day: 'numeric'
                });
                const total = parseFloat(p.srp) * parseInt(p.qty);
                return `
                  <tr>
                    <td>${date}</td>
                    <td><strong>${escapeHtml(p.item_description)}</strong></td>
                    <td>${escapeHtml(p.category || 'N/A')}</td>
                    <td>${escapeHtml(p.brand || 'N/A')}</td>
                    <td style="text-align:center;font-weight:600;">${p.qty}</td>
                    <td style="text-align:right;">₱${parseFloat(p.srp).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td style="text-align:right;font-weight:700;color:var(--fg-primary);">₱${total.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      `;
    }

    document.getElementById('reportDetails').innerHTML = detailsHTML;
    document.getElementById('reportModal').classList.add('open');
  };

  function closeModal() {
    document.getElementById('reportModal').classList.remove('open');
  }

  function showError(message) {
    const grid = document.getElementById('reportsGrid');
    grid.innerHTML = `
      <div class="empty-state" style="grid-column: 1 / -1;">
        <i class="bi bi-exclamation-triangle" style="color:#dc3545;"></i>
        <p>${escapeHtml(message)}</p>
      </div>
    `;
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

})();
