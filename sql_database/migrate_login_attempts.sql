-- ============================================================
--  Fix&Go — Login Attempt Tracking
--  Adds per-account lockout columns to the users table
-- ============================================================

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS login_attempts  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS locked_until    DATETIME         NULL DEFAULT NULL;

-- Index for fast lockout checks
CREATE INDEX IF NOT EXISTS idx_users_locked ON users (locked_until);
