# Fix&Go Registration Flow - Summary

## ✅ Completed Changes

### 🎯 Goal
Simplify registration so all new users register as **customers first**. Supplier and Owner registration happens through the **Seller Centre** in the customer dashboard. Staff accounts are created only by owners.

---

## 📋 What Changed

### 1. **Public Registration Page** (`register.html`)
**Before:**
- Had role selection dropdown
- Users could choose: Customer, Supplier, Owner, Staff roles

**After:**
- ✅ No role selection - everyone registers as customer
- ✅ Hidden input field: `<input type="hidden" name="userType" value="customer" />`
- ✅ Updated messaging: "Want to become a Supplier or Shop Owner? Sign in and visit **Seller Centre** in your dashboard."

---

### 2. **Registration JavaScript** (`assets/js/register.js`)
**Before:**
- Validated role selection
- Required `userTypeSelect` field

**After:**
- ✅ Removed `userTypeSelect` variable
- ✅ Removed role validation logic
- ✅ Simplified form validation

---

### 3. **Backend Registration API** (`backend/register.php`)
**Before:**
```php
if (!in_array($userType, ['customer', 'sales_person', 'supplier', 'supervisor', 'owner', 'phone_technician'], true)) {
    $errors[] = 'Invalid user type selected.';
}
```

**After:**
```php
// Only allow customer, supplier, and owner registrations
// Staff roles (sales_person, supervisor, phone_technician) can only be added by owners
if (!in_array($userType, ['customer', 'supplier', 'owner'], true)) {
    $errors[] = 'Invalid user type selected.';
}
```

- ✅ Removed auto-creation of technician profiles
- ✅ Added clear comments about staff role restrictions

---

### 4. **Deleted Files**
- ❌ `fixandgo/staff-register.html` - Public staff registration page
- ❌ `fixandgo/backend/staff-register.php` - Staff registration API

---

## 🔄 New User Flows

### 👤 Customer Registration
```
1. Visit register.html
2. Enter: Name, Email, Password
3. Verify email with OTP
4. ✅ Access customer dashboard
```

### 📦 Supplier Registration
```
1. Register as customer (above)
2. Sign in to customer account
3. Navigate to "Seller Centre" in customer navbar
4. Click "Register as Supplier"
5. Fill application form (different email)
6. Wait for approval
7. ✅ Sign in with supplier credentials
```

### 🏪 Owner Registration
```
1. Register as customer
2. Sign in to customer account
3. Navigate to "Seller Centre" in customer navbar
4. Click "Register as Shop Owner"
5. Fill application form (different email + shop name)
6. Wait for approval
7. ✅ Sign in with owner credentials
```

### 👔 Staff Registration (Sales Person, Supervisor, Technician)
```
1. ❌ NO public registration
2. ✅ Owner creates staff account through staff management interface
3. Staff receives credentials from owner
4. Staff signs in with provided credentials
```

---

## 🎨 User Interface Updates

### Registration Page
- **Title:** "Create your account"
- **Subtitle:** "Join Fix&Go — book repairs, shop accessories, and more"
- **Bottom message:** "Want to become a Supplier or Shop Owner? Sign in and visit **Seller Centre** in your dashboard."

### Seller Centre (Already Implemented)
- Located at: `views/user/customer/seller-centre.html`
- Two cards: "Supplier" and "Shop Owner"
- Modal forms for each role
- Clear benefits and "How it works" section

---

## 🔒 Security & Validation

### Public Registration Endpoint
- ✅ Only accepts: `customer`, `supplier`, `owner`
- ✅ Rejects: `sales_person`, `supervisor`, `phone_technician`
- ✅ CSRF protection
- ✅ Rate limiting
- ✅ Email validation
- ✅ Password strength requirements

### Google OAuth
- ✅ Defaults to `customer` role
- ✅ Auto-verified (is_verified = 1)

---

## 📍 File Locations

### Modified Files
```
fixandgo/
├── register.html                    ✏️ Removed role selection
├── assets/js/register.js            ✏️ Removed role validation
└── backend/register.php             ✏️ Restricted to customer/supplier/owner
```

### Deleted Files
```
fixandgo/
├── staff-register.html              ❌ Deleted
└── backend/staff-register.php       ❌ Deleted
```

### Existing (Unchanged)
```
fixandgo/
├── views/user/customer/seller-centre.html    ✅ Handles Supplier/Owner registration
├── views/user/owner/staff.html               ✅ Owner staff management
└── backend/google-callback.php               ✅ Already defaults to customer
```

---

## ✅ Benefits

1. **Simpler Onboarding** - No confusing role selection for new users
2. **Better UX** - Everyone starts as customer, can explore platform
3. **Controlled Access** - Supplier/Owner go through approval process
4. **Secure Staff Management** - Only owners can create staff accounts
5. **Clear Upgrade Path** - Seller Centre provides obvious next steps
6. **Reduced Errors** - Less chance of users selecting wrong role

---

## 🧪 Testing Checklist

- [ ] Register new customer account
- [ ] Verify email with OTP
- [ ] Access customer dashboard
- [ ] Navigate to Seller Centre
- [ ] Submit Supplier application
- [ ] Submit Owner application
- [ ] Verify staff-register.html returns 404
- [ ] Verify backend rejects staff role from public endpoint
- [ ] Test Google OAuth registration (should be customer)
- [ ] Verify owner can still create staff through staff management

---

## 📝 Notes

- Login page unchanged - supports all user types
- Dashboard routing unchanged - works for all roles
- Staff management interface (for owners) unchanged
- Seller Centre already had complete implementation
- No database schema changes required
