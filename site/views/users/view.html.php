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

class CjBlogViewUsers extends JViewLegacy {

	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$active	= $app->getMenu()->getActive();
		$model = $this->getModel();
		$cache = JFactory::getCache();
		$document = JFactory::getDocument();
		
		/********************************** PARAMS *****************************/
		$appparams = JComponentHelper::getParams(CJBLOG);
		$menuParams = new JRegistry;
		
		if ($active) {
		
			$menuParams->loadString($active->params);
		}
		
		$this->params = clone $menuParams;
		$this->params->merge($appparams);
		/********************************** PARAMS *****************************/
		
		$id = $app->input->getInt('id', 0);
		$result = new stdClass();
		$page_heading = '';
		$page_url = 'index.php?option='.CJBLOG.'&view=users';
		
		switch ($this->action){
			
			case 'top_bloggers':
				
				$result = $model->get_users(2);
				$page_heading = JText::_('LBL_TOP_BLOGGERS');
				$page_url = $page_url.'&task=top';
				
				break;

			case 'search':

				$query = $app->input->getString('search', '');
				$result = $model->get_users(4, array('query'=>$query));
				$page_heading = JText::_('LBL_SEARCH');
			
				break;
				
			case 'badge_owners':
				
				$result = $model->get_users(3, array('badge_id'=>$id));
					
				$badge = CjBlogApi::get_badge_details($id);
				$page_heading = JText::sprintf('TXT_USERS_WHO_WON_BADGE', $badge['title'], array('jsSafe'=>true));
				$page_url = $page_url.'&task=badge&id='.$id.':'.$badge['alias'];
				
				break;
			
			case 'new_bloggers':
			default:
				
				$result = $model->get_users(1);
				$page_heading = JText::_('LBL_NEW_BLOGGERS');
				break;
		}
		
		if(!empty($result->users)){
			
			$userIds = array_keys($result->users);

			if(!empty($userIds))
			{
				$api = new CjLibApi();
				$avatarApp = $this->params->get('avatar_component', 'cjforum');
				$profileApp = $this->params->get('profile_component', 'cjforum');
				
				$api->prefetchUserProfiles($avatarApp, $userIds);
				
				if($avatarApp != $profileApp)
				{
					$api->prefetchUserProfiles($profileApp, $userIds);
				}
			}
		}

		$this->params->set('page_heading', $this->params->get('page_heading', $page_heading));
		$title = $this->params->get('page_title', '');
		
		if (empty($title)) {
		
			$title = $page_heading;
		}
		
		$document->setTitle(CjBlogHelper::get_page_title($title));
		
		$this->assign('brand', $app->getCfg('sitename'));
		
		if(!empty($result)){
			
			$this->assignRef('users', $result->users);
			$this->assignRef('pagination', $result->pagination);
			$this->assignRef('state', $result->state);
		}
		
		$this->assignRef('page_url', $page_url);

		if ($this->params->get('menu-meta_description'))
		{
			$document->setDescription($this->params->get('menu-meta_description'));
		}
		
		if ($this->params->get('menu-meta_keywords'))
		{
			$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		
		if ($this->params->get('robots'))
		{
			$document->setMetadata('robots', $this->params->get('robots'));
		}
		
		parent::display ( $tpl );
	}
}