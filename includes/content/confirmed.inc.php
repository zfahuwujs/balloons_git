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
|	confirmed.inc.php
|   ========================================
|	Order Confirmation
+--------------------------------------------------------------------------
*/


phpExtension();


require_once("classes/newCart.php");
$cart = new basket();
$basket = $cart->getContents();
$gateway = $cart->getGateway();
$cart_order_id = $cart->getOrderId();




// WORK OUT IS THE ORDER WAS SUCCESSFULL OR NOT ;)

// 1. Include gateway file



// Override basket value as fix for some gateways

if(isset($_GET['pg']) && !empty($_GET['pg'])){

	$pg = preg_replace('/[^a-zA-Z0-9_\-\+]/', '',base64_decode($_GET['pg']));

	if(ereg("Authorize|WorldPay|SagePay|Protx|SECPay|BluePay|mals-e|Nochex_APC",$pg)){

		$basket['gateway'] = $pg;

	}



############################################################################################

// Following lines added for Sir William's PayPal AutoReturn Fix

} elseif(isset($_GET['tx']) && isset($_GET['st'])) {

	$gateway = "PayPal";
	$module = fetchDbConfig("PayPal");
 
 ############################################################################################

} elseif(isset($_GET["OrderID"]) && isset($_GET["CrossReference"])){

 $basket['gateway'] = "cardSave";

############################################################################################

} elseif(!isset($basket['gateway'])){

	echo "Error: No payment gateway variable is set!";

	exit;
}

include("modules/gateway/".$basket['gateway']."/transfer.inc.php");


// Google Analytics Ecommerce mod- Set to FALSE to turn off, set to analytics code to turn on (i.e. UA-XXXXXXX-XX)
// For more info see: http://code.google.com/apis/analytics/docs/tracking/gaTrackingEcommerce.html
$AnalyticsCode = $config['analytics_eid'];


// 2. Include function which returns ture or false
$success = success();

if(($success == FALSE ) && (isset($_GET['tx']) && isset($_GET['st']))){
        $success = pdtcheck();
}
if($success==false && $gateway == "PayPal" && $_GET['f']!=1){
	$wait = true;
}else{
	$wait = false;
}

if($_GET['waiting'] > 300){
	$wait = false;
}



$confirmation = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/confirmed.tpl");



	$confirmation->assign("LANG_CONFIRMATION_SCREEN",$lang['front']['confirmed']['confirmation_screen']);

	$confirmation->assign("LANG_CART",$lang['front']['confirmed']['cart']);

	$confirmation->assign("LANG_ADDRESS",$lang['front']['confirmed']['address']);

	$confirmation->assign("LANG_PAYMENT",$lang['front']['confirmed']['payment']);

	$confirmation->assign("LANG_COMPLETE",$lang['front']['confirmed']['complete']);

	

	if($success == TRUE){


		if($stateUpdate == TRUE){

				include_once("includes/orderSuccess.inc.php");

		}

		

		$confirmation->assign("LANG_ORDER_SUCCESSFUL",$lang['front']['confirmed']['order_success']);

		

		// add affilate tracking code/module

		$affiliateModule = $db->select("SELECT status, folder, `default` FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='affiliate' AND status = 1");

	

		if($affiliateModule == TRUE) {

		

			for($i=0; $i<count($affiliateModule); $i++){

			

				if($affiliateModule[$i]['status']==1){

						$order = $db->select("SELECT prod_total FROM ".$glob['dbprefix']."CubeCart_order_sum WHERE cart_order_id = ".$db->mySQLSafe($cart_order_id)." LIMIT 1");

						include("modules/affiliate/".$affiliateModule[$i]['folder']."/tracker.inc.php");

						// VARS AVAILABLE

						// Order Id Number $cart_order_id

						// Order Total $order[0]['prod_total']

						$confirmation->assign("AFFILIATE_IMG_TRACK",$affCode);

						$confirmation->parse("confirmation.session_true.order_success.aff_track");

				

				}

			

			}

		

		}




	// BEGIN: Robz E-Commerce Tracking

		
	if(isset($config['analytics_uid']) && isset($config['analytics_ecommerce']) && $config['analytics_uid'] != "" && $config['analytics_ecommerce'] == 1){

		//$body->assign("GAT_TRACKING_ID",$config['analytics_uid']);
		
		$gatOrderSum=$db->select("SELECT * FROM CubeCart_order_sum WHERE cart_order_id = '{$cart_order_id}'");
		
		$TransCode='
		
			"'.$gatOrderSum[0]['cart_order_id'].'",
		
			"'.$config['storeName'].'",
		
			"'.$gatOrderSum[0]['prod_total'].'",
		
			"'.$gatOrderSum[0]['total_tax'].'",
		
			"'.$gatOrderSum[0]['total_ship'].'",
		
			"'.$gatOrderSum[0]['town'].'",
		
			"'.$gatOrderSum[0]['county'].'",
		
			"'.$gatOrderSum[0]['country'].'"';
		
		
		$body->assign("GAT_TRANS_CODE",$TransCode);
		
		$gatOrders=$db->select("SELECT * FROM CubeCart_order_inv WHERE cart_order_id = '{$cart_order_id}'");
		
		for($x=0;$x<count($gatOrders);$x++){
		
			$TrackerCode='
			
			"'.$cart_order_id.'",
			
			"'.$gatOrders[$x]['productCode'].'",
			
			"'.$gatOrders[$x]['name'].'",
			
			"'.$gatOrders[$x]['product_options'].'",
			
			"'.$gatOrders[$x]['price']/$gatOrders[$x]['quantity'].'",
			
			"'.$gatOrders[$x]['quantity'].'"';
		
			$body->assign("GAT_ITEMS_CODE",$TrackerCode);
	
			$body->parse("body.google_analytics.gat_body.gat_body_item_repeat");

		}
		
		$body->parse("body.google_analytics.gat_body");
		
	}
	// END: Robz E-Commerce Tracking


		

		$confirmation->parse("confirmation.session_true.order_success");

		

		// empty basket & other session data

		$basket = $cart->emptyCart();

		

	}elseif($wait==true){
		
		if(isset($_GET)){
			$getKeys = array_keys($_GET);
			$refreshurl = '/confirmed.php';
			for($i = 0; $i < count($getKeys); $i++){
				if($getKeys[$i]!='waiting'){
					if($i==0){
						$refreshurl .= '?'.$getKeys[$i].'='.$_GET[$getKeys[$i]];
					}else{
						$refreshurl .= '&'.$getKeys[$i].'='.$_GET[$getKeys[$i]];
					}
				}
			}
			if(isset($refreshurl)){
				$refreshurl .= '&waiting='.($_GET['waiting'] + 5);
			}
		}
		header("refresh:5;url=".$refreshurl); 
		$confirmation->parse("confirmation.session_true.order_wait");
	}else {

		

		$confirmation->assign("LANG_ORDER_FAILED",$lang['front']['confirmed']['order_fail']);

		$confirmation->assign("LANG_ORDER_RETRY",$lang['front']['confirmed']['try_again_desc']);

		$confirmation->assign("LANG_RETRY_BUTTON",$lang['front']['confirmed']['try_again']);

		$confirmation->parse("confirmation.session_true.order_failed");

	}

	

	$confirmation->assign("LANG_LOGIN_REQUIRED",$lang['front']['confirmed']['request_login']);

	

	$confirmation->parse("confirmation.session_true");

	

	$confirmation->parse("confirmation");

	

$page_content = $confirmation->text("confirmation");

?>