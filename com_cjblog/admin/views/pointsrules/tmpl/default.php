<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

if(CJBLOG_MAJOR_VERSION < 4) {
    JHtml::_('bootstrap.tooltip');
    JHtml::_('behavior.multiselect');
    JHtml::_('formbehavior.chosen', 'select');
}

$app 		= JFactory::getApplication();
$user 		= JFactory::getUser();
$userId 	= $user->id;
$listOrder 	= $this->escape($this->state->get('list.ordering'));
$listDirn 	= $this->escape($this->state->get('list.direction'));
$archived 	= $this->state->get('filter.published') == 2 ? true : false;
$trashed 	= $this->state->get('filter.published') == - 2 ? true : false;
$saveOrder 	= $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_cjblog&task=pointsrules.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'pointsrulesList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
$rowClass   = CJBLOG_MAJOR_VERSION < 4 ? 'row-fluid' : 'row';
$span		= !empty( $this->sidebar) ? 'col-md-10' : 'col-md-12';
?>
<div id="cj-wrapper" class="<?php echo $rowClass;?>">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="col-md-2">
		<?php echo $this->sidebar; ?>
	</div>
	<?php endif;?>
	<div id="j-main-container">
        <script type="text/javascript">
        	Joomla.orderTable = function()
        	{
        		table = document.getElementById("sortTable");
        		direction = document.getElementById("directionTable");
        		order = table.options[table.selectedIndex].value;
        		if (order != '<?php echo $listOrder; ?>')
        		{
        			dirn = 'desc';
        		}
        		else
        		{
        			dirn = direction.options[direction.selectedIndex].value;
        		}
        		Joomla.tableOrdering(order, dirn, '');
        	}
        </script>

		<form action="<?php echo JRoute::_('index.php?option=com_cjblog&view=pointsrules'); ?>" method="post" name="adminForm" id="adminForm">
    		<?php
    		// Search tools bar
    		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    		?>
    		
			<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
			<?php else : ?>
			<table class="table table-striped" id="pointsrulesList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="hidden-phone">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" style="min-width: 55px" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="5%">
							<?php echo JHtml::_('searchtools.sort', 'COM_CJBLOG_POINTS', 'a.points', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'COM_CJBLOG_APP_NAME', 'a.app_name', $listDirn, $listOrder); ?>
						</th>
						<th width="15%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'COM_CJBLOG_RULE_NAME', 'a.rule_name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
    			foreach ($this->items as $i => $item)
    			:
    				$item->max_ordering = 0; // ??
    				$ordering = ($listOrder == 'a.created');
    				$canCreate = $user->authorise('core.create', 'com_cjblog');
    				$canEdit = $user->authorise('core.edit', 'com_cjblog');
    				$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
    				$canEditOwn = $user->authorise('core.edit.own', 'com_cjblog') && $item->created_by == $userId;
    				$canChange = $user->authorise('core.edit.state', 'com_cjblog') && $canCheckin;
    				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="order nowrap center hidden-phone">
							<?php
								$iconClass = '';
								if (! $canChange)
								{
									$iconClass = ' inactive';
								}
								elseif (! $saveOrder)
								{
									$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
								}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>"> <i class="icon-menu"></i></span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display: none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php endif; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'pointsrules.', $canChange, 'cb'); ?>
							</div>
						</td>
						<td class="has-context">
							<div class="pull-left">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'pointsrules.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_cjblog&task=pointsrule.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
										<?php echo $this->escape($item->title); ?>
									</a>
								<?php else : ?>
									<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
								<?php endif; ?>
							</div>
							<div class="small"> &nbsp;&raquo; <?php echo strip_tags($item->description);?></div>
						</td>
						<td>
							<?php echo (int) $item->points; ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo $this->escape($item->app_name); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo $this->escape($item->rule_name); ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $this->escape($item->access_level); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
    		<?php endif; ?>
    		<?php echo $this->pagination->getListFooter(); ?>
    
    		<input type="hidden" name="task" value="" /> 
    		<input type="hidden" name="boxchecked" value="0" />
    		<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>
