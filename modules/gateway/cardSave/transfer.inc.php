<?php

$module = fetchDbConfig("cardSave");

function createHash($time, $countryCode){
	global $module, $basket, $ccUserData, $cart_order_id, $config, $glob, $GLOBALS;
	
	$cryptVars="PreSharedKey=" . $module['acKey'];
	$cryptVars=$cryptVars . '&MerchantID=' . $module['acName'];
	$cryptVars=$cryptVars . '&Password=' . $module['acPass'];
	$cryptVars=$cryptVars . '&Amount=' . str_replace('.', '', $basket['grandTotal']);
	$cryptVars=$cryptVars . '&CurrencyCode=' . '826';
	$cryptVars=$cryptVars . '&OrderID=' . $cart_order_id;
	$cryptVars=$cryptVars . '&TransactionType=' . 'SALE';
	$cryptVars=$cryptVars . '&TransactionDateTime=' . $time;
	$cryptVars=$cryptVars . '&CallbackURL='.$glob['storeURL'].'/confirmed.php';
	$cryptVars=$cryptVars . '&OrderDescription=' . $cart_order_id;
	$cryptVars=$cryptVars . '&CustomerName=' . $basket['delInf']['title'].' '.$basket['delInf']['firstName'].' '.$basket['delInf']['lastName'];
	$cryptVars=$cryptVars . '&Address1=' . $basket['delInf']['add_1'];
	$cryptVars=$cryptVars . '&Address2=' . $basket['delInf']['add_2'];
	$cryptVars=$cryptVars . '&Address3=';
	$cryptVars=$cryptVars . '&Address4=';
	$cryptVars=$cryptVars . '&City=' . $basket['delInf']['town'];
	$cryptVars=$cryptVars . '&State=' . $basket['delInf']['county'];
	$cryptVars=$cryptVars . '&PostCode=' . $basket['delInf']['postcode'];
	$cryptVars=$cryptVars . '&CountryCode=' . $countryCode;
	$cryptVars=$cryptVars . "&CV2Mandatory=" . 'true';
	$cryptVars=$cryptVars . "&Address1Mandatory=" . 'true';
	$cryptVars=$cryptVars . "&CityMandatory=" . 'true';
	$cryptVars=$cryptVars . "&PostCodeMandatory=" . 'true';
	$cryptVars=$cryptVars . "&StateMandatory=" . 'true';
	$cryptVars=$cryptVars . "&CountryMandatory=" . 'true';
	$cryptVars=$cryptVars . "&ResultDeliveryMethod=" . 'SERVER';
	$cryptVars=$cryptVars . "&ServerResultURL=".$glob['storeURL']."/modules/gateway/cardSave/confirmed.php";
	$cryptVars=$cryptVars . "&PaymentFormDisplaysResult=" . 'false';
	
	return sha1($cryptVars);
}

function fixedVars(){
	global $module, $basket, $ccUserData, $cart_order_id, $config, $glob, $GLOBALS, $db;

	$cCode = $db->select("
	SELECT numcode
	FROM ".$glob['dbprefix']."CubeCart_iso_countries
	WHERE id = ".$db->mySQLSafe($basket['delInf']['country']));
	
	if($cCode == TRUE){
		$countryCode = $cCode[0]["numcode"];
	}else{
		$countryCode = "826";
	}
	
	$time = date('Y-m-d H:i:s P', time());
	//$time = "2010-07-01 18:31:49 +01:00";

	$hiddenVars = "<input type='hidden' name='HashDigest' value='".createHash($time, $countryCode)."' />
					<input type='hidden' name='MerchantID' value='".$module['acName']."' />
					<input type='hidden' name='Amount' value='".str_replace('.', '', $basket['grandTotal'])."' />
					<input type='hidden' name='CurrencyCode' value='826' />
					<input type='hidden' name='OrderID' value='".$cart_order_id."' />
					<input type='hidden' name='TransactionType' value='SALE' />
					<input type='hidden' name='TransactionDateTime' value='".$time."' />
					<input type='hidden' name='CallbackURL' value='http://www.tirallyschool.co.uk/confirmed.php' />
					<input type='hidden' name='OrderDescription' value='".$cart_order_id."' />
					<input type='hidden' name='CustomerName' value='".$basket['delInf']['title']." ".$basket['delInf']['firstName']." ".$basket['delInf']['lastName']."' />
					<input type='hidden' name='Address1' value='".$basket['delInf']['add_1']."' />
					<input type='hidden' name='Address2' value='".$basket['delInf']['add_2']."' />
					<input type='hidden' name='Address3' value='' />
					<input type='hidden' name='Address4' value='' />
					<input type='hidden' name='City' value='".$basket['delInf']['town']."' />
					<input type='hidden' name='State' value='".$basket['delInf']['county']."' />
					<input type='hidden' name='PostCode' value='".$basket['delInf']['postcode']."' />
					<input type='hidden' name='CountryCode' value='".$countryCode."' />
					<input type='hidden' name='CV2Mandatory' value='true' />
					<input type='hidden' name='Address1Mandatory' value='true' />
					<input type='hidden' name='CityMandatory' value='true' />
					<input type='hidden' name='PostCodeMandatory' value='true' />
					<input type='hidden' name='StateMandatory' value='true' />
					<input type='hidden' name='CountryMandatory' value='true' />
					<input type='hidden' name='ResultDeliveryMethod' value='SERVER' />
					<input type='hidden' name='ServerResultURL' value='".$glob['storeURL']."/modules/gateway/cardSave/confirmed.php' />
					<input type='hidden' name='PaymentFormDisplaysResult' value='false' />";
					
			return $hiddenVars;
}

function repeatVars(){
	return "";
}

function successConfirm(){
	global $db;
	
	if(isset($_POST["OrderID"]) && isset($_POST["StatusCode"]) && $_POST["StatusCode"] == 0){
		return TRUE;
	}else{
		return FALSE;
	}
}

function success(){
	global $db, $glob;
	
	$output = $db->select("SELECT status FROM ".$glob['dbprefix']."CubeCart_order_sum WHERE cart_order_id = ".$db->mySQLSafe($_GET["OrderID"]));
	
	if($output[0]["status"] == 2){
		return TRUE;
	}else{
		return FALSE;
	}
}

///////////////////////////

// Other Vars

////////

$formAction = "https://mms.cardsaveonlinepayments.com/Pages/PublicPages/PaymentForm.aspx";

$formMethod = "post";

$formTarget = "_self";

$transfer = "auto";

$stateUpdate = FALSE;

?>