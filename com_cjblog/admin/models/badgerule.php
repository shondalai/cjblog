<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JLoader::register('CjBlogHelper', JPATH_ADMINISTRATOR . '/components/com_cjblog/helpers/cjblog.php');

class CjBlogModelBadgerule extends JModelAdmin
{
	protected $text_prefix = 'COM_CJBLOG';

	public $typeAlias = 'com_cjblog.badgerule';
	
	protected $_item = null;
	
	protected function canDelete ($record)
	{
		if (! empty($record->id))
		{
			if ($record->published != - 2)
			{
				return;
			}
			$user = JFactory::getUser();
			return $user->authorise('core.delete', 'com_cjblog');
		}
	}

	public function getTable ($type = 'Badgerules', $prefix = 'CjBlogTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm ($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_cjblog.badgerule', 'badgerule', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		$jinput = JFactory::getApplication()->input;
		
		// The front end calls this model and uses p_id to avoid id clashes so
		// we need to check for that first.
		if ($jinput->get('p_id'))
		{
			$id = $jinput->get('p_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it
		// to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}
		// Determine correct permissions to check.
		if ($this->getState('badgerule.id'))
		{
			$id = $this->getState('badgerule.id');
		}
		
		$user = JFactory::getUser();
		
		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if (! $user->authorise('core.edit.state', 'com_cjblog'))
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}
		
		return $form;
	}
	
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		
		if($item->id)
		{
			$item->badgeRule = json_decode($item->rule_content);
		}
		else 
		{
			$app 				= JFactory::getApplication();
			$input 				= JFilterInput::getInstance();
			$appName 			= $app->input->getCmd('app');
			$ruleName 			= $app->input->getCmd('asset');
			$ruleFile 			= JPATH_ADMINISTRATOR.'/components/'.$appName.'/cjblog_rules.xml';
			
			if(! file_exists($ruleFile))
			{
				$ruleFile = JPATH_ROOT.'/components/'.$appName.'/cjblog_rules.xml';
				if(! file_exists($ruleFile))
				{
					$ruleFile = JPATH_ADMINISTRATOR.'/components/com_cjblog/cjblog_rules.xml';
				}
			}
			
			$badgeRules = simplexml_load_file($ruleFile);
			if(empty($badgeRules) || empty($badgeRules->badge_rule))
			{
				// chances are that the rules are available in cjblog rules xml itself.
				$ruleFile = JPATH_ADMINISTRATOR.'/components/com_cjblog/cjblog_rules.xml';
				$badgeRules = simplexml_load_file($ruleFile);
				if(empty($badgeRules) || empty($badgeRules->badge_rule))
				{
					throw new Exception(JText::_('JERROR_ALERTNOAUTHOR').'| RC=2');
				}
			}
			
			foreach ($badgeRules->badge_rule as $badgeRule)
			{
				$item->rule_name 			= $input->clean($badgeRule->rule_name, 'string');
				$item->asset_name			= $input->clean($badgeRule->appname, 'string');
				$item->asset_title			= $input->clean($badgeRule->apptitle, 'string');
				$item->description 			= $input->clean($badgeRule->rule_description, 'string');
				
				if($item->asset_name != $appName || $item->rule_name != $ruleName)
				{
					continue;
				}
				
				$item->badgeRule			= new stdClass();
				$item->badgeRule->join		= $input->clean($badgeRule->rules['join'], 'string');
				$item->badgeRule->method	= $input->clean($badgeRule->rules['method'], 'string');
				$item->badgeRule->ref_id	= $input->clean($badgeRule->rules['ref_id'], 'string');
				$item->badgeRule->multiple	= $input->clean($badgeRule->rules['multiple'], 'boolean');
				$item->badgeRule->rules 	= array();
					
				$rawRules = $badgeRule->rules->children();
				if(empty($rawRules))
				{
					throw new Exception(JText::_('JERROR_ALERTNOAUTHOR').'| RC=3');
				}
					
				foreach($rawRules as $rawRule)
				{
					$rule 					= new stdClass();
					$rule->name				= $input->clean($rawRule['name'], 'string');
					$rule->dataType			= $input->clean($rawRule['dataType'], 'string');
					$rule->compare			= $input->clean($rawRule['compare'], 'string');
					$rule->label			= $input->clean($rawRule['label'], 'string');
					$rule->type				= $input->clean($rawRule['type'], 'string');
					$rule->value 			= 1;
				
					if($rule->type == 'list' || $rule->type == 'checkbox')
					{
						$rule->options = $rule->children();
						$rule->value = array();
					}
					
					$item->badgeRule->rules[] 	= $rule;
				}
				
				break;
			}
		}
		
		if(empty($item->badgeRule))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR').'| RC=4');
		}
		
// 		echo '<pre>'.print_r($item, true).'</pre>';
		
		return $item;
	}

	protected function loadFormData ()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_cjblog.edit.badgerule.data', array());
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		$this->preprocessData('com_cjblog.badgerule', $data);
		
		return $data;
	}

	public function save ($data)
	{
		$app = JFactory::getApplication();
		$input = JFilterInput::getInstance();
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		if (empty($data['created']))
		{
			$data['created'] = $date->toSql();
		}
			
		if (empty($data['created_by']))
		{
			$data['created_by'] = $user->get('id');
		}
		
		$ruleFile = JPATH_ADMINISTRATOR.'/components/'.$data['asset_name'].'/cjblog_rules.xml';
		if(! file_exists($ruleFile))
		{
			$ruleFile = JPATH_ROOT.'/components/'.$data['asset_name'].'/cjblog_rules.xml';
			if(! file_exists($ruleFile))
			{
				$ruleFile = JPATH_ADMINISTRATOR.'/components/com_cjblog/cjblog_rules.xml';
			}
		}
		
		$badgeRules = simplexml_load_file($ruleFile);
		if(empty($badgeRules) || empty($badgeRules->badge_rule))
		{
			$ruleFile = JPATH_ADMINISTRATOR.'/components/com_cjblog/cjblog_rules.xml';
			$badgeRules = simplexml_load_file($ruleFile);
			
			if(empty($badgeRules) || empty($badgeRules->badge_rule))
			{
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR').'| RC=2');
			}
		}
		
		foreach ($badgeRules->badge_rule as $badgeRule)
		{
			$rule_name 				= $input->clean($badgeRule->rule_name, 'string');
			$asset_name				= $input->clean($badgeRule->appname, 'string');
			
			if($asset_name != $data['asset_name'] || $rule_name != $data['rule_name'])
			{
				continue;
			}
			
			$rawRules = $badgeRule->rules->children();
			if(empty($rawRules))
			{
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR').'| RC=3');
			}
			
			$ruleContent			= new stdClass();
			$ruleContent->join		= $input->clean($badgeRule->rules['join'], 'string');
			$ruleContent->method	= $input->clean($badgeRule->rules['method'], 'string');
			$ruleContent->ref_id	= $input->clean($badgeRule->rules['ref_id'], 'string');
			$ruleContent->multiple	= $input->clean($badgeRule->rules['multiple'], 'boolean');
			$ruleContent->rules 	= array();
			
			foreach($rawRules as $rawRule)
			{
				$rule 				= new stdClass();
				$rule->name			= $input->clean($rawRule['name'], 'string');
				$rule->dataType		= $input->clean($rawRule['dataType'], 'string');
				$rule->compare		= $input->clean($rawRule['compare'], 'string');
				$rule->label		= $input->clean($rawRule['label'], 'string');
				$rule->type			= $input->clean($rawRule['type'], 'string');
				
				if($rule->type == 'checkbox')
				{
					$rule->value = $app->input->post->get($rule->name, null, 'array');
					if($rule_node['dataType'] == 'int')
					{
						JArrayHelper::toInteger($rule->value);
					}
				} 
				else 
				{
					$rule->value = $app->input->post->get($rule->name, null, 'string');
				}
				
				if(empty($rule->value)) 
				{
					throw new Exception(JText::_('JERROR_ALERTNOAUTHOR').'| RC=4');
					return false;
				}
			
				$ruleContent->rules[] 	= $rule;
			}
			
			if(empty($ruleContent))
			{
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR').'| RC=5');
			}
			
			$data['asset_title']	= $input->clean($badgeRule->apptitle, 'string');
			$data['rule_content'] 	= json_encode($ruleContent);
		}
		
		if (parent::save($data))
		{
			return true;
		}
		
		return false;
	}

	protected function cleanCache ($group = null, $client_id = 0)
	{
		parent::cleanCache('com_cjblog');
	}
}