<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

class CjBlogController extends JControllerLegacy {

	protected $default_view = 'dashboard';

	public function display( $cachable = false, $urlparams = false ) {
		$document   = JFactory::getDocument();
		$custom_tag = true;
		$params     = JComponentHelper::getParams( 'com_cjblog' );
		$loadBsCss  = $params->get( 'load_bootstrap_css', false );
		$vName      = $this->input->getCmd( 'view', 'categories' );

		if ( CJBLOG_MAJOR_VERSION < 4 )
		{
			CjLib::behavior( 'bootstrap', [ 'loadcss' => $loadBsCss ] );
			CjScript::_( 'fontawesome', [ 'custom' => $custom_tag ] );
			JHtml::_( 'script', 'system/core.js', false, true );

			if ( $params->get( 'ui_layout', 'default' ) == 'default' )
			{
				CJLib::behavior( 'bscore', [ 'customtag' => $custom_tag ] );
			}
			if ( $vName == 'form' )
			{
				JHtml::_( 'behavior.framework' );
			}
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

		parent::display();

		return $this;
	}

}
