<?php
/**
 * @version		$Id: users.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CJBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');

class CjBlogModelUsers extends JModelLegacy {

	var $_pagination = null;
	
	function __construct() {

		parent::__construct ();
	}
	
	function get_users($action = 1, $options = array())
	{
		$app 			= JFactory::getApplication();
		$db 			= JFactory::getDbo();
		$params 		= JComponentHelper::getParams('com_cjblog');
		$aboutTextApp 	= $params->get('about_text_app', 'cjblog');
		$result 		= new stdClass();
		$wheres 		= array();
		
		$limit 			= $app->getCfg('list_limit', 20);
		$limitstart 	= $app->input->getInt('limitstart', 0);
		$limitstart 	= ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$query = $db->getQuery(true)
			->select('u.id, u.avatar, u.points, u.num_articles, u.num_badges, ju.name, ju.username, ju.registerDate, ju.lastvisitDate, ju.email')
			->from('#__cjblog_users u')
			->join('left', '#__users ju on ju.id = u.id');
		
		if($aboutTextApp == 'easyprofile')
		{
			$query
				->select($db->qn($db->escape('e.' . $params->get('easyprofile_about', 'author_info'))).' AS about')
				->join('left', '#__jsn_users AS e on ju.id = e.id');
		}
		else
		{
			$query->select('u.about');
		}
		
		$search = !empty($options['query']) ? $options['query'] : '';
		$wheres[] = 'ju.block = 0';
		
		switch ($action){
			
			case 1: //latest
				$query->where('u.num_articles > 0');
				$query->order('ju.registerDate desc');
				break;
				
			case 2: //top
				$query->where('u.num_articles > 0');
				break;
				
			case 3: // badge owners
				$query->where('u.id in( select user_id from #__cjblog_user_badge_map where badge_id = '.((int) $options['badge_id']).')');
				break;
				
			case 4: //search
				$query
					->where('u.num_articles > 0')
					->where('ju.name like \'%'.$db->escape($search).'%\' or ju.username like \'%'.$db->escape($search).'%\'');
				break;
			
			default:
				$query->order('u.num_articles desc');
				break;
		}
		
		$excludes = $params->get('exclude_user_groups', array());
		if(!empty($excludes))
		{
			JArrayHelper::toInteger($excludes);
			$subQuery = $db->getQuery(true)
				->select('distinct(user_id)')
				->from('#__usergroups as ug1')
				->join('inner', '#__usergroups AS ug2 ON ug2.lft >= ug1.lft AND ug1.rgt >= ug2.rgt')
				->join('inner', '#__user_usergroup_map AS m ON ug2.id=m.group_id')
				->where('ug1.id in ('.implode(',', $excludes).')');
			
			$query->where('u.id not in (' . $subQuery->__toString() . ')');
		}

		$db->setQuery($query, $limitstart, $limit);
		$result->users = $db->loadAssocList('id');
		
		if (empty($this->_pagination)) 
		{
			jimport('joomla.html.pagination');
			$query->clear('select')->clear('limit')->select('count(*)');
			$db->setQuery($query);
			$total = (int) $db->loadResult();
			
			$this->_pagination = new JPagination($total, $limitstart, $limit);
		}
		
		$result->pagination = $this->_pagination;
		$result->state = array('limit'=>$limit, 'limitstart'=>$limitstart, 'search'=>$search);
		
		return $result;
	}
	
	function save_about($id, $about){
		
		$query = '
			update
				'.T_CJBLOG_USERS.'
			set
				about = '.$this->_db->quote($about).'
			where
				id = '.$id;

		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			return true;
		}
		
		return false;
	}
	
	function save_user_avatar_name($id, $avatar){
		
		$query = '
			update
				'.T_CJBLOG_USERS.'
			set
				avatar = '.$this->_db->quote($avatar).'
			where
				id = '.$id;
		
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			return true;
		}
		
		$this->setError($this->_db->getErrorMsg());
		return false;
	}
	
	function hit($id){
		
		$query = 'update '.T_CJBLOG_USERS.' set profile_views = profile_views + 1 where id = '.$id;
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			return true;
		}
		
		return false;
	}
	
	function get_user_point_details($user_id = 0, $params = array()){
		
		$app = JFactory::getApplication();
		
		$limit = $app->getCfg('list_limit', 20);
		$limitstart = $app->input->getInt('start', 0);
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$order = isset($params['order']) ? $params['order'] : 'a.created';
		$order_dir = isset($params['order_dir']) ? $params['order_dir'] : 'desc';
		
		$wheres = array();
		
		if($user_id){
			
			$wheres[] = 'a.user_id = '.$user_id;
		}
		
		$where = !empty($wheres) ? 'where ('.implode(') and (', $wheres).')' : '';
		
		jimport('joomla.html.pagination');
		
		$query = 'select count(*) from '.T_CJBLOG_POINTS.' a '.$where;
		$this->_db->setQuery($query);
		$total = (int)$this->_db->loadResult();
		
		$result = new stdClass();
		$result->pagination = new JPagination($total, $limitstart, $limit);
		
		$query = '
			select
				a.id, a.points, a.description, a.created, 
				r.id as rule_id, r.description as rule_description
			from
				'.T_CJBLOG_POINTS.' a
			left join
				'.T_CJBLOG_POINT_RULES.' r on a.rule_id = r.id
			'.$where.'
			order by
				'.$order.' '.$order_dir;
		
		$this->_db->setQuery($query, $limitstart, $limit);
		$result->points = $this->_db->loadObjectList();
		$result->state = array('limit'=>$limit, 'limitstart'=>$limitstart);
		
		return $result;
	}
}
?>

