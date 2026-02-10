#INVONOS DB migration
ALTER TABLE `ip_payments` ADD `receipt_number` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `ip_payments` ADD `client_id` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `ip_payments` ADD `receipt_id` VARCHAR(50) DEFAULT NULL;

CREATE TABLE `ip_expense_custom` (
  `expense_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `expense_custom_fieldid` int(11) NOT NULL,
  `expense_custom_fieldvalue` text NOT NULL,
  PRIMARY KEY (`expense_custom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ip_expense_sumex` (
  `sumex_id` int(11) NOT NULL AUTO_INCREMENT,
  `sumex_expense` int(11) NOT NULL,
  `sumex_reason` int(11) NOT NULL,
  `sumex_diagnosis` varchar(500) NOT NULL,
  `sumex_observations` varchar(500) NOT NULL,
  `sumex_treatmentstart` date NOT NULL,
  `sumex_treatmentend` date NOT NULL,
  `sumex_casedate` date NOT NULL,
  `sumex_casenumber` varchar(35) DEFAULT NULL,
  PRIMARY KEY (`sumex_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


CREATE TABLE `ip_companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_date_created` datetime NOT NULL,
  `company_date_modified` datetime NOT NULL,
  `company_name` text NOT NULL,
  `company_surname` varchar(255) NOT NULL,
  `company_phone` text NOT NULL,
  `company_mobile` text NOT NULL,
  `company_active` int(1) NOT NULL,
  `company_language` varchar(255) NOT NULL,
  `company_address_1` text NOT NULL,
  `company_address_2` text NOT NULL,
  `company_city` text NOT NULL,
  `company_state` text NOT NULL,
  `company_zip` text NOT NULL,
  `company_country` text NOT NULL,
  `company_fax` text NOT NULL,
  `company_email` text NOT NULL,
  `company_web` text NOT NULL,
  `company_vat_id` int(11) NOT NULL,
  `company_tax_code` text NOT NULL,
  `company_avs` varchar(16) NOT NULL,
  `company_insurednumber` varchar(30) NOT NULL,
  `company_veka` varchar(30) NOT NULL,
  `company_birthdate` date NOT NULL,
  `company_gender` int(1) NOT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


CREATE TABLE `ip_expense_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `item_tax_rate_id` int(11) NOT NULL DEFAULT 0,
  `item_product_id` int(11) DEFAULT NULL,
  `item_date_added` date NOT NULL,
  `item_task_id` int(11) DEFAULT NULL,
  `item_name` text DEFAULT NULL,
  `item_description` longtext DEFAULT NULL,
  `item_quantity` decimal(10,2) NOT NULL,
  `item_price` decimal(20,2) DEFAULT NULL,
  `item_discount_amount` decimal(20,2) DEFAULT NULL,
  `item_order` int(2) NOT NULL DEFAULT 0,
  `item_is_recurring` tinyint(1) DEFAULT NULL,
  `item_product_unit` varchar(50) DEFAULT NULL,
  `item_product_unit_id` int(11) DEFAULT NULL,
  `item_date` date DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `expense_id` (`expense_id`,`item_tax_rate_id`,`item_date_added`,`item_order`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `ip_expenses_recurring` (
  `expense_recurring_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `recur_start_date` date NOT NULL,
  `recur_end_date` date DEFAULT NULL,
  `recur_frequency` varchar(255) NOT NULL,
  `recur_next_date` date DEFAULT NULL,
  PRIMARY KEY (`expense_recurring_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ip_expense_item_amounts` (
  `item_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_subtotal` decimal(20,2) DEFAULT NULL,
  `item_tax_total` decimal(20,2) DEFAULT NULL,
  `item_discount` decimal(20,2) DEFAULT NULL,
  `item_total` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`item_amount_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `ip_expense_amounts` (
  `expense_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `expense_sign` decimal(20,2) NOT NULL,
  `expense_item_subtotal` decimal(20,2) NOT NULL,
  `expense_item_tax_total` decimal(20,2) NOT NULL,
  `expense_tax_total` decimal(20,2) NOT NULL,
  `expense_total` decimal(20,2) NOT NULL,
  `expense_paid` decimal(20,2) NOT NULL,
  `expense_balance` decimal(20,2) NOT NULL,
  PRIMARY KEY (`expense_amount_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ip_company_custom` (
  `company_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `company_custom_fieldid` int(11) NOT NULL,
  `company_custom_fieldvalue` text DEFAULT NULL,
  PRIMARY KEY (`company_custom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ip_company_notes` (
  `company_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `company_note_date` date NOT NULL,
  `company_note` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`company_note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ip_expense_tax_rates` (
  `expense_tax_rate_id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  `include_item_tax` int(1) NOT NULL,
  `include_tax` int(1) NOT NULL,
  `expense_tax_rate_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `ip_expenses` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_number` varchar(255) DEFAULT '',
  `expense_category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expense_date_created` date NOT NULL,
  `expense_time_created` time NOT NULL,
  `expense_date_due` date NOT NULL,
  `expense_currency_id` int(11) DEFAULT NULL,
  `expense_currency_code` varchar(3) DEFAULT 'USD',
  `expense_rate` decimal(15,4) DEFAULT '1.0000',
  `expense_status_id` tinyint(1) DEFAULT 1 COMMENT '1=новый, 2=подтвержденный, 3=оплаченный',
  `expense_is_read_only` tinyint(1) DEFAULT 0,
  `expense_password` varchar(255) DEFAULT NULL,
  `expense_url_key` varchar(32) NOT NULL,
  `expense_terms` text,
  `expense_notes` text,
  `expense_date_modified` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_id`),
  KEY `expense_category_id` (`expense_category_id`),
  KEY `user_id` (`user_id`),
  KEY `expense_date_created` (`expense_date_created`),
  KEY `expense_status_id` (`expense_status_id`),
  FOREIGN KEY (`expense_category_id`) REFERENCES `ip_expense_categories` (`expense_category_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`user_id`) REFERENCES `ip_users` (`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ==========================================
-- Таблица категорий расходов (пока статическая, потом можно сделать редактируемой)
-- ==========================================
CREATE TABLE IF NOT EXISTS `ip_expense_categories` (
  `expense_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_category_name` varchar(255) NOT NULL,
  `expense_category_description` text,
  `expense_category_color` varchar(7) DEFAULT '#007bff',
  `expense_category_icon` varchar(50) DEFAULT 'fa fa-shopping-cart',
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`expense_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Вставка тестовых категорий
INSERT INTO `ip_expense_categories` (`expense_category_name`, `expense_category_description`, `expense_category_color`, `expense_category_icon`) VALUES
('Канцелярские товары', 'Бумага, ручки, папки, офисная мебель', '#007bff', 'fa fa-pencil'),
('Транспорт', 'Топливо, общественный транспорт, такси, ремонт автомобиля', '#28a745', 'fa fa-car'),
('Питание', 'Представительские расходы, обеды, ужины с клиентами', '#dc3545', 'fa fa-utensils'),
('IT и ПО', 'Компьютерное оборудование, программное обеспечение, интернет', '#6f42c1', 'fa fa-laptop'),
('Маркетинг и реклама', 'Печать, баннеры, онлайн реклама, промо материалы', '#fd7e14', 'fa fa-bullhorn'),
('Коммунальные услуги', 'Электричество, отопление, телефон, интернет', '#20c997', 'fa fa-home'),
('Командировки', 'Отели, авиабилеты, визы, суточные расходы', '#17a2b8', 'fa fa-plane'),
('Образование', 'Курсы, конференции, книги, семинары', '#6c757d', 'fa fa-graduation-cap'),
('Медицина', 'Медицинские услуги, лекарства, страховка', '#e83e8c', 'fa fa-heartbeat'),
('Прочее', 'Прочие расходы, которые не подходят под другие категории', '#495057', 'fa fa-ellipsis-h');