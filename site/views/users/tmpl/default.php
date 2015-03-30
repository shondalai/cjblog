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
$itemid 		= CJFunctions::get_active_menu_id();
$page_heading 	= $this->params->get('page_heading');
$active_id 		= 2;
$api 			= new CjLibApi();
$avatarApp 		= $this->params->get('avatar_component', 'cjblog');
$profileApp 	= $this->params->get('profile_component', 'cjblog');
$layout 		= $this->params->get('layout', 'default');
$editor 		= $this->params->get('default_editor', 'wysiwygbb');
$bbcode 		= $editor == 'wysiwygbb' ? true : false;
?>

<div id="cj-wrapper">
	<?php include_once JPATH_COMPONENT.'/helpers/header.php';?>
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params));?>
	
	<?php if(!empty($page_heading)):?>
    <h1 class="nopad-top padbottom-5 page-header"><?php echo $this->escape($page_heading);?></h1>
    <?php endif;?>
	
    <?php if(!empty($this->users)):?>
	<div class="panel panel-default">
		<div class="panel-heading">
		    <form action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users&task=search')?>" class="nospace-bottom">
		    	<div class="input-append">
		    		<input name="search" type="text" value="<?php echo $this->state['search'];?>" placeholder="<?php echo JText::_('LBL_SEARCH');?>">
		    		<button type="submit" class="btn"><?php echo JText::_('LBL_SEARCH');?></button>
		    	</div>
		    </form>
		</div>
	
		<ul class="list-group nomargin-left topics-list nopad-left">
    		<?php foreach ($this->users as $i=>$user): ?>
    		<li class="list-group-item">
				<div class="media">
					<?php if($avatarApp != 'none'):?>
					<div class="media-left">
						<a href="<?php echo $api->getUserProfileUrl($profileApp, $user['id'], true);?>" class="thumbnail nomargin-bottom">
							<img class="img-avatar" src="<?php echo $api->getUserAvatarImage($avatarApp, $user['id'], $user['email'], 48, true);?>" style="max-width: 45px;">
						</a>
					</div>
					<?php endif;?>
				
					<div class="media-left hidden-xs hidden-phone">
						<div class="panel panel-default item-count-box">
							<div class="panel-body center item-count-num"><?php echo $user['num_articles'];?></div>
							<div class="panel-footer text-nowrap text-muted item-count-caption"><?php echo JText::plural('COM_CJFORUM_ARTICLES_LABEL', $user['num_articles']);?></div>
						</div>
					</div>
					<div class="media-body">
						<h4 class="media-heading"><?php echo $api->getUserProfileUrl($profileApp, $user['id'], false, $this->escape($user['name']));?></h4>
						<div class="text-muted">
							<span class="margin-right-10"><?php echo JText::_('LBL_POINTS').': '.$user['points'];?></span>
							<span class="margin-right-10"><?php echo JText::_('LBL_BADGES').': '.$user['num_badges'];?></span>
						</div>
						<div class="muted padbottom-5">
							<?php echo strip_tags(JHtml::_('string.truncate', CJFunctions::parse_html($user['about'], false, $bbcode), 250));?>
						</div>
					</div>
				</div>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
		
		<div class="row-fluid">
			<div class="span12">
				<?php 
				echo CJFunctions::get_pagination(
						$this->page_url, 
						$this->pagination->get('pages.start'), 
						$this->pagination->get('pages.current'), 
						$this->pagination->get('pages.total'),
						JFactory::getApplication()->getCfg('list_limit', 20),
						true
					);
				?>
			</div>
		</div>
	<?php else:?>
	<p><?php echo JText::_('LBL_NO_RESULTS_FOUND');?></p>
	<?php endif;?>
</div>