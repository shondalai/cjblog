<?php
/**
 * @version		$Id: badges.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');

class CjBlogModelBadges extends JModelLegacy {

	function __construct() {

		parent::__construct ();
	}
	
	function get_badges(){
		
		$app = JFactory::getApplication();
		$result = new stdClass();
		$where = array();
		
		$filter_order       = $app->getUserStateFromRequest( "cjblogbadges.filter_order",'filter_order','b.id',	'cmd' );
		$filter_order_Dir   = $app->getUserStateFromRequest( "cjblogbadges.filter_order_Dir",'filter_order_Dir','DESC','word' );
		$search             = $app->getUserStateFromRequest( "cjblogbadges.search",'filter_search','','string' );
		$limitstart         = $app->getUserStateFromRequest( "cjblogbadges.limitstart",'limitstart','','int' );
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		if ($search) {
			 
			$where[] = 'b.title like \'%'.$this->_db->escape($search).'%\' or b.name like \'%'.$this->_db->escape($search).'%\'';
		}
		
		$condition 	= count( $where ) ? ' where ('.implode( ') and (', $where ).')' : '';
		$orderby    = 'order by '.$filter_order.' '.$filter_order_Dir;
		
		if (empty($this->_pagination)) {
				
			$query = 'select count(*) from '.T_CJBLOG_BADGES.' '.$condition;
			$this->_db->setQuery($query);
			$total = $this->_db->loadResult();
				
			jimport('joomla.html.pagination');
			$result->pagination = new JPagination($total, $limitstart, $limit);
		}
		
		$query = 'select b.id, b.title, b.description, b.published, b.icon, b.css_class from '.T_CJBLOG_BADGES.' b '.$condition;
		$this->_db->setQuery($query, $limitstart, $limit);
		
		$result->badges = $this->_db->loadObjectList();
		$result->state = array('limit'=>$limit, 'limitstart'=>$limitstart, 'list.ordering'=>$filter_order, 'list.direction'=>$filter_order_Dir, 'filter.search'=>$search);
		
		return $result;
	}

	function import_rules(){
	
		$app = JFactory::getApplication();
		$file = $app->input->files->get('rule-file', array(), 'array');
	
		if ($file['error'] > 0){
	
			$this->setError('File/File Size Error. File Error='.$file['error'].'| File Size='.$file['size']);
	
			return false;
		}
	
		//check the file extension is ok
		if (JFile::getExt($file['name']) != 'xml'){
	
			$this->setError('Invalid file extension.');
			return false;
		}
		
		$plugin = simplexml_load_file($file['tmp_name']);
		if(empty($plugin) || empty($plugin['name']) || $plugin['type'] != 'cjblog_badges') return false;
		
		$filename = $plugin['name'].'.xml';
		
		if(file_exists($filename)) return false;

		if(JFile::upload($file['tmp_name'], CJBLOG_PLUGINS_BASE_DIR.'badges/'.$filename)){
			
			return true;
		}
	
		return false;
	}
	
	function get_components(){
		
		$query = '
			select 
				extension_id, name, element 
			from 
				#__extensions 
			where 
				'.$this->_db->quoteName('type').' = '.$this->_db->quote('component').' and client_id = 1 and enabled = 1
			order by
				element';
				
		$this->_db->setQuery($query);
		$components = $this->_db->loadObjectList();
		
		return $components;
	}
	
	function get_badge($id){
		
		$query = 'select id, title, alias, description, icon, css_class, published from '.T_CJBLOG_BADGES.' where id = '.$id;
		$this->_db->setQuery($query);
		$badge = $this->_db->loadObject();
		
		if($this->_db->getErrorNum()){
			
			JLog::add('BadgesModel.get_badge - DB Error: '.$this->_db->getErrorMsg(), JLog::ERROR, CJBLOG);
		}
		
		return $badge;
	}
	
	function get_all_badges(){
		
		$query = 'select id, title, description from '.T_CJBLOG_BADGES;
		$this->_db->setQuery($query);
		$badges = $this->_db->loadObjectList();
		
		return $badges;
	}
	
	function save_badge(){
		
		$app = JFactory::getApplication();
		$badge = new stdClass();
		
		$badge->id = $app->input->post->getInt('badge_id', 0);
		$badge->title = $app->input->post->getString('badge_title', null);
		$badge->alias = $app->input->post->getString('badge_alias', null);
		$badge->description = $app->input->post->getString('badge_description', null);
		$badge->icon = $app->input->post->getString('badge_icon', null);
		$badge->css_class = $app->input->post->getString('badge_classname', null);
		$badge->published = $app->input->post->getInt('badge_state', 0);
		
		if(empty($badge->alias)){
			
			$badge->alias = JFilterOutput::stringURLUnicodeSlug($badge->title);
		}
		
		if(empty($badge->title) || empty($badge->description) || (empty($badge->icon) && empty($badge->css_class))){
			
			$app->enqueueMessage(JText::_('COM_CJBLOG_MISSING_REQUIRED_FIELDS'), 'error');
			return $badge;
		}
		
		$icon = $badge->icon ? $this->_db->quote($badge->icon) : 'null';
		$classname = $badge->css_class ? $this->_db->quote($badge->css_class) : 'null';
		$query = '';
		
		if($badge->id == 0){
		
			$query = '
				insert into 
					'.T_CJBLOG_BADGES.'(title, alias, description, icon, css_class, published)
				values (
					'.$this->_db->quote($badge->title).',
					'.$this->_db->quote($badge->alias).',
					'.$this->_db->quote($badge->description).',
					'.$icon.',
					'.$classname.',
					'.$badge->published.')';
		} else {
			
			$query = '
				update
					'.T_CJBLOG_BADGES.'
				set
					title = '.$this->_db->quote($badge->title).',
					description = '.$this->_db->quote($badge->description).',
					icon = '.$icon.',
					css_class = '.$classname.',
					published = '.$badge->published.'
				where
					id = '.$badge->id; 
		}
		
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			return true;
		}
		
		return $badge;
	}
	
	function publish(array $ids, $status){
		
		$query = 'update '.T_CJBLOG_BADGES.' set published = '.$status.' where id in ('.implode(',', $ids).')';
		$this->_db->setQuery($query);
			
		if($this->_db->query()){
			
			return true;
		}
		
		return false;
	}
	
	function delete(array $ids){
		
		$query = 'delete from '.T_CJBLOG_USER_BADGE_MAP.' where badge_id in ('.implode(',', $ids).')';
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			$query = 'delete from '.T_CJBLOG_BADGES.' where id in ('.implode(',', $ids).')';
			$this->_db->setQuery($query);
			
			if($this->_db->query()){
					
				return true;
			}
		}
		
		return false;
	}
	
	function publish_rules(array $ids, $status){
	
		$query = 'update '.T_CJBLOG_BADGE_RULES.' set published = '.$status.' where id in ('.implode(',', $ids).')';
		$this->_db->setQuery($query);
			
		if($this->_db->query()){
				
			return true;
		}
	
		return false;
	}
	
	function delete_rules(array $ids){
	
		$query = 'delete from '.T_CJBLOG_BADGE_RULES.' where id in ('.implode(',', $ids).')';
		$this->_db->setQuery($query);
	
		if($this->_db->query()){
			
			return true;
		}
	
		return false;
	}
	
	function get_badge_rules(){
		
		$app = JFactory::getApplication();
		$result = new stdClass();
		$where = array();
		
		$filter_order       = $app->getUserStateFromRequest( "cjblogbadgerules.filter_order",'filter_order','b.id',	'cmd' );
		$filter_order_Dir   = $app->getUserStateFromRequest( "cjblogbadgerules.filter_order_Dir",'filter_order_Dir','DESC','word' );
		$search             = $app->getUserStateFromRequest( "cjblogbadgerules.search",'filter_search','','string' );
		$limitstart         = $app->getUserStateFromRequest( "cjblogbadgerules.limitstart",'limitstart','','int' );
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		if ($search) {
		
			$where[] = 'b.asset_name like \'%'.$this->_db->escape($search).'%\' or b.rule_name like \'%'.$this->_db->escape($search).'%\'';
		}
		
		$condition 	= count( $where ) ? ' where ('.implode( ') and (', $where ).')' : '';
		$orderby    = 'order by '.$filter_order.' '.$filter_order_Dir;
		
		if (empty($this->_pagination)) {
		
			$query = 'select count(*) from '.T_CJBLOG_BADGE_RULES.' '.$condition;
			$this->_db->setQuery($query);
			$total = $this->_db->loadResult();
		
			jimport('joomla.html.pagination');
			$result->pagination = new JPagination($total, $limitstart, $limit);
		}
		
		$query = '
			select 
				b.id, b.title, b.description, b.published, b.asset_name, b.asset_title, b.rule_name, b.rule_content, b.badge_id, b.access,
				a.title as badge_title, a.css_class,
				l.title as viewlevel
			from 
				'.T_CJBLOG_BADGE_RULES.' b 
			left join
				'.T_CJBLOG_BADGES.' a on b.badge_id = a.id
			left join
				#__viewlevels l on b.access = l.id
			'.$condition;
		
		$this->_db->setQuery($query, $limitstart, $limit);
		
		$result->rules = $this->_db->loadObjectList();
		$result->state = array('limit'=>$limit, 'limitstart'=>$limitstart, 'list.ordering'=>$filter_order, 'list.direction'=>$filter_order_Dir, 'filter.search'=>$search);
		
		return $result;
	}
	
	function get_rule_types(){
		
		$path = CJBLOG_PLUGINS_BASE_DIR.'badges'.DS;
		$files = JFolder::files($path, 'xml');
		$types = array();
		
		if(empty($files)) return false;
		
		foreach($files as $file){
			
			$plugin = simplexml_load_file($path.$file);
			
			if($plugin['type'] != 'cjblog_badges') continue;
			
			$type = new stdClass();
			
			$type->asset_name = $plugin['name'];
			$type->title = JText::_($plugin['title']);
			$type->rule_types = array();
			
			foreach($plugin->extension_rule as $extension_rule){
				
				$rtype = new stdClass();

				$rtype->name = $extension_rule->rule_name;
				$rtype->description = $extension_rule->rule_description;
				
				$type->rule_types[] = $rtype;
			}
			
			$types[] = $type;
		}
		
		return $types;
	}
	
	function get_rule_type($id, $asset_name, $name){

		$type = new stdClass();
		$type->id = 0;
		$type->rule_name = $name;
		$type->asset_name = $asset_name;
		$type->title = '';
		$type->badge_id = 0;
		$type->published = 0;
		$type->description = null;
		
		$db_record = null;
		
		if($id > 0){
				
			$query = '
					select 
						id, title, badge_id, rule_name, asset_name, rule_content, description, published, access
					from 
						'.T_CJBLOG_BADGE_RULES.' 
					where 
						id = '.$id;
			
			$this->_db->setQuery($query);
		
			$db_record = $this->_db->loadObject();

			$type->id = $db_record->id;
			$type->title = $db_record->title;
			$type->badge_id = $db_record->badge_id;
			$type->asset_name = $db_record->asset_name;
			$type->rule_name = $db_record->rule_name;
			$type->published = $db_record->published;
			$type->description = $db_record->description;
			$type->access = $db_record->access;
		}
		
		$path = JPATH_ROOT.'/media/cjblog/plugins/badges/'.$type->asset_name.'.xml';
		if(!JFile::exists($path)) return false;
		
		$plugin = simplexml_load_file($path);
		if(empty($plugin) || empty($plugin['name']) || ($plugin['name'] != $type->asset_name) || empty($plugin->extension_rule)) return false;
		
		foreach ($plugin->extension_rule as $extension_rule){
			
			if($extension_rule->rule_name == $type->rule_name){
				
				$rule_nodes = $extension_rule->rules->children();
				$rules = array();
				
				if(empty($rule_nodes)) return false;
				
				foreach($rule_nodes as $rule_node){
					
					$rule = new stdClass();
					
					$rule->label = $rule_node['label'];
					$rule->name = $rule_node['name'];
					$rule->type = $rule_node['type'];
					$rule->dataType = $rule_node['dataType'];
					$rule->compare = $rule_node['compare'];
					
					if($rule->type == 'list' || $rule->type == 'checkbox'){
						
						$rule->options = $rule_node->children();
						$rule->value = array();
					} else {
						
						$rule->value = '';
					}
					
					$rules[] = $rule;
				}
				
				if(empty($rules)) return false;

				$type->rule_content = new stdClass();
				$type->rule_content->join = $extension_rule->rules['join'];
				$type->rule_content->method = $extension_rule->rules['method'];
				$type->access = empty($type->access) ? $extension_rule->access_level : $type->access;
				$type->rule_content->rules = $rules;
				$type->description = empty($type->description) ? $extension_rule->rule_description : $type->description;
				
				break;
			}
		}
		
		if(!empty($db_record->rule_content)){
		
			$rule_values = json_decode($db_record->rule_content);
		
			foreach ($rule_values->rules as $rule_value){
		
				foreach ($type->rule_content->rules as &$rule){
					
					if($rule->name == $rule_value->name){
						
						$rule->value = $rule_value->value;
					}
				}
			}
		}
		
		$badges = $this->get_all_badges();
		$type->badges = !empty($badges) ? $badges : array();
			
		return $type;
	}
	
	function save_rule(){
		
		$app = JFactory::getApplication();
		
		$id = $app->input->post->getInt('id', 0);
		$asset_name = $app->input->post->getCmd('asset_name', null);
		$rule_name = $app->input->post->getCmd('rule_name', null);
		$title = $app->input->post->getString('rule_title', null);
		$description = $app->input->post->getString('rule_description', null);
		$badge_id = $app->input->post->getInt('badge_id', 0);
		$published = $app->input->post->getInt('rule_status', 0);
		$access = $app->input->post->getInt('access', 1);
		
		if(empty($title) || empty($rule_name) || empty($asset_name) || empty($description) || $badge_id <= 0) {
			
			$app->enqueueMessage('Required fields missing. Try again');
			return false;
		}
		
		$path = JPATH_ROOT.'/media/cjblog/plugins/badges/'.$asset_name.'.xml';
		if(!JFile::exists($path)) {
			
			$app->enqueueMessage('Missing badge rules file.');
			return false;
		}
		
		$plugin = simplexml_load_file($path);
		if(
				empty($plugin) || 
				empty($plugin['name']) ||
				empty($plugin['title']) || 
				($plugin['name'] != $asset_name) || 
				empty($plugin->extension_rule)) {
			
			$app->enqueueMessage('Invalid badge rules file found.');
			return false;
		}
		
		$rule_values = new stdClass();
		$asset_title = $plugin['title'];
		
		foreach ($plugin->extension_rule as $extension_rule){
				
			if($extension_rule->rule_name == $rule_name){
				
				$rule_nodes = $extension_rule->rules->children();
				$rules = array();
				
				if(empty($rule_nodes)) return false;
				
				foreach($rule_nodes as $rule_node){

					$rule = new stdClass();
					$rule->name = (string)$rule_node['name'];
					$rule->compare = (string)$rule_node['compare'];
					$rule->dataType = (string)$rule_node['dataType'];
					
					if($rule_node['type'] == 'checkbox'){
						
						$rule->value = $app->input->post->get($rule->name, null, 'array');
						
						if($rule_node['dataType'] == 'int'){
							
							JArrayHelper::toInteger($rule->value);
						}
					} else {
						
						$rule->value = $app->input->post->get($rule->name, null, 'string');
					}
					
					if(empty($rule->value)) {
						
						$app->enqueueMessage('Rule value not entered, please try again.');
						return false;
					}
					
					$rules[] = $rule;
				}
				
				if(empty($rules)) {
					
					$app->enqueueMessage('No matched rules found in badge rules page with request values.');
					return false;
				}
				
				$rule_values->join = (string)$extension_rule->rules['join'];
				$rule_values->method = (string)$extension_rule->rules['method'];
				$rule_values->rules = $rules;
				$rule_values->multiple = (int)$extension_rule->rules['multiple'];;
				
				break;
			}
		}
		
		if(empty($rule_values)) {
			
			$app->enqueueMessage('No rules to save');
			return false;
		}
		
		$rule_content = json_encode($rule_values);
		
		if($id > 0){
			
			$query = '
				update
					'.T_CJBLOG_BADGE_RULES.'
				set
					badge_id = '.$badge_id.',
					title = '.$this->_db->quote($title).',
					description = '.$this->_db->quote($description).',
					published = '.$published.',
					rule_content = '.$this->_db->quote($rule_content).',
					access = '.$access.'
				where
					id = '.$id.' and rule_name = '.$this->_db->quote($rule_name).' and asset_name = '.$this->_db->quote($asset_name);
			
			$this->_db->setQuery($query);
			
			if($this->_db->query()){
				
				return true;
			}
		} else {
			
			$query = '
				insert into
					'.T_CJBLOG_BADGE_RULES.' (badge_id, title, description, asset_name, asset_title, rule_name, rule_content, published, access)
				values
					('.
					$badge_id.','.
					$this->_db->quote($title).','.
					$this->_db->quote($description).','.
					$this->_db->quote($asset_name).','.
					$this->_db->quote($asset_title).','.
					$this->_db->quote($rule_name).','.
					$this->_db->quote($rule_content).','.
					$published.','.
					$access.
					')';
			
			$this->_db->setQuery($query);
			
			if($this->_db->query()){
				
				return true;
			}
		}
		
		$app->enqueueMessage($this->_db->getErrorMsg());
		
		return false;
	}
	
	public function get_badge_activity(){

		$app = JFactory::getApplication();
		$result = new stdClass();
		$where = array();
		
		$filter_order       = $app->getUserStateFromRequest( "cjblogbadgeactivity.filter_order",'filter_order','a.date_assigned', 'cmd' );
		$filter_order_Dir   = $app->getUserStateFromRequest( "cjblogbadgeactivity.filter_order_Dir",'filter_order_Dir','desc','word' );
		$search             = $app->getUserStateFromRequest( "cjblogbadgeactivity.search",'filter_search','','string' );
		$limitstart         = $app->getUserStateFromRequest( "cjblogbadgeactivity.limitstart",'limitstart','','int' );
		$userid				= $app->input->getInt('userid', 0);
		$ruleid				= $app->input->getInt('ruleid', 0);
		
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
		
			$query = 'select count(*) from '.T_CJBLOG_USER_BADGE_MAP.' a left join #__users u on a.user_id = u.id '.$condition;
			$this->_db->setQuery($query);
			$total = $this->_db->loadResult();
		
			jimport('joomla.html.pagination');
			$result->pagination = new JPagination($total, $limitstart, $limit);
		}
		
		$query = '
				select
					a.date_assigned, a.ref_id, a.badge_id, a.rule_id,
					u.id as user_id, u.name, u.username,
					b.title as badge_title, r.description as badge_description, r.published, r.asset_title, r.asset_name,
					b.css_class, b.icon
				from
					'.T_CJBLOG_USER_BADGE_MAP.' a
				left join
					'.T_CJBLOG_BADGE_RULES.' r on a.rule_id = r.id
				left join
					'.T_CJBLOG_BADGES.' b on a.badge_id = b.id
				left join
					#__users u on a.user_id = u.id
				'
				.$condition
				.$orderby;
		
		$this->_db->setQuery($query, $limitstart, $limit);
		$result->activity = $this->_db->loadObjectList();
		
		$query = 'select id, title, asset_title from '.T_CJBLOG_BADGE_RULES;
		$this->_db->setQuery($query);
		$result->rules = $this->_db->loadObjectList();
		
		$query = '
				select 
					distinct a.user_id as id, u.name, u.username 
				from 
					'.T_CJBLOG_USER_BADGE_MAP.' a
				left join
					#__users u on a.user_id = u.id';
		
		$this->_db->setQuery($query);
		$result->users = $this->_db->loadObjectList();
		
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
	
	public function delete_activity($ids){
		
		$values = array();
		$userids = array();
		
		foreach($ids as $id){
			
			$explode = explode(',', $id);
			
			if(count($explode) == 3){
			
				$values[] = $this->_db->quote(intval($explode[0]).','.intval($explode[1]).','.intval($explode[2]));
				$userids[] = intval($explode[0]);
			}
		}
		
		if(!empty($values)){
			
			$query = "delete from ".T_CJBLOG_USER_BADGE_MAP." where concat(user_id,',',badge_id,',',rule_id) in (".implode(',', $values).')';
			$this->_db->setQuery($query);
			
			if($this->_db->query()){
				
				$query = '
						update 
							'.T_CJBLOG_USERS.' u 
						set 
							u.num_badges = (select count(*) from '.T_CJBLOG_USER_BADGE_MAP.' m where m.user_id = u.id)
						where 
							u.id in ('.implode(',', $userids).')';
				
				$this->_db->setQuery($query);
				$this->_db->query();
				
				return true;
			}
		}
		
		return false;
	}
}