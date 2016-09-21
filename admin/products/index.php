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

|	index.php

|   ========================================

|	Add/Edit/Delete Products	

+--------------------------------------------------------------------------

*/

include("../../includes/ini.inc.php");
include("../../includes/global.inc.php");
require_once("../../classes/db.inc.php");
$db = new db();
include_once("../../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include("../../includes/sslSwitch.inc.php");
include("../includes/auth.inc.php");
include("../../includes/sef_urls.inc.php");
include("../includes/rte/fckeditor.php");
include("../../classes/gd.inc.php");
include("../../includes/currencyVars.inc.php");
if(permission("products","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}
$productsPerPage = 25;

if(isset($_GET['action']) && $_GET['action']=="gbase"){
	$db = new db();
	$query = "SELECT inv.price, inv.sale_price, inv.productCode, inv.productId, inv.name, inv.description, inv.prodWeight, inv.condition, inv.brand, manufacturerCode, productCodeAsc FROM ".$glob['dbprefix']."CubeCart_inventory inv WHERE showProd = 1 ORDER BY name ASC";

$results = $db->select($query);
if($results==TRUE) {
	$googleBaseContent = "id\tlink\ttitle\tdescription\timage_link\tprice\tweight\tcondition\tBrand\tMPN\tGTIN\tshipping\r\n";
		
	for($g=0; $g<count($results); $g++){
		$salePrice = salePrice($results[$g]['price'], $results[$g]['sale_price']);
		if($salePrice > 0){
			$price = $salePrice;
		} else {
			$price = $results[$i]['price'];
		}	
		$shippingModules = $db->select("SELECT folder FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='shipping' AND status = 1");
		$noItems = 1;
		$sum = 0;
		$shippingPrice = 0;
		$shipRegion = 0;
		$shippingRate = null;
		if($shippingModules == TRUE){
			for($s=0; $s<count($shippingModules); $s++){
				$shipKey++;
				include("../../modules/shipping/".$shippingModules[$s]['folder']."/calc.php"); 
				$displacer = 0;
				while(empty($methodData[$displacer]["sumMethod"]) || !is_numeric($methodData[$displacer]["sumMethod"])){
					$displacer++;
				}
				$shippingPrice = $methodData[$displacer]["sumMethod"];
				$shipRegion = $basket['delInf']['country'];
				$shippingRate = $basket['delInf']['country'].':::'.$shippingPrice;
			}
		}
		
			
		$name = str_replace(array("&nbsp;","\t","\r","\n","\0","\x0B","
		"),"",strip_tags($results[$g]['name']));
		$name = str_replace("  ","",$name);
		$desc = str_replace(array("&nbsp;","\t","\r","\n","\0","\x0B","
		"),"",strip_tags($results[$g]['description']));
		$desc = str_replace("  ","",$desc);
		// SEO friendly mod
		if($config['sef'] == 0) {
		$googleBaseContent .= $results[$g]['productId']."\t".$glob['storeURL']."/index.php?act=viewProd&productId=".$results[$g]['productId']."\t".$name."\t".$desc;
		} else {
		include_once($web."includes/sef_urls.inc.php");
		$googleBaseContent .= $results[$g]['productId']."\t".$glob['storeURL']."/".sef_get_base_url(). generateProductUrl($results[$g]['productId'])."\t".$name."\t".$desc;
		}
		// SEO friendly mod
			
			
		if($results[$g]['image'])
			$googleBaseContent .= "\t".$glob['storeURL']."/images/uploads/".$results[$g]['image'];
		else
			$googleBaseContent .= "\t".$glob['storeURL']."/skins/".$config['skinDir']."/styleImages/nophoto.gif";
			
		$googleBaseContent .= "\t".$price."\t".$results[$g]['prodWeight']."\t".$results[$g]['condition']."\t".$results[$g]['brand']."\t".$results[$g]['manufacturerCode']."\t".$results[$g]['productCodeAsc']."\t".$shippingRate."\r\n";
		
		set_time_limit ($timeout);
	}
		
		$filename = "GoogleBaseFeed";
		$contentLength = strlen($googleBaseContent);
		$content = $googleBaseContent;
		$filename .=".txt";
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header("Content-Disposition: attachment; filename=".$filename);
		header("Content-type: text/plain");
		header("Content-type: application/octet-stream");
		header("Content-length: ".$contentLength);
		header("Content-Transfer-Encoding: binary");
		echo $content;
		exit;
	}
} elseif(isset($_GET['delete']) && $_GET["delete"]>0){

	/*	BEGIN BRANDS MOD DELETE	*/
	$old_product = $db->select ( "SELECT productBrand FROM " . $glob['dbprefix'] . "CubeCart_inventory WHERE productId = " . $db->mySQLSafe ( $_GET['delete'] ) );
	$count = $db->select ( "SELECT noProducts FROM " . $glob['dbprefix'] . "CubeCart_brands WHERE id = " . $old_product[0]['productBrand']  );
	$update_record['noProducts'] = ( (int) $count[0]['noProducts'] ) - 1;
	$where = "id = " . $db->mySQLSafe ( $old_product[0]['productBrand'] );
	$process = $db->update ( $glob['dbprefix']."CubeCart_brands", $update_record, $where );
	unset ( $update_record, $where, $process );
	/*	END BRANDS MOD DELETE	*/

	// delete product
	$where = "productId=".$db->mySQLSafe($_GET["delete"]);
	$delete = $db->delete($glob['dbprefix']."CubeCart_inventory", $where);
	// delete coresponding product reviews 
	$del_rev = $_GET["delete"] ;
    $delete2 = "DELETE FROM ".($glob['dbprefix']."CubeCart_store_ratings WHERE product_id=$del_rev");
    $del= mysql_query($delete2);

	// set categories -1
	$cats = $db->select("SELECT cat_id FROM ".$glob['dbprefix']."CubeCart_cats_idx WHERE productId=".$db->mySQLSafe($_GET["delete"]));
	if($cats==TRUE){
		for($i=0;$i<count($cats);$i++){
			$db->categoryNos($cats[$i]['cat_id'], "-");
		}
	}

	// delete category index
	$where = "productId=".$db->mySQLSafe($_GET["delete"]);  
	$deleteIdx = $db->delete($glob['dbprefix']."CubeCart_cats_idx", $where);
	unset($record);

	// delete product options
	$record['product'] = $db->mySQLSafe($_GET["delete"]);
	$where = "product=".$db->mySQLSafe($_GET["delete"]);  
	$deleteOps = $db->delete($glob['dbprefix']."CubeCart_options_bot", $where);
	unset($record);

	if($delete == TRUE){
		$msg = "<p class='infoText'>".$lang['admin']['products']['delete_success']."</p>";
		/// Robz Sitemap Addition Part #1
		$delRec["type"] = $db->mySQLSafe("Product");
		$delRec["aspect_id"] = $db->mySQLSafe($_GET['delete']);
		$delRec["action"] = $db->mySQLSafe("Delete");
		$insert = $db->insert($glob['dbprefix']."CubeCart_mods", $delRec);
		/// END Part #1
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['products']['delete_fail']."</p>";
	}

} elseif(isset($_POST['productId'])) {
	//option stock
	$currentStock = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE productId = '.$db->mySQLSafe($_POST['productId']));
	if(isset($_POST['optStockId'])){
		$dbSave=0;
		for($i=0; $i<count($_POST['optStockId']); $i++){
			$optRec["stock"] = $db->mySQLSafe($_POST['optStock'][$i]);
			$optRec["options"] = $db->mySQLSafe($_POST['optStockId'][$i]);
			$optRec["prodId"] = $db->mySQLSafe($_POST['productId']);
			$chkExists = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_options_stock WHERE options=".$db->mySQLSafe($_POST['optStockId'][$i]));
			if($chkExists){
				$update = $db->update($glob['dbprefix']."CubeCart_options_stock", $optRec, "options=".$db->mySQLSafe($_POST['optStockId'][$i]));
				if($update==true && $chkExists[0]['stock'] < $_POST['optStock'][$i]){
					$notifyWithOpt = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_stock_notify 
																				WHERE productId = '.$db->mySQLSafe($_POST['productId'])." 
																				AND prodOptions = ".$db->mySQLSafe($_POST['optStockId'][$i]));
					
					if($notifyWithOpt==true){
						include_once($_SERVER['DOCUMENT_ROOT']."/classes/htmlMimeMail.php");
						$mail = new htmlMimeMail();
						foreach($notifyWithOpt as $item){
							$text = 'The product you were interested "'.$currentStock[0]['name'].'" is back in stock.'."\r\nTo view the product please click the following link: ".$glob['storeURL'].$glob['rootRel'].generateProductUrl($currentStock[0]['productId']);
							$mail->setText($text);
							$mail->setReturnPath($config['masterEmail']);
							$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
							$mail->setSubject('Back in stock');	
							$mail->setHeader('X-Mailer', 'CubeCart Mailer');
							$send = $mail->send(array($item['email']), $config['mailMethod']);
							$db->delete($glob['dbprefix'].'CubeCart_stock_notify','id = '.$db->mySQLSafe($item['id']),1);
						}
					}
				}
			}else{
				$insert = $db->insert($glob['dbprefix']."CubeCart_options_stock", $optRec);
			}
			if($update==true || $insert==true){
				$dbSave=1;
			}
		}
	}
	
// generate product code
	if(empty($_POST['productCode'])){
		$chars = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","1","2","3","4","5","6","7","8","9","0");
		$max_chars = count($chars) - 1;
		srand((double)microtime()*1000000);
		for($i = 0; $i < 5; $i++){
			$randChars = ($i == 0) ? $chars[rand(0, $max_chars)] : $randnum . $chars[rand(0, $max_chars)];
		}
		$record["productCode"] = $db->mySQLSafe(strtoupper(substr($_POST['name'],0,3)).$randChars.$_POST['cat_id']);
	} else {
		$record["productCode"] = $db->mySQLSafe($_POST['productCode']);	
	}
	$record["name"] = $db->mySQLSafe($_POST['name']);		
	$record["cat_id"] = $db->mySQLSafe($_POST['cat_id']);	
	$record["short_description"] = $db->mySQLSafe($_POST['FCKeditor2']);
	$record["description"] = $db->mySQLSafe($_POST['FCKeditor']);
	$record["image"] = $db->mySQLSafe(preg_replace('/thumb_/i', '', $_POST['image']));
	$record["tradePrice"] = $db->mySQLSafe($_POST['tradePrice']);
	$record["tradeSale"] = $db->mySQLSafe($_POST['tradeSale']); 
	$record["price"] = $db->mySQLSafe($_POST['price']);  
	$record["cost_price"] = $db->mySQLSafe($_POST['cost_price']);  
	$record["profit_margin"] = $db->mySQLSafe($_POST['profit_margin']);
	$record["sale_price"] = $db->mySQLSafe($_POST['sale_price']);
	
	if(isset($_POST['productId']) && $_POST['productId'] > 0){
		$currentStock = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE productId = '.$db->mySQLSafe($_POST['productId']));
		if($currentStock==true){
			if($currentStock[0]['stock_level'] < $_POST['stock_level']){
				$notify = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_stock_notify WHERE productId = '.$db->mySQLSafe($_POST['productId'])." AND prodOptions = ".$db->mySQLSafe(0));
			}
		}
	}
	
	$record["stock_level"] = $db->mySQLSafe($_POST['stock_level']); 
	$record["useStockLevel"] = $db->mySQLSafe($_POST['useStockLevel']);
	$record["digital"] = $db->mySQLSafe($_POST['digital']);
	$record["digitalDir"] = $db->mySQLSafe($_POST['digitalDir']);
	$record["prodWeight"] = $db->mySQLSafe($_POST['prodWeight']);
	$record["taxType"] = $db->mySQLSafe($_POST['taxType']); 
	$record["featured"] = $db->mySQLSafe($_POST['featured']);
	$record["latest"] = $db->mySQLSafe($_POST['latest']);
	$record["popular"] = $db->mySQLSafe($_POST['popular']);
	$record["sale"] = $db->mySQLSafe($_POST['sale']);
	$record["brand"] = $db->mySQLSafe($_POST['gBrand']);
	$record["manufacturerCode"] = $db->mySQLSafe($_POST['gManufacturerCode']);
	$record["productCodeAsc"] = $db->mySQLSafe($_POST['gProductCodeAsc']);
	$record["purMeth"] = $db->mySQLSafe($_POST['purMeth']);
	//$record["bestseller"] = $db->mySQLSafe($_POST['bestseller']);
	$record["showProd"] = $db->mySQLSafe($_POST['showProd']);
	$record["condition"] = $db->mySQLSafe($_POST['condition']);
	if(is_array($_POST['forModels'])){
		$forModelz = $_POST['forModels'];
		$record["forModels"] = implode(' ',$forModelz);
	}

	$record["forModels"] = $db->mySQLSafe($record['forModels']);
	
	
	/*	BEGIN BRANDS MOD UPDATE/INSERT	*/
	$record["productBrand"] = $db->mySQLSafe( $_POST['brand'] );
	if ( $_POST['oldBrand'] !== $_POST['brand'] && !isset ( $_GET['mode'] ) ){
		$old_no_products = $db->select ( "SELECT noProducts FROM " . $golb['dbprefix'] . "CubeCart_brands WHERE id = " . $db->mySQLSafe ( $_POST['oldBrand'] ) );
		$old_no_products = (int) $old_no_products[0]['noProducts'];
		if ( $_POST['oldBrand'] !== 0 ){
			$update_record['noProducts'] = $old_no_products - 1;
			$where = "id = " . $db->mySQLSafe ( $_POST['oldBrand'] );
			$process = $db->update ( $glob['dbprefix']."CubeCart_brands", $update_record, $where );
			unset ( $old_no_products, $update_record, $where, $process );
		}
		$new_no_products = $db->select ( "SELECT noProducts FROM " . $golb['dbprefix'] . "CubeCart_brands WHERE id = " . $db->mySQLSafe ( $_POST['brand'] ) );
		$new_no_products = (int) $new_no_products[0]['noProducts'];
		$update_record['noProducts'] = $new_no_products + 1;
		$where = "id = " . $db->mySQLSafe ( $_POST['brand'] );
		$process = $db->update ( $glob['dbprefix']."CubeCart_brands", $update_record, $where );
		unset ( $update_record, $where, $process );
	}
	/*	END BRANDS MOD UPDATE/INSERT	*/

	/* <rf> search engine friendly url mod */
	if($config['seftags']) {
		$record["prod_metatitle"] = $db->mySQLSafe($_POST['prod_metatitle']); 
		$record["prod_metadesc"] = $db->mySQLSafe($_POST['prod_metadesc']); 
		$record["prod_metakeywords"] = $db->mySQLSafe($_POST['prod_metakeywords']); 
		if($config['sefcustomurl'] == 1) $record["prod_sefurl"] = $db->mySQLSafe($_POST['prod_sefurl']); 		
	}
    /* <rf> end mod */

	// if image is a JPG check thumbnail doesn't exist and if not make one
	$imageFormat = strtoupper(ereg_replace(".*\.(.*)$","\\1",$_POST['imageName']));
	if($imageFormat == "JPG" || $imageFormat == "JPEG" || $imageFormat == "PNG" || ($imageFormat == "GIF" && $config['gdGifSupport']==1)){
		if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$_POST['imageName'])){
			@chmod($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$_POST['imageName'], 0775);
			unlink($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$_POST['imageName']);
		}
		$thumb=new thumbnail($GLOBALS['rootDir']."/images/uploads/".$_POST['imageName']);
		$thumb->size_auto($config['gdthumbSize']);
		$thumb->jpeg_quality($config['gdquality']);
		$thumb->save($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$_POST['imageName']);
	}

	if(isset($_POST['productId']) && $_POST['productId']>0) {
		$where = "productId=".$db->mySQLSafe($_POST['productId']);
		$update = $db->update($glob['dbprefix']."CubeCart_inventory", $record, $where);
		unset($record, $where);
		
		if($notify==true && $update==true && $currentStock==true){
			include_once($_SERVER['DOCUMENT_ROOT']."/classes/htmlMimeMail.php");
			$mail = new htmlMimeMail();
			foreach($notify as $item){
				$text = 'The product you were interested "'.$currentStock[0]['name'].'" is back in stock.'."\r\nTo view the product please click the following link: ".$glob['storeURL'].$glob['rootRel'].generateProductUrl($currentStock[0]['productId']);
				$mail->setText($text);
				$mail->setReturnPath($config['masterEmail']);
				$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
				$mail->setSubject('Back in stock');	
				$mail->setHeader('X-Mailer', 'CubeCart Mailer');
				$send = $mail->send(array($item['email']), $config['mailMethod']);
				$db->delete($glob['dbprefix'].'CubeCart_stock_notify','id = '.$db->mySQLSafe($item['id']),1);
			}
		}
		
		
		// update category count
		if($_POST['oldCatId']!==$_POST['cat_id']){
			// set old category -1 IF IT WAS IN THERE BEFORE
			$numOldCat = $db->numrows("SELECT * FROM ".$glob['dbprefix']."CubeCart_cats_idx WHERE cat_id = ".$db->mySQLSafe($_POST['oldCatId'])." AND productId = ".$db->mySQLSafe($_POST['productId']));
			if($numOldCat>0){
				$db->categoryNos($_POST['oldCatId'], "-");
			}
			// set new category +1 IF IT WAS NOT IN THERE BEFORE
			$numNewCat = $db->numrows("SELECT * FROM ".$glob['dbprefix']."CubeCart_cats_idx WHERE cat_id = ".$db->mySQLSafe($_POST['cat_id'])." AND productId = ".$db->mySQLSafe($_POST['productId']));
			if($numNewCat == 0) {
				$db->categoryNos($_POST['cat_id'], "+");
			}
			// delete old idx
			$where = "productId = ".$db->mySQLSafe($_POST['productId'])." AND cat_id = ".$db->mySQLSafe($_POST['oldCatId']);  
			$deleteIdx = $db->delete($glob['dbprefix']."CubeCart_cats_idx", $where);
			unset($record);
			// delete new index if it was added as an extra before
			$where = "productId = ".$db->mySQLSafe($_POST['productId'])." AND cat_id = ".$db->mySQLSafe($_POST['cat_id']);  
			$deleteIdx = $db->delete($glob['dbprefix']."CubeCart_cats_idx", $where);
			unset($record);
			// add new idx
			$record['productId'] = $db->mySQLSafe($_POST['productId']);
			$record['cat_id'] = $db->mySQLSafe($_POST['cat_id']);  
			$insertIdx = $db->insert($glob['dbprefix']."CubeCart_cats_idx", $record);
			unset($record);
		}
		if($update == TRUE){
			$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products']['update_successful']."</p>";
			/// Robz Sitemap Addition Part #2
			$updRec["type"] = $db->mySQLSafe("Product");
			$updRec["aspect_id"] = $db->mySQLSafe($_POST['productId']);
			$updRec["action"] = $db->mySQLSafe("Update");
			$insert = $db->insert($glob['dbprefix']."CubeCart_mods", $updRec);
			/// END Part #2
			/// Robz Sitemap Addition Part #5
			$modRec["lastModified"] = $db->mySQLSafe(time());
			$update = $db->update($glob['dbprefix']."CubeCart_inventory", $modRec, "productId = ".$_POST['productId']);
			/// END Part #5
		}elseif($dbSave==1){
			$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products']['add_success']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products']['update_fail']."</p>";
		}
	} else {
		/// Robz Sitemap Addition Part #4
		$record["lastModified"] = $db->mySQLSafe(time());
		/// END Part #4
		$insert = $db->insert($glob['dbprefix']."CubeCart_inventory", $record);
		unset($record);
		$record['cat_id'] = $db->mySQLSafe($_POST['cat_id']);
		$record['productId'] = $db->insertid();  
		$insertIdx = $db->insert($glob['dbprefix']."CubeCart_cats_idx", $record);
		unset($record);
		if($insert == TRUE){
			$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products']['add_success']."</p>";
			/// Robz Sitemap Addition Part #3
			$insRec["type"] = $db->mySQLSafe("Product");
			$insRec["aspect_id"] = $db->mySQLSafe(mysql_insert_id());
			$insRec["action"] = $db->mySQLSafe("Insert");
			$insert = $db->insert($glob['dbprefix']."CubeCart_mods", $insRec);
			/// END Part #3
			// notch up amount of products in category
			$db->categoryNos($_POST['cat_id'], "+");
		}elseif($dbSave==1){
			$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products']['add_success']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products']['add_fail']."</p>";
		}
	}
}elseif(isset($_POST["delete_mass"])){
	$msg = "";
	$mass_error = 0;
	$deletions = $_POST["mass_delete"];
	for($i=0; $i<count($deletions); $i++){
		echo count($deletions);
		echo ' - '.$i;
		$where = "productId = ".$deletions[$i];
		$delete = $db->delete($glob['dbprefix']."CubeCart_inventory", $where);
		if($delete != TRUE){
 			$msg .= "<p class='warnText'>An error occurred while deleting product number: ".$deletions[$i]."</p>";
		}
 		$cats = $db->select("SELECT cat_id FROM ".$glob['dbprefix']."CubeCart_cats_idx WHERE productId=".$deletions[$i]);
		if($cats==TRUE){
			for($j=0; $j<count($cats); $j++){
				$db->categoryNos($cats[$j]['cat_id'], "-");
			}
		}
		// delete category index
		$where = "productId = ".$deletions[$i];
		$deleteIdx = $db->delete($glob['dbprefix']."CubeCart_cats_idx", $where);
		if($deleteIdx != TRUE){
 			$msg .= "<p class='warnText'>An error occurred while deleting category indexes for product number: ".$deletions[$i]."</p>";
		}
		// delete product options
		$where = "product = ".$deletions[$i];
		$deleteOps = $db->delete($glob['dbprefix']."CubeCart_options_bot", $where);
		if($deleteOps != TRUE){
 			$msg .= "<p class='warnText'>An error occurred while deleting options for product number: ".$deletions[$i]."</p>";
		}
	}
	if($mass_error == 0){
		$msg = "<p class='infoText'>All selected products have been successfully deleted</p>";
		/// Robz Sitemap Addition Part #6
		$delRec["type"] = $db->mySQLSafe("Product");
		$delRec["aspect_id"] = $db->mySQLSafe(0);
		$delRec["action"] = $db->mySQLSafe("Delete");
		$insert = $db->insert($glob['dbprefix']."CubeCart_mods", $delRec);
		/// END Part #6
	}
}
if(!isset($_GET['mode'])){
	// make sql query
	if(isset($_GET['edit']) && $_GET['edit']>0 OR isset($_GET['clone']) && $_GET['clone']>0){
		$product = (isset($_GET['edit']) && $_GET['edit']>0) ? $db->mySQLSafe($_GET['edit']) : $db->mySQLSafe($_GET['clone']);
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = $product"); 
	} else {
		if(isset($_GET['orderCol']) && !empty($_GET['orderCol']) && isset($_GET['orderDir']) && !empty($_GET['orderDir'])){
			$orderBy =  $glob['dbprefix']."CubeCart_inventory.".$_GET['orderCol']." ".$_GET['orderDir'];
		} else {
			$orderBy = $glob['dbprefix']."CubeCart_inventory.productId ASC";
		}
		$whereClause = "";
		if(isset($_GET['searchStr']) && !empty($_GET['searchStr'])){
		$searchwords = split ( "[ ,]", $_GET['searchStr']);   
		foreach($searchwords as $word){
			$searchArray[]=$word;
		}
		$noKeys = count($searchArray);
		for ($i=0; $i<$noKeys;$i++) {
			$ucSearchTerm = strtoupper($searchArray[$i]);
			if(($ucSearchTerm!=="AND")AND($ucSearchTerm!=="OR")){
				$like .= "(name LIKE '%".$searchArray[$i]."%' OR description LIKE '%".$searchArray[$i]."%' OR  productCode LIKE '%".$searchArray[$i]."%') OR ";
			} else {
				$like = substr($like,0,strlen($like)-3);
				$like .= $ucSearchTerm;
			}  
		}
		$like = substr($like,0,strlen($like)-3);
		$whereClause .= "WHERE ".$like;
	}
	if(isset($_GET['category']) && $_GET['category']>0){
		if(isset($like)){
			$whereClause .= " AND ";
		} else {
			$whereClause .= " WHERE ";
		}
		$whereClause .= $glob['dbprefix']."CubeCart_inventory.cat_id = ".$_GET['category']; 
	}
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory INNER JOIN ".$glob['dbprefix']."CubeCart_category on ".$glob['dbprefix']."CubeCart_inventory.cat_id = ".$glob['dbprefix']."CubeCart_category.cat_id ".$whereClause." ORDER BY ".$orderBy;
	} 
	// query database
	if(isset($_GET['page'])){
		$page = $_GET['page'];
	} else {
		$page = 0;
	}
	$results = $db->select($query, $productsPerPage, $page);
	$results[0]['forModels'] = split(' ', $results[0]['forModels']);
	$numrows = $db->numrows($query);
	$pagination = $db->paginate($numrows, $productsPerPage, $page, "page");
}
	$query = "SELECT cat_id, cat_name, cat_father_id FROM ".$glob['dbprefix']."CubeCart_category ORDER BY cat_id DESC";
	$categoryArray = $db->select($query);
	
	$modelsArray= $db->select("SELECT {$glob['dbprefix']}CubeCart_model.id, make, model FROM {$glob['dbprefix']}CubeCart_model LEFT JOIN {$glob['dbprefix']}CubeCart_make ON {$glob['dbprefix']}CubeCart_make.id = {$glob['dbprefix']}CubeCart_model.make_id ORDER BY make ASC, model ASC");
	
	include("../includes/header.inc.php"); 
?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle"><?php echo $lang['admin']['products']['prod_inventory'];?></p></td>
    <?php if(!isset($_GET["mode"])){ ?>
    <td align="right" valign="middle"><a <?php if(permission("products","write")==TRUE){ ?>href="?mode=new" <?php } else { echo $link401; } ?> class="addNew"><?php echo $lang['admin']['add_new'];?></a></td>
    <?php } ?>
  </tr>
  <tr align="right">
    <td colspan="2" nowrap='nowrap'><a href="index.php?action=gbase"><img src="../images/googleBase.gif" alt="Download Google Base Product Feed" width="76" height="28" border="0" title="" /></a></td>
  </tr>
</table>
<br />
<div style="float:right;"><a href="/pdf/Google_Base_Feed_Instructions.pdf" class="txtLink" target="_blank">Google Base Feed Instructions</a></div>
<br clear="all" />
<?php if(isset($msg)){ echo stripslashes($msg); }?>
<?php

if(!isset($_GET['mode']) && !isset($_GET['edit']) && !isset($_GET['clone'])){

?>
<?php if($results == TRUE){ ?>
<p class="copyText"><?php echo $lang['admin']['products']['current_prods_in_db'];?></p>
<form name="filter" method="get" action="<?php echo $GLOBALS['rootRel'];?>admin/products/index.php">
  <p align="right" class="copyText">
    <select name="category" class="textbox">
      <option value="All" <?php if(isset($_GET['category']) && $_GET['category']=="All") echo "selected='selected'"; ?>><?php echo $lang['admin']['products']['all_cats'];?></option>
      <?php for ($i=0; $i<count($categoryArray); $i++){ ?>
      <option value="<?php echo $categoryArray[$i]['cat_id']; ?>" <?php if(isset($_GET['category']) && $categoryArray[$i]['cat_id']==$_GET['category']) echo "selected='selected'"; ?>><?php echo getCatDir($categoryArray[$i]['cat_name'],$categoryArray[$i]['cat_father_id'], $categoryArray[$i]['cat_id']); ?></option>
      <?php } ?>
    </select>
    by
    <select name="orderCol" class="textbox">
      <option value="name" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=="name") echo "selected='selected'";?>><?php echo $lang['admin']['products']['prod_name'];?></option>
      <option value="productId" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=="productId") echo "selected='selected'";?>><?php echo $lang['admin']['products']['prod_id'];?></option>
      <option value="productCode" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=="productCode") echo "selected='selected'";?>><?php echo $lang['admin']['products']['prod_code'];?></option>
      <option value="cat_id" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=="cat_id") echo "selected='selected'";?>><?php echo $lang['admin']['products']['master_cat2'];?></option>
      <option value="stock_level" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=="stock_level") echo "selected='selected'";?>><?php echo $lang['admin']['products']['stock_level'];?></option>
      <option value="price" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=="price") echo "selected='selected'";?>><?php echo $lang['admin']['products']['normal_price'];?></option>
      <option value="sale_price" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=="sale_price") echo "selected='selected'";?>><?php echo $lang['admin']['products']['sale_price'];?></option>
    </select>
    in
    <select name="orderDir" class="textbox">
      <option value="ASC" <?php if(isset($_GET['orderDir']) && $_GET['orderDir']=="ASC") echo "selected='selected'";?>><?php echo $lang['admin']['products']['asc'];?></option>
      <option value="DESC" <?php if(isset($_GET['orderDir']) && $_GET['orderDir']=="DESC") echo "selected='selected'";?>><?php echo $lang['admin']['products']['desc'];?></option>
    </select>
    <?php echo $lang['admin']['products']['containing_text'];?>
    <input type="text" name="searchStr" class="textbox" value="<?php if(isset($_GET['searchStr']))echo $_GET['searchStr']; ?>" />
    <input name="submit" type="submit" value="<?php echo $lang['admin']['products']['filter'];?>" class="submit" />
    <input name="Button" type="button" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="<?php echo $lang['admin']['products']['reset'];?>" class="submit" />
  </p>
</form>
<?php } ?>
<p class="copyText"><?php echo $pagination; ?></p>
<form name="m_delete" method="post" action="<?php echo $GLOBALS['rootRel'];?>admin/products/index.php">
<p class="copyText">
  <input type="submit" name="delete_mass" class="submit" value="Delete Products" />
</p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
    <td align="center" class="tdTitle">Delete?</td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['products']['id'];?></td>
    <td align="center" class="tdTitle <?php evoHideAlt(86); ?>"><?php echo $lang['admin']['products']['type'];?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['products']['prod_code'];?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['products']['name'];?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['products']['master_cat2'];?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['products']['image'];?></td>
    <td align="center" class="tdTitle <?php evoHideAlt(96); ?>"><?php echo $lang['admin']['products']['price_sale_price'];?></td>
    <td align="center" class="tdTitle <?php evoHideAlt(31); ?>">Related Products</td>
    <td align="center" class="tdTitle">Show/Hide</td>
    <td class="tdTitle" align="center">Edit</td>
    <td class="tdTitle" align="center">Delete</td>
    <td class="tdTitle" align="center">Clone</td>
    <td class="tdTitle <?php evoHideAlt(102); ?>" align="center" width="10%">Language</td>
  </tr>
  <?php 

  if($results == TRUE){

  	$ecat = $_REQUEST['category'];
    $eocol = $_REQUEST['orderCol'];
    $eodir = $_REQUEST['orderDir'];
    $esstr = $_REQUEST['searchStr'];
    $esub = $_REQUEST['submit'];

	for ($i=0; $i<count($results); $i++){ 

  	

	$cellColor = "";

	$cellColor = cellColor($i);

  ?>
  <tr>
    <td align="center" class="<?php echo $cellColor; ?>"><input type="checkbox" name="mass_delete[]" value="<?php echo $results[$i]['productId']; ?>" /></td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['productId']; ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>  <?php evoHideAlt(86); ?>"><img src="../images/productIcon<?php echo $results[$i]['digital'];?>.gif" alt="" width="16" height="16" title="" /></td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['productCode']; ?></span></td>
    <td align="left" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['name']; ?></span></td>
    <td class="<?php echo $cellColor; ?>"><span class="txtDir"><?php echo getCatDir($results[$i]['cat_name'],$results[$i]['cat_father_id'], $results[$i]['cat_id']);?></span><br />
      <a href="javascript:;" <?php if(permission("products","edit")==TRUE){ ?>onclick="openPopUp('<?php echo $GLOBALS['rootRel']; ?>admin/products/extraCats.php?productId=<?php echo $results[$i]['productId']; ?>&amp;cat_id=<?php echo $results[$i]['cat_id']; ?>&amp;cat_father_id=<?php echo $results[$i]['cat_father_id']; ?>&amp;cat_name=<?php echo urlencode($results[$i]['cat_name']); ?>&amp;name=<?php echo urlencode($results[$i]['name']); ?>','extraCats',500,450,1);" class="txtLink"<?php } else { echo $link401; } ?>>Add to More Categories</a></td>
    <td align="center" valign="middle"  class="<?php echo $cellColor; ?>"><?php

	if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/".$results[$i]['image']) && !empty($results[$i]['image'])){

		

		$imgSize = getimagesize($GLOBALS['rootDir']."/images/uploads/thumbs/".$results[$i]['image']);

		$imgFile = "thumbs/".$results[$i]['image']; 

	

	} elseif(file_exists($GLOBALS['rootDir']."/images/uploads/".$results[$i]['image']) && !empty($results[$i]['image'])){

		

		$imgSize = getimagesize($GLOBALS['rootDir']."/images/uploads/".$results[$i]['image']); 

		$imgFile = $results[$i]['image'];

		

	}

	?>
      <?php if($imgFile == TRUE){ ?>
      <img src="<?php echo $GLOBALS['rootRel'];?>images/uploads/<?php echo $imgFile; ?>" alt="<?php echo $results[$i]['name']; ?>" title="" <?php if($imgSize['0']>49){ ?>height="50"<?php } // end if image exists ?> />
      <div><a href="javascript:;" <?php if(permission("products","edit")==TRUE){ ?>onclick="openPopUp('<?php echo $GLOBALS['rootRel']; ?>admin/products/extraImgs.php?productId=<?php echo $results[$i]['productId']; ?>&amp;img=<?php echo urlencode($results[$i]['image']); ?>&amp;cat=1','extraImgs',550,450,1);" class="txtLink"<?php } else { echo $link401; } ?>>Add More Images</a></div>
      <?php 

	unset($imgFile);

	} else { echo "&nbsp;"; }// end if image exists ?>
    </td>
    <td align="center" class="<?php echo $cellColor; ?> <?php evoHideAlt(96); ?>"><span class="copyText"><?php echo priceFormat($results[$i]['price']); ?></span>
      <?php 

	$salePrice = salePrice($results[$i]['price'], $results[$i]['sale_price']);

	if($salePrice==TRUE){?>
      <br />
      <span class="txtRed">
      <?php

	echo priceFormat($salePrice);

	?>
      </span>
      <?php } ?>
    </td>
    <!-- start mod: Related Products -->
    <td align="center" class="<?php echo $cellColor; ?>  <?php evoHideAlt(31); ?>"><a href="javascript:;" <?php if(permission("products","edit")==TRUE){ ?>onclick="openPopUp('<?php echo $GLOBALS['rootRel']; ?>admin/products/relatedProds.php?productId=<?php echo $results[$i]['productId']; ?>','relatedProds',550,600,'1,resizable=1');" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['related_prods']['related_products'];?></a></td>
    <!-- end mod: Related Products -->
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['showProd'] == 0 ? 'Hide' : 'Show'; ?></span></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a <?php if(permission("products","edit")==TRUE){ ?>href="?edit=<?php echo $results[$i]['productId']."&category=$ecat&orderCol=$eocol&orderDir=$eodir&searchStr=$esstr&submit=$esub";  if (!empty($page)) echo "&cpage=$page"; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="/admin/images/edit.gif" alt="Edit" title="Edit" width="16" border="0" /></a></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a <?php if(permission("products","delete")==TRUE){ ?>href="javascript:decision('<?php echo $lang['admin']['delete_q'];?>','?delete=<?php echo $results[$i]['productId']; ?>&cat_id=<?php echo $results[$i]['cat_id']; ?>');" class="txtLink"<?php } else { echo $link401; } ?>><img src="/admin/images/del.gif" alt="Delete" title="Delete" border="0" /></a></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a <?php if(permission("products","edit")==TRUE){ ?>href="?clone=<?php echo $results[$i]['productId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['products']['clone'];?></a></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?> <?php evoHideAlt(102); ?>"><a <?php if(permission("products","edit")==TRUE){ ?>href="languages.php?prod_master_id=<?php echo $results[$i]['productId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['products']['languages'];?></a></td>
  </tr>
  <?php } // end loop

  } else { ?>
  <tr>
    <td colspan="6" class="tdText"><?php echo $lang['admin']['products']['no_products_exist'];?></td>
  </tr>
  <?php } ?>
</table>
<p class="copyText"><?php echo $pagination; ?></p>
<?php 

} elseif(($_GET["mode"]=="new" && permission("products","write")==TRUE) || ($_GET["edit"]>0 && permission("products","edit")==TRUE) || ($_GET["clone"]>0 && permission("products","write")==TRUE)){ 

$ecat = $_REQUEST['category'];
    $eocol = $_REQUEST['orderCol'];
    $eodir = $_REQUEST['orderDir'];
    $esstr = $_REQUEST['searchStr'];
    $esub = $_REQUEST['submit'];

if(isset($_GET["edit"]) && $_GET["edit"]>0){ $modeTxt = $lang['admin']['edit']; } else { $modeTxt = $lang['admin']['add']; }

if (isset($_GET["clone"]) && $_GET["clone"]>0) {unset($results[0]['productCode'], $results[0]['productId']);}

?>
<p class="copyText"><?php echo $lang['admin']['products']['add_prod_desc'];?></p>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/products/index.php?category=<?=$ecat?>&orderCol=<?=$eocol?>&orderDir=<?=$eodir?>&searchStr=<?=$esstr?>&submit=<?=$esub?><?php if (isset($_GET['cpage'])) { echo "&page=".$_GET['cpage'];} ?>" method="post" enctype="multipart/form-data" name="form1" language="javascript">
  <table border="0" cellspacing="0" cellpadding="3" class="mainTable" width="100%">
    <tr>
      <td colspan="2" class="tdTitle"><?php if(isset($_GET["edit"]) && $_GET["edit"]>0){ echo $modeTxt; } else { echo $modeTxt; } echo $lang['admin']['products']['product'];?>
      </td>
    </tr>
    <tr>
      <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products']['prod_name2'];?></strong></td>
      <td><input name="name" type="text" class="textbox" value="<?php if(isset($results[0]['name'])) echo validHTML($results[0]['name']); ?>" maxlength="255" />
      </td>
    </tr>
    <tr>
      <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products']['prod_stock_no'];?></strong> <br />
        <?php echo $lang['admin']['products']['auto_generated'];?> </td>
      <td><input name="productCode" type="text" class="textbox" value="<?php if(isset($results[0]['productCode'])) echo $results[0]['productCode']; ?>" maxlength="255" /></td>
    </tr>
    <tr <?php evoHide(32); ?>>
      <td width="25%" class="tdText"><strong>Description: Category page</strong> <br />
        <?php echo $lang['admin']['products']['secondary_lang'];?> </td>
    </tr>
    <tr <?php evoHide(32); ?>>
      <td colspan="2" class="tdRichText"><?php
		$oFCKeditor2 = new FCKeditor('FCKeditor2');
		$oFCKeditor2->BasePath = $GLOBALS['rootRel'].'admin/includes/rte/' ;
		if(isset($results[0]['short_description'])){ 
			$oFCKeditor2->Value = $results[0]['short_description'];
		} else {
			$oFCKeditor2->Value = "";
		}
		$oFCKeditor2->Create();
		?>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="tdRichText"><span class="tdText"><strong>Description: Product page</strong></span> </td>
    </tr>
    <tr>
      <td colspan="2" class="tdRichText"><?php
		$oFCKeditor = new FCKeditor('FCKeditor');
		$oFCKeditor->BasePath = $GLOBALS['rootRel'].'admin/includes/rte/' ;
		if(isset($results[0]['description'])){ 
			$oFCKeditor->Value = $results[0]['description'];
		} else {
			$oFCKeditor->Value = "";
		}
		$oFCKeditor->Create();
		?>
      </td>
    </tr>
    
    
    <tr <?php evoHide(44); ?>>
        <td align="left" valign="top" class="tdText"><strong>Image: </strong></td>
      <td valign="top"><div id="selectedImage">
          <?php if(!empty($results[0]['image'])){ ?>
          <img src="<?php echo $GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$results[0]['image']; ?>" alt="<?php echo $results[0]['image']; ?>" title="" />
          <div  style="padding: 3px;">
            <input type="button" class="submit" src="../images/remove.gif" name="remove" value="<?php echo $lang['admin']['remove']; ?>" onclick="addImage('','')" />
          </div>
          <?php } ?>
        </div>
        <div id="imageControls">
          <table border="0" cellspacing="0" cellpadding="3">
            <tr>
              <td><a href="/admin/filemanager/upload.php" class="submit" target="_blank"><?php echo $lang['admin']['categories']['upload_new_image']; ?></a></td>
            </tr>
            <tr>
              <td><input name="browse" class="submit" type="button" id="browse" onclick="openPopUp('../filemanager/browse.php?custom=1&amp;cat=1','filemanager',450,500)" value="Browse Images" /></td>
            </tr>
          </table>
        </div>
        <input type="hidden" name="image" id="imageName" value="<?php echo $results[0]['image']; ?>" />
      </td>
    </tr>
    
    <!--add 2nd sent of image buttons-->
    <?php /*?><tr <?php evoHide(44); ?>>
        <td align="left" valign="top" class="tdText"><strong>Image 2: </strong></td>
      <td valign="top"><div id="selectedImage2">
          <?php if(!empty($results[0]['image2'])){ ?>
          <img src="<?php echo $GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$results[0]['image2']; ?>" alt="<?php echo $results[0]['image2']; ?>" title="" />
          <div  style="padding: 3px;">
            <input type="button" class="submit" src="../images/remove.gif" name="remove" value="<?php echo $lang['admin']['remove']; ?>" onclick="addImage2('','')" />
          </div>
          <?php } ?>
        </div>
        <div id="imageControls2">
          <table border="0" cellspacing="0" cellpadding="3">
            <tr>
              <td><a href="/admin/filemanager/upload.php" class="submit" target="_blank"><?php echo $lang['admin']['categories']['upload_new_image']; ?></a></td>
            </tr>
            <tr>
              <td><input name="browse" class="submit" type="button" id="browse" onclick="openPopUp('../filemanager/browse.php?custom=1&amp;cat=1','filemanager',450,500)" value="Browse Images" /></td>
            </tr>
          </table>
        </div>
        <input type="hidden" name="image" id="imageName2" value="<?php echo $results[0]['image2']; ?>" />
      </td>
    </tr><?php */?>
    
    
    
    <tr <?php evoHide(34); ?>>
      <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products']['category'];?></strong></td>
      <td><select name="cat_id" class="textbox">
          <?php for ($i=0; $i<count($categoryArray); $i++){ ?>
          <option value="<?php echo $categoryArray[$i]['cat_id']; ?>" <?php if(isset($results[0]['cat_id']) && $categoryArray[$i]['cat_id']==$results[0]['cat_id']) { echo "selected='selected'"; } ?>><?php echo getCatDir($categoryArray[$i]['cat_name'],$categoryArray[$i]['cat_father_id'], $categoryArray[$i]['cat_id']); ?></option>
          <?php } ?>
        </select>
      </td>
    </tr>
    
  
    
    
    
    <?php /*?><script language='javascript'>
    	function calculate() {
    		var cost = document.getElementById('cost_price').value;
    		var profit = document.getElementById('profit_margin').value;
    		var price = cost * ((profit / 100) + 1);
    		document.getElementById('price').value = price;
    	}
    </script><?php */?>
    <tr <?php evoHide(25); ?>>
        <td class="tdText"><strong>Choose Brand: </strong></td>
        <td class="tdText">
            <?php
                $brand = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_brands ORDER BY id ASC");
                if($brand==true){
                    echo '<select name="brand" class="textbox">
                    <option class="brand" value="0">Select a Brand</option>';
                    for($q=0; $q<count($brand); $q++){
                        echo '<option class="brand" value="'.$brand[$q]['id'].'" '; if($brand[$q]['id']==$results[0]['productBrand']){echo'selected="selected"';} echo'>'.$brand[$q]['brandName'].'</option>';
                     }
                     echo'</select>';
                }else{
                    echo'There are no brands to select';
                }
            ?>
            <input name="oldBrand" type="hidden" value="<?php echo $results[0]['productBrand'] ?>" />
        </td>
    </tr>
    <tr style="display:none;">
      <td width="25%" class="tdText"><strong>Cost Price:</strong></td>
      <td><input name="cost_price" id='cost_price' onchange="calculate();" value="<?php if(isset($results[0]['cost_price'])) echo $results[0]['cost_price']; ?>" type="text" class="textbox" size="10" /></td>
    </tr>
    <tr style="display:none;">
      <td width="25%" class="tdText"><strong>Profit Margin (%):</strong></td>
      <td><input name="profit_margin" id='profit_margin' onchange="calculate();" value="<?php if(isset($results[0]['price'])) echo $results[0]['profit_margin']; ?>" type="text" class="textbox" size="10" /></td>
    </tr>
    <tr <?php evoHide(96); ?>>
      <td width="25%" class="tdText"><strong>Selling Price:</strong></td>
      <td><input name="price" id='price' value="<?php if(isset($results[0]['price'])) echo $results[0]['price']; ?>" type="text" class="textbox" size="10" /></td>
    </tr>
    <tr <?php evoHide(95); ?>>
      <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products']['sale_price2'];?></strong></td>
      <td class="tdText"><input name="sale_price" value="<?php if(isset($results[0]['sale_price'])) echo $results[0]['sale_price']; ?>" type="text" class="textbox" size="10" /> <?php echo $lang['admin']['products']['sale_mode_desc'];?></td>
    </tr>
    <tr <?php evoHide(33); ?>>
      <td width="25%" class="tdText"><strong>Trade Selling Price:</strong></td>
      <td><input name="tradePrice" id='tradePrice' value="<?php if(isset($results[0]['tradePrice'])) echo $results[0]['tradePrice']; ?>" type="text" class="textbox" size="10" /></td>
    </tr>
    <tr <?php evoHide(33); ?>>
      <td width="25%" class="tdText"><strong>Trade Sale Price:</strong></td>
      <td class="tdText"><input name="tradeSale" id='tradeSale' value="<?php if(isset($results[0]['tradeSale'])) echo $results[0]['tradeSale']; ?>" type="text" class="textbox" size="10" /> <?php echo $lang['admin']['products']['sale_mode_desc'];?></td>
    </tr>
    <tr <?php evoHide(38); ?>>
      <td class="tdText"><strong><?php echo $lang['admin']['products']['prod_weight'];?></strong></td>
      <td class="tdText"><input name="prodWeight" type="text" class="textbox" size="10" value="<?php if(isset($results[0]['prodWeight'])) echo $results[0]['prodWeight']; ?>" />
        <?php echo $config['weightUnit']; ?></td>
    </tr>
    <tr <?php evoHide(41); ?>>
      <td class="tdText"><strong><?php echo $lang['admin']['products']['tax_class'];?></strong></td>
      <td class="tdText"><select name="taxType">
          <?php

	$taxTypes = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_taxes"); 

	?>
          <?php for($i=0; $i<count($taxTypes);$i++){ ?>
          <option value="<?php echo $taxTypes[$i]['id']; ?>" <?php if(isset($results[0]['taxType']) && $taxTypes[$i]['id'] == $results[0]['taxType']) echo "selected='selected'"; ?>><?php echo $taxTypes[$i]['taxName']; ?> (<?php echo $taxTypes[$i]['percent']; ?>%)</option>
          <?php } ?>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(94); ?>>
      <td class="tdText"><strong><?php echo $lang['admin']['products']['use_stock_q'];?></strong></td>
      <td class="tdText">
      	<select name="useStockLevel" id="useStockLevel" class="textbox">
        	<option value="0" <?php if(isset($results[0]['useStockLevel']) && $results[0]['useStockLevel']==0) { echo "selected='selected'"; } ?>>No</option>
            <option value="1" <?php if(isset($results[0]['useStockLevel']) && $results[0]['useStockLevel']==1) { echo "selected='selected'"; } ?>>Yes</option>
        </select></td>
    </tr>
    <!--option stock level-->
    <?php
		$firstOption=$db->select("
		SELECT MIN(top.parent)
		FROM ".$glob['dbprefix']."CubeCart_options_bot AS bot
		INNER JOIN ".$glob['dbprefix']."CubeCart_options_mid AS mid
		ON bot.value_id=mid.value_id 
		INNER JOIN ".$glob['dbprefix']."CubeCart_options_top AS top
		ON bot.option_id=top.option_id 
		WHERE bot.product=".$db->mySQLSafe($results[0]['productId'])."
		GROUP BY mid.value_id
		ORDER BY top.parent, top.option_name, mid.value_name, bot.assign_id
		");
		if($firstOption){
			$options=$db->select("
			SELECT top.parent, top.option_id, mid.value_name, bot.assign_id 
			FROM ".$glob['dbprefix']."CubeCart_options_bot AS bot
			INNER JOIN ".$glob['dbprefix']."CubeCart_options_mid AS mid
			ON bot.value_id=mid.value_id 
			INNER JOIN ".$glob['dbprefix']."CubeCart_options_top AS top
			ON bot.option_id=top.option_id 
			WHERE bot.product=".$db->mySQLSafe($results[0]['productId'])."
			AND top.parent=".$db->mySQLSafe($firstOption[0]['MIN(top.parent)'])."
			GROUP BY mid.value_id
			ORDER BY top.parent, top.option_name, mid.value_name, bot.assign_id
			");
		}
		if ($options) {
	?>
    <tr>
    	<td class="tdText"><strong>Product option stock</strong></td>
        <td class="tdText">
        	<?php
				function checkSubOptions($prod,$id,$lastOpt='',$lastOptId='',$db){
					$options2=$db->select("
						SELECT * 
						FROM ".$glob['dbprefix']."CubeCart_options_bot AS bot
						INNER JOIN ".$glob['dbprefix']."CubeCart_options_mid AS mid
						ON bot.value_id=mid.value_id 
						INNER JOIN ".$glob['dbprefix']."CubeCart_options_top AS top
						ON bot.option_id=top.option_id 
						WHERE bot.product=".$db->mySQLSafe($prod)."
						AND top.parent=".$db->mySQLSafe($id)." 
						GROUP BY mid.value_id
						ORDER BY top.parent, top.option_name, mid.value_name, bot.assign_id
						");
						if($options2){
							for($o2=0; $o2<count($options2); $o2++){
								if(checkSubOptions($prod,$options2[$o2]['option_id'],$lastOpt.' - '.$options2[$o2]['value_name'],$lastOptId.'|'.$options2[$o2]['assign_id'],$db)==false){
									$stock=$db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_options_stock WHERE options=".$db->mySQLSafe($lastOptId.'|'.$options2[$o2]['assign_id']));
									echo '
									<tr>
										<td>'.$lastOpt.' - '.$options2[$o2]['value_name'].'</td>
										<td>
											<input name="optStock[]" type="text" value="'.$stock[0]['stock'].'" class="textbox" size="10" />
											<input name="optStockId[]" type="hidden" value="'.$lastOptId.'|'.$options2[$o2]['assign_id'].'" class="textbox" size="10" />
										</td>
									</tr>	
									';
								}
								if(($o2+1)>=count($options2)){
									return true;
								}
							}
						}else{
							return false;
						}
				}
            	
				echo'<table>';
				for($o=0; $o<count($options); $o++){
					if(checkSubOptions($results[0]['productId'],$options[$o]['option_id'],$options[$o]['value_name'],$options[$o]['assign_id'], $db)==false){
						$stock=$db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_options_stock WHERE options=".$db->mySQLSafe($options[$o]['assign_id']));
						echo '
						<tr>
							<td>'.$options[$o]['value_name'].'</td>
							<td>
								<input name="optStock[]" type="text" value="'.$stock[0]['stock'].'" class="textbox" size="10" />
								<input name="optStockId[]" type="hidden" value="'.$options[$o]['assign_id'].'" class="textbox" size="10" />
							</td>
						</tr>	
						';
					}
				}
				echo'</table>';
			?>
            <input name="stock_level" value="<?php if(isset($results[0]['stock_level'])) echo $results[0]['stock_level']; ?>" type="hidden" class="textbox" size="10" />
        </td>
    </tr>
    <?php }else{ ?>
    <!--end option stock level-->
    <tr <?php evoHide(94); ?> id="productStock">
      <td class="tdText"><strong><?php echo $lang['admin']['products']['stock_level2'];?><br />
        </strong></td>
      <td class="tdText"><input name="stock_level" value="<?php if(isset($results[0]['stock_level'])) echo $results[0]['stock_level']; ?>" type="text" class="textbox" size="10" /> <?php echo $lang['admin']['products']['reduce_stock_level'];?></td>
    </tr>
    <?php } ?>
    
    
    <tr <?php evoHide(94); ?>>
      <td class="tdText"><strong>Purchase Method:</strong></td>
      <td><select name="purMeth" class="textbox">
          <option value="0" <?php if($results[0]['purMeth'] == 0) echo "selected"; ?>>Add to Basket</option>
          <option value="1" <?php if($results[0]['purMeth'] == 1) echo "selected"; ?>>Get Quotation</option>
          <option value="2" <?php if($results[0]['purMeth'] == 2) echo "selected"; ?>>Use Both</option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(86); ?>>
      <td class="tdText" valign="top"><strong><?php echo $lang['admin']['products']['product_type'];?></strong> </td>
      <td class="tdText"><?php echo $lang['admin']['products']['tangible'];?><span class="tdText">
        <input name="digital" onclick="digitalDir.className='hiddenTextbox';digitalDir.value=''" type="radio" value="0" <?php if(isset($results[0]['digital']) && $results[0]['digital']==0) { echo "checked='checked'"; } elseif(!isset($results[0]['digital'])) { echo "checked='checked'"; } ?> />
        </span> <?php echo $lang['admin']['products']['digital'];?><span class="tdText">
        <input name="digital" onclick="digitalDir.className='dirTextbox'" type="radio" value="1" <?php if(isset($results[0]['digital']) && $results[0]['digital']==1) { echo "checked='checked'"; } ?> />
        <input name="digitalDir" type="text" id="digitalDir" value="<?php if(isset($results[0]['digitalDir'])) echo $results[0]['digitalDir']; ?>" maxlength="255" <?php if(isset($results[0]['digitalDir']) && $results[0]['digital']==1) { echo "class='dirTextbox'"; } else { ?>class="hiddenTextbox" <?php } ?> />
        <br />
        <?php echo $lang['admin']['products']['digi_path'];?></span></td>
    </tr>
    <tr <?php evoHide(26); ?>>
      <td class="tdText"><strong>Include in Featured Products: </strong></td>
      <td class="tdText"><select name="featured" class="textbox">
      	  <option value="0" <?php if(isset($results[0]['featured']) && $results[0]['featured']==0) echo "selected='selected'"; ?>>No</option>
          <option value="1" <?php if(isset($results[0]['featured']) && $results[0]['featured']==1) echo "selected='selected'"; ?>>Yes</option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(27); ?>>
      <td class="tdText"><strong>Include in Latest Products: </strong></td>
      <td class="tdText"><select name="latest" class="textbox">
      	  <option value="0" <?php if(isset($results[0]['latest']) && $results[0]['latest']==0) echo "selected='selected'"; ?>>No</option>
          <option value="1" <?php if(isset($results[0]['latest']) && $results[0]['latest']==1) echo "selected='selected'"; ?>>Yes</option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(73); ?>>
      <td class="tdText"><strong>Include in Sale Products: </strong></td>
      <td class="tdText"><select name="sale" class="textbox">
      	  <option value="0" <?php if(isset($results[0]['sale']) && $results[0]['sale']==0) echo "selected='selected'"; ?>>No</option>
          <option value="1" <?php if(isset($results[0]['sale']) && $results[0]['sale']==1) echo "selected='selected'"; ?>>Yes</option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(28); ?>>
      <td class="tdText"><strong>Include in Popular Products: </strong></td>
      <td class="tdText"><select name="popular" class="textbox">
      	  <option value="0" <?php if(isset($results[0]['popular']) && $results[0]['popular']==0) echo "selected='selected'"; ?>>No</option>
          <option value="1" <?php if(isset($results[0]['popular']) && $results[0]['popular']==1) echo "selected='selected'"; ?>>Yes</option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(33); ?>>
      <td class="tdText"><strong>Include in Bestseller Products: </strong></td>
      <td class="tdText"><select name="bestseller" class="textbox">
	      <option value="0" <?php if(isset($results[0]['bestseller']) && $results[0]['bestseller']==0) echo "selected='selected'"; ?>>No</option>
          <option value="1" <?php if(isset($results[0]['bestseller']) && $results[0]['bestseller']==1) echo "selected='selected'"; ?>>Yes</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="tdText"><strong>Hide/Show Product:</strong></td>
      <td><select name="showProd" class="textbox">
          <option value="0" <?php if($results[0]['showProd'] == 0) echo "selected"; ?>>Hide</option>
          <option value="1" <?php if($results[0]['showProd'] == 1 || !isset($results[0]['showProd'])) echo "selected"; ?>>Show</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="tdText"><strong>Condition:</strong><br />(Google Base Feed)</td>
      <td><select name="condition" class="textbox">
          <option value="new" <?php if($results[0]['condition'] == "new") echo "selected"; ?>>New</option>
          <option value="used" <?php if($results[0]['condition'] == "used") echo "selected"; ?>>Used</option>
          <option value="refurbished" <?php if($results[0]['condition'] == "refurbished") echo "selected"; ?>>Refurbished</option>
        </select>
        </td>
    </tr>
    <tr>
      <td class="tdText"><strong>Brand:</strong><br />(Google Base Feed)</td>
      <td><input name="gBrand" type="text" class="textbox" value="<?php if(isset($results[0]['brand'])) echo $results[0]['brand']; ?>" maxlength="255" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong>Manufacturer Part Number (MPN):</strong><br />(Google Base Feed)</td>
      <td><input name="gManufacturerCode" type="text" class="textbox" value="<?php if(isset($results[0]['manufacturerCode'])) echo $results[0]['manufacturerCode']; ?>" maxlength="255" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong>Universal Product Code (UPC):</strong><br />(Google Base Feed)</td>
      <td><input name="gProductCodeAsc" type="text" class="textbox" value="<?php if(isset($results[0]['productCodeAsc'])) echo $results[0]['productCodeAsc']; ?>" maxlength="255" /></td>
    </tr>
    <!-- <rf> search engine friendly mod -->
    <?php if($config['seftags']) { ?>
    <tr>
      <td colspan="2"><p>
        <table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
          <tr>
            <td colspan="2" class="tdTitle"><strong>DO NOT EDIT anything in the section below without consulting your account manager first</strong></td>
          </tr>
          <tr>
            <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['browser_title']; ?></strong></td>
            <td align="left"><input name="prod_metatitle" type="text" size="35" class="textbox" value="<?php if(isset($results[0]['prod_metatitle'])) echo $results[0]['prod_metatitle']; ?>" /></td>
          </tr>
          <tr>
            <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['settings']['meta_desc'];?></strong></td>
            <td align="left"><textarea name="prod_metadesc" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['prod_metadesc'])) echo $results[0]['prod_metadesc']; ?>
</textarea></td>
          </tr>
          <tr>
            <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['settings']['meta_keywords'];?></strong> <?php echo $lang['admin']['settings']['comma_separated'];?></td>
            <td align="left"><textarea name="prod_metakeywords" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['prod_metakeywords'])) echo $results[0]['prod_metakeywords']; ?>
</textarea></td>
          </tr>
          <?php if($config['sefcustomurl'] == 1) { ?>
          <tr>
            <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo 'Custom URL:';?></strong></td>
            <td align="left">http://ccroot/
              <input name="prod_sefurl" type="text" size="20" class="textbox" value="<?php if(isset($results[0]['prod_sefurl'])) echo $results[0]['prod_sefurl']; ?>" />
              /p_xx.html</td>
          </tr>
          <?php } ?>
        </table>
        </p></td>
    </tr>
    <?php } ?>
    <!-- <rf> end mod -->
    <tr>
      <td width="25%">&nbsp;</td>
      <td><input type="hidden" name="oldCatId" value="<?php if(isset($results[0]['cat_id'])) echo $results[0]['cat_id']; ?>" />
        <input type="hidden" name="productId" value="<?php if(isset($results[0]['productId'])) echo $results[0]['productId']; ?>" />
        <input type="submit" name="Submit" class="submit" value="<?php if(isset($_GET["edit"]) && $_GET["edit"]>0){ echo $modeTxt; } else { echo $modeTxt;  } ?> <?php echo $lang['admin']['products']['product'];?>" /></td>
    </tr>
  </table>
  <br />
  <div class="tdText"><em><u><strong><?php echo $lang['admin']['products']['digi_info'];?></strong></u></em> <?php echo $lang['admin']['products']['digi_desc'];?> </div>
</form>
<?php } ?>
<?php include("../includes/footer.inc.php"); ?>
