<?php
/**
 * @version		$Id: cpanel.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.controllers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerCPanel extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_cpanel');
	}
	
	function get_cpanel(){
		
		$view = $this->getView('dashboard', 'html');
		$view->display();
	}
}