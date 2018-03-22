<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$user 			= JFactory::getUser();
$api 			= new CjLibApi();

$article		= $displayData['article'];
$profile		= $displayData['profile'];
$params			= $displayData['params'];
$theme 			= $params->get('theme', 'default');
$avatarApp		= $params->get('avatar_component', 'cjblog');
$profileApp		= $params->get('profile_component', 'cjblog');
$avatarSize		= $params->get('article_avatar_size', 64);

// Check if author has pro-capabilities
$proUser		= JFactory::getUser($article->created_by)->authorise('core.pro', 'com_cjblog');
$userProfileUrl	= ($proUser ? $api->getUserProfileUrl($profileApp, $article->created_by) : '#');
?>
<div class="article-info">
	<div class="media">
		<?php if($params->get('show_avatar') && $avatarApp != 'none'):?>
		<div class="pull-left">
			<?php if($profileApp != 'none'):?>
			<a href="<?php echo $userProfileUrl;?>" class="thumbnail no-margin-bottom">
				<img class="media-object" src="<?php echo $api->getUserAvatarImage($avatarApp, $article->created_by, '', 64);?>" 
					alt="<?php echo $this->escape($article->author);?>" style="min-width: <?php echo $avatarSize;?>px; max-width: <?php echo $avatarSize;?>px;">
			</a>
			<?php else:?>
			<img class="media-object" src="<?php echo $api->getUserAvatarImage($avatarApp, $article->created_by, '', $avatarSize);?>"
				 alt="<?php echo $this->escape($article->author);?>" style="min-width: <?php echo $avatarSize;?>px; max-width: <?php echo $avatarSize;?>px;">
			<?php endif;?>
		</div>
		<?php endif;?>
		
		<div class="media-body">
			<ul class="unstyled list-unstyled inline list-inline forum-info">
				<?php if($params->get('show_author_name')):?>
				<li><?php echo JText::sprintf('COM_CJBLOG_ARTICLE_WRITTEN_BY', 
						(($profileApp != 'none' && $proUser) ? JHtml::link($userProfileUrl, $article->author) : $article->author))?></li>
				<?php endif;?>
				
				<?php if($params->get('show_category')):?>
				<li><?php echo JText::sprintf('COM_CJBLOG_ARTICLE_CATEGORY', 
						JHtml::link(CjBlogHelperRoute::getCategoryRoute($article->catid), $article->category_title));?></li>
				<?php endif;?>
				
				<?php if($params->get('show_date')):?>
				<li><?php echo JText::sprintf('COM_CJBLOG_ARTICLE_DATE', CjLibDateUtils::getHumanReadableDate($article->created));?></li>
				<?php endif;?>
			</ul>
			
			<?php if($article->hits > intval($params->get('hot_article_hits', 250))):?>
			<span class="label label-important">
				<i class="fa fa-fire"></i> <?php echo JText::_('COM_CJBLOG_HOT')?>
			</span>
			<?php endif;?>
			
			<?php if($params->get('show_hits')):?>
			<span class="label label-info">
				<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $article->hits);?>
			</span>
			<?php endif;?>
	
        	<?php if($proUser && !empty($profile['about']) && ($params->get('show_article_info') == 2 || $params->get('show_article_info') == 3)):?>
        	<div class="text-muted author-about"><?php echo $profile['about'];?></div>
        	<?php endif;?>
		</div>
	</div>
</div>