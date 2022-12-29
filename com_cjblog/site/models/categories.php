<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Categories\Categories;

defined( '_JEXEC' ) or die;

if ( CJBLOG_MAJOR_VERSION < 4 )
{
	require_once JPATH_ROOT . '/components/com_content/models/categories.php';

	class CjBlogModelCategories extends ContentModelCategories {

		public $_context = 'com_cjblog.categories';

	}
}
else
{
	class CjBlogModelCategories extends \Joomla\Component\Content\Site\Model\CategoriesModel {

		public $_context = 'com_cjblog.categories';
	}
}
