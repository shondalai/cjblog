<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JFormHelper::loadFieldClass('editor');

class JFormFieldCjeditor extends JFormFieldEditor
{
	public $type = 'Cjeditor';
	
	protected function getEditor()
	{
		$params = JComponentHelper::getParams('com_cjblog');
		$this->editorType = array($params->get('default_editor'));
		
		return parent::getEditor();
	}
	
	public function save()
	{
		parent::save();
	}
}
