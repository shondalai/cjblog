<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogViewProfile extends JViewLegacy
{

	protected $item;

	protected $params;

	protected $print;

	protected $state;

	protected $user;

	public function display ($tpl = null)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		$this->item  = $this->get('Item');
		$this->print = $app->input->getBool('print');
		$this->state = $this->get('State');
		$this->user  = &$user;
		$this->layout= $app->input->getCmd('layout');
		$this->tab	 = $app->input->getCmd('tab', 'summary');
		
		// Merge article params. If this is single-article view, menu params override article params
		// Otherwise, article params override menu item params
		$this->params = $this->state->get('params');
		$active = $app->getMenu()->getActive();
		$temp = clone ($this->params);
		$item = $this->item;
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}
		
		// Check to see which parameters should take priority
		if ($active)
		{
			$currentLink = $active->link;
			
			// If the current view is the active item and an article view for
			// this article, then the menu item params take priority
			if (strpos($currentLink, 'view=profile') && (strpos($currentLink, '&id=' . (string) $item->id)))
			{
				// Load layout from active query (in case it is an alternative
				// menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
				// Check for alternative layout of article
				elseif ($layout = $item->params->get('article_layout'))
				{
					$this->setLayout($layout);
				}
				
				// $item->params are the article params, $temp are the menu item
				// params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);
			}
			else
			{
				// Current view is not a single article, so the article params
				// take priority here
				// Merge the menu item params with the article params so that
				// the article params take priority
				$temp->merge($item->params);
				$item->params = $temp;
				
				// Check for alternative layouts (since we are not in a
				// single-article menu item)
				// Single-article menu item layout takes priority over alt
				// layout for an article
				if ($layout = $item->params->get('profile_layout'))
				{
					$this->setLayout($layout);
				}
			}
		}
		else
		{
			// Merge so that item params take priority
			$temp->merge($item->params);
			$item->params = $temp;
			
			// Check for alternative layouts (since we are not in a
			// single-article menu item)
			// Single-article menu item layout takes priority over alt layout
			// for an article
			if ($layout = $item->params->get('profile_layout'))
			{
				$this->setLayout($layout);
			}
		}
		
		$offset = $this->state->get('list.offset');
		
		// Process the content plugins for article description
		$item->text = $item->about;
		JPluginHelper::importPlugin('content');
		$app->triggerEvent('onContentPrepare', array('com_cjblog.profile',	&$item,	&$this->params,	$offset));
		
		$item->event = new stdClass();
		$results = $app->triggerEvent('onContentAfterTitle', array('com_cjblog.profile', &$item, &$this->params, $offset));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));
		
		$results = $app->triggerEvent('onContentBeforeIntro', array('com_cjblog.profile', &$item, &$this->params, $offset));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));
		
		$results = $app->triggerEvent('onContentAfterIntro', array('com_cjblog.profile', &$item, &$this->params, $offset));
		$item->event->afterDisplayContent = trim(implode("\n", $results));
		
		JPluginHelper::importPlugin('cjblog');
		$app->triggerEvent('onProfilePrepareContent', array('com_cjblog.profile', &$item, &$this->params, $offset));
		
		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx', ''));
		
		// Trigger CjBlog Apps
		JPluginHelper::importPlugin('cjblogapps');
		$apps 			= new stdClass();
		$apps->id 		= $this->tab;
		$apps->tabs 	= array();
		$apps->content 	= '';
		
		$app->triggerEvent('onProfileDisplay', array('com_cjblog.profile', &$item, &$this->params, &$apps));
		$this->apps 	= $apps;
		
		$this->_prepareDocument();
		
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument ()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_CJBLOG_PROFILE'));
		}
		
		$title = $this->params->get('page_title', '');
		
		$id = (int) @$menu->query['id'];
		
		// if the menu item does not concern this article
		if ($menu && ($menu->query['option'] != 'com_cjblog' || $menu->query['view'] != 'profile' || $id != $this->item->id))
		{
			// If this is not a single article menu item, set the page title to the article title
			if ($this->item->name)
			{
				$title = $this->item->name;
			}
			
			$path = array(array('title' => $this->item->name,'link' => ''));
			
			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}
		
		// Check for empty title and add site name if param is set
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
		
		if (empty($title))
		{
			$title = $this->item->name;
		}
		$this->document->setTitle($title);
		
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}
}