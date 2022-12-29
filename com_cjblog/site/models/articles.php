<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class CjBlogModelArticles extends ListModel
{
	protected $_item = null;

	private $_model = null;
	
	public function __construct ($config = array())
	{
		parent::__construct($config);
		$this->populateState();
		
		if(CJBLOG_MAJOR_VERSION < 4) {
		    require_once JPATH_ROOT . '/components/com_content/models/articles.php';
		}
	}
	
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		parent::populateState($ordering, $direction);
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_cjblog');
		
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

		$featured = $app->input->get('filter_featured', null);
		if($featured) {
			$this->setState('filter_featured', $featured);
		}

		$search = $app->input->get('list_filter', '', 'string');
		$this->setState('list.filter', $search);

		$searchField = $app->input->get('list_filter_field', 'title', 'string');
		$params->set('filter_field', $searchField);
		$this->setState('params', $params);
		
		$authorId = $app->input->get('filter_author_id', 0, 'unint');
		if($authorId)
		{
			$this->setState('filter.author_id', $authorId);
			$this->setState('filter.author_id.include', true);
		}

		$categories = $app->input->get('catid', [], 'unint');
		if(!empty($categories))
		{
			$this->setState('filter.category_id', $categories);
			$this->setState('filter.category_id.include', true);
		}

		$excludedCategories = $params->get('exclude_categories');
		if(!empty($excludedCategories))
		{
			$this->setState('filter.category_id', $excludedCategories);
			$this->setState('filter.category_id.include', false);
		}
		
		$month = $app->input->getInt('month');
		if($month)
		{
		    $date = Factory::getDate();
		    $this->setState('filter_field', 'month');
			$this->setState('list.filter', $date->year . '-' . $month . '-1');
		}
		
		$this->setState('list.direction', $listOrder);
		
		$limit = $app->get('list_limit', 20);
		$this->setState('list.limit', $limit);
		
		$limitStart = $app->get('start', 0);
		$this->setState('list.start', $limitStart);
	}

	public function getItems()
	{
	    if(CJBLOG_MAJOR_VERSION < 4) {
	        JLoader::import('articles', JPATH_ROOT . '/components/com_content/models/');
	        $this->_model = JModelLegacy::getInstance( 'articles', 'ContentModel' );
	    } else {
		    $this->_model = Factory::getApplication()->bootComponent('com_content')->getMVCFactory()->createModel('Articles', 'Site');
	    }

		$this->_model->setState('list.ordering', $this->getState('list.ordering'));
		$this->_model->setState('list.filter', $this->getState('list.filter'));
		$this->_model->setState('filter.author_id', $this->getState('filter.author_id'));
		$this->_model->setState('filter.author_id.include', $this->getState('filter.author_id.include'));
		$this->_model->setState('filter.category_id', $this->getState('filter.category_id'));
		$this->_model->setState('filter.category_id.include', $this->getState('filter.category_id.include'));
		$this->_model->setState('filter.field', $this->getState('filter_field'));
		$this->_model->setState('filter.featured', $this->getState('filter_featured'));
		$this->_model->setState('list.filter', $this->getState('list.filter'));
		$this->_model->setState('list.direction', $this->getState('list.direction'));
		$this->_model->setState('list.limit', $this->getState('list.limit'));
		$this->_model->setState('list.start', $this->getState('list.start'));
		$this->_model->setState('params', $this->getState('params'));

	    return $this->_model->getItems();
	}
	
	public function getPagination()
	{
	    return $this->_model ? $this->_model->getPagination() : null;
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