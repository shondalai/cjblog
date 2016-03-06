<?php
/**
 * @version		$Id: articles.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CJBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');

class CjBlogModelArticles extends JModelLegacy {

	protected $_item;
	
	function __construct() {

		parent::__construct ();
	}
	
	function get_articles($options = array()){
		
		$params = JComponentHelper::getParams(CJBLOG);
		
		$exclude_users = isset($options['exclude_users']) ? $options['exclude_users'] : array();
		$pagination = isset($options['pagination']) ? $options['pagination'] : true;
		$favorites = isset($options['favorites']) ? $options['favorites'] : false;
		$limitstart = isset($options['limitstart']) ? $options['limitstart'] : 0;
		$limit = (isset($options['limit']) && $options['limit'] > 0) ? $options['limit'] : 20;
		$catid = isset($options['catid']) ? $options['catid'] : 0;
		$user_id = isset($options['user_id']) ? $options['user_id'] : 0;
		$order = isset($options['order']) ? $options['order'] : 'a.created';
		$order_dir = isset($options['order_dir']) ? $options['order_dir'] : 'desc';
		$search = isset($options['search']) ? $options['search'] : null;
		$day_limit = isset($options['day_limit']) ? $options['day_limit'] : 0;
		$max_cat_levels = isset($options['max_cat_levels']) ? $options['max_cat_levels'] : 0; 
		$exclude_categories = $params->get('exclude_categories', array());
		$tag_id = isset($options['tag_id']) ? $options['tag_id'] : 0;
		
		$app = JFactory::getApplication();
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		$lang = JFactory::getLanguage();
		
		$wheres = array();
		$return = new stdClass();
		$null_date = $this->_db->getNullDate();
		$now = $date->toSql();
		
		if($user_id > 0){
			
			$wheres[] = 'a.created_by = '.$user_id;
		}
		
		if($tag_id > 0){
			
			$wheres[] = 'a.id in (select item_id from #__cjblog_tagmap where tag_id = '.$options['tag_id'].')';
		}
		
		if($favorites){
			
			$wheres[] = 'a.id in (select content_id from '.T_CJBLOG_FAVORITES.' where user_id = '.$user->id.')';
		}
		
		if($catid > 0){
			
			$max_cat_levels_query = '
					select 
						sub.id 
					from 
						#__categories sub 
					inner join 
						#__categories this on sub.lft > this.lft AND sub.rgt < this.rgt 
					where 
						this.id = '.$catid;
			
			if($max_cat_levels >= 0){
				
				$max_cat_levels_query = $max_cat_levels_query.' and sub.level <= this.level + '.$max_cat_levels;
			}

			$wheres[] = 'a.catid = '.$catid.' or a.catid in ('.$max_cat_levels_query.')';
		}  
		
		if(!empty($exclude_categories)){
			
			$wheres[] = 'a.catid not in (
					select 
						distinct(c1.id) 
					from 
						#__categories c1, #__categories c2 
					where 
						c1.lft between c2.lft and c2.rgt and c2.id in ('.implode(',', $exclude_categories).'))';
		}
		
		if($day_limit > 0){
			
			$wheres[] = 'a.created >= DATE_SUB(CURRENT_DATE, INTERVAL '.$day_limit.' DAY)';
		}
		
		if(!empty($exclude_users)){
			
			$wheres[] = 'a.created_by not in ('.implode(',', $exclude_users).')';
		}
		
		if(!empty($search)){
			
			$wheres[] = '
				a.title like \'%'.$this->_db->escape($search).'%\' or 
				u.name like \'%'.$this->_db->escape($search).'%\' or 
				u.username like \'%'.$this->_db->escape($search).'%\'';
		}
		
		$wheres[] = 'a.state = 1';
		$wheres[] = 'a.publish_up = '.$this->_db->Quote($null_date).' OR a.publish_up <= '.$this->_db->Quote($now);
		$wheres[] = 'a.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')';
		
		if ($app->getLanguageFilter()) {
		
			$wheres[] = 'a.language in (' . $this->_db->Quote( $lang->getTag() ) . ',' . $this->_db->Quote('*') . ')';
		}
		
		$year = $app->input->getInt('year');
		$month = $app->input->getInt('month');
		if($year)
		{
			$wheres[] = 'year(a.created) = '.$year;
			if($month)
			{
				$wheres[]  = 'month(a.created) = '.$month;
			}
		}
		
		
		$where = '('.implode(') and (', $wheres).')';
		
		if($pagination){
			
			$query = '
				select 
					count(*) 
				from
					#__content a '.(!empty($search) ? '
				left join
					#__users u on a.created_by = u.id' : '').'
				where 
					'.$where;
			
			$this->_db->setQuery($query);
			$total = $this->_db->loadResult();

			jimport('joomla.html.pagination');
			$return->pagination = new JPagination($total, $limitstart, $limit);
		}
		
		$query = '
			select
				a.id, a.title, a.alias, a.catid, a.created_by, a.created, a.checked_out, a.checked_out_time, a.hits, a.introtext, a.fulltext, a.images,
				c.title as category_title, c.alias as category_alias, a.language, a.featured, a.modified, a.publish_up,
				u.'.$this->_db->quoteName($params->get('user_display_name', 'name')).' as display_name, u.username, u.name as author, u.email as author_email
			from 
				#__content a 
			left join
				#__categories c on a.catid = c.id
			left join
				#__users u on a.created_by = u.id
			where 
				'.$where.'
			order by
				'.$order.' '.$order_dir;
		
		$this->_db->setQuery($query, $limitstart, $limit);
		$articles = $this->_db->loadObjectList('id');

		if(!empty($articles)){
				
			$ids = array();
				
			foreach($articles as &$article){
		
				$ids[] = $article->id;
				$article->tags = array();

				// Get display date
				switch ($params->get('list_show_date', 1) == 1)
				{
					case 'modified':
						$article->displayDate = $article->modified;
						break;
				
					case 'published':
						$article->displayDate = ($article->publish_up == 0) ? $article->created : $article->publish_up;
						break;
				
					case 'created':
						$article->displayDate = $article->created;
						break;
				}
			}
				
			$tags = $this->get_tags_by_itemids($ids);
				
			if(!empty($tags)){
		
				foreach($tags as $tag){
						
					if(array_key_exists($tag->item_id, $articles)){
		
						$articles[$tag->item_id]->tags[] = $tag;
					}
				}
			}
		}
		
		$return->articles = $articles;
		$return->lists = array('limitstart'=>$limitstart, 'limit'=>$limit, 'search'=>$search);
		
		return $return;
	}
	
	function get_excluded_categories($excludes){
		
		$query = '
				select 
					distinct(c1.id)
				from
					#__categories c1, #__categories c2
				where 
					c1.lft between c2.lft and c2.rgt and c2.id in ('.implode(',', $excludes).')';
		
		$this->_db->setQuery($query);
		
		return $this->_db->loadColumn();
	}
	
	function add_to_favorites($id, $userid){
		
		$created = JFactory::getDate()->toSql();
		
		$query = '
			insert into 
				'.T_CJBLOG_FAVORITES.'(content_id, user_id, created)
			values 
				('.$id.','.$userid.','.$this->_db->quote($created).')';
		
		$this->_db->setQuery($query);
		
		if($this->_db->query() && $this->_db->getAffectedRows() > 0){
			
			$query = '
				insert into
					'.T_CJBLOG_CONTENT.'(id, favorites)
				values 
					('.$id.', 1)
				on duplicate key
					update favorites = favorites + 1';
			
			$this->_db->setQuery($query);
			$this->_db->query();
		}

		$query = 'select favorites from '.T_CJBLOG_CONTENT.' where id = '.$id;
		$this->_db->setQuery($query);
			
		return $this->_db->loadResult();
	}
	
	function remove_favorite($id, $userid){
		
		$query = 'delete from '.T_CJBLOG_FAVORITES.' where content_id = '.$id.' and user_id = '.$userid;
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			$query = 'update '.T_CJBLOG_CONTENT.' set favorites = favorites - 1 where id = '.$id.' and favorites > 0';
			$this->_db->setQuery($query);
			
			if($this->_db->query()){
				
				$query = 'select favorites from '.T_CJBLOG_CONTENT.' where id = '.$id;
				$this->_db->setQuery($query);
					
				return $this->_db->loadResult();
			}
		}
		
		return 0;
	}
	
	function get_article($id = null){
		
		$query = '
			select
				a.id, a.title, a.alias, a.introtext, a.introtext as articletext, a.fulltext, 
				a.catid, a.publish_up, a.publish_down, a.metakey, a.metadesc, a.language, a.state
			from
				#__content a
			where 
				a.id = '.$id;
		
		$this->_db->setQuery($query);
		$article = $this->_db->loadObject();
		
		if(!empty($article->fulltext)){
			
			$article->articletext .= '<hr id="system-readmore" />'.$article->fulltext;
		}
		
		return $article;
	}
	
	public function check_out($id){
		
		$user = JFactory::getUser();
		$created = JFactory::getDate();
		
		$query = '
			update 
				#__content 
			set 
				checked_out = '.$user->id.', 
				checked_out_time = '.$this->_db->quote($created).' 
			where 
				id = '.$id.' and (checked_out = 0 or checked_out = '.$user->id.')';
		
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			$count = $this->_db->getAffectedRows();
			
			return $count > 0;
		}
		
		return false;
	}
	
	public function publish_article($id, $state){
		
		$user = JFactory::getUser();
		$article = $this->get_article($id);
		
		if($this->can_edit_state($article, $user)){
			
			$query = 'update #__content set state = '.$state.' where id = '.$id.' and (checked_out = 0 OR checked_out = '.$user->id.')';
			$this->_db->setQuery($query);
			
			if($this->_db->query()){
				
				$query = 'update '.T_CJBLOG_USERS.' u set num_articles = (select count(*) from #__content c where c.created_by = u.id and state = 1 group by c.created_by)';
				$this->_db->setQuery($query);
				$this->_db->query();
				
				return true;
			}
		}
		
		return false;
	}
	
	protected function can_edit_state($record, $user){
		
		// Check for existing article.
		if (!empty($record->id)) {
			return $user->authorise('core.edit.state', 'com_content.article.'.(int) $record->id);
		}
		// New article, so check against the category.
		elseif (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_content.category.'.(int) $record->catid);
		}
		
		return false;
	}
	
	public function search_tags($search){
		
		$query = "select id, tag_text, description from #__cjblog_tags where tag_text like '%".$this->_db->escape($search)."%'";
		$this->_db->setQuery($query);
		$tags = $this->_db->loadObjectList();
		
		return $tags;
	}
	
	public function get_tags($limitstart, $limit, $params, $keywords){
		
		$return = array();
		$wheres = array();
		
		if(!empty($keywords)){
			
			$wheres[] = 'a.tag_text like \'%'.$this->_db->escape($keywords).'%\'';
		}
		
		$where = !empty($wheres) ? ' where ('.implode(' ) and ( ', $wheres).')' : '';
		
		$query = '
				select 
					a.id, a.tag_text, a.alias, a.description,
					s.num_items
				from
					#__cjblog_tags as a
				left join
					#__cjblog_tags_stats as s on a.id = s.tag_id 
				'.$where;
		
		$this->_db->setQuery($query, $limitstart, $limit);
		$return['tags'] = $this->_db->loadObjectList();
		
		/************ pagination *****************/
		$query = 'select count(*) from #__cjblog_tags';
		
		jimport('joomla.html.pagination');
		$this->_db->setQuery($query);
		$total = $this->_db->loadResult();
		
		$return['pagination'] = new JPagination( $total, $limitstart, $limit );
		/************ pagination *****************/
		
		return $return;
	}
	
	public function get_tags_by_itemids($ids){

		$query = '
			select
				map.item_id,
				tag.id as tag_id, tag.tag_text, tag.alias, tag.description
			from
				#__cjblog_tagmap map
			left join
				#__cjblog_tags tag on tag.id = map.tag_id
			where
				map.item_id in ('.implode(',', $ids).')';
			
		$this->_db->setQuery($query);
		$tags = $this->_db->loadObjectList();
		
		return !empty($tags) ? $tags : array();
	}
	
	public function get_tag_details($tag_id){
		
		$query = 'select id, tag_text, alias, description from #__cjblog_tags where id = '.$tag_id;
		$this->_db->setQuery($query);
		
		return $this->_db->loadObject();
	}
	
	public function save_tag_details($tag){
		
		if(empty($alias)) $alias = JFilterOutput::stringURLUnicodeSlug($tag->title);
		
		$query = '
				update 
					#__cjblog_tags 
				set 
					tag_text = '.$this->_db->quote($tag->title).', 
					alias = '.$this->_db->quote($tag->alias).', 
					description = '.$this->_db->quote($tag->description).'
				where 
					id = '.$tag->id;
		
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			return true;
		}
		
		return false;
	}
	
	public function delete_tag($tagid){
		
		$query = 'delete from #__cjblog_tags where id = '.$tagid;
		$this->_db->setQuery($query);
		
		if($this->_db->query() && $this->_db->getAffectedRows() > 0){
			
			$query = 'delete from #__cjblog_tagmap where tag_id = '.$tagid;
			$this->_db->setQuery($query);
			$this->_db->query();
			
			$query = 'delete from #__cjblog_tags_stats where tag_id = '.$tagid;
			$this->_db->setQuery($query);
			$this->_db->query();
			
			return true;
		}
		
		return false;
	}
}
?>