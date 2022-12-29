<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die();

class JFormFieldModal_Article extends JFormField
{

	protected $type = 'Modal_Article';

	protected function getInput ()
	{
		$allowEdit = ((string) $this->element['edit'] == 'true') ? true : false;
		$allowClear = ((string) $this->element['clear'] != 'false') ? true : false;
		
		// Load language
		JFactory::getLanguage()->load('com_cjblog', JPATH_ADMINISTRATOR);
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');
		
		// Build the script.
		$script = array();
		
		// Select button script
		$script[] = '	function jSelectArticle_' . $this->id . '(id, title, catid, object) {';
		$script[] = '		document.getElementById("' . $this->id . '_id").value = id;';
		$script[] = '		document.getElementById("' . $this->id . '_name").value = title;';
		
		if ($allowEdit)
		{
			$script[] = '		jQuery("#' . $this->id . '_edit").removeClass("hidden");';
		}
		
		if ($allowClear)
		{
			$script[] = '		jQuery("#' . $this->id . '_clear").removeClass("hidden");';
		}
		
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';
		
		// Clear button script
		static $scriptClear;
		
		if ($allowClear && ! $scriptClear)
		{
			$scriptClear = true;
			
			$script[] = '	function jClearArticle(id) {';
			$script[] = '		document.getElementById(id + "_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "' .
					 htmlspecialchars(JText::_('COM_CJBLOG_SELECT_A_ARTICLE', true), ENT_COMPAT, 'UTF-8') . '";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
			$script[] = '		if (document.getElementById(id + "_edit")) {';
			$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		return false;';
			$script[] = '	}';
		}
		
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		// Setup variables for display.
		$html = array();
		$link = 'index.php?option=com_cjblog_articles&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_' . $this->id;
		
		if (isset($this->element['language']))
		{
			$link .= '&amp;forcedLanguage=' . $this->element['language'];
		}
		
		$db = JFactory::getDbo();
		$db->setQuery('SELECT title' . ' FROM #__cjblog_articles' . ' WHERE id = ' . (int) $this->value);
		
		try
		{
			$title = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
		    throw new Exception($e->getMessage(), 500);
		}
		
		if (empty($title))
		{
			$title = JText::_('COM_CJBLOG_SELECT_A_ARTICLE');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
		
		// The active article id field.
		if (0 == (int) $this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int) $this->value;
		}
		
		// The current article display field.
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
		$html[] = '<a class="modal btn hasTooltip" title="' . JHtml::tooltipText('COM_CJBLOG_CHANGE_ARTICLE') . '"  href="' . $link . '&amp;' .
				 JSession::getFormToken() . '=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> ' . JText::_('JSELECT') .
				 '</a>';
		
		// Edit article button
		if ($allowEdit)
		{
			$html[] = '<a class="btn hasTooltip' . ($value ? '' : ' hidden') .
					 '" href="index.php?option=com_cjblog_articles&layout=modal&tmpl=component&task=article.edit&id=' . $value .
					 '" target="_blank" title="' . JHtml::tooltipText('COM_CJBLOG_EDIT_ARTICLE') . '" ><span class="icon-edit"></span> ' .
					 JText::_('JACTION_EDIT') . '</a>';
		}
		
		// Clear article button
		if ($allowClear)
		{
			$html[] = '<button id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearArticle(\'' . $this->id .
					 '\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</button>';
		}
		
		$html[] = '</span>';
		
		// class='required' for client side validation
		$class = '';
		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}
		
		$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';
		
		return implode("\n", $html);
	}
}
