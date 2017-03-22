-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               5.6.31-log - MySQL Community Server (GPL)
-- Server Betriebssystem:        Win64
-- HeidiSQL Version:             9.3.0.5089
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportiere Datenbank Struktur für botl
CREATE DATABASE IF NOT EXISTS `botl` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `botl`;

-- Exportiere Struktur von Tabelle botl.guild_calendar_data
CREATE TABLE IF NOT EXISTS `guild_calendar_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(4) unsigned NOT NULL DEFAULT '2016',
  `month_num` int(2) unsigned NOT NULL DEFAULT '6',
  `day_num` char(5) DEFAULT '',
  `time` char(5) DEFAULT '',
  `event_name` char(50) NOT NULL,
  `event_desc` varchar(100) DEFAULT '',
  `event_img` varchar(500) DEFAULT '',
  `end_month_num` int(2) unsigned NOT NULL DEFAULT '6',
  `end_day_num` char(5) DEFAULT '',
  `end_event_name` char(50) NOT NULL,
  `end_event_desc` varchar(100) DEFAULT '',
  `end_time` char(5) DEFAULT '',
  `end_event_img` varchar(500) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle botl.guild_calendar_data: ~19 rows (ungefähr)
DELETE FROM `guild_calendar_data`;
/*!40000 ALTER TABLE `guild_calendar_data` DISABLE KEYS */;
INSERT INTO `guild_calendar_data` (`id`, `year`, `month_num`, `day_num`, `time`, `event_name`, `event_desc`, `event_img`, `end_month_num`, `end_day_num`, `end_event_name`, `end_event_desc`, `end_time`, `end_event_img`) VALUES
	(7, 2016, 5, '26', NULL, 'Warcraft- Film', '15:00 !', '', 6, '', '', '', '', ''),
	(8, 2016, 8, '30', NULL, 'WoW: Legion - Release', 'Es ist soweit!', '', 6, '', '', '', '', ''),
	(9, 2016, 6, '3', NULL, '(I)AN-Party', ':3', '', 6, '', '', '', '', ''),
	(10, 2016, 12, '9', NULL, 'Addiii BDay', '<3', '', 6, '', '', '', '', ''),
	(11, 2016, 6, '5', '00:01', 'Dunkelmond-Jahrmarkt beginnt', '', '', 6, '11', 'Dunkelmond-Jahrmarkt endet.', '', '', ''),
	(13, 2016, 6, '1', '00:01', 'Zeitwanderung: WotLK beginnt!', '', '', 6, '', '', '', '', ''),
	(15, 2016, 6, '8', '00:01', 'Bonusereignis: Apexis beginnt!', '', '', 6, '', '', '', '', ''),
	(17, 2016, 6, '15', '00:01', 'Bonusereignis: Arenascharmützel beginnt!', '', '', 6, '', '', '', '', ''),
	(19, 2016, 6, '21', '10:00', 'Sonnenwendfest beginnt!', '', '', 6, '', '', '', '', ''),
	(20, 2016, 6, '22', '00:01', 'Zeitwanderung: Cataclysm beginnt!', '', '', 6, '', '', '', '', ''),
	(22, 2016, 6, '29', '00:01', 'Bonusereignis: Schlachtfelder beginnt!', '', '', 6, '', '', '', '', ''),
	(24, 2016, 7, '3', '00:01', 'Dunkelmond-Jahrmarkt beginnt', '', '', 7, '4', 'Dunkelmond-Jahrmarkt endet.', '', '', ''),
	(25, 2016, 7, '3', '14:00', 'Anglerwettbewerb im Schlingendorntal', '', '', 6, '', '', '', '', '');
/*!40000 ALTER TABLE `guild_calendar_data` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
