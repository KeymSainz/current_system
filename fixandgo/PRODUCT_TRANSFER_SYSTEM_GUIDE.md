# Product Transfer System - Implementation Guide

## Overview

This system enables a hierarchical product flow:
1. **Supplier** → **Owner** (existing functionality)
2. **Owner** → **Supervisor** (NEW)
3. **Supervisor** → **Sales Person** (NEW)

## Database Changes

### New Tables Created

#### 1. `product_transfers`
Tracks individual transfer requests between users.

**Columns:**
- `id` - Transfer ID
- `product_id` - Reference to supplier_products
- `from_user_id` - Sender (owner or supervisor)
- `to_user_id` - Recipient (supervisor or sales_person)
- `transfer_type` - 'owner_to_supervisor' or 'supervisor_to_sales'
- `quantity` - Number of units being transferred
- `status` - 'pending', 'accepted', 'rejected'
- `notes` - Optional transfer notes
- `transferred_at` - When transfer was initiated
- `responded_at` - When recipient accepted/rejected

#### 2. `staff_assignments`
Links supervisors and sales persons to their owner.

**Columns:**
- `id` - Assignment ID
- `owner_id` - The shop owner
- `staff_id` - Supervisor or sales person user ID
- `staff_role` - 'supervisor' or 'sales_person'
- `assigned_at` - When staff was assigned
- `is_active` - Whether assignment is active

#### 3. `product_transfer_history`
Audit trail of all product movements.

**Columns:**
- `id` - History entry ID
- `product_id` - Product being transferred
- `from_user_id` - Sender (NULL for initial supplier receipt)
- `to_user_id` - Recipient
- `action` - 'received_from_supplier', 'sent_to_supervisor', 'sent_to_sales', 'accepted', 'rejected'
- `quantity` - Number of units
- `notes` - Optional notes
- `created_at` - Timestamp

### Modified Tables

#### `supplier_products`
Added columns:
- `current_holder_id` - INT UNSIGNED NULL - Who currently has the product
- `holder_type` - ENUM('owner', 'supervisor', 'sales_person') NULL - Type of current holder

## API Endpoints

### Base URL
`fixandgo/backend/product_transfers.php`

### GET Endpoints

#### 1. Get Staff List
**Endpoint:** `?action=get_staff_list&role=supervisor|sales_person`

**Access:** Owner (for supervisors), Supervisor (for sales persons)

**Response:**
```json
{
  "success": true,
  "staff": [
    {
      "id": 123,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "is_active": 1,
      "assigned_at": "2026-05-01 10:00:00"
    }
  ],
  "count": 1
}
```

#### 2. Get My Products
**Endpoint:** `?action=get_my_products`

**Access:** Owner, Supervisor, Sales Person

**Response:**
```json
{
  "success": true,
  "products": [
    {
      "id": 456,
      "category": "Screen",
      "brand": "Samsung",
      "item_description": "Galaxy S22 Screen Replacement",
      "qty": 10,
      "srp": 1500.00,
      "image_path": "uploads/products/abc.jpg",
      "status": "owner_received",
      "holder_type": "owner",
      "supplier_first_name": "Jane",
      "supplier_last_name": "Smith",
      "supplier_email": "supplier@example.com"
    }
  ],
  "count": 1
}
```

#### 3. Get Pending Transfers
**Endpoint:** `?action=get_pending_transfers`

**Access:** Supervisor, Sales Person

**Response:**
```json
{
  "success": true,
  "transfers": [
    {
      "id": 789,
      "product_id": 456,
      "quantity": 5,
      "status": "pending",
      "notes": "Urgent delivery needed",
      "transferred_at": "2026-05-06 14:30:00",
      "transfer_type": "owner_to_supervisor",
      "category": "Screen",
      "brand": "Samsung",
      "item_description": "Galaxy S22 Screen Replacement",
      "srp": 1500.00,
      "from_first_name": "Owner",
      "from_last_name": "Name",
      "from_email": "owner@example.com",
      "from_role": "owner"
    }
  ],
  "count": 1
}
```

#### 4. Get Transfer History
**Endpoint:** `?action=get_transfer_history&product_id=456`

**Access:** Owner, Supervisor, Sales Person, Supplier (if they own/are involved with the product)

**Response:**
```json
{
  "success": true,
  "history": [
    {
      "id": 1,
      "action": "received_from_supplier",
      "quantity": 10,
      "notes": "Initial delivery",
      "created_at": "2026-05-01 10:00:00",
      "from_first_name": "Supplier",
      "from_last_name": "Name",
      "from_email": "supplier@example.com",
      "from_role": "supplier",
      "to_first_name": "Owner",
      "to_last_name": "Name",
      "to_email": "owner@example.com",
      "to_role": "owner"
    },
    {
      "id": 2,
      "action": "sent_to_supervisor",
      "quantity": 5,
      "notes": "For branch A",
      "created_at": "2026-05-06 14:30:00",
      "from_first_name": "Owner",
      "from_last_name": "Name",
      "from_role": "owner",
      "to_first_name": "Supervisor",
      "to_last_name": "Name",
      "to_role": "supervisor"
    }
  ],
  "count": 2
}
```

### POST Endpoints

#### 1. Send to Staff
**Endpoint:** POST with `action=send_to_staff`

**Access:** Owner (to supervisor), Supervisor (to sales person)

**Request Body:**
```json
{
  "action": "send_to_staff",
  "product_id": 456,
  "to_user_id": 123,
  "quantity": 5,
  "notes": "Urgent delivery needed"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Product sent successfully to John Doe.",
  "transfer_id": 789
}
```

#### 2. Accept Transfer
**Endpoint:** POST with `action=accept_transfer`

**Access:** Supervisor, Sales Person

**Request Body:**
```json
{
  "action": "accept_transfer",
  "transfer_id": 789
}
```

**Response:**
```json
{
  "success": true,
  "message": "Transfer accepted successfully."
}
```

#### 3. Reject Transfer
**Endpoint:** POST with `action=reject_transfer`

**Access:** Supervisor, Sales Person

**Request Body:**
```json
{
  "action": "reject_transfer",
  "transfer_id": 789,
  "reason": "Insufficient storage space"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Transfer rejected successfully."
}
```

## Installation Steps

### 1. Run Database Migration

```bash
# Using MySQL command line
mysql -u root -p fixandgo < fixandgo/backend/migrate_product_transfers.sql

# OR import via phpMyAdmin
# 1. Open phpMyAdmin
# 2. Select 'fixandgo' database
# 3. Go to Import tab
# 4. Choose file: migrate_product_transfers.sql
# 5. Click Go
```

### 2. Verify Tables Created

```sql
-- Check if tables exist
SHOW TABLES LIKE 'product_transfers';
SHOW TABLES LIKE 'staff_assignments';
SHOW TABLES LIKE 'product_transfer_history';

-- Check if columns added to supplier_products
DESCRIBE supplier_products;
```

### 3. Assign Staff to Owner

Before owners can send products to supervisors, you need to assign staff:

```sql
-- Example: Assign supervisor to owner
INSERT INTO staff_assignments (owner_id, staff_id, staff_role)
VALUES (
  (SELECT id FROM users WHERE email = 'owner@example.com'),
  (SELECT id FROM users WHERE email = 'supervisor@example.com'),
  'supervisor'
);

-- Example: Assign sales person to owner
INSERT INTO staff_assignments (owner_id, staff_id, staff_role)
VALUES (
  (SELECT id FROM users WHERE email = 'owner@example.com'),
  (SELECT id FROM users WHERE email = 'sales@example.com'),
  'sales_person'
);
```

## Frontend Integration Examples

### Owner Dashboard - Send to Supervisor

```javascript
// 1. Get list of supervisors
fetch('../../../backend/product_transfers.php?action=get_staff_list&role=supervisor', {
  credentials: 'include'
})
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Populate dropdown with supervisors
      const select = document.getElementById('supervisorSelect');
      data.staff.forEach(supervisor => {
        const option = document.createElement('option');
        option.value = supervisor.id;
        option.textContent = `${supervisor.first_name} ${supervisor.last_name}`;
        select.appendChild(option);
      });
    }
  });

// 2. Send product to selected supervisor
function sendToSupervisor(productId, supervisorId, quantity, notes) {
  fetch('../../../backend/product_transfers.php', {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      action: 'send_to_staff',
      product_id: productId,
      to_user_id: supervisorId,
      quantity: quantity,
      notes: notes
    })
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        // Refresh product list
      } else {
        alert('Error: ' + data.message);
      }
    });
}
```

### Supervisor Dashboard - View Pending Transfers

```javascript
// Get pending transfers
fetch('../../../backend/product_transfers.php?action=get_pending_transfers', {
  credentials: 'include'
})
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Display pending transfers
      data.transfers.forEach(transfer => {
        console.log(`Transfer from ${transfer.from_first_name}: ${transfer.item_description} (${transfer.quantity} units)`);
      });
    }
  });

// Accept transfer
function acceptTransfer(transferId) {
  fetch('../../../backend/product_transfers.php', {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      action: 'accept_transfer',
      transfer_id: transferId
    })
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert('Transfer accepted!');
        // Refresh transfers list
      }
    });
}

// Reject transfer
function rejectTransfer(transferId, reason) {
  fetch('../../../backend/product_transfers.php', {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      action: 'reject_transfer',
      transfer_id: transferId,
      reason: reason
    })
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert('Transfer rejected!');
        // Refresh transfers list
      }
    });
}
```

### Supervisor Dashboard - Send to Sales Person

```javascript
// 1. Get list of sales persons
fetch('../../../backend/product_transfers.php?action=get_staff_list&role=sales_person', {
  credentials: 'include'
})
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Populate dropdown with sales persons
      const select = document.getElementById('salesPersonSelect');
      data.staff.forEach(sales => {
        const option = document.createElement('option');
        option.value = sales.id;
        option.textContent = `${sales.first_name} ${sales.last_name}`;
        select.appendChild(option);
      });
    }
  });

// 2. Send product to selected sales person
function sendToSalesPerson(productId, salesPersonId, quantity, notes) {
  fetch('../../../backend/product_transfers.php', {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      action: 'send_to_staff',
      product_id: productId,
      to_user_id: salesPersonId,
      quantity: quantity,
      notes: notes
    })
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        // Refresh product list
      } else {
        alert('Error: ' + data.message);
      }
    });
}
```

## Workflow Examples

### Example 1: Owner Sends to Supervisor

1. **Owner** views their inventory (products with `current_holder_id = owner_id`)
2. **Owner** clicks "Send to Supervisor" on a product
3. **Owner** selects supervisor from dropdown (populated from `staff_assignments`)
4. **Owner** enters quantity and optional notes
5. **Owner** clicks "Send"
6. System creates `product_transfers` record with `status = 'pending'`
7. System adds entry to `product_transfer_history`
8. **Supervisor** receives notification
9. **Supervisor** views pending transfers
10. **Supervisor** clicks "Accept" or "Reject"
11. If accepted:
    - `product_transfers.status` → 'accepted'
    - `supplier_products.current_holder_id` → supervisor_id
    - `supplier_products.holder_type` → 'supervisor'
    - History entry added
    - Owner receives notification

### Example 2: Supervisor Sends to Sales Person

1. **Supervisor** views their inventory (products with `current_holder_id = supervisor_id`)
2. **Supervisor** clicks "Send to Sales Person" on a product
3. **Supervisor** selects sales person from dropdown
4. **Supervisor** enters quantity and optional notes
5. **Supervisor** clicks "Send"
6. System creates `product_transfers` record with `status = 'pending'`
7. System adds entry to `product_transfer_history`
8. **Sales Person** receives notification
9. **Sales Person** views pending transfers
10. **Sales Person** clicks "Accept" or "Reject"
11. If accepted:
    - `product_transfers.status` → 'accepted'
    - `supplier_products.current_holder_id` → sales_person_id
    - `supplier_products.holder_type` → 'sales_person'
    - History entry added
    - Supervisor receives notification

## Security Features

1. **Role-Based Access Control:**
   - Owners can only send to supervisors
   - Supervisors can only send to sales persons
   - Users can only view/manage their own products

2. **Validation:**
   - Quantity checks (can't send more than available)
   - Ownership verification (can't send products you don't have)
   - Role verification (recipient must have correct role)

3. **Audit Trail:**
   - All transfers logged in `product_transfer_history`
   - Timestamps for all actions
   - Notes preserved for accountability

## Testing Checklist

- [ ] Database migration runs without errors
- [ ] Tables created successfully
- [ ] Existing products updated with `current_holder_id`
- [ ] Owner can view list of supervisors
- [ ] Owner can send product to supervisor
- [ ] Supervisor receives notification
- [ ] Supervisor can view pending transfers
- [ ] Supervisor can accept transfer
- [ ] Product `current_holder_id` updates correctly
- [ ] Supervisor can view list of sales persons
- [ ] Supervisor can send product to sales person
- [ ] Sales person receives notification
- [ ] Sales person can accept transfer
- [ ] Transfer history displays correctly
- [ ] Rejection workflow works
- [ ] Quantity validation works
- [ ] Role validation works

## Next Steps

1. **Create UI Components:**
   - Owner inventory page with "Send to Supervisor" button
   - Supervisor inventory page with "Send to Sales Person" button
   - Pending transfers page for supervisors and sales persons
   - Transfer history modal/page

2. **Add Staff Management:**
   - Owner can add/remove supervisors
   - Owner can add/remove sales persons
   - Staff assignment UI

3. **Enhance Features:**
   - Bulk transfers (send multiple products at once)
   - Transfer requests (staff can request products from owner/supervisor)
   - Low stock alerts
   - Transfer analytics/reports

## Date
May 6, 2026
