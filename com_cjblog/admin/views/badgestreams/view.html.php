<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogViewBadgeStreams extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	public function display ($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			CjBlogHelper::addSubmenu('badgestreams');
		}
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
		    throw new Exception(implode("\n", $errors), 500);
		}
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			
			if(CJBLOG_MAJOR_VERSION < 4) {
			    $this->sidebar = JHtmlSidebar::render();
			}
		}
		
		parent::display($tpl);
	}

	protected function addToolbar ()
	{
		$canDo = JHelperContent::getActions('com_cjblog');
		$user = JFactory::getUser();
		
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JToolbarHelper::title(JText::_('COM_CJBLOG_BADGES_TITLE'), 'stack badgestream');
		
		if ($canDo->get('core.manage'))
		{
			JToolbarHelper::addNew('badgestream.add');
			JToolbarHelper::editList('badgestream.edit');
			JToolbarHelper::publish('badgestreams.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('badgestreams.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('badgestreams.archive');
			JToolbarHelper::checkin('badgestreams.checkin');
		}
		
		if ($this->state->get('filter.published') == - 2 && $canDo->get('core.admin'))
		{
			JToolbarHelper::deleteList('', 'badgestreams.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('badgestreams.trash');
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
