<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

abstract class JHtmlIcon
{

	public static function create ($category, $params, $attribs = array(), $legacy = false)
	{
		JHtml::_('bootstrap.tooltip');
		
		$uri = JUri::getInstance();
		
		$url = 'index.php?option=com_cjblog&task=article.add&return=' . base64_encode($uri) . '&t_id=0&catid=' . $category->id;
		
		if ($params->get('show_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'system/new.png', JText::_('JNEW'), null, true);
			}
			else
			{
				$text = '<span class="icon-plus"></span>&#160;' . JText::_('JNEW') . '&#160;';
			}
		}
		else
		{
			$text = JText::_('JNEW') . '&#160;';
		}
		
		// Add the button classes to the attribs array
		if (isset($attribs['class']))
		{
			$attribs['class'] = $attribs['class'] . ' btn btn-primary';
		}
		else
		{
			$attribs['class'] = 'btn btn-primary';
		}
		
		$button = JHtml::_('link', JRoute::_($url), $text, $attribs);
		
		$output = '<span class="hasTooltip" title="' . JHtml::tooltipText('COM_CJBLOG_CREATE_ARTICLE') . '">' . $button . '</span>';
		
		return $output;
	}

	public static function email ($article, $params, $attribs = array(), $legacy = false)
	{
		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';
		
		$uri = JUri::getInstance();
		$base = $uri->toString(array(
				'scheme',
				'host',
				'port'
		));
		$template = JFactory::getApplication()->getTemplate();
		$link = $base . JRoute::_(CjBlogHelperRoute::getArticleRoute($article->slug, $article->catid), false);
		$url = 'index.php?option=com_mailto&tmpl=component&template=' . $template . '&link=' . MailToHelper::addLink($link);
		
		$status = 'width=400,height=350,menubar=yes,resizable=yes';
		
		if ($params->get('show_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'system/emailButton.png', JText::_('JGLOBAL_EMAIL'), null, true);
			}
			else
			{
				$text = '<span class="icon-envelope"></span> ' . JText::_('JGLOBAL_EMAIL');
			}
		}
		else
		{
			$text = JText::_('JGLOBAL_EMAIL');
		}
		
		$attribs['title'] = JText::_('JGLOBAL_EMAIL');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
		
		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);
		
		return $output;
	}

	public static function edit ($article, $params, $attribs = array(), $legacy = false)
	{
		$user = JFactory::getUser();
		$uri = JUri::getInstance();
		
		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return;
		}
		
		// Ignore if the state is negative (trashed).
		if ($article->state < 0)
		{
			return;
		}
		
		JHtml::_('bootstrap.tooltip');
		
		// Show checked_out icon if the article is checked out by a different
		// user
		if (property_exists($article, 'checked_out') && property_exists($article, 'checked_out_time') && $article->checked_out > 0 &&
				 $article->checked_out != $user->get('id'))
		{
			$checkoutUser = JFactory::getUser($article->checked_out);
			$button = JHtml::_('image', 'system/checked_out.png', null, null, true);
			$date = JHtml::_('date', $article->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT') . ' :: ' . JText::sprintf('COM_CJBLOG_CHECKED_OUT_BY', $checkoutUser->name) . ' <br /> ' .
					 $date;
			
			return '<span class="hasTooltip" title="' . JHtml::tooltipText($tooltip . '', 0) . '">' . $button . '</span>';
		}
		
		$url = 'index.php?option=com_cjblog&task=article.edit&t_id=' . $article->id . '&return=' . base64_encode($uri);
		
		if ($article->state == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}
		
		$date = JHtml::_('date', $article->created);
		$author = $article->created_by_alias ? $article->created_by_alias : $article->author;
		
		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_CJBLOG_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));
		
		if ($legacy)
		{
			$icon = $article->state ? 'edit.png' : 'edit_unpublished.png';
			if (strtotime($article->publish_up) > strtotime(JFactory::getDate()) ||
					 ((strtotime($article->publish_down) < strtotime(JFactory::getDate())) && $article->publish_down != '0000-00-00 00:00:00'))
			{
				$icon = 'edit_unpublished.png';
			}
			$text = JHtml::_('image', 'system/' . $icon, JText::_('JGLOBAL_EDIT'), null, true);
		}
		else
		{
			$icon = $article->state ? 'edit' : 'eye-close';
			if (strtotime($article->publish_up) > strtotime(JFactory::getDate()) ||
					 ((strtotime($article->publish_down) < strtotime(JFactory::getDate())) && $article->publish_down != '0000-00-00 00:00:00'))
			{
				$icon = 'eye-close';
			}
			$text = '<span class="hasTooltip icon-' . $icon . ' tip" title="' . JHtml::tooltipText(JText::_('COM_CJBLOG_EDIT_ITEM'), $overlib, 0) .
					 '"></span>&#160;' . JText::_('JGLOBAL_EDIT') . '&#160;';
		}
		
		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);
		
		return $output;
	}

	public static function print_popup ($article, $params, $attribs = array(), $legacy = false)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$request = $input->request;
		
		$url = CjBlogHelperRoute::getArticleRoute($article->slug, $article->catid);
		$url .= '&tmpl=component&print=1&layout=default&page=' . @ $request->limitstart;
		
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
		
		// checks template image directory for image, if non found default are
		// loaded
		if ($params->get('show_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
			}
			else
			{
				$text = '<span class="icon-print"></span>&#160;' . JText::_('JGLOBAL_PRINT') . '&#160;';
			}
		}
		else
		{
			$text = JText::_('JGLOBAL_PRINT');
		}
		
		$attribs['title'] = JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
		$attribs['rel'] = 'nofollow';
		
		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}

	public static function print_screen ($article, $params, $attribs = array(), $legacy = false)
	{
		// Checks template image directory for image, if none found default are
		// loaded
		if ($params->get('show_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
			}
			else
			{
				$text = '<span class="icon-print"></span>&#160;' . JText::_('JGLOBAL_PRINT') . '&#160;';
			}
		}
		else
		{
			$text = JText::_('JGLOBAL_PRINT');
		}
		
		return '<a href="#" onclick="window.print();return false;">' . $text . '</a>';
	}
}
