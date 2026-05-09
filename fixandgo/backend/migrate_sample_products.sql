-- ============================================================
--  Fix&Go — Migration: Sample Shop Products (v3 — local images)
--  Clears old sample products and re-inserts with local image paths.
--
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- ── Step 1: Ensure image_path column exists ───────────────────────────────
ALTER TABLE supplier_products
  MODIFY COLUMN image_path VARCHAR(500) NULL;

-- ── Step 2: Insert sample supplier user ──────────────────────────────────
INSERT IGNORE INTO users
  (first_name, last_name, email, password_hash, role, is_verified, is_active)
VALUES
  ('Sample', 'Supplier', 'supplier@fixandgo.com',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'supplier', 1, 1);

-- ── Step 3: Remove old sample products (clean re-run) ────────────────────
DELETE FROM supplier_products
WHERE supplier_id = (SELECT id FROM users WHERE email = 'supplier@fixandgo.com' LIMIT 1);

-- ── Step 4: Insert 20 products with local image paths ────────────────────

-- CHARGERS
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Charger', 'Apple',
  '20W USB-C Power Adapter with Lightning Cable — iPhone 8 and above',
  50, 599.00, 'uploads/products/charger_apple.jpg', 'owner_received',
  'Includes 1m USB-C to Lightning cable. Fast charging compatible.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Charger', 'Anker',
  'Anker 65W GaN USB-C Wall Charger — 3-Port Fast Charging Adapter',
  45, 1299.00, 'uploads/products/charger_anker.jpg', 'owner_received',
  'Charges laptop, phone, and tablet simultaneously. Foldable plug.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- CABLES
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Cable', 'Baseus',
  'Baseus 100W USB-C to USB-C Braided Cable 2m — Black',
  120, 349.00, 'uploads/products/cable_usbc.jpg', 'owner_received',
  'Nylon braided. Supports 100W fast charging and 480Mbps data transfer.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Cable', 'Ugreen',
  'Ugreen MFi Lightning to USB-A Cable 1.5m — White',
  90, 299.00, 'uploads/products/cable_lightning.jpg', 'owner_received',
  'Apple MFi certified. Compatible with all iPhone and iPad models.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- SCREENS
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Screen', 'OPPO',
  'OPPO A5s / A12 LCD Display + Touch Screen Digitizer Assembly — Black',
  20, 1200.00, 'uploads/products/screen_oppo.jpg', 'owner_received',
  'OEM quality replacement LCD. Includes adhesive and tools.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Screen', 'Samsung',
  'Samsung Galaxy A32 AMOLED Display + Digitizer Assembly — Black',
  18, 1800.00, 'uploads/products/screen_samsung.jpg', 'owner_received',
  'Original AMOLED panel. Pre-installed adhesive. Includes flex cable.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- BATTERIES
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Battery', 'Samsung',
  'Samsung Galaxy S8 Replacement Li-ion Battery 3000mAh — EB-BG950ABA',
  35, 450.00, 'uploads/products/battery_samsung.jpg', 'owner_received',
  '3.85V nominal voltage. Includes installation guide.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Battery', 'Apple',
  'Apple iPhone 13 Replacement Battery 3227mAh — A2656',
  28, 799.00, 'uploads/products/battery_apple.jpg', 'owner_received',
  'OEM-grade capacity. Includes adhesive strips and pentalobe screwdriver.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- TEMPERED GLASS
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Tempered Glass', 'Spigen',
  'Spigen GLAStR SLIM HD Tempered Glass — iPhone 12 / 12 Pro',
  100, 299.00, 'uploads/products/glass_spigen.jpg', 'owner_received',
  '9H hardness, 0.3mm ultra-thin. Case-friendly design. Pack of 1.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Tempered Glass', 'Nillkin',
  'Nillkin Amazing H+ Pro Tempered Glass — Samsung Galaxy A54 5G',
  75, 199.00, 'uploads/products/glass_nillkin.jpg', 'owner_received',
  '0.2mm ultra-thin. Anti-explosion edge. Full coverage.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- CASES
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Case', 'Spigen',
  'Spigen Tough Armor Case — Samsung Galaxy S23 Ultra — Gunmetal',
  80, 699.00, 'uploads/products/case_spigen.jpg', 'owner_received',
  'Military-grade drop protection. Dual-layer design with kickstand.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Case', 'OtterBox',
  'OtterBox Commuter Series Case — iPhone 14 Pro — Black',
  60, 899.00, 'uploads/products/case_otterbox.jpg', 'owner_received',
  'Slim two-piece design. Port covers keep out dust and debris.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- EARPHONES
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Earphones', 'Samsung',
  'Samsung Galaxy Buds2 Pro True Wireless Earbuds — Graphite',
  25, 7999.00, 'uploads/products/earphones_samsung.jpg', 'owner_received',
  'Active noise cancellation. 360° audio. IPX7 water resistant.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Earphones', 'Apple',
  'Apple AirPods Pro (2nd Gen) with MagSafe Charging Case',
  15, 14999.00, 'uploads/products/earphones_apple.jpg', 'owner_received',
  'Adaptive transparency. Personalized spatial audio. H2 chip.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- POWER BANKS
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Power Bank', 'Anker',
  'Anker PowerCore 20000mAh Portable Charger — Black',
  40, 1899.00, 'uploads/products/powerbank_anker.jpg', 'owner_received',
  'Charges iPhone 15 almost 5 times. Dual USB-A + USB-C output.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Power Bank', 'Baseus',
  'Baseus Blade 100W 20000mAh Power Bank — Slim Design — Black',
  30, 2499.00, 'uploads/products/powerbank_baseus.jpg', 'owner_received',
  'Ultra-slim 14.5mm body. Supports 100W PD fast charging for laptops.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- HOLDERS
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Holder', 'Baseus',
  'Baseus Magnetic Car Phone Holder — Dashboard Mount — Black',
  55, 399.00, 'uploads/products/holder_car.jpg', 'owner_received',
  'Strong N52 magnet. 360° rotation. Compatible with all phones.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Holder', 'Ugreen',
  'Ugreen Desk Phone Stand Adjustable Foldable Holder — Silver',
  65, 349.00, 'uploads/products/holder_desk.jpg', 'owner_received',
  'Aluminum alloy. Adjustable angle 0–100°. Fits phones 4–7.9 inches.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- CHARGING PADS
INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Charging Pad', 'Belkin',
  'Belkin BOOST↑CHARGE 15W Wireless Charging Pad — White',
  35, 1199.00, 'uploads/products/pad_belkin.jpg', 'owner_received',
  'Qi-certified. Compatible with iPhone, Samsung, and all Qi devices.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

INSERT INTO supplier_products (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes)
SELECT u.id, 'Charging Pad', 'Anker',
  'Anker 3-in-1 MagSafe Wireless Charging Station — White',
  20, 2999.00, 'uploads/products/pad_anker.jpg', 'owner_received',
  'Charges iPhone, AirPods, and Apple Watch simultaneously.'
FROM users u WHERE u.email = 'supplier@fixandgo.com' LIMIT 1;

-- ── Step 5: Verify ────────────────────────────────────────────────────────
SELECT sp.id, sp.category, sp.brand, sp.item_description, sp.qty, sp.srp, sp.image_path
FROM supplier_products sp
WHERE sp.status = 'owner_received'
ORDER BY sp.category, sp.brand;

SELECT CONCAT('Total products: ', COUNT(*)) AS result
FROM supplier_products WHERE status = 'owner_received';

SELECT 'Migration v3 complete — local images.' AS status;
