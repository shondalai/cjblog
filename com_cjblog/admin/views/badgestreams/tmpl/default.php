<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

use Joomla\String\StringHelper;

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
	$saveOrderingUrl = 'index.php?option=com_cjblog&task=badgestreams.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'badgeList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
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
        			dirn = 'asc';
        		}
        		else
        		{
        			dirn = direction.options[direction.selectedIndex].value;
        		}
        		Joomla.tableOrdering(order, dirn, '');
        	}
        </script>
        
        <form action="<?php echo JRoute::_('index.php?option=com_cjblog&view=badgestreams'); ?>" method="post" name="adminForm" id="adminForm">
    		<?php
    		// Search tools bar
    		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options'=>array('filterButton'=>true)));
    		?>
    		<?php if (empty($this->items)) : ?>
    			<div class="alert alert-no-items">
    				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
    			</div>
    		<?php else : ?>
			<table class="table table-striped" id="badgeList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'a.created', $listDirn, $listOrder, null, 'desc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
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
						<th>
							<?php echo JText::_('COM_CJBLOG_BADGE_LABEL');?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
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
					{
						$item->max_ordering = 0; // ??
						$ordering = ($listOrder == 'a.ordering');
						$canChange = $user->authorise('core.edit.state', 'com_cjblog');
						?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->id; ?>">
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
									<?php echo JHtml::_('jgrid.published', $item->published, $i, 'badgestreams.', $canChange, 'cb'); ?>
									<?php
										// Create dropdown items
										$action = $archived ? 'unarchive' : 'archive';
										JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'badgestreams');
										
										$action = $trashed ? 'untrash' : 'trash';
										JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'badgestreams');
										
										// Render dropdown list
										echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
									?>
								</div>
							</td>
							<td class="has-context">
								<div class="pull-left">
									<?php echo $item->title; ?>
									<small class="muted text-muted"><?php echo StringHelper::substr(strip_tags($item->description), 0, 120);?></small>
								</div>
							</td>
							<td>
								<?php if(!empty($item->icon)):?>
								<img alt="<?php echo $this->escape($item->badge_name)?>" src="<?php echo $item->icon;?>" title="<?php echo $this->escape($item->badge_name);?>">
								<?php else :?>
								<span class="badge <?php echo $item->css_class;?>"><i class="fa fa-dot-circle-o"></i> <?php echo $this->escape($item->badge_name);?></span>
								<?php endif;?>
							</td>
							<td class="small hidden-phone">
								<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->created_by); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
									<?php echo $this->escape($item->author_name); ?>
								</a>
							</td>
							<td class="nowrap small hidden-phone">
								<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
							</td>
							<td class="center hidden-phone">
								<?php echo (int) $item->id; ?>
							</td>
						</tr>
						<?php 
					}
					?>
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
