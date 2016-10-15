<?php
/**
 * @version		$Id: mod_cjbloggers.php 01 2012-05-19 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Modules.site
 * @copyright	Copyright (C) 2009 - 2016 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT.'/modules/mod_cjbloggers/helper.php';
require_once JPATH_ROOT.'/components/com_cjlib/framework.php';

CJLib::import('corejoomla.framework.core');
CJLib::import('corejoomla.ui.bootstrap', true);

$type = intval($params->get('list_type', 2));
$count = intval($params->get('count', 10));
$avatar_size = intval($params->get('avatar_size', 48));
$excludes = $params->get('exclude_user_groups', array());

$bloggers = CjBloggersHelper::get_bloggers_list($type, $count, $excludes);

if(!empty($bloggers))
{
	$options = JComponentHelper::getParams('com_cjblog');
	$avatarApp = $options->get('avatar_component', 'cjblog');
	$profileApp = $options->get('profile_component', 'cjblog');
	$ids = array();
	$api = new CjLibApi();
	
	foreach ($bloggers as $blogger)
	{
		$userIds[] = $blogger->id;
	}

	if(!empty($userIds))
	{
		$api->prefetchUserProfiles($avatarApp, $userIds);
		if($avatarApp != $profileApp)
		{
			$api->prefetchUserProfiles($profileApp, $userIds);
		}
	}
	
	$doc = JFactory::getDocument();
	$doc->addStyleSheet(CJLIB_URI.'/framework/assets/cj.framework.css');
	$doc->addScriptDeclaration('jQuery(document).ready(function($){$(\'body\').tooltip({selector: \'.tooltip-hover\'});});');
	
	echo '<div id="cj-wrapper"><div class="clearfix">';
	
	foreach ($bloggers as $blogger)
	{
		?>
		<a href="<?php echo $api->getUserProfileUrl($profileApp, $blogger->id);?>" class="thumbnail pull-left margin-bottom-5">
			<img class="img-avatar" src="<?php echo $api->getUserAvatarImage($avatarApp, $blogger->id, $blogger->email, $avatar_size, true);?>" width="<?php echo $avatar_size?>px;" 
				alt="<?php echo CjLibUtils::escape($blogger->name);?>" style="max-width: <?php echo $avatar_size?>px;">
		</a>
		<?php
	}
	
	echo '</div></div>';
}