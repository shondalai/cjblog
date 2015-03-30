<?php
/**
 * @version		$Id: view.html.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.view' );

class CjBlogViewDashboard extends JViewLegacy {

	function display($tpl = null) {
		
		JToolBarHelper::title(JText::_('COM_CJBLOG')." <small>[".JText::_("COM_CJBLOG_CONTROL_PANEL")."]</small>");
		
		$app = JFactory::getApplication();
		$version = $app->getUserState(CJBLOG.'.VERSION', null);
		
		if(!$version){
				
			$version = CJFunctions::get_component_update_check(CJBLOG, CJBLOG_VERSION);
			$v = array();
			
			if(!empty($version)){
				
				$v['connect'] = (int)$version['connect'];
				$v['version'] = (string)$version['version'];
				$v['released'] = (string)$version['released'];
				$v['changelog'] = (string)$version['changelog'];
				$v['status'] = (string)$version['status'];
					
				$app->setUserState(CJBLOG.'.VERSION', $v);
			}
		}
		
		$this->assignRef('version', $version);
		
		parent::display ( $tpl );
	}
}