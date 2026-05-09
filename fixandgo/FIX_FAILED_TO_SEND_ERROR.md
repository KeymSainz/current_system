# Fix "Failed to Send" Error

**Date:** May 6, 2026  
**Issue:** "2 product(s) failed to send" error when trying to send products to supervisor

---

## 🔍 Root Cause

The error occurs because products in the `supplier_products` table don't have the `current_holder_id` field set. The transfer system needs to know who currently owns each product.

**Why this happens:**
- The `current_holder_id` column was added in the migration
- But existing products weren't updated with this value
- When you try to send a product, the system checks: `WHERE sp.id = ? AND sp.current_holder_id = ?`
- If `current_holder_id` is NULL, the product is not found → Transfer fails

---

## ✅ Solution (3 Options)

### Option 1: Auto-Fix via Web Interface (Easiest)

1. **Run the diagnostic:**
   ```
   http://your-domain/fixandgo/backend/debug_product_transfer_error.php
   ```

2. **Review the output:**
   - Check if `current_holder_id` column exists
   - See how many products have NULL `current_holder_id`
   - View your products and supervisors

3. **Click the "Auto-Fix Products" button**
   - This will automatically set `current_holder_id` for all your products
   - Takes 1 second to complete

4. **Done!** Go back to Manage Products and try sending again

---

### Option 2: Run SQL Script (Recommended)

1. **Open your database tool** (phpMyAdmin, MySQL Workbench, etc.)

2. **Run this SQL:**
   ```sql
   USE fixandgo;
   
   -- Set current_holder_id from product_submissions
   UPDATE supplier_products sp
   JOIN submission_items si ON si.product_id = sp.id
   JOIN product_submissions ps ON ps.id = si.submission_id
   SET 
     sp.current_holder_id = ps.owner_id,
     sp.holder_type = 'owner'
   WHERE sp.status IN ('owner_received', 'sent_to_owner', 'verified')
     AND ps.owner_id IS NOT NULL
     AND sp.current_holder_id IS NULL;
   
   -- Verify the fix
   SELECT 
       COUNT(*) as total_products,
       COUNT(current_holder_id) as products_with_holder,
       COUNT(*) - COUNT(current_holder_id) as products_without_holder
   FROM supplier_products;
   ```

3. **Check the result:**
   - `products_without_holder` should be 0
   - If not, see "Manual Fix" below

4. **Done!** Try sending products again

---

### Option 3: Use Provided SQL File

1. **Locate the file:**
   ```
   fixandgo/backend/fix_product_holders.sql
   ```

2. **Run it in your database tool**
   - Opens phpMyAdmin → Import → Select file → Go
   - Or copy/paste the contents into SQL tab

3. **Review the output:**
   - Shows before/after counts
   - Lists fixed products

4. **Done!** Try sending products again

---

## 🔧 Manual Fix (If Needed)

If the auto-fix doesn't work, you can manually set the owner:

```sql
-- Replace 123 with YOUR user_id (check session or users table)
UPDATE supplier_products 
SET current_holder_id = 123, 
    holder_type = 'owner'
WHERE current_holder_id IS NULL 
  AND status IN ('owner_received', 'sent_to_owner', 'verified');
```

**How to find your user_id:**
```sql
SELECT id, email, role 
FROM users 
WHERE role = 'owner';
```

---

## 🧪 Testing After Fix

1. **Hard refresh browser:** `Ctrl + F5`

2. **Go to Manage Products page**

3. **Select 1-2 products** (check the checkboxes)

4. **Click "Send to Supervisor"**

5. **Expected result:**
   - ✅ Modal appears with supervisor dropdown
   - ✅ Select supervisor, enter quantity
   - ✅ Click "Send Products"
   - ✅ Success message: "2 product(s) sent to supervisor successfully!"

6. **If it still fails:**
   - Open browser console (F12)
   - Look for error messages
   - Run diagnostic again: `debug_product_transfer_error.php`

---

## 📊 Understanding the Database Structure

### Before Fix:
```
supplier_products table:
id | item_description | qty | status         | current_holder_id | holder_type
---|------------------|-----|----------------|-------------------|------------
1  | USB-C Cable      | 50  | owner_received | NULL              | NULL
2  | Tempered Glass   | 100 | owner_received | NULL              | NULL
```

**Problem:** `current_holder_id` is NULL → System can't find products you own

### After Fix:
```
supplier_products table:
id | item_description | qty | status         | current_holder_id | holder_type
---|------------------|-----|----------------|-------------------|------------
1  | USB-C Cable      | 50  | owner_received | 5                 | owner
2  | Tempered Glass   | 100 | owner_received | 5                 | owner
```

**Fixed:** `current_holder_id = 5` (your user_id) → System knows you own these products

---

## 🔍 Diagnostic Tools

### 1. Debug Script
**URL:** `http://your-domain/fixandgo/backend/debug_product_transfer_error.php`

**Shows:**
- ✅ Session info (user_id, role)
- ✅ Database table structure
- ✅ Your products and their holder status
- ✅ Available supervisors
- ✅ Auto-fix button

### 2. Supervisor Setup Check
**URL:** `http://your-domain/fixandgo/backend/check_supervisor_setup.php`

**Shows:**
- ✅ Registered supervisors
- ✅ Active/verified status
- ✅ API endpoint test
- ✅ Recommendations

---

## 🚨 Common Issues

### Issue 1: "Product not found or you do not own it"
**Cause:** `current_holder_id` is NULL or doesn't match your user_id  
**Fix:** Run the SQL fix above

### Issue 2: "Insufficient quantity available"
**Cause:** Product quantity is less than what you're trying to send  
**Fix:** Check product quantity in database or reduce transfer quantity

### Issue 3: "Recipient not found"
**Cause:** Supervisor doesn't exist or is inactive  
**Fix:** Verify supervisor is active in Manage Staff page

### Issue 4: "Invalid transfer: owner cannot send to sales_person"
**Cause:** Trying to send directly to sales person (must go through supervisor)  
**Fix:** Send to supervisor first, then supervisor sends to sales person

---

## 📝 Complete Workflow

### Step 1: Fix Products
```bash
# Option A: Web interface
http://your-domain/fixandgo/backend/debug_product_transfer_error.php
→ Click "Auto-Fix Products" button

# Option B: SQL
Run: fixandgo/backend/fix_product_holders.sql
```

### Step 2: Verify Fix
```sql
SELECT 
    sp.id,
    sp.item_description,
    sp.current_holder_id,
    sp.holder_type,
    u.email as holder_email
FROM supplier_products sp
LEFT JOIN users u ON u.id = sp.current_holder_id
WHERE sp.current_holder_id IS NOT NULL
LIMIT 10;
```

### Step 3: Test Transfer
1. Go to Manage Products
2. Select products
3. Click "Send to Supervisor"
4. Select supervisor
5. Enter quantity
6. Click "Send Products"
7. ✅ Success!

---

## 📞 Still Having Issues?

If the error persists after running the fix:

1. **Run diagnostic:**
   ```
   http://your-domain/fixandgo/backend/debug_product_transfer_error.php
   ```

2. **Check browser console (F12):**
   - Look for red error messages
   - Share the error message

3. **Check database:**
   ```sql
   -- Verify products have holder
   SELECT COUNT(*) as products_without_holder
   FROM supplier_products
   WHERE current_holder_id IS NULL
     AND status IN ('owner_received', 'verified');
   ```

4. **Check PHP error log:**
   - Look for errors in `product_transfers.php`
   - Check server error logs

---

## ✅ Summary

**Problem:** Products don't have `current_holder_id` set  
**Solution:** Run SQL fix to set `current_holder_id` for all products  
**Result:** Transfer system can now find your products and send them to supervisors

**Quick Fix:**
```sql
UPDATE supplier_products sp
JOIN submission_items si ON si.product_id = sp.id
JOIN product_submissions ps ON ps.id = si.submission_id
SET sp.current_holder_id = ps.owner_id, sp.holder_type = 'owner'
WHERE sp.current_holder_id IS NULL AND ps.owner_id IS NOT NULL;
```

**Test:** Select products → Send to Supervisor → Success! ✅

---

**Files Created:**
- ✅ `debug_product_transfer_error.php` - Diagnostic tool
- ✅ `fix_product_holders.php` - Auto-fix via web
- ✅ `fix_product_holders.sql` - SQL fix script
- ✅ `FIX_FAILED_TO_SEND_ERROR.md` - This guide

**Next:** Run the fix and test! 🚀
