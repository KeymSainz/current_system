<?php
/**
 * Fix&Go — Notifications API
 * Handles user notifications (fetch, mark as read, delete)
 *
 * GET  ?action=list      → list all notifications for logged-in user
 * GET  ?action=unread    → count unread notifications
 * POST ?action=mark_read → mark notification(s) as read
 * POST ?action=delete    → delete notification(s)
 */

session_start();
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Check if user is logged in
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? '';

// Supervisors should not access notifications (per requirements)
if ($userRole === 'supervisor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Supervisors do not have access to notifications.']);
    exit;
}

$pdo = require __DIR__ . '/db.php';
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    // =====================
    // GET REQUESTS
    // =====================
    if ($method === 'GET') {
        
        if ($action === 'list') {
            // Fetch all notifications for the user, ordered by newest first
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            
            $stmt = $pdo->prepare(
                "SELECT id, type, title, body, is_read, created_at
                 FROM notifications
                 WHERE user_id = ?
                 ORDER BY created_at DESC
                 LIMIT ? OFFSET ?"
            );
            $stmt->execute([$userId, $limit, $offset]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = (int) $stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
            exit;
        }
        
        if ($action === 'unread') {
            // Count unread notifications
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0"
            );
            $stmt->execute([$userId]);
            $unreadCount = (int) $stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'unread_count' => $unreadCount
            ]);
            exit;
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid action for GET request.']);
        exit;
    }
    
    // =====================
    // POST REQUESTS
    // =====================
    if ($method === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        
        if ($action === 'mark_read') {
            // Mark notification(s) as read
            $ids = $body['ids'] ?? [];
            
            if (empty($ids) || !is_array($ids)) {
                echo json_encode(['success' => false, 'message' => 'No notification IDs provided.']);
                exit;
            }
            
            $ids = array_map('intval', $ids);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            
            $stmt = $pdo->prepare(
                "UPDATE notifications
                 SET is_read = 1
                 WHERE id IN ($placeholders) AND user_id = ?"
            );
            $stmt->execute([...$ids, $userId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Notification(s) marked as read.',
                'affected' => $stmt->rowCount()
            ]);
            exit;
        }
        
        if ($action === 'mark_all_read') {
            // Mark all notifications as read for this user
            $stmt = $pdo->prepare(
                "UPDATE notifications
                 SET is_read = 1
                 WHERE user_id = ? AND is_read = 0"
            );
            $stmt->execute([$userId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'All notifications marked as read.',
                'affected' => $stmt->rowCount()
            ]);
            exit;
        }
        
        if ($action === 'delete') {
            // Delete notification(s)
            $ids = $body['ids'] ?? [];
            
            if (empty($ids) || !is_array($ids)) {
                echo json_encode(['success' => false, 'message' => 'No notification IDs provided.']);
                exit;
            }
            
            $ids = array_map('intval', $ids);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            
            $stmt = $pdo->prepare(
                "DELETE FROM notifications
                 WHERE id IN ($placeholders) AND user_id = ?"
            );
            $stmt->execute([...$ids, $userId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Notification(s) deleted.',
                'affected' => $stmt->rowCount()
            ]);
            exit;
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid action for POST request.']);
        exit;
    }
    
    // Unsupported method
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
