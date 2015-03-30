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

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.modal');

$listOrder = $this->escape($this->state['list.ordering']);
$listDirn = $this->escape($this->state['list.direction']);

$user = JFactory::getUser();
?>

<form action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar cleafix">
		<div class="filter-search fltlft">
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state['filter.search']); ?>" title="<?php echo JText::_('COM_CJBLOG_SEARCH_BADGES'); ?>" />
			<button type="submit" class="btn btn-primary"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="btn" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_RESET'); ?></button>
		</div>
		<div class="pull-right">
			<select name="ruleid" onchange="this.form.submit();">
				<option><?php echo JText::_('COM_CJBLOG_SELECT_OPTION');?></option>
				<?php foreach($this->rules as $rule):?>
				<option value="<?php echo $rule->id;?>"<?php echo $this->state['filter.ruleid'] == $rule->id ? 'selected="selected"' : '';?>>
					<?php echo $this->escape($rule->title.' ('.JText::_($rule->asset_title).')');?>
				</option>
				<?php endforeach;?>
			</select>
		</div>
	</fieldset>
	<div class="clr clearfix"> </div>

	<table class="adminlist table table-stripped table-hover">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="left" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_NAME', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th class="left" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_USERNAME', 'u.username', $listDirn, $listOrder); ?>
				</th>
				<th class="left" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_BADGE', 'a.badge_id', $listDirn, $listOrder); ?>
				</th>
				<th class="left" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_ASSET', 'r.asset_name', $listDirn, $listOrder); ?>
				</th>
				<th class="left">
					<?php echo JText::_('COM_CJBLOG_DESCRIPTION');?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_DATE', 'a.date_assigned', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item):?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php if ($user->authorise('core.manage')): ?>
						<?php echo JHtml::_('grid.id', $i, $item->user_id.','.$item->badge_id.','.$item->rule_id); ?>
					<?php endif; ?>
				</td>
				<td><?php echo $this->escape($item->name);?></td>
				<td><?php echo $this->escape($item->username);?></td>
				<td class="left"><span class="badge <?php echo $item->css_class;?>">&bull; <?php echo $this->escape($item->badge_title);?></span></td>
				<td class="left"><?php echo $this->escape(JText::_($item->asset_title)); ?></td>
				<td class="left"><?php echo $this->escape($item->badge_description); ?></td>
				<td><?php echo JHTML::Date($item->date_assigned, JText::_('DATE_FORMAT_LC2'));?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot><tr><td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
	</table>

	<div style="display: none;">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="badgeactivity" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="userid" value="<?php echo $this->state['filter.userid']; ?>" />
		<span id="url-get-usernames"><?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity&task=search');?></span>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>