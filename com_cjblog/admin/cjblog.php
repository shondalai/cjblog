<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();
JHtml::_('behavior.tabstate');

if (! JFactory::getUser()->authorise('core.manage', 'com_cjblog'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

////////////////////////////////////////// CjLib Includes ///////////////////////////////////////////////
require_once JPATH_ROOT.'/components/com_cjlib/framework.php';
require_once JPATH_ROOT.'/components/com_cjlib/framework/api.php';
CJLib::import('corejoomla.framework.core');

require_once JPATH_COMPONENT_SITE.'/lib/api.php';
require_once JPATH_COMPONENT_SITE.'/helpers/constants.php';
////////////////////////////////////////// CjLib Includes ///////////////////////////////////////////////


JLoader::register('CjBlogHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/cjblog.php');
JFactory::getLanguage()->load('com_cjblog', JPATH_ROOT);

$controller = JControllerLegacy::getInstance('CjBlog');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
