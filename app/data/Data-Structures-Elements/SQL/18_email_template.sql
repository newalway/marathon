
INSERT INTO `setting_option` (`id`, `option_name`, `option_value`, `option_title`, `option_type`, `group_type`, `cat_type`, `updated_at`, `created_at`, `param`)
VALUES
	(50, 'fos_resetting_email_subject', 'Reset Password', 'Subject', 'text', 'Resetting Password Email Template', 'email', '2019-04-02 16:07:28', '2019-04-02 13:33:10', NULL),
	(51, 'fos_resetting_email_message', 'Hello {first_name}!\r\n\r\nTo reset your password - please visit {confirmation_url}\r\n\r\nRegards,\r\nMarathon', 'Message', 'textarea', 'Resetting Password Email Template', 'email', '2019-04-02 17:27:18', '2019-04-02 13:33:10', 'a:4:{i:0;s:7:\"{email}\";i:1;s:12:\"{first_name}\";i:2;s:11:\"{last_name}\";i:3;s:18:\"{confirmation_url}\";}');
