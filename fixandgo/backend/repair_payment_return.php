<?php
/**
 * Fix&Go — Repair PayMongo Return Handler
 * PayMongo redirects here after checkout (success or cancel).
 */
require_once __DIR__ . '/helpers.php';
startSecureSession();

$status    = $_GET['status']    ?? 'cancel';
$reference = $_GET['ref']       ?? '';
$pdo = require __DIR__ . '/db.php';

if ($status === 'success' && $reference) {
    try {
        // Mark repair_payments record as paid
        $stmt = $pdo->prepare(
            "UPDATE repair_payments SET status='paid', paid_at=NOW()
             WHERE reference=? AND status='pending'"
        );
        $stmt->execute([$reference]);

        // Also mark booking as customer-paid
        $rp = $pdo->prepare("SELECT booking_id, customer_id FROM repair_payments WHERE reference=?");
        $rp->execute([$reference]);
        $row = $rp->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Ensure customer payment columns exist
            try {
                $pdo->exec("ALTER TABLE bookings
                    ADD COLUMN IF NOT EXISTS customer_payment_method ENUM('cash','gcash','maya','bank_transfer','card','grab_pay','other') NULL DEFAULT NULL,
                    ADD COLUMN IF NOT EXISTS customer_payment_status ENUM('pending','paid') NOT NULL DEFAULT 'pending',
                    ADD COLUMN IF NOT EXISTS customer_paid_at DATETIME NULL DEFAULT NULL");
            } catch (Exception $ae) {}

            $pdo->prepare(
                "UPDATE bookings
                 SET customer_payment_method='online', customer_payment_status='paid',
                     customer_paid_at=NOW(), updated_at=NOW()
                 WHERE id=? AND customer_id=?"
            )->execute([$row['booking_id'], $row['customer_id']]);

            // Notify technician
            try {
                require_once __DIR__ . '/notification_helper.php';
                $bRow = $pdo->prepare("SELECT b.device_name, b.technician_id FROM bookings b WHERE b.id=?");
                $bRow->execute([$row['booking_id']]);
                $bData = $bRow->fetch(PDO::FETCH_ASSOC);
                if ($bData && $bData['technician_id']) {
                    sendNotification(
                        $bData['technician_id'], 'payment_received',
                        'Online Payment Received',
                        "Customer paid online (GCash/Card) for repair #{$row['booking_id']} ({$bData['device_name']})."
                    );
                }
            } catch (Exception $ne) { error_log('[repair_payment_return notify] '.$ne->getMessage()); }
        }
    } catch (Exception $e) {
        error_log('[repair_payment_return] ' . $e->getMessage());
    }
    header('Location: ../views/user/customer/repairs.php?payment=success&ref=' . urlencode($reference));
} else {
    header('Location: ../views/user/customer/repairs.php?payment=cancel');
}
exit;
