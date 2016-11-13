<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogModelProfile extends JModelItem
{
	protected $_context = 'com_cjblog.article';

	protected function populateState ()
	{
		$app = JFactory::getApplication('site');
		
		// Load state from the request.
		$pk = $app->input->getInt('uId', $app->input->getInt('id'));
		$this->setState('profile.id', $pk);
		
		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
		// TODO: Tune these values based on other permissions.
		$user = JFactory::getUser();
		
		if ((! $user->authorise('core.edit.state', 'com_cjblog')) && (! $user->authorise('core.edit', 'com_cjblog')))
		{
			$this->setState('filter.published', 1);
		}
	}

	public function getItem ($pk = null)
	{
		$user = JFactory::getUser();
		if(!$pk)
		{
			$pk = (int) $this->getState('profile.id');
			$pk = $pk > 0 ? $pk : $user->id;
			$this->setState('profile.id', $pk);
		}
		
		if ($this->_item === null)
		{
			$this->_item = array();
		}
		
		if (! isset($this->_item[$pk]))
		{
			try
			{
				$db 			= $this->getDbo();
				$params 		= clone $this->getState('params');
				$aboutTextApp 	= $params->get('about_text_app', 'cjblog');
				
				$query = $db->getQuery(true)
					->select('u.id, u.name, u.username, u.email, u.block, u.registerDate, u.lastvisitDate, u.sendEmail, u.activation, u.params')
					->from('#__users AS u');
				
				$query->select(
						$this->getState('item.select', 
								'a.handle, a.num_articles, a.avatar, a.fans, a.birthday, a.profile_views, a.points, a.banned,'.
								'a.gender, a.location, a.twitter, a.facebook, a.gplus, a.linkedin, a.flickr, a.bebo, a.skype, a.attribs, a.metadesc, a.metadata'));
				
				$query->join('left', '#__cjblog_users AS a on a.id = u.id');
				
				if($aboutTextApp == 'easyprofile')
				{
					$query
						->select($db->qn($db->escape('e.' . $params->get('easyprofile_about', 'author_info'))).' AS about')
						->join('left', '#__jsn_users AS e on u.id = e.id');
				}
				else
				{
					$query->select('a.about');
				}
				
				$query->where("u.id = ".$pk);
				$db->setQuery($query);
// 				echo $query->dump();jexit();
				
				$data = $db->loadObject();
				
				if (empty($data))
				{
					throw new Exception(JText::_('COM_CJBLOG_ERROR_USER_NOT_FOUND'), 404);
				}

				// Convert parameter fields to objects.
				$registry = new JRegistry();
				$registry->loadString($data->attribs);
				
				if($params)
				{
					$data->params = clone $params;
				}
				else
				{
					$data->params =  new JRegistry();
				}
				
				$data->params->merge($registry);
				
				$registry = new JRegistry();
				$registry->loadString($data->metadata);
				$data->metadata = $registry;
				
				// Extract custom profile fields
				$fields = $data->params->toArray();
				$data->fields = new JRegistry();
				foreach($fields as $key => $value)
				{
					if(strpos($key, 'profile_field_') === 0)
					{
						$data->fields->def($key, $value);
					}
				}
				
				// Technically guest could edit an article, but lets not check
				// that to improve performance a little.
				if (! $user->get('guest'))
				{
					$userId = $user->get('id');
					$asset = 'com_cjblog';
					
					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset))
					{
						$data->params->set('access-edit', true);
					}
					// Now check if edit.own is available.
					elseif (! empty($userId) && $user->authorise('core.edit.own', $asset))
					{
						// Check for a valid user and that they are the owner.
						if ($userId == $data->id)
						{
							$data->params->set('access-edit', true);
						}
					}
					
					// Check general edit state permission first.
					if ($user->authorise('core.edit.state', $asset))
					{
						$data->params->set('access-edit-state', true);
					}
					// Now check if edit.state.own is available.
					elseif (! empty($userId) && $user->authorise('core.edit.state.own', $asset))
					{
						// Check for a valid user and that they are the owner.
						if ($userId == $data->id)
						{
							$data->params->set('access-edit-state', true);
						}
					}
				}
				
				// Compute view access permissions.
				if ($access = $this->getState('filter.access'))
				{
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}
				
				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	public function hit ($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);
		
		if ($hitcount)
		{
			$pk = (! empty($pk)) ? $pk : (int) $this->getState('profile.id');
			
			$table = JTable::getInstance('Profile', 'CjBlogTable');
			$table->load($pk);
			$table->hit($pk);
		}
		
		return true;
	}
	
	public function getSummary()
	{
		$db = JFactory::getDbo();
		$summary = new stdClass();
		$pk = (int) $this->getState('profile.id', JFactory::getUser()->id);
		
		$articles = $this->getArticles(5);
		$summary->articles = $articles->items;
		
		if(!empty($summary->articles))
		{
			foreach ($summary->articles as &$item)
			{
				$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
				$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
	
				// No link for ROOT category
				if ($item->parent_alias == 'root')
				{
					$item->parent_slug = null;
				}
	
				$item->catslug = $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
			}
			reset($summary->articles);
		}

		$favorites = $this->getFavorites(5);
		$summary->favorites = $favorites->items;

		if(!empty($summary->favorites))
		{
			foreach ($summary->favorites as &$item)
			{
				$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
				$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
		
				// No link for ROOT category
				if ($item->parent_alias == 'root')
				{
					$item->parent_slug = null;
				}
		
				$item->catslug = $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
			}
			reset($summary->articles);
		}
		
		$reputation = $this->getReputation(5);
		$summary->reputation = $reputation->items;
		
		$api = CjBlogApi::getBadgesApi();
		$summary->badges = $api->getUserBadges($pk, 0, 20);

		return $summary;
	}
	
	public function getFavorites($limit = 20)
	{
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk = $this->getState('profile.id');
		
		JLoader::import('articles', JPATH_COMPONENT.'/models');
		$model = JModelLegacy::getInstance( 'articles', 'CjBlogModel' );
		
		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState('filter.author_id', $pk);
		$model->setState('filter.favored', 1);
		$model->setState('list.ordering', 'a.created');
		$model->setState('list.direction', 'desc');
		$model->setState('list.limit', $limit);
		$return->items = $model->getItems();
		
// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam('id', $pk);
		
		return $return;
	}
	
	public function getReputation($limit = 20)
	{
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk = $this->getState('profile.id');
		
		JLoader::import('reputation', JPATH_COMPONENT.'/models');
		$model = JModelLegacy::getInstance( 'reputation', 'CjBlogModel' );
		
		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState('filter.author_id', $pk);
		$model->setState('list.ordering', 'a.created');
		$model->setState('list.direction', 'desc');
		$model->setState('list.limit', $limit);
		$return->items = $model->getItems();
		
// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam('id', $pk);
		
		return $return;
	}
	
	public function getBadges($limit = 20)
	{
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk = $this->getState('profile.id');
		
		$api = CjBlogApi::getBadgesApi();
		$badges = $api->getUserBadges($pk, 0, 1000);

		return $badges;
	}
	
	public function getArticles($limit = 10)
	{
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk = $this->getState('profile.id');
		
		JLoader::import('articles', JPATH_ROOT.'/components/com_content/models');
		$model = JModelLegacy::getInstance( 'articles', 'ContentModel' );
		
		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState('filter.author_id', $pk);
		$model->setState('list.ordering', 'a.created');
		$model->setState('list.direction', 'desc');
		$model->setState('list.limit', $limit);
		$return->items = $model->getItems();
		$return->state = $model->getState();
		
// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam('id', $pk);
		
		return $return;
	}
	
	public function getQuestions($limit = 20)
	{
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk = $this->getState('profile.id');
		
		JLoader::import('questions', JPATH_ROOT.'/components/com_communityanswers/models');
		$model = JModelLegacy::getInstance( 'questions', 'CommunityAnswersModel' );
		
		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState('filter.author_id', $pk);
		$model->setState('list.ordering', 'a.created');
		$model->setState('list.direction', 'desc');
		$model->setState('list.limit', $limit);
		$return->items = $model->getItems();
		
// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam('id', $pk);
		
		return $return;
	}

	public function getQuizzes($limit = 20)
	{
	    $return = new stdClass();
	    $router = JFactory::getApplication()->getRouter();
	    $pk = $this->getState('profile.id');
	
	    JLoader::import('quizzes', JPATH_ROOT.'/components/com_communityquiz/models');
	    $model = JModelLegacy::getInstance( 'quizzes', 'CommunityQuizModel' );
	
	    $state = $model->getState(); // access the state first so that it can be modified
	    $model->setState('filter.author_id', $pk);
	    $model->setState('list.ordering', 'a.created');
	    $model->setState('list.direction', 'desc');
	    $model->setState('list.limit', $limit);
	    $return->items = $model->getItems();
	
	    // 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
	    $return->pagination = $model->getPagination();
	    $return->pagination->setAdditionalUrlParam('id', $pk);
	
	    return $return;
	}

	public function getSurveys($limit = 20)
	{
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk = $this->getState('profile.id');
	
		JLoader::import('surveys', JPATH_ROOT.'/components/com_communitysurveys/models');
		$model = JModelLegacy::getInstance( 'surveys', 'CommunitySurveysModel' );
	
		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState('filter.author_id', $pk);
		$model->setState('list.ordering', 'a.created');
		$model->setState('list.direction', 'desc');
		$model->setState('list.limit', $limit);
		$return->items = $model->getItems();
	
		// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam('id', $pk);
	
		return $return;
	}
	
	public function getPolls($limit = 20)
	{
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk = $this->getState('profile.id');
		
		JLoader::import('polls', JPATH_ROOT.'/components/com_communitypolls/models');
		$model = JModelLegacy::getInstance( 'polls', 'CommunityPollsModel' );
		
		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState('filter.author_id', $pk);
		$model->setState('list.ordering', 'a.created');
		$model->setState('list.direction', 'desc');
		$model->setState('list.limit', $limit);
		$return->items = $model->getItems();
		
// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam('id', $pk);
		
		return $return;
	}
}