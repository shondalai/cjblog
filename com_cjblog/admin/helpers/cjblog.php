<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogHelper extends JHelperContent
{

	public static $extension = 'com_cjblog';

	public static function addSubmenu ($vName)
	{
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_DASHBOARD'), 'index.php?option=com_cjblog&view=dashboard', $vName == 'dashboard');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_POINTS'), 'index.php?option=com_cjblog&view=points', $vName == 'points');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_POINTS_RULES'), 'index.php?option=com_cjblog&view=pointsrules', $vName == 'pointsrules');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_BADGES'), 'index.php?option=com_cjblog&view=badges', $vName == 'badges');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_BADGE_RULES'), 'index.php?option=com_cjblog&view=badgerules', $vName == 'badgerules');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_BADGE_STREAM'), 'index.php?option=com_cjblog&view=badgestreams', $vName == 'badgestreams');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_EMAIL_TEMPLATES'), 'index.php?option=com_cjblog&view=emails', $vName == 'emails');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_ARTICLES'), 'index.php?option=com_content&view=articles');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_REVIEWS'), 'index.php?option=com_cjblog&view=reviews', $vName == 'reviews');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_CATEGORIES'), 'index.php?option=com_categories&extension=com_content', $vName == 'categories');
		JHtmlSidebar::addEntry(JText::_('COM_CJBLOG_MENU_USERS'), 'index.php?option=com_cjblog&view=users', $vName == 'users');
	}
	
	public static function uploadFiles($postId, $postType, $fieldName = 'attachment_file')
	{
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		
		jimport('joomla.filesystem.file');
		$files = $app->input->files->get('attachment_file');
		$uploaded = array();
		
		if(!empty($files))
		{
			foreach ($files as $i=>$file)
			{
				if(empty($file['tmp_name']))
				{
					continue;
				}
					
				$filename = $postId.'_'.$postType.'_'.JFile::makeSafe($file['name']);
				$src = $file['tmp_name'];
				$dest = CJBLOG_ATTACHMENTS_DIR.$filename;
					
				if(JFile::upload($src, $dest))
				{
					$upload = new stdClass();
					$upload->name = $filename;
					$upload->size = (int) $file['size'];
					$uploaded[] = $upload;
				}
			}
		}
			
		if(!empty($uploaded))
		{
			$query = $db->getQuery(true)
				->insert('#__cjblog_attachments')
				->columns('post_id, post_type, created_by, hash, filesize, folder, filetype, filename');
		
			foreach ($uploaded as $upload)
			{
				$hash = md5_file(CJBLOG_ATTACHMENTS_DIR.$upload->name);
				$query->values($postId.','.$postType.','.$user->id.','.$db->q($hash).','.$upload->size.','.$db->q(CJBLOG_ATTACHMENTS_PATH).','.$db->q('').','.$db->q($upload->name));
			}
		
			$db->setQuery($query);
		
			try
			{
				$db->execute();
			}
			catch (Exception $e){}
		}
	}
}
