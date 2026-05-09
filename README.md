# Fix&Go - Phone Repair Shop Management System

A comprehensive web-based platform for managing phone repair shops, connecting suppliers, shop owners, supervisors, sales persons, and customers.

## 🚀 Features

### Multi-Role System
- **Customers**: Book repairs, browse products, track orders
- **Suppliers**: List and manage product catalog, receive orders
- **Shop Owners**: Manage staff, purchase parts, track revenue
- **Supervisors**: Manage inventory, distribute products to sales team
- **Sales Persons**: Display products to customers, process sales
- **Technicians**: Handle repair bookings and services
- **Admin**: Oversee platform operations and user management

### Key Functionality
- 🔐 Secure authentication with OTP verification
- 📦 Product transfer system (Supplier → Owner → Supervisor → Sales Person)
- 💳 PayMongo payment integration
- 📄 Document approval system for seller applications
- 📊 Real-time inventory management
- 🔔 Notification system
- 📱 Responsive design (mobile-friendly)
- 🌓 Dark/Light theme support

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Payment**: PayMongo API
- **Email**: PHPMailer
- **UI Framework**: Bootstrap 5.3.8
- **Icons**: Bootstrap Icons, Font Awesome

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependencies)
- PayMongo account (for payment processing)

## 🔧 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/fixandgo.git
cd fixandgo
```

### 2. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE fixandgo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p fixandgo < fixandgo/backend/schema.sql

# Run migrations (in order)
mysql -u root -p fixandgo < fixandgo/backend/migrate_admin.sql
mysql -u root -p fixandgo < fixandgo/backend/migrate_login_attempts.sql
mysql -u root -p fixandgo < fixandgo/backend/migrate_login_logs.sql
mysql -u root -p fixandgo < fixandgo/backend/migrate_seller_applications.sql
mysql -u root -p fixandgo < fixandgo/backend/migrate_document_approvals.sql
mysql -u root -p fixandgo < fixandgo/backend/migrate_add_technicians.sql
mysql -u root -p fixandgo < fixandgo/backend/migrate_sales_person.sql
mysql -u root -p fixandgo < fixandgo/backend/migrate_paymongo.sql
mysql -u root -p fixandgo < fixandgo/backend/migrate_product_transfers.sql
mysql -u root -p fixandgo < fixandgo/backend/migrate_supervisor_reports.sql
```

### 3. Configuration

Create `fixandgo/backend/config.php`:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'fixandgo');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// PayMongo Configuration
define('PAYMONGO_SECRET_KEY', 'your_paymongo_secret_key');
define('PAYMONGO_PUBLIC_KEY', 'your_paymongo_public_key');

// Email Configuration (for PHPMailer)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_app_password');
define('SMTP_FROM', 'your_email@gmail.com');
define('SMTP_FROM_NAME', 'Fix&Go');

// Site Configuration
define('SITE_URL', 'http://localhost/fixandgo');
define('SITE_NAME', 'Fix&Go');

// Session Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes
```

### 4. Set Permissions

```bash
# Make uploads directory writable
chmod -R 755 fixandgo/uploads/
chmod -R 755 fixandgo/assets/uploads/

# Create directories if they don't exist
mkdir -p fixandgo/uploads/products
mkdir -p fixandgo/uploads/documents
mkdir -p fixandgo/uploads/avatars
```

### 5. Admin Setup

```bash
# Create admin account
php fixandgo/backend/admin_setup.php
```

Or visit: `http://localhost/fixandgo/backend/admin_setup.php`

## 📖 Usage

### Access Points

- **Main Site**: `http://localhost/fixandgo/`
- **Login**: `http://localhost/fixandgo/login.html`
- **Dashboard**: `http://localhost/fixandgo/dashboard.html` (after login)
- **Admin Panel**: Login with admin credentials

### Default Roles

After setup, you can register users with different roles:
- Customer (default registration)
- Supplier (via Seller Centre application)
- Shop Owner (via Seller Centre application)
- Supervisor (registered by shop owner)
- Sales Person (registered by shop owner)
- Technician (registered by shop owner)

## 🔄 Product Transfer Flow

```
Supplier → Owner → Supervisor → Sales Person → Customer
```

1. **Supplier** lists products
2. **Owner** purchases products from supplier
3. **Owner** sends products to **Supervisor**
4. **Supervisor** distributes products to **Sales Person**
5. **Sales Person** displays products to **Customers**

## 🗂️ Project Structure

```
fixandgo/
├── assets/
│   ├── css/          # Stylesheets
│   ├── js/           # JavaScript files
│   └── images/       # Static images
├── backend/
│   ├── *.php         # Backend API files
│   ├── migrate_*.sql # Database migrations
│   └── config.php    # Configuration (create this)
├── views/
│   └── user/
│       ├── admin/    # Admin views
│       ├── customer/ # Customer views
│       ├── owner/    # Owner views
│       ├── supervisor/ # Supervisor views
│       ├── sales_person/ # Sales person views
│       └── supplier/ # Supplier views
├── uploads/          # User uploaded files
├── index.html        # Landing page
├── login.html        # Login page
├── dashboard.html    # Main dashboard
└── README.md         # This file
```

## 🔒 Security Features

- Password hashing (bcrypt)
- CSRF token protection
- SQL injection prevention (prepared statements)
- XSS protection
- Session management with timeout
- Login attempt limiting
- Secure file upload validation

## 🐛 Troubleshooting

### Common Issues

**Database connection failed:**
- Check `config.php` credentials
- Verify MySQL service is running
- Check database exists

**Products not transferring:**
- Run `fix_purchased_products_holder.sql`
- Verify user roles are correct
- Check product ownership in database

**Payment not working:**
- Verify PayMongo API keys
- Check webhook configuration
- Review PayMongo dashboard for errors

## 📝 Documentation

Additional documentation files:
- `PRODUCT_TRANSFER_SYSTEM_GUIDE.md` - Product transfer workflow
- `SUPERVISOR_TO_SALES_PERSON_TRANSFER.md` - Supervisor transfer guide
- `DOCUMENT_APPROVAL_SYSTEM.md` - Document approval process
- `DATABASE_SETUP_GUIDE.md` - Detailed database setup

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is proprietary software. All rights reserved.

## 👥 Authors

- Your Name - Initial work

## 🙏 Acknowledgments

- Bootstrap team for the UI framework
- PayMongo for payment processing
- PHPMailer for email functionality
- All contributors and testers

## 📞 Support

For support, email support@fixandgo.com or open an issue in the repository.

---

**Note**: Remember to update sensitive configuration files and never commit them to version control!
