# Quick Test Guide - Supervisor Inventory

## 🚀 Quick Test (5 minutes)

### Step 1: Send Product (as Owner)
1. Login as **Owner**
2. Go to **Manage Products** page
3. Check a product checkbox
4. Click **"Send to Supervisor"** button (should appear when product is selected)
5. Select supervisor from dropdown
6. Enter quantity (e.g., 5)
7. Click **"Send Products"**
8. **Press F12** → Check Console tab for:
   ```
   === TRANSFER RESULTS ===
   All results: [{success: true, message: "Product sent successfully..."}]
   ```

### Step 2: View Product (as Supervisor)
1. Login as **Supervisor** (the one you sent to)
2. Go to **Inventory** page
3. **Press F12** → Check Console tab for:
   ```
   === LOADING SUPERVISOR PRODUCTS ===
   Total products loaded: 1 (or more)
   ```
4. **Look at the page** → Product should appear in the grid

## ✅ Success Indicators

### Owner Side:
- ✅ Alert: "1 product(s) sent to supervisor successfully!"
- ✅ Console: `success: true` in transfer results
- ✅ Product quantity reduced in owner's list

### Supervisor Side:
- ✅ Console: `Total products loaded: X` (where X > 0)
- ✅ Product card appears in inventory grid
- ✅ Shows correct category, brand, quantity, price

## ❌ If It Doesn't Work

### Check 1: Browser Console (F12)
Look for errors in red. Common issues:
- Network errors (check if backend is running)
- Authentication errors (re-login)
- JavaScript errors (check file paths)

### Check 2: Debug Endpoint
1. Login as **Supervisor**
2. Go to: `http://your-domain/fixandgo/backend/debug_supervisor_products.php`
3. Check JSON response:
   - `products_for_user`: Should show products
   - `recent_transfers`: Should show your transfer

### Check 3: Database
Run this SQL:
```sql
SELECT id, category, brand, item_description, qty, current_holder_id, holder_type
FROM supplier_products
WHERE holder_type = 'supervisor'
ORDER BY id DESC
LIMIT 5;
```
Should show products with `holder_type = 'supervisor'`

## 🔧 Common Fixes

### Issue: "No supervisors assigned to you"
**Fix**: Make sure supervisors are registered in Staff Management page

### Issue: "Failed to send"
**Fix**: Check browser console for specific error message

### Issue: Products sent but not showing
**Fix**: 
1. Hard refresh supervisor page (Ctrl+F5)
2. Check debug endpoint
3. Verify supervisor is logged in correctly

### Issue: "Insufficient quantity available"
**Fix**: Check owner's product has enough quantity

## 📊 What to Report

If still not working, provide:
1. **Browser console logs** (copy the text)
2. **Debug endpoint output** (copy the JSON)
3. **SQL query result** (copy the rows)
4. **Screenshots** of the issue

## 🎯 Expected Flow

```
Owner (qty: 10)
    ↓ Send 5 units
Owner (qty: 5) + Supervisor (qty: 5)
    ↓ Send 3 more units
Owner (qty: 2) + Supervisor (qty: 8)
```

Each transfer:
- Reduces owner's quantity
- Creates/updates supervisor's product
- Auto-accepted (no approval needed)
- Immediate visibility
