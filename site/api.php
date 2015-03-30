<?php
/**
 * @version		$Id: cjblog.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('CJBLOG') or define('CJBLOG', 'com_cjblog');

require_once JPATH_ROOT.'/components/'.CJBLOG.'/helpers/constants.php';

/* *************************************************************** */
/* ************************** CJLIB Includes ********************* */
$cjlib = JPATH_ROOT.'/components/com_cjlib/framework.php';

if(!file_exists($cjlib)){

	die('CJLib (CoreJoomla API Library) component not found. Please download and install it to continue.');
}

require_once $cjlib;

CJLib::import('corejoomla.framework.core');
/* ************************** CJLIB Includes ********************* */

// Load Language
JFactory::getLanguage()->load(CJBLOG, JPATH_ROOT);

// Add logger
$date = JFactory::getDate()->format('Y.m.d');
JLog::addLogger(array('text_file' => CJBLOG.'.'.$date.'.log.php'), JLog::ALL, CJBLOG);
/* *************************************************************** */

class CjBlogApi 
{
	private static $_users = array();
	private static $_badge_rules = array();
	private static $_errors = array();
	private static $_enable_logging = false;
	
	/**
	 * Sets the debug logging enabled or disabled 
	 * 
	 * @param booleab $state sets the state of logging
	 */
	public static function set_logging($state = true){
		
		CjBlogApi::$_enable_logging = $state;
	}
	
	/**
	 * Gets the user profile/profiles of a given id or ids.
	 * 
	 * @param mixed $identifier id/ids of the user(s)
	 * @param boolean $force_reload tells if the profiles should be loaded forcibly or not
	 * 
	 * @return mixed single or array of user profile associative array.
	 */
	public static function get_user_profile($identifier, $force_reload = false){
		
		if(is_array($identifier)){
		
			$return = array();
			self::load_users($identifier, $force_reload);
			
			foreach ($identifier as $id){
			
				if(!empty(self::$_users[$id])){
					
					$return[$id] = self::$_users[$id];
				}
			}

			return $return;
		} elseif(is_numeric($identifier)){

			self::load_users(array($identifier), $force_reload);
			
			if(!empty(self::$_users[$identifier])){
				
				return self::$_users[$identifier];
			}
		}
		
		return false;
	}
	
	/**
	 * Function to get avatar of a single user or multiple users.
	 * 
	 * @param int $identifier user id or array of ids of whom the avatar(s) is/are being retrieved.
	 * @param int $size size of the avatar
	 * 
	 * @return mixed <br/> 
	 * 	- avatar image if <code>identifier</code> is numeric, <br/>
	 *  - associative array of avatars with userid as index of the array elements if <code>identifier</code> is array,<br/> 
	 *  - default avatar image otherwise.
	 */
	public static function get_user_avatar_image($identifiers, $size = 48, $force_reload = false){
		
		$size = ($size > 224 ? 256 : ($size > 160 ? 192 : ( $size > 128 ? 160 : ( $size > 96 ? 128 : ( $size > 64 ? 96 : ( $size > 48 ? 64 : ( $size > 32 ? 48 : ( $size > 23 ? 32 : 16 ) ) ) ) ) ) ) );
		$default_avatar = CJBLOG_MEDIA_URI.'images/'.$size.'-nophoto.jpg';
		$avatar_loc = CJBLOG_AVATAR_BASE_URI.'size-'.$size.'/';
		
		if(is_numeric($identifiers)){
			
			$profile = self::get_user_profile($identifiers, $force_reload);
			
			if($profile && !empty($profile['avatar'])){
				
				return $avatar_loc.$profile['avatar'];
			}
		} elseif (is_array($identifiers)){
			
			$return = array();
			$profiles = self::get_user_profile($identifiers, $force_reload);
			
			if(!empty($profiles)){
					
				foreach ($profiles as $userid=>$profile){

					if(!empty($profile['avatar'])){
					
						$return[$userid] = $avatar_loc.$profile['avatar'];
					}else{
						
						$return[$userid] = $default_avatar;
					}
				}
			} else {
					
				foreach ($identifiers as $userid){
			
					if($userid){
							
						$return[$userid] = $default_avatar;
					}
				}
			}
			
			return $return;
		}
		
		return $default_avatar;
	}
	
	/**
	 * Gets the user profile url of one or more user ids. 
	 *  - If <code>identifier</code> is numeric, a single profile url is returned, 
	 *  - if <code>identifier</code> is an array of integers, respective associative array of user profiles is returned with userid as index of the array elements,
	 *  - false otherwise. 
	 * @param mixed $identifiers numeric or array of numeric user ids
	 * @param string $username if <code>path_only</code> is set as false, this option tells if the link value should be user original name or username.
	 * @param boolean $path_only if set to true, uri of the profile is returned, otherwise html link of the user profile is returned.
	 * 
	 * @return mixed user profile or array of user profiles based on the arguments passed. 
	 */
	public static function get_user_profile_url($identifiers, $username = 'name', $path_only = false, $attribs = null, $xhtml = true, $ssl = null){
		
		require_once JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'router.php';
		
		$params = JComponentHelper::getParams(CJBLOG);
		$profile_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=profile');
		$profiles = self::get_user_profile($identifiers);
		$username = $params->get('user_display_name', $username);
		
		if(CjBlogApi::$_enable_logging){
			
			JLog::add('Get Profile Urls - Profiles Loaded: '.count($profiles), JLog::DEBUG, CJBLOG);
		}
		
		if(!empty($profiles)){
			
			if(is_numeric($identifiers)){
				
				if($path_only){
				
					return JRoute::_('index.php?option='.CJBLOG.'&view=profile&id='.$profiles['id'].':'.$profiles['username'].$profile_itemid, $xhtml, $ssl);
				} else {
					
					return JHtml::link(
							JRoute::_('index.php?option='.CJBLOG.'&view=profile&id='.$profiles['id'].':'.$profiles['username'].$profile_itemid, $xhtml, $ssl),
							CJFunctions::escape($profiles[$username]),
							$attribs
						);
				}
			} elseif(is_array($identifiers)) {
				
				if(!empty($profiles)){
					
					$return = array();
					
					if(in_array(0, $identifiers)){
						
						if(null == $attribs) $attribs = array();
						$attribs['onclick'] = 'return false';
						
						$return[0] = $path_only ? '#' : JText::_('COM_CJBLOG_GUEST');
					}
					
					foreach ($profiles as $profile){
						
						if($path_only){
						
							$return[$profile['id']] = JRoute::_('index.php?option='.CJBLOG.'&view=profile&id='.$profile['id'].':'.$profile['username'].$profile_itemid);
						} else {
								
							$return[$profile['id']] = JHtml::link(
									JRoute::_('index.php?option='.CJBLOG.'&view=profile&id='.$profile['id'].':'.$profile['username'].$profile_itemid),
									CJFunctions::escape($profile[$username]),
									$attribs
								);
						}
					}
					
					return $return;
				}
			}
		}
		
		return $path_only ? '#' : JText::_('COM_CJBLOG_GUEST');
	}
	
	/**
	 * Gets the user avatar linked with user profile.
	 * 
	 * @param mixed $userids single id of the user or array of user ids
	 * @param int $size height of the avatar
	 * @param string $username what name to display username or name?
	 * @param array $attribs An associative array of attributes to add to the link
	 */
	public static function get_user_avatar($userids, $size = 48, $username = 'name', array $attribs = array(), array $image_attribs = array()){
		
		if(!array_key_exists('height', $image_attribs)){
			
			$image_attribs['height'] = $size;
		}
		
		if(!is_array($userids)) $userids = intval($userids);
		
		if(is_numeric($userids)){
			
			$profile = self::get_user_profile($userids);
			$avatar_loc = self::get_user_avatar_image($userids, $size);

			$attribs['class'] = empty($attribs['class']) ? 'tooltip-hover' : $attribs['class'].' tooltip-hover';
			$attribs['title'] = empty($attribs['title']) ? $profile[$username] : $attribs['title'];
			$attribs['data-toggle'] = 'tooltip';

			$avatar_image = '<img src="'.$avatar_loc.'" alt="'.$attribs['title'].'" '.JArrayHelper::toString($image_attribs).'/>';
			$profile_url = self::get_user_profile_url($userids, $username, true);

			return JHtml::link($profile_url, $avatar_image, $attribs);
		} elseif(is_array($userids) && !empty($userids)){
			
			$avatar_images = self::get_user_avatar_image($userids, $size);
			$profile_urls = self::get_user_profile_url($userids, $username, true);
			$profiles = self::get_user_profile($userids);
			$return = array();
			
			foreach ($userids as $userid){
				
				if(!empty($avatar_images[$userid]) && !empty($profile_urls[$userid])){

					$attribs['class'] = empty($attribs['class']) ? 'tooltip-hover' : $attribs['class'].' tooltip-hover';
					$attribs['title'] = CJFunctions::escape($profiles[$userid][$username]);
					$attribs['data-toggle'] = 'tooltip';

					$avatar_loc = self::get_user_avatar_image($userids, $size);
					$avatar_image = '<img src="'.$avatar_loc[$userid].'" alt="'.$attribs['title'].'" '.JArrayHelper::toString($image_attribs).'/>';

					$return[$userid] = JHtml::link($profile_urls[$userid], $avatar_image, $attribs);
				}
			}
			
			return $return;
		}
		
		return false;
	}
	
	/**
	 * Prefetches user profiles to be used across the request life cycle
	 * 
	 * @param array $identifiers array of user ids to load
	 * @param boolean $force_reload indicates to load even if the user is already loaded
	 */
	public static function load_users(array $identifiers = array(), $force_reload = false)
	{
		$notfound = array();
		JArrayHelper::toInteger($identifiers);
		
		foreach ($identifiers as $userid)
		{
			if (!$force_reload && (!$userid || $userid != intval($userid))) 
			{
				unset($userid);
			} 
			elseif (empty(self::$_users[$userid]) && !in_array($userid, $notfound)) 
			{
				$notfound[] = $userid;
			}
		}
		
		if(!empty($notfound))
		{
			$db = JFactory::getDbo();
			$params = JComponentHelper::getParams('com_cjblog');
			$profileApp = $params->get('profile_component', 'cjblog');
			
			$query = $db->getQuery(true)
				->select('ju.id, u.avatar, u.points, u.num_articles, u.num_badges, u.country, u.profile_views')
				->select('ju.name, ju.username, ju.email, ju.block, ju.registerDate, ju.lastvisitDate, ju.params')
				->from('#__users ju')
				->join('left', '#__cjblog_users u on u.id = ju.id')
				->where('ju.id in ('.implode(',', $notfound).')');
			
			if($profileApp == 'easyprofile')
			{
				$query
					->select($db->qn($db->escape('e.' . $params->get('easyprofile_about', 'author_info'))).' AS about')
					->join('left', '#__jsn_users AS e on ju.id = e.id');
			}
			else
			{
				$query->select('u.about');
			}
			
			$db->setQuery ( $query );
			$users = $db->loadAssocList();
			
			if(!empty($users))
			{
				foreach ($users as $user)
				{
					self::$_users[$user['id']] = $user;
				}
				
				if(CjBlogApi::$_enable_logging)
				{
					JLog::add('Load Users - After Load - Successfully loaded: ', JLog::DEBUG, CJBLOG);
				}
				
				return;
			}
			
			if($db->getErrorNum())
			{
				JLog::add('Load Users - After Load - Somthing went wrong. DB Error: '.$db->getErrorMsg().$query, JLog::ERROR, CJBLOG);
			}
		}
	}
	
	/**
	 * Gets the fill url of the user avatar image.
	 * 
	 * @param string $avatar avatar image name
	 * @param int $size height of the image to load
	 */
	public static function resolve_avatar_location($avatar, $size){
		
		$size = ($size > 255 ? 256 : ($size > 191 ? 192 : ( $size > 159 ? 160 : ( $size > 127 ? 128 : ( $size > 95 ? 96 : ( $size > 63 ? 64 : ( $size > 47 ? 48 : ( $size > 31 ? 32 : 16 ) ) ) ) ) ) ) );
		
		return !empty($avatar) ? CJBLOG_AVATAR_BASE_URI.'size-'.$size.'/'.$avatar : CJBLOG_MEDIA_URI.'images/'.$size.'-nophoto.jpg';
	}
	
	public static function get_user_badges($userid, $limitstart = 0, $limit = 50){
		
		$where = ''; 
		
		if(is_array($userid)){
			
			JArrayHelper::toInteger($userid);
			$where = 'm.user_id in ('.implode(',', $userid).')';
		} else {
			
			$userid = intval($userid);
			$where = 'm.user_id = '.$userid;
		}
		
		$db = JFactory::getDbo();
		
		$query = '
			select
				count(*) as num_times,
				a.id, a.asset_name, a.asset_title, a.description, a.published,
				b.id as badge_id, b.title, b.alias, b.icon, b.css_class
			from
				'.T_CJBLOG_USER_BADGE_MAP.' m
			left join
				'.T_CJBLOG_BADGE_RULES.' a on a.id = m.rule_id
			left join
				'.T_CJBLOG_BADGES.' b on b.id = m.badge_id
			where
				a.published = 1 and '.$where.'
			group by
				m.badge_id, m.rule_id
			order by
				m.date_assigned desc';
		
		$db->setQuery($query, $limitstart, $limit);
		$badges = $db->loadAssocList();

		if($db->getErrorNum()){
		
			JLog::add('CjBlogApi.get_user_badges - DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
		}
		
		return $badges;
	}
	
	public static function get_badge_details($id){
		
		$db = JFactory::getDbo();
		
		$query = 'select id, title, alias, description, icon, css_class, published from '.T_CJBLOG_BADGES.' where id = '.$id;
		$db->setQuery($query);
		$badge = $db->loadAssoc();
		
		if($db->getErrorNum()){
			
			JLog::add('CjBlogApi.get_badge_details - DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
		}
		
		return $badge;
	}
	
	public static function get_badges_by_rule_name($rule_name){
	
		$db = JFactory::getDbo();
	
		$query = '
			select
				a.id, a.title, a.description, a.rule_name, b.icon, b.published, b.css_class
			from
				'.T_CJBLOG_BADGE_RULES.' a
			inner join
				'.T_CJBLOG_BADGES.' b on b.id = a.badge_id
			where
				a.rule_name = '.$db->quote($rule_name).' and
				a.published = 1
			order by
				a.title';
	
		$db->setQuery($query);
		$badges = $db->loadObjectList();

		if($db->getErrorNum()){
				
			JLog::add('CjBlogApi.get_badges_by_component_name - DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
		}
		
		return $badges;
	}
	
	public static function award_points($rule_name, $user_id=0, $points=0, $reference=null, $description=null, $conditional_rules = array()){
		
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$points = intval($points);
		
		if(CjBlogApi::$_enable_logging){
			
			JLog::add('CjBlogApi.award_points - Rule: '.$rule_name.'| UserID: '.$user_id, JLog::DEBUG, CJBLOG);
		}

		if(strlen($rule_name) < 3) return false;
		if(!$user_id && $user->guest) return false;
		
		$user_id = $user_id > 0 ? $user_id : $user->id;
		
		$query = '
			select 
				id, name, asset_name, description, points, published, auto_approve, access, conditional_rules
			from 
				'.T_CJBLOG_POINT_RULES.' 
			where 
				name='.$db->quote($rule_name);

		if($db->getErrorNum()){
		
			JLog::add('CjBlogApi.award_points - DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
		}
		
		$db->setQuery($query);
		$rule = $db->loadObject();
		$rule->conditional_rules = json_decode($rule->conditional_rules);
		
		if(!$rule || !$rule->id || ($rule->published != '1') || ($points == 0 && $rule->points == 0 && empty($rule->conditional_rules))) return false;
		if(!in_array($rule->access, JAccess::getAuthorisedViewLevels($user_id))) return false;
		
		if(!empty($rule->conditional_rules) && is_array($rule->conditional_rules))
		{
			$match_found = false;
			$gtFoundValue = $geFoundValue = $ltFoundValue = $leFoundValue = $foundValue = 0;
			foreach ($rule->conditional_rules as $condition)
			{
				if(isset($condition->criteria) && isset($conditional_rules[$condition->criteria]))
				{
					$paramValue = (int) $conditional_rules[$condition->criteria];
					$conditionValue = (int) $condition->value;
					
					switch ($condition->comparator)
					{
						case 'eq':
							if($paramValue == $conditionValue)
							{
								$points = (int) $condition->points;
								$foundValue = $conditionValue;
								$match_found = true;
							}
							break;
							
						case 'gt':
							if($paramValue > $conditionValue && $conditionValue > $gtFoundValue)
							{
								$points = (int) $condition->points;
								$gtFoundValue = $foundValue = $conditionValue;
								$match_found = true;
							}
							break;
						case 'ge':
							if($paramValue >= $conditionValue && $conditionValue >= $geFoundValue)
							{
								$points = (int) $condition->points;
								$geFoundValue = $foundValue = $conditionValue;
								$match_found = true;
							}
							break;
						case 'lt':
							if($paramValue < $conditionValue && $conditionValue < $ltFoundValue)
							{
								$points = (int) $condition->points;
								$ltFoundValue = $foundValue = $conditionValue;
								$match_found = true;
							}
							break;
						case 'le':
							if($paramValue <= $conditionValue && $conditionValue <= $leFoundValue)
							{
								$points = (int) $condition->points;
								$leFoundValue = $foundValue = $conditionValue;
								$match_found = true;
							}
							break;
					}
				}
			}

			if($match_found)
			{
				$reference = $reference.'.'.$foundValue;
			}
			else
			{
				return false;
			}
		}
		else if(!$points || $points == 0) 
		{
			$points = $rule->points;
		}
		
		if($reference){
			
			$query = '
				select 
					count(*) 
				from 
					'.T_CJBLOG_POINTS.' 
				where 
					user_id = '.$user_id.' and rule_id='.$rule->id.' and ref_id='.$db->quote($reference);
			
			$db->setQuery($query);
			$count = (int)$db->loadResult();

			if($db->getErrorNum()){
			
				JLog::add('CjBlogApi.award_points - DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
			}

			if($count > 0) return false;
		}
		
		$reference = !$reference ? 'null' : $db->quote($reference);
		$description = !$description ? 'null' : $db->quote(CJFunctions::clean_value($description, true));
		$createdate = JFactory::getDate()->toSql();
		$published = $rule->auto_approve == 1 ? 1 : 2;
		
		$query = '
			insert into 
				'.T_CJBLOG_POINTS.'(user_id, rule_id, points, ref_id, published, description, created_by, created)
			values 
				('.$user_id.','.$rule->id.','.$points.','.$reference.','.$published.','.$description.','.$user->id.','.$db->quote($createdate).')';
		
		$db->setQuery($query);
		
		if(!$db->query()){
			
			CjBlogApi::$_errors[] = 'Error: '.$db->getErrorMsg();

			if($db->getErrorNum()){
			
				JLog::add('CjBlogApi.award_points - DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
			}

			return false;
		}
		
		if($published == 1)
		{
			$query = 'update '.T_CJBLOG_USERS.' set points = points '.($points > 0 ? '+'.$points : '-'.abs($points)).' where id = '.$user_id;
			$db->setQuery($query);
			
			if(!$db->query())
			{
				CjBlogApi::$_errors[] = 'Error: '.$db->getErrorMsg();
			}
		}
		
		$params = JComponentHelper::getParams(CJBLOG);
		if($user_id && $user_id == $user->id && ($params->get('display_messages', 0) == 1))
		{
			$message = $published == 1 ? 'COM_CJBLOG_POINTS_ASSIGNED_FOR' : 'COM_CJBLOG_POINTS_ASSIGNED_FOR_PENDING';
			JFactory::getApplication()->enqueueMessage(JText::sprintf($message, $points, $description));
		}
		
		return true;
	}
	
	public static function get_user_badges_markup($user){
		
		return '
			<span class="label tooltip-hover" title="'.JText::_('LBL_ARTICLES').'">'.$user['num_articles'].'</span>
			<span class="label tooltip-hover" title="'.JText::_('LBL_REPUTATION').'">'.$user['points'].'</span>
			<span class="label label-info tooltip-hover" title="'.JText::_('LBL_BADGES').'"><i class="icon-star-empty icon-white"></i> '.$user['num_badges'].'</span>';
	}
	
	public static function assign_custom_badge($ruleId, $userId, $ref_id = 0)
	{
		$db = JFactory::getDbo();
		$created = JFactory::getDate()->toSql();
		
		try 
		{
			$query = $db->getQuery(true)
				->select('a.badge_id as id, b.title')
				->from('#__cjblog_badge_rules')
				->leftJoin('#__cjblog_badges b on a.badge_id = b.id')
				->where('id = '.$ruleId);
			
			$db->setQuery($query);
			$badge = (int) $db->loadObject();
			
			if($badge && $badge->id > 0)
			{
				$query = $db->getQuery(true)
					->insert('#__cjblog_user_badge_map')
					->columns('user_id, badge_id, rule_id, ref_id, date_assigned')
					->values($userId.','.$badge->id.','.$ruleId.','.$ref_id.','.$db->q($created));
				
				$db->setQuery($query);
				$db->execute();
				
				$query = $db->getQuery(true)
					->update('#__cjblog_users')
					->set('num_badges = (select count(*) from #__cjblog_user_badge_map where user_id = '.$userId.')')
					->where('id = '.$userId);
				
				$db->setQuery($query);
				$db->execute();
				
				$query = $db->getQuery(true)
					->update('#__cjblog_bage_rules')
					->set('num_assigned = num_assigned + 1')
					->where('id = '.$ruleId);
				
				$db->setQuery($query);
				$db->execute();
				
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_CJBLOG_MSG_YOU_EARNED_BADGE', $badge->title));
				
				if(CjBlogApi::$_enable_logging)
				{
					JLog::add('Custom Badge Rule - Badge assigned. User ID: '.$userId.'| Badge ID: '.$badge->id, JLog::DEBUG, CJBLOG);
				}
			}
		}
		catch (Exception $e)
		{
			JLog::add('Trigger Badge Rule - No Rules Loaded. DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
		}
			
		JLog::add('Trigger Badge Rule - After processing, something went wrong. DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
	}
	
	public static function trigger_badge_rule($name, array $params, $userid = 0){
		
		$db = JFactory::getDbo();
		if(!$userid) $userid = JFactory::getUser()->id; 
		
		if(CjBlogApi::$_enable_logging){
		
			JLog::add('Trigger Badge Rule - Before Start - Available Badge Rules: '.count(CjBlogApi::$_badge_rules), JLog::DEBUG, CJBLOG);
		}
		
		if(empty(CjBlogApi::$_badge_rules)){
			
			$query = '
				select 
					id, asset_name, rule_name, rule_content, badge_id, access
				from 
					'.T_CJBLOG_BADGE_RULES.' 
				where 
					badge_id in (select badge_id from '.T_CJBLOG_BADGES.' where published = 1) and published = 1';
			
			$db->setQuery($query);
			
			$rules = $db->loadObjectList();
			
			if(!empty($rules)){
				
				foreach ($rules as $rule){
					
					$content = json_decode($rule->rule_content);
					
					if(!empty($content)){
					
						CjBlogApi::$_badge_rules[$rule->rule_name][] = array(
							'id'=>$rule->id, 'asset'=>$rule->asset_name, 'content'=>$content, 'badge_id'=>$rule->badge_id, 'access'=>$rule->access);
					} 
				}
			} else {
				
				JLog::add('Trigger Badge Rule - No Rules Loaded. DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
			}
		}
		
		if(CjBlogApi::$_enable_logging){
		
			JLog::add('Trigger Badge Rule - Rules Loaded: '.count(CjBlogApi::$_badge_rules), JLog::DEBUG, CJBLOG);
		}
		
		if(!empty(CjBlogApi::$_badge_rules[$name])){
			
			foreach(CjBlogApi::$_badge_rules[$name] as $badge_rule){
				
				if(!in_array($badge_rule['access'], JAccess::getAuthorisedViewLevels($userid))) continue;

				$rule_content = $badge_rule['content'];
				
				if(!empty($rule_content->rules)){
					
					$validated = 0;
					
					if(CjBlogApi::$_enable_logging){
					
						JLog::add('Trigger Badge Rule - Before validation. Conditions: '.count($rule_content->rules), JLog::DEBUG, CJBLOG);
					}
					
					switch ($rule_content->join){
						
						case 'and':
							
							$validated = 1;
							
							foreach ($rule_content->rules as $rule){
								
								if(empty($params[$rule->name])) return -1;
								
								if(!CjBlogApi::validate_condition($rule->compare, $rule->dataType, $rule->value, $params[$rule->name])){
									
									$validated = 0;
									break;
								}
							}
							
							break;
							
						case 'or':
							
							foreach ($rule_content->rules as $rule){
									
								if(empty($params[$rule->name])) return false;
									
								if(CjBlogApi::validate_condition($rule->compare, $rule->dataType, $rule->value, $params[$rule->name])){
									
									$validated = 1;
									break;
								}
							}
							
							break;
					}
					
					if(CjBlogApi::$_enable_logging){
					
						JLog::add('Trigger Badge Rule - After validation. Status: '.$validated, JLog::DEBUG, CJBLOG);
					}
					
					if($validated == 0) continue;
					
					// validated, assign badge now.
					$where = '';
					$created = JFactory::getDate()->toSql();
					
					if($rule_content->multiple == 1){
						
						$ref_id = !empty($params['ref_id']) ? (int) $params['ref_id'] : 0;
						$where = ' and ref_id > 0 and ref_id='.$ref_id;
					} else {
						
						$ref_id = 0;
					}
					
					$query = '
						select 
							count(*) 
						from 
							'.T_CJBLOG_USER_BADGE_MAP.' 
						where 
							user_id = '.$userid.' and rule_id = '.$badge_rule['id'].' and badge_id = '.$badge_rule['badge_id'].$where;
					
					$db->setQuery($query);
					$count = $db->loadResult();
					
					if($count > 0){
						
						if(CjBlogApi::$_enable_logging){
						
							JLog::add('Trigger Badge Rule - Conflicting badge exists, returning.', JLog::DEBUG, CJBLOG);
						}
						
						continue;
					}
					
					$query = '
						insert into
							'.T_CJBLOG_USER_BADGE_MAP.'(user_id, badge_id, rule_id, ref_id, date_assigned)
						values 
							('.$userid.','.$badge_rule['badge_id'].','.$badge_rule['id'].','.$ref_id.','.$db->quote($created).')';
	
					$db->setQuery($query);
						
					if($db->query()){
						
						$query = 'update '.T_CJBLOG_USERS.' set num_badges = (select count(*) from '.T_CJBLOG_USER_BADGE_MAP.' where user_id = '.$userid.') where id = '.$userid;
						$db->setQuery($query);
						$db->query();
						
						$query = 'update '.T_CJBLOG_BADGE_RULES.' set num_assigned = num_assigned + 1 where id = '.$badge_rule['id'];
						$db->setQuery($query);
						$db->query();
						
						if(CjBlogApi::$_enable_logging){
						
							JLog::add('Trigger Badge Rule - Badge assigned. User ID: '.$userid.'| Badge ID: '.$badge_rule['badge_id'], JLog::DEBUG, CJBLOG);
						}
						
						continue;
					}
					
					JLog::add('Trigger Badge Rule - After processing, something went wrong. DB Error: '.$db->getErrorMsg(), JLog::ERROR, CJBLOG);
				}
			}
			
			return true;
		}
		
		if(CjBlogApi::$_enable_logging){
		
			JLog::add('Trigger Badge Rule - No rules found with the rule name - '.$name.' - to execute.', JLog::DEBUG, CJBLOG);
		}
		
		return false;
	}
	
	private static function validate_condition($type, $dataType, $compare, $value){
		
		if(CjBlogApi::$_enable_logging){
		
			JLog::add('Trigger Badge Rule - Validate Condition - Type: '.$type.'| Data Type: '.$dataType.'| Compare: '.$compare.'| To: '.$value, JLog::DEBUG, CJBLOG);
		}
		
		switch ($type){
		
			case 'eq':
				
				switch ($dataType){
					
					case 'int':
						
						return $compare == $value;
						
					case 'string':
						
						return strcmp($compare, $value) == 0;
						
					case 'date':
						
						return strtotime($compare) == strtotime($value);
				}
					
			case 'ne':
				
				switch ($dataType){
						
					case 'int':
				
						return $compare != $value;
				
					case 'string':
				
						return strcmp($compare, $value) != 0;
				
					case 'date':
				
						return strtotime($compare) != strtotime($value);
				}
		
			case 'ge':
				
				switch ($dataType){
						
					case 'int':
				
						return $value >= $compare;
				
					case 'string':
				
						return strcmp($value, $compare) >= 0;
				
					case 'date':
				
						return strtotime($value) >= strtotime($compare);
				}
					
			case 'gt':
				
				switch ($dataType){
				
					case 'int':
				
						return $value > $compare;
				
					case 'string':
				
						return strcmp($value, $compare) > 0;
				
					case 'date':
				
						return strtotime($value) > strtotime($compare);
				}
					
			case 'le':
				
				switch ($dataType){
				
					case 'int':
				
						return $value <= $compare;
				
					case 'string':
				
						return strcmp($value, $compare) <= 0;
				
					case 'date':
				
						return strtotime($value) <= strtotime($compare);
				}
					
			case 'lt':
				
				switch ($dataType){
				
					case 'int':
				
						return $value < $compare;
				
					case 'string':
				
						return strcmp($value, $compare) < 0;
				
					case 'date':
				
						return strtotime($value) < strtotime($compare);
				}
		}
		
		return false;
	}
}