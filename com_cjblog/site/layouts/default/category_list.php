<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$user 		= JFactory::getUser();
$access 	= $user->getAuthorisedViewLevels();
$params 	= $displayData['params'];
$theme 		= $params->get('theme', 'default');
$item 		= $displayData['category'];
$maxlevel 	= $displayData['maxlevel'];
$columns	= (int) $params->get('max_category_columns', 3);
$spanLg 	= (int) 12 / $columns;
$spanMd 	= (int) 12 / $columns;;
$spanSm 	= 6;
$spanXs 	= 12;

if(!in_array($item->id, $params->get('exclude_categories', array())))
{
	?>
	<div class="media">
		<?php 
		$categoryUrl = CjBlogHelperRoute::getCategoryRoute($item);
		if($params->get('show_description_image', 1) == 1 && !empty($catImage))
		{
			?>
			<div class="media-left hidden-phone">
				<a href="#" class="thumbnail" onclick="return false;">
					<img src="<?php echo $catParams->get('image');?>" alt="<?php echo $this->escape($catParams->get('image_alt'));?>" class="media-object" style="max-width: 128px;">
				</a>
			</div>
			<?php 
		}
		?>
		<div class="media-body">
	        <?php 
	        if($params->get('show_parent') && $item->parent_id)
	        {
	            ?>
	            <h4 class="panel-title">
	                <a href="<?php echo JRoute::_($categoryUrl);?>"><?php echo $this->escape($item->title);?></a>
	                
	                <?php if($params->get('show_feed_link', 1) == 1):?>
	        		<a href="<?php echo JRoute::_($categoryUrl.'&format=feed&type=rss');?>" 
	        			title="<?php echo JText::_('COM_CJBLOG_RSS_FEED');?>" data-toggle="tooltip">
	        			<sup class="margin-left-5"><small><i class="fa fa-rss-square"></i></small></sup>
	        		</a>
	        		<?php endif;?>
	    		</h4>
	            <?php
	        }
	        
	        if($params->get('show_parent') && $item->parent_id)
	        {
	        	?>
	            <hr class="no-margin-top margin-bottom-5">
	            <?php
	        }
	
	        if(!empty($item->description) && $params->get('show_description'))
	        {
	        	echo JHtml::_('content.prepare', $item->description, '', 'com_cjblog.categories');
	        }
			?>
		</div>
	</div>
	<?php 
	if ($maxlevel != 0 && count($item->getChildren()) > 0) 
	{
		$categories = $item->getChildren();
		$itemNum = 0;
		?>
		<div class="row-fluid">
	    	<?php
	    	foreach ($categories as $node)
	    	{
	    		if(in_array($node->access, $access) && !in_array($node->id, $params->get('exclude_categories', array())))
	    		{
	    			?>
	    			<div class="span<?php echo $spanMd?> margin-bottom-5 no-margin-left">
						<a href="<?php echo JRoute::_(CjBlogHelperRoute::getCategoryRoute($node));?>">
							<?php echo $this->escape($node->title);?>
							<span class="text-muted visible-phone">(<?php echo JText::plural('COM_CJBLOG_NUM_ARTICLES', $node->numitems);?>)</span>
						</a>
						
						<?php if($params->get('show_feed_link', 1) == 1):?>
						<a href="<?php echo JRoute::_(CjBlogHelperRoute::getCategoryRoute($node).'&format=feed&type=rss');?>" 
							title="<?php echo JText::_('COM_CJBLOG_RSS_FEED');?>" data-toggle="tooltip">
							<sup class="margin-left-5"><small><i class="fa fa-rss-square"></i></small></sup>
						</a>
						<?php endif;?>
						
						<small class="text-muted hidden-phone">(<?php echo $node->numitems;?>)</small>
	    			</div>
	    			<?php
	    			$itemNum++;
	    		} 
	    	}
	    	?>
	    </div>
	    <?php
	}
}