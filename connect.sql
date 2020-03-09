-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `connect`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` varchar(50) NOT NULL,
  `idLevel` int(4) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `password-hash` varchar(50) DEFAULT NULL,
  `contact_no` varchar(25) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `login_count` int(11) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` (`user_id`, `idLevel`, `first_name`, `last_name`, `email`, `password`, `password-hash`, `contact_no`, `create_at`, `update_at`, `last_login_at`, `login_count`, `status`) VALUES
('43e1c77e-5a23-11ea-9949-7085c259afcc',	1,	'santosh',	'mahajan',	'santosh+04@gmail.com',	'123456',	NULL,	'9999888888',	NULL,	NULL,	NULL,	NULL,	0),
('6c5f99ca-5a23-11ea-9949-7085c259afcc',	1,	'santosh test',	'mahajan test',	'santosh+01@gmail.com',	'123456',	NULL,	'9999888888',	NULL,	NULL,	NULL,	NULL,	1),
('6eba762b-589d-11ea-a2d8-b05adae1ff5e',	1,	'test',	'01',	'ashishvishvkarma+01@bitcot.com',	'12345',	NULL,	'1236547',	'2020-02-26 00:00:00',	NULL,	'2020-02-29 10:52:54',	2020,	1),
('7a849035-589d-11ea-a2d8-b05adae1ff5e',	NULL,	'test',	'01',	'ashishvishvkarma+02@bitcot.com',	NULL,	NULL,	'1236547',	'2020-02-26 00:00:00',	NULL,	NULL,	NULL,	1),
('7a84fe38-589d-11ea-a2d8-b05adae1ff5e',	NULL,	'test',	'01',	'ashishvishvkarma+03@bitcot.com',	NULL,	NULL,	'1236547',	'2020-02-26 00:00:00',	NULL,	NULL,	NULL,	1),
('ae3cbce3-5a21-11ea-9949-7085c259afcc',	NULL,	'santosh',	'mahajan',	'santosh@gmail.com',	'123456',	NULL,	'9999888888',	NULL,	NULL,	NULL,	NULL,	0),
('b23683e2-5ab8-11ea-89d3-7085c259afcc',	1,	'santosh test',	'mahajan test',	'santosh+09@gmail.com',	'123456',	NULL,	'9999888888',	NULL,	NULL,	NULL,	NULL,	1),
('bdca14f4-5a30-11ea-9949-7085c259afcc',	1,	'santosh test',	'mahajan test',	'santosh+08@gmail.com',	'123456',	NULL,	'9999888888',	NULL,	NULL,	NULL,	NULL,	1);

DELIMITER ;;

CREATE TRIGGER `before_insert_user` BEFORE INSERT ON `users` FOR EACH ROW
SET new.user_id = uuid();;

DELIMITER ;

DROP TABLE IF EXISTS `users_levels`;
CREATE TABLE `users_levels` (
  `id_level` int(11) NOT NULL AUTO_INCREMENT,
  `Level` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_level`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users_levels` (`id_level`, `Level`, `status`) VALUES
(1,	'admin',	1),
(2,	'developer',	1);

-- 2020-03-09 07:30:18
