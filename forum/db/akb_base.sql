-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               5.6.24 - MySQL Community Server (GPL)
-- Server Betriebssystem:        Win32
-- HeidiSQL Version:             9.1.0.4906
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Datenbank Struktur für ian_forumcms
CREATE DATABASE IF NOT EXISTS `ian_forumcms` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `ian_forumcms`;


-- Exportiere Struktur von Tabelle ian_forumcms.akb_server_configs
CREATE TABLE IF NOT EXISTS `akb_server_configs` (
  `option_name` char(100) NOT NULL,
  `option_value` char(255) NOT NULL,
  UNIQUE KEY `config_name` (`option_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Fragile forum configs.\r\n\r\nNEVER touch this, except you know, what you''re doing.';

-- Exportiere Daten aus Tabelle ian_forumcms.akb_server_configs: ~15 rows (ungefähr)
DELETE FROM `akb_server_configs`;
/*!40000 ALTER TABLE `akb_server_configs` DISABLE KEYS */;
INSERT INTO `akb_server_configs` (`option_name`, `option_value`) VALUES
	('about', 'Wir machen hier ganz viele tolle Dinge.\r\nTanzen, Lachen, Springen und einfach Spaß haben.'),
	('account_validation_system', '1'),
	('can_register', '1'),
	('copyright_text', 'Forensoftware: %st - System Version: %sv - Database Version: %dbv | Copyright 2015, %sa - All rights reserved'),
	('db_version', '0.87.1'),
	('default_avatar', 'default.png'),
	('default_avatar_path', './images/avatars/'),
	('forum_description', 'Dein Portal für drachiges und mehr!'),
	('ip_validation_system', '1'),
	('last_user_status_update', '1442127563'),
	('login_ban_system', '1'),
	('max_users', '100'),
	('software_author', 'Alexander Bretzke'),
	('software_title', 'Akasi Board'),
	('software_version', '0.73 Development Release'),
	('thread_entries_per_page', '10'),
	('user_capacity_system', '1');
/*!40000 ALTER TABLE `akb_server_configs` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
