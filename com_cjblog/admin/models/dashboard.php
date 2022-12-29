<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogModelDashboard extends JModelList
{
	public function __construct ($config = array())
	{
		parent::__construct($config);
	}

	protected function populateState ($ordering = null, $direction = null)
	{
		parent::populateState('a.created', 'desc');
		$this->setState('list.limit', 5);
	}
	
	public function getItems()
	{
		$app = JFactory::getApplication();

		$model = null;

		if(CJBLOG_MAJOR_VERSION < 4)
		{
			require_once JPATH_ROOT.'/components/com_content/router.php';
			require_once JPATH_ROOT.'/components/com_content/helpers/route.php';
			JLoader::import('articles', JPATH_ROOT.'/components/com_content/models');
			$model = JModelList::getInstance('Articles', 'ContentModel');
		}
		else
		{
			$model = $app->bootComponent('com_content')->getMVCFactory()->createModel('Articles', 'Site', ['ignore_request' => true]);
			$contentParams = JComponentHelper::getParams('com_content');
			$model->setState('params', $contentParams);
		}

		$model->getState();
		$model->setState('list.limit', 5);
		$model->setState('list.start', 0);

		$items = $model->getItems();

		return $items;
	}
	
	public function getArticleCountByDay()
	{
		$db = JFactory::getDbo();
		try 
		{
			$query = $db->getQuery(true)
				->select('count(*) as articles, date(created) as cdate')
				->from('#__content')
				->where('created >= DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR)')
				->group('date(created)')
				->order('created desc');
			
			$db->setQuery($query);
			$articleCounts = $db->loadAssocList('cdate');
			return $articleCounts;
		}
		catch (Exception $e){}
		return false;
	}
}