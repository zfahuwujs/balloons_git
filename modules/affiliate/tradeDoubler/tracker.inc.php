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
|	Tracking code for tradeDoubler	
+--------------------------------------------------------------------------
*/
$module = fetchDbConfig("tradeDoubler");

if($affVar['testMode'] == 1){
$testVar = "&testonly=1";
} else {
$testVar = "";
}

$affCode = "<!-- begin tradeDoubler Affiliate Tracker -->\r\n";
$affCode .= "<img src='http://www.awin1.com/sale.php?sale=".sprintf("%.2f",$order[0]['prod_total'])."&extra=".$basket['cart_order_id']."&type=s&mid=".$module['acNo'].$testVar."' />\r\n";
$affCode .= "<!-- end tradeDoubler Affiliate Tracker -->\r\n";
?>