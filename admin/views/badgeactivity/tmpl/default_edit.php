<?php 
/**
 * @version		$Id: default_edit.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2014 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die;
JHtml::_('behavior.modal');
$this->loadHelper('select');
?>

<script type="text/javascript">
<!--
var badge_rules = new Array();
<?php foreach ($this->rules as $rule):?>
var rule = new Object();
rule.id = <?php echo $rule->id?>;
rule.title = "<?php echo $this->escape($rule->title);?>";
rule.description = "<?php echo strip_tags($rule->description);?>";
rule.content = <?php echo $rule->rule_content;?>;
badge_rules.push(rule);
<?php endforeach;?>
//-->
</script>
<div id="cj-wrapper">
	<p><?php echo JText::_('COM_CJBLOG_BADGE_CUSTOM_TRIGGER_HELP');?></p>
	<form id="adminForm" name="adminForm" class="form-horizontal" method="post" action="<?php echo JRoute::_('index.php?option=com_cjblog&view=badgeactivity')?>">
		<div class="control-group">
			<label class="control-label" for="inputRuleId"><?php echo JText::_('COM_CJBLOG_RULE_NAME');?>:</label>
			<div class="controls">
				<select name="ruleId" id="inputRuleId" size="1">
					<option><?php echo JText::_('COM_CJBLOG_SELECT_OPTION')?></option>
					<?php foreach ($this->rules as $rule):?>
					<option value="<?php echo $rule->id;?>"><?php echo $this->escape($rule->title).' ('.JText::_($rule->asset_title).')';?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		
		<div id="ruleParams"></div>
		
		<div class="control-group">
			<label class="control-label"><?php echo JText::_('COM_CJBLOG_TARGET_USER');?></label>
			<div class="controls">
				<input id="userName" name="userName" type="text" value="" readonly="readonly">
				<button class="btn btn-select-user" type="button"><?php echo JText::_('COM_CJBLOG_SELECT_USER')?></button>
				<input id="userId" name="userId" type="hidden" value="0">
				<a class="modal" style="display: none;" id="userselect" 
					href="<?php echo JRoute::_('index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=userId');?>" 
					id="user-id-select-model" rel="{handler: 'iframe', size: {x: 960, y: 600}}">Select</a>
			</div>
		</div>

		<input type="hidden" name="task" value="" />
		<input type="hidden" id="cjblog_page_id" value="badge_trigger_form">
	</form>
</div>

<script type="text/javascript">
function jSelectUser_userId(id, username)
{
	document.getElementById('userId').value = id;
	document.getElementById('userName').value = username;
	try {
		document.getElementById('sbox-window').close();	
	} catch(err) {
		SqueezeBox.close();
	}
}

window.addEvent("domready", function() {
	$$("button.btn-select-user").each(function(el) {
		el.addEvent("click", function(e) {
			try {
				new Event(e).stop();
			} catch(anotherMTUpgradeIssue) {
				try {
					e.stop();
				} catch(WhateverIsWrongWithYouIDontCare) {
					try {
						DOMEvent(e).stop();
					} catch(NoBleepinWay) {
					}
				}
			}
			SqueezeBox.fromElement($('userselect'), {
				parse: 'rel'
			});
		});
	});
});
</script>
