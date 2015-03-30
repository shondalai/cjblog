<?php
/**
 * @version		$Id: view.html.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.view' );

class CjBlogViewUsers extends JViewLegacy {

	function display($tpl = null) {
		
		JToolBarHelper::title(JText::_('COM_CJBLOG')." <small>[".JText::_("COM_CJBLOG_USERS")."]</small>");
		JToolbarHelper::divider();
		JToolbarHelper::deleteList();
		JToolbarHelper::custom('users.sync', 'sync.png', 'sync.png', 'COM_CJBLOG_SYNC_USERS', false);
		
		$model = $this->getModel();
		$return = $model->get_users();
		$this->assignRef('items', $return->users);
		$this->assignRef('state', $return->state);
		$this->assignRef('pagination', $return->pagination);
		
		parent::display ( $tpl );
	}
}