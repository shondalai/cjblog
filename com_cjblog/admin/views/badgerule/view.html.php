<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogViewBadgerule extends JViewLegacy
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
		JToolbarHelper::title(JText::_('COM_CJBLOG_PAGE_' . ($checkedOut ? 'VIEW_BADGE_RULE_DETAILS' : ($isNew ? 'ADD_BADGE_RULE' : 'EDIT_BADGE_RULE'))), 'pencil-2 badge-add');
		
		// For new records, check the create permission.
		if ($user->authorise('core.manage', 'com_cjblog'))
		{
			JToolbarHelper::apply('badgerule.apply');
			JToolbarHelper::save('badgerule.save');
			JToolbarHelper::save2new('badgerule.save2new');
			JToolbarHelper::cancel('badgerule.cancel');
		}
	}
}
