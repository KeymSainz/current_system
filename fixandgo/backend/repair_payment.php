<?php
/**
 * Fix&Go — Repair Payment via PayMongo
 *
 * POST action=create_checkout  → create PayMongo checkout for a completed repair
 * GET  action=status&ref=X     → check payment status
 */
require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Customer login required.']);
    exit;
}

$customerId = (int) $_SESSION['user_id'];
$config     = require __DIR__ . '/config.php';
$pdo        = require __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$method     = $_SERVER['REQUEST_METHOD'];
$secretKey  = $config['paymongo_secret_key'] ?? '';
$appUrl     = rtrim($config['app_url'] ?? 'https://fixandgo.great-site.net', '/');

// ── PayMongo helper ───────────────────────────────────────────
function pmRequest(string $endpoint, string $verb, array $data, string $key): array {
    $ch = curl_init('https://api.paymongo.com/v1' . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($key . ':'),
        ],
        CURLOPT_CUSTOMREQUEST  => $verb,
        CURLOPT_POSTFIELDS     => json_encode($data),
    ]);
    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => json_decode($raw, true) ?? []];
}

// ── GET: check status ─────────────────────────────────────────
if ($method === 'GET' && ($_GET['action'] ?? '') === 'status') {
    $ref = trim($_GET['ref'] ?? '');
    if (!$ref) { echo json_encode(['success' => false, 'message' => 'ref required']); exit; }
    try {
        $stmt = $pdo->prepare(
            "SELECT rp.status, rp.amount, rp.checkout_url, b.customer_payment_status
             FROM repair_payments rp
             JOIN bookings b ON b.id = rp.booking_id
             WHERE rp.reference = ? AND rp.customer_id = ?"
        );
        $stmt->execute([$ref, $customerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($row ? ['success' => true, 'payment' => $row] : ['success' => false, 'message' => 'Not found.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ── POST: create checkout ─────────────────────────────────────
if ($method === 'POST') {
    $body      = json_decode(file_get_contents('php://input'), true) ?? [];
    $bookingId = (int)($body['booking_id'] ?? 0);

    if (!$bookingId) {
        echo json_encode(['success' => false, 'message' => 'booking_id required.']); exit;
    }

    try {
        // Fetch booking — must belong to customer and be completed
        $stmt = $pdo->prepare(
            "SELECT b.id, b.status, b.device_name, b.repair_fee, b.labor_fee, b.parts_fee,
                    b.total_amount, b.customer_payment_status, b.technician_id,
                    COALESCE(b.total_amount, b.repair_fee, 0) AS final_amount
             FROM bookings b
             WHERE b.id = ? AND b.customer_id = ? AND b.status = 'completed'"
        );
        $stmt->execute([$bookingId, $customerId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            echo json_encode(['success' => false, 'message' => 'Booking not found or not completed.']); exit;
        }

        if ($booking['customer_payment_status'] === 'paid') {
            echo json_encode(['success' => false, 'message' => 'This repair has already been paid.']); exit;
        }

        $amountPeso = (float)$booking['final_amount'];
        if ($amountPeso <= 0) {
            echo json_encode(['success' => false, 'message' => 'No amount set for this repair. Ask your technician to set the fee first.']); exit;
        }
        $amountCentavos = (int)round($amountPeso * 100);
        $reference  = 'REPAIR-' . $bookingId . '-' . time();
        $deviceName = $booking['device_name'] ?: 'Device Repair';

        // Build line items
        $lineItems = [];
        $laborFee = (float)($booking['labor_fee'] ?? 0);
        $partsFee = (float)($booking['parts_fee'] ?? 0);

        if ($laborFee > 0 && $partsFee > 0) {
            $lineItems[] = [
                'amount'      => (int)round($laborFee * 100),
                'currency'    => 'PHP',
                'description' => 'Labor / Service Fee',
                'name'        => 'Labor Fee',
                'quantity'    => 1,
            ];
            $lineItems[] = [
                'amount'      => (int)round($partsFee * 100),
                'currency'    => 'PHP',
                'description' => 'Parts & Replacement Cost',
                'name'        => 'Parts Fee',
                'quantity'    => 1,
            ];
        } else {
            $lineItems[] = [
                'amount'      => $amountCentavos,
                'currency'    => 'PHP',
                'description' => 'Repair service for ' . $deviceName,
                'name'        => $deviceName . ' Repair',
                'quantity'    => 1,
            ];
        }

        $successUrl = $appUrl . '/views/user/customer/repairs.php?payment=success&ref=' . urlencode($reference);
        $cancelUrl  = $appUrl . '/views/user/customer/repairs.php?payment=cancel';

        $res = pmRequest('/checkout_sessions', 'POST', [
            'data' => [
                'attributes' => [
                    'billing'              => null,
                    'cancel_url'           => $cancelUrl,
                    'description'          => "Fix&Go Repair Payment — {$deviceName} #{$bookingId}",
                    'line_items'           => $lineItems,
                    'payment_method_types' => ['card', 'gcash', 'paymaya', 'grab_pay', 'dob', 'brankas_bdo', 'brankas_landbank', 'brankas_metrobank'],
                    'reference_number'     => $reference,
                    'send_email_receipt'   => false,
                    'show_description'     => true,
                    'show_line_items'      => true,
                    'success_url'          => $successUrl,
                ],
            ],
        ], $secretKey);

        if ($res['code'] !== 200 || empty($res['body']['data']['attributes']['checkout_url'])) {
            $errMsg = $res['body']['errors'][0]['detail'] ?? 'PayMongo checkout creation failed.';
            echo json_encode(['success' => false, 'message' => $errMsg]); exit;
        }

        $session     = $res['body']['data'];
        $checkoutUrl = $session['attributes']['checkout_url'];
        $sessionId   = $session['id'];

        // Ensure repair_payments table exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS repair_payments (
            id              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            booking_id      INT UNSIGNED NOT NULL,
            customer_id     INT UNSIGNED NOT NULL,
            technician_id   INT UNSIGNED NULL,
            reference       VARCHAR(100) NOT NULL UNIQUE,
            paymongo_id     VARCHAR(100) NULL,
            amount          DECIMAL(10,2) NOT NULL,
            currency        CHAR(3) NOT NULL DEFAULT 'PHP',
            status          ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
            checkout_url    VARCHAR(1000) NULL,
            paid_at         DATETIME NULL,
            created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_booking (booking_id),
            INDEX idx_customer (customer_id),
            INDEX idx_reference (reference)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->prepare(
            "INSERT INTO repair_payments
             (booking_id, customer_id, technician_id, reference, paymongo_id, amount, checkout_url)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        )->execute([$bookingId, $customerId, $booking['technician_id'], $reference, $sessionId, $amountPeso, $checkoutUrl]);

        echo json_encode([
            'success'      => true,
            'checkout_url' => $checkoutUrl,
            'reference'    => $reference,
            'amount'       => $amountPeso,
        ]);

    } catch (Exception $e) {
        error_log('[repair_payment] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
