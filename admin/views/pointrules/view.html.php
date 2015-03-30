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

class CjBlogViewPointRules extends JViewLegacy {

	function display($tpl = null) {
		
		JToolBarHelper::title(JText::_('COM_CJBLOG')." <small>[".JText::_("COM_CJBLOG_POINT_RULES")."]</small>");
		JToolBarHelper::divider();
		$model = $this->getModel();
		$app = JFactory::getApplication();
		
		switch ($this->action){
			
			case 'default':
				
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::deleteList();
				
				$return = $model->get_rules();
				$items = $return->rules ? $return->rules : array();
				$this->assignRef('items', $items);
				$this->assignRef('state', $return->state);
				$this->assignRef('pagination', $return->pagination);
				
				$tpl = null;
				
				break;
				
			case 'form':

				JToolBarHelper::save();
				JToolBarHelper::cancel();
				
				$id = $app->input->getInt('id', 0);
				if(!$id) return CJFunctions::throw_error(JText::_('COM_CJBLOG_NO_ITEM_FOUND'), 404);
				
				$rule = $model->get_rule($id);
				$this->assignRef('rule', $rule);
				
				$tpl = 'form';
				
				break;
		}
		
		parent::display ( $tpl );
	}
}