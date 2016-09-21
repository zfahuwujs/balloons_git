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
|	session.inc.php
|   ========================================
|	Session Links & Welcome Text	
+--------------------------------------------------------------------------
*/

phpExtension();

/* <rf> search engine friendly mods */
if(user_is_search_engine() == false || $config['sef'] == 0) {

$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/session.tpl");

// build attributes
if($ccUserData[0]['customer_id']>0){

		$box_content->assign("LANG_WELCOME_BACK",$lang['front']['boxes']['welcome_back']);
		$box_content->assign("TXT_USERNAME",$ccUserData[0]['firstName']." ".$ccUserData[0]['lastName']);
		$box_content->assign("LANG_LOGOUT",$lang['front']['boxes']['logout']);
		$box_content->assign("LANG_YOUR_ACCOUNT",$lang['front']['boxes']['your_account']);
		$box_content->parse("session.session_true");

} else {

		$box_content->assign("LANG_WELCOME_GUEST",$lang['front']['boxes']['welcome_guest']);
		$box_content->assign("VAL_SELF",base64_encode(currentPage()));
		$box_content->assign("LANG_LOGIN",$lang['front']['boxes']['login']);
		$box_content->assign("LANG_REGISTER",$lang['front']['boxes']['register']);
		$box_content->parse("session.session_false");

}

$box_content->parse("session");
$box_content = $box_content->text("session");

} else {
	$box_content = null;
}
/* <rf> end mod */
?>
