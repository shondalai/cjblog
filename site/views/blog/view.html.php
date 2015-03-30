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

class CjBlogViewBlog extends JViewLegacy {

	protected $params;
	
	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$active	= $app->getMenu()->getActive();
		$cache = JFactory::getCache();
		$user = JFactory::getUser(); 
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
		
		$id = $app->input->getInt('id', 0);
		
		if(!$id) 
		{
			if(!$user->guest)
			{
				$id = $user->id;
			} 
			else if($this->params->get('blogUserId', 0) > 0)
			{
				$id = $this->params->get('blogUserId');
			}
			else
			{
				CJFunctions::throw_error(JText::_('MSG_NO_USER_FOUND'), 403);
				return;
			}
		}
		
		$limitstart = $app->input->getInt('start', 0);
		$limitstart = floor($limitstart / 5) * 5;
		
		$profile = $cache->call(array('CjBlogApi', 'get_user_profile'), $id);
		$articles = $model->get_articles(array(
						'published'=>1, 
						'pagination'=>true, 
						'limitstart'=>$limitstart, 
						'limit'=>5, 
						'order'=>'a.created', 
						'order_dir'=>'desc',
						'user_id'=>$id));
		
		$this->assignRef('user', $profile);
		$this->assignRef('articles', $articles->articles);
		$this->assignRef('pagination', $articles->pagination);
		
		$document = JFactory::getDocument();
		$title = JText::sprintf('TXT_USERS_BLOG', addslashes($profile['name']));
		$title = CjBlogHelper::get_page_title($title);
		
		$document->setTitle($title);
		
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