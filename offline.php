<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <!-- PWA -->
  <meta name="theme-color" content="#e6a800">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Fix&amp;Go">
  <link rel="manifest" href="fixandgo/manifest.json">
  <link rel="apple-touch-icon" href="fixandgo/assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="fixandgo/assets/css/mobile.css">
  <title>Fix&amp;Go — You're Offline</title>
  <link rel="manifest" href="fixandgo/manifest.json"/>
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{
      min-height:100vh;display:flex;flex-direction:column;align-items:center;
      justify-content:center;text-align:center;padding:2rem;
      background:#0f1117;color:#e2e8f0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    }
    .icon{font-size:4rem;margin-bottom:1.5rem;opacity:0.6;}
    h1{font-size:1.6rem;font-weight:800;margin-bottom:0.75rem;color:#fff;}
    p{color:#94a3b8;font-size:0.95rem;line-height:1.6;max-width:320px;margin-bottom:2rem;}
    .btn{
      display:inline-flex;align-items:center;gap:0.5rem;
      padding:0.75rem 1.5rem;border-radius:12px;
      background:linear-gradient(135deg,#e6a800,#c98f00);
      color:#000;font-weight:800;font-size:0.9rem;
      border:none;cursor:pointer;text-decoration:none;
      transition:opacity 0.2s;
    }
    .btn:hover{opacity:0.85;}
    .logo{font-size:1.4rem;font-weight:900;color:#e6a800;margin-bottom:2rem;letter-spacing:-0.5px;}
  </style>
</head>
<body>
  <div class="logo">🔧 Fix&amp;Go</div>
  <div class="icon">📡</div>
  <h1>You're Offline</h1>
  <p>No internet connection detected. Some features require a connection — please check your network and try again.</p>
  <button class="btn" onclick="window.location.reload()">
    🔄 Try Again
  </button>
<script src="fixandgo/assets/js/pwa.js" defer></script>
</body>
</html>

