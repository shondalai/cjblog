<?php
/**
 * @version		$Id: badgerules.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.controllers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerBadgeRules extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_badge_rules_list');
		$this->registerTask('import', 'import_rules');
		$this->registerTask('add', 'get_rule_types_list');
		$this->registerTask('edit', 'get_create_rule_page');
	}
	
	function get_badge_rules_list(){
		
		$view = $this->getView('badgerules', 'html');
		$model = $this->getModel('badges');
		$view->setModel($model, true);
		$view->assign('action', 'rule_list');
		$view->display();
	}
	
	function get_rule_types_list(){
		
		$view = $this->getView('badgerules', 'html');
		$model = $this->getModel('badges');
		$view->setModel($model, true);
		$view->assign('action', 'rule_types');
		$view->display();
	}

	function import_rules(){
	
		$model = $this->getModel('badges');
	
		if($model->import_rules()){
				
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_IMPORT_SUCCESS'));
		} else {
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_IMPORT_FAILED'));
		}
	}
	
	function get_create_rule_page(){
		
		$view = $this->getView('badgerules', 'html');
		$model = $this->getModel('badges');
		$view->setModel($model, true);
		$view->assign('action', 'edit');
		$view->display();
	}
	
	function save()
	{
		$model = $this->getModel('badges');
		if($model->save_rule())
		{
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_BADGE_RULE_SAVED'));
		} 
		else 
		{
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_ERROR_PROCESSING'));
		}
	}
	
	function publish(){
		
		$model = $this->getModel('badges');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
		
		if(!empty($ids) && $model->publish_rules($ids, 1)){
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_PUBLISH_SUCCESS'));
		} else {
			
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_PUBLISH_FAILED'));
		}
	}
	
	function unpublish(){
		
		$model = $this->getModel('badges');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
		
		if(!empty($ids) && $model->publish_rules($ids, 0)){
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_UNPUBLISH_SUCCESS'));
		} else {
				
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_UNPUBLISH_FAILED'));
		}
	}
	
	function remove(){
		
		$model = $this->getModel('badges');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
		
		if(!empty($ids) && $model->delete_rules($ids)){
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_REMOVE_SUCCESS'));
		} else {
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_REMOVE_FAILED'));
		}
	}
	
	function cancel(){
		
		$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgerules', false), JText::_('COM_CJBLOG_OPERATION_CANCELLED'));
	}
}