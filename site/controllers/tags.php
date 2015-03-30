<?php
/**
 * @version		$Id: tags.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CJBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerTags extends JControllerLegacy {
	
	function __construct() {
		
		parent::__construct();
		
		$this->registerDefaultTask('get_tags_listing');
		$this->registerTask('get_tag', 'get_tag_details');
		$this->registerTask('save_tag', 'save_tag_details');
	}
	
	public function get_tags_listing(){
		
		$model = $this->getModel('articles');
		$view = $this->getView('tags', 'html');
		
		$view->setModel($model, true);
		$view->display();
	}
	
	public function get_tag_details(){

		$user = JFactory::getUser();
		
		if(!$user->authorise('core.manage')){
		
			echo json_encode(array('error'=>JText::_('JERROR_ALERTNOAUTHOR')));
		} else {
		
			$app = JFactory::getApplication();
			$model = $this->getModel('articles');
		
			$tag_id = $app->input->getInt('tagid');
		
			if($tag_id){
		
				$tag = $model->get_tag_details($tag_id);
				echo json_encode(array('tag'=>$tag));
			} else {
		
				echo json_encode(array('error'=>JText::_('MSG_MISSING_REQUIRED')));
			}
		}
		
		jexit();
	}

	public function delete_tag(){
	
		$user = JFactory::getUser();
	
		if($user->guest || !$user->authorise('core.manage')){
	
			echo json_encode(array('error'=>JText::_('JERROR_ALERTNOAUTHOR')));
		} else {
	
			$app = JFactory::getApplication();
			$model = $this->getModel('articles');
	
			$tag_id = $app->input->getInt('tagid', 0);
	
			if($tag_id > 0){
	
				if($model->delete_tag($tag_id)){
	
					echo json_encode(array('data'=>1));
				} else {
	
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
				}
			} else {
	
				echo json_encode(array('error'=>JText::_('MSG_MISSING_REQUIRED')));
			}
		}
	
		jexit();
	}
	
	public function save_tag_details(){

		$user = JFactory::getUser();
		
		if(!$user->authorise('core.manage')){
		
			echo json_encode(array('error'=>JText::_('JERROR_ALERTNOAUTHOR')));
		} else {
		
			$app = JFactory::getApplication();
			$model = $this->getModel('articles');
		
			$tag = new stdClass();
			
			$tag->id = $app->input->getInt('tagid', 0);
			$tag->title = $app->input->getString('name', null);
			$tag->alias = $app->input->getString('alias', '');
			$tag->description = $app->input->getString('description', '');
		
			if($tag->id && !empty($tag->title)){
		
				if($model->save_tag_details($tag)){
				
					echo json_encode(array('tag'=>1));
				} else {
					
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
				}
			} else {
		
				echo json_encode(array('error'=>JText::_('MSG_MISSING_REQUIRED')));
			}
		}
		
		jexit();
	}
}