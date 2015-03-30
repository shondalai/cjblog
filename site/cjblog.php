<?php
/**
 * @package     corejoomla.site
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2015 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined ( '_JEXEC' ) or die;

require_once 'api.php';

CJLib::import('corejoomla.nestedtree.core');
CJLib::import('corejoomla.template.core');

require_once JPATH_SITE.'/components/com_content/helpers/route.php';
require_once JPATH_COMPONENT.'/helpers/helper.php';
require_once JPATH_COMPONENT.'/helpers/route.php';

$app = JFactory::getApplication();
$view = $app->input->get('view');
$task = $app->input->get('task');

if($view == 'form' || $task == 'article.add' || $task == 'article.edit')
{
	$controller = JControllerLegacy::getInstance('CjBlog');
	$controller->execute($app->input->get('task'));
	$controller->redirect();
}
else 
{
	$path = JPATH_COMPONENT.'/controllers/'.$view.'.php';
	if(!file_exists($path)) CJFunctions::throw_error('View '.JString::ucfirst($view).' not found!', 500);
	require_once $path;
	
	$custom_tag = false;
	JHtml::_('jquery.framework');
	$params = JComponentHelper::getParams('com_cjblog');
	CJLib::behavior('bscore', array('customtag'=>$custom_tag));
	CJFunctions::load_jquery(array('libs'=>array('fontawesome'), 'custom_tag'=>$custom_tag));
	
	/**************************** MEDIA **************************************/
	$document = JFactory::getDocument();
	$document->addStyleSheet(CJBLOG_MEDIA_URI.'css/cjblog.min.css');
	CJFunctions::add_script_to_document($document, 'cjblog.min.js', true, CJBLOG_MEDIA_URI.'js/');
	/**************************** MEDIA **************************************/
	
	$class = 'CjBlogController'.JString::ucfirst($view);
	$controller = new $class();
	
	$controller->execute($task);
	echo '<input id="cjblog_page_id" value="'.$view.'" type="hidden">';
	echo '<div style="text-align: center; margin-top: 20px;" class="text-center muted">Powered by <a href="http://www.corejoomla.com" rel="follow">CjBlog</a></div>';
	
	$controller->redirect();
}