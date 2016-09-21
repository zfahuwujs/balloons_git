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
|	viewCat.inc.php
|   ========================================<strong></strong>
|	Display the Current Category	
+--------------------------------------------------------------------------
*/
phpExtension();

if(isset($_GET['page'])){
	
	$page = treatGet($_GET['page']);

} else {
	
	$page = 0;

}

$search = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/search.tpl");

$cats = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_category ORDER BY cat_name ASC");

if($cats){
	for($i = 0; $i < count($cats); $i++){
		$search->assign("CAT_ID", $cats[$i]['cat_id']);
		$search->assign("CAT_NAME", $cats[$i]['cat_name']);
		$search->parse("search.cats");
	}
}

if(isset($_GET['submit'])){


	if($_GET['name'] == "" && $_GET['cat_id'] == "0" && $_GET['max_price'] == "" && $_GET['min_price'] == ""){
		$searchDetails = 'You did not enter a specific search.<br />All products have been listed.<br />';
	}else{
		$searchDetails = 'You searched for the following:<br /><br />';
	}
	
	if($_GET['name'] != ""){
		$searchDetails .= "Keyword: ".$_GET['name']."<br />";
	}
	
	if($_GET['min_price'] != ""){
		$min_price = " AND price >= ".$db->mySQLSafe($_GET['min_price']);
		$searchDetails .= "Minimum Price: ".$_GET['min_price']."<br />";
	}
	
	if($_GET['max_price'] != ""){
		$max_price = " AND price <= ".$db->mySQLSafe($_GET['max_price']);
		$searchDetails .= "Maximum Price: ".$_GET['max_price']."<br />";
	}
	
	$price = $max_price.$min_price;
	$productResults = '';
	
	if($_GET['cat_id'] == 0){
		$productResults = $db->select("SELECT * FROM 
								".$glob['dbprefix']."CubeCart_inventory 
								WHERE (name LIKE ".$db->mySQLSafe("%".$_GET['name']."%")." 
								OR description LIKE ".$db->mySQLSafe("%".$_GET['name']."%").")
								 ".$price." 
								ORDER BY productId ASC");
	}else{
		$catName = $db->select("SELECT cat_name FROM 
								".$glob['dbprefix']."CubeCart_category 
								WHERE cat_id = ".$db->mySQLSafe($_GET['cat_id']));
								
		$searchDetails .= "Category: ".$catName[0]['cat_name']."<br />";
		
		$productResults = $db->select("SELECT * FROM 
								".$glob['dbprefix']."CubeCart_inventory 
								WHERE (name LIKE ".$db->mySQLSafe("%".$_GET['name']."%")." 
								OR description LIKE ".$db->mySQLSafe("%".$_GET['name']."%").")
								 ".$price." 
								AND cat_id = ".$db->mySQLSafe($_GET['cat_id'])."
								ORDER BY productId ASC");
	}
	
	
	
	
	if($productResults){
		$search->assign("LANG_IMAGE",$lang['front']['viewCat']['image']);
		$search->assign("LANG_DESC",$lang['front']['viewCat']['description']);
		$search->assign("LANG_PRICE",$lang['front']['viewCat']['price']);
			
		for($i = 0; $i < count($productResults); $i++){
			$search->assign("CLASS",cellColor($i, $tdEven="tdEven", $tdOdd="tdOdd"));
			
			if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$productResults[$i]['image'])){
				$search->assign("SRC_PROD_THUMB",$GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$productResults[$i]['image']);
			} else {
				$search->assign("SRC_PROD_THUMB",$GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif");
			}
	
	
			$search->assign("TXT_TITLE",validHTML($productResults[$i]['name']));		
	
			$search->assign("TXT_DESC",substr(strip_tags($productResults[$i]['short_description']),0,$config['productPrecis'])."&hellip;");
			
			
			if(salePrice($productResults[$i]['price'], $productResults[$i]['sale_price'])==FALSE){
				if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
					$search->assign("TXT_PRICE",priceFormat($productResults[$i]['tradePrice']));
				}else{
					$search->assign("TXT_PRICE",priceFormat($productResults[$i]['price']));
				}
			} else {
				if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
					$search->assign("TXT_PRICE","<span class='txtOldPrice'>".priceFormat($productResults[$i]['tradePrice'])."</span>");
				}else{
					$search->assign("TXT_PRICE","<span class='txtOldPrice'>".priceFormat($productResults[$i]['price'])."</span>");
				}
			}
			
			if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
				$salePrice = salePrice($productResults[$i]['tradePrice'], $productResults[$i]['tradeSale']);
			}else{
				$salePrice = salePrice($productResults[$i]['price'], $productResults[$i]['sale_price']);
			}
			
			$search->assign("TXT_SALE_PRICE", priceFormat($salePrice));
			
			if(isset($_GET['add']) && isset($_GET['quan'])){
				$search->assign("CURRENT_URL",str_replace(array("&amp;add=".$_GET['add'],"&amp;quan=".$_GET['quan']),"",currentPage()));	
			} else {
				$search->assign("CURRENT_URL",currentPage());	
			}
	
			if($config['outofstockPurchase']==1){
				$search->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
				$search->assign("PRODUCT_ID",$productResults[$i]['productId']);
				$search->parse("search.productTable.products.buy_btn");
			
			} elseif($productResults[$i]['useStockLevel']==1 && $productResults[$i]['stock_level']>0){
				$search->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
				$search->assign("PRODUCT_ID",$productResults[$i]['productId']);
				$search->parse("search.productTable.products.buy_btn");
			
			} elseif($productResults[$i]['useStockLevel']==0){
				$search->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
				$search->assign("PRODUCT_ID",$productResults[$i]['productId']);
				$search->parse("search.productTable.products.buy_btn");
			
			}
	
			$search->assign("BTN_MORE",$lang['front']['viewCat']['more']);
			$search->assign("PRODUCT_ID",$productResults[$i]['productId']);
	
			if($productResults[$i]['stock_level']<1 && $productResults[$i]['useStockLevel']==1 && $productResults[$i]['digital']==0){
				$search->assign("TXT_OUTOFSTOCK",$lang['front']['viewCat']['out_of_stock']);		
			} else {		
				$search->assign("TXT_OUTOFSTOCK","");
			}
			
			$search->parse("search.productTable.products");
		}
		
		
		
		
		
		
		$search->parse("search.productTable");
	}else{
		$searchDetails .="<br /><strong>Your search returned 0 results.</strong><br />";
	}
}
$search->assign("SEARCH_QUERY", $searchDetails);

$search->parse("search");
$page_content = $search->text("search");
?>