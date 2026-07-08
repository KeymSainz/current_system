-- ============================================================
--  Fix&Go — Marketplace Test Data Seed
--  Run in phpMyAdmin: Import → select this file → Go
--  Safe to run multiple times (uses INSERT IGNORE / ON DUPLICATE KEY)
-- ============================================================

USE fixandgo;

-- ============================================================
-- STEP 1: Update existing users with marketplace profile data
-- ============================================================

-- Hakim Supplier (id=94) — give shop name, bio, specializations
UPDATE users SET
    shop_name      = 'Hakim Mobile Parts Supply',
    bio            = 'Trusted supplier of genuine and high-quality mobile phone parts and accessories. Serving Fix&Go shops since 2020.',
    specializations = 'iPhone Parts, Samsung Parts, Chargers, Batteries, Tempered Glass',
    profile_image  = 'https://ui-avatars.com/api/?name=Hakim+Supplier&background=e6a800&color=fff&size=200&bold=true'
WHERE id = 94;

-- Hakim SalesPerson (id=98) — give shop name, bio, specializations
UPDATE users SET
    shop_name      = 'QuickFix Mobile Repair',
    bio            = 'Expert mobile phone technician with 5+ years of experience. Specializing in iPhone and Samsung repairs. Fast, reliable, and affordable service.',
    specializations = 'Screen Repair, Battery Replacement, Water Damage, Charging Port Fix, Software Issues',
    profile_image  = 'https://ui-avatars.com/api/?name=Hakim+SalesPerson&background=3b82f6&color=fff&size=200&bold=true'
WHERE id = 98;

-- ============================================================
-- STEP 2: Add technician_profiles entries for sales persons
-- ============================================================

INSERT INTO technician_profiles (user_id, specialization, experience_years, bio, availability, rating_avg, rating_count)
VALUES
  (98, 'Screen Repair, Battery Replacement, Water Damage, Charging Port Fix', 5,
   'Expert mobile phone technician with 5+ years of experience. Fast, reliable, and affordable service.',
   'available', 4.8, 24)
ON DUPLICATE KEY UPDATE
  specialization   = VALUES(specialization),
  experience_years = VALUES(experience_years),
  bio              = VALUES(bio),
  rating_avg       = VALUES(rating_avg),
  rating_count     = VALUES(rating_count);

-- ============================================================
-- STEP 3: Add more supplier_products with stock (owner_received)
-- ============================================================

-- Products from Hakim Supplier (id=94) — status owner_received so they show in marketplace
INSERT INTO supplier_products
    (supplier_id, category, brand, item_description, qty, srp, status, image_path, is_displayed, created_at)
VALUES
-- iPhone accessories
(94, 'LCD / Screen',   'Apple',    'iPhone 15 Pro Max OLED Screen Assembly',          15, 8500.00, 'owner_received', NULL, 1, NOW()),
(94, 'LCD / Screen',   'Apple',    'iPhone 14 / 14 Plus LCD Screen Replacement',      20, 6500.00, 'owner_received', NULL, 1, NOW()),
(94, 'LCD / Screen',   'Apple',    'iPhone 13 / 13 Mini OLED Display',                18, 5800.00, 'owner_received', NULL, 1, NOW()),
(94, 'Battery',        'Apple',    'iPhone 15 Series 3279mAh Replacement Battery',    30, 1200.00, 'owner_received', NULL, 1, NOW()),
(94, 'Battery',        'Apple',    'iPhone 14 Pro 3200mAh High Capacity Battery',     25, 1100.00, 'owner_received', NULL, 1, NOW()),
(94, 'Battery',        'Apple',    'iPhone 13 3227mAh OEM Battery',                   40, 950.00,  'owner_received', NULL, 1, NOW()),
(94, 'Tempered Glass', 'Apple',    'iPhone 15 Pro Max 9H Tempered Glass (2-pack)',     50, 299.00,  'owner_received', NULL, 1, NOW()),
(94, 'Tempered Glass', 'Apple',    'iPhone 14 / 14 Plus Privacy Tempered Glass',      45, 350.00,  'owner_received', NULL, 1, NOW()),
(94, 'Charger',        'Apple',    'iPhone 20W USB-C Fast Charger + Cable',            35, 650.00,  'owner_received', NULL, 1, NOW()),
(94, 'Back Cover',     'Apple',    'iPhone 15 Pro Titanium Back Glass Replacement',   12, 2800.00, 'owner_received', NULL, 1, NOW()),

-- Samsung accessories
(94, 'LCD / Screen',   'Samsung',  'Samsung Galaxy S24 Ultra AMOLED Display',         10, 9200.00, 'owner_received', NULL, 1, NOW()),
(94, 'LCD / Screen',   'Samsung',  'Samsung Galaxy A54 Super AMOLED Screen',          22, 3800.00, 'owner_received', NULL, 1, NOW()),
(94, 'LCD / Screen',   'Samsung',  'Samsung Galaxy A34 LCD Screen Assembly',          28, 2900.00, 'owner_received', NULL, 1, NOW()),
(94, 'Battery',        'Samsung',  'Samsung Galaxy S24 4000mAh Battery',              20, 1350.00, 'owner_received', NULL, 1, NOW()),
(94, 'Battery',        'Samsung',  'Samsung Galaxy A54 5000mAh Battery',              35, 980.00,  'owner_received', NULL, 1, NOW()),
(94, 'Tempered Glass', 'Samsung',  'Samsung Galaxy S24 Ultra Curved Tempered Glass',  40, 420.00,  'owner_received', NULL, 1, NOW()),
(94, 'Charger',        'Samsung',  'Samsung 45W Super Fast Charger USB-C',            30, 750.00,  'owner_received', NULL, 1, NOW()),
(94, 'Back Cover',     'Samsung',  'Samsung Galaxy S24 Back Glass Panel',             15, 1800.00, 'owner_received', NULL, 1, NOW()),

-- Xiaomi / Redmi
(94, 'LCD / Screen',   'Xiaomi',   'Xiaomi 14 Pro AMOLED Display Assembly',           8,  7500.00, 'owner_received', NULL, 1, NOW()),
(94, 'LCD / Screen',   'Xiaomi',   'Redmi Note 13 Pro LCD Screen',                    25, 2200.00, 'owner_received', NULL, 1, NOW()),
(94, 'Battery',        'Xiaomi',   'Xiaomi 14 4610mAh Battery',                       20, 890.00,  'owner_received', NULL, 1, NOW()),
(94, 'Tempered Glass', 'Xiaomi',   'Redmi Note 13 Series Tempered Glass (3-pack)',    60, 199.00,  'owner_received', NULL, 1, NOW()),
(94, 'Charger',        'Xiaomi',   'Xiaomi 67W Turbo Charger + Type-C Cable',         25, 580.00,  'owner_received', NULL, 1, NOW()),

-- Universal / Accessories
(94, 'Earphones',      'Generic',  'TWS Bluetooth 5.3 Earbuds with Charging Case',    50, 450.00,  'owner_received', NULL, 1, NOW()),
(94, 'Earphones',      'Generic',  'Wired Type-C Earphones Hi-Fi Sound',              80, 180.00,  'owner_received', NULL, 1, NOW()),
(94, 'Charger',        'Generic',  'Universal 65W GaN Charger 3-Port USB-C/A',        20, 850.00,  'owner_received', NULL, 1, NOW()),
(94, 'Charger',        'Generic',  'Magnetic Wireless Charger 15W MagSafe Compatible',30, 480.00,  'owner_received', NULL, 1, NOW()),
(94, 'Tempered Glass', 'Generic',  'Universal Anti-Blue Light Screen Protector',      100,149.00,  'owner_received', NULL, 1, NOW()),
(94, 'Back Cover',     'Generic',  'Shockproof Military Grade Phone Case (Universal)',70, 250.00,  'owner_received', NULL, 1, NOW()),
(94, 'Tools',          'Generic',  'Professional Phone Repair Tool Kit (32-piece)',   15, 1200.00, 'owner_received', NULL, 1, NOW()),
(94, 'Tools',          'Generic',  'Precision Screwdriver Set for Mobile Repair',     20, 650.00,  'owner_received', NULL, 1, NOW());

-- ============================================================
-- STEP 4: Add sales_products for the sales person (id=98)
-- ============================================================

INSERT INTO sales_products
    (sales_person_id, name, description, category, price, stock, image_path, is_active, created_at)
VALUES
(98, 'iPhone Screen Repair Service',
     'Professional iPhone screen replacement. All models supported. Genuine parts used. 30-day warranty.',
     'Screen Repair', 1500.00, 50, NULL, 1, NOW()),
(98, 'Samsung Screen Repair Service',
     'Expert Samsung AMOLED/LCD screen replacement. Same-day service available. 30-day warranty.',
     'Screen Repair', 1200.00, 50, NULL, 1, NOW()),
(98, 'Battery Replacement Service',
     'Fast battery replacement for all phone brands. OEM quality batteries. 90-day warranty.',
     'Battery Replacement', 800.00, 100, NULL, 1, NOW()),
(98, 'Water Damage Repair',
     'Complete water damage assessment and repair. Ultrasonic cleaning + component replacement.',
     'Water Damage', 2500.00, 20, NULL, 1, NOW()),
(98, 'Charging Port Repair',
     'Fix loose or broken charging ports. Type-C, Lightning, and Micro-USB supported.',
     'Charging Port', 600.00, 80, NULL, 1, NOW()),
(98, 'Software Troubleshooting',
     'Fix software issues, factory reset, OS update, virus removal, and data recovery.',
     'Software', 500.00, 100, NULL, 1, NOW());

-- ============================================================
-- STEP 5: Add a second supplier user for variety
-- ============================================================

-- Only insert if email doesn't exist
INSERT IGNORE INTO users
    (first_name, last_name, email, phone, password_hash, role, is_verified, is_active,
     shop_name, bio, specializations, profile_image, created_at)
VALUES
(
  'Maria', 'Santos',
  'maria.supplier@fixandgo.test',
  '09171234567',
  '$2y$12$dummyhashformariaXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
  'supplier', 1, 1,
  'Santos Mobile Accessories Hub',
  'Premium mobile accessories supplier. We carry the latest and most durable phone parts for all major brands.',
  'Samsung Parts, Oppo Parts, Vivo Parts, Accessories',
  'https://ui-avatars.com/api/?name=Maria+Santos&background=28a745&color=fff&size=200&bold=true',
  NOW()
);

-- ============================================================
-- STEP 6: Add a second sales person for variety
-- ============================================================

INSERT IGNORE INTO users
    (first_name, last_name, email, phone, password_hash, role, is_verified, is_active,
     shop_name, bio, specializations, profile_image, created_at)
VALUES
(
  'Juan', 'Dela Cruz',
  'juan.tech@fixandgo.test',
  '09281234567',
  '$2y$12$dummyhashforjuanXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
  'sales_person', 1, 1,
  'JDC Phone Repair Center',
  'Certified mobile technician with 8 years of experience. Specializing in Oppo, Vivo, and Realme repairs. Walk-in and home service available.',
  'Oppo Repair, Vivo Repair, Realme Repair, Data Recovery, IMEI Repair',
  'https://ui-avatars.com/api/?name=Juan+Dela+Cruz&background=6366f1&color=fff&size=200&bold=true',
  NOW()
);

-- ============================================================
-- STEP 7: Add products for Maria Santos (get her ID dynamically)
-- ============================================================

SET @maria_id = (SELECT id FROM users WHERE email = 'maria.supplier@fixandgo.test' LIMIT 1);

INSERT INTO supplier_products
    (supplier_id, category, brand, item_description, qty, srp, status, image_path, is_displayed, created_at)
SELECT @maria_id, category, brand, item_description, qty, srp, 'owner_received', NULL, 1, NOW()
FROM (
  SELECT 'LCD / Screen'   AS category, 'Oppo'    AS brand, 'Oppo Reno 11 Pro AMOLED Display'              AS item_description, 14  AS qty, 4200.00 AS srp
  UNION ALL
  SELECT 'LCD / Screen',   'Vivo',    'Vivo V30 Pro AMOLED Screen Assembly',                                                    18,      3900.00
  UNION ALL
  SELECT 'LCD / Screen',   'Realme',  'Realme 12 Pro+ LCD Screen',                                                              22,      2800.00
  UNION ALL
  SELECT 'Battery',        'Oppo',    'Oppo Reno 11 4800mAh Battery',                                                          30,       950.00
  UNION ALL
  SELECT 'Battery',        'Vivo',    'Vivo V30 4600mAh Battery',                                                              28,       880.00
  UNION ALL
  SELECT 'Battery',        'Realme',  'Realme 12 Pro 5000mAh Battery',                                                         35,       820.00
  UNION ALL
  SELECT 'Tempered Glass', 'Oppo',    'Oppo Reno 11 Series 9H Tempered Glass',                                                 55,       280.00
  UNION ALL
  SELECT 'Tempered Glass', 'Vivo',    'Vivo V30 Anti-Fingerprint Tempered Glass',                                              50,       260.00
  UNION ALL
  SELECT 'Charger',        'Oppo',    'Oppo 80W SuperVOOC Charger + Cable',                                                    20,       750.00
  UNION ALL
  SELECT 'Charger',        'Vivo',    'Vivo 44W FlashCharge Adapter',                                                          22,       680.00
  UNION ALL
  SELECT 'Back Cover',     'Oppo',    'Oppo Reno 11 Pro Back Glass Replacement',                                               10,      1600.00
  UNION ALL
  SELECT 'Earphones',      'Generic', 'Bluetooth 5.3 Neckband Earphones IPX5 Waterproof',                                     40,       380.00
  UNION ALL
  SELECT 'Tools',          'Generic', 'LCD Separator Machine for Phone Screen Repair',                                          5,      4500.00
) AS t
WHERE @maria_id IS NOT NULL;

-- ============================================================
-- STEP 8: Add technician_profiles for Juan Dela Cruz
-- ============================================================

SET @juan_id = (SELECT id FROM users WHERE email = 'juan.tech@fixandgo.test' LIMIT 1);

INSERT INTO technician_profiles (user_id, specialization, experience_years, bio, availability, rating_avg, rating_count)
SELECT @juan_id,
  'Oppo Repair, Vivo Repair, Realme Repair, Data Recovery',
  8,
  'Certified mobile technician with 8 years of experience. Walk-in and home service available.',
  'available', 4.9, 41
WHERE @juan_id IS NOT NULL
ON DUPLICATE KEY UPDATE
  specialization   = VALUES(specialization),
  experience_years = VALUES(experience_years),
  rating_avg       = VALUES(rating_avg),
  rating_count     = VALUES(rating_count);

-- ============================================================
-- STEP 9: Add shops for the owner
-- ============================================================

INSERT IGNORE INTO shops (owner_id, name, description, address, city, phone, email, is_active, created_at)
SELECT id,
  'Fix&Go Main Branch',
  'Your trusted phone repair and accessories shop. Fast, reliable, and affordable service.',
  '123 Mahatma Gandhi St., Brgy. Poblacion',
  'Makati City',
  '09171234567',
  'fixandgo.main@gmail.com',
  1, NOW()
FROM users WHERE role = 'owner' AND is_active = 1 LIMIT 1;

-- ============================================================
-- DONE
-- ============================================================

SELECT CONCAT('supplier_products total: ', COUNT(*)) AS status FROM supplier_products;
SELECT CONCAT('technician_profiles total: ', COUNT(*)) AS status FROM technician_profiles;
SELECT CONCAT('sales_products total: ', COUNT(*)) AS status FROM sales_products;
SELECT CONCAT('shops total: ', COUNT(*)) AS status FROM shops;
