<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_content/models/category.php';
require_once JPATH_ROOT . '/components/com_content/helpers/query.php';

class CjBlogModelCategory extends ContentModelCategory
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();
		parent::populateState($ordering, $direction);
		
		$orderCol = $app->input->get('filter_order', 'a.created');
		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'a.created';
		}
		
		$this->setState('list.ordering', $orderCol);
		
		$listOrder = $app->input->get('filter_order_Dir', 'DESC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'DESC';
		}
		
		$search = $app->input->get('list_filter', '', 'string');
		$this->setState('list.filter', $search);
		
		$authorId = $app->input->get('filter_author_id', 0, 'unint');
		if($authorId)
		{
			$this->setState('filter.author_id', $authorId);
			$this->setState('filter.author_id.include', true);
		}
		
		$this->setState('list.direction', $listOrder);
		
		$limit = $app->get('list_limit', 20);
		$this->setState('list.limit', $limit);
	}
}
