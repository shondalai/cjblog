<?php
/**
 * @version		$Id: articles.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CJBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerArticles extends JControllerLegacy {
	
	function __construct() {
		
		parent::__construct();
		
		$this->registerDefaultTask('get_latest_articles');
		$this->registerTask('trending', 'get_trending_articles');
		$this->registerTask('popular', 'get_popular_articles');
		$this->registerTask('favorites', 'get_favorite_articles');
		$this->registerTask('favorite', 'add_to_favorites');
		$this->registerTask('remove_favorite', 'remove_favorite');
		$this->registerTask('rate', 'rate_article');
		$this->registerTask('publish', 'publish_article');
		$this->registerTask('unpublish', 'unpublish_article');
		$this->registerTask('get_tags', 'get_tags');
		$this->registerTask('tag', 'get_tagged_articles');
	}
	
	function get_latest_articles(){
		
		$view = $this->getView('articles', 'html');
		$model = $this->getModel('articles');
		$category_model = $this->getModel('categories');
		
		$view->setModel($model, true);
		$view->setModel($category_model, false);
		$view->assign('action', 'latest_articles');
		$view->display();
	}
	
	function get_trending_articles(){
		
		$view = $this->getView('articles', 'html');
		$model = $this->getModel('articles');
		$category_model = $this->getModel('categories');
		
		$view->setModel($model, true);
		$view->setModel($category_model, false);
		$view->assign('action', 'trending_articles');
		$view->display();
	}
	
	function get_popular_articles(){
		
		$view = $this->getView('articles', 'html');
		$model = $this->getModel('articles');
		$category_model = $this->getModel('categories');
		
		$view->setModel($model, true);
		$view->setModel($category_model, false);
		$view->assign('action', 'popular_articles');
		$view->display();
	}
	
	function get_favorite_articles(){
	
		$view = $this->getView('articles', 'html');
		$model = $this->getModel('articles');
		$category_model = $this->getModel('categories');
	
		$view->setModel($model, true);
		$view->setModel($category_model, false);
		$view->assign('action', 'favorite_articles');
		$view->display();
	}

	function get_tagged_articles(){
	
		$view = $this->getView('articles', 'html');
		$model = $this->getModel('articles');
		$category_model = $this->getModel('categories');
	
		$view->setModel($model, true);
		$view->setModel($category_model, false);
		$view->assign('action', 'tagged_articles');
		$view->display();
	}
	
	function add_to_favorites(){
		
		$user = JFactory::getUser();
		
		if($user->guest){
				
			echo json_encode(array('error'=>JText::_('JERROR_ALERTNOAUTHOR')));
		} else {
			
			$app = JFactory::getApplication();
			$model = $this->getModel('articles');
			
			$id = $app->input->getInt('id');

			if($id > 0){
				
				$count = $model->add_to_favorites($id, $user->id);
				echo json_encode(array('data'=>JText::sprintf('TXT_NUM_FAVOURED', $count)));
			}
		}
		
		jexit();
	}
	
	function remove_favorite(){
		
		$user = JFactory::getUser();
		
		if($user->guest){
				
			echo json_encode(array('error'=>JText::_('JERROR_ALERTNOAUTHOR')));
		} else {
			
			$app = JFactory::getApplication();
			$model = $this->getModel('articles');
			
			$id = $app->input->getInt('id');

			if($id > 0){
				
				$count = $model->remove_favorite($id, $user->id);
				echo json_encode(array('data'=>JText::sprintf('TXT_NUM_FAVOURED', $count)));
			}
		}
		
		jexit();
	}
	
	function rate_article(){
		
		$user = JFactory::getUser();
		
		if(!$user->authorise('articles.rate', CJBLOG)){
				
			echo json_encode(array('error'=>JText::_('JERROR_ALERTNOAUTHOR')));
		} else {
			
			$app = JFactory::getApplication();
			
			$id = $app->input->getInt('id');
			$rating = $app->input->getFloat('rating');

			if($id > 0 && $rating > 0){
				
				$return = CJFunctions::store_rating(CJBLOG_ASSET_ID, $id, $rating, $user->id);
				
				if($return){
				
					$hash = CJFunctions::get_hash('com_content.article.rating.item_'.$id);
					$domain = JRoute::_(ContentHelperRoute::getArticleRoute($id));
					$app->input->cookie->set($hash, 1, time()+60*60*24*365, $domain);
					
					echo json_encode(array('data'=>JText::_('MSG_THANK_YOU_FOR_RATING')));
				} else if($return == -1){
					
					echo json_encode(array('error'=>JText::_('MSG_ALREADY_RATED')));
				}else{
					
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING').' - Error Code: 1.'));
				}
			} else {
				
				echo json_encode(array('error'=>''));
			}
		}
		
		jexit();
	}
	
	function publish_article(){

		$user = JFactory::getUser();
		
		if(!$user->authorise('core.edit.state')){

			return CJFunctions::throw_error(JText::_('JERROR_ALERTNOAUTHOR'), 401);
		} else {
				
			$app = JFactory::getApplication();
			$model = $this->getModel('articles');
			$id = $app->input->getInt('id');
			$return = $app->input->getBase64('return', null);

			if (empty($return) || !JUri::isInternal(base64_decode($return))) {
				
				$return = JURI::base();
			} else {
				$return = base64_decode($return);
			}
			
			if($model->publish_article($id, 1)){
				
				$this->setRedirect($return, JText::_('MSG_ARTICLE_PUBLISHING_SUCCESS'));
			} else {
				
				$this->setRedirect($return, JText::_('MSG_ERROR_PROCESSING'));
			}
		}		
	}
	
	function unpublish_article(){

		$user = JFactory::getUser();
		
		if(!$user->authorise('core.edit.state')){

			return CJFunctions::throw_error(JText::_('JERROR_ALERTNOAUTHOR'), 401);
		} else {

			$app = JFactory::getApplication();
			$model = $this->getModel('articles');
			$id = $app->input->getInt('id');
			$return = $app->input->getBase64('return', null);
			
			if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			
				$return = JURI::base();
			} else {
				$return = base64_decode($return);
			}
			
			if($model->publish_article($id, 2)){
				
				$this->setRedirect($return, JText::_('MSG_ARTICLE_PUBLISHING_SUCCESS'));
			} else {
				
				$this->setRedirect($return, JText::_('MSG_ERROR_PROCESSING'));
			}
		}		
	}
	
	public function get_tags(){
		
		$app = JFactory::getApplication();
		$model = $this->getModel('articles');
		$search = $app->input->getString('like');
		
		if(!empty($search)){
			
			$tags = $model->search_tags($search);
			echo json_encode(array('tags'=>$tags));
		} else {
			
			echo json_encode(array('tags'=>array()));
		}
				
		jexit();
	}
}
?>