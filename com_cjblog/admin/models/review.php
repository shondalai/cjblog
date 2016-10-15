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

class CjBlogModelReview extends JModelAdmin
{
	protected $text_prefix = 'COM_CJBLOG';

	public $typeAlias = 'com_cjblog.review';
	
	protected $_item = null;
	
	public function __construct($config)
	{
		parent::__construct($config);
	}

	public function getTable ($type = 'Reviews', $prefix = 'CjBlogTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save ($data)
	{
		$app = JFactory::getApplication();
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
	
		if (isset($data['id']) && $data['id'])
		{
			// editing the review
		}
		else
		{
			// New review. A review created and created_by field can be set
			// by the user, so we don't touch either of these if they are set.
			if (empty($data['created']))
			{
				$data['created'] = $date->toSql();
			}
				
			if (empty($data['created_by']))
			{
				$data['created_by'] = $user->id;
			}
		}

		if (parent::save($data))
		{
			$articleId = (int) $this->getState($this->getName() . '.id');
			$this->publishArticles(array($articleId), (int) $data['published']);
			return true;
		}
		
		return false;
	}
	
	public function getForm ($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_cjblog.review', 'review', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		$jinput = JFactory::getApplication()->input;
	
		// The front end calls this model and uses t_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('r_id'))
		{
			$id = $jinput->get('r_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}
		
		if ($this->getState('review.id'))
		{
			$id = $this->getState('review.id');
		}
	
		$user = JFactory::getUser();
	
		// Modify the form based on Edit State access controls.
		if (! $user->authorise('core.edit.state', 'com_cjblog'))
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');
				
			// Disable fields while saving.
			// The controller has already verified this is an review you can edit.
			$form->setFieldAttribute('published', 'filter', 'unset');
		}
	
		return $form;
	}

	protected function loadFormData ()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_cjblog.edit.review.data', array());
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		$this->preprocessData('com_cjblog.review', $data);
		
		return $data;
	}
	
	public function publish(&$pks, $value = 1)
	{
		parent::publish($pks, $value);
		$this->publishArticles($pks, $value);
	}
	
	private function publishArticles(&$pks, $value = 1)
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_content/models');
		$model = JModelLegacy::getInstance('Article', 'ContentModel');
		$model->publish($pks, $value);
	}
}