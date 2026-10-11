-- =====================================================================
-- Archer Monitor - Skema Database (MariaDB / MySQL, utf8mb4)
-- Dihasilkan dari migration Laravel; setara dengan `php artisan migrate`.
-- Catatan: panjang varchar default = 191 (Schema::defaultStringLength(191)
--          untuk kompatibilitas MariaDB/MySQL lama di shared hosting).
-- Urutan tabel mengikuti dependensi foreign key.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- CREATE DATABASE IF NOT EXISTS `archer` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `archer`;

-- ---------- Tabel bawaan Laravel ----------

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'athlete',
  `phone` varchar(30) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- ---------- Tabel domain panahan ----------

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `yoyo_references` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `level` decimal(4,1) NOT NULL,
  `vo2max` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `yoyo_references_level_unique` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `physical_tests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `tested_at` date NOT NULL,
  `venue` varchar(150) DEFAULT NULL,
  `yoyo_level` decimal(4,1) DEFAULT NULL,
  `yoyo_vo2` decimal(8,2) DEFAULT NULL,
  `yoyo_vo2_bm` decimal(8,2) DEFAULT NULL,
  `stork_right` decimal(8,2) DEFAULT NULL,
  `stork_right_bm` decimal(8,2) DEFAULT NULL,
  `stork_left` decimal(8,2) DEFAULT NULL,
  `stork_left_bm` decimal(8,2) DEFAULT NULL,
  `grip_right` decimal(8,2) DEFAULT NULL,
  `grip_right_bm` decimal(8,2) DEFAULT NULL,
  `grip_left` decimal(8,2) DEFAULT NULL,
  `grip_left_bm` decimal(8,2) DEFAULT NULL,
  `bench_pull` decimal(8,2) DEFAULT NULL,
  `bench_pull_bm` decimal(8,2) DEFAULT NULL,
  `core` decimal(8,2) DEFAULT NULL,
  `core_bm` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `physical_tests_athlete_id_tested_at_index` (`athlete_id`,`tested_at`),
  CONSTRAINT `physical_tests_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `technique_tests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `tested_at` date NOT NULL,
  `venue` varchar(150) DEFAULT NULL,
  `qualification` decimal(8,2) DEFAULT NULL,
  `qualification_bm` decimal(8,2) DEFAULT NULL,
  `elimination` decimal(8,2) DEFAULT NULL,
  `elimination_bm` decimal(8,2) DEFAULT NULL,
  `max_shooting` decimal(8,2) DEFAULT NULL,
  `max_shooting_bm` decimal(8,2) DEFAULT NULL,
  `bow_training` decimal(8,2) DEFAULT NULL,
  `bow_training_bm` decimal(8,2) DEFAULT NULL,
  `bow_draw` decimal(8,2) DEFAULT NULL,
  `bow_draw_bm` decimal(8,2) DEFAULT NULL,
  `battery` decimal(8,2) DEFAULT NULL,
  `battery_bm` decimal(8,2) DEFAULT NULL,
  `speed_shooting` decimal(8,2) DEFAULT NULL,
  `speed_shooting_bm` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `technique_tests_athlete_id_tested_at_index` (`athlete_id`,`tested_at`),
  CONSTRAINT `technique_tests_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `shoot_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint(20) unsigned NOT NULL,
  `log_date` date NOT NULL,
  `techniques` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`techniques`)),
  `shot_100` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shoot_logs_athlete_id_log_date_unique` (`athlete_id`,`log_date`),
  CONSTRAINT `shoot_logs_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Tabel RBAC (di-seed dari config/rbac.php) ----------

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `route_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pattern` varchar(150) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `sort` smallint(5) unsigned NOT NULL DEFAULT 0,
  `description` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route_permissions_pattern_unique` (`pattern`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `route` varchar(150) NOT NULL,
  `param` varchar(50) DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `sort` smallint(5) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
