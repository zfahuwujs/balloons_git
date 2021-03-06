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
|	newsletter.inc.php
|   ========================================
|	Subscribe to the Newsletter	
+--------------------------------------------------------------------------
*/

phpExtension();

// send email if form is submit
if(isset($_POST['submit']) && $ccUserData[0]['customer_id']>0){
		
		// update database
			$data['optIn1st'] = $db->mySQLSafe($_POST['optIn1st']); 
			$data['htmlEmail'] = $db->mySQLSafe($_POST['htmlEmail']); 
	
			$where = "customer_id = ".$ccUserData[0]['customer_id'];
			$update = $db->update($glob['dbprefix']."CubeCart_customer",$data,$where);
	
			// rebuild customer array
			$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_sessions INNER JOIN ".$glob['dbprefix']."CubeCart_customer ON ".$glob['dbprefix']."CubeCart_sessions.customer_id = ".$glob['dbprefix']."CubeCart_customer.customer_id WHERE sessId = '".$_SESSION['ccUser']."'";
			$ccUserData = $db->select($query);
}

$newsletter = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/newsletter.tpl");

	$newsletter->assign("LANG_NEWSLETTER_TITLE",$lang['front']['newsletter']['newsletter_prefs']);
	
	if($update == TRUE) {
		$newsletter->assign("LANG_NEWSLETTER_DESC",$lang['front']['newsletter']['edit_prefs_below']);
	} else {
		$newsletter->assign("LANG_NEWSLETTER_DESC",$lang['front']['newsletter']['prefs_updates']);
	}
	
	if($ccUserData[0]['customer_id']>0) { 
	
		$newsletter->assign("TXT_SUBSCRIBED",$lang['front']['newsletter']['subscribe']);
		$newsletter->assign("LANG_YES",$lang['front']['yes']);
		$newsletter->assign("LANG_NO",$lang['front']['no']);
		
		if($ccUserData[0]['optIn1st']==1){
			$newsletter->assign("STATE_SUBSCRIBED_YES","checked='checked'");
			$newsletter->assign("STATE_SUBSCRIBED_NO","");
		} else {
			$newsletter->assign("STATE_SUBSCRIBED_YES","");
			$newsletter->assign("STATE_SUBSCRIBED_NO","checked='checked'");
		}
		
		$newsletter->assign("TXT_EMAIL_FORMAT",$lang['front']['newsletter']['email_format']);
		$newsletter->assign("LANG_TEXT",$lang['front']['newsletter']['plain_text']);
		$newsletter->assign("LANG_HTML",$lang['front']['newsletter']['html']);
		$newsletter->assign("LANG_HTML_ABBR",$lang['front']['newsletter']['html_abbr']);
		
		if($ccUserData[0]['htmlEmail']==1){
			$newsletter->assign("STATE_HTML_TEXT","");
			$newsletter->assign("STATE_HTML_HTML","checked='checked'");
		} else {
			$newsletter->assign("STATE_HTML_TEXT","checked='checked'");
			$newsletter->assign("STATE_HTML_HTML","");	
		}
		
		$newsletter->assign("TXT_SUBMIT",$lang['front']['newsletter']['update']);

		$newsletter->parse("newsletter.session_true");
	
	} else { 
		$newsletter->assign("LANG_LOGIN_REQUIRED",$lang['front']['newsletter']['login_required']);
		$newsletter->parse("newsletter.session_false");
	
	}
	
	$newsletter->parse("newsletter");
$page_content = $newsletter->text("newsletter");
?>