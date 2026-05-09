-- ============================================================
--  Fix&Go — Login / Logout Activity Log
--  Run this once to add the user_activity_logs table
-- ============================================================

CREATE TABLE IF NOT EXISTS user_activity_logs (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED    NOT NULL,
  action      ENUM('login','logout','session_expired','login_failed') NOT NULL,
  ip_address  VARCHAR(45)     NOT NULL DEFAULT '',
  user_agent  VARCHAR(512)    NOT NULL DEFAULT '',
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user_action  (user_id, action),
  INDEX idx_created_at   (created_at),
  CONSTRAINT fk_ual_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Also add last_login_at column to users table for quick lookup
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS last_login_at  DATETIME NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS last_logout_at DATETIME NULL DEFAULT NULL;
