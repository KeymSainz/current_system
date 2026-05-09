# Supervisor: Send Products to Sales Person Guide

## Overview
Supervisors can now select multiple products from their inventory and send them to sales persons. This allows sales persons to view and sell these products to customers.

## Features

### 1. Product Selection
- **Checkbox on each product card**: Click to select individual products
- **Select All checkbox**: Quickly select/deselect all visible products
- **Visual feedback**: Selected products have a highlighted border
- **Selection counter**: Shows how many products are currently selected

### 2. Send to Sales Person
- **Bulk action**: Send multiple products at once
- **Status update**: Products change status from `sent_to_supervisor` to `sent_to_sales_person`
- **Confirmation dialog**: Prevents accidental sends
- **Success feedback**: Shows how many products were sent

### 3. Selection Toolbar
- Appears when products are available
- Shows selection count
- "Send to Sales Person" button (disabled when no products selected)
- "Select All" checkbox for quick selection

## User Flow

### Supervisor Workflow
1. **Login** as supervisor
2. **Navigate** to Inventory Management
3. **View** products sent by owner (status: `sent_to_supervisor`)
4. **Select** products:
   - Click checkbox on individual products, OR
   - Click "Select All" to select all visible products
5. **Click** "Send to Sales Person" button
6. **Confirm** the action in the dialog
7. **Success**: Products are now available to sales persons

### What Happens After Sending
- Product status changes: `sent_to_supervisor` → `sent_to_sales_person`
- Products disappear from supervisor's inventory view
- Products become visible to sales persons
- Sales persons can now view and sell these products

## Product Status Flow

```
Owner → Supervisor → Sales Person

1. Owner creates/purchases product (status: 'verified')
2. Owner sends to supervisor (status: 'sent_to_supervisor')
3. Supervisor reviews and sends to sales person (status: 'sent_to_sales_person')
4. Sales person can now sell the product
```

## UI Components

### Selection Toolbar
```
┌─────────────────────────────────────────────────────────┐
│ ☑ Select All    [2 selected]    [Send to Sales Person] │
└─────────────────────────────────────────────────────────┘
```

### Product Card with Checkbox
```
┌──────────────────────┐
│ ☑                    │  ← Checkbox
│   [Product Image]    │
│                      │
│   Product Name       │
│   ₱1,500.00         │
│   [In Stock] 100 units│
│   [Edit] [Delete]    │
└──────────────────────┘
```

## Technical Details

### Frontend (inventory.js)
- **selectedProductIds**: Set to track selected product IDs
- **handleProductCheckbox()**: Handles individual checkbox changes
- **handleSelectAll()**: Handles select all checkbox
- **updateSelectionUI()**: Updates UI based on selection state
- **handleSendToSalesPerson()**: Sends selected products to backend

### Backend (supervisor_inventory.php)
- **Action**: `send_to_sales_person`
- **Parameters**: `product_ids` (array of integers)
- **Process**:
  1. Validates product IDs
  2. Updates status to `sent_to_sales_person`
  3. Only updates products with status `sent_to_supervisor`
  4. Returns count of updated products

### Database
- **Table**: `supplier_products`
- **Column**: `status` (ENUM)
- **New Value**: `sent_to_sales_person`

## Database Migration

### Required Migration
Run this SQL to add the new status value:

```sql
-- File: backend/migrate_add_product_statuses.sql
USE fixandgo;

ALTER TABLE supplier_products
  MODIFY COLUMN status 
    ENUM('pending','verified','rejected','owner_received','draft','sent_to_supervisor','sent_to_sales_person')
    NOT NULL DEFAULT 'pending';
```

**⚠️ IMPORTANT**: This migration must be run before using the send to sales person feature!

## API Endpoint

### Send to Sales Person
```
POST /backend/supervisor_inventory.php
Content-Type: application/json

{
  "action": "send_to_sales_person",
  "product_ids": [1, 2, 3, 4, 5]
}
```

**Response (Success)**:
```json
{
  "success": true,
  "message": "5 product(s) sent to sales person successfully.",
  "updated_count": 5
}
```

**Response (Error)**:
```json
{
  "success": false,
  "message": "No products were updated. They may have already been sent or do not exist."
}
```

## Testing Checklist

- [ ] Migration run successfully
- [ ] Supervisor can see products with status `sent_to_supervisor`
- [ ] Checkboxes appear on product cards
- [ ] Individual checkbox selection works
- [ ] "Select All" checkbox works
- [ ] Selection counter updates correctly
- [ ] "Send to Sales Person" button disabled when no selection
- [ ] "Send to Sales Person" button enabled when products selected
- [ ] Confirmation dialog appears
- [ ] Products sent successfully
- [ ] Products disappear from supervisor view after sending
- [ ] Product status updated to `sent_to_sales_person` in database
- [ ] Sales person can see the products (future feature)

## Future Enhancements

- [ ] Sales person interface to view products
- [ ] Sales person can mark products as sold
- [ ] Sales tracking and reporting
- [ ] Commission calculation for sales persons
- [ ] Product return/recall from sales person
- [ ] Bulk actions: Edit, Delete selected products
- [ ] Filter by selection status
- [ ] Export selected products to CSV/PDF

## Files Modified

### Created
- ✅ `fixandgo/SUPERVISOR_SEND_TO_SALES_GUIDE.md`

### Modified
- ✅ `fixandgo/views/user/supervisor/inventory.html` - Added selection toolbar and checkboxes
- ✅ `fixandgo/views/user/supervisor/inventory.js` - Added selection logic
- ✅ `fixandgo/backend/supervisor_inventory.php` - Added send_to_sales_person action
- ✅ `fixandgo/backend/migrate_add_product_statuses.sql` - Added new status value

## Troubleshooting

### Issue: Checkboxes not appearing
- **Cause**: JavaScript not loaded or products not rendering
- **Solution**: Check browser console for errors, refresh page

### Issue: "Send to Sales Person" button always disabled
- **Cause**: Selection state not updating
- **Solution**: Check `updateSelectionUI()` function, verify checkbox events

### Issue: Products not sent
- **Cause**: Migration not run, status mismatch
- **Solution**: Run migration, verify product status is `sent_to_supervisor`

### Issue: Products still visible after sending
- **Cause**: Frontend not reloading products
- **Solution**: Check `loadProducts()` is called after successful send

## Support

For issues or questions:
1. Check browser console for JavaScript errors
2. Check PHP error logs for backend issues
3. Verify migration was run successfully
4. Ensure product status is correct in database

---

**Status**: ✅ Feature Complete and Ready for Testing
