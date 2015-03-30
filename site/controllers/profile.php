<?php
/**
 * @version		$Id: profile.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerProfile extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_users_profile');
		$this->registerTask('save_about', 'save_about');
		$this->registerTask('upload_avatar', 'upload_avatar');
		$this->registerTask('save_avatar', 'save_avatar');
	}

	function get_users_profile(){

		$view = $this->getView('profile', 'html');
		$model = $this->getModel('users');
		$articles = $this->getModel('articles');

		$view->setModel($model, true);
		$view->setModel($articles, false);
		$view->display();
	}
	
	function save_about(){
		
		$user = JFactory::getUser();
		$model = $this->getModel('users');
		$app = JFactory::getApplication();
		
		$about = $app->input->getHtml('user-about');
		$id = $app->input->getInt('id');
		
		if($user->id != $id && !$user->authorise('core.manage')){
			
			echo json_encode(array('error'=>JText::_('JERROR_ALERTNOAUTHOR')));
		} else {
			
			if(!empty($about) && $id && $model->save_about($id, $about)){
				
				$about = CJFunctions::preprocessHtml($about, false, true);
				echo json_encode(array('data'=>$about));
			} else {
				
				echo json_encode(array('error'=>JText::_('MSG_MISSING_REQUIRED_FIELDS')));
			}
		}
		
		jexit();
	}
	
	function upload_avatar(){
		
		$input = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		
		$id = $input->getInt('id', 0);
		$xhr = ($input->server->get('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest');
		
		if (!$xhr) echo '<textarea>';
		
		if($user->id != $id && !$user->authorise('core.manage')){
			
			echo json_encode(array('error'=>JText::_('JERROR_ALERTNOAUTHOR')));
		} else {
			
			if(!$id){
				
				echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
			} else {

				$tmp_file = $input->files->get('input-avatar-image');
				
				if($tmp_file['error'] > 0){
					
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
				} else {
					
					$temp_image_path = $tmp_file['tmp_name'];
					$temp_image_name = $tmp_file['name'];
					
					$temp_image_ext = JFile::getExt($temp_image_name);
					list($temp_image_width, $temp_image_height, $temp_image_type) = getimagesize($temp_image_path);
					
					if ($temp_image_type === NULL 
							|| $temp_image_width < 128 
							|| $temp_image_height < 128 
							|| !in_array(strtolower($temp_image_ext), array('png', 'jpg', 'gif'))
							|| !in_array($temp_image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
						
						echo json_encode(array('error'=>JText::_('MSG_INVALID_IMAGE_FILE')));
					} else {
	
						$user_profile = CjBlogApi::get_user_profile($id);
						$file_name = '';
						
						if(!empty($user_profile['avatar'])){
							
							$file_name = $user_profile['avatar'];
						} else {
						
							$file_name = CJFunctions::generate_random_key(25, 'abcdefghijklmnopqrstuvwxyz1234567890').'.'.$temp_image_ext;
						}
					
						$uploaded_image_path = CJBLOG_AVATAR_BASE_DIR.'original'.DS.$file_name;
						
						if(JFile::upload($temp_image_path, $uploaded_image_path)){
						
							echo json_encode(array('avatar'=>array(
									'url'=>CJBLOG_AVATAR_BASE_URI.'original/'.$file_name, 'file_name'=>$file_name,
									'width'=>$temp_image_width,
									'height'=>$temp_image_height)));
						} else {
							
							echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
						}
					}
				}
			}
		}
		
		if (!$xhr) echo '</textarea>';
		jexit();
	}
	
	function save_avatar(){
		
		$input = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		
		$id = $input->getInt('id', 0);
		$filename = $input->getString('file_name', null);
		$coords = $input->getString('coords', null);
		
		if($user->id != $id && !$user->authorise('core.manage')){
				
			echo json_encode(array('error'=>JText::_('JERROR_ALERTNOAUTHOR')));
		} else {

			$filename = JFile::makeSafe($filename);
			$file_path = CJBLOG_AVATAR_BASE_DIR.'original'.DS.$filename;
			$coords = explode(',', $coords);
			$sizes = array(16, 32, 48, 64, 96, 128, 160, 192, 256);

			if(!$id || empty($coords) || (count($coords) != 6) || empty($filename) || !JFile::exists($file_path)){
		
				echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING').'| Error Code 1.'));
			} else {
				
				require_once CJLIB_PATH.DS.'framework'.DS.'class.upload.php';
				list($temp_image_width, $temp_image_height, $temp_image_type) = getimagesize($file_path);
				
				foreach ($sizes as $size){
					
					$handle = new thumnail_upload($file_path);
					$handle->image_precrop = array($coords[1], $temp_image_width - $coords[2],  $temp_image_height - $coords[3], $coords[0]);
					$handle->file_overwrite = true;
					$handle->file_auto_rename = false;
					$handle->image_convert = 'jpg';
					$handle->jpeg_quality = 80;
					$handle->image_resize = true;
					$handle->image_x = $size;
					$handle->image_y = $size;
					$handle->process(CJBLOG_AVATAR_BASE_DIR.'size-'.$size.DS);
					
					if (!$handle->processed) {
// 						echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING').'| Error Code 2.<br/><br/>'.$handle->log));
						echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING').'| Error Code 2.'));
						jexit();
					}
				}
				
				$new_file = JFile::stripExt($filename).'.jpg';
				$model = $this->getModel('users');
				
				if($model->save_user_avatar_name($id, $new_file)){

					JFile::delete($file_path);
					echo json_encode(array('src'=>CJBLOG_AVATAR_BASE_URI.'size-256/'.$new_file));
				} else {
					
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING').'| Error Code 3.'.$model->getError()));
				}
			}
		}
		
		jexit();
	}
}
?>