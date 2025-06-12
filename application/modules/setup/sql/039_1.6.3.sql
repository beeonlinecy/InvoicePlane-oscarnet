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

CREATE TABLE `ip_expenses` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `expense_date_created` date NOT NULL,
  `expense_time_created` time NOT NULL,
  `expense_date_modified` datetime NOT NULL,
  `payment_method` int(11) NOT NULL,
  `photo_url` char(32) NOT NULL,
  `is_read_only` tinyint(1) NOT NULL,
  `expense_status_id` tinyint(2) NOT NULL,
  `creditexpense_parent_i` int(11) NOT NULL,
  `expense_password` varchar(60) NOT NULL,
  `expense_discount_amount` decimal(20,2) NOT NULL,
  `expense_discount_percent` decimal(20,2) NOT NULL,
  `expense_date_due` date NOT NULL,
  `expense_number` varchar(100) NOT NULL,
  `creditexpense_parent_id` int(11) NOT NULL,
  PRIMARY KEY (`expense_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;