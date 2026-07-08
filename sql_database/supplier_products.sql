-- ============================================================
--  Fix&Go — Supplier Products Schema
--  Tables: supplier_products, product_submissions, submission_items
-- ============================================================

-- ── 1. SUPPLIER PRODUCTS ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS supplier_products (
  id               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  supplier_id      INT UNSIGNED     NOT NULL,
  category         VARCHAR(100)     NOT NULL,
  brand            VARCHAR(100)     NOT NULL DEFAULT '',
  item_description VARCHAR(500)     NOT NULL,
  qty              INT UNSIGNED     NOT NULL DEFAULT 0,
  srp              DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
  image_path       VARCHAR(500)     NULL,                   -- stored file path e.g. uploads/products/abc.jpg
  status           ENUM(
                     'draft',
                     'verified',
                     'sent_to_owner',
                     'owner_received',
                     'rejected'
                   )                NOT NULL DEFAULT 'draft',
  notes            TEXT             NULL,
  verified_at      DATETIME         NULL,
  sent_at          DATETIME         NULL,
  created_at       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_supplier  (supplier_id),
  INDEX idx_status    (status),
  INDEX idx_category  (category),
  CONSTRAINT fk_sp_supplier
    FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── ALTER: add image_path if table already exists ────────────
-- Run this if you already created the table without image_path:
-- ALTER TABLE supplier_products ADD COLUMN image_path VARCHAR(500) NULL AFTER srp;

-- ── 2. PRODUCT SUBMISSIONS LOG ───────────────────────────────
CREATE TABLE IF NOT EXISTS product_submissions (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  supplier_id     INT UNSIGNED  NOT NULL,
  owner_id        INT UNSIGNED  NULL,
  submitted_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status          ENUM('pending','acknowledged','rejected') NOT NULL DEFAULT 'pending',
  owner_notes     TEXT          NULL,
  acknowledged_at DATETIME      NULL,

  PRIMARY KEY (id),
  INDEX idx_sub_supplier (supplier_id),
  INDEX idx_sub_owner    (owner_id),
  CONSTRAINT fk_sub_supplier
    FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_sub_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 3. SUBMISSION ITEMS ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS submission_items (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  submission_id INT UNSIGNED NOT NULL,
  product_id    INT UNSIGNED NOT NULL,

  PRIMARY KEY (id),
  CONSTRAINT fk_si_submission
    FOREIGN KEY (submission_id) REFERENCES product_submissions(id) ON DELETE CASCADE,
  CONSTRAINT fk_si_product
    FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
