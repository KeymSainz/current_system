-- ============================================================
--  Fix&Go — Live Database Patch (InfinityFree Compatible)
--  Database: if0_42189730_fixandgo
--  Run: phpMyAdmin → SQL tab → paste → Go
--  Safe to run multiple times.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ── 1. Fix users role ENUM ────────────────────────────────────────────────
ALTER TABLE `users`
  MODIFY COLUMN `role`
    ENUM('admin','customer','supplier','owner','sales_person','supervisor','phone_technician')
    NOT NULL DEFAULT 'customer';

-- ── 2. Fix provider column ────────────────────────────────────────────────
ALTER TABLE `users`
  MODIFY COLUMN `provider` VARCHAR(30) NULL DEFAULT NULL;

-- ── 3. Add missing users columns (one per statement for InfinityFree) ────
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `is_banned`          TINYINT(1)       NOT NULL DEFAULT 0;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `banned_reason`      VARCHAR(500)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `banned_at`          DATETIME         NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `login_attempts`     TINYINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `locked_until`       DATETIME         NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `last_login_at`      DATETIME         NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `last_logout_at`     DATETIME         NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `phone`              VARCHAR(30)      NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `bio`                TEXT             NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `description`        TEXT             NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `specializations`    TEXT             NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `shop_name`          VARCHAR(191)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `shop_image`         VARCHAR(500)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `profile_image`      VARCHAR(500)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `avatar_url`         VARCHAR(500)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `address_line`       VARCHAR(255)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `barangay`           VARCHAR(120)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `city`               VARCHAR(120)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `province`           VARCHAR(120)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `region`             VARCHAR(120)     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `zip_code`           VARCHAR(10)      NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `address_verified`   TINYINT(1)       NOT NULL DEFAULT 0;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `gender`             ENUM('male','female','other') NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `date_of_birth`      DATE             NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `status`             VARCHAR(30)      NULL DEFAULT 'active';
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `application_status` ENUM('pending','approved','rejected') NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `application_notes`  TEXT             NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `reviewed_by`        INT UNSIGNED     NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `reviewed_at`        DATETIME         NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `provider_id`        VARCHAR(191)     NULL;

-- ── 4. Fix otp_tokens purpose ENUM ───────────────────────────────────────
ALTER TABLE `otp_tokens`
  MODIFY COLUMN `purpose` ENUM('verify','login','reset') NOT NULL DEFAULT 'verify';
ALTER TABLE `otp_tokens` ADD COLUMN IF NOT EXISTS `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0;

-- ── 5. Create rate_limits if missing ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `identifier`   VARCHAR(100) NOT NULL,
  `action`       VARCHAR(50)  NOT NULL,
  `attempted_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ident_action` (`identifier`, `action`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 6. Create user_activity_logs if missing ───────────────────────────────
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `action`     ENUM('login','logout','login_failed','session_expired') NOT NULL,
  `ip_address` VARCHAR(45)  NULL,
  `user_agent` VARCHAR(512) NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 7. Create remember_tokens if missing ─────────────────────────────────
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `token_hash` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_token` (`token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 8. Create conversations if missing ───────────────────────────────────
CREATE TABLE IF NOT EXISTS `conversations` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_a_id`  INT UNSIGNED NOT NULL,
  `user_b_id`  INT UNSIGNED NOT NULL,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pair` (`user_a_id`, `user_b_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 9. Add missing columns to messages ───────────────────────────────────
ALTER TABLE `messages` ADD COLUMN IF NOT EXISTS `conversation_id` INT UNSIGNED NULL;
ALTER TABLE `messages` ADD COLUMN IF NOT EXISTS `file_url`        VARCHAR(500) NULL;
ALTER TABLE `messages` ADD COLUMN IF NOT EXISTS `file_type`       ENUM('image','video') NULL;
ALTER TABLE `messages` ADD COLUMN IF NOT EXISTS `file_name`       VARCHAR(255) NULL;

-- ── 10. Fix notifications columns ────────────────────────────────────────
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `type`  VARCHAR(60)  NOT NULL DEFAULT 'system';
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `title` VARCHAR(191) NULL;
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `body`  TEXT         NULL;

-- ── 11. Fix supplier_products columns ────────────────────────────────────
ALTER TABLE `supplier_products` ADD COLUMN IF NOT EXISTS `current_holder_id` INT UNSIGNED NULL;
ALTER TABLE `supplier_products` ADD COLUMN IF NOT EXISTS `holder_type`        ENUM('supplier','owner','supervisor','sales_person','phone_technician') NULL;
ALTER TABLE `supplier_products` ADD COLUMN IF NOT EXISTS `is_displayed`       TINYINT(1)   NOT NULL DEFAULT 0;
ALTER TABLE `supplier_products` ADD COLUMN IF NOT EXISTS `sent_at`            DATETIME     NULL;
ALTER TABLE `supplier_products` ADD COLUMN IF NOT EXISTS `verified_at`        DATETIME     NULL;
ALTER TABLE `supplier_products` ADD COLUMN IF NOT EXISTS `notes`              TEXT         NULL;
ALTER TABLE `supplier_products` ADD COLUMN IF NOT EXISTS `brand`              VARCHAR(100) NULL;

-- ── 12. Fix seller_applications columns ──────────────────────────────────
ALTER TABLE `seller_applications`
  MODIFY COLUMN `role` ENUM('supplier','owner','phone_technician') NOT NULL DEFAULT 'supplier';
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `middle_name`      VARCHAR(80)   NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `suffix`           VARCHAR(20)   NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `shop_address`     TEXT          NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `address_lat`      DECIMAL(10,7) NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `address_lng`      DECIMAL(10,7) NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `specializations`  TEXT          NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `experience_yrs`   TINYINT UNSIGNED NULL DEFAULT 0;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `entity_type`      ENUM('sole_proprietorship','corporation','one_person_corp') NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `business_name`    VARCHAR(191)  NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `general_location` VARCHAR(191)  NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `business_email`   VARCHAR(191)  NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `doc_cert`         VARCHAR(500)  NULL;
ALTER TABLE `seller_applications` ADD COLUMN IF NOT EXISTS `overall_status`   VARCHAR(50)   NULL;

-- ── 13. Fix bookings columns ──────────────────────────────────────────────
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `fault_description`       TEXT          NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `issue_description`       TEXT          NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `phone_history`           TEXT          NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `expected_fix`            TEXT          NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `phone_photo`             VARCHAR(500)  NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `service_type`            ENUM('home_service','shop_fix') NOT NULL DEFAULT 'shop_fix';
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `repair_fee`              DECIMAL(10,2) NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `labor_fee`               DECIMAL(10,2) NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `parts_fee`               DECIMAL(10,2) NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `payment_note`            VARCHAR(255)  NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `payment_status`          ENUM('unpaid','paid','pending_collection') NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `receipt_path`            VARCHAR(500)  NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `price_photo_path`        VARCHAR(500)  NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `customer_payment_method` ENUM('cash','gcash','maya','bank_transfer','card','online','other') NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `customer_payment_status` ENUM('pending','paid') NOT NULL DEFAULT 'pending';
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `customer_payment_note`   VARCHAR(255)  NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `customer_paid_at`        DATETIME      NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `parts_replaced`          TEXT          NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `contact_number`          VARCHAR(30)   NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `device_name`             VARCHAR(191)  NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `device_model`            VARCHAR(191)  NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `cancel_reason`           VARCHAR(255)  NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `total_amount`            DECIMAL(10,2) NULL;
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `payment_method`          ENUM('cash','bank_transfer','gcash','maya','other') NULL;

-- ── 14. Create wishlist if missing ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_product` (`user_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 15. Create vouchers if missing ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `code`           VARCHAR(50)   NOT NULL,
  `discount_type`  ENUM('fixed','percent') NOT NULL DEFAULT 'fixed',
  `discount_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `min_order`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_uses`       INT UNSIGNED  NOT NULL DEFAULT 1,
  `used_count`     INT UNSIGNED  NOT NULL DEFAULT 0,
  `valid_from`     DATETIME      NULL,
  `valid_until`    DATETIME      NULL,
  `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 16. Create customer_payments if missing ───────────────────────────────
CREATE TABLE IF NOT EXISTS `customer_payments` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `customer_id`   INT UNSIGNED  NOT NULL,
  `reference`     VARCHAR(100)  NOT NULL,
  `paymongo_id`   VARCHAR(100)  NULL,
  `amount`        DECIMAL(10,2) NOT NULL,
  `currency`      CHAR(3)       NOT NULL DEFAULT 'PHP',
  `status`        ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `checkout_url`  VARCHAR(1000) NULL,
  `cart_snapshot` JSON          NULL,
  `paid_at`       DATETIME      NULL,
  `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reference` (`reference`),
  KEY `idx_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 17. Fix shops table columns ───────────────────────────────────────────
ALTER TABLE `shops` ADD COLUMN IF NOT EXISTS `logo_url`    VARCHAR(500) NULL;
ALTER TABLE `shops` ADD COLUMN IF NOT EXISTS `is_active`   TINYINT(1)   NOT NULL DEFAULT 1;
ALTER TABLE `shops` ADD COLUMN IF NOT EXISTS `description` TEXT         NULL;
ALTER TABLE `shops` ADD COLUMN IF NOT EXISTS `city`        VARCHAR(120) NULL;
ALTER TABLE `shops` ADD COLUMN IF NOT EXISTS `address`     TEXT         NULL;
ALTER TABLE `shops` ADD COLUMN IF NOT EXISTS `phone`       VARCHAR(30)  NULL;
ALTER TABLE `shops` ADD COLUMN IF NOT EXISTS `email`       VARCHAR(191) NULL;

-- ── 18. Fix technician_profiles columns ──────────────────────────────────
ALTER TABLE `technician_profiles` ADD COLUMN IF NOT EXISTS `description`   TEXT NULL;
ALTER TABLE `technician_profiles` ADD COLUMN IF NOT EXISTS `certifications` TEXT NULL;

-- ── 19. Fix customer_orders columns ──────────────────────────────────────
ALTER TABLE `customer_orders` ADD COLUMN IF NOT EXISTS `cancel_reason` VARCHAR(255) NULL;
ALTER TABLE `customer_orders` ADD COLUMN IF NOT EXISTS `cancel_notes`  TEXT         NULL;

-- ── 20. Fix owner_payments columns ───────────────────────────────────────
ALTER TABLE `owner_payments` ADD COLUMN IF NOT EXISTS `purchase_quantities` JSON NULL;

-- ── 21. Create supervisor_reports if missing ──────────────────────────────
CREATE TABLE IF NOT EXISTS `supervisor_reports` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `supervisor_id`  INT UNSIGNED  NOT NULL,
  `owner_id`       INT UNSIGNED  NULL,
  `report_year`    SMALLINT UNSIGNED NOT NULL,
  `report_month`   TINYINT UNSIGNED  NOT NULL,
  `total_products` INT UNSIGNED  NOT NULL DEFAULT 0,
  `total_quantity` INT UNSIGNED  NOT NULL DEFAULT 0,
  `total_value`    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `report_data`    JSON          NULL,
  `sent_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_supervisor` (`supervisor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 22. Add admin user (Password: Admin@1234) ─────────────────────────────
INSERT IGNORE INTO `users`
  (`first_name`, `last_name`, `email`, `password_hash`, `role`, `is_verified`, `is_active`)
VALUES
  ('Fix', 'Admin', 'admin@fixandgo.com',
   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'admin', 1, 1);

-- ── 23. Mark your accounts as verified ───────────────────────────────────
UPDATE `users` SET `is_verified` = 1 WHERE `email` IN ('kimsacatan@gmail.com','kimlingas7@gmail.com');

-- ── Re-enable FK checks ───────────────────────────────────────────────────
SET FOREIGN_KEY_CHECKS = 1;

-- ── Verify: show all users ────────────────────────────────────────────────
SELECT `id`, `email`, `role`, `is_verified`, `is_active` FROM `users` ORDER BY `id`;
