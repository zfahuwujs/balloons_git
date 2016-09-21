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
|	transfer.php
|   ========================================
|	Core functions for the PaySystems Gateway	
+--------------------------------------------------------------------------
*/
/*
//////////////////////////
// PAYSYSTEMS GATEWAY
//////////////////////////
// L@@K AT ALL THE LOVELY 
// VARIABLES WE HAVE TO
// PLAY WITH!!
//////

//////////////////////////
// IN THE REPEATED REGION
//////
$orderInv['productId']						- product id as an integer
$orderInv['name']							- product name as a varchar
$orderInv['price']							- price of each product (inc options)
$orderInv['quantity']						- quantity of products as an integer
$orderInv['product_options']				- products attributes as test
$orderInv['productCode']					- product code as a varchar
$i											- This is the current incremented integer starting at 0

/////////////////////////
// FIXED VARS
///////
$cart_order_id							- cart order id as a varchar
$ccUserData[0]['email']						- Customers email address
$ccUserData[0]['title']						- Customers title (Mr Miss etc...)
$ccUserData[0]['firstName']					- Customers first name
$ccUserData[0]['lastName']					- Customers last name 
$ccUserData[0]['add_1']						- Invoice Address line 1
$ccUserData[0]['add_2']						- Invoice Address line 1
$ccUserData[0]['town']						- Invoice Town or city
$ccUserData[0]['county']					- Invoice County or state
$ccUserData[0]['postcode']					- Invoice Post/Zip Code
$ccUserData[0]['country']					- Invoice country Id we can look up the country name like this
										countryName($ccUserData[0]['country']);
$ccUserData[0]['phone']						- Contact phone no
$ccUserData[0]['mobile']					- Mobile/Cell phone number

$basket['delInf']['title']				- Delivery title (Mr Miss etc...)
$basket['delInf']['firstName']			- Delivery customers first name
$basket['delInf']['lastName']			- Delivery customers last name 
$basket['delInf']['add_1']				- Delivery Address line 1
$basket['delInf']['add_2']				- Delivery Address line 1
$basket['delInf']['town']				- Delivery Town or city
$basket['delInf']['county']				- Delivery County or state
$basket['delInf']['postcode']			- Delivery Post/Zip Code
$basket['delInf']['country']			- Delivery  country Id we can look up the country name like this	
									countryName($basket['delInf']['country']);


$basket['subTotal'] 					- Order Subtotal (exTax and Shipping)
$basket['grandTotal']					- Basket total which has to be paid (inc Tax and Shipping).
$basket['tax']							- Total tax to pay
$basket['shipCost']						- Shipping price
////////////////////////////////////////////////////////
*/

$module = fetchDbConfig("PaySystems");

function repeatVars(){

		return FALSE;
	
}

function fixedVars(){
	
	global $module, $basket, $ccUserData, $cart_order_id, $config, $GLOBALS;
	
		
	if($config['defaultCurrency']=="USD")  
	{
		$country_code = "UNI";
	} 
	elseif($config['defaultCurrency']=="AUD")
	{
		$country_code = "AUS";
	} 
	elseif($config['defaultCurrency']=="CAD")
	{
		$country_code = "CAN";
	} 
	elseif($config['defaultCurrency']=="GBP")
	{
		$country_code = "GBR";
	}
	
	
	$hiddenVars = "<input type='hidden' name='product1' value='".$cart_order_id."' />
				<input type='hidden' name='total' value='".$basket['grandTotal']."' />
				<input type=hidden name='companyid' value='".$module['acNo']."' />
				<input type=hidden name='formget' value='Y' /> 
				<input type=hidden name='redirect' value='".$GLOBALS['storeURL']."/index.php?act=conf&amp;oid=".base64_encode($cart_order_id)."' />
				<input type=hidden name='redirectfail' value='".$GLOBALS['storeURL']."/index.php?act=conf&amp;f=1&amp;oid=".base64_encode($cart_order_id)."' />
				<input type=hidden name='currency' value='".$country_code."' />
				<input type=hidden name='b_firstname' value='".$ccUserData[0]['firstName']."' />
				<input type=hidden name='b_middlename' value='' />
				<input type=hidden name='b_lastname' value='".$ccUserData[0]['lastName']."' />
				<input type=hidden name='b_address' value='".$ccUserData[0]['add_1']." ".$ccUserData[0]['add_2']."' />
				<input type=hidden name='b_city' value='".$ccUserData[0]['town']."' />
				<input type=hidden name='b_state' value='".$ccUserData[0]['county']."' />
				<input type=hidden name='b_zip' value='".$ccUserData[0]['postcode']."' />
				<input type=hidden name='b_country' value='".countryName($ccUserData[0]['country'])."' />
				<input type=hidden name='b_tel' value='".$ccUserData[0]['phone']."' />
				<input type=hidden name='email' value='".$ccUserData[0]['email']."' />";
				
		return $hiddenVars;
	
}

function success(){
	global $basket;
	
	if( (base64_decode($_GET['oid']) == $basket['cart_order_id']) && !isset($_GET['f']) ) {
	
		return TRUE;
	
	} else {
	
		return FALSE;
	
	}

}

///////////////////////////
// Other Vars
////////
$formAction = "https://secure.paysystems1.com/cgi-v310/payment/onlinesale-tpppro.asp";
$formMethod = "post";
$formTarget = "_self";
$transfer = "auto";
$stateUpdate = TRUE;
?>