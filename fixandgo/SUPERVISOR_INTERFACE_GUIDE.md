# Fix&Go Supervisor Interface Guide

## Overview

The Supervisor role has been created to oversee and manage the product inventory for the shop owner. Supervisors have full CRUD (Create, Read, Update, Delete) access to all products in the owner's inventory.

## 🎯 Supervisor Capabilities

### ✅ What Supervisors Can Do:

1. **View All Products** - See complete inventory with details
2. **Add New Products** - Add products to owner's inventory
3. **Edit Products** - Update product details, prices, stock levels
4. **Delete Products** - Remove products from inventory
5. **Search & Filter** - Find products by name, category, stock level
6. **Monitor Statistics** - View inventory metrics and values

### ❌ What Supervisors Cannot Do:

- Cannot approve/reject supplier submissions (owner only)
- Cannot make purchases from suppliers (owner only)
- Cannot manage staff (owner only)
- Cannot access financial reports (owner only)

## 📁 File Structure

```
fixandgo/
├── views/user/supervisor/
│   ├── dashboard.html          # Main supervisor dashboard
│   ├── inventory.html          # Inventory management page
│   ├── inventory.js            # Inventory management logic
│   ├── profile.html            # Supervisor profile (placeholder)
│   └── reports.html            # Reports page (placeholder)
├── backend/
│   └── supervisor_inventory.php # Backend API for inventory management
```

## 🚀 How to Access

### 1. Register a Supervisor

**Option A: Owner Registration (Recommended)**
- Login as owner
- Go to "Manage Staff" page
- Click "Register Staff" button
- Fill in supervisor details
- Submit form

**Option B: Self-Registration**
- Go to staff registration page
- Select "Supervisor" role
- Fill in application form
- Wait for owner approval

### 2. Login as Supervisor

- Go to login page
- Enter supervisor email and password
- You'll be redirected to supervisor dashboard

## 📊 Dashboard Features

### Statistics Cards

- **Total Products** - Count of all products in inventory
- **In Stock** - Products with stock ≥ 10 units
- **Low Stock** - Products with 1-9 units (warning)
- **Total Value** - Sum of (price × quantity) for all products

### Quick Actions

- **Manage Inventory** - Go to inventory management page
- **Add Product** - Quick link to add new product
- **View Reports** - Access inventory reports (coming soon)

## 🛠️ Inventory Management

### Product Grid View

Products are displayed in a responsive grid with:
- Product image
- Category badge
- Product name
- Price
- Stock quantity with status badge
- Edit and Delete buttons

### Search & Filter

**Search Bar:**
- Search by product name
- Real-time filtering as you type

**Category Filter:**
- All Categories
- Screens
- Batteries
- Accessories
- Chargers
- Cases
- Other

**Stock Level Filter:**
- All Stock Levels
- In Stock (≥10 units)
- Low Stock (1-9 units)
- Out of Stock (0 units)

### Add Product

Click "Add Product" button to open modal with fields:
- **Product Name*** (required, max 150 chars)
- **Category*** (required, dropdown)
- **Description** (optional, textarea)
- **Price*** (required, ₱, min 0)
- **Stock Quantity*** (required, min 0)
- **Product Image** (optional, max 5MB, JPG/PNG/WEBP)

### Edit Product

Click "Edit" button on any product card:
- Pre-fills form with existing data
- Can update any field
- Can replace product image
- Old image is deleted when new one is uploaded

### Delete Product

Click "Delete" button on any product card:
- Shows confirmation dialog
- Permanently removes product
- Deletes associated image file
- Cannot be undone

## 🔒 Security Features

### Authentication
- Session-based authentication
- Role verification on every request
- Automatic redirect if not logged in
- Automatic redirect if wrong role

### Authorization
- Only supervisors can access supervisor pages
- Backend validates supervisor role
- Products belong to owner (supervisor manages on behalf)

### Input Validation

**Frontend:**
- Required field validation
- Format validation (price, quantity)
- File type validation (images only)
- File size validation (max 5MB)

**Backend:**
- SQL injection protection (prepared statements)
- XSS protection (input sanitization)
- File upload validation
- Role verification

## 📡 API Endpoints

### GET Requests

**List Products**
```
GET /backend/supervisor_inventory.php?action=list
Returns: { success: true, products: [...] }
```

**Get Statistics**
```
GET /backend/supervisor_inventory.php?action=stats
Returns: { success: true, stats: {...} }
```

### POST Requests

**Add Product**
```
POST /backend/supervisor_inventory.php
Content-Type: multipart/form-data
Body: { action: 'add', name, category, description, price, stock_quantity, image }
Returns: { success: true, message, product_id }
```

**Update Product**
```
POST /backend/supervisor_inventory.php
Content-Type: multipart/form-data
Body: { action: 'update', product_id, name, category, description, price, stock_quantity, image }
Returns: { success: true, message }
```

**Delete Product**
```
POST /backend/supervisor_inventory.php
Content-Type: application/json
Body: { action: 'delete', product_id }
Returns: { success: true, message }
```

## 🎨 UI/UX Features

### Responsive Design
- Mobile-friendly grid layout
- Adapts to screen size
- Touch-friendly buttons
- Optimized for tablets

### Visual Feedback
- Hover effects on cards
- Loading states
- Success/error alerts
- Smooth animations

### Stock Status Badges
- **In Stock** (Green) - 10+ units
- **Low Stock** (Red) - 1-9 units
- **Out of Stock** (Gray) - 0 units

### Theme Support
- Light/dark mode toggle
- Consistent with Fix&Go branding
- CSS custom properties

## 🔄 Workflow Example

### Adding a New Product

1. Supervisor logs in
2. Clicks "Manage Inventory" or "Add Product"
3. Fills in product form:
   - Name: "iPhone 14 Pro Screen"
   - Category: "Screens"
   - Description: "Original OLED display"
   - Price: 8500.00
   - Stock: 50
   - Image: Upload photo
4. Clicks "Add Product"
5. Product appears in inventory grid
6. Owner can see it in their products page

### Updating Stock Levels

1. Supervisor searches for product
2. Clicks "Edit" button
3. Updates stock quantity
4. Clicks "Update Product"
5. Stock level updates immediately
6. Badge color changes if threshold crossed

### Managing Low Stock

1. Supervisor filters by "Low Stock"
2. Reviews products with <10 units
3. Edits each product to update stock
4. Or notifies owner to reorder

## 📝 Best Practices

### For Supervisors

1. **Regular Stock Checks** - Review inventory daily
2. **Accurate Data** - Keep product info up-to-date
3. **Image Quality** - Use clear product photos
4. **Categorization** - Use correct categories
5. **Stock Alerts** - Monitor low stock items

### For Owners

1. **Trust but Verify** - Review supervisor changes periodically
2. **Clear Guidelines** - Set stock level policies
3. **Regular Training** - Train supervisors on system
4. **Access Control** - Only assign trusted staff

## 🐛 Troubleshooting

### Cannot Login
- Verify supervisor account is active
- Check email/password are correct
- Ensure owner has approved account

### Cannot Add Product
- Check all required fields are filled
- Verify image is under 5MB
- Ensure valid image format (JPG/PNG/WEBP)

### Products Not Showing
- Check internet connection
- Refresh the page
- Clear browser cache
- Check browser console for errors

### Image Upload Fails
- Reduce image file size
- Convert to JPG/PNG format
- Check server upload limits
- Verify uploads folder permissions

## 🔮 Future Enhancements

Planned features for supervisor role:

- [ ] Bulk product import (CSV/Excel)
- [ ] Product history/audit log
- [ ] Low stock email alerts
- [ ] Barcode scanning
- [ ] Inventory reports & analytics
- [ ] Product categories management
- [ ] Multi-location inventory
- [ ] Stock transfer between locations

## 📞 Support

If you encounter issues:
1. Check this guide first
2. Review browser console for errors
3. Verify database connection
4. Check file permissions
5. Contact system administrator

---

**Version**: 1.0  
**Last Updated**: 2026-05-01  
**Role**: Supervisor  
**Access Level**: Inventory Management Only
