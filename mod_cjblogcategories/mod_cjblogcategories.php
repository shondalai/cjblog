<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  mod_cjblogcategories
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/components/com_cjlib/framework.php';
require_once JPATH_SITE . '/components/com_cjblog/helpers/route.php';
require_once JPATH_ROOT . '/modules/mod_cjblogcategories/helper.php';

CJLib::import('corejoomla.framework.core');
jimport('joomla.application.categories');
CJFunctions::load_jquery(array('libs' => array('treeview')));

$app = JFactory::getApplication();
$categories = JCategories::getInstance('Content', array('countItems' => true, 'assetid' => 'cjblogcategories'));

if (is_object($categories))
{
	$root = intval($params->get('catid', 0));
	$excluded = trim($params->get('excluded', ''));
	$excluded = explode(',', $excluded);
	$excluded = Joomla\Utilities\ArrayHelper::toInteger($excluded);
	
	$nodes = $categories->get($root);
	$fields = array();
	$script = '';
	
	if ($nodes)
	{
		$nodes = $nodes->getChildren(false);
		$catid = $app->input->getInt('id', 0);
		$appname = $app->input->getCmd('option', '');
		$view = $app->input->getCmd('view', '');
		
		if ($catid > 0 && $appname == 'com_cjblog' && $view = 'category')
		{
			$script = '
				jQuery(".cjblogcategories").find("li[rel=\'' . $catid . '\']").find(".expandable-hitarea:first").click();
				jQuery(".cjblogcategories").find("li[rel=\'' . $catid . '\']").parents("li.expandable").find(".expandable-hitarea:first").click();
				jQuery(".cjblogcategories").find("li[rel=\'' . $catid . '\']").find("a:first").css("font-weight", "bold");';
		}
		
		JFactory::getDocument()->addScriptDeclaration('jQuery(document).ready(function($){
		        jQuery(".cjblogcategories").find(".cat-list:first").treeview({"collapsed": true});' . $script . 'jQuery(".cjblogcategories").show();});');
		
		echo '<div class="cjblogcategories" style="display: none;">' . CjBlogCategoriesHelper::get_tree_list($nodes, $excluded) . '</div>';
	}
}