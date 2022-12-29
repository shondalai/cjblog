<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogViewPointsrules extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	public function display ($tpl = null)
	{
		CjBlogHelper::addSubmenu('pointsrules');
		
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
		
		JToolbarHelper::title(JText::_('COM_CJBLOG_POINTS_RULES_TITLE'), 'stack pointsrules');
		
		JToolbarHelper::custom('pointsrules.scan', 'refresh.png', 'refresh.png', 'COM_CJBLOG_SCAN_RULES', false);
		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('activity.edit');
		}
		
		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('pointsrules.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('pointsrules.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::checkin('pointsrules.checkin');
		}
		
		if ($this->state->get('filter.published') == - 2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'pointsrules.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('pointsrules.trash');
		}
		
		if ($user->authorise('core.admin', 'com_cjblog'))
		{
			JToolbarHelper::preferences('com_cjblog');
		}
	}

	protected function getSortFields ()
	{
		return array(
				'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
				'a.state' => JText::_('JSTATUS'),
				'a.title' => JText::_('JGLOBAL_TITLE'),
				'access_level' => JText::_('JGRID_HEADING_ACCESS'),
				'a.created_by' => JText::_('JAUTHOR'),
				'a.created' => JText::_('JDATE'),
				'a.points' => JText::_('COM_CJBLOG_POINTS'),
				'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
