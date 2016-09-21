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
|	transfer.inc.php
|   ========================================
|	Core functions for the PayPal Express Checkout Gateway
+--------------------------------------------------------------------------
*/
if(isset($_POST['gateway']))
{
	
	// Get Express Checkout module vars
	$module = fetchDbConfig("ExpressCheckout");
		
	// set include path for PayPal SDK	
	set_include_path($glob['rootDir'] . "/pear" . PATH_SEPARATOR . get_include_path());
	
	// Required files
	require_once 'PayPal.php';
	
	require_once 'PayPal/Profile/Handler.php';
	require_once 'PayPal/Profile/Handler/Array.php';
	require_once 'PayPal/Profile/API.php';
	
	require_once 'PayPal/Type/BasicAmountType.php';
	
	require_once 'PayPal/Type/SetExpressCheckoutRequestType.php';
	require_once 'PayPal/Type/SetExpressCheckoutRequestDetailsType.php';
	require_once 'PayPal/Type/SetExpressCheckoutResponseType.php';
	
	require_once 'PayPal/Type/GetExpressCheckoutDetailsRequestType.php';
	require_once 'PayPal/Type/GetExpressCheckoutDetailsResponseDetailsType.php';
	require_once 'PayPal/Type/GetExpressCheckoutDetailsResponseType.php';
	

	$currencyCodeType = $config['defaultCurrency'];
	
	
	// Override as only USD is supported at time of writing this module
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
   
	$returnURL = $glob['storeURL']."/modules/gateway/ExpressCheckout/return.php";
	$cancelURL = $glob['storeURL']."/cart.php?act=step5";
   
	$ec_request =& PayPal::getType('SetExpressCheckoutRequestType');
   
	$ec_details =& PayPal::getType('SetExpressCheckoutRequestDetailsType');
	$ec_details->setReturnURL($returnURL);
	$ec_details->setCancelURL($cancelURL);
	$ec_details->setPaymentAction($paymentType);
	
	$amt_type =& PayPal::getType('BasicAmountType');
	$amt_type->setattr('currencyID', $currencyCodeType);
	$amt_type->setval($basket['grandTotal'], 'iso-8859-1');  
	$ec_details->setOrderTotal($amt_type);
	
	$ec_request->setSetExpressCheckoutRequestDetails($ec_details);
	
	
	$caller =& PayPal::getCallerServices($profile);
	
	// Execute SOAP request
	$response = $caller->SetExpressCheckout($ec_request);
	
	$ack = $response->getAck();
   
	switch($ack) 
	{
		case "Success":
		case "SuccessWithWarning":
			$token = $response->getToken();
		break;
		
		// we don't want this to happen :O(
		default:
			header("Location: confirmed.php?f=1");
			exit;  
	}

}


function repeatVars()
{

		return FALSE;
	
}

function fixedVars()
{
	global $token;

	$hiddenVars = "<input type='hidden' name='cmd' value='_express-checkout' />
				<input type='hidden' name='token' value='".$token."' />";

	return $hiddenVars;
}

function success()
{
	global $basket;
	
	if($_GET['f']==1)
	{
	
		return FALSE;
	
	}
	else
	{
	
		return TRUE;
	
	}

}

///////////////////////////
// Other Vars
////////
if($module['gateway']==1)
{
	$formAction = "https://www.paypal.com/cgi-bin/webscr";
}
else
{
	$formAction = "https://www.sandbox.paypal.com/cgi-bin/webscr";
}

$formMethod = "get";
$formTarget = "_self";
$transfer = "auto";
$stateUpdate = TRUE;
?>