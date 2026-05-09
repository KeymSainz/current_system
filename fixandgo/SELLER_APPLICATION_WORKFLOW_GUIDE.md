# Seller Application Workflow Guide

## Overview

The Fix&Go platform includes a complete seller application system that allows customers to apply to become Suppliers or Shop Owners. The workflow includes document submission, admin review, approval/rejection, and automatic notifications.

## Complete Workflow

### 1. Customer Submits Application

**Location:** `fixandgo/views/user/customer/seller-centre.html`

**Process:**
1. Customer logs in to their account
2. Navigates to "Seller Centre" from the sidebar
3. Chooses between two roles:
   - **Supplier** - Supply phone parts and accessories
   - **Shop Owner** - Manage a phone repair shop

4. Fills out the application form:
   - Personal Information (First Name, Last Name)
   - Company Name
   - Email (must be different from customer email)
   - Phone Number
   - Shop Name (Shop Owners only)
   - Password for new seller account
   
5. Uploads required documents:
   - **Government-Issued ID** (required) - Driver's License, Passport, National ID, or PhilSys
   - **BIR Certificate of Registration** (recommended) - BIR Form 2303
   - **DTI/SEC Business Registration** (required for Shop Owners) - DTI Business Name Certificate or SEC Registration
   - **Bank Account Proof** (required) - Bank statement, passbook, or account details screenshot

6. Submits the application

**Backend:** `fixandgo/backend/seller_apply.php`

**What Happens:**
- Application is saved to `seller_applications` table with status `'pending'`
- Documents are uploaded to `uploads/applications/` directory
- A new user account is created with:
  - `is_active = 0` (inactive until approved)
  - `application_status = 'pending'`
  - Role set to 'supplier' or 'owner'
- Admin receives an email notification about the new application
- Customer sees success message: "Application submitted! Our admin will review your documents within 1–2 business days."

---

### 2. Admin Reviews Application

**Location:** `fixandgo/fixandgo/views/admin/dashboard.html`

**Process:**
1. Admin logs in to the admin dashboard
2. Sees pending applications count in:
   - Dashboard stats (orange badge if pending > 0)
   - Sidebar "Applicants" link (red badge with count)
   - Dashboard "Pending Applications" table

3. Clicks "Applicants" to view all pending applications
4. For each application, admin can:
   - **View Documents** - Opens modal showing all uploaded documents
   - **Approve** - Opens approval modal
   - **Reject** - Opens rejection modal

**Viewing Documents:**
- Clicks "Docs" button
- Modal displays all uploaded documents:
  - Government ID
  - BIR Certificate (if provided)
  - DTI/SEC Registration (if provided)
  - Bank Account Proof
- Documents are displayed as images or PDF previews
- Admin can download or view in full size

---

### 3. Admin Approves Application

**Process:**
1. Admin clicks "Approve" button
2. Approval modal opens showing:
   - Applicant name
   - Email
   - Role (Supplier or Shop Owner)
3. Admin can add optional welcome notes
4. Admin clicks "Approve" to confirm

**Backend:** `fixandgo/backend/admin.php` (action=approve)

**What Happens:**
1. **Database Updates:**
   - `seller_applications` table:
     - `status` = 'approved'
     - `admin_notes` = notes from admin
     - `reviewed_by` = admin user ID
     - `reviewed_at` = current timestamp
   
   - `users` table:
     - `is_active` = 1 (account activated)
     - `application_status` = 'approved'
     - `application_notes` = notes from admin
     - `reviewed_by` = admin user ID
     - `reviewed_at` = current timestamp

2. **In-App Notification Sent:**
   - Notification sent to the new seller account
   - Type: 'system'
   - Title: "Application Approved! 🎉"
   - Body: "Congratulations! Your [Role] application has been approved. You can now log in and start using your seller account."
   - If admin added notes, they're included in the notification

3. **Email Sent:**
   - Subject: "Application Approved — Fix&Go [Role]"
   - Contains:
     - Congratulations message
     - Next steps (login, complete profile, add products)
     - Admin notes (if any)
     - Login link

4. **User Can Now:**
   - Log in using their seller email and password
   - Access their seller dashboard
   - Complete shop setup
   - Add products
   - Start selling

---

### 4. Admin Rejects Application

**Process:**
1. Admin clicks "Reject" button
2. Rejection modal opens showing:
   - Applicant name
   - Email
3. Admin **must** provide a rejection reason
4. Admin clicks "Reject" to confirm

**Backend:** `fixandgo/backend/admin.php` (action=reject)

**What Happens:**
1. **Database Updates:**
   - `seller_applications` table:
     - `status` = 'rejected'
     - `admin_notes` = rejection reason
     - `reviewed_by` = admin user ID
     - `reviewed_at` = current timestamp
   
   - `users` table:
     - `is_active` = 0 (account remains inactive)
     - `application_status` = 'rejected'
     - `application_notes` = rejection reason
     - `reviewed_by` = admin user ID
     - `reviewed_at` = current timestamp

2. **In-App Notification Sent:**
   - Notification sent to the **original customer account** (not the seller account)
   - Type: 'system'
   - Title: "Application Update"
   - Body: "Unfortunately, your [Role] application has been rejected. Reason: [rejection reason]"

3. **Email Sent:**
   - Subject: "Application Update — Fix&Go [Role]"
   - Contains:
     - Polite rejection message
     - Rejection reason
     - Option to contact support or reapply

4. **User Status:**
   - Seller account remains inactive
   - User cannot log in with seller credentials
   - Customer account remains active
   - User can reapply with updated information

---

## Database Schema

### `seller_applications` Table

```sql
CREATE TABLE IF NOT EXISTS seller_applications (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id         INT UNSIGNED NOT NULL,           -- Customer who applied
  role            VARCHAR(20)  NOT NULL,           -- 'supplier' or 'owner'
  first_name      VARCHAR(100) NOT NULL,
  last_name       VARCHAR(100) NOT NULL,
  email           VARCHAR(255) NOT NULL,           -- Email for new seller account
  phone           VARCHAR(20)  NULL,
  company_name    VARCHAR(255) NULL,
  shop_name       VARCHAR(255) NULL,               -- For owners only
  doc_gov_id      VARCHAR(255) NULL,               -- Path to government ID
  doc_bir         VARCHAR(255) NULL,               -- Path to BIR certificate
  doc_dti         VARCHAR(255) NULL,               -- Path to DTI/SEC registration
  doc_bank        VARCHAR(255) NULL,               -- Path to bank proof
  status          VARCHAR(20)  NOT NULL DEFAULT 'pending',  -- 'pending', 'approved', 'rejected'
  admin_notes     TEXT         NULL,               -- Admin's notes/reason
  reviewed_by     INT UNSIGNED NULL,               -- Admin user ID
  reviewed_at     DATETIME     NULL,
  submitted_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  INDEX idx_status (status),
  INDEX idx_user_role (user_id, role),
  CONSTRAINT fk_seller_app_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### `users` Table (Relevant Fields)

```sql
is_active            TINYINT(1)   NOT NULL DEFAULT 1,
application_status   VARCHAR(20)  NULL,      -- 'pending', 'approved', 'rejected'
application_notes    TEXT         NULL,
reviewed_by          INT UNSIGNED NULL,
reviewed_at          DATETIME     NULL,
```

---

## File Structure

### Frontend Files

```
fixandgo/views/user/customer/seller-centre.html
  └─ Customer-facing seller application form
  
fixandgo/fixandgo/views/admin/dashboard.html
  └─ Admin dashboard with applicant review interface
```

### Backend Files

```
fixandgo/backend/seller_apply.php
  └─ Handles application submission
  
fixandgo/backend/admin.php
  └─ Handles approve/reject actions
  
fixandgo/backend/notification_helper.php
  └─ Helper functions for sending notifications
  
fixandgo/backend/mailer.php
  └─ Email sending functionality
```

### Document Storage

```
fixandgo/uploads/applications/
  ├─ govIdFile_[uniqid].jpg
  ├─ birFile_[uniqid].pdf
  ├─ dtiFile_[uniqid].jpg
  └─ bankFile_[uniqid].png
```

---

## Notification Flow

### When Application is Submitted

1. **Admin receives email:**
   - Subject: "New [Role] Application — Fix&Go Seller Centre"
   - Contains applicant details and document checklist
   - Prompts admin to review in dashboard

### When Application is Approved

1. **New seller receives in-app notification:**
   - Visible in notification bell (navbar)
   - Type: system
   - Title: "Application Approved! 🎉"
   
2. **New seller receives email:**
   - Subject: "Application Approved — Fix&Go [Role]"
   - Welcome message with next steps
   - Login link

### When Application is Rejected

1. **Customer receives in-app notification:**
   - Sent to original customer account
   - Type: system
   - Title: "Application Update"
   - Contains rejection reason
   
2. **Customer receives email:**
   - Subject: "Application Update — Fix&Go [Role]"
   - Polite rejection message
   - Rejection reason
   - Contact information

---

## Admin Dashboard Features

### Statistics Display

- **Pending Applications Count** - Highlighted in orange if > 0
- **Total Suppliers** - Count of approved suppliers
- **Total Shop Owners** - Count of approved owners
- **New Today** - Applications submitted today

### Applicants Table

Displays for each pending application:
- Application ID
- Applicant name and company
- Email
- Phone
- Role (Supplier or Shop Owner)
- Submission date
- Action buttons:
  - **Docs** - View uploaded documents
  - **Approve** - Approve application
  - **Reject** - Reject application

### Document Viewer

Modal that displays:
- Government ID (image/PDF)
- BIR Certificate (if uploaded)
- DTI/SEC Registration (if uploaded)
- Bank Account Proof (image/PDF)

Each document can be:
- Viewed inline
- Downloaded
- Opened in new tab

---

## Security Features

### Document Upload Security

- **File Type Validation:** Only JPG, PNG, PDF allowed
- **File Size Limit:** Maximum 5MB per file
- **Secure Storage:** Files stored outside web root with unique names
- **Access Control:** Only admins can view documents

### Application Security

- **Email Uniqueness:** Seller email must be different from customer email
- **Duplicate Prevention:** Cannot submit multiple pending applications for same role
- **Session Validation:** Must be logged in as customer to apply
- **Admin-Only Actions:** Only admins can approve/reject applications

### Password Security

- **Minimum Length:** 8 characters required
- **Bcrypt Hashing:** Passwords hashed with bcrypt
- **Confirmation Required:** Password must be entered twice

---

## User Experience Flow

### For Customers

1. **Discovery**
   - See "Seller Centre" in sidebar
   - Learn about supplier and owner roles
   - View requirements and benefits

2. **Application**
   - Fill out form with personal and business details
   - Upload required documents
   - Submit application

3. **Waiting**
   - See success message
   - Receive confirmation that admin will review within 1-2 days
   - Can continue using customer account normally

4. **Approval**
   - Receive in-app notification
   - Receive email with login instructions
   - Can log in with new seller credentials

5. **Rejection**
   - Receive in-app notification with reason
   - Receive email explaining rejection
   - Can contact support or reapply

### For Admins

1. **Notification**
   - See pending count in dashboard
   - Receive email for each new application

2. **Review**
   - View applicant details
   - Check uploaded documents
   - Verify information

3. **Decision**
   - Approve with optional welcome message
   - OR Reject with required reason

4. **Follow-up**
   - Applicant automatically notified
   - Email sent automatically
   - Application removed from pending list

---

## Testing the Workflow

### Test as Customer

1. Log in as a customer
2. Go to Seller Centre
3. Click "Register as Supplier" or "Register as Shop Owner"
4. Fill out the form with test data
5. Upload sample documents (use any JPG/PNG/PDF files)
6. Submit application
7. Verify success message appears

### Test as Admin

1. Log in as admin
2. Check dashboard for pending applications
3. Click "Applicants" in sidebar
4. Click "Docs" to view uploaded documents
5. Click "Approve" and add optional notes
6. Verify success message
7. Check that application is removed from pending list

### Test Notifications

1. After approval, log in as the new seller
2. Check notification bell in navbar
3. Verify approval notification appears
4. Check email inbox for approval email

### Test Rejection

1. Submit another application as customer
2. Log in as admin and reject it with a reason
3. Log back in as customer
4. Check notification bell for rejection notification
5. Check email for rejection email

---

## Troubleshooting

### Application Not Appearing in Admin Dashboard

- Check that application status is 'pending'
- Verify admin is logged in correctly
- Refresh the applicants page
- Check browser console for errors

### Documents Not Uploading

- Verify file size is under 5MB
- Check file type is JPG, PNG, or PDF
- Ensure `uploads/applications/` directory exists and is writable
- Check PHP upload limits in php.ini

### Notifications Not Sending

- Verify notification_helper.php is included
- Check that user ID is correct
- Verify notifications table exists
- Check browser console for errors

### Emails Not Sending

- Verify SMTP settings in config.php
- Check mailer.php is working
- Test email sending separately
- Check spam folder

---

## Future Enhancements

Potential improvements for the seller application system:

1. **Application Status Tracking**
   - Show application status in customer dashboard
   - Display progress timeline
   - Show estimated review time

2. **Document Verification**
   - OCR for automatic ID verification
   - Integration with government databases
   - Automated document validation

3. **Multi-Step Application**
   - Break application into multiple steps
   - Save progress between steps
   - Allow editing before final submission

4. **Application History**
   - Show previous applications
   - Allow reapplication with pre-filled data
   - Track rejection reasons

5. **Admin Notes System**
   - Internal notes visible only to admins
   - Application review checklist
   - Flagging system for suspicious applications

6. **Bulk Actions**
   - Approve/reject multiple applications at once
   - Export applications to CSV
   - Batch document download

---

## Support

For issues or questions about the seller application system:
- Refer to this guide for workflow details
- Check the main Fix&Go documentation
- Contact the development team

---

**Last Updated:** May 3, 2026  
**Version:** 1.0.0
