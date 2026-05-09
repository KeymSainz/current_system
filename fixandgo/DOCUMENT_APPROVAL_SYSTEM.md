# Document Approval System

## Overview
The admin can now approve or reject application documents **one by one** instead of approving/rejecting the entire application at once.

## Features

### 1. Individual Document Review
- Each document (Gov ID, BIR, DTI, Bank) can be approved or rejected separately
- Admin can see the status of each document: Pending, Approved, or Rejected
- Documents show preview images when available

### 2. Rejection with Reason
- When rejecting a document, admin **must provide a reason**
- The reason is stored in the database
- Applicant receives a **notification** and **email** with the rejection reason
- Rejected documents can be re-approved after applicant resubmits

### 3. Approval Flow
1. Admin clicks "Docs" button on any applicant
2. Modal shows all documents with their current status
3. Admin reviews each document and clicks "Approve" or "Reject"
4. If rejecting, a prompt asks for the rejection reason
5. Once ALL required documents are approved, a "Approve Application" button appears
6. Admin clicks final approval to activate the seller account

### 4. Notifications
- **Document Rejected**: Applicant gets notified immediately with the reason
- **Application Approved**: Applicant gets notified when fully approved
- Notifications appear in-app and via email

## Database Changes

### New Table: `document_approvals`
```sql
CREATE TABLE document_approvals (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  document_type ENUM('gov_id', 'bir', 'dti', 'bank'),
  status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  rejection_reason TEXT NULL,
  reviewed_by INT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  UNIQUE KEY (application_id, document_type)
);
```

### Updated Table: `seller_applications`
- Added `overall_status` column to track if all docs are approved

## Files Modified

### Backend
- `fixandgo/backend/document_approvals.php` - NEW: API for document approval/rejection
- `fixandgo/backend/admin.php` - Existing approval endpoints still work for final approval
- `fixandgo/backend/migrate_document_approvals.sql` - NEW: Database migration

### Frontend
- `fixandgo/fixandgo/views/admin/dashboard.html` - Updated document modal with approve/reject buttons

## How to Use (Admin)

1. **View Documents**
   - Go to Admin Dashboard → Applicants
   - Click "Docs" button on any pending application
   - Modal opens showing all submitted documents

2. **Review Each Document**
   - Click "Open" to view the full document in a new tab
   - If document is valid: Click "Approve"
   - If document is invalid: Click "Reject" and enter reason

3. **Final Approval**
   - Once all required documents are approved, green banner appears
   - Click "Approve Application" to fully activate the seller account
   - Applicant can now log in

## How It Works (Applicant Side)

1. **Rejection Notification**
   - Applicant receives notification: "Your [Document Name] has been rejected. Reason: [Admin's reason]"
   - Email is also sent with the same information

2. **Resubmission**
   - Applicant can resubmit the rejected document
   - Admin reviews again and can approve

3. **Approval Notification**
   - Once fully approved, applicant receives: "Application Approved! 🎉"
   - They can now log in with their seller account

## Migration Steps

1. Run the SQL migration:
   ```bash
   http://localhost/current_system/fixandgo/backend/migrate_document_approvals.sql
   ```
   Or import it via phpMyAdmin

2. The system is now ready to use!

## Benefits

✅ **More Control**: Admin can reject specific documents without rejecting entire application
✅ **Clear Communication**: Applicants know exactly which document needs fixing
✅ **Better UX**: Applicants don't have to resubmit all documents, just the rejected ones
✅ **Audit Trail**: All document approvals/rejections are tracked with timestamps
✅ **Notifications**: Automatic email and in-app notifications keep applicants informed
