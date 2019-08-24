/*
-- Query: SELECT * FROM marathon.power
LIMIT 0, 1000

-- Date: 2018-05-09 12:21
*/
INSERT INTO `power` (`id`,`updated_at`,`created_at`,`position`) VALUES (1,'2018-05-09 12:15:51','2018-05-09 12:15:10',1);
INSERT INTO `power` (`id`,`updated_at`,`created_at`,`position`) VALUES (2,'2018-05-09 12:15:51','2018-05-09 12:15:19',2);
INSERT INTO `power` (`id`,`updated_at`,`created_at`,`position`) VALUES (3,'2018-05-09 12:15:51','2018-05-09 12:15:27',3);
INSERT INTO `power` (`id`,`updated_at`,`created_at`,`position`) VALUES (4,'2018-05-09 12:15:51','2018-05-09 12:15:36',4);

INSERT INTO `power_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (1,1,'Electric',NULL,'en');
INSERT INTO `power_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (2,1,'ไฟฟ้า',NULL,'th');
INSERT INTO `power_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (3,2,'Self Generator',NULL,'en');
INSERT INTO `power_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (4,2,'Self Generator',NULL,'th');
INSERT INTO `power_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (5,3,'Hybrid',NULL,'en');
INSERT INTO `power_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (6,3,'ไฮบริด',NULL,'th');
INSERT INTO `power_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (7,4,'No power',NULL,'en');
INSERT INTO `power_translation` (`id`,`translatable_id`,`title`,`description`,`locale`) VALUES (8,4,'ไม่ใช้พลังงาน',NULL,'th');
