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
|	PayPal Express Checkout Gateway
+--------------------------------------------------------------------------
*/
include("../../../includes/ini.inc.php");
include("../../../includes/global.inc.php");
require_once("../../../classes/db.inc.php");
$db = new db();
include_once("../../../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../../../includes/sessionStart.inc.php");
include_once("../../../includes/sslSwitch.inc.php");
include_once("../../../includes/session.inc.php");
include_once("../../../language/".$config['defaultLang']."/lang.inc.php");
include("../../../includes/currencyVars.inc.php");
require_once("../../../classes/cart.php");

$cart = new cart();
$basket = $cart->cartContents($ccUserData[0]['basket']);

if(isset($_REQUEST['token']))
{
	
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
	
	require_once 'PayPal/Type/DoExpressCheckoutPaymentRequestType.php';
	require_once 'PayPal/Type/DoExpressCheckoutPaymentRequestDetailsType.php';
	require_once 'PayPal/Type/DoExpressCheckoutPaymentResponseType.php';
	
	// comment our when other currencies available

	$currencyCodeType = $config['defaultCurrency'];
	
	
	// comment our when other currencies available
	//$currencyCodeType = "USD"; // Only USD Supported 13-Oct-06
	
	$certFile = $glob['rootDir']. "/pear/cert_key_pem.txt";
	
	// Set environment Sandox/Live
	$environment = $module['gateway'] ? "Live" : "Sandbox";
	
	define('PAYPAL_URL', 'https://www.' . $environment . '.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=');

	
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
	
	$caller =& PayPal::getCallerServices($profile);
	
	// We have a TOKEN from paypal
	// GetExpressCheckoutDetails handling here	

	$ecd =& PayPal::getType('GetExpressCheckoutDetailsRequestType');
	$ecd->setToken($_GET['token']);
	$response = $caller->GetExpressCheckoutDetails($ecd);

	if(PayPal::isError($response)  || ($response->getAck() != 'Success' && $response->getAck() != 'SuccessWithWarning')) 
	{
		//if(PayPal::isError($response)) {
		//	print $response->getMessage();
		//}
		header("Location: ../../../confirmed.php?f=1");
		exit;
	} 
	else 
	{
		$details = $response->getGetExpressCheckoutDetailsResponseDetails();
		$payerInfo = $details->getPayerInfo();
		$pdt =& PayPal::getType('PaymentDetailsType');

		$orderTotal =& PayPal::getType('BasicAmountType');
		$orderTotal->setval(number_format($basket['grandTotal'], 2));
		$orderTotal->setattr('currencyID', $currencyCodeType);
		$pdt->setOrderTotal($orderTotal);
		
		if(($basket['subTotal'] + $basket['tax'] + $basket['shipCost']) == $basket['grandTotal']) 
		{
			$itemTotal =& PayPal::getType('BasicAmountType');
			$itemTotal->setval(number_format($basket['subTotal'], 2));
			$itemTotal->setattr('currencyID', $currencyCodeType); // USD
			$pdt->setItemTotal($itemTotal);

			$taxTotal =& PayPal::getType('BasicAmountType');
			$taxTotal->setval(number_format($basket['tax'], 2));
			$taxTotal->setattr('currencyID', $currencyCodeType); // USD
			$pdt->setTaxTotal($taxTotal);

			$shippingTotal =& PayPal::getType('BasicAmountType');
			$shippingTotal->setval(number_format($basket['shipCost'], 2));
			$shippingTotal->setattr('currencyID', $currencyCodeType); // USD
			$pdt->setShippingTotal($shippingTotal);
		}
		
		$pdt->setInvoiceID($basket['cart_order_id']);
		
		$details =& PayPal::getType('DoExpressCheckoutPaymentRequestDetailsType');
		$details->setPaymentAction('Sale');
		$details->setToken($_GET['token']);
		$details->setPayerID($payerInfo->getPayerID());
		$details->setPaymentDetails($pdt);

		$ecprt =& PayPal::getType('DoExpressCheckoutPaymentRequestType');
		$ecprt->setDoExpressCheckoutPaymentRequestDetails($details);

		$response = $caller->DoExpressCheckoutPayment($ecprt);

		if(PayPal::isError($response)  || ($response->getAck() != 'Success' && $response->getAck() != 'SuccessWithWarning')) 
		{
			//if(PayPal::isError($response)) {
			//	print $response->getMessage();
			//}
			header("Location: ../../../confirmed.php?f=1");
			exit;
		} 
		else 
		{
			$details = $response->getDoExpressCheckoutPaymentResponseDetails();
			$paymentInfo = $details->getPaymentInfo();
			$paymentStatus = $paymentInfo->getPaymentStatus();
	
			switch ($paymentStatus) 
			{
				case 'Completed':
				case 'Pending':
					header("Location: ../../../confirmed.php");
					exit;
				default:
					header("Location: ../../../confirmed.php?f=1");
					exit;
			}
		
		}
		
	}

}
else
{
	header("location: ../../../index.php");
	exit;
}
?>