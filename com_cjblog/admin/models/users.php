<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class CjBlogModelUsers extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'username', 'a.username',
				'email', 'a.email',
				'block', 'a.block',
				'sendEmail', 'a.sendEmail',
				'registerDate', 'a.registerDate',
				'lastvisitDate', 'a.lastvisitDate',
				'activation', 'a.activation',
				'articles', 'cju.num_articles',
				'hits', 'cju.profile_views',
				'points', 'cju.points',
				'active',
				'group_id',
				'range',
				'state',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout', 'default', 'cmd'))
		{
			$this->context .= '.' . $layout;
		}

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$active = $this->getUserStateFromRequest($this->context . '.filter.active', 'filter_active');
		$this->setState('filter.active', $active);

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
		$this->setState('filter.state', $state);

		$groupId = $this->getUserStateFromRequest($this->context . '.filter.group', 'filter_group_id', null, 'int');
		$this->setState('filter.group_id', $groupId);
		
		$range = $this->getUserStateFromRequest($this->context . '.filter.range', 'filter_range');
		$this->setState('filter.range', $range);

		$groups = json_decode(base64_decode($app->input->get('groups', '', 'BASE64')));

		if (isset($groups))
		{
			$groups = \Joomla\Utilities\ArrayHelper::toInteger($groups);
		}

		$this->setState('filter.groups', $groups);

		$excluded = json_decode(base64_decode($app->input->get('excluded', '', 'BASE64')));

		if (isset($excluded))
		{
			$excluded = \Joomla\Utilities\ArrayHelper::toInteger($excluded);
		}

		$this->setState('filter.excluded', $excluded);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_users');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.active');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.group_id');
		$id .= ':' . $this->getState('filter.range');

		return parent::getStoreId($id);
	}

	/**
	 * Gets the list of users and adds expensive joins to the result set.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (empty($this->cache[$store]))
		{
			$groups = $this->getState('filter.groups');
			$groupId = $this->getState('filter.group_id');

			if (isset($groups) && (empty($groups) || $groupId && !in_array($groupId, $groups)))
			{
				$items = array();
			}
			else
			{
				$items = parent::getItems();
			}

			// Bail out on an error or empty list.
			if (empty($items))
			{
				$this->cache[$store] = $items;

				return $items;
			}

			// Joining the groups with the main query is a performance hog.
			// Find the information only on the result set.

			// First pass: get list of the user id's and reset the counts.
			$userIds = array();

			foreach ($items as $item)
			{
				$userIds[] = (int) $item->id;
				$item->group_count = 0;
				$item->group_names = '';
				$item->note_count = 0;
			}

			// Get the counts from the database only for the users in the list.
			$db = $this->getDbo();
			$query = $db->getQuery(true);

			// Join over the group mapping table.
			$query->select('map.user_id, COUNT(map.group_id) AS group_count')
				->from('#__user_usergroup_map AS map')
				->where('map.user_id IN (' . implode(',', $userIds) . ')')
				->group('map.user_id')
				// Join over the user groups table.
				->join('LEFT', '#__usergroups AS g2 ON g2.id = map.group_id');

			$db->setQuery($query);

			// Load the counts into an array indexed on the user id field.
			try
			{
				$userGroups = $db->loadObjectList('user_id');
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

			$query->clear()
				->select('n.user_id, COUNT(n.id) As note_count')
				->from('#__user_notes AS n')
				->where('n.user_id IN (' . implode(',', $userIds) . ')')
				->where('n.state >= 0')
				->group('n.user_id');

			$db->setQuery($query);

			// Load the counts into an array indexed on the aro.value field (the user id).
			try
			{
				$userNotes = $db->loadObjectList('user_id');
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

			// Second pass: collect the group counts into the master items array.
			foreach ($items as &$item)
			{
				if (isset($userGroups[$item->id]))
				{
					$item->group_count = $userGroups[$item->id]->group_count;

					// Group_concat in other databases is not supported
					$item->group_names = $this->_getUserDisplayedGroups($item->id);
				}

				if (isset($userNotes[$item->id]))
				{
					$item->note_count = $userNotes[$item->id]->note_count;
				}
			}

			// Add the items to the internal cache.
			$this->cache[$store] = $items;
		}

		return $this->cache[$store];
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);

		$query->from($db->quoteName('#__users') . ' AS a');
		
		// join over #__cjblog_users table
		$query
			->select(
					'cju.handle, cju.about, cju.avatar, cju.birthday, cju.num_articles AS articles, cju.points,'.
					'cju.fans, cju.profile_views AS hits, cju.checked_out, cju.checked_out_time')
			->join('LEFT', '#__cjblog_users AS cju on cju.id = a.id');
		
		// If the model is set to check item state, add to the query.
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('a.block = ' . (int) $state);
		}

		// If the model is set to check the activated state, add to the query.
		$active = $this->getState('filter.active');

		if (is_numeric($active))
		{
			if ($active == '0')
			{
				$query->where('a.activation = ' . $db->quote(''));
			}
			elseif ($active == '1')
			{
				$query->where($query->length('a.activation') . ' = 32');
			}
		}
		
		// Filter the items over the group id if set.
		$groupId = $this->getState('filter.group_id');
		$groups = $this->getState('filter.groups');

		if ($groupId || isset($groups))
		{
			$query->join('LEFT', '#__user_usergroup_map AS map2 ON map2.user_id = a.id')
				->group($db->quoteName(array('a.id', 'a.name', 'a.username', 'a.password', 'a.block', 'a.sendEmail', 'a.registerDate', 'a.lastvisitDate', 'a.activation', 'a.params', 'a.email')));

			if ($groupId)
			{
				$query->where('map2.group_id = ' . (int) $groupId);
			}

			if (isset($groups))
			{
				$query->where('map2.group_id IN (' . implode(',', $groups) . ')');
			}
		}

		// Filter the items over the search string if set.
		if ($this->getState('filter.search') !== '' && $this->getState('filter.search') !== null)
		{
			// Escape the search token.
			$token = $db->quote('%' . $db->escape($this->getState('filter.search')) . '%');

			// Compile the different search clauses.
			$searches = array();
			$searches[] = 'a.name LIKE ' . $token;
			$searches[] = 'a.username LIKE ' . $token;
			$searches[] = 'a.email LIKE ' . $token;

			// Add the clauses to the query.
			$query->where('(' . implode(' OR ', $searches) . ')');
		}

		// Add filter for registration ranges select list
		$range = $this->getState('filter.range');

		// Apply the range filter.
		if ($range)
		{
			// Get UTC for now.
			$dNow = new JDate;
			$dStart = clone $dNow;

			switch ($range)
			{
				case 'past_week':
					$dStart->modify('-7 day');
					break;

				case 'past_1month':
					$dStart->modify('-1 month');
					break;

				case 'past_3month':
					$dStart->modify('-3 month');
					break;

				case 'past_6month':
					$dStart->modify('-6 month');
					break;

				case 'post_year':
				case 'past_year':
					$dStart->modify('-1 year');
					break;

				case 'today':
					// Ranges that need to align with local 'days' need special treatment.
					$app = JFactory::getApplication();
					$offset = $app->getCfg('offset');

					// Reset the start time to be the beginning of today, local time.
					$dStart = new JDate('now', $offset);
					$dStart->setTime(0, 0, 0);

					// Now change the timezone back to UTC.
					$tz = new DateTimeZone('GMT');
					$dStart->setTimezone($tz);
					break;
			}

			if ($range == 'post_year')
			{
				$query->where(
					'a.registerDate < ' . $db->quote($dStart->format('Y-m-d H:i:s'))
				);
			}
			else
			{
				$query->where(
					'a.registerDate >= ' . $db->quote($dStart->format('Y-m-d H:i:s')) .
						' AND a.registerDate <=' . $db->quote($dNow->format('Y-m-d H:i:s'))
				);
			}
		}

		// Filter by excluded users
		$excluded = $this->getState('filter.excluded');

		if (!empty($excluded))
		{
			$query->where('id NOT IN (' . implode(',', $excluded) . ')');
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.name')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
// echo $query->dump();
		return $query;
	}

	/**
	 * SQL server change
	 *
	 * @param   integer  $user_id  User identifier
	 *
	 * @return  string             Groups titles imploded :$
	 */
	function _getUserDisplayedGroups($user_id)
	{
		$db = JFactory::getDbo();
		$query = "SELECT title FROM " . $db->quoteName('#__usergroups') . " ug left join " .
			$db->quoteName('#__user_usergroup_map') . " map on (ug.id = map.group_id)" .
			" WHERE map.user_id=" . (int) $user_id;

		$db->setQuery($query);
		$result = $db->loadColumn();

		return implode("\n", $result);
	}
	
	public function syncUsers($startId, $endId)
	{
		$db = JFactory::getDbo();
		
		try
		{
			$query =
				'insert into 
					#__cjblog_users (id, handle, points) 
					(
						select 
							u.id, replace(u.username, \'-\', \'_\'), ifnull(sum(p.points), 0) as points 
						from 
							#__users AS u 
						left join 
							#__cjblog_points AS p on p.user_id = u.id 
						where 
							u.id >= '.$startId.' and u.id <= '.$endId.'
						group by u.id
					)
				 on duplicate key 
    				update id = values(id)';

			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
 			throw new Exception($e);
		}
		
		// now update
		try
		{
			$query = $db->getQuery(true)
				->update('#__cjblog_users AS u')
				->set('points = (select sum(p.points) from #__cjblog_points AS p where p.user_id = u.id group by p.user_id)')
				->set('num_articles = (select count(*) from #__content AS t where t.created_by = u.id and t.state = 1 group by t.created_by)')
				->where('u.id >= '.$startId.' and u.id <= '.$endId);
			
			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			// 			throw new Exception($e);
		}
			
		return false;
	}
	
	public function getFirstAndLastUserId()
	{
		try 
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('min(id) AS min_id, max(id) AS max_id')
				->from('#__users');
			
			$db->setQuery($query);
			$result = $db->loadObject();
			
			return $result;
		}
		catch (Exception $e)
		{
		}
	}
}
