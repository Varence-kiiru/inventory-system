CREATE TABLE IF NOT EXISTS `users` (
    `user_id` INT PRIMARY KEY AUTO_INCREMENT,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT NOT NULL DEFAULT 2,
    `profile_photo` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `products` (
    `product_id` INT PRIMARY KEY AUTO_INCREMENT,
    `product_code` VARCHAR(50) UNIQUE NOT NULL,
    `product_name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `unit_price` DECIMAL(10,2) NOT NULL,
    `is_vat_exempt` BOOLEAN DEFAULT FALSE,
    `stock_quantity` INT NOT NULL DEFAULT 0,
    `min_stock_level` INT NOT NULL DEFAULT 10,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `customers` (
    `customer_id` INT PRIMARY KEY AUTO_INCREMENT,
    `customer_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100),
    `phone` VARCHAR(20),
    `address` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `sales` (
    `sale_id` INT PRIMARY KEY AUTO_INCREMENT,
    `customer_id` INT,
    `user_id` INT,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `vat_amount` DECIMAL(10,2) NOT NULL,
    `vatable_subtotal` DECIMAL(10,2) NOT NULL,
    `non_vatable_subtotal` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(50),
    `payment_status` VARCHAR(20) DEFAULT 'pending',
    `status` VARCHAR(20) DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS `sale_items` (
    `sale_item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `sale_id` INT,
    `product_id` INT,
    `quantity` INT NOT NULL,
    `unit_price` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE IF NOT EXISTS `system_settings` (
    `setting_key` VARCHAR(50) PRIMARY KEY,
    `setting_value` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
