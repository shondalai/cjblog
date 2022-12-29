<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$app 				= JFactory::getApplication();
$user 				= JFactory::getUser();
$params 			= $displayData['params'];

if($params->get('show_credits_block', 1) == 1)
{
	?>
	<div class="text-center margin-top-20">
		<small class="text-muted">
			<?php echo JText::_('COM_CJBLOG_CREDITS_TEXT');?> <a href="https://github.com/shondalai/cjblog" target="_blank">CjBlog</a>
		</small>
	</div>
	<?php
}