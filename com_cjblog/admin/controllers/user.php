<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogControllerUser extends JControllerForm
{
	public function __construct ($config = array())
	{
		parent::__construct($config);
	}
	
	public function batch ($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Set the model
		$model = $this->getModel('User', '', array());
		
		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_cjblog&view=users' . $this->getRedirectToListAppend(), false));
		
		return parent::batch($model);
	}

	protected function postSaveHook (JModelLegacy $model, $validData = array())
	{
		return;
	}
}
