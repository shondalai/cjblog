<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();
?>

<form action="<?php echo JRoute::_('index.php?option=com_cjblog&view=badgerule');?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<ul class="unstyled">
			<?php foreach($this->items as $app):?>
			<li>
				<h3><?php echo $this->escape($app['title']);?></h3>
				<ul>
					<?php foreach ($app['templates'] as $template):?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_cjblog&task=badgerule.edit&id=0&app='.$template->appname.'&asset='.$template->asset_name);?>">
							<?php echo $this->escape($template->description);?>
						</a>
					</li>
					<?php endforeach;?>
				</ul>
			</li>
			<?php endforeach;?>
		</ul>
		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="badgerules" />
		<input type="hidden" name="boxchecked" value="0" />
		
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
