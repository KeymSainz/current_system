# Quick Guide: Assign Supervisors to Owner

## Problem
You registered 2 supervisors but when clicking "Send to Supervisor", no supervisors appear in the dropdown.

## Cause
The supervisors are not **assigned** to the owner in the `staff_assignments` table.

## Solution

### Step 1: Check Current Assignments

Open this page in your browser:
```
http://localhost/current_system/fixandgo/backend/check_staff_assignments.php
```

This will show you:
- Which supervisors are assigned to which owners
- Which supervisors are NOT assigned to any owner
- SQL commands to fix the assignments

### Step 2: Assign Supervisors to Owner

Run these SQL commands in phpMyAdmin or MySQL command line:

#### Option A: If you know the emails

```sql
-- Assign first supervisor to owner
INSERT INTO staff_assignments (owner_id, staff_id, staff_role)
VALUES (
  (SELECT id FROM users WHERE email = 'owner@example.com' AND role = 'owner'),
  (SELECT id FROM users WHERE email = 'supervisor1@example.com' AND role = 'supervisor'),
  'supervisor'
);

-- Assign second supervisor to owner
INSERT INTO staff_assignments (owner_id, staff_id, staff_role)
VALUES (
  (SELECT id FROM users WHERE email = 'owner@example.com' AND role = 'owner'),
  (SELECT id FROM users WHERE email = 'supervisor2@example.com' AND role = 'supervisor'),
  'supervisor'
);
```

**Replace:**
- `owner@example.com` with your actual owner email
- `supervisor1@example.com` with your first supervisor's email
- `supervisor2@example.com` with your second supervisor's email

#### Option B: If you know the user IDs

```sql
-- Example: Owner ID = 10, Supervisor IDs = 20, 21
INSERT INTO staff_assignments (owner_id, staff_id, staff_role) VALUES (10, 20, 'supervisor');
INSERT INTO staff_assignments (owner_id, staff_id, staff_role) VALUES (10, 21, 'supervisor');
```

### Step 3: Verify Assignments

Refresh the check page:
```
http://localhost/current_system/fixandgo/backend/check_staff_assignments.php
```

You should now see the supervisors listed under the owner.

### Step 4: Test Send to Supervisor

1. Log in as the owner
2. Go to Manage Products page
3. Select one or more products (check the checkbox)
4. Click "Send to Supervisor" button
5. A modal should appear with a dropdown showing your 2 supervisors
6. Select a supervisor, enter quantity, and click "Send Products"

## Finding User IDs and Emails

If you don't know the emails or IDs, run this query:

```sql
-- Get all owners
SELECT id, first_name, last_name, email, role FROM users WHERE role = 'owner';

-- Get all supervisors
SELECT id, first_name, last_name, email, role FROM users WHERE role = 'supervisor';

-- Get all sales persons
SELECT id, first_name, last_name, email, role FROM users WHERE role = 'sales_person';
```

## Assign Sales Persons (Optional)

If you also want supervisors to send products to sales persons:

```sql
-- Assign sales person to owner
INSERT INTO staff_assignments (owner_id, staff_id, staff_role)
VALUES (
  (SELECT id FROM users WHERE email = 'owner@example.com' AND role = 'owner'),
  (SELECT id FROM users WHERE email = 'sales@example.com' AND role = 'sales_person'),
  'sales_person'
);
```

## Common Issues

### Issue: "No supervisors assigned to you"

**Cause:** The `staff_assignments` table is empty or supervisors are not assigned to your owner account.

**Solution:** Follow Step 2 above to assign supervisors.

### Issue: "Table 'staff_assignments' doesn't exist"

**Cause:** You haven't run the migration yet.

**Solution:** Run the migration:
```bash
mysql -u root -p fixandgo < fixandgo/backend/migrate_product_transfers.sql
```

### Issue: Duplicate entry error when inserting

**Cause:** The supervisor is already assigned to that owner.

**Solution:** Check existing assignments:
```sql
SELECT * FROM staff_assignments WHERE owner_id = YOUR_OWNER_ID;
```

To remove and re-add:
```sql
DELETE FROM staff_assignments WHERE owner_id = YOUR_OWNER_ID AND staff_id = YOUR_SUPERVISOR_ID;
-- Then run the INSERT again
```

## What Happens After Assignment

Once supervisors are assigned:

1. **Owner** can see the supervisors in the dropdown when clicking "Send to Supervisor"
2. **Owner** can send products to specific supervisors
3. **Supervisor** receives a notification
4. **Supervisor** can view pending transfers in their dashboard
5. **Supervisor** can accept or reject the transfer
6. If accepted, the product's `current_holder_id` changes to the supervisor
7. **Supervisor** can then send products to sales persons (if assigned)

## Date
May 6, 2026
