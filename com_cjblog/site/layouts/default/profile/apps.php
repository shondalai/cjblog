<?php
/**
 * @package     corejoomla.site
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2018 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$item 		        = $displayData['item'];
$apps 		        = $displayData['apps'];
$params  			= $item->params;
$profileUri 		= CjBlogHelperRoute::getProfileRoute($item->id);
?>
<div class="tabpanel" id="profile-tabs">
	<ul class="nav nav-tabs" role="tablist" style="border-bottom: none;">
		<?php 
		$tabs		= $apps->tabs;
		$maxTabs 	= (int) $params->get('profile_max_display_tabs', 4);
		$maxTabs	= count($tabs) > $maxTabs ? $maxTabs : count($tabs);
		$activated 	= false;
		
		for($i = 0; $i < $maxTabs; $i++)
		{
			$activated = ( $activated || $tabs[$i]->id == $apps->id ) ? true : false;
			?>
			<li role="presentation"<?php echo $tabs[$i]->id == $apps->id ? ' class="active"' : '';?>>
				<a href="<?php echo JRoute::_($profileUri.'&tab='.$tabs[$i]->id);?>">
					<i class="<?php echo $tabs[$i]->icon;?>"></i> <?php echo $this->escape($tabs[$i]->title);?>
				</a>
			</li>
			<?php
		}
		
		if(count($tabs) > $maxTabs)
		{
			?>
			<li role="presentation" class="dropdown<?php echo ! $activated ? ' active' : '';?>">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
					<i class="fa fa-cogs"></i> <?php echo JText::_('COM_CJFORUM_APPS_LABEL');?> <span class="caret"></span>
				</a>
				
				<?php 
				$currentIdx 	= 0;
				$totalRows 		= count($tabs) - $maxTabs;
				$numCols 		= $totalRows > 5 ? 2 : 1;
				$span 			= 12 / $numCols;
				$maxRows 		= ceil($totalRows / $numCols);
				?>
				<ul class="dropdown-menu multi-column columns-<?php echo $numCols;?>" role="menu">
					<li>
						<div class="row">
							<?php 
							for($i = $maxTabs; $i < count($tabs); $i++)
							{
								if($currentIdx == 0 || $currentIdx == $maxRows)
								{
									?>
									<div class="span<?php echo $span;?> col-sm-<?php echo $span;?>">
										<ul class="multi-column-dropdown">
									<?php
								}
								?>
								<li role="presentation"<?php echo $tabs[$i]->id == $apps->id ? ' class="active"' : '';?>>
									<a href="<?php echo JRoute::_($profileUri.'&tab='.$tabs[$i]->id);?>">
										<i class="<?php echo $tabs[$i]->icon;?>"></i> <?php echo $this->escape($tabs[$i]->title);?>
									</a>
								</li>
								<?php
								if($currentIdx == $maxRows - 1 || $currentIdx == $totalRows)
								{
									?>
										</ul>
									</div>
									<?php
								}
								$currentIdx++;
							}
							?>
						</div>
					</li>
				</ul>
			</li>
			<?php
		}
		?>
	</ul>
</div>
<hr class="no-space-top">
<div class="tab-content">
	<div role="tabpanel" class="tab-pane active">
		<?php echo $apps->content;?>
	</div>
</div>