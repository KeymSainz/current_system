<?php
/**
 * Fix&Go — PayMongo Test Purchase
 * Creates a real test checkout session using PayMongo test keys.
 * Use PayMongo test card numbers to complete the payment.
 *
 * Test Cards:
 *   Visa (success):    4343 4343 4343 4345  exp: any future  cvv: any
 *   Mastercard:        5555 4444 3333 1111  exp: any future  cvv: any
 *   GCash (test):      use test phone number in PayMongo sandbox
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

// Allow only owners in test mode
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$config  = require __DIR__ . '/config.php';
$pdo     = require __DIR__ . '/db.php';
$ownerId = (int) $_SESSION['user_id'];

// ── Enforce TEST mode only ───────────────────────────────────
$secretKey = $config['paymongo_secret_key'] ?? '';
if (!str_starts_with($secretKey, 'sk_test_')) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Only PayMongo TEST keys (sk_test_) are allowed.',
    ]);
    exit;
}

// Only allow in development
if (($config['app_env'] ?? 'development') !== 'development') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Test endpoint disabled in production.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'POST only.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];

// ── Build dummy line items ────────────────────────────────────
$dummyItems = $body['items'] ?? [
    [
        'name'        => 'iPhone 14 Tempered Glass 2.5D Full Cover',
        'description' => 'Tempered Glass — from kim supplier',
        'amount'      => 29900,   // ₱299.00 in centavos
        'quantity'    => 2,
        'currency'    => 'PHP',
    ],
    [
        'name'        => 'Samsung Galaxy S8 Battery 3000mAh',
        'description' => 'Battery — from kim supplier',
        'amount'      => 45000,   // ₱450.00 in centavos
        'quantity'    => 1,
        'currency'    => 'PHP',
    ],
];

$totalCentavos = array_sum(array_map(fn($i) => $i['amount'] * $i['quantity'], $dummyItems));
$reference     = 'FG-TEST-' . strtoupper(substr(md5(uniqid($ownerId, true)), 0, 8));

$appUrl     = rtrim($config['app_url'], '/');
$successUrl = $appUrl . '/backend/paymongo_return.php?status=success&ref=' . $reference;
$cancelUrl  = $appUrl . '/backend/paymongo_return.php?status=cancel&ref='  . $reference;

// ── Call PayMongo API ─────────────────────────────────────────
$payload = [
    'data' => [
        'attributes' => [
            'cancel_url'           => $cancelUrl,
            'description'          => 'Fix&Go Test Purchase — ' . $reference,
            'line_items'           => $dummyItems,
            'payment_method_types' => ['card', 'gcash', 'paymaya', 'grab_pay'],
            'reference_number'     => $reference,
            'send_email_receipt'   => false,
            'show_description'     => true,
            'show_line_items'      => true,
            'success_url'          => $successUrl,
        ],
    ],
];

$auth = base64_encode($config['paymongo_secret_key'] . ':');
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
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    echo json_encode(['success' => false, 'message' => 'cURL error: ' . $curlErr]);
    exit;
}

$decoded = json_decode($response, true);

if ($httpCode !== 200 && $httpCode !== 201) {
    $errMsg = $decoded['errors'][0]['detail'] ?? ('PayMongo error (HTTP ' . $httpCode . ')');
    echo json_encode(['success' => false, 'message' => $errMsg, 'raw' => $decoded]);
    exit;
}

$sessionId   = $decoded['data']['id'];
$checkoutUrl = $decoded['data']['attributes']['checkout_url'];

// ── Save to DB ────────────────────────────────────────────────
try {
    $pdo->prepare(
        'INSERT INTO owner_payments
         (owner_id, reference, paymongo_id, amount, currency, status, checkout_url, product_ids, created_at)
         VALUES (?, ?, ?, ?, "PHP", "pending", ?, ?, NOW())'
    )->execute([
        $ownerId,
        $reference,
        $sessionId,
        $totalCentavos / 100,
        $checkoutUrl,
        json_encode([]),   // dummy — no real product IDs
    ]);
} catch (Exception $e) {
    // Table may not exist yet — still return the checkout URL
    error_log('[PayMongo Test] DB error: ' . $e->getMessage());
}

echo json_encode([
    'success'      => true,
    'reference'    => $reference,
    'checkout_url' => $checkoutUrl,
    'amount'       => number_format($totalCentavos / 100, 2),
    'currency'     => 'PHP',
    'items'        => count($dummyItems),
    'note'         => 'Use test card 4343 4343 4343 4345 to complete payment.',
]);
