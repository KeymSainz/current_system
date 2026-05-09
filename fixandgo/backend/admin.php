<?php
/**
 * Fix&Go — Admin API
 * All admin actions go through this single endpoint.
 *
 * Requires: role = 'admin' in session
 *
 * GET  ?action=stats          → dashboard counts
 * GET  ?action=users          → all users (filterable by role/status)
 * GET  ?action=applicants     → pending supplier/owner applications
 * POST action=approve         → approve supplier/owner application
 * POST action=reject          → reject supplier/owner application
 * POST action=ban             → ban a user
 * POST action=unban           → unban a user
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

// ── Auth guard: admin only ────────────────────────────────────────────────
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin access required.']);
    exit;
}

$pdo    = require __DIR__ . '/db.php';
$method = $_SERVER['REQUEST_METHOD'];

// ============================================================
// GET
// ============================================================
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'stats';

    // ── Dashboard stats ───────────────────────────────────────
    if ($action === 'stats') {
        $stats = [];

        // Total users by role
        $stmt = $pdo->query(
            "SELECT role, COUNT(*) AS cnt FROM users WHERE role != 'admin' GROUP BY role"
        );
        $byRole = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $byRole[$row['role']] = (int)$row['cnt'];
        }

        // Pending applicants
        $pending = $pdo->query(
            "SELECT COUNT(*) FROM users
             WHERE role IN ('supplier','owner') AND application_status = 'pending'"
        )->fetchColumn();

        // Banned users
        $banned = $pdo->query(
            "SELECT COUNT(*) FROM users WHERE is_banned = 1"
        )->fetchColumn();

        // New users today
        $today = $pdo->query(
            "SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE() AND role != 'admin'"
        )->fetchColumn();

        echo json_encode([
            'success' => true,
            'stats'   => [
                'total_customers'    => $byRole['customer']         ?? 0,
                'total_suppliers'    => $byRole['supplier']         ?? 0,
                'total_owners'       => $byRole['owner']            ?? 0,
                'total_technicians'  => $byRole['phone_technician'] ?? 0,
                'total_sales_person' => $byRole['sales_person']     ?? 0,
                'total_supervisors'  => $byRole['supervisor']       ?? 0,
                'pending_applicants' => (int)$pending,
                'banned_users'       => (int)$banned,
                'new_today'          => (int)$today,
            ],
        ]);
        exit;
    }

    // ── All users ─────────────────────────────────────────────
    if ($action === 'users') {
        $role   = $_GET['role']   ?? '';
        $status = $_GET['status'] ?? '';
        $search = trim($_GET['search'] ?? '');

        $where  = ["u.role != 'admin'"];
        $params = [];

        if ($role) {
            $where[]  = 'u.role = ?';
            $params[] = $role;
        }
        if ($status === 'banned') {
            $where[] = 'u.is_banned = 1';
        } elseif ($status === 'active') {
            $where[] = 'u.is_banned = 0 AND u.is_active = 1';
        } elseif ($status === 'pending') {
            $where[] = "u.application_status = 'pending'";
        }
        if ($search) {
            $where[]  = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $like     = '%' . $search . '%';
            $params   = array_merge($params, [$like, $like, $like]);
        }

        $sql = "SELECT
                    u.id, u.first_name, u.last_name, u.email, u.phone,
                    u.role, u.provider, u.is_verified, u.is_active,
                    u.is_banned, u.banned_reason, u.banned_at,
                    u.application_status, u.application_notes,
                    u.created_at,
                    COALESCE(u.login_attempts, 0) AS login_attempts,
                    u.locked_until,
                    u.last_login_at,
                    u.last_logout_at
                FROM users u
                WHERE " . implode(' AND ', $where) . "
                ORDER BY u.created_at DESC
                LIMIT 500";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'users' => $users]);
        exit;
    }

    // ── Pending applicants ────────────────────────────────────
    if ($action === 'applicants') {
        $stmt = $pdo->query(
            "SELECT sa.id, sa.first_name, sa.last_name, sa.email, sa.phone,
                    sa.role, sa.company_name, sa.shop_name,
                    sa.doc_gov_id, sa.doc_bir, sa.doc_dti, sa.doc_bank,
                    sa.status, sa.submitted_at
             FROM seller_applications sa
             WHERE sa.status = 'pending'
             ORDER BY sa.submitted_at ASC"
        );
        echo json_encode(['success' => true, 'applicants' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    // ── Get user application documents ────────────────────────
    if ($action === 'user_documents') {
        $userId = (int)($_GET['user_id'] ?? 0);
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User ID required']);
            exit;
        }

        // Get user info
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        // Get application documents from seller_applications table
        $stmt = $pdo->prepare(
            "SELECT id, company_name, shop_name, doc_gov_id, doc_bir, doc_dti, doc_bank, 
                    status, submitted_at, admin_notes
             FROM seller_applications 
             WHERE email = ? 
             ORDER BY submitted_at DESC 
             LIMIT 1"
        );
        $stmt->execute([$user['email']]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'user' => $user,
            'application' => $application
        ]);
        exit;
    }

    // ── Login / Logout logs ───────────────────────────────────
    if ($action === 'login_logs') {
        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = min(100, max(1, (int)($_GET['limit'] ?? 25)));
        $offset = ($page - 1) * $limit;

        $search        = trim($_GET['search']        ?? '');
        $filterAction  = trim($_GET['filter_action'] ?? '');
        $filterDate    = trim($_GET['date']           ?? '');

        // Build WHERE clauses
        $where  = [];
        $params = [];

        if ($search !== '') {
            $where[]  = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $like     = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($filterAction !== '' && in_array($filterAction, ['login','logout','session_expired'], true)) {
            $where[]  = "l.action = ?";
            $params[] = $filterAction;
        }

        if ($filterDate !== '') {
            $where[]  = "DATE(l.created_at) = ?";
            $params[] = $filterDate;
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Total count
        $countStmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM user_activity_logs l
             JOIN users u ON l.user_id = u.id
             $whereSQL"
        );
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Fetch page
        $dataStmt = $pdo->prepare(
            "SELECT l.id, l.action, l.ip_address, l.user_agent, l.created_at,
                    u.first_name, u.last_name, u.email, u.role
             FROM user_activity_logs l
             JOIN users u ON l.user_id = u.id
             $whereSQL
             ORDER BY l.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $dataStmt->execute([...$params, $limit, $offset]);
        $logs = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'logs'    => $logs,
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit,
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ============================================================
// POST
// ============================================================
if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    // ── Approve applicant ─────────────────────────────────────
    if ($action === 'approve') {
        $appId  = (int)($body['user_id'] ?? 0); // user_id here is application id
        $notes  = trim($body['notes'] ?? '');
        if (!$appId) { echo json_encode(['success' => false, 'message' => 'Application ID required.']); exit; }

        // Get application
        $stmt = $pdo->prepare("SELECT * FROM seller_applications WHERE id = ? AND status = 'pending'");
        $stmt->execute([$appId]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$app) { echo json_encode(['success' => false, 'message' => 'Application not found or already reviewed.']); exit; }

        // Update application status
        $pdo->prepare(
            "UPDATE seller_applications SET status='approved', admin_notes=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?"
        )->execute([$notes, $_SESSION['user_id'], $appId]);

        // Activate the user account
        $pdo->prepare(
            "UPDATE users SET is_active=1, application_status='approved', application_notes=?, reviewed_by=?, reviewed_at=NOW()
             WHERE email=? AND role=?"
        )->execute([$notes, $_SESSION['user_id'], $app['email'], $app['role']]);

        echo json_encode(['success' => true, 'message' => 'Application approved. User can now log in.']);
        
        // Get the user ID for notification
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$app['email'], $app['role']]);
        $newUserId = $stmt->fetchColumn();

        // Send notification to the approved user (new seller account)
        if ($newUserId) {
            require_once __DIR__ . '/notification_helper.php';
            $roleLabel = $app['role'] === 'owner' ? 'Shop Owner' : 'Supplier';
            $notifTitle = 'Application Approved! 🎉';
            $notifBody = "Congratulations! Your {$roleLabel} application has been approved. You can now log in and start using your seller account.";
            if ($notes) {
                $notifBody .= " Admin note: {$notes}";
            }
            sendNotification($newUserId, 'system', $notifTitle, $notifBody);
        }

        // Also notify the original customer account
        $customerId = (int)($app['user_id'] ?? 0);
        if ($customerId && $customerId !== $newUserId) {
            require_once __DIR__ . '/notification_helper.php';
            $roleLabel = $app['role'] === 'owner' ? 'Shop Owner' : 'Supplier';
            sendNotification(
                $customerId,
                'system',
                'Seller Application Approved! 🎉',
                "Your {$roleLabel} application has been approved! Log in with your seller email ({$app['email']}) to access your new seller dashboard."
            );
        }

        // Send approval email
        require_once __DIR__ . '/mailer.php';
        $roleLabel = $app['role'] === 'owner' ? 'Shop Owner' : 'Supplier';
        $subject = "Application Approved — Fix&Go {$roleLabel}";
        $emailBody = "
<h2>🎉 Congratulations! Your Application Has Been Approved</h2>
<p>Dear {$app['first_name']} {$app['last_name']},</p>
<p>We're excited to inform you that your <strong>{$roleLabel}</strong> application has been approved!</p>
<p><strong>What's Next?</strong></p>
<ol>
  <li>Log in to your seller account using your registered email: <strong>{$app['email']}</strong></li>
  <li>Complete your shop profile and upload your logo</li>
  <li>Start adding products to your catalog</li>
  <li>Begin receiving orders from customers</li>
</ol>
" . ($notes ? "<p><strong>Admin Note:</strong> {$notes}</p>" : '') . "
<p>Welcome to the Fix&Go seller community!</p>
";
        sendEmail($app['email'], $app['first_name'] . ' ' . $app['last_name'], $subject, $emailBody);
        
        exit;
    }

    // ── Reject applicant ──────────────────────────────────────
    if ($action === 'reject') {
        $appId  = (int)($body['user_id'] ?? 0);
        $notes  = trim($body['notes'] ?? '');
        if (!$appId) { echo json_encode(['success' => false, 'message' => 'Application ID required.']); exit; }
        if (!$notes) { echo json_encode(['success' => false, 'message' => 'Rejection reason is required.']); exit; }

        $stmt = $pdo->prepare("SELECT * FROM seller_applications WHERE id = ? AND status = 'pending'");
        $stmt->execute([$appId]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$app) { echo json_encode(['success' => false, 'message' => 'Application not found or already reviewed.']); exit; }

        // Update application status to rejected
        $pdo->prepare(
            "UPDATE seller_applications SET status='rejected', admin_notes=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?"
        )->execute([$notes, $_SESSION['user_id'], $appId]);

        // Get the customer ID for notification (from the original customer account)
        $customerId = $app['user_id']; // This is the customer who applied

        // Send notification to the customer who applied BEFORE deleting the seller account
        if ($customerId) {
            require_once __DIR__ . '/notification_helper.php';
            $roleLabel = $app['role'] === 'owner' ? 'Shop Owner' : 'Supplier';
            $notifTitle = 'Application Rejected';
            $notifBody = "Unfortunately, your {$roleLabel} application has been rejected. Reason: {$notes}. You can reapply with the same email after reviewing the requirements.";
            sendNotification($customerId, 'system', $notifTitle, $notifBody);
        }

        // Send rejection email BEFORE deleting the account
        require_once __DIR__ . '/mailer.php';
        $roleLabel = $app['role'] === 'owner' ? 'Shop Owner' : 'Supplier';
        $subject = "Application Rejected — Fix&Go {$roleLabel}";
        $emailBody = "
<h2>Application Rejected</h2>
<p>Dear {$app['first_name']} {$app['last_name']},</p>
<p>Thank you for your interest in becoming a <strong>{$roleLabel}</strong> on Fix&Go.</p>
<p>After careful review, we regret to inform you that we are unable to approve your application at this time.</p>
<p><strong>Reason:</strong> {$notes}</p>
<p><strong>What's Next?</strong></p>
<ul>
  <li>Review the rejection reason carefully</li>
  <li>Prepare the correct documents</li>
  <li>You can reapply using the same email address</li>
</ul>
<p>Your email address has been freed up and is available for reapplication.</p>
<p>If you have any questions, please contact our support team.</p>
";
        sendEmail($app['email'], $app['first_name'] . ' ' . $app['last_name'], $subject, $emailBody);

        // DELETE the seller user account to free up the email for reapplication
        // This allows the applicant to use the same email when applying again
        $pdo->prepare("DELETE FROM users WHERE email = ? AND role = ?")->execute([$app['email'], $app['role']]);

        echo json_encode(['success' => true, 'message' => 'Application rejected. Email freed for reapplication.']);
        exit;
    }

    // ── Ban user ──────────────────────────────────────────────
    if ($action === 'ban') {
        $userId = (int)($body['user_id'] ?? 0);
        $reason = trim($body['reason'] ?? '');
        if (!$userId) { echo json_encode(['success' => false, 'message' => 'User ID required.']); exit; }
        if (!$reason) { echo json_encode(['success' => false, 'message' => 'Ban reason is required.']); exit; }

        $stmt = $pdo->prepare(
            "UPDATE users
             SET is_banned = 1, banned_reason = ?, banned_at = NOW(), is_active = 0
             WHERE id = ? AND role != 'admin'"
        );
        $stmt->execute([$reason, $userId]);

        echo json_encode(['success' => true, 'message' => 'User has been banned.']);
        exit;
    }

    // ── Unban user ────────────────────────────────────────────
    if ($action === 'unban') {
        $userId = (int)($body['user_id'] ?? 0);
        if (!$userId) { echo json_encode(['success' => false, 'message' => 'User ID required.']); exit; }

        $stmt = $pdo->prepare(
            "UPDATE users
             SET is_banned = 0, banned_reason = NULL, banned_at = NULL, is_active = 1
             WHERE id = ? AND role != 'admin'"
        );
        $stmt->execute([$userId]);

        echo json_encode(['success' => true, 'message' => 'User has been unbanned.']);
        exit;
    }

    // ── Unlock locked account ─────────────────────────────────
    if ($action === 'unlock') {
        $userId = (int)($body['user_id'] ?? 0);
        if (!$userId) { echo json_encode(['success' => false, 'message' => 'User ID required.']); exit; }

        $stmt = $pdo->prepare(
            "UPDATE users
             SET login_attempts = 0, locked_until = NULL
             WHERE id = ? AND role != 'admin'"
        );
        $stmt->execute([$userId]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Account unlocked. User can now log in.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
