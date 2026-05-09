<?php
/**
 * Fix&Go — Create Test Notifications
 * Helper script to create sample notifications for testing
 * 
 * Usage: Run this script directly in the browser or via CLI
 */

require_once __DIR__ . '/db.php';

try {
    // Get all non-supervisor users
    $stmt = $pdo->query(
        "SELECT id, email, role, first_name, last_name 
         FROM users 
         WHERE role != 'supervisor' 
         ORDER BY id ASC 
         LIMIT 10"
    );
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($users)) {
        echo "No users found to create notifications for.\n";
        exit;
    }

    // Sample notification templates
    $templates = [
        [
            'type' => 'booking_confirmed',
            'title' => 'Booking Confirmed',
            'body' => 'Your phone repair booking has been confirmed. Our technician will contact you shortly.'
        ],
        [
            'type' => 'order_placed',
            'title' => 'Order Placed Successfully',
            'body' => 'Your order for phone accessories has been placed. We\'ll notify you when it ships.'
        ],
        [
            'type' => 'order_shipped',
            'title' => 'Order Shipped',
            'body' => 'Great news! Your order has been shipped and is on its way to you.'
        ],
        [
            'type' => 'payment_received',
            'title' => 'Payment Received',
            'body' => 'We\'ve received your payment. Thank you for your purchase!'
        ],
        [
            'type' => 'promotion',
            'title' => 'Special Offer: 20% Off',
            'body' => 'Get 20% off on all phone cases this week! Use code CASE20 at checkout.'
        ],
        [
            'type' => 'system',
            'title' => 'Welcome to Fix&Go',
            'body' => 'Thank you for joining Fix&Go! Explore our services and shop for quality phone accessories.'
        ],
        [
            'type' => 'message',
            'title' => 'New Message',
            'body' => 'You have a new message from our support team regarding your recent inquiry.'
        ]
    ];

    $insertStmt = $pdo->prepare(
        "INSERT INTO notifications (user_id, type, title, body, is_read, created_at)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $count = 0;
    foreach ($users as $user) {
        // Create 2-3 notifications per user
        $numNotifications = rand(2, 3);
        
        for ($i = 0; $i < $numNotifications; $i++) {
            $template = $templates[array_rand($templates)];
            
            // Randomly mark some as read
            $isRead = rand(0, 2) === 0 ? 1 : 0;
            
            // Create timestamps in the past (last 7 days)
            $daysAgo = rand(0, 7);
            $hoursAgo = rand(0, 23);
            $minutesAgo = rand(0, 59);
            $createdAt = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days -{$hoursAgo} hours -{$minutesAgo} minutes"));
            
            $insertStmt->execute([
                $user['id'],
                $template['type'],
                $template['title'],
                $template['body'],
                $isRead,
                $createdAt
            ]);
            
            $count++;
        }
    }

    echo "✓ Successfully created {$count} test notifications for " . count($users) . " users.\n";
    echo "\nUsers with notifications:\n";
    foreach ($users as $user) {
        $name = trim($user['first_name'] . ' ' . $user['last_name']) ?: $user['email'];
        echo "  - {$name} ({$user['role']})\n";
    }

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
