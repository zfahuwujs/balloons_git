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

$popularProds = $db->select("SELECT name, image, productId FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 ORDER BY popularity DESC",$config['noPopularBoxItems']);



$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/popularProducts.tpl");



$box_content->assign("LANG_POPULAR_PRODUCTS_TITLE",$lang['front']['boxes']['popular_products']);



if($popularProds == TRUE){

	// start loop

	for ($i=0; $i<count($popularProds); $i++){

		

		if(($val = prodAltLang($popularProds[$i]['productId'])) == TRUE){

			

				$popularProds[$i]['name'] = $val['name'];

		

		}

		$popularProds[$i]['name'] = validHTML($popularProds[$i]['name']);
		
//MOD Popular Product Images - START
if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$popularProds[$i]['image'])){
		$box_content->assign("PROD_IMG_SRC",$GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$popularProds[$i]['image']);
		} else {
		$box_content->assign("PROD_IMG_SRC",$GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif");
		}
//MOD Popular Product Images - END

		$box_content->assign("DATA",$popularProds[$i]);

		$box_content->parse("popular_products.li");

	

	} // end loop

} 

$box_content->parse("popular_products");

$box_content = $box_content->text("popular_products");

?>