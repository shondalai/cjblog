<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die();

class com_cjblogInstallerScript
{

	function install ($parent)
	{
		// $parent is the class calling this method
		$parent->getParent()->setRedirectURL('index.php?option=com_cjblog');
	}

	function uninstall ($parent)
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_CJBLOG_UNINSTALL_TEXT') . '</p>';
	}

	function update ($parent)
	{
		$db = JFactory::getDBO();
		if (method_exists($parent, 'extension_root'))
		{
			$sqlfile = $parent->getPath('extension_root') . '/sql/install.mysql.utf8.sql';
		}
		else
		{
			$sqlfile = $parent->getParent()->getPath('extension_root') . '/sql/install.mysql.utf8.sql';
		}
		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);
		if ($buffer !== false)
		{
			jimport('joomla.installer.helper');
			$queries = $db->splitSql($buffer);
			if (count($queries) != 0)
			{
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);
						if (! $db->execute())
						{
// 							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
// 							return false;
						}
					}
				}
			}
		}
		
		require_once JPATH_ROOT.'/components/com_cjblog/helpers/constants.php';
		JFolder::create(CJBLOG_MEDIA_DIR.'thumbnails');
		
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_CJBLOG_UPDATE_TEXT') . '</p>';
		$parent->getParent()->setRedirectURL('index.php?option=com_cjblog&view=dashboard');
	}

	function preflight ($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_CJBLOG_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight ($type, $parent)
	{
		$db = JFactory::getDbo();
		$update_queries = array();
		
		$update_queries[] = 'ALTER TABLE `#__cjblog_users`
			ADD COLUMN `handle` VARCHAR(32) NOT NULL,
			ADD COLUMN `birthday` DATE NOT NULL DEFAULT \'0000-00-00\',
			ADD COLUMN `gender` TINYINT(4) UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'default 0 for not specified\',
			ADD COLUMN `location` VARCHAR(50) NULL DEFAULT NULL,
			ADD COLUMN `banned` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
			ADD COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `fans` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
			ADD COLUMN `twitter` VARCHAR(45) NULL DEFAULT NULL,
			ADD COLUMN `facebook` VARCHAR(45) NULL DEFAULT NULL,
			ADD COLUMN `gplus` VARCHAR(45) NULL DEFAULT NULL,
			ADD COLUMN `linkedin` VARCHAR(45) NULL DEFAULT NULL,
			ADD COLUMN `flickr` VARCHAR(45) NULL DEFAULT NULL,
			ADD COLUMN `bebo` VARCHAR(45) NULL DEFAULT NULL,
			ADD COLUMN `skype` VARCHAR(45) NULL DEFAULT NULL,
			ADD COLUMN `metakey` TEXT NULL,
			ADD COLUMN `metadesc` TEXT NULL,
			ADD COLUMN `metadata` TEXT NULL,
			ADD COLUMN `attribs` VARCHAR(5120) NULL DEFAULT NULL';
		
		$update_queries[] = 'ALTER TABLE `#__cjblog_badges`
			ADD COLUMN `access` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
			ADD COLUMN `created_by` INT(10) UNSIGNED NOT NULL,
			ADD COLUMN `created` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
			ADD COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `publish_up` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `publish_down` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
		
		$update_queries[] = 'ALTER TABLE `#__cjblog_badges` CHANGE COLUMN `published` `published` TINYINT(3) NOT NULL';
		$update_queries[] = 'ALTER TABLE `#__cjblog_points` ADD COLUMN `title` VARCHAR(255) NOT NULL';
		$update_queries[] = 'ALTER TABLE `#__cjblog_points` CHANGE COLUMN `published` `published` TINYINT(3) NOT NULL';
		$update_queries[] = 'ALTER TABLE `#__cjblog_users` MODIFY COLUMN `about` MEDIUMTEXT DEFAULT NULL';
		
		$update_queries[] = 'ALTER TABLE `#__cjblog_points`
			ADD COLUMN `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
			ADD COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `publish_up` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `publish_down` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';

		$update_queries[] = 'ALTER TABLE `#__cjblog_point_rules`
			ADD COLUMN `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
			ADD COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `created_by` INT(10) UNSIGNED NOT NULL,
			ADD COLUMN `created` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `ordering` INT(11) UNSIGNED NOT NULL DEFAULT \'0\'';
		
		$update_queries[] = 'ALTER TABLE `#__cjblog_badge_rules`
			ADD COLUMN `created_by` INT(10) UNSIGNED NOT NULL,
			ADD COLUMN `created` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
			ADD COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `publish_up` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `publish_down` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			ADD COLUMN `ordering` INT(11) UNSIGNED NOT NULL DEFAULT \'0\'';
		
		$update_queries[] = 'ALTER TABLE `#__cjblog_badge_rules` CHANGE COLUMN `published` `published` TINYINT(3) NOT NULL';
		$update_queries[] = 'ALTER TABLE `#__cjblog_user_badge_map`	ADD COLUMN `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,	ADD PRIMARY KEY (`id`)';
		$update_queries[] = 'ALTER TABLE `#__cjblog_user_badge_map` ADD COLUMN `published` TINYINT(3) NOT NULL DEFAULT \'1\'';
		
		// Perform all queries - we don't care if it fails
		foreach ($update_queries as $query)
		{
			
			$db->setQuery($query);
			
			try
			{
				
				$db->query();
			}
			catch (Exception $e)
			{
			}
		}
		echo "<b><font color=\"red\">Database tables successfully migrated to the latest version. Please check the configuration options once again.</font></b>";
	}
}