<?php
/**
 * Fix&Go — Admin Setup & Diagnostic
 * DELETE THIS FILE after setup is complete.
 *
 * Visit: http://localhost/current_system/fixandgo/backend/admin_setup.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pdo = require __DIR__ . '/db.php';
$messages = [];
$errors   = [];

// ── Step 1: Check/add admin to role ENUM ─────────────────────────────────
try {
    $row = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch(PDO::FETCH_ASSOC);
    if ($row && strpos($row['Type'], 'admin') === false) {
        $pdo->exec("ALTER TABLE users MODIFY COLUMN role
            ENUM('customer','sales_person','supplier','supervisor','owner','phone_technician','admin')
            NOT NULL DEFAULT 'customer'");
        $messages[] = '✅ Added "admin" to role ENUM.';
    } else {
        $messages[] = '✅ Role ENUM already includes "admin".';
    }
} catch (Exception $e) {
    $errors[] = '❌ ENUM alter failed: ' . $e->getMessage();
}

// ── Step 2: Add missing columns ───────────────────────────────────────────
$cols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);

foreach ([
    "ALTER TABLE users ADD COLUMN is_banned TINYINT(1) NOT NULL DEFAULT 0"          => 'is_banned',
    "ALTER TABLE users ADD COLUMN banned_reason VARCHAR(255) NULL"                   => 'banned_reason',
    "ALTER TABLE users ADD COLUMN banned_at DATETIME NULL"                           => 'banned_at',
    "ALTER TABLE users ADD COLUMN application_status ENUM('none','pending','approved','rejected') NOT NULL DEFAULT 'none'" => 'application_status',
    "ALTER TABLE users ADD COLUMN application_notes TEXT NULL"                       => 'application_notes',
    "ALTER TABLE users ADD COLUMN reviewed_by INT UNSIGNED NULL"                     => 'reviewed_by',
    "ALTER TABLE users ADD COLUMN reviewed_at DATETIME NULL"                         => 'reviewed_at',
] as $sql => $col) {
    if (!in_array($col, $cols)) {
        try {
            $pdo->exec($sql);
            $messages[] = "✅ Added column: $col";
        } catch (Exception $e) {
            $errors[] = "❌ Failed to add $col: " . $e->getMessage();
        }
    } else {
        $messages[] = "✅ Column already exists: $col";
    }
}

// ── Step 4: Create seller_applications table ─────────────────────────────
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS seller_applications (
        id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
        user_id          INT UNSIGNED   NOT NULL,
        role             ENUM('supplier','owner') NOT NULL,
        first_name       VARCHAR(50)    NOT NULL,
        last_name        VARCHAR(50)    NOT NULL,
        email            VARCHAR(255)   NOT NULL,
        phone            VARCHAR(20)    NOT NULL,
        company_name     VARCHAR(150)   NOT NULL,
        shop_name        VARCHAR(150)   NULL,
        doc_gov_id       VARCHAR(500)   NULL,
        doc_bir          VARCHAR(500)   NULL,
        doc_dti          VARCHAR(500)   NULL,
        doc_bank         VARCHAR(500)   NULL,
        status           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        admin_notes      TEXT           NULL,
        reviewed_by      INT UNSIGNED   NULL,
        reviewed_at      DATETIME       NULL,
        submitted_at     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        INDEX idx_user (user_id),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $messages[] = '✅ seller_applications table ready.';
} catch (Exception $e) {
    $errors[] = '❌ seller_applications table: ' . $e->getMessage();
}

// ── Step 5: Create/update admin account ──────────────────────────────────
$adminEmail    = 'keymlingas@gmail.com';
$adminPassword = 'hakim1234';
$hash          = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);

$existing = $pdo->prepare("SELECT id, role FROM users WHERE email = ?")->execute([$adminEmail])
    ? $pdo->prepare("SELECT id, role FROM users WHERE email = ?")->execute([$adminEmail]) && false
    : false;

$stmt = $pdo->prepare("SELECT id, role FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    // Update role to admin and set password
    $pdo->prepare("UPDATE users SET role = 'admin', password_hash = ?, is_verified = 1, is_active = 1 WHERE email = ?")
        ->execute([$hash, $adminEmail]);
    $messages[] = "✅ Admin account updated (ID: {$existing['id']}, was role: {$existing['role']}).";
} else {
    // Insert new admin
    $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role, is_verified, is_active) VALUES (?, ?, ?, ?, 'admin', 1, 1)")
        ->execute(['Fix&Go', 'Admin', $adminEmail, $hash]);
    $messages[] = "✅ Admin account created (ID: " . $pdo->lastInsertId() . ").";
}

// ── Step 4: Verify login works ────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT id, email, role, password_hash, is_verified, is_active FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$pwOk = $admin && password_verify($adminPassword, $admin['password_hash']);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Setup</title>
  <style>
    body { font-family: monospace; background: #0f1117; color: #e2e8f0; padding: 2rem; }
    .box { background: #1a1d27; border: 1px solid #2a2d3a; border-radius: 12px; padding: 1.5rem; max-width: 700px; margin-bottom: 1rem; }
    h2  { color: #e6a800; margin-top: 0; }
    h3  { color: #94a3b8; font-size: 0.9rem; margin: 1rem 0 0.5rem; }
    .ok   { color: #4ade80; }
    .err  { color: #f87171; }
    .info { color: #60a5fa; }
    .warn { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 0.75rem 1rem; color: #f87171; margin-top: 1rem; font-size: 0.85rem; }
    .cred { background: #0f1117; border: 1px solid #2a2d3a; border-radius: 8px; padding: 1rem; margin: 0.75rem 0; }
    .cred div { margin: 0.3rem 0; }
    a { color: #e6a800; }
    li { margin: 0.3rem 0; font-size: 0.85rem; }
  </style>
</head>
<body>
  <div class="box">
    <h2>🔧 Fix&amp;Go Admin Setup</h2>

    <h3>Migration Steps:</h3>
    <ul>
      <?php foreach ($messages as $m): ?>
        <li class="ok"><?= htmlspecialchars($m) ?></li>
      <?php endforeach; ?>
      <?php foreach ($errors as $e): ?>
        <li class="err"><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>

    <h3>Admin Account Verification:</h3>
    <?php if ($admin): ?>
      <div class="cred">
        <div><span style="color:#94a3b8;">ID:</span> <span class="info"><?= $admin['id'] ?></span></div>
        <div><span style="color:#94a3b8;">Email:</span> <span class="info"><?= htmlspecialchars($admin['email']) ?></span></div>
        <div><span style="color:#94a3b8;">Role:</span> <span class="<?= $admin['role'] === 'admin' ? 'ok' : 'err' ?>"><?= $admin['role'] ?></span></div>
        <div><span style="color:#94a3b8;">Verified:</span> <span class="<?= $admin['is_verified'] ? 'ok' : 'err' ?>"><?= $admin['is_verified'] ? 'Yes' : 'No' ?></span></div>
        <div><span style="color:#94a3b8;">Active:</span> <span class="<?= $admin['is_active'] ? 'ok' : 'err' ?>"><?= $admin['is_active'] ? 'Yes' : 'No' ?></span></div>
        <div><span style="color:#94a3b8;">Password check:</span> <span class="<?= $pwOk ? 'ok' : 'err' ?>"><?= $pwOk ? '✅ Password matches' : '❌ Password mismatch' ?></span></div>
      </div>
    <?php else: ?>
      <p class="err">❌ Admin account not found!</p>
    <?php endif; ?>

    <?php if ($pwOk && $admin['role'] === 'admin'): ?>
      <div style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-radius:8px;padding:1rem;margin-top:1rem;">
        <div class="ok" style="font-weight:700;margin-bottom:0.75rem;">✅ Setup complete! Use these credentials:</div>
        <div class="cred">
          <div><span style="color:#94a3b8;">URL:</span> <a href="../login.html">http://localhost/current_system/fixandgo/login.html</a></div>
          <div><span style="color:#94a3b8;">Email:</span> <span class="info">keymlingas@gmail.com</span></div>
          <div><span style="color:#94a3b8;">Password:</span> <span class="info">hakim1234</span></div>
        </div>
        <div style="font-size:0.8rem;color:#94a3b8;margin-top:0.5rem;">
          Note: Login uses OTP — make sure your SMTP email is configured in config.php
        </div>
      </div>
    <?php endif; ?>

    <div class="warn" style="margin-top:1rem;">
      🗑️ <strong>DELETE this file after setup:</strong> <code>fixandgo/backend/admin_setup.php</code>
    </div>
  </div>
</body>
</html>
