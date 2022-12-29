<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogController extends JControllerLegacy
{

	public function __construct ($config = array())
	{
		$this->input = JFactory::getApplication()->input;
		
		// Article frontpage Editor pagebreak proxying:
		if ($this->input->get('view') === 'article' && $this->input->get('layout') === 'pagebreak')
		{
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
		// Article frontpage Editor article proxying:
		elseif ($this->input->get('view') === 'articles' && $this->input->get('layout') === 'modal')
		{
			JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
		
		parent::__construct($config);
	}

	public function display ($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$user = JFactory::getUser();
		
		$cachable = true;
		$custom_tag = true;
		
		// Set the default view name and format from the Request.
		// Note we are using t_id to avoid collisions with the router and the
		// return page.
		// Frontend is a bit messier than the backend.
		$id = $this->input->getInt('t_id');
		$replyId = $this->input->getInt('r_id');
		$vName = $this->input->getCmd('view', 'categories');
		$this->input->set('view', $vName);
		
		if ( $user->get('id') || in_array($vName, array('profile')))
		{
			$cachable = false;
		}
		
		$safeurlparams = array(
				'catid' => 'INT',
				'id' => 'INT',
				'article_id' => 'INT',
				'a_id' => 'INT',
				'cid' => 'ARRAY',
				'year' => 'INT',
				'month' => 'INT',
				'limit' => 'UINT',
				'limitstart' => 'UINT',
				'showall' => 'INT',
				'return' => 'BASE64',
				'filter' => 'STRING',
				'filter_order' => 'CMD',
				'filter_order_Dir' => 'CMD',
				'filter_search' => 'STRING',
				'print' => 'BOOLEAN',
				'lang' => 'CMD',
				'Itemid' => 'INT'
		);
		
		// Check for edit form.
		if ($vName == 'form' && ! $this->checkEditId('com_cjblog.edit.article', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
		    throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 403);
		}

		$params = JComponentHelper::getParams('com_cjblog');
		$loadBsCss = $params->get('load_bootstrap_css', false);

		if(CJBLOG_MAJOR_VERSION < 4) {
			CjLib::behavior('bootstrap', array('loadcss' => $loadBsCss));
			CjScript::_('fontawesome', array('custom'=>$custom_tag));
			JHtml::_('script', 'system/core.js', false, true);

			if($params->get('ui_layout', 'default') == 'default')
			{
				CJLib::behavior('bscore', array('customtag'=>$custom_tag));
			}
			if($vName == 'form')
			{
				JHtml::_('behavior.framework');
			}
		} else {
			$wa = $document->getWebAssetManager();
			$wa
				->useScript('jquery')
				->useScript('bootstrap.tab')
				->useScript('bootstrap.dropdown')
				->useStyle('fontawesome');
		}
		
		if ($vName == 'profileform')
		{
		    CJFunctions::add_script(JUri::root(true).'/media/system/js/tabs-state.js', $custom_tag);
		    CJFunctions::add_script(JUri::root(true).'/media/system/js/validate.js', $custom_tag);
			CJFunctions::add_script(JUri::root(true).'/media/com_cjblog/js/jquery.guillotine.js', $custom_tag);
		}
		
		CJFunctions::add_css_to_document($document, JUri::root(true).'/media/com_cjblog/css/cj.blog.min.css', $custom_tag);
		CJFunctions::add_script(JUri::root(true).'/media/com_cjblog/js/cj.blog.min.js', $custom_tag);

		parent::display($cachable, $safeurlparams);
		return $this;
	}
}
