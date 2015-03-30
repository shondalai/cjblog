<?php
/**
 * @version		$Id: blogs.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CJBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerBadges extends JControllerLegacy {
	
	function __construct() {
		
		parent::__construct();
		
		$this->registerDefaultTask('get_blog_home');
		$this->registerTask('user', 'get_user_badges');
	}
	
	function get_blog_home(){
		
		$view = $this->getView('badges', 'html');
		$model = $this->getModel('badges');
		
		$view->setModel($model, true);
		$view->assign('action', 'badges_home');
		$view->display();
	}
	
	function get_user_badges(){
		
		$view = $this->getView('badges', 'html');
		$model = $this->getModel('badges');
		
		$view->setModel($model, true);
		$view->assign('action', 'user_badges');
		$view->display();
	}
}
?>