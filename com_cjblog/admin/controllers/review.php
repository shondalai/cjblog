<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogControllerReview extends JControllerForm
{
	public function __construct ($config = array())
	{
		parent::__construct($config);
	}
	
	protected function postSaveHook (JModelLegacy $model, $validData = array())
	{
		return;
	}
}
