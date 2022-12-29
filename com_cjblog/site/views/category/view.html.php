<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogViewCategory extends JViewCategory
{

	protected $lead_items = array();

	protected $intro_items = array();

	protected $link_items = array();

	protected $columns = 1;

	protected $defaultPageTitle = 'COM_CJBLOG_ARTICLES';

	protected $viewName = 'article';
	
	public $state = null;
	
	public $pagination = null;
	
	public $items = null;
	
	public $category = null;

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl
	 *        	The name of the template file to parse; automatically searches
	 *        	through the template paths.
	 *        	
	 * @return mixed A string if successful, otherwise a Error object.
	 */
	public function display ($tpl = null)
	{
		parent::commonCategoryDisplay();
		
		// Prepare the data
		// Get the metrics for the structural page layout.
		$params = $this->params;
		$numLeading = $params->def('num_leading_articles', 1);
		$numIntro = $params->def('num_intro_articles', 4);
		$numLinks = $params->def('num_links', 4);
		
		$this->params->set('catid', $this->category->id);
		
		// Compute the article slugs and prepare introtext (runs content
		// plugins).
		foreach ($this->items as $item)
		{
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
			
			$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
			
			// No link for ROOT category
			if ($item->parent_alias == 'root')
			{
				$item->parent_slug = null;
			}
			
			$item->catslug = $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
		}
		
		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will
		// match
		$app = JFactory::getApplication();
		$active = $app->getMenu()->getActive();
		
		if ((! $active) ||
				 ((strpos($active->link, 'view=category') === false) || (strpos($active->link, '&id=' . (string) $this->category->id) === false)))
		{
			// Get the layout from the merged category params
			if ($layout = $this->category->params->get('category_layout'))
			{
				$this->setLayout($layout);
			}
		}
		// At this point, we are in a menu item, so we don't override the layout
		elseif (isset($active->query['layout']))
		{
			// We need to set the layout from the query in case this is an
			// alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}
		
		// For blog layouts, preprocess the breakdown of leading, intro and
		// linked articles.
		// This makes it much easier for the designer to just interrogate the
		// arrays.
		if (($params->get('layout_type') == 'blog') || ($this->getLayout() == 'blog'))
		{
			// $max = count($this->items);
			
			foreach ($this->items as $i => $item)
			{
				if ($i < $numLeading)
				{
					$this->lead_items[] = $item;
				}
				
				elseif ($i >= $numLeading && $i < $numLeading + $numIntro)
				{
					$this->intro_items[] = $item;
				}
				
				elseif ($i < $numLeading + $numIntro + $numLinks)
				{
					$this->link_items[] = $item;
				}
				else
				{
					continue;
				}
			}
			
			$this->columns = max(1, $params->def('num_columns', 1));
			
			$params->def('multi_column_order', 1);
		}
		
		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 */
	protected function prepareDocument ()
	{
		parent::prepareDocument();
		$menu = $this->menu;
		$id = (int) @$menu->query['id'];

		if ($menu && (!isset($menu->query['option']) || $menu->query['option'] !== 'com_content' || $menu->query['view'] === 'article'
		              || $id != $this->category->id))
		{
			$path = array(array('title' => $this->category->title, 'link' => ''));
			$category = $this->category->getParent();

			while ($category !== null && $category->id !== 'root'
			       && (!isset($menu->query['option']) || $menu->query['option'] !== 'com_content' || $menu->query['view'] === 'article' || $id != $category->id))
			{
				$path[] = array('title' => $category->title, 'link' => CjBlogHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}

			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$this->pathway->addItem($item['title'], $item['link']);
			}
		}
		
		parent::addFeed();
	}
}
