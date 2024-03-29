<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogViewArticles extends JViewLegacy
{
	protected $extension 		= 'com_cjblog';
	protected $defaultPageTitle = 'COM_CJBLOG_ARTICLES';
	protected $viewName 		= 'articles';

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl
	 *        	The name of the template file to parse; automatically searches
	 *        	through the template paths.
	 *        	
	 * @return mixed A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$user   = JFactory::getUser();
		$params = $app->getParams();

		// Get some data from the models
		$state      = $this->get('State');
		$items      = $this->get('Items');
		$category   = $this->get('Category');
		$children   = $this->get('Children');
		$parent     = $this->get('Parent');
		$pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
		    throw new Exception(implode("\n", $errors), 500);
		}

		if ($category)
		{
			// Check whether category access level allows access.
			$groups = $user->getAuthorisedViewLevels();
			
			if (!in_array($category->access, $groups))
			{
			    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			}
			
			// Setup the category parameters.
			$cparams          = $category->getParams();
			$category->params = clone $params;
			$category->params->merge($cparams);
			
			$children = array($category->id => $children);
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx', ''));

		$maxLevel         = $params->get('maxLevel', -1);
		$this->maxLevel   = &$maxLevel;
		$this->state      = &$state;
		$this->items      = &$items;
		$this->category   = &$category;
		$this->children   = &$children;
		$this->params     = &$params;
		$this->parent     = &$parent;
		$this->pagination = &$pagination;
		$this->user       = &$user;
		$this->document   = JFactory::getDocument();

		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will match
		$active = $app->getMenu()->getActive();

		if ((!$active) || ((strpos($active->link, 'view=category') === false) || (strpos($active->link, '&id=' . (string) $this->category->id) === false)))
		{
			if ($category && $layout = $category->params->get('category_layout'))
			{
				$this->setLayout($layout);
			}
		}
		elseif (isset($active->query['layout']))
		{
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

		if(!empty($this->category)) 
		{
			$this->category->tags = new JHelperTags;
			$this->category->tags->getItemTags($this->extension . '.category', $this->category->id);
		}
		
		$this->prepareDocument();

		return parent::display($tpl);
	}
	
	protected function prepareDocument()
	{
		$app           = JFactory::getApplication();
		$menus         = $app->getMenu();
		$this->pathway = $app->getPathway();
		$title         = null;
	
		// Because the application sets a default page title, we need to get it from the menu item itself
		$this->menu = $menus->getActive();
	
		if ($this->menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $this->menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_($this->defaultPageTitle));
		}
	
		$title = $this->params->get('page_title', '');
	
		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}
	
		$this->document->setTitle($title);
	
		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
	
		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
	
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
	
	protected function addFeed()
	{
		if ($this->params->get('show_feed_link', 1) == 1)
		{
			$link    = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}
	}
}
