# Quick Start: Quantity Selector Feature

## 🚀 Setup (One-Time)

### Step 1: Run Database Migration
Open **phpMyAdmin** and run this SQL file:
```
fixandgo/backend/migrate_add_purchase_quantities.sql
```

Or copy-paste this SQL:
```sql
USE fixandgo;

ALTER TABLE owner_payments
ADD COLUMN purchase_quantities JSON NULL COMMENT 'Custom quantities per product ID' 
AFTER product_ids;
```

✅ **Done!** The feature is now active.

---

## 📦 How to Use

### As Owner: Buy Custom Quantity

1. **Login** as owner
2. **Navigate** to dashboard
3. **Accept** products from suppliers (if not already accepted)
4. **Find** the product you want to buy
5. **Click** "Buy This" button
6. **Quantity Modal Appears**:
   - See available stock
   - Use +/- buttons or type quantity
   - See total price update in real-time
7. **Click** "Proceed to Payment"
8. **Complete** PayMongo checkout
9. **Done!** Your inventory updated with selected quantity

### Example
```
Product: iPhone Screen Protector
Available: 100 units
Unit Price: ₱50

You select: 25 units
Total: ₱1,250

After payment:
✓ You get: 25 units in your inventory
✓ Supplier has: 75 units remaining
✓ Product still available for other buyers
```

---

## 🎯 Key Features

### ✅ Single Product Purchase
- Select exact quantity you need
- Real-time price calculation
- Cannot exceed available stock
- Minimum 1 unit

### ✅ Bulk Purchase
- Buy multiple products at once
- Currently purchases all available stock
- Individual quantities coming soon

### ✅ Smart Inventory
- Supplier stock reduced by purchased amount
- Product stays available if stock remains
- Only marked "sold" when stock reaches 0

---

## 🔍 Quick Checks

### Is it working?
1. Click "Buy This" on any product
2. Do you see a quantity selector modal?
   - ✅ **YES** → Feature is working!
   - ❌ **NO** → Check if migration was run

### After purchase:
1. Check your "Manage Products" page
2. Do you see the purchased quantity?
   - ✅ **YES** → Everything working correctly!
   - ❌ **NO** → Check browser console for errors

---

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| No quantity modal appears | Run the database migration |
| Can't change quantity | Check browser console for errors |
| Wrong quantity received | Clear browser cache and try again |
| Supplier stock not reduced | Verify migration was successful |

---

## 📚 More Information

- **Full Guide**: `QUANTITY_SELECTOR_GUIDE.md`
- **Implementation Details**: `IMPLEMENTATION_SUMMARY_QUANTITY_SELECTOR.md`
- **Database Setup**: `DATABASE_SETUP_GUIDE.md`

---

## 💡 Tips

1. **Test First**: Try with small quantities to verify it works
2. **Check Stock**: Always verify available stock before purchasing
3. **Bulk Discount**: Contact supplier for bulk pricing (feature coming soon)
4. **Track Purchases**: View history in "Purchase History" page

---

**Need Help?** Check the full documentation or contact support.
