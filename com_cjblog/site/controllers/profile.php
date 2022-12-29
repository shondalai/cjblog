<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

class CjBlogControllerProfile extends JControllerForm
{
	protected $view_item = 'profileform';
	protected $view_list = 'profile';
	protected $urlVar = 'p_id';
	protected $text_prefix = 'COM_CJBLOG_PROFILE';

	public function add ()
	{
		if (! parent::add())
		{
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}
	
	protected function allowAdd ($data = array())
	{
		return JFactory::getUser()->authorise('core.admin', 'com_cjblog');
	}
	
	public function cancel ($key = 'p_id')
	{
		parent::cancel($key);
		
		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	public function edit ($key = null, $urlVar = 'p_id')
	{
		$result = parent::edit($key, $urlVar);
		
		if (!$result)
		{
			$this->setRedirect($this->getReturnPage());
		}
		
		return $result;
	}
	
	protected function allowEdit ($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->id;
		$asset = 'com_cjblog';
	
		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			return true;
		}
	
		if ($recordId && $recordId == $userId)
		{
			return true;
		}
		
		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	public function getModel ($name = 'profileform', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		
		return $model;
	}

	protected function getRedirectToItemAppend ($recordId = null, $urlVar = 'p_id')
	{
		// Need to override the parent method completely.
		$tmpl = $this->input->get('tmpl');
		// $layout = $this->input->get('layout', 'edit');
		$append = '';
		
		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}
		
		// TODO This is a bandaid, not a long term solution.
		// if ($layout)
		// {
		// $append .= '&layout=' . $layout;
		// }
		$append .= '&layout=edit';
		
		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}
		
		$itemId = $this->input->getInt('Itemid');
		$return = $this->getReturnPage();
		
		if ($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}
		
		if ($return)
		{
			$append .= '&return=' . base64_encode($return);
		}
		
		return $append;
	}

	protected function getReturnPage ()
	{
		$return = $this->input->get('return', null, 'base64');
		if (empty($return) || ! JUri::isInternal(base64_decode($return)))
		{
			return JRoute::_(CjBlogHelperRoute::getUsersRoute());
		}
		else
		{
			return base64_decode($return);
		}
	}

	protected function postSaveHook (JModelLegacy $model, $validData = array())
	{
		return;
	}
	
	public function save($key = null, $urlVar = 'p_id')
	{
		return parent::save($key, $urlVar);
	}
	
	public function download()
	{
	    if(JFactory::getUser()->guest)
	    {
	        throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
	    }
	    
	    $model = $this->getModel('Profile');
	    $filename = $model->getGDPRProfileData();
	    
	    header("Pragma: public");
	    $now = gmdate("D, d M Y H:i:s");
	    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
	    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
	    header("Last-Modified: {$now} GMT");
	    header("Content-Description: File Transfer");
	    header("Content-Type: application/force-download");
	    header("Content-type: application/octet-stream");
	    header("Content-Type: application/download");
	    header("Content-Disposition: attachment; filename=data_export_" . date("Y-m-d") . ".zip");
	    header("Content-Transfer-Encoding: binary");
	    header("Content-Length: ".filesize($filename));
	    
	    readfile($filename);
	    jexit();
	}
	
	public function delete()
	{
	    $user = JFactory::getUser();
	    if($user->guest)
	    {
	        throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
	    }
	    
	    $model = $this->getModel('Profile');
	    $deleteData = $this->input->getInt('delete_data');
	    
	    if(!$model->deleteProfileAndData($deleteData))
	    {
	        $this->setRedirect(JRoute::_(CjBlogHelperRoute::getProfileRoute($user->id)), JText::_('COM_CJBLOG_ERROR_PERFORMING_ACTION'));
	    }
	    else
	    {
	        $this->setRedirect(JUri::root(true));
	    }
	}
}
