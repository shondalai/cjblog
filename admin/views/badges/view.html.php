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

class CjBlogViewBadges extends JViewLegacy {

	function display($tpl = null) {
		
		JToolBarHelper::title(JText::_('COM_CJBLOG')." <small>[".JText::_("COM_CJBLOG_BADGES")."]</small>");
		JToolBarHelper::divider();
		$model = $this->getModel();
		$app = JFactory::getApplication();
		
		switch ($this->action){
			
			case 'default':
				
				JToolBarHelper::addNew();
				JToolBarHelper::editList();
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::deleteList();
				
				$return = $model->get_badges();
				$items = $return->badges ? $return->badges : array();
				$this->assignRef('items', $items);
				$this->assignRef('state', $return->state);
				$this->assignRef('pagination', $return->pagination);
				
				$tpl = null;
				break;
				
			case 'add':
				
				JToolBarHelper::save();
				JToolBarHelper::cancel();
				
				$components = $model->get_components();
				$badge = new stdClass();
				$badge->title = $badge->alias = $badge->name = $badge->description = $badge->asset_name = $badge->icon = $badge->css_class = '';
				$badge->id = $badge->published = 0;
				
				$this->assignRef('components', $components);
				$this->assignRef('badge', $badge);
				
				$tpl = 'form';
				
				break;
				
			case 'edit':
				
				JToolBarHelper::save();
				JToolBarHelper::cancel();
				
				if(!empty($this->badge)){
					
					$app->enqueueMessage($model->getError());
					$this->assignRef('badge', $this->badge);
				} else {
					
					$id = $app->input->getInt('id', 0);
					
					if(!$id){
						
						return CJFunctions::throw_error(JText::_('COM_CJBLOG_NO_ITEM_FOUND'), 404);
					}
					
					$badge = $model->get_badge($id);
					$this->assignRef('badge', $badge);
				}
				
				$components = $model->get_components();
				$this->assignRef('components', $components);
				
				$tpl = 'form';
				break;
		}
		
		parent::display ( $tpl );
	}
}