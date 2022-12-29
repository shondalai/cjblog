<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

require_once JPATH_ROOT.'/components/com_cjblog/models/users.php';

class CjBlogModelLeaderboard extends CjBlogModelUsers
{
	public function __construct ($config = array())
	{
		parent::__construct($config);
	}

	protected function populateState ($ordering = null, $direction = null)
	{
		parent::populateState('registerDate', 'desc');
		
		$this->setState('list.ordering', 'num_articles');
		$this->setState('list.direction', 'desc');
		$this->setState('list.limit', 20);
	}
}