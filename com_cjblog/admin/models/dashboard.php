<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.modellist' );
require_once JPATH_ADMINISTRATOR.'/components/com_content/models/articles.php';

class CjBlogModelDashboard extends ContentModelArticles
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