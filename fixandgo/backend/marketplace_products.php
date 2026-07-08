<?php
/**
 * Marketplace Products API
 * Returns all available products from suppliers for the marketplace view
 */

session_start();
header('Content-Type: application/json');

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    // Get all available products with supplier information
    // Statuses: owner_received = accepted by owner, sent_to_sales_person = in shop
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.item_description,
            p.category,
            p.brand,
            p.srp,
            p.qty,
            p.image_path,
            p.status,
            CONCAT(u.first_name, ' ', u.last_name) as supplier_name,
            u.id as supplier_id,
            COALESCE(u.shop_name, CONCAT(u.first_name, ' ', u.last_name)) as supplier_shop
        FROM supplier_products p
        INNER JOIN users u ON p.supplier_id = u.id
        WHERE p.status IN ('owner_received','sent_to_sales_person','verified')
          AND p.qty > 0
        ORDER BY p.created_at DESC
    ");
    
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products)
    ]);
    
} catch (PDOException $e) {
    error_log("Marketplace products error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
