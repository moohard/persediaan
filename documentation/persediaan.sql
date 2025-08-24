-- =================================================================
-- Skrip Database untuk Aplikasi Persediaan Barang
-- Database: MySQL
-- Dibuat pada: 23 Agustus 2025
-- =================================================================

-- Hapus database jika sudah ada (opsional)
-- DROP DATABASE IF EXISTS db_persediaan_barang;

-- Buat database baru
CREATE DATABASE IF NOT EXISTS db_persediaan_barang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Gunakan database
USE db_persediaan_barang;

-- =================================================================
-- Modul 1: Master Data (Data Inti)
-- =================================================================

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE brands (
    brand_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    brand_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (brand_id) REFERENCES brands(brand_id)
);

CREATE TABLE product_variants (
    variant_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    sku VARCHAR(100) NOT NULL UNIQUE COMMENT 'Stock Keeping Unit, kunci operasional utama',
    variant_name VARCHAR(100) COMMENT 'e.g., Merah, XL',
    purchase_price DECIMAL(15, 2) NOT NULL,
    selling_price DECIMAL(15, 2) NOT NULL,
    attributes JSON COMMENT 'e.g., {"warna": "Merah", "ukuran": "XL"}',
    reorder_level INT DEFAULT 0,
    barcode VARCHAR(100),
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

CREATE TABLE warehouses (
    warehouse_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE suppliers (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT
);

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT
);

-- =================================================================
-- Modul 2: Manajemen Stok (Inti Operasional)
-- =================================================================

CREATE TABLE stock_levels (
    stock_level_id INT AUTO_INCREMENT PRIMARY KEY,
    variant_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    quantity_on_hand INT NOT NULL DEFAULT 0 COMMENT 'Jumlah fisik di gudang',
    quantity_committed INT NOT NULL DEFAULT 0 COMMENT 'Dipesan pelanggan, belum dikirim',
    quantity_on_order INT NOT NULL DEFAULT 0 COMMENT 'Dipesan ke supplier, belum diterima',
    UNIQUE KEY uk_variant_warehouse (variant_id, warehouse_id),
    FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(warehouse_id) ON DELETE CASCADE
);

CREATE TABLE stock_movements (
    movement_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    variant_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    quantity_change INT NOT NULL COMMENT 'Positif untuk masuk, Negatif untuk keluar',
    movement_type ENUM('GOODS_RECEIPT', 'SALES_SHIPMENT', 'STOCK_ADJUSTMENT', 'TRANSFER_OUT', 'TRANSFER_IN', 'CUSTOMER_RETURN') NOT NULL,
    reference_id INT COMMENT 'ID dari PO, SO, atau Adjustment',
    movement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT COMMENT 'ID pengguna yang melakukan aksi',
    FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(warehouse_id) ON DELETE CASCADE
);

-- =================================================================
-- Modul 3 & 4: Pembelian & Penjualan
-- =================================================================

CREATE TABLE purchase_orders (
    po_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    order_date DATE NOT NULL,
    expected_delivery_date DATE,
    status ENUM('DRAFT', 'SUBMITTED', 'PARTIALLY_RECEIVED', 'COMPLETED', 'CANCELLED') NOT NULL DEFAULT 'DRAFT',
    total_amount DECIMAL(15, 2),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
);

CREATE TABLE purchase_order_items (
    po_item_id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT NOT NULL,
    variant_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    unit_price DECIMAL(15, 2) NOT NULL,
    quantity_received INT DEFAULT 0,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id)
);

CREATE TABLE sales_orders (
    so_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    order_date DATE NOT NULL,
    status ENUM('PENDING', 'PAID', 'SHIPPED', 'COMPLETED', 'CANCELLED') NOT NULL DEFAULT 'PENDING',
    shipping_address TEXT,
    total_amount DECIMAL(15, 2),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE sales_order_items (
    so_item_id INT AUTO_INCREMENT PRIMARY KEY,
    so_id INT NOT NULL,
    variant_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (so_id) REFERENCES sales_orders(so_id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id)
);

-- =================================================================
-- Tambahkan Indexes untuk Performa
-- =================================================================
CREATE INDEX idx_variant_sku ON product_variants(sku);
CREATE INDEX idx_product_name ON products(name);
CREATE INDEX idx_movement_date ON stock_movements(movement_date);

-- =================================================================
-- Masukkan Data Contoh (Sample Data)
-- =================================================================

-- 1. Kategori & Brand
INSERT INTO categories (name) VALUES ('Pakaian'), ('Elektronik');
INSERT INTO brands (name) VALUES ('Adidata'), ('Samsonic');

-- 2. Produk & Varian
INSERT INTO products (name, category_id, brand_id) VALUES
('Kemeja Flanel Lengan Panjang', 1, 1),
('Smartphone Galaxy Z', 2, 2);

INSERT INTO product_variants (product_id, sku, variant_name, purchase_price, selling_price, attributes, reorder_level) VALUES
(1, 'AD-KFLP-MR-L', 'Merah, L', 150000, 250000, '{"warna": "Merah", "ukuran": "L"}', 10),
(1, 'AD-KFLP-BR-L', 'Biru, L', 150000, 250000, '{"warna": "Biru", "ukuran": "L"}', 10),
(2, 'SS-GZ-256-BLK', '256GB, Hitam', 12000000, 15000000, '{"penyimpanan": "256GB", "warna": "Hitam"}', 5);

-- 3. Gudang
INSERT INTO warehouses (name, address) VALUES
('Gudang Pusat Jakarta', 'Jl. Raya Bogor KM 20, Jakarta Timur'),
('Toko Cabang Bandung', 'Jl. Asia Afrika No. 1, Bandung');

-- 4. Level Stok Awal
INSERT INTO stock_levels (variant_id, warehouse_id, quantity_on_hand, quantity_on_order) VALUES
(1, 1, 50, 20), -- Kemeja Merah L di Jakarta
(1, 2, 25, 0),  -- Kemeja Merah L di Bandung
(2, 1, 40, 0),  -- Kemeja Biru L di Jakarta
(3, 1, 15, 10); -- Smartphone di Jakarta

-- 5. Pergerakan Stok
INSERT INTO stock_movements (variant_id, warehouse_id, quantity_change, movement_type, reference_id) VALUES
(1, 1, 70, 'GOODS_RECEIPT', 101),
(1, 1, -5, 'SALES_SHIPMENT', 201),
(1, 1, -15, 'TRANSFER_OUT', 301),
(1, 2, 15, 'TRANSFER_IN', 301);

-- 6. Supplier & Customer
INSERT INTO suppliers (name, contact_person) VALUES ('PT. Sandang Jaya', 'Bapak Heru');
INSERT INTO customers (name, email) VALUES ('Andi Budi', 'andi.budi@example.com');

-- Selesai
-- =================================================================
