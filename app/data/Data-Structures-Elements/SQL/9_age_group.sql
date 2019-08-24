/*
-- Query: SELECT * FROM marathon.age_group
LIMIT 0, 1000

-- Date: 2018-05-09 12:24
*/
INSERT INTO `age_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (1,1,'2018-05-09 12:06:48','2018-05-09 11:47:59');
INSERT INTO `age_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (2,2,'2018-05-09 12:06:48','2018-05-09 11:48:18');
INSERT INTO `age_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (3,3,'2018-05-09 12:06:48','2018-05-09 11:48:27');
INSERT INTO `age_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (4,4,'2018-05-09 12:06:48','2018-05-09 11:48:36');
INSERT INTO `age_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (5,5,'2018-05-09 12:06:48','2018-05-09 11:48:44');
INSERT INTO `age_group` (`id`,`position`,`updated_at`,`created_at`) VALUES (6,6,'2018-05-09 12:06:48','2018-05-09 11:48:50');


INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (1,1,'Teens(15-25)','en');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (2,1,'วัยรุ่น(15-25)','th');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (3,2,'Adult(25-40)','en');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (4,2,'ผู้ใหญ่(25-40)','th');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (5,3,'Middle-aged(40-65)','en');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (6,3,'วัยกลางคน(40-65)','th');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (7,4,'Elderly people(65+)','en');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (8,4,'ผู้สูงอายุ(65+)','th');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (9,5,'Physical needs','en');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (10,5,'ผู้ต้องการกายภาพ','th');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (11,6,'Athlete','en');
INSERT INTO `age_group_translation` (`id`,`translatable_id`,`title`,`locale`) VALUES (12,6,'นักกีฬา','th');
