<?php
/**
 * @version		$Id: badges.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CJBlog
 * @subpackage	Components.site
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
	
	public function get_badges(){
		
		$query = '
				select
					a.id, a.asset_name, a.asset_title, a.description, a.published, a.num_assigned,
					b.id as badge_id, b.title, b.alias, b.icon, b.css_class
				from
					'.T_CJBLOG_BADGE_RULES.' a
				left join
					'.T_CJBLOG_BADGES.' b on a.badge_id = b.id
				order by
					a.asset_title asc';
		
		$this->_db->setQuery($query);
		$list = $this->_db->loadObjectList();
		
		if(empty($list)) return false;
		
		$badges = array();
		
		foreach($list as $badge){
			
			if(empty($badges[$badge->asset_name])) {
				
				$badges[$badge->asset_name] = array('title'=>$badge->asset_title, 'badges'=>array());
			}
			
			$badges[$badge->asset_name]['badges'][] = $badge;
		}
		
		return $badges;
	}
	
	function get_user_badges($id){
		
		$query = '
				select
					count(*) as num_assigned,
					a.id, a.asset_name, a.asset_title, a.description, a.published,
					b.id as badge_id, b.title, b.alias, b.icon, b.css_class
				from
					'.T_CJBLOG_USER_BADGE_MAP.' m
				left join
					'.T_CJBLOG_BADGE_RULES.' a on m.rule_id = a.id
				left join
					'.T_CJBLOG_BADGES.' b on a.badge_id = b.id
				where
					m.user_id = '.$id.' and a.published = 1
				group by
					m.badge_id, m.rule_id
				order by
					a.asset_title asc';
		
		$this->_db->setQuery($query);
		$list = $this->_db->loadObjectList();
		
		if(empty($list)) return false;
		
		$badges = array();
		
		foreach($list as $badge){
			
			if(empty($badges[$badge->asset_name])) {
				
				$badges[$badge->asset_name] = array('title'=>$badge->asset_title, 'badges'=>array());
			}
			
			$badges[$badge->asset_name]['badges'][] = $badge;
		}
		
		return $badges;
	}
}
?>

