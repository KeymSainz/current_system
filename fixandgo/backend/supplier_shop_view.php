<?php
/**
 * Fix&Go — Supplier Shop View API
 * Returns:
 *   - all products currently on sale in the shop (owner_received)
 *   - the logged-in supplier's own products (all statuses)
 *
 * GET ?action=shop_products   → all owner_received products
 * GET ?action=my_products     → supplier's own products
 * GET ?action=both            → both sets combined
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'supplier') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$supplierId = (int) $_SESSION['user_id'];
$pdo        = require __DIR__ . '/db.php';
$action     = $_GET['action'] ?? 'both';

function resolveImg(?string $path): ?string {
    if (!$path) return null;
    if (str_starts_with($path, 'http')) return $path;
    return $path; // local path — frontend prefixes base URL
}

switch ($action) {

    // ── All products currently in the shop ────────────────────
    case 'shop_products':
        $stmt = $pdo->query(
            "SELECT sp.id, sp.category, sp.brand, sp.item_description,
                    sp.qty, sp.srp, sp.image_path, sp.notes,
                    CONCAT(u.first_name,' ',u.last_name) AS supplier_name
             FROM supplier_products sp
             JOIN users u ON u.id = sp.supplier_id
             WHERE sp.status = 'owner_received'
             ORDER BY sp.category ASC, sp.item_description ASC"
        );
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($products as &$p) { $p['image_path'] = resolveImg($p['image_path']); }
        unset($p);
        echo json_encode(['success' => true, 'products' => $products, 'total' => count($products)]);
        break;

    // ── Supplier's own products ───────────────────────────────
    case 'my_products':
        $stmt = $pdo->prepare(
            "SELECT id, category, brand, item_description,
                    qty, srp, image_path, status, notes, created_at
             FROM supplier_products
             WHERE supplier_id = ?
             ORDER BY created_at DESC"
        );
        $stmt->execute([$supplierId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($products as &$p) { $p['image_path'] = resolveImg($p['image_path']); }
        unset($p);
        echo json_encode(['success' => true, 'products' => $products, 'total' => count($products)]);
        break;

    // ── Both ──────────────────────────────────────────────────
    case 'both':
    default:
        // All shop products
        $shopStmt = $pdo->query(
            "SELECT sp.id, sp.category, sp.brand, sp.item_description,
                    sp.qty, sp.srp, sp.image_path, sp.notes,
                    CONCAT(u.first_name,' ',u.last_name) AS supplier_name
             FROM supplier_products sp
             JOIN users u ON u.id = sp.supplier_id
             WHERE sp.status = 'owner_received'
             ORDER BY sp.category ASC, sp.item_description ASC"
        );
        $shopProducts = $shopStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($shopProducts as &$p) { $p['image_path'] = resolveImg($p['image_path']); }
        unset($p);

        // Supplier's own products
        $myStmt = $pdo->prepare(
            "SELECT id, category, brand, item_description,
                    qty, srp, image_path, status, notes, created_at
             FROM supplier_products
             WHERE supplier_id = ?
             ORDER BY created_at DESC"
        );
        $myStmt->execute([$supplierId]);
        $myProducts = $myStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($myProducts as &$p) { $p['image_path'] = resolveImg($p['image_path']); }
        unset($p);

        echo json_encode([
            'success'       => true,
            'shop_products' => $shopProducts,
            'my_products'   => $myProducts,
            'shop_total'    => count($shopProducts),
            'my_total'      => count($myProducts),
        ]);
        break;
}
