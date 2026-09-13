-- =====================================================================
-- E-Commerce and Grocery Delivery Management System
-- Database Schema (Run this whole file in phpMyAdmin -> SQL tab)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS grocery_delivery_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE grocery_delivery_system;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- users : all platform users (customer / seller / delivery_manager / admin)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    role ENUM('customer','seller','delivery_manager','admin') NOT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- sellers : extended profile for seller users
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS sellers;
CREATE TABLE sellers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    shop_name VARCHAR(150) NOT NULL,
    shop_description TEXT,
    shop_logo_path VARCHAR(255) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    is_approved ENUM('pending','approved','rejected','suspended') NOT NULL DEFAULT 'pending',
    rejection_reason VARCHAR(255) DEFAULT NULL,
    commission_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sellers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- categories : hierarchical grocery/product categories
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS products;
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    brand VARCHAR(100) DEFAULT NULL,
    unit VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock_qty INT NOT NULL DEFAULT 0,
    reorder_level INT NOT NULL DEFAULT 5,
    expiry_date DATE DEFAULT NULL,
    primary_image_path VARCHAR(255) DEFAULT NULL,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    is_removed TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_seller FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id),
    CONSTRAINT chk_products_stock CHECK (stock_qty >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- product_images : additional images per product
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS product_images;
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_pimages_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- coupons : seller_id NULL => platform-wide coupon created by admin
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS coupons;
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT DEFAULT NULL,
    created_by_admin TINYINT(1) NOT NULL DEFAULT 0,
    code VARCHAR(40) NOT NULL UNIQUE,
    discount_pct DECIMAL(5,2) NOT NULL,
    max_uses INT NOT NULL DEFAULT 100,
    uses_count INT NOT NULL DEFAULT 0,
    min_order_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    valid_until DATE NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT fk_coupons_seller FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- delivery_zones
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS delivery_zones;
CREATE TABLE delivery_zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zone_name VARCHAR(100) NOT NULL,
    delivery_fee DECIMAL(8,2) NOT NULL DEFAULT 0,
    estimated_days INT NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- addresses : customer saved shipping addresses
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS addresses;
CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    label VARCHAR(50) DEFAULT 'Home',
    full_address VARCHAR(255) NOT NULL,
    city VARCHAR(100) DEFAULT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_addresses_user FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    delivery_zone_id INT NOT NULL,
    payment_method ENUM('COD','Card') NOT NULL DEFAULT 'COD',
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    delivery_fee DECIMAL(8,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('pending','confirmed','processing','shipped','delivered','cancelled','return_requested','returned') NOT NULL DEFAULT 'pending',
    coupon_id INT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES users(id),
    CONSTRAINT fk_orders_zone FOREIGN KEY (delivery_zone_id) REFERENCES delivery_zones(id),
    CONSTRAINT fk_orders_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- order_items
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS order_items;
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    seller_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    item_status ENUM('pending','confirmed','processing','shipped','delivered','cancelled','returned') NOT NULL DEFAULT 'pending',
    tracking_note VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT fk_items_seller FOREIGN KEY (seller_id) REFERENCES sellers(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- delivery_agents
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS delivery_agents;
CREATE TABLE delivery_agents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(120) NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- delivery_assignments
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS delivery_assignments;
CREATE TABLE delivery_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    agent_id INT NOT NULL,
    assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('assigned','picked_up','in_transit','delivered','failed') NOT NULL DEFAULT 'assigned',
    delivery_zone VARCHAR(100) DEFAULT NULL,
    failed_reason VARCHAR(255) DEFAULT NULL,
    delivered_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_assignments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_assignments_agent FOREIGN KEY (agent_id) REFERENCES delivery_agents(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- reviews
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS reviews;
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    order_id INT NOT NULL,
    customer_id INT NOT NULL,
    rating TINYINT NOT NULL,
    review_text TEXT,
    seller_reply TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_reviews_customer FOREIGN KEY (customer_id) REFERENCES users(id),
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- wishlists
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS wishlists;
CREATE TABLE wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wishlist_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wishlist_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_wish (customer_id, product_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- return_requests
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS return_requests;
CREATE TABLE return_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    order_item_id INT NOT NULL,
    customer_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
    seller_note VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_returns_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_returns_item FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    CONSTRAINT fk_returns_customer FOREIGN KEY (customer_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- disputes
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS disputes;
CREATE TABLE disputes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    seller_id INT DEFAULT NULL,
    order_id INT DEFAULT NULL,
    description TEXT NOT NULL,
    status ENUM('open','resolved') NOT NULL DEFAULT 'open',
    admin_note VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_disputes_customer FOREIGN KEY (customer_id) REFERENCES users(id),
    CONSTRAINT fk_disputes_seller FOREIGN KEY (seller_id) REFERENCES sellers(id),
    CONSTRAINT fk_disputes_order FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- notifications
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS notifications;
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- announcements : platform-wide admin announcements
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS announcements;
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_announce_admin FOREIGN KEY (admin_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- featured_products : homepage featured items set by admin
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS featured_products;
CREATE TABLE featured_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_featured_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- settings : simple key/value platform settings (e.g. default commission)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
    setting_key VARCHAR(60) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- SEED DATA
-- =====================================================================

-- default password for ALL seeded accounts below is:  Password123
-- hash generated with PHP password_hash('Password123', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password_hash, phone, role, is_active) VALUES
('Platform Admin', 'admin@grocery.test', '$2b$10$QhThppt1IcHuLbKsH2IBcOLtq/jthb5lVRfaYJxW5BET0DdiRE272', '01700000001', 'admin', 1),
('Dhaka Delivery Manager', 'delivery@grocery.test', '$2b$10$QhThppt1IcHuLbKsH2IBcOLtq/jthb5lVRfaYJxW5BET0DdiRE272', '01700000002', 'delivery_manager', 1),
('Fresh Mart Seller', 'seller@grocery.test', '$2b$10$QhThppt1IcHuLbKsH2IBcOLtq/jthb5lVRfaYJxW5BET0DdiRE272', '01700000003', 'seller', 1),
('John Customer', 'customer@grocery.test', '$2b$10$QhThppt1IcHuLbKsH2IBcOLtq/jthb5lVRfaYJxW5BET0DdiRE272', '01700000004', 'customer', 1);

INSERT INTO sellers (user_id, shop_name, shop_description, address, is_approved, commission_rate) VALUES
(3, 'Fresh Mart', 'Fresh groceries and household essentials delivered daily.', 'Gulshan, Dhaka', 'approved', 10.00);

INSERT INTO categories (parent_id, name, description) VALUES
(NULL, 'Groceries', 'Everyday grocery items'),
(NULL, 'Household', 'Household and cleaning products'),
(1, 'Fruits & Vegetables', 'Fresh produce'),
(1, 'Dairy & Eggs', 'Milk, cheese, eggs'),
(1, 'Rice, Flour & Grains', 'Staple grains'),
(2, 'Cleaning Supplies', 'Cleaning products'),
(2, 'Personal Care', 'Personal hygiene products');

INSERT INTO delivery_zones (zone_name, delivery_fee, estimated_days) VALUES
('Dhaka City', 60.00, 1),
('Chattogram', 100.00, 2),
('Sylhet', 120.00, 3),
('Rajshahi', 110.00, 3);

INSERT INTO products (seller_id, category_id, name, description, brand, unit, price, stock_qty, reorder_level, expiry_date, is_available) VALUES
(1, 3, 'Fresh Red Apples', 'Crisp and juicy imported red apples.', 'FarmFresh', '1 kg', 220.00, 50, 10, '2026-09-15', 1),
(1, 4, 'Full Cream Milk', 'Pasteurized full cream milk.', 'DairyBest', '1 litre', 95.00, 40, 10, '2026-09-01', 1),
(1, 5, 'Basmati Rice', 'Premium long grain basmati rice.', 'GoldenGrain', '5 kg', 750.00, 30, 5, NULL, 1),
(1, 6, 'Dishwashing Liquid', 'Grease-cutting dishwashing liquid.', 'CleanPro', '500 ml', 130.00, 60, 10, NULL, 1),
(1, 7, 'Herbal Shampoo', 'Nourishing herbal shampoo for all hair types.', 'PureCare', '200 ml', 210.00, 25, 5, NULL, 1);

INSERT INTO settings (setting_key, setting_value) VALUES
('default_commission_rate', '10.00'),
('platform_name', 'FreshCart Marketplace');
