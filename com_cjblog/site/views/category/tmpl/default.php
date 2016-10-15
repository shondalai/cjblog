<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('behavior.caption');

$params		= $this->params;
$layout 	= $params->get('ui_layout', 'default');
$theme 		= $params->get('theme', 'default');
$category	= $this->category;
$params->set('show_parent', false);
?>
<div id="cj-wrapper" class="articles-list<?php echo $this->pageclass_sfx;?>">
	<?php 
	echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$params, 'state'=>$this->state));
	
	if($params->get('list_show_search_form', 1) == 1 || $params->get('show_category_list', 1) == 1)
	{
		?>
		<div class="panel panel-<?php echo $theme;?>">
			<?php 
			if($params->get('show_category_list', 1) == 1)
			{
				?>
				<div class="panel-heading">
					<h2 class="panel-title">
						<span><i class="fa fa-folder-open"></i> <?php echo JText::_('COM_CJBLOG_CATEGORIES_LABEL');?></span>
						<?php if(!empty($category->parent_id)):?>
						<a href="<?php echo JRoute::_(CjBlogHelperRoute::getCategoryRoute($category));?>">
							<small>: <?php echo $this->escape($category->title). ($category->numitems ? ' ('.$category->numitems.')'  : '');?></small> 
						</a>
						<?php endif;?>
					</h2>
				</div>
				<?php 
			}
			?>
			<div class="panel-body">
				<?php 
				if 
				(
					$params->get('show_category_list', 1) == 1 &&  !empty($category->id) && 
					( ( !empty($category->description) && $params->get('show_description') ) || count($category->getChildren()) > 0 )
				)
				{
				    
					echo JLayoutHelper::render($layout.'.category_list', array('category'=>$category, 'params'=>$params, 'maxlevel'=>1), '', array('debug' => false));
				}
				
				if( $params->get('list_show_search_form', 1) == 1 )
				{
					echo JLayoutHelper::render($layout.'.search_form', array('params'=>$params, 'state'=>$this->state, 'catid'=>(isset($category->id) ? $category->id : 0)), '', array('debug' => false));
				} 
				?>
			</div>
		</div>
		<?php 
	}
	
	echo JLayoutHelper::render($layout.'.articles_list', array('data'=>$this), '', array('debug'=>false));
	echo JLayoutHelper::render($layout.'.credits', array('params'=>$params));?>
</div>