<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogModelBadgeTemplates extends JModelLegacy
{
	public function __construct ($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'id', 'a.id'
			);
			
			if (JLanguageAssociations::isEnabled())
			{
				$config['filter_fields'][] = 'association';
			}
		}
		
		parent::__construct($config);
	}

	protected function populateState ($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		// List state information.
		parent::populateState('a.created', 'desc');
	}
	
	public static function getItems()
	{
		$components = array();
		$db = JFactory::getDbo();
		$input =  JFilterInput::getInstance();
	
		$query = $db->getQuery(true)
			->select('element')
			->from('#__extensions')
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('enabled').' = 1')
			->where($db->qn('client_id').' = 1');
		$db->setQuery($query);
	
		try
		{
			$components = $db->loadColumn();
		}
		catch (Exception $e)
		{
			return false;
		}
	
		if(empty($components))
		{
			return false;
		}
	
		$types = array();
		foreach ($components as $component)
		{
			$ruleFile = JPATH_ADMINISTRATOR.'/components/'.$component.'/cjblog_rules.xml';
			if(! file_exists($ruleFile))
			{
				$ruleFile = JPATH_ROOT.'/components/'.$component.'/cjblog_rules.xml';
				if(! file_exists($ruleFile))
				{
					continue;
				}
			}
				
			$rules = simplexml_load_file($ruleFile);
			if(empty($rules) || empty($rules->badge_rule))
			{
				continue;
			}
	
			foreach ($rules->badge_rule as $rule)
			{
				$type 				= new stdClass();
				$type->asset_name 	= $input->clean((string) $rule->rule_name, 'string');
				$type->appname 		= $input->clean((string) $rule->appname, 'string');
				$type->description 	= $input->clean((string) $rule->rule_description, 'string');

				if(!$type->appname || !isset($types[$type->appname]))
				{
					$types[$type->appname] = array();
					$types[$type->appname]['title'] = $input->clean((string) $rule->apptitle, 'string');
					$types[$type->appname]['templates'] = array();
				}
	
				$types[$type->appname]['templates'][] = $type;
			}
		}
	
		return $types;
	}
}