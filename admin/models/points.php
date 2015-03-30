<?php
/**
 * @version		$Id: points.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');

class CjBlogModelPoints extends JModelLegacy {

	function __construct() {

		parent::__construct ();
	}
	
	function get_recent_activity(){

		$app = JFactory::getApplication();
		$result = new stdClass();
		$where = array();
		
		$filter_order       = $app->getUserStateFromRequest( "cjblogpoints.filter_order",'filter_order','a.created',	'cmd' );
		$filter_order_Dir   = $app->getUserStateFromRequest( "cjblogpoints.filter_order_Dir",'filter_order_Dir','desc','word' );
		$search             = $app->getUserStateFromRequest( "cjblogpoints.search",'filter_search','','string' );
		$limitstart         = $app->getUserStateFromRequest( "cjblogpoints.limitstart",'limitstart','','int' );
		$ruleid				= $app->getUserStateFromRequest( "cjblogpoints.ruleid",'ruleid','','int' );
		$userid				= $app->input->getInt('userid', 0);
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		if($userid > 0){
			
			$where[] = 'a.user_id = '.$userid;
		}
		
		if($ruleid > 0){
			
			$where[] = 'a.rule_id = '.$ruleid;
		}
		
		if ($search) {
		
			$where[] = 'u.username like \'%'.$this->_db->escape($search).'%\' or u.name like \'%'.$this->_db->escape($search).'%\'';
		}
		
		$condition 	= count( $where ) ? ' where ('.implode( ') and (', $where ).')' : '';
		$orderby    = ' order by '.$filter_order.' '.$filter_order_Dir;
		
		if (empty($this->_pagination)) {
		
			$query = 'select count(*) from '.T_CJBLOG_POINTS.' a left join #__users u on a.user_id = u.id '.$condition;
			$this->_db->setQuery($query);
			$total = $this->_db->loadResult();
		
			jimport('joomla.html.pagination');
			$result->pagination = new JPagination($total, $limitstart, $limit);
		}
		
		$query = '
				select 
					a.id, a.description, a.published, a.created, a.points,
					u.id as user_id, u.name, u.username,
					p.title as rule_title, p.description as rule_description
				from 
					'.T_CJBLOG_POINTS.' a
				left join
					'.T_CJBLOG_POINT_RULES.' p on a.rule_id = p.id 
				left join
					#__users u on a.user_id = u.id
				'.$condition.$orderby;
		
		$this->_db->setQuery($query, $limitstart, $limit);
		$result->points = $this->_db->loadObjectList();
		
		$query = 'select id, title from '.T_CJBLOG_POINT_RULES;
		$this->_db->setQuery($query);
		$result->rules = $this->_db->loadObjectList();
		
		$result->state = array(
				'limit'=>$limit, 
				'limitstart'=>$limitstart, 
				'list.ordering'=>$filter_order, 
				'list.direction'=>$filter_order_Dir, 
				'filter.search'=>$search,
				'filter.userid'=>$userid,
				'filter.ruleid'=>$ruleid);
		
		return $result;
	}
	
	function get_rules(){
	
		$app = JFactory::getApplication();
		$result = new stdClass();
		$wheres = array();
	
		$filter_order       = $app->getUserStateFromRequest( "cjblogpointrules.filter_order",'filter_order','a.id',	'cmd' );
		$filter_order_Dir   = $app->getUserStateFromRequest( "cjblogpointrules.filter_order_Dir",'filter_order_Dir','DESC','word' );
		$search             = $app->getUserStateFromRequest( "cjblogpointrules.search",'filter_search','','string' );
		$limitstart         = $app->getUserStateFromRequest( "cjblogpointrules.limitstart",'limitstart','','int' );
	
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	
		if ($search) {
	
			$wheres[] = 'a.name like \'%'.$this->_db->escape($search).'%\' or a.description like \'%'.$this->_db->escape($search).'%\'';
		}
	
		$condition 	= count( $wheres ) ? ' where ('.implode( ') and (', $wheres ).')' : '';
		$orderby    = ' order by '.$filter_order.' '.$filter_order_Dir;
	
		if (empty($this->_pagination)) {
	
			$query = 'select count(*) from '.T_CJBLOG_POINT_RULES.' '.$condition;
			$this->_db->setQuery($query);
			$total = $this->_db->loadResult();

			jimport('joomla.html.pagination');
			$result->pagination = new JPagination($total, $limitstart, $limit);
		}
	
		$query = '
			select
				a.id, a.name, a.title, a.description, a.published, a.points, a.asset_name, a.auto_approve, a.catid, l.title as viewlevel
			from
				'.T_CJBLOG_POINT_RULES.' a
			left join
				#__viewlevels l on a.access = l.id
			'.$condition.$orderby;

		$this->_db->setQuery($query, $limitstart, $limit);
	
		$result->rules = $this->_db->loadObjectList();
		$result->state = array('limit'=>$limit, 'limitstart'=>$limitstart, 'list.ordering'=>$filter_order, 'list.direction'=>$filter_order_Dir, 'filter.search'=>$search);
	
		return $result;
	}
	
	function publish(array $ids, $status){
		
		$id = implode(',', $ids);
		
		$query = 'select user_id from '.T_CJBLOG_POINTS.' where id in ('.$id.')';
		$this->_db->setQuery($query);
		$users = $this->_db->loadResultArray();
		
		$query = 'update '.T_CJBLOG_POINTS.' set published = '.$status.' where id in ('.$id.')';
		$this->_db->setQuery($query);
			
		if($this->_db->query()){
			
			if(!empty($users)){
			
				$query = '
					update
						'.T_CJBLOG_USERS.' u
					set
						points = (select sum(points) from '.T_CJBLOG_POINTS.' p where p.user_id = u.id and p.published = 1 group by p.user_id)
					where
						u.id in ('.$id.')';
			
				$this->_db->setQuery($query);
				$this->_db->query();
			}

			return true;
		}
		
		return false;
	}
	
	function set_auto_approve(array $ids, $status){
	
		$id = implode(',', $ids);
	
		$query = 'update '.T_CJBLOG_POINT_RULES.' set auto_approve = '.$status.' where id in ('.$id.')';
		$this->_db->setQuery($query);
			
		if($this->_db->query()){
				
			return true;
		}
	
		return false;
	}
	
	function delete(array $ids){
		
		$id = implode(',', $ids);
		
		$query = 'select user_id from '.T_CJBLOG_POINTS.' where id in ('.$id.')';
		$this->_db->setQuery($query);
		$users = $this->_db->loadResultArray();
		
		$query = 'delete from '.T_CJBLOG_POINTS.' where id in ('.$id.')';
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			if(!empty($users)){
				
				$query = '
					update 
						'.T_CJBLOG_USERS.' u 
					set 
						points = (select sum(points) from '.T_CJBLOG_POINTS.' p where p.user_id = u.id and p.published = 1 group by p.user_id)
					where 
						u.id in ('.$id.')';
				
				$this->_db->setQuery($query);
				$this->_db->query();
			}
			
			return true;
		}
		
		return false;
	}
	
	function publish_rules(array $ids, $status){
		
		$id = implode(',', $ids);
		
		$query = 'update '.T_CJBLOG_POINT_RULES.' set published = '.$status.' where id in ('.$id.')';
		$this->_db->setQuery($query);
			
		if($this->_db->query()){
			
			return true;
		}
		
		return false;
	}
	
	function delete_rules(array $ids){
		
		$id = implode(',', $ids);
		
		$query = 'delete from '.T_CJBLOG_POINT_RULES.' where id in ('.$id.')';
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			return true;
		}
		
		return false;
	}
	
	function import_rules(){
		
		$app = JFactory::getApplication();
		$file = $app->input->files->get('rule-file', array(), 'array');
		
		JLog::add('Import Rules - Started: File: '.print_r($file, true), JLog::DEBUG, CJBLOG);
		
		if ($file['error'] > 0){
		
			$error = 'File/File Size Error. File Error='.$file['error'].'| File Size='.$file['size'];
			$this->setError();
			JLog::add('Import Rules - Validation: Error: '.$error, JLog::ERROR, CJBLOG);

			return false;
		}
		
		//check the file extension is ok
		if (JFile::getExt($file['name']) != 'xml'){
		
			$this->setError('Invalid file extension.');
			JLog::add('Import Rules - Validation: Error: Invalid file extension: '.JFile::getExt($file['name']), JLog::ERROR, CJBLOG);
			
			return false;
		}
		
		$extensions = simplexml_load_file($file['tmp_name']);
		if(empty($extensions) || empty($extensions->extension_rule)) {
			
			JLog::add('Import Rules - Validation: Error: File has no extension rules. Plugin: '.print_r($extensions, true), JLog::ERROR, CJBLOG);
			return false;
		}
		
		$db = JFactory::getDbo();
		
		foreach ($extensions->extension_rule as $extension_rule)
		{
			try 
			{
				$condtions = array();
				if(!empty($extension_rule->conditional_rule))
				{
					$condition = new stdClass();
					$condition->criteria = (string) $extension_rule->conditional_rule;
					$condition->comparator = 'eq';
					$condition->value = 1;
					$condition->points = 0;
					$conditions[] = $condition;
				}
				
				$conditional_rules = json_encode($conditions);
				
				$query = $db->getQuery(true)
					->insert('#__cjblog_point_rules')
					->columns('name, title, description, asset_name, auto_approve, access, points, published, conditional_rules')
					->values(
						$db->q($extension_rule->rule_name).','.
						$db->q($extension_rule->rule_title).','.
						$db->q($extension_rule->rule_description).','.
						$db->q($extension_rule->asset_name).','.
						intval($extension_rule->auto_approve).','.
						intval($extension_rule->access_level).',0, 0,'.
						$db->q($conditional_rules));
				$db->setQuery($query);
				$db->execute();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage('Rule '.$extension_rule->rule_name.' is not installed as it already exists.');
			}
		}
		
		JLog::add('Import Rules - Somthing wrong in DB query. DB Error: '.$this->_db->getErrorMsg(), JLog::ERROR, CJBLOG);
		
		return true;
	}
	
	public function get_rule($id){
		
		$query = '
				select 
					id, title, name, description, asset_name, points, auto_approve, published, catid, access, conditional_rules
				from
					'.T_CJBLOG_POINT_RULES.'
				where 
					id = '.$id;
		
		$this->_db->setQuery($query);
		$rule = $this->_db->loadObject();
		
		return $rule;
	}
	
	public function save_rule()
	{
		$app = JFactory::getApplication();
		$rule = new stdClass();
		
		$rule->id = $app->input->post->getInt('rule_id', 0);
		$rule->title = $app->input->post->getString('rule_title', null);
		$rule->description = $app->input->post->getString('rule_description', null);
		$rule->points = $app->input->post->getInt('rule_points', 0);
		$rule->conditional_rules = $app->input->post->getString('conditional_rules', null);
		$rule->published = $app->input->post->getInt('rule_state', 0);
		$rule->auto_approve = $app->input->post->getInt('rule_auto_approve', 0);
		$rule->access = $app->input->post->getInt('access', 1);
		
		if(empty($rule->title) || empty($rule->description))
		{
			$app->enqueueMessage(JText::_('COM_CJBLOG_MISSING_REQUIRED_FIELDS'), 'error');
			return $rule;
		}
		
		try 
		{
			$db = JFactory::getDbo();
			$db->updateObject('#__cjblog_point_rules', $rule, 'id');
			return true;
		}
		catch (Exception $e)
		{
			return $rule;
		}
		
		return $rule;
	}
}
?>