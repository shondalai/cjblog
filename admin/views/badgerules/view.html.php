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

class CjBlogViewBadgeRules extends JViewLegacy {

	function display($tpl = null) {
		
		JToolBarHelper::title(JText::_('COM_CJBLOG')." <small>[".JText::_("COM_CJBLOG_BADGE_RULES")."]</small>");
		JToolBarHelper::divider();
		$model = $this->getModel();
		$app = JFactory::getApplication();
		
		switch ($this->action){
			
			case 'rule_list':
				
				JToolBarHelper::addNew();
				JToolBarHelper::editList();
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::deleteList();
				
				$return = $model->get_badge_rules();
				$items = !empty($return->rules) ? $return->rules : array();
				$this->assignRef('items', $items);
				$this->assignRef('state', $return->state);
				$this->assignRef('pagination', $return->pagination);
				
				break;
			
			case 'rule_types':
				
				JToolBarHelper::cancel();
				
				$rule_types = $model->get_rule_types();
				$this->assignRef('rules', $rule_types);
				
				$tpl = 'rule_types';
				
				break;
				
			case 'edit':

				JToolBarHelper::cancel();
				JToolBarHelper::save();
				
				$id = $app->input->getInt('id', 0);
				$name = $app->input->getCmd('name', null);
				$asset_name = $app->input->getCmd('asset', null);
				
				if($id > 0 || (!empty($name) && !empty($asset_name))){
				
					$rule_type = $model->get_rule_type($id, $asset_name, $name);
					
					if(empty($rule_type)){
						
						return CJFunctions::throw_error(JText::_('COM_CJBLOG_ERROR_PROCESSING'), 404);
					}
					
					$this->assignRef('rule_type', $rule_type);
				} else {
					
					return CJFunctions::throw_error(JText::_('COM_CJBLOG_NO_ITEM_FOUND'), 404);
				}
				
				$tpl = 'rule_form';
				
				break;
		}
		
		parent::display ( $tpl );
	}
}