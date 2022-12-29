<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$params		= $this->params;
$layout 	= $params->get('ui_layout', 'default');
$theme 		= $params->get('theme', 'default');
$category	= $this->category;
$params->set('show_parent', false);
?>
<div id="cj-wrapper" class="articles-list<?php echo $this->pageclass_sfx;?>">
	<?php 
	echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$params, 'state'=>$this->state));
	echo JLayoutHelper::render( $layout . '.category_search_box', [ 'catid' => $category->id, 'params' => $params, 'state' => $this->state ], '', [ 'debug' => false ] );
	echo JLayoutHelper::render($layout.'.articles_list', array('data'=>$this), '', array('debug'=>false));
	echo JLayoutHelper::render($layout.'.credits', array('params'=>$params));?>
</div>