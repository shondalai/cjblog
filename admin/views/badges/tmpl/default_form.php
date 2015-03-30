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
<form action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badges');?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">

	<fieldset class="adminform">
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_CJBLOG_TITLE')?><sup>*</sup>:</div>
			<div class="controls"><input type="text" size="40" class="span3 required" name="badge_title" value="<?php echo $this->escape($this->badge->title);?>"></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_CJBLOG_ALIAS')?><sup>*</sup>:</div>
			<div class="controls"><input type="text" size="40" class="span3 required" name="badge_alias" value="<?php echo $this->escape($this->badge->alias);?>"></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_CJBLOG_DESCRIPTION')?><sup>*</sup>:</div>
			<div class="controls"><textarea class="span3 required" rows="5" cols="40" name="badge_description"><?php echo $this->escape($this->badge->description);?></textarea></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_CJBLOG_PUBLISHED')?>:</div>
			<div class="controls">
				<select name="badge_state" size="1" class="required">
					<option value="0"<?php echo ($this->badge->published == 0) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_CJBLOG_UNPUBLISHED');?></option>
					<option value="1"<?php echo ($this->badge->published == 1) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_CJBLOG_PUBLISHED');?></option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_CJBLOG_ICON')?>:</div>
			<div class="controls"><input type="text" size="40" class="span3" name="badge_icon" value="<?php echo $this->escape($this->badge->icon);?>"></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_CJBLOG_BADGE_TYPE')?>:</div>
			<div class="controls">
				<select name="badge_classname" size="1">
					<option><?php echo JText::_('COM_CJBLOG_SELECT_OPTION');?></option>
					<option value="badge"<?php echo $this->badge->css_class == 'badge' ? ' selected="selected"' : '';?>>badge</option>
					<option value="badge-success"<?php echo $this->badge->css_class == 'badge-success' ? ' selected="selected"' : '';?>>badge-success</option>
					<option value="badge-warning"<?php echo $this->badge->css_class == 'badge-warning' ? ' selected="selected"' : '';?>>badge-warning</option>
					<option value="badge-important"<?php echo $this->badge->css_class == 'badge-important' ? ' selected="selected"' : '';?>>badge-important</option>
					<option value="badge-info"<?php echo $this->badge->css_class == 'badge-info' ? ' selected="selected"' : '';?>>badge-info</option>
					<option value="badge-inverse"<?php echo $this->badge->css_class == 'badge-inverse' ? ' selected="selected"' : '';?>>badge-inverse</option>
				</select>
			</div>
		</div>
	</fieldset>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="badge_id" value="<?php echo $this->badge->id;?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>