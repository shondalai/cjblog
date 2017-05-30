<?php
/**
 * @package     corejoomla.site
 * @subpackage  mod_cjblogarchive
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/components/com_cjblog/helpers/constants.php';
require_once JPATH_ROOT . '/components/com_cjblog/helpers/route.php';
require_once JPATH_ROOT . '/components/com_cjblog/helpers/query.php';
require_once JPATH_ROOT . '/components/com_cjblog/lib/api.php';
require_once JPATH_ROOT . '/components/com_cjblog/helpers/helper.php';

////////////////////////////////////////// CjLib Includes ///////////////////////////////////////////////
require_once JPATH_ROOT . '/components/com_cjlib/framework.php';
require_once JPATH_ROOT . '/components/com_cjlib/framework/api.php';
CJLib::import('corejoomla.framework.core');
////////////////////////////////////////// CjLib Includes ///////////////////////////////////////////////

$app 		= JFactory::getApplication();
$document 	= JFactory::getDocument();
$db 		= JFactory::getDbo();
$query 		= $db->getQuery(true);
$options	= JComponentHelper::getParams('com_cjblog');

$query
	->select($query->year('a.created').' as year')
	->select($query->month('a.created').' as month')
	->select('count(*) as count')
	->from('#__content as a')
	->group('year')
	->group('month')
	->order('year')
	->order('month');

$excludedCategories = $options->get('exclude_categories', array());
$excludedCategories = Joomla\Utilities\ArrayHelper::toInteger($excludedCategories);
if(!empty($excludedCategories))
{
	$query->where('a.catid not in ('.implode(',', $excludedCategories).')');
}

$db->setQuery($query);
$results = $db->loadObjectList();

$archives = array();
foreach ($results as $result)
{
	$archives[$result->year][] = $result;
}

$selected = $app->input->getInt('year');
if($selected)
{
	$month = $app->input->getInt('month');
	if($month)
	{
		$selected = $selected.$month;
	}
}

$script = 'jQuery(".cjblog-archive-list").find("li[rel=\''.$selected.'\']").find(".expandable-hitarea:first").click();
		jQuery(".cjblog-archive-list").find("li[rel=\''.$selected.'\']").parents("li.expandable").find(".expandable-hitarea:first").click();
		jQuery(".cjblog-archive-list").find("li[rel=\''.$selected.'\']").find("a:first").css("font-weight", "bold");';

CJFunctions::load_jquery(array('libs'=>array('treeview')));
$document->addScriptDeclaration('jQuery(document).ready(function($){jQuery(".cjblog-archive-list").find(".cat-list:first").treeview({"collapsed": true});'.$script.'});');

require JModuleHelper::getLayoutPath('mod_cjblogarchive', $params->get('layout', 'default'));