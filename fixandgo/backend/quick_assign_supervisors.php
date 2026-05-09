<?php
/**
 * Quick Assign Supervisors to Owner
 * This script helps you quickly assign supervisors to an owner
 */

require_once __DIR__ . '/db.php';
$pdo = require __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    $ownerId = (int)$_POST['owner_id'];
    $supervisorId = (int)$_POST['supervisor_id'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO staff_assignments (owner_id, staff_id, staff_role, is_active)
            VALUES (?, ?, 'supervisor', 1)
            ON DUPLICATE KEY UPDATE is_active = 1
        ");
        $stmt->execute([$ownerId, $supervisorId]);
        $message = "✅ Supervisor assigned successfully!";
        $messageType = "success";
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
        $messageType = "error";
    }
}

// Get all owners
$stmt = $pdo->query("SELECT id, first_name, last_name, email FROM users WHERE role = 'owner' AND is_active = 1 ORDER BY first_name, last_name");
$owners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all supervisors
$stmt = $pdo->query("SELECT id, first_name, last_name, email FROM users WHERE role = 'supervisor' AND is_active = 1 ORDER BY first_name, last_name");
$supervisors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current assignments
$stmt = $pdo->query("
    SELECT sa.*, 
           o.first_name as owner_fname, o.last_name as owner_lname, o.email as owner_email,
           s.first_name as supervisor_fname, s.last_name as supervisor_lname, s.email as supervisor_email
    FROM staff_assignments sa
    JOIN users o ON o.id = sa.owner_id
    JOIN users s ON s.id = sa.staff_id
    WHERE sa.staff_role = 'supervisor'
    ORDER BY o.first_name, s.first_name
");
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quick Assign Supervisors</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; padding: 2rem; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 0.5rem; }
        .subtitle { color: #666; margin-bottom: 2rem; font-size: 0.95rem; }
        .section { background: white; padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 1rem; font-size: 1.3rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333; }
        select, button { width: 100%; padding: 0.75rem; border: 2px solid #ddd; border-radius: 6px; font-size: 1rem; font-family: inherit; }
        select:focus { outline: none; border-color: #e6a800; }
        button { background: #e6a800; color: white; font-weight: 600; cursor: pointer; border: none; margin-top: 0.5rem; }
        button:hover { background: #cc9600; }
        button:active { transform: scale(0.98); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #e6a800; color: white; font-weight: 600; }
        tr:hover { background: #f9f9f9; }
        .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600; }
        .badge-owner { background: #e6a800; color: white; }
        .badge-supervisor { background: #3b82f6; color: white; }
        .message { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-weight: 600; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .empty { text-align: center; padding: 2rem; color: #999; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
        code { background: #f0f0f0; padding: 0.2rem 0.4rem; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Quick Assign Supervisors</h1>
        <p class="subtitle">Assign supervisors to owners so they can send products</p>

        <?php if (isset($message)): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="grid">
            <!-- Assign Form -->
            <div class="section">
                <h2>➕ Assign Supervisor to Owner</h2>
                
                <?php if (count($owners) === 0): ?>
                    <div class="empty">❌ No owners found. Please create an owner account first.</div>
                <?php elseif (count($supervisors) === 0): ?>
                    <div class="empty">❌ No supervisors found. Please register supervisors first.</div>
                <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label>Select Owner:</label>
                            <select name="owner_id" required>
                                <option value="">-- Choose Owner --</option>
                                <?php foreach ($owners as $owner): ?>
                                    <option value="<?= $owner['id'] ?>">
                                        <?= htmlspecialchars($owner['first_name'] . ' ' . $owner['last_name']) ?> 
                                        (<?= htmlspecialchars($owner['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select Supervisor:</label>
                            <select name="supervisor_id" required>
                                <option value="">-- Choose Supervisor --</option>
                                <?php foreach ($supervisors as $supervisor): ?>
                                    <option value="<?= $supervisor['id'] ?>">
                                        <?= htmlspecialchars($supervisor['first_name'] . ' ' . $supervisor['last_name']) ?> 
                                        (<?= htmlspecialchars($supervisor['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" name="assign">
                            ✅ Assign Supervisor to Owner
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Info Box -->
            <div class="section">
                <h2>ℹ️ How It Works</h2>
                <p style="margin-bottom: 1rem; line-height: 1.6;">
                    <strong>Registering</strong> a supervisor creates a user account with <code>role='supervisor'</code>.
                </p>
                <p style="margin-bottom: 1rem; line-height: 1.6;">
                    <strong>Assigning</strong> a supervisor links them to an owner in the <code>staff_assignments</code> table.
                </p>
                <p style="margin-bottom: 1rem; line-height: 1.6;">
                    Only <strong>assigned</strong> supervisors will appear in the "Send to Supervisor" dropdown.
                </p>
                <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 1rem; margin-top: 1rem;">
                    <strong>💡 Tip:</strong> You can assign the same supervisor to multiple owners, or multiple supervisors to one owner.
                </div>
            </div>
        </div>

        <!-- Current Assignments -->
        <div class="section">
            <h2>📋 Current Assignments</h2>
            <?php if (count($assignments) === 0): ?>
                <div class="empty">
                    No supervisors assigned yet. Use the form above to assign supervisors to owners.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Owner</th>
                            <th>Supervisor</th>
                            <th>Assigned Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td>
                                    <span class="badge badge-owner">OWNER</span>
                                    <?= htmlspecialchars($assignment['owner_fname'] . ' ' . $assignment['owner_lname']) ?>
                                    <br>
                                    <small style="color: #666;"><?= htmlspecialchars($assignment['owner_email']) ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-supervisor">SUPERVISOR</span>
                                    <?= htmlspecialchars($assignment['supervisor_fname'] . ' ' . $assignment['supervisor_lname']) ?>
                                    <br>
                                    <small style="color: #666;"><?= htmlspecialchars($assignment['supervisor_email']) ?></small>
                                </td>
                                <td><?= date('M d, Y', strtotime($assignment['assigned_at'])) ?></td>
                                <td>
                                    <?php if ($assignment['is_active']): ?>
                                        <span style="color: #28a745; font-weight: 600;">✓ Active</span>
                                    <?php else: ?>
                                        <span style="color: #dc3545; font-weight: 600;">✗ Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Available Users -->
        <div class="grid">
            <div class="section">
                <h2>👥 Available Owners (<?= count($owners) ?>)</h2>
                <?php if (count($owners) === 0): ?>
                    <div class="empty">No owners found</div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($owners as $owner): ?>
                                <tr>
                                    <td><?= $owner['id'] ?></td>
                                    <td><?= htmlspecialchars($owner['first_name'] . ' ' . $owner['last_name']) ?></td>
                                    <td><?= htmlspecialchars($owner['email']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>👔 Available Supervisors (<?= count($supervisors) ?>)</h2>
                <?php if (count($supervisors) === 0): ?>
                    <div class="empty">No supervisors found</div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($supervisors as $supervisor): ?>
                                <tr>
                                    <td><?= $supervisor['id'] ?></td>
                                    <td><?= htmlspecialchars($supervisor['first_name'] . ' ' . $supervisor['last_name']) ?></td>
                                    <td><?= htmlspecialchars($supervisor['email']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
