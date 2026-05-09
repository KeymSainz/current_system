# Fix&Go Database Setup Guide

This guide will help you set up the complete Fix&Go database with all necessary tables and sample data.

## 📋 Prerequisites

- MySQL 5.7+ or MariaDB 10.2+
- PHP 7.4+ with PDO MySQL extension
- phpMyAdmin (optional, for GUI-based setup)
- Command-line access to MySQL (optional, for CLI-based setup)

## 🗄️ Database Structure

The Fix&Go system includes the following tables:

### Core Tables
- **users** - All user accounts (customer, owner, supplier, staff, technician)
- **otp_tokens** - Email verification and password reset codes
- **remember_tokens** - "Remember me" session tokens
- **rate_limits** - Rate limiting for security

### Shop & Booking Tables
- **shops** - Shop information owned by owners
- **shop_members** - Links technicians to shops
- **technician_profiles** - Extended profiles for technicians
- **services** - Repair services offered by shops
- **devices** - Customer devices
- **bookings** - Service bookings
- **booking_status_history** - Audit trail for bookings
- **reviews** - Customer reviews
- **messages** - In-app messaging
- **notifications** - System notifications
- **promotions** - Discount codes

### Product & Inventory Tables
- **supplier_products** - Products from suppliers AND owner's inventory
- **owner_inventory** - Purchase history tracking (optional)
- **payments** - PayMongo payment records

## 🚀 Setup Methods

### Method 1: Using phpMyAdmin (Recommended for Beginners)

1. **Open phpMyAdmin** in your browser (usually at `http://localhost/phpmyadmin`)

2. **Click on "Import" tab** in the top menu

3. **Choose the SQL file**:
   - Click "Choose File" button
   - Navigate to: `fixandgo/backend/complete_database_setup.sql`
   - Select the file

4. **Import the database**:
   - Leave all settings as default
   - Click "Go" button at the bottom
   - Wait for the import to complete

5. **Verify the setup**:
   - You should see a new database called `fixandgo`
   - Click on `fixandgo` in the left sidebar
   - You should see 18 tables listed

### Method 2: Using MySQL Command Line

1. **Open your terminal/command prompt**

2. **Navigate to the backend folder**:
   ```bash
   cd fixandgo/backend
   ```

3. **Run the SQL file**:
   ```bash
   mysql -u root -p < complete_database_setup.sql
   ```
   
4. **Enter your MySQL root password** when prompted

5. **Verify the setup**:
   ```bash
   mysql -u root -p
   USE fixandgo;
   SHOW TABLES;
   ```
   You should see 18 tables listed.

### Method 3: Copy-Paste in phpMyAdmin

1. **Open the SQL file** in a text editor:
   - File: `fixandgo/backend/complete_database_setup.sql`

2. **Copy all the content** (Ctrl+A, Ctrl+C)

3. **Open phpMyAdmin** and click on "SQL" tab

4. **Paste the content** into the SQL query box

5. **Click "Go"** to execute

## ⚙️ Configuration

After setting up the database, update your database credentials:

1. **Open** `fixandgo/backend/config.php`

2. **Update the database settings**:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'fixandgo');
   define('DB_USER', 'root');          // Change if needed
   define('DB_PASS', '');              // Add your password
   define('DB_CHARSET', 'utf8mb4');
   ```

3. **Save the file**

## 🎯 Sample Accounts

The database includes sample accounts for testing. All accounts use the password: **Password1**

| Role | Email | Password |
|------|-------|----------|
| Owner | owner@fixandgo.com | Password1 |
| Supplier | supplier@fixandgo.com | Password1 |
| Supervisor | supervisor@fixandgo.com | Password1 |
| Sales Person | sales@fixandgo.com | Password1 |
| Technician | carlos@fixandgo.com | Password1 |
| Technician | ana@fixandgo.com | Password1 |
| Technician | marco@fixandgo.com | Password1 |
| Customer | customer@fixandgo.com | Password1 |

## 🔧 Enable Event Scheduler (Optional but Recommended)

The database includes automatic cleanup events for expired tokens. To enable them:

### Using phpMyAdmin:
1. Click on "SQL" tab
2. Run this command:
   ```sql
   SET GLOBAL event_scheduler = ON;
   ```

### Using MySQL Command Line:
```bash
mysql -u root -p -e "SET GLOBAL event_scheduler = ON;"
```

### Make it Permanent:
Add this line to your MySQL configuration file (`my.cnf` or `my.ini`):
```ini
[mysqld]
event_scheduler = ON
```

## 📊 Database Features

### Staff Management
- **Supervisors & Sales Persons**: Can be registered directly by owners or apply themselves
- **Technicians**: Must apply through staff registration page
- **Approval System**: Pending applications require owner approval
- **Status Tracking**: Active/Inactive status for all staff

### Product & Inventory System
- **Supplier Products**: Suppliers submit products for owner review
- **Owner Inventory**: After payment, products are added to owner's inventory
- **Purchase History**: Tracks all owner purchases from suppliers
- **PayMongo Integration**: Secure payment processing

### Security Features
- **Password Hashing**: Bcrypt encryption for all passwords
- **OTP Verification**: Email verification for new accounts
- **Rate Limiting**: Prevents brute force attacks
- **Remember Me Tokens**: Secure persistent sessions

## 🔍 Verify Installation

### Check Tables:
```sql
USE fixandgo;
SHOW TABLES;
```
You should see 18 tables.

### Check Sample Data:
```sql
SELECT email, role FROM users;
```
You should see 8 sample users.

### Check Products:
```sql
SELECT name, price, status FROM supplier_products;
```
You should see 3 sample products.

## 🐛 Troubleshooting

### Error: "Database already exists"
- The script uses `CREATE DATABASE IF NOT EXISTS`, so this shouldn't happen
- If you want to start fresh, drop the database first:
  ```sql
  DROP DATABASE IF EXISTS fixandgo;
  ```
  Then run the setup script again.

### Error: "Access denied"
- Check your MySQL username and password
- Make sure your MySQL user has CREATE DATABASE privileges
- Try using root account: `mysql -u root -p`

### Error: "Table already exists"
- The script uses `CREATE TABLE IF NOT EXISTS`, so this is safe
- If you want to recreate tables, drop them first or drop the entire database

### Sample data not inserted
- The script uses `INSERT IGNORE`, so duplicates are skipped
- If you've already inserted sample data, it won't be duplicated
- To reset sample data, delete from users table first:
  ```sql
  DELETE FROM users WHERE email LIKE '%@fixandgo.com';
  ```

### Event scheduler not working
- Check if it's enabled: `SHOW VARIABLES LIKE 'event_scheduler';`
- Should show "ON"
- If "OFF", run: `SET GLOBAL event_scheduler = ON;`

## 📝 Next Steps

After successful database setup:

1. ✅ Update `backend/config.php` with your database credentials
2. ✅ Test login with sample accounts
3. ✅ Configure PayMongo API keys in `backend/config.php`
4. ✅ Set up email configuration for OTP delivery
5. ✅ Remove sample data before going live
6. ✅ Change all default passwords

## 🔒 Security Recommendations

Before deploying to production:

1. **Remove sample data**:
   ```sql
   DELETE FROM users WHERE email LIKE '%@fixandgo.com';
   DELETE FROM shops WHERE id = 1;
   DELETE FROM services WHERE shop_id = 1;
   DELETE FROM supplier_products WHERE id <= 3;
   ```

2. **Create a dedicated database user** (don't use root):
   ```sql
   CREATE USER 'fixandgo_user'@'localhost' IDENTIFIED BY 'strong_password_here';
   GRANT ALL PRIVILEGES ON fixandgo.* TO 'fixandgo_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **Update config.php** with the new user credentials

4. **Enable SSL** for database connections in production

5. **Regular backups**: Set up automated database backups

## 📞 Support

If you encounter any issues:
1. Check the error logs in phpMyAdmin or MySQL error log
2. Verify your MySQL version is 5.7 or higher
3. Ensure PHP PDO MySQL extension is installed
4. Check file permissions on the backend folder

---

**Database Version**: 1.0  
**Last Updated**: 2026-05-01  
**Compatible with**: MySQL 5.7+, MariaDB 10.2+
