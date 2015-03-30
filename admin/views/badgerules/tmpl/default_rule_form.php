<?php
/**
 * @version		$Id: default_form.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JHtml::_('behavior.tooltip');
$user = JFactory::getUser();
?>
<form action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgerules');?>" method="post" name="adminForm" id="adminForm">

	<dl class="dl-horizontal">
		<dt><?php echo JText::_('COM_CJBLOG_RULE_NAME');?></dt><dd><?php echo $this->rule_type->rule_name;?></dd>
		<dt><?php echo JText::_('COM_CJBLOG_ASSET_NAME');?></dt><dd><?php echo $this->rule_type->asset_name;?></dd>
	</dl>

	<label><?php echo JText::_('COM_CJBLOG_TITLE')?><sup>*</sup>:</label>
	<input type="text" size="50" class="span4" name="rule_title" value="<?php echo $this->escape($this->rule_type->title);?>" placeholder="<?php echo JText::_('COM_CJBLOG_BADGE_RULE_TITLE_HELP');?>">
	
	<label><?php echo JText::_('COM_CJBLOG_BADGE');?><sup>*</sup></label>
	<select size="1" name="badge_id">
		<option value="0"><?php echo JText::_('COM_CJBLOG_SELECT_OPTION');?></option>
		<?php foreach ($this->rule_type->badges as $badge):?>
		<option value="<?php echo $badge->id;?>"<?php echo $badge->id == $this->rule_type->badge_id ? ' selected="selected"': '';?>><?php echo $this->escape($badge->title);?></option>
		<?php endforeach;?>
	</select>

	<label><?php echo JText::_('COM_CJBLOG_DESCRIPTION')?><sup>*</sup>:</label>
	<textarea rows="3" cols="50" class="span4 required" name="rule_description"><?php echo $this->escape($this->rule_type->description);?></textarea>
	<span class="help-block"><?php echo JText::_('COM_CJBLOG_BADGE_RULE_DESCRIPTION_HELP');?></span>
	
	<label><?php echo JText::_('COM_CJBLOG_STATUS')?><sup>*</sup>:</label>
	<select name="rule_status" size="1">
		<option value="1"<?php echo $this->rule_type->published == 1 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_CJBLOG_PUBLISHED');?></option>
		<option value="0"<?php echo $this->rule_type->published == 0 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_CJBLOG_UNPUBLISHED');?></option>
	</select>
	
	<label class="control-label"><?php echo JText::_('COM_CJBLOG_ACCESS_LEVEL')?>:</label>
	<?php echo JHTML::_('access.assetgrouplist', 'access', $this->rule_type->access); ?>
	
	<h2 class="page-header"><?php echo JText::_('COM_CJBLOG_RULE_VALUES');?></h2>
	
	<?php foreach ($this->rule_type->rule_content->rules as $rule):?>
	<label><?php echo JText::_($rule->label);?></label>
	
	<?php 
	switch ($rule->type){
		
		case 'text':
			
			echo '<input type="text" name="'.$rule->name.'" value="'.$rule->value.'">';
			break;
			
		case 'list':
			
			echo '<select name="'.$rule->name.'" size="1">';
			
			foreach ($rule->options as $rule_option){
				
				echo '<option value="'.$rule_option['value'].'"'.($rule->value == $rule_option['value'] ? ' selected="selected"' : '').'>'.((string)$rule_option).'</option>';
			}
			echo '</select>';
			
			break;
			
		case 'checkbox':
			
			foreach($rule->options as $rule_option){
			
				echo '<label><input type="checkbox" name="'.$rule->name.'[]" value="'.$rule_option['value'].'"'.(in_array($rule_option['value'], $rule->value) ? ' checked="checked"' : '').'> '.((string)$rule_option).'</label>';
			}
			break;
	}
	
	?>
	
	<?php endforeach;?>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->rule_type->id;?>">
	<input type="hidden" name="rule_name" value="<?php echo $this->rule_type->rule_name;?>">
	<input type="hidden" name="asset_name" value="<?php echo $this->rule_type->asset_name;?>">
	<?php echo JHtml::_('form.token'); ?>
</form>