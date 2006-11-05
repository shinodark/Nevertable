-- phpMyAdmin SQL Dump
-- version OVH
-- http://www.phpmyadmin.net
-- 
-- Serveur: sql10
-- Généré le : Dimanche 05 Novembre 2006 à 21:51
-- Version du serveur: 4.0.25
-- Version de PHP: 4.4.4
-- 
-- Base de données: `nevercor`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_com`
-- 

CREATE TABLE `nvrtbl_com` (
  `id` int(11) NOT NULL auto_increment,
  `replay_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `timestamp` timestamp(14) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ind_id` (`id`),
  KEY `ind_replay` (`replay_id`)
) TYPE=MyISAM COMMENT='commentaires de la nevertable';

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_conf`
-- 

CREATE TABLE `nvrtbl_conf` (
  `conf_name` varchar(255) binary NOT NULL default '',
  `conf_value` text,
  `conf_desc` text,
  PRIMARY KEY  (`conf_name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_maps`
-- 

CREATE TABLE `nvrtbl_maps` (
  `id` int(11) NOT NULL auto_increment,
  `set_id` tinyint(4) NOT NULL default '0',
  `level_num` tinyint(4) NOT NULL default '0',
  `level_name` varchar(64) NOT NULL default '',
  `map_solfile` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_online`
-- 

CREATE TABLE `nvrtbl_online` (
  `user_id` int(11) NOT NULL default '1',
  `ident` varchar(200) NOT NULL default '',
  `logged_time` int(11) NOT NULL default '0'
) TYPE=MyISAM;

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
  `timestamp` timestamp(14) NOT NULL,
  `isbest` tinyint(4) NOT NULL default '0',
  `comments_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `ind_id` (`id`),
  KEY `ind_level` (`levelset`,`level`),
  KEY `ind_folder` (`folder`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_sets`
-- 

CREATE TABLE `nvrtbl_sets` (
  `id` int(11) NOT NULL auto_increment,
  `set_name` varchar(64) NOT NULL default '',
  `set_path` varchar(64) NOT NULL default '',
  `author` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_tags`
-- 

CREATE TABLE `nvrtbl_tags` (
  `id` int(11) NOT NULL auto_increment,
  `pseudo` mediumtext NOT NULL,
  `link` mediumtext NOT NULL,
  `content` text NOT NULL,
  `timestamp` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_users`
-- 

CREATE TABLE `nvrtbl_users` (
  `id` int(11) NOT NULL auto_increment,
  `level` tinyint(4) NOT NULL default '0',
  `pseudo` varchar(32) NOT NULL default '',
  `passwd` varchar(32) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `user_limit` smallint(6) NOT NULL default '0',
  `user_sidebar_comments` smallint(6) NOT NULL default '0',
  `user_sidebar_comlength` smallint(6) NOT NULL default '0',
  `user_sort` varchar(10) binary NOT NULL default '',
  `user_theme` varchar(40) binary NOT NULL default '',
  `user_avatar` varchar(40) binary NOT NULL default '',
  `user_speech` text NOT NULL,
  `user_localisation` varchar(40) binary NOT NULL default '',
  `user_web` varchar(80) binary NOT NULL default '',
  `stat_total_records` int(11) NOT NULL default '0',
  `stat_best_records` int(11) NOT NULL default '0',
  `stat_comments` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `ind_pseudo` (`pseudo`(1))
) TYPE=MyISAM;
