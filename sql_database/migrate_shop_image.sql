-- Fix&Go — Add shop_image to users and phone_photo to bookings
USE fixandgo;

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS shop_image VARCHAR(500) NULL AFTER profile_image;

ALTER TABLE bookings
  ADD COLUMN IF NOT EXISTS phone_photo VARCHAR(500) NULL AFTER expected_fix;

SELECT 'Shop image + phone photo migration complete.' AS status;
