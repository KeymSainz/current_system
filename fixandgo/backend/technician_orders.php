<?php
/**
 * Fix&Go — Technician Orders API
 *
 * GET  ?action=shops          → list suppliers + owners with their available products
 * GET  ?action=my_orders      → technician's order history
 * GET  ?action=order_stats    → counts by status
 * POST action=place_cod       → place a COD order (no PayMongo)
 * POST action=create_checkout → create PayMongo checkout (gcash/card)
 * POST action=cancel_order    → cancel a pending order
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
    echo json_encode(['success' => false, 'message' => 'Technician access required.']);
    exit;
}

$techId = (int) $_SESSION['user_id'];
$pdo    = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ───────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'shops';

    // ── List shops (suppliers + owners) with products ─────────
    if ($action === 'shops') {
        try {
            // Get suppliers with their available products
            $stmt = $pdo->query(
                "SELECT
                    u.id                                        AS seller_id,
                    'supplier'                                  AS seller_role,
                    CONCAT(u.first_name,' ',u.last_name)        AS seller_name,
                    COALESCE(u.shop_name, CONCAT(u.first_name,' ',u.last_name)) AS shop_name,
                    u.email                                     AS seller_email,
                    u.phone                                     AS seller_phone,
                    COALESCE(u.city,'')                         AS shop_city,
                    COALESCE(u.province,'')                     AS shop_province,
                    NULL                                        AS shop_address,
                    NULL                                        AS shop_logo
                 FROM users u
                 WHERE u.role = 'supplier' AND u.is_active = 1
                   AND EXISTS (
                     SELECT 1 FROM supplier_products sp
                     WHERE sp.supplier_id = u.id
                       AND sp.status IN ('sent_to_sales_person','owner_received','verified')
                       AND sp.qty > 0
                   )
                 ORDER BY u.first_name"
            );
            $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get owners with their shops and products
            $ownerStmt = $pdo->query(
                "SELECT
                    u.id                                        AS seller_id,
                    'owner'                                     AS seller_role,
                    CONCAT(u.first_name,' ',u.last_name)        AS seller_name,
                    COALESCE(s.name, u.shop_name,
                        CONCAT(u.first_name,' ',u.last_name))   AS shop_name,
                    COALESCE(s.email, u.email)                  AS seller_email,
                    COALESCE(s.phone, u.phone)                  AS seller_phone,
                    COALESCE(s.city, u.city,'')                 AS shop_city,
                    ''                                          AS shop_province,
                    COALESCE(s.address,'')                      AS shop_address,
                    COALESCE(s.logo_url,'')                     AS shop_logo
                 FROM users u
                 LEFT JOIN shops s ON s.owner_id = u.id AND s.is_active = 1
                 WHERE u.role = 'owner' AND u.is_active = 1
                   AND EXISTS (
                     SELECT 1 FROM supplier_products sp
                     WHERE sp.supplier_id = u.id
                       AND sp.status IN ('sent_to_sales_person','owner_received','verified')
                       AND sp.qty > 0
                   )
                 ORDER BY u.first_name"
            );
            $owners = $ownerStmt->fetchAll(PDO::FETCH_ASSOC);

            $allSellers = array_merge($sellers, $owners);

            // Fetch products for each seller
            foreach ($allSellers as &$seller) {
                $pStmt = $pdo->prepare(
                    "SELECT id, category, brand, item_description AS name,
                            qty, srp AS price, image_path, supplier_id
                     FROM supplier_products
                     WHERE supplier_id = ?
                       AND status IN ('sent_to_sales_person','owner_received','verified')
                       AND qty > 0
                     ORDER BY category, item_description"
                );
                $pStmt->execute([$seller['seller_id']]);
                $seller['products'] = $pStmt->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($seller);

            // Filter out sellers with no products
            $allSellers = array_values(array_filter($allSellers, fn($s) => !empty($s['products'])));

            echo json_encode(['success' => true, 'shops' => $allSellers]);
        } catch (Exception $e) {
            error_log('[tech_orders shops] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── My orders ─────────────────────────────────────────────
    if ($action === 'my_orders') {
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    o.id, o.seller_role, o.fulfillment_type, o.delivery_address,
                    o.payment_method, o.payment_status, o.order_status,
                    o.subtotal, o.shipping_fee, o.total_amount,
                    o.reference, o.notes, o.seller_notes,
                    o.created_at, o.updated_at,
                    CONCAT(u.first_name,' ',u.last_name) AS seller_name,
                    COALESCE(u.shop_name, CONCAT(u.first_name,' ',u.last_name)) AS shop_name
                 FROM technician_orders o
                 JOIN users u ON u.id = o.seller_id
                 WHERE o.technician_id = ?
                 ORDER BY o.created_at DESC"
            );
            $stmt->execute([$techId]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch items for each order
            foreach ($orders as &$order) {
                $iStmt = $pdo->prepare(
                    "SELECT product_name, category, unit_price, quantity, subtotal
                     FROM technician_order_items WHERE order_id = ?"
                );
                $iStmt->execute([$order['id']]);
                $order['items'] = $iStmt->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($order);

            echo json_encode(['success' => true, 'orders' => $orders]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Order stats ───────────────────────────────────────────
    if ($action === 'order_stats') {
        try {
            $stmt = $pdo->prepare(
                "SELECT
                    COUNT(*)                                                              AS total,
                    SUM(CASE WHEN order_status='pending'   THEN 1 ELSE 0 END)            AS pending,
                    SUM(CASE WHEN order_status='confirmed' THEN 1 ELSE 0 END)            AS confirmed,
                    SUM(CASE WHEN order_status='delivered' THEN 1 ELSE 0 END)            AS delivered,
                    SUM(CASE WHEN order_status='cancelled' THEN 1 ELSE 0 END)            AS cancelled
                 FROM technician_orders WHERE technician_id = ?"
            );
            $stmt->execute([$techId]);
            echo json_encode(['success' => true, 'stats' => $stmt->fetch(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
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

    // ── Shared: validate + build order ───────────────────────
    function buildOrder(array $body, int $techId, PDO $pdo): array {
        $sellerId        = (int)($body['seller_id']       ?? 0);
        $sellerRole      = $body['seller_role']            ?? 'supplier';
        $fulfillmentType = $body['fulfillment_type']       ?? 'delivery';
        $deliveryAddress = trim($body['delivery_address']  ?? '');
        $paymentMethod   = $body['payment_method']         ?? 'cod';
        $notes           = trim($body['notes']             ?? '');
        $cartItems       = $body['cart']                   ?? [];

        if (!$sellerId || empty($cartItems)) {
            throw new InvalidArgumentException('Seller and cart items are required.');
        }
        if (!in_array($sellerRole, ['supplier','owner'])) {
            throw new InvalidArgumentException('Invalid seller role.');
        }
        if (!in_array($fulfillmentType, ['pickup','delivery'])) {
            throw new InvalidArgumentException('Invalid fulfillment type.');
        }
        if ($fulfillmentType === 'delivery' && empty($deliveryAddress)) {
            throw new InvalidArgumentException('Delivery address is required for delivery orders.');
        }
        if (!in_array($paymentMethod, ['cod','gcash','card'])) {
            throw new InvalidArgumentException('Invalid payment method.');
        }

        // Verify seller exists
        $sStmt = $pdo->prepare("SELECT id, role FROM users WHERE id = ? AND role = ? AND is_active = 1");
        $sStmt->execute([$sellerId, $sellerRole]);
        if (!$sStmt->fetch()) {
            throw new InvalidArgumentException('Seller not found or inactive.');
        }

        // Validate products and compute totals
        $lineItems   = [];
        $subtotal    = 0.0;

        foreach ($cartItems as $item) {
            $productId = (int)($item['id'] ?? 0);
            $quantity  = max(1, (int)($item['quantity'] ?? 1));
            if (!$productId) continue;

            $pStmt = $pdo->prepare(
                "SELECT id, item_description, category, srp, qty, supplier_id
                 FROM supplier_products
                 WHERE id = ? AND supplier_id = ?
                   AND status IN ('sent_to_sales_person','owner_received','verified')
                   AND qty > 0"
            );
            $pStmt->execute([$productId, $sellerId]);
            $product = $pStmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new InvalidArgumentException("Product #{$productId} not available from this seller.");
            }
            if ($product['qty'] < $quantity) {
                throw new InvalidArgumentException("Not enough stock for \"{$product['item_description']}\". Only {$product['qty']} left.");
            }

            $unitPrice = (float)$product['srp'];
            $lineItems[] = [
                'product_id'   => $productId,
                'product_name' => $product['item_description'],
                'category'     => $product['category'],
                'unit_price'   => $unitPrice,
                'quantity'     => $quantity,
                'subtotal'     => $unitPrice * $quantity,
            ];
            $subtotal += $unitPrice * $quantity;
        }

        if (empty($lineItems)) {
            throw new InvalidArgumentException('No valid products in cart.');
        }

        $shippingFee = ($fulfillmentType === 'delivery') ? 50.0 : 0.0;
        $totalAmount = $subtotal + $shippingFee;

        return compact(
            'sellerId','sellerRole','fulfillmentType','deliveryAddress',
            'paymentMethod','notes','lineItems','subtotal','shippingFee','totalAmount'
        );
    }

    // ── Place COD order ───────────────────────────────────────
    if ($action === 'place_cod') {
        try {
            $order = buildOrder($body, $techId, $pdo);
            if ($order['paymentMethod'] !== 'cod') {
                throw new InvalidArgumentException('Use create_checkout for GCash/Card payments.');
            }

            $reference = 'FG-TECH-' . strtoupper(bin2hex(random_bytes(5))) . '-' . time();

            $pdo->beginTransaction();

            // Insert order
            $ins = $pdo->prepare(
                "INSERT INTO technician_orders
                    (technician_id, seller_id, seller_role, fulfillment_type, delivery_address,
                     payment_method, payment_status, order_status, subtotal, shipping_fee,
                     total_amount, reference, notes, created_at, updated_at)
                 VALUES (?,?,?,?,?,  'cod','pending','pending',?,?,  ?,?,?, NOW(),NOW())"
            );
            $ins->execute([
                $techId, $order['sellerId'], $order['sellerRole'],
                $order['fulfillmentType'], $order['deliveryAddress'] ?: null,
                $order['subtotal'], $order['shippingFee'],
                $order['totalAmount'], $reference, $order['notes'] ?: null,
            ]);
            $orderId = (int)$pdo->lastInsertId();

            // Insert items
            $iIns = $pdo->prepare(
                "INSERT INTO technician_order_items
                    (order_id, product_id, product_name, category, unit_price, quantity, subtotal)
                 VALUES (?,?,?,?,?,?,?)"
            );
            foreach ($order['lineItems'] as $item) {
                $iIns->execute([
                    $orderId, $item['product_id'], $item['product_name'],
                    $item['category'], $item['unit_price'], $item['quantity'], $item['subtotal'],
                ]);
            }

            $pdo->commit();

            // Notify seller
            try {
                require_once __DIR__ . '/notification_helper.php';
                $techRow = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                $techRow->execute([$techId]);
                $tr = $techRow->fetch(PDO::FETCH_ASSOC);
                $techName = trim(($tr['first_name']??'') . ' ' . ($tr['last_name']??''));
                sendNotification(
                    $order['sellerId'],
                    'new_order',
                    'New Order from Technician',
                    "Technician {$techName} placed a COD order #{$orderId} (₱" . number_format($order['totalAmount'],2) . ")."
                );
            } catch (Exception $ne) { error_log('[tech_orders cod notify] ' . $ne->getMessage()); }

            echo json_encode([
                'success'   => true,
                'order_id'  => $orderId,
                'reference' => $reference,
                'message'   => 'Order placed successfully!',
            ]);
        } catch (InvalidArgumentException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('[tech_orders place_cod] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Order failed: ' . $e->getMessage()]);
        }
        exit;
    }

    // ── Create PayMongo checkout (GCash / Card) ───────────────
    if ($action === 'create_checkout') {
        try {
            $order = buildOrder($body, $techId, $pdo);
            if ($order['paymentMethod'] === 'cod') {
                throw new InvalidArgumentException('Use place_cod for COD orders.');
            }

            $config    = require __DIR__ . '/config.php';
            $secretKey = $config['paymongo_secret_key'] ?? '';
            if (empty($secretKey)) {
                throw new RuntimeException('Payment gateway not configured.');
            }

            $reference = 'FG-TECH-' . strtoupper(bin2hex(random_bytes(5))) . '-' . time();

            // Build PayMongo line items
            $pmLineItems = [];
            foreach ($order['lineItems'] as $item) {
                $pmLineItems[] = [
                    'currency'    => 'PHP',
                    'amount'      => (int) round($item['unit_price'] * 100),
                    'name'        => $item['product_name'],
                    'quantity'    => $item['quantity'],
                    'description' => $item['category'] ?? $item['product_name'],
                ];
            }
            if ($order['shippingFee'] > 0) {
                $pmLineItems[] = [
                    'currency'    => 'PHP',
                    'amount'      => (int) round($order['shippingFee'] * 100),
                    'name'        => 'Shipping Fee',
                    'quantity'    => 1,
                    'description' => 'Standard delivery',
                ];
            }

            // Get technician info for billing
            $tStmt = $pdo->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
            $tStmt->execute([$techId]);
            $tech = $tStmt->fetch(PDO::FETCH_ASSOC);

            $appUrl     = rtrim($config['app_url'], '/');
            $successUrl = $appUrl . '/backend/technician_payment_return.php?status=success&ref=' . $reference;
            $cancelUrl  = $appUrl . '/backend/technician_payment_return.php?status=cancel&ref='  . $reference;

            $pmPaymentTypes = $order['paymentMethod'] === 'gcash' ? ['gcash'] : ['card'];

            $payload = [
                'data' => [
                    'attributes' => [
                        'billing' => [
                            'name'  => trim(($tech['first_name']??'') . ' ' . ($tech['last_name']??'')),
                            'email' => $tech['email'] ?? '',
                            'phone' => $tech['phone'] ?? '',
                        ],
                        'send_email_receipt'   => false,
                        'show_description'     => true,
                        'show_line_items'      => true,
                        'line_items'           => $pmLineItems,
                        'payment_method_types' => $pmPaymentTypes,
                        'success_url'          => $successUrl,
                        'cancel_url'           => $cancelUrl,
                        'description'          => 'Fix&Go Technician Order — ' . $reference,
                        'reference_number'     => $reference,
                        'metadata'             => [
                            'technician_id'   => $techId,
                            'seller_id'       => $order['sellerId'],
                            'seller_role'     => $order['sellerRole'],
                            'fulfillment'     => $order['fulfillmentType'],
                            'delivery_addr'   => $order['deliveryAddress'],
                            'payment_method'  => $order['paymentMethod'],
                            'notes'           => $order['notes'],
                            'cart'            => json_encode($body['cart']),
                        ],
                    ],
                ],
            ];

            $auth = base64_encode($secretKey . ':');
            $ch   = curl_init('https://api.paymongo.com/v1/checkout_sessions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Basic ' . $auth,
                ],
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $decoded = json_decode($response, true);
            if ($httpCode !== 200 && $httpCode !== 201) {
                $errMsg = $decoded['errors'][0]['detail'] ?? ('PayMongo error HTTP ' . $httpCode);
                throw new RuntimeException($errMsg);
            }

            $checkoutUrl = $decoded['data']['attributes']['checkout_url'] ?? '';
            $pmId        = $decoded['data']['id'] ?? '';
            if (!$checkoutUrl) throw new RuntimeException('No checkout URL from PayMongo.');

            // Save pending order
            $pdo->beginTransaction();
            $ins = $pdo->prepare(
                "INSERT INTO technician_orders
                    (technician_id, seller_id, seller_role, fulfillment_type, delivery_address,
                     payment_method, payment_status, order_status, subtotal, shipping_fee,
                     total_amount, reference, paymongo_id, checkout_url, notes, created_at, updated_at)
                 VALUES (?,?,?,?,?,  ?,  'pending','pending',?,?,  ?,?,?,?,?, NOW(),NOW())"
            );
            $ins->execute([
                $techId, $order['sellerId'], $order['sellerRole'],
                $order['fulfillmentType'], $order['deliveryAddress'] ?: null,
                $order['paymentMethod'],
                $order['subtotal'], $order['shippingFee'],
                $order['totalAmount'], $reference, $pmId, $checkoutUrl,
                $order['notes'] ?: null,
            ]);
            $orderId = (int)$pdo->lastInsertId();

            $iIns = $pdo->prepare(
                "INSERT INTO technician_order_items
                    (order_id, product_id, product_name, category, unit_price, quantity, subtotal)
                 VALUES (?,?,?,?,?,?,?)"
            );
            foreach ($order['lineItems'] as $item) {
                $iIns->execute([
                    $orderId, $item['product_id'], $item['product_name'],
                    $item['category'], $item['unit_price'], $item['quantity'], $item['subtotal'],
                ]);
            }
            $pdo->commit();

            echo json_encode([
                'success'      => true,
                'checkout_url' => $checkoutUrl,
                'reference'    => $reference,
                'order_id'     => $orderId,
            ]);
        } catch (InvalidArgumentException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('[tech_orders checkout] ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Cancel order ──────────────────────────────────────────
    if ($action === 'cancel_order') {
        $orderId = (int)($body['order_id'] ?? 0);
        if (!$orderId) { echo json_encode(['success'=>false,'message'=>'Order ID required.']); exit; }
        try {
            $upd = $pdo->prepare(
                "UPDATE technician_orders SET order_status='cancelled', updated_at=NOW()
                 WHERE id=? AND technician_id=? AND order_status='pending'"
            );
            $upd->execute([$orderId, $techId]);
            if ($upd->rowCount() === 0) {
                echo json_encode(['success'=>false,'message'=>'Order not found or cannot be cancelled.']);
                exit;
            }
            echo json_encode(['success'=>true,'message'=>'Order cancelled.']);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
