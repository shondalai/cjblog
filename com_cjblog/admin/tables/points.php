<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( 'JPATH_PLATFORM' ) or die();

class CjBlogTablePoints extends JTable {

	public function __construct( JDatabaseDriver $db ) {
		parent::__construct( '#__cjblog_points', 'id', $db );
	}

	public function check() {
		if ( empty( $this->publish_down ) )
		{
			$this->publish_down = $this->_db->getNullDate();
		}

		if ( empty( $this->publish_up ) )
		{
			$this->publish_up = $this->_db->getNullDate();
		}

		if ( $this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up )
		{
			// Swap the dates.
			$temp               = $this->publish_up;
			$this->publish_up   = $this->publish_down;
			$this->publish_down = $temp;
		}

		return parent::check();
	}

}
