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

|	cartNavi.inc.php

|   ========================================

|	Cart Pages Navigation Links Box	

+--------------------------------------------------------------------------

*/



phpExtension();



$box_content=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/cartNavi.tpl");



$box_content->assign("LANG_LINKS",$lang['front']['boxes']['lang_links']);



if(isset($links)){

	unset($links);

}



switch ($_GET['act']) {

	case "step1":

	$links[] = array (

		'link' => "cart.php?act=reg&amp;redir=%2Fcart.php%3Fact%3Dstep1",

		'text' => $lang['front']['boxes']['reg_and_checkout']);

	$links[] = array (

		'link' => "index.php",

		'text' => $lang['front']['boxes']['cont_shopping']);

	break;

	

	case "step4":

	if($config['shipAddressLock']==0){

	$links[] = array (

		'link' => "cart.php?act=step3",

		'text' => $lang['front']['boxes']['edit_del_add']);

	}

	case "step3":

	$links[] = array (

		'link' => "index.php?act=profile&amp;f=".treatGet($_GET['act']),

		'text' => $lang['front']['boxes']['edit_inv_add']);

	

	case "step2":

	$links[] = array (

		'link' => "cart.php?act=".treatGet($_GET['act'])."&amp;mode=emptyCart",

		'text' => $lang['front']['boxes']['empty_cart']);

	break;



	case "cart":

	$links[] = array (

		'link' => "cart.php?act=".treatGet($_GET['act'])."&amp;mode=emptyCart",

		'text' => $lang['front']['boxes']['empty_cart']);

	break;



}

if(!empty($_SERVER['HTTP_REFERER'])){

	$links[] = array (

		'link' => str_replace("&","&amp;",$_SERVER['HTTP_REFERER']),

		'text' => $lang['front']['boxes']['prev_page']);

}

$links[] = array (

		'link' => "index.php",

		'text' => $lang['front']['boxes']['homepage']);



for($i=0;$i<count($links);$i++){

	$box_content->assign("VAL_LINK",$links[$i]['link']);

	$box_content->assign("TXT_LINK",$links[$i]['text']);

	$box_content->parse("links.repeat_region");

}



$box_content->parse("links");



$box_content = $box_content->text("links");

?> 