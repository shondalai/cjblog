<?php
/**
 * @version		$Id: points.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.controllers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerBadgeactivity extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_recent_activity');
	}
	
	function get_recent_activity(){
		
		$view = $this->getView('badgeactivity', 'html');
		$model = $this->getModel('badges');
		$user_model = $this->getModel('users');
		
		$view->setModel($model, true);
		$view->setModel($user_model, false);
		$view->assign('action', 'default');
		$view->display();
	}
	
	function remove(){
	
		$model = $this->getModel('badges');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		if(!empty($ids) && $model->delete_activity($ids)){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity', false), JText::_('COM_CJBLOG_REMOVE_SUCCESS'));
		} else {
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity', false), JText::_('COM_CJBLOG_REMOVE_FAILED'));
		}
	}
	
	public function add()
	{
		$view = $this->getView('badgeactivity', 'html');
		$model = $this->getModel('badges');
		$user_model = $this->getModel('users');
		
		$view->setModel($model, true);
		$view->setModel($user_model, false);
		$view->assign('action', 'edit');
		$view->display();
	}
	
	public function save()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('badges');
		$ruleId = $app->input->post->getInt('ruleId', 0);
		$userId = $app->input->post->getInt('userId', 0);
		
		if($ruleId && $userId)
		{
			$return = $model->get_badge_rules();
			$badgeRules = !empty($return->rules) ? $return->rules : array();
			
			if(!empty($badgeRules))
			{
				$selectedRule = null;
				
				foreach ($badgeRules as $badgeRule)
				{
					if($badgeRule->id == $ruleId)
					{
						$selectedRule = $badgeRule;
						break;
					}
				}
				
				if($selectedRule && $selectedRule->rule_content)
				{
					$ruleContent = json_decode($selectedRule->rule_content);
					if(!empty($ruleContent->rules))
					{
						$params = array();
						
						foreach ($ruleContent->rules as $rule)
						{
							$params[$rule->name] = $app->input->post->get($rule->name, null, $rule->dataType);
						}
						
						CjBlogApi::trigger_badge_rule($selectedRule->rule_name, $params, $userId);
						$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity', false), JText::_('COM_CJBLOG_SUCCESS'));
						
						return;
					}
				}
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity&task=add', false), JText::_('COM_CJBLOG_ERROR_PROCESSING'));
	}
	
	function cancel(){
	
		$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity', false), JText::_('COM_CJBLOG_OPERATION_CANCELLED'));
	}
}