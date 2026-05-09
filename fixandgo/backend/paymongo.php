<?php
/**
 * Fix&Go — PayMongo Payment API
 *
 * POST action=create_payment_link   → create a PayMongo payment link for accepted products
 * POST action=create_checkout       → create a PayMongo checkout session
 * GET  action=payment_status&ref=X  → check payment status by reference
 *
 * Flow:
 *  1. Owner accepts products → clicks "Buy Products"
 *  2. Frontend calls POST action=create_checkout with product IDs
 *  3. Backend creates a PayMongo checkout session
 *  4. Owner is redirected to PayMongo checkout page
 *  5. After payment, PayMongo redirects to success/cancel URL
 *  6. Webhook (paymongo_webhook.php) updates payment record in DB
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

// Auth — only owners can initiate payments
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$ownerId = (int) $_SESSION['user_id'];
$config  = require __DIR__ . '/config.php';
$pdo     = require __DIR__ . '/db.php';
$method  = $_SERVER['REQUEST_METHOD'];

// ── Enforce TEST mode only ───────────────────────────────────
$secretKey = $config['paymongo_secret_key'] ?? '';
if (!str_starts_with($secretKey, 'sk_test_')) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Only PayMongo TEST keys (sk_test_) are allowed. Live payments are disabled.',
    ]);
    exit;
}

// ── PayMongo helper ──────────────────────────────────────────
function paymongoRequest(string $endpoint, string $method, array $data, string $secretKey): array {
    $url  = 'https://api.paymongo.com/v1' . $endpoint;
    $auth = base64_encode($secretKey . ':');

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . $auth,
        ],
        CURLOPT_CUSTOMREQUEST  => $method,
    ]);

    if ($method !== 'GET' && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);
    return ['code' => $httpCode, 'body' => $decoded];
}

// ── GET: payment status ──────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    $ref    = trim($_GET['ref'] ?? '');

    if ($action === 'payment_status' && $ref) {
        $stmt = $pdo->prepare(
            'SELECT id, reference, amount, currency, status, paymongo_id,
                    checkout_url, paid_at, created_at
             FROM owner_payments
             WHERE reference = ? AND owner_id = ?
             LIMIT 1'
        );
        $stmt->execute([$ref, $ownerId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            echo json_encode(['success' => false, 'message' => 'Payment not found.']);
            exit;
        }

        // If still pending, check with PayMongo
        if ($payment['status'] === 'pending' && $payment['paymongo_id']) {
            $res = paymongoRequest(
                '/checkout_sessions/' . $payment['paymongo_id'],
                'GET', [], $config['paymongo_secret_key']
            );
            if ($res['code'] === 200) {
                $pmStatus = $res['body']['data']['attributes']['payment_intent']['attributes']['status'] ?? '';
                if ($pmStatus === 'succeeded') {
                    $pdo->prepare(
                        "UPDATE owner_payments SET status='paid', paid_at=NOW() WHERE reference=?"
                    )->execute([$ref]);
                    $payment['status'] = 'paid';
                }
            }
        }

        echo json_encode(['success' => true, 'payment' => $payment]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── POST: create checkout ────────────────────────────────────
if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    if ($action === 'create_checkout') {
        $productIds = array_map('intval', $body['product_ids'] ?? []);
        $quantities = $body['quantities'] ?? []; // Custom quantities per product

        if (empty($productIds)) {
            echo json_encode(['success' => false, 'message' => 'No products selected.']);
            exit;
        }

        // Fetch the accepted products owned by this owner
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $pdo->prepare(
            "SELECT DISTINCT sp.id, sp.item_description, sp.brand, sp.category,
                    sp.qty, sp.srp, sp.image_path,
                    CONCAT(u.first_name, ' ', u.last_name) AS supplier_name
             FROM supplier_products sp
             JOIN users u ON u.id = sp.supplier_id
             JOIN submission_items si ON si.product_id = sp.id
             JOIN product_submissions ps ON ps.id = si.submission_id AND ps.owner_id = ?
             WHERE sp.id IN ($placeholders)
               AND sp.status = 'owner_received'
             GROUP BY sp.id, sp.item_description, sp.brand, sp.category,
                      sp.qty, sp.srp, sp.image_path, supplier_name"
        );
        $stmt->execute(array_merge([$ownerId], $productIds));
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($products)) {
            echo json_encode(['success' => false, 'message' => 'No valid accepted products found.']);
            exit;
        }

        // Build line items for PayMongo
        $lineItems = [];
        $totalAmount = 0;
        $purchaseQuantities = []; // Track actual quantities being purchased

        foreach ($products as $p) {
            $productId = (int)$p['id'];
            $availableQty = max(1, (int)$p['qty']);
            
            // Use custom quantity if provided, otherwise use full stock
            $quantity = isset($quantities[$productId]) 
                ? max(1, min((int)$quantities[$productId], $availableQty))
                : $availableQty;
            
            $purchaseQuantities[$productId] = $quantity;
            
            $unitAmount  = (int) round((float)$p['srp'] * 100); // PayMongo uses centavos
            $totalAmount += $unitAmount * $quantity;

            $name = $p['item_description'];
            if ($p['brand']) $name = $p['brand'] . ' — ' . $name;

            $lineItems[] = [
                'currency'    => 'PHP',
                'amount'      => $unitAmount,
                'description' => substr($p['category'] . ' from ' . $p['supplier_name'], 0, 255),
                'name'        => substr($name, 0, 255),
                'quantity'    => $quantity,
            ];
        }

        // Generate unique reference
        $reference = 'FG-' . strtoupper(substr(md5(uniqid($ownerId, true)), 0, 10));

        $appUrl      = rtrim($config['app_url'], '/');
        $successUrl  = $appUrl . '/backend/paymongo_return.php?status=success&ref=' . $reference;
        $cancelUrl   = $appUrl . '/backend/paymongo_return.php?status=cancel&ref=' . $reference;

        // Create PayMongo checkout session
        $payload = [
            'data' => [
                'attributes' => [
                    'billing'              => null,
                    'cancel_url'           => $cancelUrl,
                    'description'          => 'Fix&Go — Product Purchase #' . $reference,
                    'line_items'           => $lineItems,
                    'payment_method_types' => ['card', 'gcash', 'paymaya', 'grab_pay'],
                    'reference_number'     => $reference,
                    'send_email_receipt'   => false,
                    'show_description'     => true,
                    'show_line_items'      => true,
                    'success_url'          => $successUrl,
                ],
            ],
        ];

        $res = paymongoRequest('/checkout_sessions', 'POST', $payload, $config['paymongo_secret_key']);

        if ($res['code'] !== 200 && $res['code'] !== 201) {
            $errMsg = $res['body']['errors'][0]['detail'] ?? 'PayMongo error.';
            echo json_encode(['success' => false, 'message' => $errMsg]);
            exit;
        }

        $session     = $res['body']['data'];
        $sessionId   = $session['id'];
        $checkoutUrl = $session['attributes']['checkout_url'];

        // Save payment record to DB
        $pdo->prepare(
            'INSERT INTO owner_payments
             (owner_id, reference, paymongo_id, amount, currency,
              status, checkout_url, product_ids, purchase_quantities, created_at)
             VALUES (?, ?, ?, ?, "PHP", "pending", ?, ?, ?, NOW())'
        )->execute([
            $ownerId,
            $reference,
            $sessionId,
            $totalAmount / 100,   // store in PHP (not centavos)
            $checkoutUrl,
            json_encode($productIds),
            json_encode($purchaseQuantities),
        ]);

        echo json_encode([
            'success'      => true,
            'reference'    => $reference,
            'checkout_url' => $checkoutUrl,
            'amount'       => $totalAmount / 100,
            'currency'     => 'PHP',
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
