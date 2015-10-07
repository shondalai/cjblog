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