<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$data 		= $displayData['data'];
$layout 	= $data->params->get('ui_layout', 'default');
?>
<div id="cj-wrapper" class="profile-favorites<?php echo $data->pageclass_sfx;?>">
	<?php echo JLayoutHelper::render($layout.'.articles_list', array('topics'=>$data->items, 'params'=>$data->params, 'pagination'=>$data->pagination, 'heading'=>'', 'viewName'=>''));?>
</div>