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
|	logout.inc.php
|   ========================================
|	Destroy users session	
+--------------------------------------------------------------------------
*/

phpExtension();

// delete cookie
$logout = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/logout.tpl");

$logout->assign("LANG_LOGOUT_TITLE",$lang['front']['logout']['logout']);

	setcookie("ccUser");
	setcookie("ccRemember");
	$logoutVal['customer_id'] = 0;
	$update = $db->update($glob['dbprefix']."CubeCart_sessions", $logoutVal, "sessId=".$db->MySQLsafe($_SESSION['ccUser']) );
	//unset($_SESSION['ccUser']);

if($update == TRUE && !isset($_GET['s'])) {

	header("Location: index.php?act=logout&s=1");
	exit;

} elseif(isset($_GET['s']) && $_GET['s']==1) {

	$logout->assign("LANG_LOGOUT_STATUS",$lang['front']['logout']['session_destroyed']);

} else {

	$logout->assign("LANG_LOGOUT_STATUS",$lang['front']['logout']['no_session']);

}

$logout->parse("logout");
$page_content = $logout->text("logout");
?>