<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogControllerRating extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	public function execute($task)
	{
		try 
		{
			$user = JFactory::getUser();
			if($user->guest)
			{
				throw new Exception(JText::_('COM_CJBLOG_ERROR_LOGIN_TO_EXECUTE'), 403);
			}

			$pk = $this->input->getInt('cid', 0);
			switch ($task)
			{
				case 'tlike':
					$this->likeOrDislike($pk, $pk, 1, ITEM_TYPE_ARTICLE);
					break;
			
				case 'tdislike':
					$this->likeOrDislike($pk, $pk, 0, ITEM_TYPE_ARTICLE);
					break;
					
				case 'rlike':
					$articleId = $this->input->getInt('t_id', 0);
					$this->likeOrDislike($pk, $articleId, 1, ITEM_TYPE_REPLY);
					break;
			
				case 'rdislike':
					$articleId = $this->input->getInt('t_id', 0);
					$this->likeOrDislike($pk, $articleId, 0, ITEM_TYPE_REPLY);
					break;
			}
		}
		catch (Exception $e)
		{
			echo new JResponseJson($e);
		}
	}
	
	public function likeOrDislike($pk, $articleId, $state, $type)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('Ratings');
		$karma = $model->like($pk, $articleId, $state, $type);
		
		if($karma !== false)
		{
			$message = $state ? JText::plural('COM_CJBLOG_NUM_LIKES', $karma) : JText::plural('COM_CJBLOG_NUM_DISLIKES', $karma);
			echo new JResponseJson($message);
		}
		else
		{
			throw new Exception(JText::_('COM_CJBLOG_ERROR_PERFORMING_ACTION'), 500);
		}
	}
}
