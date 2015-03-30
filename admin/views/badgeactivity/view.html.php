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

class CjBlogViewBadgeactivity extends JViewLegacy {

	function display($tpl = null) {
		
		JToolBarHelper::title(JText::_('COM_CJBLOG')." <small>[".JText::_("COM_CJBLOG_BADGE_ACTIVITY")."]</small>");
		JToolBarHelper::divider();
		$model = $this->getModel();
		$app = JFactory::getApplication();
		$cache = JFactory::getCache();
		
		switch ($this->action){
			
			case 'default':
				
				JToolBarHelper::addNew();
				JToolBarHelper::deleteList();
				$return = $model->get_badge_activity();
				$items = $return->activity ? $return->activity : array();
				
				$this->assignRef('items', $items);
				$this->assignRef('state', $return->state);
				$this->assignRef('pagination', $return->pagination);
				$this->assignRef('users', $return->users);
				$this->assignRef('rules', $return->rules);
				
				$tpl = null;
				break;
				
			case 'edit':
				
				JToolBarHelper::cancel();
				JToolBarHelper::save();
				
				$return = $model->get_badge_rules();
				$rules = !empty($return->rules) ? $return->rules : array();
				$this->assignRef('rules', $rules);
				
				$tpl = 'edit';
				break;
		}
		
		parent::display ( $tpl );
	}
}