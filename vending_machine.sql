-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `available_currency`;
CREATE TABLE `available_currency` (
  `id` int NOT NULL,
  `one_rs_coin` bigint unsigned NOT NULL DEFAULT '0',
  `two_rs_coin` bigint unsigned NOT NULL DEFAULT '0',
  `five_rs_coin` bigint unsigned NOT NULL DEFAULT '0',
  `ten_rs_coin` bigint unsigned NOT NULL DEFAULT '0',
  `one_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  `two_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  `five_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  `ten_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  `twenty_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  `fifty_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  `hundread_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  `two_hundred_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  `five_hundred_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  `two_thousand_rs_note` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `available_currency` (`id`, `one_rs_coin`, `two_rs_coin`, `five_rs_coin`, `ten_rs_coin`, `one_rs_note`, `two_rs_note`, `five_rs_note`, `ten_rs_note`, `twenty_rs_note`, `fifty_rs_note`, `hundread_rs_note`, `two_hundred_rs_note`, `five_hundred_rs_note`, `two_thousand_rs_note`) VALUES
(1,	21,	7,	4,	3,	7,	14,	0,	21,	8,	0,	0,	4,	0,	0);

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1,	'2014_10_12_000000_create_users_table',	1),
(2,	'2014_10_12_100000_create_password_resets_table',	1),
(3,	'2016_06_01_000001_create_oauth_auth_codes_table',	1),
(4,	'2016_06_01_000002_create_oauth_access_tokens_table',	1),
(5,	'2016_06_01_000003_create_oauth_refresh_tokens_table',	1),
(6,	'2016_06_01_000004_create_oauth_clients_table',	1),
(7,	'2016_06_01_000005_create_oauth_personal_access_clients_table',	1),
(8,	'2021_05_07_153551_create_products_table',	1),
(9,	'2021_05_08_025638_create_available_currency_table',	1),
(10,	'2021_05_09_113406_create_purchase_data_table',	1),
(11,	'2021_05_09_115504_create_purchased_items_table',	1);

DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `client_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('1e9502f7d0d15eb218a456e4aece559dacefa8374c2c6ab1f0be83a073f4de12878f2b14afcc823a',	1,	1,	'MyApp',	'[]',	0,	'2021-05-09 15:35:41',	'2021-05-09 15:35:41',	'2022-05-09 21:05:41');

DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `client_id` int NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1,	NULL,	'Laravel Personal Access Client',	'Gfegp5UnOgbjAwb7N32cbLvHHorYmtmAIXWA8I58',	'http://localhost',	1,	0,	0,	'2021-05-09 15:35:10',	'2021-05-09 15:35:10'),
(2,	NULL,	'Laravel Password Grant Client',	'3jeJQR6xxfMLrYCMlTMgxwdWh7tW5lMB0GNIQVOs',	'http://localhost',	0,	1,	0,	'2021-05-09 15:35:10',	'2021-05-09 15:35:10');

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
CREATE TABLE `oauth_personal_access_clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_personal_access_clients_client_id_index` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1,	1,	'2021-05-09 15:35:10',	'2021-05-09 15:35:10');

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `available_stock` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_code_unique` (`sku_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `sku_code`, `name`, `detail`, `price`, `available_stock`, `image`, `created_at`) VALUES
(3,	'Chk01',	'Chocolate Cookies',	'100 GM Per Pack',	'95.00',	'486',	'',	'2021-05-07 07:17:42'),
(4,	'ORG-01',	'Orange Biscuit',	'150 GM Per Pack',	'25.00',	'144',	'',	'2021-05-07 16:39:08'),
(5,	'Cake-01',	'Pinapple Cake',	'250 GM Per Pack',	'250.00',	'0',	'',	'2021-05-07 16:43:16'),
(7,	'Cake-02',	'Pinapple Cake',	'500 GM Per Pack',	'450.00',	'3',	'',	'2021-05-07 16:43:46'),
(10,	'Cake-03',	'Pinapple Cake',	'750 GM Per Pack',	'700.00',	'2',	'',	'2021-05-07 16:49:00'),
(11,	'Cake-04',	'Pinapple Cake',	'750 GM Per Pack',	'700.00',	'2',	'',	'2021-05-07 16:49:26'),
(12,	'Cake-07',	'Pinapple Cake',	'750 GM Per Pack',	'700.00',	'2',	'product_images/tTYgHBXDjSCqMvW5h0DEGH9jmYQwukVOOx1b0Qer.jpg',	'2021-05-08 19:53:46');

DROP TABLE IF EXISTS `purchase_data`;
CREATE TABLE `purchase_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payable_amt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submitted_currency_object` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `returned_currency_object` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `booking_status` enum('initiated','complete','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `purchase_data` (`id`, `booking_id`, `payable_amt`, `submitted_currency_object`, `returned_currency_object`, `booking_status`, `created_at`) VALUES
(1,	'60980179b3fa6',	'240',	'3-1-1-1-1-2-0-3-2-0-0-1-0-0',	'0-0-1-1-0-0-0-0-2-0-0-0-0-0',	'initiated',	'2021-05-09 15:36:25'),
(2,	'60980316a96a3',	'240',	'3-1-1-1-1-2-0-3-2-0-0-1-0-0',	'0-0-1-1-0-0-0-0-2-0-0-0-0-0',	'initiated',	'2021-05-09 15:43:18'),
(3,	'60980ffa7bf8c',	'240',	'3-1-1-1-1-2-0-3-2-0-0-1-0-0',	'0-0-1-1-0-0-0-0-2-0-0-0-0-0',	'initiated',	'2021-05-09 16:38:18'),
(4,	'6098166d3a49d',	'240',	'3-1-1-1-1-2-0-3-2-0-0-1-0-0',	'0-0-1-1-0-0-0-0-2-0-0-0-0-0',	'initiated',	'2021-05-09 17:05:49'),
(5,	'6098193aea13a',	'240',	'3-1-1-1-1-2-0-3-2-0-0-1-0-0',	'0-0-1-1-0-0-0-0-2-0-0-0-0-0',	'complete',	'2021-05-09 17:17:46'),
(6,	'60981a621b407',	'240',	'3-1-1-1-1-2-0-3-2-0-0-1-0-0',	'0-0-1-1-0-0-0-0-2-0-0-0-0-0',	'initiated',	'2021-05-09 17:22:42'),
(7,	'60981c6939d07',	'240',	'3-1-1-1-1-2-0-3-2-0-0-1-0-0',	'0-0-1-1-0-0-0-0-2-0-0-0-0-0',	'complete',	'2021-05-09 17:31:21'),
(8,	'60981dcde153c',	'240',	'3-1-1-1-1-2-0-3-2-0-0-1-0-0',	'0-0-1-1-0-0-0-0-2-0-0-0-0-0',	'cancelled',	'2021-05-09 17:37:17');

DROP TABLE IF EXISTS `purchased_items`;
CREATE TABLE `purchased_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `buy_price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `purchased_items_booking_id_foreign` (`booking_id`),
  KEY `purchased_items_product_id_foreign` (`product_id`),
  CONSTRAINT `purchased_items_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `purchase_data` (`id`),
  CONSTRAINT `purchased_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `purchased_items` (`id`, `booking_id`, `product_id`, `quantity`, `buy_price`, `created_at`) VALUES
(1,	1,	3,	2,	'95.00',	'2021-05-09 15:36:25'),
(2,	1,	4,	2,	'25.00',	'2021-05-09 15:36:25'),
(3,	2,	3,	2,	'95.00',	'2021-05-09 15:43:18'),
(4,	2,	4,	2,	'25.00',	'2021-05-09 15:43:18'),
(5,	3,	3,	2,	'95.00',	'2021-05-09 16:38:18'),
(6,	3,	4,	2,	'25.00',	'2021-05-09 16:38:18'),
(7,	4,	3,	2,	'95.00',	'2021-05-09 17:05:49'),
(8,	4,	4,	2,	'25.00',	'2021-05-09 17:05:49'),
(9,	5,	3,	2,	'95.00',	'2021-05-09 17:17:46'),
(10,	5,	4,	2,	'25.00',	'2021-05-09 17:17:46'),
(11,	6,	3,	2,	'95.00',	'2021-05-09 17:22:42'),
(12,	6,	4,	2,	'25.00',	'2021-05-09 17:22:42'),
(13,	7,	3,	2,	'95.00',	'2021-05-09 17:31:21'),
(14,	7,	4,	2,	'25.00',	'2021-05-09 17:31:21'),
(15,	8,	3,	2,	'95.00',	'2021-05-09 17:37:17'),
(16,	8,	4,	2,	'25.00',	'2021-05-09 17:37:17');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1,	'Vidhi Yewale',	'vidhi@pramodyewale.com',	NULL,	'$2y$10$e8lByOm/WBi3K5Dg3dTJ/uoVot1oKHCDPAHcJ3OEUrFPyeoLyZkLe',	NULL,	'2021-05-07 07:04:53',	'2021-05-07 07:04:53'),
(2,	'Vidhi Yewale',	'vidhi@yewale.com',	NULL,	'$2y$10$YR2c/Sh4tWCOIYF8GH9JYes4QsJsoS5sLKk8/mDhMq/O68YYTO5.O',	NULL,	'2021-05-08 19:46:57',	'2021-05-08 19:46:57');

-- 2021-05-09 17:40:54
