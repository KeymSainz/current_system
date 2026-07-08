<?php
/**
 * Fix&Go — Sales Person Inventory API
 *
 * GET  ?action=list          → all products sent to sales person (inventory)
 * GET  ?action=displayed     → only products marked for display (manage products)
 * GET  ?action=stats         → inventory statistics
 * POST action=toggle_display → toggle is_displayed for a product
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (
    empty($_SESSION['user_id']) ||
    empty($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'sales_person'
) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$pdo    = require __DIR__ . '/db.php';
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    // All products in the sales person's inventory
    if ($action === 'list') {
        $salesPersonId = (int) $_SESSION['user_id'];
        
        $stmt = $pdo->prepare(
            "SELECT
                sp.id,
                sp.category,
                sp.brand,
                sp.item_description AS name,
                sp.qty              AS quantity,
                sp.srp              AS price,
                sp.image_path,
                sp.status,
                sp.is_displayed,
                sp.updated_at       AS last_updated
             FROM supplier_products sp
             WHERE sp.current_holder_id = ? AND sp.holder_type = 'sales_person'
             ORDER BY sp.updated_at DESC"
        );
        $stmt->execute([$salesPersonId]);
        
        // Debug logging
        error_log("Sales Person ID: " . $salesPersonId);
        error_log("Products found: " . $stmt->rowCount());
        
        echo json_encode(['success' => true, 'items' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    // Only products the sales person chose to display to customers
    if ($action === 'displayed') {
        $salesPersonId = (int) $_SESSION['user_id'];
        
        $stmt = $pdo->prepare(
            "SELECT
                sp.id,
                sp.category,
                sp.brand,
                sp.item_description AS name,
                sp.qty              AS quantity,
                sp.srp              AS price,
                sp.image_path,
                sp.is_displayed,
                sp.updated_at       AS last_updated
             FROM supplier_products sp
             WHERE sp.current_holder_id = ? 
               AND sp.holder_type = 'sales_person'
               AND sp.is_displayed = 1
             ORDER BY sp.updated_at DESC"
        );
        $stmt->execute([$salesPersonId]);
        echo json_encode(['success' => true, 'items' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    if ($action === 'stats') {
        $salesPersonId = (int) $_SESSION['user_id'];
        
        $stmt = $pdo->prepare(
            "SELECT
                COUNT(*)                                                    AS total_items,
                SUM(CASE WHEN qty > 0  THEN 1 ELSE 0 END)                  AS in_stock,
                SUM(CASE WHEN qty > 0 AND qty < 10 THEN 1 ELSE 0 END)      AS low_stock,
                SUM(CASE WHEN qty = 0  THEN 1 ELSE 0 END)                  AS out_of_stock,
                SUM(CASE WHEN is_displayed = 1 THEN 1 ELSE 0 END)          AS displayed
             FROM supplier_products
             WHERE current_holder_id = ? AND holder_type = 'sales_person'"
        );
        $stmt->execute([$salesPersonId]);
        echo json_encode(['success' => true, 'stats' => $stmt->fetch(PDO::FETCH_ASSOC)]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── POST ──────────────────────────────────────────────────────
if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    // Toggle whether a product is displayed to customers
    if ($action === 'toggle_display') {
        $salesPersonId = (int) $_SESSION['user_id'];
        $productId = intval($body['product_id'] ?? 0);
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID required.']);
            exit;
        }

        // Verify the product is actually in this sales person's inventory
        $stmt = $pdo->prepare(
            "SELECT id, is_displayed FROM supplier_products
             WHERE id = ? AND current_holder_id = ? AND holder_type = 'sales_person'"
        );
        $stmt->execute([$productId, $salesPersonId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found in your inventory.']);
            exit;
        }

        $newVal = $product['is_displayed'] ? 0 : 1;
        // When displaying a product, also set status to sent_to_sales_person so it appears in shop
        $newStatus = $newVal ? 'sent_to_sales_person' : 'verified';
        $pdo->prepare(
            "UPDATE supplier_products SET is_displayed = ?, status = ?, updated_at = NOW() WHERE id = ?"
        )->execute([$newVal, $newStatus, $productId]);

        echo json_encode([
            'success'      => true,
            'is_displayed' => $newVal,
            'message'      => $newVal ? 'Product is now displayed to customers.' : 'Product removed from display.',
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
