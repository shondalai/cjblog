<?php
/**
 * @version		$Id: default_form.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// Load the tooltip behavior.
$user = JFactory::getUser();
?>

<form action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgerules');?>" method="post" name="adminForm" id="adminForm">
	<ul class="unstyled">
		<?php foreach($this->rules as $rule):?>
		<li>
			<?php echo $this->escape($rule->title);?>
			<ul>
				<?php foreach ($rule->rule_types as $type):?>
				<li><a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgerules&task=edit&asset='.$rule->asset_name.'&name='.$type->name);?>"><?php echo $this->escape($type->description);?></a></li>
				<?php endforeach;?>
			</ul>
		</li>
		<?php endforeach;?>
	</ul>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="badgerules" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>