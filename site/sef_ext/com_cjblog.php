<?php
/**
 * @version		$Id: com_cjblog.php 01 2011-01-11 11:37:09Z maverick $
 * @package		CoreJoomla.cjblog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2013 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */

// ------------------ standard plugin initialize function - don't change ---------------------------
global $sh_LANG, $sefConfig; 
$shLangName = '';;
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
// ------------------ standard plugin initialize function - don't change ---------------------------

$database = JFactory::getDBO();
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');

if (!empty($Itemid)) shRemoveFromGETVarsList('Itemid');
if (!empty($limit))  shRemoveFromGETVarsList('limit');
if (isset($limitstart)) shRemoveFromGETVarsList('limitstart');

$shName = shGetComponentPrefix($option);
$shName = empty($shName) ? getMenuTitle($option, (isset($controller)?@$controller:(isset($view) ? @$view : null)), $Itemid ) : $shName;
if (!empty($shName) && $shName != '/') $title[] = $shName;  // V x

if (isset($task)) {
	
	$title[] = $task;
}

switch ($view){
	
	case 'profile':
		
		if(isset($id)) {
			
			$query =  'select alias from #__users where id = '.$id;
			$title[] = '';
		}	

		
		break;
		
}

shRemoveFromGETVarsList('task');
shRemoveFromGETVarsList('view');


// ------------------ standard plugin finalize function - don't change ---------------------------
if ($dosef){
	$string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
			(isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
			(isset($shLangName) ? @$shLangName : null));
}
// ------------------ standard plugin finalize function - don't change ---------------------------