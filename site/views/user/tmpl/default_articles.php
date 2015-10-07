<?php
/**
 * @version		$Id: default.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$itemid = CJFunctions::get_active_menu_id();
$active_id = 6;
$layout	= $this->params->get('layout', 'default');
?>

<div id="cj-wrapper">
	
	<?php include_once JPATH_COMPONENT.'/helpers/header.php';?>
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params));?>
	
	<div class="container-fluid">
		<h1 class="nopad-top padbottom-5 page-header"><?php echo !empty($this->page_heading) ? $this->page_heading : $this->params->get('page_heading');?></h1>
		
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<td><?php echo JText::_('JGRID_HEADING_ID');?></td>
					<td><?php echo JText::_('LBL_TITLE');?></td>
					<td><?php echo JText::_('LBL_CATEGORY');?></td>
					<td><?php echo JText::_('LBL_DATE');?></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->articles as $i=>$article):?>
				<tr>
					<td><?php echo $article->id;?></td>
					<td>
						<?php 
						echo JHtml::link(
							ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias), 
							$this->escape($article->title));
						?>
					</td>
					<td>
						<?php 
						echo JHtml::link(
							JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=latest&id='.$article->catid.':'.$article->category_alias.$articles_itemid)
							,$this->escape($article->category_title));
						?>
					</td>
					<td nowrap="nowrap"><?php echo CJFunctions::get_localized_date($article->created);?></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		
		<div class="row-fluid">
			<div class="span12">
				<?php 
				echo CJFunctions::get_pagination(
						$this->page_url, 
						$this->pagination->get('pages.start'), 
						$this->pagination->get('pages.current'), 
						$this->pagination->get('pages.total'),
						JFactory::getApplication()->getCfg('list_limit', 20),
						true
					);
				?>
			</div>
		</div>
	</div>
</div>