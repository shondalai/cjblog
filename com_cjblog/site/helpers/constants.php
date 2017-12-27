<?php 
/**
 * @package     corejoomla.site
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

defined('CJBLOG_CURR_VERSION') or define('CJBLOG_CURR_VERSION',				'@version@');
defined('CJBLOG_CJLIB_VER') or define('CJBLOG_CJLIB_VER',					'2.6.0');
defined('CJBLOG_MEDIA_DIR') or define('CJBLOG_MEDIA_DIR',					JPATH_ROOT.'/media/com_cjblog/');
defined('CJBLOG_MEDIA_URI') or define('CJBLOG_MEDIA_URI',					JURI::root(true).'/media/com_cjblog/');
defined('CJBLOG_BADGES_BASE_DIR') or define('CJBLOG_BADGES_BASE_DIR',		JPATH_ROOT.'/images/badges/');
defined('CJBLOG_BADGES_BASE_URI') or define('CJBLOG_BADGES_BASE_URI',		JURI::root(true).'/images/badges/');
defined('CJBLOG_AVATAR_BASE_DIR') or define('CJBLOG_AVATAR_BASE_DIR',		JPATH_ROOT.'/images/avatar/');
defined('CJBLOG_AVATAR_BASE_URI') or define('CJBLOG_AVATAR_BASE_URI',		JURI::root(true).'/images/avatar/');
defined('CJBLOG_PLUGINS_BASE_DIR') or define('CJBLOG_PLUGINS_BASE_DIR',		JPATH_ROOT.'/media/cjblog/plugins/');
defined('CJBLOG_ATTACHMENTS_DIR') or define('CJBLOG_ATTACHMENTS_DIR',		JPATH_ROOT.'/media/cjblog/attachments/');
defined('CJBLOG_ATTACHMENTS_PATH') or define('CJBLOG_ATTACHMENTS_PATH',		'media/cjblog/attachments');
defined('CJBLOG_ATTACHMENTS_URI') or define('CJBLOG_ATTACHMENTS_URI',		JURI::root(true).'/media/cjblog/attachments');
defined('CJBLOG_ASSET_ID') or define('CJBLOG_ASSET_ID', 					1);

defined('BLOG_SESSION_CONFIG') or define('BLOG_SESSION_CONFIG',				'blog_session_config');
defined('BLOG_DEFAULT_AVATAR') or define('BLOG_DEFAULT_AVATAR',				'blog_default_avatar');
?>