<?php 
/**
 * @version		$Id: header.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$categories_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=categories');
$users_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=users');
$user_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=user');
$blog_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=blog');
$profile_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=profile');
$articles_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=articles');
$search_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=search');
$badges_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=badges');
$form_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option=com_cjblog&view=form&layout=edit');
$tags_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=tags');

$user = JFactory::getUser();
$app = JFactory::getApplication();
?>