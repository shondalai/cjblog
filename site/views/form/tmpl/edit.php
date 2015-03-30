<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');

$user = JFactory::getUser();
$params = $this->state->get('params');
$layout = $this->params->get('layout', 'default');
$editoroptions = isset($params->show_publishing_options);

if (! $editoroptions)
{
	$params->show_urls_images_frontend = '0';
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task);
		}
	}
</script>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>
	
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params));?>
	
	<form
		action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>"
		method="post" name="adminForm" id="adminForm"
		class="form-validate form-vertical">
		<h2><?php echo JText::_('COM_CJBLOG_FILL_BASIC_DETAILS');?>:</h2>
		<table class="table table-hover">
			<tr>
				<th class="span3"><?php echo $this->form->getLabel('title'); ?></th>
				<td class="inputbox"><?php echo $this->form->getInput('title'); ?></td>
			</tr>
      		
      		<?php if($user->authorise('core.edit.state')):?>
      		<tr>
				<th class="span3"><?php echo $this->form->getLabel('alias'); ?></th>
				<td class="inputbox"><?php echo $this->form->getInput('alias'); ?></td>
			</tr>
      		<?php endif;?>
      		
      		<tr>
				<th><?php echo $this->form->getLabel('catid'); ?></th>
				<td><?php echo $this->form->getInput('catid'); ?></td>
			</tr>
     
     		 <?php if($user->authorise('core.edit.state')):?>
      		<tr>
				<th><?php echo $this->form->getLabel('state'); ?></th>
				<td><?php echo $this->form->getInput('state'); ?></td>
			</tr>
      		<?php endif;?>
      		
      		<tr>
      			<th><?php echo $this->form->getLabel('tags'); ?></th>
				<td><?php echo $this->form->getInput('tags'); ?></td>
			</tr>
		</table>
		<h2><?php echo JText::_('COM_CJBLOG_WRITE_ARTICLE_HERE');?>:</h2>
		
		<?php echo $this->form->getInput('articletext'); ?>
		
		<h2><?php echo JText::_('COM_CJBLOG_WRITE_META_INFORMATION');?>:</h2>
		<table class="table table-hover">
			<tr>
				<th class="span3"><?php echo $this->form->getLabel('metakey'); ?></th>
				<td class="inputbox"><?php echo $this->form->getInput('metakey'); ?></td>
			</tr>
			<tr>
				<th><?php echo $this->form->getLabel('metadesc'); ?></th>
				<td class="inputbox"><?php echo $this->form->getInput('metadesc'); ?></td>
			</tr>
		</table>

		<div class="form-actions formelm-buttons">
			<button class="btn btn-default" type="button" onclick="Joomla.submitbutton('article.cancel')"><?php echo JText::_('JCANCEL') ?></button>
			<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('article.save')"><?php echo JText::_('JSUBMIT') ?></button>
		</div>

		<input type="hidden" name="a_id" value="<?php echo !empty($this->item->id) ? $this->item->id : 0;?>">
		<input type="hidden" name="id" value="<?php echo !empty($this->item->id) ? $this->item->id : 0;?>">
		<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
		<input type="hidden" name="task" value="article.save" />

		<div style="display: none;">
			<?php echo $this->form->getInput('language'); ?>
			<?php echo $this->form->getInput('publish_up'); ?>
			<?php echo $this->form->getInput('publish_down'); ?>
		</div>
		
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>
</div>
