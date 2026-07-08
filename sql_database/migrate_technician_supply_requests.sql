-- Fix&Go — Technician Supply Requests Migration
USE fixandgo;

CREATE TABLE IF NOT EXISTS technician_supply_requests (
  id                  INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  technician_id       INT UNSIGNED   NOT NULL,
  product_id          INT UNSIGNED   NOT NULL,
  supplier_id         INT UNSIGNED   NOT NULL,
  quantity_requested  INT            NOT NULL DEFAULT 1,
  note                TEXT           NULL,
  status              ENUM('pending','approved','rejected','fulfilled','cancelled') NOT NULL DEFAULT 'pending',
  supplier_notes      TEXT           NULL,
  created_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_technician (technician_id),
  INDEX idx_supplier   (supplier_id),
  INDEX idx_product    (product_id),
  INDEX idx_status     (status),

  CONSTRAINT fk_tsr_tech     FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tsr_supplier FOREIGN KEY (supplier_id)   REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tsr_product  FOREIGN KEY (product_id)    REFERENCES supplier_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Technician supply requests migration complete.' AS status;
