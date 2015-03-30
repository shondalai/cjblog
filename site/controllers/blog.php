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

class CjBlogControllerBlog extends JControllerLegacy {
	
	function __construct() {
		
		parent::__construct();
		
		$this->registerDefaultTask('get_blog_home');
	}
	
	function get_blog_home(){
		
		$view = $this->getView('blog', 'html');
		$model = $this->getModel('articles');
		
		$view->setModel($model, true);
		$view->display();
	}
}
?>