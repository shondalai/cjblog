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
$profileApp			= $this->params->get('profile_component', 'cjblog');
$layout			 	= $this->params->get('layout', 'default');
$return				= base64_encode(JRoute::_('index.php'));
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
		    	
		    	<?php if(!empty($this->articles)):?>
		    	<?php foreach($this->articles as $article):?>
		    	<div class="article-block padbottom-20 clearfix">
		    		<?php if($this->params->get('show_thumbnails')):?>
		    		<div class="<?php echo $this->params->get('thumbnail_size') == 0 ? 'span2 col-md-2' : 'span3 col-md-3';?> nospace-left">
		    			<a class="thumbnail" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias));?>">
		    				<img alt="<?php echo $this->escape($article->title);?>" style="width: auto;" 
		    					src="<?php echo CjBlogHelper::get_article_thumbnail($article, ($this->params->get('thumbnail_size') == 0 ? 96 : 160));?>">
		    			</a>
		    		</div>
		    		<?php endif;?>
		    		<div class="<?php echo $this->params->get('show_thumbnails') ? ($this->params->get('thumbnail_size') == 0 ? 'span10' : 'span9') : 'span12';?>">
		    			
		    			<h3 class="nopad-top nopad-bottom">
							<?php if($article->hits > $this->params->get('hot_article_num_hits', 250)):?>
							<i class="icon-fire tooltip-hover" title="<?php echo JText::_('LBL_HOT');?>"></i>
							<?php endif;?>
		    				<?php if($this->params->get('link_titles')):?>
		    				<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias));?>">
		    					<?php echo $this->escape($article->title);?>
		    				</a>
		    				<?php else:?>
		    				<?php echo $this->escape($article->title);?>
		    				<?php endif;?>
		    			</h3>
		    			<div class="muted text-muted padbottom-5">
							<small class="align-middle">
								<?php 
								if($this->params->get('show_author')){
									
									if($this->params->get('link_author')){
										
										echo JText::sprintf('TXT_WRITTEN_BY', $api->getUserProfileUrl($profileApp, $article->created_by, false, $this->escape($article->display_name) )).' ';
									} else {
										
										echo JText::sprintf('TXT_WRITTEN_BY', $this->escape($article->display_name)).' ';
									}
								}
								
								$cat_name = $this->params->get('link_category')
									? JHtml::link(JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=latest&id='.$article->catid.':'.$article->category_alias.$articles_itemid), $this->escape($article->category_title))
									: $this->escape($article->category_title);
									
								if($this->params->get('show_category') && $this->params->get('show_create_date')){
									
									
									echo JText::sprintf('TXT_POSTED_IN_CATEGORY_ON', $cat_name, CJFunctions::get_localized_date($article->created, 'd F Y'));
								} else {
									
									if($this->params->get('show_category')){
										
										echo JText::sprintf('TXT_POSTED_IN_CATEGORY', $cat_name);
									}
									
									if($this->params->get('show_create_date')){
										
										echo JText::sprintf('TXT_POSTED_ON', CJFunctions::get_localized_date($article->created, 'd F Y'));
									}
								}
								?>
								
								<?php if($this->params->get('show_hits')):?>
								<i class="icon-eye-open"></i> <?php echo JText::sprintf('TXT_NUM_HITS', $article->hits)?>.
								<?php endif;?>
								
							</small>
						</div>
		    			<?php 
		    			if($this->params->get('show_intro')){
		    				echo CjBlogHelper::get_intro_text($article->introtext, $this->params->get('character_limit'));
		    			}
		    			?>
						<div class="tags margin-top-10 margin-bottom-10">
							<?php foreach($article->tags as $tag):?>
							<a title="<?php echo JText::sprintf('TXT_TAGGED_ARTICLES', $this->escape($tag->tag_text));?>" class="tooltip-hover label" 
								href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=tag&id='.$tag->tag_id.':'.$tag->alias.$itemid);?>">
								<?php echo $this->escape($tag->tag_text);?>
							</a>
							<?php endforeach;?>
						</div>
		    			<?php if($this->params->get('show_readmore')):?>
		    			<div>
		    				<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias));?>">
		    					<?php echo JText::_('LBL_READ_MORE');?>..
		    				</a>
		    			</div>
		    			<?php endif;?>
		    		</div>
		    	</div>
		    	<?php endforeach;?>
		    	<?php else:?>
		    	<p><?php echo JText::_('LBL_NO_RESULTS_FOUND');?></p>
		    	<?php endif;?>
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