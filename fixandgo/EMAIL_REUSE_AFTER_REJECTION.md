# Email Reuse After Application Rejection

## Problem
When an applicant's seller application (supplier or shop owner) was rejected, their email remained in the `users` table, preventing them from reapplying with the same email address.

## Solution
When an admin rejects an application, the system now **deletes the seller user account** from the `users` table, freeing up the email for reapplication.

---

## What Changed

### Before (Old Behavior)
```sql
-- Rejection kept the user account but marked it inactive
UPDATE users 
SET is_active = 0, 
    application_status = 'rejected', 
    application_notes = 'rejection reason'
WHERE email = 'applicant@example.com' AND role = 'supplier';
```

**Problem**: Email `applicant@example.com` is still in the database, so the applicant cannot reapply with the same email.

### After (New Behavior)
```sql
-- Rejection deletes the seller user account completely
DELETE FROM users 
WHERE email = 'applicant@example.com' AND role = 'supplier';
```

**Result**: Email `applicant@example.com` is freed up and can be used to apply again.

---

## How It Works

### 1. Admin Rejects Application
When admin clicks "Reject" on an application:

1. **Update application status** in `seller_applications` table:
   ```sql
   UPDATE seller_applications 
   SET status = 'rejected', 
       admin_notes = 'rejection reason',
       reviewed_by = admin_id,
       reviewed_at = NOW()
   WHERE id = application_id;
   ```

2. **Send notifications** (email + in-app) to the customer:
   - Notification includes rejection reason
   - Email explains they can reapply with the same email

3. **Delete the seller user account**:
   ```sql
   DELETE FROM users 
   WHERE email = 'applicant@example.com' 
   AND role = 'supplier';
   ```

### 2. Applicant Views Rejection
- Customer logs in to their **customer account**
- Goes to **Seller Centre**
- Sees rejection notice with:
  - Which documents were rejected
  - Rejection reasons for each document
  - "Submit New Application" button

### 3. Applicant Reapplies
- Clicks "Submit New Application"
- Can use the **same email** as before (now available)
- Uploads corrected documents
- Submits new application

### 4. Email Validation
The existing email validation in `seller_apply.php` automatically works:

```php
$emailCheck = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$emailCheck->execute([$email]);
if ($emailCheck->fetch()) {
    // Email exists - reject
} else {
    // Email available - allow registration
}
```

Since the user account was deleted, the email check passes! ✅

---

## Important Notes

### What Gets Deleted
- ✅ **Seller user account** (from `users` table)
- ❌ **Application record** (kept in `seller_applications` for history)
- ❌ **Document approval records** (kept in `document_approvals` for audit)
- ❌ **Customer account** (untouched - they can still shop)

### What Gets Preserved
The `seller_applications` table keeps the rejected application for:
- **Admin audit trail** — Track all applications and decisions
- **Historical reference** — See previous rejection reasons
- **Analytics** — Measure rejection rates and common issues

### Database Relationships
The `users` table deletion is safe because:
- `seller_applications.user_id` references the **customer account** (not the seller account)
- Foreign keys use `ON DELETE SET NULL` or `ON DELETE CASCADE` appropriately
- No orphaned records are created

---

## User Flow Example

### Scenario: Supplier applies with wrong documents

1. **Initial Application**
   - Customer (email: `customer@example.com`) applies as Supplier
   - Uses email: `supplier@example.com` for seller account
   - System creates:
     - Entry in `seller_applications` (status: pending)
     - Entry in `users` (role: supplier, email: `supplier@example.com`)

2. **Admin Rejects**
   - Admin reviews documents
   - Rejects with reason: "BIR certificate is expired"
   - System:
     - Updates `seller_applications` (status: rejected)
     - Sends notification to customer
     - **Deletes** user account (`supplier@example.com` freed)

3. **Applicant Sees Rejection**
   - Logs in as customer (`customer@example.com`)
   - Goes to Seller Centre
   - Sees: "Application Rejected - BIR certificate is expired"
   - Clicks "Submit New Application"

4. **Reapplication**
   - Uses **same email** `supplier@example.com` ✅
   - Uploads corrected BIR certificate
   - Submits successfully
   - System creates new:
     - Entry in `seller_applications` (new ID, status: pending)
     - Entry in `users` (role: supplier, email: `supplier@example.com`)

5. **Admin Approves**
   - Admin reviews new application
   - Approves all documents
   - Applicant can now log in as supplier

---

## Email Notification

When an application is rejected, the applicant receives this email:

```
Subject: Application Rejected — Fix&Go Supplier

Dear John Doe,

Thank you for your interest in becoming a Supplier on Fix&Go.

After careful review, we regret to inform you that we are unable to 
approve your application at this time.

Reason: BIR certificate is expired. Please provide a current certificate.

What's Next?
• Review the rejection reason carefully
• Prepare the correct documents
• You can reapply using the same email address

Your email address has been freed up and is available for reapplication.

If you have any questions, please contact our support team.
```

---

## Testing Checklist

### Test Case 1: Email Reuse After Rejection
- [ ] Apply as supplier with email `test@example.com`
- [ ] Admin rejects application
- [ ] Check database: `test@example.com` should NOT exist in `users` table
- [ ] Check database: Application record still exists in `seller_applications`
- [ ] Try to apply again with `test@example.com` — should succeed ✅

### Test Case 2: Customer Account Unaffected
- [ ] Customer applies as supplier (different email)
- [ ] Admin rejects application
- [ ] Customer can still log in with their customer account ✅
- [ ] Customer can still shop and place orders ✅

### Test Case 3: Application History Preserved
- [ ] Apply as supplier
- [ ] Admin rejects
- [ ] Apply again with same email
- [ ] Admin can see both applications in history ✅

### Test Case 4: Notifications Work
- [ ] Admin rejects application
- [ ] Customer receives email notification ✅
- [ ] Customer receives in-app notification ✅
- [ ] Email mentions they can reapply with same email ✅

---

## Database Schema Impact

### `users` Table
```sql
-- Before rejection
SELECT * FROM users WHERE email = 'supplier@example.com';
-- Returns: 1 row (supplier account)

-- After rejection
SELECT * FROM users WHERE email = 'supplier@example.com';
-- Returns: 0 rows (account deleted)
```

### `seller_applications` Table
```sql
-- Application record is preserved
SELECT * FROM seller_applications WHERE email = 'supplier@example.com';
-- Returns: 1 or more rows (all applications, including rejected)
```

---

## Security Considerations

### Why This Is Safe

1. **Customer Account Protected**
   - Only the **seller account** is deleted
   - Customer account remains intact
   - Customer can continue shopping

2. **Audit Trail Maintained**
   - Application records preserved
   - Document approval records preserved
   - Admin can review rejection history

3. **No Data Loss**
   - Uploaded documents remain in `uploads/applications/`
   - Rejection reasons stored in `seller_applications`
   - Admin notes preserved

4. **Foreign Key Safety**
   - `seller_applications.user_id` points to customer (not deleted)
   - `document_approvals.application_id` uses CASCADE
   - No orphaned records

### Why Deletion Is Better Than Deactivation

**Deactivation (Old)**:
- ❌ Email remains blocked
- ❌ Applicant must use different email
- ❌ Confusing for users
- ❌ Database clutter with inactive accounts

**Deletion (New)**:
- ✅ Email freed for reuse
- ✅ Applicant can use same email
- ✅ Clear and simple
- ✅ Clean database

---

## Alternative Approaches Considered

### Option 1: Keep Account, Allow Duplicate Emails
**Rejected** — Would violate unique email constraint and cause login issues

### Option 2: Soft Delete with Flag
**Rejected** — Still blocks email reuse, adds complexity

### Option 3: Delete Account (CHOSEN)
**Selected** — Simple, clean, allows email reuse

---

## Files Modified

1. **`fixandgo/backend/admin.php`**
   - Changed rejection logic from UPDATE to DELETE
   - Updated email notification text
   - Added message about email being freed

2. **`fixandgo/views/user/customer/seller-centre.html`**
   - Updated rejected UI to show document-level rejection reasons
   - Added "Submit New Application" button
   - Shows which documents were rejected and why

---

## Support

For questions or issues:
- Check `fixandgo/DOCUMENT_RESUBMISSION_GUIDE.md` for resubmission flow
- Check `fixandgo/DOCUMENT_APPROVAL_SYSTEM.md` for approval system
- Review admin rejection code: `fixandgo/backend/admin.php` (line ~343)

---

**Implementation Complete! ✅**

Applicants can now reapply with the same email after rejection.
