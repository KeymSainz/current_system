# Implementation Summary: Quantity Selector Feature

## Task Completed
✅ **Owner can select custom quantity when purchasing products from suppliers**

Previously, owners were forced to buy all available stock. Now they can choose how many units to purchase.

---

## Changes Made

### 1. Frontend Updates (`assets/js/dashboard.js`)

#### Added Global Product Storage
```javascript
window.allProducts = products; // Store for quantity modal access
```

#### Created Quantity Selection Modal
- **Single Product Purchase**: Shows interactive modal with:
  - Quantity input field (validated against available stock)
  - +/- buttons for easy adjustment
  - Unit price display
  - Real-time total calculation
  - Responsive design with smooth animations

- **Bulk Product Purchase**: Shows confirmation dialog
  - Currently purchases all available stock
  - Individual quantity selection for bulk coming in future update

#### Updated Payment Request
```javascript
fetch('backend/paymongo.php', {
  method: 'POST',
  body: JSON.stringify({ 
    action: 'create_checkout', 
    product_ids: [productId],
    quantities: { [productId]: qty }  // ← NEW
  })
})
```

---

### 2. Backend Updates (`backend/paymongo.php`)

#### Added Quantity Parameter Handling
```php
$quantities = $body['quantities'] ?? []; // Custom quantities per product

foreach ($products as $p) {
    $productId = (int)$p['id'];
    $availableQty = max(1, (int)$p['qty']);
    
    // Use custom quantity if provided, otherwise use full stock
    $quantity = isset($quantities[$productId]) 
        ? max(1, min((int)$quantities[$productId], $availableQty))
        : $availableQty;
    
    $purchaseQuantities[$productId] = $quantity;
    // ... create line items with custom quantity
}
```

#### Updated Payment Record Storage
```php
$pdo->prepare(
    'INSERT INTO owner_payments
     (owner_id, reference, paymongo_id, amount, currency,
      status, checkout_url, product_ids, purchase_quantities, created_at)
     VALUES (?, ?, ?, ?, "PHP", "pending", ?, ?, ?, NOW())'
)->execute([
    $ownerId,
    $reference,
    $sessionId,
    $totalAmount / 100,
    $checkoutUrl,
    json_encode($productIds),
    json_encode($purchaseQuantities),  // ← NEW
]);
```

---

### 3. Payment Processing Updates

#### Updated `backend/paymongo_return.php`
```php
function addProductsToOwnerInventory($pdo, $paymentId, $ownerId) {
    // Fetch purchase_quantities from payment record
    $stmt = $pdo->prepare(
        'SELECT product_ids, purchase_quantities FROM owner_payments WHERE id = ? LIMIT 1'
    );
    
    $purchaseQuantities = json_decode($payment['purchase_quantities'], true) ?? [];
    
    foreach ($products as $product) {
        $productId = (int)$product['id'];
        
        // Use custom quantity if available
        $purchasedQty = isset($purchaseQuantities[$productId]) 
            ? max(1, (int)$purchaseQuantities[$productId])
            : max(1, (int)$product['qty']);
        
        // Add to owner inventory with purchased quantity
        $insertStmt->execute([
            $ownerId,
            $product['category'],
            $product['brand'],
            $product['item_description'],
            $purchasedQty,  // ← Use purchased quantity, not full stock
            // ...
        ]);
        
        // Reduce supplier's stock by purchased quantity
        $reduceStockStmt->execute([$purchasedQty, $productId]);
    }
    
    // Mark as sold only if stock reaches 0
    $updateStmt = $pdo->prepare(
        "UPDATE supplier_products 
         SET status = 'owner_received'
         WHERE id IN ($placeholders) AND qty = 0"
    );
}
```

#### Updated `backend/paymongo_webhook.php`
- Same logic as `paymongo_return.php`
- Handles webhook-triggered payment confirmations
- Ensures consistent inventory processing

---

### 4. Database Migration

#### Created `backend/migrate_add_purchase_quantities.sql`
```sql
USE fixandgo;

ALTER TABLE owner_payments
ADD COLUMN purchase_quantities JSON NULL COMMENT 'Custom quantities per product ID' 
AFTER product_ids;
```

**⚠️ IMPORTANT**: This migration must be run before using the quantity selector feature!

---

## How It Works

### Before (Old Behavior)
1. Owner clicks "Buy This" on product with 100 units
2. Payment created for all 100 units
3. After payment, all 100 units transferred to owner
4. Supplier's product marked as sold (qty = 0)

### After (New Behavior)
1. Owner clicks "Buy This" on product with 100 units
2. **Quantity modal appears**
3. Owner selects 25 units
4. Payment created for 25 units only
5. After payment:
   - Owner receives 25 units in inventory
   - Supplier's stock reduced to 75 units
   - Product remains available for other owners
   - Only marked as sold when qty reaches 0

---

## Data Flow

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Owner Dashboard (dashboard.js)                           │
│    - Clicks "Buy This" on product                           │
│    - Quantity modal shows                                   │
│    - Selects quantity: 25 units                             │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Payment API (paymongo.php)                               │
│    - Receives: product_ids=[123], quantities={123: 25}      │
│    - Validates: 25 ≤ 100 (available stock) ✓                │
│    - Calculates: 25 × ₱50 = ₱1,250                          │
│    - Creates PayMongo checkout session                      │
│    - Saves: purchase_quantities={123: 25} to DB             │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. PayMongo Checkout                                        │
│    - Owner pays ₱1,250                                      │
│    - Payment succeeds                                       │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Payment Processing (paymongo_return.php/webhook.php)    │
│    - Reads: purchase_quantities={123: 25}                   │
│    - Adds 25 units to owner's inventory                     │
│    - Reduces supplier's stock: 100 → 75                     │
│    - Product status: still "verified" (not sold)            │
└─────────────────────────────────────────────────────────────┘
```

---

## Testing Checklist

- [ ] Run database migration: `migrate_add_purchase_quantities.sql`
- [ ] Test single product purchase with custom quantity
- [ ] Test quantity validation (cannot exceed available stock)
- [ ] Test quantity validation (minimum 1 unit)
- [ ] Test +/- buttons in quantity modal
- [ ] Test direct input in quantity field
- [ ] Test real-time total calculation
- [ ] Test payment with custom quantity
- [ ] Verify owner receives correct quantity
- [ ] Verify supplier stock reduced correctly
- [ ] Verify product not marked as sold if stock remains
- [ ] Test bulk purchase (multiple products)
- [ ] Test purchase tracking in owner_inventory table

---

## Files Created/Modified

### Created
- ✅ `fixandgo/backend/migrate_add_purchase_quantities.sql`
- ✅ `fixandgo/QUANTITY_SELECTOR_GUIDE.md`
- ✅ `fixandgo/IMPLEMENTATION_SUMMARY_QUANTITY_SELECTOR.md`

### Modified
- ✅ `fixandgo/assets/js/dashboard.js`
- ✅ `fixandgo/backend/paymongo.php`
- ✅ `fixandgo/backend/paymongo_return.php`
- ✅ `fixandgo/backend/paymongo_webhook.php`

---

## Next Steps

1. **Run Migration**:
   ```
   Open phpMyAdmin → Import → Select migrate_add_purchase_quantities.sql → Go
   ```

2. **Test the Feature**:
   - Login as owner
   - Accept products from supplier
   - Click "Buy This" on a product
   - Select custom quantity
   - Complete payment
   - Verify inventory updated correctly

3. **Future Enhancements** (Optional):
   - Individual quantity selection for bulk purchases
   - Quantity presets (25%, 50%, 75%, 100%)
   - Minimum order quantity per product
   - Bulk discount calculations

---

## Support

If you encounter issues:
1. Check browser console for JavaScript errors
2. Check PHP error logs for backend issues
3. Verify migration was run successfully
4. Ensure `purchase_quantities` column exists in `owner_payments` table

---

**Status**: ✅ Feature Complete and Ready for Testing
