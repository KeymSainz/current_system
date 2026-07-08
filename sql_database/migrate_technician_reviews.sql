-- ============================================================
--  Fix&Go — Technician Reviews Table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

CREATE TABLE IF NOT EXISTS technician_reviews (
  id              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  booking_id      INT UNSIGNED   NOT NULL,
  technician_id   INT UNSIGNED   NOT NULL,
  customer_id     INT UNSIGNED   NOT NULL,
  rating          TINYINT        NOT NULL COMMENT '1–5 stars',
  comment         TEXT           NULL,
  media_1_url     VARCHAR(512)   NULL COMMENT 'Photo or video URL (proof of repair)',
  media_1_type    VARCHAR(10)    NULL COMMENT 'image or video',
  media_2_url     VARCHAR(512)   NULL,
  media_2_type    VARCHAR(10)    NULL,
  media_3_url     VARCHAR(512)   NULL,
  media_3_type    VARCHAR(10)    NULL,
  created_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE  KEY uq_booking_review (booking_id),
  INDEX   idx_technician (technician_id),
  INDEX   idx_customer   (customer_id),
  INDEX   idx_rating     (rating),

  CONSTRAINT fk_tr_booking
    FOREIGN KEY (booking_id)    REFERENCES bookings(id)  ON DELETE CASCADE,
  CONSTRAINT fk_tr_technician
    FOREIGN KEY (technician_id) REFERENCES users(id)     ON DELETE CASCADE,
  CONSTRAINT fk_tr_customer
    FOREIGN KEY (customer_id)   REFERENCES users(id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add constraint check
ALTER TABLE technician_reviews
  ADD CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5);

SELECT 'technician_reviews table created.' AS status;
DESCRIBE technician_reviews;
