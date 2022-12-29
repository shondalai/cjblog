<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$app 				= JFactory::getApplication();
$user 				= JFactory::getUser();
$cache 				= JFactory::getCache();

$params 			= $displayData['params'];
$theme 				= $params->get('theme', 'default');
$profileComponent	= $params->get('profile_component', 'cjblog');
$displayName		= $params->get('display_name', 'name');

JLoader::import('statistics', JPATH_COMPONENT.'/models');
$model = JModelLegacy::getInstance( 'statistics', 'CjBlogModel' );

$api = new CjLibApi();
$return = $cache->call(array($model, 'getStatistics'));
$latest = $return->latestMember ? $api->getUserProfileUrl($profileComponent, $return->latestMember->id, false, $this->escape($return->latestMember->$displayName)) : 'N/A';
?>
<?php if($params->get('show_footer_block', 1) == 1):?>

<h3 class="cjheader"><?php echo JText::sprintf('COM_CJBLOG_BLOGS_STATISTICS', $app->getCfg('sitename'));?></h3>
<?php echo JText::sprintf('COM_CJBLOG_TOTAL_ARTICLES', $return->articles);?> &bull; 
<?php echo JText::sprintf('COM_CJBLOG_TOTAL_MEMBERS', $return->users);?> &bull; 
<?php echo JText::sprintf('COM_CJBLOG_OUR_LATEST_MEMBER', $latest);?>

<?php endif;?>