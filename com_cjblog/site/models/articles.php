<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_content/models/articles.php';

class CjBlogModelArticles extends ContentModelArticles
{
	protected $_item = null;
	
	public function __construct ($config = array())
	{
		parent::__construct($config);
	}
	
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		parent::populateState($ordering, $direction);
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_cjforum');
		
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
		
		$excludedCategories = $params->get('exclude_categories');
		if(!empty($excludedCategories))
		{
			$this->setState('filter.category_id', $excludedCategories);
			$this->setState('filter.category_id.include', false);
		}
		
		$year = $app->input->getInt('year');
		if($year)
		{
			$this->setState('filter.year', $year);
		}
		
		$month = $app->input->getInt('month');
		if($month)
		{
			$this->setState('filter.month', $month);
		}
		
		$this->setState('list.direction', $listOrder);
		
		$limit = $app->get('list_limit', 20);
		$this->setState('list.limit', $limit);
	}

	protected function getListQuery()
	{
		$query = parent::getListQuery();
		$year = $this->getState('filter.year');
		
		if($year)
		{
			$query->where('year(a.created) = '.$year);
			$month = $this->getState('filter.month');
			
			if($month)
			{
				$query->where('month(a.created) = '.$month);
			}
		}
		
// 		echo $query->dump();
		
		return $query;
	}
	
	public function getCategory ()
	{
		if (! is_object($this->_item))
		{
			if (isset($this->state->params))
			{
				$params = $this->state->params;
				$options = array();
				$options['countItems'] = $params->get('show_cat_num_articles', 1) || ! $params->get('show_empty_categories_cat', 0);
			}
			else
			{
				$options['countItems'] = 0;
			}
				
			$categories = JCategories::getInstance('Content', $options);
			$this->_item = $categories->get($this->getState('filter.category_id', 'root'));
				
			// Compute selected asset permissions.
			if (is_object($this->_item))
			{
				$user = JFactory::getUser();
				$asset = 'com_cjblog.category.' . $this->_item->id;
	
				// Check general create permission.
				if ($user->authorise('core.create', $asset))
				{
					$this->_item->getParams()->set('access-create', true);
				}
	
				// TODO: Why aren't we lazy loading the children and siblings?
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;
	
				if ($this->_item->getParent())
				{
					$this->_parent = $this->_item->getParent();
				}
	
				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else
			{
				$this->_children = false;
				$this->_parent = false;
			}
		}
		
		return $this->_item;
	}
}