/*
-- Query: SELECT * FROM marathon.customer_group
LIMIT 0, 1000

-- Date: 2018-05-09 12:23
*/
INSERT INTO `customer_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (1,1,'2018-05-09 12:07:25','2018-05-09 11:32:43');
INSERT INTO `customer_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (2,2,'2018-05-09 12:07:25','2018-05-09 11:32:51');
INSERT INTO `customer_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (3,3,'2018-05-09 12:07:25','2018-05-09 11:33:00');
INSERT INTO `customer_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (4,4,'2018-05-09 12:07:25','2018-05-09 11:33:07');

INSERT INTO `customer_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (1,1,'Home Use','en');
INSERT INTO `customer_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (2,1,'ใช้งานที่บ้าน','th');
INSERT INTO `customer_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (3,2,'Light Commercial','en');
INSERT INTO `customer_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (4,2,'Light Commercial','th');
INSERT INTO `customer_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (5,3,'Commercial','en');
INSERT INTO `customer_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (6,3,'เชิงพาณิชย์','th');
INSERT INTO `customer_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (7,4,'Free Weight','en');
INSERT INTO `customer_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (8,4,'Free Weight','th');
