<?php
/**
 * Fix&Go — Admin Password Hash Generator
 * DELETE THIS FILE after you've set your admin password.
 *
 * Usage: Open in browser, copy the hash, run the UPDATE query below.
 */

// ── Set your desired admin password here ──────────────────────────────────
$password = 'Admin1234';   // ← CHANGE THIS to your own password

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// ── Auto-update the admin account in the database ─────────────────────────
$pdo = require __DIR__ . '/db.php';

$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = 'admin@gmail.com' AND role = 'admin'");
$stmt->execute([$hash]);
$updated = $stmt->rowCount();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Hash Generator</title>
  <style>
    body { font-family: monospace; background: #0f1117; color: #e2e8f0; padding: 2rem; }
    .box { background: #1a1d27; border: 1px solid #2a2d3a; border-radius: 12px; padding: 1.5rem; max-width: 700px; }
    h2 { color: #e6a800; margin-top: 0; }
    .hash { background: #0f1117; padding: 1rem; border-radius: 8px; word-break: break-all; color: #4ade80; font-size: 0.85rem; margin: 1rem 0; }
    .warn { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 0.75rem 1rem; color: #f87171; margin-top: 1rem; font-size: 0.85rem; }
    .ok   { background: rgba(34,197,94,0.1);  border: 1px solid rgba(34,197,94,0.3);  border-radius: 8px; padding: 0.75rem 1rem; color: #4ade80; margin-top: 1rem; font-size: 0.85rem; }
    label { color: #94a3b8; font-size: 0.8rem; }
  </style>
</head>
<body>
  <div class="box">
    <h2>🔐 Admin Password Setup</h2>

    <label>Password used:</label>
    <div class="hash"><?= htmlspecialchars($password) ?></div>

    <label>Generated bcrypt hash:</label>
    <div class="hash"><?= htmlspecialchars($hash) ?></div>

    <?php if ($updated > 0): ?>
      <div class="ok">
        ✅ Admin password updated successfully in the database.<br>
        You can now log in at <a href="../login.html" style="color:#4ade80;">login.html</a>
        with <strong>admin@fixandgo.com</strong> / <strong><?= htmlspecialchars($password) ?></strong>
      </div>
    <?php else: ?>
      <div class="warn">
        ⚠️ Admin account not found in the database.<br>
        Make sure you ran <strong>migrate_admin.sql</strong> first in phpMyAdmin.
      </div>
    <?php endif; ?>

    <div class="warn" style="margin-top:1rem;">
      🗑️ <strong>DELETE this file after use:</strong>
      <code>fixandgo/backend/gen_admin_hash.php</code>
    </div>
  </div>
</body>
</html>
