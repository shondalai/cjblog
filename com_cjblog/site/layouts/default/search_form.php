<?php
/**
 * @package     CjBlog
 * @subpackage  com_communitysurveys
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$app 				= JFactory::getApplication();
$user 				= JFactory::getUser();

$params 			= $displayData['params'];
$state				= $displayData['state'];
$catid				= (int) $displayData['catid'];
$theme 				= $params->get('theme', 'default');

$api = new CjLibApi();
?>
<div class="row-fluid search-form">
    <div class="span3"></div>
	<div class="span6">
		<form action="<?php echo JRoute::_('index.php');?>" method="get" class="no-margin-bottom margin-top-10 text-center"> 
			<div class="input-append ">
				<input type="text" name="list_filter" id="articles_search_box" value="<?php echo $state->get('list.filter', '');?>" 
					class="form-control" placeholder="<?php echo JText::_('COM_CJBLOG_SEARCH_PLACEHOLDER');?>">
				<span class="input-group-btn">
				    <button class="btn btn-default" type="submit"><?php echo JText::_('COM_CJBLOG_LABEL_SEARCH');?></button>
                </span>
			</div>
			<div class="text-center">
				<a href="<?php echo JRoute::_('index.php?option=com_cjblog&view=search');?>">
					<?php echo JText::_('COM_CJBLOG_TRY_ADVANCED_SEARCH')?>
				</a>
			</div>
			<input type="hidden" name="view" value="articles">
			<input type="hidden" name="catid" value="<?php echo $catid;?>">
			<input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_('index.php'));?>">
		</form>
	</div>
	<div class="span3"></div>
</div>