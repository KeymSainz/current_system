<?php
/**
 * Fix&Go — Technician Marketplace API
 * Allows phone_technician to browse supplier products and request them.
 *
 * GET  ?action=browse          → list available supplier products (qty>0, status verified/draft)
 * GET  ?action=suppliers       → list all active suppliers
 * GET  ?action=my_requests     → list this technician's supply requests
 * GET  ?action=request_stats   → stats for this technician's requests
 * POST action=request_product  → submit a product request to a supplier
 * POST action=cancel_request   → cancel a pending request
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (
    empty($_SESSION['user_id']) ||
    empty($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'phone_technician'
) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Technician access required.']);
    exit;
}

$techId = (int) $_SESSION['user_id'];
$pdo    = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method = $_SERVER['REQUEST_METHOD'];

/* ── helpers ─────────────────────────────────────────────────── */
function mktTableExists(PDO $pdo, string $table): bool {
    $r = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES
                        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
    $r->execute([$table]);
    return (bool) $r->fetchColumn();
}

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'browse';

    // ── Browse available supplier products ────────────────────
    // Shows all products currently displayed/for sale in the shop,
    // exactly what customers see — is_displayed=1 across all holders.
    if ($action === 'browse') {
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    sp.id,
                    sp.category,
                    COALESCE(sp.brand, '')                  AS brand,
                    sp.item_description                     AS name,
                    sp.qty,
                    sp.srp                                  AS price,
                    COALESCE(sp.image_path, '')             AS image_path,
                    sp.status,
                    sp.holder_type,
                    sp.current_holder_id,
                    sp.supplier_id,
                    CONCAT(sup.first_name, ' ', sup.last_name) AS supplier_name,
                    sup.email                               AS supplier_email,
                    COALESCE(holder.shop_name,
                        CONCAT(holder.first_name, ' ', holder.last_name)) AS seller_name,
                    holder.role                             AS seller_role
                 FROM supplier_products sp
                 JOIN users sup    ON sup.id    = sp.supplier_id
                 JOIN users holder ON holder.id = sp.current_holder_id
                 WHERE sp.is_displayed = 1
                   AND sp.qty > 0
                 ORDER BY sp.updated_at DESC"
            );
            $stmt->execute();
            echo json_encode(['success' => true, 'products' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            error_log('[mkt browse] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── List active suppliers ─────────────────────────────────
    if ($action === 'suppliers') {
        try {
            $stmt = $pdo->prepare(
                "SELECT id, first_name, last_name, email
                 FROM users
                 WHERE role = 'supplier' AND is_active = 1
                 ORDER BY first_name, last_name"
            );
            $stmt->execute();
            echo json_encode(['success' => true, 'suppliers' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            error_log('[mkt suppliers] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── My supply requests ────────────────────────────────────
    if ($action === 'my_requests') {
        if (!mktTableExists($pdo, 'technician_supply_requests')) {
            echo json_encode(['success' => true, 'requests' => []]);
            exit;
        }
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    tsr.id,
                    tsr.quantity_requested,
                    tsr.note,
                    tsr.status,
                    tsr.supplier_notes,
                    tsr.created_at,
                    tsr.updated_at,
                    sp.item_description             AS product_name,
                    sp.category                     AS product_category,
                    CONCAT(u.first_name, ' ', u.last_name) AS supplier_name
                 FROM technician_supply_requests tsr
                 JOIN supplier_products sp ON sp.id = tsr.product_id
                 JOIN users u ON u.id = tsr.supplier_id
                 WHERE tsr.technician_id = ?
                 ORDER BY tsr.created_at DESC"
            );
            $stmt->execute([$techId]);
            echo json_encode(['success' => true, 'requests' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            error_log('[mkt my_requests] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Request stats ─────────────────────────────────────────
    if ($action === 'request_stats') {
        if (!mktTableExists($pdo, 'technician_supply_requests')) {
            echo json_encode(['success' => true, 'stats' => ['total'=>0,'pending'=>0,'approved'=>0,'rejected'=>0,'fulfilled'=>0,'cancelled'=>0]]);
            exit;
        }
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    COUNT(*)                                                          AS total,
                    SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END)            AS pending,
                    SUM(CASE WHEN status = 'approved'  THEN 1 ELSE 0 END)            AS approved,
                    SUM(CASE WHEN status = 'rejected'  THEN 1 ELSE 0 END)            AS rejected,
                    SUM(CASE WHEN status = 'fulfilled' THEN 1 ELSE 0 END)            AS fulfilled,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END)            AS cancelled
                 FROM technician_supply_requests
                 WHERE technician_id = ?"
            );
            $stmt->execute([$techId]);
            echo json_encode(['success' => true, 'stats' => $stmt->fetch(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            error_log('[mkt request_stats] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
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

    // ── Request a product ─────────────────────────────────────
    if ($action === 'request_product') {
        $productId  = (int)($body['product_id']  ?? 0);
        $supplierId = (int)($body['supplier_id'] ?? 0);
        $quantity   = (int)($body['quantity']    ?? 0);
        $note       = trim($body['note'] ?? '');

        if (!$productId || !$supplierId || $quantity < 1) {
            echo json_encode(['success' => false, 'message' => 'Product, supplier, and quantity (≥1) are required.']);
            exit;
        }

        try {
            // Verify product exists, is available in shop, and has stock
            // supplier_id is the original supplier — used for the request target
            $check = $pdo->prepare(
                "SELECT id, supplier_id FROM supplier_products
                 WHERE id = ? AND status IN ('sent_to_sales_person','owner_received','verified') AND qty > 0"
            );
            $check->execute([$productId]);
            $prod = $check->fetch(PDO::FETCH_ASSOC);
            if (!$prod) {
                echo json_encode(['success' => false, 'message' => 'Product not found, not available, or out of stock.']);
                exit;
            }
            // Use the product's actual supplier_id if not provided
            if (!$supplierId) {
                $supplierId = (int)$prod['supplier_id'];
            }

            // Ensure table exists
            if (!mktTableExists($pdo, 'technician_supply_requests')) {
                echo json_encode(['success' => false, 'message' => 'Supply requests table not found. Please run the migration.']);
                exit;
            }

            $ins = $pdo->prepare(
                "INSERT INTO technician_supply_requests
                    (technician_id, product_id, supplier_id, quantity_requested, note, status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())"
            );
            $ins->execute([$techId, $productId, $supplierId, $quantity, $note ?: null]);
            $requestId = (int)$pdo->lastInsertId();

            // Notify supplier
            try {
                require_once __DIR__ . '/notification_helper.php';
                $techName = '';
                $techRow = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                $techRow->execute([$techId]);
                $tr = $techRow->fetch(PDO::FETCH_ASSOC);
                if ($tr) $techName = trim(($tr['first_name']??'') . ' ' . ($tr['last_name']??''));
                sendNotification(
                    $supplierId,
                    'supply_request',
                    'New Product Request',
                    "Technician {$techName} has requested {$quantity} unit(s) of a product. Request #{$requestId}."
                );
            } catch (Exception $ne) {
                error_log('[mkt request_product notify] ' . $ne->getMessage());
            }

            echo json_encode(['success' => true, 'message' => 'Request submitted successfully.', 'request_id' => $requestId]);
        } catch (Exception $e) {
            error_log('[mkt request_product] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Cancel a pending request ──────────────────────────────
    if ($action === 'cancel_request') {
        $requestId = (int)($body['request_id'] ?? 0);
        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID required.']);
            exit;
        }

        if (!mktTableExists($pdo, 'technician_supply_requests')) {
            echo json_encode(['success' => false, 'message' => 'Supply requests table not found.']);
            exit;
        }

        try {
            $del = $pdo->prepare(
                "DELETE FROM technician_supply_requests
                 WHERE id = ? AND technician_id = ? AND status = 'pending'"
            );
            $del->execute([$requestId, $techId]);
            if ($del->rowCount() === 0) {
                echo json_encode(['success' => false, 'message' => 'Request not found or cannot be cancelled (only pending requests can be cancelled).']);
                exit;
            }
            echo json_encode(['success' => true, 'message' => 'Request cancelled.']);
        } catch (Exception $e) {
            error_log('[mkt cancel_request] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
