<?php
/**
 * Fix&Go — Technician PayMongo Return Handler
 * PayMongo redirects here after checkout (success or cancel).
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();

$status    = $_GET['status']    ?? 'cancel';
$reference = $_GET['ref']       ?? '';

$pdo = require __DIR__ . '/db.php';

if ($status === 'success' && $reference) {
    // Mark order as paid
    try {
        $pdo->prepare(
            "UPDATE technician_orders
             SET payment_status='paid', order_status='confirmed', updated_at=NOW()
             WHERE reference=? AND payment_status='pending'"
        )->execute([$reference]);
    } catch (Exception $e) {
        error_log('[tech_payment_return] ' . $e->getMessage());
    }
    header('Location: ../views/user/phone_technician/supply-requests.php?payment=success&ref=' . urlencode($reference));
} else {
    header('Location: ../views/user/phone_technician/supply-requests.php?payment=cancel');
}
exit;
