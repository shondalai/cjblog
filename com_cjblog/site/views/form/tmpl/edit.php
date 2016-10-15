<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');

JFactory::getLanguage()->load('com_content');

// Create shortcut to parameters.
$params = $this->state->get('params');
//$images = json_decode($this->item->images);
//$urls = json_decode($this->item->urls);

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);
if (!$editoroptions)
{
	$params->show_urls_images_frontend = '0';
}

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			" . $this->form->getField('articletext')->save() . "
			Joomla.submitform(task);
		}
	}
");

$theme = $params->get('theme', 'default');
$layout = $params->get('ui_layout', 'default');
?>
<div id="cj-wrapper" class="edit form-page<?php echo $this->pageclass_sfx; ?>">
	
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$params, 'state'=>$this->state));?>

	<?php if ($params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_content&a_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('COM_CONTENT_ARTICLE_CONTENT') ?></a></li>
				<?php if ($params->get('show_urls_images_frontend') ) : ?>
				<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS') ?></a></li>
				<?php endif; ?>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_CONTENT_PUBLISHING') ?></a></li>
				<li><a href="#language" data-toggle="tab"><?php echo JText::_('JFIELD_LANGUAGE_LABEL') ?></a></li>
				<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('COM_CONTENT_METADATA') ?></a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="editor">
					<?php echo $this->form->renderField('title'); ?>

					<?php if (is_null($this->item->id)) : ?>
						<?php echo $this->form->renderField('alias'); ?>
					<?php endif; ?>
					
					<?php echo $this->form->renderField('catid'); ?>
					<?php echo $this->form->renderField('tags'); ?>

					<?php echo $this->form->getInput('articletext'); ?>
				</div>
				<?php if ($params->get('show_urls_images_frontend')): ?>
				<div class="tab-pane" id="images">
					<div class="panel panel-<?php echo $theme;?> margin-top-10">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo $this->form->getLabel('image_intro', 'images');?></h3>
						</div>
						<div class="panel-body">
							<?php echo $this->form->renderField('image_intro', 'images'); ?>
							<?php echo $this->form->renderField('image_intro_alt', 'images'); ?>
							<?php echo $this->form->renderField('image_intro_caption', 'images'); ?>
							<?php echo $this->form->renderField('float_intro', 'images'); ?>
						</div>
					</div>
					<div class="panel panel-<?php echo $theme;?>">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo $this->form->getLabel('image_fulltext', 'images');?></h3>
						</div>
						<div class="panel-body">
							<?php echo $this->form->renderField('image_fulltext', 'images'); ?>
							<?php echo $this->form->renderField('image_fulltext_alt', 'images'); ?>
							<?php echo $this->form->renderField('image_fulltext_caption', 'images'); ?>
							<?php echo $this->form->renderField('float_fulltext', 'images'); ?>
						</div>
					</div>
					
					<div class="panel panel-<?php echo $theme;?>">
						<div class="panel-body">
							<?php echo $this->form->renderField('urla', 'urls'); ?>
							<?php echo $this->form->renderField('urlatext', 'urls'); ?>
							<div class="control-group">
								<div class="controls">
									<?php echo $this->form->getInput('targeta', 'urls'); ?>
								</div>
							</div>
							<?php echo $this->form->renderField('urlb', 'urls'); ?>
							<?php echo $this->form->renderField('urlbtext', 'urls'); ?>
							<div class="control-group">
								<div class="controls">
									<?php echo $this->form->getInput('targetb', 'urls'); ?>
								</div>
							</div>
							<?php echo $this->form->renderField('urlc', 'urls'); ?>
							<?php echo $this->form->renderField('urlctext', 'urls'); ?>
							<div class="control-group">
								<div class="controls">
									<?php echo $this->form->getInput('targetc', 'urls'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<div class="tab-pane" id="publishing">
					<?php if ($params->get('save_history', 0)) : ?>
						<?php echo $this->form->renderField('version_note'); ?>
					<?php endif; ?>
					<?php echo $this->form->renderField('created_by_alias'); ?>
					<?php if ($this->item->params->get('access-change')) : ?>
						<?php echo $this->form->renderField('state'); ?>
						<?php echo $this->form->renderField('featured'); ?>
						<?php echo $this->form->renderField('publish_up'); ?>
						<?php echo $this->form->renderField('publish_down'); ?>
					<?php endif; ?>
					<?php echo $this->form->renderField('access'); ?>
					<?php if (is_null($this->item->id)):?>
						<div class="control-group">
							<div class="control-label">
							</div>
							<div class="controls">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="tab-pane" id="language">
					<?php echo $this->form->renderField('language'); ?>
				</div>
				<div class="tab-pane" id="metadata">
					<?php echo $this->form->renderField('metadesc'); ?>
					<?php echo $this->form->renderField('metakey'); ?>

					<input type="hidden" name="task" value="" />
					<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
					<?php if ($this->params->get('enable_category', 0) == 1) :?>
					<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
					<?php endif; ?>
				</div>
			</div>
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
		
		<div class="panel panel-<?php echo $theme?>">
			<div class="panel-body">
				<div class="btn-toolbar text-center">
					<div class="btn-group">
						<button type="button" class="btn" onclick="Joomla.submitbutton('article.cancel')">
							<i class="fa fa-times-circle"></i> <?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
					<div class="btn-group">
						<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
							<i class="fa fa-check"></i> <?php echo JText::_('JSAVE') ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>