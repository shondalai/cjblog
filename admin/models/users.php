<?php
/**
 * @version		$Id: users.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');

class CjBlogModelUsers extends JModelLegacy {

	function __construct() {

		parent::__construct ();
	}
	
	function get_users(){
		
		$app = JFactory::getApplication();
		$result = new stdClass();
		$where = array();
		
        $filter_order       = $app->getUserStateFromRequest( "cjblogusers.filter_order",'filter_order','ju.id',	'cmd' );
        $filter_order_Dir   = $app->getUserStateFromRequest( "cjblogusers.filter_order_Dir",'filter_order_Dir','DESC','word' );
        $search             = $app->getUserStateFromRequest( "cjblogusers.search",'filter_search','','string' );
        $limitstart         = $app->getUserStateFromRequest( "cjblogusers.limitstart",'limitstart','','int' );

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        
        if ($search) {
        	
        	$where[] = 'ju.username like \'%'.$this->_db->escape($search).'%\' or ju.name like \'%'.$this->_db->escape($search).'%\'';
        }

        $condition 	= count( $where ) ? ' where ('.implode( ') and (', $where ).')' : '';
        $orderby    = 'order by '.$filter_order.' '.$filter_order_Dir;
		
		if (empty($this->_pagination)) {
			
			$query = 'select count(*) from '.T_CJBLOG_USERS.' u left join #__users ju on u.id = ju.id '.$condition;
			$this->_db->setQuery($query);
			$total = $this->_db->loadResult();
			
			jimport('joomla.html.pagination');
			$result->pagination = new JPagination($total, $limitstart, $limit);
		}
		
		$query = '
			select
				u.id, u.about, u.avatar, u.points, u.num_articles, u.num_badges, u.profile_views,
				ju.name, ju.username, ju.registerDate, ju.lastvisitDate
			from
				'.T_CJBLOG_USERS.' u
			left join
				#__users ju on ju.id = u.id
			'.$condition.' '.$orderby;
		
		$this->_db->setQuery($query, $limitstart, $limit);
		
		$result->users = $this->_db->loadObjectList();
		$result->state = array('limit'=>$limit, 'limitstart'=>$limitstart, 'list.ordering'=>$filter_order, 'list.direction'=>$filter_order_Dir, 'filter.search'=>$search);
		
		return $result;
	}
	
	function synchronize(){
		
		$query = 'insert into '.T_CJBLOG_USERS.'(id) (select id from #__users ju where ju.id not in (select id from '.T_CJBLOG_USERS.'))';
		$this->_db->setQuery($query);
		
		if(!$this->_db->query()){
				
			return false;
		}
		
		$query = 'update '.T_CJBLOG_USERS.' u set num_articles = (select count(*) from #__content c where c.created_by = u.id and state = 1 group by c.created_by)';
		$this->_db->setQuery($query);
		
		if(!$this->_db->query()){
			
			return false;
		}
		
		$query = 'update '.T_CJBLOG_USERS.' u set points = (select sum(points) from '.T_CJBLOG_POINTS.' p where p.user_id = u.id group by p.user_id)';
		$this->_db->setQuery($query);
		
		if(!$this->_db->query()){
				
			return false;
		}
		
		$query = 'update '.T_CJBLOG_USERS.' u set num_badges = (select count(*) from '.T_CJBLOG_USER_BADGE_MAP.' m where m.user_id = u.id group by m.user_id)';
		$this->_db->setQuery($query);
		
		if(!$this->_db->query()){
		
			return false;
		}
		
		$query = 'update '.T_CJBLOG_BADGE_RULES.' r set num_assigned = (select count(*) from '.T_CJBLOG_USER_BADGE_MAP.' m where m.rule_id = r.id group by m.rule_id)';
		$this->_db->setQuery($query);
		
		if(!$this->_db->query()){
		
			return false;
		}
		
		return true;
	}
	
	function get_all_user_names($search = null){
		
		$where = '';
		
		if(!empty($search)){
			
			$where = ' where username like \'%'.$this->_db->escape($search).'%\' or name like \'%'.$this->_db->escape($search).'%\'';
		}
		
		$query = 'select id, name, username from #__users'.$where;
		$this->_db->setQuery($query, 0, 50);
		
		return $this->_db->loadObjectList();
	}
	
	function delete($ids){

		$id = implode(',', $ids);
		
		$query = 'delete from '.T_CJBLOG_USERS.' where id in ('.$id.')';
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
				
			return true;
		}
		
		return false;
	}
}