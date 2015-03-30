<?php
/**
 * @version		$Id: view.html.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.view' );

class CjBlogViewUser extends JViewLegacy {

	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$active	= $app->getMenu()->getActive();
		$model = $this->getModel();
		$cache = JFactory::getCache();
		$user = JFactory::getUser();
		
		/********************************** PARAMS *****************************/
		$appparams = JComponentHelper::getParams(CJBLOG);
		$menuParams = new JRegistry;
		$page_heading = '';
		
		if ($active) {
		
			$menuParams->loadString($active->params);
		}
		
		$this->params = clone $menuParams;
		$this->params->merge($appparams);
		/********************************** PARAMS *****************************/
		
		$result = new stdClass();
		
		switch ($this->action){
			
			case 'user_points':
				
				$id = $app->input->getInt('id', 0);
				
				if(!$id){
					
					$id = $user->id;
				}
				
				$order = $app->getUserStateFromRequest( "cjbloguserpoints.order", 'order', 'a.created', 'cmd' );
				$order_Dir   = $app->getUserStateFromRequest( "cjbloguserpoints.order_Dir",'order_Dir','DESC','word' );
				
				$profile = $cache->call(array('CjBlogApi', 'get_user_profile'), $id);
				$result = $model->get_user_point_details($id, array('order'=>$order, 'order_dir'=>$order_Dir));
				$points = !empty($result->points) ? $result->points : array();
				
				$this->assignRef('profile', $profile);
				$this->assignRef('points', $points);
				$this->assignRef('pagination', $result->pagination);
				$this->assignRef('state', $result->state);
				
				$page_heading = JText::sprintf('COM_CJBLOG_MY_POINTS_DETAILS', $profile['points']);
				
				break;
				
			case 'user_articles':

				$id = $app->input->getInt('id', 0);
				$my = null;
				
				if(!$id){

					$id = $user->id;
					$my = $user;
				} else {
					
					$my = JFactory::getUser($id);
				}

				$limit = $app->getCfg('list_limit', 20);
				$limitstart = $app->input->getInt('start', 0);
				$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
				$username = $this->params->get('user_display_name', 'name');
				
				$articles = $model->get_articles(array(
						'published'=>1,
						'pagination'=>true,
						'user_id'=>$my->id,
						'limitstart'=>$limitstart,
						'limit'=>$limit,
						'order'=>'a.created',
						'order_dir'=>'desc'));
				

				$page_heading = JText::sprintf('TXT_USER_ARTICLES', $my->$username, array('jsSafe'=>true));
				$page_url = 'index.php?option='.CJBLOG.'&view=user&task=articles&id='.$my->id.':'.$my->username;
				
				$this->assignRef('articles', $articles->articles);
				$this->assignRef('pagination', $articles->pagination);
				$this->assignRef('page_url', $page_url);
				
				$tpl = 'articles';
				
				break;
				
			default:
				break;
		}
		
		$this->params->set('page_heading', $this->params->get('page_heading', $page_heading));
		$title = $this->params->get('page_title', '');
		
		if (empty($title)) {
				
			$title = $page_heading;
		}
		
		JFactory::getDocument()->setTitle(CjBlogHelper::get_page_title($title));
		
		parent::display ( $tpl );
	}
}