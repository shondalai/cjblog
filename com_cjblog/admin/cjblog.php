<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

if (! JFactory::getUser()->authorise('core.manage', 'com_cjblog'))
{
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

////////////////////////////////////////// CjLib Includes ///////////////////////////////////////////////
require_once JPATH_ROOT.'/components/com_cjlib/framework.php';
CJLib::import('corejoomla.framework.core');

require_once JPATH_ROOT.'/components/com_cjblog/lib/api.php';
require_once JPATH_ROOT.'/components/com_cjblog/helpers/constants.php';
////////////////////////////////////////// CjLib Includes ///////////////////////////////////////////////


JLoader::register('CjBlogHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/cjblog.php');
JFactory::getLanguage()->load('com_cjblog', JPATH_ROOT);

$controller = JControllerLegacy::getInstance('CjBlog');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
