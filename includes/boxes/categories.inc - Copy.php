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
|	categories.inc.php
|   ========================================
|	Categories Box	
+--------------------------------------------------------------------------
*/
phpExtension();
// query database
$results = $db->select("SELECT cat_name, cat_id FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = 0 ORDER BY disp_order ASC");

$box_content=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/categories.tpl");
$box_content->assign("LANG_CATEGORY_TITLE",$lang['front']['boxes']['shop_by_cat']);
	$box_content->assign("LANG_HOME",$lang['front']['boxes']['homepage']);
	

	if($results == TRUE){
		for ($i=0; $i<count($results); $i++){
			
			$subs = $db->select("SELECT cat_name, cat_id FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = ".$db->mySQLSafe($results[$i]['cat_id'])." ORDER BY cat_name ASC");
			if($subs == TRUE){
				for($j = 0; $j < count($subs); $j++){
					$box_content->assign("SUB_CAT_ID",$subs[$j]['cat_id']);
					$box_content->assign("SUB_CAT_NAME",$subs[$j]['cat_name']);
					
					$box_content->parse("categories.li.subs.sub_cat");
				}
				$box_content->parse("categories.li.subs");
			}
			
			
			$box_content->assign("CAT_ID",$results[$i]['cat_id']);
			$box_content->assign("CAT_NAME",$results[$i]['cat_name']);
			
			$box_content->parse("categories.li");
		} // end for loop
	}
	
	
	
	
	
	
	
	if($config['saleMode']>0 && $config['saleProdsCat']==1){
		$box_content->parse("categories.sale");		
	}
	if($config['popularProdsCat']==1){
		$box_content->parse("categories.popular");		
	}
	if($config['featuredProdsCat']==1){
		$box_content->parse("categories.featured");		
	}
	if($config['latestProdsCat']==1){
		$box_content->parse("categories.latest");		
	}
	if($config['bestsellerProdsCat']==1){
		$box_content->parse("categories.bestseller");		
	}
	
$box_content->assign('DOMAIN_URI', $glob['storeURL']);
$box_content->parse("categories");
$box_content = $box_content->text("categories");