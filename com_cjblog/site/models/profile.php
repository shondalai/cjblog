<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

class CjBlogModelProfile extends JModelItem {

	protected $_context = 'com_cjblog.article';

	protected function populateState() {
		$app = JFactory::getApplication( 'site' );

		// Load state from the request.
		$pk = $app->input->getInt( 'uId', $app->input->getInt( 'id' ) );
		$this->setState( 'profile.id', $pk );

		$offset = $app->input->getUInt( 'limitstart' );
		$this->setState( 'list.offset', $offset );

		// Load the parameters.
		$params = $app->getParams();
		$this->setState( 'params', $params );

		// TODO: Tune these values based on other permissions.
		$user = JFactory::getUser();

		if ( ( ! $user->authorise( 'core.edit.state', 'com_cjblog' ) ) && ( ! $user->authorise( 'core.edit', 'com_cjblog' ) ) )
		{
			$this->setState( 'filter.published', 1 );
		}
	}

	public function getItem( $pk = null ) {
		$user = JFactory::getUser();
		if ( ! $pk )
		{
			$pk = (int) $this->getState( 'profile.id' );
			$pk = $pk > 0 ? $pk : $user->id;
			$this->setState( 'profile.id', $pk );
		}

		if ( $this->_item === null )
		{
			$this->_item = [];
		}

		if ( ! isset( $this->_item[$pk] ) )
		{
			try
			{
				$db           = $this->getDbo();
				$params       = clone $this->getState( 'params' );
				$aboutTextApp = $params->get( 'about_text_app', 'cjblog' );

				$query = $db->getQuery( true )
				            ->select( 'u.id, u.name, u.username, u.email, u.block, u.registerDate, u.lastvisitDate, u.sendEmail, u.activation, u.params' )
				            ->from( '#__users AS u' );

				$query->select(
					$this->getState( 'item.select',
						'a.handle, a.num_articles, a.avatar, a.fans, a.birthday, a.profile_views, a.points, a.banned,' .
						'a.gender, a.location, a.twitter, a.facebook, a.gplus, a.linkedin, a.flickr, a.bebo, a.skype, a.attribs, a.metadesc, a.metadata' ) );

				$query->join( 'left', '#__cjblog_users AS a on a.id = u.id' );

				if ( $aboutTextApp == 'easyprofile' )
				{
					$query
						->select( $db->qn( $db->escape( 'e.' . $params->get( 'easyprofile_about_field', 'author_info' ) ) ) . ' AS about' )
						->join( 'left', '#__jsn_users AS e on u.id = e.id' );
				}
				else
				{
					$query->select( 'a.about' );
				}

				$query->where( "u.id = " . $pk );
				$db->setQuery( $query );
				// 				echo $query->dump();jexit();

				$data = $db->loadObject();

				if ( empty( $data ) )
				{
					throw new Exception( JText::_( 'COM_CJBLOG_ERROR_USER_NOT_FOUND' ), 404 );
				}

				// Convert parameter fields to objects.
				$registry = new JRegistry();
				if ( ! empty( $data->attribs ) )
				{
					$registry->loadString( $data->attribs );
				}

				if ( $params )
				{
					$data->params = clone $params;
				}
				else
				{
					$data->params = new JRegistry();
				}

				$data->params->merge( $registry );

				$registry = new JRegistry();
				if ( ! empty( $data->metadata ) )
				{
					$registry->loadString( $data->metadata );
				}
				$data->metadata = $registry;

				// Extract custom profile fields
				$fields       = $data->params->toArray();
				$data->fields = new JRegistry();
				foreach ( $fields as $key => $value )
				{
					if ( strpos( $key, 'profile_field_' ) === 0 )
					{
						$data->fields->def( $key, $value );
					}
				}

				// Technically guest could edit an article, but lets not check
				// that to improve performance a little.
				if ( ! $user->get( 'guest' ) )
				{
					$userId = $user->get( 'id' );
					$asset  = 'com_cjblog';

					// Check general edit permission first.
					if ( $user->authorise( 'core.edit', $asset ) )
					{
						$data->params->set( 'access-edit', true );
					}
					// Now check if edit.own is available.
					elseif ( ! empty( $userId ) && $user->authorise( 'core.edit.own', $asset ) )
					{
						// Check for a valid user and that they are the owner.
						if ( $userId == $data->id )
						{
							$data->params->set( 'access-edit', true );
						}
					}

					// Check general edit state permission first.
					if ( $user->authorise( 'core.edit.state', $asset ) )
					{
						$data->params->set( 'access-edit-state', true );
					}
					// Now check if edit.state.own is available.
					elseif ( ! empty( $userId ) && $user->authorise( 'core.edit.state.own', $asset ) )
					{
						// Check for a valid user and that they are the owner.
						if ( $userId == $data->id )
						{
							$data->params->set( 'access-edit-state', true );
						}
					}
				}

				// Compute view access permissions.
				if ( $access = $this->getState( 'filter.access' ) )
				{
					// If the access filter has been set, we already know this user can view.
					$data->params->set( 'access-view', true );
				}

				$this->_item[$pk] = $data;
			}
			catch ( Exception $e )
			{
				if ( $e->getCode() == 404 )
				{
					// Need to go thru the error handler to allow Redirect to work.
					throw new Exception( $e->getMessage(), 404 );
				}
				else
				{
					$this->setError( $e );
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	public function hit( $pk = 0 ) {
		$input    = JFactory::getApplication()->input;
		$hitcount = $input->getInt( 'hitcount', 1 );

		if ( $hitcount )
		{
			$pk = ( ! empty( $pk ) ) ? $pk : (int) $this->getState( 'profile.id' );

			$table = JTable::getInstance( 'Profile', 'CjBlogTable' );
			$table->load( $pk );
			$table->hit( $pk );
		}

		return true;
	}

	public function getSummary() {
		$db      = JFactory::getDbo();
		$summary = new stdClass();
		$pk      = (int) $this->getState( 'profile.id', JFactory::getUser()->id );

		$articles          = $this->getArticles( 5 );
		$summary->articles = $articles->items;

		if ( ! empty( $summary->articles ) )
		{
			foreach ( $summary->articles as &$item )
			{
				$item->slug        = $item->alias ? ( $item->id . ':' . $item->alias ) : $item->id;
				$item->parent_slug = ( $item->parent_alias ) ? ( $item->parent_id . ':' . $item->parent_alias ) : $item->parent_id;

				// No link for ROOT category
				if ( $item->parent_alias == 'root' )
				{
					$item->parent_slug = null;
				}

				$item->catslug = $item->category_alias ? ( $item->catid . ':' . $item->category_alias ) : $item->catid;
			}
			reset( $summary->articles );
		}

		$favorites          = $this->getFavorites( 5 );
		$summary->favorites = $favorites->items;

		if ( ! empty( $summary->favorites ) )
		{
			foreach ( $summary->favorites as &$item )
			{
				$item->slug        = $item->alias ? ( $item->id . ':' . $item->alias ) : $item->id;
				$item->parent_slug = ( $item->parent_alias ) ? ( $item->parent_id . ':' . $item->parent_alias ) : $item->parent_id;

				// No link for ROOT category
				if ( $item->parent_alias == 'root' )
				{
					$item->parent_slug = null;
				}

				$item->catslug = $item->category_alias ? ( $item->catid . ':' . $item->category_alias ) : $item->catid;
			}
			reset( $summary->articles );
		}

		$reputation          = $this->getReputation( 5 );
		$summary->reputation = $reputation->items;

		$api             = CjBlogApi::getBadgesApi();
		$summary->badges = $api->getUserBadges( $pk, 0, 20 );

		return $summary;
	}

	public function getFavorites( $limit = 20 ) {
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk     = $this->getState( 'profile.id' );

		JLoader::import( 'articles', JPATH_COMPONENT . '/models' );
		$model = JModelLegacy::getInstance( 'articles', 'CjBlogModel' );

		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState( 'filter.author_id', $pk );
		$model->setState( 'filter.favored', 1 );
		$model->setState( 'list.ordering', 'a.created' );
		$model->setState( 'list.direction', 'desc' );
		$model->setState( 'list.limit', $limit );
		$return->items = $model->getItems();

		// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam( 'id', $pk );

		return $return;
	}

	public function getReputation( $limit = 20 ) {
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk     = $this->getState( 'profile.id' );

		JLoader::import( 'reputation', JPATH_COMPONENT . '/models' );
		$model = JModelLegacy::getInstance( 'reputation', 'CjBlogModel' );

		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState( 'filter.author_id', $pk );
		$model->setState( 'list.ordering', 'a.created' );
		$model->setState( 'list.direction', 'desc' );
		$model->setState( 'list.limit', $limit );
		$return->items = $model->getItems();

		// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam( 'id', $pk );

		return $return;
	}

	public function getBadges( $limit = 20 ) {
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk     = $this->getState( 'profile.id' );

		$api    = CjBlogApi::getBadgesApi();
		$badges = $api->getUserBadges( $pk, 0, 1000 );

		return $badges;
	}

	public function getArticles( $limit = 10 ) {
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk     = $this->getState( 'profile.id' );

		JLoader::import( 'articles', JPATH_ROOT . '/components/com_content/models' );
		$model = JModelLegacy::getInstance( 'articles', 'ContentModel' );

		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState( 'filter.author_id', $pk );
		$model->setState( 'list.ordering', 'a.created' );
		$model->setState( 'list.direction', 'desc' );
		$model->setState( 'list.limit', $limit );
		$return->items = $model->getItems();
		$return->state = $model->getState();

		// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam( 'id', $pk );

		return $return;
	}

	public function getQuestions( $limit = 20 ) {
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk     = $this->getState( 'profile.id' );

		JLoader::import( 'questions', JPATH_ROOT . '/components/com_communityanswers/models' );
		$model = JModelLegacy::getInstance( 'questions', 'CommunityAnswersModel' );

		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState( 'filter.author_id', $pk );
		$model->setState( 'list.ordering', 'a.created' );
		$model->setState( 'list.direction', 'desc' );
		$model->setState( 'list.limit', $limit );
		$return->items = $model->getItems();

		// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam( 'id', $pk );

		return $return;
	}

	public function getQuizzes( $limit = 20 ) {
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk     = $this->getState( 'profile.id' );

		JLoader::import( 'quizzes', JPATH_ROOT . '/components/com_communityquiz/models' );
		$model = JModelLegacy::getInstance( 'quizzes', 'CommunityQuizModel' );

		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState( 'filter.author_id', $pk );
		$model->setState( 'list.ordering', 'a.created' );
		$model->setState( 'list.direction', 'desc' );
		$model->setState( 'list.limit', $limit );
		$return->items = $model->getItems();

		// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam( 'id', $pk );

		return $return;
	}

	public function getSurveys( $limit = 20 ) {
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk     = $this->getState( 'profile.id' );

		JLoader::import( 'surveys', JPATH_ROOT . '/components/com_communitysurveys/models' );
		$model = JModelLegacy::getInstance( 'surveys', 'CommunitySurveysModel' );

		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState( 'filter.author_id', $pk );
		$model->setState( 'list.ordering', 'a.created' );
		$model->setState( 'list.direction', 'desc' );
		$model->setState( 'list.limit', $limit );
		$return->items = $model->getItems();

		// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam( 'id', $pk );

		return $return;
	}

	public function getPolls( $limit = 20 ) {
		$return = new stdClass();
		$router = JFactory::getApplication()->getRouter();
		$pk     = $this->getState( 'profile.id' );

		JLoader::import( 'polls', JPATH_ROOT . '/components/com_communitypolls/models' );
		$model = JModelLegacy::getInstance( 'polls', 'CommunityPollsModel' );

		$state = $model->getState(); // access the state first so that it can be modified
		$model->setState( 'filter.author_id', $pk );
		$model->setState( 'list.ordering', 'a.created' );
		$model->setState( 'list.direction', 'desc' );
		$model->setState( 'list.limit', $limit );
		$return->items = $model->getItems();

		// 		$router->setVars(array('id'=>$this->_item[$pk]->handle));
		$return->pagination = $model->getPagination();
		$return->pagination->setAdditionalUrlParam( 'id', $pk );

		return $return;
	}

	public function getGDPRProfileData() {
		$user = JFactory::getUser();
		$data = new stdClass();
		$db   = $this->getDbo();

		$query = $db->getQuery( true )
		            ->select( 'a.handle, a.avatar, a.about, a.gender, a.birthday, a.location, a.points, a.num_articles, a.num_badges, a.profile_views' )
		            ->select( 'a.twitter, a.facebook, a.gplus, a.linkedin, a.flickr, a.bebo, a.skype' )
		            ->select( 'u.name, u.email' )
		            ->from( '#__cjblog_users AS a' )
		            ->join( 'left', '#__users AS u ON u.id = a.id' )
		            ->where( 'a.id = ' . $user->id );
		$db->setQuery( $query );
		$data->profile = $db->loadObject();

		$query = $db->getQuery( true )
		            ->select( 'a.id, a.title, a.introtext, a.fulltext, a.created, a.modified_by, a.modified, a.metakey, a.metadesc' )
		            ->from( '#__content AS a' )
		            ->where( 'a.created_by = ' . $user->id, ' AND a.state = 1' );
		$db->setQuery( $query );
		$data->articles = $db->loadObjectList();

		$data->badges = CjBlogApi::getBadgesApi()->getUserBadges( $user->id );

		$query = $db->getQuery( true )
		            ->select( 'title, description, points, created' )
		            ->from( '#__cjblog_points' )
		            ->where( 'user_id = ' . $user->id );
		$db->setQuery( $query );
		$data->points = $db->loadObjectList();

		$targetDir = CJBLOG_MEDIA_DIR . 'downloads/' . $user->id . '/';
		\Joomla\CMS\Filesystem\Folder::create( $targetDir );
		$filename = $targetDir . 'export.zip';
		$zip      = new ZipArchive();
		$zip->open( $filename, ZipArchive::CREATE );

		// add html download
		$html = JLayoutHelper::render( 'export.profile', [ 'data' => $data ] );
		\Joomla\CMS\Filesystem\File::write( $targetDir . 'index.html', $html );
		$zip->addFile( $targetDir . 'index.html', 'index.html' );

		if ( file_exists( CJBLOG_AVATAR_BASE_DIR . 'size-256/' . $data->profile->avatar ) && is_file( CJBLOG_AVATAR_BASE_DIR . 'size-256/' . $data->profile->avatar ) )
		{
			$zip->addFile( CJBLOG_AVATAR_BASE_DIR . 'size-256/' . $data->profile->avatar, 'media/images/' . $data->profile->avatar );
		}

		$zip->addFile( CJLIB_MEDIA_PATH . '/fontawesome/css/font-awesome.min.css', 'media/css/font-awesome.min.css' );
		$zip->addFile( CJLIB_MEDIA_PATH . '/fontawesome/fonts/fontawesome-webfont.svg', 'media/fonts/fontawesome-webfont.svg' );
		$zip->addFile( CJLIB_MEDIA_PATH . '/fontawesome/fonts/fontawesome-webfont.ttf', 'media/fonts/fontawesome-webfont.ttf' );
		$zip->addFile( CJLIB_MEDIA_PATH . '/fontawesome/fonts/fontawesome-webfont.woff', 'media/fonts/fontawesome-webfont.woff' );
		$zip->addFile( CJLIB_MEDIA_PATH . '/fontawesome/fonts/fontawesome-webfont.woff2', 'media/fonts/fontawesome-webfont.woff2' );
		$zip->addFile( CJLIB_MEDIA_PATH . '/fontawesome/fonts/FontAwesome.eot', 'media/fonts/FontAwesome.eot' );
		$zip->addFile( CJLIB_MEDIA_PATH . '/bootstrap/css/bootstrap.v4.min.css', 'media/css/bootstrap.v4.min.css' );
		$zip->addFile( CJLIB_MEDIA_PATH . '/bootstrap/js/bootstrap.bundle.v4.min.js', 'media/js/bootstrap.bundle.v4.min.js' );
		$zip->addFile( CJLIB_MEDIA_PATH . '/jquery/jquery-3.3.1.slim.min.js', 'media/js/jquery-3.3.1.slim.min.js' );

		$error = $zip->close();

		// cleanup
		\Joomla\CMS\Filesystem\File::delete( $targetDir . 'index.html' );

		return $filename;
	}

	public function deleteProfileAndData( $deleteData = false ) {
		$user = JFactory::getUser();
		$db   = $this->getDbo();

		if ( $deleteData )
		{
			$query = $db->getQuery( true )
			            ->select( 'id' )
			            ->from( '#__content' )
			            ->where( 'created_by = ' . $user->id );
			$db->setQuery( $query );
			$articles = $db->loadColumn();

			if ( ! empty( $articles ) )
			{
				$table = $this->getTable( 'Content', 'JTable' );
				foreach ( $articles as $i => $pk )
				{
					if ( $table->load( $pk ) )
					{
						$table->delete( $pk );
					}
				}

				$query = $db->getQuery( true )
				            ->delete( '#__cjblog_tracking' )
				            ->where( 'post_id in (' . implode( ',', $articles ) . ') and post_type = 1' );
				$db->setQuery( $query );
				$db->execute();
			}
		}

		$query = $db->getQuery( true )
		            ->delete( '#__cjblog_users' )
		            ->where( 'id = ' . $user->id );
		$db->setQuery( $query );
		$db->execute();

		$query = $db->getQuery( true )
		            ->delete( '#__cjblog_points' )
		            ->where( 'user_id = ' . $user->id );
		$db->setQuery( $query );
		$db->execute();

		return true;
	}

}