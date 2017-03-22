-- --------------------------------------------------------
-- Host:                         vweb17.nitrado.net
-- Server Version:               5.1.73-1+deb6u1 - (Debian)
-- Server Betriebssystem:        debian-linux-gnu
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Datenbank Struktur für ni204675_1sql3
CREATE DATABASE IF NOT EXISTS `ni204675_1sql3` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `ni204675_1sql3`;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_account
CREATE TABLE IF NOT EXISTS `akb_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User Identifier',
  `username` varchar(13) NOT NULL COMMENT 'Username',
  `pass_hash` varchar(199) NOT NULL COMMENT 'Password',
  `extra_val` varchar(100) NOT NULL COMMENT 'Extra value for the calculation of the password hash',
  `crypt_level` int(5) NOT NULL COMMENT 'Level of the password hash calculation',
  `last_sid` varchar(150) DEFAULT NULL COMMENT 'SID of the last session',
  `sid` varchar(150) DEFAULT NULL COMMENT 'Actual user SID',
  `admin_sid` varchar(150) DEFAULT '0' COMMENT '< Unused >',
  `email` varchar(254) DEFAULT NULL COMMENT 'User E-Mail',
  `last_response` timestamp NULL DEFAULT NULL COMMENT 'Last activity time',
  `registered_date` int(30) DEFAULT NULL COMMENT 'Zeitpunkt der Registrierung',
  `registered_ip` varchar(50) DEFAULT '000.000.000.00' COMMENT 'IP-Adress, that was used for the registration process',
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'Last login time',
  `last_login_ip` varchar(50) DEFAULT '000.000.000.00' COMMENT 'Last IP-Adress that was used for the login',
  `failed_login_attempts` int(1) DEFAULT '0',
  `logged_in` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Actual login status | 0 = Not Logged in,  1 = Logged in',
  `mail_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Mail verification status | 0 = Not verified, 1 = verified',
  `accepted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Verification status of a new account | 0 = not verified, 1 = verified',
  `account_level` int(1) unsigned zerofill NOT NULL DEFAULT '1' COMMENT 'User account level',
  `persistent_session_status` int(1) DEFAULT NULL COMMENT 'Persistent session status | 0 = Not persistent, 1 = persistent',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `pass_hash` (`pass_hash`),
  UNIQUE KEY `sid` (`sid`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='Account System';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_account: ~5 rows (ungefähr)
DELETE FROM `akb_account`;
/*!40000 ALTER TABLE `akb_account` DISABLE KEYS */;
INSERT INTO `akb_account` (`id`, `username`, `pass_hash`, `extra_val`, `crypt_level`, `last_sid`, `sid`, `admin_sid`, `email`, `last_response`, `registered_date`, `registered_ip`, `last_login`, `last_login_ip`, `failed_login_attempts`, `logged_in`, `mail_verified`, `accepted`, `account_level`, `persistent_session_status`) VALUES
	(1, 'Arenima', 'a4e782d9798469685eb7214793712760', '2237549703', 101, '658f90bfcf4a676c0a60582bcbe58337', NULL, '0', 'admin@baneofthelegion.de', NULL, 1442130301, '95.90.251.2', '2015-11-15 20:40:44', '95.90.251.48', 0, 0, 1, 1, 3, 1),
	(2, 'Savyon', '57217baf7d3aec41c8480a4bc5e3a07c', '2329355926', 16, NULL, '4b62bacb5b305b5eb02b7d2b726f5ac7', '0', 'savyon89@web.de', NULL, 1442133753, '94.134.249.154', '2015-09-13 10:43:24', '94.134.249.154', 0, 1, 1, 1, 2, 1),
	(11, 'addi_miip', '78342ff06e341f867d49dbdf9b2646691', '2140461912', 250, NULL, 'b37c2937a50215d347d2f8ae39c90f97', '0', 'adriana.do@gmx.de', NULL, 1442223705, '84.161.244.70', '2015-09-14 11:48:39', '95.90.251.2', 0, 1, 1, 1, 2, 1),
	(12, 'Shrelkargor', '220112d865b92ad446f0df05d3b35000', '2017119075', 207, NULL, 'e39370bb2528a1d0a521dff50037f206', '0', 'reyshidera.reiko@gmail.com', NULL, 1442240877, '89.244.66.185', '2015-11-22 18:23:00', '94.134.4.177', 0, 1, 1, 1, 1, 0),
	(13, 'Suvi', 'ff99657c6a54c2f45e22170971f80b1e', '1561900149', 206, NULL, '4146c97daeb147b75d062dc4ac65ab4b', '0', 'luna-summer@online.de', NULL, 1442934819, '91.14.32.113', '2015-09-22 17:21:53', '91.14.32.113', 0, 1, 1, 1, 1, 1);
/*!40000 ALTER TABLE `akb_account` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_account_ban
CREATE TABLE IF NOT EXISTS `akb_account_ban` (
  `id` int(5) NOT NULL COMMENT 'User Identifier',
  `ban_type` int(1) NOT NULL COMMENT 'Type of the ban',
  `ban_duration` int(15) DEFAULT NULL COMMENT 'Ban duration in seconds',
  `banned_at` int(20) DEFAULT NULL COMMENT 'Time of the ban in seconds ( Unix Timestamp )',
  `banned_by` int(5) DEFAULT NULL COMMENT 'User Identifier of the executing banner',
  PRIMARY KEY (`id`,`ban_type`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_account_ban: ~0 rows (ungefähr)
DELETE FROM `akb_account_ban`;
/*!40000 ALTER TABLE `akb_account_ban` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_account_ban` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_account_blocks
CREATE TABLE IF NOT EXISTS `akb_account_blocks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blocked_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`blocked_id`,`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_account_blocks: ~0 rows (ungefähr)
DELETE FROM `akb_account_blocks`;
/*!40000 ALTER TABLE `akb_account_blocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_account_blocks` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_account_changelogs
CREATE TABLE IF NOT EXISTS `akb_account_changelogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifier',
  `account_id` int(5) NOT NULL DEFAULT '0' COMMENT 'Account ID',
  `change_type` int(2) NOT NULL DEFAULT '0' COMMENT 'Type of the change || 1 = Account User || 2 = Account Pass || 3 = Account Mail || 4 = Misc. Account Data || 5 = User Preferences',
  `prev_data` varchar(900) NOT NULL COMMENT 'Additional account data ( a.e : Current account data )',
  `new_data` varchar(900) NOT NULL COMMENT 'The changed data',
  `debug_msg` varchar(900) NOT NULL COMMENT 'Detailed informations about the changes',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of the change',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Logging system for user changes';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_account_changelogs: ~0 rows (ungefähr)
DELETE FROM `akb_account_changelogs`;
/*!40000 ALTER TABLE `akb_account_changelogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_account_changelogs` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_account_data
CREATE TABLE IF NOT EXISTS `akb_account_data` (
  `account_id` int(255) NOT NULL AUTO_INCREMENT COMMENT 'Account ID',
  `username` varchar(50) NOT NULL COMMENT 'Username',
  `gender` int(1) NOT NULL DEFAULT '1' COMMENT 'Gender ID | 1 = Undefined, 2 = Female, 3 = Male',
  `avatar` varchar(500) NOT NULL DEFAULT './images/avatars/default.png' COMMENT 'Avatar path',
  `post_counter` int(50) NOT NULL DEFAULT '0' COMMENT 'Number of posts',
  `email` varchar(254) NOT NULL DEFAULT 'n.A' COMMENT 'User email',
  `user_title` varchar(50) DEFAULT 'Neuankömmling' COMMENT 'User title',
  `messenger_skype` varchar(50) DEFAULT NULL COMMENT 'Skype name',
  `messenger_icq` varchar(50) DEFAULT NULL COMMENT 'Skype name',
  `user_description` varchar(500) DEFAULT NULL COMMENT '< Unused >',
  `profile_views` int(10) NOT NULL DEFAULT '0' COMMENT 'Number of the profile views for this user',
  `user_rank` int(11) DEFAULT '1' COMMENT 'User rank ID',
  `signature` varchar(250) DEFAULT NULL COMMENT 'User signature',
  `login_status` int(1) NOT NULL DEFAULT '0' COMMENT 'Actual login status | 0 = Not logged in, 1 Logged in',
  `location` varchar(50) NOT NULL DEFAULT 'n.A' COMMENT 'User location',
  `emoticons` int(1) NOT NULL DEFAULT '1' COMMENT 'State of the forum emoticons',
  `user_cursor` int(1) NOT NULL DEFAULT '1' COMMENT 'State of the forum cursor',
  `ajax_msg` int(1) NOT NULL DEFAULT '1' COMMENT 'State of the realtime notofications',
  `chat_emoticons` int(1) NOT NULL DEFAULT '0' COMMENT 'State of the chat emoticons',
  `chat_sidebar_state` int(1) NOT NULL DEFAULT '1' COMMENT 'State of the chat sidebar',
  `design_template` int(2) NOT NULL DEFAULT '2' COMMENT 'Active design ID',
  `language` int(3) NOT NULL DEFAULT '1' COMMENT 'Active language ID',
  PRIMARY KEY (`account_id`,`username`),
  KEY `account_id` (`account_id`),
  KEY `Username` (`username`),
  CONSTRAINT `ID` FOREIGN KEY (`account_id`) REFERENCES `akb_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Username` FOREIGN KEY (`username`) REFERENCES `akb_account` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='Account Daten';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_account_data: ~5 rows (ungefähr)
DELETE FROM `akb_account_data`;
/*!40000 ALTER TABLE `akb_account_data` DISABLE KEYS */;
INSERT INTO `akb_account_data` (`account_id`, `username`, `gender`, `avatar`, `post_counter`, `email`, `user_title`, `messenger_skype`, `messenger_icq`, `user_description`, `profile_views`, `user_rank`, `signature`, `login_status`, `location`, `emoticons`, `user_cursor`, `ajax_msg`, `chat_emoticons`, `chat_sidebar_state`, `design_template`, `language`) VALUES
	(1, 'Arenima', 3, './images/avatars/cat_burning.gif', 2, 'admin@baneofthelegion.de', 'Administrator', NULL, NULL, NULL, 60, 1, NULL, 0, 'n.A', 2, 1, 1, 0, 1, 2, 1),
	(2, 'Savyon', 1, './images/avatars/avatar-703de10ca7d294f37bcf67cde3c24917.png', 0, 'savyon89@web.de', 'Neuankömmling', NULL, NULL, NULL, 15, 1, NULL, 1, 'n.A', 1, 1, 1, 0, 1, 2, 1),
	(11, 'addi_miip', 1, './images/avatars/default.png', 0, 'adriana.do@gmx.de', 'Neuankömmling', NULL, NULL, NULL, 18, 1, NULL, 1, 'n.A', 1, 1, 1, 0, 1, 2, 1),
	(12, 'Shrelkargor', 1, './images/avatars/shrelkargor.gif', 0, 'reyshidera.reiko@gmail.com', 'Neuankömmling', NULL, NULL, NULL, 15, 1, NULL, 1, 'n.A', 1, 1, 1, 0, 1, 2, 1),
	(13, 'Suvi', 1, './images/avatars/default.png', 0, 'luna-summer@online.de', 'Neuankömmling', NULL, NULL, NULL, 14, 1, NULL, 1, 'n.A', 1, 1, 1, 0, 1, 2, 1);
/*!40000 ALTER TABLE `akb_account_data` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_account_data_profile
CREATE TABLE IF NOT EXISTS `akb_account_data_profile` (
  `id` int(11) unsigned NOT NULL COMMENT 'User ID',
  `location` varchar(50) NOT NULL DEFAULT '',
  `hobbies` varchar(500) NOT NULL DEFAULT '',
  `about` varchar(15000) NOT NULL DEFAULT '',
  `msngr_skype` varchar(100) NOT NULL DEFAULT '',
  `msngr_icq` varchar(100) NOT NULL DEFAULT '',
  `sn_facebook` varchar(100) NOT NULL DEFAULT '',
  `sn_twitter` varchar(100) NOT NULL DEFAULT '',
  `sn_googleplus` varchar(100) NOT NULL DEFAULT '',
  `sn_tumblr` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_account_data_profile: ~5 rows (ungefähr)
DELETE FROM `akb_account_data_profile`;
/*!40000 ALTER TABLE `akb_account_data_profile` DISABLE KEYS */;
INSERT INTO `akb_account_data_profile` (`id`, `location`, `hobbies`, `about`, `msngr_skype`, `msngr_icq`, `sn_facebook`, `sn_twitter`, `sn_googleplus`, `sn_tumblr`) VALUES
	(1, 'Schleswig-Holstein, Deutschland', 'Programmierung, CGI, Digital Art', '<span style="font-family:georgia,serif"><span style="font-size:18px"><span style="color:#FF8C00">Hall&ouml;chen, neugieriges Ding, welches sich grade in mein Profil geschlichen hat :3<br />\r\n<br />\r\nMir scheint, als wolltest du mehr &uuml;ber die Person erfahren, welche hinter &quot;Arenima&quot; steckt?<br />\r\nNun denn, so sei es.</span></span></span><br />\r\n&nbsp;\r\n<hr /><br />\r\n<span style="font-family:georgia,serif"><span style="font-size:18px"><span style="color:#FF8C00">Ich bin der Alex, auch genannt &quot;Ian&quot;. Der Kosename hat sich irgendwie so durchgesetzt und wie der zustande kam, wei&szlig; ich inzwischen auch nicht mehr x3<br />\r\nViele meiner Freunde nennen mich derweil so, aber dem Internet und sonstigem RL bleibt er fern :3<br />\r\nIch wohne in Schleswig-Holstein und habe 3 andere Gildenmitglieder auch direkt in meiner N&auml;he.<br />\r\nUnd genau diese waren die Gr&uuml;ndungmitglieder der Gilde x3<br />\r\nDazu sp&auml;ter mehr.<br />\r\nIch bin 18 jahre alt, habe j&uuml;ngst meine Mittlere Reife nachgeholt und bin jetzt in einer &quot;Schulischen Ausbildung&quot; zum &quot;Kaufm&auml;nnischen Assistenten f&uuml;r Informationsverarbeitung&quot;.<br />\r\nGenerell treibe ich Dinge wie z.B.:</span></span></span>\r\n\r\n<ul>\r\n	<li><span style="font-family:georgia,serif"><span style="font-size:18px"><span style="color:#FF8C00">Programmierung</span></span></span></li>\r\n	<li><span style="font-family:georgia,serif"><span style="font-size:18px"><span style="color:#FF8C00">CGI (Blender)</span></span></span></li>\r\n	<li><span style="font-family:georgia,serif"><span style="font-size:18px"><span style="color:#FF8C00">Digital Art(Photoshop)</span></span></span></li>\r\n	<li><span style="font-family:georgia,serif"><span style="font-size:18px"><span style="color:#FF8C00">Spielen &lt;3</span></span></span></li>\r\n	<li><span style="font-family:georgia,serif"><span style="font-size:18px"><span style="color:#FF8C00">Mit vielen tollen Leuten schreiben :3</span></span></span></li>\r\n</ul>\r\n<br />\r\n<span style="font-family:georgia,serif"><span style="font-size:18px"><span style="color:#FF8C00">Auch wenn die Gilde Anfangs nur eine Idee war, wollten wir es mal probieren. Hatten aber weder eine Idee, wer der Gildenleiter sein sollte, noch einen Plan, wie die Gilde denn hei&szlig;en soll.<br />\r\nBromnir(aka. Addi) kam dann auf &quot;Bane of the Legion&quot;. Ein passender Name f&uuml;r das kommende Addon &quot;Legion&quot; :3<br />\r\nDann bin ich irgendwie an die Gildenleitung geraten und d&ouml;del hier jetzt im eigens gecodetem Forum und der HP vor mich hin :P<br />\r\n<br />\r\n​So, das sollte f&uuml;r&#39;s erste reichen x3</span></span></span><br />\r\n&nbsp;', '', '', '', '', '', ''),
	(2, '', '', '', '', '', '', '', '', ''),
	(11, 'Im Norden', '', '- folgt bald* -<br />\r\n<br />\r\n<br />\r\n___<br />\r\n* in 3 Jahren oder so', '', '', '', '@Miip_95', '', 'arkenburglar.tumblr.com'),
	(12, '', '', '', '', '', '', '', '', ''),
	(13, '', '', '', '', '', '', '', '', '');
/*!40000 ALTER TABLE `akb_account_data_profile` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_account_friends
CREATE TABLE IF NOT EXISTS `akb_account_friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`friend_id`,`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_account_friends: ~0 rows (ungefähr)
DELETE FROM `akb_account_friends`;
/*!40000 ALTER TABLE `akb_account_friends` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_account_friends` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_account_logs
CREATE TABLE IF NOT EXISTS `akb_account_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) DEFAULT NULL,
  `message` varchar(500) DEFAULT NULL,
  `pass` varchar(500) DEFAULT NULL,
  `pass_md5` varchar(500) DEFAULT NULL,
  `message_fail` varchar(500) DEFAULT NULL,
  `user_agent` varchar(250) DEFAULT NULL,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sid` varchar(50) DEFAULT NULL,
  `user_ip` varchar(150) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COMMENT='Account System Logs';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_account_logs: ~51 rows (ungefähr)
DELETE FROM `akb_account_logs`;
/*!40000 ALTER TABLE `akb_account_logs` DISABLE KEYS */;
INSERT INTO `akb_account_logs` (`id`, `account_id`, `message`, `pass`, `pass_md5`, `message_fail`, `user_agent`, `time`, `sid`, `user_ip`) VALUES
	(1, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-13 09:48:44', 'c9f3c7750fab33fe77bff7d953ab58f9', '95.90.251.2'),
	(2, 1, 'User wurde erfolgreich ausgeloggt.', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-13 10:39:04', 'c9f3c7750fab33fe77bff7d953ab58f9', '95.90.251.2'),
	(3, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-13 10:42:35', 'c9e8d5a45cd54f94960b925bef74c5ef', '95.90.251.2'),
	(4, 2, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 44.0.2403.155 auf Windows', '2015-09-13 10:43:24', '4b62bacb5b305b5eb02b7d2b726f5ac7', '94.134.249.154'),
	(5, 1, 'User wurde erfolgreich ausgeloggt.', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-14 10:14:39', 'c9e8d5a45cd54f94960b925bef74c5ef', '95.90.251.2'),
	(6, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-14 11:35:04', 'dc217430183aad66683f6bf015a951c4', '95.90.251.2'),
	(7, 1, 'User wurde erfolgreich ausgeloggt.', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-14 11:38:29', 'dc217430183aad66683f6bf015a951c4', '95.90.251.2'),
	(8, 11, NULL, NULL, NULL, 'Ungültiges Passwort', NULL, '2015-09-14 11:44:48', NULL, '84.161.244.70'),
	(9, 11, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.85 auf Windows', '2015-09-14 11:45:06', '9ba4a5445693c08e5987314837558a98', '84.161.244.70'),
	(10, 11, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.85 auf Windows', '2015-09-14 11:46:28', '4500c4951e2780fcc5265ca13ef7ec42', '84.161.244.70'),
	(11, 11, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-14 11:48:39', 'b37c2937a50215d347d2f8ae39c90f97', '95.90.251.2'),
	(12, 11, NULL, NULL, NULL, 'Ungültiges Passwort', NULL, '2015-09-14 11:49:03', NULL, '84.161.244.70'),
	(13, 1, NULL, NULL, NULL, 'Ungültiges Passwort', NULL, '2015-09-14 11:50:22', NULL, '95.90.251.2'),
	(14, 1, NULL, NULL, NULL, 'Ungültiges Passwort', NULL, '2015-09-14 11:50:33', NULL, '95.90.251.2'),
	(15, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-14 11:50:47', 'a64d5ec975172960bf8ac3f875aa04d1', '95.90.251.2'),
	(16, 11, NULL, NULL, NULL, 'Ungültiges Passwort', NULL, '2015-09-14 11:55:24', NULL, '84.161.244.70'),
	(17, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-14 12:04:09', 'ab23c1373d6b92bf31069c24be44de38', '95.90.251.2'),
	(18, 1000000, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.85 auf Windows', '2015-09-14 16:28:26', '750daa9d61f60eec4b9c296c055c2ca1', '89.244.66.185'),
	(19, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.85 auf Windows', '2015-09-14 21:21:14', '7205b0ed50d8680e69ba823d28689e9b', '89.244.66.185'),
	(20, 11, NULL, NULL, NULL, 'Ungültiges Passwort', NULL, '2015-09-15 16:47:54', NULL, '84.161.246.172'),
	(21, 11, NULL, NULL, NULL, 'Ungültiges Passwort', NULL, '2015-09-15 16:48:30', NULL, '84.161.246.172'),
	(22, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.93 auf Windows', '2015-09-19 12:42:42', 'edc018103282af03b973bb4799773629', '89.244.74.150'),
	(23, NULL, NULL, NULL, NULL, 'Ungültiger Username und Passwort!', NULL, '2015-09-20 19:15:00', NULL, '95.90.250.199'),
	(24, NULL, NULL, NULL, NULL, 'Ungültiger Username und Passwort!', NULL, '2015-09-20 19:15:09', NULL, '95.90.250.199'),
	(25, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-20 19:15:24', '46442cffb46bc5976aa95588574666ff', '95.90.250.199'),
	(26, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.94 auf Linux', '2015-09-21 09:22:05', '9cf515f9a41dce5360971a5e997cdcbb', '176.0.19.228'),
	(27, NULL, NULL, NULL, NULL, 'Ungültiger Username und Passwort!', NULL, '2015-09-21 18:27:28', NULL, '95.90.250.199'),
	(28, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-21 18:27:36', '4beab4113115829866bb72ab4a796e25', '95.90.250.199'),
	(29, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.93 auf Windows', '2015-09-21 21:30:39', 'd2c3a74c377070adf9892cd2e58aac27', '94.134.27.46'),
	(30, 13, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Mozilla Firefox, Version: 40.0 auf Windows', '2015-09-22 17:21:53', '4146c97daeb147b75d062dc4ac65ab4b', '91.14.32.113'),
	(31, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.93 auf Windows', '2015-09-22 21:35:09', 'a5efbc5fb1820b0995792e0b5f19bf3b', '94.134.11.56'),
	(32, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.94 auf Linux', '2015-09-22 22:00:54', 'c90a08e9b3ead0a6875eb120cb83a8e0', '95.90.250.199'),
	(33, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.94 auf Linux', '2015-09-23 07:44:41', 'd44c0ad96c980e458e8ca0b595d15eef', '46.114.6.110'),
	(34, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-09-23 11:43:53', '95241a0cd8a8595473da7398283ad9ec', '95.90.250.250'),
	(35, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.99 auf Windows', '2015-09-24 16:40:30', '56f2280dc14399be2564886a4327a47c', '89.244.66.180'),
	(36, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.99 auf Windows', '2015-09-26 16:55:15', '0b573b230f7c7a38db62f2eea5b989a3', '94.134.8.113'),
	(37, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.101 auf Windows', '2015-10-01 23:46:27', '7a47fbe2382700579cfa60933657f31b', '94.134.19.36'),
	(38, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.101 auf Windows', '2015-10-06 18:41:04', '062e1cc8f58506aba03e7c8e8fad58e2', '94.134.3.162'),
	(39, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.101 auf Windows', '2015-10-09 23:50:28', '1e535414a118ddf19301a166f35d696f', '94.134.11.166'),
	(40, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.101 auf Windows', '2015-10-12 21:51:35', '1d25f2eeee08fdd7f86d5a651593ef3c', '89.244.70.246'),
	(41, 1, 'User wurde erfolgreich ausgeloggt.', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-10-14 15:50:48', '95241a0cd8a8595473da7398283ad9ec', '95.90.251.16'),
	(42, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 43.0.2357.81 auf Windows', '2015-10-14 15:50:59', 'ccc46f10c93b988a779eb17c5201e10f', '95.90.251.16'),
	(43, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.93 auf Windows', '2015-10-16 18:31:09', 'ee84f7fec37dc16065caf5e7dd855b32', '95.90.251.16'),
	(44, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 46.0.2490.71 auf Windows', '2015-10-18 19:08:49', '6e6d7ac691a25599615c77a554b1804f', '89.244.76.136'),
	(45, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.93 auf Windows', '2015-10-22 18:05:03', '8175acc6bf7a2a197aaeee8dae7b56de', '95.90.251.16'),
	(46, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 46.0.2490.80 auf Windows', '2015-10-25 00:43:26', '47a4091aa4a68609b9df7a7627e038d8', '89.244.79.202'),
	(47, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.93 auf Windows', '2015-10-25 09:31:34', '74485067bea59034ef884c412c83afa3', '95.90.251.16'),
	(48, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 46.0.2490.80 auf Windows', '2015-10-28 22:59:47', '684a3b09719cc8faac4640fd39d9d383', '94.134.26.136'),
	(49, 1, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 45.0.2454.93 auf Windows', '2015-11-15 20:40:44', '658f90bfcf4a676c0a60582bcbe58337', '95.90.251.48'),
	(50, 12, 'User erfolgreich eingeloggt', NULL, NULL, NULL, 'Google Chrome, Version: 46.0.2490.86 auf Windows', '2015-11-22 18:23:00', 'e39370bb2528a1d0a521dff50037f206', '94.134.4.177'),
	(51, 1, 'User wurde erfolgreich ausgeloggt.', NULL, NULL, NULL, 'Google Chrome, Version: 46.0.2490.86 auf Windows', '2015-11-27 18:32:17', '658f90bfcf4a676c0a60582bcbe58337', '95.90.250.104');
/*!40000 ALTER TABLE `akb_account_logs` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_account_token
CREATE TABLE IF NOT EXISTS `akb_account_token` (
  `uid` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_account_token: ~1 rows (ungefähr)
DELETE FROM `akb_account_token`;
/*!40000 ALTER TABLE `akb_account_token` DISABLE KEYS */;
INSERT INTO `akb_account_token` (`uid`, `token`) VALUES
	(13, '11f49301d4f5ea83b89e4174d513fbff9c3640826201d730bef80c0d95390a66');
/*!40000 ALTER TABLE `akb_account_token` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_blocked_ip
CREATE TABLE IF NOT EXISTS `akb_blocked_ip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(30) NOT NULL DEFAULT '0',
  `bad_logins` int(1) unsigned NOT NULL DEFAULT '0',
  `last_try` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`ip`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_blocked_ip: ~11 rows (ungefähr)
DELETE FROM `akb_blocked_ip`;
/*!40000 ALTER TABLE `akb_blocked_ip` DISABLE KEYS */;
INSERT INTO `akb_blocked_ip` (`id`, `ip`, `bad_logins`, `last_try`) VALUES
	(1, '95.90.251.2', 2, '2015-09-14 11:50:33'),
	(2, '94.134.249.154', 0, '2015-09-13 10:43:23'),
	(3, '84.161.244.70', 0, '2015-09-14 11:57:23'),
	(4, '84.161.246.172', 2, '2015-09-15 16:48:30'),
	(5, '95.90.250.199', 0, '2015-09-22 22:00:54'),
	(6, '176.0.19.228', 0, '2015-09-21 09:22:05'),
	(7, '91.14.32.113', 0, '2015-09-22 17:16:58'),
	(8, '46.114.6.110', 0, '2015-09-23 07:44:41'),
	(9, '95.90.250.250', 0, '2015-09-23 11:43:53'),
	(10, '95.90.251.16', 0, '2015-10-14 15:50:59'),
	(11, '95.90.251.48', 0, '2015-11-15 20:40:44');
/*!40000 ALTER TABLE `akb_blocked_ip` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_board_categories
CREATE TABLE IF NOT EXISTS `akb_board_categories` (
  `id` int(5) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_board_categories: ~5 rows (ungefähr)
DELETE FROM `akb_board_categories`;
/*!40000 ALTER TABLE `akb_board_categories` DISABLE KEYS */;
INSERT INTO `akb_board_categories` (`id`, `name`) VALUES
	(1, 'Gilden News'),
	(2, 'Mitglieder'),
	(3, 'Smalltalk'),
	(4, 'Technischer Support'),
	(5, 'Archiv');
/*!40000 ALTER TABLE `akb_board_categories` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_chat_private
CREATE TABLE IF NOT EXISTS `akb_chat_private` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_key` varchar(200) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `time_posted` int(20) NOT NULL,
  `time_posted_readable` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_chat_private: ~0 rows (ungefähr)
DELETE FROM `akb_chat_private`;
/*!40000 ALTER TABLE `akb_chat_private` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_chat_private` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_chat_public
CREATE TABLE IF NOT EXISTS `akb_chat_public` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pub_id` int(11) DEFAULT NULL,
  `userid` int(11) NOT NULL,
  `time_posted` int(20) NOT NULL,
  `time_posted_readable` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_chat_public: ~7 rows (ungefähr)
DELETE FROM `akb_chat_public`;
/*!40000 ALTER TABLE `akb_chat_public` DISABLE KEYS */;
INSERT INTO `akb_chat_public` (`id`, `pub_id`, `userid`, `time_posted`, `time_posted_readable`, `content`) VALUES
	(1, NULL, 1, 1442132866, '2015-09-13 10:27:46', 'Willkommen im Foren-Chat von Bane of the Legion!'),
	(2, NULL, 12, 1442328684, '2015-09-15 16:51:24', 'Meep'),
	(3, NULL, 2, 1442739189, '2015-09-20 10:53:09', 'Blblb :D'),
	(4, NULL, 2, 1442739195, '2015-09-20 10:53:15', '#mature'),
	(5, NULL, 12, 1442743203, '2015-09-20 12:00:03', 'Hätten mal lieber &quot;Mature for the Legion&quot; nehmen sollen '),
	(6, NULL, 1, 1442820195, '2015-09-21 09:23:15', 'Voll Legionsgewalt'),
	(7, NULL, 12, 1442863867, '2015-09-21 21:31:07', 'Yaaay, Gewalt o.o');
/*!40000 ALTER TABLE `akb_chat_public` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_chat_rooms
CREATE TABLE IF NOT EXISTS `akb_chat_rooms` (
  `room_key` varchar(200) NOT NULL,
  `user_1` int(11) NOT NULL,
  `user_2` int(11) NOT NULL,
  PRIMARY KEY (`room_key`,`user_1`,`user_2`),
  UNIQUE KEY `room_key` (`room_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_chat_rooms: ~3 rows (ungefähr)
DELETE FROM `akb_chat_rooms`;
/*!40000 ALTER TABLE `akb_chat_rooms` DISABLE KEYS */;
INSERT INTO `akb_chat_rooms` (`room_key`, `user_1`, `user_2`) VALUES
	('4cbb9adb79d69656aa7efa76b9e39a3f', 2, 1),
	('8a656110740ca55e6bd49149425a9c5c', 13, 1),
	('a33e821e39d0c5d1e0a8a47c2d7ede1a', 11, 1);
/*!40000 ALTER TABLE `akb_chat_rooms` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_forum_accountdata
CREATE TABLE IF NOT EXISTS `akb_forum_accountdata` (
  `account_id` int(11) DEFAULT NULL,
  `thread_id` int(11) DEFAULT NULL,
  `unread` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Userdaten der Threads';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_forum_accountdata: ~0 rows (ungefähr)
DELETE FROM `akb_forum_accountdata`;
/*!40000 ALTER TABLE `akb_forum_accountdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_forum_accountdata` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_forum_main
CREATE TABLE IF NOT EXISTS `akb_forum_main` (
  `id` int(1) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `theme_ID` int(1) DEFAULT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `icon_id` int(2) DEFAULT '1',
  `title` varchar(200) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `closed` int(1) unsigned zerofill NOT NULL DEFAULT '0',
  `category` int(11) DEFAULT NULL,
  `guest_posts` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Hauptforen';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_forum_main: ~6 rows (ungefähr)
DELETE FROM `akb_forum_main`;
/*!40000 ALTER TABLE `akb_forum_main` DISABLE KEYS */;
INSERT INTO `akb_forum_main` (`id`, `theme_ID`, `icon`, `icon_id`, `title`, `description`, `closed`, `category`, `guest_posts`) VALUES
	(1, NULL, NULL, 1, 'Neuigkeiten', 'Ankündigungen und vieles mehr!', 0, 1, 0),
	(2, NULL, NULL, 1, 'Planungen', 'Geplante Events und co.', 0, 1, 0),
	(3, NULL, NULL, 1, 'Mitglieder Informationen', 'Hier könnt ihr Informationen zu einzelnen Mitgliedern finden!', 0, 2, 0),
	(4, NULL, NULL, 1, 'Off-Topic', 'Alles was keinen Platz in anderen Bereichen findet, kommt hier rein!', 0, 3, 0),
	(5, NULL, NULL, 1, 'Bugtracker', 'Ihr habt einen Bug auf der Homepage gefunden? Teilt ihn hier dem Entwickler mit!', 0, 4, 0),
	(6, NULL, NULL, 1, 'Archiv', 'Hier sind alle archivierten Themen zu finden.', 0, 5, 0);
/*!40000 ALTER TABLE `akb_forum_main` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_forum_post_create_save
CREATE TABLE IF NOT EXISTS `akb_forum_post_create_save` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(150) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` mediumtext,
  PRIMARY KEY (`token`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_forum_post_create_save: ~0 rows (ungefähr)
DELETE FROM `akb_forum_post_create_save`;
/*!40000 ALTER TABLE `akb_forum_post_create_save` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_forum_post_create_save` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_forum_read_data
CREATE TABLE IF NOT EXISTS `akb_forum_read_data` (
  `account_id` int(5) NOT NULL,
  `thread_id` int(5) NOT NULL,
  `board_id` int(5) NOT NULL,
  PRIMARY KEY (`account_id`,`thread_id`,`board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Read / Unread System';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_forum_read_data: ~5 rows (ungefähr)
DELETE FROM `akb_forum_read_data`;
/*!40000 ALTER TABLE `akb_forum_read_data` DISABLE KEYS */;
INSERT INTO `akb_forum_read_data` (`account_id`, `thread_id`, `board_id`) VALUES
	(1, 1, 1),
	(1, 2, 3),
	(2, 1, 1),
	(12, 2, 3),
	(13, 2, 3);
/*!40000 ALTER TABLE `akb_forum_read_data` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_forum_subdata
CREATE TABLE IF NOT EXISTS `akb_forum_subdata` (
  `sub_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  PRIMARY KEY (`sub_id`,`user_id`,`thread_id`),
  KEY `sub_id` (`sub_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Abo System';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_forum_subdata: ~1 rows (ungefähr)
DELETE FROM `akb_forum_subdata`;
/*!40000 ALTER TABLE `akb_forum_subdata` DISABLE KEYS */;
INSERT INTO `akb_forum_subdata` (`sub_id`, `user_id`, `thread_id`) VALUES
	(2, 1, 2);
/*!40000 ALTER TABLE `akb_forum_subdata` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_forum_thread
CREATE TABLE IF NOT EXISTS `akb_forum_thread` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_forum_id` int(10) NOT NULL DEFAULT '0',
  `icon` int(1) unsigned NOT NULL DEFAULT '0',
  `icon_id` int(1) DEFAULT '1',
  `title` text NOT NULL,
  `description` text,
  `date_created` int(10) DEFAULT NULL,
  `last_replyTime` int(10) DEFAULT NULL,
  `rating` int(1) NOT NULL DEFAULT '0',
  `rating_votes` int(5) DEFAULT '0',
  `author_id` int(11) DEFAULT NULL,
  `last_post_author_id` int(11) DEFAULT NULL,
  `posts` int(50) NOT NULL DEFAULT '0',
  `views` int(10) NOT NULL DEFAULT '0',
  `closed` int(1) DEFAULT '0',
  `last_activity` int(1) DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Foren Threads';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_forum_thread: ~2 rows (ungefähr)
DELETE FROM `akb_forum_thread`;
/*!40000 ALTER TABLE `akb_forum_thread` DISABLE KEYS */;
INSERT INTO `akb_forum_thread` (`id`, `main_forum_id`, `icon`, `icon_id`, `title`, `description`, `date_created`, `last_replyTime`, `rating`, `rating_votes`, `author_id`, `last_post_author_id`, `posts`, `views`, `closed`, `last_activity`) VALUES
	(1, 1, 0, 1, 'Informationen über "Bane of the Legion"', NULL, 1442132786, 1442132786, 0, 0, 1, 1, 0, 43, 1, 1),
	(2, 3, 0, 1, '[SAMMLUNG] Abwesenheit', NULL, 1442940889, 1442940889, 0, 0, 1, 1, 0, 14, 0, 1);
/*!40000 ALTER TABLE `akb_forum_thread` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_forum_thread_create_save
CREATE TABLE IF NOT EXISTS `akb_forum_thread_create_save` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(150) NOT NULL,
  `board_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `content` mediumtext,
  PRIMARY KEY (`token`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_forum_thread_create_save: ~0 rows (ungefähr)
DELETE FROM `akb_forum_thread_create_save`;
/*!40000 ALTER TABLE `akb_forum_thread_create_save` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_forum_thread_create_save` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_forum_thread_posts
CREATE TABLE IF NOT EXISTS `akb_forum_thread_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT '0',
  `date_posted` varchar(50) NOT NULL,
  `date_edited` varchar(50) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_forum_thread_posts: ~2 rows (ungefähr)
DELETE FROM `akb_forum_thread_posts`;
/*!40000 ALTER TABLE `akb_forum_thread_posts` DISABLE KEYS */;
INSERT INTO `akb_forum_thread_posts` (`id`, `thread_id`, `author_id`, `date_posted`, `date_edited`, `text`) VALUES
	(1, 1, 1, '1442132786', '2015-11-08 08:15:14', '<span style="font-size:16px"><span style="font-family:georgia,serif"><span style="color:#FF8C00">Wir, &quot;Bane of the Legion&quot;, beheimatet auf dem Server Arthas-EU, sind eine aufstrebende Gilde, welche PvP sowie PvE und hier und dort mal ein wenig Rollenspiel betreibt.<br />\r\nWir sind also f&uuml;r alles m&ouml;gliche offen und kommen dem auch nach.<br />\r\n<br />\r\nRaids sind ab Legion geplant und werden dann auch regelm&auml;&szlig;ig bestritten.<br />\r\nBei uns herrscht ein angenehmes Klima, in welchem jeder seinen Platz findet.<br />\r\nSpammer, Flamer/Hater bilden dort jedoch eine Ausnahme und werden strikt geahndet.<br />\r\n<br />\r\nDie Legionsfl&uuml;che sind ein absolut durchgeknallter Haufen, welcher auch wirklich durchgeknallte Sachen tut. Wie es sich eben geh&ouml;rt.<br />\r\n<br />\r\nDie HP ist unter &quot;www.baneofthelegion.de&quot; zu finden. Im Forum dessen befindet ihr euch jetzt grade in diesem Moment :3<br />\r\nBeide dieser Seiten sind noch WiP(Work in progress) Projekte einer Einzelperson(mir). Verzeiht daher die hier und dort sporadisch auftretenden Bugs :P<br />\r\n<br />\r\nFalls ihr NOCH mehr &uuml;ber uns erfahren wollt, so besucht </span><a href="http://www.baneofthelegion.de/?page=aboutus"><span style="color:#00FFFF">DIESE</span></a><span style="color:#FF8C00"> Seite.</span></span></span>'),
	(2, 2, 1, '1442940889', '2015-09-22 19:27:55', '<span style="font-family:georgia,serif"><span style="color:#FFA500">Hier k&ouml;nnt ihr eure Abwesenheit melden. Ob es eine verringerte Onlinezeit ist, oder Urlaub.<br />\r\n<br />\r\n<u><span style="font-size:18px"><strong><em>Die aktuelle Liste:</em></strong></span></u></span></span>\r\n\r\n<ul>\r\n	<li><span style="font-family:georgia,serif"><span style="color:#FFA500"><strong>Addi - Bromnir [Unter der Woche weniger online, daf&uuml;r am Wochenende mehr]</strong></span></span></li>\r\n</ul>\r\n');
/*!40000 ALTER TABLE `akb_forum_thread_posts` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_gallery_comments
CREATE TABLE IF NOT EXISTS `akb_gallery_comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `time_posted` int(11) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_gallery_comments: ~0 rows (ungefähr)
DELETE FROM `akb_gallery_comments`;
/*!40000 ALTER TABLE `akb_gallery_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_gallery_comments` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_gallery_data
CREATE TABLE IF NOT EXISTS `akb_gallery_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Incremental Identifier',
  `img_name` char(150) DEFAULT NULL,
  `img_display_name` char(150) DEFAULT NULL,
  `img_description` text,
  `uploaded_by_id` int(11) DEFAULT NULL,
  `upload_time` int(11) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `favorites` int(11) NOT NULL DEFAULT '0',
  `category` int(2) NOT NULL DEFAULT '0',
  `theme` int(2) NOT NULL DEFAULT '0',
  `rating` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_gallery_data: ~3 rows (ungefähr)
DELETE FROM `akb_gallery_data`;
/*!40000 ALTER TABLE `akb_gallery_data` DISABLE KEYS */;
INSERT INTO `akb_gallery_data` (`id`, `img_name`, `img_display_name`, `img_description`, `uploaded_by_id`, `upload_time`, `views`, `comments`, `favorites`, `category`, `theme`, `rating`) VALUES
	(1, 'img-d2526a5c38f08daa3043609c376d3a41.png', 'Banner', 'Das Banner von Bane of the Legion.', 1, 1442130626, 10, 0, 0, 1, 2, 0),
	(2, 'img-ed324d13599e272f4e2fd89070c19320.png', 'Shadowmoon - Wallpaper', '', 1, 1442435827, 6, 0, 0, 3, 1, 0),
	(3, 'img-a710d5276f58b3d4c46ee560423257ce.png', 'BotL Logo - Large', '', 1, 1442694386, 11, 0, 0, 1, 0, 0);
/*!40000 ALTER TABLE `akb_gallery_data` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_gallery_data_thumb
CREATE TABLE IF NOT EXISTS `akb_gallery_data_thumb` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Incremental Identifier',
  `img_name` char(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `img_name` (`img_name`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_gallery_data_thumb: ~3 rows (ungefähr)
DELETE FROM `akb_gallery_data_thumb`;
/*!40000 ALTER TABLE `akb_gallery_data_thumb` DISABLE KEYS */;
INSERT INTO `akb_gallery_data_thumb` (`id`, `img_name`) VALUES
	(3, 'thumb-a710d5276f58b3d4c46ee560423257ce.png'),
	(1, 'thumb-d2526a5c38f08daa3043609c376d3a41.png'),
	(2, 'thumb-ed324d13599e272f4e2fd89070c19320.png');
/*!40000 ALTER TABLE `akb_gallery_data_thumb` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_gallery_temp
CREATE TABLE IF NOT EXISTS `akb_gallery_temp` (
  `id` int(11) NOT NULL,
  `img_name` varchar(150) NOT NULL,
  `uploader_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `img_name` (`img_name`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_gallery_temp: 0 rows
DELETE FROM `akb_gallery_temp`;
/*!40000 ALTER TABLE `akb_gallery_temp` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_gallery_temp` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_messages
CREATE TABLE IF NOT EXISTS `akb_messages` (
  `id` int(11) DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `content` text,
  `date` timestamp NULL DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_messages: ~0 rows (ungefähr)
DELETE FROM `akb_messages`;
/*!40000 ALTER TABLE `akb_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_messages` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_message_request_data
CREATE TABLE IF NOT EXISTS `akb_message_request_data` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `last_post` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`thread_id`,`request_id`,`last_post`),
  KEY `request_id` (`request_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_message_request_data: 0 rows
DELETE FROM `akb_message_request_data`;
/*!40000 ALTER TABLE `akb_message_request_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_message_request_data` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_permission_system
CREATE TABLE IF NOT EXISTS `akb_permission_system` (
  `id` int(11) NOT NULL COMMENT 'Permission Identifier',
  `permission_name` char(64) NOT NULL COMMENT 'Group Identifier',
  `min_security_level` int(11) NOT NULL COMMENT 'The min. account level, that is needed for this permission',
  `description` tinytext NOT NULL COMMENT 'Permission description',
  PRIMARY KEY (`id`,`min_security_level`),
  UNIQUE KEY `permission_name` (`permission_name`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Permission system';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_permission_system: ~0 rows (ungefähr)
DELETE FROM `akb_permission_system`;
/*!40000 ALTER TABLE `akb_permission_system` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_permission_system` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_portal_data
CREATE TABLE IF NOT EXISTS `akb_portal_data` (
  `news_id` int(11) NOT NULL COMMENT 'Thread Identifier of the thread, which is used for the news system. If blank, it will be chosen automatically',
  `news_type` int(11) NOT NULL COMMENT 'Type of the news. | 1 = Thread, 2 = Blog, 3 = Video',
  PRIMARY KEY (`news_id`,`news_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Portal data';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_portal_data: ~1 rows (ungefähr)
DELETE FROM `akb_portal_data`;
/*!40000 ALTER TABLE `akb_portal_data` DISABLE KEYS */;
INSERT INTO `akb_portal_data` (`news_id`, `news_type`) VALUES
	(1, 1);
/*!40000 ALTER TABLE `akb_portal_data` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_protection_system_logs
CREATE TABLE IF NOT EXISTS `akb_protection_system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifier',
  `account_id` varchar(50) DEFAULT '0' COMMENT 'Identifier of the executing User',
  `user_ip` varchar(50) DEFAULT NULL COMMENT 'IP-Adress of the executing User',
  `saved_ip` varchar(50) DEFAULT NULL COMMENT 'The saved IP-Adress, that have to equal to the user_ip (for access)',
  `message` varchar(100) DEFAULT '0' COMMENT 'Internal message',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Time of the event',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Account - Schutzsystem Logs';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_protection_system_logs: ~0 rows (ungefähr)
DELETE FROM `akb_protection_system_logs`;
/*!40000 ALTER TABLE `akb_protection_system_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_protection_system_logs` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_ranks
CREATE TABLE IF NOT EXISTS `akb_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Rank Identifier',
  `rank_name` varchar(50) NOT NULL COMMENT 'Rank Name',
  PRIMARY KEY (`id`,`rank_name`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_ranks: ~0 rows (ungefähr)
DELETE FROM `akb_ranks`;
/*!40000 ALTER TABLE `akb_ranks` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_ranks` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_server_configs
CREATE TABLE IF NOT EXISTS `akb_server_configs` (
  `option_name` char(100) NOT NULL,
  `option_value` char(255) NOT NULL,
  UNIQUE KEY `config_name` (`option_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Fragile forum configs.\r\n\r\nNEVER touch this, except you know,';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_server_configs: ~17 rows (ungefähr)
DELETE FROM `akb_server_configs`;
/*!40000 ALTER TABLE `akb_server_configs` DISABLE KEYS */;
INSERT INTO `akb_server_configs` (`option_name`, `option_value`) VALUES
	('about', 'Hier sind die Member von "Bane of the Legion" zuhause!'),
	('account_validation_system', '1'),
	('can_register', '1'),
	('copyright_text', 'Forensoftware: %st - System Version: %sv - Database Version: %dbv | Copyright 2015, %sa - All rights reserved'),
	('db_version', '0.87.1'),
	('default_avatar', 'default.png'),
	('default_avatar_path', './images/avatars/'),
	('forum_description', 'News, Updates und Smalltalk von Bane of the Legion'),
	('ip_validation_system', '1'),
	('last_user_status_update', '1448645515'),
	('login_ban_system', '1'),
	('max_users', '20'),
	('software_author', 'Alexander Bretzke'),
	('software_title', 'Akasi Board'),
	('software_version', '0.73 Development Release'),
	('thread_entries_per_page', '10'),
	('user_capacity_system', '1');
/*!40000 ALTER TABLE `akb_server_configs` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_temp_uploads
CREATE TABLE IF NOT EXISTS `akb_temp_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '1',
  `file_name` varchar(250) NOT NULL DEFAULT 'Image',
  KEY `id` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_temp_uploads: 0 rows
DELETE FROM `akb_temp_uploads`;
/*!40000 ALTER TABLE `akb_temp_uploads` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_temp_uploads` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_user_hidden_boards
CREATE TABLE IF NOT EXISTS `akb_user_hidden_boards` (
  `user_id` int(5) NOT NULL,
  `cat_id` int(5) NOT NULL,
  `state` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_user_hidden_boards: ~8 rows (ungefähr)
DELETE FROM `akb_user_hidden_boards`;
/*!40000 ALTER TABLE `akb_user_hidden_boards` DISABLE KEYS */;
INSERT INTO `akb_user_hidden_boards` (`user_id`, `cat_id`, `state`) VALUES
	(1, 1, 1),
	(1, 2, 1),
	(1, 3, 1),
	(1, 4, 1),
	(1, 5, 1),
	(2, 1, 1),
	(13, 1, 1),
	(13, 2, 1);
/*!40000 ALTER TABLE `akb_user_hidden_boards` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_user_rank
CREATE TABLE IF NOT EXISTS `akb_user_rank` (
  `level` int(11) NOT NULL,
  `rank_name` char(64) NOT NULL,
  `exp_needed` int(11) NOT NULL,
  PRIMARY KEY (`level`),
  UNIQUE KEY `rank_name` (`rank_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_user_rank: ~0 rows (ungefähr)
DELETE FROM `akb_user_rank`;
/*!40000 ALTER TABLE `akb_user_rank` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_user_rank` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_user_rank_data
CREATE TABLE IF NOT EXISTS `akb_user_rank_data` (
  `user_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_user_rank_data: ~0 rows (ungefähr)
DELETE FROM `akb_user_rank_data`;
/*!40000 ALTER TABLE `akb_user_rank_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `akb_user_rank_data` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ni204675_1sql3.akb_user_sessions
CREATE TABLE IF NOT EXISTS `akb_user_sessions` (
  `id` varchar(13) NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `last_user_ip` varchar(50) DEFAULT '0',
  `current_user_ip` varchar(50) DEFAULT '0',
  `last_sid` varchar(150) DEFAULT '0',
  `sid` varchar(150) DEFAULT '0',
  `session_started` timestamp NULL DEFAULT NULL,
  `persistent_session_status` int(1) DEFAULT NULL,
  `last_activity` int(20) NOT NULL DEFAULT '0',
  `online` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Session System';

-- Exportiere Daten aus Tabelle ni204675_1sql3.akb_user_sessions: ~5 rows (ungefähr)
DELETE FROM `akb_user_sessions`;
/*!40000 ALTER TABLE `akb_user_sessions` DISABLE KEYS */;
INSERT INTO `akb_user_sessions` (`id`, `active`, `last_user_ip`, `current_user_ip`, `last_sid`, `sid`, `session_started`, `persistent_session_status`, `last_activity`, `online`) VALUES
	('1', 0, '95.90.251.48', '0', '658f90bfcf4a676c0a60582bcbe58337', NULL, '2015-11-15 20:40:44', 1, 1448645515, 0),
	('11', 1, '0', '95.90.251.2', '0', 'b37c2937a50215d347d2f8ae39c90f97', '2015-09-14 11:48:39', 1, 1442224214, 0),
	('12', 1, '0', '94.134.4.177', '0', 'e39370bb2528a1d0a521dff50037f206', '2015-11-22 18:23:00', 0, 1448212983, 0),
	('13', 1, '0', '91.14.32.113', '0', '4146c97daeb147b75d062dc4ac65ab4b', '2015-09-22 17:21:53', 1, 1442943500, 0),
	('2', 1, '0', '94.134.249.154', '0', '4b62bacb5b305b5eb02b7d2b726f5ac7', '2015-09-13 10:43:24', 1, 1444484510, 0);
/*!40000 ALTER TABLE `akb_user_sessions` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
