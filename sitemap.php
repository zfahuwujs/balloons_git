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
|	Main pages of the store	
+--------------------------------------------------------------------------
*/
	include_once("includes/ini.inc.php");
	
	// INCLUDE CORE VARIABLES & FUNCTIONS
	include_once("includes/global.inc.php");
	
	// check if installed
	if($glob['installed']==0){
	
		header("location: install/index.php");
		exit;
		
	} elseif((file_exists($glob['rootDir']."/install/index.php") || file_exists($glob['rootDir']."/upgrade.php") && $glob['installed']==1)){
	
		echo "<strong>WARNING</strong> - Your store will not function until the install directory and/or upgrade.php is deleted from the server.";
		exit;
		
	}
	
	// initiate db class
	include_once("classes/db.inc.php");
	$db = new db();
	include_once("includes/functions.inc.php");
	$config = fetchDbConfig("config");
	
	include_once("includes/sessionStart.inc.php");
	
	/* <rf> search engine friendly url mod */
	include_once("includes/sef_urls.inc.php");	
	$sefroot = sef_script_name();
	if($config['sef'] == 0 && preg_match('#'.$glob['rootRel'].$sefroot.'#i', $_SERVER['PHP_SELF'])) {
		// if this script got called by the shop script and we aren't using sef urls then redirect to index.php	
		Header("Location: ".$glob['rootRel']."index.php");
	}
	/* <rf> end of mod */
	
	include_once("includes/sslSwitch.inc.php");
	
	// get session data
	include_once("includes/session.inc.php");
	
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
	
	$body = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/global/index.tpl");

	if(isset($_GET['searchStr'])){
		$body->assign("SEARCHSTR",treatGet($_GET['searchStr']));
	} else {
		$body->assign("SEARCHSTR","");
	}
	
	$body->assign("CURRENCY_VER",$currencyVer);
	$body->assign("VAL_ISO",$charsetIso);
	$body->assign("VAL_SKIN",$config['skinDir']);
		
	// START  MAIN CONTENT
  include("includes/content/siteMap.inc.php");
  $body->assign("PAGE_CONTENT",$page_content);
  // END  MAIN CONTENT
	
	// START META DATA
	if(isset($meta)){
		/* <rf> search engine friendly url mod */
		$body->assign("META_TITLE", sefMetaTitle());
		$body->assign("META_DESC", sefMetaDesc());
		$body->assign("META_KEYWORDS", sefMetaKeywords());
		/* <rf> end mod */
	} else {
		$body->assign("META_TITLE",htmlspecialchars($config['siteTitle']).c());
		$body->assign("META_DESC",$config['metaDescription']);
		$body->assign("META_KEYWORDS",$config['metaKeyWords']);
	}
	
	// START CONTENT BOXES
	include("includes/boxes/searchForm.inc.php");
	$body->assign("SEARCH_FORM",$box_content);
	
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
	
	include("includes/boxes/recentProducts.inc.php");
	$body->assign("RECENT_PRODS",$box_content);
	
	/*	begin brand box include	*/
	include("includes/boxes/brands.inc.php");
	$body->assign("BRANDS",$box_content);
	
	include("includes/boxes/topNav.inc.php");
	$body->assign("TOP_NAV",$box_content);
	
	// END CONTENT BOXES
	
	// parse and spit out final document
	$body->parse("body");
	$body->out("body");
?>