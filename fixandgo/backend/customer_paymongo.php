<?php
/**
 * Fix&Go — Customer PayMongo Checkout
 *
 * POST action=create_checkout
 *   body: {
 *     payment_method: 'gcash' | 'card',
 *     cart: [ { id, item_description, srp, quantity } ],
 *     card_number (optional, for display only — actual card is handled by PayMongo)
 *   }
 *
 * Returns: { success, checkout_url, reference }
 *
 * Flow:
 *   1. Customer selects GCash or Card → clicks Place Order
 *   2. Frontend POSTs here with cart + payment method
 *   3. We create a PayMongo checkout session
 *   4. Customer is redirected to PayMongo hosted page
 *   5. PayMongo redirects to customer_payment_return.php
 *   6. Return handler places the orders in DB and redirects to orders.php
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

// Auth — customers only
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Customer login required.']);
    exit;
}

$customerId = (int)$_SESSION['user_id'];
$config     = require __DIR__ . '/config.php';
$pdo        = require __DIR__ . '/db.php';

// ── Validate secret key ──────────────────────────────────────
$secretKey = $config['paymongo_secret_key'] ?? '';
if (empty($secretKey)) {
    echo json_encode(['success' => false, 'message' => 'Payment gateway not configured.']);
    exit;
}

// ── Parse request ────────────────────────────────────────────
$body          = json_decode(file_get_contents('php://input'), true) ?? [];
$paymentMethod = $body['payment_method'] ?? '';
$cartItems     = $body['cart'] ?? [];

if (!in_array($paymentMethod, ['gcash', 'card'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment method.']);
    exit;
}
if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit;
}

// ── Verify customer address ──────────────────────────────────
$addrStmt = $pdo->prepare(
    "SELECT first_name, last_name, email, phone,
            address_line, barangay, city, province, zip_code, address_verified
     FROM users WHERE id = ?"
);
$addrStmt->execute([$customerId]);
$customer = $addrStmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo json_encode(['success' => false, 'message' => 'Customer not found.']);
    exit;
}

$addressComplete = !empty($customer['address_line']) && !empty($customer['barangay'])
                && !empty($customer['city'])         && !empty($customer['province'])
                && !empty($customer['zip_code'])     && !empty($customer['phone']);

if (!$addressComplete || !$customer['address_verified']) {
    echo json_encode(['success' => false, 'message' => 'Please complete your delivery address before paying.']);
    exit;
}

// ── Build line items & validate stock ────────────────────────
$lineItems   = [];
$totalAmount = 0; // in centavos

foreach ($cartItems as $item) {
    $productId = (int)($item['id'] ?? 0);
    $quantity  = max(1, (int)($item['quantity'] ?? 1));

    if (!$productId) continue;

    // Fetch product from DB
    $pStmt = $pdo->prepare(
        "SELECT id, item_description, srp, qty, status, is_displayed
         FROM supplier_products WHERE id = ?"
    );
    $pStmt->execute([$productId]);
    $product = $pStmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => "Product #{$productId} not found."]);
        exit;
    }
    if ($product['status'] !== 'sent_to_sales_person' || !$product['is_displayed']) {
        echo json_encode(['success' => false, 'message' => "Product \"{$product['item_description']}\" is no longer available."]);
        exit;
    }
    if ($product['qty'] < $quantity) {
        echo json_encode(['success' => false, 'message' => "Not enough stock for \"{$product['item_description']}\". Only {$product['qty']} left."]);
        exit;
    }

    $unitCentavos = (int) round((float)$product['srp'] * 100);
    $totalAmount += $unitCentavos * $quantity;

    $lineItems[] = [
        'currency'    => 'PHP',
        'amount'      => $unitCentavos,
        'name'        => $product['item_description'],
        'quantity'    => $quantity,
        'description' => $product['item_description'],
    ];
}

if (empty($lineItems)) {
    echo json_encode(['success' => false, 'message' => 'No valid products in cart.']);
    exit;
}

// Add shipping fee (₱50 = 5000 centavos)
$shippingCentavos = 5000;
$totalAmount     += $shippingCentavos;
$lineItems[] = [
    'currency'    => 'PHP',
    'amount'      => $shippingCentavos,
    'name'        => 'Shipping Fee',
    'quantity'    => 1,
    'description' => 'Standard delivery',
];

// ── Generate unique reference ────────────────────────────────
$reference = 'FG-CUST-' . strtoupper(bin2hex(random_bytes(6))) . '-' . time();

// ── Build PayMongo payload ───────────────────────────────────
$appUrl     = rtrim($config['app_url'], '/');
$successUrl = $appUrl . '/backend/customer_payment_return.php?status=success&ref=' . $reference;
$cancelUrl  = $appUrl . '/backend/customer_payment_return.php?status=cancel&ref='  . $reference;

// Map payment method to PayMongo payment method type
$pmPaymentTypes = $paymentMethod === 'gcash' ? ['gcash'] : ['card'];

$payload = [
    'data' => [
        'attributes' => [
            'billing'              => [
                'name'  => trim($customer['first_name'] . ' ' . $customer['last_name']),
                'email' => $customer['email'] ?? '',
                'phone' => $customer['phone'] ?? '',
                'address' => [
                    'line1'       => $customer['address_line'],
                    'city'        => $customer['city'],
                    'state'       => $customer['province'],
                    'postal_code' => $customer['zip_code'],
                    'country'     => 'PH',
                ],
            ],
            'send_email_receipt'   => false,
            'show_description'     => true,
            'show_line_items'      => true,
            'line_items'           => $lineItems,
            'payment_method_types' => $pmPaymentTypes,
            'success_url'          => $successUrl,
            'cancel_url'           => $cancelUrl,
            'description'          => 'Fix&Go Order — ' . $reference,
            'reference_number'     => $reference,
            'metadata'             => [
                'customer_id'    => $customerId,
                'payment_method' => $paymentMethod,
                'cart'           => json_encode($cartItems),
            ],
        ],
    ],
];

// ── Call PayMongo API ────────────────────────────────────────
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
    $errMsg = $decoded['errors'][0]['detail'] ?? ('PayMongo error (HTTP ' . $httpCode . ')');
    error_log('[customer_paymongo] ' . $errMsg . ' | payload: ' . json_encode($payload));
    echo json_encode(['success' => false, 'message' => $errMsg]);
    exit;
}

$checkoutUrl = $decoded['data']['attributes']['checkout_url'] ?? '';
$pmId        = $decoded['data']['id'] ?? '';

if (!$checkoutUrl) {
    echo json_encode(['success' => false, 'message' => 'Could not get checkout URL from PayMongo.']);
    exit;
}

// ── Save pending payment record ──────────────────────────────
try {
    $pdo->prepare(
        "INSERT INTO customer_payments
            (customer_id, reference, paymongo_id, amount, currency,
             status, payment_method, checkout_url, cart_snapshot, created_at)
         VALUES (?, ?, ?, ?, 'PHP', 'pending', ?, ?, ?, NOW())"
    )->execute([
        $customerId,
        $reference,
        $pmId,
        $totalAmount / 100, // store in PHP pesos
        $paymentMethod,
        $checkoutUrl,
        json_encode($cartItems),
    ]);
} catch (Exception $e) {
    // Table may not exist yet — still return the checkout URL
    error_log('[customer_paymongo] DB error: ' . $e->getMessage());
}

echo json_encode([
    'success'      => true,
    'checkout_url' => $checkoutUrl,
    'reference'    => $reference,
]);
