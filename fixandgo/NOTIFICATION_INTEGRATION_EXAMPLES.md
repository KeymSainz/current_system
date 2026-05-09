# Notification Integration Examples

This document provides practical examples of how to integrate the notification system into various parts of the Fix&Go platform.

## Setup

First, include the notification helper in your PHP file:

```php
require_once __DIR__ . '/notification_helper.php';
```

## Example 1: Booking System Integration

### When a booking is created/confirmed:

```php
<?php
// In your booking confirmation code (e.g., backend/bookings.php)
require_once __DIR__ . '/notification_helper.php';

// After successfully creating a booking
if ($bookingCreated) {
    $bookingId = $pdo->lastInsertId();
    $customerId = $_SESSION['user_id'];
    $bookingDate = $_POST['booking_date'];
    
    // Send notification to customer
    notifyBookingConfirmed($customerId, $bookingId, $bookingDate);
    
    // Also notify the assigned technician
    if ($technicianId) {
        sendNotification(
            $technicianId,
            'booking_confirmed',
            'New Booking Assignment',
            "You have been assigned to booking #{$bookingId} scheduled for {$bookingDate}."
        );
    }
}
```

### When a booking is cancelled:

```php
<?php
// In your booking cancellation code
require_once __DIR__ . '/notification_helper.php';

if ($bookingCancelled) {
    $customerId = $booking['customer_id'];
    $bookingId = $booking['id'];
    $reason = $_POST['cancellation_reason'] ?? '';
    
    // Notify customer
    notifyBookingCancelled($customerId, $bookingId, $reason);
    
    // Notify technician if assigned
    if ($booking['technician_id']) {
        sendNotification(
            $booking['technician_id'],
            'booking_cancelled',
            'Booking Cancelled',
            "Booking #{$bookingId} has been cancelled by the customer."
        );
    }
}
```

## Example 2: Order System Integration

### When an order is placed:

```php
<?php
// In your order creation code (e.g., backend/orders.php)
require_once __DIR__ . '/notification_helper.php';

if ($orderCreated) {
    $orderId = $pdo->lastInsertId();
    $customerId = $_SESSION['user_id'];
    $orderTotal = $_POST['total'];
    
    // Notify customer
    notifyOrderPlaced($customerId, $orderId, $orderTotal);
    
    // Notify shop owner
    $stmt = $pdo->prepare("SELECT owner_id FROM shops WHERE id = ?");
    $stmt->execute([$shopId]);
    $ownerId = $stmt->fetchColumn();
    
    if ($ownerId) {
        sendNotification(
            $ownerId,
            'order_placed',
            'New Order Received',
            "You have received a new order #{$orderId} for ₱" . number_format($orderTotal, 2) . "."
        );
    }
}
```

### When an order is shipped:

```php
<?php
// In your order shipping code
require_once __DIR__ . '/notification_helper.php';

if ($orderShipped) {
    $customerId = $order['customer_id'];
    $orderId = $order['id'];
    $trackingNumber = $_POST['tracking_number'] ?? '';
    
    // Notify customer
    notifyOrderShipped($customerId, $orderId, $trackingNumber);
}
```

### When an order is delivered:

```php
<?php
// In your order delivery confirmation code
require_once __DIR__ . '/notification_helper.php';

if ($orderDelivered) {
    $customerId = $order['customer_id'];
    $orderId = $order['id'];
    
    // Notify customer
    notifyOrderDelivered($customerId, $orderId);
}
```

## Example 3: Payment System Integration

### When a payment is received:

```php
<?php
// In your payment processing code (e.g., backend/paymongo.php)
require_once __DIR__ . '/notification_helper.php';

if ($paymentSuccessful) {
    $customerId = $_SESSION['user_id'];
    $amount = $paymentIntent['amount'] / 100; // Convert from cents
    $reference = $paymentIntent['id'];
    
    // Notify customer
    notifyPaymentReceived($customerId, $amount, $reference);
    
    // If this is for an order, also update order status and notify
    if ($orderId) {
        sendNotification(
            $customerId,
            'order_placed',
            'Payment Confirmed',
            "Your payment for order #{$orderId} has been confirmed. We'll start processing your order shortly."
        );
    }
}
```

## Example 4: User Registration Integration

### When a new user registers:

```php
<?php
// In your registration code (e.g., backend/register.php)
require_once __DIR__ . '/notification_helper.php';

if ($registrationSuccessful) {
    $userId = $pdo->lastInsertId();
    $firstName = $_POST['first_name'];
    
    // Send welcome notification
    notifyWelcome($userId, $firstName);
}
```

## Example 5: Messaging System Integration

### When a new message is received:

```php
<?php
// In your messaging code (e.g., backend/messages.php)
require_once __DIR__ . '/notification_helper.php';

if ($messageSent) {
    $recipientId = $_POST['recipient_id'];
    $senderId = $_SESSION['user_id'];
    
    // Get sender's name
    $stmt = $pdo->prepare(
        "SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = ?"
    );
    $stmt->execute([$senderId]);
    $senderName = $stmt->fetchColumn();
    
    // Get message preview (first 50 characters)
    $messageBody = $_POST['message'];
    $preview = substr($messageBody, 0, 50);
    if (strlen($messageBody) > 50) {
        $preview .= '...';
    }
    
    // Notify recipient
    notifyNewMessage($recipientId, $senderName, $preview);
}
```

## Example 6: Admin Actions Integration

### When a seller application is approved:

```php
<?php
// In your admin approval code (e.g., backend/admin.php)
require_once __DIR__ . '/notification_helper.php';

if ($applicationApproved) {
    $applicantId = $application['user_id'];
    
    sendNotification(
        $applicantId,
        'system',
        'Application Approved',
        'Congratulations! Your seller application has been approved. You can now start selling on Fix&Go.'
    );
}
```

### When a seller application is rejected:

```php
<?php
// In your admin rejection code
require_once __DIR__ . '/notification_helper.php';

if ($applicationRejected) {
    $applicantId = $application['user_id'];
    $reason = $_POST['rejection_reason'] ?? 'Application did not meet requirements.';
    
    sendNotification(
        $applicantId,
        'system',
        'Application Rejected',
        "Unfortunately, your seller application has been rejected. Reason: {$reason}"
    );
}
```

## Example 7: Promotion System Integration

### Send a promotion to a specific user:

```php
<?php
// In your promotion code
require_once __DIR__ . '/notification_helper.php';

$userId = 123;
notifyPromotion(
    $userId,
    'Special Offer: 20% Off Phone Cases',
    'Get 20% off on all phone cases this week! Use code CASE20 at checkout. Offer valid until May 10, 2026.'
);
```

### Send a promotion to all users:

```php
<?php
// In your bulk promotion code
require_once __DIR__ . '/notification_helper.php';

$count = notifyPromotionToAll(
    'Flash Sale: 50% Off Screen Protectors',
    'Limited time offer! Get 50% off on all screen protectors today only. Use code SCREEN50 at checkout.'
);

echo "Promotion sent to {$count} users.";
```

## Example 8: OTP System Integration

### When sending an OTP:

```php
<?php
// In your OTP generation code (e.g., backend/otp.php)
require_once __DIR__ . '/notification_helper.php';

if ($otpGenerated) {
    $userId = $_SESSION['user_id'];
    $otp = generateOTP(); // Your OTP generation function
    
    // Send OTP via email (existing functionality)
    sendOTPEmail($userEmail, $otp);
    
    // Also send as in-app notification
    notifyOTP($userId, $otp);
}
```

## Example 9: Technician Assignment

### When a technician is assigned to a repair:

```php
<?php
// In your technician assignment code
require_once __DIR__ . '/notification_helper.php';

if ($technicianAssigned) {
    $technicianId = $_POST['technician_id'];
    $repairId = $_POST['repair_id'];
    $customerName = $repair['customer_name'];
    $deviceType = $repair['device_type'];
    
    sendNotification(
        $technicianId,
        'booking_confirmed',
        'New Repair Assignment',
        "You have been assigned to repair {$customerName}'s {$deviceType}. Repair ID: #{$repairId}"
    );
}
```

## Example 10: Inventory Alerts

### When inventory is low:

```php
<?php
// In your inventory monitoring code
require_once __DIR__ . '/notification_helper.php';

if ($inventoryLow) {
    $ownerId = $product['owner_id'];
    $productName = $product['item_description'];
    $currentQty = $product['qty'];
    
    sendNotification(
        $ownerId,
        'system',
        'Low Inventory Alert',
        "Your inventory for '{$productName}' is running low. Current quantity: {$currentQty}. Consider restocking soon."
    );
}
```

## Example 11: Scheduled Notifications

### Daily summary notification (run via cron job):

```php
<?php
// In a scheduled script (e.g., cron/daily_summary.php)
require_once __DIR__ . '/../backend/notification_helper.php';
require_once __DIR__ . '/../backend/db.php';

// Get all owners
$stmt = $pdo->query("SELECT id FROM users WHERE role = 'owner'");
$owners = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($owners as $ownerId) {
    // Get today's stats
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM orders 
         WHERE owner_id = ? AND DATE(created_at) = CURDATE()"
    );
    $stmt->execute([$ownerId]);
    $todayOrders = $stmt->fetchColumn();
    
    if ($todayOrders > 0) {
        sendNotification(
            $ownerId,
            'system',
            'Daily Summary',
            "You received {$todayOrders} new order(s) today. Check your dashboard for details."
        );
    }
}
```

## Example 12: Cleanup Old Notifications

### Run periodically to keep database clean:

```php
<?php
// In a scheduled cleanup script (e.g., cron/cleanup_notifications.php)
require_once __DIR__ . '/../backend/notification_helper.php';

// Delete notifications older than 90 days
$deleted = deleteOldNotifications(90);

echo "Deleted {$deleted} old notifications.\n";
```

## Best Practices

### 1. **Be Specific**
```php
// ❌ Bad
sendNotification($userId, 'system', 'Update', 'Something happened.');

// ✅ Good
sendNotification($userId, 'order_shipped', 'Order Shipped', 
    "Your order #12345 has been shipped with tracking number ABC123.");
```

### 2. **Include Relevant Details**
```php
// ✅ Include IDs, dates, amounts, names
notifyBookingConfirmed($userId, $bookingId, $bookingDate);
notifyPaymentReceived($userId, $amount, $reference);
```

### 3. **Use Appropriate Types**
```php
// Use the correct notification type for proper icon display
'booking_confirmed'  // For bookings
'order_placed'       // For orders
'payment_received'   // For payments
'message'            // For messages
'promotion'          // For promotions
'system'             // For general system messages
```

### 4. **Handle Errors Gracefully**
```php
// Notification failures shouldn't break your main flow
if (!notifyOrderPlaced($userId, $orderId, $total)) {
    error_log("Failed to send order notification to user {$userId}");
    // Continue with order processing
}
```

### 5. **Avoid Notification Spam**
```php
// Don't send too many notifications at once
// Consider batching or summarizing

// ❌ Bad - sends 10 notifications
foreach ($products as $product) {
    sendNotification($userId, 'system', 'Product Update', "Product {$product['name']} updated.");
}

// ✅ Good - sends 1 summary notification
$productNames = array_column($products, 'name');
$summary = implode(', ', $productNames);
sendNotification($userId, 'system', 'Products Updated', 
    count($products) . " products have been updated: {$summary}");
```

## Testing Your Integration

After integrating notifications, test with these steps:

1. **Trigger the action** (e.g., create a booking, place an order)
2. **Check the database** - Verify notification was created:
   ```sql
   SELECT * FROM notifications WHERE user_id = YOUR_USER_ID ORDER BY created_at DESC LIMIT 5;
   ```
3. **Check the UI** - Login as the user and verify:
   - Badge shows unread count
   - Notification appears in dropdown
   - Correct icon and text are displayed
4. **Test marking as read** - Click the notification and verify it's marked as read

## Troubleshooting

### Notification not appearing?
- Check if notification was created in database
- Verify user is logged in
- Verify user is not a supervisor
- Check browser console for errors

### Wrong icon showing?
- Verify you're using a valid notification type
- Check `getNotificationIcon()` function in index.html

### Notification sent to wrong user?
- Double-check the `$userId` parameter
- Verify user ID exists in users table

---

**Need Help?** Refer to `NOTIFICATIONS_GUIDE.md` for complete documentation.
