<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$data 		= $displayData['data'];
?>
<div id="cj-wrapper" class="profile-reputation">
	<?php if(!empty($data->items)):?>
	<h3 class="cjheader"><?php echo JText::sprintf('COM_CJBLOG_REPUTATION_HEADING', CjLibUtils::formatNumber($data->item->points));?></h3>
	<table class="table table-hover table-striped">
		<?php foreach ($data->items as $item):?>
		<tr>
			<th>
				<div title="<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2'));?>" data-toggle="tooltip">
					<?php echo CjLibDateUtils::getShortDate($item->created);?>
				</div>
			</th>
			<td>
				<span class="label label-<?php echo $item->points > 0 ? 'success' : 'danger';?>"><?php echo $item->points;?></span>
			</td>
			<td>
				<?php echo $item->title;;?>
			</td>
		</tr>
		<?php endforeach;?>
	</table>
	
	<?php if (!empty($data->items)) : ?>
	<?php if (($data->params->def('show_pagination', 2) == 1  || ($data->params->get('show_pagination') == 2)) && ($data->pagination->pagesTotal > 1)) : ?>
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
			<div class="pagination">
				<?php if ($data->params->def('show_pagination_results', 1)) : ?>
					<p class="counter pull-right">
						<?php echo $data->pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>
		
				<?php echo $data->pagination->getPagesLinks(); ?>
			</div>
		</form>
	<?php endif; ?>
	<?php  endif; ?>
	
	<?php else :?>
	<div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo JText::_('COM_CJBLOG_NO_RESULTS_FOUND')?></div>
	<?php endif;?>
</div>