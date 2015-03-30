<?php
/**
 * @version		$Id: points.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.controllers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerPoints extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_recent_activity');
		$this->registerTask('save', 'save_custom_points');
	}
	
	function get_recent_activity(){
		
		$view = $this->getView('points', 'html');
		$model = $this->getModel('points');
		$user_model = $this->getModel('users');
		
		$view->setModel($model, true);
		$view->setModel($user_model, false);
		$view->assign('action', 'default');
		$view->display();
	}
	
	function publish(){
	
		$model = $this->getModel('points');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
	
		if(!empty($ids) && $model->publish($ids, 1)){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=points', false), JText::_('COM_CJBLOG_PUBLISH_SUCCESS'));
		} else {
				
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=points', false), JText::_('COM_CJBLOG_PUBLISH_FAILED'));
		}
	}
	
	function unpublish(){
	
		$model = $this->getModel('points');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
	
		if(!empty($ids) && $model->publish($ids, 0)){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=points', false), JText::_('COM_CJBLOG_UNPUBLISH_SUCCESS'));
		} else {
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=points', false), JText::_('COM_CJBLOG_UNPUBLISH_FAILED'));
		}
	}
	
	function add(){

		$view = $this->getView('points', 'html');
		$model = $this->getModel('points');
		$user_model = $this->getModel('users');
		
		$view->setModel($model, true);
		$view->setModel($user_model, false);
		$view->assign('action', 'form');
		$view->display();
	}
	
	function remove(){
	
		$model = $this->getModel('points');
		$app = JFactory::getApplication();
		$ids = $app->input->getArray(array('cid'=>'array'));
		JArrayHelper::toInteger($ids['cid']);

		if(!empty($ids['cid']) && $model->delete($ids['cid'])){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=points', false), JText::_('COM_CJBLOG_REMOVE_SUCCESS'));
		} else {
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=points', false), JText::_('COM_CJBLOG_REMOVE_FAILED'));
		}
	}
	
	function cancel(){
	
		$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=points', false), JText::_('COM_CJBLOG_OPERATION_CANCELLED'));
	}
	
	function save_custom_points(){
		
		$app = JFactory::getApplication();
		$ids = $app->input->getArray(array('cid'=>'array', 'points'=>'int', 'description'=>'string'));
		JArrayHelper::toInteger($ids['cid']);
		
		if(!empty($ids['cid']) && !empty($ids['description']) && $ids['points'] != 0){

			foreach ($ids['cid'] as $userid){
					
				CjBlogApi::award_points('com_system.custom', $userid, $ids['points'], null, $ids['description']);
			}

			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=points', false), JText::_('COM_CJBLOG_SUCCESS'));
		} else {
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=points', false), JText::_('COM_CJBLOG_FAILED'));
		}
	}
}