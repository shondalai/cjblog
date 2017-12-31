<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

// Base this model on the backend version.
require_once JPATH_ROOT . '/components/com_content/models/form.php';

class CjBlogModelForm extends ContentModelForm
{
	public $typeAlias = 'com_cjblog.article';
	
	public function __construct($config)
	{
		parent::__construct($config);
		$this->populateState();
	}
	
	protected function populateState()
	{
		parent::populateState();
		
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$appParams = JComponentHelper::getParams('com_cjblog');
		$params->merge($appParams);
		
		$this->setState('params', $params);
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFormPath(JPATH_ROOT.'/components/com_content/models/forms');
		$form = parent::getForm($data, $loadData);
		
		$form->setFieldAttribute('title', 'class', 'input-block-level');
		$form->setFieldAttribute('alias', 'class', 'input-block-level');
		$form->setFieldAttribute('catid', 'class', 'input-block-level');
		$form->setFieldAttribute('tags', 'class', 'input-block-level');
		
		return $form;
	}
}