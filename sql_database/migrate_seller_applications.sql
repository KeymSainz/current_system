-- ============================================================
-- Fix&Go — Seller Applications Table
-- Stores full application data submitted from Seller Centre
-- ============================================================

USE fixandgo;

CREATE TABLE IF NOT EXISTS seller_applications (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  user_id          INT UNSIGNED   NOT NULL,          -- the customer who applied
  role             ENUM('supplier','owner') NOT NULL,
  first_name       VARCHAR(50)    NOT NULL,
  last_name        VARCHAR(50)    NOT NULL,
  email            VARCHAR(255)   NOT NULL,          -- seller email (different from customer)
  phone            VARCHAR(20)    NOT NULL,
  company_name     VARCHAR(150)   NOT NULL,
  shop_name        VARCHAR(150)   NULL,              -- owner only
  -- Document paths (stored in uploads/applications/)
  doc_gov_id       VARCHAR(500)   NULL,
  doc_bir          VARCHAR(500)   NULL,
  doc_dti          VARCHAR(500)   NULL,              -- owner only
  doc_bank         VARCHAR(500)   NULL,
  status           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  admin_notes      TEXT           NULL,
  reviewed_by      INT UNSIGNED   NULL,
  reviewed_at      DATETIME       NULL,
  submitted_at     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user   (user_id),
  INDEX idx_status (status),
  INDEX idx_role   (role),
  CONSTRAINT fk_sa_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
