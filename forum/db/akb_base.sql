-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               5.6.24 - MySQL Community Server (GPL)
-- Server Betriebssystem:        Win32
-- HeidiSQL Version:             9.1.0.4906
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Struktur von Tabelle ian_forumcms.akb_account
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Account System';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_account_ban
CREATE TABLE IF NOT EXISTS `akb_account_ban` (
  `id` int(5) NOT NULL COMMENT 'User Identifier',
  `ban_type` int(1) NOT NULL COMMENT 'Type of the ban',
  `ban_duration` int(15) DEFAULT NULL COMMENT 'Ban duration in seconds',
  `banned_at` int(20) DEFAULT NULL COMMENT 'Time of the ban in seconds ( Unix Timestamp )',
  `banned_by` int(5) DEFAULT NULL COMMENT 'User Identifier of the executing banner',
  PRIMARY KEY (`id`,`ban_type`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_account_blocks
CREATE TABLE IF NOT EXISTS `akb_account_blocks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blocked_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`blocked_id`,`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_account_changelogs
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

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_account_data
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
  `design_template` int(2) NOT NULL DEFAULT '1' COMMENT 'Active design ID',
  `language` int(3) NOT NULL DEFAULT '1' COMMENT 'Active language ID',
  PRIMARY KEY (`account_id`,`username`),
  KEY `account_id` (`account_id`),
  KEY `Username` (`username`),
  CONSTRAINT `ID` FOREIGN KEY (`account_id`) REFERENCES `akb_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Username` FOREIGN KEY (`username`) REFERENCES `akb_account` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Account Daten';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_account_data_profile
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

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_account_friends
CREATE TABLE IF NOT EXISTS `akb_account_friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`friend_id`,`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_account_logs
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Account System Logs';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_blocked_ip
CREATE TABLE IF NOT EXISTS `akb_blocked_ip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(30) NOT NULL DEFAULT '0',
  `bad_logins` int(1) unsigned NOT NULL DEFAULT '0',
  `last_try` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`ip`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_board_categories
CREATE TABLE IF NOT EXISTS `akb_board_categories` (
  `id` int(5) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_chat_private
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

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_chat_public
CREATE TABLE IF NOT EXISTS `akb_chat_public` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pub_id` int(11) DEFAULT NULL,
  `userid` int(11) NOT NULL,
  `time_posted` int(20) NOT NULL,
  `time_posted_readable` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_chat_rooms
CREATE TABLE IF NOT EXISTS `akb_chat_rooms` (
  `room_key` varchar(200) NOT NULL,
  `user_1` int(11) NOT NULL,
  `user_2` int(11) NOT NULL,
  PRIMARY KEY (`room_key`,`user_1`,`user_2`),
  UNIQUE KEY `room_key` (`room_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_forum_accountdata
CREATE TABLE IF NOT EXISTS `akb_forum_accountdata` (
  `account_id` int(11) DEFAULT NULL,
  `thread_id` int(11) DEFAULT NULL,
  `unread` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Userdaten der Threads';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_forum_main
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Hauptforen';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_forum_post_create_save
CREATE TABLE IF NOT EXISTS `akb_forum_post_create_save` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(150) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` mediumtext,
  PRIMARY KEY (`token`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_forum_read_data
CREATE TABLE IF NOT EXISTS `akb_forum_read_data` (
  `account_id` int(5) NOT NULL,
  `thread_id` int(5) NOT NULL,
  `board_id` int(5) NOT NULL,
  PRIMARY KEY (`account_id`,`thread_id`,`board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Read / Unread System';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_forum_subdata
CREATE TABLE IF NOT EXISTS `akb_forum_subdata` (
  `sub_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  PRIMARY KEY (`sub_id`,`user_id`,`thread_id`),
  KEY `sub_id` (`sub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Abo System';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_forum_thread
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Foren Threads';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_forum_thread_create_save
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

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_forum_thread_posts
CREATE TABLE IF NOT EXISTS `akb_forum_thread_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT '0',
  `date_posted` varchar(50) NOT NULL,
  `date_edited` varchar(50) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_gallery_comments
CREATE TABLE IF NOT EXISTS `akb_gallery_comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `time_posted` int(11) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_gallery_data
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_gallery_data_thumb
CREATE TABLE IF NOT EXISTS `akb_gallery_data_thumb` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Incremental Identifier',
  `img_name` char(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `img_name` (`img_name`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_gallery_temp
CREATE TABLE IF NOT EXISTS `akb_gallery_temp` (
  `id` int(11) NOT NULL,
  `img_name` varchar(150) NOT NULL,
  `uploader_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `img_name` (`img_name`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_messages_admin
CREATE TABLE IF NOT EXISTS `akb_messages_admin` (
  `id` int(11) DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `content` text,
  `date` timestamp NULL DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_messages_mod
CREATE TABLE IF NOT EXISTS `akb_messages_mod` (
  `id` int(11) DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `content` text,
  `date` timestamp NULL DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_messages_user
CREATE TABLE IF NOT EXISTS `akb_messages_user` (
  `id` int(11) DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `content` text,
  `date` timestamp NULL DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_message_request_data
CREATE TABLE IF NOT EXISTS `akb_message_request_data` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `last_post` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`thread_id`,`request_id`,`last_post`),
  KEY `request_id` (`request_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_permission_system
CREATE TABLE IF NOT EXISTS `akb_permission_system` (
  `id` int(11) NOT NULL COMMENT 'Permission Identifier',
  `permission_name` char(64) NOT NULL COMMENT 'Group Identifier',
  `min_security_level` int(11) NOT NULL COMMENT 'The min. account level, that is needed for this permission',
  `description` tinytext NOT NULL COMMENT 'Permission description',
  PRIMARY KEY (`id`,`min_security_level`),
  UNIQUE KEY `permission_name` (`permission_name`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Permission system';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_portal_data
CREATE TABLE IF NOT EXISTS `akb_portal_data` (
  `news_id` int(11) NOT NULL COMMENT 'Thread Identifier of the thread, which is used for the news system. If blank, it will be chosen automatically',
  `news_type` int(11) NOT NULL COMMENT 'Type of the news. | 1 = Thread, 2 = Blog, 3 = Video',
  PRIMARY KEY (`news_id`,`news_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Portal data';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_protection_system_logs
CREATE TABLE IF NOT EXISTS `akb_protection_system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifier',
  `account_id` varchar(50) DEFAULT '0' COMMENT 'Identifier of the executing User',
  `user_ip` varchar(50) DEFAULT NULL COMMENT 'IP-Adress of the executing User',
  `saved_ip` varchar(50) DEFAULT NULL COMMENT 'The saved IP-Adress, that have to equal to the user_ip (for access)',
  `message` varchar(100) DEFAULT '0' COMMENT 'Internal message',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Time of the event',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Account - Schutzsystem Logs';

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_ranks
CREATE TABLE IF NOT EXISTS `akb_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Rank Identifier',
  `rank_name` varchar(50) NOT NULL COMMENT 'Rank Name',
  PRIMARY KEY (`id`,`rank_name`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_temp_uploads
CREATE TABLE IF NOT EXISTS `akb_temp_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '1',
  `file_name` varchar(250) NOT NULL DEFAULT 'Image',
  KEY `id` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_user_hidden_boards
CREATE TABLE IF NOT EXISTS `akb_user_hidden_boards` (
  `user_id` int(5) NOT NULL,
  `cat_id` int(5) NOT NULL,
  `state` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_user_rank
CREATE TABLE IF NOT EXISTS `akb_user_rank` (
  `level` int(11) NOT NULL,
  `rank_name` char(64) NOT NULL,
  `exp_needed` int(11) NOT NULL,
  PRIMARY KEY (`level`),
  UNIQUE KEY `rank_name` (`rank_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_user_rank_data
CREATE TABLE IF NOT EXISTS `akb_user_rank_data` (
  `user_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle ian_forumcms.akb_user_sessions
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

-- Daten Export vom Benutzer nicht ausgewählt
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
