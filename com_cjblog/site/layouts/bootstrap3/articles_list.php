<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$user 			= JFactory::getUser();
$api 			= new CjLibApi();

$data			= $displayData['data'];
$state			= $data->state;
$pagination 	= $data->pagination;
$items 			= $data->items;
$params			= $data->params;

$category		= isset($data->category) ? $data->category : null;
$heading 		= isset($data->heading) ? $data->heading : JText::_('COM_CJBLOG_ARTICLES');

$subHeading 	= ($category && $category->id > 1) ? ' <small>['.$this->escape($category->title).']</small>' : '';
$theme 			= $params->get('theme', 'default');
$avatarApp		= $params->get('avatar_component', 'cjblog');
$profileApp		= $params->get('profile_component', 'cjblog');
$avatarSize 	= $params->get('list_avatar_size', 48);
$thumbSize		= $params->get('list_thumbnail_size', 96);

// echo '<pre>'.print_r($params, true).'</pre>';

if(!empty($items))
{
	if(!empty($heading) && $params->get('list_show_pagetitle'))
	{
		?>
		<h3 class="page-header"><?php echo $heading.$subHeading;?></h3>
		<?php
	}
		
	foreach($items as $item)
	{
		$slug 			= $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
		$catslug 		= $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
		$authorName 	= $this->escape($item->author);
		$profileUrl 	= $api->getUserProfileUrl($profileApp, $item->created_by);
		$avatarUrl		= $api->getUserAvatarImage($avatarApp, $item->created_by, $item->author_email, $avatarSize, true);
		$articleUrl 	= JRoute::_(ContentHelperRoute::getArticleRoute($slug, $catslug));
		$categoryLink 	= JHtml::link(JRoute::_(CjBlogHelperRoute::getCategoryRoute($item->catid, $item->language)), $this->escape($item->category_title));
		$authorLink 	= $params->get('list_link_author') ? JHtml::link($profileUrl, $authorName) : $authorName;
		$articleDate	= CjLibDateUtils::getHumanReadableDate($item->created);
		?>
		<div class="article-block">
			<div class="media">
				<?php if($params->get('list_show_avatar')):?>
		    		<?php if($avatarApp != 'none'):?>
					<div class="pull-left hidden-xs">
						<?php if($profileApp != 'none'):?>
						<a href="<?php echo $profileUrl;?>" title="<?php echo $authorName?>" class="thumbnail no-margin-bottom" data-toggle="tooltip">
							<img alt="<?php echo $authorName;?>" src="<?php echo $avatarUrl;?>" style="max-width: <?php echo $avatarSize;?>px;" class="media-object">
						</a>
						<?php else:?>
						<div class="thumbnail no-margin-bottom">
							<img alt="<?php echo $authorName;?>" src="<?php echo $avatarUrl;?>" style="max-width: <?php echo $avatarSize;?>px;" class="media-object">
						</div>
						<?php endif;?>
					</div>
					<?php endif;?>
	    		<?php endif;?>
				<h3 class="media-heading no-margin-top"><a href="<?php echo $articleUrl; ?>"><?php echo $this->escape($item->title); ?></a></h3>
				<div class="media-body">
					<?php 
					if($params->get('list_show_author') || $params->get('list_show_date'))
					{
						?>
						<span class="text-muted cj-article-info">
							<?php
							if($params->get('list_show_author') && $params->get('list_show_date'))
							{
								echo JText::sprintf('COM_CJBLOG_POSTED_BY_AUTHOR_ON', $authorLink, $articleDate);
							}
							else if($params->get('list_show_author'))
							{
								echo JText::sprintf('COM_CJBLOG_POSTED_BY_AUTHOR', $authorLink);
							}
							else if($params->get('list_show_date'))
							{
								echo JText::sprintf('COM_CJBLOG_POSTED_ON_DATE', $articleDate);
							}
							?>
						</span>
						<?php 
					}
			
					if($params->get('list_show_category'))
					{
						?><span class="text-muted cj-article-info"><?php echo JText::sprintf('COM_CJBLOG_POSTED_IN_CATEGORY', $categoryLink);?></span><?php
					}
					
					if($params->get('list_show_hits'))
					{
						?><span class="text-muted cj-article-info"><?php echo JText::sprintf('COM_CJBLOG_ARTICLE_HITS', $item->hits);?></span><?php
					}
					?>
				</div>
			</div>
			
			<div class="media">
				<?php if($params->get('list_show_thumbnails')):?>
				<div class="pull-left hidden-xs">
					<a href="<?php echo $articleUrl?>" class="thumbnail no-margin-bottom">
						<img src="<?php echo CjBlogSiteHelper::getArticleThumbnail($item, $thumbSize);?>" 
							style="width: auto; max-width: <?php echo $thumbSize?>px;" class="media-object"/>
					</a>
				</div>
				<?php endif;?>
				<div class="media-body">
					<div class="margin-bottom-10">
						<?php echo JHtml::_('string.truncate', strip_tags($item->introtext), $params->get('list_description_limit', 180));?>
					</div>
				</div>
			</div>
		</div>
		
		<hr />
		<?php
	}
	
	if (($params->def('show_pagination', 2) == 1  || ($params->get('show_pagination') == 2)) && ($pagination->pagesTotal > 1))
	{
		?>
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
			<div class="pagination">
				<?php if ($params->def('show_pagination_results', 1)) : ?>
					<p class="counter pull-right">
						<?php echo $pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>
		
				<?php echo $pagination->getPagesLinks(); ?>
			</div>
		</form>
		<?php
	}
}
else if($params->get('show_no_articles', 1) == 1)
{
	?>
	<div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo JText::_('COM_CJBLOG_NO_ARTICLES_FOUND')?></div>
	<?php 
}