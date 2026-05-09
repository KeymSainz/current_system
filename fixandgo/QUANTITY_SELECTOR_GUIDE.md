# Quantity Selector Feature Guide

## Overview
The quantity selector feature allows shop owners to purchase custom quantities of products from suppliers instead of being forced to buy all available stock.

## Features

### 1. Single Product Purchase
- When buying a single product, a modal appears with:
  - Quantity input with +/- buttons
  - Unit price display
  - Real-time total calculation
  - Maximum quantity validation (cannot exceed available stock)

### 2. Bulk Product Purchase
- When buying multiple products at once:
  - Shows confirmation dialog with total count
  - Purchases all available stock for each product
  - Note: Individual quantity selection for bulk purchases coming in future update

### 3. Backend Processing
- Custom quantities are stored in `owner_payments.purchase_quantities` (JSON format)
- Payment amount calculated based on selected quantities
- Supplier's stock is reduced by purchased quantity (not zeroed out)
- Owner receives exact quantity purchased in their inventory

## Database Changes

### Migration Required
Run this migration to add the `purchase_quantities` column:

```sql
-- File: backend/migrate_add_purchase_quantities.sql
USE fixandgo;

ALTER TABLE owner_payments
ADD COLUMN purchase_quantities JSON NULL COMMENT 'Custom quantities per product ID' 
AFTER product_ids;
```

### Table Structure
```sql
owner_payments
├── product_ids           JSON    -- Array of product IDs
├── purchase_quantities   JSON    -- Object mapping product_id => quantity
└── ...
```

Example data:
```json
{
  "product_ids": [1, 2, 3],
  "purchase_quantities": {
    "1": 5,
    "2": 10,
    "3": 3
  }
}
```

## Implementation Details

### Frontend (dashboard.js)
1. **Quantity Modal**: Shows when buying single product
   - Input validation (min: 1, max: available stock)
   - Real-time total calculation
   - Responsive design with smooth animations

2. **Payment Request**: Sends quantities to backend
   ```javascript
   {
     action: 'create_checkout',
     product_ids: [123],
     quantities: { 123: 5 }
   }
   ```

### Backend (paymongo.php)
1. **Quantity Processing**:
   - Accepts `quantities` parameter (optional)
   - Validates quantities against available stock
   - Uses custom quantity if provided, otherwise uses full stock
   - Stores quantities in `purchase_quantities` column

2. **Payment Creation**:
   - Calculates total based on custom quantities
   - Creates PayMongo checkout session
   - Saves payment record with quantities

### Payment Processing (paymongo_return.php & paymongo_webhook.php)
1. **Inventory Update**:
   - Reads `purchase_quantities` from payment record
   - Adds purchased quantity to owner's inventory
   - Reduces supplier's stock by purchased quantity (not full stock)
   - Marks supplier product as "sold" only if stock reaches 0

2. **Purchase Tracking**:
   - Records actual purchased quantity in `owner_inventory` table
   - Includes quantity in product notes for audit trail

## User Flow

### Owner Purchasing Single Product
1. Owner views accepted products from supplier
2. Clicks "Buy This" button on a product
3. Quantity selector modal appears
4. Owner adjusts quantity using +/- buttons or direct input
5. Total price updates in real-time
6. Owner clicks "Proceed to Payment"
7. Redirected to PayMongo checkout
8. After payment, purchased quantity added to owner's inventory
9. Supplier's stock reduced by purchased amount

### Owner Purchasing Multiple Products
1. Owner clicks "Buy All from [Supplier]" button
2. Confirmation dialog shows total products and amount
3. Owner confirms purchase
4. All available stock for each product is purchased
5. Payment processed and inventory updated

## Testing

### Test Scenarios
1. **Single Product - Partial Purchase**:
   - Product has 100 units
   - Owner buys 25 units
   - Expected: Owner gets 25, supplier has 75 remaining

2. **Single Product - Full Purchase**:
   - Product has 50 units
   - Owner buys 50 units
   - Expected: Owner gets 50, supplier product marked as sold

3. **Bulk Purchase**:
   - 3 products with varying stock levels
   - Owner buys all
   - Expected: All stock transferred, products marked as sold

4. **Quantity Validation**:
   - Product has 10 units
   - Owner tries to buy 15 units
   - Expected: Quantity capped at 10

## Future Enhancements
- [ ] Individual quantity selection for bulk purchases
- [ ] Quantity presets (25%, 50%, 75%, 100%)
- [ ] Minimum order quantity per product
- [ ] Bulk discount calculations
- [ ] Stock reservation during checkout process
- [ ] Partial payment for large orders

## Files Modified
- `fixandgo/assets/js/dashboard.js` - Added quantity modal and selection logic
- `fixandgo/backend/paymongo.php` - Added quantities parameter handling
- `fixandgo/backend/paymongo_return.php` - Updated inventory processing
- `fixandgo/backend/paymongo_webhook.php` - Updated inventory processing
- `fixandgo/backend/migrate_add_purchase_quantities.sql` - Database migration

## Troubleshooting

### Issue: Quantity modal not appearing
- **Cause**: `allProducts` variable not set
- **Solution**: Ensure products are loaded before clicking buy button

### Issue: Full stock purchased instead of custom quantity
- **Cause**: Migration not run, `purchase_quantities` column missing
- **Solution**: Run `migrate_add_purchase_quantities.sql`

### Issue: Supplier stock not reduced
- **Cause**: Payment processing function not updated
- **Solution**: Ensure both `paymongo_return.php` and `paymongo_webhook.php` are updated

## Support
For issues or questions, check:
1. Browser console for JavaScript errors
2. PHP error logs for backend issues
3. Database logs for SQL errors
4. PayMongo dashboard for payment status
