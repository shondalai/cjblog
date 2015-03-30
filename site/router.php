<?php
/**
 * @package     corejoomla.site
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2015 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined ( '_JEXEC' ) or die;

class CjBlogRouter extends JComponentRouterBase
{
	public function build(&$query)
	{
		$segments = array();
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$params = JComponentHelper::getParams('com_cjblog');
		$advanced = $params->get('sef_advanced_link', 0);
		
		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
			$menuItemGiven = false;
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}
		
		// Check again
		if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_cjblog')
		{
			$menuItemGiven = false;
			unset($query['Itemid']);
		}
		
		if(isset($query['task'])) {
			 
			$segments[] = $query['task'];
				
			if($query['task'] != 'article.edit' && $query['task'] != 'article.add')
			{
				unset($query['task']);
				unset($query['view']);
			}
		}
		
		if(isset($query['id'])) {
			 
			$segments[] = $query['id'];
			unset($query['id']);
		}

		if (isset($query['layout']))
		{
			if ($menuItemGiven && isset($menuItem->query['layout']))
			{
				if ($query['layout'] == $menuItem->query['layout'])
				{
					unset($query['layout']);
				}
			}
			else
			{
				if ($query['layout'] == 'default')
				{
					unset($query['layout']);
				}
			}
		}
		return $segments;
	}

	public function parse(&$segments)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$item = $menu->getActive();
	
		$vars = array();
	
		if(isset($segments['view']))
		{
			$vars['view'] = $segments['view'];
		}
		else if($item)
		{
			$vars['view'] = $item->query['view'];
		}
	
		if(count($segments) == 2 )
		{
			$vars['task'] = $segments[0];
			$vars['id'] = $segments[1];
		}
		elseif(count($segments) == 1 )
		{
			if(!empty($vars['view']))
			{
				switch ($vars['view'])
				{
					case 'articles':
					case 'users':
					case 'tags':
	
						$vars['task']	= $segments[0];
						break;
	
					default:
	
						$vars['id'] = $segments[0];
						break;
				}
			}
		}
		elseif(count($segments) == 3)
		{
			// this should not come in ideal situation
			$vars['view'] = $segments[0];
			$vars['task'] = $segments[1];
			$vars['id'] = $segments[2];
		}
	
		return $vars;
	}
	
	public function buildOld(&$query)
	{
		$segments	= array();
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$params = JComponentHelper::getParams('com_cjblog');
		$advanced = $params->get('sef_advanced_link', 0);
		
		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
			$menuItemGiven = false;
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}
		
		// Check again
		if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_cjblog')
		{
			$menuItemGiven = false;
			unset($query['Itemid']);
		}
		
		if (isset($query['view']))
		{
			$view = $query['view'];
		}
		else
		{
			// We need to have a view in the query or it is an invalid URL
			return $segments;
		}
		
		if ($view == 'category')
		{
			if (!$menuItemGiven)
			{
				$segments[] = $view;
			}
		
			unset($query['view']);
		
			if (isset($query['id']))
			{
				$catid = $query['id'];
			}
			else
			{
				// We should have id set for this view.  If we don't, it is an error
				return $segments;
			}
		
			if ($menuItemGiven && isset($menuItem->query['id']))
			{
				$mCatid = $menuItem->query['id'];
			}
			else
			{
				$mCatid = 0;
			}
		
			$categories = JCategories::getInstance('Content');
			$category = $categories->get($catid);
		
			if (!$category)
			{
				// We couldn't find the category we were given.  Bail.
				return $segments;
			}
		
			$path = array_reverse($category->getPath());
		
			$array = array();
		
			foreach ($path as $id)
			{
				if ((int) $id == (int) $mCatid)
				{
					break;
				}
		
				list($tmp, $id) = explode(':', $id, 2);
		
				$array[] = $id;
			}
		
			$array = array_reverse($array);
		
			if (!$advanced && count($array))
			{
				$array[0] = (int) $catid . ':' . $array[0];
			}
		
			$segments = array_merge($segments, $array);
		
			unset($query['id']);
			unset($query['catid']);
		}
		
		if (isset($query['layout']))
		{
			if ($menuItemGiven && isset($menuItem->query['layout']))
			{
				if ($query['layout'] == $menuItem->query['layout'])
				{
					unset($query['layout']);
				}
			}
			else
			{
				if ($query['layout'] == 'default')
				{
					unset($query['layout']);
				}
			}
		}
		
		$total = count($segments);
		
		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}
		
		return $segments;
	}
}

function CjBlogBuildRoute(&$query)
{
	$router = new CjBlogRouter;

	return $router->build($query);
}

function CjBlogParseRoute($segments)
{
	$router = new CjBlogRouter;

	return $router->parse($segments);
}