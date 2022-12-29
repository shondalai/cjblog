<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class CjBlogViewUsers extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->canDo		= JHelperContent::getActions('com_cjblog');

		CjBlogHelper::addSubmenu('users');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
		    throw new Exception(implode("\n", $errors), 500);
		}

		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		$this->addToolbar();
		if(CJBLOG_MAJOR_VERSION < 4) {
		    $this->sidebar = JHtmlSidebar::render();
		}
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$canDo	= $this->canDo;
		$user 	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_CJBLOG_VIEW_USERS_TITLE'), 'users user');

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('user.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::divider();
			JToolbarHelper::unpublish('users.block', 'COM_CJBLOG_TOOLBAR_BLOCK', true);
			JToolbarHelper::custom('users.unblock', 'unblock.png', 'unblock_f2.png', 'COM_CJBLOG_TOOLBAR_UNBLOCK', true);
			JToolbarHelper::divider();
		}
		
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::custom('users.sync', 'refresh.png', 'refresh.png', 'COM_CJBLOG_TOOLBAR_SYNC', false);
			JToolbarHelper::preferences('com_cjblog');
			JToolbarHelper::divider();
		}

		JToolbarHelper::help('JHELP_USERS_USER_MANAGER');
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
				'a.name' => JText::_('COM_CJBLOG_HEADING_NAME'),
				'a.username' => JText::_('JGLOBAL_USERNAME'),
				'a.block' => JText::_('COM_CJBLOG_HEADING_ENABLED'),
				'a.activation' => JText::_('COM_CJBLOG_HEADING_ACTIVATED'),
				'a.email' => JText::_('JGLOBAL_EMAIL'),
				'a.lastvisitDate' => JText::_('COM_CJBLOG_HEADING_LAST_VISIT_DATE'),
				'a.registerDate' => JText::_('COM_CJBLOG_HEADING_REGISTRATION_DATE'),
				'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
