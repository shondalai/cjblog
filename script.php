<?php
/**
 * @version		$Id: script.php 01 2012-09-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
defined('CJBLOG') or define('CJBLOG', 'com_cjblog');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
 
class com_cjblogInstallerScript{
	
	function install($parent){
		
		$parent->getParent()->setRedirectURL('index.php?option=com_cjblog');
	}
 
	function uninstall($parent){
		
		echo '<p>' . JText::_('COM_CJBLOG_UNINSTALL_TEXT') . '</p>';
	}
 
	function update($parent) {
		
		$db = JFactory::getDBO();
		
		if(method_exists($parent, 'extension_root')) {
			
			$sqlfile = $parent->getPath('extension_root').DS.'sql'.DS.'install.mysql.utf8.sql';
		} else {
			
			$sqlfile = $parent->getParent()->getPath('extension_root').DS.'sql'.DS.'install.mysql.utf8.sql';
		}
		
		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);
		
		if ($buffer !== false) {
			
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			
			if (count($queries) != 0) {
				
				foreach ($queries as $query){
					
					$query = trim($query);
					
					if ($query != '' && $query{0} != '#') {
						
						$db->setQuery($query);
						
						if (!$db->query()) {
							
							CJFunctions::throw_error(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), 1);
							return false;
						}
					}
				}
			}
		}
		
		echo '<p>' . JText::sprintf('COM_CJBLOG_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
	}
 
	function preflight($type, $parent) {
		
		echo '<p>' . JText::_('COM_CJBLOG_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}
 
	function postflight($type, $parent) {
		
		$db = JFactory::getDbo();
		$update_queries[] = 'ALTER IGNORE TABLE `#__cjblog_users` MODIFY COLUMN `about` MEDIUMTEXT CHARACTER SET utf8 DEFAULT NULL';
		$update_queries[] = 'ALTER IGNORE TABLE `#__cjblog_point_rules` ADD COLUMN `conditional_rules` VARCHAR(5120)';
		
		foreach( $update_queries as $query ) {
		
			$db->setQuery( $query );
				
			try{
					
				$db->query();
			}catch(Exception $e){}
		}
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$path = JPATH_ROOT.'/images/avatar';
		JFolder::create(JPATH_ROOT.'/media/cjblog');
		touch(JPATH_ROOT.'/media/cjblog/index.html');
		
		if(!file_exists($path.'index.html')){
				
			JFolder::create($path.'original');
			touch($path.'index.html');
			
			foreach(array(16,32,48,64,96,128,160,192,256) as $size){
			
				JFolder::create($path.'size-'.$size);
				touch($path.'size-'.$size.'/index.html');
			}
		}
		
		try 
		{
			JLoader::import('joomla.application.component.model');
			JLoader::import('install', JPATH_ADMINISTRATOR.'/components/com_cjblog/models');
			$model = JModelLegacy::getInstance( 'install', 'CjBlogModel' );
			$model->createMenu();
		}
		catch (Exception $e){}
				
		echo '<p>' . JText::_('COM_CJBLOG_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}