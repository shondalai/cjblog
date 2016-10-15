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
$avatarSize		= $params->get('article_avatar_size', 96);
?>
<hr/>
<div class="media">
	<?php if($avatarApp != 'none'):?>
	<div class="pull-left">
		<?php if($profileApp != 'none'):?>
		<a href="<?php echo $api->getUserProfileUrl($profileApp, $article->created_by);?>" class="thumbnail">
			<img class="media-object" src="<?php echo $api->getUserAvatarImage($avatarApp, $article->created_by, '', 64);?>" 
				alt="<?php echo $this->escape($article->author);?>" style="min-width: <?php echo $avatarSize;?>px;">
		</a>
		<?php else:?>
		<img class="media-object" src="<?php echo $api->getUserAvatarImage($avatarApp, $article->created_by, '', $avatarSize);?>"
			 alt="<?php echo $this->escape($article->author);?>" style="min-width: <?php echo $avatarSize;?>px;">
		<?php endif;?>
	</div>
	<?php endif;?>
	<div class="media-body">
		<small class="text-muted"><?php echo JText::_('COM_CJBLOG_ARTICLE_WRITTEN_BY');?></small>
		<div class="author-name"><?php echo $article->author;?></div>
		<div class="text-muted author-about"><?php echo $profile['about'];?></div>
	</div>
</div>