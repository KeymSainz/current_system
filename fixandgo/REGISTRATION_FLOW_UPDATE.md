# Registration Flow Update

## Overview
Updated the Fix&Go registration system to streamline user onboarding. All new users now register as **customers first**, with the ability to upgrade to Supplier or Owner roles through the Seller Centre.

## Changes Made

### 1. Public Registration (register.html)
- **Removed**: Role selection dropdown
- **Default**: All new registrations are now **customer** accounts
- **Updated messaging**: Clear guidance that Supplier/Owner registration is available through Seller Centre after signing in

### 2. Registration JavaScript (assets/js/register.js)
- **Removed**: `userTypeSelect` variable and validation logic
- **Simplified**: Form validation no longer checks for role selection
- **Streamlined**: Submit button state management updated

### 3. Backend Registration (backend/register.php)
- **Restricted roles**: Only accepts `customer`, `supplier`, and `owner` registrations
- **Removed**: Staff roles (`sales_person`, `supervisor`, `phone_technician`) from public registration
- **Added comment**: Staff roles can only be created by owners through staff management interface
- **Removed**: Auto-creation of technician profiles (now handled by owner)

### 4. Deleted Files
- ❌ `fixandgo/staff-register.html` - Staff registration page removed
- ❌ `fixandgo/backend/staff-register.php` - Staff registration API removed

## New Registration Flow

### For Customers
1. Visit `register.html`
2. Fill in basic information (name, email, password)
3. Verify email with OTP
4. Access customer dashboard

### For Suppliers & Owners
1. Register as a customer (above flow)
2. Sign in to customer account
3. Navigate to **Seller Centre** in customer dashboard
4. Click "Register as Supplier" or "Register as Shop Owner"
5. Fill in seller application form (uses different email)
6. Wait for admin approval
7. Sign in with seller account credentials

### For Staff (Sales Person, Supervisor, Phone Technician)
1. **Owner creates staff accounts** through the staff management interface
2. Staff receives credentials from owner
3. Staff signs in with provided credentials
4. No public self-registration available

## Benefits

✅ **Simplified onboarding**: New users don't need to choose roles upfront
✅ **Better user experience**: Everyone starts as a customer, can explore the platform
✅ **Controlled seller access**: Supplier/Owner applications go through approval process
✅ **Secure staff management**: Only owners can create staff accounts
✅ **Clear upgrade path**: Seller Centre provides obvious path to become a seller
✅ **Reduced confusion**: No complex role selection during initial registration

## Files Modified

### Frontend
- `fixandgo/register.html` - Removed role selection, updated messaging
- `fixandgo/assets/js/register.js` - Removed role validation logic

### Backend
- `fixandgo/backend/register.php` - Restricted to customer/supplier/owner only

### Seller Centre (Already Implemented)
- `fixandgo/views/user/customer/seller-centre.html` - Handles Supplier/Owner applications

## Testing Checklist

- [ ] New user can register as customer without role selection
- [ ] Customer receives OTP and can verify email
- [ ] Customer can access dashboard after verification
- [ ] Customer can navigate to Seller Centre
- [ ] Supplier application form works from Seller Centre
- [ ] Owner application form works from Seller Centre
- [ ] Staff registration page is no longer accessible
- [ ] Backend rejects staff role registrations from public endpoint
- [ ] Owner can still create staff accounts through staff management interface

## Notes

- The Seller Centre already has the complete implementation for Supplier/Owner registration
- Staff management interface (for owners) remains unchanged
- Login flow remains unchanged - supports all user types
- Google OAuth registration defaults to customer role
