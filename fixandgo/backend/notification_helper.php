<?php
/**
 * Fix&Go — Notification Helper Functions
 * 
 * Reusable functions for creating notifications throughout the system.
 * Include this file wherever you need to send notifications to users.
 * 
 * Usage:
 *   require_once __DIR__ . '/notification_helper.php';
 *   sendNotification($userId, 'booking_confirmed', 'Booking Confirmed', 'Your booking is confirmed.');
 */

/**
 * Send a notification to a user
 * 
 * @param int $userId The ID of the user to notify
 * @param string $type The notification type (e.g., 'booking_confirmed', 'order_placed')
 * @param string $title The notification title
 * @param string $body The notification body text
 * @return bool True on success, false on failure
 */
function sendNotification($userId, $type, $title, $body) {
    try {
        $pdo = require __DIR__ . '/db.php';
        
        $stmt = $pdo->prepare(
            "INSERT INTO notifications (user_id, type, title, body, created_at)
             VALUES (?, ?, ?, ?, NOW())"
        );
        
        return $stmt->execute([$userId, $type, $title, $body]);
    } catch (Exception $e) {
        error_log("Failed to send notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Send a notification to multiple users
 * 
 * @param array $userIds Array of user IDs to notify
 * @param string $type The notification type
 * @param string $title The notification title
 * @param string $body The notification body text
 * @return int Number of notifications successfully sent
 */
function sendNotificationToMultiple($userIds, $type, $title, $body) {
    $count = 0;
    foreach ($userIds as $userId) {
        if (sendNotification($userId, $type, $title, $body)) {
            $count++;
        }
    }
    return $count;
}

/**
 * Send a booking confirmation notification
 * 
 * @param int $userId The customer's user ID
 * @param int $bookingId The booking ID
 * @param string $date The booking date
 * @return bool
 */
function notifyBookingConfirmed($userId, $bookingId, $date) {
    return sendNotification(
        $userId,
        'booking_confirmed',
        'Booking Confirmed',
        "Your phone repair booking #{$bookingId} has been confirmed for {$date}. Our technician will contact you shortly."
    );
}

/**
 * Send a booking cancellation notification
 * 
 * @param int $userId The customer's user ID
 * @param int $bookingId The booking ID
 * @param string $reason Optional cancellation reason
 * @return bool
 */
function notifyBookingCancelled($userId, $bookingId, $reason = '') {
    $body = "Your booking #{$bookingId} has been cancelled.";
    if ($reason) {
        $body .= " Reason: {$reason}";
    }
    
    return sendNotification(
        $userId,
        'booking_cancelled',
        'Booking Cancelled',
        $body
    );
}

/**
 * Send an order placed notification
 * 
 * @param int $userId The customer's user ID
 * @param int $orderId The order ID
 * @param float $total The order total
 * @return bool
 */
function notifyOrderPlaced($userId, $orderId, $total) {
    return sendNotification(
        $userId,
        'order_placed',
        'Order Placed Successfully',
        "Your order #{$orderId} for ₱" . number_format($total, 2) . " has been placed. We'll notify you when it ships."
    );
}

/**
 * Send an order shipped notification
 * 
 * @param int $userId The customer's user ID
 * @param int $orderId The order ID
 * @param string $trackingNumber Optional tracking number
 * @return bool
 */
function notifyOrderShipped($userId, $orderId, $trackingNumber = '') {
    $body = "Great news! Your order #{$orderId} has been shipped and is on its way to you.";
    if ($trackingNumber) {
        $body .= " Tracking number: {$trackingNumber}";
    }
    
    return sendNotification(
        $userId,
        'order_shipped',
        'Order Shipped',
        $body
    );
}

/**
 * Send an order delivered notification
 * 
 * @param int $userId The customer's user ID
 * @param int $orderId The order ID
 * @return bool
 */
function notifyOrderDelivered($userId, $orderId) {
    return sendNotification(
        $userId,
        'order_delivered',
        'Order Delivered',
        "Your order #{$orderId} has been delivered. Thank you for shopping with Fix&Go!"
    );
}

/**
 * Send a payment received notification
 * 
 * @param int $userId The user's ID
 * @param float $amount The payment amount
 * @param string $reference Optional payment reference
 * @return bool
 */
function notifyPaymentReceived($userId, $amount, $reference = '') {
    $body = "We've received your payment of ₱" . number_format($amount, 2) . ". Thank you!";
    if ($reference) {
        $body .= " Reference: {$reference}";
    }
    
    return sendNotification(
        $userId,
        'payment_received',
        'Payment Received',
        $body
    );
}

/**
 * Send a new message notification
 * 
 * @param int $userId The recipient's user ID
 * @param string $senderName The sender's name
 * @param string $preview Message preview (first few words)
 * @return bool
 */
function notifyNewMessage($userId, $senderName, $preview) {
    return sendNotification(
        $userId,
        'message',
        "New Message from {$senderName}",
        $preview
    );
}

/**
 * Send a promotion notification
 * 
 * @param int $userId The user's ID
 * @param string $title The promotion title
 * @param string $description The promotion description
 * @return bool
 */
function notifyPromotion($userId, $title, $description) {
    return sendNotification(
        $userId,
        'promotion',
        $title,
        $description
    );
}

/**
 * Send a promotion to all non-supervisor users
 * 
 * @param string $title The promotion title
 * @param string $description The promotion description
 * @return int Number of notifications sent
 */
function notifyPromotionToAll($title, $description) {
    try {
        $pdo = require __DIR__ . '/db.php';
        
        // Get all non-supervisor users
        $stmt = $pdo->query(
            "SELECT id FROM users WHERE role != 'supervisor' AND id IS NOT NULL"
        );
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return sendNotificationToMultiple($userIds, 'promotion', $title, $description);
    } catch (Exception $e) {
        error_log("Failed to send promotion to all: " . $e->getMessage());
        return 0;
    }
}

/**
 * Send a system notification
 * 
 * @param int $userId The user's ID
 * @param string $title The notification title
 * @param string $message The notification message
 * @return bool
 */
function notifySystem($userId, $title, $message) {
    return sendNotification(
        $userId,
        'system',
        $title,
        $message
    );
}

/**
 * Send a welcome notification to a new user
 * 
 * @param int $userId The new user's ID
 * @param string $firstName The user's first name
 * @return bool
 */
function notifyWelcome($userId, $firstName = '') {
    $greeting = $firstName ? "Welcome, {$firstName}!" : "Welcome to Fix&Go!";
    
    return sendNotification(
        $userId,
        'system',
        $greeting,
        "Thank you for joining Fix&Go! Explore our services, shop for quality phone accessories, and book repairs with our certified technicians."
    );
}

/**
 * Send an OTP notification
 * 
 * @param int $userId The user's ID
 * @param string $otp The OTP code
 * @return bool
 */
function notifyOTP($userId, $otp) {
    return sendNotification(
        $userId,
        'otp',
        'Your OTP Code',
        "Your one-time password is: {$otp}. This code will expire in 10 minutes. Do not share this code with anyone."
    );
}

/**
 * Delete old notifications (cleanup utility)
 * 
 * @param int $daysOld Delete notifications older than this many days
 * @return int Number of notifications deleted
 */
function deleteOldNotifications($daysOld = 30) {
    try {
        $pdo = require __DIR__ . '/db.php';
        
        $stmt = $pdo->prepare(
            "DELETE FROM notifications 
             WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)"
        );
        
        $stmt->execute([$daysOld]);
        return $stmt->rowCount();
    } catch (Exception $e) {
        error_log("Failed to delete old notifications: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get unread notification count for a user
 * 
 * @param int $userId The user's ID
 * @return int Number of unread notifications
 */
function getUnreadCount($userId) {
    try {
        $pdo = require __DIR__ . '/db.php';
        
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0"
        );
        
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Failed to get unread count: " . $e->getMessage());
        return 0;
    }
}
