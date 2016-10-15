<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$avatarApp		= $this->params->get('avatar_component', 'cjblog');
$profileApp		= $this->params->get('profile_component', 'cjblog');
$layout 		= $this->params->get('ui_layout', 'default');
$theme 			= $this->params->get('theme', 'default');
$this->params->set('show_parent', true);
?>

<div id="cj-wrapper" class="category-list<?php echo $this->pageclass_sfx;?>">
	<?php 
	echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params, 'state'=>$this->state));
	
	if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0)
	{ 
		$num = 1;
		foreach($this->items[$this->parent->id] as $id => $item)
		{
			echo JLayoutHelper::render($layout.'.category_list', array('category'=>$item, 'params'=>$this->params, 'maxlevel'=>$this->maxLevelcat, 'section_num'=>$num));
			$num++;
		}
	}
	
	echo JLayoutHelper::render($layout.'.credits', array('params'=>$this->params));
	?>
</div>