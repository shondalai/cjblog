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

<form action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=points');?>" method="post" name="adminForm" id="adminForm">
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
					<?php echo $this->escape($rule->title);?>
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
					<?php echo JText::_('COM_CJBLOG_RULE_NAME'); ?>
				</th>
				<th class="left" width="15%">
					<?php echo JText::_('COM_CJBLOG_RULE_DESCRIPTION'); ?>
				</th>
				<th class="left">
					<?php echo JText::_('COM_CJBLOG_DESCRIPTION'); ?>
				</th>
				<th class="left" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_NAME', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th class="left" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_USERNAME', 'u.username', $listDirn, $listOrder); ?>
				</th>
				<th class="left" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_POINTS', 'a.points', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_DATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="3%">
					<?php echo JHtml::_('grid.sort', 'COM_CJBLOG_PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="3%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item) :
			if ((!$user->authorise('core.manage')) && JAccess::check($item->id, 'core.manage')) {
				$canEdit	= false;
				$canChange	= false;
			}else{
				$canEdit	= true;
				$canChange	= true;
			}
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php if ($canEdit) : ?>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					<?php endif; ?>
				</td>
				<td class="left"><?php echo $this->escape($item->rule_title); ?></td>
				<td class="left"><?php echo $this->escape($item->rule_description); ?></td>
				<td class="left"><?php echo CJFunctions::clean_value($item->description, true); ?></td>
				<td><?php echo $this->escape($item->name);?></td>
				<td><?php echo $this->escape($item->username);?></td>
				<td><?php echo $this->escape($item->points);?></td>
				<td><?php echo $item->created;?></td>
				<td class="center">
					<a 
						class="btn btn-mini <?php echo $item->published == 1 ? 'btn-success' : 'btn-danger'?>" 
						href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=points&task='.($item->published == 1 ? 'unpublish' : 'publish').'&cid[]='.$item->id);?>">
						<i class="icon <?php echo $item->published == 1 ? 'icon-ok' : 'icon-remove'; ?> icon-white"></i>
					</a>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot><tr><td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
	</table>

	<div style="display: none;">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="points" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="userid" value="<?php echo $this->state['filter.userid']; ?>" />
		<span id="url-get-usernames"><?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users&task=search');?></span>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>