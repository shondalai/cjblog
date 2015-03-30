<?php
/**
 * @version		$Id: polls.php 01 2011-01-11 11:37:09Z maverick $
 * @package		CoreJoomla.Polls
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2014 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
$user = JFactory::getUser();
$api = new CjLibApi();

$params = $displayData['params'];
$brand = isset($displayData['brand']) ? $displayData['brand'] : JText::_('COM_CJBLOG_LABEL_HOME');
$profileComponent = $params->get('profile_component', 'cjblog');

$categories_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=categories');
$users_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=users');
$user_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=user');
$blog_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=blog');
$profile_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=profile');
$articles_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=articles');
$search_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=search');
$badges_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=badges');
$form_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=form');
$tags_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=tags');

if($params->get('show_header_bar') == 1)
{
	?>
	<nav class="navbar navbar-default" role="navigation">
		<div class="navbar-inner">
			<div class="navbar-header">
				<button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target="#cjb-navbar-collapse">
	<!-- 				<span class="sr-only">Toggle navigation</span> -->
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="brand navbar-brand" href="<?php echo JRoute::_('index.php');?>">
					<?php echo JText::_('COM_CJBLOG_LABEL_HOME');?>
				</a>
			</div>
			<div class="collapse nav-collapse navbar-collapse navbar-responsive-collapse" id="cjb-navbar-collapse">
				<?php if(!$user->guest):?>
				<ul class="nav pull-right nomargin-bottom">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<?php echo JText::_('COM_CJBLOG_LABEL_ACCOUNT');?> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							
							<?php if(!empty($profile_itemid)):?>
							<li>
								<a href="<?php echo $api->getUserProfileUrl($profileComponent, $user->id, true);?>">
									<i class="fa fa-user"></i> <?php echo JText::_('LBL_MY_PROFILE');?>
								</a>
							</li>
							<?php endif;?>
							<?php if(!empty($blog_itemid)):?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=blog&id='.$user->id.':'.$user->username.$blog_itemid);?>">
									<i class="fa fa-book"></i> <?php echo JText::_('LBL_MY_BLOG');?>
								</a>
							</li>
							<?php endif;?>
							<li class="divider"></li>
							<?php if(!empty($articles_itemid)):?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=favorites'.$articles_itemid);?>">
									<i class="fa fa-bookmark"></i> <?php echo JText::_('LBL_MY_FAVORITES');?>
								</a>
							</li>
							<?php endif;?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_cjblog&view=user&task=articles&id='.$user->id.':'.$user->username.$user_itemid);?>">
									<i class="fa fa-pencil"></i> <?php echo JText::_('LBL_MY_ARTICLES');?>
								</a>
							</li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_cjblog&view=user'.$user_itemid);?>">
									<i class="fa fa-gift"></i> <?php echo JText::_('LBL_MY_POINTS');?>
								</a>
							</li>
						</ul>
					</li>
				</ul>
				<?php endif;?>
				
				<ul class="nav nomargin-bottom">
					<li><a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=categories'.$categories_itemid)?>"><?php echo JText::_('LBL_CATEGORIES');?></a></li>
					<li><a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users'.$users_itemid)?>"><?php echo JText::_('LBL_BLOGGERS');?></a></li>
					
					<?php if(!empty($tags_itemid)):?>
					<li><a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=tags'.$tags_itemid)?>"><?php echo JText::_('LBL_TAGS');?></a></li>
					<?php endif;?>
					
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<?php echo JText::_('COM_CJBLOG_LABEL_DISCOVER');?> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<?php if(!empty($articles_itemid)):?>
							<li class="nav-header dropdown-header"><?php echo JText::_('LBL_ARTICLES');?></li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=latest'.$articles_itemid);?>">
									<i class="fa fa-tasks"></i> <?php echo JText::_('LBL_MOST_RECENT');?>
								</a>
							</li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=trending'.$articles_itemid);?>">
									<i class="fa fa-star"></i> <?php echo JText::_('LBL_TRENDING_ARTICLES');?>
								</a>
							</li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=popular'.$articles_itemid);?>">
									<i class="fa fa-fire"></i> <?php echo JText::_('LBL_ALL_TIME_POPULAR');?>
								</a>
							</li>
							<?php endif;?>
							
							<?php if(!empty($users_itemid)):?>
							<li class="divider"></li>
							<li class="nav-header dropdown-header"><?php echo JText::_('LBL_BLOGGERS');?></li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users&task=top'.$users_itemid)?>">
									<i class="fa fa-star"></i> <?php echo JText::_('LBL_TOP_BLOGGERS');?>
								</a>
							</li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users'.$users_itemid)?>">
									<i class="fa fa-users"></i> <?php echo JText::_('LBL_NEW_BLOGGERS');?>
								</a>
							</li>
							<?php endif;?>
							
							<?php if(!empty($badges_itemid)):?>
							<li class="divider"></li>
							<li class="nav-header dropdown-header"><?php echo JText::_('LBL_BADGES');?></li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badges'.$badges_itemid)?>">
									<i class="fa fa-tags"></i> <?php echo JText::_('LBL_VIEW_ALL_BADGES');?>
								</a>
							</li>
							<?php endif;?>
							
							<?php if (!empty($search_itemid)):?>
							<li class="divider"></li>
							<li class="nav-header dropdown-header"><?php echo JText::_('LBL_SEARCH');?></li>
							<li><a href="#"><i class="icon-search"></i> <?php echo JText::_('LBL_ADVANCED_SEARCH')?></a></li>
							<?php endif;?>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<?php 
}
?>