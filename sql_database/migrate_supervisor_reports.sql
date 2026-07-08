-- ============================================================
--  Fix&Go — Supervisor Reports Table Migration
--  Creates table to store supervisor reports sent to owner
-- ============================================================

USE fixandgo;

CREATE TABLE IF NOT EXISTS supervisor_reports (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  supervisor_id   INT UNSIGNED    NOT NULL,
  owner_id        INT UNSIGNED    NOT NULL,
  report_year     INT             NOT NULL,
  report_month    INT             NOT NULL,
  total_products  INT             NOT NULL DEFAULT 0,
  total_quantity  INT             NOT NULL DEFAULT 0,
  total_value     DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  report_data     JSON            NULL COMMENT 'Full report data including products',
  sent_at         DATETIME        NULL,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                           ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_supervisor (supervisor_id),
  INDEX idx_owner (owner_id),
  INDEX idx_period (report_year, report_month),
  UNIQUE KEY uq_supervisor_period (supervisor_id, owner_id, report_year, report_month),
  
  CONSTRAINT fk_sr_supervisor
    FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_sr_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'supervisor_reports table created successfully!' AS status;
