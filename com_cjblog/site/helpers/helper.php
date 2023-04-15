<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class CjBlogSiteHelper
{
	public static function uploadFiles($postId, $postType, $fieldName = 'attachment_file')
	{
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		
		jimport('joomla.filesystem.file');
		$files = $app->input->files->get($fieldName);
		$existing = $app->input->post->get('existing_attachment', array(), 'array');
		$uploaded = array();
		
		if(empty($files))
		{
			return 0;
		}
			
		foreach ($files as $i=>$file)
		{
			if(empty($file['tmp_name']))
			{
				continue;
			}
				
			$filename = $postId.'_'.$postType.'_'.\Joomla\CMS\Filesystem\File::makeSafe($file['name']);
			$src = $file['tmp_name'];
			$dest = CJBLOG_ATTACHMENTS_DIR.$filename;
				
			if(\Joomla\CMS\Filesystem\File::upload($src, $dest))
			{
				$upload = new stdClass();
				$upload->name = $filename;
				$upload->size = (int) $file['size'];
				$uploaded[] = $upload;
			}
		}
		
		// first delete the existing attachments which are deleted from the request.
		$existing = Joomla\Utilities\ArrayHelper::toInteger($existing);
		if(!empty($existing))
		{
			$query = $db->getQuery(true)
				->select('id, folder, filename')
				->from('#__cjblog_attachments')
				->where('post_id = '.$postId.' and post_type = '.$postType);
			
			$db->setQuery($query);
			$attachments = array();
			
			try
			{
				$attachments = $db->loadObjectList();
			}
			catch (Exception $e){}
			
			if(!empty($attachments))
			{
				$absolateAttachments = array();
				foreach ($attachments as $attachment)
				{
					if(!in_array($attachment->id, $existing))
					{
						$absolateAttachments[] = $attachment;
					}
				}
				
				if(!empty($absolateAttachments))
				{
					$removedIds = array();
					foreach ($absolateAttachments as $absolate)
					{
						$removedIds[] = $absolate->id;
						if(\Joomla\CMS\Filesystem\File::exists(CJBLOG_ATTACHMENTS_DIR.$absolate->filename))
						{
							\Joomla\CMS\Filesystem\File::delete(CJBLOG_ATTACHMENTS_DIR.$absolate->filename);
						}
					}
					
					$query = $db->getQuery(true)
						->delete('#__cjblog_attachments')
						->where('id in ('.implode(',', $removedIds).')');
					
					$db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch (Exception $e){}
				}
			}
		}
		else 
		{
			// simply delete all existing attachments as all are deleted from request
			$query = $db->getQuery(true)->delete('#__cjblog_attachments')->where('post_id = '.$postId.' and post_type = '.$postType);
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (Exception $e){}
		}
					
		if(!empty($uploaded))
		{
			$query = $db->getQuery(true)
				->insert('#__cjblog_attachments')
				->columns('post_id, post_type, created_by, hash, filesize, folder, filetype, filename');
		
			foreach ($uploaded as $upload)
			{
				$hash = md5_file(CJBLOG_ATTACHMENTS_DIR.$upload->name);
				$query->values($postId.','.$postType.','.$user->id.','.$db->q($hash).','.$upload->size.','.$db->q(CJBLOG_ATTACHMENTS_PATH).','.$db->q('').','.$db->q($upload->name));
			}
		
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (Exception $e){}
		}
	}
	
	public static function isUserBanned($userId = 0)
	{
		$userId = $userId ? $userId : JFactory::getUser()->id;
		
		if($userId > 0)
		{
			$profileApi = CjBlogApi::getProfileApi();
			$profile = $profileApi->getUserProfile($userId);
			return (!empty($profile['banned']) && $profile['banned'] != '0000-00-00 00:00:00');
		}
		else
		{
			return false;
		}
	}
	
	public static function getArticleThumbnail($article, $size = 256)
	{
		if(!file_exists(CJBLOG_MEDIA_DIR.'thumbnails/'.$article->id.'_thumb.jpg')) 
		{
			$params = new JRegistry;
			$params->loadString($article->images);
			
			$app 		= JFactory::getApplication();
			$intro 		= $params->get('image_intro');
			$imageFound = false;
			$filename	= 'DUMMY';
			$tmp		= $app->get('tmp_path') . DIRECTORY_SEPARATOR;

			if(!empty($intro)) 
			{
				return $intro;
			} 

			try
			{
    			preg_match_all('/<img .*src=["|\']([^"|\']+)/i', $article->introtext.$article->fulltext, $matches);
    			foreach ($matches[1] as $file_path) 
    			{
    			    $imageFound = str_replace(JUri::root(false), '/', $file_path);
    				$filename = str_replace(' ', '-', strtolower(\Joomla\CMS\Filesystem\File::makeSafe(basename($imageFound))));
    				if(\Joomla\CMS\Filesystem\File::getExt($filename) == 'jpeg')
    				{
    				    $filename = \Joomla\CMS\Filesystem\File::stripExt($filename) . '.jpg';
    				}
    				
    				if(preg_match('/(http|https)?:\/\/.*$/i', strtolower($imageFound))) 
    				{
    					CjBlogSiteHelper::fetch_image($imageFound, $filename, $tmp, 'absolute', true);
    				}
    				else 
    				{
    					\Joomla\CMS\Filesystem\File::copy($imageFound, $tmp.$filename);
    				}

    				$imgSize = @getimagesize($tmp.$filename);
    				if(!is_array($imgSize) || $imgSize[0] < 32 || $imgSize[1] < 32)
    				{
    					$imageFound = false;
    					continue;
    				}
    				
    				break;
    			}
    
    			if($imageFound)
    			{
    				if(\Joomla\CMS\Filesystem\File::exists($tmp.$filename))
    				{
    				    $image = new Zebra_Image();
    				    $image->jpeg_quality = 100;
    				    $image->preserve_aspect_ratio = true;
    				    $image->enlarge_smaller_images = true;
    				    $image->preserve_time = true;
    				    $image->handle_exif_orientation_tag = true;
    				    $image->source_path = $tmp.$filename;
    				    $image->target_path = CJBLOG_MEDIA_DIR.'thumbnails/'.$article->id.'_thumb.jpg';
    			        $image->resize($size, $size, ZEBRA_IMAGE_CROP_CENTER);
    				}
    			}
			}
			catch (Exception $e)
			{
// 			    $app->enqueueMessage($e->getMessage());
			}
		}

		return file_exists(CJBLOG_MEDIA_DIR.'thumbnails/'.$article->id.'_thumb.jpg')
				? CJBLOG_MEDIA_URI.'thumbnails/'.$article->id.'_thumb.jpg'
				: CJBLOG_MEDIA_URI.'images/'.($size >= 160 ? 'thumbnail-big.png' : 'thumbnail-small.png');
	}
	
	/**
	 * Fetch JPEG or PNG or GIF Image
	*
	* A custom function in PHP which lets you fetch jpeg or png images from remote server to your local server
	* Can also prevent duplicate by appending an increasing _xxxx to the filename. You can also overwrite it.
	*
	* Also gives a debug mode to check where the problem is, if this is not working for you.
	*
	* @author Swashata <swashata ~[at]~ intechgrity ~[dot]~ com>
	* @copyright Do what ever you wish - I like GPL <img src="http://www.intechgrity.com/wp-includes/images/smilies/icon_smile.gif?84cd58" alt=":)" class="wp-smiley">  (& love tux <img src="http://www.intechgrity.com/wp-includes/images/smilies/icon_wink.gif?84cd58" alt=";)" class="wp-smiley"> )
	* @link http://www.intechgrity.com/?p=808
	*
	* @param string $img_url The URL of the image. Should start with http or https followed by :// and end with .png or .jpeg or .jpg or .gif. Else it will not pass the validation
	* @param string $store_dir The directory where you would like to store the images.
	* @param string $store_dir_type The path type of the directory. 'relative' for the location in relation with the executing script location or 'absolute'
	* @param bool $overwrite Set to true to overwrite, false to create a new image with different name
	* @param bool|int $pref internally used to prefix the extension in case of duplicate file name. Used by the trailing recursion call
	* @param bool $debug Set to true for enable debugging and print some useful messages.
	* @return string the location of the image (either relative with the current script or abosule depending on $store_dir_type)
	*/
	public static function fetch_image($img_url, $file_name, $store_dir = 'image', $store_dir_type = 'relative', $overwrite = true, $pref = false, $debug = false) 
	{
		//first get the base name of the image
		$i_name = explode('.', $file_name);
		$i_name = $i_name[0];
	
		//now try to guess the image type from the given url
		//it should end with a valid extension...
		//good for security too
		if(preg_match('/(http|https)?:\/\/.*\.png$/i', strtolower($img_url))) 
		{
			$img_type = 'png';
		}
		else if(preg_match('/(http|https)?:\/\/.*\.(jpg|jpeg)$/i', strtolower($img_url))) 
		{
			$img_type = 'jpg';
		}
		else if(preg_match('/(http|https)?:\/\/.*\.gif$/i', strtolower($img_url))) 
		{
			$img_type = 'gif';
		}
		else 
		{
			if(true == $debug)
			{
				echo 'Invalid image URL';
			}
			
			return ''; //possible error on the image URL
		}
	
		$dir_name = (($store_dir_type == 'relative')? './' : '') . rtrim($store_dir, '/') . '/';
	
		//create the directory if not present
		if(!file_exists($dir_name))
		{
			mkdir($dir_name, 0777, true);
		}
	
		//calculate the destination image path
		$i_dest = $dir_name . $i_name . (($pref === false)? '' : '_' . $pref) . '.' . $img_type;
	
		//lets see if the path exists already
		if(file_exists($i_dest)) 
		{
			$pref = (int) $pref;
	
			//modify the file name, do not overwrite
			if(false == $overwrite)
			{
				return CjBlogSiteHelper::fetch_image($img_url, $store_dir, $store_dir_type, $overwrite, ++$pref, $debug);
			}
			else //delete & overwrite
			{
				unlink ($i_dest);
			}
		}
	
		//first check if the image is fetchable
		$img_info = @getimagesize($img_url);
		
		//is it a valid image?
		if(
				false == $img_info || 
				!isset($img_info[2]) || 
				!($img_info[2] == IMAGETYPE_JPEG || 
						$img_info[2] == IMAGETYPE_PNG || 
						$img_info[2] == IMAGETYPE_JPEG2000 || 
						$img_info[2] == IMAGETYPE_GIF)) 
		{
			if(true == $debug)
			{
				echo 'The image doesn\'t seem to exist in the remote server. Image: '.$img_url.'| Image info: '.print_r($img_info, true);
			}
			
			return ''; //return empty string
		}
	
		//now try to create the image
		if($img_type == 'jpg') 
		{
			$m_img = @imagecreatefromjpeg($img_url);
		} 
		else if($img_type == 'png') 
		{
			$m_img = @imagecreatefrompng($img_url);
			@imagealphablending($m_img, false);
			@imagesavealpha($m_img, true);
		} 
		else if($img_type == 'gif') 
		{
			$m_img = @imagecreatefromgif($img_url);
		} 
		else 
		{
			$m_img = FALSE;
		}
	
		//was the attempt successful?
		if(FALSE === $m_img) 
		{
			if(true == $debug)
			{
				echo 'Can not create image from the URL';
			}
			return '';
		}
	
		//now attempt to save the file on local server
		if($img_type == 'jpg') 
		{
			if(imagejpeg($m_img, $i_dest, 100))
			{
				return $i_dest;
			}
			else
			{
				return '';
			}
		} 
		else if($img_type == 'png') 
		{
			if(imagepng($m_img, $i_dest, 0))
			{
				return $i_dest;
			} 
			else
			{
				return '';
			}
		} 
		else if($img_type == 'gif') 
		{
			if(imagegif($m_img, $i_dest))
			{
				return $i_dest;
			}
			else
			{
				return '';
			}
		}
	
		return '';
	}
	
	public static function renderLayout($layoutFile, $displayData = null, $basePath = '', $options = null)
	{
		// Make sure we send null to JLayoutFile if no path set
		$basePath = empty($basePath) ? null : $basePath;
		$options = is_array($options) ? $options : array();
		$options = array_merge(array('client' => 0, 'component' => 'com_cjblog', 'debug' => false), $options);
		$layout = new JLayoutFile($layoutFile, $basePath, $options);
		$renderedLayout = $layout->render($displayData);
	
		return $renderedLayout;
	}
}