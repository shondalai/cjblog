<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

if(CJBLOG_MAJOR_VERSION < 4) {
    JHtml::_('formbehavior.chosen', 'select');
}

// Create shortcut to parameters.
$params = $this->state->get('params');

$app = JFactory::getApplication();
$input = $app->input;

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$params = json_decode($params);
?>
<form action="<?php echo JRoute::_('index.php?option=com_cjblog&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_CJBLOG_EMAIL_CONTENT', true)); ?>
		<div class="<?php echo CJBLOG_MAJOR_VERSION < 4 ? 'row-fluid' : 'row';?>">
			<div class="span9 col-md-9">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('description'); ?>
				</fieldset>
			</div>
			<div class="span3 col-md-3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				<fieldset class="form-vertical">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('email_type'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('email_type'); ?>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
		<?php echo JHtml::_('form.token'); ?>


		</div>
</form>
