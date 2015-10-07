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

class CjBlogViewArticles extends JViewLegacy {
	
	protected $params;
	protected $print;
	protected $state;

	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$cache = JFactory::getCache();
		$active	= $app->getMenu()->getActive();
		$user = JFactory::getUser();
		$document = JFactory::getDocument();
		$pathway = $app->getPathway();
		
		$model = $this->getModel();
		$category_model = $this->getModel('categories');
		
		$limit = $app->getCfg('list_limit', 20);
		$limitstart = $app->input->getInt('limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->print = $app->input->getBool('print');
		$catid = $app->input->getInt('id', 0);
		$this->category = $category = null;
		$max_cat_levels = 0;
		$page_heading = '';
		$page_url = 'index.php?option='.CJBLOG.'&view=articles';
		
		/********************************** PARAMS *****************************/
		$appparams = JComponentHelper::getParams(CJBLOG);
		$menuParams = new JRegistry;
		
		if ($active) {
		
			$menuParams->loadString($active->params);
			$active_menu_catid = (int)$menuParams->get('catid', 0);

			if($catid == 0 && $active_menu_catid > 0){
				
				$catid = $active_menu_catid;
			}
		}
		
		$this->params = clone $menuParams;
		$this->params->merge($appparams);
		/********************************** PARAMS *****************************/
		
		if($this->action != 'tagged_articles' && $catid > 0){
			
			$max_cat_levels = intval($this->params->get('max_category_levels', -1));
			$category = $category_model->get_category($catid);
			$groups	= $user->getAuthorisedViewLevels();
			
			if (!$category || !in_array($category->access, $groups)) {
				
				CJFunctions::throw_error(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			}
			
			$this->assignRef('category', $category);
			$app->input->set('cjblogcatid', $catid);
		}
		
		switch ($this->action){

			case 'latest_articles':
				
				$articles = $model->get_articles(array(
								'published'=>1, 
								'pagination'=>true, 
								'limitstart'=>$limitstart, 
								'limit'=>$limit, 
								'catid' => $catid,
								'max_cat_levels'=>$max_cat_levels,
								'order'=>'a.created', 
								'order_dir'=>'desc'));
				
				$this->assignRef('articles', $articles->articles);
				$this->assignRef('pagination', $articles->pagination);
				$this->assign('active_id', 3);
				
				$page_heading = JText::_('LBL_LATEST_ARTICLES');
				$page_url = $page_url.'&task=latest';
				
				break;
				
			case 'trending_articles':
				
				$trending_days = 30;
				
				$articles = $model->get_articles(array(
								'published'=>1, 
								'pagination'=>true,
								'day_limit'=>$trending_days, 
								'limitstart'=>$limitstart, 
								'limit'=>$limit, 
								'catid' => $catid,
								'max_cat_levels'=>$max_cat_levels,
								'order'=>'a.hits', 
								'order_dir'=>'desc'));
				
				$this->assignRef('articles', $articles->articles);
				$this->assignRef('pagination', $articles->pagination);
				$this->assign('active_id', 3);
				
				$page_heading = JText::_('LBL_TRENDING_ARTICLES').' ( '.JText::sprintf('TXT_IN_LAST_X_DAYS', $trending_days).' )';
				$page_url = $page_url.'&task=trending';
				
				break;
				
			case 'popular_articles':
				
				$articles = $model->get_articles(array(
								'published'=>1, 
								'pagination'=>true,
								'limitstart'=>$limitstart, 
								'limit'=>$limit, 
								'catid' => $catid,
								'max_cat_levels'=>$max_cat_levels,
								'order'=>'a.hits', 
								'order_dir'=>'desc'));
				
				$this->assignRef('articles', $articles->articles);
				$this->assignRef('pagination', $articles->pagination);
				$this->assign('active_id', 3);
				
				$page_heading = JText::_('LBL_MOST_POPULAR_ARTICLES');
				$page_url = $page_url.'&task=popular';
				
				break;

			case 'favorite_articles':
				
				$articles = $model->get_articles(array(
								'published'=>1, 
								'pagination'=>true,
								'limitstart'=>$limitstart, 
								'limit'=>$limit, 
								'catid' => $catid,
								'favorites' => true,
								'max_cat_levels'=>$max_cat_levels,
								'order'=>'a.hits', 
								'order_dir'=>'desc'));
				
				$this->assignRef('articles', $articles->articles);
				$this->assignRef('pagination', $articles->pagination);
				$this->assign('active_id', 6);
				
				$page_heading = JText::_('LBL_MY_FAVORITE_ARTICLES');
				$page_url = $page_url.'&task=favorites';
				
				break;

			case 'tagged_articles':
				
				$id = $app->input->getInt('id', 0);
				if(!$id) return CJFunctions::throw_error(JText::_('JERROR_ALERTNOAUTHOR'), 403);
				
				$articles = $model->get_articles(array(
								'tag_id'=>$id,
								'published'=>1, 
								'pagination'=>true, 
								'limitstart'=>$limitstart, 
								'limit'=>$limit, 
								'max_cat_levels'=>$max_cat_levels,
								'order'=>'a.created', 
								'order_dir'=>'desc'));
				
				$tag = $model->get_tag_details($id);
				
				$this->assignRef('articles', $articles->articles);
				$this->assignRef('pagination', $articles->pagination);
				$this->assign('page_description', $tag->description);
				$this->assign('active_id', 3);
				
				$page_heading = JText::sprintf('TXT_TAGGED_ARTICLES', $this->escape($tag->tag_text));
				$page_url = $page_url.'&task=tag&id='.$tag->id.':'.$tag->alias;
				
				break;
		}
		
		// set breadcrumbs
		$path = array(array('title' => $this->params->get('page_heading'), 'link' => ''));
		
		if($category){
			
			$temp = $category;
			$page_heading = $page_heading.': '.$category->title;
			$page_url = $page_url.'&id='.$category->id.(!empty($category->alias) ? ':'.$category->alias : '');

			while ($temp && ($active->query['option'] != CJBLOG || $active->query['view'] == 'articles') && $temp->id > 1){
				
				$path[] = array('title' => $category->title, 'link' => ContentHelperRoute::getCategoryRoute($temp->id));
				$temp = $temp->getParent();
			}
		}
		
		$path = array_reverse($path);
			
		foreach($path as $item){
		
			$pathway->addItem($item['title'], $item['link']);
		}
		
		// set browser title
		$this->params->set('page_heading', $this->params->get('page_heading', $page_heading));
		$title = $this->params->get('page_title', '');
		
		if (empty($title)) {
			
			$title = $page_heading;
		}
		
		$document->setTitle(CjBlogHelper::get_page_title($title));
		
		// set meta description		
		if ($this->params->get('menu-meta_description')){
			
			$document->setDescription($this->params->get('menu-meta_description'));
		}
		
		// set meta keywords
		if ($this->params->get('menu-meta_keywords')){
			
			$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		
		// set robots
		if ($this->params->get('robots')){
			
			$document->setMetadata('robots', $this->params->get('robots'));
		}
		
		// set nofollow if it is print
		if ($this->print){
			
			$document->setMetaData('robots', 'noindex, nofollow');
		}
		
		$this->assignRef('page_url', $page_url);
		
		// display
		parent::display ( $tpl );
	}
}