CREATE TABLE IF NOT EXISTS `#__cjblog_badges` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(32) NOT NULL,
	`alias` VARCHAR(32) NOT NULL,
	`description` VARCHAR(999) NOT NULL,
	`published` TINYINT(3) NOT NULL,
	`icon` VARCHAR(64) NULL DEFAULT NULL,
	`css_class` VARCHAR(64) NULL DEFAULT NULL,
	`access` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`created_by` INT(10) UNSIGNED NOT NULL,
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `IDX_CJBLOG_BADGES_NAME_UNIQ` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_badge_rules` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`badge_id` INT(10) UNSIGNED NOT NULL,
	`title` VARCHAR(64) NOT NULL,
	`description` TEXT NOT NULL,
	`rule_name` VARCHAR(64) NOT NULL,
	`rule_content` VARCHAR(999) NOT NULL,
	`asset_name` VARCHAR(64) NOT NULL,
	`asset_title` VARCHAR(255) NOT NULL,
	`published` TINYINT(3) NOT NULL,
	`ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`num_assigned` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`access` INT(6) UNSIGNED NOT NULL DEFAULT '1',
	`created_by` INT(10) UNSIGNED NOT NULL,
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_content` (
	`id` INT(11) NOT NULL,
	`favorites` INT(11) NOT NULL DEFAULT '0',
	`comments` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_email_templates` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL,
	`description` MEDIUMTEXT NOT NULL,
	`published` TINYINT(4) NOT NULL DEFAULT '0',
	`email_type` VARCHAR(45) NOT NULL,
	`ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`access` INT(10) UNSIGNED NOT NULL DEFAULT '1',
	`language` CHAR(7) NOT NULL DEFAULT '*',
	`publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_favorites` (
	`content_id` INT(11) NOT NULL,
	`user_id` INT(11) NOT NULL,
	`created` DATETIME NOT NULL,
	PRIMARY KEY (`content_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_followers` (
	`user_id` INT(10) UNSIGNED NOT NULL,
	`follower_id` INT(10) UNSIGNED NOT NULL,
	`since` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	UNIQUE INDEX `idx_cjblog_followers_uniq` (`user_id`, `follower_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_points` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`rule_id` INT(10) UNSIGNED NOT NULL,
	`published` TINYINT(3) NOT NULL,
	`points` INT(11) NOT NULL DEFAULT '0',
	`ref_id` VARCHAR(255) NULL DEFAULT NULL,
	`description` VARCHAR(999) NULL DEFAULT NULL,
	`created_by` INT(10) UNSIGNED NOT NULL,
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	KEY `idx_cjblog_points_rule_id` (`rule_id`),
	KEY `idx_cjblog_points_user_id` (`user_id`),
	KEY `idx_cjblog_points_state` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_points_rules` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL,
	`description` MEDIUMTEXT NOT NULL COLLATE 'latin1_swedish_ci',
	`app_name` VARCHAR(128) NOT NULL,
	`rule_name` VARCHAR(255) NOT NULL COLLATE 'latin1_swedish_ci',
	`points` INT(11) NOT NULL DEFAULT '0',
	`conditional_rules` VARCHAR(5120) NULL DEFAULT NULL,
	`published` TINYINT(4) NOT NULL,
	`auto_approve` TINYINT(1) NOT NULL DEFAULT '1',
	`access` INT(6) UNSIGNED NOT NULL DEFAULT '1',
	`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`ordering` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `idx_cjblog_points_rules_rule_name_uniq` (`rule_name`),
	INDEX `idx_cjblog_points_rules_points` (`points`),
	INDEX `idx_cjblog_points_rules_created_by` (`created_by`),
	INDEX `idx_cjblog_points_rules_created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_subscribes` (
	`subscriber_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`subscription_type` INT(10) UNSIGNED NOT NULL DEFAULT '1',
	`subscription_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`subscriber_id`, `subscription_type`, `subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_tracking` (
	`post_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`post_type` VARCHAR(6) NOT NULL DEFAULT '0',
	`ip_address` VARCHAR(39) NULL DEFAULT NULL,
	`country` VARCHAR(3) NULL DEFAULT NULL,
	`city` VARCHAR(128) NULL DEFAULT NULL,
	`browser_name` VARCHAR(32) NULL DEFAULT NULL,
	`browser_version` VARCHAR(24) NULL DEFAULT NULL,
	`os` VARCHAR(32) NULL DEFAULT NULL,
	`browser_info` TEXT NULL,
	PRIMARY KEY (`post_id`, `post_type`) USING BTREE,
	INDEX `idx_cjblog_tracking_country` (`country`),
	INDEX `idx_cjblog_tracking_city` (`city`),
	INDEX `idx_cjblog_tracking_browser` (`browser_name`),
	INDEX `idx_cjblog_tracking_os` (`os`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_users` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`about` MEDIUMTEXT NULL,
	`num_articles` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`avatar` VARCHAR(32) NULL DEFAULT NULL,
	`points` INT(11) NOT NULL DEFAULT '0',
	`num_badges` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`country` VARCHAR(255) NULL DEFAULT NULL,
	`user_rank` INT(10) UNSIGNED NULL DEFAULT NULL,
	`profile_views` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`handle` VARCHAR(32) NOT NULL,
	`birthday` DATE NOT NULL DEFAULT '0000-00-00',
	`gender` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'default 0 for not specified',
	`location` VARCHAR(50) NULL DEFAULT NULL,
	`banned` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`fans` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`twitter` VARCHAR(45) NULL DEFAULT NULL,
	`facebook` VARCHAR(45) NULL DEFAULT NULL,
	`gplus` VARCHAR(45) NULL DEFAULT NULL,
	`linkedin` VARCHAR(45) NULL DEFAULT NULL,
	`flickr` VARCHAR(45) NULL DEFAULT NULL,
	`bebo` VARCHAR(45) NULL DEFAULT NULL,
	`skype` VARCHAR(45) NULL DEFAULT NULL,
	`metakey` TEXT NULL,
	`metadesc` TEXT NULL,
	`metadata` TEXT NULL,
	`attribs` VARCHAR(5120) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_user_badge_map` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`badge_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`date_assigned` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`rule_id` INT(10) UNSIGNED NOT NULL,
	`ref_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`published` TINYINT(3) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cjblog_reviews` (
	`id` INT(10) UNSIGNED NOT NULL,
	`published` TINYINT(3) NOT NULL DEFAULT '3',
	`reviewed_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`review_date` DATETIME NULL DEFAULT NULL,
	`remarks` MEDIUMTEXT NULL,
	`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`secret_key` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `idx_cjblog_reviews_checked_out` (`checked_out`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;