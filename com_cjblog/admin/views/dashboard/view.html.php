<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogViewDashboard extends JViewLegacy
{
	protected $state;
	
	public function display ($tpl = null)
	{
		$model = $this->getModel();
		$state = $model->getState();
		
		$model->setState('list.ordering', 'a.created');
		$model->setState('list.direction', 'desc');
		$this->recent = $model->getItems();
		
		$model->setState('list.ordering', 'a.created');
		$this->trending = $model->getItems();
		$this->articleCount = $model->getArticleCountByDay();
		
		JLoader::import('joomla.application.component.model');
		JLoader::import('users', JPATH_COMPONENT_ADMINISTRATOR.'/models');
		$model = JModelLegacy::getInstance( 'users', 'CjBlogModel' );
		
		$state = $model->getState();
		$model->setState('list.limit', 5);
		$model->setState('list.ordering', 'cju.articles');
		$model->setState('list.direction', 'desc');
		$this->topusers = $model->getItems();

		CjBlogHelper::addSubmenu('dashboard');
		$this->addToolbar();
		
		if(CJBLOG_MAJOR_VERSION == 3)
		{
		    $this->sidebar = JHtmlSidebar::render();
		}
		
		$version = CJFunctions::get_component_update_check('com_cjblog', CJBLOG_CURR_VERSION);
		$v = array();
		
		if(!empty($version) && !empty($version['connect']))
		{
			$v['connect'] = (int)$version['connect'];
			$v['version'] = (string)$version['version'];
			$v['released'] = (string)$version['released'];
			$v['changelog'] = (string)$version['changelog'];
			$v['status'] = (string)$version['status'];
		}
		
		$this->version = $v;
		parent::display($tpl);
	}

	protected function addToolbar ()
	{
		$canDo = JHelperContent::getActions('com_cjblog');
		$user = JFactory::getUser();
		
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JToolbarHelper::title(JText::_('COM_CJBLOG_DASHBOARD_TITLE'), 'stack dashboard');
		
		if ($user->authorise('core.admin', 'com_cjblog'))
		{
			JToolbarHelper::preferences('com_cjblog');
		}
		
		JToolbarHelper::help('JHELP_CJBLOG_DASHABOARD');
	}
}
