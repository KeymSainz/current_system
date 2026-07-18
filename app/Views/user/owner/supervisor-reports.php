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
  <title>Fix&Go — Supervisor Reports</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/auth.css?v=5" />
  <link rel="stylesheet" href="/assets/css/dashboard.css?v=5" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    body { background: var(--fg-bg); }
    .page-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
    .page-header { margin-bottom: 2rem; }
    .page-header h2 { font-size: 1.8rem; font-weight: 800; color: var(--fg-text); margin: 0 0 0.5rem 0; }
    .page-header p { color: var(--fg-muted); margin: 0; font-size: 0.9rem; }
    
    .reports-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 1.5rem; margin-bottom: 2rem;
    }
    .report-card {
      background: var(--fg-card-bg); border: 1px solid var(--fg-border);
      border-radius: 14px; padding: 1.5rem;
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: pointer;
    }
    .report-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.12);
      border-color: var(--fg-primary);
    }
    .report-header {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 1rem; padding-bottom: 1rem;
      border-bottom: 2px solid var(--fg-border);
    }
    .report-period {
      font-size: 1.1rem; font-weight: 700; color: var(--fg-text);
    }
    .report-date {
      font-size: 0.75rem; color: var(--fg-muted);
    }
    .report-stats {
      display: grid; grid-template-columns: repeat(2, 1fr);
      gap: 0.75rem;
    }
    .stat-item {
      background: var(--fg-bg); border-radius: 8px;
      padding: 0.75rem; text-align: center;
    }
    .stat-value {
      font-size: 1.3rem; font-weight: 800; line-height: 1;
      margin-bottom: 0.25rem;
    }
    .stat-label {
      font-size: 0.7rem; color: var(--fg-muted);
      font-weight: 600; text-transform: uppercase;
    }
    .empty-state {
      text-align: center; padding: 4rem 2rem; color: var(--fg-muted);
      background: var(--fg-card-bg); border: 1px solid var(--fg-border);
      border-radius: 14px;
    }
    .empty-state i { font-size: 4rem; display: block; margin-bottom: 1rem; opacity: 0.3; }
    
    /* Modal styles */
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
      width: 100%; max-width: 900px;
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
    .modal-head h5 { margin: 0; font-weight: 800; font-size: 1.2rem; color: var(--fg-text); }
    .modal-body { padding: 1.5rem 1.75rem; }
    .btn-close-modal {
      width: 32px; height: 32px; border-radius: 8px;
      border: 1.5px solid var(--fg-border);
      background: transparent; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      color: var(--fg-muted); font-size: 1rem; transition: all 0.2s;
    }
    .btn-close-modal:hover { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,0.08); }
    
    .products-table {
      width: 100%; border-collapse: collapse;
      margin-top: 1rem;
    }
    .products-table thead th {
      background: var(--fg-primary); color: #fff;
      padding: 0.75rem 1rem; text-align: left;
      font-weight: 700; font-size: 0.75rem;
      text-transform: uppercase; letter-spacing: 0.5px;
    }
    .products-table tbody td {
      padding: 0.75rem 1rem; border-bottom: 1px solid var(--fg-border);
      color: var(--fg-text); font-size: 0.85rem;
    }
    .products-table tbody tr:hover { background: rgba(230,168,0,0.03); }
  </style>
</head>
<body>
  <nav class="fg-navbar" role="navigation">
    <a href="/dashboard.php" style="text-decoration:none;display:flex;align-items:center;">
      <img src="/assets/images/logo.png" alt="Fix&Go"
           style="height:48px;width:auto;object-fit:contain;"
           onerror="this.outerHTML='<span style=\'font-size:1.2rem;font-weight:800;color:var(--fg-primary);\'>🔧 Fix&amp;Go</span>'">
    </a>
    <div class="d-flex align-items:center gap-3">
      <span id="navRoleBadge" class="role-badge owner">🏪 Owner</span>
      <span id="navUserName" style="font-size:0.9rem;font-weight:600;color:var(--fg-text);"></span>
      <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill" id="themeIcon"></i></button>
      <a href="/dashboard.php" class="btn btn-sm"
         style="border:1.5px solid var(--fg-border);border-radius:8px;color:var(--fg-muted);background:transparent;font-size:0.85rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
      </a>
    </div>
  </nav>

  <div class="page-container">
    <div class="page-header">
      <h2><i class="bi bi-file-earmark-bar-graph"></i> Supervisor Reports</h2>
      <p>Monthly reports submitted by your supervisor</p>
    </div>

    <div class="reports-grid" id="reportsGrid">
      <div class="empty-state">
        <i class="bi bi-hourglass-split"></i>
        <p>Loading reports...</p>
      </div>
    </div>
  </div>

  <!-- Report Detail Modal -->
  <div class="modal-overlay" id="reportModal">
    <div class="modal-box">
      <div class="modal-head">
        <h5 id="modalTitle"><i class="bi bi-file-earmark-bar-graph"></i> Report Details</h5>
        <button class="btn-close-modal" id="btnCloseModal">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="modal-body">
        <div id="reportDetails">Loading...</div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/theme.js"></script>
  <script src="/assets/js/auth-utils.js"></script>
  <script src="supervisor-reports.js"></script>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>

