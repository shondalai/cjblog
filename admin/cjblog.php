<?php
/**
 * @version		$Id: cjblog.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_cjblog'.DIRECTORY_SEPARATOR.'api.php';

// Add logger
$date = JFactory::getDate()->format('Y.m.d');
JLog::addLogger(array('text_file' => CJBLOG.'.admin.'.$date.'.log.php'), JLog::ALL, CJBLOG);


/** BASIC ENTRY CHECK */
// $user = JFactory::getUser();

// if(!$user->authorise('core.manage'));{

// 	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
// }

CJLib::import('corejoomla.nestedtree.core');
CJFunctions::load_jquery(array('libs'=>array()));
CJLib::import('corejoomla.ui.bootstrap');

$input = JFactory::getApplication()->input;
$view = $input->getCmd('view', 'cpanel');
$task = $input->getCmd('task');

require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';
require_once JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php';
	
$document = JFactory::getDocument();
$app = JFactory::getApplication();

JSubMenuHelper::addEntry(JText::_('COM_CJBLOG_CONTROL_PANEL'), 'index.php?option='.CJBLOG.'&amp;view=cpanel', $view == 'cpanel');
JSubMenuHelper::addEntry(JText::_('COM_CJBLOG_USERS'), 'index.php?option='.CJBLOG.'&amp;view=users', $view == 'users');
JSubMenuHelper::addEntry(JText::_('COM_CJBLOG_BADGES'), 'index.php?option='.CJBLOG.'&amp;view=badges', $view == 'badges');
JSubMenuHelper::addEntry(JText::_('COM_CJBLOG_BADGE_RULES'), 'index.php?option='.CJBLOG.'&amp;view=badgerules', $view == 'badgerules');
JSubMenuHelper::addEntry(JText::_('COM_CJBLOG_BADGE_ACTIVITY'), 'index.php?option='.CJBLOG.'&amp;view=badgeactivity', $view == 'badgeactivity');
JSubMenuHelper::addEntry(JText::_('COM_CJBLOG_POINTS'), 'index.php?option='.CJBLOG.'&amp;view=points', $view == 'points');
JSubMenuHelper::addEntry(JText::_('COM_CJBLOG_POINT_RULES'), 'index.php?option='.CJBLOG.'&amp;view=pointrules', $view == 'pointrules');
JToolBarHelper::preferences(CJBLOG);

if(APP_VERSION > 2.5){
	
	JToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER');
	JHtmlSidebar::setAction('index.php?option=com_content&view=articles');
}

$document->addStyleSheet(JUri::root(true).'/administrator/components/'.CJBLOG.'/assets/css/cjblog.admin.min.css');
$document->addScript(JUri::root(true).'/administrator/components/'.CJBLOG.'/assets/js/cjblog.admin.min.js');

$class = 'CjBlogController'.JString::ucfirst($view);
$controller = new $class();

/********************************* VERSION CHECK *******************************/
if(empty($task)){

	$version = $app->getUserState(CJBLOG.'.VERSION');

	if(!$version){
			
		$version = CJFunctions::get_component_update_check(CJBLOG, CJBLOG_VERSION);
		$v = array();
		
		if(!empty($version)){
			
			$v['connect'] = (int)$version['connect'];
			$v['version'] = (string)$version['version'];
			$v['released'] = (string)$version['released'];
			$v['changelog'] = (string)$version['changelog'];
			$v['status'] = (int)$version['status'];
				
			$app->setUserState(CJBLOG.'.VERSION', $v);
		}
	}

	if(!empty($version['status']) && $version['status'] == 1 && !empty($version['version'])) {

		echo '<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				CjBlog '.$version['version'].' is now available, <a href="'.JUri::base(true).'/index.php?option=com_installer&view=update">Update it now</a>.
			</div>';
	}
}
/********************************* VERSION CHECK *******************************/


$controller->execute($task);
$controller->redirect();


if(empty($task)){

	echo '<input type="hidden" value="'.$view.'" id="cjblog_page_id">';
	echo '<div class="center">';
	echo '<div><small>Version: '.CJBLOG_VERSION.' | CjBlog is developed by <a target="_blank" href="http://www.corejoomla.com">corejoomla.com</a> and is licensed under Gnu/GPL.</small></div>';
	echo '<div><strong>If you use CjBlog, please please post a rating and a review at the <a target="_blank" href="http://extensions.joomla.org/extensions/authoring-a-content/blog/22885">Joomla! Extensions Directory.</a>';
	echo '</div>';
}
?>