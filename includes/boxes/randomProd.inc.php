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

|	randomProduct.inc.php

|   ========================================

|	Random Product Box	

+--------------------------------------------------------------------------

*/



phpExtension();



mt_srand ((double) microtime() * 1000000); 

$seed = mt_rand(1,10000);



if(isset($_GET['catId'])){

	

	$whereClause = "WHERE cat_id=".$db->mySQLSafe($_GET['catId']);



} else {



	$whereClause = "";



}



$randProd= $db->select("SELECT name, image, productId FROM ".$glob['dbprefix']."CubeCart_inventory ".$whereClause." ORDER BY RAND(".$seed.") LIMIT 1");

 

if($randProd==TRUE){



		if(($val = prodAltLang($randProd[0]['productId'])) == TRUE){

			

			$randProd[0]['name'] = $val['name'];

		

		}

	

	$box_content=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/randomProd.tpl");

	

	$box_content->assign("LANG_RANDOM_PRODUCT",$lang['front']['boxes']['featured_prod']);

	$box_content->assign("PRODUCT_ID",$randProd[0]['productId']);

	$box_content->assign("PRODUCT_NAME",validHTML($randProd[0]['name']));

	

	if(!empty($randProd[0]['image'])){

		$box_content->assign("IMG_SRC","images/uploads/thumbs/thumb_".$randProd[0]['image']);

	} else {

		$box_content->assign("IMG_SRC","skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif");

	}

	

	$box_content->parse("random_prod");

	

	$box_content = $box_content->text("random_prod");



} else {



	$box_content = "";

	

}

?>