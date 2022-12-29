<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  plg_content_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class plgUserCjBlog extends JPlugin
{
	public function onUserLogin($user, $options)
	{
		$app = JFactory::getApplication();
		if ($app->isClient('administrator')) return true;
		
		if( ! file_exists(JPATH_ROOT.'/components/com_cjblog/lib/api.php') )
		{
			return true;
		}
		
		require_once JPATH_ROOT.'/components/com_cjblog/lib/api.php';
		$userid = intval(JUserHelper::getUserId($user['username']));
		$api = CjBlogApi::getPointsApi();
		$api->awardPoints('com_users.login', $userid, 0, date('Ymd'), date('F j, Y, g:i a'));
		
		return true;
	}
	
	public function onUserAfterSave($user, $isnew, $success, $error)
	{
		if($isnew && $success)
		{
			if( ! file_exists(JPATH_ROOT.'/components/com_cjblog/lib/api.php') )
			{
				return true;
			}
			
			$userid = intval(JUserHelper::getUserId($user['username']));
			if($userid > 0)
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->insert('#__cjblog_users')
					->columns('id, num_articles, handle')
					->values($userid.',0,'.$db->q($user['username']));
				
				$db->setQuery($query);
				$db->execute();

				require_once JPATH_ROOT.'/components/com_cjblog/lib/api.php';
				$api = CjBlogApi::getPointsApi();
				$api->awardPoints('com_users.signup', $userid, 0, $userid, date('F j, Y, g:i a'));
			}
		}
				
		return true;
	}
}
