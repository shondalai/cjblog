<?php
/**
 * @version		$Id: default.php 01 2011-08-13 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components
 * @copyright	Copyright (C) 2009 - 2011 corejoomla.com. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die();

$active_id = 9;
$itemid = CJFunctions::get_active_menu_id();
$layout	= $this->params->get('layout', 'default');
?>
<div id="cj-wrapper">
	
	<?php include_once JPATH_COMPONENT.'/helpers/header.php';?>
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params));?>
	
	<div class="container-fluid tags-list">
		<div class="row-fluid">
			<div class="span12">
				<div class="well center">
				    <form class="nomargin-bottom" action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=tags'.$itemid)?>">
				    	<div class="input-append">
				    		<input name="search" type="text" value="<?php echo $this->state['search'];?>">
				    		<button type="submit" class="btn"><?php echo JText::_('LBL_SEARCH');?></button>
				    	</div>
				    </form>
				</div>
			</div>
		</div>
		
		<div class="row-fluid tag-row">
			<div class="span12">
				<?php 
				if(!empty($this->items)){
					
					foreach($this->items as $i=>$item){
	
						if($i > 0 && $i % 4 == 0) echo '</div></div><div class="row-fluid tag-row"><div class="span12">';
						
						echo '<div class="span3 tag-item-wrapper">';
							
							echo JHtml::link(
										JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=tag&id='.$item->id.':'.$item->alias.$articles_itemid), 
										$this->escape($item->tag_text),
										array('class'=>'label tag-item tag-list-item'));
							echo '<span class="muted"><strong>&nbsp;&times;&nbsp;'.$item->num_items.'</strong></span>';
							
							if($user->authorise('core.manage')){
		
								echo ' <a href="#" class="btn-edit-tag" onclick="return false;"><i class="icon-edit"></i></a>';
								echo ' <a href="#" class="btn-delete-tag" onclick="return false;"><i class="icon-remove"></i></a>';
							}
							
							echo '<div class="muted small-text tag-desc">'.CJFunctions::substrws($this->escape($item->description), 75).'</div>';
							echo '<input type="hidden" name="tagid" value="'.$item->id.'">';
						
						echo '</div>';
					}
				} else {
					
					echo '<div class="well">'.JText::_('MSG_NO_RESULTS').'</div>';
				}
				?>
			</div>
		</div>
		
		<div class="row-fluid margin-top-20">
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
		
		<div style="display: none">
			<input type="hidden" value="tags" id="cjblog_page_id">
			<div id="url-get-tag-details"><?php echo JRoute::_('index.php?option='.CJBLOG.'&view=tags&task=get_tag'.$itemid);?></div>
			<div id="url-delete-tag"><?php echo JRoute::_('index.php?option='.CJBLOG.'&view=tags&task=delete_tag'.$itemid);?></div>
		</div>
		
		<div id="edit-tag-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="editModalLabel"><?php echo JText::_('LBL_EDIT');?></h3>
			</div>
			<div class="modal-body">
				<form class="tag-form" action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=tags&task=save_tag'.$itemid);?>" method="post">
					<div class="alert" style="display: none;">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<div class="error-desc"></div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName"><?php echo JText::_('LBL_NAME');?></label>
						<div class="controls"><input type="text" name="name" id="inputName" placeholder="<?php echo JText::_('LBL_NAME');?>"></div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputAlias"><?php echo JText::_('LBL_ALIAS');?></label>
						<div class="controls"><input type="text" name="alias" id="inputAlias" placeholder="<?php echo JText::_('LBL_ALIAS');?>"></div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputDescription"><?php echo JText::_('LBL_DESCRIPTION');?></label>
						<div class="controls">
							<textarea name="description" id="inputDescription" placeholder="<?php echo JText::_('LBL_DESCRIPTION');?>" class="input-xlarge" cols="40" rows="5"></textarea>
						</div>
					</div>
					<input name="tagid" value="" type="hidden">
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn btn-cancel" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('LBL_CANCEL');?></button>
				<button class="btn btn-primary btn-submit" aria-hidden="true"><?php echo JText::_('JSUBMIT');?></button>
			</div>
		</div>	
		
		<div id="message-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel"><?php echo JText::_('LBL_ALERT');?></h3>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('LBL_CLOSE');?></button>
			</div>
		</div>
	
		<div id="confirm-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="confirmModalLabel"><?php echo JText::_('LBL_ALERT');?></h3>
			</div>
			<div class="modal-body"><?php echo JText::_('MSG_CONFIRM')?></div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('LBL_CLOSE');?></button>
				<button class="btn btn-primary btn-confirm" aria-hidden="true"><?php echo JText::_('LBL_CONFIRM');?></button>
			</div>
		</div>
	</div>
</div>