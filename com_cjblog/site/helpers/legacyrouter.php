<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class CjBlogRouterRulesLegacy implements JComponentRouterRulesInterface
{
    public function __construct($router)
    {
        $this->router = $router;
    }
    
    public function preprocess(&$query)
    {
    }
    
    public function build(&$query, &$segments)
	{
        $app = JFactory::getApplication();
        $menu = $app->getMenu();

		// Get a menu item based on Itemid or currently active
		$params = JComponentHelper::getParams('com_cjblog');
		$advanced = $params->get('sef_advanced_link', 0);

		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
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

		// Are we dealing with an topic or category that is attached to a menu item?
		if (
			($menuItem instanceof stdClass) && 
			($menuItem->query['view'] == $query['view']) && 
			isset($query['id']) && 
			isset($menuItem->query['id']) && 
			($menuItem->query['id'] == (int) $query['id'])
		)
		{
			unset($query['view']);

			if (isset($query['catid']))
			{
				unset($query['catid']);
			}

			if (isset($query['layout']))
			{
				unset($query['layout']);
			}

			unset($query['id']);

			return $segments;
		}
		
		switch ($view)
		{
			case 'category':

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
				
				break;

			case 'profile':

				if (!$menuItemGiven)
				{
					$segments[] = $view;
					unset($query['view']);
				}
				else if($query['view'] == $menuItem->query['view'])
				{
					unset($query['view']);
								
					if(isset($query['id']) && $query['id'] > 0)
					{
						require_once JPATH_ROOT.'/components/com_cjblog/lib/api.php';
						$api = CjBlogApi::getProfileApi();
						$profile = $api->getUserProfile($query['id']);
						
						$segments[] = $profile['handle'];
						unset($query['id']);
						unset($query['uId']);
					}
				}
								
				break;

			case 'articles':
			case 'leaderboard':
			case 'users':
			case 'search':
			case 'categories':
					
				if (!$menuItemGiven)
				{
					$segments[] = $view;
					unset($query['view']);
				}
				else if($query['view'] == $menuItem->query['view'])
				{
					unset($query['view']);
				}
					
				break;

			case 'archive':

				if (!$menuItemGiven)
				{
					$segments[] = $view;
					unset($query['view']);
				}
				
				if (isset($query['year']))
				{
					if ($menuItemGiven)
					{
						$segments[] = $query['year'];
						unset($query['year']);
					}
				}
				
				if (isset($query['year']) && isset($query['month']))
				{
					if ($menuItemGiven)
					{
						$segments[] = $query['month'];
						unset($query['month']);
					}
				}
				
				break;
				
			case 'featured':

				if (!$menuItemGiven)
				{
					$segments[] = $view;
				}
				
				unset($query['view']);
				
				break;
		}
		
		/*
		 * If the layout is specified and it is the same as the layout in the menu item, we
		* unset it so it doesn't go into the query string.
		*/
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
	}

	public function parse(&$segments, &$vars)
	{
		$total = count($segments);

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		// Count route segments
		$count = count($segments);
		
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$menu = $app->getMenu();
		$item = $menu->getActive();
		
		/*
		 * Standard routing for topics.  If we don't pick up an Itemid then we get the view from the segments
		 * the first segment is the view and the last segment is the id of the topic or category.
		 */
		if (!isset($item))
		{
			$vars['view'] = $segments[0];
			$vars['id'] = $segments[$count - 1];
			
			return $vars;
		}
		
		$params = JComponentHelper::getParams('com_cjblog');
		$advanced = $params->get('sef_advanced_link', 0);
		
		/*
		 * If there is only one segment, then it points to either an profile, topic or a category.
		 * We test it first to see if it is a category.  If the id and alias match a category,
		 * then we assume it is a category.  If they don't we assume it is an topic
		 */
		if ($count == 1)
		{
			// We check to see if an alias is given.  If not, we assume it is an profile
			if (strpos($segments[0], ':') === false)
			{
				// first check if the handle exists otherwise its a topic
				$query = $db->getQuery(true)->select('id')->from('#__cjblog_users')->where('handle = '.$db->q($segments[0]));
				$db->setQuery($query);
				$profileId = (int) $db->loadResult();
				
				if($profileId)
				{
					$vars['view'] = 'profile';
					$vars['id'] = $segments[0];
					$vars['uId'] = $profileId;
				}
				
				return $vars;
			}

			list($id, $alias) = explode(':', $segments[0], 2);

			// First we check if it is a category
			$category = JCategories::getInstance('Content')->get($id);

			if ($category && $category->alias == $alias)
			{
				$vars['view'] = 'category';
				$vars['id'] = $id;

				return $vars;
			}
		}

		/*
		 * If there was more than one segment, then we can determine where the URL points to
		* because the first segment will have the target category id prepended to it.  If the
		* last segment has a number prepended, it is an topic, otherwise, it is a category.
		*/
		if (!$advanced)
		{
			$cat_id = (int) $segments[0];
			$vars['view'] = 'category';
			$vars['id'] = $cat_id;
			return $vars;
		}

		// We get the category id from the menu item and search from there
		$id = $item->query['id'];
		$category = JCategories::getInstance('Content')->get($id);

		if (!$category)
		{
			return $vars;
		}

		$categories = $category->getChildren();
		$vars['catid'] = $id;
		$vars['id'] = $id;
		$found = 0;

		foreach ($segments as $segment)
		{
			$segment = str_replace(':', '-', $segment);

			foreach ($categories as $category)
			{
				if ($category->alias == $segment)
				{
					$vars['id'] = $category->id;
					$vars['catid'] = $category->id;
					$vars['view'] = 'category';
					$categories = $category->getChildren();
					$found = 1;
					break;
				}
			}

			if ($found == 0)
			{
				if ($advanced)
				{
					$db = JFactory::getDbo();
					$query = $db->getQuery(true)
					->select($db->quoteName('id'))
					->from('#__content')
					->where($db->quoteName('catid') . ' = ' . (int) $vars['catid'])
					->where($db->quoteName('alias') . ' = ' . $db->quote($db->quote($segment)));
					$db->setQuery($query);
					$cid = $db->loadResult();
				}
				else
				{
					$cid = $segment;
				}

				$vars['id'] = $cid;

				if ($item->query['view'] == 'archive' && $count != 1)
				{
					$vars['year'] = $count >= 2 ? $segments[$count - 2] : null;
					$vars['month'] = $segments[$count - 1];
					$vars['view'] = 'archive';
				}
			}

			$found = 0;
		}
	}
}