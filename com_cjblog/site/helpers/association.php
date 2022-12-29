<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JLoader::register('CjBlogHelper', JPATH_ADMINISTRATOR . '/components/com_cjblog/helpers/cjblog.php');
JLoader::register('CategoryHelperAssociation', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/association.php');

abstract class CjBlogHelperAssociation extends CategoryHelperAssociation
{
	public static function getAssociations ($id = 0, $view = null)
	{
		jimport('helper.route', JPATH_COMPONENT_SITE);
		
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$view = is_null($view) ? $jinput->get('view') : $view;
		$id = empty($id) ? $jinput->getInt('id') : $id;
		
		if ($view == 'article')
		{
			if ($id)
			{
				$associations = JLanguageAssociations::getAssociations('com_cjblog', '#__cjblog_articles', 'com_cjblog.item', $id);
				
				$return = array();
				
				foreach ($associations as $tag => $item)
				{
					$return[$tag] = CjBlogHelperRoute::getArticleRoute($item->id, $item->catid, $item->language);
				}
				
				return $return;
			}
		}
		
		if ($view == 'category' || $view == 'categories')
		{
			return self::getCategoryAssociations($id, 'com_cjblog');
		}
		
		return array();
	}
}
