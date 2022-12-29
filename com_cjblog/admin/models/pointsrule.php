<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JLoader::register('CjBlogHelper', JPATH_ADMINISTRATOR . '/components/com_cjblog/helpers/cjblog.php');

class CjBlogModelPointsrule extends JModelAdmin
{
	protected $text_prefix = 'COM_CJBLOG';

	public $typeAlias = 'com_cjblog.pointsrule';
	
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

	public function getTable ($type = 'Pointsrule', $prefix = 'CjBlogTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm ($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_cjblog.pointsrule', 'pointsrule', array('control' => 'jform', 'load_data' => $loadData));
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
		if ($this->getState('pointsrule.id'))
		{
			$id = $this->getState('pointsrule.id');
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

	protected function loadFormData ()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_cjblog.edit.pointsrule.data', array());
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		$this->preprocessData('com_cjblog.pointsrule', $data);
		
		return $data;
	}

	public function save ($data)
	{
		$app = JFactory::getApplication();
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

	public static function scanPointsRules()
	{
		$components = array();
		$db = JFactory::getDbo();
		
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

			self::loadPointsRules($ruleFile, $component);
		}
		
		return true;
	}
	
	private static function loadPointsRules($file, $component)
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$rules = simplexml_load_file($file);
		
		if(empty($rules) || empty($rules->points_rule)) 
		{
			return false;
		}
		
		foreach ($rules->points_rule as $rule)
		{
		
			$query = $db->getQuery(true)
				->insert('#__cjblog_points_rules')
				->columns('title, description, rule_name, app_name, points, published, auto_approve, access, created_by, created')
				->values(
					$db->q($rule->title).','.
					$db->q($rule->description).','.
					$db->q($rule->name).','.
					$db->q($rule->appname).','.
					((int)$rule->points).','.
					((int)$rule->state).','.
					((int)$rule->auto_approve).','.
					((int)$rule->access).','.
					JFactory::getUser()->id.','.
				$db->q(JFactory::getDate()->toSql()));
				
			$db->setQuery($query);
			
			try 
			{
				$db->execute();
			}
			catch (Exception $e)
			{
				// ignore if the rule already exists
// 				$app->enqueueMessage($db->getErrorMsg());
			}
		}
		
		return true;
	}
}