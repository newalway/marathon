/*
-- Query: SELECT * FROM marathon.brand
LIMIT 0, 1000

-- Date: 2018-05-09 12:18
*/
INSERT INTO `brand` (`id`,`updated_at`,`created_at`,`position`,`image`) VALUES (1,'2018-05-09 09:50:16','2018-05-09 09:49:16',1,NULL);
INSERT INTO `brand` (`id`,`updated_at`,`created_at`,`position`,`image`) VALUES (2,'2018-05-09 09:50:16','2018-05-09 09:49:46',2,NULL);

INSERT INTO `brand_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (1,1,'Marathon',NULL,'en');
INSERT INTO `brand_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (2,1,'มาราธอน',NULL,'th');
INSERT INTO `brand_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (3,2,'SIGMA',NULL,'en');
INSERT INTO `brand_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (4,2,'ซิกม่า',NULL,'th');
