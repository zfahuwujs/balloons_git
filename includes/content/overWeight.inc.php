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
|	overWeight.inc.php
|   ========================================
|	Warning for order too large	
+--------------------------------------------------------------------------
*/

phpExtension();

$over_weight = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/overWeight.tpl");

	$over_weight->assign("LANG_SORRY",$lang['front']['overWeight']['sorry']);
	$over_weight->assign("LANG_DESC",$lang['front']['overWeight']['desc']);
	
	$over_weight->parse("over_weight");
	
$page_content = $over_weight->text("over_weight");
?>