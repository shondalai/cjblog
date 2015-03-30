<?php
/**
 * @version		$Id: badges.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');

class CjBlogModelInstall extends JModelLegacy 
{
	function __construct() 
	{
		parent::__construct ();
		JLog::addLogger(array('text_file' => CJBLOG.'installer.log.php'), JLog::ALL, CJBLOG);
	}

	/**
	 * Create a Joomla menu for the main
	 * navigation tab and publish it in the CjBlog module position cjblog_menu.
	 * In addition it checks if there is a link to CjBlog in any of the menus
	 * and if not, adds a forum link in the mainmenu.
	 * Credits: Kunena Team
	 */
	function createMenu() 
	{
		$menu = array(
				'name'=>'CjBlog', 'alias'=>'blog', 'link'=>'index.php?option=com_cjblog&view=categories', 'access'=>0, 'params'=>array());
		$submenu = array(
				'categories'=>array('name'=>JText::_( 'COM_CJBLOG_MENU_ITEM_CATEGORIES' ), 'alias'=>'categories',
						'link'=>'index.php?option=com_cjblog&view=categories', 'access'=>0, 'params'=>array()),
				'articles'=>array('name'=>JText::_( 'COM_CJBLOG_MENU_ITEM_ARTICLES' ), 'alias'=>'articles',
						'link'=>'index.php?option=com_cjblog&view=articles', 'access'=>0, 'params'=>array()),
				'bloggers'=>array('name'=>JText::_( 'COM_CJBLOG_MENU_ITEM_BLOGGERS' ), 'alias'=>'bloggers',
						'link'=>'index.php?option=com_cjblog&view=users', 'access'=>0, 'params'=>array()),
				'badges'=>array('name'=>JText::_( 'COM_CJBLOG_MENU_ITEM_BADGES' ), 'alias'=>'badges',
						'link'=>'index.php?option=com_cjblog&view=badges', 'access'=>0, 'params'=>array()),
				'points'=>array('name'=>JText::_( 'COM_CJBLOG_MENU_ITEM_POINTS' ), 'alias'=>'points',
						'link'=>'index.php?option=com_cjblog&view=user', 'access'=>1, 'params'=>array()),
				'profile'=>array('name'=>JText::_( 'COM_CJBLOG_MENU_ITEM_PROFILE' ), 'alias'=>'profile',
						'link'=>'index.php?option=com_cjblog&view=profile', 'access'=>0, 'params'=>array()),
				'blog'=>array('name'=>JText::_( 'COM_CJBLOG_MENU_ITEM_BLOG' ), 'alias'=>'blog',
						'link'=>'index.php?option=com_cjblog&view=blog', 'access'=>0, 'params'=>array()),
				'form'=>array('name'=>JText::_( 'COM_CJBLOG_MENU_ITEM_FORM' ), 'alias'=>'form',
						'link'=>'index.php?option=com_cjblog&view=form', 'access'=>1, 'params'=>array()),
		);

		$lang = JFactory::getLanguage();
		$debug = $lang->setDebug(false);
		$this->createMenuItems($menu, $submenu);

		$lang->setDebug($debug);
	}

	function createMenuItems($menu, $submenu) 
	{
		jimport ( 'joomla.utilities.string' );
		jimport ( 'joomla.application.component.helper' );

		$component_id = JComponentHelper::getComponent('com_cjblog')->id;
		
		// First fix all broken menu items
		$query = "UPDATE #__menu SET component_id={$this->_db->quote($component_id)} WHERE type = 'component' AND link LIKE '%option=com_cjblog%'";
		$this->_db->setQuery ( $query );
		$this->_db->query ();

		if ($this->_db->getErrorNum ())
		{
			JLog::add('createMenuItems: '.$this->_db->getErrorMsg ().'| Query: '.$query, JLog::ERROR, CJBLOG);
			throw new Exception ( $this->_db->getErrorMsg (), $this->_db->getErrorNum () );
		}

		$table = JTable::getInstance ( 'MenuType' );
		$data = array (
				'menutype' => 'cjblogmenu',
				'title' => JText::_ ( 'COM_CJBLOG_MENU_TITLE' ),
				'description' => JText::_ ( 'COM_CJBLOG_MENU_TITLE_DESC' )
		);
		
		if (! $table->bind ( $data ) || ! $table->check ()) 
		{
			// Menu already exists, do nothing
// 			return true;
		}

// 		if (! $table->store ()) 
// 		{
// 			JLog::add('createMenuItems: '.$table->getError(), JLog::ERROR, CJBLOG);
// 			throw new Exception ( $table->getError () );
// 		}

		$table = JTable::getInstance ( 'Menu' );
		
		$paramdata = array ('menu-anchor_title'=>'',
				'menu-anchor_css'=>'',
				'menu_image'=>'',
				'menu_text'=>1,
				'page_title'=>'',
				'show_page_heading'=>0,
				'page_heading'=>'',
				'pageclass_sfx'=>'',
				'menu-meta_description'=>'',
				'menu-meta_keywords'=>'',
				'robots'=>'',
				'secure'=>0);
		
		$gparams = new JRegistry($paramdata);
		$params = clone $gparams;
		$params->loadArray($menu['params']);
		
		$data = array (
				'menutype' => 'cjblogmenu',
				'title' => $menu ['name'],
				'alias' => $menu ['alias'],
				'link' => $menu ['link'],
				'type' => 'component',
				'published' => 1,
				'parent_id' => 1,
				'level' => 1,
				'component_id' => $component_id,
				'access' => $menu ['access'] + 1,
				'params' => (string) $params,
				'home' => 0,
				'note' => '',
				'language' => '*',
				'client_id' => 0
		);
		
		$table->setLocation ( 1, 'last-child' );
		
		if ( ! $table->bind ( $data ) || ! $table->check () || ! $table->store () || !$table->rebuildPath($table->id)) 
		{
			JLog::add('createMenuItems: '.$table->getError().'| Alias: '.$table->alias.'| Table: '.print_r($table, TRUE), JLog::ERROR, CJBLOG);
			$table->alias = 'cjblog';
			throw new Exception ( $table->getError () );
			
			if (! $table->check () || ! $table->store ()) 
			{
				JLog::add('createMenuItems: '.$table->getError().'| Alias: '.$table->alias.'| Table: '.print_r($table, TRUE), JLog::ERROR, CJBLOG);
				throw new Exception ( $table->getError () );
			}
		}
		
		$parent = $table;
		$defaultmenu = 0;

		foreach ( $submenu as $menuitem ) 
		{
			$params = clone $gparams;
			$params->loadArray($menuitem['params']);
			$table = JTable::getInstance ( 'Menu' );
			$table->load(array('menutype'=>'cjblogmenu', 'link'=>$menuitem ['link']));
			$data = array (
					'menutype' => 'cjblogmenu',
					'title' => $menuitem ['name'],
					'alias' => $menuitem ['alias'],
					'link' => $menuitem ['link'],
					'type' => 'component',
					'published' => 1,
					'parent_id' => $parent->id,
					'component_id' => $component_id,
					'access' => $menuitem ['access'] + 1,
					'params' => (string) $params,
					'home' => 0,
					'language' => '*',
					'client_id' => 0
			);
			
			$table->setLocation ( $parent->id, 'last-child' );
			
			if ( ! $table->bind ( $data ) || ! $table->check () || ! $table->store ()) 
			{
				JLog::add('createMenuItems: '.$table->getError().'| Table: '.print_r($table, true), JLog::ERROR, CJBLOG);
				throw new Exception ( $table->getError () );
			}
			
			if (! $defaultmenu || (isset ( $menuitem ['default'] ))) 
			{
				$defaultmenu = $table->id;
			}
		}

		// Update forum menuitem to point into default page
		$parent->link .= "&defaultmenu={$defaultmenu}";
		
		if (! $parent->check () || ! $parent->store ()) 
		{
			JLog::add('createMenuItems: '.$parent->getError().'| Table: '.print_r($parent, true), JLog::ERROR, CJBLOG);
			throw new Exception ( $table->getError () );
		}
		
		// Finally create alias
		$defaultmenu = JMenu::getInstance('site')->getDefault();
		if (!$defaultmenu) return true;
		
		$table = JTable::getInstance ( 'Menu' );
		$table->load(array('menutype'=>$defaultmenu->menutype, 'type'=>'alias', 'title'=>JText::_ ( 'COM_CJBLOG_MENU_ITEM_BLOG' )));
		
		if (!$table->id) 
		{
			$data = array (
					'menutype' => $defaultmenu->menutype,
					'title' => JText::_ ( 'COM_CJBLOG_MENU_ITEM_BLOG' ),
					'alias' => 'cjblog-'.JFactory::getDate()->format('Y-m-d'),
					'link' => 'index.php?Itemid='.$parent->id,
					'type' => 'alias',
					'published' => 0,
					'parent_id' => 1,
					'component_id' => 0,
					'access' => 1,
					'params' => '{"aliasoptions":"'.(int)$parent->id.'","menu-anchor_title":"","menu-anchor_css":"","menu_image":""}',
					'home' => 0,
					'language' => '*',
					'client_id' => 0
			);
			if (! $table->setLocation ( 1, 'last-child' )) 
			{
				JLog::add('createMenuItems: '.$table->getError().'| Table: '.print_r($table, true), JLog::ERROR, CJBLOG);
// 				throw new Exception ( $table->getError () );
			}
		} 
		else 
		{
			$data = array (
					'alias' => 'cjblog-'.JFactory::getDate()->format('Y-m-d'),
					'link' => 'index.php?Itemid='.$parent->id,
					'params' => '{"aliasoptions":"'.(int)$parent->id.'","menu-anchor_title":"","menu-anchor_css":"","menu_image":""}',
			);
		}
		
		if (! $table->bind ( $data ) || ! $table->check () || ! $table->store ()) 
		{
			JLog::add('createMenuItems: '.$table->getError().'| Table: '.print_r($table, true), JLog::ERROR, CJBLOG);
			throw new Exception ( $table->getError () );
		}
	}

	function deleteMenu() 
	{
		$table = JTable::getInstance ( 'MenuType' );
		$table->load(array('menutype'=>'cjblogmenu'));
		
		if ($table->id) 
		{
			$success = $table->delete();
			if (!$success) 
			{
				JLog::add('createMenuItems: '.$table->getError(), JLog::ERROR, CJBLOG);
				JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}
		}

		$cache = JFactory::getCache('mod_menu');
		$cache->clean();
	}
}
?>