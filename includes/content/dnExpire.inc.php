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
|	dnExpire.inc.php
|   ========================================
|	Warning Download has Expired	
+--------------------------------------------------------------------------
*/

phpExtension();

$dn_expire = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/dnExpire.tpl");

	$dn_expire->assign("LANG_SORRY",$lang['front']['dnExpire']['sorry']);
	$dn_expire->assign("LANG_DESC",$lang['front']['dnExpire']['expired']);
	
	$dn_expire->parse("dn_expire");
	
$page_content = $dn_expire->text("dn_expire");
?>