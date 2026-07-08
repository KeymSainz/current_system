-- ============================================================
--  Fix&Go — Technician Credentials & Documents Table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

CREATE TABLE IF NOT EXISTS technician_credentials (
  id              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  technician_id   INT UNSIGNED   NOT NULL,
  doc_type        VARCHAR(30)    NOT NULL COMMENT 'gov_id | bir | tech_cert | bank | dti | skill_cert | nstp | tesda | custom',
  label           VARCHAR(120)   NOT NULL COMMENT 'Display label shown to customers',
  file_url        VARCHAR(512)   NOT NULL COMMENT 'Relative path to uploaded file',
  file_name       VARCHAR(255)   NOT NULL COMMENT 'Original file name',
  file_ext        VARCHAR(10)    NOT NULL COMMENT 'pdf | jpg | png | etc.',
  is_image        TINYINT(1)     NOT NULL DEFAULT 0,
  display_order   TINYINT        NOT NULL DEFAULT 0,
  created_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_tech  (technician_id),
  INDEX idx_type  (doc_type),

  CONSTRAINT fk_tc_tech
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'technician_credentials table created.' AS status;
DESCRIBE technician_credentials;
