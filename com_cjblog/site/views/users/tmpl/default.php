<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$theme 				= $this->params->get('theme', 'default');
$avatarComponent	= $this->params->get('avatar_component', 'cjblog');
$avatarSize			= $this->params->get('user_avatar_size', 16);
$profileComponent 	= $this->params->get('profile_component', 'cjblog');
$layout 			= $this->params->get('ui_layout', 'default');
$displayName		= $this->params->get('display_name', 'name');

$api = new CjLibApi();
?>
<div id="cj-wrapper" class="activity-details<?php echo $this->pageclass_sfx;?>">
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params, 'state'=>$this->state));?>
	
	<form id="UsersForm" action="<?php echo JRoute::_('index.php?option=com_cjblog&view=users')?>" method="post">
		<div class="form-inline" role="form">
			<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));?>
		</div>
		
		<div class="panel panel-<?php echo $theme;?>">
			<div class="panel-heading">
				<div class="panel-title"><?php echo JText::_('COM_CJBLOG_USERS_HOME');?></div>
			</div>
			<?php 
			if(!empty($this->items))
			{
			?>
			<div class="table-responsive">
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<?php if($avatarComponent != 'none'):?>
							<th class="hidden-phone" width="<?php echo $avatarSize + 10?>px"></th>
							<?php endif;?>
							<th><?php echo JText::_('COM_CJBLOG_LABEL_USERNAME')?></th>
							<th><?php echo JText::_('COM_CJBLOG_LABEL_REGISTRATION_DATE')?></th>
							<th><?php echo JText::_('COM_CJBLOG_LABEL_ARTICLES')?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($this->items as $item)
					{
						$author = $this->escape($item->$displayName);
						$profileUrl = $api->getUserProfileUrl($profileComponent, $item->id);
						?>
						<tr>
							<?php if($avatarComponent != 'none'):?>
							<td class="hidden-phone">
								<?php if($profileComponent != 'none'):?>
								<a href="<?php echo $profileUrl;?>" class="thumbnail no-margin-bottom">
									<img alt="<?php echo $author?>" src="<?php echo $api->getUserAvatarImage($avatarComponent, $item->id, $item->email, $avatarSize);?>" style="max-width: 16px;">
								</a>
								<?php else :?>
								<div class="thumbnail">
									<img alt="<?php echo $author?>" src="<?php echo $api->getUserAvatarImage($avatarComponent, $item->id, $item->email, $avatarSize, true);?>" style="max-width: 16px;">
								</div>
								<?php endif;?>
							</td>
							<?php endif;?>
							<td>
								<a href="<?php echo $profileUrl;?>"><?php echo $author;?></a>
							</td>
							<td>
								<?php echo JHtml::_('date', $item->registerDate, 'D M j, Y, g:i a'); ?>
							</td>
							<td><?php echo $item->num_articles;?></td>
						</tr>
					<?php 
					}
					?>
					</tbody>
				</table>
			</div>
			<?php 
			if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) 
			{
			?>
			<div class="panel-footer">
				<div class="pagination no-margin-top no-margin-bottom">
					<?php if ($this->params->def('show_pagination_results', 1)) : ?>
						<p class="counter pull-right">
							<?php echo $this->pagination->getPagesCounter(); ?>
						</p>
					<?php endif; ?>
		
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
			</div>
			<?php
			} 
			
			}
			else
			{
				?>
				<div class="panel-body">
					<i class="fa fa-info-circle"></i> <?php echo JText::_('COM_CJBLOG_NO_RESULTS_FOUND')?>
				</div>
				<?php 
			}?>
		</div>
	</form>
	
	<?php echo JLayoutHelper::render($layout.'.credits', array('params'=>$this->params));?>
</div>
