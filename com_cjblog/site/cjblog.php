<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/helpers/constants.php';
require_once JPATH_COMPONENT . '/helpers/route.php';
require_once JPATH_COMPONENT . '/helpers/query.php';
require_once JPATH_COMPONENT . '/lib/api.php';
require_once JPATH_COMPONENT . '/helpers/helper.php';

////////////////////////////////////////// CjLib Includes ///////////////////////////////////////////////
require_once JPATH_ROOT.'/components/com_cjlib/framework.php';
CJLib::import('corejoomla.framework.core');
////////////////////////////////////////// CjLib Includes ///////////////////////////////////////////////

if(CJBLOG_MAJOR_VERSION < 4) {
    require_once JPATH_ROOT . '/components/com_content/helpers/association.php';
    require_once JPATH_ROOT . '/components/com_content/helpers/route.php';
}

if(CjBlogSiteHelper::isUserBanned())
{
	echo JText::_('COM_CJBLOG_YOUR_ACCOUNT_IS_BLOCKED');
}
else
{
	$controller = JControllerLegacy::getInstance('CjBlog');
	$controller->execute(JFactory::getApplication()->input->get('task'));
	$controller->redirect();
}