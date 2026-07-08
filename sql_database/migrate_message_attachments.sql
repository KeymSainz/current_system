-- ============================================================
--  Fix&Go — Add file attachment support to messages table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- Add attachment columns to messages table
ALTER TABLE messages
  ADD COLUMN IF NOT EXISTS file_url   VARCHAR(512) NULL DEFAULT NULL COMMENT 'Relative path to uploaded file',
  ADD COLUMN IF NOT EXISTS file_type  VARCHAR(20)  NULL DEFAULT NULL COMMENT 'image or video',
  ADD COLUMN IF NOT EXISTS file_name  VARCHAR(255) NULL DEFAULT NULL COMMENT 'Original filename';

-- Allow body to be empty (for media-only messages)
ALTER TABLE messages MODIFY COLUMN body TEXT NULL DEFAULT NULL;

-- Create uploads directory reference index
ALTER TABLE messages ADD INDEX IF NOT EXISTS idx_file_type (file_type);

SELECT 'Message attachment columns added.' AS status;
DESCRIBE messages;
