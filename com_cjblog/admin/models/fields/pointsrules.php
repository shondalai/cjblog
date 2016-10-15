<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JFormHelper::loadFieldClass('list');

class JFormFieldPointsRules extends JFormFieldList
{
	protected $type = 'PointsRules';
	
	protected function getOptions()
	{
		$options = array();
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('distinct a.id AS value, a.title AS text')
			->from('#__cjblog_points_rules AS a');
		
		$db->setQuery($query);
		
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
		
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}