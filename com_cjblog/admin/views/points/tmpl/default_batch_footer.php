<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();
?>
<a class="btn btn-secondary" type="button" data-dismiss="modal">
	<?php echo JText::_('JCANCEL'); ?>
</a>
<button id="batch-submit-button-id" class="btn btn-success" type="submit" data-submit-task="points.batch" onclick="this.form.task.value='points.batch';return true;">
	<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>
