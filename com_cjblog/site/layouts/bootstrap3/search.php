<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2021 BulaSikku Technologies Private Limited.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$params 			= $displayData['params'];
$theme 				= $params->get('theme', 'default');
?>
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo JText::_('COM_CJBLOG_ENTER_SEARCH_CRITERIA');?></div>
			</div>
		</div>
		
		<form action="<?php echo JRoute::_('index.php');?>" method="post">
			<div class="row mb-3">
				<div class="col-md-12">
					<div class="panel panel-<?php echo $theme;?>">
						<div class="panel-heading">
							<div class="panel-title"><?php echo JText::_('COM_CJBLOG_LABEL_KEYWORDS');?></div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-8">
						 			<input name="list_filter" type="text" class="form-control" placeholder="<?php echo JText::_('COM_CJBLOG_LABEL_KEYWORDS');?>">
						 		</div>
						 		<div class="col-md-4">
        						 	<select name="list_filter_field" class="form-select">
        						 		<option value="title"><?php echo JText::_('COM_CJBLOG_SEARCH_TITLES');?></option>
        						 		<option value="author"><?php echo JText::_('COM_CJBLOG_SEARCH_USER_NAME');?></option>
        						 		<option value="createdby"><?php echo JText::_('COM_CJBLOG_SEARCH_USERID');?></option>
        						 		<option value="content"><?php echo JText::_('COM_CJBLOG_SEARCH_CONTENT');?></option>
        						 	</select>
        						 </div>
							</div>
						 	<label class="form-check mt-2">
                                <input type="checkbox" value="1" name="filter_all_keywords" class="form-check-input">
                                <?php echo JText::_('COM_CJBLOG_SEARCH_ALL_WORDS');?>
                            </label>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row mb-3">
				<div class="col-md-6">
					<div class="panel panel-<?php echo $theme;?>">
						<div class="panel-heading">
							<div class="panel-title"><?php echo JText::_('COM_CJBLOG_SEARCH_OPTIONS');?></div>
						</div>
						<div class="panel-body">
							<div class="form-group">
    							<label><?php echo JText::_('COM_CJBLOG_SEARCH_ORDER_BY');?></label>
    							<select name="filter_order" class="form-select" size="1">
    								<option value="a.created"><?php echo JText::_('COM_CJBLOG_LABEL_DATE');?></option>
    								<option value="a.catid"><?php echo JText::_('COM_CJBLOG_LABEL_CATEGORY');?></option>
    							</select>
							</div>
							<div class="form-group">
    							<label><?php echo JText::_('COM_CJBLOG_SEARCH_ORDER');?></label>
    							<select name="filter_order_Dir" class="form-select" size="1">
    								<option value="asc"><?php echo JText::_('COM_CJBLOG_ASCENDING');?></option>
    								<option value="desc"><?php echo JText::_('COM_CJBLOG_DESCENDING');?></option>
    							</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-<?php echo $theme;?>">
						<div class="panel-heading">
							<div class="panel-title"><?php echo JText::_('COM_CJBLOG_LABEL_CATEGORIES');?></div>
						</div>
						<div class="panel-body">
							<?php 
							$nocat = new JObject();
							$nocat->set('text', JText::_('COM_CJBLOG_LABEL_ALL_CATEGORIES'));
							$nocat->set('value', '0');
							$nocat->set('disable', false);
							
							$categories = JHTML::_('category.options', 'com_content');
							array_unshift($categories, $nocat);
							echo JHTML::_('select.genericlist', $categories, 'catid[]', 'size = "6" multiple="multiple", class="form-select"');
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-<?php echo $theme;?>">
				<div class="panel-body text-center">
    				<a href="<?php echo JRoute::_('index.php?option=com_cjblog')?>" class="btn btn-outline-secondary"><?php echo JText::_('JCANCEL');?></a>
    				<button class="btn btn-primary" type="submit"><i class="fa fa-search-plus"></i> <?php echo JText::_('COM_CJBLOG_LABEL_SEARCH');?></button>
    			</div>
			</div>
			
			<input type="hidden" name="view" value="articles">
			<input type="hidden" id="filter_featured" name="filter_featured" value="">
		</form>
	</div>
</div>