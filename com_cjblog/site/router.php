<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;

class CjBlogRouter extends JComponentRouterView {

	protected $noIDs = false;

	/**
	 * CjBlog Component router constructor
	 *
	 * @param   JApplicationCms  $app   The application object
	 * @param   JMenu            $menu  The menu object to work with
	 *
	 * @since 4.0.0
	 */
	public function __construct( $app = null, $menu = null ) {
		$params      = JComponentHelper::getParams( 'com_cjblog' );
		$this->noIDs = (bool) $params->get( 'sef_ids' );

		$categories = new RouterViewConfiguration( 'categories' );
		$categories->setKey( 'id' );
		$this->registerView( $categories );

		$category = new RouterViewConfiguration( 'category' );
		$category->setKey( 'id' )->setParent( $categories, 'catid' )->setNestable();
		$this->registerView( $category );

		$this->registerView( new RouterViewConfiguration( 'articles' ) );
		$this->registerView( new RouterViewConfiguration( 'leaderboard' ) );
		$this->registerView( new RouterViewConfiguration( 'users' ) );
		$this->registerView( new RouterViewConfiguration( 'search' ) );

		parent::__construct( $app, $menu );

		if ( version_compare( JVERSION, '4', 'ge' ) )
		{
			$this->attachRule( new MenuRules( $this ) );
			$this->attachRule( new StandardRules( $this ) );
			$this->attachRule( new NomenuRules( $this ) );
		}
		else
		{
			JLoader::register( 'CjBlogRouterRulesLegacy', __DIR__ . '/helpers/legacyrouter.php' );
			$this->attachRule( new CjBlogRouterRulesLegacy( $this ) );
		}
	}

	/**
	 * Method to get the segment(s) for a category
	 *
	 * @param   string  $id     ID of the category to retrieve the segments for
	 * @param   array   $query  The request that is build right now
	 *
	 * @return  array|string  The segments of this item
	 *
	 * @since 4.0.0
	 */
	public function getCategorySegment( $id, $query ) {
		$category = JCategories::getInstance( 'Content' )->get( $id );
		if ( $category )
		{
			$path    = array_reverse( $category->getPath(), true );
			$path[0] = '1:root';

			if ( $this->noIDs )
			{
				foreach ( $path as &$segment )
				{
					[ $id, $segment ] = explode( ':', $segment, 2 );
				}
			}

			return $path;
		}

		return [];
	}

	/**
	 * Method to get the segment(s) for a category
	 *
	 * @param   string  $id     ID of the category to retrieve the segments for
	 * @param   array   $query  The request that is build right now
	 *
	 * @return  array|string  The segments of this item
	 *
	 * @since 4.0.0
	 */
	public function getCategoriesSegment( $id, $query ) {
		return $this->getCategorySegment( $id, $query );
	}

	/**
	 * Method to get the id for a category
	 *
	 * @param   string  $segment  Segment to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 *
	 * @since 4.0.0
	 */
	public function getCategoryId( $segment, $query ) {
		if ( isset( $query['id'] ) )
		{
			$category = \JCategories::getInstance( 'Content', [ 'access' => false ] )->get( $query['id'] );

			if ( $category )
			{
				foreach ( $category->getChildren() as $child )
				{
					if ( $this->noIDs )
					{
						if ( $child->alias == $segment )
						{
							return $child->id;
						}
					}
					else
					{
						if ( $child->id == (int) $segment )
						{
							return $child->id;
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * Method to get the segment(s) for a category
	 *
	 * @param   string  $segment  Segment to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 *
	 * @since 4.0.0
	 */
	public function getCategoriesId( $segment, $query ) {
		return $this->getCategoryId( $segment, $query );
	}

}