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
$active_id = 5;

JPluginHelper::importPlugin( 'cjblog' );
$dispatcher 	= JDispatcher::getInstance();
$editor 		= $this->params->get('default_editor', 'wysiwygbb');
$bbcode 		= $editor == 'wysiwygbb' ? true : false;
$dateFormat 	= JText::_($this->params->get('profile_date_format', 'DATE_FORMAT_LC1'));
$api 			= new CjLibApi();
$avatarApp 		= $this->params->get('avatar_component', 'cjblog');
$profileApp		= $this->params->get('profile_component', 'cjblog');
$layout			= $this->params->get('layout', 'default');
$aboutTextApp 	= $this->params->get('about_text_app', 'cjblog');
?>

<div id="cj-wrapper">

	<?php include_once JPATH_COMPONENT.'/helpers/header.php';?>
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params));?>
	
	<div class="profile-container">
		
		<div class="container-fluid">	
			<div class="row-fluid">
				<div class="span3 vcard">
					<ul class="thumbnails nospace-left nospace-bottom nospace-top">
						<li class="span12 thumbnail avatar-wrapper nomargin-left">
							<?php if($avatarApp != 'none'):?>
							<a onclick="return false;" href="#">
								<img class="img-avatar photo" src="<?php echo $api->getUserAvatarImage($avatarApp, $this->profile['id'], $this->profile['email'], 96);?>" 
									alt="<?php echo $this->profile['name']?>" style="width: 100%">
							</a>
							<?php endif;?>
							
							<?php if(!$user->guest && $avatarApp == 'cjblog' && ($user->id == $this->profile['id'] || $user->authorise('core.manage'))):?>
							<div class="center"><a href="#" onclick="return false" class="btn-edit-avatar"><?php echo JText::_('LBL_CHANGE_AVATAR');?></a></div>
							<?php endif;?>
							
							<div class="lead nospace-bottom center fn title"><span class="full-name"><?php echo $this->escape($this->profile['name'])?></span></div>
							<p class="center" style="white-space: nowrap;"><?php echo CjBlogApi::get_user_badges_markup($this->profile);?></p>
						</li>
					</ul>
					
					<ul class="nav nav-tabs nav-stacked nospace-top">
		    			<li>
							<?php echo JHtml::link(
									JRoute::_('index.php?option='.CJBLOG.'&view=blog&id='.$this->profile['id'].':'.$this->profile['username'].$blog_itemid), 
									JText::_('LBL_VISIT_MY_BLOG'),
									array('class'=>''));?>
						</li>
						<li>
							<?php echo JHtml::link(
									JRoute::_('index.php?option='.CJBLOG.'&view=user&task=articles&id='.$this->profile['id'].':'.$this->profile['username'].$user_itemid), 
									JText::_('LBL_MY_ARTICLES'),
									array('class'=>''));?>
						</li>
						<li>
							<?php echo JHtml::link(
									JRoute::_('index.php?option='.CJBLOG.'&view=badges&task=user&id='.$this->profile['id'].':'.$this->profile['username'].$badges_itemid), 
									JText::_('LBL_MY_BADGES'),
									array('class'=>''));?>
						</li>
					</ul>
					
					<?php echo CJFunctions::load_module_position('cjblog-profile-below-avatar');?>
				</div>
				<div class="span9">
				
					<?php
					// support for external plugins on group CjBlog for event onBeforeDisplayProfile
					$plugin_output = $dispatcher->trigger('onBeforeCjBlogProfileDisplay', array($this->profile));
		
					if(!empty($plugin_output) && is_array($plugin_output)){
						
						foreach($plugin_output as $output){
							
							if(!empty($output) && !empty($output['header']) && !empty($output['content'])){
							
								echo '<h2 class="page-header">'.$this->escape($output['header']).'</h2>';
								echo CJFunctions::clean_value($output['content']);
							}
						}
					}
					?>
					
					<h2 class="page-header nopad-top margin-bottom-10">
						<?php echo JText::_('LBL_ABOUT_ME');?>
						<?php if($aboutTextApp != 'easyprofile' && !$user->guest && ($user->id == $this->profile['id'] || $user->authorise('core.manage'))):?>
						<small><a href="#" onclick="return false;" class="btn-edit-about tooltip-hover" title="<?php echo JText::_('JGLOBAL_EDIT');?>"><i class="icon-edit"></i></a></small>
						<?php endif;?>
					</h2>
					
					<div class="user-about">
						<?php echo CJFunctions::preprocessHtml($this->profile['about'], false, $bbcode);?>
					</div>
					
					<?php if($aboutTextApp != 'easyprofile' && !$user->guest && ($user->id == $this->profile['id'] || $user->authorise('core.manage'))):?>
					<form id="edit-about-form" action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=profile&task=save_about&id='.$this->profile['id'].$profile_itemid);?>" style="display: none;">
						<?php echo CJFunctions::load_editor($editor, 'user-about', 'user-about', $this->profile['about'], '10', '40', '100%', '250px', 'form-control', 'width: 100%;', true); ?>
						<div class="margin-top-10">
							<button class="btn btn-small btn-edit-about" type="button"><i class="icon-remove"></i> <?php echo JText::_('LBL_CANCEL');?></button>
							<button class="btn btn-small btn-success btn-save-about" type="button" data-loading-text="<?php echo JText::_('LBL_WAIT');?>">
								<i class="icon-ok icon-white"></i> <?php echo JText::_('LBL_SAVE');?>
							</button>
						</div>
					</form>
					<?php endif;?>
					
					<div class="muted"><?php echo JText::sprintf('TXT_REGISTERED_DATE', CJFunctions::get_localized_date($this->profile['registerDate'], $dateFormat));?></div>
					<div class="muted"><?php echo JText::sprintf('TXT_LAST_VISITED_DATE', CJFunctions::get_localized_date($this->profile['lastvisitDate'], $dateFormat));?></div>
					<div class="muted"><?php echo JText::sprintf('TXT_PROFILE_VIEWS', $this->profile['profile_views']);?></div>
					
					<?php
					// support for external plugins on group CjBlog for event onAfterDisplayProfile
					$plugin_output = $dispatcher->trigger('onAfterCjBlogProfileDisplay', array($this->profile));
	
					if(!empty($plugin_output) && is_array($plugin_output)){
						
						foreach($plugin_output as $output){
							
							if(!empty($output) && !empty($output['header']) && !empty($output['content'])){
							
								echo '<h2 class="page-header margin-bottom-10">'.$this->escape($output['header']).'</h2>';
								echo CJFunctions::clean_value($output['content']);
							}
						}
					}
					?>
				</div>
			</div>
		</div>
		
		<div class="modal" id="modal-error-message" tabindex="-1" role="dialog" aria-labelledby="model-label" aria-hidden="true" style="display: none;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h3 id="model-label"><?php echo JText::_('LBL_ERROR');?></h3>
			</div>
			<div class="modal-body">
				<p>Sample body</p>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('LBL_CLOSE');?></button>
			</div>
		</div>
		
		<div id="modal-change-avatar" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="model-label" aria-hidden="true" style="display: none;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="model-label"><?php echo JText::_('LBL_CHANGE_AVATAR');?></h3>
			</div>
			<div class="modal-body">
				<div class="avatar-upload-error" style="display: none;">
					<div class="alert alert-error"></div>
				</div>
				<div class="change-avatar-step1">
					<h2 class="nospace-top"><?php echo JText::_('LBL_CHANGE_AVATAR_STEP_1');?></h2>
					<p><?php echo JText::_('LBL_AVATAR_IMAGE_SIZE_INFO');?></p>
					<button class="btn btn-inverse btn-select-image" data-loading-text="<?php echo JText::_('LBL_WAIT');?>"><?php echo JText::_('LBL_SELECT_IMAGE');?></button>
					<form
						id="avatar-upload-form" 
						action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=profile&task=upload_avatar&id='.$this->profile['id'].$profile_itemid);?>" 
						method="post" 
						enctype="multipart/form-data" 
						style="display: none;">
						<input type="file" name="input-avatar-image" id="input-avatar-image">
					</form>
				</div>
				<div class="change-avatar-step2" style="display: none;">
					<form
						id="save-avatar-form" 
						action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=profile&task=save_avatar&id='.$this->profile['id'].$profile_itemid);?>" 
						method="post" 
						enctype="multipart/form-data">
					
						<h2 class="nospace-top"><?php echo JText::_('LBL_CHANGE_AVATAR_STEP_2');?></h2>
						<p><?php echo JText::_('LBL_SELECT_AVATAR_AREA_HELP')?></p>
						<div class="new-avatar-image"></div>
						<input type="hidden" name="coords" value="">
						<input type="hidden" name="file_name" value="">
					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('LBL_CLOSE');?></button>
				<button class="btn btn-inverse btn-save-avatar" data-loading-text="<?php echo JText::_('LBL_WAIT');?>" style="display: none;"><?php echo JText::_('LBL_SAVE_AVATAR');?></button>
			</div>
		</div>
	</div>
</div>