<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_PLATFORM') or die();

class CjBlogTableEmailTemplates extends JTable
{
	public function __construct (JDatabaseDriver $db)
	{
		parent::__construct('#__cjblog_email_templates', 'id', $db);
	}
}
