<?php
/**
 * @package     corejoomla.site
 * @subpackage  com_cjforum
 *
 * @copyright   Copyright (C) 2009 - 2014 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

$data = $displayData['data'];
$sitename = JFactory::getConfig()->get('sitename');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $sitename . ' - ' . $data->profile->name?></title>

    <!-- Bootstrap core CSS -->
    <link href="media/css/bootstrap.v4.min.css" rel="stylesheet">
    <link href="media/css/font-awesome.min.css" rel="stylesheet">
    <style type="text/css">
        body {font-size: .875rem;}
        .feather {width: 16px;height: 16px;vertical-align: text-bottom;}
        .sidebar {position: fixed;top: 0;bottom: 0;left: 0;z-index: 100;padding: 48px 0 0;box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);}
        .sidebar-sticky {position: relative;top: 0;height: calc(100vh - 48px);padding-top: .5rem;overflow-x: hidden;overflow-y: auto;}
        @supports ((position: -webkit-sticky) or (position: sticky)) {.sidebar-sticky {position: -webkit-sticky;position: sticky;}}
        .sidebar .nav-link {font-weight: 500;color: #333;}
        .sidebar .nav-link .feather {margin-right: 4px;color: #999;}
        .sidebar .nav-link.active {color: #007bff;}
        .sidebar .nav-link:hover .feather,.sidebar .nav-link.active .feather {color: inherit;}
        .sidebar-heading {font-size: .75rem;text-transform: uppercase;}
        [role="main"] {padding-top: 48px;}
        .navbar-brand {padding-top: .75rem;padding-bottom: .75rem;font-size: 1rem;background-color: rgba(0, 0, 0, .25);box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);}
        .navbar .form-control {padding: .75rem 1rem;border-width: 0;border-radius: 0;}
        .form-control-dark {color: #fff;background-color: rgba(255, 255, 255, .1);border-color: rgba(255, 255, 255, .1);}
        .form-control-dark:focus {border-color: transparent;box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);}
        .border-top { border-top: 1px solid #e5e5e5; }
        .border-bottom { border-bottom: 1px solid #e5e5e5; }
    </style>
  </head>

  <body>
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#"><?php echo $sitename?></a>
    </nav>

    <div class="container-fluid tabs">
      <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
          <div class="sidebar-sticky">
            <ul class="nav flex-column">
              <li class="nav-item"><a data-toggle="tab" class="nav-link active" href="#tab-home"><span data-feather="home"></span> <?php echo JText::_('COM_CJBLOG_LABEL_MY_PROFILE')?></a></li>
              <li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab-articles"><span data-feather="file"></span> <?php echo JText::_('COM_CJBLOG_LABEL_MY_ARTICLES')?></a></li>
              <li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab-badges"><span data-feather="file"></span> <?php echo JText::_('COM_CJBLOG_BADGES')?></a></li>
              <li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab-points"><span data-feather="file"></span> <?php echo JText::_('COM_CJBLOG_POINTS')?></a></li>
            </ul>
          </div>
        </nav>

        <main role="main" class="col-md-10 ml-sm-auto col-lg-10 px-4">
        	<div class="tab-content">
            	<div id="tab-home" class="tab-pane fade show active">
            		<h2 class="mb-3"><?php echo JText::_('COM_CJBLOG_LABEL_MY_PROFILE')?></h2>
            		<div class="container-fluid">
            			<div class="row">
            				<?php if(file_exists(CJBLOG_AVATAR_BASE_DIR . 'size-256/' . $data->profile->avatar)):?>
            				<div class="col-2">
            					<img alt="<?php echo $data->profile->handle;?>" src="media/images/<?php echo $data->profile->avatar;?>" class="img-thumbnail">
                        	</div>
                        	<?php endif;?>
                        	
            				<div class="col">
            					<?php if(!empty($data->profile->signature)):?>
            					<div class="card mb-3">
            						<div class="card-body">
            							<?php echo $data->profile->signature;?>
            						</div>
            					</div>
            					<?php endif;?>
                        		<table class="table table-hover table-striped table-sm">
                        			<tbody>
                        				<tr>
                        					<th><?php echo JText::_('JGLOBAL_USERNAME')?></th>
                        					<td><?php echo $data->profile->handle;?></td>
                        				</tr>
                        				<tr>
                        					<th><?php echo JText::_('JGLOBAL_EMAIL')?></th>
                        					<td><?php echo $data->profile->email;?></td>
                        				</tr>
                        				<tr>
                        					<th><?php echo JText::_('COM_CJBLOG_PROFILE_FIELD_GENDER')?></th>
                        					<td><?php echo $data->profile->gender;?></td>
                        				</tr>
                        				<tr>
                        					<th><?php echo JText::_('COM_CJBLOG_PROFILE_FIELD_AGE')?></th>
                        					<td><?php echo $data->profile->birthday;?></td>
                        				</tr>
                        				<tr>
                        					<th><?php echo JText::_('COM_CJBLOG_PROFILE_FIELD_LOCATION')?></th>
                        					<td><?php echo $data->profile->location;?></td>
                        				</tr>
                        				<tr>
                        					<th><?php echo JText::_('COM_CJBLOG_POINTS')?></th>
                        					<td><?php echo $data->profile->points;?></td>
                        				</tr>
                        				<tr>
                        					<th><?php echo JText::_('COM_CJBLOG_BADGES')?></th>
                        					<td><?php echo $data->profile->num_badges;?></td>
                        				</tr>
                        				<tr>
                        					<th><?php echo JText::_('COM_CJBLOG_LABEL_ARTICLES')?></th>
                        					<td><?php echo $data->profile->num_articles;?></td>
                        				</tr>
                        				
                        				<?php if(!empty($data->profile->twitter)):?>
                        				<tr>
                        					<th>Twitter</th>
                        					<td><?php echo $data->profile->twitter;?></td>
                        				</tr>
                        				<?php endif;?>
                        				
                        				<?php if(!empty($data->profile->facebook)):?>
                        				<tr>
                        					<th>Facebook</th>
                        					<td><?php echo $data->profile->facebook;?></td>
                        				</tr>
                        				<?php endif;?>
                        				
                        				<?php if(!empty($data->profile->gplus)):?>
                        				<tr>
                        					<th>Google+</th>
                        					<td><?php echo $data->profile->gplus;?></td>
                        				</tr>
                        				<?php endif;?>
                        				
                        				<?php if(!empty($data->profile->linkedin)):?>
                        				<tr>
                        					<th>Linkedin</th>
                        					<td><?php echo $data->profile->linkedin;?></td>
                        				</tr>
                        				<?php endif;?>
                        				
                        				<?php if(!empty($data->profile->flickr)):?>
                        				<tr>
                        					<th>Flickr</th>
                        					<td><?php echo $data->profile->flickr;?></td>
                        				</tr>
                        				<?php endif;?>
                        				
                        				<?php if(!empty($data->profile->bebo)):?>
                        				<tr>
                        					<th>Bebo</th>
                        					<td><?php echo $data->profile->bebo;?></td>
                        				</tr>
                        				<?php endif;?>
                        				
                        				<?php if(!empty($data->profile->skype)):?>
                        				<tr>
                        					<th>Skype</th>
                        					<td><?php echo $data->profile->skype;?></td>
                        				</tr>
                        				<?php endif;?>
                        			</tbody>
                        		</table>
                        	</div>
                        </div>
					</div>
            	</div>
            	<div id="tab-articles" class="tab-pane fade">
            		<h2 class="page-header"><?php echo JText::_('COM_CJBLOG_LABEL_MY_ARTICLES')?></h2>
            		<?php if(!empty($data->articles)):?>
            			<?php foreach ($data->articles as $article):?>
            			<div class="card mb-3">
            				<div class="card-body">
                				<h5 class="card-title"><?php echo $this->escape($article->title);?></h5>
                				<h6 class="card-subtitle mb-2 text-muted"><?php echo $article->created;?></h6>
                				<div class="card-text"><?php echo $article->introtext;?></div>
            				</div>
            			</div>
            			<?php endforeach;?>
            		<?php else:?>
        				<?php echo JText::_('JGLOBAL_SELECT_NO_RESULTS_MATCH')?>
            		<?php endif;?>
            	</div>
            	<div id="tab-badges" class="tab-pane fade">
            		<h2 class="page-header"><?php echo JText::_('COM_CJBLOG_BADGES')?></h2>
            		<?php if(!empty($data->badges)):?>
            			<?php foreach ($data->badges as $badge):?>
            			<div class="badge <?php echo $badge['css_class'];?>">
            				<i class="fa fa-bookmark"></i> <?php echo $this->escape($badge['title']);?> x <?php echo $badge['num_times']?>
            			</div>
            			<?php endforeach;?>
            		<?php else:?>
        				<?php echo JText::_('JGLOBAL_SELECT_NO_RESULTS_MATCH')?>
            		<?php endif;?>
            	</div>
            	<div id="tab-points" class="tab-pane fade">
            		<h2 class="page-header"><?php echo JText::_('COM_CJBLOG_POINTS')?></h2>
            		<?php if(!empty($data->points)):?>
            			<table class="table table-striped table-condensed table-hover">
            				<thead>
            					<tr>
            						<th><?php echo JText::_('JGLOBAL_TITLE')?></th>
            						<th><?php echo JText::_('JGLOBAL_DESCRIPTION');?></th>
            						<th width="15%"><?php echo JText::_('JDATE');?></th>
            						<th width="5%"><?php echo JText::_('COM_CJBLOG_POINTS');?></th>
            					</tr>
            				</thead>
            				<tbody>
                				<?php foreach ($data->points as $point):?>
                				<tr>
                					<td><?php echo $this->escape($point->title);?></td>
                					<td><?php echo strip_tags($point->description);?></td>
                					<td><?php echo $point->created;?></td>
                					<td><?php echo $point->points?></td>
                				</tr>
                				<?php endforeach;?>
                			</tbody>
            			</table>
            		<?php else:?>
        				<?php echo JText::_('JGLOBAL_SELECT_NO_RESULTS_MATCH')?>
            		<?php endif;?>
            	</div>
            </div>
        </main>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="media/js/jquery-3.3.1.slim.min.js"></script>
    <script>window.jQuery || document.write('<script src="media/js/jquery-3.3.1.slim.min.js"><\/script>')</script>
    <script src="media/js/bootstrap.bundle.v4.min.js"></script>
  </body>
</html>