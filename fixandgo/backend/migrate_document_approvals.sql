-- ============================================================
--  Fix&Go — Document Approval System Migration
--  Adds individual document approval tracking
-- ============================================================

USE fixandgo;

-- Create document_approvals table
CREATE TABLE IF NOT EXISTS document_approvals (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  application_id INT UNSIGNED NOT NULL,
  document_type ENUM('gov_id', 'bir', 'dti', 'bank') NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  rejection_reason TEXT NULL,
  reviewed_by INT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  UNIQUE KEY unique_app_doc (application_id, document_type),
  INDEX idx_application (application_id),
  INDEX idx_status (status),
  
  CONSTRAINT fk_doc_approval_application
    FOREIGN KEY (application_id) REFERENCES seller_applications(id) ON DELETE CASCADE,
  CONSTRAINT fk_doc_approval_reviewer
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add overall_status column to seller_applications to track if all docs are approved
ALTER TABLE seller_applications 
  ADD COLUMN overall_status ENUM('pending', 'docs_approved', 'approved', 'rejected') 
  NOT NULL DEFAULT 'pending' AFTER status;

SELECT 'Migration completed successfully!' AS message;
