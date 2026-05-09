# Seller Centre Path Fix - Implementation Summary

## Issue Identified
The seller centre page was unable to fetch document approval status from the backend API, resulting in a 404 error. This prevented applicants from seeing which documents were rejected and needing resubmission.

## Root Causes

### 1. **Incorrect Absolute Path**
The JavaScript was using an absolute path `/current_system/fixandgo/backend/document_approvals.php` which didn't match the actual server directory structure.

**Files affected:**
- `fixandgo/views/user/customer/seller-centre.html`

**Lines changed:**
- Line 663: `renderPendingUI()` function
- Line 831: `renderRejectedUI()` function  
- Line 1279: `submitResubmission()` function

**Fix:** Changed from absolute path to relative path `../../../backend/document_approvals.php`

### 2. **Overly Restrictive Authentication**
The `document_approvals.php` API had an admin-only check at the top of the file that blocked ALL requests, including the `my_documents` endpoint which should be accessible to applicants viewing their own documents.

**File affected:**
- `fixandgo/backend/document_approvals.php`

**Fix:** 
- Removed the global admin check from the top of the file
- Added specific admin checks to each admin-only action:
  - `get_documents` (admin only)
  - `approve_document` (admin only)
  - `reject_document` (admin only)
  - `notify_rejections` (admin only)
- Left `my_documents` accessible to authenticated users (applicants)
- Left `resubmit_document` accessible to authenticated users (applicants)

## Changes Made

### File: `fixandgo/views/user/customer/seller-centre.html`

**Change 1 - renderPendingUI() function (line ~663):**
```javascript
// BEFORE:
fetch('/current_system/fixandgo/backend/document_approvals.php?action=my_documents&customer_id=' + ...

// AFTER:
fetch('../../../backend/document_approvals.php?action=my_documents&customer_id=' + ...
```

**Change 2 - renderRejectedUI() function (line ~831):**
```javascript
// BEFORE:
fetch('/current_system/fixandgo/backend/document_approvals.php?action=my_documents&customer_id=' + ...

// AFTER:
fetch('../../../backend/document_approvals.php?action=my_documents&customer_id=' + ...
```

**Change 3 - submitResubmission() function (line ~1279):**
```javascript
// BEFORE:
fetch('/current_system/fixandgo/backend/document_approvals.php', { method: 'POST', ...

// AFTER:
fetch('../../../backend/document_approvals.php', { method: 'POST', ...
```

### File: `fixandgo/backend/document_approvals.php`

**Change 1 - Removed global admin check (lines 17-20):**
```php
// BEFORE:
// Admin only
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin access required.']);
    exit;
}

// AFTER:
// (Removed - admin checks now per-action)
```

**Change 2 - Added admin check to get_documents action:**
```php
if ($action === 'get_documents') {
    // Admin only for this action
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin access required.']);
        exit;
    }
    // ... rest of code
}
```

**Change 3 - Added admin check to approve_document action:**
```php
if ($action === 'approve_document') {
    // Admin only for this action
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin access required.']);
        exit;
    }
    // ... rest of code
}
```

**Change 4 - Added admin check to reject_document action:**
```php
if ($action === 'reject_document') {
    // Admin only for this action
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin access required.']);
        exit;
    }
    // ... rest of code
}
```

**Change 5 - Added admin check to notify_rejections action:**
```php
if ($action === 'notify_rejections') {
    // Admin only for this action
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin access required.']);
        exit;
    }
    // ... rest of code
}
```

## Expected Behavior After Fix

### For Applicants (Customer Account):

1. **Pending Application with Rejected Documents:**
   - Page shows "Action Required" banner with red styling
   - Each rejected document displays with:
     - Red X icon and "REJECTED" badge
     - Rejection reason in a highlighted box
     - "Resubmit Document" button
   - Approved/pending documents show with appropriate status badges

2. **Pending Application (No Rejections):**
   - Page shows "Under Review" banner with orange styling
   - All documents show with their current status (pending/approved)
   - No resubmit buttons (not needed yet)

3. **Rejected Application:**
   - Page shows "Application Not Approved" banner with red styling
   - Lists all rejected documents with reasons
   - Shows admin notes if provided
   - "Submit New Application" button to start over

4. **Approved Application:**
   - Page shows "Congratulations" banner with green styling
   - "Switch to Seller Dashboard" button to access seller account

### For Admins:

- All admin functions remain unchanged
- Can still approve/reject documents individually
- Can send rejection notifications to applicants
- Full access to all document approval endpoints

## Testing Steps

1. **Test as Customer Applicant:**
   - Log in with customer account that has a pending application
   - Go to Seller Centre page
   - Open browser console (F12) and check for:
     - "=== CHECK APPLICATION STATUS ===" log
     - "=== APPLICATION STATUS RESPONSE ===" log
     - "=== FETCH RESPONSE RECEIVED ===" log
     - "Response status: 200" (should be 200, not 404)
     - "Success: true"
     - "Has rejected documents: true/false"

2. **Test Document Rejection Flow:**
   - Admin rejects one or more documents with reasons
   - Admin clicks "Send Rejection Notification"
   - Customer refreshes Seller Centre page (Ctrl+F5)
   - Customer should see rejected documents with red styling
   - Customer clicks "Resubmit Document" button
   - Customer uploads new file
   - Document status resets to "pending"

3. **Test Document Approval Flow:**
   - Admin approves all required documents
   - Customer refreshes Seller Centre page
   - Customer should see "Congratulations" banner
   - Customer can switch to seller account

## Security Notes

- `my_documents` endpoint only returns documents for the logged-in user's application
- Applicants cannot view other applicants' documents
- Admin-only actions are protected with role checks
- File uploads are validated for type and size
- All database queries use prepared statements

## Related Files

- `fixandgo/views/user/customer/seller-centre.html` - Frontend UI
- `fixandgo/backend/document_approvals.php` - API endpoint
- `fixandgo/backend/check_application.php` - Application status check
- `fixandgo/fixandgo/views/admin/dashboard.html` - Admin document review UI

## Date
May 6, 2026
