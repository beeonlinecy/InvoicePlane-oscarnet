# Expenses schema compatibility fixes

# Create missing expense payments table (if absent)
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

# Add payment_method_id for expenses when supported
ALTER TABLE `ip_expenses`
  ADD COLUMN IF NOT EXISTS `payment_method_id` int(11) DEFAULT NULL;

# Add index for payment method lookup
ALTER TABLE `ip_expenses`
  ADD INDEX IF NOT EXISTS `idx_expenses_payment_method_id` (`payment_method_id`);
