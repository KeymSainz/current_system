<?php
/**
 * Fix&Go — Public Shop Products API
 *
 * Returns products set to display by the sales person (is_displayed = 1,
 * status = 'sent_to_sales_person') as the primary shop inventory.
 * Falls back to owner_received products if none are displayed.
 *
 * PUBLIC endpoint — no auth required (landing page).
 *
 * GET  ?action=all      → shops with their products bundled
 * GET  ?action=shops    → list shops only
 * GET  ?action=products&shop_id=N  → products for a specific shop
 */

header('Content-Type: application/json');
header('Cache-Control: no-store');
error_reporting(0);

try {
    $pdo    = require __DIR__ . '/db.php';
    $action = $_GET['action'] ?? 'all';

    function resolveImagePath(?string $path): ?string {
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return $path;
    }

    /**
     * Fetch products set to display by the sales person.
     * These are supplier_products with status='sent_to_sales_person' AND is_displayed=1.
     * Falls back to owner_received products if none are displayed.
     */
    function fetchDisplayedProducts(PDO $pdo): array {
        // Primary: products the sales person chose to display
        $stmt = $pdo->query(
            "SELECT
                sp.id,
                sp.category,
                sp.brand,
                sp.item_description,
                sp.qty,
                sp.srp,
                sp.image_path,
                sp.notes,
                'sales_display' AS source
             FROM supplier_products sp
             WHERE sp.status = 'sent_to_sales_person'
               AND sp.is_displayed = 1
               AND sp.qty > 0
             ORDER BY sp.category ASC, sp.item_description ASC"
        );
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fallback: owner_received products if sales person hasn't set any
        if (empty($products)) {
            $stmt = $pdo->query(
                "SELECT
                    sp.id,
                    sp.category,
                    sp.brand,
                    sp.item_description,
                    sp.qty,
                    sp.srp,
                    sp.image_path,
                    sp.notes,
                    'owner_stock' AS source
                 FROM supplier_products sp
                 WHERE sp.status = 'owner_received'
                 ORDER BY sp.category ASC, sp.item_description ASC"
            );
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        foreach ($products as &$p) {
            $p['image_path'] = resolveImagePath($p['image_path']);
        }
        unset($p);

        return $products;
    }

    switch ($action) {

        // ── All shops with bundled products ──────────────────
        case 'all':
            $shopStmt = $pdo->query(
                "SELECT s.id, s.name, s.description, s.address, s.city,
                        s.phone, s.email, s.logo_url,
                        CONCAT(u.first_name, ' ', u.last_name) AS owner_name
                 FROM shops s
                 JOIN users u ON u.id = s.owner_id AND u.role = 'owner'
                 WHERE s.is_active = 1
                 ORDER BY s.created_at ASC"
            );
            $shops = $shopStmt->fetchAll(PDO::FETCH_ASSOC);

            $allProducts = fetchDisplayedProducts($pdo);

            foreach ($shops as &$shop) {
                $shop['products'] = $allProducts;
            }
            unset($shop);

            echo json_encode([
                'success'  => true,
                'shops'    => $shops,
                'products' => $allProducts,
            ]);
            break;

        // ── List shops only ───────────────────────────────────
        case 'shops':
            $stmt = $pdo->query(
                "SELECT s.id, s.name, s.description, s.address, s.city,
                        s.phone, s.email, s.logo_url,
                        CONCAT(u.first_name, ' ', u.last_name) AS owner_name
                 FROM shops s
                 JOIN users u ON u.id = s.owner_id AND u.role = 'owner'
                 WHERE s.is_active = 1
                 ORDER BY s.created_at ASC"
            );
            echo json_encode(['success' => true, 'shops' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;

        // ── Products for a specific shop ──────────────────────
        case 'products':
            $shopId = (int)($_GET['shop_id'] ?? 0);
            if (!$shopId) {
                echo json_encode(['success' => false, 'message' => 'shop_id required.']);
                break;
            }
            $products = fetchDisplayedProducts($pdo);
            echo json_encode(['success' => true, 'products' => $products]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
