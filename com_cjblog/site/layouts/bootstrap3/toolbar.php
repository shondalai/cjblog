<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$user 		= JFactory::getUser();
$api 		= new CjLibApi();

$params 	= $displayData['params'];
$state 		= isset($displayData['state']) ? $displayData['state'] : null;
$brand 		= isset($displayData['brand']) ? $displayData['brand'] : JText::_('COM_CJBLOG_LABEL_HOME');
$category	= $params->get('catid', 0);
$profileApp	= $params->get('profile_component', 'cjblog');
$profileUrl	= $api->getUserProfileUrl($profileApp, $user->id);

$uri     	= JURI::getInstance();
$current 	= $uri->toString( array('scheme', 'host', 'port', 'path', 'query'));
$return		= base64_encode($current);

if($params->get('show_toolbar', 1) == 1)
{
	?>
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target="#cb-navbar-collapse">
	<!-- 				<span class="sr-only">Toggle navigation</span> -->
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo JRoute::_(CjBlogHelperRoute::getCategoriesRoute());?>">
					<?php echo JText::_('COM_CJBLOG_LABEL_HOME');?>
				</a>
			</div>
			<div class="navbar-collapse navbar-responsive-collapse" id="cb-navbar-collapse">
				<?php if(!$user->guest):?>
				<ul class="nav navbar-nav pull-right">
					<li>
						<a href="<?php echo CjBlogHelperRoute::getFormRoute(0, $category).'&return='.$return;?>">
							<i class="fa fa-edit"></i> <?php echo JText::_('COM_CJBLOG_LABEL_START_NEW_ARTICLE');?>
						</a>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<?php echo JText::_('COM_CJBLOG_LABEL_MY_STUFF');?> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li class="nav-header"><?php echo JText::_('COM_CJBLOG_LABEL_ACCOUNT');?></li>
							<?php if($user->authorise('core.create', 'com_cjblog')):?>
							<li>
								<a href="#" onclick="document.toolbarAuthorForm.submit(); return false;">
									<i class="fa fa-file-o"></i> <?php echo JText::_('COM_CJBLOG_LABEL_MY_ARTICLES');?>
								</a>
							</li>
							<?php endif;?>
							<li>
								<a href="<?php echo JRoute::_($profileUrl);?>">
									<i class="fa fa-user"></i> <?php echo JText::_('COM_CJBLOG_LABEL_MY_PROFILE');?>
								</a>
							</li>
							<?php if ($profileApp == 'cjblog'):?>
							<li>
								<a href="<?php echo JRoute::_(CjBlogHelperRoute::getProfileRoute().'&tab=reputation');?>">
									<i class="fa fa-trophy"></i> <?php echo JText::_('COM_CJBLOG_LABEL_MY_POINTS');?>
								</a>
							</li>
							<li>
								<a href="<?php echo JRoute::_(CjBlogHelperRoute::getProfileRoute().'&layout=favorites');?>">
									<i class="fa fa-heart-o"></i> <?php echo JText::_('COM_CJBLOG_LABEL_MY_FAVORITES');?>
								</a>
							</li>
							<?php endif;?>
						</ul>
					</li>
				</ul>
				<?php endif;?>
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<?php echo JText::_('COM_CJBLOG_LABEL_DISCOVER');?> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu multi-column columns-2">
							<li>
								<div class="row-fluid">
									<div class="span6">
										<ul class="multi-column-dropdown no-space-left">
											<li class="nav-header"><?php echo JText::_('COM_CJBLOG_LABEL_ACTIVITIES');?></li>
											<li>
												<a href="<?php echo JRoute::_(CjBlogHelperRoute::getCategoriesRoute());?>">
													<i class="fa fa-home"></i> <?php echo JText::_('COM_CJBLOG_BLOGS_INDEX')?>
												</a>
											</li>
											<li>
												<a href="<?php echo JRoute::_(CjBlogHelperRoute::getArticlesRoute().'&recent=true');?>">
													<i class="fa fa-tasks"></i> <?php echo JText::_('COM_CJBLOG_RECENT_ARTICLES')?>
												</a>
											</li>
							
											<li class="divider"></li>
											<li class="nav-header"><?php echo JText::_('COM_CJBLOG_LABEL_USERS');?></li>
											<li>
												<a href="<?php echo JRoute::_(CjBlogHelperRoute::getUsersRoute());?>">
													<i class="fa fa-users"></i> <?php echo JText::_('COM_CJBLOG_LABEL_MEMBERS')?>
												</a>
											</li>
											<li>
												<a href="<?php echo JRoute::_(CjBlogHelperRoute::getLeaderBoardRoute());?>">
													<i class="fa fa-trophy"></i> <?php echo JText::_('COM_CJBLOG_LEADERBOARD')?>
												</a>
											</li>
										</ul>
									</div>
									<div class="span6">
										<ul class="multi-column-dropdown no-space-left">
											<li class="nav-header"><?php echo JText::_('COM_CJBLOG_LABEL_ARTICLES');?></li>
											<li<?php echo (is_object($state) && $state->get('list.ordering') == 'hits') ? ' class="active"' : '';?>>
												<a href="#" onclick="filterArticles('', 'hits', 'desc', 0); return false;">
													<i class="fa fa-fire"></i> <?php echo JText::_('COM_CJBLOG_LABEL_POPULAR_ARTICLES')?>
												</a>
											</li>
											
											<li<?php echo !empty($featured) ? ' class="active"' : '';?>>
												<a href="#" onclick="filterArticles('only', 'votes', 'desc', 0); return false;">
													<i class="fa fa-star-o"></i> <?php echo JText::_('COM_CJBLOG_LABEL_FEATURED_ARTICLES')?>
												</a>
											</li>
											<li class="divider"></li>
											<li class="nav-header"><?php echo JText::_('COM_CJBLOG_LABEL_SEARCH');?></li>
											<li>
												<a href="<?php echo JRoute::_(CjBlogHelperRoute::getSearchRoute());?>">
													<i class="fa fa-search"></i> <?php echo JText::_('COM_CJBLOG_ADVANCED_SEARCH')?>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<?php 
}
?>

<form id="toolbarAuthorForm" name="toolbarAuthorForm" action="<?php echo JRoute::_('index.php');?>" method="post" style="display: none;">
	<input type="hidden" id="filter_author_id" name="filter_author_id" value="<?php echo $user->id;?>">
	<input type="hidden" id="option" name="option" value="com_cjblog">
	<input type="hidden" id="view" name="view" value="articles">
</form>

<form id="toolbarFilterForm" name="toolbarFilterForm" action="<?php echo JRoute::_(CjBlogHelperRoute::getArticlesRoute());?>" method="post" style="display: none;">
	<input type="hidden" id="filter_featured" name="filter_featured" value="">
	<input type="hidden" id="filter_order" name="filter_order" value="created">
	<input type="hidden" id="filter_order_Dir" name="filter_order_Dir" value="desc">
	<input type="hidden" id="filter_unanswered" name="filter_unanswered" value="0">
	<input type="hidden" id="view" name="option" value="com_cjblog">
	<input type="hidden" id="view" name="view" value="articles">
</form>

<script type="text/javascript">
<!--
function filterArticles(featured, order, direction, unanswered)
{
	document.toolbarFilterForm.filter_featured.value = featured;
	document.toolbarFilterForm.filter_order.value = order;
	document.toolbarFilterForm.filter_order_Dir.value = direction;
	document.toolbarFilterForm.filter_unanswered.value = unanswered;

	document.toolbarFilterForm.submit();
}
//-->
</script>