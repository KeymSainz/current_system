<?php
/**
 * Fix&Go — Owner Staff Management API
 * Manage staff applications and active staff members
 *
 * GET  ?action=pending     → pending staff applications
 * GET  ?action=active      → active staff members
 * GET  ?action=stats       → staff statistics
 * POST action=approve      → approve staff application(s)
 * POST action=reject       → reject staff application(s)
 * POST action=deactivate   → deactivate staff member(s)
 * POST action=activate     → reactivate staff member(s)
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$ownerId = (int) $_SESSION['user_id'];
$pdo     = require __DIR__ . '/db.php';
$method  = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'pending';

    if ($action === 'pending') {
        // Get pending staff applications
        $stmt = $pdo->prepare(
            "SELECT 
                id,
                first_name,
                last_name,
                email,
                phone,
                role,
                created_at
             FROM users
             WHERE role IN ('sales_person', 'supervisor', 'phone_technician')
               AND is_active = 0
               AND is_verified = 0
             ORDER BY created_at DESC"
        );
        $stmt->execute();
        $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'pending' => $pending,
        ]);
        exit;
    }

    if ($action === 'active') {
        // Get active staff members
        $stmt = $pdo->prepare(
            "SELECT 
                id,
                first_name,
                last_name,
                email,
                phone,
                role,
                is_active,
                created_at
             FROM users
             WHERE role IN ('sales_person', 'supervisor', 'phone_technician')
               AND is_verified = 1
             ORDER BY is_active DESC, created_at DESC"
        );
        $stmt->execute();
        $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'staff' => $staff,
        ]);
        exit;
    }

    if ($action === 'stats') {
        // Get staff statistics
        $stmt = $pdo->prepare(
            "SELECT 
                COUNT(*) AS total_staff,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_staff,
                SUM(CASE WHEN is_active = 0 AND is_verified = 0 THEN 1 ELSE 0 END) AS pending_staff,
                SUM(CASE WHEN role = 'sales_person' THEN 1 ELSE 0 END) AS sales_count,
                SUM(CASE WHEN role = 'supervisor' THEN 1 ELSE 0 END) AS supervisor_count,
                SUM(CASE WHEN role = 'phone_technician' THEN 1 ELSE 0 END) AS technician_count
             FROM users
             WHERE role IN ('sales_person', 'supervisor', 'phone_technician')"
        );
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'stats' => $stats,
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    // Register action doesn't need ids array
    if ($action === 'register') {
        // Owner directly registers a staff member (supervisor or sales_person only)
        $role       = trim($body['role'] ?? '');
        $firstName  = trim($body['first_name'] ?? '');
        $lastName   = trim($body['last_name'] ?? '');
        $email      = trim($body['email'] ?? '');
        $phone      = trim($body['phone'] ?? '');
        $password   = $body['password'] ?? '';

        // Validate required fields
        if (empty($role) || empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        // Only allow supervisor and sales_person
        if (!in_array($role, ['supervisor', 'sales_person'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid role. Only Supervisor and Sales Person can be registered directly.']);
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit;
        }

        // Validate password length
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            exit;
        }

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already registered.']);
            exit;
        }

        // Hash password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Insert new staff member (active and verified)
        $stmt = $pdo->prepare(
            "INSERT INTO users 
             (first_name, last_name, email, phone, password_hash, role, is_active, is_verified, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, 1, 1, NOW(), NOW())"
        );
        $stmt->execute([$firstName, $lastName, $email, $phone, $passwordHash, $role]);

        echo json_encode([
            'success' => true,
            'message' => 'Staff registered successfully.',
            'staff_id' => $pdo->lastInsertId()
        ]);
        exit;
    }

    // All other actions require ids array
    $ids = array_map('intval', $body['ids'] ?? []);

    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'No staff IDs provided.']);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    if ($action === 'approve') {
        // Approve staff applications
        $stmt = $pdo->prepare(
            "UPDATE users
             SET is_active = 1, is_verified = 1, updated_at = NOW()
             WHERE id IN ($placeholders)
               AND role IN ('sales_person', 'supervisor', 'phone_technician')
               AND is_active = 0"
        );
        $stmt->execute($ids);

        echo json_encode([
            'success' => true,
            'message' => 'Staff application(s) approved successfully.',
            'approved' => $stmt->rowCount()
        ]);
        exit;
    }

    if ($action === 'reject') {
        // Reject staff applications (delete the user)
        $stmt = $pdo->prepare(
            "DELETE FROM users
             WHERE id IN ($placeholders)
               AND role IN ('sales_person', 'supervisor', 'phone_technician')
               AND is_active = 0
               AND is_verified = 0"
        );
        $stmt->execute($ids);

        echo json_encode([
            'success' => true,
            'message' => 'Staff application(s) rejected.',
            'rejected' => $stmt->rowCount()
        ]);
        exit;
    }

    if ($action === 'deactivate') {
        // Deactivate staff members
        $stmt = $pdo->prepare(
            "UPDATE users
             SET is_active = 0, updated_at = NOW()
             WHERE id IN ($placeholders)
               AND role IN ('sales_person', 'supervisor', 'phone_technician')
               AND is_active = 1"
        );
        $stmt->execute($ids);

        echo json_encode([
            'success' => true,
            'message' => 'Staff member(s) deactivated.',
            'deactivated' => $stmt->rowCount()
        ]);
        exit;
    }

    if ($action === 'activate') {
        // Reactivate staff members
        $stmt = $pdo->prepare(
            "UPDATE users
             SET is_active = 1, updated_at = NOW()
             WHERE id IN ($placeholders)
               AND role IN ('sales_person', 'supervisor', 'phone_technician')
               AND is_verified = 1"
        );
        $stmt->execute($ids);

        echo json_encode([
            'success' => true,
            'message' => 'Staff member(s) reactivated.',
            'activated' => $stmt->rowCount()
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
