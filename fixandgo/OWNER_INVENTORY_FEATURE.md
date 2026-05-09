# Owner Inventory & Supplier Sales Tracking Feature

## Overview
This feature enables owners to purchase products from suppliers via PayMongo payments, and automatically adds purchased products to the owner's inventory. Suppliers can also view which owners have purchased their products.

## Database Changes

### New Table: `owner_inventory`
Run the migration file: `backend/migrate_owner_inventory.sql`

This table stores:
- Owner's purchased products
- Supplier information
- Payment reference
- Product details (snapshot at time of purchase)
- Purchase date and pricing

## Backend APIs

### 1. Owner Inventory API (`backend/owner_inventory.php`)
**Endpoints:**
- `GET ?action=inventory` - Get all products in owner's inventory
- `GET ?action=stats` - Get inventory statistics (total products, suppliers, spending)

**Authentication:** Owner role required

### 2. Supplier Sales API (`backend/supplier_sales.php`)
**Endpoints:**
- `GET ?action=purchases` - Get all products purchased by owners from this supplier
- `GET ?action=stats` - Get sales statistics (total sales, revenue, top products)

**Authentication:** Supplier role required

## Payment Flow Updates

### Modified Files:
1. **`backend/paymongo_return.php`**
   - Added `addProductsToOwnerInventory()` function
   - Automatically adds products to owner inventory after successful payment

2. **`backend/paymongo_webhook.php`**
   - Added `addProductsToOwnerInventory()` function
   - Handles webhook events for payment completion

## Frontend Pages

### For Owners:
**New Page:** `views/user/owner/inventory.html`
- View all purchased products
- Statistics dashboard (total products, suppliers, spending)
- Search and filter inventory
- Product details with supplier information

**Navigation:** Added "My Inventory" link to owner sidebar

### For Suppliers:
**New Page:** `views/user/supplier/owner-purchases.html`
- View all products purchased by owners
- Sales statistics (total sales, unique owners, revenue)
- Search and filter purchases
- Owner details for each purchase

**Navigation:** Added "Owner Purchases" link to supplier sidebar

## Features

### Owner Features:
1. **Automatic Inventory Addition**
   - Products automatically added to inventory after successful payment
   - Snapshot of product details preserved at time of purchase
   
2. **Inventory Dashboard**
   - Total products count
   - Number of unique suppliers
   - Total amount spent
   - Total items purchased

3. **Inventory Management**
   - View all purchased products
   - Search by product name, category, supplier
   - See payment reference and purchase date
   - Product images and details

### Supplier Features:
1. **Sales Tracking**
   - View all products purchased by owners
   - See which owners bought which products
   - Track payment references

2. **Sales Analytics**
   - Total sales count
   - Number of unique owner customers
   - Total revenue generated
   - Total items sold

3. **Purchase History**
   - Detailed purchase records
   - Owner contact information
   - Payment details and dates

## How It Works

### Purchase Flow:
1. Owner browses supplier products in "Products" page
2. Owner accepts products they want to purchase
3. Owner clicks "Buy Products" and completes PayMongo payment
4. After successful payment:
   - Payment status updated to "paid"
   - Products automatically added to `owner_inventory` table
   - Owner can view products in "My Inventory" page
   - Supplier can see the purchase in "Owner Purchases" page

### Data Flow:
```
Payment Success
    ↓
paymongo_return.php / paymongo_webhook.php
    ↓
addProductsToOwnerInventory()
    ↓
Insert into owner_inventory table
    ↓
Owner sees in "My Inventory"
Supplier sees in "Owner Purchases"
```

## Installation Steps

1. **Run Database Migration:**
   ```sql
   -- In phpMyAdmin, import:
   backend/migrate_owner_inventory.sql
   ```

2. **Verify Backend Files:**
   - `backend/owner_inventory.php`
   - `backend/supplier_sales.php`
   - Updated `backend/paymongo_return.php`
   - Updated `backend/paymongo_webhook.php`

3. **Verify Frontend Files:**
   - `views/user/owner/inventory.html`
   - `views/user/supplier/owner-purchases.html`
   - Updated navigation in owner and supplier pages

4. **Test the Flow:**
   - Login as owner
   - Accept supplier products
   - Complete test payment
   - Check "My Inventory" page
   - Login as supplier
   - Check "Owner Purchases" page

## Security Notes

- All APIs require proper authentication (session-based)
- Owner can only view their own inventory
- Supplier can only view purchases of their own products
- Payment verification through PayMongo API before adding to inventory
- SQL injection protection via prepared statements

## Future Enhancements

Potential improvements:
- Inventory stock management (track usage/depletion)
- Export inventory to CSV/PDF
- Inventory alerts for low stock
- Return/refund handling
- Bulk purchase discounts
- Purchase order generation
