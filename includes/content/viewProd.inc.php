<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.6
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   (c) 2005 Devellion Limited
|   Devellion Limited,
|   Westfield Lodge,
|   Westland Green,
|   Little Hadham,
|   Nr Ware, HERTS.
|   SG11 2AL
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Wednesday, 9th October 2005
|   Email: info (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	viewProduct.inc.php
|   ========================================
|	Displays the Product in Detail
+--------------------------------------------------------------------------
*/

if(!isset($config)){
	echo "<html>\r\n<head>\r\n<title>Forbidden 403</title>\r\n</head>\r\n<body><h3>Forbidden 403</h3>\r\nThe document you are requesting is forbidden.\r\n</body>\r\n</html>";
	exit;
}
// query database
$_GET['productId'] = treatGet($_GET['productId']);

//var_dump($ccUserData[0]['email']);

///////////////////////////////////////
//Set cookie for recently viewed prods

if (isset($_COOKIE['recentProds'])) {
	$currentRecentProds = $_COOKIE['recentProds'];
	$recentProdArray = explode('|', $currentRecentProds);
	if (!in_array($_GET['productId'], $recentProdArray)) {
		array_push($recentProdArray, $_GET['productId']);
	}
	$newRecentProds = '';
	if (count($recentProdArray) <= 5) {
		for($i=0; $i<count($recentProdArray); $i++) {
			if ($i == (count($recentProdArray)-1)) {
				$newRecentProds .= $recentProdArray[$i];
			} else {
				$newRecentProds .= $recentProdArray[$i].'|';
			}
		}
	} else {
		array_shift($recentProdArray);
		for($i=0; $i<count($recentProdArray); $i++) {
			if ($i == (count($recentProdArray)-1)) {
				$newRecentProds .= $recentProdArray[$i];
			} else {
				$newRecentProds .= $recentProdArray[$i].'|';
			}
		}
	}
} else {
	$newRecentProds = $_GET['productId'];
}
$timeAppend = 60*60*2;
$expiryTime = time()+$timeAppend;
setcookie('recentProds', $newRecentProds, $expiryTime, '/');

//End cookie setting
///////////////////////////////////////

/* <rf> search engine friendly mod */
if($config['seftags']) {
	$query = "SELECT prod_metatitle, productBrand, prod_metadesc, prod_metakeywords, productId, purMeth, productCode, quantity, name, description, image, noImages, price, popularity, tradePrice, tradeSale, sale_price, stock_level, useStockLevel, digital, digitalDir, cat_name, ".$glob['dbprefix']."CubeCart_inventory.cat_id, cat_father_id, cat_image, per_ship, item_ship, item_int_ship, per_int_ship, noProducts FROM ".$glob['dbprefix']."CubeCart_inventory INNER JOIN ".$glob['dbprefix']."CubeCart_category ON ".$glob['dbprefix']."CubeCart_inventory.cat_id = ".$glob['dbprefix']."CubeCart_category.cat_id where productId = ".$db->mySQLSafe($_GET['productId'])." AND CubeCart_inventory.showProd = 1";
} else {
	$query = "SELECT productId, productBrand, productCode, quantity, name, description, image, purMeth, noImages, price, popularity, sale_price, tradePrice, tradeSale, stock_level, useStockLevel, digital, digitalDir, cat_name, ".$glob['dbprefix']."CubeCart_inventory.cat_id, cat_father_id, cat_image, per_ship, item_ship, item_int_ship, per_int_ship, noProducts FROM ".$glob['dbprefix']."CubeCart_inventory INNER JOIN ".$glob['dbprefix']."CubeCart_category ON ".$glob['dbprefix']."CubeCart_inventory.cat_id = ".$glob['dbprefix']."CubeCart_category.cat_id where productId = ".$db->mySQLSafe($_GET['productId'])." AND CubeCart_inventory.showProd = 1";
}
/* <rf> end mod */


$prodArray = $db->select($query);

//var_dump($prodArray);
/* <rf> search engine friendly mod */
if($config['seftags']) {

	// get the native language for the category name
	$resultForeign = $db->select("SELECT cat_master_id as cat_id, cat_name FROM ".$glob['dbprefix']."CubeCart_cats_lang WHERE cat_lang = '".$lang_folder."' AND cat_master_id = ".$db->mySQLSafe($prodArray[0]['cat_id']));		
	if($resultForeign == TRUE){			
		$prodArray[0]['cat_name'] = $resultForeign[0]['cat_name'];		
	}

	// get alternate language title for this product
	$sefval = "";
	$sefLangProdTitle = $prodArray[0]['name'];
	if(($sefval = prodAltLang($prodArray[0]['productId'])) == TRUE){			
		// change the titles as they make more sense to the user if they are in their native languages
		// however to the search engine they will always be in the default language regardless as they can't change languages
		$sefLangProdTitle = $sefval['name'];	
		// don't worry about description it will always be in the default language regardless as search 
		// engines can't change languages	
	}

	// create title and metas
	$prevDirSymbol = $config['dirSymbol'];
	$config['dirSymbol'] = ' - ';
        if($config['sefprodnamefirst']) {
		$meta['siteTitle'] = $prodArray[0]['name'];
	} else {
                $meta['siteTitle'] = $prodArray[0]['name'];
        }
	$config['dirSymbol'] = $prevDirSymbol;
	$meta['sefSiteTitle'] = $prodArray[0]['prod_metatitle']; 
	$meta['sefSiteDesc'] = $prodArray[0]['prod_metadesc'];
	$meta['sefSiteKeywords'] = $prodArray[0]['prod_metakeywords'];
} else {
	$meta['siteTitle'] = $prodArray[0]['name'];
}
/* <rf> end mod */
//$meta['metaDescription'] = substr(strip_tags($prodArray[0]['description']),0,160);

$robz = trim(strip_tags(str_replace("\r\n", "", $prodArray[0]['description'])));

$count = 0;
$output = "";

for($i=0; $i<strlen($robz); $i++){
	$temp = substr($robz, $i, 1);
	
	if($temp == " "){
		if($count == 0){
			$output .= $temp;
			$count++;
		}
	}else{
		$output .= $temp;
		$count = 0;
	}
}
	
$meta['metaDescription'] = substr($output, 0, 156);

$view_prod = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/viewProd.tpl");

if($prodArray == TRUE){
	
	$wishMsg = '';
	if (isset($_GET['wishlist']) && $_GET['wishlist'] == 'add') {
		if (isset($ccUserData[0]['customer_id']) && $ccUserData[0]['customer_id'] > 0) {
			$wishlistCheck = $db->select("
				SELECT id
				FROM ".$glob['dbprefix']."CubeCart_wishlist
				WHERE customerId = ".$db->mySQLSafe($ccUserData[0]['customer_id'])."
				AND productID = ".$db->mySQLSafe($_GET['productId'])."
				LIMIT 1
			");	
			if ($wishlistCheck) {
				$wishMsg = '<span class="wishWarn">This product is already on your wishlist.</span><br /><br />';
			} else {
				$rec['customerId'] = $db->mySQLSafe($ccUserData[0]['customer_id']);
				$rec['productId'] = $db->mySQLSafe($_GET['productId']);
				$insert = $db->insert($glob['dbprefix']."CubeCart_wishlist", $rec);
				if($insert) {
					$wishMsg = '<span class="wishMsg">This product is now on your wishlist.</span><br /><br />';
				} else {
					$wishMsg = '<span class="wishWarn">Sorry, there was an error when adding this product to your wishlist.</span><br /><br />';
				}
			}
		} else {
			$wishMsg = '<span class="wishWarn">You must be logged in to add a product to your wishlist.</span><br /><br />';
		}
		
		$view_prod->assign("WISHLIST_MSG",$wishMsg);
	}
	
	$val = "";
	
	if(($val = prodAltLang($prodArray[0]['productId'])) == TRUE){
				
		$prodArray[0]['name'] = $val['name'];
		$prodArray[0]['description'] = $val['description'];
			
	}

// update amount of views
	$upPop['popularity'] = "popularity+1"; 
	$db->update($glob['dbprefix']."CubeCart_inventory",$upPop,"productId = ".$db->mySQLSafe($_GET['productId']));
	
	$view_prod->assign("LANG_PRODTITLE",$lang['front']['viewProd']['product']);
	$view_prod->assign("LANG_PRODINFO",$lang['front']['viewProd']['product_info']);
	$view_prod->assign("LANG_PRICE",$lang['front']['viewProd']['price']);
	$view_prod->assign("LANG_PRODCODE",$lang['front']['viewProd']['product_code']);
	$view_prod->assign("LANG_TELLFRIEND",$lang['front']['viewProd']['tellafriend']);
	$view_prod->assign("TXT_PRODTITLE",validHTML($prodArray[0]['name']));
	$view_prod->assign("TXT_DESCRIPTION",$prodArray[0]['description']);
	
	$brand = $db->select("SELECT brandImage, brandName FROM ".$glob['dbprefix']."CubeCart_brands WHERE id = ".$db->mySQLSafe($prodArray[0]['productBrand']));
	$view_prod->assign("BRAND_IMG_SRC",$brand[0]['brandImage']);
	$view_prod->assign("BRAND_NAME",$brand[0]['brandName']);
	
	
	if(isset($_GET['add']) && isset($_GET['quan'])){
		$view_prod->assign("CURRENT_URL",str_replace(array("&amp;add=".$_GET['add'],"&amp;quan=".$_GET['quan']),"",currentPage()));
	} else {
		$view_prod->assign("CURRENT_URL",currentPage());
	}
	
//check text for email me if no in stock
	$customer = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_stock_notify WHERE email = ".$db->mySQLSafe($ccUserData[0]['email'])." AND productId = ".$db->mySQLSafe($prodArray[0]['productId']));		
	
	//Check if it is a trade customer
	if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
		if($ccUserData[0]['tax'] == 1){
			if(salePrice($prodArray[0]['price'], $prodArray[0]['sale_price'])==FALSE){
				$view_prod->assign("TXT_PRICE",priceFormat($prodArray[0]['tradePrice']));
				$view_prod->assign("TXT_SALE_PRICE", "");
			}else{
				$view_prod->assign("TXT_PRICE","<strike>".priceFormat($prodArray[0]['tradePrice'])."</strike>");
				$salePrice = salePrice($prodArray[0]['tradePrice'], $prodArray[0]['tradeSale']);
				$view_prod->assign("TXT_SALE_PRICE", priceFormat($salePrice));
			}
		}
		if($ccUserData[0]['tax'] == 2){
			if(salePrice($prodArray[0]['price'], $prodArray[0]['sale_price'])==FALSE){
				$vatTradePrice = getProductPriceWithTax($_GET['productId'], 'tradePrice');
				$view_prod->assign("TXT_PRICE",priceFormat($vatTradePrice));
				$view_prod->assign("TXT_SALE_PRICE", "");
			}else{
				$vatTradePrice = getProductPriceWithTax($_GET['productId'], 'tradePrice');
				$view_prod->assign("TXT_PRICE","<strike>".priceFormat($prodArray[0]['tradePrice'])."</strike>");
				$vatSalePrice = getProductPriceWithTax($_GET['productId'], 'tradeSale');
				$salePrice = salePrice($vatTradePrice, $vatSalePrice);
				$view_prod->assign("TXT_SALE_PRICE", priceFormat($salePrice));
			}
		}
	} else {
		if($ccUserData[0]['tax'] == 1){
			if(salePrice($prodArray[0]['price'], $prodArray[0]['sale_price'])==FALSE){
				$view_prod->assign("TXT_PRICE",priceFormat($prodArray[0]['price']));
				$view_prod->assign("TXT_SALE_PRICE", "");
			}else{
				$view_prod->assign("TXT_PRICE","<strike>".priceFormat($prodArray[0]['price'])."</strike>");
				$salePrice = salePrice($prodArray[0]['price'], $prodArray[0]['sale_price']);
				$view_prod->assign("TXT_SALE_PRICE",priceFormat($salePrice));
			}
		}
		if($ccUserData[0]['tax'] == 2){
			if(salePrice($prodArray[0]['price'], $prodArray[0]['sale_price'])==FALSE){
				$vatPrice = getProductPriceWithTax($_GET['productId'], 'price');
				$view_prod->assign("TXT_PRICE",priceFormat($vatPrice));
				$view_prod->assign("TXT_SALE_PRICE", "");
			}else{
				$vatPrice = getProductPriceWithTax($_GET['productId'], 'price');
				$view_prod->assign("TXT_PRICE","<strike>".priceFormat($vatPrice)."</strike>");
				$vatSalePrice = getProductPriceWithTax($_GET['productId'], 'sale_price');
				$salePrice = salePrice($vatPrice, $vatSalePrice);
				$view_prod->assign("TXT_SALE_PRICE",priceFormat($salePrice));
			}
		}
		$view_prod->parse("view_prod.prod_true.price");			
	}
	
	//Check if it is a trade customer	
	
	
	
	$view_prod->assign("TXT_PRODCODE", $prodArray[0]['productCode']);

	$view_prod->assign("CURRENT_DIR",getCatDir($prodArray[0]['cat_name'],$prodArray[0]['cat_father_id'], $prodArray[0]['cat_id'],$link=TRUE));

	$view_prod->assign("LANG_QUAN",$lang['front']['viewProd']['quantity']);

	$view_prod->assign("PRODUCT_ID",$prodArray[0]['productId']);



	if(!empty($prodArray[0]['image'])){
		$view_prod->assign("IMG_SRC",$glob['rootRel'] . "images/uploads/".$prodArray[0]['image']);
		$view_prod->assign("IMG_SRC_THUMB",$glob['rootRel'] . "images/uploads/thumbs/thumb_".$prodArray[0]['image']);
	} else {
		$view_prod->assign("IMG_SRC","skins/".$config['skinDir']."/styleImages/nophoto.gif");
		$view_prod->assign("IMG_SRC_THUMB","skins/".$config['skinDir']."/styleImages/nophoto.gif");
	}
	
	if($prodArray[0]['noImages']>0){
		$view_prod->assign("LANG_MORE_IMAGES",$lang['front']['viewProd']['more_images']);
		$view_prod->parse("view_prod.prod_true.more_images");
		
		
		// start of multiple image on page mod - query database
$_GET['productId'] = treatGet($_GET['productId']);

$results = $db->select("SELECT img FROM ".$glob['dbprefix']."CubeCart_img_idx WHERE productId = ".$db->mySQLsafe($_GET['productId']));

$mainImage = $db->select("SELECT image FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = ".$db->mySQLsafe($_GET['productId']));

if($results == TRUE){
	// start loop
	
	for ($i=0; $i<count($results); $i++){
 		
		$view_prod->assign("VALUE_SRC",$glob['rootRel'] . "images/uploads/thumbs/thumb_".$results[$i]['img']);
		
		if(file_exists("images/uploads/thumbs/thumb_".$results[$i]['img'])){
			
			$view_prod->assign("VALUE_THUMB_SRC","images/uploads/thumbs/thumb_".$results[$i]['img']);
			$sizeThumb = getimagesize("images/uploads/thumbs/thumb_".$results[$i]['img']);
			$view_prod->assign("VALUE_THUMB_WIDTH",$sizeThumb[0]);
			
		} else {
			
			$view_prod->assign("VALUE_THUMB_SRC","images/uploads/thumbs/thumb_".$results[$i]['img']);
			$view_prod->assign("VALUE_THUMB_WIDTH",$config['gdthumbSize']);
			
			
		}
		$view_prod->assign("ALT_THUMB",$lang['front']['popup']['thumb_alt']);
		$view_prod->parse("view_prod.prod_true.more_images.thumbs");
	
	} // end loop 
	// original image
		$view_prod->parse("view_prod.prod_true.more_images");
		$view_prod->assign("VALUE_SRC", $glob['rootRel'] . "images/uploads/thumbs/thumb_".$mainImage[0]['image']);
		
		if(file_exists("images/uploads/thumbs/thumb_".$mainImage[0]['image'])){
			
			$view_prod->assign("VALUE_THUMB_SRC","images/uploads/thumbs/thumb_".$mainImage[0]['image']);
			$sizeThumb = getimagesize("images/uploads/thumbs/thumb_".$mainImage[0]['image']);
			$view_prod->assign("VALUE_THUMB_WIDTH",$sizeThumb[0]);
			
		} else {
			
			$view_prod->assign("VALUE_THUMB_SRC","images/uploads/".$mainImage[0]['image']);
			$view_prod->assign("VALUE_THUMB_WIDTH",$config['gdthumbSize']);
			
		}
		$view_prod->assign("ALT_THUMB",$lang['front']['popup']['thumb_alt']);
		$view_prod->parse("view_prod.prod_true.thumbs");
	
}
		
	}
	
	
	
	// start: Related Items Mod
	if(evoHideBol(31)==false){
	$config_rp = fetchDbConfig("Related_Products");
	if ($config_rp['status'] && $config_rp['auto'])
	{
		$name = strtolower(strip_tags($prodArray[0]['name']));

		// cut words to be ignored
		$ignore = explode(",",strtolower($config_rp['ignore']));
		foreach($ignore as $term) {
			$ignore_terms[] = trim($term);
		}
		if (is_array($ignore_terms)) {
			$name = str_replace($ignore_terms, "", $name);
		}

		$desc = "";
		if ($config_rp['use_desc'])
		{
			$desc = strtolower($prodArray[0]['description']);

			// search for "description terminators"
			$delimArray = explode(",",strtolower($config_rp['desc_up_to']));
			if ($delimArray[0]!="")
			{
				foreach ($delimArray as $delim)
				{
					$pos = strpos($desc, trim($delim));
					if ($pos > 0) {
						$delim_pos[] = $pos;
					}
				}
				// cut at first "terminator"
				if (isset($delim_pos))
				{
					if (count($delim_pos)>1) {
						$end = min($delim_pos);
					} else {
						$end = $delim_pos[0];
					}
					$desc = substr($desc,0,$end);
				}
			}

			$desc = strip_tags($desc);

			// cut words to be ignored
			if (is_array($ignore_terms)) {
				$desc = str_replace($ignore_terms, "", $desc);
			}
		}
			
		// search terms
		$search1 = $db->mySQLSafe($name);
		if ($desc != "") {
			$search2 = $db->mySQLSafe($desc);
		}
	}
	if ($config_rp['status'])
	{
		// manual
		$query = "SELECT relatedId as productId, name, image, price, sale_price, tradePrice, tradeSale, description FROM ".$glob['dbprefix']."CubeCart_mod_related_prods as rel, ".$glob['dbprefix']."CubeCart_inventory as inv WHERE rel.relatedId=inv.productId AND rel.productId=".$db->mySQLSafe($prodArray[0]['productId'])." ORDER BY id";
		$manual_related = $db->select($query);
	}
	if ($config_rp['status'] && $config_rp['auto'])
	{
		// auto search
		$manual_count = 0;
		$not_in = $prodArray[0]['productId'];
		if (is_array($manual_related))
		{
			foreach ($manual_related as $man) {
				$not_in .= ",".$man['productId'];
			}
			$manual_count = count($manual_related);
		}

		$relevance_cutoff = 3;

		if (empty($search2))
		{
			$query = "SELECT MATCH (name,description) AGAINST (".$search1.") AS score1, productId, name, image, price, sale_price, tradePrice, tradeSale, description FROM ".$glob['dbprefix']."CubeCart_inventory WHERE MATCH (name,description) AGAINST (".$search1.") > ".$relevance_cutoff." AND productId NOT IN (".$not_in.") ORDER BY MATCH (name,description) AGAINST (".$search1.") DESC LIMIT ".($config_rp['max_shown']+1);

		}
		else
		{
			$query = "SELECT MATCH (name,description) AGAINST (".$search1.") AS score1, MATCH (name,description) AGAINST (".$search2.") AS score2, productId, name, price, sale_price, tradePrice, tradeSale, image, description FROM ".$glob['dbprefix']."CubeCart_inventory WHERE MATCH (name,description) AGAINST (".$search1.") + MATCH (name,description) AGAINST (".$search2.") > ".$relevance_cutoff." AND productId NOT IN (".$not_in.") ORDER BY MATCH (name,description) AGAINST (".$search1.") + MATCH (name,description) AGAINST (".$search2.") DESC LIMIT ".($config_rp['max_shown']+1);
		}
		$auto_related = $db->select($query);

		if (is_array($manual_related) && is_array($auto_related)) {

			$related = array_merge($manual_related,$auto_related);

		} elseif (is_array($auto_related) ) {

			$related = $auto_related;

		} elseif (is_array($manual_related)) {

			$related = $manual_related;

		}
	}
	if ($config_rp['status'])
	{
		if ($config_rp['auto']==FALSE && is_array($manual_related)) {
			$related = $manual_related;
		}

		$view_prod->assign("TXT_RELATED_PRODUCTS",$lang['front']['viewProd']['related_products']);
		$view_prod->assign("TXT_NONE",$lang['front']['viewProd']['none']);
		
				
		if (isset($related))
		{
			for ($i=0; $i<count($related) && $i<$config_rp['max_shown']; $i++)
			{
				// reset width, may be set later if necessary
				$view_prod->assign("VALUE_THUMB_WIDTH","");

				$view_prod->assign("VALUE_RELATED_LINK","index.php?act=viewProd&productId=".$related[$i]['productId']);
				$view_prod->assign("VALUE_RELATED_NAME",$related[$i]['name']);
				
				
				if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 0){
					$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
					$discount = $tradeDiscount[0]['discount']/100;
					$related[$i]['tradePrice'] = $related[$i]['price'] - ($related[$i]['price'] * $discount);
					$related[$i]['tradeSale'] = $related[$i]['sale_price'] - ($related[$i]['sale_price'] * $discount);
					if($ccUserData[0]['tax'] == 1){
						if(salePrice($related[$i]['tradePrice'], $related[$i]['tradeSale'])==FALSE){
							$view_prod->assign("REL_PRICE",priceFormat($related[$i]['tradePrice']));
							$view_prod->assign("REL_PRICE_SALE", "");
						}else{
							$view_prod->assign("REL_PRICE","<strike>".priceFormat($related[$i]['tradePrice'])."</strike>");
							$salePrice = salePrice($related[$i]['tradePrice'], $related[$i]['tradeSale']);
							$view_prod->assign("REL_PRICE_SALE", priceFormat($salePrice));
						}
					}
					if($ccUserData[0]['tax'] == 2){
						if(salePrice($related[$i]['tradePrice'], $related[$i]['tradeSale'])==FALSE){
							$vatTradePrice = getProductPriceWithTax($related[$i]['productId'], 'price');
							$view_prod->assign("REL_PRICE",priceFormat($vatTradePrice));
							$view_prod->assign("REL_PRICE_SALE", "");
						}else{
							$vatTradePrice = getProductPriceWithTax($related[$i]['productId'], 'price');
							$vatTradePrice = $vatTradePrice - ($vatTradePrice * $discount);
							$view_prod->assign("REL_PRICE","<strike>".priceFormat($vatTradePrice)."</strike>");
							$vatSalePrice = getProductPriceWithTax($related[$i]['productId'], 'sale_price');
							$salePrice = salePrice($vatTradePrice, $vatSalePrice);
							$salePrice = $salePrice - ($salePrice * $discount);
							$view_prod->assign("REL_PRICE_SALE", priceFormat($salePrice));
						}
					}
				} else {
					if($ccUserData[0]['tax'] == 1){
						if(salePrice($related[$i]['price'], $related[$i]['sale_price'])==FALSE){
							$view_prod->assign("REL_PRICE",priceFormat($related[$i]['price']));
							$view_prod->assign("REL_PRICE_SALE", "");
						}else{
							$view_prod->assign("REL_PRICE","<strike>".priceFormat($related[$i]['price'])."</strike>");
							$salePrice = salePrice($related[$i]['price'], $related[$i]['sale_price']);
							$view_prod->assign("REL_PRICE_SALE",priceFormat($salePrice));
						}
					}
					if($ccUserData[0]['tax'] == 2){
						if(salePrice($related[$i]['price'], $related[$i]['sale_price'])==FALSE){
							$vatPrice = getProductPriceWithTax($related[$i]['productId'], 'price');
							$view_prod->assign("REL_PRICE",priceFormat($vatPrice));
							$view_prod->assign("REL_PRICE_SALE", "");
						}else{
							$vatPrice = getProductPriceWithTax($related[$i]['productId'], 'price');
							$view_prod->assign("REL_PRICE","<strike>".priceFormat($vatPrice)."</strike>");
							$vatSalePrice = getProductPriceWithTax($related[$i]['productId'], 'sale_price');
							$salePrice = salePrice($vatPrice, $vatSalePrice);
							$view_prod->assign("REL_PRICE_SALE",priceFormat($salePrice));
						}
					}
				}
				
				
				
				if(file_exists("images/uploads/thumbs/thumb_".$related[$i]['image'])){
					$view_prod->assign("VALUE_RELATED_THUMB","images/uploads/thumbs/thumb_".$related[$i]['image']);
				} elseif(!empty($related[$i]['image']) && file_exists("images/uploads/thumbs/thumb_".$related[$i]['image'])) {
					$view_prod->assign("VALUE_RELATED_THUMB","images/uploads/thumbs/thumb_".$related[$i]['image']);
					$view_prod->assign("VALUE_THUMB_WIDTH","width=\"".$config['gdthumbSize']."\"");
				} else {
					$view_prod->assign("VALUE_RELATED_THUMB","skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif");
				}

				$view_prod->parse("view_prod.prod_true.related_prods_true.repeat_related_prods");
			}
			$view_prod->parse("view_prod.prod_true.related_prods_true");
		}
		else
		{
			$view_prod->parse("view_prod.prod_true.related_prods_false");
		}
	}
	}
	// end: Related Items Mod
	
	//Start wishlist
	if(evoHideBol(82)==false){
	if (isset($ccUserData[0]['customer_id']) && $ccUserData[0]['customer_id'] > 0) {
		$onWishlist = $db->select("
			SELECT *
			FROM ".$glob['dbprefix']."CubeCart_wishlist
			WHERE customerId = ".$db->mySQLSafe($ccUserData[0]['customer_id'])."
			AND productID = ".$db->mySQLSafe($_GET['productId'])."
			LIMIT 1
		");	
		if($onWishlist) {
			$view_prod->parse("view_prod.prod_true.buy_btn.wishlist_on");	
		} else {
			$view_prod->parse("view_prod.prod_true.buy_btn.wishlist_add");
		}
	} else {
		$view_prod->parse("view_prod.prod_true.buy_btn.wishlist_login");
	}
	}
	
	if(evoHideBol(81)==false){
		$view_prod->parse("view_prod.prod_true.buy_btn.addthis");
	}
	
	
	#############################################################
	###	stock level stuff
	#############################################################

	$view_prod->assign("LANG_DIR_LOC",$lang['front']['viewProd']['location']);
	
	
	//purchase method
	/**/
	if($prodArray[0]['purMeth']==2){
		$view_prod->parse("view_prod.prod_true.buy_btn.add_btn.add_btn2");
		$view_prod->parse("view_prod.prod_true.buy_btn.add_btn.quote_btn");
	}elseif($prodArray[0]['purMeth']==1){
		$view_prod->parse("view_prod.prod_true.buy_btn.add_btn.quote_btn");
	}else{
		$view_prod->parse("view_prod.prod_true.buy_btn.add_btn.add_btn2");
	}
	
	
	
	/*if($prodArray[0]['stock_level']<1 && $prodArray[0]['useStockLevel']==1 && $prodArray[0]['digital']==0){
		//echo "out of stock and I know it";
		$view_prod->parse("view_prod.prod_true.out_of_stock");
		
	}*/
	if($config['outofstockPurchase']==1){
		//echo "in stock";
		//echo "1";
		
		$view_prod->assign("BTN_ADDBASKET",$lang['front']['viewProd']['add_to_basket']);
		$view_prod->parse("view_prod.prod_true.in_stock");		
		$view_prod->parse("view_prod.prod_true.buy_btn.add_btn");
				
	//else if product is set to use stock level and it's greater than 0
	} elseif($prodArray[0]['useStockLevel']==1 ){//&& $prodArray[0]['stock_level']>0
		//echo "2";	
		$view_prod->assign("BTN_ADDBASKET",$lang['front']['viewProd']['add_to_basket']);
		$view_prod->parse("view_prod.prod_true.in_stock");		
		$view_prod->parse("view_prod.prod_true.buy_btn.add_btn");	
		
	//or if the product isn't using it's stock level			
	} elseif($prodArray[0]['useStockLevel']==0){
		//echo "3";
		$view_prod->assign("BTN_ADDBASKET",$lang['front']['viewProd']['add_to_basket']);
		$view_prod->parse("view_prod.prod_true.buy_btn.add_btn");
		$view_prod->parse("view_prod.prod_true.in_stock");		
		
	}
	
	
	//fix - ALWAYS show buy_btn
	$view_prod->parse("view_prod.prod_true.buy_btn");
	
	
	#############################################################
	###	stock level stuff
	#############################################################


// build sql query for product options luuuuuurvely
$noOptions=0;
$optionId=-1;
$firstOption=$db->select("
SELECT MIN(top.parent)
FROM ".$glob['dbprefix']."CubeCart_options_bot AS bot
INNER JOIN ".$glob['dbprefix']."CubeCart_options_mid AS mid
ON bot.value_id=mid.value_id 
INNER JOIN ".$glob['dbprefix']."CubeCart_options_top AS top
ON bot.option_id=top.option_id 
WHERE bot.product=".$db->mySQLSafe($prodArray[0]['productId'])."
GROUP BY mid.value_id
ORDER BY top.parent
");
if($firstOption){
	$options = $db->select($query = "
		SELECT *
		FROM ".$glob['dbprefix']."CubeCart_options_bot AS bot 
		INNER JOIN ".$glob['dbprefix']."CubeCart_options_mid AS mid 
		ON mid.value_id = bot.value_id 
		INNER JOIN ".$glob['dbprefix']."CubeCart_options_top AS top 
		ON bot.option_id = top.option_id 
		WHERE product =".$db->mySQLSafe($_GET['productId'])."
		AND top.parent= ".$db->mySQLSafe($firstOption[0]['MIN(top.parent)'])."
		ORDER BY top.parent DESC, ordering ASC");
	if($options){
		$optionId=$options[0]['parent'];
	}else{
		$optionId=0;
	}
}
//use loopcount to prevent any instances of infinate loops
$loopCount=0;
if($optionId>=0){
	while(($options==true || $optionId==0) && $loopCount<20){
		$query = "
		SELECT * 
		FROM ".$glob['dbprefix']."CubeCart_options_bot AS bot 
		INNER JOIN ".$glob['dbprefix']."CubeCart_options_mid AS mid 
		ON mid.value_id = bot.value_id 
		INNER JOIN ".$glob['dbprefix']."CubeCart_options_top AS top 
		ON bot.option_id = top.option_id 
		WHERE product =".$db->mySQLSafe($_GET['productId'])." 
		AND top.parent=".$db->mySQLSafe($optionId)." 
		ORDER BY top.parent DESC, ordering ASC";
	
		$options = $db->select($query); 
	
		if($options == TRUE){
			$optionId=$options[0]['option_id'];
			$view_prod->assign("TXT_PROD_OPTIONS",$lang['front']['viewProd']['prod_opts']);
			for ($i=0; $i<count($options); $i++){
				$view_prod->assign("VAL_ASSIGN_ID", $options[$i]['assign_id']);
				$view_prod->assign("VAL_VALUE_NAME", $options[$i]['value_name']);
				if($options[$i]['option_price']>0){
					$view_prod->assign("VAL_OPT_SIGN",$options[$i]['option_symbol']);
					$view_prod->assign("VAL_OPT_PRICE",priceFormat($options[$i]['option_price']));
					$view_prod->parse("view_prod.prod_true.prod_opts.repeat_options.repeat_values.repeat_price");
				}
				$view_prod->parse("view_prod.prod_true.prod_opts.repeat_options.repeat_values");	
				if($options[$i]['option_id']!==$options[$i+1]['option_id']){
					$view_prod->assign("VAL_OPTS_NAME", $options[$i]['option_name']);
					
					$view_prod->parse("view_prod.prod_true.prod_opts.repeat_options");
				}
			}
		}
		
		if($optionId==0 && $options==false){
			$noOptions=1;
		}
		$a++;
	} 
	if($noOptions==0){
		$view_prod->parse("view_prod.prod_true.prod_opts");
	}
}

	///START: Customer Reviews Mod 
	if(evoHideBol(30)==false){
	$display_limit = 3;   // set this to the number of reviews to display (recommended max 5)       
	
	$productId = $prodArray[0]['productId'];
	$get_reviews = "SELECT * FROM ".$glob['dbprefix']."CubeCart_store_ratings WHERE product_id='$productId' and status='1' ORDER BY id DESC";        
	$show_reviews = $db->select($get_reviews);  
	if($show_reviews == TRUE){  
		$view_prod->assign("CUST_REV",$cust_rev);
		//  start loop  
		
			for($counter=0; $counter<count($show_reviews); $counter++){
			  if($counter<$display_limit) {
				  if ($show_reviews[$counter]['stars']==0) {
						$view_prod->assign("REV_STARS","<img src=\"images/stars-1.gif\" width=\"109\" height=\"20\" class=\"stars\">");
					 	$view_prod->assign("REV_NAME",stripslashes ($show_reviews[$counter]['cust_name']));
						 $view_prod->assign("REV_LOC",stripslashes ($show_reviews[$counter]['location']));
						 $view_prod->assign("REV_DATE", $show_reviews[$counter]['date']);
						 $view_prod->assign("REV_COMMENTS",stripslashes ($show_reviews[$counter]['comments'])); 
						 $view_prod->parse("view_prod.prod_true.customer_reviews.reviews");  
				  }elseif($show_reviews[$counter]['stars']==1){
						 $view_prod->assign("REV_STARS","<img src=\"images/stars-2.gif\" width=\"109\" height=\"20\" class=\"stars\">");
						 $view_prod->assign("REV_NAME",stripslashes ($show_reviews[$counter]['cust_name']));
						 $view_prod->assign("REV_LOC",stripslashes ($show_reviews[$counter]['location']));
						 $view_prod->assign("REV_DATE", $show_reviews[$counter]['date']);
						 $view_prod->assign("REV_COMMENTS",stripslashes ($show_reviews[$counter]['comments'])); 
					     $view_prod->parse("view_prod.prod_true.customer_reviews.reviews"); 
				  }elseif($show_reviews[$counter]['stars']==2){
						 $view_prod->assign("REV_STARS","<img src=\"images/stars-3.gif\" width=\"109\" height=\"20\" class=\"stars\">"); 
						 $view_prod->assign("REV_NAME",stripslashes ($show_reviews[$counter]['cust_name']));
						 $view_prod->assign("REV_LOC",stripslashes ($show_reviews[$counter]['location']));
						 $view_prod->assign("REV_DATE", $show_reviews[$counter]['date']);
						 $view_prod->assign("REV_COMMENTS",stripslashes ($show_reviews[$counter]['comments'])); 
					     $view_prod->parse("view_prod.prod_true.customer_reviews.reviews"); 
				  }else if($show_reviews[$counter]['stars']==3){  
						 $view_prod->assign("REV_STARS","<img src=\"images/stars-4.gif\" width=\"109\" height=\"20\" class=\"stars\">"); 
						 $view_prod->assign("REV_NAME",stripslashes ($show_reviews[$counter]['cust_name']));
						 $view_prod->assign("REV_LOC",stripslashes ($show_reviews[$counter]['location']));
						 $view_prod->assign("REV_DATE", $show_reviews[$counter]['date']);
						 $view_prod->assign("REV_COMMENTS",stripslashes ($show_reviews[$counter]['comments'])); 
					     $view_prod->parse("view_prod.prod_true.customer_reviews.reviews"); 
				  }else if($show_reviews[$counter]['stars']==4){    
						 $view_prod->assign("REV_STARS","<img src=\"images/stars-5.gif\" width=\"109\" height=\"20\" class=\"stars\">"); 
						 $view_prod->assign("REV_NAME",stripslashes ($show_reviews[$counter]['cust_name']));
						 $view_prod->assign("REV_LOC",stripslashes ($show_reviews[$counter]['location']));
						 $view_prod->assign("REV_DATE", $show_reviews[$counter]['date']);
						 $view_prod->assign("REV_COMMENTS",stripslashes ($show_reviews[$counter]['comments'])); 
					     $view_prod->parse("view_prod.prod_true.customer_reviews.reviews");  
				  }else{
				  		 $view_prod->assign("REV_STARS","<img src=\"images/stars-5.gif\" width=\"109\" height=\"20\" class=\"stars\">"); 
						 $view_prod->assign("REV_NAME",stripslashes ($show_reviews[$counter]['cust_name']));
						 $view_prod->assign("REV_LOC",stripslashes ($show_reviews[$counter]['location']));
						 $view_prod->assign("REV_DATE", $show_reviews[$counter]['date']);
						 $view_prod->assign("REV_COMMENTS",stripslashes ($show_reviews[$counter]['comments'])); 
					     $view_prod->parse("view_prod.prod_true.customer_reviews.reviews"); 
				  }
			}    
		}
	}
	$view_prod->parse("view_prod.prod_true.customer_reviews");
	}
	//END: Customer Reviews Mod 
	
	$view_prod->parse("view_prod.prod_true");

} else {// end if product array is true
	
	$view_prod->assign("LANG_PRODUCT_EXPIRED",$lang['front']['viewProd']['prod_not_found']);
	$view_prod->parse("view_prod.prod_false");

}

$view_prod->parse("view_prod");
$page_content = $view_prod->text("view_prod");
?>
