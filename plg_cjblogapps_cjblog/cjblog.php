<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT.'/components/com_cjlib/framework/api.php';
require_once JPATH_ROOT.'/components/com_cjblog/router.php';
require_once JPATH_ROOT.'/components/com_cjblog/helpers/route.php';

class PlgCjBlogAppsCjBlog extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->loadLanguage('com_cjblog', JPATH_ROOT);
	}
	
	public function onProfileDisplay($context, $profile, $params, &$apps)
	{
		if($context != 'com_cjblog.profile')
		{
			return true;
		}
		
		$summary			= new stdClass();
		$summary->id 		= 'summary';
		$summary->title 	= JText::_('COM_CJBLOG_SUMMARY');
		$summary->icon 		= 'fa fa-home';
		$apps->tabs[]		= $summary;
		
		$reputation			= new stdClass();
		$reputation->id 	= 'reputation';
		$reputation->title 	= JText::_('COM_CJBLOG_REPUTATION');
		$reputation->icon 	= 'fa fa-trophy';
		$apps->tabs[]		= $reputation;

		$badges				= new stdClass();
		$badges->id 		= 'badges';
		$badges->title 		= JText::_('COM_CJBLOG_BADGES');
		$badges->icon 		= 'fa fa-certificate';
		$apps->tabs[]		= $badges;

		$articles			= new stdClass();
		$articles->id 		= 'articles';
		$articles->title 	= JText::_('COM_CJBLOG_ARTICLES');
		$articles->icon 	= 'fa fa-file-text';
		$apps->tabs[]		= $articles;

		if(!in_array($apps->id, array('summary', 'reputation', 'badges', 'articles')))
		{
			return true;
		}
		
		if($apps->id == 'summary')
		{
			$this->loadSummary($profile, $params, $apps);
		}
		
		if($apps->id == 'reputation')
		{
			$this->loadReputation($profile, $params, $apps);
		}
		
		if($apps->id == 'badges')
		{
			$this->loadBadges($profile, $params, $apps);
		}

		if($apps->id == 'articles')
		{
			$this->loadArticles($profile, $params, $apps);
		}
		
		return true;
	}
	
	private function loadSummary($profile, $params, &$apps)
	{
		// load current tab content
		JLoader::import('profile', JPATH_ROOT.'/components/com_cjblog/models');
		$model 				= JModelLegacy::getInstance( 'profile', 'CjBlogModel' );
		$layout 			= $params->get('ui_layout', 'default');
		
		$state 				= $model->getState(); // access the state first so that it can be modified
		$data 				= new stdClass();
		$data->summary 		= $model->getSummary();
		$data->params		= $params;
		
		$content 			=  JLayoutHelper::render($layout.'.profile.summary', array('data'=>$data), null, array('debug' => false, 'client' => 0, 'component' => 'com_cjblog'));
		
		$apps->content 		= $content;
	}
	
	private function loadReputation($profile, $params, &$apps)
	{
		// load current tab content
		JLoader::import('profile', JPATH_ROOT.'/components/com_cjblog/models');
		$model 				= JModelLegacy::getInstance( 'profile', 'CjBlogModel' );
		$layout 			= $params->get('ui_layout', 'default');
		
		$state 				= $model->getState(); // access the state first so that it can be modified
		$data 				= new stdClass();
		
		$return				= $model->getReputation();
		$return->item		= $profile;
		$return->params		= $params;
	
		$content =  JLayoutHelper::render($layout.'.profile.reputation', array('data'=>$return), null, array('debug' => false, 'client' => 0, 'component' => 'com_cjblog'));
	
		$apps->content = $content;
	}
	
	private function loadBadges($profile, $params, &$apps)
	{
		// load current tab content
		JLoader::import('profile', JPATH_ROOT.'/components/com_cjblog/models');
		$model 				= JModelLegacy::getInstance( 'profile', 'CjBlogModel' );
		$layout 			= $params->get('ui_layout', 'default');
	
		$state 				= $model->getState(); // access the state first so that it can be modified
		$return				= new stdClass();
		$return->items		= $model->getBadges();
		$return->item		= $profile;
		$return->params		= $params;
		
		$content = JLayoutHelper::render($layout.'.profile.badges', array('data'=>$return), null, array('debug' => false, 'client' => 0, 'component' => 'com_cjblog'));
	
		$apps->content = $content;
	}
	
	private function loadArticles($profile, $params, &$apps)
	{
		// load current tab content
		JLoader::import('profile', JPATH_ROOT.'/components/com_cjblog/models');
		$model 				= JModelLegacy::getInstance( 'profile', 'CjBlogModel' );
		$layout 			= $params->get('ui_layout', 'default');
	
		$state 				= $model->getState(); // access the state first so that it can be modified
		$return				= $model->getArticles();
		$return->item		= $profile;
		$return->params		= $params;
		
		$content = JLayoutHelper::render($layout.'.articles_list', array('data'=>$return), null, array('debug' => false, 'client' => 0, 'component' => 'com_cjblog'));
	
		$apps->content = $content;
	}
}