<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT.'/components/com_cjblog/helpers/route.php';

class CjBlogBadgeApi 
{
	private static $_users = array();
	private static $_badge_rules = array();
	private static $_errors = array();
	private static $_enable_logging = false;
	
	public function __construct ($config = array())
	{
		if(isset($config['logging']) && $config['logging'])
		{
			CjBlogBadgeApi::$_enable_logging = true;
		}

		JFactory::getLanguage()->load('com_cjblog', JPATH_ROOT);
	}
	
	/**
	 * Sets the debug logging enabled or disabled
	 *
	 * @param booleab $state sets the state of logging
	 */
	public function set_logging($state = true)
	{
		CjBlogBadgeApi::$_enable_logging = $state;
	}
	
	public function getUserBadges($userid, $limitstart = 0, $limit = 50)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('count(*) as num_times, a.id, a.asset_name, a.asset_title, a.description, a.published,	b.id as badge_id, b.title, b.alias, b.icon, b.css_class')
			->from('#__cjblog_user_badge_map AS m')
			->join('left', '#__cjblog_badge_rules AS a ON a.id = m.rule_id')
			->join('left', '#__cjblog_badges AS b ON b.id = m.badge_id')
			->where('a.published = 1')
			->group('m.badge_id, m.rule_id')
			->order('m.date_assigned desc');

		if(is_array($userid))
		{
			$userid = \Joomla\Utilities\ArrayHelper::toInteger($userid);
			$query->where('m.user_id in ('.implode(',', $userid).')');
		} 
		else 
		{
			$userid = intval($userid);
			$query->where('m.user_id = '.$userid);
		}

		try
		{
			$db->setQuery($query, $limitstart, $limit);
			$badges = $db->loadAssocList();

		}
		catch (Exception $e)
		{
			JLog::add('CjBlogBadgeApi.get_user_badges - DB Error: '.$e->getMessage(), JLog::ERROR, CJBLOG);
		}
	
		return $badges;
	}
	
	public function getBadge($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id, title, alias, description, icon, css_class, published')
			->from('#__cjblog_badges')
			->where('where id = '.$id);

		try
		{
			$db->setQuery($query);
			$badge = $db->loadAssoc();
		}
		catch (Exception $e)
		{
			JLog::add('CjBlogBadgeApi.get_badge_details - DB Error: '.$e->getMessage(), JLog::ERROR, CJBLOG);
		}

		return $badge;
	}
	
	public function getBadgesByRuleName($rule_name)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id, a.title, a.description, a.rule_name, b.icon, b.published, b.css_class')
			->from('#__cjblog_badge_rules AS a')
			->join('inner', '#__cjblog_badges b ON b.id = a.badge_id')
			->where('a.rule_name = '.$db->q($rule_name))
			->where('a.published = 1')
			->order('a.title');

		try
		{
			$db->setQuery($query);
			$badges = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			JLog::add('CjBlogBadgeApi.get_badges_by_component_name - DB Error: '.$e->getMessage(), JLog::ERROR, CJBLOG);
		}

		return $badges;
	}
	
	public function getUserBadgesMarkup($user)
	{
		return '
			<span class="label tooltip-hover" title="'.JText::_('LBL_ARTICLES').'">'.$user['num_articles'].'</span>
			<span class="label tooltip-hover" title="'.JText::_('LBL_REPUTATION').'">'.$user['points'].'</span>
			<span class="label label-info tooltip-hover" title="'.JText::_('LBL_BADGES').'"><i class="icon-star-empty icon-white"></i> '.$user['num_badges'].'</span>';
	}
	
	public function assignCustomBadge($ruleId, $userId, $ref_id = 0)
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
	
				if(CjBlogBadgeApi::$_enable_logging)
				{
					JLog::add('Custom Badge Rule - Badge assigned. User ID: '.$userId.'| Badge ID: '.$badge->id, JLog::DEBUG, CJBLOG);
				}
			}
		}
		catch (Exception $e)
		{
			JLog::add('Trigger Badge Rule - No Rules Loaded. DB Error: '.$e->getMessage(), JLog::ERROR, CJBLOG);
		}
	}
	
	public function triggerBadgeRule($name, array $params, $userid = 0)
	{
		$db = JFactory::getDbo();
		if(!$userid) $userid = JFactory::getUser()->id;
	
		if(CjBlogBadgeApi::$_enable_logging)
		{
			JLog::add('Trigger Badge Rule - Before Start - Available Badge Rules: '.count(CjBlogBadgeApi::$_badge_rules), JLog::DEBUG, CJBLOG);
		}
	
		if(empty(CjBlogBadgeApi::$_badge_rules))
		{
			$query = $db->getQuery(true)
				->select('id, asset_name, rule_name, rule_content, badge_id, access')
				->from('#__cjblog_badge_rules')
				->where('badge_id in (select badge_id from #__cjblog_badges where published = 1)')
				->where('published = 1');
				
			$db->setQuery($query);
			$rules = $db->loadObjectList();

			if(!empty($rules))
			{
				foreach ($rules as $rule)
				{
					$content = json_decode($rule->rule_content);
					if(!empty($content))
					{
						CjBlogBadgeApi::$_badge_rules[$rule->rule_name][] = array('id'=>$rule->id, 'asset'=>$rule->asset_name, 'content'=>$content, 'badge_id'=>$rule->badge_id, 'access'=>$rule->access);
					}
				}
			} 
			else 
			{
				JLog::add('Trigger Badge Rule - No Rules Loaded.', JLog::ERROR, CJBLOG);
			}
		}
	
		if(CjBlogBadgeApi::$_enable_logging)
		{
			JLog::add('Trigger Badge Rule - Rules Loaded: '.count(CjBlogBadgeApi::$_badge_rules), JLog::DEBUG, CJBLOG);
		}

		if(!empty(CjBlogBadgeApi::$_badge_rules[$name]))
		{
			foreach(CjBlogBadgeApi::$_badge_rules[$name] as $badge_rule)
			{
				if(!in_array($badge_rule['access'], JAccess::getAuthorisedViewLevels($userid))) continue;
				$rule_content = $badge_rule['content'];
	
				if(!empty($rule_content->rules))
				{
					$validated = 0;
					if(CjBlogBadgeApi::$_enable_logging)
					{
						JLog::add('Trigger Badge Rule - Before validation. Conditions: '.count($rule_content->rules), JLog::DEBUG, CJBLOG);
					}
						
					switch ($rule_content->join)
					{
						case 'and':
							
							$validated = 1;
							foreach ($rule_content->rules as $rule)
							{
								if(empty($params[$rule->name])) return -1;
								if(!$this->validateCondition($rule->compare, $rule->dataType, $rule->value, $params[$rule->name]))
								{
									$validated = 0;
									break;
								}
							}
							break;
								
						case 'or':
								
							foreach ($rule_content->rules as $rule)
							{
								if(empty($params[$rule->name])) return false;
								if($this->validateCondition($rule->compare, $rule->dataType, $rule->value, $params[$rule->name]))
								{
									$validated = 1;
									break;
								}
							}
								
							break;
					}
						
					if(CjBlogBadgeApi::$_enable_logging)
					{
						JLog::add('Trigger Badge Rule - After validation. Status: '.$validated, JLog::DEBUG, CJBLOG);
					}
						
					if($validated == 0) continue;
						
					// validated, assign badge now.
					$created = JFactory::getDate()->toSql();
					$query = $db->getQuery(true)
						->select('count(*)')
						->from('#__cjblog_user_badge_map')
						->where('user_id = '.$userid.' and rule_id = '.$badge_rule['id'].' and badge_id = '.$badge_rule['badge_id']);
						
						
					if($rule_content->multiple == 1)
					{
						$ref_id = !empty($params['ref_id']) ? (int) $params['ref_id'] : 0;
						$query->where('ref_id > 0 and ref_id='.$ref_id);
					}
					else
					{
						$ref_id = 0;
					}
						
					$db->setQuery($query);
					$count = $db->loadResult();
						
					if($count > 0)
					{
						if(CjBlogBadgeApi::$_enable_logging)
						{
							JLog::add('Trigger Badge Rule - Conflicting badge exists, returning.', JLog::DEBUG, CJBLOG);
						}
	
						continue;
					}
					
					$query = $db->getQuery(true)
						->insert('#__cjblog_user_badge_map')
						->columns('user_id, badge_id, rule_id, ref_id, date_assigned')
						->values($userid.','.$badge_rule['badge_id'].','.$badge_rule['id'].','.$ref_id.','.$db->quote($created));
					$db->setQuery($query);
					
					if($db->execute())
					{
						$query = $db->getQuery(true)
							->update('#__cjblog_users')
							->set('num_badges = (select count(*) from #__cjblog_user_badge_map where user_id = '.$userid.')')
							->where('id = '.$userid);
						
						$db->setQuery($query);
						$db->execute();
	
						$query = $db->getQuery(true)
							->update('#__cjblog_badge_rules')
							->set('num_assigned = num_assigned + 1')
							->where('id = '.$badge_rule['id']);
						$db->setQuery($query);
						$db->execute();
	
						if(CjBlogBadgeApi::$_enable_logging)
						{
							JLog::add('Trigger Badge Rule - Badge assigned. User ID: '.$userid.'| Badge ID: '.$badge_rule['badge_id'], JLog::DEBUG, CJBLOG);
						}
	
						continue;
					}
						
					JLog::add('Trigger Badge Rule - After processing, something went wrong.', JLog::ERROR, CJBLOG);
				}
			}
				
			return true;
		}
	
		if(CjBlogBadgeApi::$_enable_logging)
		{
			JLog::add('Trigger Badge Rule - No rules found with the rule name - '.$name.' - to execute.', JLog::DEBUG, CJBLOG);
		}
	
		return false;
	}
	
	private function validateCondition($type, $dataType, $compare, $value)
	{
		if(CjBlogBadgeApi::$_enable_logging)
		{
			JLog::add('Trigger Badge Rule - Validate Condition - Type: '.$type.'| Data Type: '.$dataType.'| Compare: '.$compare.'| To: '.$value, JLog::DEBUG, CJBLOG);
		}
	
		switch ($type)
		{
			case 'eq':
	
				switch ($dataType)
				{
					case 'int':
	
						return $compare == $value;
	
					case 'string':
	
						return strcmp($compare, $value) == 0;
	
					case 'date':
	
						return strtotime($compare) == strtotime($value);
				}
					
			case 'ne':
	
				switch ($dataType)
				{
					case 'int':
	
						return $compare != $value;
	
					case 'string':
	
						return strcmp($compare, $value) != 0;
	
					case 'date':
	
						return strtotime($compare) != strtotime($value);
				}
	
			case 'ge':
	
				switch ($dataType)
				{
					case 'int':
	
						return $value >= $compare;
	
					case 'string':
	
						return strcmp($value, $compare) >= 0;
	
					case 'date':
	
						return strtotime($value) >= strtotime($compare);
				}
					
			case 'gt':
	
				switch ($dataType)
				{
					case 'int':
	
						return $value > $compare;
	
					case 'string':
	
						return strcmp($value, $compare) > 0;
	
					case 'date':
	
						return strtotime($value) > strtotime($compare);
				}
					
			case 'le':
	
				switch ($dataType)
				{
					case 'int':
	
						return $value <= $compare;
	
					case 'string':
	
						return strcmp($value, $compare) <= 0;
	
					case 'date':
	
						return strtotime($value) <= strtotime($compare);
				}
					
			case 'lt':
	
				switch ($dataType)
				{
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