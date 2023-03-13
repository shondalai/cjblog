<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\HTML\HTMLHelper;

class JFormFieldPointsrule extends JFormField
{
	public $type = 'Pointsrule';

	protected function getInput()
	{
		$html = array();
		$groups = $this->getGroups();
		$excluded = $this->getExcluded();
		$link = 'index.php?option=com_cjblog&amp;view=pointsrules&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id
		        . (isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '')
		        . (isset($excluded) ? ('&amp;excluded=' . base64_encode(json_encode($excluded))) : '');

		// Initialize some field attributes.
		$attr = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->required ? ' required' : '';

		// Build the script.
		$script = array();
		$script[] = '	function jSelectPointsrule_' . $this->id . '(id, title) {';
		$script[] = '		var old_id = document.getElementById("' . $this->id . '_id").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("' . $this->id . '_id").value = id;';
		$script[] = '			document.getElementById("' . $this->id . '").value = title;';
		$script[] = '			document.getElementById("' . $this->id . '").className = document.getElementById("' . $this->id . '").className.replace(" invalid" , "");';
		$script[] = '			' . $this->onchange;
		$script[] = '		}';

		if(CJBLOG_MAJOR_VERSION < 4) {
			$script[] = '		SqueezeBox.close();';
		} else {
			$script[] = 'var modal = bootstrap.Modal.getInstance(document.getElementById("rulesModal_jform_rule_id")); modal.hide();';
		}

		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Load the current username if available.
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_cjblog/tables');
		$table = JTable::getInstance('Pointsrule', 'CjBlogTable');

		if (is_numeric($this->value))
		{
			$table->load($this->value);
		}
		// Handle the special case for "current".
		elseif (strtoupper($this->value) == 'CURRENT')
		{
			// 'CURRENT' is not a reasonable value to be placed in the html
			$this->value = JFactory::getUser()->id;
			$table->load($this->value);
		}
		else
		{
			$table->title = JText::_('COM_CJBLOG_SELECT_A_RULE');
		}

		// Create a dummy text field with the user name.
		$html[] = '<div class="input-append input-group">';
		$html[] = '<input type="text" id="' . $this->id . '" value="' . htmlspecialchars($table->title, ENT_COMPAT, 'UTF-8') . '"' . ' class="form-control" readonly' . $attr . ' />';

		// Create the user select button.
		if ($this->readonly === false) {
			if (CJBLOG_MAJOR_VERSION < 4) {
				JHtml::_('behavior.modal', 'a.modal_' . $this->id);

				$html[] = '<a class="btn btn-primary modal_' . $this->id . '" title="' . JText::_('COM_CJBLOG_SELECT_A_RULE') . '" href="' . $link . '"' . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
				$html[] = '<i class="icon-folder-open"></i></a>';
			} else {
				$html[] = HTMLHelper::_('bootstrap.renderModal', 'rulesModal_' . $this->id, array(
					'url' => $link,
					'title' => JText::_('COM_CJBLOG_SELECT_A_RULE'),
					'closeButton' => true,
					'height' => '100%',
					'width' => '100%',
					'modalWidth' => 80,
					'bodyHeight' => 60,
					'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . JText::_('JCANCEL') . '</button>'
				));

				$html[] = '<button type="button" data-bs-toggle="modal" data-bs-target="#rulesModal_jform_rule_id" class="btn btn-primary button-select" title="'.JText::_('COM_CJBLOG_SELECT_A_RULE').'">
                				<span class="icon-folder-open icon-white" aria-hidden="true"></span>
                				<span class="visually-hidden">'.JText::_('COM_CJBLOG_SELECT_A_RULE').'</span>
                			</button>';
			}
		}

		$html[] = '</div>';

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . $this->value . '" />';

		return implode("\n", $html);
	}

	protected function getGroups()
	{
		return null;
	}

	protected function getExcluded()
	{
		return null;
	}
}