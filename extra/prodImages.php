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
|	prodImages.php
|   ========================================
|	Popup image gallery for the product	
+--------------------------------------------------------------------------
*/
// INCLUDE CORE VARIABLES & FUNCTIONS
include_once("../includes/global.inc.php");

	
// initiate db class
include_once("../classes/db.inc.php");
$db = new db();

include_once("../includes/functions.inc.php");
$config = fetchDbConfig("config");

// get session data
include_once("../includes/sessionStart.inc.php");
include_once("../includes/session.inc.php");

	$lang_folder = "";
	if(empty($ccUserData[0]['lang'])){
		$lang_folder = $config['defaultLang'];
	} else {
		$lang_folder = $ccUserData[0]['lang'];
	}
	include_once("../language/".$lang_folder."/lang.inc.php");
	
// require template class
include_once("../classes/xtpl.php");

// query database
$_GET['productId'] = treatGet($_GET['productId']);
$results = $db->select("SELECT img FROM ".$glob['dbprefix']."CubeCart_img_idx WHERE productId = ".$db->mySQLsafe($_GET['productId']));

$mainImage = $db->select("SELECT image FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = ".$db->mySQLsafe($_GET['productId']));

$prod_images = new XTemplate ("../skins/".$config['skinDir']."/styleTemplates/popup/prodImages.tpl");

$prod_images->assign("META_TITLE",$config['siteTitle'].c().$meta['siteTitle']);

$prod_images->assign("VAL_ISO",$charsetIso);

$prod_images->assign("VAL_SKIN",$config['skinDir']);

if($results == TRUE){

	// start loop
	
	for ($i=0; $i<count($results); $i++){
 		
		$prod_images->assign("VALUE_SRC","../images/uploads/".$results[$i]['img']);
		
		if(file_exists("../images/uploads/thumbs/thumb_".$results[$i]['img'])){
			
			$prod_images->assign("VALUE_THUMB_SRC","../images/uploads/thumbs/thumb_".$results[$i]['img']);
			$sizeThumb = getimagesize("../images/uploads/thumbs/thumb_".$results[$i]['img']);
			$prod_images->assign("VALUE_THUMB_WIDTH",$sizeThumb[0]);
			
		} else {
			
			$prod_images->assign("VALUE_THUMB_SRC","../images/uploads/".$results[$i]['img']);
			$prod_images->assign("VALUE_THUMB_WIDTH",$config['gdthumbSize']);
			
			
		}
		$prod_images->assign("ALT_THUMB",$lang['front']['popup']['thumb_alt']);
		$prod_images->parse("prod_images.thumbs");
	
	} // end loop 
	
	
		// original image
		
		$prod_images->assign("VALUE_SRC","../images/uploads/".$mainImage[0]['image']);
		
		if(file_exists("../images/uploads/thumbs/thumb_".$mainImage[0]['image'])){
			
			$prod_images->assign("VALUE_THUMB_SRC","../images/uploads/thumbs/thumb_".$mainImage[0]['image']);
			$sizeThumb = getimagesize("../images/uploads/thumbs/thumb_".$mainImage[0]['image']);
			$prod_images->assign("VALUE_THUMB_WIDTH",$sizeThumb[0]);
			
		} else {
			
			$prod_images->assign("VALUE_THUMB_SRC","../images/uploads/".$mainImage[0]['image']);
			$prod_images->assign("VALUE_THUMB_WIDTH",$config['gdthumbSize']);
			
		}
		$prod_images->assign("ALT_THUMB",$lang['front']['popup']['thumb_alt']);
		$prod_images->parse("prod_images.thumbs");
	
}
$size = getimagesize("../images/uploads/".$mainImage[0]['image']);

$prod_images->assign("VALUE_MASTER_SRC","../images/uploads/".$mainImage[0]['image']);
$prod_images->assign("ALT_LARGE",$lang['front']['popup']['large_alt']);

$prod_images->parse("prod_images");
$prod_images->out("prod_images");
?>