# Staff Management System Guide

## Overview

The staff management system allows:
1. **Staff** to register for positions (Sales Person, Supervisor, Phone Technician)
2. **Owner** to review and approve/reject applications
3. **Owner** to manage active staff (activate/deactivate)

## Staff Registration Process

### For Staff Applicants:

1. **Visit Registration Page:**
   - URL: `staff-register.html`
   - Or click "Staff Registration" link on main register page

2. **Fill Application Form:**
   - Select position (Sales Person, Supervisor, or Phone Technician)
   - Enter personal details (name, email, phone)
   - Create password (minimum 8 characters)

3. **Submit Application:**
   - Application is submitted with status "Pending"
   - Account is inactive until owner approves
   - Cannot login until approved

4. **Wait for Approval:**
   - Owner reviews application
   - If approved: Account activated, can login
   - If rejected: Application deleted

### Available Positions:

**📊 Sales Person**
- Handle customer inquiries
- Process bookings
- Manage sales

**👔 Supervisor**
- Oversee operations
- Manage staff
- Ensure quality service

**🔧 Phone Technician**
- Repair phones
- Diagnose issues
- Provide technical support

## Owner Management

### Access Staff Management:

1. Login as Owner
2. Go to Dashboard
3. Click "Manage Staff" in sidebar
4. URL: `views/user/owner/staff.html`

### Features:

#### 1. **Statistics Dashboard**
- Total Staff count
- Active staff count
- Pending applications count
- Technicians count

#### 2. **Pending Applications Tab**
- View all pending staff applications
- See applicant details (name, email, phone, position)
- Application date
- Actions:
  - ✅ **Approve** - Activate account, allow login
  - ❌ **Reject** - Delete application

#### 3. **Active Staff Tab**
- View all approved staff members
- See staff details and status
- Join date
- Actions:
  - ⏸️ **Deactivate** - Disable login (temporary)
  - ▶️ **Activate** - Re-enable login

## Database Structure

### Users Table:
```sql
- is_active = 0 → Pending/Deactivated (cannot login)
- is_active = 1 → Active (can login)
- is_verified = 0 → Pending approval
- is_verified = 1 → Approved by owner
```

### Staff Registration Flow:
```
1. Staff registers → is_active=0, is_verified=0
2. Owner approves → is_active=1, is_verified=1
3. Staff can login → Full access
```

### Deactivation:
```
- Owner deactivates → is_active=0 (is_verified stays 1)
- Staff cannot login
- Owner can reactivate → is_active=1
```

## URLs

- **Staff Registration:** `fixandgo/staff-register.html`
- **Owner Staff Management:** `fixandgo/views/user/owner/staff.html`

## Backend APIs

### Staff Registration:
- **File:** `backend/staff-register.php`
- **Method:** POST
- **Body:**
  ```json
  {
    "role": "sales_person|supervisor|phone_technician",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+63 912 345 6789",
    "password": "password123"
  }
  ```

### Owner Staff Management:
- **File:** `backend/owner_staff.php`
- **Methods:**
  - GET ?action=pending - Get pending applications
  - GET ?action=active - Get active staff
  - GET ?action=stats - Get statistics
  - POST action=approve - Approve application(s)
  - POST action=reject - Reject application(s)
  - POST action=deactivate - Deactivate staff
  - POST action=activate - Reactivate staff

## Security Features

### Registration:
- ✅ Email validation
- ✅ Password hashing (bcrypt, cost 12)
- ✅ Duplicate email check
- ✅ Role validation
- ✅ Minimum password length (8 characters)

### Management:
- ✅ Owner-only access
- ✅ Session-based authentication
- ✅ SQL injection protection (prepared statements)
- ✅ Confirmation dialogs for actions

## User Flow Examples

### Example 1: New Technician Application

1. **John applies as Phone Technician:**
   - Visits `staff-register.html`
   - Fills form, submits
   - Account created: is_active=0, is_verified=0
   - Technician profile created automatically

2. **Owner reviews:**
   - Sees John in "Pending Applications"
   - Reviews details
   - Clicks "Approve"
   - John's account: is_active=1, is_verified=1

3. **John can now login:**
   - Goes to login page
   - Enters credentials
   - Redirected to technician dashboard

### Example 2: Deactivating Staff

1. **Owner needs to temporarily disable a staff member:**
   - Goes to "Active Staff" tab
   - Finds staff member
   - Clicks "Deactivate"
   - Staff account: is_active=0

2. **Staff member cannot login:**
   - Tries to login
   - Account is inactive
   - Cannot access system

3. **Owner reactivates later:**
   - Clicks "Activate"
   - Staff account: is_active=1
   - Staff can login again

## Benefits

### For Staff:
- ✅ Self-service registration
- ✅ Clear position descriptions
- ✅ Immediate application submission
- ✅ Professional onboarding process

### For Owner:
- ✅ Centralized staff management
- ✅ Review applications before granting access
- ✅ Control over who can access the system
- ✅ Easy activation/deactivation
- ✅ Statistics and overview
- ✅ No manual account creation needed

## Navigation

### Owner Sidebar:
```
Dashboard
├── Manage Products
├── Purchase History
├── Manage Staff        ← New!
├── Bookings
├── Deliveries
├── Revenue Report
├── Messages
└── Profile
```

## Testing

### Test the Flow:

1. **Register as Staff:**
   - Go to `staff-register.html`
   - Fill form with test data
   - Submit application

2. **Login as Owner:**
   - Go to "Manage Staff"
   - See pending application
   - Approve it

3. **Login as Staff:**
   - Use staff credentials
   - Should be able to login
   - Access appropriate dashboard

4. **Deactivate/Reactivate:**
   - Owner deactivates staff
   - Staff cannot login
   - Owner reactivates
   - Staff can login again

## Future Enhancements

Potential improvements:
- Email notifications for approval/rejection
- Staff profile editing
- Role-based permissions
- Staff performance tracking
- Shift scheduling
- Attendance tracking
- Salary management
