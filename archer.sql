/*
SQLyog Ultimate v13.1.1 (32 bit)
MySQL - 12.2.2-MariaDB-ubu2404 : Database - archer
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `anthropometries` */

DROP TABLE IF EXISTS `anthropometries`;

CREATE TABLE `anthropometries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `measured_at` date NOT NULL,
  `height_cm` decimal(5,1) DEFAULT NULL,
  `sitting_height_cm` decimal(5,1) DEFAULT NULL,
  `weight_kg` decimal(5,1) DEFAULT NULL,
  `leg_length_cm` decimal(5,1) DEFAULT NULL,
  `arm_length_cm` decimal(5,1) DEFAULT NULL,
  `arm_span_cm` decimal(5,1) DEFAULT NULL,
  `shoulder_width_cm` decimal(5,1) DEFAULT NULL,
  `hr_rest` smallint(5) unsigned DEFAULT NULL,
  `hr_max` smallint(5) unsigned DEFAULT NULL,
  `hr_training` smallint(5) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `anthropometries_athlete_id_measured_at_index` (`athlete_id`,`measured_at`),
  CONSTRAINT `anthropometries_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `anthropometries` */

insert  into `anthropometries`(`id`,`athlete_id`,`measured_at`,`height_cm`,`sitting_height_cm`,`weight_kg`,`leg_length_cm`,`arm_length_cm`,`arm_span_cm`,`shoulder_width_cm`,`hr_rest`,`hr_max`,`hr_training`,`created_at`,`updated_at`) values 
(1,1,'2026-01-15',170.0,88.0,62.0,98.0,70.0,170.0,42.0,72,200,120,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,2,'2026-01-15',168.0,86.0,58.0,95.0,68.0,166.0,40.0,76,205,122,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,3,'2026-01-15',162.0,84.0,52.0,92.0,66.0,160.0,38.0,74,198,118,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,4,'2026-01-15',158.0,82.0,48.0,90.0,64.0,156.0,37.0,78,200,120,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(5,5,'2026-01-15',173.0,89.0,66.0,100.0,72.0,174.0,43.0,70,205,124,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(6,6,'2026-01-15',160.0,83.0,50.0,91.0,65.0,158.0,38.0,75,199,119,'2026-10-11 13:57:04','2026-10-11 13:57:04');

/*Table structure for table `athletes` */

DROP TABLE IF EXISTS `athletes`;

CREATE TABLE `athletes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(191) NOT NULL,
  `birth_place` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('L','P') DEFAULT NULL,
  `origin` varchar(150) DEFAULT NULL,
  `division` varchar(30) DEFAULT NULL,
  `registration_type` enum('reguler','prestasi','terapi') NOT NULL DEFAULT 'reguler',
  `photo` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `athletes_code_unique` (`code`),
  KEY `athletes_user_id_foreign` (`user_id`),
  CONSTRAINT `athletes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `athletes` */

insert  into `athletes`(`id`,`user_id`,`code`,`name`,`birth_place`,`birth_date`,`gender`,`origin`,`division`,`registration_type`,`photo`,`is_active`,`created_at`,`updated_at`) values 
(1,3,'DUM1','ARYA PRATAMA',NULL,'2008-03-12','L','Kota Bekasi, Jawa Barat','Recurve','prestasi','1_kX6WSM7H.webp',1,'2026-10-11 13:57:04','2026-10-11 20:14:12'),
(2,NULL,'DUM2','BAGUS SETIAWAN',NULL,'2009-07-01','L','Kota Bekasi, Jawa Barat','Recurve','prestasi','2_7ZZ3vTeE.webp',1,'2026-10-11 13:57:04','2026-10-11 20:15:05'),
(3,NULL,'DUM3','CITRA LESTARI',NULL,'2008-11-20','P','Kota Bekasi, Jawa Barat','Recurve','prestasi',NULL,1,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,NULL,'DUM4','DINDA PERMATA',NULL,'2010-01-15','P','Kota Bekasi, Jawa Barat','Recurve','prestasi',NULL,1,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(5,NULL,'DUM5','EKO NUGROHO',NULL,'2007-05-09','L','Kota Bekasi, Jawa Barat','Compound','prestasi',NULL,1,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(6,NULL,'DUM6','FARAH AULIA',NULL,'2009-09-03','P','Kota Bekasi, Jawa Barat','Compound','prestasi',NULL,1,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(7,4,'ATL7','Hendrianto','Palembang','2000-01-01','L','Palembang','Recurve','reguler',NULL,1,'2026-10-11 20:32:19','2026-10-11 20:32:19'),
(8,6,'ATL8','at-tyo','Jakarta','1995-05-07','L','Jakarta','Recurve','reguler',NULL,1,'2026-10-20 03:17:10','2026-10-20 03:17:10'),
(9,7,'ATL9','archertyo','Jakarta','2002-06-07','L','Jakarta','Recurve','reguler',NULL,1,'2026-10-23 02:01:21','2026-10-23 02:01:21');

/*Table structure for table `equipments` */

DROP TABLE IF EXISTS `equipments`;

CREATE TABLE `equipments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `riser` varchar(150) DEFAULT NULL,
  `limbs` varchar(150) DEFAULT NULL,
  `sight` varchar(150) DEFAULT NULL,
  `scope` varchar(150) DEFAULT NULL,
  `plunger` varchar(150) DEFAULT NULL,
  `clicker` varchar(150) DEFAULT NULL,
  `arrow_rest` varchar(150) DEFAULT NULL,
  `stabilizer` varchar(150) DEFAULT NULL,
  `side_stabilizer` varchar(150) DEFAULT NULL,
  `weight` varchar(150) DEFAULT NULL,
  `string` varchar(150) DEFAULT NULL,
  `peep_sight` varchar(150) DEFAULT NULL,
  `arrow` varchar(150) DEFAULT NULL,
  `finger_tab_release` varchar(150) DEFAULT NULL,
  `stand` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `equipments_athlete_id_unique` (`athlete_id`),
  CONSTRAINT `equipments_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `equipments` */

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `load_tests` */

DROP TABLE IF EXISTS `load_tests`;

CREATE TABLE `load_tests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `tested_at` date NOT NULL,
  `exercise` varchar(80) NOT NULL,
  `weight_kg` decimal(6,1) NOT NULL,
  `reps` smallint(5) unsigned NOT NULL,
  `one_rm` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `load_tests_athlete_id_exercise_tested_at_index` (`athlete_id`,`exercise`,`tested_at`),
  CONSTRAINT `load_tests_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `load_tests` */

insert  into `load_tests`(`id`,`athlete_id`,`tested_at`,`exercise`,`weight_kg`,`reps`,`one_rm`,`created_at`,`updated_at`) values 
(1,1,'2026-01-15','Back Squat',60.0,8,75.98,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,1,'2026-01-15','Bench Press',40.0,6,47.99,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,1,'2026-01-15','Seated Rowing',45.0,10,59.99,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,2,'2026-01-15','Back Squat',45.0,10,59.99,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(5,2,'2026-01-15','Bench Press',30.0,8,37.99,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(6,2,'2026-01-15','Seated Rowing',35.0,12,48.99,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(7,3,'2026-01-15','Back Squat',50.0,8,63.32,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(8,3,'2026-01-15','Bench Press',30.0,6,35.99,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(9,3,'2026-01-15','Seated Rowing',40.0,10,53.32,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(10,4,'2026-01-15','Back Squat',35.0,10,46.66,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(11,4,'2026-01-15','Bench Press',22.0,8,27.86,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(12,4,'2026-01-15','Seated Rowing',30.0,12,41.99,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(13,5,'2026-01-15','Back Squat',65.0,8,82.32,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(14,5,'2026-01-15','Bench Press',45.0,6,53.99,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(15,5,'2026-01-15','Seated Rowing',48.0,10,63.98,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(16,6,'2026-01-15','Back Squat',40.0,10,53.32,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(17,6,'2026-01-15','Bench Press',25.0,8,31.66,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(18,6,'2026-01-15','Seated Rowing',35.0,11,47.82,'2026-10-11 13:57:04','2026-10-11 13:57:04');

/*Table structure for table `menus` */

DROP TABLE IF EXISTS `menus`;

CREATE TABLE `menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `route` varchar(150) NOT NULL,
  `param` varchar(50) DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `sort` smallint(5) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `menus` */

insert  into `menus`(`id`,`label`,`route`,`param`,`roles`,`sort`,`is_active`,`created_at`,`updated_at`) values 
(1,'Dashboard','dashboard.index',NULL,'[\"admin\",\"coach\",\"athlete\"]',0,1,'2026-10-11 13:57:04','2026-10-20 02:42:37'),
(2,'Atlet','athletes.index',NULL,'[\"admin\",\"coach\"]',1,1,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,'Head to Head','h2h.index',NULL,'[\"admin\",\"coach\"]',2,1,'2026-10-11 13:57:04','2026-10-20 02:42:46'),
(4,'Metrik & BM','metrics.index',NULL,'[\"admin\"]',3,1,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(5,'Akses (RBAC)','rbac.roles.index',NULL,'[\"admin\"]',4,1,'2026-10-11 13:57:04','2026-10-20 02:33:24'),
(6,'Profil Saya','athletes.show','self_athlete','[\"athlete\"]',7,1,'2026-10-11 13:57:04','2026-10-23 05:10:13'),
(7,'Buku Saku','shoot-log.edit','self_athlete','[\"athlete\"]',8,1,'2026-10-11 13:57:04','2026-10-23 05:10:13'),
(9,'Pengguna','users.index',NULL,'[\"admin\"]',4,1,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(10,'Pembayaran','payment.index',NULL,'[\"admin\"]',5,1,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(12,'Peralatan Saya','equipment.edit','self_athlete','[\"athlete\"]',9,1,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(13,'Pembayaran','payment.my',NULL,'[\"athlete\"]',10,1,'2026-10-23 05:10:13','2026-10-23 05:10:13');

/*Table structure for table `metrics` */

DROP TABLE IF EXISTS `metrics`;

CREATE TABLE `metrics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `domain` enum('physical','technique') NOT NULL,
  `group_key` varchar(40) NOT NULL,
  `group_label` varchar(80) NOT NULL,
  `key` varchar(40) NOT NULL,
  `label` varchar(150) NOT NULL,
  `unit` varchar(30) DEFAULT NULL,
  `step` varchar(10) NOT NULL DEFAULT '0.01',
  `bm_l` decimal(10,2) DEFAULT NULL,
  `bm_p` decimal(10,2) DEFAULT NULL,
  `is_promoted` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `metrics_domain_key_unique` (`domain`,`key`),
  KEY `metrics_domain_is_active_sort_index` (`domain`,`is_active`,`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `metrics` */

insert  into `metrics`(`id`,`domain`,`group_key`,`group_label`,`key`,`label`,`unit`,`step`,`bm_l`,`bm_p`,`is_promoted`,`is_active`,`sort`,`created_at`,`updated_at`) values 
(1,'physical','endurance','Endurance','yoyo_vo2','Yo-Yo IR1 (VO2max, ml/kg/mnt)',NULL,'0.01',56.56,49.84,1,1,0,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,'physical','balance','Balance','stork_right','Standing Stork Kanan (detik)',NULL,'0.01',60.00,50.00,0,1,1,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,'physical','balance','Balance','stork_left','Standing Stork Kiri (detik)',NULL,'0.01',60.00,50.00,0,1,2,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,'physical','strength','Strength','grip_right','Grip Strength Kanan (kg)',NULL,'0.01',57.50,41.40,0,1,3,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(5,'physical','strength','Strength','grip_left','Grip Strength Kiri (kg)',NULL,'0.01',57.50,41.40,0,1,4,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(6,'physical','strength','Strength','bench_pull','Bench Pull (repetisi)',NULL,'0.01',30.00,20.00,0,1,5,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(7,'physical','strength','Strength','core','Core Stability (level)',NULL,'0.01',12.00,12.00,0,1,6,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(8,'technique','scoring','Scoring','qualification','Skor Kualifikasi',NULL,'0.01',625.00,NULL,1,1,0,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(9,'technique','scoring','Scoring','elimination','Skor Eliminasi',NULL,'0.01',135.00,NULL,0,1,1,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(10,'technique','specific_strength','Specific Strength','max_shooting','Maximal Shooting (AP)',NULL,'0.01',350.00,NULL,0,1,2,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(11,'technique','specific_strength','Specific Strength','bow_training','Bow Training (detik)',NULL,'0.01',75.00,NULL,0,1,3,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(12,'technique','specific_strength','Specific Strength','bow_draw','Bow Draw Max Quantity (repetisi)',NULL,'0.01',37.00,NULL,0,1,4,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(13,'technique','specific_strength','Specific Strength','battery','Battery Test Archery (level)',NULL,'0.01',6.00,NULL,0,1,5,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(14,'technique','specific_speed','Specific Speed','speed_shooting','Speed Shooting (AP)',NULL,'0.01',7.00,NULL,0,1,6,'2026-10-11 13:57:04','2026-10-11 13:57:04');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2026_10_11_000001_create_athletes_table',1),
(6,'2026_10_11_000002_create_rbac_tables',1),
(7,'2026_10_11_000003_create_metrics_table',1),
(8,'2026_10_23_000001_create_payments_table',2),
(9,'2026_10_23_000002_add_is_active_to_users_table',3);

/*Table structure for table `monthly_scores` */

DROP TABLE IF EXISTS `monthly_scores`;

CREATE TABLE `monthly_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `score` smallint(5) unsigned DEFAULT NULL,
  `score_note` varchar(60) DEFAULT NULL,
  `venue` varchar(150) DEFAULT NULL,
  `analysis` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `monthly_scores_athlete_id_year_month_index` (`athlete_id`,`year`,`month`),
  CONSTRAINT `monthly_scores_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `monthly_scores` */

insert  into `monthly_scores`(`id`,`athlete_id`,`year`,`month`,`score`,`score_note`,`venue`,`analysis`,`created_at`,`updated_at`) values 
(1,1,2026,1,589,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,1,2026,2,608,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,1,2026,3,620,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,1,2026,4,641,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(5,1,2026,5,650,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(6,2,2026,1,566,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(7,2,2026,2,590,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(8,2,2026,3,566,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(9,2,2026,4,585,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(10,3,2026,1,625,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(11,3,2026,2,632,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(12,3,2026,3,641,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(13,3,2026,4,647,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(14,3,2026,5,639,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(15,4,2026,1,517,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(16,4,2026,2,589,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(17,4,2026,3,583,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(18,4,2026,4,584,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(19,5,2026,1,657,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(20,5,2026,2,633,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(21,5,2026,3,654,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(22,5,2026,4,653,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(23,6,2026,1,580,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(24,6,2026,2,575,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(25,6,2026,3,582,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(26,6,2026,4,601,NULL,'Lapangan Panahan Synergi',NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04');

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `payments` */

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `proof` varchar(191) DEFAULT NULL,
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `note` varchar(500) DEFAULT NULL,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_athlete_id_year_month_unique` (`athlete_id`,`year`,`month`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  KEY `payments_year_month_status_index` (`year`,`month`,`status`),
  CONSTRAINT `payments_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payments` */

insert  into `payments`(`id`,`athlete_id`,`year`,`month`,`amount`,`proof`,`status`,`note`,`verified_by`,`verified_at`,`created_at`,`updated_at`) values 
(1,9,2026,10,150000.00,'9_202610_1SSH21.webp','verified',NULL,1,'2026-10-23 05:11:50','2026-10-23 05:11:34','2026-10-23 05:11:50');

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

/*Table structure for table `physical_tests` */

DROP TABLE IF EXISTS `physical_tests`;

CREATE TABLE `physical_tests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `tested_at` date NOT NULL,
  `venue` varchar(150) DEFAULT NULL,
  `yoyo_level` decimal(4,1) DEFAULT NULL,
  `results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `yoyo_vo2_val` decimal(8,2) GENERATED ALWAYS AS (cast(json_value(`results`,'$.yoyo_vo2.result') as decimal(8,2))) VIRTUAL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `physical_tests_athlete_id_tested_at_index` (`athlete_id`,`tested_at`),
  KEY `physical_tests_yoyo_vo2_val_index` (`yoyo_vo2_val`),
  CONSTRAINT `physical_tests_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `physical_tests` */

insert  into `physical_tests`(`id`,`athlete_id`,`tested_at`,`venue`,`yoyo_level`,`results`,`created_at`,`updated_at`) values 
(1,1,'2026-01-15',NULL,18.5,'{\"yoyo_vo2\":{\"result\":54,\"bm\":56.56},\"stork_right\":{\"result\":58,\"bm\":60},\"stork_left\":{\"result\":56,\"bm\":60},\"grip_right\":{\"result\":55,\"bm\":57.5},\"grip_left\":{\"result\":52,\"bm\":57.5},\"bench_pull\":{\"result\":33,\"bm\":30},\"core\":{\"result\":12,\"bm\":12}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,2,'2026-01-15',NULL,15.5,'{\"yoyo_vo2\":{\"result\":44,\"bm\":56.56},\"stork_right\":{\"result\":40,\"bm\":60},\"stork_left\":{\"result\":38,\"bm\":60},\"grip_right\":{\"result\":42,\"bm\":57.5},\"grip_left\":{\"result\":40,\"bm\":57.5},\"bench_pull\":{\"result\":24,\"bm\":30},\"core\":{\"result\":10,\"bm\":12}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,3,'2026-01-15',NULL,16.5,'{\"yoyo_vo2\":{\"result\":48,\"bm\":49.84},\"stork_right\":{\"result\":50,\"bm\":50},\"stork_left\":{\"result\":48,\"bm\":50},\"grip_right\":{\"result\":40,\"bm\":41.4},\"grip_left\":{\"result\":38,\"bm\":41.4},\"bench_pull\":{\"result\":22,\"bm\":20},\"core\":{\"result\":12,\"bm\":12}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,4,'2026-01-15',NULL,13.5,'{\"yoyo_vo2\":{\"result\":40,\"bm\":49.84},\"stork_right\":{\"result\":35,\"bm\":50},\"stork_left\":{\"result\":33,\"bm\":50},\"grip_right\":{\"result\":30,\"bm\":41.4},\"grip_left\":{\"result\":28,\"bm\":41.4},\"bench_pull\":{\"result\":15,\"bm\":20},\"core\":{\"result\":9,\"bm\":12}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(5,5,'2026-01-15',NULL,19.0,'{\"yoyo_vo2\":{\"result\":56,\"bm\":56.56},\"stork_right\":{\"result\":60,\"bm\":60},\"stork_left\":{\"result\":60,\"bm\":60},\"grip_right\":{\"result\":57,\"bm\":57.5},\"grip_left\":{\"result\":55,\"bm\":57.5},\"bench_pull\":{\"result\":30,\"bm\":30},\"core\":{\"result\":12,\"bm\":12}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(6,6,'2026-01-15',NULL,14.5,'{\"yoyo_vo2\":{\"result\":42,\"bm\":49.84},\"stork_right\":{\"result\":42,\"bm\":50},\"stork_left\":{\"result\":40,\"bm\":50},\"grip_right\":{\"result\":34,\"bm\":41.4},\"grip_left\":{\"result\":32,\"bm\":41.4},\"bench_pull\":{\"result\":17,\"bm\":20},\"core\":{\"result\":11,\"bm\":12}}','2026-10-11 13:57:04','2026-10-11 13:57:04');

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_staff` tinyint(1) NOT NULL DEFAULT 0,
  `sort` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`code`,`name`,`is_staff`,`sort`,`created_at`,`updated_at`) values 
(1,'admin','Administrator',1,0,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,'coach','Pelatih',1,1,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,'athlete','Atlet',0,2,'2026-10-11 13:57:04','2026-10-11 13:57:04');

/*Table structure for table `route_permissions` */

DROP TABLE IF EXISTS `route_permissions`;

CREATE TABLE `route_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pattern` varchar(150) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `sort` smallint(5) unsigned NOT NULL DEFAULT 0,
  `description` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route_permissions_pattern_unique` (`pattern`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `route_permissions` */

insert  into `route_permissions`(`id`,`pattern`,`roles`,`sort`,`description`,`created_at`,`updated_at`) values 
(1,'rbac.*','[\"admin\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,'metrics.*','[\"admin\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,'athletes.*','[\"admin\",\"coach\",\"athlete\"]',3,NULL,'2026-10-11 13:57:04','2026-10-23 02:32:45'),
(9,'equipment.*','[\"admin\",\"coach\",\"athlete\"]',12,NULL,'2026-10-11 13:57:04','2026-10-23 05:10:13'),
(10,'records.*','[\"admin\",\"coach\"]',13,NULL,'2026-10-11 13:57:04','2026-10-23 05:10:13'),
(11,'h2h.*','[\"admin\",\"coach\"]',14,NULL,'2026-10-11 13:57:04','2026-10-23 05:10:13'),
(12,'users.*','[\"admin\"]',2,NULL,'2026-10-20 00:57:11','2026-10-23 05:10:13'),
(13,'dashboard.*','[\"*\"]',15,NULL,NULL,'2026-10-23 05:10:13'),
(15,'shoot-log.edit','[\"admin\",\"coach\",\"athlete\"]',12,'edit buku saku','2026-10-23 02:16:00','2026-10-23 02:16:00'),
(17,'beban.*','[\"*\"]',19,NULL,'2026-10-23 02:37:53','2026-10-23 08:15:57'),
(18,'payment.index','[\"admin\"]',3,NULL,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(19,'payment.verify','[\"admin\"]',4,NULL,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(20,'payment.reject','[\"admin\"]',5,NULL,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(21,'athletes.destroy','[\"admin\"]',6,NULL,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(22,'athletes.index','[\"admin\",\"coach\"]',7,NULL,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(23,'athletes.create','[\"admin\",\"coach\"]',8,NULL,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(24,'athletes.store','[\"admin\",\"coach\"]',9,NULL,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(25,'athletes.edit','[\"admin\",\"coach\",\"athlete\"]',10,NULL,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(26,'athletes.update','[\"admin\",\"coach\",\"athlete\"]',11,NULL,'2026-10-23 05:10:13','2026-10-23 05:10:13'),
(27,'athletes.show','[\"*\"]',17,NULL,'2026-10-23 05:10:13','2026-10-23 08:15:57'),
(28,'shoot-log.*','[\"*\"]',18,NULL,'2026-10-23 05:10:13','2026-10-23 08:15:57'),
(29,'payment.my','[\"athlete\"]',20,NULL,'2026-10-23 05:10:13','2026-10-23 08:15:57'),
(30,'payment.upload','[\"athlete\"]',21,NULL,'2026-10-23 05:10:13','2026-10-23 08:15:57'),
(31,'payment.proof','[\"*\"]',22,NULL,'2026-10-23 05:10:13','2026-10-23 08:15:57'),
(32,'account.*','[\"*\"]',16,NULL,'2026-10-23 08:15:57','2026-10-23 08:15:57');

/*Table structure for table `shoot_logs` */

DROP TABLE IF EXISTS `shoot_logs`;

CREATE TABLE `shoot_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `log_date` date NOT NULL,
  `techniques` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `shot_100` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shoot_logs_athlete_id_log_date_unique` (`athlete_id`,`log_date`),
  CONSTRAINT `shoot_logs_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `shoot_logs` */

insert  into `shoot_logs`(`id`,`athlete_id`,`log_date`,`techniques`,`shot_100`,`notes`,`created_at`,`updated_at`) values 
(1,1,'2026-10-05','[\"a\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,1,'2026-10-06','[\"a\",\"b\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,1,'2026-10-07','[\"a\",\"b\",\"c\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,1,'2026-10-08','[\"a\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(5,2,'2026-10-05','[\"a\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(6,2,'2026-10-06','[\"a\",\"b\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(7,2,'2026-10-07','[\"a\",\"b\",\"c\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(8,2,'2026-10-08','[\"a\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(9,3,'2026-10-05','[\"a\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(10,3,'2026-10-06','[\"a\",\"b\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(11,3,'2026-10-07','[\"a\",\"b\",\"c\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(12,3,'2026-10-08','[\"a\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(13,4,'2026-10-05','[\"a\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(14,4,'2026-10-06','[\"a\",\"b\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(15,4,'2026-10-07','[\"a\",\"b\",\"c\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(16,4,'2026-10-08','[\"a\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(17,5,'2026-10-05','[\"a\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(18,5,'2026-10-06','[\"a\",\"b\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(19,5,'2026-10-07','[\"a\",\"b\",\"c\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(20,5,'2026-10-08','[\"a\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(21,6,'2026-10-05','[\"a\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(22,6,'2026-10-06','[\"a\",\"b\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(23,6,'2026-10-07','[\"a\",\"b\",\"c\"]',1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(24,6,'2026-10-08','[\"a\"]',0,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(25,1,'2026-10-09','[\"a\"]',0,NULL,'2026-10-11 14:30:28','2026-10-11 14:30:28'),
(26,1,'2026-10-10','[\"a\"]',0,NULL,'2026-10-11 14:30:28','2026-10-11 14:30:28');

/*Table structure for table `technique_tests` */

DROP TABLE IF EXISTS `technique_tests`;

CREATE TABLE `technique_tests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `tested_at` date NOT NULL,
  `venue` varchar(150) DEFAULT NULL,
  `results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `qualification_val` decimal(8,2) GENERATED ALWAYS AS (cast(json_value(`results`,'$.qualification.result') as decimal(8,2))) VIRTUAL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `technique_tests_athlete_id_tested_at_index` (`athlete_id`,`tested_at`),
  KEY `technique_tests_qualification_val_index` (`qualification_val`),
  CONSTRAINT `technique_tests_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `technique_tests` */

insert  into `technique_tests`(`id`,`athlete_id`,`tested_at`,`venue`,`results`,`created_at`,`updated_at`) values 
(1,1,'2026-01-15',NULL,'{\"qualification\":{\"result\":650,\"bm\":625},\"elimination\":{\"result\":130,\"bm\":135},\"max_shooting\":{\"result\":430,\"bm\":350},\"bow_training\":{\"result\":60,\"bm\":75},\"bow_draw\":{\"result\":49,\"bm\":37},\"battery\":{\"result\":8,\"bm\":6},\"speed_shooting\":{\"result\":8,\"bm\":7}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,2,'2026-01-15',NULL,'{\"qualification\":{\"result\":560,\"bm\":625},\"elimination\":{\"result\":118,\"bm\":135},\"max_shooting\":{\"result\":400,\"bm\":350},\"bow_training\":{\"result\":35,\"bm\":75},\"bow_draw\":{\"result\":41,\"bm\":37},\"battery\":{\"result\":7,\"bm\":6},\"speed_shooting\":{\"result\":8,\"bm\":7}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,3,'2026-01-15',NULL,'{\"qualification\":{\"result\":640,\"bm\":625},\"elimination\":{\"result\":131,\"bm\":135},\"max_shooting\":{\"result\":460,\"bm\":350},\"bow_training\":{\"result\":66,\"bm\":75},\"bow_draw\":{\"result\":42,\"bm\":37},\"battery\":{\"result\":8,\"bm\":6},\"speed_shooting\":{\"result\":9,\"bm\":7}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,4,'2026-01-15',NULL,'{\"qualification\":{\"result\":517,\"bm\":625},\"elimination\":{\"result\":120,\"bm\":135},\"max_shooting\":{\"result\":520,\"bm\":350},\"bow_training\":{\"result\":46,\"bm\":75},\"bow_draw\":{\"result\":46,\"bm\":37},\"battery\":{\"result\":8,\"bm\":6},\"speed_shooting\":{\"result\":9,\"bm\":7}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(5,5,'2026-01-15',NULL,'{\"qualification\":{\"result\":660,\"bm\":625},\"elimination\":{\"result\":134,\"bm\":135},\"max_shooting\":{\"result\":510,\"bm\":350},\"bow_training\":{\"result\":54,\"bm\":75},\"bow_draw\":{\"result\":49,\"bm\":37},\"battery\":{\"result\":8,\"bm\":6},\"speed_shooting\":{\"result\":9,\"bm\":7}}','2026-10-11 13:57:04','2026-10-11 13:57:04'),
(6,6,'2026-01-15',NULL,'{\"qualification\":{\"result\":580,\"bm\":625},\"elimination\":{\"result\":125,\"bm\":135},\"max_shooting\":{\"result\":460,\"bm\":350},\"bow_training\":{\"result\":50,\"bm\":75},\"bow_draw\":{\"result\":40,\"bm\":37},\"battery\":{\"result\":7,\"bm\":6},\"speed_shooting\":{\"result\":8,\"bm\":7}}','2026-10-11 13:57:04','2026-10-11 13:57:04');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'athlete',
  `phone` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`),
  KEY `users_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`email_verified_at`,`password`,`role`,`phone`,`is_active`,`remember_token`,`created_at`,`updated_at`) values 
(1,'Super Admin','admin@archer.test',NULL,'$2y$12$volq2Od1VCz0i9nb4.7/kOXg0VRtFoWAkm3D6wQ.ZJB.ttjFikFre','admin',NULL,1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(2,'Pelatih Demo','coach@archer.test',NULL,'$2y$12$G6l7B/y6xnczInOcIdDPW.8aqFPtNxZxcrJG3g6362mHEAdHfXfD6','coach',NULL,1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(3,'Atlet Demo','athlete@archer.test',NULL,'$2y$12$RKga0ycxcq.XGD2Bwq0IN.bz.RG6Ogx4yNbWAR0DCS4D8LW84Qyzi','athlete',NULL,1,NULL,'2026-10-11 13:57:04','2026-10-11 13:57:04'),
(4,'Hendrianto','hendrianto23@gmail.com',NULL,'$2y$12$bocGjoIh4N7Lmkeg3FdL7.kfY9lbFYlgYzQMrJY84Eaqz0epwu3ZG','athlete','081282213499',1,NULL,'2026-10-11 20:32:19','2026-10-11 20:32:19'),
(5,'Prasetyo','tyo.youichi@gmail.com',NULL,'$2y$12$ArwatkigHB3ihVywADfMmesCFuI2NTicPSWpTdf.CzUDeo2dG94bO','admin',NULL,1,'JbcqRgPcUiLyBvv35C8Oo82FHPZuveCROYEUgG1oO4hFPp2S8BuuOApKrBnn','2026-10-20 02:30:13','2026-10-20 02:30:13'),
(6,'at-tyo','tyodevprasetyo@gmail.com',NULL,'$2y$12$eAFprKT9NonLOiFlqc1BZecLRS0XOI.zIMhtZbLIJCv84WtyxRQ1C','athlete','082125393056',1,NULL,'2026-10-20 03:17:10','2026-10-20 03:17:10'),
(7,'archertyo','tyo.youichi2@gmail.com',NULL,'$2y$12$jsAwJFjARUVh5vFsMAguaOH4n5o.eqygVJMZp5PYFDFWm36BflfDq','athlete','082125393056',1,NULL,'2026-10-23 02:01:21','2026-10-23 02:01:21');

/*Table structure for table `yoyo_references` */

DROP TABLE IF EXISTS `yoyo_references`;

CREATE TABLE `yoyo_references` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `level` decimal(4,1) NOT NULL,
  `vo2max` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `yoyo_references_level_unique` (`level`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `yoyo_references` */

insert  into `yoyo_references`(`id`,`level`,`vo2max`) values 
(1,5.1,36.74),
(2,9.1,37.07),
(3,11.1,37.41),
(4,11.2,37.74),
(5,12.1,38.08),
(6,12.2,38.42),
(7,12.3,38.75),
(8,13.1,39.09),
(9,13.2,39.42),
(10,13.3,39.76),
(11,13.4,40.10),
(12,14.1,40.43),
(13,14.2,40.77),
(14,14.3,41.10),
(15,14.4,41.44),
(16,14.5,41.78),
(17,14.6,42.11),
(18,14.7,42.45),
(19,14.8,42.78),
(20,15.1,43.12),
(21,15.2,43.46),
(22,15.3,43.79),
(23,15.4,44.13),
(24,15.5,44.46),
(25,15.6,44.80),
(26,15.7,45.14),
(27,15.8,45.47),
(28,16.1,45.81),
(29,16.2,46.14),
(30,16.3,46.48),
(31,16.4,46.82),
(32,16.5,47.15),
(33,16.6,47.49),
(34,16.7,47.82),
(35,16.8,48.16),
(36,17.1,48.50),
(37,17.2,48.83),
(38,17.3,49.17),
(39,17.4,49.50),
(40,17.5,49.84),
(41,17.6,50.18),
(42,17.7,50.51),
(43,17.8,50.85),
(44,18.1,51.18),
(45,18.2,51.52),
(46,18.3,51.86),
(47,18.4,52.19),
(48,18.5,52.53),
(49,18.6,52.86),
(50,18.7,53.20),
(51,18.8,53.54),
(52,19.1,53.87),
(53,19.2,54.21),
(54,19.3,54.54),
(55,19.4,54.88),
(56,19.5,55.22),
(57,19.6,55.55),
(58,19.7,55.89),
(59,19.8,56.22),
(60,20.1,56.56),
(61,20.2,56.90),
(62,20.3,57.23),
(63,20.4,57.57),
(64,20.5,57.90),
(65,20.6,58.24),
(66,20.7,58.58),
(67,20.8,58.91),
(68,21.1,59.25),
(69,21.2,59.58),
(70,21.3,59.92),
(71,21.4,60.26),
(72,21.5,60.59),
(73,21.6,60.93),
(74,21.7,61.26),
(75,21.8,61.60),
(76,22.1,61.94),
(77,22.2,62.27),
(78,22.3,62.61),
(79,22.4,62.94),
(80,22.5,63.28),
(81,22.6,63.62),
(82,22.7,63.95),
(83,22.8,64.29),
(84,23.1,64.62),
(85,23.2,64.96),
(86,23.3,65.30),
(87,23.4,65.63),
(88,23.5,65.97),
(89,23.6,66.30),
(90,23.7,66.64),
(91,23.8,66.98);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
