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
?>
<div id="cj-wrapper" class="profile-badges">
	<div class="row">
		<?php 
		foreach ($data->items as $item)
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
		?>
	</div>
</div>