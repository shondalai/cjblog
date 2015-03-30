<?php
/**
 * @version		$Id: default.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$itemid = CJFunctions::get_active_menu_id();
$active_id = 4;

JPluginHelper::importPlugin( 'CjBlog' );
JPluginHelper::importPlugin('content');

$app 			= JFactory::getApplication();
$dispatcher 	= JDispatcher::getInstance();
$api 			= new CjLibApi();
$params 		= $app->getParams('com_content');

$old_start 		= $this->pagination->get('limitstart') + $this->pagination->get('limit');
$older_url 		= JRoute::_('index.php?option='.CJBLOG.'&view=blog&id='.$this->user['id'].':'.$this->user['username'].'&start='.$old_start.$itemid);

$new_start 		= $this->pagination->get('limitstart') - $this->pagination->get('limit');
$newer_url 		= JRoute::_('index.php?option='.CJBLOG.'&view=blog&id='.$this->user['id'].':'.$this->user['username'].'&start='.$new_start.$itemid);


$profileApp 	= $this->params->get('profile_component', 'cjblog');
$avatarApp 		= $this->params->get('avatar_component', 'cjblog');
$user_name 		= $this->params->get('user_display_name');
$layout			= $this->params->get('layout', 'default');
?>
<div id="cj-wrapper">

	<?php include_once JPATH_COMPONENT.'/helpers/header.php';?>
	<?php echo JLayoutHelper::render($layout.'.toolbar', array('params'=>$this->params));?>
	
	<div class="container-fluid">
		
		<div class="row-fluid">
			<div class="span12">
				<div class="well well-small clearfix">
					<?php if($avatarApp != 'none'):?>
					<div class="pull-left margin-right-10">
						<a href="<?php echo $api->getUserProfileUrl($profileApp, $this->user['id'], true);?>">
							<img class="img-avatar photo" src="<?php echo $api->getUserAvatarImage($avatarApp, $this->user['id'], $this->user['email'], 48);?>" 
								alt="<?php echo $this->escape($this->user['name']);?>">
						</a>
					</div>
					<?php endif;?>
					<div>
						<h2 class="nopad-top">
							<?php echo $this->escape($this->user['name'])?> 
							<small>
								( <a href="<?php echo $api->getUserProfileUrl($profileApp, $this->user['id'], true);?>">
									<?php echo JText::_('LBL_PROFILE');?>
								</a> )
							</small>
						</h2>
						<p><?php echo CjBlogApi::get_user_badges_markup($this->user);?></p>
						<?php if(!empty($this->user['about'])):?>
						<div><?php echo $this->user['about']?></div>
						<?php endif;?>
					</div>
				</div>
			</div>
		</div>
		<?php
		if(!empty($this->articles))
		{
			$i = 0;
			$columns = 1;
			$colspan = 12 / $columns;
			
			foreach($this->articles as $article)
			{
				if($i % $columns == 0)
				{
					?>
					<div class="row-fluid">
					<?php
				}
				?>
				<div class="span<?php echo $colspan;?>">
					<div class="page-header">
						<h1>
							<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias));?>">
							<?php echo $this->escape($article->title);?>
							</a>
							<?php if($article->hits > $this->params->get('hot_article_num_hits')):?>
							<sup><span class="label label-important"><i class="fa fa-fire"></i> <?php echo JText::_('LBL_HOT');?></span></sup>
							<?php endif;?>
						</h1>
						<div class="muted">
							<small class="align-middle">
								<i class="icon-eye-open"></i> <?php echo JText::sprintf('TXT_NUM_HITS', $article->hits)?>.
								<?php echo JText::sprintf(
										'TXT_POSTED_IN_CATEGORY_ON',
										JHtml::link(JRoute::_('index.php?option='.CJBLOG.'&view=category&id='.$article->catid.':'.$article->category_alias.$articles_itemid), $this->escape($article->category_title)),
										CJFunctions::get_localized_date($article->created, 'd F Y')
									);?>
							</small>
						</div>
					</div>
					<?php 
					$params = new JRegistry;
					$params->loadString($article->images);
					$intro_image = $params->get('image_intro');
					
					if(!empty($intro_image)) {
					?>
					<img alt="<?php echo $this->escape($params->get('image_intro_alt'));?>" src="<?php echo $params->get('image_intro');?>" class="img-polaroid margin-bottom-10" width="100%">
					<?php
					}
					
					$article->text = $article->introtext;
					$dispatcher->trigger('onContentPrepare', array ('com_cjblog.blog', &$article, &$params, 0));
					echo $article->text;
					?>
					
					<div class="well well-small cleafix">
						<span class="article-hits"><?php echo JText::sprintf('TXT_NUM_HITS', $article->hits);?></span>
						<div class="pull-right">
							<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias));?>">
								<?php echo JText::_('LBL_READ_MORE');?>
							</a>
						</div>
					</div>
				</div>
				<?php 
				$i++;
				
				if($i % $columns == 0 || count($this->articles) == $i)
				{
					?>
					</div>
					<?php
				}
			}
			?>
			<div class="row-fluid">
				<div class="span12">
					<ul class="pager">
						<li class="previous<?php echo $this->pagination->get('pages.current') == $this->pagination->get('pages.total') ? ' disabled' : '';?>">
							<a href="<?php echo $this->pagination->get('pages.current') == $this->pagination->get('pages.total') ? '#' : $older_url;?>">
								&larr; <?php echo JText::_('LBL_OLDER');?>
							</a>
						</li>
						<li class="next<?php echo $this->pagination->get('pages.current') == 1 ? ' disabled' : '';?>">
							<a href="<?php echo $this->pagination->get('pages.current') == 1 ? '#' : $newer_url;?>"><?php echo JText::_('LBL_NEWER');?> &rarr;</a>
						</li>
					</ul>
				</div>
			</div>
			<?php 
		}
		else
		{
			?>
			<p><?php echo JText::_('LBL_NO_ARTICLES_FOUND');?></p>
			<?php 
		}
		?>
	</div>
</div>