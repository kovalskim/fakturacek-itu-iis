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
  `hash` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `hash_validity` datetime DEFAULT NULL,
  `request_status` enum('wait','active') COLLATE utf8mb4_czech_ci NOT NULL DEFAULT 'wait',
  `who` enum('business','accountant') COLLATE utf8mb4_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `accountant_id` (`accountant_id`),
  CONSTRAINT `accountant_permission_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  CONSTRAINT `accountant_permission_ibfk_2` FOREIGN KEY (`accountant_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `accountant_permission` (`id`, `users_id`, `accountant_id`, `created`, `hash`, `hash_validity`, `request_status`, `who`) VALUES
(1,	1,	2,	'2021-11-29 09:42:43',	NULL,	NULL,	'active',	'accountant');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `categories_ibfk_1` (`users_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `categories` (`cat_id`, `name`, `users_id`) VALUES
(3,	'Radek Jůzl',	6);

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
(1,	'Radek Jůzl',	'',	'',	'Kam 204',	'Nikam',	'39601',	'radekjuzl@seznam.cz',	'',	1);

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `expenses_cat_id` int(11) DEFAULT NULL,
  `items` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `price` float NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_id` (`expenses_cat_id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`expenses_cat_id`) REFERENCES `categories` (`cat_id`),
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
  `after_due_date` int(11) NOT NULL DEFAULT 0,
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

INSERT INTO `invoices` (`id`, `users_id`, `user_name`, `user_street`, `user_city`, `user_zip`, `user_cin`, `user_vat`, `user_phone`, `user_email`, `client_id`, `client_name`, `client_street`, `client_city`, `client_zip`, `client_cin`, `client_vat`, `client_phone`, `client_email`, `created`, `due_date`, `after_due_date`, `account_number`, `variable_symbol`, `logo_path`, `vat_note`, `footer_note`, `status`, `suma`) VALUES
(1,	1,	'OSVČ',	'Božetěchova 1/2',	'Brno-Královo Pole',	'61200',	'48864820',	NULL,	'',	'osvc@fakturacek.cz',	1,	'Radek Jůzl',	'Kam 204',	'Nikam',	'39601',	'',	'',	'',	'radekjuzl@seznam.cz',	'2021-11-28 22:45:00',	'2021-12-12 23:45:00',	0,	'10006-18432071/0600',	'211101',	NULL,	0,	'0',	'canceled',	2750),
(2,	1,	'OSVČ',	'Božetěchova 1/2',	'Brno-Královo Pole',	'61200',	'48864820',	NULL,	'',	'osvc@fakturacek.cz',	1,	'Radek Jůzl',	'Kam 204',	'Nikam',	'39601',	'',	'',	'',	'radekjuzl@seznam.cz',	'2021-11-29 08:12:27',	'2021-11-12 08:12:27',	1,	'10006-18432071/0600',	'211102',	NULL,	0,	'0',	'unpaid',	600),
(3,	1,	'OSVČ',	'Božetěchova 1/2',	'Brno-Královo Pole',	'61200',	'48864820',	NULL,	'',	'osvc@fakturacek.cz',	NULL,	'Radek Jůzl',	'Kam 204',	'Nikam',	'39601',	'',	'',	'',	'radekjuzl@seznam.cz',	'2021-11-29 08:12:44',	'2021-12-13 09:12:44',	0,	'10006-18432071/0600',	'211103',	NULL,	0,	'0',	'unpaid',	2500);

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
(1,	1,	'Prvni',	55,	50,	'hours',	2750),
(2,	2,	'Hej',	50,	12,	'hours',	600),
(3,	3,	'Prvni',	50,	50,	'hours',	2500);

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
(1,	'10006-18432071/0600',	'YYMM00',	NULL,	0,	'',	1);

DROP TABLE IF EXISTS `texts`;
CREATE TABLE `texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `text` text COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `img_path` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `texts` (`id`, `type`, `text`, `img_path`) VALUES
(1,	'aboutus',	'<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Et harum quidem rerum facilis est et <a href=\"https://www.fit.vut.cz/.en\">expedita</a> distinctio. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.&nbsp;</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Morbi imperdiet, mauris ac auctor dictum, nisl ligula egestas nulla, et sollicitudin sem <strong>purus</strong> in lacus. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.&nbsp;</p><p>Sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p><ol><li><em>Lorem</em></li><li>ipsum</li><li>dolor</li></ol>',	'www/img/aboutus.jpeg'),
(2,	'contact',	'<p><strong>Lorem </strong>ipsum dolor sit amet, consectetuer adipiscing elit. <em>Aliquam ornare wisi eu metus.</em> Ut tempus purus at lorem. Nullam sit amet magna in magna gravida vehicula. Suspendisse sagittis ultrices augue. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis sapien nunc, commodo et, interdum suscipit, sollicitudin et, dolor.</p>',	'www/img/contact.jpeg');

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
(1,	'48864820',	NULL,	'OSVČ',	'osvc@fakturacek.cz',	'',	'$2y$10$Zz1ULBSoD.O8U8Qx03Eje.QwekTMO5JRdNYuOpZWq8JLWlYuKzBzy',	NULL,	NULL,	'business',	'Božetěchova 1/2',	'Brno-Královo Pole',	'61200',	NULL,	'active',	'2021-11-28 21:41:20'),
(2,	'68396201',	NULL,	'Účetní',	'ucetni@fakturacek.cz',	'',	'$2y$10$k2fb6Sklzi9z.tS0bM8yEOjAGS7xj0vbskNpDUHg3zkDDzAc5V/UK',	NULL,	NULL,	'accountant',	'Božetěchova 1/2',	'Brno-Královo Pole',	'61200',	NULL,	'active',	'2021-11-28 21:43:43'),
(3,	NULL,	NULL,	'Admin',	'ad2in@fakturacek.cz',	'',	'$2y$10$Bn4AnxPOleq.cSIVdCPPWO9Mpn5/xK3MAqX5Q4R.bdy8Ev5pnXmzi',	NULL,	NULL,	'admin',	NULL,	NULL,	NULL,	NULL,	'active',	'2021-11-28 22:58:15'),
(4,	NULL,	NULL,	'Hej',	'radekjuzl@seznam.cz',	NULL,	'$2y$10$VGDtbbNsiMTK6.iVZws0XuGd1Ijhql9HjxCvBEBeR00pxPtIzFuWW',	NULL,	NULL,	'admin',	NULL,	NULL,	NULL,	NULL,	'active',	NULL),
(5,	NULL,	NULL,	'adminss',	'adminss@seznam.cz',	NULL,	'$2y$10$8GrnTaRbspqBh6PssuqmSeOoxGDSfnp7Peabn4lL.70Quvo75GeWq',	NULL,	NULL,	'admin',	NULL,	NULL,	NULL,	NULL,	'active',	NULL),
(6,	'48864820',	NULL,	'OSVČ2',	'osvc2@fakturacek.cz',	'',	'$2y$10$Zz1ULBSoD.O8U8Qx03Eje.QwekTMO5JRdNYuOpZWq8JLWlYuKzBzy',	NULL,	NULL,	'business',	'Božetěchova 1/2',	'Brno-Královo Pole',	'61200',	NULL,	'active',	'2021-11-28 21:41:20');

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
(7,	3,	'2021-11-28 21:58:34'),
(16,	6,	'2021-11-29 08:53:54'),
(17,	2,	'2021-11-29 09:42:33'),
(18,	4,	'2021-11-29 12:23:53'),
(19,	1,	'2021-11-29 12:24:29');

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
(1,	3,	'2021-11-28 21:54:32'),
(3,	4,	'2021-11-28 22:10:19'),
(4,	5,	'2021-11-28 22:11:49');

-- 2021-11-29 12:50:31
