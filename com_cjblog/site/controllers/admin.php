<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogControllerAdmin extends JControllerAdmin
{
	protected $view_list = 'categories';
	
	public function __construct ($config = array())
	{
		parent::__construct($config);
		
		if ($this->input->get('view') == 'featured')
		{
			$this->view_list = 'featured';
		}
		
		$this->registerTask('unfeatured', 'featured');
	}

	public function featured ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$user = JFactory::getUser();
		$ids = $this->input->get('cid', array(), 'array');
		$values = array(
				'featured' => 1,
				'unfeatured' => 0
		);
		$task = $this->getTask();
		$value = \Joomla\Utilities\ArrayHelper::getValue($values, $task, 0, 'int');
		
		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (! $user->authorise('core.edit.state', 'com_cjblog.article.' . (int) $id))
			{
				// Prune items that you can't change.
				unset($ids[$i]);
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 403);
			}
		}
		
		if (empty($ids))
		{
		    throw new Exception(JText::_('JERROR_NO_ITEMS_SELECTED'), 400);
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
			
			// Publish the items.
			if (! $model->featured($ids, $value))
			{
			    throw new Exception($model->getError(), 500);
			}
		}
		
		$this->setRedirect('index.php?option=com_cjblog&view=articles');
	}

	public function getModel ($name = 'Form', $prefix = 'CjBlogModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		
		return $model;
	}

	protected function postDeleteHook (JModelLegacy $model, $ids = null)
	{
	}
}