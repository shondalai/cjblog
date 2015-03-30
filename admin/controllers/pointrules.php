<?php
/**
 * @version		$Id: pointrules.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.controllers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerPointRules extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_rules');
		$this->registerTask('import', 'import_rules');
		$this->registerTask('approve', 'set_auto_approve_true');
		$this->registerTask('disapprove', 'set_auto_approve_false');
		$this->registerTask('edit', 'edit');
		$this->registerTask('save', 'save');
	}
	
	function get_rules(){
		
		$view = $this->getView('pointrules', 'html');
		$model = $this->getModel('points');
		$view->setModel($model, true);
		$view->assign('action', 'default');
		$view->display();
	}

	function edit(){
	
		$view = $this->getView('pointrules', 'html');
		$model = $this->getModel('points');
		$view->setModel($model, true);
		$view->assign('action', 'form');
		$view->display();
	}
	
	function save(){
	
		$model = $this->getModel('points');
		$rule = $model->save_rule();
		
		if($rule === true){
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_RULE_SAVED'));
		} else {
			
			$view = $this->getView('badges', 'html');
			$view->setModel($model, true);
			$view->assignRef('rule', $rule);
			$view->assign('action', 'edit');
			$view->display();
		}
	}
	
	function import_rules(){
		
		$model = $this->getModel('points');
		
		if($model->import_rules()){
			
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_IMPORT_SUCCESS'));
		} else {
				
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_IMPORT_FAILED'));
		}
	}
	
	function publish(){
	
		$model = $this->getModel('points');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
	
		if(!empty($ids) && $model->publish_rules($ids, 1)){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_PUBLISH_SUCCESS'));
		} else {
				
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_PUBLISH_FAILED').' Error='.$model->getError());
		}
	}
	
	function unpublish(){
	
		$model = $this->getModel('points');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
	
		if(!empty($ids) && $model->publish_rules($ids, 0)){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_UNPUBLISH_SUCCESS'));
		} else {
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_UNPUBLISH_FAILED'));
		}
	}
	
	function remove(){
	
		$model = $this->getModel('points');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
	
		if(!empty($ids) && $model->delete_rules($ids)){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_REMOVE_SUCCESS'));
		} else {
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_REMOVE_FAILED'));
		}
	}
	
	function set_auto_approve_true(){
	
		$model = $this->getModel('points');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
	
		if(!empty($ids) && $model->set_auto_approve($ids, 1)){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_PUBLISH_SUCCESS'));
		} else {
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_PUBLISH_FAILED'));
		}
	}
	
	function set_auto_approve_false(){
	
		$model = $this->getModel('points');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
	
		if(!empty($ids) && $model->set_auto_approve($ids, 0)){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_UNPUBLISH_SUCCESS'));
		} else {
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_UNPUBLISH_FAILED'));
		}
	}
	
	function cancel(){
	
		$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=pointrules', false), JText::_('COM_CJBLOG_OPERATION_CANCELLED'));
	}
}