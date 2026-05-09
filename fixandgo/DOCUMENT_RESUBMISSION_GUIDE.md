# Document Resubmission System — Implementation Guide

## Overview
This system allows applicants (suppliers and shop owners) to view their application document status and resubmit rejected documents directly from the Seller Centre.

---

## Features Implemented

### 1. **Admin Side** (Already Complete)
- ✅ Individual document approval/rejection with reasons
- ✅ Main "Approve" button disabled until all documents are approved
- ✅ Rejection reasons stored and displayed
- ✅ Email and in-app notifications sent to applicants when documents are rejected

### 2. **Applicant Side** (NEW)
- ✅ View application status with document-by-document breakdown
- ✅ See which documents are pending, approved, or rejected
- ✅ View rejection reasons for each rejected document
- ✅ Resubmit rejected documents with new file upload
- ✅ Automatic notification to admin when document is resubmitted
- ✅ Document status resets to "pending" after resubmission

---

## How It Works

### For Applicants (Supplier/Owner)

1. **Check Application Status**
   - Go to **Seller Centre** page
   - If application is pending, they see a detailed status breakdown

2. **View Document Status**
   - Each document shows one of three statuses:
     - ⏳ **Pending** — Waiting for admin review
     - ✅ **Approved** — Document accepted
     - ❌ **Rejected** — Document needs resubmission

3. **Resubmit Rejected Documents**
   - Click "Resubmit Document" button on rejected documents
   - Upload a new file (JPG, PNG, or PDF, max 5MB)
   - Submit for re-review
   - Document status changes back to "Pending"
   - Admin receives notification about resubmission

4. **Notifications**
   - Applicants receive **email + in-app notification** when documents are rejected
   - Notification includes the rejection reason
   - They can click through to Seller Centre to resubmit

### For Admins

1. **Review Documents**
   - Click "Docs" button on any applicant
   - Approve or reject each document individually
   - Provide rejection reason (required)

2. **Resubmission Handling**
   - When applicant resubmits, admin receives notification
   - Resubmitted document appears as "Pending" again
   - Admin can approve or reject the new document
   - Process repeats until all documents are approved

3. **Final Approval**
   - Once all required documents are approved, "Approve Application" button becomes enabled
   - Click to fully approve the application
   - Applicant can then log in and start selling

---

## API Endpoints

### Backend: `fixandgo/backend/document_approvals.php`

#### GET Endpoints

**1. Get Documents for Admin Review**
```
GET /backend/document_approvals.php?action=get_documents&application_id={id}
```
- **Auth**: Admin only
- **Returns**: Application details + document list with approval statuses

**2. Get Applicant's Own Documents**
```
GET /backend/document_approvals.php?action=my_documents&customer_id={id}
```
- **Auth**: Customer (applicant) session
- **Returns**: Their application + document statuses
- **Used by**: Seller Centre page to show document status

#### POST Endpoints

**1. Approve Document** (Admin)
```json
POST /backend/document_approvals.php
{
  "action": "approve_document",
  "application_id": 123,
  "document_type": "gov_id"
}
```

**2. Reject Document** (Admin)
```json
POST /backend/document_approvals.php
{
  "action": "reject_document",
  "application_id": 123,
  "document_type": "gov_id",
  "reason": "ID is blurry and unreadable"
}
```

**3. Resubmit Document** (Applicant)
```
POST /backend/document_approvals.php
Content-Type: multipart/form-data

action=resubmit_document
application_id=123
document_type=gov_id
gov_idFile={file}
```
- **Auth**: Customer (applicant) session
- **File Upload**: Uses FormData with file field named `{document_type}File`
- **Actions**:
  - Uploads new file to `uploads/applications/`
  - Updates `seller_applications` table with new file path
  - Resets `document_approvals` status to "pending"
  - Clears rejection reason
  - Notifies all admins about resubmission

---

## Database Schema

### Table: `document_approvals`
```sql
CREATE TABLE document_approvals (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  application_id INT UNSIGNED NOT NULL,
  document_type ENUM('gov_id', 'bir', 'dti', 'bank') NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  rejection_reason TEXT NULL,
  reviewed_by INT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  UNIQUE KEY unique_app_doc (application_id, document_type),
  FOREIGN KEY (application_id) REFERENCES seller_applications(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### Table: `seller_applications` (Modified)
Added column:
```sql
ALTER TABLE seller_applications 
  ADD COLUMN overall_status ENUM('pending', 'docs_approved', 'approved', 'rejected') 
  NOT NULL DEFAULT 'pending' AFTER status;
```

**Status Flow:**
- `pending` — Initial state or has rejected documents
- `docs_approved` — All documents approved, ready for final approval
- `approved` — Fully approved by admin, can log in
- `rejected` — Application rejected

---

## Files Modified

### 1. **Backend API**
- `fixandgo/backend/document_approvals.php`
  - Added `my_documents` GET endpoint for applicants
  - Added `resubmit_document` POST endpoint with file upload handling
  - Handles multipart/form-data for file uploads
  - Sends notifications to admins on resubmission

### 2. **Applicant UI**
- `fixandgo/views/user/customer/seller-centre.html`
  - Updated `renderPendingUI()` to fetch and display document statuses
  - Added document status cards with approve/reject/pending badges
  - Added "Resubmit Document" buttons for rejected documents
  - Added resubmit modal with file upload
  - Added `openResubmitModal()`, `submitResubmission()`, `previewResubmitDoc()` functions

### 3. **Admin UI** (Already Complete)
- `fixandgo/fixandgo/views/admin/dashboard.html`
  - Individual approve/reject buttons per document
  - Rejection reason input
  - Main approve button disabled until all docs approved
  - Shows rejection reasons in document modal

---

## User Flow Example

### Scenario: Supplier applies with blurry ID photo

1. **Supplier submits application**
   - Uploads all documents including government ID
   - Application status: "Pending"

2. **Admin reviews documents**
   - Opens "Docs" modal
   - Approves BIR and Bank documents
   - Rejects Government ID with reason: "Photo is too blurry, please upload a clearer image"
   - Supplier receives email + in-app notification

3. **Supplier checks Seller Centre**
   - Sees application status: "Action Required"
   - Document breakdown shows:
     - ✅ BIR Certificate — Approved
     - ✅ Bank Account Proof — Approved
     - ❌ Government ID — Rejected
   - Rejection reason displayed: "Photo is too blurry, please upload a clearer image"
   - Clicks "Resubmit Document" button

4. **Supplier resubmits**
   - Uploads new, clearer ID photo
   - Clicks "Upload & Resubmit"
   - Success message: "Document resubmitted successfully. Admin will review it shortly."
   - Document status changes to "⏳ Pending"

5. **Admin receives notification**
   - "An applicant has resubmitted their Government-Issued ID. Please review the updated document."
   - Opens applicant's documents
   - Reviews new ID photo
   - Approves it

6. **All documents approved**
   - "Approve Application" button becomes enabled
   - Admin clicks to fully approve
   - Supplier receives approval email
   - Supplier can now log in and start selling

---

## Testing Checklist

### Admin Side
- [ ] Can view all applicant documents
- [ ] Can approve individual documents
- [ ] Can reject individual documents with reason
- [ ] Rejection reason is required
- [ ] Main "Approve" button is disabled until all docs approved
- [ ] Main "Approve" button enables when all docs approved
- [ ] Applicant receives notification when document is rejected

### Applicant Side
- [ ] Can view application status in Seller Centre
- [ ] Sees document-by-document breakdown
- [ ] Pending documents show "⏳ Pending" badge
- [ ] Approved documents show "✅ Approved" badge
- [ ] Rejected documents show "❌ Rejected" badge
- [ ] Rejection reason is displayed for rejected documents
- [ ] "Resubmit Document" button appears for rejected documents
- [ ] Can upload new file (JPG, PNG, PDF)
- [ ] File size validation (max 5MB)
- [ ] Resubmission success message appears
- [ ] Document status changes to "Pending" after resubmission
- [ ] Admin receives notification about resubmission

### Notifications
- [ ] Applicant receives email when document is rejected
- [ ] Applicant receives in-app notification when document is rejected
- [ ] Admin receives notification when document is resubmitted
- [ ] Notification includes document name and rejection reason

---

## Security Notes

1. **File Upload Security**
   - Only JPG, PNG, PDF allowed
   - Max file size: 5MB
   - Files stored in `uploads/applications/` with unique names
   - File extension validated server-side

2. **Authentication**
   - Applicants can only view/resubmit their own documents
   - Admin-only endpoints check `$_SESSION['user_role'] === 'admin'`
   - Customer session required for resubmission

3. **Database**
   - Foreign key constraints prevent orphaned records
   - Unique constraint on `(application_id, document_type)` prevents duplicates
   - Cascade delete removes approvals when application is deleted

---

## Next Steps (Optional Enhancements)

1. **Document History**
   - Track all versions of resubmitted documents
   - Show submission history in admin view

2. **Bulk Actions**
   - Allow admin to approve all pending documents at once
   - Bulk reject with same reason

3. **Document Preview**
   - Show document preview in resubmit modal
   - Side-by-side comparison of old vs new document

4. **Auto-Reminders**
   - Send reminder email if rejected documents not resubmitted within 7 days
   - Auto-reject application if no resubmission after 30 days

5. **Analytics**
   - Track average time to approval
   - Most commonly rejected document types
   - Resubmission success rate

---

## Support

For questions or issues:
- Check `fixandgo/DOCUMENT_APPROVAL_SYSTEM.md` for admin-side documentation
- Check `fixandgo/SETUP_DOCUMENT_APPROVAL.txt` for setup instructions
- Review database migration: `fixandgo/backend/migrate_document_approvals.sql`

**Migration Status Check:**
```bash
php fixandgo/backend/check_migration.php
```

---

**Implementation Complete! ✅**

Applicants can now view their document status and resubmit rejected documents seamlessly.
