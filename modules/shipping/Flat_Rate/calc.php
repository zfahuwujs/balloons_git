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
|	calc.php
|   ========================================
|	Calculates shipping based on a flat rate	
+--------------------------------------------------------------------------
*/
// flat rate
$module = fetchDbConfig("Flat_Rate");
if($module['status']==1){

$sum = $module['cost'];
$taxVal = taxRate($module['tax']);

if($taxVal>0){

	$val = ($taxVal / 100) * $sum;
	$sum = $sum + $val;
}

$shippingPrice .= "<option value='".$shipKey."'";

if($shipKey ==$basket['shipKey']){
	$shippingPrice .= " selected='selected'";
	$basket = $cart->setVar($lang['misc']['flatRate'],"shipMethod");
	$basket = $cart->setVar(sprintf("%.2f",$sum),"shipCost");
}

$shippingPrice .= ">".$lang['misc']['flatRate']." (".priceFormat($sum).")</option>\r\n";
$shippingAvailable = TRUE;
$shipKey++;
unset($module, $taxVal);
}
?>