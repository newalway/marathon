INSERT INTO `setting_option` (`id`, `option_name`, `option_value`, `option_title`, `option_type`, `group_type`, `cat_type`, `updated_at`, `created_at`)
VALUES
	(1, 'contact_mail_address', 'support@zap-interactive.com', 'Recipients', 'text', 'Contact Us', 'email', NULL, '2017-11-20 11:35:20'),
	(2, 'contact_mail_name', 'Marathon', 'Sender name', 'text', 'Contact Us', 'email', NULL, '2017-11-20 11:35:20'),
	(3, 'order_mail_address', 'num@zap-interactive.com', 'Recipients', 'text', 'Order', 'email', NULL, '2017-11-20 11:35:20'),
	(4, 'order_mail_name', 'Marathon', 'Sender name', 'text', 'Order', 'email', NULL, '2017-11-20 11:35:20');

INSERT INTO `setting_option` (`id`,`option_name`,`option_value`,`option_title`,`option_type`,`group_type`,`cat_type`, `updated_at`, `created_at`) VALUES (7,'low_stock_report_status','true','Enable low stock notification','boolean','Low Stock Notification','email', NULL, '2017-11-20 11:35:20');
INSERT INTO `setting_option` (`id`,`option_name`,`option_value`,`option_title`,`option_type`,`group_type`,`cat_type`, `updated_at`, `created_at`) VALUES (8,'low_stock_report_min_qty','10','Low stock quantity','text','Low Stock Notification','email', NULL, '2017-11-20 11:35:20');
INSERT INTO `setting_option` (`id`,`option_name`,`option_value`,`option_title`,`option_type`,`group_type`,`cat_type`, `updated_at`, `created_at`) VALUES (9,'low_stock_report_mail_address','support@zap-interactive.com','Recipients','text','Low Stock Notification','email', NULL, '2017-11-20 11:35:20');
INSERT INTO `setting_option` (`id`,`option_name`,`option_value`,`option_title`,`option_type`,`group_type`,`cat_type`, `updated_at`, `created_at`) VALUES (10,'low_stock_report_mail_name','Marathon','Sender name','text','Low Stock Notification','email', NULL, '2017-11-20 11:35:20');


INSERT INTO `setting_option` (`id`, `option_name`, `option_value`, `option_title`, `option_type`, `group_type`, `cat_type`, `updated_at`, `created_at`)
VALUES
	(20, 'b2b_quotation_mail_address', 'support@zap-interactive.com', 'Recipients', 'text', 'Request for Quotations', 'email', NULL, '2017-11-20 11:35:20'),
	(21, 'b2b_quotation_mail_name', 'Marathon', 'Sender name', 'text', 'Request for Quotations', 'email', NULL, '2017-11-20 11:35:20'),
	(25, 'request_service_mail_address', 'support@zap-interactive.com', 'Recipients', 'text', 'Request Service', 'email', NULL, '2017-11-20 11:35:20'),
	(26, 'request_service_mail_name', 'Marathon', 'Sender name', 'text', 'Request Service', 'email', NULL, '2017-11-20 11:35:20');
