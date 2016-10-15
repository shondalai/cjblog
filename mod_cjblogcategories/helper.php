<?php
/**
 * @package     corejoomla.site
 * @subpackage  mod_cjblogcategories
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_ROOT.'/components/com_content/helpers');

class CjBlogCategoriesHelper 
{
	public static function get_tree_list($nodes, $excluded)
	{
		$content = '<ul class="cat-list">';
		$language = JFactory::getLanguage()->getTag();
		
		foreach($nodes as $node)
		{
			if(in_array($node->id, $excluded)) 
			{
				continue;
			}
			
			if($language != '*' && $node->language != '*' && $language != $node->language)
			{
				continue;
			}
			
			$value = CjLibUtils::escape($node->title);
			if(!empty($node->numitems))
			{
				$value = $value . ' <span class="muted text-muted">(' . $node->numitems . ')</span>';
			}
	
			$content = $content . '<li rel="'.$node->id.'">';
			$content = $content . JHtml::link(JRoute::_(CjBlogHelperRoute::getCategoryRoute($node->id)), $value);
			$children = $node->getChildren();
			
			if(!empty($children)) 
			{
				$content = $content . CjBlogCategoriesHelper::get_tree_list($children, $excluded);
			}
	
			$content = $content . '</li>';
		}
	
		$content = $content . '</ul>';
	
		return $content;
	}
}