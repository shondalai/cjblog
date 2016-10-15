<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogViewBadges extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	public function display ($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			CjBlogHelper::addSubmenu('badges');
		}
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			
			return false;
		}
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}
		
		parent::display($tpl);
	}

	protected function addToolbar ()
	{
		$canDo = JHelperContent::getActions('com_cjblog');
		$user = JFactory::getUser();
		
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JToolbarHelper::title(JText::_('COM_CJBLOG_BADGES_TITLE'), 'stack badge');
		
		if ($canDo->get('core.manage'))
		{
			JToolbarHelper::addNew('badge.add');
			JToolbarHelper::editList('badge.edit');
			JToolbarHelper::publish('badges.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('badges.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('badges.archive');
			JToolbarHelper::checkin('badges.checkin');
		}
		
		if ($this->state->get('filter.published') == - 2 && $canDo->get('core.admin'))
		{
			JToolbarHelper::deleteList('', 'badges.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('badges.trash');
		}
		
		if ($user->authorise('core.admin', 'com_cjblog'))
		{
			JToolbarHelper::preferences('com_cjblog');
		}
	}

	protected function getSortFields ()
	{
		return array(
				'a.state' => JText::_('JSTATUS'),
				'a.title' => JText::_('JGLOBAL_TITLE'),
				'access_level' => JText::_('JGRID_HEADING_ACCESS'),
				'a.created_by' => JText::_('JAUTHOR'),
				'a.user_id' => JText::_('COM_CJBLOG_USER'),
				'language' => JText::_('JGRID_HEADING_LANGUAGE'),
				'a.created' => JText::_('JDATE'),
				'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
