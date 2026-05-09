# Fix&Go - Owner Inventory Setup Instructions

## Problem
You completed a test payment but the products didn't appear in the inventory because:
1. The `owner_inventory` table doesn't exist yet (migration not run)
2. The payment was processed before the inventory feature was implemented

## Solution - Follow These Steps

### Step 1: Create the Database Table

**Option A: Using phpMyAdmin (Recommended)**
1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Click on the `fixandgo` database in the left sidebar
3. Click the **"Import"** tab at the top
4. Click **"Choose File"** button
5. Navigate to: `C:\xampp\htdocs\current_system\fixandgo\backend\migrate_owner_inventory.sql`
6. Click **"Go"** button at the bottom
7. You should see: ✓ "owner_inventory table created successfully."

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p fixandgo < C:\xampp\htdocs\current_system\fixandgo\backend\migrate_owner_inventory.sql
```

### Step 2: Process Your Existing Payment

Since you already completed a payment before the inventory feature was added, we need to manually process it:

1. **Login as Owner** in your browser
2. Navigate to: `http://localhost/current_system/fixandgo/backend/process_existing_payment.php`
3. The script will:
   - Find your most recent paid payment
   - Add the products to your inventory
   - Show you a success message

### Step 3: Verify It Worked

1. Go to the Owner dashboard
2. Click on **"My Inventory"** in the left sidebar
3. You should now see your purchased products!

### Step 4: Test Future Payments

For any new payments going forward:
1. Accept supplier products
2. Click "Buy Products"
3. Complete PayMongo payment
4. Products will **automatically** be added to your inventory
5. Check "My Inventory" to see them

---

## Troubleshooting

### Issue: "Table 'fixandgo.owner_inventory' doesn't exist"

**Solution:** Run Step 1 again. The migration file must be imported into the database.

### Issue: "No paid payments found"

**Solution:** 
1. Check if your payment was successful
2. Go to phpMyAdmin → `fixandgo` database → `owner_payments` table
3. Look for your payment record
4. Check if `status` column says "paid"
5. If it says "pending", the payment might not have completed

### Issue: "Payment already processed"

**Solution:** This means the products are already in your inventory. Check the "My Inventory" page.

### Issue: Products still not showing

**Solution:**
1. Open browser console (F12)
2. Go to "My Inventory" page
3. Check for any JavaScript errors
4. Verify you're logged in as an owner
5. Check if the API is working: Open `http://localhost/current_system/fixandgo/backend/owner_inventory.php?action=inventory` in a new tab

---

## Quick Verification Checklist

- [ ] Database table `owner_inventory` exists
- [ ] Ran `process_existing_payment.php` script
- [ ] Can see "My Inventory" link in owner sidebar
- [ ] Products appear in "My Inventory" page
- [ ] Supplier can see purchases in "Owner Purchases" page

---

## For Suppliers

Suppliers can now view which owners purchased their products:

1. Login as Supplier
2. Click **"Owner Purchases"** in the left sidebar
3. View sales statistics and purchase history

---

## Need Help?

If you're still having issues:

1. Check PHP error logs: `C:\xampp\apache\logs\error.log`
2. Check browser console for JavaScript errors (F12)
3. Verify database connection in `backend/config.php`
4. Make sure you're using the correct user role (owner/supplier)

---

## Files Reference

- Migration: `backend/migrate_owner_inventory.sql`
- Process Script: `backend/process_existing_payment.php`
- Owner Inventory Page: `views/user/owner/inventory.html`
- Supplier Sales Page: `views/user/supplier/owner-purchases.html`
- Owner API: `backend/owner_inventory.php`
- Supplier API: `backend/supplier_sales.php`
