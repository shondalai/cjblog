<?php
/**
 * @version		$Id: constants.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

defined('CJBLOG_VERSION') or define('CJBLOG_VERSION', '1.4.2');

defined('CJBLOG_MEDIA_DIR') or define('CJBLOG_MEDIA_DIR',					JPATH_ROOT.'/media/com_cjblog/');
defined('CJBLOG_MEDIA_URI') or define('CJBLOG_MEDIA_URI',					JURI::root(true).'/media/com_cjblog/');
defined('CJBLOG_BADGES_BASE_DIR') or define('CJBLOG_BADGES_BASE_DIR',		JPATH_ROOT.DS.'images/badges/');
defined('CJBLOG_BADGES_BASE_URI') or define('CJBLOG_BADGES_BASE_URI',		JURI::root(true).'/images/badges/');
defined('CJBLOG_AVATAR_BASE_DIR') or define('CJBLOG_AVATAR_BASE_DIR',		JPATH_ROOT.DS.'images/avatar/');
defined('CJBLOG_AVATAR_BASE_URI') or define('CJBLOG_AVATAR_BASE_URI',		JURI::root(true).'/images/avatar/');
defined('CJBLOG_PLUGINS_BASE_DIR') or define('CJBLOG_PLUGINS_BASE_DIR',		JPATH_ROOT.DS.'media/cjblog/plugins/');
defined('CJBLOG_ASSET_ID') or define('CJBLOG_ASSET_ID', 					1);

defined('T_CJBLOG_CONFIG') or define('T_CJBLOG_CONFIG',						'#__cjblog_config');
defined('T_CJBLOG_USERS') or define('T_CJBLOG_USERS',						'#__cjblog_users');
defined('T_CJBLOG_CONTENT') or define('T_CJBLOG_CONTENT',					'#__cjblog_content');
defined('T_CJBLOG_POINTS') or define('T_CJBLOG_POINTS',						'#__cjblog_points');
defined('T_CJBLOG_POINT_RULES') or define('T_CJBLOG_POINT_RULES',			'#__cjblog_point_rules');
defined('T_CJBLOG_RANKS') or define('T_CJBLOG_RANKS',						'#__cjblog_ranks');
defined('T_CJBLOG_FAVORITES') or define('T_CJBLOG_FAVORITES',				'#__cjblog_favorites');
defined('T_CJBLOG_CONTENT') or define('T_CJBLOG_CONTENT',					'#__cjblog_content');
defined('T_CJBLOG_BADGES') or define('T_CJBLOG_BADGES',						'#__cjblog_badges');
defined('T_CJBLOG_BADGE_RULES') or define('T_CJBLOG_BADGE_RULES',			'#__cjblog_badge_rules');
defined('T_CJBLOG_USER_BADGE_MAP') or define('T_CJBLOG_USER_BADGE_MAP',		'#__cjblog_user_badge_map');

defined('BLOG_SESSION_CONFIG') or define('BLOG_SESSION_CONFIG',				'blog_session_config');
defined('BLOG_DEFAULT_AVATAR') or define('BLOG_DEFAULT_AVATAR',				'blog_default_avatar');