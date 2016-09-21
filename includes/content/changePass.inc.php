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
|	changePass.inc.php
|   ========================================
|	Change the Customers Password	
+--------------------------------------------------------------------------
*/

phpExtension();

// send email if form is submit
if(isset($_POST['submit']) && $ccUserData[0]['customer_id']>0){
	
	$checkOld = $db->numrows("SELECT customer_id FROM ".$glob['dbprefix']."CubeCart_customer WHERE customer_id=".$db->mySQLSafe($ccUserData[0]['customer_id'])." AND password = ".$db->mySQLSafe(md5($_POST['oldPass'])));
	
	if($checkOld==FALSE){
	
		$errorMsg = $lang['front']['changePass']['password_incorrect'];

	} elseif($_POST['newPass']!==$_POST['newPassConf']) {
	
		$errorMsg = $lang['front']['changePass']['conf_not_match'];
	
	} else {
		
		// update
		$data['password'] = $db->mySQLSafe(md5($_POST['newPass']));
		$where = "customer_id=".$db->mySQLSafe($ccUserData[0]['customer_id']);
		$updatePassword = $db->update($glob['dbprefix']."CubeCart_customer",$data, $where);
		 
	} 

}

$change_pass = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/changePass.tpl");

	$change_pass->assign("LANG_CHANGE_PASS_TITLE",$lang['front']['changePass']['change_pass']);
	
	if(!isset($_POST['submit'])) {
		
		$change_pass->assign("LANG_PASS_DESC",$lang['front']['changePass']['change_pass_below']);
		$change_pass->parse("change_pass.session_true.no_error");
		
	} elseif(isset($errorMsg)){
		
		$change_pass->assign("VAL_ERROR",$errorMsg);
		$change_pass->parse("change_pass.session_true.error");
		
	} else {
	
		$change_pass->assign("LANG_PASS_DESC",$lang['front']['changePass']['password_updated']);
		$change_pass->parse("change_pass.session_true.no_error");
		$change_pass->parse("change_pass.session_true");
		
	}
	
	if($ccUserData[0]['customer_id']>0 && $updatePassword == FALSE) { 
	
		$change_pass->assign("TXT_OLD_PASS",$lang['front']['changePass']['old_pass']);
		
		$change_pass->assign("TXT_NEW_PASS",$lang['front']['changePass']['new_pass']);
		
		$change_pass->assign("TXT_NEW_PASS_CONF",$lang['front']['changePass']['confirm_pass']);
	
		$change_pass->assign("TXT_SUBMIT",$lang['front']['changePass']['submit']);

		$change_pass->parse("change_pass.session_true.form");
		$change_pass->parse("change_pass.session_true");
		
	} else { 
		
		$change_pass->parse("change_pass.session_false");
	
	}
	
	$change_pass->parse("change_pass");
$page_content = $change_pass->text("change_pass");
?>