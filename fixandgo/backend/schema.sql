-- ============================================================
--  Fix&Go — Complete Database Schema
--  Import this file ONCE into phpMyAdmin to create all tables.
--  Database: if0_42315458_fixandgo  (InfinityFree)
--  Charset:  utf8mb4 / utf8mb4_unicode_ci
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ── 1. users ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`                  INT UNSIGNED      NOT NULL AUTO_INCREMENT,
  `first_name`          VARCHAR(80)       NOT NULL,
  `last_name`           VARCHAR(80)       NOT NULL,
  `email`               VARCHAR(191)      NOT NULL,
  `password_hash`       VARCHAR(255)      NULL,
  `role`                ENUM('admin','customer','supplier','owner','sales_person','supervisor','phone_technician') NOT NULL DEFAULT 'customer',
  `provider`            VARCHAR(30)       NULL DEFAULT NULL COMMENT 'google, etc.',
  `provider_id`         VARCHAR(191)      NULL DEFAULT NULL,
  `is_verified`         TINYINT(1)        NOT NULL DEFAULT 0,
  `is_active`           TINYINT(1)        NOT NULL DEFAULT 1,
  `is_banned`           TINYINT(1)        NOT NULL DEFAULT 0,
  `banned_reason`       VARCHAR(500)      NULL,
  `banned_at`           DATETIME          NULL,
  `application_status`  ENUM('pending','approved','rejected') NULL,
  `application_notes`   TEXT              NULL,
  `reviewed_by`         INT UNSIGNED      NULL,
  `reviewed_at`         DATETIME          NULL,
  `login_attempts`      TINYINT UNSIGNED  NOT NULL DEFAULT 0,
  `locked_until`        DATETIME          NULL,
  `last_login_at`       DATETIME          NULL,
  `last_logout_at`      DATETIME          NULL,
  `phone`               VARCHAR(30)       NULL,
  `bio`                 TEXT              NULL,
  `description`         TEXT              NULL,
  `specializations`     TEXT              NULL,
  `shop_name`           VARCHAR(191)      NULL,
  `shop_image`          VARCHAR(500)      NULL,
  `profile_image`       VARCHAR(500)      NULL,
  `avatar_url`          VARCHAR(500)      NULL,
  `address_line`        VARCHAR(255)      NULL,
  `barangay`            VARCHAR(120)      NULL,
  `city`                VARCHAR(120)      NULL,
  `province`            VARCHAR(120)      NULL,
  `region`              VARCHAR(120)      NULL,
  `zip_code`            VARCHAR(10)       NULL,
  `address_verified`    TINYINT(1)        NOT NULL DEFAULT 0,
  `gender`              ENUM('male','female','other') NULL,
  `date_of_birth`       DATE              NULL,
  `status`              VARCHAR(30)       NULL DEFAULT 'active',
  `created_at`          DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. otp_tokens ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `otp_tokens` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `otp_hash`   VARCHAR(255) NOT NULL,
  `purpose`    ENUM('verify','login','reset') NOT NULL DEFAULT 'verify',
  `expires_at` DATETIME     NOT NULL,
  `attempts`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_purpose` (`user_id`, `purpose`),
  CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 3. rate_limits ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `identifier`   VARCHAR(100)  NOT NULL COMMENT 'IP address or user_id',
  `action`       VARCHAR(50)   NOT NULL COMMENT 'login, register, otp, forgot_password',
  `attempted_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ident_action` (`identifier`, `action`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 4. remember_tokens ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `token_hash` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_token` (`token_hash`),
  CONSTRAINT `fk_rt_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 5. user_activity_logs ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `action`     ENUM('login','logout','login_failed','session_expired') NOT NULL,
  `ip_address` VARCHAR(45)  NULL,
  `user_agent` VARCHAR(512) NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_ual_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 6. seller_applications ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `seller_applications` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`          INT UNSIGNED  NOT NULL,
  `role`             ENUM('supplier','owner','phone_technician') NOT NULL DEFAULT 'supplier',
  `first_name`       VARCHAR(80)   NULL,
  `last_name`        VARCHAR(80)   NULL,
  `middle_name`      VARCHAR(80)   NULL,
  `suffix`           VARCHAR(20)   NULL,
  `email`            VARCHAR(191)  NULL,
  `phone`            VARCHAR(30)   NULL,
  `company_name`     VARCHAR(191)  NULL,
  `shop_name`        VARCHAR(191)  NULL,
  `shop_address`     TEXT          NULL,
  `address_lat`      DECIMAL(10,7) NULL,
  `address_lng`      DECIMAL(10,7) NULL,
  `specializations`  TEXT          NULL,
  `experience_yrs`   TINYINT UNSIGNED NULL DEFAULT 0,
  `entity_type`      ENUM('sole_proprietorship','corporation','one_person_corp') NULL,
  `business_name`    VARCHAR(191)  NULL,
  `general_location` VARCHAR(191)  NULL,
  `zip_code`         VARCHAR(10)   NULL,
  `business_email`   VARCHAR(191)  NULL,
  `doc_gov_id`       VARCHAR(500)  NULL,
  `doc_bir`          VARCHAR(500)  NULL,
  `doc_dti`          VARCHAR(500)  NULL,
  `doc_bank`         VARCHAR(500)  NULL,
  `doc_cert`         VARCHAR(500)  NULL,
  `status`           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `overall_status`   VARCHAR(50)   NULL,
  `admin_notes`      TEXT          NULL,
  `reviewed_by`      INT UNSIGNED  NULL,
  `reviewed_at`      DATETIME      NULL,
  `submitted_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id`  (`user_id`),
  KEY `idx_status`   (`status`),
  CONSTRAINT `fk_sa_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 7. document_approvals ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `document_approvals` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_id`   INT UNSIGNED NOT NULL,
  `document_type`    ENUM('gov_id','bir','dti','bank','cert') NOT NULL,
  `status`           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` TEXT         NULL,
  `reviewed_by`      INT UNSIGNED NULL,
  `reviewed_at`      DATETIME     NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_app_doc` (`application_id`, `document_type`),
  CONSTRAINT `fk_da_app` FOREIGN KEY (`application_id`) REFERENCES `seller_applications`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 8. supplier_products ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `supplier_products` (
  `id`                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `supplier_id`       INT UNSIGNED  NOT NULL,
  `category`          VARCHAR(100)  NOT NULL,
  `brand`             VARCHAR(100)  NULL,
  `item_description`  TEXT          NOT NULL,
  `qty`               INT UNSIGNED  NOT NULL DEFAULT 0,
  `srp`               DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image_path`        VARCHAR(500)  NULL,
  `status`            ENUM('draft','verified','sent_to_owner','owner_received','sent_to_supervisor','sent_to_sales_person','with_tech','rejected') NOT NULL DEFAULT 'draft',
  `notes`             TEXT          NULL,
  `current_holder_id` INT UNSIGNED  NULL,
  `holder_type`       ENUM('supplier','owner','supervisor','sales_person','phone_technician') NULL,
  `is_displayed`      TINYINT(1)    NOT NULL DEFAULT 0,
  `sent_at`           DATETIME      NULL,
  `verified_at`       DATETIME      NULL,
  `created_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_supplier`    (`supplier_id`),
  KEY `idx_holder`      (`current_holder_id`),
  KEY `idx_status`      (`status`),
  KEY `idx_is_displayed`(`is_displayed`),
  CONSTRAINT `fk_sp_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 9. product_submissions ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_submissions` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_id`      INT UNSIGNED NOT NULL,
  `owner_id`         INT UNSIGNED NOT NULL,
  `status`           ENUM('pending','acknowledged','rejected') NOT NULL DEFAULT 'pending',
  `acknowledged_at`  DATETIME     NULL,
  `submitted_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_owner`    (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 10. submission_items ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `submission_items` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` INT UNSIGNED NOT NULL,
  `product_id`    INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_submission` (`submission_id`),
  CONSTRAINT `fk_si_sub`  FOREIGN KEY (`submission_id`) REFERENCES `product_submissions`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_si_prod` FOREIGN KEY (`product_id`)    REFERENCES `supplier_products`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 11. product_transfers ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_transfers` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`    INT UNSIGNED NOT NULL,
  `from_user_id`  INT UNSIGNED NOT NULL,
  `to_user_id`    INT UNSIGNED NOT NULL,
  `transfer_type` ENUM('owner_to_supervisor','supervisor_to_sales') NOT NULL,
  `quantity`      INT UNSIGNED NOT NULL DEFAULT 1,
  `status`        ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `notes`         TEXT         NULL,
  `transferred_at`DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at`  DATETIME     NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product`   (`product_id`),
  KEY `idx_from_user` (`from_user_id`),
  KEY `idx_to_user`   (`to_user_id`),
  CONSTRAINT `fk_pt_product` FOREIGN KEY (`product_id`) REFERENCES `supplier_products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 12. product_transfer_history ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_transfer_history` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`   INT UNSIGNED NOT NULL,
  `from_user_id` INT UNSIGNED NULL,
  `to_user_id`   INT UNSIGNED NULL,
  `action`       VARCHAR(60)  NOT NULL,
  `quantity`     INT UNSIGNED NOT NULL DEFAULT 1,
  `notes`        TEXT         NULL,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 13. owner_payments ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `owner_payments` (
  `id`                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `owner_id`            INT UNSIGNED  NOT NULL,
  `reference`           VARCHAR(100)  NOT NULL,
  `paymongo_id`         VARCHAR(100)  NULL,
  `amount`              DECIMAL(10,2) NOT NULL,
  `currency`            CHAR(3)       NOT NULL DEFAULT 'PHP',
  `status`              ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `checkout_url`        VARCHAR(1000) NULL,
  `product_ids`         JSON          NULL,
  `purchase_quantities` JSON          NULL,
  `paid_at`             DATETIME      NULL,
  `created_at`          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reference` (`reference`),
  KEY `idx_owner` (`owner_id`),
  CONSTRAINT `fk_op_owner` FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 14. owner_inventory ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `owner_inventory` (
  `id`                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `owner_id`            INT UNSIGNED  NOT NULL,
  `supplier_id`         INT UNSIGNED  NULL,
  `supplier_product_id` INT UNSIGNED  NULL,
  `payment_id`          INT UNSIGNED  NULL,
  `category`            VARCHAR(100)  NULL,
  `brand`               VARCHAR(100)  NULL,
  `item_description`    TEXT          NULL,
  `qty`                 INT UNSIGNED  NOT NULL DEFAULT 0,
  `unit_price`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_price`         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image_path`          VARCHAR(500)  NULL,
  `notes`               TEXT          NULL,
  `purchased_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_owner`   (`owner_id`),
  KEY `idx_payment` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 15. customer_orders ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `customer_orders` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `customer_id`    INT UNSIGNED  NOT NULL,
  `product_id`     INT UNSIGNED  NOT NULL,
  `quantity`       INT UNSIGNED  NOT NULL DEFAULT 1,
  `unit_price`     DECIMAL(10,2) NOT NULL,
  `total_amount`   DECIMAL(10,2) NOT NULL,
  `status`         ENUM('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` ENUM('cod','gcash','card','online','paymongo') NOT NULL DEFAULT 'cod',
  `notes`          TEXT          NULL,
  `cancel_reason`  VARCHAR(255)  NULL,
  `cancel_notes`   TEXT          NULL,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_customer`  (`customer_id`),
  KEY `idx_product`   (`product_id`),
  KEY `idx_status`    (`status`),
  CONSTRAINT `fk_co_customer` FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_co_product`  FOREIGN KEY (`product_id`)  REFERENCES `supplier_products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 16. product_reviews ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`  INT UNSIGNED NOT NULL,
  `customer_id` INT UNSIGNED NOT NULL,
  `order_id`    INT UNSIGNED NULL,
  `rating`      TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `review_text` TEXT         NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customer_product` (`customer_id`, `product_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_pr_product`  FOREIGN KEY (`product_id`)  REFERENCES `supplier_products`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pr_customer` FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 17. bookings ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `bookings` (
  `id`                       INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `customer_id`              INT UNSIGNED  NOT NULL,
  `technician_id`            INT UNSIGNED  NULL,
  `contact_number`           VARCHAR(30)   NULL,
  `address`                  TEXT          NULL,
  `device_name`              VARCHAR(191)  NULL,
  `device_model`             VARCHAR(191)  NULL,
  `problem_desc`             TEXT          NULL,
  `fault_description`        TEXT          NULL,
  `issue_description`        TEXT          NULL,
  `phone_history`            TEXT          NULL,
  `expected_fix`             TEXT          NULL,
  `phone_photo`              VARCHAR(500)  NULL,
  `service_type`             ENUM('home_service','shop_fix') NOT NULL DEFAULT 'shop_fix',
  `scheduled_at`             DATETIME      NULL,
  `status`                   ENUM('pending','confirmed','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `technician_notes`         TEXT          NULL,
  `notes`                    TEXT          NULL,
  `total_price`              DECIMAL(10,2) NULL,
  `total_amount`             DECIMAL(10,2) NULL,
  -- Technician-side payment (set by technician)
  `payment_method`           ENUM('cash','bank_transfer','gcash','maya','other') NULL,
  `repair_fee`               DECIMAL(10,2) NULL COMMENT 'Total repair fee (labor+parts)',
  `labor_fee`                DECIMAL(10,2) NULL COMMENT 'Labor / service fee',
  `parts_fee`                DECIMAL(10,2) NULL COMMENT 'Parts / replacement cost',
  `payment_note`             VARCHAR(255)  NULL COMMENT 'Account or reference',
  `payment_status`           ENUM('unpaid','paid','pending_collection') NULL,
  `receipt_path`             VARCHAR(500)  NULL COMMENT 'Receipt uploaded by technician',
  `price_photo_path`         VARCHAR(500)  NULL COMMENT 'Product/parts price photo',
  -- Customer-side payment (set by customer)
  `customer_payment_method`  ENUM('cash','gcash','maya','bank_transfer','card','online','other') NULL,
  `customer_payment_status`  ENUM('pending','paid') NOT NULL DEFAULT 'pending',
  `customer_payment_note`    VARCHAR(255)  NULL,
  `customer_paid_at`         DATETIME      NULL,
  `parts_replaced`           TEXT          NULL COMMENT 'JSON array of parts replaced',
  `created_at`               DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`               DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_customer`               (`customer_id`),
  KEY `idx_technician`             (`technician_id`),
  KEY `idx_status`                 (`status`),
  KEY `idx_payment_method`         (`payment_method`),
  KEY `idx_payment_status`         (`payment_status`),
  KEY `idx_customer_payment_status`(`customer_payment_status`),
  CONSTRAINT `fk_bk_customer`    FOREIGN KEY (`customer_id`)   REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bk_technician`  FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 18. repair_payments ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `repair_payments` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `booking_id`    INT UNSIGNED  NOT NULL,
  `customer_id`   INT UNSIGNED  NOT NULL,
  `technician_id` INT UNSIGNED  NULL,
  `reference`     VARCHAR(100)  NOT NULL,
  `paymongo_id`   VARCHAR(100)  NULL,
  `amount`        DECIMAL(10,2) NOT NULL,
  `currency`      CHAR(3)       NOT NULL DEFAULT 'PHP',
  `status`        ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `checkout_url`  VARCHAR(1000) NULL,
  `paid_at`       DATETIME      NULL,
  `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reference` (`reference`),
  KEY `idx_booking`   (`booking_id`),
  KEY `idx_customer`  (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 19. technician_profiles ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `technician_profiles` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`          INT UNSIGNED NOT NULL,
  `bio`              TEXT         NULL,
  `specialization`   TEXT         NULL,
  `experience_years` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `description`      TEXT         NULL,
  `availability`     ENUM('available','busy','unavailable') NOT NULL DEFAULT 'available',
  `rating_avg`       DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  `rating_count`     INT UNSIGNED NOT NULL DEFAULT 0,
  `certifications`   TEXT         NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user` (`user_id`),
  CONSTRAINT `fk_tp_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 20. technician_credentials ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `technician_credentials` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `technician_id` INT UNSIGNED NOT NULL,
  `doc_type`      ENUM('gov_id','bir','dti','tech_cert','tesda','nstp','bank','skill_cert','shop_image','work_video','custom') NOT NULL,
  `label`         VARCHAR(120) NULL,
  `file_url`      VARCHAR(500) NOT NULL,
  `file_name`     VARCHAR(255) NULL,
  `file_ext`      VARCHAR(10)  NULL,
  `is_image`      TINYINT(1)   NOT NULL DEFAULT 0,
  `is_video`      TINYINT(1)   NOT NULL DEFAULT 0,
  `display_order` SMALLINT     NOT NULL DEFAULT 0,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tech` (`technician_id`),
  CONSTRAINT `fk_tc_tech` FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 21. technician_reviews ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `technician_reviews` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id`    INT UNSIGNED NOT NULL,
  `technician_id` INT UNSIGNED NOT NULL,
  `customer_id`   INT UNSIGNED NOT NULL,
  `rating`        TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `comment`       TEXT         NULL,
  `media_1_url`   VARCHAR(500) NULL,
  `media_1_type`  ENUM('image','video') NULL,
  `media_2_url`   VARCHAR(500) NULL,
  `media_2_type`  ENUM('image','video') NULL,
  `media_3_url`   VARCHAR(500) NULL,
  `media_3_type`  ENUM('image','video') NULL,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_booking` (`booking_id`),
  KEY `idx_technician` (`technician_id`),
  KEY `idx_customer`   (`customer_id`),
  CONSTRAINT `fk_tr_booking`    FOREIGN KEY (`booking_id`)    REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tr_technician` FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`)   ON DELETE CASCADE,
  CONSTRAINT `fk_tr_customer`   FOREIGN KEY (`customer_id`)   REFERENCES `users`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 22. sales_products ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `sales_products` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `sales_person_id`INT UNSIGNED  NOT NULL,
  `name`           VARCHAR(191)  NOT NULL,
  `description`    TEXT          NULL,
  `category`       VARCHAR(100)  NULL,
  `price`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock`          INT UNSIGNED  NOT NULL DEFAULT 0,
  `image_path`     VARCHAR(500)  NULL,
  `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sales_person` (`sales_person_id`),
  CONSTRAINT `fk_salesp_user` FOREIGN KEY (`sales_person_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 23. supply_requests ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `supply_requests` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sales_person_id`    INT UNSIGNED NOT NULL,
  `product_name`       VARCHAR(191) NOT NULL,
  `category`           VARCHAR(100) NULL,
  `quantity_requested` INT UNSIGNED NOT NULL DEFAULT 1,
  `reason`             TEXT         NULL,
  `status`             ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `supervisor_notes`   TEXT         NULL,
  `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sales_person` (`sales_person_id`),
  CONSTRAINT `fk_sr_user` FOREIGN KEY (`sales_person_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 24. supervisor_reports ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `supervisor_reports` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `supervisor_id`  INT UNSIGNED  NOT NULL,
  `owner_id`       INT UNSIGNED  NULL,
  `report_year`    SMALLINT UNSIGNED NOT NULL,
  `report_month`   TINYINT UNSIGNED  NOT NULL,
  `total_products` INT UNSIGNED  NOT NULL DEFAULT 0,
  `total_quantity` INT UNSIGNED  NOT NULL DEFAULT 0,
  `total_value`    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `report_data`    JSON          NULL COMMENT 'Serialized product snapshot',
  `sent_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_supervisor` (`supervisor_id`),
  KEY `idx_owner`      (`owner_id`),
  CONSTRAINT `fk_srep_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 25. technician_orders ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `technician_orders` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `technician_id`    INT UNSIGNED  NOT NULL,
  `seller_id`        INT UNSIGNED  NULL,
  `seller_role`      ENUM('supplier','owner') NULL,
  `fulfillment_type` ENUM('pickup','delivery') NOT NULL DEFAULT 'delivery',
  `delivery_address` TEXT          NULL,
  `payment_method`   ENUM('cod','gcash','card','online') NOT NULL DEFAULT 'cod',
  `payment_status`   ENUM('pending','paid','failed')     NOT NULL DEFAULT 'pending',
  `order_status`     ENUM('pending','confirmed','preparing','ready','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal`         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `shipping_fee`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_amount`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `reference`        VARCHAR(100)  NULL,
  `paymongo_id`      VARCHAR(100)  NULL,
  `checkout_url`     VARCHAR(1000) NULL,
  `notes`            TEXT          NULL,
  `seller_notes`     TEXT          NULL,
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reference` (`reference`),
  KEY `idx_technician` (`technician_id`),
  KEY `idx_seller`     (`seller_id`),
  CONSTRAINT `fk_to_tech` FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 26. technician_order_items ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `technician_order_items` (
  `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `order_id`     INT UNSIGNED  NOT NULL,
  `product_id`   INT UNSIGNED  NULL,
  `product_name` VARCHAR(255)  NOT NULL,
  `category`     VARCHAR(100)  NULL,
  `unit_price`   DECIMAL(10,2) NOT NULL,
  `quantity`     INT UNSIGNED  NOT NULL DEFAULT 1,
  `subtotal`     DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order`   (`order_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_toi_order`   FOREIGN KEY (`order_id`)   REFERENCES `technician_orders`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_toi_product` FOREIGN KEY (`product_id`) REFERENCES `supplier_products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 27. technician_supply_requests ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `technician_supply_requests` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `technician_id`      INT UNSIGNED NOT NULL,
  `product_id`         INT UNSIGNED NULL,
  `supplier_id`        INT UNSIGNED NULL,
  `quantity_requested` INT UNSIGNED NOT NULL DEFAULT 1,
  `note`               TEXT         NULL,
  `status`             ENUM('pending','approved','rejected','fulfilled','cancelled') NOT NULL DEFAULT 'pending',
  `supplier_notes`     TEXT         NULL,
  `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_technician` (`technician_id`),
  KEY `idx_product`    (`product_id`),
  KEY `idx_supplier`   (`supplier_id`),
  CONSTRAINT `fk_tsr_tech`     FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`)             ON DELETE CASCADE,
  CONSTRAINT `fk_tsr_product`  FOREIGN KEY (`product_id`)    REFERENCES `supplier_products`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_tsr_supplier` FOREIGN KEY (`supplier_id`)   REFERENCES `users`(`id`)             ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 28. conversations ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `conversations` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_a_id`  INT UNSIGNED NOT NULL COMMENT 'Lower of the two user IDs',
  `user_b_id`  INT UNSIGNED NOT NULL COMMENT 'Higher of the two user IDs',
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pair`    (`user_a_id`, `user_b_id`),
  KEY          `idx_user_a` (`user_a_id`),
  KEY          `idx_user_b` (`user_b_id`),
  CONSTRAINT `fk_conv_a` FOREIGN KEY (`user_a_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_conv_b` FOREIGN KEY (`user_b_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 29. messages ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `messages` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` INT UNSIGNED NOT NULL,
  `sender_id`       INT UNSIGNED NOT NULL,
  `body`            TEXT         NULL,
  `file_url`        VARCHAR(500) NULL,
  `file_type`       ENUM('image','video') NULL,
  `file_name`       VARCHAR(255) NULL,
  `is_read`         TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_conversation` (`conversation_id`),
  KEY `idx_sender`       (`sender_id`),
  KEY `idx_is_read`      (`is_read`),
  CONSTRAINT `fk_msg_conv`   FOREIGN KEY (`conversation_id`) REFERENCES `conversations`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`)       REFERENCES `users`(`id`)         ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 30. notifications ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `notifications` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `type`       VARCHAR(60)  NOT NULL DEFAULT 'system',
  `title`      VARCHAR(191) NOT NULL,
  `body`       TEXT         NULL,
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user`    (`user_id`),
  KEY `idx_is_read` (`is_read`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 31. shops ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `shops` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_id`    INT UNSIGNED NOT NULL,
  `name`        VARCHAR(191) NOT NULL,
  `description` TEXT         NULL,
  `city`        VARCHAR(120) NULL,
  `address`     TEXT         NULL,
  `phone`       VARCHAR(30)  NULL,
  `email`       VARCHAR(191) NULL,
  `logo_url`    VARCHAR(500) NULL,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_owner`    (`owner_id`),
  KEY `idx_is_active`(`is_active`),
  CONSTRAINT `fk_shop_owner` FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 32. shop_members ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `shop_members` (
  `id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_shop_user` (`shop_id`, `user_id`),
  CONSTRAINT `fk_sm_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_sm_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 33. customer_payments (PayMongo checkout for customer product orders) ─
CREATE TABLE IF NOT EXISTS `customer_payments` (
  `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `customer_id`  INT UNSIGNED  NOT NULL,
  `reference`    VARCHAR(100)  NOT NULL,
  `paymongo_id`  VARCHAR(100)  NULL,
  `amount`       DECIMAL(10,2) NOT NULL,
  `currency`     CHAR(3)       NOT NULL DEFAULT 'PHP',
  `status`       ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `checkout_url` VARCHAR(1000) NULL,
  `cart_snapshot`JSON          NULL COMMENT 'Cart items at time of checkout',
  `paid_at`      DATETIME      NULL,
  `created_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reference` (`reference`),
  KEY `idx_customer` (`customer_id`),
  CONSTRAINT `fk_cpay_customer` FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 34. wishlist ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_product` (`user_id`, `product_id`),
  CONSTRAINT `fk_wl_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)             ON DELETE CASCADE,
  CONSTRAINT `fk_wl_product` FOREIGN KEY (`product_id`) REFERENCES `supplier_products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 35. vouchers ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `code`            VARCHAR(50)   NOT NULL,
  `discount_type`   ENUM('fixed','percent') NOT NULL DEFAULT 'fixed',
  `discount_value`  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `min_order`       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_uses`        INT UNSIGNED  NOT NULL DEFAULT 1,
  `used_count`      INT UNSIGNED  NOT NULL DEFAULT 0,
  `valid_from`      DATETIME      NULL,
  `valid_until`     DATETIME      NULL,
  `is_active`       TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  DEFAULT ADMIN USER
--  Password: Admin@1234  (bcrypt cost 12)
--  ⚠ Change this password immediately after first login!
-- ============================================================
INSERT IGNORE INTO `users`
  (`first_name`, `last_name`, `email`, `password_hash`, `role`, `is_verified`, `is_active`)
VALUES
  ('Fix', 'Admin',
   'admin@fixandgo.com',
   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'admin', 1, 1);

-- ============================================================
--  RE-ENABLE FOREIGN KEY CHECKS
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  VERIFICATION QUERY — run after import to confirm all tables
-- ============================================================
SELECT table_name, table_rows
FROM information_schema.tables
WHERE table_schema = DATABASE()
ORDER BY table_name;

-- ============================================================
--  TABLE SUMMARY (35 tables)
-- ============================================================
-- 01. users                       — all roles (admin, customer, supplier…)
-- 02. otp_tokens                  — email OTP codes
-- 03. rate_limits                 — IP-based rate limiting
-- 04. remember_tokens             — "Remember Me" cookies
-- 05. user_activity_logs          — login/logout audit trail
-- 06. seller_applications         — become supplier/owner/tech applications
-- 07. document_approvals          — per-document review status
-- 08. supplier_products           — core product/inventory table
-- 09. product_submissions         — batch product submissions supplier→owner
-- 10. submission_items            — line items in a submission batch
-- 11. product_transfers           — owner→supervisor, supervisor→sales
-- 12. product_transfer_history    — immutable audit of all movements
-- 13. owner_payments              — owner PayMongo checkout sessions
-- 14. owner_inventory             — owner purchase records
-- 15. customer_orders             — customer product orders
-- 16. product_reviews             — customer product ratings
-- 17. bookings                    — repair service bookings
-- 18. repair_payments             — customer PayMongo repair payments
-- 19. technician_profiles         — extended technician profile
-- 20. technician_credentials      — technician docs/photos/videos
-- 21. technician_reviews          — post-repair customer reviews
-- 22. sales_products              — sales person direct product listings
-- 23. supply_requests             — sales person → supervisor supply requests
-- 24. supervisor_reports          — monthly inventory reports
-- 25. technician_orders           — technician buying from supplier/owner
-- 26. technician_order_items      — line items in technician orders
-- 27. technician_supply_requests  — technician supply requests to supplier
-- 28. conversations               — 1-to-1 message threads
-- 29. messages                    — individual chat messages
-- 30. notifications               — in-app notification bell
-- 31. shops                       — owner shop profiles
-- 32. shop_members                — supervisor/sales person → shop mapping
-- 33. customer_payments           — customer PayMongo product checkout
-- 34. wishlist                    — customer wishlist items
-- 35. vouchers                    — discount voucher codes
-- ============================================================
