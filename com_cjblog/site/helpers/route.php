<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

abstract class CjBlogHelperRoute {

	protected static $lookup = [];

	protected static $lang_lookup = [];

	public static function getProfileRoute( $handle = 0, $language = 0 ) {
		// 		$handle = JFilterOutput::stringURLUnicodeSlug($handle);
		$needles = [ 'profile' => [ 0 ] ];
		$link    = 'index.php?option=com_cjblog&view=profile&id=' . $handle;

		if ( $language && $language != "*" && JLanguageMultilang::isEnabled() )
		{
			self::buildLanguageLookup();

			if ( isset( self::$lang_lookup[$language] ) )
			{
				$link                .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		if ( $item = self::_findItem( $needles ) )
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	public static function getCategoryRoute( $catid = 'root', $language = 0 ) {
		if ( $catid instanceof JCategoryNode )
		{
			$id = $catid->id;
		}
		else
		{
			$id = (int) $catid;
		}

		if ( ! $catid instanceof JCategoryNode )
		{
			$catid = JCategories::getInstance( 'Content' )->get( $catid );
		}

		if ( ! $catid instanceof JCategoryNode )
		{
			$link = 'index.php?option=com_cjblog';
		}
		else
		{
			$needles = [];
			$link    = 'index.php?option=com_cjblog';
			$link    = $id ? $link . '&view=category&id=' . $id : $link . '&view=categories&id=0';

			$catids                = array_reverse( $catid->getPath() );
			$needles['category']   = $catids;
			$needles['categories'] = [ 0, $catids ];

			if ( $language && $language != "*" && JLanguageMultilang::isEnabled() )
			{
				$link                .= '&lang=' . $language;
				$needles['language'] = $language;
			}

			if ( $item = self::_findItem( $needles ) )
			{
				$link .= '&Itemid=' . $item;
			}
		}

		return $link;
	}

	public static function getFormRoute( $id = 0, $catid = 0 ) {
		// Create the link
		if ( $id )
		{
			$link = 'index.php?option=com_content&task=article.edit&t_id=' . $id;
		}
		else
		{
			$link = 'index.php?option=com_content&task=article.add';
		}

		if ( $catid )
		{
			$link = $link . '&catid=' . $catid;
		}

		return $link;
	}

	public static function getCategoriesRoute( $id = 0, $language = 0 ) {
		$needles = [ 'categories' => [ $id ] ];
		$link    = 'index.php?option=com_cjblog&view=categories';

		if ( $language && $language != "*" && JLanguageMultilang::isEnabled() )
		{
			self::buildLanguageLookup();

			if ( isset( self::$lang_lookup[$language] ) )
			{
				$link                .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		if ( $item = self::_findItem( $needles ) )
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	public static function getArticlesRoute( $id = null, $language = 0 ) {
		$needles = [ 'articles' => [ 0 ] ];
		$link    = 'index.php?option=com_cjblog&view=articles';

		if ( $language && $language != "*" && JLanguageMultilang::isEnabled() )
		{
			self::buildLanguageLookup();

			if ( isset( self::$lang_lookup[$language] ) )
			{
				$link                .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		if ( $item = self::_findItem( $needles ) )
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	public static function getApprovalRoute( $id = null, $status = 1, $key = 0 ) {
		$needles = [ 'articles' => [ 0 ] ];
		$link    = 'index.php?option=com_cjblog';

		if ( $status )
		{
			$link .= '&task=article.approve&key=' . $key;
		}
		else
		{
			$link .= '&task=article.disapprove&key=' . $key;
		}

		if ( $language && $language != "*" && JLanguageMultilang::isEnabled() )
		{
			self::buildLanguageLookup();

			if ( isset( self::$lang_lookup[$language] ) )
			{
				$link                .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		if ( $item = self::_findItem( $needles ) )
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	public static function getLeaderBoardRoute( $id = null, $language = 0 ) {
		$needles = [ 'leaderboard' => [ 0 ] ];
		$link    = 'index.php?option=com_cjblog&view=leaderboard';

		if ( $language && $language != "*" && JLanguageMultilang::isEnabled() )
		{
			self::buildLanguageLookup();

			if ( isset( self::$lang_lookup[$language] ) )
			{
				$link                .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		if ( $item = self::_findItem( $needles ) )
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	public static function getSearchRoute( $id = null, $language = 0 ) {
		$needles = [ 'search' => [ 0 ] ];
		$link    = 'index.php?option=com_cjblog&view=search';

		if ( $language && $language != "*" && JLanguageMultilang::isEnabled() )
		{
			self::buildLanguageLookup();

			if ( isset( self::$lang_lookup[$language] ) )
			{
				$link                .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		if ( $item = self::_findItem( $needles ) )
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	public static function getUsersRoute( $id = null, $language = 0 ) {
		$needles = [ 'users' => [ 0 ] ];
		$link    = 'index.php?option=com_cjblog&view=users';

		if ( $language && $language != "*" && JLanguageMultilang::isEnabled() )
		{
			self::buildLanguageLookup();

			if ( isset( self::$lang_lookup[$language] ) )
			{
				$link                .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		if ( $item = self::_findItem( $needles ) )
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	protected static function buildLanguageLookup() {
		if ( count( self::$lang_lookup ) == 0 )
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery( true )
			            ->select( 'a.sef AS sef' )
			            ->select( 'a.lang_code AS lang_code' )
			            ->from( '#__languages AS a' );

			$db->setQuery( $query );
			$langs = $db->loadObjectList();

			foreach ( $langs as $lang )
			{
				self::$lang_lookup[$lang->lang_code] = $lang->sef;
			}
		}
	}

	protected static function _findItem( $needles = null ) {
		$app      = JFactory::getApplication();
		$menus    = $app->getMenu( 'site' );
		$language = isset( $needles['language'] ) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if ( ! isset( self::$lookup[$language] ) )
		{
			self::$lookup[$language] = [];
			$component               = JComponentHelper::getComponent( 'com_cjblog' );

			$attributes = [ 'component_id' ];
			$values     = [ $component->id ];

			if ( $language != '*' )
			{
				$attributes[] = 'language';
				$values[]     = [ $needles['language'], '*' ];
			}

			$items = $menus->getItems( $attributes, $values );

			foreach ( $items as $item )
			{
				if ( isset( $item->query ) && isset( $item->query['view'] ) )
				{
					$view = $item->query['view'];

					if ( ! isset( self::$lookup[$language][$view] ) )
					{
						self::$lookup[$language][$view] = [];
					}

					if ( isset( $item->query['id'] ) )
					{
						/**
						 * Here it will become a bit tricky
						 * language != * can override existing entries
						 * language == * cannot override existing entries
						 */
						if ( ! isset( self::$lookup[$language][$view][$item->query['id']] ) || $item->language != '*' )
						{
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}
					}
					elseif ( in_array( $view, [ 'profile', 'articles', 'categories', 'leaderboard', 'users', 'search' ] ) )
					{
						if ( ! isset( self::$lookup[$language][$view][0] ) || $item->language != '*' )
						{
							self::$lookup[$language][$view][0] = $item->id;
						}
					}
				}
			}
		}

		if ( $needles )
		{
			foreach ( $needles as $view => $ids )
			{
				if ( isset( self::$lookup[$language][$view] ) )
				{
					foreach ( $ids as $id )
					{
						if ( isset( self::$lookup[$language][$view][(int) $id] ) )
						{
							return self::$lookup[$language][$view][(int) $id];
						}
					}
				}
			}
		}

		// Check if the active menuitem matches the requested language
		$active = $menus->getActive();

		if ( $active && $active->component == 'com_cjblog' && ( $language == '*' || in_array( $active->language, [ '*', $language ] ) || ! JLanguageMultilang::isEnabled() ) )
		{
			return $active->id;
		}

		// If not found, return language specific home link
		$default = $menus->getDefault( $language );

		return ! empty( $default->id ) ? $default->id : null;
	}

}
