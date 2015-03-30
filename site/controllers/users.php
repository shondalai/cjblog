<?php
/**
 * @version		$Id: users.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CJBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerUsers extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_users_home');
		$this->registerTask('top', 'get_top_bloggers');
		$this->registerTask('badge', 'get_badge_owners');
		$this->registerTask('search', 'search_bloggers');
	}

	function get_users_home(){

		$view = $this->getView('users', 'html');
		$model = $this->getModel('users');

		$view->setModel($model, true);
		$view->assign('action', 'new_bloggers');
		$view->display();
	}

	function get_top_bloggers(){

		$view = $this->getView('users', 'html');
		$model = $this->getModel('users');

		$view->setModel($model, true);
		$view->assign('action', 'top_bloggers');
		$view->display();
	}

	function search_bloggers(){
	
		$view = $this->getView('users', 'html');
		$model = $this->getModel('users');
	
		$view->setModel($model, true);
		$view->assign('action', 'search');
		$view->display();
	}
	
	function get_badge_owners(){
	
		$view = $this->getView('users', 'html');
		$model = $this->getModel('users');
	
		$view->setModel($model, true);
		$view->assign('action', 'badge_owners');
		$view->display();
	}
}
?>