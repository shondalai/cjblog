<?php
/**
 * @version		$Id: default.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$user = JFactory::getUser();
?>
<script type="text/javascript">
<!--
Joomla.submitbutton = function(task){
	if (task == ''){
		return false;
	} else {
        var action = task.split('.');
        if (action[1] != 'cancel' && action[1] != 'close'){
            select = document.getElementById('target_names');
            for (var i = 0; i < select.options.length; i++) { 
            	select.options[i].selected = true; 
            } 
            Joomla.submitform(task);
            return true;
        } else {
            return false;
        }
	}
}
//-->
</script>
<div class="container-fluid">
	<div class="row-fluid">
		
		<form class="form-horizontal" action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=points');?>" method="post" name="adminForm" id="adminForm">
			
			<div class="control-group">
				<label class="control-label required" for="input_points"><?php echo JText::_('COM_CJBLOG_POINTS')?><sup>*</sup>:</label>
				<div class="controls">
					<input type="text" name="points" class="required" id="input_points" placeholder="<?php echo JText::_('COM_CJBLOG_POINTS')?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="input-description"><?php echo JText::_('COM_CJBLOG_DESCRIPTION')?>:</label>
				<div class="controls">
					<input type="text" name="description" id="input-description" class="input-xxlarge required" placeholder="<?php echo JText::_('COM_CJBLOG_DESCRIPTION')?>">
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="username"><?php echo JText::_('COM_CJBLOG_USERNAME')?>:</label>
				<div class="controls">
					<input type="text" name="username" id="username" value="" placeholder="<?php echo JText::_('COM_CJBLOG_TYPE_IN_TO_GET_SUGGESTIONS')?>" autocomplete="off">
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<table class="item-selector">
						<tr>
							<td class="source-list items" align="center">
								<select name="source_names" id="source_names" multiple="multiple" size="10"></select>
								<div class="source_controls">
									<a href="#" class="btn btn-small tooltip-hover select_all" onclick="return false;" title="<?php echo JText::_('COM_CJBLOG_SELECT_ALL');?>">
										<i class="icon-ok-circle"></i>
									</a>
									<a href="#" class="btn btn-small tooltip-hover deselect_all" onclick="return false;" title="<?php echo JText::_('COM_CJBLOG_DESELECT_ALL');?>">
										<i class="icon-ban-circle"></i>
									</a>
								</div>
							</td>
							<td class="controls pad-left-10 pad-right-10" align="center" valign="middle">
								<ul class="unstyled">
									<li>
										<a href="#" class="btn btn-small tooltip-hover to_right" title="<?php echo JText::_('COM_CJBLOG_TO_RIGHT')?>" onclick="return false;">
											<i class="icon-hand-right"></i>
										</a>
									</li>
									<li>
										<a href="#" class="btn btn-small tooltip-hover to_left" title="<?php echo JText::_('COM_CJBLOG_TO_LEFT')?>" onclick="return false;">
											<i class="icon-hand-left"></i>
										</a>
									</li>
									<li>
										<a href="#" class="btn btn-small tooltip-hover all_right" title="<?php echo JText::_('COM_CJBLOG_ALL_RIGHT')?>" onclick="return false;">
											<i class="icon-fast-forward"></i>
										</a>
									</li>
									<li>
										<a href="#" class="btn btn-small tooltip-hover all_left" title="<?php echo JText::_('COM_CJBLOG_ALL_LEFT')?>" onclick="return false;">
											<i class="icon-fast-backward"></i>
										</a>
									</li>
								</ul>
							</td>
							<td class="target-list items" align="center">
								<select name="cid[]" id="target_names" multiple="multiple" size="10"></select>
								<div class="target_controls">
									<a href="#" class="btn btn-small tooltip-hover select_all" onclick="return false;" title="<?php echo JText::_('COM_CJBLOG_SELECT_ALL');?>">
										<i class="icon-ok-circle"></i>
									</a>
									<a href="#" class="btn btn-small tooltip-hover deselect_all" onclick="return false;" title="<?php echo JText::_('COM_CJBLOG_DESELECT_ALL');?>">
										<i class="icon-ban-circle"></i>
									</a>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
					
			<input type="hidden" name="task" value="save">
		</form>
	</div>
	
	<div style="display: none;">
		<span id="url-get-usernames"><?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users&task=search');?></span>
		<input type="hidden" id="cjblog_page_id" value="points_form">
	</div>
</div>