<?php
/**
 * @package     corejoomla.site
 * @subpackage  com_cjblogarhive
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$baseUrl = CjBlogHelperRoute::getArticlesRoute();
?>
<div class="cjblog-archive-list">
	<ul class="cat-list year">
		<?php
		foreach ($archives as $year=>$nodes)
		{
			?>
			<li rel="<?php echo $year;?>">
				<a href="<?php echo JRoute::_($baseUrl.'&year='.$year);?>">
					<?php echo $year;?>
				</a>
				
				<ul class="cat-list month">
					<?php 
					foreach ($nodes as $node)
					{
						?>
						<li rel="<?php echo $year.$node->month;?>">
							<a href="<?php echo JRoute::_($baseUrl.'&year='.$year.'&month='.$node->month);?>">
								<?php echo DateTime::createFromFormat('!m', $node->month)->format('F');?> <span class="muted text-muted"><small>(<?php echo $node->count;?>)</small></span>
							</a>
						</li>
						<?php
					}
					?>
				</ul>
			</li>
			<?php
		}
		?>
	</ul>
</div>