<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$user    			= JFactory::getUser();
$layout 			= $this->params->get('ui_layout', 'default');
$params  			= $this->item->params;
?>

<div id="cj-wrapper" class="profile-details <?php echo $this->pageclass_sfx;?>">
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params, 'state'=>$this->state));?>
	
	<?php if($params->get('enable_gdpr') && $user->id == $this->item->id):?>
	<div class="alert alert-success"><i class="fa fa-info-circle"></i> <?php echo JText::_('COM_CJBLOG_GDPR_TITLE');?></div>
	<?php endif;?>
	
	<?php echo JLayoutHelper::render($layout.'.profile.profile_details', array('item'=>$this->item));?>
	
	<?php if(count(JModuleHelper::getModules('profile-view-above-summary'))):?>
	<div class="margin-top-10">
		<?php echo CJFunctions::load_module_position('profile-view-above-summary');?>
	</div>
	<?php endif;?>
	
	<div class="margin-top-10">
		<?php echo JLayoutHelper::render($layout.'.profile.apps', array('item'=>$this->item, 'apps'=>$this->apps));?>
	</div>
	
	<?php echo JLayoutHelper::render($layout.'.credits', array('params'=>$this->params));?>
	
	<div style="display: none">
		<input id="cjblog_pageid" value="profile" type="hidden">
	</div>
</div>