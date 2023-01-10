<?php

use Joomla\CMS\Helper\ContentHelper;
use Imagine\Exception\Exception;

/**
 * @package     CjBlog
 * @subpackage  plg_content_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die;

require_once JPATH_ROOT . '/components/com_cjblog/helpers/constants.php';
require_once JPATH_ROOT . '/components/com_cjblog/helpers/route.php';
require_once JPATH_ROOT . '/components/com_cjblog/helpers/helper.php';
require_once JPATH_ROOT . '/components/com_cjblog/lib/api.php';

class PlgContentCjBlog extends JPlugin {

	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );

		if ( file_exists( JPATH_ROOT . '/components/com_content/helpers/route.php' ) )
		{
			require_once JPATH_ROOT . '/components/com_content/helpers/route.php';
		}
	}

	public function onContentPrepare( $context, &$article, &$params, $page = 0 ) {
		$app = JFactory::getApplication();
		if ( $context != 'com_content.article' || $app->isClient( 'administrator' ) || $app->isClient( 'api' ) || ! $this->params->get( 'use_cjblog_content', 1 ) )
		{
			return true;
		}

		$appParams  = JComponentHelper::getParams( 'com_cjblog' );
		$custom_tag = $appParams->get( 'custom_header_tags', true );
		$loadBsCss  = $appParams->get( 'load_bootstrap_css', false );

		$excludedCategories = $appParams->get( 'exclude_categories' );
		if ( ( is_numeric( $excludedCategories ) && $article->catid == $excludedCategories )
		     || ( is_array( $excludedCategories )
		          && in_array( $article->catid, $excludedCategories ) ) )
		{
			return true;
		}

		require_once JPATH_ROOT . '/components/com_cjlib/framework.php';
		require_once JPATH_ROOT . '/components/com_cjlib/framework/api.php';
		CJLib::import( 'corejoomla.framework.core' );
		$this->loadLanguage( 'com_cjblog', JPATH_ROOT );
		$document = JFactory::getDocument();

		if ( CJBLOG_MAJOR_VERSION < 4 )
		{
			CJFunctions::load_jquery( [ 'libs' => [ 'fontawesome' ], 'custom_tag' => $custom_tag ] );
			if ( $loadBsCss )
			{
				CjLib::behavior( 'bootstrap', [ 'loadcss' => $loadBsCss, 'customtag' => $custom_tag ] );
			}
			CJLib::behavior( 'bscore', [ 'customtag' => $custom_tag ] );
		}
		else
		{
			$wa = $document->getWebAssetManager();
			$wa
				->useScript( 'jquery' )
				->useScript( 'bootstrap.tab' )
				->useScript( 'bootstrap.dropdown' )
				->useStyle( 'fontawesome' );
		}

		CJFunctions::add_css_to_document( $document, JUri::root( true ) . '/media/com_cjblog/css/cj.blog.min.css', $custom_tag );
		CJFunctions::add_script( JUri::root( true ) . '/media/com_cjblog/js/cj.blog.min.js', $custom_tag );

		// reset article params
		if ( $article->params )
		{
			$article->params->set( 'show_title', false );
			$article->params->set( 'show_author', false );
			$article->params->set( 'show_print_icon', false );
			$article->params->set( 'show_email_icon', false );
			$article->params->set( 'access-edit', false );
		}

		$layout           = $appParams->get( 'ui_layout', 'default' );
		$user             = JFactory::getUser();
		$article->slug    = ! empty( $article->alias ) ? $article->id . ':' . $article->alias : $article->id;
		$article->catslug = ! empty( $article->category_alias ) ? ( $article->id . ':' . $article->category_alias ) : $article->id;
		$return           = ContentHelperRoute::getArticleRoute( $article->slug, $article->catslug, $article->language );
		$return           = base64_encode( $return );

		// initiate blocks
		$toolbarHtml       = '';
		$titleHtml         = '';
		$infoHtmlTop       = '';
		$infoHtmlBottom    = '';
		$socialSharingHtml = '';

		if ( $appParams->get( 'show_toolbar' ) )
		{
			$toolbarHtml = CjBlogSiteHelper::renderLayout( $layout . '.toolbar', [ 'params' => $appParams ] );
		}

		if ( $appParams->get( 'show_title' ) )
		{
			$titleHtml = '<div class="page-header no-space-top"><h2 class="no-space-top" itemprop="name">';
			$titleHtml .= CjLibUtils::escape( $article->title );
			if ( $user->authorise( 'core.edit', 'com_content.article.' . $article->id ) )
			{
				$titleHtml = $titleHtml . '<small><sup>&nbsp;<a href="' .
				             JRoute::_( ContentHelperRoute::getFormRoute( $article->id ) . '&return=' . $return ) . '">' . JText::_( 'JGLOBAL_EDIT' ) . '</a></sup></small>';
			}
			$titleHtml .= '</h2></div>';
		}
		else
		{
			$titleHtml .= '<div>&nbsp;<a href="' . JRoute::_( ContentHelperRoute::getFormRoute( $article->id ) . '&return=' . $return ) . '">' . JText::_( 'JGLOBAL_EDIT' )
			              . '</a></div>';
		}


		//*************************** SOCIAL SHARING *****************************//
		if ( $params->get( 'social_sharing', 1 ) == 1 )
		{
			JPluginHelper::importPlugin( 'corejoomla' );
			$results = $app->triggerEvent( 'onSocialsDisplay', [ 'com_communityanswers.question', $params ] );

			if ( ! empty( $results ) )
			{
				$socialSharingHtml = '<hr Class="no-space-top"/><p class="text-muted">' . JText::_( 'COM_CJBLOG_SOCIAL_SHARING_DESC' ) . '</p>' . implode( ' ', $results );
			}
		}
		//************************ END SOCIAL SHARING ***************************// 

		//************************* AUTHOR INFO *********************************//
		$showArticleInfo = $appParams->get( 'show_article_info' );
		if ( $showArticleInfo )
		{
			$profileApi   = CjBlogApi::getProfileApi();
			$profile      = $profileApi->getUserProfile( $article->created_by );
			$aboutTextApp = $appParams->get( 'about_text_app', 'cjblog' );

			// Check if author has pro-capabilities
			$proUser = JFactory::getUser( $article->created_by )->authorise( 'core.pro', 'com_cjblog' );

			if ( $proUser )
			{
				$db = JFactory::getDbo();

				switch ( $aboutTextApp )
				{
					case 'cjblog':
						// keep it as is
						break;

					case 'cjforum':
						$query = $db->getQuery( true )
						            ->select( $db->qn( 'about' ) )
						            ->from( '#__cjforum_users' )
						            ->where( 'id = ' . $article->created_by );

						$db->setQuery( $query );
						$profile['about'] = $db->loadResult();
						break;

					case 'easyprofile':
						$query = $db->getQuery( true )
						            ->select( $db->qn( $db->escape( $appParams->get( 'easyprofile_about_field', 'author_info' ) ) ) . ' AS about' )
						            ->from( '#__jsn_users' )
						            ->where( 'id = ' . $article->created_by );

						$db->setQuery( $query );
						$profile['about'] = $db->loadResult();
						break;

					default:
						$profile['about'] = '';
						break;
				}
			}
			else
			{
				$profile['about'] = '';
			}

			$html = CjBlogSiteHelper::renderLayout( $layout . '.article_info', [ 'article' => $article, 'profile' => $profile, 'params' => $appParams ] );

			if ( $showArticleInfo == 1 || $showArticleInfo == 3 )
			{
				$infoHtmlTop = $html;
			}

			if ( $showArticleInfo == 2 || $showArticleInfo == 3 )
			{
				$infoHtmlBottom = $html;
			}
		}
		//*********************** END AUTHOR INFO *********************************//

		//************************ ARTICLE IMAGES *********************************//
		$images     = json_decode( $article->images );
		$imagesHtml = '';
		if ( isset( $images->image_fulltext ) && ! empty( $images->image_fulltext ) && $appParams->get( 'show_fulltext_image' ) )
		{
			$imgfloat   = ( empty( $images->float_fulltext ) ) ? $params->get( 'float_fulltext' ) : $images->float_fulltext;
			$imagesHtml = $imagesHtml . '<div class="pull-' . htmlspecialchars( $imgfloat ) . ' item-image"> <img ';
			if ( $images->image_fulltext_caption )
			{
				$imagesHtml = $imagesHtml . 'class="caption"' . ' title="' . htmlspecialchars( $images->image_fulltext_caption ) . '"';
			}

			$imagesHtml      = $imagesHtml . 'src="' . htmlspecialchars( $images->image_fulltext ) . '" alt="' . htmlspecialchars( $images->image_fulltext_alt )
			                   . '" itemprop="image"/> </div>';
			$article->images = null;
		}
		//********************** END ARTICLE IMAGES *******************************//

		//************************* ARTICLE TAGS **********************************//
		$info     = $params->get( 'info_block_position', 0 );
		$tagsHtml = '';
		if ( $info == 0 && $params->get( 'show_tags', 1 ) && ! empty( $article->tags->itemTags ) )
		{
			$tagLayout     = new JLayoutFile( 'joomla.content.tags' );
			$tagsHtml      = $tagLayout->render( $article->tags->itemTags );
			$article->tags = null;
		}
		//*********************** END ARTICLE TAGS ********************************//

		$article->text = '<div id="cj-wrapper">' .
		                 $toolbarHtml .
		                 $titleHtml .
		                 $tagsHtml .
		                 $imagesHtml .
		                 $infoHtmlTop .
		                 $article->text .
		                 $infoHtmlBottom .
		                 $socialSharingHtml .
		                 '</div>';
	}

	public function onContentBeforeSave( $context, $article, $isNew ) {
		if ( $context != 'com_content.form' )
		{
			return true;
		}

		$user = JFactory::getUser();
		if ( ! $user->authorise( 'core.autoapprove', 'com_cjblog' ) )
		{
			$article->state = 0;
		}
		else
		{
			$article->state = 1;
		}

		return true;
	}

	public function onContentAfterSave( $context, $article, $isNew ) {
		if ( $context != 'com_content.form' )
		{
			return true;
		}

		$user = JFactory::getUser();
		$db   = JFactory::getDbo();

		if ( ! $isNew || $user->authorise( 'core.autoapprove', 'com_cjblog' ) )
		{
			return true;
		}

		try
		{
			require_once JPATH_ROOT . '/components/com_cjlib/framework.php';
			require_once JPATH_ROOT . '/components/com_cjlib/framework/api.php';
			CJLib::import( 'corejoomla.framework.core' );
			$this->loadLanguage( 'com_cjblog', JPATH_ROOT );

			$record             = new stdClass();
			$record->id         = $article->id;
			$record->published  = $user->authorise( 'core.autoapprove', 'com_cjblog' ) ? $article->state : 3;
			$record->secret_key = CjLibUtils::getRandomKey( 32 );

			$db->insertObject( '#__cjblog_reviews', $record );

			// Award points
			$params    = JComponentHelper::getParams( 'com_cjblog' );
			$pointsApp = $params->get( 'points_component', 'none' );

			if ( $pointsApp == 'cjblog' )
			{
				$api     = new CjLibApi();
				$options = [
					'function'  => 'com_content.create',
					'reference' => $article->id,
					'info'      => CjLibUtils::substrws( $article->text, 256 ),
					'component' => 'com_content',
					'title'     => JText::sprintf( 'COM_CJBLOG_POINTS_NEW_ARTICLE', $article->title ),
				];
				$api->awardPoints( $pointsApp, $article->created_by, $options );
			}

			// Send emails
			$template = null;
			$tag      = JFactory::getLanguage()->getTag();

			$query = $db->getQuery( true )
			            ->select( 'title, description, language' )
			            ->from( '#__cjblog_email_templates' )
			            ->where( 'email_type = ' . $db->q( 'com_cjblog.approval' ) )
			            ->where( 'language in (' . $db->q( $tag ) . ',' . $db->q( '*' ) . ')' )
			            ->where( 'published = 1' );

			$db->setQuery( $query );
			$templates = $db->loadObjectList( 'language' );

			if ( isset( $templates[$tag] ) )
			{
				$template = $templates[$tag];
			}
			elseif ( isset( $templates['*'] ) )
			{
				$template = $templates['*'];
			}

			if ( ! empty( $template ) )
			{
				JLoader::import( 'mail', JPATH_ROOT . '/components/com_cjblog/models' );

				$user      = JFactory::getUser();
				$config    = JFactory::getConfig();
				$sitename  = $config->get( 'sitename' );
				$message   = new stdClass();
				$mailModel = JModelLegacy::getInstance( 'mail', 'CjBlogModel' );

				$article->slug    = $article->alias ? ( $article->id . ':' . $article->alias ) : $article->id;
				$article->catslug = ! empty( $article->category_alias ) ? ( $article->catid . ':' . $article->category_alias ) : $article->catid;
				$approvalUrl      = CjBlogHelperRoute::getApprovalRoute( $article->id, true, $key );
				$disapprovalUrl   = CjBlogHelperRoute::getApprovalRoute( $article->id, false, $key );
				$articleUrl       = JRoute::_( ContentHelperRoute::getArticleRoute( $article->slug, $article->catslug ), false, - 1 );

				$recipients  = [];
				$subject     = str_ireplace( '{ARTICLE_TITLE}', $article->title, $template->title );
				$description = str_ireplace( '{SITENAME}', $sitename, $template->description );
				$description = str_ireplace( '{ARTICLE_TITLE}', $article->title, $description );
				$description = str_ireplace( '{ARTICLE_URL}', $articleUrl, $description );
				$description = str_ireplace( '{CATEGORY}', $article->category_title, $description );
				$description = str_ireplace( '{AUTHOR_NAME}', $user->$displayName, $description );
				$description = str_ireplace( '{APPROVAL_URL}', $approvalUrl, $description );
				$description = str_ireplace( '{DISAPPROVAL_URL}', $disapprovalUrl, $description );

				if ( ! empty( $recipients ) && ! empty( $message ) )
				{
					$message->asset_name  = $emailType;
					$message->subject     = $subject;
					$message->description = $description;

					$mailModel->enqueueMail( $message, $recipients, 'none' );
				}
			}
		}
		catch ( Exception $e )
		{
			return false;
		}

		return true;
	}

	public function onContentChangeState( $context, $pks, $value ) {
		if ( $context != 'com_content.article' )
		{
			return;
		}

		// sync users
		$db      = JFactory::getDbo();
		$userIds = [];
		$pks     = Joomla\Utilities\ArrayHelper::toInteger( $pks );

		try
		{
			$query = $db->getQuery( true )
			            ->select( 'a.created_by' )
			            ->from( '#__content AS a' )
			            ->where( 'a.id IN (' . implode( ',', $pks ) . ')' );
			$db->setQuery( $query );
			$userIds = $db->loadColumn();
		}
		catch ( Exception $e )
		{
			return;
		}

		try
		{
			$query = 'insert into
					#__cjblog_users (id, handle, points)
					(
						select
							u.id, replace(u.username, \'-\', \'_\'), sum(p.points) as points
						from
							#__users AS u
						left join
							#__cjblog_points AS p on p.user_id = u.id
						where
							u.id IN (' . implode( ',', $userIds ) . ')
						group by u.id
					)
				 on duplicate key
    				update id = values(id)';

			$db->setQuery( $query );
			$db->execute();
		}
		catch ( Exception $e )
		{
			// 			throw new Exception($e);
		}

		// now update
		try
		{
			$query = $db->getQuery( true )
			            ->update( '#__cjblog_users AS u' )
			            ->set( 'points = (select sum(p.points) from #__cjblog_points AS p where p.user_id = u.id group by p.user_id)' )
			            ->set( 'num_articles = (select count(*) from #__content AS t where t.created_by = u.id and t.state = 1 group by t.created_by)' )
			            ->where( 'u.id IN (' . implode( ',', $userIds ) . ')' );

			$db->setQuery( $query );
			$db->execute();
		}
		catch ( Exception $e )
		{
			// 			throw new Exception($e);
		}

		return false;
	}

}

