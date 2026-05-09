# Seller Application Flow Diagram

## Visual Workflow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         SELLER APPLICATION WORKFLOW                      │
└─────────────────────────────────────────────────────────────────────────┘

┌──────────────┐
│   CUSTOMER   │
│   (Logged    │
│     In)      │
└──────┬───────┘
       │
       │ 1. Navigate to Seller Centre
       ▼
┌──────────────────────────────────────────────────────────────────────────┐
│                          SELLER CENTRE PAGE                               │
│  ┌────────────────────┐              ┌────────────────────┐             │
│  │   📦 SUPPLIER      │              │   🏪 SHOP OWNER    │             │
│  │                    │              │                    │             │
│  │ • List products    │              │ • Manage staff     │             │
│  │ • Receive orders   │              │ • Accept bookings  │             │
│  │ • Track sales      │              │ • Purchase parts   │             │
│  │                    │              │ • View reports     │             │
│  └────────┬───────────┘              └────────┬───────────┘             │
│           │                                   │                          │
│           └───────────────┬───────────────────┘                          │
└───────────────────────────┼──────────────────────────────────────────────┘
                            │
                            │ 2. Click "Register as..."
                            ▼
┌──────────────────────────────────────────────────────────────────────────┐
│                        APPLICATION FORM MODAL                             │
│                                                                           │
│  Personal Information:                                                    │
│  ├─ First Name, Last Name                                                │
│  ├─ Company Name                                                         │
│  ├─ Email (different from customer email)                                │
│  ├─ Phone Number                                                         │
│  ├─ Shop Name (owners only)                                              │
│  └─ Password                                                             │
│                                                                           │
│  Required Documents:                                                      │
│  ├─ 🆔 Government-Issued ID (required)                                   │
│  ├─ 📄 BIR Certificate (recommended)                                     │
│  ├─ 🏢 DTI/SEC Registration (required for owners)                        │
│  └─ 🏦 Bank Account Proof (required)                                     │
│                                                                           │
│  [Cancel]  [Submit Application]                                          │
└───────────────────────────┬──────────────────────────────────────────────┘
                            │
                            │ 3. Submit
                            ▼
┌──────────────────────────────────────────────────────────────────────────┐
│                    BACKEND: seller_apply.php                              │
│                                                                           │
│  ✓ Validate form data                                                    │
│  ✓ Check for duplicate applications                                      │
│  ✓ Upload documents to uploads/applications/                             │
│  ✓ Save to seller_applications table (status='pending')                  │
│  ✓ Create user account (is_active=0, role=supplier/owner)                │
│  ✓ Send email to admin                                                   │
│                                                                           │
└───────────────────────────┬──────────────────────────────────────────────┘
                            │
                            ├──────────────────────────────────────┐
                            │                                      │
                            ▼                                      ▼
                    ┌───────────────┐                    ┌────────────────┐
                    │   CUSTOMER    │                    │     ADMIN      │
                    │               │                    │                │
                    │ ✓ Success     │                    │ 📧 Email:      │
                    │   message     │                    │ "New [Role]    │
                    │               │                    │  Application"  │
                    │ "Application  │                    │                │
                    │  submitted!"  │                    │ 🔔 Dashboard:  │
                    │               │                    │ Pending badge  │
                    └───────────────┘                    └────────┬───────┘
                                                                  │
                                                                  │ 4. Admin reviews
                                                                  ▼
┌──────────────────────────────────────────────────────────────────────────┐
│                         ADMIN DASHBOARD                                   │
│                                                                           │
│  Pending Applications:                                                    │
│  ┌────────────────────────────────────────────────────────────────────┐  │
│  │ #123 │ John Doe │ john@example.com │ Supplier │ May 3, 2026       │  │
│  │      │ ABC Trading Co.              │          │                   │  │
│  │      │ [Docs] [Approve] [Reject]    │          │                   │  │
│  └────────────────────────────────────────────────────────────────────┘  │
│                                                                           │
└───────────────────────────┬──────────────────────────────────────────────┘
                            │
                            │ 5. Admin clicks...
                            │
            ┌───────────────┴───────────────┐
            │                               │
            ▼                               ▼
    ┌───────────────┐             ┌───────────────┐
    │   [Docs]      │             │  [Approve] or │
    │               │             │   [Reject]    │
    │ View uploaded │             │               │
    │ documents:    │             └───────┬───────┘
    │ • Gov ID      │                     │
    │ • BIR Cert    │                     │
    │ • DTI/SEC     │                     │
    │ • Bank Proof  │                     │
    └───────────────┘                     │
                                          │
                        ┌─────────────────┴─────────────────┐
                        │                                   │
                        ▼                                   ▼
            ┌───────────────────────┐         ┌───────────────────────┐
            │   APPROVE MODAL       │         │   REJECT MODAL        │
            │                       │         │                       │
            │ Applicant: John Doe   │         │ Applicant: John Doe   │
            │ Email: john@...       │         │ Email: john@...       │
            │ Role: Supplier        │         │                       │
            │                       │         │ Rejection Reason:     │
            │ Notes (optional):     │         │ [Required field]      │
            │ [Welcome message...]  │         │                       │
            │                       │         │                       │
            │ [Cancel] [Approve]    │         │ [Cancel] [Reject]     │
            └───────────┬───────────┘         └───────────┬───────────┘
                        │                                 │
                        │ 6. Confirm                      │ 6. Confirm
                        ▼                                 ▼
        ┌───────────────────────────┐     ┌───────────────────────────┐
        │ BACKEND: admin.php        │     │ BACKEND: admin.php        │
        │ action=approve            │     │ action=reject             │
        │                           │     │                           │
        │ ✓ Update application      │     │ ✓ Update application      │
        │   status='approved'       │     │   status='rejected'       │
        │ ✓ Activate user account   │     │ ✓ Keep account inactive   │
        │   (is_active=1)           │     │   (is_active=0)           │
        │ ✓ Send notification       │     │ ✓ Send notification       │
        │ ✓ Send email              │     │ ✓ Send email              │
        └───────────┬───────────────┘     └───────────┬───────────────┘
                    │                                 │
                    │                                 │
        ┌───────────┴───────────┐         ┌───────────┴───────────┐
        │                       │         │                       │
        ▼                       ▼         ▼                       ▼
┌──────────────┐      ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ NEW SELLER   │      │ NEW SELLER   │  │  CUSTOMER    │  │  CUSTOMER    │
│              │      │              │  │  (Original)  │  │  (Original)  │
│ 🔔 In-App:   │      │ 📧 Email:    │  │              │  │              │
│ "Application │      │ "Application │  │ 🔔 In-App:   │  │ 📧 Email:    │
│  Approved!"  │      │  Approved"   │  │ "Application │  │ "Application │
│              │      │              │  │  Rejected"   │  │  Update"     │
│ ✅ Can now   │      │ • Welcome    │  │              │  │              │
│    log in    │      │ • Next steps │  │ • Reason     │  │ • Reason     │
│              │      │ • Login link │  │ • Can        │  │ • Can        │
│              │      │              │  │   reapply    │  │   reapply    │
└──────────────┘      └──────────────┘  └──────────────┘  └──────────────┘
```

## Key Points

### 📝 Application Submission
- Customer fills out form with personal and business details
- Uploads required documents (Gov ID, BIR, DTI/SEC, Bank Proof)
- Application saved with status 'pending'
- Admin notified via email

### 👀 Admin Review
- Admin sees pending applications in dashboard
- Can view all uploaded documents
- Can approve with optional notes or reject with required reason

### ✅ Approval Path
- Application status → 'approved'
- User account activated (is_active = 1)
- **Notification sent to new seller account**
- **Email sent with welcome message**
- Seller can log in and start selling

### ❌ Rejection Path
- Application status → 'rejected'
- User account remains inactive
- **Notification sent to original customer account**
- **Email sent with rejection reason**
- Customer can reapply with updated information

## Notification Recipients

```
APPROVAL:
┌─────────────────┐
│  New Seller     │ ← In-app notification
│  Account        │ ← Email notification
└─────────────────┘

REJECTION:
┌─────────────────┐
│  Original       │ ← In-app notification
│  Customer       │ ← Email notification
│  Account        │
└─────────────────┘
```

## Database Flow

```
seller_applications table:
┌──────────────────────────────────────────────────────────────┐
│ id │ user_id │ role │ email │ status │ documents │ notes    │
├────┼─────────┼──────┼───────┼────────┼───────────┼──────────┤
│ 1  │ 123     │ supp │ j@... │pending │ [paths]   │ NULL     │
│    │         │ lier │       │        │           │          │
└────┴─────────┴──────┴───────┴────────┴───────────┴──────────┘
                                  │
                                  │ Admin approves/rejects
                                  ▼
┌──────────────────────────────────────────────────────────────┐
│ id │ user_id │ role │ email │ status   │ documents │ notes  │
├────┼─────────┼──────┼───────┼──────────┼───────────┼────────┤
│ 1  │ 123     │ supp │ j@... │approved  │ [paths]   │Welcome!│
│    │         │ lier │       │          │           │        │
└────┴─────────┴──────┴───────┴──────────┴───────────┴────────┘

users table:
┌──────────────────────────────────────────────────────────────┐
│ id │ email │ role     │ is_active │ application_status      │
├────┼───────┼──────────┼───────────┼─────────────────────────┤
│ 456│ j@... │ supplier │ 0         │ pending                 │
└────┴───────┴──────────┴───────────┴─────────────────────────┘
                                  │
                                  │ Admin approves
                                  ▼
┌──────────────────────────────────────────────────────────────┐
│ id │ email │ role     │ is_active │ application_status      │
├────┼───────┼──────────┼───────────┼─────────────────────────┤
│ 456│ j@... │ supplier │ 1         │ approved                │
└────┴───────┴──────────┴───────────┴─────────────────────────┘
                                  │
                                  │ Can now log in!
                                  ▼
                          ┌──────────────────┐
                          │  Seller Dashboard│
                          └──────────────────┘
```

---

**Created:** May 3, 2026  
**Version:** 1.0.0
