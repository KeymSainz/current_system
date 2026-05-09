# Admin View Documents Feature - Implementation Summary

## Overview
Added the ability for admins to view application documents for suppliers and shop owners even after their applications have been approved or rejected.

---

## What Was Added

### 1. **Backend API Endpoint** ✅
**File:** `fixandgo/backend/admin.php`

**New Endpoint:** `GET /backend/admin.php?action=user_documents&user_id={id}`

**Purpose:** Fetches user information and their application documents from the `seller_applications` table.

**Returns:**
```json
{
  "success": true,
  "user": {
    "id": 123,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "09123456789",
    "role": "supplier"
  },
  "application": {
    "id": 45,
    "company_name": "ABC Trading",
    "shop_name": "ABC Shop",
    "doc_gov_id": "uploads/applications/govIdFile_xxx.png",
    "doc_bir": "uploads/applications/birFile_xxx.png",
    "doc_dti": "uploads/applications/dtiFile_xxx.png",
    "doc_bank": "uploads/applications/bankFile_xxx.png",
    "status": "approved",
    "submitted_at": "2024-01-15 10:30:00",
    "admin_notes": "Approved - all documents verified"
  }
}
```

---

### 2. **Frontend - View Documents Button** ✅
**File:** `fixandgo/fixandgo/views/admin/dashboard.html`

**Added:**
- "Docs" button appears for all suppliers and shop owners in the user list
- Button is styled with blue color to indicate it's an informational action
- Only shows for users with role `supplier` or `owner`

**Button Location:**
- All Users section
- Suppliers section
- Shop Owners section
- Any filtered view showing suppliers/owners

---

### 3. **Frontend - View Documents Function** ✅
**Function:** `viewUserDocs(userId)`

**Features:**
- Fetches user and application data from the backend
- Displays documents in the same modal used for pending applications
- Shows application status badge (Pending/Approved/Rejected)
- Displays admin notes if any were added during review
- Shows submission date
- Displays all uploaded documents with preview for images
- Allows opening documents in new tab

**Document Display:**
- ✅ Government-Issued ID (Required)
- ✅ BIR Certificate (Optional)
- ✅ DTI/SEC Registration (Required for owners)
- ✅ Bank Account Proof (Required)

**Visual Indicators:**
- ✅ Green checkmark for uploaded documents
- ❌ Red X for missing documents
- Image preview for photo documents
- "Open" button to view full document in new tab

---

## How It Works

### User Flow:
1. Admin logs into admin dashboard
2. Navigates to any user section (All Users, Suppliers, or Shop Owners)
3. Sees "Docs" button next to suppliers and shop owners
4. Clicks "Docs" button
5. Modal opens showing:
   - User information
   - Application status (Approved/Rejected/Pending)
   - Admin notes from review
   - All application documents with previews
6. Admin can click "Open" to view full document
7. Admin closes modal when done

---

## Technical Details

### Database Query:
The system queries the `seller_applications` table using the user's email to find their most recent application:

```sql
SELECT id, company_name, shop_name, doc_gov_id, doc_bir, doc_dti, doc_bank, 
       status, submitted_at, admin_notes
FROM seller_applications 
WHERE email = ? 
ORDER BY submitted_at DESC 
LIMIT 1
```

### Security:
- ✅ Admin-only access (requires admin session)
- ✅ User ID validation
- ✅ Email-based lookup (prevents unauthorized access)
- ✅ Proper error handling

### File Paths:
Documents are stored in: `fixandgo/uploads/applications/`

The system automatically constructs the correct relative path from the admin dashboard location.

---

## UI/UX Features

### Button Design:
```
[📄 Docs] - Blue button with document icon
```

### Modal Display:
- **Header:** User name and status badge
- **Info Section:** 
  - Email and phone
  - Role (Supplier/Owner)
  - Company name
  - Shop name (if applicable)
  - Submission date
  - Admin notes (if any)
- **Documents Section:**
  - List of all documents
  - Visual indicators for uploaded/missing
  - Image previews
  - Open in new tab buttons

### Status Badges:
- ⏳ **Pending** - Yellow/Orange
- ✅ **Approved** - Green
- ❌ **Rejected** - Red

---

## Benefits

### For Admins:
1. **Easy Access** - View documents anytime without searching
2. **Quick Reference** - Check documents when handling support requests
3. **Audit Trail** - Review what documents were submitted
4. **Verification** - Re-verify documents if needed
5. **Context** - See admin notes from original review

### For System:
1. **Transparency** - Complete record of applications
2. **Compliance** - Easy document retrieval for audits
3. **Support** - Help resolve user issues faster
4. **Quality Control** - Review approval decisions

---

## Testing Checklist

### Test Scenarios:
- ✅ Click "Docs" button for approved supplier
- ✅ Click "Docs" button for approved shop owner
- ✅ Click "Docs" button for rejected application
- ✅ View documents with all files uploaded
- ✅ View documents with some files missing
- ✅ Open document in new tab
- ✅ View image previews
- ✅ Check admin notes display
- ✅ Verify status badge shows correctly
- ✅ Close modal and reopen
- ✅ Test with different user roles

### Expected Results:
- Button only appears for suppliers and owners
- Modal opens smoothly
- All information displays correctly
- Documents can be opened
- Images show previews
- Missing documents show appropriate message
- Modal closes properly

---

## Files Modified

1. **fixandgo/backend/admin.php**
   - Added `user_documents` action endpoint
   - Queries user and application data
   - Returns JSON response

2. **fixandgo/fixandgo/views/admin/dashboard.html**
   - Added "Docs" button to user rows
   - Added `viewUserDocs()` function
   - Reuses existing documents modal
   - Enhanced modal content for user documents

---

## Future Enhancements (Optional)

### Possible Improvements:
1. **Download All** - Button to download all documents as ZIP
2. **Document History** - Show all applications if user applied multiple times
3. **Document Comparison** - Compare original vs updated documents
4. **Inline Editing** - Allow admins to update admin notes
5. **Document Status** - Mark individual documents as verified/rejected
6. **Notifications** - Alert user when admin views their documents
7. **Activity Log** - Track when admins view documents

---

## Conclusion

The feature is fully implemented and ready to use. Admins can now easily view application documents for any supplier or shop owner directly from the user list, making it convenient to:
- Review past applications
- Verify user information
- Handle support requests
- Conduct audits
- Make informed decisions

The feature integrates seamlessly with the existing admin dashboard and uses the same modal design for consistency.
