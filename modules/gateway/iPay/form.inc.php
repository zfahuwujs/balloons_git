<?php
if($_GET['process']==1){

	/*
	 *	CMI Gateway for PHP CubeCart Processing Script
	 *	(c)2004 iPAY
	 */
	 
	require("cmi_gateway.php");
	
	define("RESULT_APPROVED", "APPROVED");
	define("RESULT_DECLINED", "DECLINE");
	
	// Define some default value constants
	define("DEFAULT_ACTION", "SALE");
	define("DEFAULT_FRAUD_SCRUB_LEVEL", "0");
	define("DEFAULT_EFFECTIVE_DATE", "NOW");
	define("DEFAULT_TRANSACTION_DATE", "NOW");
	define("DEFAULT_ANNIVERSARY_DATE", "NOW");
	define("DEFAULT_AUTO_CALCULATE_CREDIT", "NO");
	define("DEFAULT_DOMESTIC_AVS", "NONE");
	define("DEFAULT_INTERNATIONAL_AVS", "NONE");	
	
	$dump_results = TRUE;
	
	$firstName	 = $_POST["firstName"];
	$lastName	 = $_POST["lastName"];
	$amount		 = $_POST["amount"];
	$cardNumber	 = $_POST["cardNumber"];
	$expirationMonth = $_POST["expirationMonth"];
	$expirationYear	 = $_POST["expirationYear"];
	$cvc2		 = $_POST["cvc2"];
	$emailAddress 	 = $_POST["emailAddress"];
	$addr1 		 = $_POST["addr1"];
	$addr2 		 = $_POST["addr2"];
	$city 		 = $_POST["city"];
	$st 		 = $_POST["state"];
	$country 	 = $_POST["country"];
	$postalCode 	 = $_POST["postalCode"];
	$billingOption	 = $CONFIG["CUBECART_BILLING_OPTION"];
	$productName	 = $CONFIG["CUBECART_PRODUCT_NAME"];
	
	$rqt = new Request();
	$rqt->CreateTransaction(DEFAULT_ACTION, DEFAULT_FRAUD_SCRUB_LEVEL, DEFAULT_EFFECTIVE_DATE, DEFAULT_TRANSACTION_DATE, DEFAULT_ANNIVERSARY_DATE, DEFAULT_AUTO_CALCULATE_CREDIT);
	$rqt->SetCardInfo($cardNumber, $expirationMonth, $expirationYear, $cvc2, DEFAULT_DOMESTIC_AVS, DEFAULT_INTERNATIONAL_AVS);
	$rqt->SetConsumer($firstName, $lastName, $addr1, $addr2, $city, $state, $country, $postalCode, $emailAddress, $_SERVER["REMOTE_ADDR"]);
	$rqt->SetBilling($billingOption, $amount, "", "", $productName, "");
	
	$rspxml = $rqt->Submit();
	
	if (strlen($rspxml) > 0)
	{
		$rsp = new Response($rspxml);
		
		if ($dump_results)
		{
			$rqt->_xml->dump_file("/tmp/request.xml", false, true);
			$rsp->_xml->dump_file("/tmp/response.xml", false, true);
		}
		
		$result = $rsp->Transaction->Response->responseCode;
		
		echo "RESULT: " . $result . "<br />\n";
		
		if (strtoupper($result) == RESULT_APPROVED)
		{

		header("Location: confirmed.php");
		exit;
		
		}
		else
		{

		header("Location: confirmed.php&f=1");
		exit;

		}
	}
}


$formTemplate = new XTemplate ("modules/gateway/iPay/form.tpl");

$formTemplate->assign("VAL_FIRST_NAME",$ccUserData[0]['firstName']);
$formTemplate->assign("VAL_LAST_NAME",$ccUserData[0]['lastName']);
$formTemplate->assign("VAL_EMAIL_ADDRESS",$ccUserData[0]['email']);
$formTemplate->assign("VAL_ADD_1",$ccUserData[0]['add_1']);
$formTemplate->assign("VAL_ADD_2",$ccUserData[0]['add_2']);
$formTemplate->assign("VAL_CITY",$ccUserData[0]['town']);
$formTemplate->assign("VAL_COUNTY",$ccUserData[0]['county']);
$formTemplate->assign("VAL_POST_CODE",$ccUserData[0]['postcode']);
$formTemplate->assign("VAL_CART_ORDER_ID",$basket['cart_order_id']);
$formTemplate->assign("VAL_GRAND_TOTAL",$basket['grandTotal']);
$formTemplate->assign("VAL_MERCH_ID",$module['acNo']);


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

	$formTemplate->assign("LANG_CC_INFO_TITLE",$lang['module']['iPay']['cc_info_title']);
	$formTemplate->assign("LANG_FIRST_NAME",$lang['module']['iPay']['first_name']); 
	$formTemplate->assign("LANG_LAST_NAME",$lang['module']['iPay']['last_name']); 
	//$formTemplate->assign("LANG_CARD_TYPE",$lang['module']['iPay']['card_type']);
	$formTemplate->assign("LANG_CARD_NUMBER",$lang['module']['iPay']['card_number']);
	$formTemplate->assign("LANG_EXPIRES",$lang['module']['iPay']['expires']);
	$formTemplate->assign("LANG_MMYYYY",$lang['module']['iPay']['mmyyyy']);
	$formTemplate->assign("LANG_SECURITY_CODE",$lang['module']['iPay']['security_code']);
	$formTemplate->assign("LANG_CUST_INFO_TITLE",$lang['module']['iPay']['customer_info']);
	$formTemplate->assign("LANG_EMAIL",$lang['module']['iPay']['email']);
	$formTemplate->assign("LANG_ADDRESS",$lang['module']['iPay']['address']);
	$formTemplate->assign("LANG_CITY",$lang['module']['iPay']['city']);
	$formTemplate->assign("LANG_STATE",$lang['module']['iPay']['state']);
	$formTemplate->assign("LANG_ZIPCODE",$lang['module']['iPay']['zipcode']);
	$formTemplate->assign("LANG_COUNTRY",$lang['module']['iPay']['country']);
	$formTemplate->assign("LANG_OPTIONAL",$lang['module']['iPay']['optional']);


$formTemplate->parse("form");
$formTemplate = $formTemplate->text("form");
?>