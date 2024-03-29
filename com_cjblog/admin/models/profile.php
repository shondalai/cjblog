<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

JLoader::register('CjBlogHelper', JPATH_ADMINISTRATOR . '/components/com_cjblog/helpers/cjblog.php');

class CjBlogModelProfile extends JModelAdmin
{
	protected $text_prefix = 'COM_CJBLOG';

	public $typeAlias = 'com_cjblog.profile';
	
	protected $_item = null;
	
	public function __construct($config)
	{
		$config['event_after_delete'] = 'onProfileAfterDelete';
		$config['event_after_save'] = 'onProfileAfterSave';
		$config['event_before_delete'] = 'onProfileBeforeDelete';
		$config['event_before_save'] = 'onProfileBeforeSave';
		$config['event_change_state'] = 'onProfileChangeState';
			
		parent::__construct($config);
		
		$this->populateState();
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Load the parameters.
		$params = JComponentHelper::getParams('com_cjblog');
		$this->setState('params', $params);
	
		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	protected function batchCopy ($value, $pks, $contexts)
	{
		$categoryId = (int) $value;
		
		$i = 0;
		
		if (! parent::checkCategoryId($categoryId))
		{
			return false;
		}
		
		// Parent exists so we let's proceed
		while (! empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);
			
			$this->table->reset();
			
			// Check that the row actually exists
			if (! $this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);
					
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}
			
			// Reset the ID because we are making a copy
			$this->table->id = 0;
			if (! $this->table->check())
			{
				$this->setError($table->getError());
				return false;
			}
			
			// Store the row.
			if (! $this->table->store())
			{
				$this->setError($table->getError());
				return false;
			}
			
			// Get the new item ID
			$newId = $this->table->get('id');
			
			// Add the new ID to the array
			$newIds[$i] = $newId;
			$i ++;
		}
		
		// Clean the cache
		$this->cleanCache();
		
		return $newIds;
	}

	protected function canDelete ($record)
	{
		if (! empty($record->id))
		{
			if ($record->state != - 2)
			{
				return;
			}
			$user = JFactory::getUser();
			return $user->authorise('core.delete', 'com_cjblog');
		}
	}

	public function getTable ($type = 'Profile', $prefix = 'CjBlogTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItem ($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Convert the params field to an array.
			$registry = new JRegistry();
			if(!empty($item->attribs))
			{
				$registry->loadString($item->attribs);
			}
			$item->attribs = $registry->toArray();
			
			// Convert the metadata field to an array.
			$registry = new JRegistry();
			if(!empty($item->metadata))
			{
				$registry->loadString($item->metadata);
			}
			$item->metadata = $registry->toArray();
		}

		return $item;
	}

	public function getForm ($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_cjblog.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));	
		if (empty($form))
		{
			return false;
		}
		$jinput = JFactory::getApplication()->input;
		
		// The front end calls this model and uses t_id to avoid id clashes so
		// we need to check for that first.
		if ($jinput->get('p_id'))
		{
			$id = $jinput->get('p_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it
		// to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}
		// Determine correct permissions to check.
		if ($this->getState('profile.id'))
		{
			$id = $this->getState('profile.id');
		}
		
		$user = JFactory::getUser();
		
		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if (! $user->authorise('core.edit.state', 'com_cjblog'))
		{
			// Disable fields for display.
			$form->setFieldAttribute('banned', 'disabled', 'true');
			$form->setFieldAttribute('banned', 'filter', 'unset');
		}
		
		$app = JFactory::getApplication();
		
		if ($app->isClient('site') && $this->getState('profile.id'))
		{
			$form->setFieldAttribute('handle', 'readonly', 'true');
		}
		
		return $form;
	}

	protected function loadFormData ()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_cjblog.edit.profile.data', array());
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		$this->preprocessData('com_cjblog.profile', $data);
		
		return $data;
	}
	
	protected function preprocessForm(JForm $form, $data, $group = 'cjblog')
	{
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);
		$app = JFactory::getApplication();

		// Trigger the form preparation event.
		$app->triggerEvent('onProfilePrepareForm', array($form, $data));
	}

	public function save ($data)
	{
		$app = JFactory::getApplication();
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		if (isset($data['id']) && $data['id'])
		{
			// Existing item
			$data['modified'] = $date->toSql();
			$data['modified_by'] = $user->get('id');
		}
		else
		{
			// New article. A article created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (empty($data['created']))
			{
				$data['created'] = $date->toSql();
			}
				
			if (empty($data['created_by']))
			{
				$data['created_by'] = $user->get('id');
			}
		}
		
		$userId = !empty($data['id']) ? $data['id'] : 0;
		$avatar = $this->uploadAvatar($userId);
		
		if(!empty($avatar))
		{
			$data['avatar'] = $avatar;
		}
		
		$data['ip_address'] = CjLibUtils::getUserIpAddress();
		JPluginHelper::importPlugin('cjblog');
		
		if (parent::save($data))
		{
			return true;
		}
		
		return false;
	}
	
	private function uploadAvatar($userId = 0)
	{
		$app = JFactory::getApplication();
		$tmp_file = $app->input->files->get('avatar_file');
		$coords = $app->input->getString('avatar-coords');
		$coords = explode(',', $coords);
		$sizes = array(16, 32, 48, 64, 96, 128, 160, 192, 256);
		$temp_image_path = '';
		$temp_image_name = '';
		$file_path = null;
		$file_name = null;
		
		if(count($coords) != 6)
		{
			return false;
		}
		
		if(!$tmp_file || !$tmp_file['tmp_name'] || !$tmp_file['name'] || $tmp_file['error'] > 0)
		{
			// avatar not changed but coords may have adjusted
			if($userId)
			{
				$api = CjBlogApi::getProfileApi();
				$profile = $api->getUserProfile($userId);
				$file_name = $profile['avatar'];
				
				if(!empty($profile['avatar']) && \Joomla\CMS\Filesystem\File::exists(CJBLOG_AVATAR_BASE_DIR.'/size-256/'.$profile['avatar']))
				{
					$file_path = CJBLOG_AVATAR_BASE_DIR.'/size-256/'.$profile['avatar'];
					list($temp_image_width, $temp_image_height, $temp_image_type) = getimagesize($file_path);
					$file_name = basename($profile['avatar']);
				}
			}
		}
		else 
		{
			$temp_image_path = $tmp_file['tmp_name'];
			$temp_image_name = $tmp_file['name'];
			
			$temp_image_ext = \Joomla\CMS\Filesystem\File::getExt($temp_image_name);
			list($temp_image_width, $temp_image_height, $temp_image_type) = getimagesize($temp_image_path);
			
			if ($temp_image_type === NULL
					|| $temp_image_width < 64
					|| $temp_image_height < 64
					|| !in_array(strtolower($temp_image_ext), array('png', 'jpg', 'gif'))
					|| !in_array($temp_image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF)))
			{
				// 			echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING').'| Error Code 2.'));
				return false;
			}
			
			$file_name = \Joomla\CMS\Filesystem\File::makeSafe(CjLibUtils::getRandomKey(25, 'abcdefghijklmnopqrstuvwxyz1234567890'));
			$file_path = CJBLOG_AVATAR_BASE_DIR.'original/'.$file_name.'.'.$temp_image_ext;
			
			if(! \Joomla\CMS\Filesystem\File::upload($temp_image_path, $file_path))
			{
				echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING').'| Error Code 3.'));
				return false;
			}
		}
		
		if(empty($file_path))
		{
			return false;
		}
		
		require_once CJLIB_PATH.'/framework/class.upload.php';
		
		// devide/multiply with scale obtain correct image coords
		$scale = $coords[0] == 0 ? 0.0001 : $coords[0];
		$top = $scale < 1 ? $coords[3] / $scale : $coords[3] * $scale;
		$left = $scale < 1 ? $coords[2] / $scale : $coords[2] * $scale;
		$width = $scale < 1 ? $coords[4] / $scale : $coords[4] * $scale;
		$height = $scale < 1 ? $coords[5] / $scale : $coords[5] * $scale;
		$right = $temp_image_width - ($left + $width);
		$bottom = $temp_image_height - ($top + $height);
		
		foreach ($sizes as $size)
		{
			// coords [scale, angle, left, top, width, height]
			$handle = new thumnail_upload($file_path);
			$handle->image_precrop = array($top, $right, $bottom, $left); //TRBL
			$handle->file_overwrite = true;
			$handle->file_auto_rename = false;
			$handle->image_convert = 'jpg';
			$handle->jpeg_quality = 80;
			$handle->image_resize = true;
			$handle->image_x = $size;
			$handle->image_y = $size;
			$handle->image_rotate = $coords[1] > 0 ? $coords[1] : null;
			$handle->file_new_name_body = $file_name;
			$handle->process(CJBLOG_AVATAR_BASE_DIR.'size-'.$size.'/');
			
			if (!$handle->processed) 
			{
// 				echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING').'| Error Code 4.'));
				return false;
			}
		}

		return !empty($file_name) ? $file_name.'.jpg' : false;
	}
}