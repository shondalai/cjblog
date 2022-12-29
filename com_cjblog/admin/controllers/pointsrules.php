<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogControllerPointsrules extends JControllerAdmin
{
	protected $text_prefix = 'COM_CJBLOG';
	
	public function __construct ($config = array())
	{
		parent::__construct($config);
	}

	public function getModel ($name = 'Pointsrule', $prefix = 'CjBlogModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		
		return $model;
	}

	protected function postDeleteHook (JModelLegacy $model, $ids = null)
	{
	}
	
	public function scan()
	{
		$model = $this->getModel();
		$model->scanPointsRules();
		$this->setRedirect(JRoute::_('index.php?option=com_cjblog&view=pointsrules', false), JText::_('COM_CJBLOG_OPERATION_SUCCESSFULLY_COMPLETED'));
	}
}