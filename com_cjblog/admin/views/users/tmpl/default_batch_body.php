<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$published = $this->state->get('filter.published');

$user = JFactory::getUser();
$rowClass = CJBLOG_MAJOR_VERSION == 3 ? 'control-group span6' : 'form-group col-md-6';
?>
<div class="container">
	<div class="row">
		<div class="<?php echo $rowClass;?>">
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.language', []); ?>
			</div>
		</div>
		<div class="<?php echo $rowClass;?>">
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.access', []); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<?php if ($published >= 0) : ?>
		<div class="<?php echo $rowClass;?>">
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.item', ['extension' => 'com_cjblog']); ?>
			</div>
		</div>
		<?php endif; ?>
		<div class="<?php echo $rowClass;?>">
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.tag', []); ?>
			</div>
		</div>
		<?php if ($user->authorise('core.admin', 'com_cjblog')) : ?>
        <div class="<?php echo $rowClass;?>">
            <div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.workflowstage', ['extension' => 'com_cjblog']); ?>
            </div>
        </div>
		<?php endif; ?>
	</div>
</div>
