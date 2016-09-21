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

|	saleItems.inc.php

|   ========================================

|	Sales Items Box	

+--------------------------------------------------------------------------

*/



phpExtension();



// query database

if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
	$saleItems = $db->select("SELECT name, productId, tradePrice as price, tradeSale as sale_price, price - sale_price as saving FROM ".$glob['dbprefix']."CubeCart_inventory WHERE price > sale_price AND sale_price > 0 ORDER BY saving DESC",$config['noSaleBoxItems']);
}else{
	$saleItems = $db->select("SELECT name, productId, price, sale_price, price - sale_price as saving FROM ".$glob['dbprefix']."CubeCart_inventory WHERE price > sale_price AND sale_price > 0 ORDER BY saving DESC",$config['noSaleBoxItems']);
}



if($saleItems == TRUE && $config['saleMode']>0){



$salePrice = 0;



$box_content=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/saleItems.tpl");



$box_content->assign("LANG_SALE_ITEMS_TITLE",$lang['front']['boxes']['sale_items']);



	for($i=0;$i<count($saleItems);$i++){

			

			

			

			if(($val = prodAltLang($saleItems[$i]['productId'])) == TRUE){

			

				$saleItems[$i]['name'] = $val['name'];

		

			}

			

			$salePrice = salePrice($saleItems[$i]['price'], $saleItems[$i]['sale_price']);

			$saleItems[$i]['name'] = validHTML($saleItems[$i]['name']);
			
			//MOD Popular Product Images - START
			if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$popularProds[$i]['image'])){
					$box_content->assign("PROD_IMG_SRC",$GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$popularProds[$i]['image']);
					} else {
					$box_content->assign("PROD_IMG_SRC",$GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif");
					}
			//MOD Popular Product Images - END
			

			$box_content->assign("DATA",$saleItems[$i]);

			$box_content->assign("SAVING",priceFormat($saleItems[$i]['price'] - $salePrice));
			
			$box_content->assign("PRICE",priceFormat($salePrice));

			$box_content->assign("LANG_SAVE",$lang['front']['boxes']['save']);

			$box_content->parse("sale_items.li");

	

	} // end loop

	

$box_content->parse("sale_items");

$box_content = $box_content->text("sale_items"); 



} else {

	

	$box_content = "";



}

?>