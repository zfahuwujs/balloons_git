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

|	info.inc.php

|   ========================================

|	Info & Stats Box	

+--------------------------------------------------------------------------

*/

phpExtension();



// query database

$noProducts = $db->select("SELECT count(productId) as no FROM ".$glob['dbprefix']."CubeCart_inventory");

// query database

$noCategories = $db->select("SELECT count(cat_id) as no FROM ".$glob['dbprefix']."CubeCart_category"); 

 



$box_content=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/info.tpl");



$box_content->assign("LANG_INFO_TITLE",$lang['front']['boxes']['information']);

$box_content->assign("LANG_INFO_PRODUCTS",$lang['front']['boxes']['products']);

$box_content->assign("DATA_NO_PRODUCTS",$noProducts[0]['no']);

$box_content->assign("LANG_INFO_CATEGORIES",$lang['front']['boxes']['categories']);

$box_content->assign("DATA_NO_CATEGORIES",$noCategories[0]['no']);

$box_content->assign("LANG_INFO_PRICES",$lang['front']['boxes']['prices']);

$box_content->assign("DATA_CURRENCY",$currencyVars[0]['name']);



$box_content->parse("info");



$box_content = $box_content->text("info");

?>