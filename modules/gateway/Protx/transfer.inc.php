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
|	Core functions for the PROTX Gateway	
+--------------------------------------------------------------------------
|	Thanks to Ben XO for Bug Fixing ( xo at dubplates dot org )
+--------------------------------------------------------------------------
*/
/*
//////////////////////////
// PROTX GATEWAY
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

$module = fetchDbConfig("Protx");


/////////////////////////////////////////////////////////
///////////////   START OF PROTX FUNCTIONS  /////////////
/////////////////////////////////////////////////////////

// ** Base 64 Encoding function **
// PHP does it natively but just for consistency and ease of maintenance, let's declare our own function
function base64Encode($plain) {
  // Initialise output variable
  $output = "";
  
  // Do encoding
  $output = base64_encode($plain);
  
  // Return the result
  return $output;
}


// ** Base 64 decoding function **
// PHP does it natively but just for consistency and ease of maintenance, let's declare our own function

function base64Decode($scrambled) {
  // Initialise output variable
  $output = "";
  
  // Do encoding
  $output = base64_decode($scrambled);
  
  // Return the result
  return $output;
}


/*  The SimpleXor encryption algorithm                                                                                **
**  NOTE: This is a placeholder really.  Future releases of VSP Form will use AES or TwoFish.  Proper encryption      **
**       This simple function and the Base64 will deter script kiddies and prevent the "View Source" type tampering    **
**      It won't stop a half decent hacker though, but the most they could do is change the amount field to something **
**      else, so provided the vendor checks the reports and compares amounts, there is no harm done.  It's still      **
**      more secure than the other PSPs who don't both encrypting their forms at all                                  */

function simpleXor($InString, $Key) {
  // Initialise key array
  $KeyList = array();
  // Initialise out variable
  $output = "";
  
  // Convert $Key into array of ASCII values
  for($i = 0; $i < strlen($Key); $i++){
    $KeyList[$i] = ord(substr($Key, $i, 1));
  }

  // Step through string a character at a time
  for($i = 0; $i < strlen($InString); $i++) {
    // Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
    // % is MOD (modulus), ^ is XOR
    $output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
  }

  // Return the result
  return $output;
}

/* The getToken function.                                                                                         **
** NOTE: A function of convenience that extracts the value from the "name=value&name2=value2..." VSP reply string **
**     Works even if one of the values is a URL containing the & or = signs.                                      */

function getToken($thisString) {

  // List the possible tokens
  $Tokens = array(
    "Status",
    "StatusDetail",
    "VendorTxCode",
    "VPSTxId",
    "TxAuthNo",
    "Amount",
    "AVSCV2", 
    "AddressResult", 
    "PostCodeResult", 
    "CV2Result", 
    "GiftAid", 
    "3DSecureStatus", 
    "CAVV" );

  // Initialise arrays
  $output = array();
  $resultArray = array();
  
  // Get the next token in the sequence
  for ($i = count($Tokens)-1; $i >= 0 ; $i--){
    // Find the position in the string
    $start = strpos($thisString, $Tokens[$i]);
    // If it's present
    if ($start !== false){
      // Record position and token name
      $resultArray[$i]->start = $start;
      $resultArray[$i]->token = $Tokens[$i];
    }
  }
  
  // Sort in order of position
  sort($resultArray);

  // Go through the result array, getting the token values
  for ($i = 0; $i<count($resultArray); $i++){
    // Get the start point of the value
    $valueStart = $resultArray[$i]->start + strlen($resultArray[$i]->token) + 1;
    // Get the length of the value
    if ($i==(count($resultArray)-1)) {
      $output[$resultArray[$i]->token] = substr($thisString, $valueStart);
    } else {
      $valueLength = $resultArray[$i+1]->start - $resultArray[$i]->start - strlen($resultArray[$i]->token) - 2;
      $output[$resultArray[$i]->token] = substr($thisString, $valueStart, $valueLength);
    }      

  }

  // Return the ouput array
  return $output;

}

// Randomise based on time
function randomise() {
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}

/////////////////////////////////////////////////////////
///////////////   END OF PROTX FUNCTIONS  ///////////////
/////////////////////////////////////////////////////////

function repeatVars(){

	return FALSE;
}

function fixedVars(){
	
	global $module, $basket, $ccUserData, $cart_order_id, $config, $GLOBALS;
	
					if(!empty($basket['delInf']['add_2'])){
						
						$delAdd = $basket['delInf']['add_1'].", ".$basket['delInf']['add_1'].", ".$basket['delInf']['town'].", ".$basket['delInf']['county'].", ".countryName($basket['delInf']['country']);
					
					} else {
						
						$delAdd = $basket['delInf']['add_1'].", ".$basket['delInf']['town'].", ".$basket['delInf']['county'].", ".countryName($basket['delInf']['country']);
					
					}
					
					
					
					if(!empty($ccUserData[0]['add_2'])){

						$invAdd = $ccUserData[0]['add_1'].", ".$ccUserData[0]['add_2'].", ".$ccUserData[0]['town'].", ".$ccUserData[0]['county'].", ".countryName($ccUserData[0]['country']);
					
					} else {
						
						$invAdd = $ccUserData[0]['add_1'].", ".$ccUserData[0]['town'].", ".$ccUserData[0]['county'].", ".countryName($ccUserData[0]['country']);
					
					}
	
			$VendorTxCode = 'CC3'.(rand(0,32000)*rand(0,32000));

			$cryptVars = 
				"VendorTxCode=".$VendorTxCode
				."&Amount=".$basket['grandTotal']
				."&Currency=".$config['defaultCurrency']
				."&Description=Cart - ".$cart_order_id
				."&CustomerEmail=".$ccUserData[0]['email']
				."&CustomerName=".$ccUserData[0]['title']." ".$ccUserData[0]['firstName']." ".$ccUserData[0]['lastName']
				."&VendorEmail=".$config['masterEmail']	."&DeliveryAddress=".$delAdd
				."&DeliveryPostCode=".$basket['delInf']['postcode']
				."&BillingAddress=".$invAdd
				."&BillingPostCode=".$ccUserData[0]['postcode']
				."&ContactNumber=".$ccUserData[0]['phone']
//				."&ApplyAVSCV2=0&Apply3DSecure=0&ShoppingBasket=ON"  We have taken our the ShoppingBasket Variable - It seemed Protx didn't need it.
				."&ApplyAVSCV2=0&Apply3DSecure=0"
				."&SuccessURL=".$GLOBALS['storeURL']."/modules/gateway/Protx/confirmed.php?oid=".base64_encode($cart_order_id)."&amt=".base64_encode($basket['grandTotal'])
				."&FailureURL=".$GLOBALS['storeURL']."/modules/gateway/Protx/confirmed.php?f=1";
			;
			
			$encrypted = base64Encode(SimpleXor($cryptVars,$module['passphrase']));
	
			$hiddenVars = "<input type='hidden' name='VendorTxCode' value='".$VendorTxCode."' /> 
					<input type='hidden' name='VPSProtocol' value='2.22' />
					<input type='hidden' name='TxType' value='PAYMENT' />
					<input type='hidden' name='Vendor' value='".$module['acNo']."' />
					<input type='hidden' name='Crypt' value='".$encrypted."' />";

			return $hiddenVars;	
}

function successFirst(){
	
	if(isset($_GET['crypt']) && isset($_GET['oid'])){
	
		return TRUE;
	
	} else {
	
		return FALSE;
	
	}

}


function success(){
	global $basket;
	
		if($_GET['f']==1){
		
			return FALSE;
			
		} else {
		
			return TRUE;
			
		}	

}

///////////////////////////
// Other Vars
////////

if($module['gate'] == "sim") {
	
	$formAction = "https://ukvpstest.protx.com/VSPSimulator/VSPFormGateway.asp";

} elseif($module['gate'] == "test") {
	
	$formAction ="https://ukvpstest.protx.com/vps2form/submit.asp";

} elseif($module['gate'] == "live"){
	
	$formAction ="https://ukvps.protx.com/vps2form/submit.asp";

}

$formMethod = "post";
$formTarget = "_self";
$transfer = "auto";
$stateUpdate = TRUE;
?>
