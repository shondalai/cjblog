<?php
/**
 * @version		$Id: helper.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.helpers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CjBlogHelper {
	
	public static function get_config($rebuild=false) {
		 
		$app = JFactory::getApplication();
		$config = $app->getUserState( BLOG_SESSION_CONFIG );
	
		if(empty($config) || $rebuild) {
			 
			$db = JFactory::getDBO();
	
			$query = 'SELECT config_name, config_value FROM '. T_CJBLOG_CONFIG;
			$db->setQuery($query);
			$configt = $db->loadObjectList();
	
			if($configt) {
				 
				foreach($configt as $ct) {
					 
					$config[$ct->config_name] = $ct->config_value;
				}
			}else {
				 
				return CJFunctions::throw_error(JText::_('MSG_UNAUTHORISED').' Error code: 10001.', 403);
			}
	
			$app->setUserState( BLOG_SESSION_CONFIG, $config );
		}
	
		return $config;
	}
	
	/**
	 * @deprecated use get_article_thumbnail
	 * @param unknown $id
	 * @param unknown $html
	 * @param number $size
	 * @return string
	 */
	public static function get_thumbnail($id, $html, $size=256){
		
		if(!file_exists(CJBLOG_MEDIA_DIR.'thumbnails/'.$id.'_thumb.jpg')) {
			
			preg_match_all('/<img .*src=["|\']([^"|\']+)/i', $html, $matches);

			foreach ($matches[1] as $key=>$file_path) {
				
				require_once CJLIB_PATH.'/framework/class.upload.php';
				
				$handle = new thumnail_upload($file_path);
				$handle->file_new_name_body = $id.'_thumb';
				$handle->image_ratio_y = true;
				$handle->image_x = $size;
				$handle->image_resize = true;
				$handle->file_overwrite = true;
				$handle->file_auto_rename = false;
				$handle->image_convert = 'jpg';
				$handle->jpeg_quality = 80;
				$handle->process(CJBLOG_MEDIA_DIR.'thumbnails/');
				break;
			}
		}
	
		return file_exists(CJBLOG_MEDIA_DIR.'thumbnails/'.$id.'_thumb.jpg') 
			? CJBLOG_MEDIA_URI.'thumbnails/'.$id.'_thumb.jpg'
			: CJBLOG_MEDIA_URI.'images/'.($size >= 160 ? 'thumbnail-big.png' : 'thumbnail-small.png');
	}

	public static function get_article_thumbnail($article, $size=256){
	
		if(!file_exists(CJBLOG_MEDIA_DIR.'thumbnails/'.$article->id.'_thumb.jpg')) {
			
			$params = new JRegistry;
			$params->loadString($article->images);
			$intro = $params->get('image_intro');
			$image_found = false;
			
			if(!empty($intro)) {
					
				$image_found = $intro;
			} else {
			
				preg_match_all('/<img .*src=["|\']([^"|\']+)/i', $article->introtext.$article->fulltext, $matches);
				
				foreach ($matches[1] as $key=>$file_path) {
					
					$image_found = $file_path;
					break;
				}
			}
			
			if($image_found)
			{
				$tmp = JFactory::getApplication()->getCfg('tmp_path');
				$filename = str_replace(' ', '-', JFile::makeSafe(basename($image_found)));
				
				if(preg_match('/(http|https)?:\/\/.*$/i', strtolower($image_found))) 
				{
					CjBlogHelper::fetch_image($image_found, $tmp, 'absolute', true);
				}
				else 
				{
					JFile::copy($image_found, $tmp.'/'.$filename);
				}

				if(JFile::exists($tmp.'/'.$filename))
				{
					require_once CJLIB_PATH.'/framework/class.upload.php';
					
					$handle = new thumnail_upload($tmp.'/'.$filename);
					$handle->file_new_name_body = $article->id.'_thumb';
					$handle->image_ratio_y = true;
					$handle->image_x = $size;
					$handle->image_resize = true;
					$handle->file_overwrite = true;
					$handle->file_auto_rename = false;
					$handle->image_convert = 'jpg';
					$handle->jpeg_quality = 80;
					$handle->process(CJBLOG_MEDIA_DIR.'thumbnails/');
// 					JFactory::getApplication()->enqueueMessage($handle->log);
				}
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
	public static function fetch_image($img_url, $store_dir = 'image', $store_dir_type = 'relative', $overwrite = false, $pref = false, $debug = false) {
		//first get the base name of the image
		$i_name = explode('.', basename($img_url));
		$i_name = $i_name[0];
	
		//now try to guess the image type from the given url
		//it should end with a valid extension...
		//good for security too
		if(preg_match('/(http|https)?:\/\/.*\.png$/i', strtolower($img_url))) {
			$img_type = 'png';
		}
		else if(preg_match('/(http|https)?:\/\/.*\.(jpg|jpeg)$/i', strtolower($img_url))) {
			$img_type = 'jpg';
		}
		else if(preg_match('/(http|https)?:\/\/.*\.gif$/i', strtolower($img_url))) {
			$img_type = 'gif';
		}
		else {
			if(true == $debug)
				echo 'Invalid image URL';
			return ''; //possible error on the image URL
		}
	
		$dir_name = (($store_dir_type == 'relative')? './' : '') . rtrim($store_dir, '/') . '/';
	
		//create the directory if not present
		if(!file_exists($dir_name))
			mkdir($dir_name, 0777, true);
	
		//calculate the destination image path
		$i_dest = $dir_name . $i_name . (($pref === false)? '' : '_' . $pref) . '.' . $img_type;
	
		//lets see if the path exists already
		if(file_exists($i_dest)) {
			$pref = (int) $pref;
	
			//modify the file name, do not overwrite
			if(false == $overwrite)
				return CjBlogHelper::fetch_image($img_url, $store_dir, $store_dir_type, $overwrite, ++$pref, $debug);
			//delete & overwrite
			else
				unlink ($i_dest);
		}
	
		//first check if the image is fetchable
		$img_info = @getimagesize($img_url);
		
		//is it a valid image?
		if(false == $img_info || !isset($img_info[2]) || !($img_info[2] == IMAGETYPE_JPEG || $img_info[2] == IMAGETYPE_PNG || $img_info[2] == IMAGETYPE_JPEG2000 || $img_info[2] == IMAGETYPE_GIF)) {
			if(true == $debug)
				echo 'The image doesn\'t seem to exist in the remote server. Image: '.$img_url.'| Image info: '.print_r($img_info, true);
// 			jexit();
			return ''; //return empty string
		}
	
		//now try to create the image
		if($img_type == 'jpg') {
			$m_img = @imagecreatefromjpeg($img_url);
		} else if($img_type == 'png') {
			$m_img = @imagecreatefrompng($img_url);
			@imagealphablending($m_img, false);
			@imagesavealpha($m_img, true);
		} else if($img_type == 'gif') {
			$m_img = @imagecreatefromgif($img_url);
		} else {
			$m_img = FALSE;
		}
	
		//was the attempt successful?
		if(FALSE === $m_img) {
			if(true == $debug)
				echo 'Can not create image from the URL';
			return '';
		}
	
		//now attempt to save the file on local server
		if($img_type == 'jpg') {
			if(imagejpeg($m_img, $i_dest, 100))
				return $i_dest;
			else
				return '';
		} else if($img_type == 'png') {
			if(imagepng($m_img, $i_dest, 0))
				return $i_dest;
			else
				return '';
		} else if($img_type == 'gif') {
			if(imagegif($m_img, $i_dest))
				return $i_dest;
			else
				return '';
		}
	
		return '';
	}
	
	public static function get_intro_text($string,$min=10,$clean=false) {
		$string = str_replace('<br />',' ',$string);
		$string = str_replace('</p>',' ',$string);
		$string = str_replace('<li>',' ',$string);
		$string = str_replace('</li>',' ',$string);
		$text = trim(strip_tags($string));
		if(strlen($text)>$min) {
			$blank = strpos($text,' ');
			if($blank) {
				# limit plus last word
				$extra = strpos(substr($text,$min),' ');
				$max = $min+$extra;
				$r = substr($text,0,$max);
				if(strlen($text)>=$max && !$clean) $r=trim($r,'.').'...';
			} else {
				# if there are no spaces
				$r = substr($text,0,$min).'...';
			}
		} else {
			# if original length is lower than limit
			$r = $text;
		}
		return trim($r);
	}
	
	public static function get_category_table($categories, $params, $options){
		
		if(empty($categories)) return '';
		
		//************************** PARAMS *********************************//
		$class = isset($options['class']) ? $options['class'] : 'category-table';
		$base_url = $options['base_url'];
		$itemid = $options['itemid'];
		$categories_excluded = $params->get('exclude_categories', array());
		//************************** PARAMS *********************************//
		$count_of_excluded = 0;
		
		foreach ($categories as $category){
			
			if(in_array($category->id, $categories_excluded)){
				
				$count_of_excluded++;
			}
		}
		
		$content = '<div class="'.$class.'" id="'.$class.'"><div class="row-fluid">';
		$column_span = 12 / $params->get('max_category_columns', 3);
		$categories_per_column = ceil((count($categories) - $count_of_excluded) / $params->get('max_category_columns', 3));
		$num_subcategories = 0;
		$i = 0;
		
		foreach ($categories as $category){
			
			if(!in_array($category->id, $categories_excluded)){
				
				if($i % $categories_per_column == 0){
					
					$content = $content .'<div class="span'.$column_span.'">';
				}
				
				$content = $content . '<ul class="category"><li class="parent">';
				$content = $content . '<a href="'.JRoute::_($base_url.'&id='.$category->id.':'.$category->alias.$itemid).'">'.CJFunctions::escape($category->title).'</a>';
				
				if($params->get('show_cat_num_articles')){
					
					$content = $content . ' <span class="muted">('.$category->numitems.')</span>';
				}
				
				if($params->get('show_base_description')){
					
					$content = $content . '<div>'.$category->description.'</div>';
				}
				
				
				if($params->get('show_base_image')){
					
					$category_params = json_decode($category->params);
					
					if(!empty($category_params) && !empty($category_params->image)){
						
						$content = $content . '<img class="img-polaroid padbottom-5" src="'.$category_params->image.'"/>';
					}
				}
				
				$content = $content . '</li>';
				
				$children = $category->getChildren();
				
				if(!empty($children)){
					
					$num_subcategories = 1;
					
					foreach ($children as $child){
						
						if(!in_array($child->id, $categories_excluded)){
							
							$content = $content.'<li>';
							$content = $content.'<a href="'.JRoute::_($base_url.'&id='.$child->id.':'.$child->alias.$itemid).'">'.CJFunctions::escape($child->title).'</a>';
										
							if($params->get('show_cat_num_articles')){
								
								$content = $content.' <span class="muted">('.$child->numitems.')</span></li>';
							}
							
							$content = $content.'</li>';
						}
						
						if($num_subcategories == $params->get('max_category_subitems')){
							
							break;
						}
					}
				}
				
				$content = $content . '</ul>';
				
				if(($i % $categories_per_column == $categories_per_column - 1) || ($i+1 == count($categories))){
					
					$content = $content .'</div>';
				}
				
				$i++;
			} // end if the category not excluded
		} // end for
		
		$content = $content . '</div></div>';
		
		return $content;
	}
	
	public static function get_category_table_reccursive($categories, $params){
		
		//************************** PARAMS *********************************//
		$base_url = $params['base_url'];
		$itemid = $params['itemid'];
		$parent_level = isset($params['parent_level']) ? $params['parent_level'] : 0;
		$max_columns = isset($params['max_columns']) ? $params['max_columns'] : 3;
		$max_subitems = isset($params['max_subitems']) ? $params['max_subitems'] : 5;
		$class = isset($params['class']) ? $params['class'] : 'category-table';
		$level = isset($params['level_column']) ? $params['level_column'] : 'nlevel';
		$exclude_categories = isset($params['exclude_categories']) ? explode(',', $params['exclude_categories']) : array();
		//************************** PARAMS *********************************//
		
		
		$content = '<div class="'.$class.'" id="'.$class.'">';
		
		$current_column = 0;
		$current_item = 0;
		$parent_categories = array();
		$category_level = $parent_level + 1;
		
		// get top level categories first
		foreach($categories as $category){
			
			if($category->$level == $category_level && !in_array($category->id, $exclude_categories)){
				
				$parent_categories[] = $category;
			}
		}
		
		if(count($parent_categories) > 0){
			
			// now we get number of parent categories, lets split to columns
			$categories_per_column = ceil(count($parent_categories) / $max_columns);
			
			$cursor = 0;
			$total_categories = count($categories);
			
			for($col = 0; $col < $max_columns; $col++){
				
				$content = $content .'<div class="span'.round(12/$max_columns).'">';
				$previous_column = 'none';
				$column_parent_count = 0;
				$sub_category_count = 0;
				
				for($i = $cursor; $i < $total_categories; $i++){
					
					$category = $categories[$i];
					
					if(in_array($category->id, $exclude_categories)){
						
						if($i+1 == $total_categories){
							
							break;
						}
						
						$temp = $categories[++$i];
						
						while($temp->$level > $category->$level || $i == $total_categories){
							
							$temp = $categories[++$i];
						}
						
						if($i == $total_categories){
							
							break;
						}
						
						$category = $temp;
					}
					
					$category_url = JRoute::_($base_url.'&id='.$category->id.':'.$category->alias.$itemid);
					
					if($category->$level == $category_level){
						
						if($previous_column != 'none'){
							
							$content = $content . '</ul>';
						}
						
						if($column_parent_count == $categories_per_column){
							
							$cursor = $i;
							break;
						}
						
						$content = $content . '<ul class="category"><li class="parent"><a href="'.$category_url.'">'.CJFunctions::escape($category->title).'</a>';
						
						if($params['show_cat_num_articles']){
							
							$content = $content . ' <span class="muted">('.$category->numitems.')</span></li>';
						}
						
						if($params['show_base_description']){
							
							$content = $content . '<div>'.$category->description.'</div>';
						}
						
						
						if($params['show_base_image']){
							
							$category_params = json_decode($category->params);
							
							if(!empty($category_params) && !empty($category_params->image)){
								
								$content = $content . '<img class="img-polaroid padbottom-5" src="'.$category_params->image.'"/>';
							}
						}
						
						$previous_column = 'parent';
						$column_parent_count++;
						$sub_category_count = 0;
					} else if ($category->$level == $category_level + 1 && ($sub_category_count < $max_subitems || $max_subitems == -1)){
						
						$content = $content . '<li><a href="'.$category_url.'">'.CJFunctions::escape($category->title).'</a>';
						
						if($params['show_cat_num_articles']){
							
							$content = $content . ' <span class="muted">('.$category->numitems.')</span></li>';
						}
						
						$previous_column = 'child';
						$sub_category_count++;
					}
					
					if($i == ($total_categories - 1)){
						
						$cursor = $i + 1;
						$content = $content . '</ul>';
					}
				}
				
				$content = $content . '</div>';
			}
		}
		
		$content = $content . '</div>';
		
		return $content;
	}
	
	public static function get_page_title($title){
		
		$app = JFactory::getApplication();
		
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		
		return $title;
	}
}