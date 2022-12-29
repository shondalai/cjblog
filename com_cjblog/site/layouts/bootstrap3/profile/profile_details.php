<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$user    			= JFactory::getUser();
$item 		        = $displayData['item'];
$params  			= $item->params;
$avatarComponent  	= $params->get('avatar_component', 'cjblog');
$profileComponent 	= $params->get('profile_component', 'cjblog');
$points				= $params->get('points_component', 'cjblog');
$layout 			= $params->get('ui_layout', 'default');
$theme 				= $params->get('theme', 'default');
$displayName		= $params->get('display_name', 'name');

$api				= new CjLibApi();
$microdata 			= new JMicrodata('Person');
$profileUrl 		= $api->getUserProfileUrl($profileComponent, $item->id);
$profileUri 		= CjBlogHelperRoute::getProfileRoute($item->id);
$avatarImage 		= $api->getUserAvatarImage($avatarComponent, $item->id, $item->email, 192);
$authorName			= $this->escape($item->$displayName);
$age 				= JFactory::getDate($item->birthday)->diff(JFactory::getDate())->y;
?>
<div class="no-space-left no-space-right" <?php echo $microdata->displayScope();?>>
	<div class="row">
		<div class="col-md-12">
        	<div class="profile-header margin-bottom-10">
        		<h1 class="page-header no-space-top no-margin-bottom">
        		
        			<?php echo $microdata->content($authorName)->property('name')->fallback('Thing', 'name')->display();?>
        			
        			<?php if($user->authorise('core.manage', 'com_cjblog')):?>
        			<small>
        				&nbsp;
        				<a href="<?php echo JRoute::_('index.php?option=com_cjblog&task=profile.edit&p_id='.$item->id.'&return='.base64_encode($profileUri));?>">
        					(<?php echo JText::_('JGLOBAL_EDIT');?>)
        				</a>
        			</small>
        			<?php endif;?>
        		</h1>
        	</div>
    	</div>
    </div>
    <div class="row">
    	<div class="col-md-3">
    		<div class="profile-avatar">
    			<a <?php echo $microdata->property('url')->display();?> href="<?php echo $profileUrl;?>" class="thumbnail margin-bottom-5">
    				<img <?php echo $microdata->property('image')->display();?> src="<?php echo $avatarImage;?>" alt="<?php echo $authorName?>" class="media-object" style="width: 100%;;"/>
    			</a>
    		</div>
    		<div class="profile-icons inline center text-center margin-bottom-10">
    			<?php if(!empty($item->twitter)):?>
    			<a href="https://twitter.com/<?php echo $this->escape($item->twitter);?>" title="Twitter" data-toggle="tooltip" target="_blank">
    				<i class="fa fa-twitter fa-border"></i>
    			</a>
    			<?php endif;?>
    			<?php if(!empty($item->facebook)):?>
    			<a href="https://www.facebook.com/<?php echo $this->escape($item->facebook);?>" title="Facebook" data-toggle="tooltip" target="_blank">
    				<i class="fa fa-facebook fa-border"></i>
    			</a>
    			<?php endif;?>
    			<?php if(!empty($item->gplus)):?>
    			<a href="https://plus.google.com/<?php echo $this->escape($item->gplus);?>" title="Google+" data-toggle="tooltip" target="_blank">
    				<i class="fa fa-google-plus fa-border"></i>
    			</a>
    			<?php endif;?>
    			<?php if(!empty($item->linkedin)):?>
    			<a href="https://www.linkedin.com/profile/view?id=<?php echo $this->escape($item->linkedin);?>" title="Linkedin" data-toggle="tooltip" target="_blank">
    				<i class="fa fa-linkedin fa-border"></i>
    			</a>
    			<?php endif;?>
    			<?php if(!empty($item->flickr)):?>
    			<a href="https://www.flickr.com/photos/<?php echo $this->escape($item->flickr);?>" title="Flickr" data-toggle="tooltip" target="_blank">
    				<i class="fa fa-flickr fa-border"></i>
    			</a>
    			<?php endif;?>
    			<?php if(!empty($item->skype)):?>
    			<a href="skype:<?php echo $this->escape($item->skype);?>" title="Skype" data-toggle="tooltip">
    				<i class="fa fa-skype fa-border"></i>
    			</a>
    			<?php endif;?>
    		</div>
    	</div>
    	<div class="col-md-9">
    		<?php 
    		if($item->id == $user->id && $params->get('enable_gdpr'))
    		{
    		    ?>
        		<div class="row">
        			<div class="col-md-12">
            		    <div id="profile-actions">
                        	<a class="btn btn-primary btn-xs" 
                        		href="<?php echo JRoute::_('index.php?option=com_cjblog&task=profile.edit&p_id='.$item->id.'&return='.base64_encode($profileUri));?>">
                        		<i class="fa fa-pencil"></i> <?php echo JText::_('COM_CJBLOG_EDIT_PROFILE');?>
                        	</a>
                        	<a class="btn btn-success btn-xs" 
                        		href="<?php echo JRoute::_('index.php?option=com_cjblog&task=profile.download&p_id='.$item->id.'&return='.base64_encode($profileUri));?>">
                        		<i class="fa fa-download"></i> <?php echo JText::_('COM_CJBLOG_DOWNLOAD_MY_DATA');?>
                        	</a>
                        	<?php if($params->get('gdpr_delete_profile')):?>
                        	<a class="btn btn-danger btn-xs" href="#" onclick="return false;" data-toggle="modal" data-target="#confirm-delete-profile">
                        		<i class="fa fa-times"></i> <?php echo JText::_('COM_CJBLOG_PERMANENT_DELETE');?>
                        	</a>
                        	<?php endif;?>
                        </div>
                    	<?php if($params->get('gdpr_delete_profile')):?>
                        <div id="confirm-delete-profile" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteProfileLabel">
                        	<div class="modal-dialog" role="document">
                        		<div class="modal-content">
                        			<div class="modal-header">
                        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">�</button>
                        				<h4 id="confirmDeleteProfileLabel"><?php echo JText::_('COM_CJBLOG_CONFIRM_DELETE');?></h4>
                        			</div>
                                	<div class="modal-body">
                                		<form action="<?php JRoute::_('index.php')?>" method="post" id="delete_profile_data_form" name="delete_profile_data_form">
                                			<p><?php echo JText::_('COM_CJBLOG_CONFIRM_DELETE_USER_DATA_MESSAGE');?></p>
                                			<?php if($params->get('gdpr_delete_posts')):?>
                                			<div class="checkbox inline form-check">
                        						<input class="form-check-input" type="checkbox" name="delete_data" id="delete_data" value="1">
                        						<label class="form-check-label" for="delete_data"><?php echo JText::sprintf('COM_CJBLOG_DELETE_ALL_MY_DATA');?></label>
                                            </div>
                                            <?php endif;?>
                                			<input type="hidden" name="option" value="com_cjblog" />
                                			<input type="hidden" name="task" value="profile.delete" />
                                			<input type="hidden" name="p_id" value="<?php echo $item->id;?>"/>
                                			<input type="hidden" name="return" value="<?php echo base64_encode($profileUri);?>">
                                		</form>
                                	</div>
                                	<div class="modal-footer">
                                		<button type="button" data-dismiss="modal" class="btn"><?php echo JText::_('JCANCEL')?></button>
                                		<button type="button" class="btn btn-primary" onclick="document.delete_profile_data_form.submit();return false;">
                                			<?php echo JText::_('JACTION_DELETE')?>
                                		</button>
                                	</div>
                                </div>
							</div>
                        </div>
                        <?php endif;?>
        			</div>
        		</div>
        		<?php 
    		}
    		?>
    		<div class="row">
    			<div class="col-md-5">
            		<table class="table table-striped table-hover table-condensed">
            			<tbody>
    						<?php if(!empty($item->website) && $params->get('profile_show_website', 1) == 1):?>
    						<tr>
    							<th><?php echo JText::_('COM_CJBLOG_PROFILE_FIELD_WEBSITE');?></th>
    							<td><?php echo $this->escape($item->website);?></td>
    						</tr>
    						<?php endif;?>
    						
    						<?php if(!empty($item->location) && $params->get('profile_show_location', 1) == 1):?>
    						<tr>
    							<th><?php echo JText::_('COM_CJBLOG_PROFILE_FIELD_LOCATION');?></th>
    							<td><?php echo $microdata->content($this->escape($item->location))->property('homeLocation')->display();?></td>
    						</tr>
    						<?php endif;?>
    						
    						<?php if($params->get('profile_show_gender', 1) == 1):?>
    						<tr>
    							<th><?php echo JText::_('COM_CJBLOG_PROFILE_FIELD_GENDER');?></th>
    							<td><?php echo $microdata->content(JText::sprintf('COM_CJBLOG_PROFILE_GENDER_'.((int)$item->gender)))->property('gender')->display();?></td>
    						</tr>
    						<?php endif;?>
    						
    						<?php if($params->get('profile_show_age', 1) == 1):?>
    						<tr>
    							<th><?php echo JText::_('COM_CJBLOG_PROFILE_FIELD_AGE');?></th>
    							<td><?php echo ($age > 150 || $age < 2) ? JText::_('COM_CJBLOG_NOT_SPECIFIED') : JText::sprintf('COM_CJBLOG_PROFILE_AGE', $age);?></td>
    						</tr>
    						<?php endif;?>
    						
    						<?php if($params->get('profile_show_member_since', 1) == 1):?>
    						<tr>
    							<th><?php echo JText::_('COM_CJBLOG_MEMBER_SINCE');?></th>
    							<td><?php echo JFactory::getDate($item->registerDate)->format('F jS, Y');?></td>
    						</tr>
    						<?php endif;?>
    						
    						<?php if($params->get('profile_show_last_visit', 1) == 1):?>
    						<tr>
    							<th><?php echo JText::_('COM_CJBLOG_LAST_SEEN');?></th>
    							<td><?php echo CjLibDateUtils::getHumanReadableDate($item->lastvisitDate);?></td>
    						</tr>
    						<?php endif;?>
    						
    						<tr class="info">
    							<th><?php echo JText::_('COM_CJBLOG_ARTICLES');?></th>
    							<td><?php echo CjLibUtils::formatNumber($item->num_articles);?></td>
    						</tr>
    						<?php if($points != 'none'):?>
    						<tr class="success">
    							<th><?php echo JText::_('COM_CJBLOG_POINTS');?></th>
    							<td>
    								<span data-toggle="tooltip" title="<?php echo $item->points;?>">
    									<?php echo $microdata->content(CjLibUtils::formatNumber($item->points))->property('award')->display();?>
    								</span>
    							</td>
    						</tr>
    						<?php endif;?>
            			</tbody>
            		</table>
            	</div>
            	<div class="col-md-7">
            		<div class="panel panel-<?php echo $theme?>" style="max-height: 250px; overflow-y: auto;">
            			<div class="panel-body">
            				<?php 
            				if(!empty($item->about))
            				{
            					echo $item->about;
            				}
            				else 
            				{
            					echo JText::sprintf('COM_CJBLOG_MESSAGE_NO_INFORMATION_PROVIDED', $this->escape($authorName));
            				}
            				?>
            			</div>
            		</div>
            	</div>
        	</div>
    	</div>
    </div>
    <?php if(count($item->fields)):?>
	<div class="row">
		<div class="col-md-12">
    		<div class="panel panel-<?php echo $layout;?> custom-profile-fields">
    			<div class="panel-body">
    				<dl class="dl-horizontal">
    					<?php foreach ($item->fields as $label=>$value):?>
    					<dt><?php echo JText::_('PLG_CJBLOG_'.$label.'_LABEL', true);?></dt>
    					<dd><?php echo $this->escape($value);?></dd>
    					<?php endforeach;?>
    				</dl>
    			</div>
    		</div>
        </div>
	</div>
	<?php endif;?>
</div>