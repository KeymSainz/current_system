-- ============================================================
--  Fix&Go — Technician Inventory Seed (Hakim Technicians)
--
--  HOW TO USE:
--  1. Open phpMyAdmin → select "fixandgo" database
--  2. Click the "SQL" tab
--  3. Paste this entire file and click "Go"
--
--  BEFORE RUNNING: Verify Hakim's technician user ID by running:
--  SELECT id, first_name, last_name, email, role
--  FROM users
--  WHERE role = 'phone_technician'
--    AND first_name LIKE '%Hakim%';
--
--  Then replace the @hakim_id value below with the correct ID.
--  e.g. If Hakim's ID is 100: SET @hakim_id = 100;
--
--  supplier_id 94 = Hakim (as supplier/owner account)
-- ============================================================

USE fixandgo;

-- Auto-detect Hakim's technician user ID
SET @hakim_id = (
    SELECT id FROM users
    WHERE role = 'phone_technician'
      AND (first_name LIKE '%Hakim%' OR email LIKE '%hakim%')
    LIMIT 1
);

-- Safety check — will insert nothing if @hakim_id is NULL
-- Run: SELECT @hakim_id; to verify before inserting

INSERT INTO supplier_products
  (supplier_id, category, brand, item_description, qty, srp, image_path, status, current_holder_id, holder_type, notes, is_displayed)
VALUES

  -- ── LCD / Screens (4 items) ────────────────────────────────
  (94, 'LCD / Screen', 'Apple',
   'iPhone 12 LCD Screen Replacement — OEM Quality OLED',
   5, 4800.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   'High-quality OLED screen replacement for iPhone 12. Includes adhesive strips.', 1),

  (94, 'LCD / Screen', 'Samsung',
   'Samsung Galaxy A54 AMOLED LCD Assembly with Frame',
   4, 3500.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   'Original-quality AMOLED display assembly with frame for Galaxy A54.', 1),

  (94, 'LCD / Screen', 'Xiaomi',
   'Xiaomi Redmi Note 11 LCD Display + Touch Digitizer Assembly',
   6, 2200.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   'Full LCD + digitizer combo for Redmi Note 11. Compatible with all variants.', 1),

  (94, 'LCD / Screen', 'OPPO',
   'OPPO A57 LCD Screen Assembly with Outer Glass',
   4, 1800.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   'LCD screen with digitizer for OPPO A57. Plug-and-play replacement.', 1),

  -- ── Batteries (3 items) ────────────────────────────────────
  (94, 'Battery', 'Apple',
   'iPhone 11 High Capacity Replacement Battery 3110mAh',
   8, 1500.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   'Genuine-spec replacement battery for iPhone 11. Zero-cycle, fully tested.', 1),

  (94, 'Battery', 'Samsung',
   'Samsung Galaxy S21 Battery EB-BG991ABY 4000mAh',
   7, 1800.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   'OEM-grade replacement battery for Galaxy S21. Includes tools.', 1),

  (94, 'Battery', 'Vivo',
   'Vivo Y21 Replacement Battery B-R8 5000mAh',
   10, 1600.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   'High-capacity 5000mAh replacement battery for Vivo Y21.', 1),

  -- ── Screen Protectors (3 items) ────────────────────────────
  (94, 'Screen Protector', 'Nillkin',
   'Samsung Galaxy A54 9H Tempered Glass Screen Protector (Pack of 2)',
   20, 1500.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   '9H hardness anti-scratch tempered glass for Galaxy A54. Case-friendly edges.', 1),

  (94, 'Screen Protector', 'Baseus',
   'iPhone 12 / 12 Pro Privacy Anti-Spy Tempered Glass Protector',
   15, 1700.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   'Privacy screen protector blocks side-angle viewing. Oleophobic coating.', 1),

  (94, 'Screen Protector', 'Generic',
   'Universal Hydrogel Film — Flexible Screen Protector (All Models)',
   25, 2000.00, NULL, 'with_tech', @hakim_id, 'phone_technician',
   'Full-coverage hydrogel film. Self-healing, anti-fingerprint, fits all phone models.', 1);

-- ============================================================
--  Verify inserted records
-- ============================================================
SELECT
    sp.id,
    sp.category,
    sp.brand,
    sp.item_description,
    sp.qty,
    sp.srp,
    sp.status,
    sp.is_displayed,
    u.first_name,
    u.last_name,
    u.role
FROM supplier_products sp
JOIN users u ON u.id = sp.current_holder_id
WHERE sp.current_holder_id = @hakim_id
  AND sp.holder_type = 'phone_technician'
ORDER BY sp.id DESC
LIMIT 10;
