<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogControllerBadgeRule extends JControllerForm
{
	public function __construct ($config = array())
	{
		parent::__construct($config);
	}

	protected function postSaveHook (JModelLegacy $model, $validData = array())
	{
		return;
	}
	
	public function add()
	{
		$app = JFactory::getApplication();
		$assetId = $app->input->getString('asset');
		
		if(empty($assetId))
		{
			$this->setRedirect(JRoute::_('index.php?option=com_cjblog&view=badgetemplates', false, -1));
			return true;
		}
		
		return parent::add();
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$app = $this->input->get('app', '', 'string');
		$asset = $this->input->get('asset', '', 'string');
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		
		if ($app)
		{
			$append .= '&app=' . $app;
		}
		
		if ($asset)
		{
			$append .= '&asset=' . $asset;
		}
	
		return $append;
	}
}
