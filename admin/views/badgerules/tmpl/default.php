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

<form action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgerules');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar clearfix">
		<div class="filter-search">
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state['filter.search']); ?>" title="<?php echo JText::_('COM_CJBLOG_SEARCH_BADGE_RULES'); ?>" />
			<button type="submit" class="btn btn-primary"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="btn" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_RESET'); ?></button>
		</div>
	</fieldset>

	<table class="adminlist table table-stripped table-hover">
		<thead>
			<tr>
				<th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
				<th width="10%"><?php echo JText::_('COM_CJBLOG_TITLE'); ?></th>
				<th class="nowrap" width="10%"><?php echo JHtml::_('grid.sort', 'COM_CJBLOG_BADGE', 'b.badge_id', $listDirn, $listOrder); ?></th>
				<th class="nowrap" width="10%"><?php echo JHtml::_('grid.sort', 'COM_CJBLOG_RULE_NAME', 'b.rule_name', $listDirn, $listOrder); ?></th>
				<th class="nowrap" width="5%"><?php echo JHtml::_('grid.sort', 'COM_CJBLOG_ASSET_NAME', 'b.asset_name', $listDirn, $listOrder); ?></th>
				<th class="nowrap" width="10%"><?php echo JHtml::_('grid.sort', 'COM_CJBLOG_ASSET_TITLE', 'b.asset_name', $listDirn, $listOrder); ?></th>
				<th class="nowrap" width="3%"><?php echo JHtml::_('grid.sort', 'COM_CJBLOG_PUBLISHED', 'b.published', $listDirn, $listOrder); ?></th>
				<th class="nowrap" width="5%"><?php echo JHtml::_('grid.sort', 'COM_CJBLOG_ACCESS', 'b.access', $listDirn, $listOrder); ?></th>
				<th><?php echo JText::_('COM_CJBLOG_DESCRIPTION'); ?></th>
				<th><?php echo JText::_('COM_CJBLOG_RULE_CONTENT'); ?></th>
				<th class="nowrap" width="3%"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'b.id', $listDirn, $listOrder); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
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
				<td>
					<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgerules&task=edit&id='.(int) $item->id); ?>" 
						title="<?php echo JText::sprintf('COM_CJBLOG_EDIT_BADGE_RULE', $this->escape($item->title)); ?>">
						<?php echo $this->escape($item->title); ?>
					</a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
				</td>
				<td class="center"><span class="badge <?php echo $item->css_class;?>">&bull; <?php echo $this->escape($item->badge_title);?></span></td>
				<td class="center"><?php echo $this->escape($item->rule_name); ?></td>
				<td class="center"><?php echo $this->escape($item->asset_name); ?></td>
				<td class="center"><?php echo JText::_($item->asset_title, true); ?></td>
				<td class="center">
					<a class="btn btn-mini <?php echo $item->published == 1 ? 'btn-success' : 'btn-danger'?>" href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgerules&task='.($item->published == 1 ? 'unpublish' : 'publish').'&cid[]='.$item->id);?>">
						<i class="icon <?php echo $item->published == 1 ? 'icon-ok' : 'icon-remove'; ?> icon-white"></i>
					</a>
				</td>
				<td><?php echo $this->escape($item->viewlevel); ?></td>
				<td class="left"><?php echo $this->escape($item->description); ?></td>
				<td class="left"><?php echo $this->escape($item->rule_content); ?></td>
				<td class="center"><?php echo (int) $item->id; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="badgerules" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


<form name="import-rules-form" id="import-rules-form" action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgerules&task=import`')?>" enctype="multipart/form-data" method="post">
	<div class="well">
		<label><?php echo JText::_('COM_CJBLOG_IMPORT_BADGE_INFO');?></label>
		<input type="file" name="rule-file">
		<input type="submit" class="btn">
	</div>
</form>