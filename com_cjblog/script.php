<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined( '_JEXEC' ) or die();

class com_cjblogInstallerScript {

	function install( $parent ) {
		// $parent is the class calling this method
		$parent->getParent()->setRedirectURL( 'index.php?option=com_cjblog' );
	}

	function uninstall( $parent ) {
		// $parent is the class calling this method
		echo '<p>' . JText::_( 'COM_CJBLOG_UNINSTALL_TEXT' ) . '</p>';
	}

	function update( $parent ) {
		require_once JPATH_ROOT . '/components/com_cjblog/helpers/constants.php';
		JFolder::create( CJBLOG_MEDIA_DIR . 'thumbnails' );

		// $parent is the class calling this method
		echo '<p>' . JText::_( 'COM_CJBLOG_UPDATE_TEXT' ) . '</p>';
		$parent->getParent()->setRedirectURL( 'index.php?option=com_cjblog&view=dashboard' );
	}

	function preflight( $type, $parent ) {
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_( 'COM_CJBLOG_PREFLIGHT_' . $type . '_TEXT' ) . '</p>';
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight( $type, $parent ) {
		echo "<b><font color=\"red\">Database tables successfully migrated to the latest version. Please check the configuration options once again.</font></b>";
	}

}