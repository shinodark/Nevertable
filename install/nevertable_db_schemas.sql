-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Dimanche 01 Juillet 2007 à 02:42
-- Version du serveur: 5.0.38
-- Version de PHP: 5.2.1
-- 
-- Base de données: `shinobufan`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_com`
-- 

CREATE TABLE `nvrtbl_com` (
  `id` int(11) NOT NULL auto_increment,
  `replay_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `content` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ind_replay` (`replay_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='commentaires de la nevertable';

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_conf`
-- 

CREATE TABLE `nvrtbl_conf` (
  `conf_name` varchar(255) NOT NULL default '',
  `conf_value` text,
  `conf_desc` text,
  PRIMARY KEY  (`conf_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_maps`
-- 

CREATE TABLE `nvrtbl_maps` (
  `id` int(11) NOT NULL auto_increment,
  `set_id` tinyint(4) NOT NULL default '0',
  `level_num` tinyint(4) NOT NULL default '0',
  `map_solfile` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_online`
-- 

CREATE TABLE `nvrtbl_online` (
  `user_id` int(11) NOT NULL default '1',
  `ident` varchar(200) NOT NULL default '',
  `logged_time` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_rec`
-- 

CREATE TABLE `nvrtbl_rec` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `levelset` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `time` double NOT NULL default '0',
  `coins` int(11) NOT NULL default '0',
  `replay` text NOT NULL,
  `type` tinyint(4) NOT NULL default '0',
  `folder` tinyint(4) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `isbest` tinyint(4) NOT NULL default '0',
  `comments_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `ind_level` (`levelset`,`level`),
  KEY `ind_folder` (`folder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_sets`
-- 

CREATE TABLE `nvrtbl_sets` (
  `id` int(11) NOT NULL auto_increment,
  `set_name` varchar(64) NOT NULL default '',
  `set_path` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_tags`
-- 

CREATE TABLE `nvrtbl_tags` (
  `id` int(11) NOT NULL auto_increment,
  `pseudo` mediumtext NOT NULL,
  `link` mediumtext NOT NULL,
  `content` text NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ip_log` varchar(16) NOT NULL,
  `pub` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_users`
-- 

CREATE TABLE `nvrtbl_users` (
  `id` int(11) NOT NULL auto_increment,
  `level` tinyint(4) NOT NULL default '0',
  `pseudo` varchar(32) NOT NULL default '',
  `passwd` varchar(40) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `user_limit` smallint(6) NOT NULL default '0',
  `user_comments_limit` smallint(6) NOT NULL,
  `user_sidebar_comments` smallint(6) NOT NULL default '0',
  `user_sidebar_comlength` smallint(6) NOT NULL default '0',
  `user_sort` tinyint(4) NOT NULL default '0',
  `user_theme` varchar(40) NOT NULL default '',
  `user_lang` varchar(2) NOT NULL default 'en',
  `user_avatar` varchar(40) NOT NULL default '',
  `user_speech` text NOT NULL,
  `user_localisation` varchar(40) NOT NULL default '',
  `user_web` varchar(80) NOT NULL default '',
  `stat_total_records` int(11) NOT NULL default '0',
  `stat_best_records` int(11) NOT NULL default '0',
  `stat_comments` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `ind_pseudo` (`pseudo`(1))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- 
-- Contenu de la table `nvrtbl_conf`
-- 

INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('version', '2.99.1', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('nvtbl_path', 'nevertable-trunk/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('admin_mail', 'nevertable@nevercorner.net', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('replay_dir', 'replays/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('image_dir', 'images/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('shot_dir', 'images/levelshots/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('smilies_dir', 'smilies/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('theme_dir', 'themes/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('avatar_dir', 'avatars/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('cache_dir', 'cache/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('tmp_dir', 'tmp/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('folders_desc', 'folder_desc.txt', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('limit', '50', 'Number of record default limit');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('date_format', 'dS of F Y h:i:s A', 'date format for comments');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('date_format_mini', 'dS of F h:i:s A', 'date format for comments preview');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('sidebar_comments', '8', 'Number of comments in sidebar (default value)');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('sidebar_comlength', '40', 'Length of comments in sidebar (default value)');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('sidebar_autowrap', '25', 'Autowrap limit for comments in sidebar (default value)');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('upload_size_max', '2048000', 'Maximum upload size in bytes');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('avatar_size_max', '30720', 'avatar max weight in bytes');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('avatar_width_max', '128', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('avatar_height_max', '128', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('photo_quality', '90', 'Quality of resized pictures');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('profile_quote_max', '500', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('cookie_name', 'nvrtbl_data', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('cookie_expire', '31536000', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('cookie_path', '/table/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('cookie_domain', 'nevercorner.net', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('online_idletime', '600', 'Time in seconds after an online user become idle.');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('theme_default', 'default', 'Default theme');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('tag_maxsize', '1024', 'Maximum size for a tag');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('tag_limit', '20', 'Number of tags displayed');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('tag_flood_limit', '300', 'Seconds allowed between two tags.');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('neverstats_liste', 'contrib/neverstats/liste.txt', 'File containing stats dump of a folder.');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('lang_dir', 'lang/', NULL);
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('comments_limit', '10', 'Number of comments displayed on one page');
INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES ('default_lang', 'en', 'Default language');

