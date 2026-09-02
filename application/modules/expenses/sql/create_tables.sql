-- Создание таблиц для модуля учета расходов InvoicePlane
-- Дата создания: 2025-11-27

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

-- ==========================================
-- Основная таблица расходов
-- ==========================================
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
  `payment_method_id` int(11) DEFAULT NULL,
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
  KEY `payment_method_id` (`payment_method_id`),
  KEY `expense_date_created` (`expense_date_created`),
  KEY `expense_status_id` (`expense_status_id`),
  FOREIGN KEY (`expense_category_id`) REFERENCES `ip_expense_categories` (`expense_category_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`user_id`) REFERENCES `ip_users` (`user_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`payment_method_id`) REFERENCES `ip_payment_methods` (`payment_method_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- Таблица сумм расходов (аналогично invoice_amounts)
-- ==========================================
CREATE TABLE IF NOT EXISTS `ip_expense_amounts` (
  `expense_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `expense_item_subtotal` decimal(15,2) DEFAULT '0.00',
  `expense_item_tax_total` decimal(15,2) DEFAULT '0.00',
  `expense_tax_total` decimal(15,2) DEFAULT '0.00',
  `expense_total` decimal(15,2) DEFAULT '0.00',
  `expense_paid` decimal(15,2) DEFAULT '0.00',
  `expense_balance` decimal(15,2) DEFAULT '0.00',
  `expense_sign` decimal(15,2) DEFAULT '1.00',
  PRIMARY KEY (`expense_amount_id`),
  KEY `expense_id` (`expense_id`),
  FOREIGN KEY (`expense_id`) REFERENCES `ip_expenses` (`expense_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- Таблица позиций расходов
-- ==========================================
CREATE TABLE IF NOT EXISTS `ip_expense_items` (
  `expense_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `item_tax_rate_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` text,
  `item_quantity` decimal(15,4) DEFAULT '0.0000',
  `item_price` decimal(15,4) DEFAULT '0.0000',
  `item_discount_amount` decimal(15,4) DEFAULT '0.0000',
  `item_discount_percent` decimal(15,4) DEFAULT '0.0000',
  `item_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`expense_item_id`),
  KEY `expense_id` (`expense_id`),
  KEY `item_tax_rate_id` (`item_tax_rate_id`),
  FOREIGN KEY (`expense_id`) REFERENCES `ip_expenses` (`expense_id`) ON DELETE CASCADE,
  FOREIGN KEY (`item_tax_rate_id`) REFERENCES `ip_tax_rates` (`tax_rate_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- Таблица сумм позиций расходов
-- ==========================================
CREATE TABLE IF NOT EXISTS `ip_expense_item_amounts` (
  `item_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_item_id` int(11) NOT NULL,
  `item_subtotal` decimal(15,2) DEFAULT '0.00',
  `item_tax_total` decimal(15,2) DEFAULT '0.00',
  `item_total` decimal(15,2) DEFAULT '0.00',
  `item_discount` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`item_amount_id`),
  KEY `expense_item_id` (`expense_item_id`),
  FOREIGN KEY (`expense_item_id`) REFERENCES `ip_expense_items` (`expense_item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- Таблица налогов расходов
-- ==========================================
CREATE TABLE IF NOT EXISTS `ip_expense_tax_rates` (
  `expense_tax_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  `include_item_tax` tinyint(1) DEFAULT 0,
  `include_tax` tinyint(1) DEFAULT 0,
  `expense_tax_rate_amount` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`expense_tax_rate_id`),
  KEY `expense_id` (`expense_id`),
  KEY `tax_rate_id` (`tax_rate_id`),
  FOREIGN KEY (`expense_id`) REFERENCES `ip_expenses` (`expense_id`) ON DELETE CASCADE,
  FOREIGN KEY (`tax_rate_id`) REFERENCES `ip_tax_rates` (`tax_rate_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- Таблица платежей по расходам
-- ==========================================
CREATE TABLE IF NOT EXISTS `ip_expense_payments` (
  `expense_payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_method_id` int(11) DEFAULT NULL,
  `payment_method_name` varchar(255) DEFAULT '',
  `payment_note` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_payment_id`),
  KEY `expense_id` (`expense_id`),
  KEY `payment_method_id` (`payment_method_id`),
  FOREIGN KEY (`expense_id`) REFERENCES `ip_expenses` (`expense_id`) ON DELETE CASCADE,
  FOREIGN KEY (`payment_method_id`) REFERENCES `ip_payment_methods` (`payment_method_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- Кастомные поля для расходов
-- ==========================================
CREATE TABLE IF NOT EXISTS `ip_expense_custom` (
  `expense_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `expense_custom_fieldid` int(11) NOT NULL,
  `expense_custom_fieldvalue` varchar(255) DEFAULT '',
  PRIMARY KEY (`expense_custom_id`),
  KEY `expense_id` (`expense_id`),
  KEY `expense_custom_fieldid` (`expense_custom_fieldid`),
  FOREIGN KEY (`expense_id`) REFERENCES `ip_expenses` (`expense_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- Настройки модуля расходов
-- ==========================================
INSERT IGNORE INTO `ip_settings` (`setting_key`, `setting_value`, `setting_type`) VALUES
('expenses_default_expense_status', '1', 'default'),
('expenses_due_after_days', '30', 'default'),
('expenses_generate_number_for_new', '1', 'default'),
('expenses_next_number_prefix', 'EXP', 'default'),
('expenses_next_number', '1', 'default'),
('expenses_default_currency_code', 'USD', 'default'),
('expenses_mark_as_paid_on_payment', '1', 'default'),
('expenses_default_notes', 'Расход по модулю InvoicePlane', 'default'),
('expenses_default_terms', 'Оплата должна быть произведена в течение 30 дней', 'default');

-- ==========================================
-- Создание индексов для оптимизации
-- ==========================================
CREATE INDEX idx_expenses_status_date ON ip_expenses (expense_status_id, expense_date_created);
CREATE INDEX idx_expenses_category ON ip_expenses (expense_category_id, expense_status_id);
CREATE INDEX idx_expenses_user ON ip_expenses (user_id, expense_date_created);
CREATE INDEX idx_expense_items_order ON ip_expense_items (expense_id, item_order);
