-- ============================================================
-- Fix&Go — Document Approvals Table
-- Tracks per-document approval status for seller/technician applications
-- Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

CREATE TABLE IF NOT EXISTS document_approvals (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  application_id   INT UNSIGNED   NOT NULL,
  document_type    VARCHAR(30)    NOT NULL,   -- gov_id | cert | bir | dti | bank
  status           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  rejection_reason TEXT           NULL,
  reviewed_by      INT UNSIGNED   NULL,
  reviewed_at      DATETIME       NULL,
  created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_app_doc (application_id, document_type),
  INDEX idx_application (application_id),
  INDEX idx_status (status),

  CONSTRAINT fk_da_application
    FOREIGN KEY (application_id) REFERENCES seller_applications(id) ON DELETE CASCADE,
  CONSTRAINT fk_da_reviewer
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Also ensure seller_applications has overall_status column
ALTER TABLE seller_applications
  ADD COLUMN IF NOT EXISTS overall_status
    ENUM('pending','docs_approved','approved','rejected') NOT NULL DEFAULT 'pending'
    AFTER status;

SELECT 'Document approvals migration complete.' AS status;
