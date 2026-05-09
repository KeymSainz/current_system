# Owner Products Management Guide

## Overview

The owner now has TWO pages for managing products:

### 1. **Manage Products** (`products.html`)
- **Purpose:** Manage your shop's product inventory
- **Shows:** ALL products you own (created + purchased)
- **Features:**
  - View all your products
  - Edit product details
  - Delete products
  - See which products were purchased (marked with cart icon)
  - Statistics: Total products, verified, drafts, total value

### 2. **Purchase History** (`inventory.html`)
- **Purpose:** Track your purchases from suppliers
- **Shows:** Detailed purchase records
- **Features:**
  - See which supplier you bought from
  - View payment references
  - Track purchase dates and amounts
  - Statistics: Total spent, suppliers, items purchased

## Navigation Structure

```
Owner Dashboard
├── Manage Products      ← Your shop inventory (all products)
├── Purchase History     ← Track supplier purchases
├── Bookings
├── Deliveries
├── Revenue Report
├── Messages
└── Profile
```

## How It Works

### When You Purchase Products:

1. **Payment Completes** → Products are added to `supplier_products` table
2. **Manage Products Page** → Shows the new products (marked as "Purchased")
3. **Purchase History Page** → Shows the purchase record with supplier info

### Product Flow:

```
Supplier Product
    ↓
Owner Buys (PayMongo)
    ↓
├─→ Added to supplier_products (owner's inventory)
│   └─→ Visible in "Manage Products"
│
└─→ Tracked in owner_inventory (purchase record)
    └─→ Visible in "Purchase History"
```

## Key Features

### Manage Products Page:
- ✅ View all your products in one place
- ✅ Products you created + products you purchased
- ✅ Purchased products marked with 🛒 icon
- ✅ Edit quantities, prices, descriptions
- ✅ Delete products you don't need
- ✅ Search and filter products
- ✅ Statistics dashboard

### Purchase History Page:
- ✅ See all your purchases from suppliers
- ✅ Supplier contact information
- ✅ Payment references for accounting
- ✅ Purchase dates and amounts
- ✅ Search by product, supplier, or payment ref
- ✅ Purchase statistics

## Database Structure

### supplier_products Table:
```
- Used by BOTH suppliers AND owners
- supplier_id = owner's ID for purchased products
- status = "verified" for purchased products
- notes = includes "Purchased from supplier (Payment ID: X)"
```

### owner_inventory Table (Optional):
```
- Tracks purchase history
- Links to original supplier
- Stores payment details
- Used for Purchase History page
```

## URLs

- **Manage Products:** `views/user/owner/products.html`
- **Purchase History:** `views/user/owner/inventory.html`

## Backend APIs

- **Manage Products:** `backend/owner_shop_products.php`
  - GET ?action=list - Get all owner's products
  - GET ?action=stats - Get product statistics
  - POST action=add - Add new product
  - POST action=update - Update product
  - POST action=delete - Delete product(s)

- **Purchase History:** `backend/owner_inventory.php`
  - GET ?action=inventory - Get purchase history
  - GET ?action=stats - Get purchase statistics

## Setup Instructions

### For Existing Payments:
1. Login as Owner
2. Visit: `backend/process_existing_payment.php`
3. Products will be added to your inventory
4. Go to "Manage Products" to see them

### For New Payments:
- Automatic! Products appear immediately after payment

### Optional - Purchase Tracking:
If you want the "Purchase History" page:
1. Run migration: `backend/migrate_owner_inventory.sql`
2. Creates `owner_inventory` table
3. Tracks detailed purchase records

## Benefits

### ✅ Clear Separation:
- **Manage Products** = Your working inventory
- **Purchase History** = Accounting/tracking

### ✅ Unified Management:
- All products in one place
- Same interface for created + purchased products
- Easy to manage and edit

### ✅ Purchase Attribution:
- Know which products were purchased
- Track supplier sources
- Payment references for accounting

### ✅ Flexible:
- Purchase History page is optional
- Core functionality works without it
- Can add tracking later if needed

## Common Questions

**Q: Where do purchased products appear?**
A: In "Manage Products" page, marked with a cart icon.

**Q: Can I edit purchased products?**
A: Yes! They're yours now. Edit freely.

**Q: What's the difference between the two pages?**
A: "Manage Products" = your inventory. "Purchase History" = purchase records for accounting.

**Q: Do I need the Purchase History page?**
A: No, it's optional. "Manage Products" is the main page.

**Q: Can I delete purchased products?**
A: Yes, from "Manage Products" page.

**Q: How do I know which products I purchased?**
A: They have a 🛒 "Purchased" badge in "Manage Products".

## Troubleshooting

**Issue: Products not showing after payment**
- Check "Manage Products" page (not Purchase History)
- Run `backend/process_existing_payment.php` for old payments
- Verify payment was successful in database

**Issue: Can't see Manage Products link**
- Make sure you're logged in as Owner
- Check sidebar navigation
- Clear browser cache

**Issue: Purchase History page empty**
- This page requires `owner_inventory` table
- Run migration: `backend/migrate_owner_inventory.sql`
- Or just use "Manage Products" page instead
