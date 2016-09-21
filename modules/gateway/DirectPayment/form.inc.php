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
|	form.inc.php
|   ========================================
|	PayPal Direct Payment Gateway
+--------------------------------------------------------------------------
*/

if($_GET['process']==1)
{
	
	// Get Direct Payment module vars
	$module = fetchDbConfig("DirectPayment");	
	
	// set include path for PayPal SDK	
	set_include_path($glob['rootDir'] . "/pear" . PATH_SEPARATOR . get_include_path());

	require_once 'PayPal.php';
	
	require_once 'PayPal/Profile/Handler.php';
	require_once 'PayPal/Profile/Handler/Array.php';
	require_once 'PayPal/Profile/API.php';
	
	require_once 'PayPal/Type/DoDirectPaymentRequestType.php';
	require_once 'PayPal/Type/DoDirectPaymentRequestDetailsType.php';
	require_once 'PayPal/Type/DoDirectPaymentResponseType.php';
	// Add all of the types
	require_once 'PayPal/Type/BasicAmountType.php';
	require_once 'PayPal/Type/PaymentDetailsType.php';
	require_once 'PayPal/Type/AddressType.php';
	require_once 'PayPal/Type/CreditCardDetailsType.php';
	require_once 'PayPal/Type/PayerInfoType.php';
	require_once 'PayPal/Type/PersonNameType.php';
	
	
	$currencyCodeType = $config['defaultCurrency'];
	
	// Override as only USD is supported at time of writing
	//$currencyCodeType = "USD";
	$certFile = $glob['rootDir']. "/pear/cert_key_pem.txt";
	
	// Set environment Sandox/Live
	$environment = $module['gateway'] ? "Live" : "Sandbox";

	$handler = & ProfileHandler_Array::getInstance(array(
				'username' => $module['username'],
				'certificateFile' => $certFile,
				'subject' => '',
				'environment' => $environment));
	
	$pid = ProfileHandler::generateID();
	
	$profile = & new APIProfile($pid, $handler);
	$profile->setAPIUsername($module['username']);
	$profile->setAPIPassword($module['password']);
	$profile->setSignature(null); 
	$profile->setCertificateFile($certFile);
	$profile->setEnvironment($environment);      
	
	// Build our request from $_POST
	// $dp_request = new TransactionSearchRequestType();
	$dp_request =& PayPal::getType('DoDirectPaymentRequestType');
	if (PayPal::isError($dp_request)) {
    	header("Location: confirmed.php?f=1");
		exit;
	}
	
	$paymentType = "Sale";
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$creditCardType = $_POST["cardType"];
	$creditCardNumber = $_POST["cardNumber"];
	$expDateMonth = $_POST["expirationMonth"];
	// Month must be padded with leading zero
	$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
	$expDateYear = $_POST["expirationYear"];
	$cvv2Number = $_POST["cvc2"];
	$address1 = $_POST["addr1"];
	$address2 = $_POST["addr2"];
	$city = $_POST["city"];
	$state = $_POST['state'];
	$zip = $_POST["postalCode"];
	$amount = $basket['grandTotal'];
	$countryISO = $_POST["country"];
	
	// Populate SOAP request information
	// Payment details
	$OrderTotal =& PayPal::getType('BasicAmountType');
	if (PayPal::isError($OrderTotal)) {
		//var_dump($OrderTotal);
		header("Location: confirmed.php?f=1");
		exit;
	}
	$OrderTotal->setattr('currencyID', $currencyCodeType);
	$OrderTotal->setval($amount, 'iso-8859-1');
	$PaymentDetails =& PayPal::getType('PaymentDetailsType');
	$PaymentDetails->setOrderTotal($OrderTotal);
	$PaymentDetails->setInvoiceID($orderID);
	
	$shipTo =& PayPal::getType('AddressType');
	$shipTo->setName($basket['delInf']['firstName']."
	".$basket['delInf']['lastName']);
	$shipTo->setStreet1($basket['delInf']['add_1']);
	$shipTo->setStreet2($basket['delInf']['add_2']);
	$shipTo->setCityName($basket['delInf']['town']);
	$shipTo->setStateOrProvince($basket['delInf']['county']);
	$shipTo->setCountry(countryIso($basket['delInf']['country']));
	$shipTo->setPostalCode($basket['delInf']['postcode']);
	$PaymentDetails->setShipToAddress($shipTo);
	
	$dp_details =& PayPal::getType('DoDirectPaymentRequestDetailsType');
	$dp_details->setPaymentDetails($PaymentDetails);
	
	// Credit Card info
	$card_details =& PayPal::getType('CreditCardDetailsType');
	$card_details->setCreditCardType($creditCardType);
	$card_details->setCreditCardNumber($creditCardNumber);
	$card_details->setExpMonth($padDateMonth);
	$card_details->setExpYear($expDateYear);
	$card_details->setCVV2($cvv2Number);
	
	$payer =& PayPal::getType('PayerInfoType');
	$person_name =& PayPal::getType('PersonNameType');
	$person_name->setFirstName($firstName);
	$person_name->setLastName($lastName);
	$payer->setPayerName($person_name);
	$payer->setPayer($emailAddress);
	$payer->setPayerCountry($countryISO);
	$payer->setAddress($shipTo);
	
	$card_details->setCardOwner($payer);
	
	$dp_details->setCreditCard($card_details);
	$dp_details->setIPAddress($_SERVER['SERVER_ADDR']);
	$dp_details->setPaymentAction($paymentType);
	
	$dp_request->setDoDirectPaymentRequestDetails($dp_details);
	
	$caller =& PayPal::getCallerServices($profile);
	
	// Execute SOAP request
	$response = $caller->DoDirectPayment($dp_request);
	
	$ack = $response->getAck();
	
	switch($ack) 
	{
	   case "Success":
	   case "SuccessWithWarning":
		  header("Location: confirmed.php");
		  exit;
		break;
	   
	   default:  
		  header("Location: confirmed.php?f=1");
		  exit;  
	}
	
}


$formTemplate = new XTemplate ("modules/gateway/DirectPayment/form.tpl");

$formTemplate->assign("VAL_FIRST_NAME",$ccUserData[0]['firstName']);
$formTemplate->assign("VAL_LAST_NAME",$ccUserData[0]['lastName']);
$formTemplate->assign("VAL_EMAIL_ADDRESS",$ccUserData[0]['email']);
$formTemplate->assign("VAL_ADD_1",$ccUserData[0]['add_1']);
$formTemplate->assign("VAL_ADD_2",$ccUserData[0]['add_2']);
$formTemplate->assign("VAL_CITY",$ccUserData[0]['town']);

// look up county
$isoLookup = $db->select("SELECT abbrev FROM `".$glob['dbprefix']."CubeCart_iso_counties`
WHERE `name` LIKE '%".ucfirst(str_replace(" ","",strtolower($ccUserData[0]['county'])))."%'");

if(strlen($ccUserData[0]['county'])==2){

	$countyIso = strtoupper($ccUserData[0]['county']);

} elseif($isoLookup == TRUE) {
	
	$countyIso = $isoLookup[0]['abbrev'];

} else {

	$countyIso = "XX";

}

$formTemplate->assign("VAL_COUNTY",$countyIso);
$formTemplate->assign("VAL_POST_CODE",$ccUserData[0]['postcode']);
$formTemplate->assign("VAL_CART_ORDER_ID",$basket['cart_order_id']);
$formTemplate->assign("VAL_ORDER_TOTAL",$basket['grandTotal']);
$formTemplate->assign("VAL_ITEM_TOTAL",$basket['subTotal']);
$formTemplate->assign("VAL_TAX_TOTAL",$basket['tax']);
$formTemplate->assign("VAL_SHIPPING_TOTAL",$basket['shipCost']);


$currency = $db->select("SELECT currency FROM ".$glob['dbprefix']."CubeCart_sessions WHERE sessId = ".$db->mySQLSafe($_SESSION['ccUser']));

if($currency == TRUE && $currency[0]['currency'] != ''){
	$formTemplate->assign("VAL_CURRENCY_ID", $currency[0]['currency']);
} else {
	$formTemplate->assign("VAL_CURRENCY_ID", $config['defaultCurrency']);
}

$acceptedCards = array("Visa" => "Visa", "MasterCard" => "Master Card", "Amex" => "American Express", "Discover" => "Discover");

foreach($acceptedCards as $cardType => $cardName) {
	$formTemplate->assign("VAL_CARD_TYPE",$cardType);
	
	if($cardType == "Visa")
		$formTemplate->assign("CARD_SELECTED","selected='selected'");
	else
		$formTemplate->assign("CARD_SELECTED","");
	$formTemplate->assign("VAL_CARD_NAME",$cardName);
	$formTemplate->parse("form.repeat_cards");
}

$countries = $db->select("SELECT id, iso, printable_name FROM ".$glob['dbprefix']."CubeCart_iso_countries ORDER BY printable_name"); 
	
	for($i=0; $i<count($countries); $i++){
				
			
		if($countries[$i]['id'] == $ccUserData[0]['country']){
			$formTemplate->assign("COUNTRY_SELECTED","selected='selected'");
		} else {
			$formTemplate->assign("COUNTRY_SELECTED","");
		}
	
		$formTemplate->assign("VAL_COUNTRY_ISO",$countries[$i]['iso']);

		$countryName = "";
		$countryName = $countries[$i]['printable_name'];

		if(strlen($countryName)>20){

			$countryName = substr($countryName,0,20)."&hellip;";

		}

		$formTemplate->assign("VAL_COUNTRY_NAME",$countryName);
		$formTemplate->parse("form.repeat_countries");
	}

$formTemplate->assign("LANG_CC_INFO_TITLE",$lang['module']['directPayment']['cc_info_title']);
$formTemplate->assign("LANG_FIRST_NAME",$lang['module']['directPayment']['first_name']); 
$formTemplate->assign("LANG_LAST_NAME",$lang['module']['directPayment']['last_name']); 
$formTemplate->assign("LANG_CARD_TYPE",$lang['module']['directPayment']['card_type']);
$formTemplate->assign("LANG_CARD_NUMBER",$lang['module']['directPayment']['card_number']);
$formTemplate->assign("LANG_EXPIRES",$lang['module']['directPayment']['expires']);
$formTemplate->assign("LANG_MMYYYY",$lang['module']['directPayment']['mmyyyy']);
$formTemplate->assign("LANG_SECURITY_CODE",$lang['module']['directPayment']['security_code']);
$formTemplate->assign("LANG_CUST_INFO_TITLE",$lang['module']['directPayment']['customer_info']);
$formTemplate->assign("LANG_EMAIL",$lang['module']['directPayment']['email']);
$formTemplate->assign("LANG_ADDRESS",$lang['module']['directPayment']['address']);
$formTemplate->assign("LANG_CITY",$lang['module']['directPayment']['city']);
$formTemplate->assign("LANG_STATE",$lang['module']['directPayment']['state']);
$formTemplate->assign("LANG_ZIPCODE",$lang['module']['directPayment']['zipcode']);
$formTemplate->assign("LANG_COUNTRY",$lang['module']['directPayment']['country']);
$formTemplate->assign("LANG_OPTIONAL",$lang['module']['directPayment']['optional']);

$formTemplate->parse("form");
$formTemplate = $formTemplate->text("form");
?>
