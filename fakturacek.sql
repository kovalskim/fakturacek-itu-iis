-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `accountant_permission`;
CREATE TABLE `accountant_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `accountant_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `accountant_id` (`accountant_id`),
  CONSTRAINT `accountant_permission_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  CONSTRAINT `accountant_permission_ibfk_2` FOREIGN KEY (`accountant_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `cin` varchar(8) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `vat` varchar(12) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `zip` varchar(5) COLLATE utf8mb4_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `phone` varchar(13) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `clients` (`id`, `name`, `cin`, `vat`, `street`, `city`, `zip`, `email`, `phone`, `users_id`) VALUES
(1,	'Pepa z depa',	'12345679',	NULL,	'Radkovo 245',	'Plzeň',	'43245',	'pepa@zdepa.cz',	NULL,	2);

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `categories_id` int(11) NOT NULL,
  `items` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `price` float NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_id` (`categories_id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `user_street` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `user_city` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `user_zip` varchar(5) COLLATE utf8mb4_czech_ci NOT NULL,
  `user_cin` varchar(8) COLLATE utf8mb4_czech_ci NOT NULL,
  `user_vat` varchar(12) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `user_phone` varchar(13) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `client_street` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `client_city` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `client_zip` varchar(5) COLLATE utf8mb4_czech_ci NOT NULL,
  `client_cin` varchar(8) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `client_vat` varchar(12) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `client_phone` varchar(13) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `client_email` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` datetime DEFAULT NULL,
  `account_number` varchar(25) COLLATE utf8mb4_czech_ci NOT NULL,
  `variable_symbol` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `vat_note` int(11) NOT NULL,
  `footer_note` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `status` enum('unpaid','paid','canceled') COLLATE utf8mb4_czech_ci NOT NULL,
  `suma` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `invoices` (`id`, `users_id`, `user_name`, `user_street`, `user_city`, `user_zip`, `user_cin`, `user_vat`, `user_phone`, `user_email`, `client_id`, `client_name`, `client_street`, `client_city`, `client_zip`, `client_cin`, `client_vat`, `client_phone`, `client_email`, `created`, `due_date`, `account_number`, `variable_symbol`, `logo_path`, `vat_note`, `footer_note`, `status`, `suma`) VALUES
(1,	2,	'Martin Kovalski',	'Stará osada',	'Brno',	'51202',	'12345678',	NULL,	NULL,	'email@panapodnikatele.cz',	1,	'Pepa z depa',	'Radkovo 245',	'Plzeň',	'43245',	'12345679',	NULL,	NULL,	'pepa@zdepa.cz',	'2021-11-24 17:47:27',	'2021-12-08 18:47:27',	'123/2010',	'2021001',	NULL,	0,	'Jsem osoba zapsaná v rejstříku někde',	'unpaid',	4325);

DROP TABLE IF EXISTS `invoices_items`;
CREATE TABLE `invoices_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoices_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `count` float NOT NULL,
  `unit_price` float NOT NULL,
  `type` enum('hours','pieces') COLLATE utf8mb4_czech_ci NOT NULL,
  `suma` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_id` (`invoices_id`),
  CONSTRAINT `invoices_items_ibfk_1` FOREIGN KEY (`invoices_id`) REFERENCES `invoices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `invoices_items` (`id`, `invoices_id`, `name`, `count`, `unit_price`, `type`, `suma`) VALUES
(1,	1,	'Práce na silnici',	3,	700,	'hours',	2100),
(2,	1,	'Dloubaní se v nose',	2,	300,	'pieces',	600);

DROP TABLE IF EXISTS `setting_invoices`;
CREATE TABLE `setting_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_number` varchar(25) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `variable_symbol` enum('YYMM00','YY0000','YY000') COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `vat_note` int(11) NOT NULL DEFAULT 0,
  `footer_note` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `setting_invoices_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `setting_invoices` (`id`, `account_number`, `variable_symbol`, `logo_path`, `vat_note`, `footer_note`, `users_id`) VALUES
(1,	NULL,	NULL,	NULL,	0,	NULL,	2);

DROP TABLE IF EXISTS `texts`;
CREATE TABLE `texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `text` text COLLATE utf8mb4_czech_ci NOT NULL,
  `img_path` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `texts` (`id`, `type`, `text`, `img_path`) VALUES
(1,	'aboutus',	'<p><strong>kkasdnhasjlkd</strong></p>',	'www/img/aboutus.jpeg'),
(2,	'contact',	'<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam ornare wisi eu metus. Ut tempus purus at lorem. Nullam sit amet magna in magna gravida vehicula. Suspendisse sagittis ultrices augue. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis sapien nunc, commodo et, interdum suscipit, sollicitudin et, dolor. Nam libero tempore, cum soluta nobis est.</p>',	'www/img/contact.jpeg');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cin` varchar(8) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `vat` varchar(12) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `phone` varchar(13) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `hash` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `hash_validity` datetime DEFAULT NULL,
  `role` enum('admin','accountant','business') COLLATE utf8mb4_czech_ci NOT NULL,
  `street` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `zip` varchar(5) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `status` enum('new','active','banned') COLLATE utf8mb4_czech_ci NOT NULL DEFAULT 'new',
  `email_verification` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `users` (`id`, `cin`, `vat`, `name`, `email`, `phone`, `password`, `hash`, `hash_validity`, `role`, `street`, `city`, `zip`, `avatar_path`, `status`, `email_verification`) VALUES
(1,	NULL,	NULL,	'Admin',	'admin@fakturacek.cz',	NULL,	'$2y$10$EkpTYHKufe7jCAwEzYEr2OTa5tdPNGCRGF6fqufVte.jC73fvym1G',	NULL,	NULL,	'admin',	NULL,	NULL,	NULL,	NULL,	'active',	'2021-11-24 20:40:21'),
(2,	'61019836',	NULL,	'Business',	'business@fakturacek.cz',	NULL,	'$2y$10$UujM3C3lJFY4dlkuy88LteFX06bCNG8LGNSa9Rc5J9/qxavJ86eF.',	NULL,	NULL,	'business',	'Kolejní 66',	'Brno',	'45678',	NULL,	'active',	'2021-11-24 20:40:28'),
(3,	'71048065',	NULL,	'Accountant',	'accountant@fakturacek.cz',	'123456789',	'$2y$10$KUkctHpRXI71vM41yRI/Q.Sxm3FiYF3JX6tf88qBzdwbffeuiNQ32',	NULL,	NULL,	'accountant',	'test 55',	'Testova',	'12345',	NULL,	'active',	'2021-11-24 20:40:34');

DROP TABLE IF EXISTS `users_last_login`;
CREATE TABLE `users_last_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `users_last_login_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `users_last_password_change`;
CREATE TABLE `users_last_password_change` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `users_last_password_change_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


-- 2021-11-24 21:16:11
