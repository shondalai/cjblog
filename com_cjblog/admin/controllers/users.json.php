<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogControllerUsers extends JControllerAdmin
{
	protected $text_prefix = 'COM_CJBLOG';
	
	public function __construct ($config = array())
	{
		parent::__construct($config);
	}

	public function execute ($task)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$model = $this->getModel();
		
		if(!$user->authorise('core.admin'))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}
		
		$startId = $app->input->getInt('startId', 0);
		$endId = $app->input->getInt('endId', 0);
		$lastId = $app->input->getInt('lastId', 0);
		
		try 
		{
			if($startId == 0 && $endId <= 0)
			{
				$result = $model->getFirstAndLastUserId();
				
				if(empty($result->min_id) || empty($result->max_id))
				{
					throw new Exception(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 500);
				}
				else 
				{
					echo new JResponseJson($result, "<i class='fa fa-spinner fa-spin'></i> Syncing data now. This may take some time depending on the amount of data present.");
				}
			}
			else if(($endId <= $lastId + 250) && $model->syncUsers($startId, $endId))
			{
				echo new JResponseJson(-1, "<i class='fa fa-spinner fa-spin'></i> Syncing data now. This may take some time depending on the amount of data present.");
			}
			else 
			{
				throw new Exception(JText::_('COM_CJBLOG_OPERATION_SUCCESSFULLY_COMPLETED'));
			}
		}
		catch(Exception $e)
		{
			echo new JResponseJson($e);
		}
	}

	public function getModel ($name = 'Users', $prefix = 'CjBlogModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}