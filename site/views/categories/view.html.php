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

class CjBlogViewCategories extends JViewLegacy {
	
	protected $params;

	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$document = JFactory::getDocument();
		$active = $app->getMenu()->getActive();
		
		$parent_id = $app->input->getInt('id', 0);
		/********************************** PARAMS *****************************/
		$appparams = JComponentHelper::getParams(CJBLOG);
		$menuParams = new JRegistry;
		$page_heading = JText::_('LBL_CATEGORIES');
		
		if ($active) {
		
			$menuParams->loadString($active->params);
		}
		
		$this->params = clone $menuParams;
		$this->params->merge($appparams);
		/********************************** PARAMS *****************************/
		
		$parent_id = $parent_id == 0 ? 'root' : $parent_id;
		$categories = $model->get_categories($parent_id, false);
// 		$categories = $cache->call(array($model, 'get_categories'), $parent_id == 0 ? 'root' : $parent_id, false);
		$this->assignRef('categories', $categories);
		$this->assign('brand', $app->getCfg('sitename'));

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