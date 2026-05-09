<?php
/**
 * Simple Test - Check what's happening
 */

// Start output buffering to catch any errors
ob_start();

require_once __DIR__ . '/helpers.php';
startSecureSession();

// Clear any previous output
ob_end_clean();

// Set headers
header('Content-Type: application/json');
header('Cache-Control: no-store');

// Check session
if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in', 'session' => $_SESSION]);
    exit;
}

try {
    $pdo = require __DIR__ . '/db.php';
    
    $userId = $_SESSION['user_id'];
    $userRole = $_SESSION['user_role'] ?? 'unknown';
    
    // Get user info
    $stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get products
    $stmt = $pdo->prepare("
        SELECT id, category, brand, item_description, qty, current_holder_id, holder_type
        FROM supplier_products
        WHERE current_holder_id = ?
        LIMIT 3
    ");
    $stmt->execute([$userId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get supervisors
    $stmt = $pdo->query("
        SELECT id, first_name, last_name, email, is_active, is_verified
        FROM users
        WHERE role = 'supervisor'
          AND is_active = 1
          AND is_verified = 1
    ");
    $supervisors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Test successful',
        'user' => $user,
        'products_count' => count($products),
        'products' => $products,
        'supervisors_count' => count($supervisors),
        'supervisors' => $supervisors
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
