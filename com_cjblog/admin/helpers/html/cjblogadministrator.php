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

abstract class JHtmlCjBlogAdministrator
{
	public static function association ($articleid)
	{
		// Defaults
		$html = '';
		
		// Get the associations
		if ($associations = JLanguageAssociations::getAssociations('com_cjblog', '#__cjblog_articles', 'com_cjblog.item', $articleid))
		{
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}
			
			// Get the associated menu items
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('c.*')
				->select('l.sef as lang_sef')
				->from('#__content as c')
				->select('cat.title as category_title')
				->join('LEFT', '#__categories as cat ON cat.id=c.catid')
				->where('c.id IN (' . implode(',', array_values($associations)) . ')')
				->join('LEFT', '#__languages as l ON c.language=l.lang_code')
				->select('l.image')
				->select('l.title as language_title');
			$db->setQuery($query);
			
			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				throw new Exception($e->getMessage(), 500);
			}
			
			if ($items)
			{
				foreach ($items as &$item)
				{
					$text = strtoupper($item->lang_sef);
					$url = JRoute::_('index.php?option=com_content&task=article.edit&id=' . (int) $item->id);
					$tooltipParts = array(
							JHtml::_('image', 'mod_languages/' . $item->image . '.gif', $item->language_title, array('title' => $item->language_title), true), 
							$item->title,'(' . $item->category_title . ')');
					$item->link = JHtml::_('tooltip', implode(' ', $tooltipParts), null, null, $text, $url, null, 'hasTooltip label label-association label-' . $item->lang_sef);
				}
			}
			
			$html = JLayoutHelper::render('joomla.content.associations', $items);
		}
		
		return $html;
	}

	public static function featured ($value = 0, $i, $canChange = true)
	{
		JHtml::_('bootstrap.tooltip');
		
		// Array of image, task, title, action
		$states = array(
				0 => array(
						'unfeatured',
						'articles.featured',
						'COM_CJBLOG_UNFEATURED',
						'COM_CJBLOG_TOGGLE_TO_FEATURE'
				),
				1 => array(
						'featured',
						'articles.unfeatured',
						'COM_CJBLOG_FEATURED',
						'COM_CJBLOG_TOGGLE_TO_UNFEATURE'
				)
		);
		$state = \Joomla\Utilities\ArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon = $state[0];
		
		if ($canChange)
		{
			$html = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' .
					 ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[3]) . '"><i class="icon-' . $icon . '"></i></a>';
		}
		else
		{
			$html = '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[2]) .
					 '"><i class="icon-' . $icon . '"></i></a>';
		}
		
		return $html;
	}
}
