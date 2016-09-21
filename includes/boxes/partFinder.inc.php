<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.15
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2005 - 2006. All rights reserved.
|   Devellion Limited,
|   22 Thomas Heskin Court,
|   Station Road,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 3EE
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Thursday, 4th January 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	categories.inc.php
|   ========================================
|	Categories Box	
+--------------------------------------------------------------------------
*/
phpExtension();

$results = $db->select("SELECT cat_name, cat_id FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = 0 ORDER BY disp_order ASC");

$box_content=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/partFinder.tpl");

$selected = '';
$categories = $db->select("SELECT cat_name, cat_id FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id > 0 AND cat_father_id = 0 ORDER BY cat_name ASC");
if($categories == TRUE){
	
	for($i = 0; $i < count($categories); $i++){
		if($_GET['category'] == $categories[$i]['cat_id']){
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		
		$box_content->assign("CATEGORIES",'<option value="'.$categories[$i]['cat_id'].'" '.$selected.'>'.$categories[$i]['cat_name'].'</option>');
		$box_content->parse("partFinder.categories");
	}
}

$makes = $db->select("SELECT id, make FROM ".$glob['dbprefix']."CubeCart_make WHERE id > 0 ORDER BY make ASC");
if($makes == TRUE){
	
	for($i = 0; $i < count($makes); $i++){
		if($_GET['make'] == $makes[$i]['id']){
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$box_content->assign("MAKES",'<option value="'.$makes[$i]['id'].'" '.$selected.'>'.$makes[$i]['make'].'</option>');
		$box_content->parse("partFinder.makes");
	}
}

if (isset($_GET['make'])) {
	$models = $db->select("SELECT model, id FROM {$glob['dbprefix']}CubeCart_model WHERE id > 0 AND make_id = {$db->mySQLSafe($_GET['make'])} ORDER BY model ASC");
}

if($models == TRUE){
	
	for($i = 0; $i < count($models); $i++){
		if($_GET['model'] == $models[$i]['id']){
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		
		$box_content->assign("MODELS",'<option value="'.$models[$i]['id'].'" '.$selected.'>'.$models[$i]['model'].'</option>');
		$box_content->parse("partFinder.models");
	}
}

unset($selected);














$box_content->parse("partFinder");
$box_content = $box_content->text("partFinder");