<?php
/**
 * @version		$Id: view.html.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.view' );

class CjBlogViewProfile extends JViewLegacy {

	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$active	= $app->getMenu()->getActive();
		$cache = JFactory::getCache();
		$user = JFactory::getUser();
		$document = JFactory::getDocument();
		$model = $this->getModel();
		$articles_model = $this->getModel('articles');
		
		/********************************** PARAMS *****************************/
		$appparams = JComponentHelper::getParams(CJBLOG);
		$menuParams = new JRegistry;
		
		if ($active) {
		
			$menuParams->loadString($active->params);
		}
		
		$this->params = clone $menuParams;
		$this->params->merge($appparams);
		/********************************** PARAMS *****************************/
		
		$document->addScript(CJBLOG_MEDIA_URI.'js/jquery.form.js');
		$document->addScript(CJBLOG_MEDIA_URI.'js/jquery.Jcrop.min.js');
		$id = $app->input->getInt('id', 0);
		
		if(!$id) {
			
			if(!$user->guest){
			
				$id = $user->id;
			} else {
				
				CJFunctions::throw_error(JText::_('MSG_NO_USER_FOUND'), 403);
				return;
			}
		}
		
		if($user->id != $id){
		
			$model->hit($id);
		}
		
		$profile = $cache->call(array('CjBlogApi', 'get_user_profile'), $id);
// 		$badges = $cache->call(array('CjBlogApi', 'get_user_badges'), $id);
// 		$articles = $cache->call(array($articles_model, 'get_articles'), array('published'=>1, 'pagination'=>false, 'limitstart'=>0, 'limit'=>5, 'user_id'=>$id));
		
		if(!$profile) CJFunctions::throw_error(JText::_('MSG_NO_USER_FOUND'), 403);
		
		$this->assignRef('profile', $profile);
		$document->setTitle(CjBlogHelper::get_page_title($profile['name']));

		if ($this->params->get('menu-meta_description'))
		{
			$document->setDescription($this->params->get('menu-meta_description'));
		}
		
		if ($this->params->get('menu-meta_keywords'))
		{
			$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		
		if ($this->params->get('robots'))
		{
			$document->setMetadata('robots', $this->params->get('robots'));
		}
		
		parent::display ( $tpl );
	}
}