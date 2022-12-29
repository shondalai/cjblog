<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogViewPointsrule extends JViewLegacy
{

	protected $form;

	protected $item;

	protected $state;

	public function display ($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->canDo = JHelperContent::getActions('com_cjblog');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
		    throw new Exception(implode("\n", $errors), 500);
		}
		
		if ($this->getLayout() == 'modal')
		{
			$this->form->setFieldAttribute('language', 'readonly', 'true');
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar ()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		
		// Built the actions for new and existing records.
		$canDo = $this->canDo;
		JToolbarHelper::title(JText::_('COM_CJBLOG_PAGE_EDIT_POINTS_RULE_TYPE'), 'pencil-2 pointsrule-add');
		
		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories('com_cjblog', 'core.manage')) > 0))
		{
			JToolbarHelper::apply('pointsrule.apply');
			JToolbarHelper::save('pointsrule.save');
			JToolbarHelper::cancel('pointsrule.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (! $checkedOut)
			{
				// Since it's an existing record, check the edit permission, or
				// fall back to edit own if the owner.
				if ($canDo->get('core.manage'))
				{
					JToolbarHelper::apply('pointsrule.apply');
					JToolbarHelper::save('pointsrule.save');
				}
			}
			
			JToolbarHelper::cancel('pointsrule.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
