<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$data 		= $displayData['data'];
$params		= $data->params;
?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<h3 class="cjheader"><?php echo JText::_('COM_CJBLOG_ARTICLES');?></h3>
		<?php if(!empty($data->summary->articles)):?>
		<ul class="list-summary">
			<?php foreach ($data->summary->articles as $item):?>
			<li><?php echo JHtml::link(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language), $this->escape($item->title), array('title'=>$this->escape($item->title)));?></li>
			<?php endforeach;?>
		</ul>
		<?php else:?>
		<p><?php echo JText::_('COM_CJBLOG_NO_RESULTS_FOUND');?></p>
		<?php endif;?>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<h3 class="cjheader"><?php echo JText::_('COM_CJBLOG_FAVORITES');?></h3>
		<?php if(!empty($data->summary->favorites)):?>
		<ul class="list-summary">
			<?php foreach ($data->summary->favorites as $item):?>
			<li><?php echo JHtml::link(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language), $this->escape($item->title), array('title'=>$this->escape($item->title)));?></li>
			<?php endforeach;?>
		</ul>
		<?php else :?>
		<p><?php echo JText::_('COM_CJBLOG_NO_RESULTS_FOUND');?></p>
		<?php endif;?>
	</div>
</div>
<div class="row margin-bottom-20">
	<div class="col-md-12">
		<h3 class="cjheader"><?php echo JText::_('COM_CJBLOG_BADGES');?></h3>
		<?php 
		if(!empty($data->summary->badges))
		{
			foreach ($data->summary->badges as $item)
			{
				?>
				<div class="col-lg-2 col-md-3 col-sm-6">
					<div class="cjblog-badge" data-title="<?php echo $this->escape($item['title']);?>" data-html="true" data-container="body"
						data-content="<?php echo $this->escape($item['description']);?>" data-toggle="popover" data-placement="auto" data-trigger="hover">
						<?php 
						if(!empty($item['icon']))
						{
							?><img alt="<?php echo $this->escape($item['title'])?>" src="<?php echo $item['icon'];?>" style="max-width: 96px;"><?php 
						}
						else 
						{
							?><span class="badge <?php echo $item['css_class'];?>"><i class="fa fa-dot-circle-o"></i> <?php echo $this->escape(strip_tags($item['title']));?></span><?php 
						}
						
						if($item['num_times'] > 1)
						{
							?><small>x <?php echo $item['num_times'];?></small><?php
						}
						?>
					</div>
				</div>
				<?php 
			}
		}
		else 
		{
			?><p><?php echo JText::_('COM_CJBLOG_NO_RESULTS_FOUND');?></p><?php 
		}
		?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h3 class="cjheader"><?php echo JText::_('COM_CJBLOG_REPUTATION');?></h3>
		<?php if(!empty($data->summary->reputation)):?>
		<ul class="list-summary">
			<?php foreach ($data->summary->reputation as $item):?>
			<li title="<?php echo strip_tags($item->title);?>">
				<span class="label label-<?php echo $item->points > 0 ? 'success' : 'danger';?>"><?php echo $item->points;?></span> <?php echo $item->title;?>
			</li>
			<?php endforeach;?>
		</ul>
		<?php else :?>
		<p><?php echo JText::_('COM_CJBLOG_NO_RESULTS_FOUND');?></p>
		<?php endif;?>
	</div>
</div>