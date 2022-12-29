<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldUsers extends JFormFieldList
{
	public $type = 'Users';
	protected $comParams = null;

	public function __construct()
	{
		parent::__construct();
	}
	
	protected function getInput()
	{
		$chosenAjaxSettings = new JRegistry(
				array(
						'selector'    => '#'.$this->id,
						'type'        => 'GET',
						'url'         => JURI::root() . 'index.php?option=com_cjblog&task=users.find&format=json',
						'dataType'    => 'json',
						'jsonTermKey' => 'q'
				)
		);
		
		JHtml::_('formbehavior.ajaxchosen', $chosenAjaxSettings);
		
		return parent::getInput();	
	}
	
	protected function getOptions()
	{
		if(!empty($this->value))
		{
			return $this->value;
		}
		else
		{
			return parent::getOptions();
		}
	}

	public function allowCustom()
	{
		return false;
	}
}
