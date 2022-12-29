<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JFormHelper::loadFieldClass('list');

class JFormFieldRuleApp extends JFormFieldList
{
	protected $type = 'RuleApp';
	
	protected function getOptions()
	{
		$options = array();
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('distinct a.app_name AS value, a.app_name AS text')
			->from('#__cjblog_points_rules AS a');
		
		$db->setQuery($query);
		
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
		    throw new Exception($e->getMessage(), 500);
		}
		
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}