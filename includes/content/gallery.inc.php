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
|	viewDoc.inc.php
|   ========================================
|	Displays a site document	
+--------------------------------------------------------------------------
*/
// query database

phpExtension();


$view_gallery = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/gallery.tpl");

$galImages = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images AS images INNER JOIN ".$glob['dbprefix']."CubeCart_imgcat_idx AS idx ON images.imgId=idx.imgId WHERE idx.catId=6");
if($galImages==true){
	for($i=0; $i<count($galImages); $i++){
		$view_gallery->assign("IMG_SRC", $galImages[$i]['imgLoc']);
		$view_gallery->assign("IMG_SRC_THUMB", $galImages[$i]['thumbLoc']);
		if($config['galleryType']==1){
			$view_gallery->parse("view_gallery.galleria.images");
		}else{
			$view_gallery->parse("view_gallery.lightbox.images");
		}
	}
	if($config['galleryType']==1){
		$view_gallery->parse("view_gallery.galleria");
	}else{
		$view_gallery->parse("view_gallery.lightbox");
	}
}


$view_gallery->parse("view_gallery");
$page_content = $view_gallery->text("view_gallery");
?>