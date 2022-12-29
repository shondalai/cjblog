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
$return = $cache->call(array($model, 'getLoggedInUsers'));

$api = new CjLibApi();
?>
<?php if($params->get('show_footer_block', 1) == 1):?>

<h3 class="cjheader"><?php echo JText::_('COM_CJBLOG_WHO_IS_ONLINE');?></h3>
<p><?php echo JText::sprintf('COM_CJBLOG_ONLINE_USERS_DETAILS', ($return->guests + $return->members), $return->guests, $return->members)?></p>
<p>
	<?php 
	echo JText::_('COM_CJBLOG_REGISTERED_USERS');
	
	if(!empty($return->users))
	{
		foreach ($return->users as $item)
		{
			echo ',&nbsp;&nbsp;'.$api->getUserProfileUrl($profileComponent, $item->id, false, $this->escape($item->$displayName));
		}
	}
	?>
</p>
<?php endif;?>