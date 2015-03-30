<?php
/**
 * @version		$Id: default.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>
<div class="container-fluid">	
	<div class="row-fluid">
		<div class="span9">
			<ul class="thumbnails">
				<li class="span2">
					<div class="thumbnail center">
						<a class="thumbnail" href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badges');?>">
							<img src="<?php echo JUri::base(true).'/components/'.CJBLOG.'/assets/images/badges-96px.png'?>" alt="">
						</a>
						<span><a href="#"><?php echo JText::_('COM_CJBLOG_BADGES');?></a></span>
					</div>
				</li>
				<li class="span2">
					<div class="thumbnail center">
						<a class="thumbnail" href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badgerules');?>">
							<img src="<?php echo JUri::base(true).'/components/'.CJBLOG.'/assets/images/badge-rules-96px.png'?>" alt="">
						</a>
						<span><a href="#"><?php echo JText::_('COM_CJBLOG_BADGE_RULES');?></a></span>
					</div>
				</li>
				<li class="span2">
					<div class="thumbnail center">
						<a class="thumbnail" href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=points');?>">
							<img src="<?php echo JUri::base(true).'/components/'.CJBLOG.'/assets/images/points-96px.png'?>" alt="">
						</a>
						<span><a href="#"><?php echo JText::_('COM_CJBLOG_POINTS');?></a></span>
					</div>
				</li>
				<li class="span2">
					<div class="thumbnail center">
						<a class="thumbnail" href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users');?>">
							<img src="<?php echo JUri::base(true).'/components/'.CJBLOG.'/assets/images/users-96px.png'?>" alt="">
						</a>
						<span><a href="#"><?php echo JText::_('COM_CJBLOG_USERS');?></a></span>
					</div>
				</li>
			</ul>
		</div>
		<div class="span3">
			<table class="table table-hover table-striped table-bordered">
				<caption><h4>Version Information</h4></caption>
				<tbody>
					<tr>
						<td>Installed Version:</td>
						<td><?php echo CJBLOG_VERSION;?></td>
					<tr>
					<?php if(!empty($this->version)):?>
					<tr>
						<td>Latest Version:</td>
						<td><?php echo $this->version['version'];?></td>
					</tr>
					<tr>
						<td>Release Date:</td>
						<td><?php echo $this->version['released'];?></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: center;">
							<?php if($this->version['status'] == 1):?>
							<a href="<?php echo JUri::base(true);?>/index.php?option=com_installer&view=update" target="_blank" class="btn btn-danger">
								<i class="icon-download icon-white"></i> <span style="color: white">Please Update</span>
							</a>
							<?php else:?>
							<a href="#" class="btn btn-success"><i class="icon-ok icon-white"></i> <span style="color: white">Up-to date</span></a>
							<?php endif;?>
						</td>
					</tr>
					<?php endif;?>
				</tbody>
			</table>
			
			<div class="well">
				<strong>If you use CjBlog, please post a rating and a review at the Joomla Extension Directory</strong>
				<div style="text-align: center; margin-top: 10px;">
					<a class="btn btn-primary" href="http://extensions.joomla.org/extensions/authoring-a-content/blog/22885" target="_blank">
						<i class="icon-share icon-white"></i> <span style="color: white">Post Your Review</span>
					</a>
				</div>
			</div>
			
			<div class="well">
				<div><strong>Credits: </strong></div>
				<div>CjBlog is a free software released under Gnu/GPL license.</div>
				<div>Copyright© 2009-12 corejoomla.com</div>
				<div>Core Components: Bootstrap, jQuery and ofcourse Joomla®.</div>
			</div>
		</div>
	</div>
</div>