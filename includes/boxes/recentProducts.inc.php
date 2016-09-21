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

|	popularProducts.inc.php

|   ========================================

|	Display the most Popular Products	

+--------------------------------------------------------------------------

*/



phpExtension();



// query database
	
	
	
	$recentProdIds = explode('|', $_COOKIE['recentProds']);
	
	
	
	
	$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/recentProducts.tpl");
	
	
	
	$box_content->assign("LANG_RECENT_PRODUCTS_TITLE",$lang['front']['boxes']['recent_products']);
	
	
	
	if(isset($_COOKIE['recentProds']) && $recentProdIds == TRUE){
	
		// start loop
	
		for ($i=0; $i<count($recentProdIds); $i++){
	
			$recentProds = $db->select("SELECT name, image, productId FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = ".$db->mySQLSafe($recentProdIds[$i]));
	
			if($recentProds) {
		
				if(($val = prodAltLang($recentProds[0]['productId'])) == TRUE){
		
					
		
						$recentProds[0]['name'] = $val['name'];
		
				
		
				}
		
				$recentProds[0]['name'] = validHTML($recentProds[0]['name']);
				
		//MOD Popular Product Images - START
		if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$recentProds[0]['image'])){
				$box_content->assign("PROD_IMG_SRC",$GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$recentProds[0]['image']);
				} else {
				$box_content->assign("PROD_IMG_SRC",$GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif");
				}
		//MOD Popular Product Images - END
		
				$box_content->assign("DATA",$recentProds[0]);
		
				$box_content->parse("recent_products.li");
	
			}
	
		} // end loop
	
	} 
	
	$box_content->parse("recent_products");

	$box_content = $box_content->text("recent_products");

?>