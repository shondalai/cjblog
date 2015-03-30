<?php
/**
 * @version		$Id: default.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$itemid = CJFunctions::get_active_menu_id();
$active_id = 7;
$app = JFactory::getApplication();
$page_heading = $this->params->get('page_heading');
$layout			 	= $this->params->get('layout', 'default');	
?>
<div id="cj-wrapper">

	<?php include_once JPATH_COMPONENT.'/helpers/header.php';?>
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params));?>
	
	<div class="container-fluid">	
		<div class="row-fluid">
			<div class="span12">
	    
		    	<?php if(!empty($page_heading)):?>
		    	<h1 class="nopad-top padbottom-5 page-header"><?php echo $this->escape($page_heading);?></h1>
		    	<?php endif;?>
			
				<?php if(!empty($this->badgegroups)):?>
				<?php foreach($this->badgegroups as $group):?>
				<h2><?php echo JText::_($group['title']);?></h2>
				<table class="table table-bordered table-hover badges">
					<?php foreach($group['badges'] as $i=>$badge):?>
					<tr>
						<td width="25%" nowrap="nowrap">
							<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users&task=badge&id='.$badge->badge_id.':'.$badge->alias.$users_itemid);?>">
								<?php if(!empty($badge->icon)):?>
								<img src="<?php echo CJBLOG_BADGES_BASE_URI.$badge->icon;?>"/>
								<?php else:?>
								<span class="badge <?php echo $badge->css_class;?>">&bull; <?php echo $this->escape($badge->title);?></span>
								<?php endif;?>
							</a>
							
							<?php if($badge->num_assigned > 1):?>
							<strong>&nbsp;x&nbsp;<?php echo $badge->num_assigned;?></strong>
							<?php endif;?>
						</td>
						<td>
							<?php echo $this->escape($badge->description);?>
						</td>
					</tr>
					<?php endforeach;?>
				</table>
				<?php endforeach;?>
				<?php else:?>
					<?php echo JText::_('LBL_NO_RESULTS_FOUND');?>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>