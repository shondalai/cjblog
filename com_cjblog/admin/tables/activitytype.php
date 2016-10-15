<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_PLATFORM') or die();

class CjBlogTableActivitytype extends JTable
{
	public function __construct (JDatabaseDriver $db)
	{
		parent::__construct('#__cjblog_activity_types', 'id', $db);
	}
}
