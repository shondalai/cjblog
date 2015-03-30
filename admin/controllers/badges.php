<?php
/**
 * @version		$Id: badges.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.controllers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerBadges extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_badges_list');
		$this->registerTask('add', 'add');
		$this->registerTask('edit', 'edit');
		$this->registerTask('publish', 'publish');
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('remove', 'remove');
		$this->registerTask('canel', 'cancel');
		$this->registerTask('save', 'save');
	}
	
	function get_badges_list(){
		
		$view = $this->getView('badges', 'html');
		$model = $this->getModel('badges');
		$view->setModel($model, true);
		$view->assign('action', 'default');
		$view->display();
	}
	
	function add(){
		
		$view = $this->getView('badges', 'html');
		$model = $this->getModel('badges');
		$view->setModel($model, true);
		$view->assign('action', 'add');
		$view->display();
	}
	
	function edit(){
		
		$view = $this->getView('badges', 'html');
		$model = $this->getModel('badges');
		$view->setModel($model, true);
		$view->assign('action', 'edit');
		$view->display();
	}
	
	function save(){
	
		$model = $this->getModel('badges');
		$badge = $model->save_badge();
		
		if($badge === true){
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badges', false), JText::_('COM_CJBLOG_BADGE_SAVED'));
		} else {
			
			$view = $this->getView('badges', 'html');
			$view->setModel($model, true);
			$view->assignRef('badge', $badge);
			$view->assign('action', 'edit');
			$view->display();
		}
	}
	
	function publish(){
		
		$model = $this->getModel('badges');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
		
		if(!empty($ids) && $model->publish($ids, 1)){
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badges', false), JText::_('COM_CJBLOG_PUBLISH_SUCCESS'));
		} else {
			
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badges', false), JText::_('COM_CJBLOG_PUBLISH_FAILED'));
		}
	}
	
	function unpublish(){
		
		$model = $this->getModel('badges');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
		
		if(!empty($ids) && $model->publish($ids, 0)){
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badges', false), JText::_('COM_CJBLOG_UNPUBLISH_SUCCESS'));
		} else {
				
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badges', false), JText::_('COM_CJBLOG_UNPUBLISH_FAILED'));
		}
	}
	
	function remove(){
		
		$model = $this->getModel('badges');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($ids);
		
		if(!empty($ids) && $model->delete($ids)){
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badges', false), JText::_('COM_CJBLOG_REMOVE_SUCCESS'));
		} else {
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badges', false), JText::_('COM_CJBLOG_REMOVE_FAILED'));
		}
	}
	
	function cancel(){
		
		$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badges', false), JText::_('COM_CJBLOG_OPERATION_CANCELLED'));
	}
}