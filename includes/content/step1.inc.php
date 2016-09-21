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

|	step1.inc.php

|   ========================================

|	Step 1 Of the Checkout Pages	

+--------------------------------------------------------------------------

*/

phpExtension();



$enableSSl = 1;

require_once("classes/cart.php");

$cart = new cart();

$basket = $cart->cartContents($ccUserData[0]['basket']);

if($_GET['act']=="step1") {

    $basket = $cart->setVar(1,"currentStep");

    $basket = $cart->setVar(2,"stepLimit");

}

if($ccUserData[0]['customer_id']>0){

	header("Location: cart.php?act=step2");

}


$login_register = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/step1.tpl");



if($basket == FALSE) {



	$login_register->assign("LANG_CART_EMPTY",$lang['front']['step1']['lang_empty_cart']);

	$login_register->parse("session_page.cart_false");

	

	} else {

	

	$login_register->assign("LANG_LOGIN_TITLE",$lang['front']['step1']['allready_customer']);

	$login_register->assign("LANG_LOGIN_BELOW",$lang['front']['step1']['login_below']);

	$login_register->assign("VAL_SELF",base64_encode((currentPage())));
	
	if($config['usernameType']==1){
		$login_register->assign("LANG_USERNAME","Username:");
	}else{
		$login_register->assign("LANG_USERNAME","Email:");
	}

	

	if(isset($_POST['username'])){

		$login_register->assign("VAL_USERNAME",$_POST['username']);

	}

	

	$login_register->assign("LANG_PASSWORD",$lang['front']['step1']['password']);

	$login_register->assign("LANG_REMEMBER",$lang['front']['step1']['remember_me']);

	$login_register->assign("TXT_LOGIN",$lang['front']['step1']['login']);

	$login_register->assign("LANG_FORGOT_PASS",$lang['front']['step1']['forgot_pass_q']);

	

	

	$login_register->assign("LANG_EXPRESS_REGISTER",$lang['front']['step1']['need_register']);

	$login_register->assign("LANG_CONT_REGISTER",$lang['front']['step1']['express_register']);

	$login_register->assign("LANG_REGISTER_BUTN",$lang['front']['step1']['reg_and_cont']);

	

	$login_register->assign("LANG_CONT_SHOPPING",$lang['front']['step1']['cont_shopping_q']);

	$login_register->assign("LANG_CONT_SHOPPING_BTN",$lang['front']['step1']['cont_shopping']);

	$login_register->assign("LANG_CONT_SHOPPING_DESC",$lang['front']['step1']['cont_browsing']);

	if(evoHideBol(97)==false){
		$login_register->parse("session_page.cart_true.no_reg");
	}

	$login_register->parse("session_page.cart_true");



} 



$login_register->parse("session_page");

$page_content = $login_register->text("session_page");

?>