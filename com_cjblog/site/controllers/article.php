<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

if ( CJBLOG_MAJOR_VERSION < 4 )
{
	require_once JPATH_ROOT . '/components/com_content/controllers/article.php';

	class CjBlogArticleController extends ContentControllerArticle {

		public function __construct( $config = [] ) {
			parent::__construct( $config );
		}

	}
}
else
{
	class CjBlogArticleController extends \Joomla\Component\Content\Site\Controller\ArticleController {

		public function __construct( $config = [] ) {
			parent::__construct( $config );
		}

	}
}

class CjBlogControllerArticle extends CjBlogArticleController {

	public function __construct( $config = [] ) {
		parent::__construct( $config );

		JModelLegacy::addIncludePath( JPATH_ROOT . '/components/com_content/models/' );
	}

	public function add() {
		if ( parent::add() )
		{
			// send redirect to cjblog article form or joomla article form
		}
	}

	public function edit( $key = null, $urlVar = 'a_id' ) {
		$result = parent::edit( $key, $urlVar );

		if ( ! $result )
		{
			// redirect to cjblog article edit page or joomla article edit page
		}

		return $result;
	}

	public function getModel( $name = 'Form', $prefix = 'ContentModel', $config = [ 'ignore_request' => true ] ) {
		$model = parent::getModel( $name, $prefix, $config );

		return $model;
	}

	public function save( $key = null, $urlVar = 'a_id' ) {
		$result = parent::save( $key, $urlVar );

		// If ok, redirect to the return page.
		if ( $result )
		{
			// redirect to CjBlog articles/article page
		}

		return $result;
	}

	public function approve() {
		$this->approval( 1 );
	}

	public function disapprove() {
		$this->approval( 0 );
	}

	private function approval( $status ) {
		JFactory::getApplication();
		$secret = $app->input->getCmd( 'key' );
		if ( empty( $secret ) )
		{
			throw new Exception( JText::_( 'JERROR_ALERTNOAUTHOR' ), 403 );
		}

		$model  = $this->getModel( 'article' );
		$result = $model->approve( $status, $key );

		if ( ! $result )
		{
			$app->enqueueMessage( JText::_( 'COM_CJBLOG_APPROVAL_FAILED' ) );
		}
		else
		{
			$app->enqueueMessage( JText::_( 'COM_CJBLOG_APPROVAL_SUCCESS' ) );
		}
	}

}
