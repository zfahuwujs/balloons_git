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
|	cart.php
|   ========================================
|	The heart of the shopping cart pages	
+--------------------------------------------------------------------------
*/
	include_once("includes/ini.inc.php");
	
	
	
	// INCLUDE CORE VARIABLES & FUNCTIONS
	include_once("includes/global.inc.php");
	
	// initiate db class
	include_once("classes/db.inc.php");
	$db = new db();
	
	include_once("includes/functions.inc.php");
	$config = fetchDbConfig("config");
	
	include_once("includes/sessionStart.inc.php");
	
	include_once("includes/sslSwitch.inc.php");
	
	// get session data
	if($_GET['act']!=="logout"){
	include_once("includes/session.inc.php");
	}
	
	// get exchange rates etc
	include_once("includes/currencyVars.inc.php");
	
	$lang_folder = "";
	if(empty($ccUserData[0]['lang'])){
		$lang_folder = $config['defaultLang'];
	} else {
		$lang_folder = $ccUserData[0]['lang'];
	}
	include_once("language/".$lang_folder."/lang.inc.php");
	
	// require template class
	include_once("classes/xtpl.php");
	
	if($_GET['act']=="printOrder"){
		$body = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/global/ord.tpl");
	}else{
		$body = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/global/cart.tpl");
	}
	
	if(isset($_GET['searchStr'])){
		$body->assign("SEARCHSTR",treatGet($_GET['searchStr']));
	} else {
		$body->assign("SEARCHSTR","");
	}
	$body->assign("CURRENCY_VER",$currencyVer);
	$body->assign("VAL_ISO",$charsetIso);
	$body->assign("VAL_SKIN",$config['skinDir']);
	$body->assign('DOMAIN_URI', $glob['storeURL']);
	$body->assign('CUR_URL', currentPage());
	
	if($ccUserData[0]['tax'] == 1){
		$body->assign('VAT_SEL_NO', 'selected="selected"');
	}else{
		$body->assign('VAT_SEL_NO', '');
	}
	if($ccUserData[0]['tax'] == 2){
		$body->assign('VAT_SEL_YES', 'selected="selected"');
	}else{
		$body->assign('VAT_SEL_YES', '');
	}
	
	
		// START META DATA
	$body->assign("META_TITLE",htmlspecialchars($config['siteTitle']).c());
	$body->assign("META_DESC",$config['metaDescription']);
	$body->assign("META_KEYWORDS",$config['metaKeyWords']);
		
	// START  MAIN CONTENT
	switch (treatGet($_GET['act'])) {
		
		case "onePage":
			include("includes/content/onePageCart.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
			break;
		
		case "step1":
			include("includes/content/step1.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break;
		
		case "cart":
		case "step2":
		case "step3":
		case "step4":
			include("includes/content/cart.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break; 
		
		case "step5":
			include("includes/content/gateway.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break; 
		
		case "reg":
			include("includes/content/reg.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break;
		
		case "viewOrders":
			include("includes/content/viewOrders.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break;
		
		case "viewOrder":
			include("includes/content/viewOrder.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break;
		
		case "printOrder":
			include("includes/content/printOrder.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break;
		
		case "noShip":
			include("includes/content/noShip.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break;
		
		case "overWeight":
			include("includes/content/overWeight.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break;
		
		case "confirmed":
			include("includes/content/confirmed.inc.php");
			$body->assign("PAGE_CONTENT",$page_content);
		break;
		
		default:
			header("Location: index.php");
			exit;
		break;
  	
	}
	// END MAIN CONTENT
	
	// START CONTENT BOXES
	
	include("includes/boxes/cartNavi.inc.php");
	$body->assign("CART_NAVI",$box_content);
	
	include("includes/boxes/searchForm.inc.php");
	$body->assign("SEARCH_FORM",$box_content);
	
	include("includes/boxes/prouctModuleSide.inc.php");
    $body->assign("PRODUCTS_MODULE_SIDE",$box_content);  
	
	include("includes/boxes/session.inc.php");
	$body->assign("SESSION",$box_content);

	include("includes/boxes/categories.inc.php");
	$body->assign("CATEGORIES",$box_content);
	
	include("includes/boxes/randomProd.inc.php");
	$body->assign("RANDOM_PROD",$box_content);
	
	include("includes/boxes/info.inc.php");
	$body->assign("INFORMATION",$box_content);
	
	include("includes/boxes/language.inc.php");
	$body->assign("LANGUAGE",$box_content);
	
	include("includes/boxes/currency.inc.php");
	$body->assign("CURRENCY",$box_content);
	
	include("includes/boxes/shoppingCart.inc.php");
	$body->assign("SHOPPING_CART",$box_content);
	
	include("includes/boxes/popularProducts.inc.php");
	$body->assign("POPULAR_PRODUCTS",$box_content);
	
	include("includes/boxes/saleItems.inc.php");
	$body->assign("SALE_ITEMS",$box_content);
	
	include("includes/boxes/mailList.inc.php");
	$body->assign("MAIL_LIST",$box_content);
	
	include("includes/boxes/siteDocs.inc.php");
	$body->assign("SITE_DOCS",$box_content);
	
	include("includes/boxes/siteDocsLeft.inc.php");
	$body->assign("SITE_DOCS_LEFT",$box_content);
	
	include("includes/boxes/fontResize.inc.php");
	$body->assign("FONT_RESIZE",$box_content);
	
	include("includes/boxes/partFinder.inc.php");
	$body->assign("PART_FINDER",$box_content);

	
	/*include("includes/boxes/recentProducts.inc.php");
	$body->assign("RECENT_PRODS",$box_content);*/
	
	/*	begin brand box include	*/
	include("includes/boxes/brands.inc.php");
	$body->assign("BRANDS",$box_content);
	
	include("includes/boxes/topNav.inc.php");
	$body->assign("TOP_NAV",$box_content);
	
	
	// END CONTENT BOXES
			if(!empty($config['analytics_uid'])){
		$body->assign("GOOGLE_ANALYTICS_UID",$config['analytics_uid']);
		$body->parse("body.google_analytics");
	}
	
	if (isset($_COOKIE['recentProds'])) {
		$body->parse("body.recent_prods");
	}
	
	if($config['facebook']!=''){$body->assign("FACEBOOK",'<a href = "'.$config['facebook'].'" target="_blank"><img src = "/skins/FixedSize/styleImages/backgrounds/facebook.png" /></a>');}else{$body->assign("FACEBOOK",'');}
	if($config['twitter']!=''){$body->assign("TWITTER",'<a href = "'.$config['twitter'].'" target="_blank"><img src = "/skins/FixedSize/styleImages/backgrounds/twitter.png" /></a>');}else{$body->assign("TWITTER",'');}
	if($config['linkedin']!=''){$body->assign("LINKEDIN",'<a href = "'.$config['linkedin'].'" target="_blank"><img src = "/skins/FixedSize/styleImages/backgrounds/linkedin.png" /></a>');}else{$body->assign("LINKEDIN",'');}
	if($config['youtube']!=''){$body->assign("YOUTUBE",'<a href = "'.$config['youtube'].'" target="_blank"><img src = "/skins/FixedSize/styleImages/backgrounds/youtube.png" /></a>');}else{$body->assign("YOUTUBE",'');}
	$body->assign("COMPANY_NAME",$config['storeName']);
	$body->assign("COMPANY_ADDRESS",nl2br($config['storeAddress']));
	$body->assign("PHONE",$config['masterPhone']);
	
	if(evoHideBol(104)==false){
		$body->parse("body.font_resize_js");
	}
	if($_GET['act']!='onePage' && $_GET['act']!='step5'){
		$body->parse("body.left_column");
	}
	if($_GET['act']!='step5'){
		$body->parse("body.tabs");
	}
	
	// parse and spit out final document
	$body->parse("body");
	$body->out("body");