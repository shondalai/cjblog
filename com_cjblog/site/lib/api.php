<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT.'/components/com_cjblog/helpers/constants.php';

class CjBlogApi
{
	public static function getPointsApi($options = null)
	{
		require_once JPATH_ROOT.'/components/com_cjblog/lib/points.php';
		
		$date = JFactory::getDate()->format('Y.m.d');
		JLog::addLogger(array('text_file' => 'com_cjblog'.'.'.$date.'.log.php'), JLog::ALL, 'com_cjblog');
		
		$pointsApi = new CjBlogPointsApi($options);
		
		return $pointsApi;
	}
	
	public static function getProfileApi($options = null)
	{
		require_once JPATH_ROOT.'/components/com_cjblog/lib/profile.php';
		
		$date = JFactory::getDate()->format('Y.m.d');
		JLog::addLogger(array('text_file' => 'com_cjblog'.'.'.$date.'.log.php'), JLog::ALL, 'com_cjblog');
		
		$profileApi = new CjBlogProfileApi($options);
		
		return $profileApi;
	}

	public static function getBadgesApi($options = null)
	{
		require_once JPATH_ROOT.'/components/com_cjblog/lib/badge.php';
	
		jimport('joomla.log.log');
		$date = JFactory::getDate()->format('Y.m.d');
		JLog::addLogger(array('text_file' => 'com_cjblog'.'.'.$date.'.log.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE}'), JLog::ALL, 'com_cjblog');
		
		$badgeApi = new CjBlogBadgeApi($options);
		
		return $badgeApi;
	}
	
	public static function checkMessages($userId)
	{
		$count = 0;
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('count(*)')
			->from('#__cjblog_messages_map')
			->where('receiver_id = '.$userId.' and receiver_state = 0');
		
		$db->setQuery($query);
		
		try
		{
			$count = $db->loadResult();
		}
		catch (Exception $e)
		{
			JLog::add('CjBlogApi.check_messages - DB Error: '.$db->getErrorMsg(), JLog::ERROR, 'com_cjblog');
		}
		
		return $count;
	}
	
	public static function getActivityDate($strdate)
	{
		if(empty($strdate) || $strdate == '0000-00-00 00:00:00')
		{
			return JText::_('LBL_NA');
		}
	
		jimport('joomla.utilities.date');
		$user = JFactory::getUser();
	
		// Given time
		$date = new JDate(JHtml::date($strdate, 'Y-m-d H:i:s'));
		$compareTo = new JDate(JHtml::date('now', 'Y-m-d H:i:s'));
		$diff = $compareTo->toUnix() - $date->toUnix();
	
		$diff = abs($diff);
		$dayDiff = floor($diff/86400);
	
		if($dayDiff == 0)
		{
			if($diff < 3600)
			{
				return JText::sprintf('COM_CJBLOG_DATE_FORMAT_MINUTES', floor($diff/60));
			}
			else
			{
				return JText::sprintf('COM_CJBLOG_DATE_FORMAT_HOURS', floor($diff/3600));
			}
		} else
		{
			return $date->format(JText::_('COM_CJBLOG_DATE_FORMAT_FULL_DATE', false, false));
		}
	}
}