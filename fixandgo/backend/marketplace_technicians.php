<?php
/**
 * Marketplace Technicians API
 * Returns all sales persons (technicians) with their profiles for the marketplace view
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
    // Get all sales persons and suppliers with their profile information
    // Also pull from technician_profiles if available
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.first_name,
            u.last_name,
            u.email,
            u.role,
            COALESCE(u.profile_image, u.avatar_url) as profile_image,
            COALESCE(u.bio, tp.bio) as bio,
            COALESCE(u.specializations, tp.specialization) as specializations,
            COALESCE(u.shop_name, CONCAT(u.first_name, ' ', u.last_name)) as shop_name,
            u.phone,
            u.is_active,
            COALESCE(tp.experience_years, 0) as experience_years,
            COALESCE(tp.rating_avg, 0.0) as rating_avg,
            COALESCE(tp.rating_count, 0) as rating_count,
            (SELECT COUNT(*) FROM sales_products sp WHERE sp.sales_person_id = u.id AND sp.is_active = 1) as services_count,
            (SELECT COUNT(*) FROM supplier_products p WHERE p.supplier_id = u.id AND p.status = 'owner_received' AND p.qty > 0) as products_count,
            CASE 
                WHEN u.role = 'sales_person' THEN 'Technician / Sales'
                WHEN u.role = 'supplier'     THEN 'Supplier'
                ELSE u.role
            END as role_label
        FROM users u
        LEFT JOIN technician_profiles tp ON tp.user_id = u.id
        WHERE u.role IN ('sales_person', 'supplier') 
          AND u.is_active = 1
        ORDER BY tp.rating_avg DESC, u.created_at DESC
    ");
    
    $stmt->execute();
    $technicians = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'technicians' => $technicians,
        'count' => count($technicians)
    ]);
    
} catch (PDOException $e) {
    error_log("Marketplace technicians error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
