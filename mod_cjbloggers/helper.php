<?php
/**
 * @package     corejoomla.site
 * @subpackage  mod_cjbloggers
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;


class CjBloggersHelper 
{
	public static function get_bloggers_list($type, $count = 10, $excludes = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id, a.avatar, a.num_articles, a.profile_views, a.points, u.name, u.username, u.email')
			->from('#__cjblog_users a')
			->join('left', '#__users u on a.id = u.id')
			->where('a.num_articles > 0');
		
		if(!empty($excludes))
		{
			$query2 = $db->getQuery(true)
				->select('distinct(user_id)')
				->from('#__usergroups as ug1')
				->join('inner', '#__usergroups AS ug2 ON ug2.lft >= ug1.lft AND ug1.rgt >= ug2.rgt')
				->join('inner', '#__user_usergroup_map AS m ON ug2.id=m.group_id')
				->where('ug1.id in ('.implode(',', $excludes).'))');
			
			$query->where('u.id not in ('.$query2->__toString().')');
 		}
 		
 		switch ($type)
 		{
 			case 1:
 				$query->order('a.id desc');
 				break;
 				
 			case 2:
 				$query->order('a.num_articles desc');
 				break;
 				
 			default:
 				$query->order('a.profile_views desc');
 				break;
 		}
		
		$db->setQuery($query, 0, $count);
		$bloggers = $db->loadObjectList();
		
		return $bloggers;
	}
}