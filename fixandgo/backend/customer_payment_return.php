<?php
/**
 * Fix&Go — Customer Payment Return Handler
 *
 * PayMongo redirects here after GCash / card payment.
 * Verifies the payment with PayMongo, places the orders in DB,
 * then redirects the customer to orders.php (success) or checkout.php (cancel).
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();

$config = require __DIR__ . '/config.php';
$pdo    = require __DIR__ . '/db.php';

$status = $_GET['status'] ?? 'cancel';
$ref    = trim($_GET['ref'] ?? '');

$appUrl      = rtrim($config['app_url'], '/');
$ordersUrl   = $appUrl . '/views/user/customer/orders.php';
$checkoutUrl = $appUrl . '/views/user/customer/checkout.php';

// ── Helper: place orders from cart snapshot ──────────────────
function placeOrdersFromCart(PDO $pdo, int $customerId, array $cartItems, string $paymentMethod, string $reference): array {
    $errors = [];

    $pdo->beginTransaction();
    try {
        foreach ($cartItems as $item) {
            $productId = (int)($item['id'] ?? 0);
            $quantity  = max(1, (int)($item['quantity'] ?? 1));

            if (!$productId) continue;

            // Lock and fetch product
            $pStmt = $pdo->prepare(
                "SELECT id, srp, qty, status, is_displayed
                 FROM supplier_products WHERE id = ? FOR UPDATE"
            );
            $pStmt->execute([$productId]);
            $product = $pStmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || $product['qty'] < $quantity) {
                $errors[] = "Product #{$productId} out of stock.";
                continue;
            }

            $unitPrice   = (float)$product['srp'];
            $totalAmount = $unitPrice * $quantity;

            // Insert order
            $pdo->prepare(
                "INSERT INTO customer_orders
                    (customer_id, product_id, quantity, unit_price, total_amount,
                     status, payment_method, notes)
                 VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)"
            )->execute([
                $customerId, $productId, $quantity,
                $unitPrice, $totalAmount,
                $paymentMethod,
                'Paid via PayMongo — Ref: ' . $reference,
            ]);

            // Deduct stock
            $pdo->prepare("UPDATE supplier_products SET qty = qty - ? WHERE id = ?")
                ->execute([$quantity, $productId]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('[customer_payment_return] placeOrders error: ' . $e->getMessage());
        $errors[] = 'Database error placing orders.';
    }

    return $errors;
}

// ── Handle cancel ────────────────────────────────────────────
if ($status !== 'success' || !$ref) {
    if ($ref) {
        try {
            $pdo->prepare(
                "UPDATE customer_payments SET status='cancelled' WHERE reference=? AND status='pending'"
            )->execute([$ref]);
        } catch (Exception $e) { /* table may not exist */ }
    }
    header('Location: ' . $checkoutUrl . '?payment=cancelled');
    exit;
}

// ── Handle success ───────────────────────────────────────────

// 1. Load payment record
$payment = null;
try {
    $stmt = $pdo->prepare(
        "SELECT id, customer_id, paymongo_id, payment_method, cart_snapshot, status
         FROM customer_payments WHERE reference = ? LIMIT 1"
    );
    $stmt->execute([$ref]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('[customer_payment_return] DB read error: ' . $e->getMessage());
}

// 2. Verify with PayMongo
$secretKey = $config['paymongo_secret_key'] ?? '';
$pmStatus  = '';

if ($payment && $payment['paymongo_id']) {
    $auth = base64_encode($secretKey . ':');
    $ch   = curl_init('https://api.paymongo.com/v1/checkout_sessions/' . $payment['paymongo_id']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'Authorization: Basic ' . $auth,
        ],
    ]);
    $res     = curl_exec($ch);
    $decoded = json_decode($res, true);
    curl_close($ch);

    $pmStatus = $decoded['data']['attributes']['payment_intent']['attributes']['status'] ?? '';
}

// 3. If payment confirmed, place orders
if ($pmStatus === 'succeeded' || $pmStatus === 'paid') {
    // Mark payment as paid
    try {
        $pdo->prepare(
            "UPDATE customer_payments SET status='paid', paid_at=NOW() WHERE reference=?"
        )->execute([$ref]);
    } catch (Exception $e) { /* ignore */ }

    // Place orders
    $customerId  = $payment ? (int)$payment['customer_id'] : 0;
    $cartItems   = $payment ? (json_decode($payment['cart_snapshot'], true) ?? []) : [];
    $payMethod   = $payment ? $payment['payment_method'] : 'gcash';

    if ($customerId && !empty($cartItems)) {
        placeOrdersFromCart($pdo, $customerId, $cartItems, $payMethod, $ref);
    }

    // Redirect to orders page with success flag
    header('Location: ' . $ordersUrl . '?payment=success&ref=' . urlencode($ref));
    exit;
}

// 4. Payment not confirmed — redirect back to checkout
header('Location: ' . $checkoutUrl . '?payment=failed&ref=' . urlencode($ref));
exit;
