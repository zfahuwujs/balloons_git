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
|	unsubscribe.inc.php
|   ========================================
|	Unsubscribe page from Bulk Email	
+--------------------------------------------------------------------------
*/
phpExtension();

$unsubscribe=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/unsubscribe.tpl");

// get all required data
if(isset($_POST['email'])){

	$record["optIn1st"] = 0;		

	$where = "email = ".$db->mySQLSafe($_POST['email']);
	
	$update =$db->update($glob['dbprefix']."CubeCart_customer", $record, $where);

}

$unsubscribe->assign("UNSUBSCRIBE_TITLE",$lang['front']['unsubscribe']['unsubscribe']);

$unsubscribe->assign("TXT_ENTER_EMAIL",$lang['front']['unsubscribe']['email']);

$unsubscribe->assign("TXT_SUBMIT",$lang['front']['unsubscribe']['go']);

if(isset($_POST['email']) && validateEmail($_POST['email'])==FALSE){

	$unsubscribe->assign("VAL_ERROR",$lang['front']['unsubscribe']['enter_valid_email']);
	$unsubscribe->parse("unsubscribe.error");
	$unsubscribe->parse("unsubscribe.form");
	
} elseif($update == TRUE && isset($_POST['email'])){
	
	$unsubscribe->assign("LANG_UNSUBSCRIBE_DESC",sprintf($lang['front']['unsubscribe']['email_removed'],$_POST['email']));
	$unsubscribe->parse("unsubscribe.no_error");
	
} elseif($update == FALSE && isset($_POST['email'])) {
	
	$unsubscribe->assign("LANG_UNSUBSCRIBE_DESC",sprintf($lang['front']['unsubscribe']['email_not_found'],$_POST['email']));
	$unsubscribe->parse("unsubscribe.error");
	$unsubscribe->parse("unsubscribe.form");

} else {
	$unsubscribe->assign("LANG_UNSUBSCRIBE_DESC",$lang['front']['unsubscribe']['enter_email_below']);
	$unsubscribe->parse("unsubscribe.no_error");
	$unsubscribe->parse("unsubscribe.form");
}

$unsubscribe->parse("unsubscribe");
$page_content = $unsubscribe->text("unsubscribe");
?>