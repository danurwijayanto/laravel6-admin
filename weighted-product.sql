-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.7.19 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for ulil-spk
CREATE DATABASE IF NOT EXISTS `ulil-spk` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `ulil-spk`;

-- Dumping structure for table ulil-spk.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.failed_jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

-- Dumping structure for table ulil-spk.kelaslm
CREATE TABLE IF NOT EXISTS `kelaslm` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa` int(10) unsigned NOT NULL,
  `id_mapellm` int(10) unsigned NOT NULL,
  `nama_kelas` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jadwal` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelaslm_id_siswa_foreign` (`id_siswa`),
  KEY `kelaslm_id_mapellm_foreign` (`id_mapellm`),
  CONSTRAINT `kelaslm_id_mapellm_foreign` FOREIGN KEY (`id_mapellm`) REFERENCES `mapellm` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelaslm_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.kelaslm: ~0 rows (approximately)
/*!40000 ALTER TABLE `kelaslm` DISABLE KEYS */;
/*!40000 ALTER TABLE `kelaslm` ENABLE KEYS */;

-- Dumping structure for table ulil-spk.mapellm
CREATE TABLE IF NOT EXISTS `mapellm` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mapel` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_kelas` int(10) unsigned NOT NULL,
  `kuota_kelas` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mapellm_kode_mapel_unique` (`kode_mapel`),
  UNIQUE KEY `mapellm_nama_mapel_unique` (`nama_mapel`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.mapellm: ~4 rows (approximately)
/*!40000 ALTER TABLE `mapellm` DISABLE KEYS */;
/*!40000 ALTER TABLE `mapellm` ENABLE KEYS */;

-- Dumping structure for table ulil-spk.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.migrations: ~13 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2020_08_15_232324_create_permissions_table', 1),
	(5, '2020_08_15_232336_create_roles_table', 1),
	(6, '2020_08_15_232522_create_users_permissions_table', 1),
	(7, '2020_08_15_232859_create_users_roles_table', 1),
	(8, '2020_08_15_233103_create_roles_permissions_table', 1),
	(9, '2020_08_15_233616_add_column_user_roles', 1),
	(23, '2020_08_19_092240_create_mapel_l_m_table', 2),
	(24, '2020_08_19_092243_create_siswa_table', 2),
	(25, '2020_08_19_094254_create_pivot_siswa_mapellm_table', 2),
	(28, '2020_08_24_164050_change_vector_datatype_table_siswa', 3),
	(29, '2020_08_31_085951_drop_unique_table_nama_kelas_tabel_kelas_lm', 4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table ulil-spk.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.password_resets: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Dumping structure for table ulil-spk.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.permissions: ~0 rows (approximately)
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;

-- Dumping structure for table ulil-spk.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.roles: ~1 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'Super Admin', 'super_admin', NULL, NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table ulil-spk.roles_permissions
CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `roles_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `roles_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `roles_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.roles_permissions: ~0 rows (approximately)
/*!40000 ALTER TABLE `roles_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles_permissions` ENABLE KEYS */;

-- Dumping structure for table ulil-spk.siswa
CREATE TABLE IF NOT EXISTS `siswa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_siswa` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_raport` double(8,2) NOT NULL,
  `pilih_lm1` int(10) unsigned NOT NULL,
  `pilih_lm2` int(10) unsigned NOT NULL,
  `pilih_lm3` int(10) unsigned NOT NULL,
  `vektor_v1` decimal(8,4) DEFAULT NULL,
  `vektor_v2` decimal(8,4) DEFAULT NULL,
  `vektor_v3` decimal(8,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswa_nis_unique` (`nis`),
  KEY `siswa_pilih_lm1_foreign` (`pilih_lm1`),
  KEY `siswa_pilih_lm2_foreign` (`pilih_lm2`),
  KEY `siswa_pilih_lm3_foreign` (`pilih_lm3`),
  CONSTRAINT `siswa_pilih_lm1_foreign` FOREIGN KEY (`pilih_lm1`) REFERENCES `mapellm` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_pilih_lm2_foreign` FOREIGN KEY (`pilih_lm2`) REFERENCES `mapellm` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_pilih_lm3_foreign` FOREIGN KEY (`pilih_lm3`) REFERENCES `mapellm` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=857 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.siswa: ~107 rows (approximately)
/*!40000 ALTER TABLE `siswa` DISABLE KEYS */;
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;

-- Dumping structure for table ulil-spk.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ulil-spk.users: ~2 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role_id`) VALUES
	(1, 'admin', 'danurwijayanto@gmail.com', NULL, '$2y$10$ZZvD0ysjxM9N6s.TbaqMCuTCM78LChc33CMIpI6dSUoK19McFNPiG', NULL, '2020-08-16 00:06:43', '2020-08-19 02:27:27', 1),
	(3, 'mobinity.fx', 'mobinity.fx@gmail.com', NULL, '$2y$10$ZZvD0ysjxM9N6s.TbaqMCuTCM78LChc33CMIpI6dSUoK19McFNPiG', NULL, NULL, '2020-08-19 03:46:14', 1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
