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

class CjBlogAdminHelper {
	
	public static function get_fields_html($fields, $component_params){
		
		$return = '';
		
		foreach ($fields as $field){
			
			switch ($field->attributes()->type){
				
				case 'text':
					$return = $return . '<div class="control-group">';
					$return = $return . '<label class="control-label tooltip-hover" title="'.JText::_($field->attributes()->description).'">'.JText::_($field->attributes()->label).':</label>';
					$return = $return . '<div class="controls"><input type="text" class="span2" name="'.$field->attributes()->name.'" value="'.$field->attributes()->default.'" /></div></div>';
					break;
					
				case 'radio':
					
					$return = $return . '<div class="control-group">';
					$return = $return . '<label class="control-label tooltip-hover" title="'.JText::_($field->attributes()->description).'">'.JText::_($field->attributes()->label).':</label>';
					$return = $return . '<div class="controls"><div class="btn-group" data-toggle-name="'.$field->attributes()->name.'" data-toggle="buttons-radio" >';
					
					foreach ($field->children() as $option){
						
						$return = $return . '<button type="button" value="'.$option->attributes()->value.'" class="btn" data-toggle="button" data-toggle-class="'.$option->attributes()->class.'">'.JText::_((string)$option).'</button>';
					}
					
					$return  = $return . '</div><input type="hidden" name="'.$field->attributes()->name.'" value="'.$field->attributes()->default.'" /></div></div>';
					break;
					
				case 'checkbox':
					
					break;
					
				case 'list':
					
					$return = $return . '<div class="control-group">';
					$return = $return . '<label class="control-label tooltip-hover" title="'.JText::_($field->attributes()->description).'">'.JText::_($field->attributes()->label).':</label>';
					$return = $return . '<div class="controls"><select name="'.$field->attributes()->name.'">';
					
					foreach ($field->children() as $option){
						
						$return = $return . '<option value="'.$option->attributes()->value.'">'.JText::_((string)$option).'</option>';
					}
					
					$return  = $return . '</div></div></div>';
					break;
					
				case 'textarea':
					
					break;
			}
		}
		
		return $return;
	}
}