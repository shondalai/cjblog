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

class CjBlogViewPoints extends JViewLegacy {

	function display($tpl = null) {
		
		JToolBarHelper::title(JText::_('COM_CJBLOG')." <small>[".JText::_("COM_CJBLOG_POINTS")."]</small>");
		JToolBarHelper::divider();
		$model = $this->getModel();
		$app = JFactory::getApplication();
		$cache = JFactory::getCache();
		
		switch ($this->action){
			
			case 'default':
				
				JToolBarHelper::addNew();
				JToolBarHelper::divider();
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::deleteList();
				
				$return = $model->get_recent_activity();
				$items = $return->points ? $return->points : array();
				
				$this->assignRef('items', $items);
				$this->assignRef('state', $return->state);
				$this->assignRef('pagination', $return->pagination);
				$this->assignRef('users', $return->users);
				$this->assignRef('rules', $return->rules);
				
				break;
				
			case 'form':
				
				JToolBarHelper::cancel();
				JToolBarHelper::save();
				
				$tpl = 'form';
				break;
		}
		
		parent::display ( $tpl );
	}
}