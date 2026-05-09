# Testing Guide: Seller Centre Document Rejection Display

## What Was Fixed

The seller centre page now correctly displays rejected documents to applicants. Previously, the page showed "Application Pending Review" even after admin rejected documents and sent notifications.

## Quick Test Steps

### Step 1: Admin Rejects Documents

1. Log in as **Admin**
2. Go to Admin Dashboard
3. Find a pending seller application
4. Click "Review Documents"
5. For at least one document:
   - Click the red "Reject" button
   - Enter a rejection reason (e.g., "Image is blurry, please upload a clearer photo")
   - Click "Reject Document"
6. Click the orange "Send Rejection Notification" button
7. Confirm notification was sent (should show success message)

### Step 2: Customer Views Rejected Documents

1. Log out from admin account
2. Log in as the **Customer** who submitted the application
   - Use the CUSTOMER email (the one they're logged in with)
   - NOT the seller application email
3. Go to **Seller Centre** page
4. **Hard refresh the page** (Ctrl+F5 or Cmd+Shift+R)
5. Open browser console (F12) to check logs

### Step 3: Verify Display

**What you should see:**

✅ **Red "Action Required" banner** instead of orange "Under Review"
✅ **Rejected documents** displayed with:
   - Red X icon
   - "REJECTED" badge in red
   - Rejection reason in a highlighted box
   - "Resubmit Document" button

✅ **Approved/Pending documents** shown with appropriate status badges

**Console logs should show:**
```
=== CHECK APPLICATION STATUS ===
User: {id: X, email: "...", role: "customer"}
=== APPLICATION STATUS RESPONSE ===
Data: {success: true, application: {...}}
Application found: {id: X, status: "pending", ...}
Application status: pending
Rendering PENDING UI
=== RENDER PENDING UI CALLED ===
Application ID: X
Customer ID: X
Fetching document statuses for pending application: X
=== FETCH RESPONSE RECEIVED ===
Response status: 200  ← SHOULD BE 200, NOT 404
=== DOCUMENT API RESPONSE ===
Full response: {success: true, documents: [...]}
Success: true
Documents: [...]
=== DOCUMENT ANALYSIS ===
Has rejected documents: true  ← SHOULD BE TRUE if documents were rejected
Total documents: 4
Rejected count: 1 (or more)
```

### Step 4: Test Document Resubmission

1. Click the **"Resubmit Document"** button on a rejected document
2. Upload a new file (JPG, PNG, or PDF, max 5MB)
3. Click **"Upload & Resubmit"**
4. Should see success message
5. Page should refresh automatically
6. Document status should change from "REJECTED" to "PENDING"

### Step 5: Admin Reviews Resubmitted Document

1. Log in as **Admin**
2. Go to Admin Dashboard
3. Find the same application (should be back to "pending")
4. Click "Review Documents"
5. The resubmitted document should show as "PENDING" with the new file
6. Admin can approve or reject again

## Troubleshooting

### Issue: Still shows "Application Pending Review" (no rejected documents)

**Check:**
1. Did you hard refresh (Ctrl+F5)?
2. Are you logged in with the CUSTOMER account (not the seller email)?
3. Check console for errors:
   - If "Response status: 404" → Path issue (should be fixed now)
   - If "Response status: 403" → Authentication issue
   - If "Has rejected documents: false" → Documents weren't actually rejected

**Solution:**
- Clear browser cache
- Log out and log back in
- Check that admin actually clicked "Send Rejection Notification"

### Issue: Console shows "Response status: 404"

**This means the path is still wrong.**

**Check:**
1. What is your actual server directory structure?
2. Where is the `fixandgo` folder located?
3. Try these paths in order:
   - `../../../backend/document_approvals.php` (current fix)
   - `../../backend/document_approvals.php` (if fixandgo is one level up)
   - `/fixandgo/backend/document_approvals.php` (if fixandgo is at root)

### Issue: Console shows "Response status: 403"

**This means authentication failed.**

**Check:**
1. Are you logged in?
2. Check `sessionStorage` in browser DevTools:
   - Application tab → Session Storage
   - Should have `fg_user` key with user data
3. Try logging out and back in

### Issue: "Resubmit Document" button doesn't work

**Check:**
1. Console for errors when clicking button
2. File size (must be under 5MB)
3. File type (must be JPG, PNG, or PDF)
4. Network tab in DevTools to see the upload request

## Expected Flow Summary

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Customer submits application                             │
│    Status: PENDING                                           │
│    Display: "Application Pending Review" (orange)           │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Admin reviews and rejects some documents                 │
│    Admin clicks "Send Rejection Notification"               │
│    Status: PENDING (with rejected documents)                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Customer views Seller Centre                             │
│    Display: "Action Required" (red)                         │
│    Shows: Rejected documents with reasons + resubmit button │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Customer resubmits rejected documents                    │
│    Status: PENDING (documents reset to pending)             │
│    Display: "Application Pending Review" (orange)           │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Admin reviews resubmitted documents                      │
│    If approved: Status → APPROVED                           │
│    If rejected: Back to step 2                              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. Customer sees "Congratulations" banner                   │
│    Can switch to seller account                             │
└─────────────────────────────────────────────────────────────┘
```

## Files Modified

1. **fixandgo/views/user/customer/seller-centre.html**
   - Changed 3 fetch paths from absolute to relative
   - Lines: 663, 831, 1279

2. **fixandgo/backend/document_approvals.php**
   - Removed global admin check
   - Added per-action admin checks
   - Allows `my_documents` for authenticated users

## Need Help?

If the issue persists after following this guide:

1. Check the browser console for the exact error message
2. Check the Network tab in DevTools to see the actual request/response
3. Verify the file paths match your server directory structure
4. Check PHP error logs on the server

## Date
May 6, 2026
