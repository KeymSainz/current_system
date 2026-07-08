<?php
/**
 * Fix&Go — Customer Orders API
 *
 * GET  ?action=list              → list this customer's orders
 * GET  ?action=detail&id=X      → single order detail
 * GET  ?action=product&id=X     → product detail + reviews + shop info
 * POST action=place              → place a new order
 * POST action=cancel             → cancel a pending order
 * POST action=review             → submit a product review
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

// Allow public access for product detail (no auth needed)
$action = $_GET['action'] ?? ($_POST['action'] ?? (json_decode(file_get_contents('php://input'), true)['action'] ?? ''));

// Product detail is public
if ($action !== 'product') {
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Customer login required.']);
        exit;
    }
}

$customerId = (int)($_SESSION['user_id'] ?? 0);
$pdo        = require __DIR__ . '/db.php';
$method     = $_SERVER['REQUEST_METHOD'];

// ── GET actions ───────────────────────────────────────────────
if ($method === 'GET') {

    // ── Product detail + reviews + shop info ──────────────────
    if ($action === 'product') {
        $productId = (int)($_GET['id'] ?? 0);
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID required.']);
            exit;
        }

        // Product info + seller (sales person) + shop
        $stmt = $pdo->prepare(
            "SELECT
                sp.id,
                sp.category,
                sp.brand,
                sp.item_description,
                sp.qty,
                sp.srp,
                sp.image_path,
                sp.notes,
                sp.status,
                sp.is_displayed,
                -- Sales person info
                u.first_name AS seller_first,
                u.last_name  AS seller_last,
                -- Shop info (if owner has a shop)
                s.id         AS shop_id,
                s.name       AS shop_name,
                s.description AS shop_description,
                s.city       AS shop_city,
                s.phone      AS shop_phone,
                s.email      AS shop_email
             FROM supplier_products sp
             LEFT JOIN users u ON u.id = sp.current_holder_id AND u.role = 'sales_person'
             LEFT JOIN shops s ON s.is_active = 1
             WHERE sp.id = ?
             LIMIT 1"
        );
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            exit;
        }

        // Reviews
        $rStmt = $pdo->prepare(
            "SELECT
                pr.id,
                pr.rating,
                pr.review_text,
                pr.created_at,
                CONCAT(u.first_name, ' ', LEFT(u.last_name, 1), '.') AS reviewer_name
             FROM product_reviews pr
             JOIN users u ON u.id = pr.customer_id
             WHERE pr.product_id = ?
             ORDER BY pr.created_at DESC
             LIMIT 20"
        );
        $rStmt->execute([$productId]);
        $reviews = $rStmt->fetchAll(PDO::FETCH_ASSOC);

        // Average rating
        $avgStmt = $pdo->prepare(
            "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews
             FROM product_reviews WHERE product_id = ?"
        );
        $avgStmt->execute([$productId]);
        $ratingInfo = $avgStmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success'       => true,
            'product'       => $product,
            'reviews'       => $reviews,
            'avg_rating'    => round((float)($ratingInfo['avg_rating'] ?? 0), 1),
            'total_reviews' => (int)($ratingInfo['total_reviews'] ?? 0),
        ]);
        exit;
    }

    // ── List orders ───────────────────────────────────────────
    if ($action === 'list') {
        $status = $_GET['status'] ?? 'all';

        // Use a safe minimal query first — extra columns added with COALESCE
        $sql = "SELECT
                    co.id,
                    co.quantity,
                    co.unit_price,
                    co.total_amount,
                    co.status,
                    co.payment_method,
                    co.created_at,
                    co.product_id,
                    sp.item_description AS product_name,
                    sp.category,
                    sp.brand,
                    sp.image_path,
                    sp.current_holder_id AS sales_person_id,
                    CONCAT(seller.first_name,' ',seller.last_name) AS seller_name,
                    COALESCE(seller.first_name,'Fix&Go') AS seller_shop_name
                FROM customer_orders co
                JOIN supplier_products sp ON sp.id = co.product_id
                LEFT JOIN users seller ON seller.id = sp.current_holder_id
                LEFT JOIN users cu ON cu.id = co.customer_id
                WHERE co.customer_id = ?";
        $params = [$customerId];

        if ($status !== 'all') {
            $sql    .= " AND co.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY co.created_at DESC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'orders' => $orders]);
        } catch (\Exception $e) {
            error_log('[customer_orders list] ' . $e->getMessage());
            echo json_encode(['success' => true, 'orders' => []]);
        }
        exit;
    }

    // ── Single order detail ───────────────────────────────────
    if ($action === 'detail') {
        $orderId = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare(
            "SELECT co.*, sp.item_description AS product_name, sp.category, sp.brand, sp.image_path
             FROM customer_orders co
             JOIN supplier_products sp ON sp.id = co.product_id
             WHERE co.id = ? AND co.customer_id = ?"
        );
        $stmt->execute([$orderId, $customerId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found.']);
            exit;
        }
        echo json_encode(['success' => true, 'order' => $order]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── POST actions ──────────────────────────────────────────────
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $postAction = $body['action'] ?? '';

    // ── Place order ───────────────────────────────────────────
    if ($postAction === 'place') {
        $productId     = (int)($body['product_id'] ?? 0);
        $quantity      = max(1, (int)($body['quantity'] ?? 1));
        $paymentMethod = in_array($body['payment_method'] ?? '', ['cod','gcash','card'])
                         ? $body['payment_method'] : 'cod';
        $notes         = trim($body['notes'] ?? '');

        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID required.']);
            exit;
        }

        // Fetch product & check stock
        $stmt = $pdo->prepare(
            "SELECT id, item_description, srp, qty, status, is_displayed
             FROM supplier_products WHERE id = ? FOR UPDATE"
        );
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            exit;
        }
        if ($product['status'] !== 'sent_to_sales_person' || !$product['is_displayed']) {
            echo json_encode(['success' => false, 'message' => 'Product is not available for purchase.']);
            exit;
        }
        if ($product['qty'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock. Only ' . $product['qty'] . ' left.']);
            exit;
        }

        $unitPrice   = (float)$product['srp'];
        $totalAmount = $unitPrice * $quantity;

        $pdo->beginTransaction();
        try {
            // Insert order
            $ins = $pdo->prepare(
                "INSERT INTO customer_orders
                    (customer_id, product_id, quantity, unit_price, total_amount, status, payment_method, notes)
                 VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)"
            );
            $ins->execute([$customerId, $productId, $quantity, $unitPrice, $totalAmount, $paymentMethod, $notes]);
            $orderId = $pdo->lastInsertId();

            // Deduct stock
            $pdo->prepare("UPDATE supplier_products SET qty = qty - ? WHERE id = ?")
                ->execute([$quantity, $productId]);

            $pdo->commit();

            // ── Notify the sales person ───────────────────────
            try {
                require_once __DIR__ . '/notification_helper.php';
                // Get customer name
                $custStmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                $custStmt->execute([$customerId]);
                $cust = $custStmt->fetch(PDO::FETCH_ASSOC);
                $custName = trim(($cust['first_name'] ?? '') . ' ' . ($cust['last_name'] ?? '')) ?: 'A customer';

                // Get sales person (current_holder_id)
                $spStmt = $pdo->prepare("SELECT current_holder_id FROM supplier_products WHERE id = ?");
                $spStmt->execute([$productId]);
                $spRow = $spStmt->fetch(PDO::FETCH_ASSOC);
                $salesPersonId = $spRow['current_holder_id'] ?? null;

                if ($salesPersonId) {
                    sendNotification(
                        $salesPersonId,
                        'new_order',
                        'New Order Received',
                        "{$custName} ordered {$quantity}x {$product['item_description']} (Order #{$orderId}) for ₱" . number_format($totalAmount, 2) . "."
                    );
                }

                // Notify customer their order was placed
                sendNotification(
                    $customerId,
                    'order_placed',
                    'Order Placed Successfully',
                    "Your order #{$orderId} for {$quantity}x {$product['item_description']} (₱" . number_format($totalAmount, 2) . ") has been placed. We'll notify you when it ships."
                );
            } catch (Exception $ne) {
                error_log('[customer_orders notify] ' . $ne->getMessage());
            }

            // ── Auto-create conversation + order message ──────
            try {
                if (empty($salesPersonId)) {
                    $spStmt2 = $pdo->prepare("SELECT current_holder_id FROM supplier_products WHERE id = ?");
                    $spStmt2->execute([$productId]);
                    $spRow2 = $spStmt2->fetch(PDO::FETCH_ASSOC);
                    $salesPersonId = $spRow2['current_holder_id'] ?? null;
                }
                if ($salesPersonId && $salesPersonId != $customerId) {
                    $a = min((int)$customerId, (int)$salesPersonId);
                    $b = max((int)$customerId, (int)$salesPersonId);
                    $convCheck = $pdo->prepare("SELECT id FROM conversations WHERE user_a_id = ? AND user_b_id = ?");
                    $convCheck->execute([$a, $b]);
                    $conv = $convCheck->fetch(PDO::FETCH_ASSOC);
                    if (!$conv) {
                        $pdo->prepare("INSERT INTO conversations (user_a_id, user_b_id) VALUES (?, ?)")->execute([$a, $b]);
                        $autoConvId = (int) $pdo->lastInsertId();
                    } else {
                        $autoConvId = (int) $conv['id'];
                    }
                    $autoMsg = "🛒 New Order #" . $orderId . "\n"
                             . "Product: " . $product['item_description'] . "\n"
                             . "Quantity: " . $quantity . "x\n"
                             . "Total: ₱" . number_format($totalAmount, 2) . "\n"
                             . "Payment: " . strtoupper($paymentMethod)
                             . ($notes ? "\nNotes: " . $notes : "")
                             . "\n\nPlease let me know when my order will be processed. Thank you!";
                    try {
                        $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, body) VALUES (?, ?, ?)")
                            ->execute([$autoConvId, $customerId, $autoMsg]);
                    } catch (Exception $colEx) {
                        $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, body, sent_at) VALUES (?, ?, ?, NOW())")
                            ->execute([$autoConvId, $customerId, $autoMsg]);
                    }
                    $pdo->prepare("UPDATE conversations SET updated_at = NOW() WHERE id = ?")
                        ->execute([$autoConvId]);
                }
            } catch (Exception $me) {
                error_log('[customer_orders auto-message] ' . $me->getMessage());
            }

            echo json_encode([
                'success'  => true,
                'message'  => 'Order placed successfully!',
                'order_id' => $orderId,
            ]);
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('[customer_orders place] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to place order. Please try again.']);
        }
        exit;
    }

    // ── Cancel order ──────────────────────────────────────────
    if ($postAction === 'cancel') {
        $orderId = (int)($body['order_id'] ?? 0);
        $reason  = trim($body['reason'] ?? '');
        $notes   = trim($body['notes']  ?? '');

        $stmt = $pdo->prepare(
            "SELECT id, product_id, quantity, status FROM customer_orders
             WHERE id = ? AND customer_id = ?"
        );
        $stmt->execute([$orderId, $customerId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found.']);
            exit;
        }
        if ($order['status'] !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Only pending orders can be cancelled.']);
            exit;
        }

        $pdo->beginTransaction();
        try {
            $pdo->prepare(
                "UPDATE customer_orders
                 SET status = 'cancelled', cancel_reason = ?, cancel_notes = ?, updated_at = NOW()
                 WHERE id = ?"
            )->execute([$reason ?: null, $notes ?: null, $orderId]);

            // Restore stock
            $pdo->prepare("UPDATE supplier_products SET qty = qty + ? WHERE id = ?")
                ->execute([$order['quantity'], $order['product_id']]);

            $pdo->commit();
            // Notify sales person of cancellation
            try {
                require_once __DIR__ . '/notification_helper.php';
                $spStmt = $pdo->prepare(
                    "SELECT sp.current_holder_id, sp.item_description,
                            u.first_name, u.last_name
                     FROM supplier_products sp
                     JOIN customer_orders co ON co.product_id = sp.id
                     JOIN users u ON u.id = co.customer_id
                     WHERE co.id = ?"
                );
                $spStmt->execute([$orderId]);
                $row = $spStmt->fetch(PDO::FETCH_ASSOC);
                if ($row && $row['current_holder_id']) {
                    $custName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: 'A customer';
                    sendNotification(
                        $row['current_holder_id'],
                        'order_cancelled',
                        'Order Cancelled',
                        "{$custName} cancelled Order #{$orderId} ({$row['item_description']})" . ($reason ? ". Reason: {$reason}" : '.')
                    );
                }
            } catch (Exception $ne) { error_log('[customer_orders cancel notify] ' . $ne->getMessage()); }

            echo json_encode(['success' => true, 'message' => 'Order cancelled.']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Failed to cancel order.']);
        }
        exit;
    }

    // ── Submit review ─────────────────────────────────────────
    if ($postAction === 'review') {
        $productId  = (int)($body['product_id'] ?? 0);
        $orderId    = (int)($body['order_id'] ?? 0) ?: null;
        $rating     = max(1, min(5, (int)($body['rating'] ?? 5)));
        $reviewText = trim($body['review_text'] ?? '');

        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID required.']);
            exit;
        }

        // Check if already reviewed this product
        $check = $pdo->prepare(
            "SELECT id FROM product_reviews WHERE product_id = ? AND customer_id = ?"
        );
        $check->execute([$productId, $customerId]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'You have already reviewed this product.']);
            exit;
        }

        $pdo->prepare(
            "INSERT INTO product_reviews (product_id, customer_id, order_id, rating, review_text)
             VALUES (?, ?, ?, ?, ?)"
        )->execute([$productId, $customerId, $orderId, $rating, $reviewText]);

        echo json_encode(['success' => true, 'message' => 'Review submitted. Thank you!']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
