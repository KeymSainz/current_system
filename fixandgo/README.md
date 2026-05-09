# Fix&Go — Authentication System

A secure, responsive authentication system for the Fix&Go service-booking platform.

---

## Folder Structure

```
fixandgo/
├── login.html              # Login page
├── register.html           # Registration page
├── otp.html                # Email OTP verification
├── forgot-password.html    # Forgot password (2-step)
├── dashboard.html          # Role-based dashboard
│
├── assets/
│   ├── css/
│   │   ├── auth.css        # All auth page styles + dark mode
│   │   └── dashboard.css   # Dashboard-specific styles
│   └── js/
│       ├── theme.js        # Dark/light mode toggle
│       ├── auth-utils.js   # Shared utilities (CSRF, rate limit, OTP, etc.)
│       ├── login.js        # Login page logic
│       ├── register.js     # Registration page logic
│       ├── otp.js          # OTP verification logic
│       ├── forgot-password.js  # Forgot password flow
│       └── dashboard.js    # RBAC dashboard rendering
│
└── backend/
    ├── config.php          # App configuration (DB, SMTP, OAuth)
    ├── db.php              # PDO database connection
    ├── helpers.php         # CSRF, rate limiting, sanitization, session
    ├── mailer.php          # PHPMailer email service
    ├── register.php        # POST /backend/register.php
    ├── login.php           # POST /backend/login.php
    ├── verify-otp.php      # POST /backend/verify-otp.php
    ├── resend-otp.php      # POST /backend/resend-otp.php
    ├── reset-password.php  # POST /backend/reset-password.php
    ├── logout.php          # POST /backend/logout.php
    ├── google-auth-init.php    # GET  — starts Google OAuth flow
    ├── google-callback.php     # GET  — Google OAuth callback
    └── schema.sql          # MySQL database schema
```

---

## Quick Start

### 1. Database Setup
```sql
mysql -u root -p < fixandgo/backend/schema.sql
```

### 2. Configure Backend
Edit `fixandgo/backend/config.php`:
- Set your DB credentials
- Set SMTP credentials (Gmail App Password recommended)
- Set Google OAuth client ID/secret from [Google Cloud Console](https://console.cloud.google.com/)

### 3. Serve with PHP
```bash
php -S localhost:8000
# Then open: http://localhost:8000/fixandgo/login.html
```

### 4. Demo Mode (Frontend Only)
Open `fixandgo/login.html` directly in a browser.  
The JS layer simulates the backend using `sessionStorage` — no PHP/DB needed for UI testing.

---

## Security Features

| Feature | Implementation |
|---|---|
| Password hashing | bcrypt (cost 12) via `password_hash()` |
| SQL injection | PDO prepared statements throughout |
| CSRF protection | Synchronizer token pattern (session-stored) |
| Rate limiting | DB-backed per-IP attempt tracking |
| Session fixation | `session_regenerate_id(true)` on login |
| OTP storage | bcrypt-hashed, never plaintext |
| Remember Me | SHA-256 hashed token in DB + httpOnly cookie |
| XSS prevention | `htmlspecialchars()` on all output |
| Secure cookies | `httponly`, `samesite=Strict`, `secure` flags |
| User enumeration | Generic error messages on login/forgot-password |
| Timing attacks | Dummy hash comparison when user not found |
| Google OAuth CSRF | State parameter validation |

---

## User Roles

| Role | Description | Dashboard |
|---|---|---|
| `customer` | Books repairs, tracks devices | Booking & history |
| `technician` | Manages assigned repair jobs | Job queue & schedule |
| `owner` | Manages shop, staff, analytics | Full shop overview |

---

## OTP Flow

1. User registers → server generates 6-digit OTP, bcrypt-hashes it, stores in `otp_tokens`
2. PHPMailer sends OTP to user's email
3. User enters OTP on `/otp.html`
4. Server verifies with `password_verify()`, checks expiry (10 min) and attempt limit (3)
5. On success: `users.is_verified = 1`, session created, redirect to dashboard

---

## Google OAuth Flow

1. User clicks "Continue with Google" → `google-auth-init.php` generates state token, redirects to Google
2. User consents → Google redirects to `google-callback.php?code=...&state=...`
3. State validated → code exchanged for access token → profile fetched
4. User upserted in DB → session created → redirect to dashboard

---

## Password Requirements

- Minimum 8 characters
- At least 1 uppercase letter (A–Z)
- At least 1 lowercase letter (a–z)
- At least 1 number (0–9)

Enforced on both frontend (real-time) and backend (server-side validation).
