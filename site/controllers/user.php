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

class CjBlogControllerUser extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_user_points');
		$this->registerTask('articles','get_user_articles');
	}

	function get_user_points(){

		if(JFactory::getUser()->guest){
		
			CJFunctions::throw_error(JText::_('JERROR_ALERTNOAUTHOR'), 401);
		} else {
				
			$view = $this->getView('user', 'html');
			$model = $this->getModel('users');
	
			$view->setModel($model, true);
			$view->assign('action', 'user_points');
			$view->display();
		}
	}

	function get_user_articles(){

		$view = $this->getView('user', 'html');
		$model = $this->getModel('articles');

		$view->setModel($model, true);
		$view->assign('action', 'user_articles');
		$view->display();
	}
}
?>