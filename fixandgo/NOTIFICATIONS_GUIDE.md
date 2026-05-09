# Fix&Go Notifications System Guide

## Overview

The Fix&Go platform now includes a comprehensive notifications system that allows users to receive real-time updates about bookings, orders, payments, and system messages. The notification bell icon appears in the navbar beside the "Technicians" link for all logged-in users except supervisors.

## Features

### 1. **Notification Bell Icon**
- Located in the navbar beside the "Technicians" link
- Shows a red badge with the count of unread notifications
- Visible to all users except supervisors
- Clicking the bell opens a dropdown with all notifications

### 2. **Notification Types**
The system supports various notification types:
- `booking_confirmed` - Booking confirmations
- `booking_cancelled` - Booking cancellations
- `order_placed` - New order notifications
- `order_shipped` - Shipping updates
- `order_delivered` - Delivery confirmations
- `payment_received` - Payment confirmations
- `message` - New messages
- `otp` - OTP codes
- `system` - System announcements
- `promotion` - Special offers and promotions

### 3. **Notification Dropdown**
- Clean, modern design matching the Fix&Go theme
- Shows up to 20 most recent notifications
- Displays notification icon, title, body, and time ago
- Unread notifications are highlighted with an orange accent
- Click on a notification to mark it as read
- "Mark all read" button to mark all notifications as read at once

### 4. **Real-time Updates**
- Unread count updates automatically every 30 seconds
- Badge shows "99+" for counts over 99
- Notifications are fetched fresh when dropdown is opened

## User Roles & Access

### Who Can See Notifications?
✅ **Customers** - Full access to notifications  
✅ **Owners** - Full access to notifications  
✅ **Suppliers** - Full access to notifications  
✅ **Sales Persons** - Full access to notifications  
✅ **Phone Technicians** - Full access to notifications  
❌ **Supervisors** - No access to notifications (per requirements)

## Backend API

### Endpoints

#### 1. **List Notifications**
```
GET /backend/notifications.php?action=list&limit=20&offset=0
```
Returns a list of notifications for the logged-in user.

**Response:**
```json
{
  "success": true,
  "notifications": [
    {
      "id": 1,
      "type": "booking_confirmed",
      "title": "Booking Confirmed",
      "body": "Your phone repair booking has been confirmed.",
      "is_read": 0,
      "created_at": "2026-05-03 10:30:00"
    }
  ],
  "total": 5,
  "limit": 20,
  "offset": 0
}
```

#### 2. **Get Unread Count**
```
GET /backend/notifications.php?action=unread
```
Returns the count of unread notifications.

**Response:**
```json
{
  "success": true,
  "unread_count": 3
}
```

#### 3. **Mark as Read**
```
POST /backend/notifications.php?action=mark_read
Content-Type: application/json

{
  "ids": [1, 2, 3]
}
```
Marks specific notifications as read.

**Response:**
```json
{
  "success": true,
  "message": "Notification(s) marked as read.",
  "affected": 3
}
```

#### 4. **Mark All as Read**
```
POST /backend/notifications.php?action=mark_all_read
Content-Type: application/json

{}
```
Marks all notifications as read for the logged-in user.

**Response:**
```json
{
  "success": true,
  "message": "All notifications marked as read.",
  "affected": 5
}
```

#### 5. **Delete Notifications**
```
POST /backend/notifications.php?action=delete
Content-Type: application/json

{
  "ids": [1, 2, 3]
}
```
Deletes specific notifications.

**Response:**
```json
{
  "success": true,
  "message": "Notification(s) deleted.",
  "affected": 3
}
```

## Database Schema

The notifications are stored in the `notifications` table:

```sql
CREATE TABLE IF NOT EXISTS notifications (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NOT NULL,
  type        VARCHAR(50)  NOT NULL,
  title       VARCHAR(150) NOT NULL,
  body        TEXT         NOT NULL,
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  INDEX idx_user_read (user_id, is_read),
  CONSTRAINT fk_notif_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Creating Notifications Programmatically

To create a notification from your PHP code:

```php
<?php
require_once __DIR__ . '/db.php';

function createNotification($userId, $type, $title, $body) {
    global $pdo;
    
    $stmt = $pdo->prepare(
        "INSERT INTO notifications (user_id, type, title, body, created_at)
         VALUES (?, ?, ?, ?, NOW())"
    );
    
    return $stmt->execute([$userId, $type, $title, $body]);
}

// Example usage:
createNotification(
    123,                          // user_id
    'booking_confirmed',          // type
    'Booking Confirmed',          // title
    'Your repair booking is confirmed for May 5, 2026.' // body
);
```

## Testing

### Create Test Notifications

Run the test notification creator script:

```
http://your-domain/fixandgo/backend/create_test_notifications.php
```

This will create 2-3 sample notifications for each non-supervisor user in your database.

### Manual Testing Steps

1. **Login as a non-supervisor user** (customer, owner, supplier, etc.)
2. **Check the navbar** - You should see a bell icon beside "Technicians"
3. **Create test notifications** - Run the test script above
4. **Refresh the page** - The bell should show a red badge with the count
5. **Click the bell** - The dropdown should open with your notifications
6. **Click a notification** - It should be marked as read (orange highlight removed)
7. **Click "Mark all read"** - All notifications should be marked as read
8. **Login as a supervisor** - The bell icon should NOT appear

## UI/UX Features

### Visual Indicators
- **Red badge** - Shows unread count
- **Orange accent bar** - Left border on unread notifications
- **Orange background** - Subtle highlight on unread items
- **Icon per type** - Each notification type has a unique icon
- **Time ago** - Human-readable timestamps (e.g., "2h ago", "3d ago")

### Interactions
- **Click notification** - Marks as read
- **Click "Mark all read"** - Marks all as read
- **Click outside** - Closes the dropdown
- **Auto-refresh** - Unread count updates every 30 seconds

### Responsive Design
- Dropdown width adjusts on mobile devices
- Maximum width of 380px on desktop
- Scrollable list for many notifications
- Fixed position dropdown for easy access

## Integration Points

### Where to Add Notification Triggers

1. **Booking System** - When bookings are created, confirmed, or cancelled
2. **Order System** - When orders are placed, shipped, or delivered
3. **Payment System** - When payments are received or refunded
4. **Messaging System** - When new messages arrive
5. **Admin Actions** - When admins approve/reject applications
6. **Promotions** - When new offers are available

### Example Integration

```php
// In your booking confirmation code:
if ($bookingConfirmed) {
    createNotification(
        $customerId,
        'booking_confirmed',
        'Booking Confirmed',
        "Your repair booking #{$bookingId} has been confirmed for {$bookingDate}."
    );
}
```

## Troubleshooting

### Notifications Not Showing
1. Check if user is logged in
2. Verify user role is not 'supervisor'
3. Check browser console for JavaScript errors
4. Verify backend API is accessible

### Badge Not Updating
1. Check if polling is working (every 30 seconds)
2. Verify API endpoint returns correct count
3. Check network tab for failed requests

### Dropdown Not Opening
1. Check if JavaScript is loaded
2. Verify no console errors
3. Check if dropdown element exists in DOM

## Future Enhancements

Potential improvements for the notification system:

1. **Push Notifications** - Browser push notifications for real-time alerts
2. **Email Notifications** - Send important notifications via email
3. **SMS Notifications** - Send critical alerts via SMS
4. **Notification Preferences** - Let users choose which notifications to receive
5. **Notification History** - Archive old notifications
6. **Rich Notifications** - Add images, buttons, and actions to notifications
7. **Notification Categories** - Group notifications by category
8. **Sound Alerts** - Play sound when new notification arrives

## Support

For issues or questions about the notification system, please contact the development team or refer to the main Fix&Go documentation.

---

**Last Updated:** May 3, 2026  
**Version:** 1.0.0
