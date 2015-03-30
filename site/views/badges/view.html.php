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

class CjBlogViewBadges extends JViewLegacy {

	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$active	= $app->getMenu()->getActive();
		$model = $this->getModel();
		$articles_model = $this->getModel('badges');
		$page_heading = '';
		
		/********************************** PARAMS *****************************/
		$appparams = JComponentHelper::getParams(CJBLOG);
		$menuParams = new JRegistry;
		
		if ($active) {
		
			$menuParams->loadString($active->params);
		}
		
		$this->params = clone $menuParams;
		$this->params->merge($appparams);
		/********************************** PARAMS *****************************/
		
		switch ($this->action){
			
			case 'badges_home':
				
				$badges = $model->get_badges();
				$this->assignRef('badgegroups', $badges);
				$page_heading = JText::_('LBL_BADGES');
				
				break;
			
			case 'user_badges':
				
				$id = $app->input->getInt('id', null);
				$my = JFactory::getUser($id);
				
				$badges = $model->get_user_badges($my->id);
				$this->assignRef('badgegroups', $badges);
				$page_heading = JText::sprintf('TXT_BADGES_OWNED_BY', $my->name, array('jsSafe'=>true));
				$document->setMetaData('robots','noindex,follow');
				
				break;
		}
		
		$this->params->set('page_heading', $this->params->get('page_heading', $page_heading));
		$title = $this->params->get('page_title', '');
		
		if (empty($title)) {
				
			$title = $page_heading;
		}
		
		$document->setTitle(CjBlogHelper::get_page_title($title));

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