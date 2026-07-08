-- ============================================================
--  Fix&Go — Sample Products Seed
--  Run this in phpMyAdmin: select fixandgo database, then
--  click Import and upload this file.
--
--  supplier_id 94 = Hakim Supplier
--  current_holder_id 96 = Kim Owners (products visible in shop)
-- ============================================================
USE fixandgo;

INSERT INTO supplier_products
  (supplier_id, category, brand, item_description, qty, srp, image_path, status, current_holder_id, holder_type, notes, is_displayed)
VALUES
  (94, 'LCD / Screen',   'Apple Compatible', 'iPhone LCD Screen Replacement',       100, 2500.00, NULL, 'owner_received', 96, 'owner', 'High-quality LCD replacement for iPhone.',           1),
  (94, 'LCD / Screen',   'Samsung',          'Samsung AMOLED LCD Screen',            100, 4500.00, NULL, 'owner_received', 96, 'owner', 'Original-quality AMOLED display.',                  1),
  (94, 'Tempered Glass', 'Nillkin',          'Tempered Glass Screen Protector',      100,  500.00, NULL, 'owner_received', 96, 'owner', '9H tempered glass protection.',                     1),
  (94, 'Back Cover',     'Romoss',           'Fast Charging Power Bank 20000mAh',   100, 1800.00, NULL, 'owner_received', 96, 'owner', 'Fast charging dual USB power bank.',                1),
  (94, 'Earphones',      'Haylou',           'Wireless Bluetooth Earbuds',           100, 1500.00, NULL, 'owner_received', 96, 'owner', 'True wireless earbuds with noise reduction.',       1),
  (94, 'Battery',        'Apple Compatible', 'Original iPhone Battery Replacement',  100, 2200.00, NULL, 'owner_received', 96, 'owner', 'Replacement battery for iPhone.',                   1),
  (94, 'Battery',        'Samsung',          'Samsung Battery Replacement',          100, 1800.00, NULL, 'owner_received', 96, 'owner', 'Replacement battery for Samsung devices.',          1),
  (94, 'Charger',        'Anker',            'USB-C Fast Charger 65W',               100, 1700.00, NULL, 'owner_received', 96, 'owner', '65W fast charging adapter.',                        1),
  (94, 'Charger',        'Apple',            'MagSafe Wireless Charger',             100, 2500.00, NULL, 'owner_received', 96, 'owner', 'Magnetic wireless charger.',                        1),
  (94, 'Back Cover',     'Spigen',           'Phone Camera Lens Protector',          100,  600.00, NULL, 'owner_received', 96, 'owner', 'Scratch-resistant lens protection.',                1),
  (94, 'Back Cover',     'Black Shark',      'Gaming Phone Cooling Fan',             100, 1200.00, NULL, 'owner_received', 96, 'owner', 'Cooling accessory for gaming phones.',              1),
  (94, 'Back Cover',     'Xiaomi',           'Bluetooth Selfie Stick Tripod',        100,  850.00, NULL, 'owner_received', 96, 'owner', 'Tripod and selfie stick combo.',                    1),
  (94, 'Back Cover',     'Spigen',           'Premium Leather Phone Case',           100,  900.00, NULL, 'owner_received', 96, 'owner', 'Shockproof premium leather case.',                  1),
  (94, 'Tools',          'Jakemy',           'Mobile Phone Repair Toolkit',          100, 1100.00, NULL, 'owner_received', 96, 'owner', 'Professional repair toolkit.',                      1),
  (94, 'Charger',        'Apple',            'USB-C to Lightning Cable',             100,  700.00, NULL, 'owner_received', 96, 'owner', 'Charging and data cable.',                          1),
  (94, 'Back Cover',     'DJI',              'Smartphone Gimbal Stabilizer',         100, 5000.00, NULL, 'owner_received', 96, 'owner', '3-axis mobile stabilizer.',                         1),
  (94, 'Charger',        'Baseus',           'Wireless Car Charger Mount',           100, 1400.00, NULL, 'owner_received', 96, 'owner', 'Wireless charging car mount.',                      1),
  (94, 'Back Cover',     'SanDisk',          'Memory Card 256GB',                    100, 1600.00, NULL, 'owner_received', 96, 'owner', 'High-speed microSD card.',                          1),
  (94, 'Back Cover',     'UGREEN',           'USB OTG Adapter',                      100,  550.00, NULL, 'owner_received', 96, 'owner', 'OTG adapter for mobile devices.',                   1),
  (94, 'Earphones',      'JBL',              'Portable Bluetooth Speaker',           100, 3200.00, NULL, 'owner_received', 96, 'owner', 'Portable Bluetooth speaker.',                       1);

SELECT 'Seed complete — 20 products inserted.' AS status;
