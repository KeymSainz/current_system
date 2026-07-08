# Fix&Go — Project Structure (MVC Reference)

## Overview

Fix&Go is a PHP + HTML/JS application following a **loosely-coupled MVC pattern**:
- **Model** → `backend/*.php` (data access + business logic, respond as JSON APIs)
- **View** → `views/**/*.html` + root HTML files (frontend rendered in browser)
- **Controller** → inline JS in each view page (calls backend APIs, updates DOM)

---

## Directory Structure

```
fixandgo/
├── index.html                  # Landing page (guest + logged-in marketplace)
├── dashboard.html              # Role router — redirects to correct role dashboard
├── login.html                  # Login page
├── register.html               # Registration page
├── forgot-password.html        # Password reset request
├── otp.html                    # OTP verification
├── offline.html                # PWA offline fallback
├── manifest.json               # PWA manifest
├── sw.js                       # Service worker
│
├── assets/                     # Static frontend assets
│   ├── css/
│   │   ├── auth.css            # Auth pages + shared navbar/sidebar styles
│   │   ├── dashboard.css       # Role-router dashboard styles
│   │   ├── landing.css         # Landing page specific styles
│   │   ├── mobile.css          # Mobile/PWA responsive overrides
│   │   └── supplier.css        # Supplier/technician dashboard shared styles
│   ├── js/
│   │   ├── auth-utils.js       # FGAuth — session management, UserStore
│   │   ├── cart.js             # Shopping cart logic
│   │   ├── dashboard.js        # Role-router JS
│   │   ├── landing.js          # Landing page JS
│   │   ├── login.js            # Login form handler
│   │   ├── register.js         # Registration form handler
│   │   ├── otp.js              # OTP verification handler
│   │   ├── forgot-password.js  # Password reset handler
│   │   ├── ph-location.js      # Philippine location dropdowns
│   │   ├── pwa.js              # PWA install prompt
│   │   ├── session-timeout.js  # Auto-logout on inactivity
│   │   └── theme.js            # Dark/light theme toggle
│   └── images/
│       ├── logo.png
│       └── icons/              # PWA icons (72–512px)
│
├── backend/                    # MODEL layer — PHP API endpoints
│   │
│   ├── Core / Config
│   │   ├── config.php          # DB credentials (env-based)
│   │   ├── config.example.php  # Template for .env setup
│   │   ├── db.php              # PDO connection factory
│   │   ├── helpers.php         # Shared utility functions
│   │   ├── notification_helper.php  # Push notification helpers
│   │   ├── mailer.php          # PHPMailer wrapper
│   │   └── csrf-token.php      # CSRF token generation
│   │
│   ├── Auth
│   │   ├── login.php           # POST: authenticate user, create session
│   │   ├── logout.php          # POST: destroy session
│   │   ├── register.php        # POST: create new user account
│   │   ├── verify-otp.php      # POST: verify OTP code
│   │   ├── resend-otp.php      # POST: resend OTP
│   │   ├── forgot-password.php # POST: initiate password reset
│   │   ├── reset-password.php  # POST: complete password reset
│   │   ├── google-auth-init.php    # GET: initiate Google OAuth
│   │   ├── google-callback.php     # GET: handle Google OAuth callback
│   │   ├── session-user.php        # GET: return current session user as JSON
│   │   └── session-ping.php        # GET: keep session alive
│   │
│   ├── User / Profile
│   │   ├── profile.php         # GET/POST: user profile data
│   │   ├── customer_profile.php  # GET/POST: customer-specific profile
│   │   └── unlock_account.php  # POST: admin unlock locked accounts
│   │
│   ├── Notifications & Messages
│   │   ├── notifications.php   # GET/POST: list, mark read, mark all read
│   │   └── messages.php        # GET/POST: conversations, send/receive messages
│   │
│   ├── Shop / Marketplace
│   │   ├── shop_products.php       # GET: public product listing
│   │   ├── marketplace_products.php  # GET: marketplace product search
│   │   ├── marketplace_technicians.php  # GET: technician listings
│   │   ├── technicians.php         # GET: technician public profiles
│   │   └── maps-config.php         # GET: return maps API config safely
│   │
│   ├── Customer
│   │   ├── customer_orders.php     # GET/POST: customer order management
│   │   ├── customer_paymongo.php   # POST: create PayMongo payment link
│   │   └── customer_payment_return.php  # GET: handle payment return
│   │
│   ├── Repairs / Bookings
│   │   ├── repair_bookings.php     # GET/POST: create/manage repair bookings
│   │   ├── repair_payment.php      # POST: repair payment processing
│   │   ├── repair_payment_return.php  # GET: repair payment return handler
│   │   └── reviews.php             # GET/POST: repair reviews
│   │
│   ├── Technician
│   │   ├── technician_dashboard.php  # GET/POST: technician dashboard API
│   │   ├── technician_apply.php      # POST: apply to become technician
│   │   ├── technician_credentials.php  # GET/POST: manage credentials
│   │   ├── technician_marketplace.php  # GET: technician marketplace view
│   │   ├── technician_orders.php     # GET/POST: technician parts orders
│   │   └── technician_payment_return.php  # GET: payment return
│   │
│   ├── Supplier
│   │   ├── supplier_products.php   # GET/POST: manage supplier products
│   │   ├── supplier_orders.php     # GET/POST: supplier order management
│   │   ├── supplier_sales.php      # GET: sales reporting
│   │   ├── supplier_shop_view.php  # GET: supplier shop public view
│   │   ├── supplier_tech_requests.php  # GET/POST: tech supply requests
│   │   └── seller_tech_orders.php  # GET/POST: orders placed by technicians
│   │
│   ├── Owner
│   │   ├── owner_inventory.php     # GET/POST: owner inventory management
│   │   ├── owner_products.php      # GET/POST: owner product management
│   │   ├── owner_shop_products.php # GET: owner shop product view
│   │   ├── owner_staff.php         # GET/POST: staff management
│   │   └── owner_supervisor_reports.php  # GET: supervisor reports
│   │
│   ├── Sales Person
│   │   ├── sales_inventory.php     # GET/POST: sales inventory
│   │   ├── sales_orders.php        # GET/POST: sales order management
│   │   ├── sales_products.php      # GET/POST: sales product management
│   │   └── sales_supply_requests.php  # GET/POST: supply requests
│   │
│   ├── Supervisor
│   │   ├── supervisor_inventory.php  # GET/POST: supervisor inventory
│   │   └── supervisor_reports.php    # GET: supervisor reporting
│   │
│   ├── Admin
│   │   └── admin.php               # GET/POST: admin panel API
│   │
│   ├── Payments (PayMongo)
│   │   ├── paymongo.php            # PayMongo SDK wrapper
│   │   └── paymongo_webhook.php    # POST: webhook handler
│   │
│   ├── Transfers / Inventory
│   │   ├── product_transfers.php   # GET/POST: product transfer between roles
│   │   └── document_approvals.php  # GET/POST: document approval workflow
│   │
│   ├── Applications
│   │   ├── seller_apply.php        # POST: apply as seller
│   │   └── switch_to_seller.php    # POST: switch role to seller
│   │
│   └── Migrations / Seeds (SQL — run once on setup)
│       ├── migrate_repair_payment.sql          # Repair payment columns
│       ├── migrate_technician_credentials_v2.sql  # Credentials table
│       └── seed_products.sql                   # Sample product data
│
├── views/                      # VIEW layer — HTML pages per role
│   └── user/
│       ├── customer/           # Customer role views
│       │   ├── dashboard.html
│       │   ├── orders.html
│       │   ├── repairs.html
│       │   ├── messages.html
│       │   ├── notifications.html
│       │   ├── profile.html
│       │   ├── settings.html
│       │   ├── wishlist.html
│       │   ├── vouchers.html
│       │   ├── become-technician.html
│       │   └── seller-centre.html
│       │
│       ├── phone_technician/   # Technician role views
│       │   ├── dashboard.html
│       │   ├── repairs.html
│       │   ├── inventory.html
│       │   ├── products.html
│       │   ├── supply-requests.html
│       │   ├── messages.html
│       │   └── profile.html
│       │
│       ├── supplier/           # Supplier role views
│       │   ├── dashboard.html
│       │   ├── products.html
│       │   ├── orders.html
│       │   ├── deliveries.html
│       │   ├── tech-requests.html
│       │   ├── tech-orders.html
│       │   ├── owner-purchases.html
│       │   ├── sales-report.html
│       │   ├── messages.html
│       │   └── profile.html
│       │
│       ├── owner/              # Owner role views
│       ├── sales_person/       # Sales person role views
│       └── supervisor/         # Supervisor role views
│
└── uploads/                    # User-uploaded files (excluded from git)
    ├── avatars/
    ├── applications/
    ├── credentials/
    ├── messages/
    ├── products/
    ├── repair_photos/
    ├── reviews/
    └── shop_images/
```

---

## MVC Data Flow

```
Browser (View HTML)
    │
    ├── fetch('backend/login.php', POST)
    │       │
    │       └── backend/login.php (Model)
    │               ├── db.php → PDO → MySQL
    │               ├── validates credentials
    │               └── returns JSON { success, user }
    │
    └── JS in HTML (Controller)
            ├── receives JSON
            ├── saves to sessionStorage via FGAuth.UserStore
            └── updates DOM / redirects
```

---

## Key Conventions

| Convention | Detail |
|---|---|
| All backend files return | `{ "success": true/false, "data/message": ... }` JSON |
| Authentication | PHP session (`$_SESSION`) + `session-user.php` for SPA verification |
| Frontend auth store | `FGAuth.UserStore` (sessionStorage wrapper in `auth-utils.js`) |
| Relative API paths | Views use `../../../backend/` or `backend/` from root |
| Role-based routing | `dashboard.html` detects role and redirects to correct view |
| Uploads | Stored in `uploads/` with sub-folders per type |

---

## Production Files — DO NOT DELETE

`config.php`, `db.php`, `helpers.php`, `auth-utils.js`, `login.php`, `logout.php`, `register.php`, `session-user.php`, `notifications.php`, `messages.php`, `shop_products.php`, `repair_bookings.php`, `technician_dashboard.php`

---

*Generated: Fix&Go Project Cleanup — June 2026*
