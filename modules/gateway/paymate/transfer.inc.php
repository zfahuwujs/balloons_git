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
|	Core functions for the PayMate Gateway	
+--------------------------------------------------------------------------
*/
/*
//////////////////////////
// PAYMATE GATEWAY
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

$module = fetchDbConfig("paymate");

function repeatVars(){

		return FALSE;
	
}

function fixedVars(){
	
	global $module, $basket, $ccUserData, $cart_order_id, $config, $GLOBALS;
	
	$hiddenVars = "<input type='hidden' name='mid' value='".$module['email']."' />
		<input type='hidden' name='amt' value='".$basket['grandTotal']."' />
		<input type='hidden' name='currency' value='".$config['defaultCurrency']."' />
		<input type='hidden' name='ref' value='".$cart_order_id."' /> 
		<input type='hidden' name='pmt_sender_email' value='".$ccUserData[0]['email']."' />
		<input type='hidden' name='pmt_contact_firstname' value='".$ccUserData[0]['firstName']."' />
		<input type='hidden' name='pmt_contact_surname' value='".$ccUserData[0]['lastName']."' />
		<input type='hidden' name='pmt_contact_phone' value='".$ccUserData[0]['phone']."' />
		<input type='hidden' name='regindi_address1' value='".$ccUserData[0]['add_1']." ".$ccUserData[0]['add_2']."' />
		<input type='hidden' name='regindi_address2' value='".$ccUserData[0]['town']."' />
		<input type='hidden' name='regindi_sub' value='".$ccUserData[0]['county']."' />
		<input type='hidden' name='regindi_pcode' value='".$ccUserData[0]['postcode']."' /> 
		<input type='hidden' name='return' value='".$GLOBALS['storeURL']."/confirmed.php?act=conf' />
		<input type='hidden' name='popup' value='0' />";
				
			return $hiddenVars;
	
}

function success(){
	global $basket;
	
	if( ($_POST['ref'] == $basket['cart_order_id']) && $_POST['responseCode']=="PA" ) {
	
		return TRUE;
	
	} else {
	
		return FALSE;
	
	}

}

///////////////////////////
// Other Vars
////////
$formAction = "https://www.paymate.com.au/PayMate/ExpressPayment";
$formMethod = "post";
$formTarget = "_self";
$transfer = "auto";
$stateUpdate = TRUE;
?>