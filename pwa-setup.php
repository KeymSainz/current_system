<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Fix&amp;Go — PWA Setup Guide</title>
  <link rel="manifest" href="manifest.json"/>
  <link rel="stylesheet" href="assets/css/mobile.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{background:#0f1117;color:#e2e8f0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;padding:1.5rem;max-width:680px;margin:0 auto;}
    h1{font-size:1.5rem;font-weight:900;color:#e6a800;margin-bottom:0.25rem;}
    .subtitle{color:#94a3b8;font-size:0.9rem;margin-bottom:2rem;}
    .card{background:#1a1d2e;border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:1.25rem;margin-bottom:1rem;}
    .card h2{font-size:1rem;font-weight:800;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem;}
    .card p,.card li{font-size:0.86rem;color:#94a3b8;line-height:1.65;}
    .card ul{padding-left:1.25rem;}
    .card ol{padding-left:1.25rem;}
    .card ol li{margin-bottom:0.5rem;}
    .badge{display:inline-flex;align-items:center;padding:0.15rem 0.55rem;border-radius:20px;font-size:0.72rem;font-weight:700;}
    .badge-green{background:rgba(40,167,69,0.15);color:#28A745;}
    .badge-yellow{background:rgba(230,168,0,0.15);color:#e6a800;}
    .badge-blue{background:rgba(59,130,246,0.15);color:#3b82f6;}
    .check{color:#28A745;margin-right:0.35rem;}
    .status-row{display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.84rem;}
    .status-row:last-child{border-bottom:none;}
    code{background:rgba(255,255,255,0.08);padding:0.15rem 0.45rem;border-radius:6px;font-size:0.8rem;font-family:monospace;}
    .btn{display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.25rem;border-radius:10px;background:linear-gradient(135deg,#e6a800,#c98f00);color:#000;font-weight:800;font-size:0.88rem;border:none;cursor:pointer;text-decoration:none;margin-top:0.5rem;}
    .btn:hover{opacity:0.88;}
    .btn-outline{background:transparent;border:1.5px solid #e6a800;color:#e6a800;}
    .section-title{font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#94a3b8;margin:1.5rem 0 0.75rem;}
    #swStatus,#manifestStatus,#httpsStatus{font-weight:700;}
  </style>
</head>
<body>
  <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;">
    <span style="font-size:2rem;">🔧</span>
    <div>
      <h1>Fix&amp;Go PWA Setup</h1>
      <div class="subtitle">Progressive Web App — Installation &amp; Testing Guide</div>
    </div>
  </div>

  <!-- Live Status -->
  <div class="section-title">Live PWA Status</div>
  <div class="card">
    <h2><i class="bi bi-activity" style="color:#28A745;"></i> Current Environment</h2>
    <div class="status-row">
      <span>HTTPS / Localhost</span>
      <span id="httpsStatus">Checking…</span>
    </div>
    <div class="status-row">
      <span>Service Worker</span>
      <span id="swStatus">Checking…</span>
    </div>
    <div class="status-row">
      <span>Web Manifest</span>
      <span id="manifestStatus">Checking…</span>
    </div>
    <div class="status-row">
      <span>Installable (beforeinstallprompt)</span>
      <span id="installStatus"><span class="badge badge-yellow">Waiting…</span></span>
    </div>
    <div class="status-row">
      <span>Running as Standalone</span>
      <span id="standaloneStatus">Checking…</span>
    </div>
  </div>

  <!-- What was built -->
  <div class="section-title">What Was Built</div>
  <div class="card">
    <h2><i class="bi bi-check-circle-fill" style="color:#28A745;"></i> PWA Features Added</h2>
    <div class="status-row"><span>manifest.json</span><span class="badge badge-green">✓ Created</span></div>
    <div class="status-row"><span>Service Worker (sw.js)</span><span class="badge badge-green">✓ Created</span></div>
    <div class="status-row"><span>Offline fallback page</span><span class="badge badge-green">✓ Created</span></div>
    <div class="status-row"><span>App icons (8 sizes: 72–512px)</span><span class="badge badge-green">✓ Generated</span></div>
    <div class="status-row"><span>PWA meta tags</span><span class="badge badge-green">✓ 68 pages</span></div>
    <div class="status-row"><span>Install prompt banner</span><span class="badge badge-green">✓ Created</span></div>
    <div class="status-row"><span>mobile.css (responsive)</span><span class="badge badge-green">✓ Created</span></div>
    <div class="status-row"><span>Bottom nav — Customer (12 pages)</span><span class="badge badge-green">✓ Added</span></div>
    <div class="status-row"><span>Bottom nav — Technician (8 pages)</span><span class="badge badge-green">✓ Added</span></div>
    <div class="status-row"><span>Bottom nav — Supplier (10 pages)</span><span class="badge badge-green">✓ Added</span></div>
    <div class="status-row"><span>Auth CSS mobile improvements</span><span class="badge badge-green">✓ Updated</span></div>
  </div>

  <!-- How to install on Android -->
  <div class="section-title">Install on Android</div>
  <div class="card">
    <h2><i class="bi bi-android2" style="color:#3ddc84;"></i> Android — Chrome</h2>
    <ol>
      <li>Open <code>http://&lt;your-IP&gt;/</code> in Chrome</li>
      <li>Tap the <strong>⋮ menu</strong> (top right)</li>
      <li>Tap <strong>"Add to Home screen"</strong> or <strong>"Install app"</strong></li>
      <li>Tap <strong>Add / Install</strong> in the confirmation dialog</li>
      <li>Fix&amp;Go icon appears on your home screen ✅</li>
    </ol>
    <p style="margin-top:0.75rem;color:#e6a800;font-size:0.82rem;">
      💡 An <strong>Install banner</strong> will also appear automatically at the bottom of the screen after 3 seconds.
    </p>
  </div>

  <!-- How to install on iOS -->
  <div class="section-title">Install on iPhone / iPad</div>
  <div class="card">
    <h2><i class="bi bi-apple" style="color:#e2e8f0;"></i> iOS — Safari</h2>
    <ol>
      <li>Open the site in <strong>Safari</strong> (must be Safari — Chrome on iOS doesn't support install)</li>
      <li>Tap the <strong>Share</strong> button <i class="bi bi-box-arrow-up"></i></li>
      <li>Scroll down and tap <strong>"Add to Home Screen"</strong></li>
      <li>Edit the name if desired → tap <strong>Add</strong></li>
      <li>Fix&amp;Go icon appears on your home screen ✅</li>
    </ol>
    <p style="margin-top:0.75rem;color:#94a3b8;font-size:0.82rem;">
      Note: iOS Safari doesn't support the install prompt banner — use the manual steps above.
    </p>
  </div>

  <!-- Accessing from phone -->
  <div class="section-title">Access from Your Phone (XAMPP)</div>
  <div class="card">
    <h2><i class="bi bi-wifi" style="color:#3b82f6;"></i> Connect Phone to XAMPP</h2>
    <p>Your phone and PC must be on the <strong>same WiFi network</strong>.</p>
    <ol style="margin-top:0.75rem;">
      <li>Find your PC's local IP: open CMD → type <code>ipconfig</code> → look for <code>IPv4 Address</code></li>
      <li>On your phone, open: <code>http://&lt;PC-IP&gt;/</code></li>
      <li>Example: <code id="exampleUrl">http://192.168.1.x/</code></li>
    </ol>
    <p style="margin-top:0.75rem;color:#e6a800;font-size:0.82rem;">
      ⚠️ Service workers require HTTPS or localhost. On local network HTTP the SW will register but some features may be limited.
    </p>
  </div>

  <!-- HTTPS note -->
  <div class="section-title">For Full PWA on Network (HTTPS)</div>
  <div class="card">
    <h2><i class="bi bi-shield-lock-fill" style="color:#e6a800;"></i> Enable HTTPS on XAMPP</h2>
    <p style="margin-bottom:0.75rem;">Service workers work fully on <code>localhost</code>. For network access from phones, enable SSL:</p>
    <ol>
      <li>Open <code>c:\xampp\apache\conf\extra\httpd-ssl.conf</code></li>
      <li>Or use a tunnel tool like <strong>ngrok</strong>: <code>ngrok http 80</code></li>
      <li>ngrok gives you a public HTTPS URL instantly — perfect for mobile testing</li>
    </ol>
  </div>

  <a href="index.php" class="btn" style="display:flex;width:fit-content;">
    <i class="bi bi-house-fill"></i> Back to Fix&amp;Go
  </a>
  <div style="height:2rem;"></div>

  <script>
  // Status checks
  (function(){
    // HTTPS check
    var isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
    document.getElementById('httpsStatus').innerHTML = isSecure
      ? '<span class="badge badge-green">✓ Secure / Localhost</span>'
      : '<span class="badge badge-yellow">⚠ HTTP (SW limited)</span>';

    // SW check
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.getRegistrations().then(function(regs){
        if (regs.length > 0) {
          document.getElementById('swStatus').innerHTML = '<span class="badge badge-green">✓ Registered (' + regs.length + ')</span>';
        } else {
          document.getElementById('swStatus').innerHTML = '<span class="badge badge-yellow">⏳ Not registered yet (reload page)</span>';
        }
      });
    } else {
      document.getElementById('swStatus').innerHTML = '<span class="badge" style="background:rgba(220,53,69,0.15);color:#dc3545;">✗ Not supported</span>';
    }

    // Manifest check
    fetch('manifest.json').then(function(r){
      document.getElementById('manifestStatus').innerHTML = r.ok
        ? '<span class="badge badge-green">✓ Found (HTTP ' + r.status + ')</span>'
        : '<span class="badge" style="background:rgba(220,53,69,0.15);color:#dc3545;">✗ Error ' + r.status + '</span>';
    }).catch(function(){
      document.getElementById('manifestStatus').innerHTML = '<span class="badge" style="background:rgba(220,53,69,0.15);color:#dc3545;">✗ Not reachable</span>';
    });

    // Standalone check
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
    document.getElementById('standaloneStatus').innerHTML = isStandalone
      ? '<span class="badge badge-green">✓ Running as app</span>'
      : '<span class="badge badge-blue">ℹ Browser mode</span>';

    // Install prompt
    window.addEventListener('beforeinstallprompt', function(){
      document.getElementById('installStatus').innerHTML = '<span class="badge badge-green">✓ Installable!</span>';
    });
    window.addEventListener('appinstalled', function(){
      document.getElementById('installStatus').innerHTML = '<span class="badge badge-green">✓ Installed!</span>';
      document.getElementById('standaloneStatus').innerHTML = '<span class="badge badge-green">✓ Running as app</span>';
    });

    // Show local IP example
    document.getElementById('exampleUrl').textContent = 'http://' + location.hostname + '/';
  })();
  </script>
</body>
</html>
