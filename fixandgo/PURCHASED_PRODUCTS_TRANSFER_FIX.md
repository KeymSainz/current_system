# Purchased Products Transfer Fix

## ❌ Issue

When owners tried to send purchased products to supervisors, they got the error:
```
"Product not found or you do not own it"
```

## 🔍 Root Cause

When owners purchase products from suppliers via PayMongo, new product entries are created in the `supplier_products` table. However, these new entries were missing:
- `current_holder_id` (should be set to owner's user ID)
- `holder_type` (should be set to 'owner')

The product transfer system checks these fields to verify ownership, so without them, the transfer fails.

## ✅ Solution Applied

### 1. Fixed Future Purchases

**Files Modified:**
- `fixandgo/backend/paymongo_return.php`
- `fixandgo/backend/paymongo_webhook.php`

**Changes:**
- Added `current_holder_id` and `holder_type` to the INSERT statement
- Set `current_holder_id = owner_id`
- Set `holder_type = 'owner'`

**Before:**
```sql
INSERT INTO supplier_products
(supplier_id, category, brand, item_description, qty, srp,
 image_path, notes, status, verified_at, created_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, "verified", NOW(), NOW())
```

**After:**
```sql
INSERT INTO supplier_products
(supplier_id, category, brand, item_description, qty, srp,
 image_path, notes, status, current_holder_id, holder_type, verified_at, created_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, "verified", ?, "owner", NOW(), NOW())
```

### 2. Fixed Existing Purchased Products

**File Created:**
`fixandgo/backend/fix_purchased_products_holder.sql`

This SQL script updates all existing purchased products to set:
- `current_holder_id` = `supplier_id` (which is the owner's ID for purchased products)
- `holder_type` = 'owner'

## 🔧 How to Apply the Fix

### Step 1: Run the SQL Script

Execute the SQL script to fix existing products:

```bash
# Option 1: Using MySQL command line
mysql -u your_username -p fixandgo < fixandgo/backend/fix_purchased_products_holder.sql

# Option 2: Using phpMyAdmin
# - Open phpMyAdmin
# - Select 'fixandgo' database
# - Go to SQL tab
# - Copy and paste the contents of fix_purchased_products_holder.sql
# - Click "Go"
```

### Step 2: Verify the Fix

Check that products were updated:

```sql
SELECT 
    id,
    item_description,
    supplier_id AS owner_id,
    current_holder_id,
    holder_type,
    qty
FROM supplier_products
WHERE notes LIKE '%Purchased from supplier%'
LIMIT 10;
```

**Expected Result:**
- `current_holder_id` should match `supplier_id`
- `holder_type` should be 'owner'

### Step 3: Test the Transfer

1. **As Owner**: Go to "Manage Products" page
2. Select a purchased product (one with "Purchased" label)
3. Click "Send to Supervisor"
4. Choose a supervisor and quantity
5. Click "Send Products"
6. **Expected**: Transfer should succeed without errors

## 📊 What the Fix Does

### For New Purchases:
```
Owner buys product from supplier
    ↓
PayMongo processes payment
    ↓
New product entry created with:
    - supplier_id = owner_id
    - current_holder_id = owner_id ✅ (NEW)
    - holder_type = 'owner' ✅ (NEW)
    - status = 'verified'
    - notes = 'Purchased from supplier...'
```

### For Existing Purchases:
```
SQL script finds products with:
    - notes LIKE '%Purchased from supplier%'
    - current_holder_id IS NULL
    - holder_type IS NULL
    ↓
Updates them to set:
    - current_holder_id = supplier_id
    - holder_type = 'owner'
```

## 🧪 Testing Checklist

- [ ] Run the SQL script to fix existing products
- [ ] Verify products have `current_holder_id` and `holder_type` set
- [ ] Try to send a purchased product to supervisor
- [ ] Verify transfer succeeds without errors
- [ ] Check supervisor inventory to confirm product appears
- [ ] Purchase a new product and verify it has holder info set automatically

## 🔍 Verification Queries

### Check if products need fixing:
```sql
SELECT COUNT(*) AS needs_fixing
FROM supplier_products
WHERE notes LIKE '%Purchased from supplier%'
  AND (current_holder_id IS NULL OR holder_type IS NULL);
```

### Check fixed products:
```sql
SELECT 
    id,
    item_description,
    supplier_id,
    current_holder_id,
    holder_type,
    SUBSTRING(notes, 1, 50) AS notes_preview
FROM supplier_products
WHERE notes LIKE '%Purchased from supplier%'
  AND current_holder_id IS NOT NULL
  AND holder_type = 'owner'
LIMIT 20;
```

### Check transfer history:
```sql
SELECT 
    pt.id,
    pt.transfer_type,
    pt.quantity,
    pt.status,
    pt.transferred_at,
    sp.item_description,
    u_from.email AS from_email,
    u_to.email AS to_email
FROM product_transfers pt
JOIN supplier_products sp ON sp.id = pt.product_id
JOIN users u_from ON u_from.id = pt.from_user_id
JOIN users u_to ON u_to.id = pt.to_user_id
WHERE pt.transfer_type = 'owner_to_supervisor'
ORDER BY pt.id DESC
LIMIT 10;
```

## 🐛 Troubleshooting

### Error still appears after running SQL:
1. Verify SQL script executed successfully
2. Check if products have holder info: `SELECT * FROM supplier_products WHERE id = [product_id]`
3. Verify owner's user ID matches `current_holder_id`
4. Clear browser cache and refresh page

### Transfer fails with different error:
1. Check if supervisor exists and is active
2. Verify product has sufficient quantity
3. Check browser console for detailed error message
4. Check PHP error logs

### Products don't appear in supervisor inventory:
1. Verify transfer succeeded (check `product_transfers` table)
2. Check if supervisor is logged in correctly
3. Hard refresh supervisor inventory page (Ctrl+F5)
4. Verify supervisor's user ID matches `current_holder_id` of transferred product

## 📝 Summary

This fix ensures that:
- ✅ All purchased products have proper ownership tracking
- ✅ Owners can transfer purchased products to supervisors
- ✅ Future purchases automatically set holder information
- ✅ Existing purchased products are retroactively fixed
- ✅ The complete transfer chain works: Supplier → Owner → Supervisor → Sales Person

## 🔗 Related Files

- `fixandgo/backend/paymongo_return.php` - Payment success handler (FIXED)
- `fixandgo/backend/paymongo_webhook.php` - Payment webhook handler (FIXED)
- `fixandgo/backend/fix_purchased_products_holder.sql` - SQL fix script (NEW)
- `fixandgo/backend/product_transfers.php` - Transfer API (uses holder info)
