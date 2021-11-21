-- Adminer 4.8.1 MySQL 5.5.5-10.4.21-MariaDB dump

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

INSERT INTO `clients` (`id`, `name`, `cin`, `street`, `city`, `zip`, `email`, `phone`, `users_id`) VALUES
(1,	'Radek Jůzl',	'',	'Kam 204',	'Nikam',	'39601',	'radekjuzl@seznam.cz',	'',	4),
(2,	'Pepa Novák',	'',	'sadasfd 55',	'asfa',	'16402',	'',	'7856541123',	4);

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
  `variable_symbol` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `status` enum('unpaid','paid','canceled') COLLATE utf8mb4_czech_ci NOT NULL,
  `users_clients_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `users_clients_id` (`users_clients_id`),
  CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`users_clients_id`) REFERENCES `users_clients` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `invoices_items`;
CREATE TABLE `invoices_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoices_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `count` float NOT NULL,
  `unit_price` float NOT NULL,
  `type` enum('hours','pieces') COLLATE utf8mb4_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_id` (`invoices_id`),
  CONSTRAINT `invoices_items_ibfk_1` FOREIGN KEY (`invoices_id`) REFERENCES `invoices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `setting_invoices`;
CREATE TABLE `setting_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_number` varchar(25) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `variable_symbol` enum('yymmxx','yyxxxx','yyxxx') COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `vat` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `setting_invoices_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `setting_invoices` (`id`, `account_number`, `variable_symbol`, `logo_path`, `vat`, `users_id`) VALUES
(1,	NULL,	NULL,	NULL,	NULL,	9),
(2,	NULL,	NULL,	NULL,	NULL,	5),
(3,	'1234567890/0600',	'yyxxxx',	'www/logo/5NF8D73LAG.jpeg',	'Jsem plátce DPH.',	4),
(4,	NULL,	NULL,	NULL,	NULL,	2);

DROP TABLE IF EXISTS `texts`;
CREATE TABLE `texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `text` text COLLATE utf8mb4_czech_ci NOT NULL,
  `img_path` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `texts` (`id`, `type`, `text`, `img_path`) VALUES
(1,	'aboutus',	'kkasdnhasjlkd',	'www/img/aboutus.jpeg'),
(2,	'contact',	'<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam ornare wisi eu metus. Ut tempus purus at lorem. Nullam sit amet magna in magna gravida vehicula. Suspendisse sagittis ultrices augue. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis sapien nunc, commodo et, interdum suscipit, sollicitudin et, dolor. Nam libero tempore, cum soluta nobis est.</p>\n',	'www/img/contact.jpeg');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cin` varchar(8) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `phone` varchar(13) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `hash` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `hash_validity` datetime DEFAULT NULL,
  `role` enum('admin','accountant','business') COLLATE utf8mb4_czech_ci NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `street` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `zip` varchar(5) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `verified` int(11) NOT NULL DEFAULT 0,
  `avatar_path` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `users` (`id`, `cin`, `name`, `email`, `phone`, `password`, `hash`, `hash_validity`, `role`, `deleted`, `street`, `city`, `zip`, `verified`, `avatar_path`) VALUES
(1,	NULL,	'Admin',	'admin@fakturacek.cz',	'+420124543333',	'$2y$10$EkpTYHKufe7jCAwEzYEr2OTa5tdPNGCRGF6fqufVte.jC73fvym1G',	NULL,	NULL,	'admin',	0,	NULL,	NULL,	NULL,	1,	'www/avatars/AE2OEJGW97.jpeg'),
(2,	NULL,	'Business',	'business@fakturacek.cz',	NULL,	'$2y$10$UujM3C3lJFY4dlkuy88LteFX06bCNG8LGNSa9Rc5J9/qxavJ86eF.',	NULL,	NULL,	'business',	0,	'Kolejní 66',	'Brno',	'45678',	1,	NULL),
(3,	NULL,	'Accountant',	'accountant@fakturacek.cz',	NULL,	'$2y$10$KUkctHpRXI71vM41yRI/Q.Sxm3FiYF3JX6tf88qBzdwbffeuiNQ32',	NULL,	NULL,	'accountant',	0,	'test 55',	'Testova',	'12345',	1,	NULL),
(4,	'12345678',	'Radek Jůzl',	'radekjuzl@seznam.cz',	'124543333',	'$2y$10$fIjseCS/5fFUq.Z8VNw.YOdGNV8D5po/AgmvLsjEy48Fd/ahff9E6',	NULL,	NULL,	'business',	0,	'Kam 204',	'Nikam',	'39601',	1,	'www/avatars/4ZPSBR31W3.jpeg'),
(5,	'12345671',	'Radek Smrdí',	'radeksmrdi@fakthodne.cz',	'+420124543333',	'$2y$10$N7yWDAXGJSErERJNnJPO7eHyBGbgsfPs1mYB.VjmzWB/1RYW2OWs.',	NULL,	NULL,	'business',	0,	'Záchod 124',	'Smradlachov',	'45323',	1,	NULL),
(6,	NULL,	'Jouda Jouda',	'jouda@fakturacek.cz',	NULL,	'$2y$10$i.g261.CFQMNKOeOewYaP.lnPn2jn1zUMTIDWI8Rahc9QZQ2aMux.',	NULL,	NULL,	'admin',	0,	NULL,	NULL,	NULL,	1,	NULL),
(7,	NULL,	'Radek Jo',	'helevole@vole.cz',	NULL,	'$2y$10$85rHJn7YQpZ/5PmsOoZi9ep7X43phJgt8keGvcX3ruGQ2zt55A0Bm',	NULL,	NULL,	'admin',	0,	NULL,	NULL,	NULL,	1,	NULL),
(8,	NULL,	'Nové nemehlo',	'hele@sesnemehlo.cz',	'+420124543333',	'$2y$10$1q5Ksbm1wqhdX5okB7W7BuzjB5IdpNBSHlZn20Bh98IhtwmaYBIb2',	NULL,	NULL,	'admin',	0,	NULL,	NULL,	NULL,	1,	'www/avatars/5GBZFOLIED.jpeg'),
(9,	'12546987',	'Test Faktur',	'nekdo@seznam.cz',	'',	'$2y$10$b/cR.n5XNQz1LKUXrK8qcO4SHTLNAwPvRTRtEKvAUjZuxC/gP8yka',	NULL,	NULL,	'business',	0,	'Zahrada 12',	'Nekde',	'74123',	1,	NULL);

DROP TABLE IF EXISTS `users_last_login`;
CREATE TABLE `users_last_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `users_last_login_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `users_last_login` (`id`, `users_id`, `timestamp`) VALUES
(1,	3,	'2021-11-19 18:29:11'),
(18,	2,	'2021-11-21 13:44:49'),
(21,	1,	'2021-11-21 16:49:09'),
(22,	4,	'2021-11-21 18:12:53');

DROP TABLE IF EXISTS `users_last_password_change`;
CREATE TABLE `users_last_password_change` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `users_last_password_change_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `users_last_password_change` (`id`, `users_id`, `timestamp`) VALUES
(2,	4,	'2021-11-15 19:25:14'),
(4,	3,	'2021-11-15 19:27:07'),
(6,	1,	'2021-11-19 22:14:41'),
(7,	2,	'2021-11-20 11:06:30');

-- 2021-11-21 18:14:27
