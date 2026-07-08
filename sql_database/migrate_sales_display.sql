-- ============================================================
-- Fix&Go — Sales Person Display Flag Migration
-- Adds is_displayed column so sales person can choose which
-- products from their inventory to show to customers
-- ============================================================

USE fixandgo;

ALTER TABLE supplier_products
  ADD COLUMN IF NOT EXISTS is_displayed TINYINT(1) NOT NULL DEFAULT 0
  COMMENT '1 = sales person chose to display this to customers';

CREATE INDEX IF NOT EXISTS idx_sp_displayed ON supplier_products(is_displayed);
