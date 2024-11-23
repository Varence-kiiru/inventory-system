-- Database Backup 2024-11-16 14:25:33

DROP TABLE IF EXISTS customers;
CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO customers VALUES ('1','Varence Nganga','The Olivian Group limited','varence@gmail.com','0719728666','Nairobi','active','2024-11-15 23:05:41');
INSERT INTO customers VALUES ('2','Miguel kiiru','','miguel@gmail.com','0715462406','','active','2024-11-15 23:11:23');
INSERT INTO customers VALUES ('3','Olivia Wangeci','','olivia@gmail.com','0783756878','','active','2024-11-16 15:21:49');


DROP TABLE IF EXISTS products;
CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_code` varchar(50) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `is_vat_exempt` tinyint(1) DEFAULT 0,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `min_stock_level` int(11) NOT NULL DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `product_code` (`product_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products VALUES ('1','001','Jinko Tiger 540 Watts','','17000.00','1','100','10','2024-11-15 20:22:51','2024-11-15 20:22:51');
INSERT INTO products VALUES ('5','002','Deye 12KVa','','300000.00','0','10','1','2024-11-15 20:26:07','2024-11-15 22:57:11');
INSERT INTO products VALUES ('7','003','Deye 8KVa','','196000.00','0','10','1','2024-11-15 20:36:37','2024-11-15 22:57:43');
INSERT INTO products VALUES ('8','004','JA Bifacial Panel','','19600.00','1','100','10','2024-11-15 20:38:04','2024-11-15 20:38:04');
INSERT INTO products VALUES ('9','005','Uhome 5KW Battery','','98000.00','0','20','5','2024-11-15 22:08:38','2024-11-15 22:08:38');
INSERT INTO products VALUES ('10','006','MUST 1KW Imverter','','30000.00','0','20','5','2024-11-15 22:22:51','2024-11-15 22:22:51');


DROP TABLE IF EXISTS sale_items;
CREATE TABLE `sale_items` (
  `sale_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `vat_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`sale_item_id`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`),
  CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS sales;
CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`sale_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS stock_transactions;
CREATE TABLE `stock_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `transaction_type` varchar(10) NOT NULL,
  `notes` text DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`transaction_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `stock_transactions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO stock_transactions VALUES ('1','5','5','add','','2024-11-15 22:57:11');
INSERT INTO stock_transactions VALUES ('2','7','2','add','','2024-11-15 22:57:20');
INSERT INTO stock_transactions VALUES ('3','7','3','add','','2024-11-15 22:57:43');


DROP TABLE IF EXISTS system_settings;
CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company_logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO system_settings VALUES ('company_logo','assets/images/uploads/company_logo_1731689240.png','2024-11-15 19:47:20','');
INSERT INTO system_settings VALUES ('company_name','The Olivian Group Limited','2024-11-15 19:49:52','');
INSERT INTO system_settings VALUES ('contact_email','info@olivian.co.ke','2024-11-15 19:38:06','');
INSERT INTO system_settings VALUES ('currency','KES','2024-11-15 19:37:39','');
INSERT INTO system_settings VALUES ('email_notifications','on','2024-11-15 19:38:07','');
INSERT INTO system_settings VALUES ('last_backup','','2024-11-15 19:37:40','');
INSERT INTO system_settings VALUES ('low_stock_threshold','10','2024-11-15 19:37:39','');
INSERT INTO system_settings VALUES ('stock_alerts','on','2024-11-15 19:38:07','');
INSERT INTO system_settings VALUES ('vat_number','P052291267A','2024-11-15 19:38:07','');
INSERT INTO system_settings VALUES ('vat_rate','16.00','2024-11-15 19:37:39','');


DROP TABLE IF EXISTS users;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 2,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



