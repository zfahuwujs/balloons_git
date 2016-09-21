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

|	index.inc.php

|   ========================================

|	The Homepage :O)	

+--------------------------------------------------------------------------

*/

// Seans 301 http://site.com redirect to http://www.site.com mod =]
/*if(strpos($_SERVER['HTTP_HOST'],'www')===FALSE){
	header( "HTTP/1.1 301 Moved Permanently" );
	header( "Location: http://www.".$_SERVER['HTTP_HOST'] );
}
*/
phpExtension();


$index=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/index.tpl");

include("includes/boxes/categoriesWithImages.inc.php");
$index->assign("CAT_WITH_IMAGES",$box_content);


include("language/".$lang_folder."/home.inc.php");
if($home['enabled']==0){
	include("language/".$config['defaultLang']."/home.inc.php");
}
//$index->assign("HOME_TITLE",validHTML(stripslashes($home['title'])));
//$index->assign("HOME_CONTENT",stripslashes($home['copy']));

if($lang_folder !== $config['defaultLang']){

	$result = $db->select("SELECT doc_master_id AS doc_id, doc_name, doc_content FROM ".$glob['dbprefix']."CubeCart_docs_lang WHERE doc_master_id = ".$db->mySQLSafe($_GET['docId'])." AND doc_lang=".$db->mySQLSafe($lang_folder));
	/* <rf> search engine friendly mod */
	if($config['seftags']) {
		// get metas for the docs
		$sefresult = $db->select("SELECT doc_metatitle, doc_metadesc, doc_metakeywords FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = ".$db->mySQLSafe($_GET['docId'])); 
		$result['sefSiteTitle'] = $sefresult['sefSiteTitle'];
		$result['sefSiteDesc'] = $sefresult['sefSiteDesc'];
		$result['sefSiteKeywords'] = $sefresult['sefSiteKeywords'];
	} 
	/* <rf> end mod */

}

if($config['seftags']) {
	$result = $db->select("SELECT doc_id, doc_metatitle, doc_metadesc, doc_metakeywords, doc_name, doc_content, sub_heading FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = 7"); 
} else {
	$result = $db->select("SELECT doc_id, doc_name, doc_content, sub_heading FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = 7"); 
}

if(!empty($result[0]['doc_content'])){
	$index->assign("HOME_CONTENT",stripslashes($result[0]['doc_content']));
	if($result[0]['sub_heading']!=''){
		$index->assign("HOME_TITLE","<h1>".$result[0]['sub_heading']."</h1>");
	}
	
	$index->parse("index.home_content");
}



// START HOMPAGE PRODUCT MODULES
//check which poduct modules to use and count how many are in use
$modCount=0;
$latest='';
$featured='';
$popular='';
$sale='';
$bestseller='';
if($config['latestProdsHome']==1){$latest='latest'; $modCount=$modCount+1;}
if($config['featuredProdsHome']==1){$featured='featured'; $modCount=$modCount+1;}
if($config['popularProdsHome']==1){$popular='popular'; $modCount=$modCount+1;}
if($config['saleProdsHome']==1){$sale='sale'; $modCount=$modCount+1;}
if($config['bestsellerProdsHome']==1){$bestseller='bestseller'; $modCount=$modCount+1;}

//repeat output for each module
if($modCount>0){
	for($m=0; $m<$modCount; $m++){
		//get title, db column name and number of products
		if($latest!=''){$numProds=$config['noLatestProds']; $module=$latest; $latest=''; $order='lastModified DESC'; $index->assign("PROD_MOD_TITLE",'Latest Products');}
		elseif($featured!=''){$numProds=$config['noFeaturedProds']; $module=$featured; $featured=''; $order='name ASC'; $index->assign("PROD_MOD_TITLE",'Featured Products');}
		elseif($popular!=''){$numProds=$config['noPopularProds']; $module=$popular; $popular=''; $order='popularity DESC'; $index->assign("PROD_MOD_TITLE",'Popular Products');}
		elseif($sale!=''){$numProds=$config['noSaleProds']; $module=$sale; $sale=''; $order='name ASC'; $index->assign("PROD_MOD_TITLE",'Sale Products');}
		elseif($bestseller!=''){$numProds=$config['noBestsellerProds']; $module=$bestseller; $bestseller=''; $order='salesCount DESC'; $index->assign("PROD_MOD_TITLE",'Best Seller Products');}
		//get trade or standard prices for current module
		if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 0){
			$featuredProducts = $db->select("SELECT name, productId, image, price, sale_price, tradePrice , tradeSale , stock_level, useStockLevel, short_description FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 AND ".$module." = 1 ORDER BY ".$order." LIMIT ".$numProds);
		}else{
			$featuredProducts = $db->select("SELECT name, productId, image, price, sale_price, stock_level, productCode, useStockLevel, short_description FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 AND ".$module." = 1 ORDER BY ".$order." LIMIT ".$numProds);
		}

		if($featuredProducts==TRUE){
			for($i=0;$i<count($featuredProducts);$i++){
				if(($val = prodAltLang($featuredProducts[$i]['productId'])) == TRUE){
						$featuredProducts[$i]['name'] = $val['name'];
				}
		
				if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$featuredProducts[$i]['image'])){
					$index->assign("VAL_IMG_SRC","images/uploads/thumbs/thumb_".$featuredProducts[$i]['image']);
				} else {
					$index->assign("VAL_IMG_SRC",$GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif");
				}
				
				if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 0){
					$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
					$discount = $tradeDiscount[0]['discount']/100;
					$featuredProducts[$i]['tradePrice'] = $featuredProducts[$i]['price'] - ($featuredProducts[$i]['price'] * $discount);
					$featuredProducts[$i]['tradeSale'] = $featuredProducts[$i]['sale_price'] - ($featuredProducts[$i]['sale_price'] * $discount);
					if($ccUserData[0]['tax'] == 1){
						
						if(salePrice($featuredProducts[$i]['tradePrice'], $featuredProducts[$i]['tradeSale'])==FALSE){
							$index->assign("TXT_PRICE",priceFormat($featuredProducts[$i]['tradePrice']));
							$index->assign("TXT_SALE_PRICE", "");
						}else{
							$index->assign("TXT_PRICE","<strike>".priceFormat($featuredProducts[$i]['tradePrice'])."</strike>");
							$salePrice = salePrice($featuredProducts[$i]['tradePrice'], $featuredProducts[$i]['tradeSale']);
							$index->assign("TXT_SALE_PRICE", priceFormat($salePrice));
						}
					}
					if($ccUserData[0]['tax'] == 2){
						
						if(salePrice($featuredProducts[$i]['tradePrice'], $featuredProducts[$i]['tradeSale'])==FALSE){
							$vatTradePrice = getProductPriceWithTax($featuredProducts[$i]['productId'], 'price');
							$vatTradePrice = $vatTradePrice - ($vatTradePrice * $discount);
							$index->assign("TXT_PRICE",priceFormat($vatTradePrice));
							$index->assign("TXT_SALE_PRICE", "");
						}else{
							$vatTradePrice = getProductPriceWithTax($featuredProducts[$i]['productId'], 'price');
							$vatTradePrice = $vatTradePrice - ($vatTradePrice * $discount);
							$index->assign("TXT_PRICE","<strike>".priceFormat($vatTradePrice)."</strike>");
							$vatSalePrice = getProductPriceWithTax($featuredProducts[$i]['productId'], 'sale_price');
							$salePrice = salePrice($vatTradePrice, $vatSalePrice);
							$salePrice = $salePrice - ($salePrice * $discount);
							$index->assign("TXT_SALE_PRICE", priceFormat($salePrice));
						}
					}
				} else {
					if($ccUserData[0]['tax'] == 1){
						if(salePrice($featuredProducts[$i]['price'], $featuredProducts[$i]['sale_price'])==FALSE){
							$index->assign("TXT_PRICE",priceFormat($featuredProducts[$i]['price']));
							$index->assign("TXT_SALE_PRICE", "");
						}else{
							$index->assign("TXT_PRICE","<strike>".priceFormat($featuredProducts[$i]['price'])."</strike>");
							$salePrice = salePrice($featuredProducts[$i]['price'], $featuredProducts[$i]['sale_price']);
							$index->assign("TXT_SALE_PRICE",priceFormat($salePrice));
						}
					}
					if($ccUserData[0]['tax'] == 2){
						if(salePrice($featuredProducts[$i]['price'], $featuredProducts[$i]['sale_price'])==FALSE){
							$vatPrice = getProductPriceWithTax($featuredProducts[$i]['productId'], 'price');
							$index->assign("TXT_PRICE",priceFormat($vatPrice));
							$index->assign("TXT_SALE_PRICE", "");
						}else{
							$vatPrice = getProductPriceWithTax($featuredProducts[$i]['productId'], 'price');
							$index->assign("TXT_PRICE","<strike>".priceFormat($vatPrice)."</strike>");
							$vatSalePrice = getProductPriceWithTax($featuredProducts[$i]['productId'], 'sale_price');
							$salePrice = salePrice($vatPrice, $vatSalePrice);
							$index->assign("TXT_SALE_PRICE",priceFormat($salePrice));
						}
					}
				}
				
				
				if($config['outofstockPurchase']==1){
					$index->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
					$index->assign("PRODUCT_ID",$featuredProducts[$i]['productId']);
					$index->parse("index.prod_mod.module.prods.buy_btn");
				} elseif($featuredProducts[$i]['useStockLevel']==1 && $featuredProducts[$i]['stock_level']>0){
					$index->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
					$index->assign("PRODUCT_ID",$featuredProducts[$i]['productId']);
					$index->parse("index.prod_mod.module.prods.buy_btn");
				} elseif($featuredProducts[$i]['useStockLevel']==0){
					$index->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
					$index->assign("PRODUCT_ID",$featuredProducts[$i]['productId']);
					$index->parse("index.prod_mod.module.prods.buy_btn");
				}
				$index->assign("BTN_MORE",$lang['front']['viewCat']['more']);
				$index->assign("VAL_PRODUCT_ID",$featuredProducts[$i]['productId']);
				$index->assign("TXT_DESC",substr(strip_tags($featuredProducts[$i]['short_description']),0,$config['productPrecis'])."&hellip;");
				$index->assign("VAL_PRODUCT_NAME",validHTML($featuredProducts[$i]['name']));
				
				$index->assign("COLOR",cellColor($i, $tdEven="homeProdsEven", $tdOdd="homeProdsOdd"));
				$index->assign("PART_NO",$featuredProducts[$i]['productCode']);
				
				$index->assign("MOD_NUM",$m);
				$index->parse("index.prod_mod.module.prods");
			}	
		}
		$index->parse("index.prod_mod.module");
	}
	$index->parse("index.prod_mod");
}


// END PRODUCT modules




/* Starting HomePage Categories - MarksCarts http://cc3.biz/ 
Following line modified for use with Sir William's Category & Product 
Sort Order. The first line below is commented (//) and the second line is
active. If you have Sir William's sort order mod installed, you can remove
the comment double-slashes from the first line below, and place them in front
of the second line instead.*/
$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = ".$db->mySQLSafe($_GET['catId'])." ORDER BY disp_order ASC";
//$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = ".$db->mySQLSafe($_GET['catId'])." ORDER BY cat_name ASC";
$resultsForeign = $db->select("SELECT cat_master_id as cat_id, cat_name FROM ".$glob['dbprefix']."CubeCart_cats_lang WHERE cat_lang = '".$lang_folder."'");
$homePageCats = "";
$homePageCats = $db->select($query);
if($config['showHomePageCats']==1 && $homePageCats==TRUE){
	for ($i=0; $i<count($homePageCats); $i++){
 
		if(is_array($resultsForeign)){

		for ($k=0; $k<count($resultsForeign); $k++){

		if($resultsForeign[$k]['cat_id'] == $homePageCats[$i]['cat_id']){
   
		$homePageCats[$i]['cat_name'] = $resultsForeign[$k]['cat_name'];
   
		}
   
		}
 
	}

	if(empty($homePageCats[$i]['cat_image'])){
		$index->assign("IMG_CATEGORY",$GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/nophoto.png");
	} else {
		$index->assign("IMG_CATEGORY",$GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$homePageCats[$i]['cat_image']);
	}
 
	$index->assign("TXT_LINK_CATID",$homePageCats[$i]['cat_id']);

	$index->assign("TXT_CATEGORY", validHTML($homePageCats[$i]['cat_name']));
	
	$index->assign("TXT_CATDESC",substr(strip_tags($homePageCats[$i]['cat_desc']),0,$config['productPrecis'])."&hellip;");
 
	$index->assign("NO_PRODUCTS", $homePageCats[$i]['noProducts']);
 
	$index->parse("index.homepage_cats.homepage_cats_loop");

	} 
	
	$index->parse("index.homepage_cats");
}
/* Ending HomePage Categories - MarksCarts http://cc3.biz/ */


 //===========
//slider images
$rand_img = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_slider WHERE slide_pos > 0 ORDER BY slide_pos ASC');
if($rand_img==TRUE)
{
	for ($im=0; $im<count($rand_img); $im++)
	{
		$index->assign("SLIDER_IMG_PATH", 'images/uploads/thumbs/thumb_'.$rand_img[$im]['image']);
		
		if(!empty($rand_img[$im]['text'])){
			$index->assign("SLIDER_IMG_TEXT",$rand_img[$im]['text']);
			$index->parse("index.slider_images.para");
		}

		
		$index->assign("SLIDER_IMG_TITLE",$rand_img[$im]['title']);
		if($rand_img[$im]['link'] !== ''){
            $index->assign("SLIDER_IMG_LINK","<a href = '".$rand_img[$im]['link']."'>");
            $index->assign("SLIDER_IMG_END","</a>");                                      
        }else{
	        $index->assign("SLIDER_IMG_LINK","");
			$index->assign("SLIDER_IMG_END","");  
        }
		$index->parse("index.slider_images");
	}
}
//=============

//=============

	$brands = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_brands ORDER BY slideOrder');
	if($brands==true){
		for($i = 0; $i < count($brands); $i++){
			if(!empty($brands[$i]['brandImage'])){
				$index->assign("BRAND_IMAGE",'/images/uploads/'.$brands[$i]['brandImage']);
				$index->assign("BRAND_NAME",$brands[$i]['brandName']);
				$index->assign("BRAND_ID",$brands[$i]['id']);
				$index->parse("index.brands.item");
			}
		}
		$index->parse("index.brands");
	}


$index->parse("index");

$page_content = $index->text("index");

?>