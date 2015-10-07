<?php
/**
 * @package     corejoomla.site
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2015 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined ( '_JEXEC' ) or die;

$app 				= JFactory::getApplication();
$api				= new CjLibApi();
$itemid 			= CJFunctions::get_active_menu_id();
$jform_itemid 		= CJFunctions::get_active_menu_id(true, 'index.php?option=com_content&view=form&layout=edit');
$active_id 			= $this->active_id;
$child_categories 	= !empty($this->category) ? $this->category->getChildren() : false;
$page_heading 		= $this->params->get('page_heading');
$task 				= $app->input->getCmd('task', '');
$cat_alias 			= !empty($this->category) ? '&catid='.$this->category->id : '';
$layout			 	= $this->params->get('layout', 'default');
$return				= base64_encode(JRoute::_('index.php'));
$profileApp			= $this->params->get('profile_component', 'cjblog');
$avatarApp			= $this->params->get('avatar_component', 'cjblog');
$avatarSize			= $this->params->get('list_avatar_size', 48);
?>

<div id="cj-wrapper">

	<?php include_once JPATH_COMPONENT.'/helpers/header.php';?>
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params));?>
	
	<div class="container-fluid category-table">
		<div class="row-fluid">
			<div class="span12">
	    
		    	<?php if(!empty($page_heading)):?>
		    	<h1 class="nopad-top padbottom-5 page-header"><?php echo $this->escape($page_heading);?></h1>
		    	<?php endif;?>
		    	
				<?php if(!empty($this->page_description)):?>
				<div class="well well-small"><?php echo $this->page_description;?></div>
				<?php endif;?>
		    	
		    	<?php if(!empty($this->category) && count($child_categories) > 0):?>
		    	<div class="accordion" id="sub-categories">
		    		<div class="accordion-group">
		    			<div class="accordion-heading">
		    				<?php 
		    				if($user->authorise('core.create', 'com_content'))
		    				{
		    					if($this->params->get('form_handler', 'cjblog') == 'cjblog')
		    					{
		    						?>
		    						<a href="<?php echo CjBlogHelperRoute::getFormRoute().'&return='.$return;?>" class="pull-right accordion-toggle">
		    							<strong><i class="icon-edit"></i> <?php echo JText::_('LBL_SUBMIT_ARTICLE');?></strong>
		    						</a>
		    						<?php
		    					}
		    					else 
		    					{
		    						?>
		    						<a href="<?php echo JRoute::_('index.php?option=com_content&task=article.add'.$jform_itemid);?>" class="pull-right accordion-toggle">
		    							<strong><i class="icon-edit"></i> <?php echo JText::_('LBL_SUBMIT_ARTICLE');?></strong>
		    						</a>
		    						<?php 
		    					}
		    				}
		    				?>
		    				<a class="accordion-toggle" data-toggle="collapse" data-parent="#sub-categories" href="#sub-categories-content">
		    					<strong><?php echo JText::_('LBL_RELATED_TOPICS');?> <i class="icon-chevron-down"></i></strong>
		    				</a>
		    			</div>
		    			<div id="sub-categories-content" class="accordion-body collapse in">
		    				<div class="accordion-inner">
		    					<?php echo CjBlogHelper::get_category_table(
		    							$child_categories, 
		    							$this->params, 
		    							array('base_url'=>'index.php?option='.CJBLOG.'&view=categories&task=latest', 'itemid' => $articles_itemid));?>
		    				</div>
		    			</div>
		    		</div>
		    	</div>
		    	<?php elseif($user->authorise('core.create', 'com_content')):?>
		    	<div class="accordion panel panel-default" id="sub-categories">
		    		<div class="accordion-group">
		    			<div class="accordion-heading">
		    				<?php if($this->params->get('form_handler', 'cjblog') == 'cjblog'):?>
		    				<a href="<?php echo CjBlogHelperRoute::getFormRoute().'&return='.$return;?>" class="accordion-toggle">
		    					<strong><i class="icon-edit"></i> <?php echo JText::_('LBL_SUBMIT_ARTICLE');?></strong>
		    				</a>
		    				<?php else:?>
		    				<a href="<?php echo JRoute::_('index.php?option=com_content'.$jform_itemid);?>" class="accordion-toggle">
		    					<strong><i class="icon-edit"></i> <?php echo JText::_('LBL_SUBMIT_ARTICLE');?></strong>
		    				</a>
		    				<?php endif;?>
		    			</div>
		    		</div>
		    	</div>
		    	<?php endif;?>
		    	
		    	<?php 
		    	if(!empty($this->articles))
		    	{
		    		echo JLayoutHelper::render('default.articles_list', array('items'=>$this->articles, 'pagination'=>$this->pagination, 'params'=>$this->params, 'category'=>$this->category));
		    	}
		    	else
		    	{
		    		?>
			    	<p><?php echo JText::_('LBL_NO_RESULTS_FOUND');?></p>
		    		<?php 
		    	}
		    	?>
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span12">
				<?php 
				echo CJFunctions::get_pagination(
						$this->page_url, 
						$this->pagination->get('pages.start'), 
						$this->pagination->get('pages.current'), 
						$this->pagination->get('pages.total'),
						$app->getCfg('list_limit', 20),
						true
					);
				?>
			</div>
		</div>
	</div>
</div>