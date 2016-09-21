<?php

phpExtension();

if (isset($config['prodSortOrder'])) { $prodSortOrder = $config['prodSortOrder']; }
if (isset($_GET['sortOrder'])) { $prodSortOrder = treatGet($_GET['sortOrder']); }
if ($_GET['sortOrder'] == "default") { $prodSortOrder = $config['prodSortOrder']; }
if (isset($prodSortOrder)) { $prodSortMethod = " ORDER BY ".$glob['dbprefix']."CubeCart_inventory.".$prodSortOrder; }

if (isset($_GET['page'])) {
	$page = treatGet($_GET['page']);
} else {
	$page = 0;
}

$view_cat = new XTemplate("skins/".$config['skinDir']."/styleTemplates/content/viewCat.tpl");

include("includes/boxes/subCatWithImages.inc.php");
$view_cat->assign("SUBCAT_WITH_IMAGES",$box_content);


////////////////////////
// BUILD SUB CATEGORIES
////////////////////////

if (isset($_GET['catId'])) {
	$_GET['catId'] = treatGet($_GET['catId']);
	
	// build query
	$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = ".$db->mySQLSafe($_GET['catId'])." ORDER BY disp_order ASC";
	// get category array in foreign innit
	// start mod: Category Descriptions
	$resultsForeign = $db->select("SELECT cat_desc, cat_master_id as cat_id, cat_name FROM ".$glob['dbprefix']."CubeCart_cats_lang WHERE cat_lang = '".$lang_folder."'");
	// end mod: Category Descriptions
	// query database
	$subCategories = "";
	$subCategories = $db->select($query);
}

if (isset($_GET['catId']) && $_GET['catId'] > 0 && $subCategories == TRUE) {
	for ($i = 0; $i < count($subCategories); $i++) {
		if (is_array($resultsForeign)) {
			for ($k = 0; $k < count($resultsForeign); $k++) {
				if ($resultsForeign[$k]['cat_id'] == $subCategories[$i]['cat_id']) {
					$subCategories[$i]['cat_name'] = $resultsForeign[$k]['cat_name'];
					// start mod: Category Descriptions
					$subCategories[$i]['cat_desc'] = $resultsForeign[$k]['cat_desc'];
					// end mod: Category Descriptions
				}	
			}
		}

		if (empty($subCategories[$i]['cat_image'])) {
			$view_cat->assign("IMG_CATEGORY", $GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/nophoto.png");
		} else {
			$view_cat->assign("IMG_CATEGORY", $GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$subCategories[$i]['cat_image']);
		}
		
		$view_cat->assign("TXT_LINK_CATID", $subCategories[$i]['cat_id']);
		$view_cat->assign("TXT_CATEGORY", validHTML($subCategories[$i]['cat_name']));
		// start mod: Category Descriptions
		$view_cat->assign("TXT_CATDESC",substr(strip_tags($subCategories[$i]['cat_desc']),0,$config['productPrecis'])."&hellip;");
		// end mod: Category Descriptions
		$view_cat->assign("NO_PRODUCTS", $subCategories[$i]['noProducts']);
		$view_cat->parse("view_cat.sub_cats.sub_cats_loop");
	} // end loop results

	$view_cat->parse("view_cat.sub_cats");
} // end $subCategories == TRUE

////////////////////////////
// BUILD PRODUCTS
////////////////////////////

// build query

if (isset($_GET['advSrchBtn'])) {
	
	//did they mean to search?
	if($_GET['prodCode'] == '' && $_GET['keywords'] == ''){
	
		$msg = "You didn't search for anything";
			
	} else {

		
		
		//get category
		//if($category != 'select'){
		//	$categorySearch = ' AND cat_id IN ('.implode(',',allSubcats($category)).')';
		//}
		//which search? make && model OR category && keywords?
		if(isset($_GET['keywords']) && ($_GET['keywords'] != '')){
			
				
				
				//get keywords
				$keywordSearch = '';
				$keywordSearch .= " AND (name like ".$db->mySQLSafe('%'.$_GET['keywords'].'%');
				$keywordSearch .= " OR description like ".$db->mySQLSafe('%'.$_GET['keywords'].'%');
				$keywordSearch .= ")";
			
		}
		if(isset($_GET['prodCode']) && ($_GET['prodCode'] != '')){
			
				//get keywords
				$prodCodeSearch = '';
				$prodCodeSearch .= " AND productCode like ".$db->mySQLSafe('%'.$_GET['prodCode'].'%');
			
		}
	
	}
	
	
	
	$productListQuery = "SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 ".$keywordSearch.$prodCodeSearch;
	
	

}else if (isset($_GET['searchStr'])) {
	
	// Fix for SQL Injection if Reg Globals is On
	if (isset($searchArray)) {
		unset($searchArray);
	}
	$searchwords = split ( "[ ,]", treatGet($_GET['searchStr']));   
	foreach ($searchwords as $word) {
		$searchArray[] = $word;
	}

	$noKeys = count($searchArray);
	$like = "";

	for ($i = 0; $i < $noKeys; $i++) {
		$ucSearchTerm = strtoupper($searchArray[$i]);
		if (($ucSearchTerm !== "AND") && ($ucSearchTerm !== "OR")) {
			/* start mod: Simple Search Improvement v1.0 - by Estelle */
			/*$like .= "(name LIKE '%".$searchArray[$i]."%' OR description LIKE '%".$searchArray[$i]."%' OR productCode LIKE '%".$searchArray[$i]."%') OR ";*/
			$like .= "(name LIKE '%".$searchArray[$i]."%' OR description LIKE '%".$searchArray[$i]."%' OR productCode LIKE '%".$searchArray[$i]."%') AND ";
			/* end mod: Simple Search Improvement v1.0 - by Estelle */
			// see if search terrm is in database
			$searchQuery = "SELECT id FROM ".$glob['dbprefix']."CubeCart_search WHERE searchstr='".$ucSearchTerm."'";
			$searchLogs = $db->select($searchQuery);
			$insertStr['searchstr'] = $db->mySQLsafe($ucSearchTerm);
			$insertStr['hits'] = $db->mySQLsafe(1);
			$updateStr['hits'] = "hits+1";
			
			if ($searchLogs == TRUE) {
				$db->update($glob['dbprefix']."CubeCart_search", $updateStr, "id=".$searchLogs[0]['id'], $quote = "");
			} elseif (!empty($_GET['searchStr'])) {
				$db->insert($glob['dbprefix']."CubeCart_search", $insertStr);
			}
		} else {
			/* start mod: Simple Search Improvement v1.0 - by Estelle */
			$like = substr($like,0,strlen($like)-4);
			/* end mod: Simple Search Improvement v1.0 - by Estelle */
			$like .= $ucSearchTerm;
		}
	}
	/* start mod: Simple Search Improvement v1.0 - by Estelle */
	/*$like = substr($like,0,strlen($like)-3);*/
	$like = substr($like,0,strlen($like)-4);
	/* end mod: Simple Search Improvement v1.0 - by Estelle */

	$productListQuery = "SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 AND ".$like.$prodSortMethod;
	
} elseif ($_GET['catId'] == "wishlist" && isset($ccUserData[0]['customer_id']) && $ccUserData[0]['customer_id'] > 0) {
	
	$productListQuery = "
		SELECT ".$glob['dbprefix']."CubeCart_cats_idx.cat_id, ".$glob['dbprefix']."CubeCart_cats_idx.productId, productCode, quantity, description,  short_description, image, price, tradePrice, tradeSale, name, popularity, sale_price, stock_level, useStockLevel 
		FROM ".$glob['dbprefix']."CubeCart_wishlist
		INNER JOIN ".$glob['dbprefix']."CubeCart_cats_idx 
		ON ".$glob['dbprefix']."CubeCart_wishlist.productId = ".$glob['dbprefix']."CubeCart_cats_idx.productId 
		INNER JOIN ".$glob['dbprefix']."CubeCart_inventory 
		ON ".$glob['dbprefix']."CubeCart_cats_idx.productId = ".$glob['dbprefix']."CubeCart_inventory.productId 
		WHERE customerId = ".$db->mySQLSafe($ccUserData[0]['customer_id'])."
		AND showProd = 1
		GROUP BY ".$glob['dbprefix']."CubeCart_inventory.productId".$prodSortMethod;
			
}elseif($_GET['catId']=="sale" && $config['saleProdsCat']==1) {
	
	$productListQuery = "SELECT ".$glob['dbprefix']."CubeCart_cats_idx.cat_id, ".$glob['dbprefix']."CubeCart_cats_idx.productId, productCode, quantity, description,  short_description, image, price, tradePrice, tradeSale, name, popularity, sale_price, stock_level, useStockLevel FROM ".$glob['dbprefix']."CubeCart_cats_idx INNER JOIN ".$glob['dbprefix']."CubeCart_inventory ON ".$glob['dbprefix']."CubeCart_cats_idx.productId = ".$glob['dbprefix']."CubeCart_inventory.productId WHERE showProd = 1 AND sale_price > 0 AND sale = 1 GROUP BY ".$glob['dbprefix']."CubeCart_inventory.productId".$prodSortMethod;
	
}elseif($_GET['catId']=="latest" && $config['latestProdsCat']==1) {
	
	$productListQuery = "SELECT ".$glob['dbprefix']."CubeCart_cats_idx.cat_id, ".$glob['dbprefix']."CubeCart_cats_idx.productId, productCode, quantity, description,  short_description, image, price, tradePrice, tradeSale, name, popularity, sale_price, stock_level, useStockLevel FROM ".$glob['dbprefix']."CubeCart_cats_idx INNER JOIN ".$glob['dbprefix']."CubeCart_inventory ON ".$glob['dbprefix']."CubeCart_cats_idx.productId = ".$glob['dbprefix']."CubeCart_inventory.productId WHERE showProd = 1 AND latest = 1 GROUP BY ".$glob['dbprefix']."CubeCart_inventory.productId ORDER BY lastModified DESC";

}elseif($_GET['catId']=="featured" && $config['featuredProdsCat']==1) {
	
	$productListQuery = "SELECT ".$glob['dbprefix']."CubeCart_cats_idx.cat_id, ".$glob['dbprefix']."CubeCart_cats_idx.productId, productCode, quantity, description,  short_description, image, price, tradePrice, tradeSale, name, popularity, sale_price, stock_level, useStockLevel FROM ".$glob['dbprefix']."CubeCart_cats_idx INNER JOIN ".$glob['dbprefix']."CubeCart_inventory ON ".$glob['dbprefix']."CubeCart_cats_idx.productId = ".$glob['dbprefix']."CubeCart_inventory.productId WHERE showProd = 1 AND featured = 1 GROUP BY ".$glob['dbprefix']."CubeCart_inventory.productId".$prodSortMethod;

}elseif($_GET['catId']=="bestseller" && $config['bestsellerProdsCat']==1) {
	
	$productListQuery = "SELECT ".$glob['dbprefix']."CubeCart_cats_idx.cat_id, ".$glob['dbprefix']."CubeCart_cats_idx.productId, productCode, quantity, description,  short_description, image, price, tradePrice, tradeSale, name, popularity, sale_price, stock_level, useStockLevel FROM ".$glob['dbprefix']."CubeCart_cats_idx INNER JOIN ".$glob['dbprefix']."CubeCart_inventory ON ".$glob['dbprefix']."CubeCart_cats_idx.productId = ".$glob['dbprefix']."CubeCart_inventory.productId WHERE showProd = 1 AND bestseller = 1 GROUP BY ".$glob['dbprefix']."CubeCart_inventory.productId ORDER BY salesCount DESC";

}elseif($_GET['catId']=="popular" && $config['popularProdsCat']==1) {
	
	$productListQuery = "SELECT ".$glob['dbprefix']."CubeCart_cats_idx.cat_id, ".$glob['dbprefix']."CubeCart_cats_idx.productId, productCode, quantity, description,  short_description, image, price, tradePrice, tradeSale, name, popularity, sale_price, stock_level, useStockLevel FROM ".$glob['dbprefix']."CubeCart_cats_idx INNER JOIN ".$glob['dbprefix']."CubeCart_inventory ON ".$glob['dbprefix']."CubeCart_cats_idx.productId = ".$glob['dbprefix']."CubeCart_inventory.productId WHERE showProd = 1 AND popular = 1 GROUP BY ".$glob['dbprefix']."CubeCart_inventory.productId ORDER BY popularity DESC";


}elseif($_GET['catId'] == "allItems") {
    $productListQuery = "SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 ORDER BY lastModified DESC";
}else {
	$productListQuery = "SELECT ".$glob['dbprefix']."CubeCart_cats_idx.cat_id, ".$glob['dbprefix']."CubeCart_cats_idx.productId, productCode, quantity, description,  short_description, image, price, tradePrice, tradeSale, name, popularity, sale_price, stock_level, useStockLevel FROM ".$glob['dbprefix']."CubeCart_cats_idx INNER JOIN ".$glob['dbprefix']."CubeCart_inventory ON ".$glob['dbprefix']."CubeCart_cats_idx.productId = ".$glob['dbprefix']."CubeCart_inventory.productId WHERE showProd = 1 AND ".$glob['dbprefix']."CubeCart_cats_idx.cat_id = ".$db->mySQLSafe($_GET['catId']).$prodSortMethod;	
}

$productResults = $db->select($productListQuery, $config['productPages'], $page);

// Following lines added for Sir William's Category / Product Order Mod
// This routine will generate a random shuffle of the returned products based on the users
// session ID.  This insures that subsequent pages of returned results will retain the same
// "randomly" generated list of products.  There's a switch in the categoryOrder.php file
// which will globally turn the random list generation on or off.

// $intSession is an integer string made up of just the numbers in the Session.  It  is used to
// seed the random function so that all subsequent "random" displays will show the proper order.

if($prodSortOrder == "digital ASC" && is_array($productResults)) {
  $intSession = ereg_replace("[a-zA-Z]", "", $_SESSION['ccUser']);
  srand(intval(substr($intSession,0,5)));
  shuffle($productResults);
} elseif($prodSortOrder == "digital DESC" && is_array($productResults)) {
  shuffle($productResults);
}

// get different languages 
if ($productResults == TRUE && $lang_folder !== $config['defaultLang']) {
	for ($i = 0; $i < count($productResults); $i++) {
		if (($val = prodAltLang($productResults[$i]['productId'])) == TRUE) {
			$productResults[$i]['name'] = $val['name'];
			$productResults[$i]['short_description'] = $val['short_description'];
		}
	}
}

$totalNoProducts = $db->numrows($productListQuery);

// get current category info
/* <rf> search engine friendly mod */
if (isset($_GET['catId'])) {
	if ($_GET['catId'] > 0) {
		// start mod: Category Descriptions
		$currentCatQuery = "
			SELECT cat_desc, cat_metatitle, cat_metadesc, cat_metakeywords, cat_name, cat_father_id, cat_id, cat_image
			FROM ".$glob['dbprefix']."CubeCart_category
			WHERE cat_id = ".$db->mySQLSafe($_GET['catId']);
		// end mod: Category Descriptions
		$currentCat = $db->select($currentCatQuery);	
		var_dump($_GET['catId']);
	var_dump($currentCat);	
		// start mod: Category Descriptions
		$resultForeign = $db->select("
			SELECT cat_desc, cat_master_id as cat_id, cat_name
			FROM ".$glob['dbprefix']."CubeCart_cats_lang
			WHERE cat_lang = '".$lang_folder."'
			AND cat_master_id = ".$db->mySQLSafe($_GET['catId']));
		// end mod: Category Descriptions
		if ($resultForeign == TRUE) {
			$currentCat[0]['cat_name'] = $resultForeign[0]['cat_name'];
			// start mod: Category Descriptions
			$currentCat[0]['cat_desc'] = $resultForeign[0]['cat_desc'];
			// end mod: Category Descriptions
		}
		$prevDirSymbol = $config['dirSymbol'];
		$config['dirSymbol'] = ' - ';
		$meta['siteTitle'] = $currentCat[0]['cat_name'];
		$config['dirSymbol'] = $prevDirSymbol;
		if (strip_tags(!empty($currentCat[0]['cat_desc']))) {
			$meta['metaDescription'] = substr(strip_tags($currentCat[0]['cat_desc']),0,160);
		} else {
			$meta['metaDescription'] = substr(strip_tags($config['metaDescription']),0,160);
		}
		$meta['sefSiteTitle'] = $currentCat[0]['cat_metatitle']; 
		$meta['sefSiteDesc'] = $currentCat[0]['cat_metadesc'];
		$meta['sefSiteKeywords'] = $currentCat[0]['cat_metakeywords'];
	} elseif(strcmp($_GET['catId'], "saleItems") == 0) {
		$meta['siteTitle'] = $lang['front']['boxes']['sale_items'];
		$meta['metaDescription'] = substr(strip_tags($config['metaDescription']),0,160);
	} 
} elseif ($_GET['catId'] > 0) {
	// start mod: Category Descriptions
	$currentCatQuery = "SELECT cat_desc, cat_name, cat_father_id, cat_id, cat_image FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($_GET['catId']);
	// end mod: Category Descriptions
	$currentCat = $db->select($currentCatQuery);
	// start mod: Category Descriptions
	$resultForeign = $db->select("SELECT cat_desc, cat_master_id as cat_id, cat_name FROM ".$glob['dbprefix']."CubeCart_cats_lang WHERE cat_lang = '".$lang_folder."' AND cat_master_id = ".$db->mySQLSafe($_GET['catId']));
	// end mod: Category Descriptions
	if ($resultForeign == TRUE) {
		$currentCat[0]['cat_name'] = $resultForeign[0]['cat_name'];
		// start mod: Category Descriptions
		$currentCat[0]['cat_desc'] = $resultForeign[0]['cat_desc'];
		// end mod: Category Descriptions
	}
}		
/* <rf> end mod */

if (!empty($currentCat[0]['cat_image'])) {
	$view_cat->assign("IMG_CURENT_CATEGORY","images/uploads/thumbs/thumb_".$currentCat[0]['cat_image']);
	$view_cat->assign("TXT_CURENT_CATEGORY",validHTML($currentCat[0]['cat_name']));
	$view_cat->parse("view_cat.cat_img");
}

if (isset($_GET['searchStr'])) {
	$view_cat->assign("TXT_CAT_TITLE",$lang['front']['viewCat']['search_results']);
} elseif ($_GET['catId'] == "wishlist" && isset($ccUserData[0]['customer_id']) && $ccUserData[0]['customer_id'] > 0) {
	$view_cat->assign("TXT_CAT_TITLE","Sale Products");		
} elseif ($_GET['catId'] == "sale" && $config['saleProdsCat'] == 1) {
	$view_cat->assign("TXT_CAT_TITLE","Sale Products");
} elseif($_GET['catId']=="latest" && $config['latestProdsCat']==1) {
	$view_cat->assign("TXT_CAT_TITLE","Latest Products");
} elseif($_GET['catId']=="featured" && $config['featuredProdsCat']==1) {
	$view_cat->assign("TXT_CAT_TITLE","Featured Products");
} elseif($_GET['catId']=="popular" && $config['popularProdsCat']==1) {
	$view_cat->assign("TXT_CAT_TITLE","Popular Products");
} elseif($_GET['catId']=="bestseller" && $config['bestsellerProdsCat']==1) {
	$view_cat->assign("TXT_CAT_TITLE","Best Seller Products");
} elseif($_GET['catId'] == "allItems"){
	$view_cat->assign("TXT_CAT_TITLE", "All Products");
} else {
	$view_cat->assign("TXT_CAT_TITLE",validHTML($currentCat[0]['cat_name']));
	// start mod: Category Descriptions
	$view_cat->assign("TXT_CAT_DESC",$currentCat[0]['cat_desc']);
	// end mod: Category Descriptions
}

$view_cat->assign("LANG_IMAGE",$lang['front']['viewCat']['image']);
$view_cat->assign("LANG_DESC",$lang['front']['viewCat']['description']);
$view_cat->assign("LANG_PRICE",$lang['front']['viewCat']['price']);
$view_cat->assign("PAGINATION",$db->paginate($totalNoProducts, $config['productPages'], $page, "page"));

// repeated region
if ($productResults == TRUE) {
	
	if ($_GET['catId'] > 0) {	
		$view_cat->assign("LANG_CURRENT_DIR",$lang['front']['viewCat']['products_in']);
		$view_cat->assign("CURRENT_DIR",getCatDir(validHTML($currentCat[0]['cat_name']),$currentCat[0]['cat_father_id'], $currentCat[0]['cat_id'], $link=TRUE));
		// This part adds the User-Selectable Sort Orders
		$sortOptions = "<option value=\"default\">Default</option>";
		for ($i=0; $i<count($sortType); $i++) {
			$sortOptions .= "<option value=\"".$sortType[$i]['method']."\"";
			if($prodSortOrder == $sortType[$i]['method']) { $sortOptions .= " selected"; }
			$sortOptions .= ">".$sortType[$i]['name']."</option>";
		}
		$view_cat->assign("LINK_CATID",$_GET['catId']);
		$view_cat->assign("SORT_BY_TEXT",$lang['admin']['categories']['prodsort_sordorder']);
		$view_cat->assign("SORT_BY_OPTIONS",$sortOptions);
		$view_cat->parse("view_cat.prod_sort");
	}
	
	for ($i = 0; $i < count($productResults); $i++) {	
		// alternate class
		$view_cat->assign("CLASS",cellColor($i, $tdEven="tdEven", $tdOdd="tdOdd"));

		if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$productResults[$i]['image'])){
			$view_cat->assign("SRC_PROD_THUMB","images/uploads/thumbs/thumb_".$productResults[$i]['image']);
		} else {
			$view_cat->assign("SRC_PROD_THUMB",$GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.png");
		}

		$view_cat->assign("TXT_TITLE",validHTML($productResults[$i]['name']));		
		$view_cat->assign("TXT_DESC",substr(strip_tags($productResults[$i]['short_description']),0,$config['productPrecis'])."&hellip;");
		
		if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
					if($ccUserData[0]['tax'] == 1){
						if(salePrice($productResults[$i]['tradePrice'], $productResults[$i]['tradeSale'])==FALSE){
							$view_cat->assign("TXT_PRICE",priceFormat($productResults[$i]['tradePrice']));
							$view_cat->assign("TXT_SALE_PRICE", "");
						}else{
							$view_cat->assign("TXT_PRICE","<strike>".priceFormat($productResults[$i]['tradePrice'])."</strike>");
							$salePrice = salePrice($productResults[$i]['tradePrice'], $productResults[$i]['tradeSale']);
							$view_cat->assign("TXT_SALE_PRICE", priceFormat($salePrice));
						}
					}
					if($ccUserData[0]['tax'] == 2){
						if(salePrice($productResults[$i]['tradePrice'], $productResults[$i]['tradeSale'])==FALSE){
							$vatTradePrice = getProductPriceWithTax($productResults[$i]['productId'], 'tradePrice');
							$view_cat->assign("TXT_PRICE",priceFormat($vatTradePrice));
							$view_cat->assign("TXT_SALE_PRICE", "");
						}else{
							$vatTradePrice = getProductPriceWithTax($productResults[$i]['productId'], 'tradePrice');
							$view_cat->assign("TXT_PRICE","<strike>".priceFormat($productResults[$i])."</strike>");
							$vatSalePrice = getProductPriceWithTax($productResults[$i]['productId'], 'tradeSale');
							$salePrice = salePrice($vatTradePrice, $vatSalePrice);
							$view_cat->assign("TXT_SALE_PRICE", priceFormat($salePrice));
						}
					}
				} else {
					if($ccUserData[0]['tax'] == 1){
						if(salePrice($productResults[$i]['price'], $productResults[$i]['sale_price'])==FALSE){
							$view_cat->assign("TXT_PRICE",priceFormat($productResults[$i]['price']));
							$view_cat->assign("TXT_SALE_PRICE", "");
						}else{
							$view_cat->assign("TXT_PRICE","<strike>".priceFormat($productResults[$i]['price'])."</strike>");
							$salePrice = salePrice($productResults[$i]['price'], $productResults[$i]['sale_price']);
							$view_cat->assign("TXT_SALE_PRICE",priceFormat($salePrice));
						}
					}
					if($ccUserData[0]['tax'] == 2){
						if(salePrice($productResults[$i]['price'], $productResults[$i]['sale_price'])==FALSE){
							$vatPrice = getProductPriceWithTax($productResults[$i]['productId'], 'price');
							$view_cat->assign("TXT_PRICE",priceFormat($vatPrice));
							$view_cat->assign("TXT_SALE_PRICE", "");
						}else{
							$vatPrice = getProductPriceWithTax($productResults[$i]['productId'], 'price');
							$view_cat->assign("TXT_PRICE","<strike>".priceFormat($vatPrice)."</strike>");
							$vatSalePrice = getProductPriceWithTax($productResults[$i]['productId'], 'sale_price');
							$salePrice = salePrice($vatPrice, $vatSalePrice);
							$view_cat->assign("TXT_SALE_PRICE",priceFormat($salePrice));
						}
					}
				}
		
			
		if (isset($_GET['add']) && isset($_GET['quan'])) {
			$view_cat->assign("CURRENT_URL", str_replace(array("&amp;add=".$_GET['add'], "&amp;quan=".$_GET['quan']), "", currentPage()));
		} else {
			$view_cat->assign("CURRENT_URL", currentPage());
		}

		if ($config['outofstockPurchase'] == 1) {
			$view_cat->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
			$view_cat->assign("PRODUCT_ID",$productResults[$i]['productId']);
			$view_cat->parse("view_cat.productTable.products.buy_btn");
		} elseif ($productResults[$i]['useStockLevel'] == 1 && $productResults[$i]['stock_level'] > 0) {
			$view_cat->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
			$view_cat->assign("PRODUCT_ID",$productResults[$i]['productId']);
			$view_cat->parse("view_cat.productTable.products.buy_btn");
		} elseif ($productResults[$i]['useStockLevel'] == 0) {
			$view_cat->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
			$view_cat->assign("PRODUCT_ID",$productResults[$i]['productId']);
			$view_cat->parse("view_cat.productTable.products.buy_btn");
		}

		$view_cat->assign("BTN_MORE",$lang['front']['viewCat']['more']);
		$view_cat->assign("PRODUCT_ID",$productResults[$i]['productId']);
		$view_cat->assign("COLOR",cellColor($i, $tdEven="homeProdsEven", $tdOdd="homeProdsOdd"));
		$view_cat->assign("PART_NO",$productResults[$i]['productCode']);
		

		if ($productResults[$i]['stock_level'] < 1 && $productResults[$i]['useStockLevel'] == 1 && $productResults[$i]['digital'] == 0) {
			$view_cat->assign("TXT_OUTOFSTOCK", $lang['front']['viewCat']['out_of_stock']);
		} else {
			$view_cat->assign("TXT_OUTOFSTOCK", "");
		}
		
		$view_cat->parse("view_cat.productTable.products");
	}
	$view_cat->parse("view_cat.productTable");

} elseif(isset($_GET['searchStr'])) {
	$view_cat->assign("TXT_NO_PRODUCTS", $lang['front']['viewCat']['no_products_match']." ".treatGet($_GET['searchStr']));
	$view_cat->parse("view_cat.noProducts");
} else {
	$view_cat->assign("TXT_NO_PRODUCTS", $lang['front']['viewCat']['no_prods_in_cat']);
	$view_cat->parse("view_cat.noProducts");
}

$view_cat->parse("view_cat");
$page_content = $view_cat->text("view_cat");
?>