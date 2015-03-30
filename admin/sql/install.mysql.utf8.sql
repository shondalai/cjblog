CREATE TABLE IF NOT EXISTS `#__cjblog_badge_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `rule_name` varchar(64) NOT NULL,
  `rule_content` varchar(999) NOT NULL,
  `asset_name` varchar(64) NOT NULL,
  `asset_title` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `num_assigned` int(10) unsigned NOT NULL DEFAULT '0',
  `access` int(6) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cjblog_badges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `alias` varchar(32) NOT NULL,
  `description` varchar(999) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `icon` varchar(64) DEFAULT NULL,
  `css_class` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_CJBLOG_BADGES_NAME_UNIQ` (`alias`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cjblog_content` (
  `id` int(11) NOT NULL,
  `favorites` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cjblog_favorites` (
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`content_id`,`user_id`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cjblog_point_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 NOT NULL,
  `points` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `asset_name` varchar(128) NOT NULL,
  `auto_approve` tinyint(1) NOT NULL DEFAULT '1',
  `conditional_rules` VARCHAR(5120),
  `access` int(6) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_points_rule_name` (`name`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cjblog_points` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `rule_id` int(10) unsigned NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `points` int(11) NOT NULL DEFAULT '0',
  `ref_id` varchar(255) DEFAULT NULL,
  `description` varchar(999) DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cjblog_ranks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `classname` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cjblog_user_badge_map` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `badge_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_assigned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rule_id` int(10) unsigned NOT NULL,
  `ref_id` int(10) unsigned DEFAULT NULL
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cjblog_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `about` varchar(999) DEFAULT NULL,
  `num_articles` int(10) unsigned NOT NULL DEFAULT '0',
  `avatar` varchar(32) DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT '0',
  `num_badges` int(10) unsigned NOT NULL DEFAULT '0',
  `country` varchar(255) DEFAULT NULL,
  `user_rank` int(10) unsigned DEFAULT NULL,
  `profile_views` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cjlib_config` (
  `config_name` varchar(255) NOT NULL,
  `config_value` text NOT NULL,
  PRIMARY KEY (`config_name`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `#__cjblog_tagmap` (
  `tag_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`tag_id`,`item_id`),
  KEY `IDX_CJBLOG_TAGSMAP_ITEMID` (`item_id`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `#__cjblog_tags` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tag_text` varchar(50) NOT NULL DEFAULT '0',
  `alias` varchar(50) NOT NULL,
  `description` MEDIUMTEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_CJBLOG_TAGS_TAGTEXT` (`tag_text`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `#__cjblog_tags_stats` (
  `tag_id` int(11) NOT NULL,
  `num_items` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tag_id`)
)  DEFAULT CHARSET=utf8;