<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogCategories extends JCategories
{

	public function __construct ($options = array())
	{
		$options['table'] = '#__cjblog_articles';
		$options['extension'] = 'com_cjblog';
		
		parent::__construct($options);
	}
}
