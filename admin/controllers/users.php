<?php
/**
 * @version		$Id: users.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.controllers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerUsers extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_users_list');
		$this->registerTask('users.sync', 'sync_users');
		$this->registerTask('search', 'search_users');
		$this->registerTask('remove', 'remove_users');
	}
	
	function get_users_list(){
		
        $view = $this->getView('users', 'html');
        $model = $this->getModel('users');
        $view->setModel($model, true);
        $view->display();
	}
	
	function sync_users(){
		
		$model = $this->getModel('users');
    	
        $view = $this->getView('users', 'html');
        $model = $this->getModel('users');
        
        if($model->synchronize()){

        	$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=users', false), JText::_('COM_CJBLOG_USER_SYNC_COMPLETED'));
        } else {
        	
        	$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=users', false), JText::_('COM_CJBLOG_ERROR_PROCESSING'));
        }
	}
	
	function search_users(){
		
		$model = $this->getModel('users');
		$search = JFactory::getApplication()->input->getString('search', null);
		$users = array();
		
		if(!empty($search)){
		
			$users = $model->get_all_user_names($search);
		}
		
		echo json_encode(array('data'=>$users));
		jexit();
	}
	
	function remove_users(){

		$model = $this->getModel('users');
		$app = JFactory::getApplication();
		$ids = $app->input->getArray(array('cid'=>'array'));
		JArrayHelper::toInteger($ids['cid']);
		
		if(!empty($ids['cid']) && $model->delete($ids['cid'])){
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=users', false), JText::_('COM_CJBLOG_REMOVE_SUCCESS'));
		} else {
		
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=users', false), JText::_('COM_CJBLOG_REMOVE_FAILED'));
		}
	}
}