<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogController extends JControllerLegacy
{

	protected $default_view = 'dashboard';

	public function display ($cachable = false, $urlparams = false)
	{
		$view = $this->input->get('view', 'dashboard');
		$layout = $this->input->get('layout');
		$id = $this->input->getInt('id');
		
		CjScript::_('fontawesome');
		parent::display();
		
		return $this;
	}
}
