-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Samedi 19 Mai 2007 à 22:28
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='commentaires de la nevertable';

-- --------------------------------------------------------

-- 
-- Structure de la table `nvrtbl_online`
-- 

CREATE TABLE `nvrtbl_online` (
  `user_id` int(11) NOT NULL default '1',
  `ident` varchar(200) NOT NULL default '',
  `logged_time` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  `user_sort` varchar(10) character set latin1 collate latin1_bin NOT NULL default '',
  `user_theme` varchar(40) character set latin1 collate latin1_bin NOT NULL default '',
  `user_avatar` varchar(40) character set latin1 collate latin1_bin NOT NULL default '',
  `user_speech` text NOT NULL,
  `user_localisation` varchar(40) character set latin1 collate latin1_bin NOT NULL default '',
  `user_web` varchar(80) character set latin1 collate latin1_bin NOT NULL default '',
  `stat_total_records` int(11) NOT NULL default '0',
  `stat_best_records` int(11) NOT NULL default '0',
  `stat_comments` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `ind_pseudo` (`pseudo`(1))
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


-- 
-- Structure de la table `nvrtbl_conf`
-- 

CREATE TABLE `nvrtbl_conf` (
  `conf_name` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `conf_value` text,
  `conf_desc` text,
  PRIMARY KEY  (`conf_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `nvrtbl_conf`
-- 

INSERT INTO `nvrtbl_conf` (`conf_name`, `conf_value`, `conf_desc`) VALUES 
(0x76657273696f6e, '2.1.2+svn-11012006', NULL),
(0x6e7674626c5f70617468, 'table/', NULL),
(0x61646d696e5f6d61696c, 'nevertable@nevercorner.net', NULL),
(0x7265706c61795f646972, 'replays/', NULL),
(0x696d6167655f646972, 'images/', NULL),
(0x73686f745f646972, 'images/levelshots/', NULL),
(0x736d696c6965735f646972, 'smilies/', NULL),
(0x7468656d655f646972, 'themes/', NULL),
(0x6176617461725f646972, 'avatars/', NULL),
(0x63616368655f646972, 'cache/', NULL),
(0x746d705f646972, 'tmp/', NULL),
(0x666f6c646572735f64657363, 'folder_desc.txt', NULL),
(0x6c696d6974, '50', 'Number of record default limit'),
(0x646174655f666f726d6174, 'dS of F Y h:i:s A', 'date format for comments'),
(0x646174655f666f726d61745f6d696e69, 'dS of F h:i:s A', 'date format for comments preview'),
(0x736964656261725f636f6d6d656e7473, '8', 'Number of comments in sidebar (default value)'),
(0x736964656261725f636f6d6c656e677468, '40', 'Length of comments in sidebar (default value)'),
(0x736964656261725f6175746f77726170, '25', 'Autowrap limit for comments in sidebar (default value)'),
(0x75706c6f61645f73697a655f6d6178, '2048000', 'Maximum upload size in bytes'),
(0x6176617461725f73697a655f6d6178, '30720', 'avatar max weight in bytes'),
(0x6176617461725f77696474685f6d6178, '128', NULL),
(0x6176617461725f6865696768745f6d6178, '128', NULL),
(0x70726f66696c655f71756f74655f6d6178, '500', NULL),
(0x636f6f6b69655f6e616d65, 'nvrtbl_data', NULL),
(0x636f6f6b69655f657870697265, '31536000', NULL),
(0x636f6f6b69655f70617468, '/table/', NULL),
(0x636f6f6b69655f646f6d61696e, 'nevercorner.net', NULL),
(0x6f6e6c696e655f69646c6574696d65, '600', 'Time in seconds after an online user become idle.'),
(0x7468656d655f64656661756c74, 'Sulfur', 'Default theme'),
(0x7461675f6d617873697a65, '1024', 'Maximum size for a tag'),
(0x7461675f6c696d6974, '20', 'Number of tags displayed'),
(0x7461675f666c6f6f645f6c696d6974, '300', 'Seconds allowed between two tags.'),
(0x6e6576657273746174735f6c69737465, 'contrib/neverstats/liste.txt', 'File containing stats dump of a folder.');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=76 ;

-- 
-- Contenu de la table `nvrtbl_maps`
-- 

INSERT INTO `nvrtbl_maps` (`id`, `set_id`, `level_num`, `level_name`, `map_solfile`) VALUES 
(1, 1, 1, 'easy.sol', 'easy.sol'),
(2, 1, 2, 'peasy.sol', 'peasy.sol'),
(3, 1, 3, 'coins.sol', 'coins.sol'),
(4, 1, 4, 'goslow.sol', 'goslow.sol'),
(5, 1, 5, 'fence.sol', 'fence.sol'),
(6, 1, 6, 'bumper.sol', 'bumper.sol'),
(7, 1, 7, 'maze.sol', 'maze.sol'),
(8, 1, 8, 'goals.sol', 'goals.sol'),
(9, 1, 9, 'hole.sol', 'hole.sol'),
(10, 1, 10, 'bumps.sol', 'bumps.sol'),
(11, 1, 11, 'corners.sol', 'corners.sol'),
(12, 1, 12, 'easytele.sol', 'easytele.sol'),
(13, 1, 13, 'zigzag.sol', 'zigzag.sol'),
(14, 1, 14, 'greed.sol', 'greed.sol'),
(15, 1, 15, 'mover.sol', 'mover.sol'),
(16, 1, 16, 'wakka.sol', 'wakka.sol'),
(17, 1, 17, 'curbs.sol', 'curbs.sol'),
(18, 1, 18, 'curved.sol', 'curved.sol'),
(19, 1, 19, 'stairs.sol', 'stairs.sol'),
(20, 1, 20, 'rampdn.sol', 'rampdn.sol'),
(21, 1, 21, 'sync.sol', 'sync.sol'),
(22, 1, 22, 'plinko.sol', 'plinko.sol'),
(23, 1, 23, 'drops.sol', 'drops.sol'),
(24, 1, 24, 'locks.sol', 'locks.sol'),
(25, 1, 25, 'spiralin.sol', 'spiralin.sol'),
(26, 2, 1, 'grid.sol', 'grid.sol'),
(27, 2, 2, 'four.sol', 'four.sol'),
(28, 2, 3, 'telemaze.sol', 'telemaze.sol'),
(29, 2, 4, 'spiraldn.sol', 'spiraldn.sol'),
(30, 2, 5, 'islands.sol', 'islands.sol'),
(31, 2, 6, 'angle.sol', 'angle.sol'),
(32, 2, 7, 'spiralup.sol', 'spiralup.sol'),
(33, 2, 8, 'rampup.sol', 'rampup.sol'),
(34, 2, 9, 'check.sol', 'check.sol'),
(35, 2, 10, 'risers.sol', 'risers.sol'),
(36, 2, 11, 'tilt.sol', 'tilt.sol'),
(37, 2, 12, 'gaps.sol', 'gaps.sol'),
(38, 2, 13, 'pyramid.sol', 'pyramid.sol'),
(39, 2, 14, 'quads.sol', 'quads.sol'),
(40, 2, 15, 'frogger.sol', 'frogger.sol'),
(41, 2, 16, 'timer.sol', 'timer.sol'),
(42, 2, 17, 'spread.sol', 'spread.sol'),
(43, 2, 18, 'hump.sol', 'hump.sol'),
(44, 2, 19, 'movers.sol', 'movers.sol'),
(45, 2, 20, 'teleport.sol', 'teleport.sol'),
(46, 2, 21, 'poker.sol', 'poker.sol'),
(47, 2, 22, 'invis.sol', 'invis.sol'),
(48, 2, 23, 'ring.sol', 'ring.sol'),
(49, 2, 24, 'pipe.sol', 'pipe.sol'),
(50, 2, 25, 'title.sol', 'title.sol'),
(51, 3, 1, 'descent.sol', 'descent.sol'),
(52, 3, 2, 'dance2.sol', 'dance2.sol'),
(53, 3, 3, 'snow.sol', 'snow.sol'),
(54, 3, 4, 'drive1.sol', 'drive1.sol'),
(55, 3, 5, 'glasstower.sol', 'glasstower.sol'),
(56, 3, 6, 'scrambling.sol', 'scrambling.sol'),
(57, 3, 7, 'trust.sol', 'trust.sol'),
(58, 3, 8, 'loop1.sol', 'loop1.sol'),
(59, 3, 9, 'maze1.sol', 'maze1.sol'),
(60, 3, 10, 'up.sol', 'up.sol'),
(61, 3, 11, 'circuit2.sol', 'circuit2.sol'),
(62, 3, 12, 'comeback.sol', 'comeback.sol'),
(63, 3, 13, 'maze2.sol', 'maze2.sol'),
(64, 3, 14, 'earthquake.sol', 'earthquake.sol'),
(65, 3, 15, 'circuit1.sol', 'circuit1.sol'),
(66, 3, 16, 'turn.sol', 'turn.sol'),
(67, 3, 17, 'assault.sol', 'assault.sol'),
(68, 3, 18, 'narrow.sol', 'narrow.sol'),
(69, 3, 19, 'loop2.sol', 'loop2.sol'),
(70, 3, 20, 'drive2.sol', 'drive2.sol'),
(71, 3, 21, 'running.sol', 'running.sol'),
(72, 3, 22, 'bombman.sol', 'bombman.sol'),
(73, 3, 23, 'climb.sol', 'climb.sol'),
(74, 3, 24, 'dance1.sol', 'dance1.sol'),
(75, 3, 25, 'hard.sol', 'hard.sol');
