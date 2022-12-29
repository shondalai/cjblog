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

class CjBlogModelBadgestream extends JModelAdmin
{
	protected $text_prefix = 'COM_CJBLOG';

	public $typeAlias = 'com_cjblog.badgestream';
	
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

	public function getTable ($type = 'Badgestreams', $prefix = 'CjBlogTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm ($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_cjblog.badgestream', 'badgestream', array('control' => 'jform', 'load_data' => $loadData));
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
		if ($this->getState('badgestream.id'))
		{
			$id = $this->getState('badgestream.id');
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
		$data = $app->getUserState('com_cjblog.edit.badgestream.data', array());
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		$this->preprocessData('com_cjblog.badgestream', $data);
		
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