-- ============================================================================
-- PIOS - Jeu de donnees de demonstration MySQL
-- A executer APRES : php artisan migrate --seed
-- Le script est idempotent : il actualise les donnees PIOS-DEMO existantes.
-- Toutes les dates sont relatives a la date d'execution pour alimenter le dashboard.
-- Compte par defaut : celinbell195@gmail.com / mot de passe : password
-- ============================================================================

SET NAMES utf8mb4;
SET @now = NOW();
SET @today = CURDATE();
START TRANSACTION;

-- Devise, pays, entreprise, boutique et utilisateur administrateur
INSERT INTO currencies (code, name, symbol, decimal_places, created_at, updated_at)
VALUES ('XOF', 'Franc CFA BCEAO', 'F CFA', 0, @now, @now)
ON DUPLICATE KEY UPDATE name = VALUES(name), symbol = VALUES(symbol), updated_at = @now;
SET @currency_id = (SELECT id FROM currencies WHERE code = 'XOF' LIMIT 1);

INSERT INTO countries (name, iso_code, currency_id, phone_prefix, tax_rules, payment_providers, created_at, updated_at)
VALUES ('Togo', 'TG', @currency_id, '+228', JSON_OBJECT(), JSON_ARRAY('tmoney', 'flooz'), @now, @now)
ON DUPLICATE KEY UPDATE currency_id = VALUES(currency_id), payment_providers = VALUES(payment_providers), updated_at = @now;
SET @country_id = (SELECT id FROM countries WHERE iso_code = 'TG' LIMIT 1);

INSERT INTO companies (name, legal_name, email, phone, address, country_id, currency_id, tax_id, is_active, created_at, updated_at)
SELECT 'PIOS Demo', 'PIOS Demo SARL', 'contact@pios-demo.tg', '+228 90 00 10 10', 'Boulevard du 13 Janvier, Lome', @country_id, @currency_id, 'TG-PIOS-2026', 1, @now, @now
WHERE NOT EXISTS (SELECT 1 FROM companies WHERE name = 'PIOS Demo' AND deleted_at IS NULL);
SET @company_id = (SELECT id FROM companies WHERE name = 'PIOS Demo' AND deleted_at IS NULL ORDER BY id LIMIT 1);

UPDATE companies SET legal_name = 'PIOS Demo SARL', email = 'contact@pios-demo.tg', phone = '+228 90 00 10 10',
    address = 'Boulevard du 13 Janvier, Lome', country_id = @country_id, currency_id = @currency_id,
    tax_id = 'TG-PIOS-2026', is_active = 1, updated_at = @now
WHERE id = @company_id;

INSERT INTO shops (company_id, name, code, address, city, phone, is_active, created_at, updated_at)
VALUES (@company_id, 'Boutique principale', 'LOM-001', 'Boulevard du 13 Janvier', 'Lome', '+228 90 00 10 10', 1, @now, @now)
ON DUPLICATE KEY UPDATE name = VALUES(name), address = VALUES(address), city = VALUES(city), phone = VALUES(phone), is_active = 1, updated_at = @now;
SET @shop_id = (SELECT id FROM shops WHERE company_id = @company_id AND code = 'LOM-001' LIMIT 1);

INSERT INTO warehouses (company_id, shop_id, name, is_default, created_at, updated_at)
SELECT @company_id, @shop_id, 'Depot principal', 1, @now, @now
WHERE NOT EXISTS (SELECT 1 FROM warehouses WHERE company_id = @company_id AND name = 'Depot principal');
SET @warehouse_id = (SELECT id FROM warehouses WHERE company_id = @company_id AND name = 'Depot principal' LIMIT 1);

INSERT INTO users (company_id, name, email, email_verified_at, password, is_active, created_at, updated_at)
SELECT @company_id, 'Administrateur PIOS', 'celinbell195@gmail.com', @now,
       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, @now, @now
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'celinbell195@gmail.com');
SET @user_id = (SELECT id FROM users WHERE email = 'celinbell195@gmail.com' LIMIT 1);
UPDATE users SET company_id = @company_id, is_active = 1, updated_at = @now WHERE id = @user_id;

INSERT INTO shop_user (shop_id, user_id, is_default, created_at, updated_at)
VALUES (@shop_id, @user_id, 1, @now, @now)
ON DUPLICATE KEY UPDATE is_default = 1, updated_at = @now;

INSERT IGNORE INTO roles (company_id, name, guard_name, created_at, updated_at)
VALUES (@company_id, 'admin', 'web', @now, @now);
SET @admin_role_id = (SELECT id FROM roles WHERE company_id = @company_id AND name = 'admin' AND guard_name = 'web' LIMIT 1);
INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id, company_id)
VALUES (@admin_role_id, 'App\\Models\\User', @user_id, @company_id);
INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT id, @admin_role_id FROM permissions WHERE guard_name = 'web';

-- Categories et marques
INSERT INTO categories (company_id, parent_id, name, slug, description, is_active, created_at, updated_at) VALUES
(@company_id, NULL, 'Televisions', 'televisions', 'Televiseurs LED, Smart TV et accessoires', 1, @now, @now),
(@company_id, NULL, 'Froid', 'froid', 'Refrigerateurs, congelateurs et climatiseurs', 1, @now, @now),
(@company_id, NULL, 'Cuisson', 'cuisson', 'Cuisinieres, fours et petits appareils', 1, @now, @now),
(@company_id, NULL, 'Entretien', 'entretien', 'Lavage, repassage et entretien de la maison', 1, @now, @now),
(@company_id, NULL, 'Ventilation', 'ventilation', 'Ventilateurs et climatiseurs', 1, @now, @now)
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), is_active = 1, updated_at = @now;

INSERT INTO brands (company_id, name, slug, description, is_active, created_at, updated_at) VALUES
(@company_id, 'Samsung', 'samsung', 'Electronique et electromenager', 1, @now, @now),
(@company_id, 'LG', 'lg', 'Electromenager et image', 1, @now, @now),
(@company_id, 'Nasco', 'nasco', 'Equipements adaptes au marche ouest-africain', 1, @now, @now),
(@company_id, 'Binatone', 'binatone', 'Petit electromenager', 1, @now, @now),
(@company_id, 'Hisense', 'hisense', 'Image et electromenager', 1, @now, @now)
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), is_active = 1, updated_at = @now;

SET @cat_tv = (SELECT id FROM categories WHERE company_id = @company_id AND slug = 'televisions');
SET @cat_froid = (SELECT id FROM categories WHERE company_id = @company_id AND slug = 'froid');
SET @cat_cuisson = (SELECT id FROM categories WHERE company_id = @company_id AND slug = 'cuisson');
SET @cat_entretien = (SELECT id FROM categories WHERE company_id = @company_id AND slug = 'entretien');
SET @cat_ventilation = (SELECT id FROM categories WHERE company_id = @company_id AND slug = 'ventilation');
SET @brand_samsung = (SELECT id FROM brands WHERE company_id = @company_id AND slug = 'samsung');
SET @brand_lg = (SELECT id FROM brands WHERE company_id = @company_id AND slug = 'lg');
SET @brand_nasco = (SELECT id FROM brands WHERE company_id = @company_id AND slug = 'nasco');
SET @brand_binatone = (SELECT id FROM brands WHERE company_id = @company_id AND slug = 'binatone');
SET @brand_hisense = (SELECT id FROM brands WHERE company_id = @company_id AND slug = 'hisense');

-- Catalogue de produits
INSERT INTO products (company_id, category_id, brand_id, sku, barcode, name, slug, description, purchase_price, sale_price, promo_price, stock_quantity, alert_threshold, unit, warranty_months, is_serialized, is_active, notes, created_at, updated_at) VALUES
(@company_id, @cat_tv, @brand_samsung, 'TV-SAM-43-AU', '620260000001', 'Smart TV Samsung 43 pouces', 'smart-tv-samsung-43', 'Smart TV UHD avec Wi-Fi et applications integrees', 142000, 185000, 175000, 14, 4, 'piece', 24, 1, 1, 'Produit vedette', @now, @now),
(@company_id, @cat_tv, @brand_hisense, 'TV-HIS-55-U7', '620260000002', 'Smart TV Hisense 55 pouces', 'smart-tv-hisense-55', 'Televiseur 4K grand format', 235000, 295000, NULL, 8, 3, 'piece', 24, 1, 1, NULL, @now, @now),
(@company_id, @cat_froid, @brand_lg, 'REF-LG-260', '620260000003', 'Refrigerateur LG 260 L', 'refrigerateur-lg-260', 'Refrigerateur double porte faible consommation', 255000, 320000, 305000, 7, 3, 'piece', 24, 1, 1, NULL, @now, @now),
(@company_id, @cat_froid, @brand_nasco, 'CON-NAS-300', '620260000004', 'Congelateur Nasco 300 L', 'congelateur-nasco-300', 'Congelateur coffre tropicalise', 210000, 275000, NULL, 2, 3, 'piece', 18, 1, 1, 'Stock faible volontaire pour les alertes', @now, @now),
(@company_id, @cat_entretien, @brand_lg, 'LAV-LG-8KG', '620260000005', 'Machine a laver LG 8 kg', 'machine-laver-lg-8kg', 'Lave-linge automatique 8 kg', 185000, 240000, NULL, 5, 2, 'piece', 24, 1, 1, NULL, @now, @now),
(@company_id, @cat_cuisson, @brand_nasco, 'CUI-NAS-4F', '620260000006', 'Cuisiniere Nasco 4 feux', 'cuisiniere-nasco-4-feux', 'Cuisiniere gaz avec four', 125000, 165000, 155000, 9, 3, 'piece', 12, 0, 1, NULL, @now, @now),
(@company_id, @cat_cuisson, @brand_samsung, 'MIC-SAM-23', '620260000007', 'Micro-ondes Samsung 23 L', 'micro-ondes-samsung-23', 'Micro-ondes numerique 23 litres', 72000, 95000, NULL, 11, 3, 'piece', 12, 1, 1, NULL, @now, @now),
(@company_id, @cat_cuisson, @brand_binatone, 'MIX-BIN-15', '620260000008', 'Mixeur Binatone 1.5 L', 'mixeur-binatone-15', 'Mixeur robuste avec moulin', 31000, 45000, NULL, 18, 5, 'piece', 6, 0, 1, NULL, @now, @now),
(@company_id, @cat_cuisson, @brand_binatone, 'BOU-BIN-17', '620260000009', 'Bouilloire Binatone 1.7 L', 'bouilloire-binatone-17', 'Bouilloire electrique inox', 15000, 25000, 22000, 24, 6, 'piece', 6, 0, 1, NULL, @now, @now),
(@company_id, @cat_entretien, @brand_binatone, 'FER-BIN-2200', '620260000010', 'Fer a repasser vapeur', 'fer-repasser-vapeur', 'Fer vapeur 2200 watts', 11000, 18500, NULL, 20, 5, 'piece', 6, 0, 1, NULL, @now, @now),
(@company_id, @cat_ventilation, @brand_nasco, 'VEN-NAS-16', '620260000011', 'Ventilateur Nasco 16 pouces', 'ventilateur-nasco-16', 'Ventilateur sur pied trois vitesses', 18500, 28000, NULL, 16, 5, 'piece', 6, 0, 1, NULL, @now, @now),
(@company_id, @cat_ventilation, @brand_lg, 'CLI-LG-12K', '620260000012', 'Climatiseur LG 1.5 CV', 'climatiseur-lg-15cv', 'Climatiseur split inverter', 275000, 350000, 335000, 0, 2, 'piece', 24, 1, 1, 'Rupture volontaire pour les alertes', @now, @now)
ON DUPLICATE KEY UPDATE category_id = VALUES(category_id), brand_id = VALUES(brand_id), barcode = VALUES(barcode), name = VALUES(name), description = VALUES(description), purchase_price = VALUES(purchase_price), sale_price = VALUES(sale_price), promo_price = VALUES(promo_price), stock_quantity = VALUES(stock_quantity), alert_threshold = VALUES(alert_threshold), warranty_months = VALUES(warranty_months), is_serialized = VALUES(is_serialized), is_active = 1, notes = VALUES(notes), updated_at = @now;

-- Clients et fournisseurs
INSERT INTO customers (company_id, name, email, phone, secondary_phone, address, city, customer_type, company_name, tax_id, credit_limit, total_spent, total_debt, loyalty_points, notes, is_active, created_at, updated_at)
SELECT @company_id, 'Afi Mensah', 'afi.mensah@example.com', '+228 90 11 22 33', NULL, 'Agoe', 'Lome', 'individual', NULL, NULL, 250000, 575000, 120000, '575', 'Cliente fidele', 1, @now, @now
WHERE NOT EXISTS (SELECT 1 FROM customers WHERE company_id = @company_id AND email = 'afi.mensah@example.com');
INSERT INTO customers (company_id, name, email, phone, address, city, customer_type, company_name, tax_id, credit_limit, total_spent, total_debt, loyalty_points, is_active, created_at, updated_at)
SELECT @company_id, 'Kossi Amouzou', 'kossi.amouzou@example.com', '+228 91 44 55 66', 'Be Klikame', 'Lome', 'individual', NULL, NULL, 100000, 235000, 0, '235', 1, @now, @now
WHERE NOT EXISTS (SELECT 1 FROM customers WHERE company_id = @company_id AND email = 'kossi.amouzou@example.com');
INSERT INTO customers (company_id, name, email, phone, address, city, customer_type, company_name, tax_id, credit_limit, total_spent, total_debt, loyalty_points, notes, is_active, created_at, updated_at)
SELECT @company_id, 'ETS Koffi Services', 'achats@koffiservices.tg', '+228 22 21 10 10', 'Tokoin', 'Lome', 'professional', 'ETS Koffi Services', 'TG-RCCM-45872', 800000, 460000, 0, '460', 'Client professionnel', 1, @now, @now
WHERE NOT EXISTS (SELECT 1 FROM customers WHERE company_id = @company_id AND email = 'achats@koffiservices.tg');

SET @customer_afi = (SELECT id FROM customers WHERE company_id = @company_id AND email = 'afi.mensah@example.com' LIMIT 1);
SET @customer_kossi = (SELECT id FROM customers WHERE company_id = @company_id AND email = 'kossi.amouzou@example.com' LIMIT 1);
SET @customer_ets = (SELECT id FROM customers WHERE company_id = @company_id AND email = 'achats@koffiservices.tg' LIMIT 1);

INSERT INTO suppliers (company_id, name, contact_name, email, phone, address, city, country, tax_id, payment_terms, total_purchases, total_debt, next_payment_date, notes, is_active, created_at, updated_at)
SELECT @company_id, 'West Africa Electronics', 'Kodjo Lawson', 'ventes@wae.tg', '+228 22 25 30 30', 'Zone portuaire', 'Lome', 'Togo', 'TG-WAE-1002', '30 jours', 520000, 120000, DATE_ADD(@today, INTERVAL 12 DAY), 'Fournisseur principal', 1, @now, @now
WHERE NOT EXISTS (SELECT 1 FROM suppliers WHERE company_id = @company_id AND email = 'ventes@wae.tg');
INSERT INTO suppliers (company_id, name, contact_name, email, phone, address, city, country, payment_terms, total_purchases, total_debt, notes, is_active, created_at, updated_at)
SELECT @company_id, 'Electro Distribution Togo', 'Ama Tete', 'contact@edt.tg', '+228 22 40 50 60', 'Adidogome', 'Lome', 'Togo', 'Comptant', 275000, 0, 'Petit electromenager', 1, @now, @now
WHERE NOT EXISTS (SELECT 1 FROM suppliers WHERE company_id = @company_id AND email = 'contact@edt.tg');
SET @supplier_wae = (SELECT id FROM suppliers WHERE company_id = @company_id AND email = 'ventes@wae.tg' LIMIT 1);

-- References produits utilisees dans les transactions
SET @p_tv = (SELECT id FROM products WHERE company_id = @company_id AND sku = 'TV-SAM-43-AU');
SET @p_fridge = (SELECT id FROM products WHERE company_id = @company_id AND sku = 'REF-LG-260');
SET @p_washer = (SELECT id FROM products WHERE company_id = @company_id AND sku = 'LAV-LG-8KG');
SET @p_cooker = (SELECT id FROM products WHERE company_id = @company_id AND sku = 'CUI-NAS-4F');
SET @p_microwave = (SELECT id FROM products WHERE company_id = @company_id AND sku = 'MIC-SAM-23');
SET @p_mixer = (SELECT id FROM products WHERE company_id = @company_id AND sku = 'MIX-BIN-15');
SET @p_kettle = (SELECT id FROM products WHERE company_id = @company_id AND sku = 'BOU-BIN-17');
SET @p_iron = (SELECT id FROM products WHERE company_id = @company_id AND sku = 'FER-BIN-2200');
SET @p_fan = (SELECT id FROM products WHERE company_id = @company_id AND sku = 'VEN-NAS-16');

-- Huit ventes reparties sur les sept derniers jours
INSERT INTO sales (company_id, shop_id, user_id, customer_id, invoice_number, subtotal, discount_amount, discount_percent, tax_amount, tax_percent, total, amount_paid, change_amount, payment_method, payment_details, credit_amount, notes, status, created_at, updated_at) VALUES
(@company_id, @shop_id, @user_id, @customer_afi, 'PIOS-DEMO-V001', 185000, 10000, 0, 0, 0, 175000, 175000, 0, 'mobile_money', JSON_OBJECT('provider','TMoney'), 0, 'Donnee demo', 'completed', DATE_ADD(@today, INTERVAL 10 HOUR), @now),
(@company_id, @shop_id, @user_id, @customer_kossi, 'PIOS-DEMO-V002', 70000, 0, 0, 0, 0, 70000, 70000, 0, 'cash', NULL, 0, 'Donnee demo', 'completed', DATE_ADD(@today, INTERVAL 12 HOUR), @now),
(@company_id, @shop_id, @user_id, @customer_afi, 'PIOS-DEMO-V003', 320000, 0, 0, 0, 0, 320000, 200000, 0, 'credit', NULL, 120000, 'Vente a credit demo', 'completed', DATE_ADD(DATE_SUB(@today, INTERVAL 1 DAY), INTERVAL 15 HOUR), @now),
(@company_id, @shop_id, @user_id, NULL, 'PIOS-DEMO-V004', 56000, 0, 0, 0, 0, 56000, 56000, 0, 'cash', NULL, 0, 'Donnee demo', 'completed', DATE_ADD(DATE_SUB(@today, INTERVAL 2 DAY), INTERVAL 11 HOUR), @now),
(@company_id, @shop_id, @user_id, @customer_ets, 'PIOS-DEMO-V005', 95000, 0, 0, 0, 0, 95000, 95000, 0, 'mobile_money', JSON_OBJECT('provider','Flooz'), 0, 'Donnee demo', 'completed', DATE_ADD(DATE_SUB(@today, INTERVAL 3 DAY), INTERVAL 14 HOUR), @now),
(@company_id, @shop_id, @user_id, NULL, 'PIOS-DEMO-V006', 55500, 0, 0, 0, 0, 55500, 55500, 0, 'cash', NULL, 0, 'Donnee demo', 'completed', DATE_ADD(DATE_SUB(@today, INTERVAL 4 DAY), INTERVAL 16 HOUR), @now),
(@company_id, @shop_id, @user_id, @customer_ets, 'PIOS-DEMO-V007', 240000, 0, 0, 0, 0, 240000, 240000, 0, 'mixed', JSON_ARRAY(JSON_OBJECT('method','cash','amount',100000),JSON_OBJECT('method','mobile_money','amount',140000)), 0, 'Donnee demo', 'completed', DATE_ADD(DATE_SUB(@today, INTERVAL 5 DAY), INTERVAL 13 HOUR), @now),
(@company_id, @shop_id, @user_id, @customer_kossi, 'PIOS-DEMO-V008', 165000, 10000, 0, 0, 0, 155000, 155000, 0, 'card', NULL, 0, 'Donnee demo', 'completed', DATE_ADD(DATE_SUB(@today, INTERVAL 6 DAY), INTERVAL 9 HOUR), @now)
ON DUPLICATE KEY UPDATE customer_id = VALUES(customer_id), subtotal = VALUES(subtotal), discount_amount = VALUES(discount_amount), total = VALUES(total), amount_paid = VALUES(amount_paid), payment_method = VALUES(payment_method), payment_details = VALUES(payment_details), credit_amount = VALUES(credit_amount), status = 'completed', created_at = VALUES(created_at), updated_at = @now;

DELETE si FROM sale_items si INNER JOIN sales s ON s.id = si.sale_id WHERE s.invoice_number LIKE 'PIOS-DEMO-V%';
INSERT INTO sale_items (sale_id, product_id, product_name, product_sku, quantity, unit_price, discount_amount, total, created_at, updated_at)
SELECT s.id, p.id, p.name, p.sku, 1, 185000, 10000, 175000, s.created_at, @now FROM sales s JOIN products p ON p.id = @p_tv WHERE s.invoice_number = 'PIOS-DEMO-V001'
UNION ALL SELECT s.id, p.id, p.name, p.sku, 1, 45000, 0, 45000, s.created_at, @now FROM sales s JOIN products p ON p.id = @p_mixer WHERE s.invoice_number = 'PIOS-DEMO-V002'
UNION ALL SELECT s.id, p.id, p.name, p.sku, 1, 25000, 0, 25000, s.created_at, @now FROM sales s JOIN products p ON p.id = @p_kettle WHERE s.invoice_number = 'PIOS-DEMO-V002'
UNION ALL SELECT s.id, p.id, p.name, p.sku, 1, 320000, 0, 320000, s.created_at, @now FROM sales s JOIN products p ON p.id = @p_fridge WHERE s.invoice_number = 'PIOS-DEMO-V003'
UNION ALL SELECT s.id, p.id, p.name, p.sku, 2, 28000, 0, 56000, s.created_at, @now FROM sales s JOIN products p ON p.id = @p_fan WHERE s.invoice_number = 'PIOS-DEMO-V004'
UNION ALL SELECT s.id, p.id, p.name, p.sku, 1, 95000, 0, 95000, s.created_at, @now FROM sales s JOIN products p ON p.id = @p_microwave WHERE s.invoice_number = 'PIOS-DEMO-V005'
UNION ALL SELECT s.id, p.id, p.name, p.sku, 3, 18500, 0, 55500, s.created_at, @now FROM sales s JOIN products p ON p.id = @p_iron WHERE s.invoice_number = 'PIOS-DEMO-V006'
UNION ALL SELECT s.id, p.id, p.name, p.sku, 1, 240000, 0, 240000, s.created_at, @now FROM sales s JOIN products p ON p.id = @p_washer WHERE s.invoice_number = 'PIOS-DEMO-V007'
UNION ALL SELECT s.id, p.id, p.name, p.sku, 1, 165000, 10000, 155000, s.created_at, @now FROM sales s JOIN products p ON p.id = @p_cooker WHERE s.invoice_number = 'PIOS-DEMO-V008';

-- Paiements associes aux ventes
INSERT INTO payments (company_id, user_id, payable_id, payable_type, payment_number, amount, payment_method, payment_details, direction, reference, notes, created_at, updated_at)
SELECT @company_id, @user_id, id, 'App\\Models\\Sale', CONCAT('PAY-', invoice_number), amount_paid,
       CASE WHEN payment_method = 'mixed' THEN 'mixed' ELSE payment_method END, payment_details, 'in', invoice_number, 'Donnee demo', created_at, @now
FROM sales WHERE invoice_number LIKE 'PIOS-DEMO-V%'
ON DUPLICATE KEY UPDATE payable_id = VALUES(payable_id), amount = VALUES(amount), payment_method = VALUES(payment_method), payment_details = VALUES(payment_details), updated_at = @now;

-- Approvisionnement et reception
INSERT INTO purchases (company_id, supplier_id, warehouse_id, user_id, reference, subtotal, discount_amount, tax_amount, total, amount_paid, credit_amount, payment_method, expected_delivery_date, received_date, status, notes, created_at, updated_at)
VALUES (@company_id, @supplier_wae, @warehouse_id, @user_id, 'PIOS-DEMO-ACH001', 520000, 0, 0, 520000, 400000, 120000, 'bank_transfer', DATE_SUB(@today, INTERVAL 9 DAY), DATE_SUB(@today, INTERVAL 8 DAY), 'received', 'Approvisionnement demo', DATE_SUB(@now, INTERVAL 8 DAY), @now)
ON DUPLICATE KEY UPDATE total = VALUES(total), amount_paid = VALUES(amount_paid), credit_amount = VALUES(credit_amount), received_date = VALUES(received_date), status = 'received', updated_at = @now;
SET @purchase_id = (SELECT id FROM purchases WHERE reference = 'PIOS-DEMO-ACH001');
DELETE FROM purchase_items WHERE purchase_id = @purchase_id;
INSERT INTO purchase_items (purchase_id, product_id, quantity_ordered, quantity_received, unit_price, total, created_at, updated_at) VALUES
(@purchase_id, @p_tv, 2, 2, 142000, 284000, DATE_SUB(@now, INTERVAL 8 DAY), @now),
(@purchase_id, @p_washer, 1, 1, 185000, 185000, DATE_SUB(@now, INTERVAL 8 DAY), @now),
(@purchase_id, @p_mixer, 1, 1, 31000, 31000, DATE_SUB(@now, INTERVAL 8 DAY), @now),
(@purchase_id, @p_iron, 1, 1, 20000, 20000, DATE_SUB(@now, INTERVAL 8 DAY), @now);

DELETE FROM stock_movements WHERE company_id = @company_id AND reference LIKE 'PIOS-DEMO-%';
INSERT INTO stock_movements (company_id, product_id, warehouse_id, type, quantity, unit_price, reference, notes, created_by, created_at, updated_at) VALUES
(@company_id, @p_tv, @warehouse_id, 'entry', 2, 142000, 'PIOS-DEMO-ACH001', 'Reception fournisseur', @user_id, DATE_SUB(@now, INTERVAL 8 DAY), @now),
(@company_id, @p_washer, @warehouse_id, 'entry', 1, 185000, 'PIOS-DEMO-ACH001', 'Reception fournisseur', @user_id, DATE_SUB(@now, INTERVAL 8 DAY), @now),
(@company_id, @p_mixer, @warehouse_id, 'entry', 1, 31000, 'PIOS-DEMO-ACH001', 'Reception fournisseur', @user_id, DATE_SUB(@now, INTERVAL 8 DAY), @now),
(@company_id, @p_iron, @warehouse_id, 'entry', 1, 20000, 'PIOS-DEMO-ACH001', 'Reception fournisseur', @user_id, DATE_SUB(@now, INTERVAL 8 DAY), @now);

-- Caisse, depenses, garantie et notifications
DELETE FROM cash_registers WHERE company_id = @company_id AND opening_notes = 'PIOS-DEMO';
INSERT INTO cash_registers (company_id, shop_id, user_id, cashier_id, opening_amount, expected_amount, total_sales, total_expenses, total_cash_in, total_cash_out, opening_notes, status, created_at, updated_at)
VALUES (@company_id, @shop_id, @user_id, @user_id, 50000, 205000, 245000, 35000, 0, 55000, 'PIOS-DEMO', 'open', DATE_ADD(@today, INTERVAL 7 HOUR), @now);
SET @cash_id = LAST_INSERT_ID();

DELETE FROM expenses WHERE company_id = @company_id AND notes = 'PIOS-DEMO';
INSERT INTO expenses (company_id, shop_id, cash_register_id, user_id, category, amount, description, expense_date, payment_method, notes, created_at, updated_at) VALUES
(@company_id, @shop_id, @cash_id, @user_id, 'transport', 35000, 'Livraison locale et carburant', @today, 'cash', 'PIOS-DEMO', DATE_ADD(@today, INTERVAL 9 HOUR), @now),
(@company_id, @shop_id, NULL, @user_id, 'electricite', 60000, 'Facture electricite boutique', DATE_SUB(@today, INTERVAL 4 DAY), 'mobile_money', 'PIOS-DEMO', DATE_SUB(@now, INTERVAL 4 DAY), @now);

INSERT INTO warranties (company_id, sale_id, customer_id, product_id, user_id, warranty_number, serial_number, purchase_date, duration_months, expiry_date, problem_description, status, resolution_notes, created_at, updated_at)
SELECT @company_id, s.id, @customer_afi, @p_tv, @user_id, 'GAR-PIOS-DEMO-001', 'SAM43-DEMO-26001', DATE(s.created_at), 24, DATE_ADD(DATE(s.created_at), INTERVAL 24 MONTH), NULL, 'active', 'Garantie demo', s.created_at, @now
FROM sales s WHERE s.invoice_number = 'PIOS-DEMO-V001'
ON DUPLICATE KEY UPDATE sale_id = VALUES(sale_id), expiry_date = VALUES(expiry_date), status = 'active', updated_at = @now;

DELETE FROM app_notifications WHERE company_id = @company_id AND JSON_UNQUOTE(JSON_EXTRACT(data, '$.source')) = 'PIOS-DEMO';
INSERT INTO app_notifications (company_id, user_id, type, title, message, data, is_read, created_at, updated_at) VALUES
(@company_id, @user_id, 'stock_low', 'Stock faible', 'Le congelateur Nasco 300 L a atteint son seuil d alerte.', JSON_OBJECT('source','PIOS-DEMO','sku','CON-NAS-300'), 0, @now, @now),
(@company_id, @user_id, 'stock_out', 'Rupture de stock', 'Le climatiseur LG 1.5 CV est en rupture.', JSON_OBJECT('source','PIOS-DEMO','sku','CLI-LG-12K'), 0, @now, @now),
(@company_id, @user_id, 'customer_credit', 'Creance client', 'Une creance de 120 000 FCFA est ouverte pour Afi Mensah.', JSON_OBJECT('source','PIOS-DEMO','customer_id',@customer_afi), 0, @now, @now);

INSERT INTO settings (company_id, `key`, value, created_at, updated_at) VALUES
(@company_id, 'currency', JSON_QUOTE('XOF'), @now, @now),
(@company_id, 'receipt_width', JSON_QUOTE('80mm'), @now, @now),
(@company_id, 'default_tax_percent', '0', @now, @now)
ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = @now;

COMMIT;

-- Controle rapide apres execution
SELECT @company_id AS company_id, @shop_id AS shop_id, @user_id AS admin_user_id,
       (SELECT COUNT(*) FROM products WHERE company_id = @company_id) AS produits,
       (SELECT COUNT(*) FROM sales WHERE company_id = @company_id AND invoice_number LIKE 'PIOS-DEMO-V%') AS ventes_demo,
       (SELECT SUM(total) FROM sales WHERE company_id = @company_id AND invoice_number LIKE 'PIOS-DEMO-V%') AS chiffre_affaires_demo;
