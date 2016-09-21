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
|	tracker.php
|   ========================================
|	Tracking code for clixGalore	
+--------------------------------------------------------------------------
*/
$module = fetchDbConfig("clixGalore");
$affCode = "<!--begin clixGalore code copyright 2004 -->\r\n";
$affCode .= "<img 
src='https://www.clixGalore.com/AdvTransaction.aspx?AdID=".$module['acNo']."&SV=".sprintf("%.2f", $order[0]['prod_total'])."&OID=".$basket['cart_order_id']."' height='0' width='0' border='0' />\r\n";
$affCode .= "<!--end clixGalore code -->\r\n";
?>