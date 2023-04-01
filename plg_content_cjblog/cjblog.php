<?php

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

		if ( file( JPATH_ROOT . '/components/com_cjlib/framework.php' ) )
		{
			require_once JPATH_ROOT . '/components/com_cjlib/framework.php';
			CJLib::import( 'corejoomla.framework.core' );
			$this->loadLanguage( 'com_cjblog', JPATH_ROOT );
		}
	}

	public function onContentPrepare( $context, &$article, &$params, $page = 0 ) {
		$app = JFactory::getApplication();
		if (
			$context != 'com_content.article'
			|| $app->isClient( 'administrator' )
			|| $app->isClient( 'api' )
			|| ! $this->params->get( 'use_cjblog_content', 1 )
			|| ! file_exists( JPATH_ROOT . '/components/com_cjlib/framework.php' )
		)
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

		$document = JFactory::getDocument();
		if ( CJBLOG_MAJOR_VERSION < 4 )
		{
			CjScript::_( 'fontawesome', [ 'custom' => $custom_tag ] );
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
		elseif ( $user->authorise( 'core.edit', 'com_content.article.' . $article->id ) )
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

	public function onContentAfterSave( $context, $article, $isNew ) {
		if ( $context != 'com_content.form' || ! file_exists( JPATH_ROOT . '/components/com_cjlib/framework.php' ) )
		{
			return true;
		}

		$user = JFactory::getUser();
		$db   = JFactory::getDbo();

		if ( ! $isNew || $article->state != 1 )
		{
			return true;
		}

		try
		{
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
		}
		catch ( RuntimeException $e )
		{
			return false;
		}

		return true;
	}

	public function onContentChangeState( $context, $pks, $value ) {
		if ( $context != 'com_content.article' || ! file_exists( JPATH_ROOT . '/components/com_cjlib/framework.php' ) )
		{
			return true;
		}

		// sync users
		$db       = JFactory::getDbo();
		$articles = [];
		$pks      = Joomla\Utilities\ArrayHelper::toInteger( $pks );

		try
		{
			$query = $db->getQuery( true )
			            ->select( 'a.id, a.created_by, a.title' )
			            ->from( '#__content AS a' )
			            ->where( 'a.id IN (' . implode( ',', $pks ) . ')' );
			$db->setQuery( $query );
			$articles = $db->loadObjectList();
		}
		catch ( RuntimeException $e )
		{
			return false;
		}

		if ( empty( $articles ) )
		{
			return true;
		}

		try
		{
			$userIds  = array_column( $articles, 'created_by' );
			$subQuery = $db->getQuery( true )
			               ->select( "u.id, replace(u.username, '-', '_'), IFNULL(sum(p.points), 0) as points" )
			               ->from( "#__users AS u" )
			               ->join( "left", "#__cjblog_points AS p", "p.user_id = u.id" )
			               ->where( "u.id IN (" . implode( ',', $userIds ) . ")" )
			               ->group( "u.id" );
			$query    = 'insert into #__cjblog_users (id, handle, points) ' . $subQuery->__toString() . ' on duplicate key update id = values(id)';
			$db->setQuery( $query );
			$db->execute();
		}
		catch ( RuntimeException $e )
		{
			return false;
		}

		// now update
		try
		{
			$query = $db->getQuery( true )
			            ->update( '#__cjblog_users AS u' )
			            ->set( 'u.points = IFNULL((select sum(p.points) from #__cjblog_points AS p where p.user_id = u.id group by p.user_id), 0)' )
			            ->set( 'u.num_articles = IFNULL((select count(*) from #__content AS t where t.created_by = u.id and t.state = 1 group by t.created_by), 0)' )
			            ->where( 'u.id IN (' . implode( ',', $userIds ) . ')' );

			$db->setQuery( $query );
			$db->execute();
		}
		catch ( RuntimeException $e )
		{
			return false;
		}

		// Award points
		$params    = JComponentHelper::getParams( 'com_cjblog' );
		$pointsApp = $params->get( 'points_component', 'none' );

		if ( $pointsApp == 'cjblog' )
		{
			$api = new CjLibApi();
			foreach ( $articles as $article )
			{
				$options = [
					'function'  => $value == 1 ? 'com_content.create' : 'com_content.delete',
					'reference' => $article->id,
					'info'      => CjLibUtils::substrws( $article->text, 256 ),
					'component' => 'com_content',
					'title'     => JText::sprintf( $value == 1 ? 'COM_CJBLOG_POINTS_NEW_ARTICLE' : 'COM_CJBLOG_POINTS_DELETED_ARTICLE', $article->title ),
				];
				$api->awardPoints( $pointsApp, $article->created_by, $options );
			}
		}

		return true;
	}

}

