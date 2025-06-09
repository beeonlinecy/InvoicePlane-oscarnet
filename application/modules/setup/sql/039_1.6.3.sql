#INVONOS DB migration
ALTER TABLE `ip_payments` ADD `receipt_number` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `ip_payments` ADD `client_id` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `ip_payments` ADD `receipt_id` VARCHAR(50) DEFAULT NULL;

INSERT INTO `ip_invoice_groups` (
  `invoice_group_name`,
  `invoice_group_identifier_format`,
  `invoice_group_next_id`,
  `invoice_group_left_pad`
) VALUES (
  'Receipts Default',
  'RCPT-{{{year}}}{{{id}}}',
  1,
  5
);

INSERT INTO `ip_settings` (`setting_key`, `setting_value`) VALUES ('default_payment_group', '5');