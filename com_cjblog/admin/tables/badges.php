<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_PLATFORM') or die();

class CjBlogTableBadges extends JTable
{
	public function __construct (JDatabaseDriver $db)
	{
		parent::__construct('#__cjblog_badges', 'id', $db);
	}
	
	public function check ()
	{
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('COM_CJBLOG_WARNING_PROVIDE_VALID_NAME'));
				
			return false;
		}
	
		if (trim($this->alias) == '')
		{
			$this->alias = $this->title;
		}
	
		$this->alias = JApplication::stringURLSafe($this->alias);
	
		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}
	
		if (trim(str_replace('&nbsp;', '', $this->description)) == '')
		{
			$this->description = '';
		}
	
		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}
	
		// Clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (! empty($this->metakey))
		{
			// Only process if not empty
				
			// Array of characters to remove
			$bad_characters = array("\n", "\r", "\"", "<", ">");
				
			// Remove bad characters
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey);
				
			// Create array using commas as delimiter
			$keys = explode(',', $after_clean);
				
			$clean_keys = array();
				
			foreach ($keys as $key)
			{
				if (trim($key))
				{
					// Ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			// Put array back together delimited by ", "
			$this->metakey = implode(", ", $clean_keys);
		}
	
		return true;
	}
}
