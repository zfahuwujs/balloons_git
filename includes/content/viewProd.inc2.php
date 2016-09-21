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
if(isset($_GET['addReview']) && isset($_GET['productId'])){
	header('location: /index.php?act=viewProd&productId='.$_GET['productId'].'#3');
	exit;
}
// query database
$_GET['productId'] = treatGet($_GET['productId']);

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
	$query = "SELECT prod_metatitle, prod_metadesc, prod_metakeywords, productId, purMeth, productCode, quantity, name, description, image, noImages, price, popularity, tradePrice, tradeSale, sale_price, stock_level, useStockLevel, digital, digitalDir, cat_name, ".$glob['dbprefix']."CubeCart_inventory.cat_id, cat_father_id, cat_image, per_ship, item_ship, item_int_ship, per_int_ship, noProducts FROM ".$glob['dbprefix']."CubeCart_inventory INNER JOIN ".$glob['dbprefix']."CubeCart_category ON ".$glob['dbprefix']."CubeCart_inventory.cat_id = ".$glob['dbprefix']."CubeCart_category.cat_id where productId = ".$db->mySQLSafe($_GET['productId'])." AND CubeCart_inventory.showProd = 1";
} else {
	$query = "SELECT productId, productCode, quantity, name, description, image, purMeth, noImages, price, popularity, sale_price, tradePrice, tradeSale, stock_level, useStockLevel, digital, digitalDir, cat_name, ".$glob['dbprefix']."CubeCart_inventory.cat_id, cat_father_id, cat_image, per_ship, item_ship, item_int_ship, per_int_ship, noProducts FROM ".$glob['dbprefix']."CubeCart_inventory INNER JOIN ".$glob['dbprefix']."CubeCart_category ON ".$glob['dbprefix']."CubeCart_inventory.cat_id = ".$glob['dbprefix']."CubeCart_category.cat_id where productId = ".$db->mySQLSafe($_GET['productId'])." AND CubeCart_inventory.showProd = 1";
}
/* <rf> end mod */


$prodArray = $db->select($query);


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
	}elseif(isset($_GET['wishlist']) && $_GET['wishlist'] == 'remove'){
		$where = 'customerId = '.$db->mySQLSafe($ccUserData[0]['customer_id']).' AND productID = '.$db->mySQLSafe($_GET['productId']);
		$db->delete($glob['dbprefix'].'CubeCart_wishlist',$where,1);
		header('location: /'. generateProductUrl($_GET['productId']));
		exit;
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
	$features = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_bike_features WHERE productId = ".$db->mySQLsafe($_GET['productId']));
	if($features){
		$featureTable = '<table width="100%" border="0" cellspacing="0" cellpadding="3" class="bikeSpecTable">';
		for ($i=0; $i<count($features); $i++){
			$featureTable .= '<tr><td class="'.cellColor($i).'">'.stripslashes($features[$i]['featureName']).'</td><td class="'.cellColor($i).'">'.stripslashes($features[$i]['featureValue']).'</td></tr>';
		}
		$featureTable .= '</table>';
		$view_prod->assign("TXT_FEATURES",$featureTable);
	}
	if(isset($_GET['add']) && isset($_GET['quan'])){
		$view_prod->assign("CURRENT_URL",str_replace(array("&amp;add=".$_GET['add'],"&amp;quan=".$_GET['quan']),"",currentPage()));
	} else {
		$view_prod->assign("CURRENT_URL",currentPage());
	}
	
	$deliveryInfo = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_docs WHERE isDelivery = 1');
	if($deliveryInfo==true){
		$view_prod->assign("TXT_DELIVERY",$deliveryInfo[0]['doc_content']);
	}
	
	$reviews = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_reviews WHERE product = '.$db->mySQLSafe($prodArray[0]['productId']).' AND approved = 1');
	if($reviews==true){
		$reviewCount = count($reviews);
		$average = $db->select('SELECT ROUND(AVG(stars)) AS rating FROM '.$glob['dbprefix'].'CubeCart_reviews WHERE product = '.$db->mySQLSafe($prodArray[0]['productId']).' AND approved = 1');
		if($average==true){
			$starsTable = '<table><tr>';
			for($r = 0; $r < (int)$average[0]['rating']; $r++){
				$starsTable .= '<td><img src="/skins/FixedSize/styleImages/backgrounds/fullStar.png" width="17" height="16" /></td>';
			}
			if((5 - (int)$average[0]['rating']) > 0){
				for($t = 0; $t < (5 - (int)$average[0]['rating']); $t++){
					$starsTable .= '<td><img src="/skins/FixedSize/styleImages/backgrounds/emptyStar.png" width="17" height="16" /></td>';
				}
			}
			$starsTable .= '</tr></table>';
		}
		$reviewHtml = $starsTable.' <strong>Rating:</strong> '.$average[0]['rating'].' out of 5 stars (based on <a href="/index.php?act=viewProd&productId='.$prodArray[0]['productId'].'&amp;addReview">'.$reviewCount.' reviews</a>)';
	}else{
		$reviewHtml = 'No reviews posted, be the first to <a href="/index.php?act=viewProd&productId='.$prodArray[0]['productId'].'&amp;addReview">review '.$prodArray[0]['name'].'</a>';
	}
	if(isset($reviewHtml)){
		$view_prod->assign("REVIEWHTML",$reviewHtml);
	}
	
	//Check if it is a trade customer
	if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
		
		if(salePrice($prodArray[0]['price'], $prodArray[0]['sale_price'])==FALSE){
			$view_prod->assign("TXT_PRICE","Price: <span class='priceText'>".priceFormat($prodArray[0]['tradePrice']).'</span>');
			$view_prod->assign("TXT_SALE_PRICE", "");
		}else{
			$view_prod->assign("TXT_PRICE","Was: ".priceFormat($prodArray[0]['tradePrice']));
			$salePrice = salePrice($prodArray[0]['price'], $prodArray[0]['tradeSale']);
			$view_prod->assign("TXT_SALE_PRICE", "Now: ".priceFormat($salePrice));
		}
	} else {
		if(salePrice($prodArray[0]['price'], $prodArray[0]['sale_price'])==FALSE){
			$view_prod->assign("TXT_PRICE","Price: <span class='priceText'>".priceFormat($prodArray[0]['price']).'</span>');
			$view_prod->assign("TXT_SALE_PRICE", "");
		}else{
			$view_prod->assign("TXT_PRICE","Was: ".priceFormat($prodArray[0]['price']));
			$salePrice = salePrice($prodArray[0]['price'], $prodArray[0]['sale_price']);
			$view_prod->assign("TXT_SALE_PRICE", "Now: ".priceFormat($salePrice));
		}			
	}
	//Check if it is a trade customer	
	
	
	$view_prod->assign("TXT_PRODCODE", $prodArray[0]['productCode']);

	$view_prod->assign("CURRENT_DIR",getCatDir($prodArray[0]['cat_name'],$prodArray[0]['cat_father_id'], $prodArray[0]['cat_id'],$link=TRUE));

	$view_prod->assign("LANG_QUAN",$lang['front']['viewProd']['quantity']);

	$view_prod->assign("PRODUCT_ID",$prodArray[0]['productId']);
	if($ccUserData[0]['customer_id'] > 0){
		$checkWishes = $db->select('SELECT * FROM '.$glob['dbprefox'].'CubeCart_wishlist WHERE productId = '.$db->mySQLSafe($prodArray[0]['productId']).' AND customerId = '.$db->mySQLSafe($ccUserData[0]['customer_id']));
		if($checkWishes==true){
			$view_prod->assign("WISHLINK",'/?wishlist=remove&amp;productId={PRODUCT_ID}&amp;act=viewProd');
			$view_prod->assign("WISHTEXT",'Remove From Wish List');
		}else{
			$view_prod->assign("WISHLINK",'/?wishlist=add&amp;productId={PRODUCT_ID}&amp;act=viewProd');
			$view_prod->assign("WISHTEXT",'Add to Wish List');
		}
	}else{
		$view_prod->assign("WISHTEXT",'Please login to add this product to your Wish List');
		$view_prod->assign("WISHLINK",'/index.php?act=login&redir='.base64_encode(thisPageURL()));
	}
	
	if(isset($config['prodSortOrder'])) {
		$prodSortOrder = $config['prodSortOrder']; 
	}
	if(isset($_GET['sortOrder'])) {
		$prodSortOrder = treatGet($_GET['sortOrder']); 
	}
	if($_GET['sortOrder'] == "default") {
		$prodSortOrder = $config['prodSortOrder']; 
	}
	if(isset($prodSortOrder)) {
		$prodSortMethod = " ORDER BY ".$glob['dbprefix']."CubeCart_inventory.".$prodSortOrder; 
	}
	$thiscat = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE showProd = 1 AND cat_id = '.$db->mySQLSafe($prodArray[0]['cat_id']).$prodSortMethod);
	if($thiscat==true){
		for($z = 0; $z < count($thiscat); $z++){
			if($thiscat[$z]['productId'] == $prodArray[0]['productId']){
				if(isset($thiscat[($z-1)]['productId'])){
					$prvious = '<a href="index.php?act=viewProd&amp;productId='.$thiscat[($z-1)]['productId'].'">Previous Product</a>';
				}else{
					$prvious = 'Previous Product';
				}
				if(isset($thiscat[($z+1)]['productId'])){
					$next = '<a href="index.php?act=viewProd&amp;productId='.$thiscat[($z+1)]['productId'].'">Next Product</a>';
				}else{
					$next = 'Next Product';
				}
			}
		}
		$view_prod->assign("NEXT",$next);
		$view_prod->assign("PREV",$prvious);
	}

	if(!empty($prodArray[0]['image'])){
		$view_prod->assign("LRG_SRC",$glob['rootRel'] . "images/uploads/".$prodArray[0]['image']);
		$view_prod->assign("IMG_SRC",$glob['rootRel'] . "image_resize.php?size=300&amp;im=images/uploads/".$prodArray[0]['image']);
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
		$view_prod->assign("VALUE_IMG_SRC",$glob['rootRel'] . "image_resize.php?size=300&im=images/uploads/thumbs/thumb_".$results[$i]['img']);
		$view_prod->assign("VALUE_ZOOM_SRC","/images/uploads/thumbs/thumb_".$results[$i]['img']);
		if(file_exists("images/uploads/thumbs/thumb_".$results[$i]['img'])){
			
			$view_prod->assign("VALUE_THUMB_SRC","image_resize.php?size=67&y_size=67&im=images/uploads/thumbs/thumb_".$results[$i]['img']);
			$sizeThumb = getimagesize("images/uploads/thumbs/thumb_".$results[$i]['img']);
			$view_prod->assign("VALUE_THUMB_WIDTH",$sizeThumb[0]);
			
		} else {
			
			$view_prod->assign("VALUE_THUMB_SRC","image_resize.php?size=67&y_size=67&im=images/uploads/thumbs/thumb_".$results[$i]['img']);
			$view_prod->assign("VALUE_THUMB_WIDTH",$config['gdthumbSize']);
			
			
		}
		$view_prod->assign("ALT_THUMB",$lang['front']['popup']['thumb_alt']);
		$view_prod->parse("view_prod.prod_true.thumbs");
	
	} // end loop 
	// original image
		
		$view_prod->assign("VALUE_SRC", $glob['rootRel'] . "image_resize.php?size=67&y_size=67&im=images/uploads/thumbs/thumb_".$mainImage[0]['image']);
		$view_prod->assign("VALUE_IMG_SRC", $glob['rootRel'] . "image_resize.php?size=300&im=images/uploads/thumbs/thumb_".$mainImage[0]['image']);
		$view_prod->assign("VALUE_ZOOM_SRC", $glob['rootRel'] . "images/uploads/thumbs/thumb_".$mainImage[0]['image']);
		if(file_exists("images/uploads/thumbs/thumb_".$mainImage[0]['image'])){
			
			$view_prod->assign("VALUE_THUMB_SRC","image_resize.php?size=67&y_size=67&im=images/uploads/thumbs/thumb_".$mainImage[0]['image']);
			$sizeThumb = getimagesize("images/uploads/thumbs/thumb_".$mainImage[0]['image']);
			$view_prod->assign("VALUE_THUMB_WIDTH",$sizeThumb[0]);
			
		} else {
			
			$view_prod->assign("VALUE_THUMB_SRC","image_resize.php?size=67&y_size=67&im=images/uploads/".$mainImage[0]['image']);
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
		$query = "SELECT relatedId as productId, name, image, description, price, sale_price FROM ".$glob['dbprefix']."CubeCart_mod_related_prods as rel, ".$glob['dbprefix']."CubeCart_inventory as inv WHERE showProd = 1 AND rel.relatedId=inv.productId AND rel.productId=".$db->mySQLSafe($prodArray[0]['productId'])." ORDER BY id";
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
			$query = "SELECT MATCH (name,description) AGAINST (".$search1.") AS score1, productId, name, image, description, price, sale_price FROM ".$glob['dbprefix']."CubeCart_inventory WHERE MATCH (name,description) AGAINST (".$search1.") > ".$relevance_cutoff." AND showProd = 1 AND productId NOT IN (".$not_in.") ORDER BY MATCH (name,description) AGAINST (".$search1.") DESC LIMIT ".($config_rp['max_shown']+1);

		}
		else
		{
			$query = "SELECT MATCH (name,description) AGAINST (".$search1.") AS score1, MATCH (name,description) AGAINST (".$search2.") AS score2, productId, name, image, description, price, sale_price FROM ".$glob['dbprefix']."CubeCart_inventory WHERE MATCH (name,description) AGAINST (".$search1.") + MATCH (name,description) AGAINST (".$search2.") > ".$relevance_cutoff." AND showProd = 1 AND productId NOT IN (".$not_in.") ORDER BY MATCH (name,description) AGAINST (".$search1.") + MATCH (name,description) AGAINST (".$search2.") DESC LIMIT ".($config_rp['max_shown']+1);
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
				$view_prod->assign("REL_PRODUCT_ID",$related[$i]['productId']);
				if(salePrice($related[$i]['price'], $related[$i]['sale_price'])==FALSE){
					$view_prod->assign("REL_PRICE",priceFormat($related[$i]['price'],true));
				} else {
					$view_prod->assign("REL_PRICE",priceFormat($related[$i]['sale_price'],true));
				}
				$view_prod->assign("REL_DESC",chopper($related[$i]['short_description'],20,'/index.php?act=viewProd&amp;productId='.$related[$i]['productId']));
				
				if(file_exists("images/uploads/thumbs/thumb_".$related[$i]['image'])){
					$view_prod->assign("VALUE_RELATED_THUMB","image_resize.php?size=173&amp;y_size=141&amp;im=images/uploads/thumbs/thumb_".$related[$i]['image']);
				} elseif(!empty($related[$i]['image']) && file_exists("images/uploads/thumbs/thumb_".$related[$i]['image'])) {
					$view_prod->assign("VALUE_RELATED_THUMB","image_resize.php?size=173&amp;y_size=141&amp;im=images/uploads/thumbs/thumb_".$related[$i]['image']);
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
	
	if($prodArray[0]['purMeth']==2){
		$view_prod->parse("view_prod.prod_true.buy_btn.add_btn");
		$view_prod->parse("view_prod.prod_true.buy_btn.quote_btn");
	}elseif($prodArray[0]['purMeth']==1){
		$view_prod->parse("view_prod.prod_true.buy_btn.quote_btn");
	}else{
		$view_prod->parse("view_prod.prod_true.buy_btn.add_btn");
	}
	
	if($config['outofstockPurchase']==1){
	
		$view_prod->assign("BTN_ADDBASKET",$lang['front']['viewProd']['add_to_basket']);
		$view_prod->parse("view_prod.prod_true.buy_btn");
		
	
	} elseif($prodArray[0]['useStockLevel']==1 && $prodArray[0]['stock_level']>0){
	
		$view_prod->assign("BTN_ADDBASKET",$lang['front']['viewProd']['add_to_basket']);
		$view_prod->parse("view_prod.prod_true.buy_btn");	
		
	} elseif($prodArray[0]['useStockLevel']==0){

		$view_prod->assign("BTN_ADDBASKET",$lang['front']['viewProd']['add_to_basket']);
		$view_prod->parse("view_prod.prod_true.buy_btn");

	}

	$view_prod->assign("LANG_DIR_LOC",$lang['front']['viewProd']['location']);


	if($config['stockLevel']==1 && $prodArray[0]['useStockLevel']==1 && $prodArray[0]['stock_level']>0){
		
		$view_prod->assign("TXT_INSTOCK",'<span class="instock">'.$lang['front']['viewProd']['instock'].'</span>');
	
	} elseif($prodArray[0]['useStockLevel']==1 && $prodArray[0]['stock_level']>0 || $prodArray[0]['useStockLevel']==0) {
		
		$view_prod->assign("TXT_INSTOCK",'<span class="instock">'.$lang['front']['viewProd']['instock'].'</span>');
	
	} else {
		
		$view_prod->assign("TXT_INSTOCK","");
	
	}


	if($prodArray[0]['stock_level']<1 && $prodArray[0]['useStockLevel']==1 && $prodArray[0]['digital']==0){
	
		$view_prod->assign("TXT_OUTOFSTOCK",'<span class="nostock">'.$lang['front']['viewProd']['out_of_stock'].'</span>');
		
	} else {
	
		$view_prod->assign("TXT_OUTOFSTOCK","");
	
	}

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
	$get_reviews = "SELECT * FROM ".$glob['dbprefix']."CubeCart_reviews WHERE product = ".$db->mySQLSafe($productId)." and approved='1' ORDER BY id DESC LIMIT ".$display_limit;        
	$show_reviews = $db->select($get_reviews);  
	if($show_reviews == TRUE){  
		$view_prod->assign("CUST_REV",$cust_rev);
		for($r=0; $r<count($show_reviews); $r++){
			
			$starsTable = '<table><tr>';
			for($s = 0; $s < $show_reviews[$r]['stars']; $s++){
				$starsTable .= '<td><img src="/skins/FixedSize/styleImages/backgrounds/fullStar.png" width="17" height="16" /></td>';
			}
			if((5 - $show_reviews[$r]['stars']) > 0){
				for($t = 0; $t < (5 - $show_reviews[$r]['stars']); $t++){
					$starsTable .= '<td><img src="/skins/FixedSize/styleImages/backgrounds/emptyStar.png" width="17" height="16" /></td>';
				}
			}
			
			$starsTable .= '</tr></table>';
			$view_prod->assign("USERNAME",$show_reviews[$r]['postName']);
			$view_prod->assign("REWVIEWDATE",date('jS \of F Y',$show_reviews[$r]['timeAdded']));
			$view_prod->assign("REV_STARS",$starsTable);
			$view_prod->assign("REVIEWTEXT",nl2br(strip_tags(stripslashes($show_reviews[$r]['comments']))));
			$view_prod->parse("view_prod.prod_true.reviewItem");
		}
	}
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
