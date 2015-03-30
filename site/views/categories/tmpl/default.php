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
defined ( '_JEXEC' ) or die;

$itemid 		= CJFunctions::get_active_menu_id();
$active_id 		= 1;
$page_heading 	= $this->params->get('page_heading');
$layout			= $this->params->get('layout', 'default');?>

<div id="cj-wrapper">

	<?php include_once JPATH_COMPONENT.'/helpers/header.php';?>
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params));?>
	
	<div class="container-fluid category-table">
	  <div class="row-fluid">
	    <div class="span12">
	    
	    	<?php if(!empty($page_heading)):?>
	    	<h1 class="nopad-top padbottom-5 page-header"><?php echo $this->escape($page_heading);?></h1>
	    	<?php endif;?>
	    	
	    	<?php echo CjBlogHelper::get_category_table($this->categories, $this->params, array('base_url'=>'index.php?option='.CJBLOG.'&view=categories&task=latest', 'itemid' => $articles_itemid,));?>
	    </div>
	  </div>
	</div>
</div>