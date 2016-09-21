<?php


if ($ccUserData[0]['customer_id'] > 0) {
	
	phpExtension();
	require_once("classes/cart.php");
	
	$cart = new cart();
	$basket = $cart->cartContents($ccUserData[0]['basket']);
	$gateway = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/gateway.tpl");
	$gateway->assign("VAL_SKIN",$config['skinDir']);
	$gateway->assign("LANG_PAYMENT",$lang['front']['gateway']['payment']);
	$gateway->assign("LANG_CART",$lang['front']['gateway']['cart']);
	$gateway->assign("LANG_ADDRESS",$lang['front']['gateway']['address']);
	$gateway->assign("LANG_PAYMENT",$lang['front']['gateway']['payment']);
	$gateway->assign("LANG_COMPLETE",$lang['front']['gateway']['complete']);
	
	// sanitise gateway variable
	if ($basket == TRUE && isset($_POST['gateway']) && !eregi("[^0-9a-z_-]",$_POST['gateway'])) {
		$gateway->assign("CONTINUE_SHOPPING_BUTTON", "");
		$gateway->assign("CONTINUE_SHOPPING_HEIGHT", "25px");	
		$basket = $cart->setVar($_POST['gateway'],"gateway");
		// build order number
		if (!isset($basket['cart_order_id']) || (isset($basket['cart_order_id']) && empty($basket['cart_order_id']))){
			$cart_order_id = date("ymd-His-").rand(1000,9999);
			$cart->setVar($cart_order_id,"cart_order_id");
			$basket = $cart->setVar(0,"mailSent");
			$record['noOrders'] = "noOrders + 1";
			if ($ccUserData[0]['customer_id'] > 0) {
				$where = "customer_id = ".$ccUserData[0]['customer_id'];
			} else $where = "customer_id = 0";
			$update = $db->update($glob['dbprefix']."CubeCart_customer", $record, $where);
		} else {
			$cart_order_id = $basket["cart_order_id"];
			$basket = $cart->setVar(1,"mailSent");
			// delete old orders with that Id
			$where = "cart_order_id = '".$cart_order_id."'";
			$delete = $db->delete($glob['dbprefix']."CubeCart_order_sum", $where);
			$delete = $db->delete($glob['dbprefix']."CubeCart_order_inv", $where);
			$delete = $db->delete($glob['dbprefix']."CubeCart_Downloads", $where);
		}

		include("modules/gateway/".$_POST['gateway']."/transfer.inc.php");	

		// insert order inventory
		$transVars = "";
		for ($i=0; $i<count($basket['invArray']); $i++) {
			$orderInv['cart_order_id'] = $db->mySQLSafe($cart_order_id);
			$orderInv['productId'] = $db->mySQLSafe($basket['invArray'][$i+1]["productId"]);
			$orderInv['name'] = $db->mySQLSafe($basket['invArray'][$i+1]["name"]);
			$orderInv['price'] = $db->mySQLSafe($basket['invArray'][$i+1]["price"]);
			$orderInv['quantity'] = $db->mySQLSafe($basket['invArray'][$i+1]["quantity"]);
			$orderInv['product_options'] = $db->mySQLSafe($basket['invArray'][$i+1]["prodOptions"]);
			$orderInv['productCode'] = $db->mySQLSafe($basket['invArray'][$i+1]["productCode"]);
			$orderInv['digital'] = $db->mySQLSafe($basket['invArray'][$i+1]["digital"]);
			if(isset($basket['invArray'][$i+1]["optionKeys"])){
				$orderInv['optionKeys'] = $db->mySQLSafe($basket['invArray'][$i+1]["optionKeys"]);
			}
			$insert = $db->insert($glob['dbprefix']."CubeCart_order_inv", $orderInv);
			// start mod: Customer Order Confirmation Email - http://cubecart.expandingbrain.com
			include("includes/estelles_mod_store/customer_order_conf_email.gateway1.inc.php");
			// end mod: Customer Order Confirmation Email - by Estelle

			##################################################################################
			## Admin E-Mail Fix by Sir William -- http://www.swscripts.com/
			// build admin email product list
	
			if($basket['mailSent']==0){ // send only if not sent already for current order number
				$prodtext .= sprintf($lang['front']['gateway']['admin_email_body_4'],
							$basket['invArray'][$i+1]["name"]);
	
				if(!empty($basket['invArray'][$i+1]["prodOptions"])){
				$prodtext .= sprintf($lang['front']['gateway']['admin_email_body_5'],
							str_replace(array("\r","\n")," ",$basket['invArray'][$i+1]["prodOptions"]));
				}
				$prodtext .= sprintf($lang['front']['gateway']['admin_email_body_6'],
	
							$basket['invArray'][$i+1]["quantity"],
							$basket['invArray'][$i+1]["productCode"],
							priceFormat($basket['invArray'][$i+1]["price"]));
	
			}

			##################################################################################

			foreach ($orderInv as $key => $value){
				$orderInv[$key] = str_replace("'","",$value);
			}
	
			$transVars .= repeatVars();

			if($basket['invArray'][$i+1]["digital"]==1){
				$digitalProduct['cart_order_id'] = $db->mySQLSafe($cart_order_id);
				$digitalProduct['customerId'] = $db->mySQLSafe($ccUserData[0]['customer_id']);
				$digitalProduct['expire'] = $db->mySQLSafe(time()+$config['dnLoadExpire']);
				$digitalProduct['productId'] = $db->mySQLSafe($basket['invArray'][$i+1]["productId"]);
				$digitalProduct['accessKey'] = $db->mySQLSafe(randomPass());
				$insert = $db->insert($glob['dbprefix']."CubeCart_Downloads", $digitalProduct);
			}
		}

		if($insert==FALSE) {
			echo "An error building the order inventory was encountered. Please inform a member of staff.";
			exit;
		}

		// insert order summary

		//////////////////
		// Invoice info
		/////
	
		$orderSum['cart_order_id'] = $db->mySQLSafe($cart_order_id);
		$orderSum['customer_id'] = $db->mySQLSafe($ccUserData[0]['customer_id']);
		$orderSum['email'] = $db->mySQLSafe($ccUserData[0]['email']);
		$orderSum['name'] = $db->mySQLSafe($ccUserData[0]['title']." ".$ccUserData[0]['firstName']." ".$ccUserData[0]['lastName']); 
		$orderSum['add_1'] = $db->mySQLSafe($ccUserData[0]['add_1']);
		$orderSum['add_2'] = $db->mySQLSafe($ccUserData[0]['add_2']);
		$orderSum['town'] = $db->mySQLSafe($ccUserData[0]['town']);
		$orderSum['county'] = $db->mySQLSafe($ccUserData[0]['county']);
		$orderSum['postcode'] = $db->mySQLSafe($ccUserData[0]['postcode']);
		$orderSum['country'] = $db->mySQLSafe(countryName($ccUserData[0]['country']));
		$orderSum['phone'] = $db->mySQLSafe($ccUserData[0]['phone']);
		$orderSum['mobile'] = $db->mySQLSafe($ccUserData[0]['mobile']);
	
		if($basket['couponCode']){
			$orderSum['couponCode'] = $db->mySQLSafe($basket['couponCode']); 
		}
		
		if($basket['discountTot']){
			$orderSum['discountTot'] = $db->mySQLSafe($basket['discountTot']); 
			$discountTotal = $basket['discountTot'];
		}else{
			$discountTotal = "0.00";
		}
		
		$currency = $db->select("SELECT currency FROM ".$glob['dbprefix']."CubeCart_sessions WHERE sessId = ".$db->mySQLSafe($_SESSION['ccUser']));

		if($currency == TRUE){
			$orderSum['currency'] = $db->mySQLSafe($currency[0]['currency']);
		} else {
			$orderSum['currency'] = $config['defaultCurrency'];
		}

		//////////////////
		// Delivery info
		/////
	
		$orderSum['name_d'] = $db->mySQLSafe($basket['delInf']['title']." ".$basket['delInf']['firstName']." ".$basket['delInf']['lastName']); 
		$orderSum['add_1_d'] = $db->mySQLSafe($basket['delInf']['add_1']);
		$orderSum['add_2_d'] = $db->mySQLSafe($basket['delInf']['add_2']);
		$orderSum['town_d'] = $db->mySQLSafe($basket['delInf']['town']);
		$orderSum['county_d'] = $db->mySQLSafe($basket['delInf']['county']);
		$orderSum['postcode_d'] = $db->mySQLSafe($basket['delInf']['postcode']);
		$orderSum['country_d'] = $db->mySQLSafe(countryName($basket['delInf']['country']));

		//////////////////
		// Summary
		/////
	
		$orderSum['subtotal'] = $db->mySQLSafe($basket['subTotal']);
	
		$orderSum['total_ship'] = $db->mySQLSafe($basket['shipCost']);
	
		$orderSum['total_tax'] = $db->mySQLSafe($basket['tax']);
	
		$orderSum['prod_total'] = $db->mySQLSafe($basket['grandTotal']);
	
		$orderSum['shipMethod'] = $db->mySQLSafe($basket['shipMethod']); 
	
		
	
		if(!empty($sec_order_id)){
	
			$orderSum['sec_order_id'] = $db->mySQLSafe($sec_order_id);
	
		}
	
		
	
		$orderSum['ip'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
	
		$orderSum['time'] = $db->mySQLSafe(time());
	
		$orderSum['customer_comments'] = $db->mySQLSafe($_POST['customer_comments']);
	
		
	
		$cart->setVar($_POST['customer_comments'],"customer_comments");
	
		
	
		$orderSum['gateway'] = $db->mySQLSafe($_POST['gateway']);
	
		
	
		$insert = $db->insert($glob['dbprefix']."CubeCart_order_sum", $orderSum);
	
		
	
		if($insert==FALSE) {
	
			echo "An error building the order summary was encountered. Please inform a member of staff.";
	
			exit;
	
		}
	
		
	
		##################################################################################
	
		## Admin E-Mail Fix by Sir William -- http://www.swscripts.com/
	
		// notify shop owner of new order
	
		
	
		if($basket['mailSent']==0){ // send only if not sent already for current order number
	
			
	
			include_once("classes/htmlMimeMail.php");
	
			$mail = new htmlMimeMail();
	
		
	
			if($basket['shipCost']>0){
	
				$emailShipCost = $basket['shipCost'];
	
			} else {
	
				$emailShipCost = "0.00";
	
			}
	
			$text = sprintf($lang['front']['gateway']['admin_email_body_1'],
	
						$cart_order_id,
	
						formatTime(time()),
	
						$ccUserData[0]['title']." ".$ccUserData[0]['firstName']." ".$ccUserData[0]['lastName'],
	
						$ccUserData[0]['email'],
	
						priceFormat($basket['subTotal']),
						
						priceFormat($discountTotal),
	
						priceFormat($emailShipCost),
	
						priceFormat($basket['tax']),
	
						priceFormat($basket['grandTotal']),
	
						$ccUserData[0]['title']." ".$ccUserData[0]['firstName']." ".$ccUserData[0]['lastName'],
	
						$ccUserData[0]['add_1'],
	
						$ccUserData[0]['add_2'],
	
						$ccUserData[0]['town'],
	
						$ccUserData[0]['county'],
	
						$ccUserData[0]['postcode'],
	
						countryName($ccUserData[0]['country']),
	
						$basket['delInf']['title']." ".$basket['delInf']['firstName']." ".$basket['delInf']['lastName'],
	
						$basket['delInf']['add_1'],
	
						$basket['delInf']['add_2'],
	
						$basket['delInf']['town'],
	
						$basket['delInf']['county'],
	
						$basket['delInf']['postcode'],
	
						countryName($basket['delInf']['country']),
	
						str_replace("_"," ",$_POST['gateway']),
	
						str_replace("_"," ",$basket['shipMethod']));
	
		
	
			if(!empty($_POST['customer_comments'])){
	
				$text .= sprintf($lang['front']['gateway']['admin_email_body_2'],
	
							$_POST['customer_comments']);
	
			}
	
			$text .= $lang['front']['gateway']['admin_email_body_3'];
			$text .= $prodtext;
	
			$mail->setText($text);
	
			// uncomment the following two lines to have the order message come from customer
	
			// $mail->setReturnPath($ccUserData[0]['email']);
	
			// $mail->setFrom($ccUserData[0]['firstName']." ".$ccUserData[0]['lastName'].' <'.$ccUserData[0]['email'].'>');
	
		
	
			// uncomment the following two lines to have the order message come from store admin
	
			$mail->setReturnPath($config['masterEmail']);
	
			$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
	
		
	
			$mail->setSubject($lang['front']['gateway']['newOrderSubj'].$cart_order_id);
	
			$mail->setHeader('X-Mailer', 'CubeCart Mailer');
	
			$send = $mail->send(array($config['masterEmail']), $config['mailMethod']);	
	
		}
	
	
	
		##################################################################################
		// start mod: Customer Order Confirmation Email - http://cubecart.expandingbrain.com
		$coce_mod = fetchDbConfig("Customer_Order_Conf_Email");
		if ($coce_mod && $coce_mod['status'] && ($basket['mailSent']==0 || $basket['mailResend']==1)) { 
			include("includes/estelles_mod_store/customer_order_conf_email.gateway2.inc.php");
		}
		// end mod: Customer Order Confirmation Email - by Estelle
	
	
	
		if($transfer == "manual"){
	
			
	
			$gateway->assign("LANG_FORM_TITLE",$lang['front']['gateway']['fill_out_below']);
	
			
	
			include("modules/gateway/".$_POST['gateway']."/form.inc.php");
			$gateway->assign("FORM_TEMPLATE",$formTemplate);
			$gateway->parse("gateway.cart_true.transfer.manual_submit");
		} else {
			$gateway->assign("LANG_TRANSFERRING",$lang['front']['gateway']['transferring']);
			$gateway->parse("gateway.cart_true.transfer.auto_submit");
		}
	
		$gateway->assign("VAL_FORM_ACTION",$formAction);
		$gateway->assign("VAL_FORM_METHOD",$formMethod);
		$gateway->assign("VAL_TARGET",$formTarget);

		// add fixed vars
	
		$transVars .= fixedVars();
	
		$gateway->assign("FORM_PARAMETERS",$transVars);
		$gateway->assign("LANG_CHECKOUT",$lang['front']['gateway']['go_now']);
		$gateway->parse("gateway.cart_true.transfer");
		$gateway->parse("gateway.cart_true");

	} elseif($basket==TRUE && !isset($_POST['gateway'])) {
	
		$gateway->assign("CONTINUE_SHOPPING_BUTTON", "<p><div><a href='/' class='txtUpdate'>".$lang['front']['step1']['cont_shopping']."</a></div></p>");
		$gateway->assign("CONTINUE_SHOPPING_HEIGHT", "50px");
		$gateway->assign("VAL_FORM_ACTION","cart.php?act=step5");
		$gateway->assign("VAL_FORM_METHOD","post");
		$gateway->assign("VAL_TARGET","_self");
		$gateway->assign("LANG_CHECKOUT",$lang['front']['gateway']['continue']);
		$gateway->assign("LANG_CHOOSE_GATEWAY",$lang['front']['gateway']['choose_method']);
	
		$gatewayModules = $db->select("SELECT folder, `default` FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='gateway' AND status = 1");
	
		if ($gatewayModules == TRUE) {
			$gateway->assign("LANG_COMMENTS",$lang['front']['gateway']['your_comments']);
	
			for ($i=0; $i<count($gatewayModules); $i++) {

				$gateway->assign("TD_CART_CLASS",cellColor($i, $tdEven="tdcartEven", $tdOdd="tdcartOdd"));
				$module = fetchDbConfig($gatewayModules[$i]['folder']);
				$gateway->assign("VAL_GATEWAY_DESC",$module['desc']);
				$gateway->assign("VAL_GATEWAY_FOLDER",$gatewayModules[$i]['folder']);
				
				if ($gatewayModules[$i]['default'] == 1) {
					$gateway->assign("VAL_CHECKED","checked='checked'");
				} else {
					$gateway->assign("VAL_CHECKED","");
				}
				$gateway->parse("gateway.cart_true.choose_gate.gateways_true");
			}
	
			if (isset($basket['customer_comments'])) {
				$gateway->assign("VAL_CUSTOMER_COMMENTS",$basket['customer_comments']);
			} 

			$gateway->parse("gateway.cart_true.choose_gate");
		} else {
			$gateway->assign("LANG_GATEWAYS_FALSE",$lang['front']['gateway']['none_configured']);
			$gateway->parse("gateway.cart_true.choose_gate.gateways_flase");
			$gateway->parse("gateway.cart_true.choose_gate");
		}
		$gateway->parse("gateway.cart_true");
		
	} else {
		$gateway->assign("CONTINUE_SHOPPING_BUTTON", "<p><div><a href='/' class='txtUpdate'>".$lang['front']['step1']['cont_shopping']."</a></div></p>");
		$gateway->assign("CONTINUE_SHOPPING_HEIGHT", "50px");
		$gateway->assign("LANG_CART_EMPTY","Your shopping cart is currently empty.");
		$gateway->parse("gateway.cart_false");
	} 

	$gateway->parse("gateway");
	$page_content = $gateway->text("gateway");

} else {

	phpExtension();
	
	require_once("classes/cart.php");
	
	$cart = new cart();
	
	$basket = $cart->cartContents($ccUserData[0]['basket']);
	
	$gateway = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/gateway.tpl");
	
	$gateway->assign("VAL_SKIN",$config['skinDir']);
	
	$gateway->assign("LANG_PAYMENT",$lang['front']['gateway']['payment']);
	
	$gateway->assign("LANG_CART",$lang['front']['gateway']['cart']);
	
	$gateway->assign("LANG_ADDRESS",$lang['front']['gateway']['address']);
	
	$gateway->assign("LANG_PAYMENT",$lang['front']['gateway']['payment']);
	
	$gateway->assign("LANG_COMPLETE",$lang['front']['gateway']['complete']);
	
	// sanitise gateway variable

	$gatewayModules = $db->select("SELECT folder, `default` FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='gateway' AND status = 1");

	if ($basket == TRUE && isset($_POST['gateway']) && !eregi("[^0-9a-z_-]",$_POST['gateway'])) {
	
		$gateway->assign("CONTINUE_SHOPPING_BUTTON", "");
		$gateway->assign("CONTINUE_SHOPPING_HEIGHT", "25px");

		//$basket = $cart->setVar($basket['shipCost'],"shipCost");
		$basket = $cart->setVar($_POST['gateway'],"gateway");	
		include("modules/gateway/".$_POST['gateway']."/transfer.inc.php");
	
		// build order number
	
		if(!isset($basket['cart_order_id']) || (isset($basket['cart_order_id']) && empty($basket['cart_order_id']))){
	
			
	
			$cart_order_id = date("ymd-His-").rand(1000,9999);
	
			$cart->setVar($cart_order_id,"cart_order_id");
	
			$basket = $cart->setVar(0,"mailSent");
	
			
	
			//$record['noOrders'] = "noOrders + 1";
	//
	//		$where = "customer_id = ".$ccUserData[0]['customer_id'];
	//
	//		$update = $db->update($glob['dbprefix']."CubeCart_customer", $record, $where);
	
			
	
			
	
		} else {
	
			
	
			$cart_order_id = $basket["cart_order_id"];
	
			$basket = $cart->setVar(1,"mailSent");
	
			
	
			// delete old orders with that Id
	
			$where = "cart_order_id = '".$cart_order_id."'";
	
			$delete = $db->delete($glob['dbprefix']."CubeCart_order_sum", $where);
	
			$delete = $db->delete($glob['dbprefix']."CubeCart_order_inv", $where);
	
			$delete = $db->delete($glob['dbprefix']."CubeCart_Downloads", $where);
	
		
	
		}
	
			
	
		// insert order inventory
	
		
	
		$transVars = "";
	
		
	
		for($i=0;$i<count($basket['invArray']);$i++){
	
			
	
			$orderInv['cart_order_id'] = $db->mySQLSafe($cart_order_id);
	
			$orderInv['productId'] = $db->mySQLSafe($basket['invArray'][$i+1]["productId"]);
	
			$orderInv['name'] = $db->mySQLSafe($basket['invArray'][$i+1]["name"]);
	
			$orderInv['price'] = $db->mySQLSafe($basket['invArray'][$i+1]["price"]);
	
			$orderInv['quantity'] = $db->mySQLSafe($basket['invArray'][$i+1]["quantity"]);
	
			$orderInv['product_options'] = $db->mySQLSafe($basket['invArray'][$i+1]["prodOptions"]);
	
			$orderInv['productCode'] = $db->mySQLSafe($basket['invArray'][$i+1]["productCode"]);
	
			$orderInv['digital'] = $db->mySQLSafe($basket['invArray'][$i+1]["digital"]);
	
		
	
			$insert = $db->insert($glob['dbprefix']."CubeCart_order_inv", $orderInv);
		// start mod: Customer Order Confirmation Email - http://cubecart.expandingbrain.com
			include("includes/estelles_mod_store/customer_order_conf_email.gateway1.inc.php");
			// end mod: Customer Order Confirmation Email - by Estelle
	
	
	
			##################################################################################
	
			## Admin E-Mail Fix by Sir William -- http://www.swscripts.com/
	
			// build admin email product list
	
			
	
			if($basket['mailSent']==0){ // send only if not sent already for current order number
	
			
	
				$prodtext .= sprintf($lang['front']['gateway']['admin_email_body_4'],
	
							$basket['invArray'][$i+1]["name"]);
	
				
	
				if(!empty($basket['invArray'][$i+1]["prodOptions"])){
	
				$prodtext .= sprintf($lang['front']['gateway']['admin_email_body_5'],
	
							str_replace(array("\r","\n")," ",$basket['invArray'][$i+1]["prodOptions"]));
	
				}
	
	
				
	
				$prodtext .= sprintf($lang['front']['gateway']['admin_email_body_6'],
	
							$basket['invArray'][$i+1]["quantity"],
	
							$basket['invArray'][$i+1]["productCode"],
	
							priceFormat($basket['invArray'][$i+1]["price"]));
	
						
	
			}
	
						
	
			##################################################################################
	
			
	
			foreach($orderInv as $key => $value){
	
			
	
				$orderInv[$key] = str_replace("'","",$value);
	
			
	
			}
	
			$transVars .= repeatVars();
	
			
	
			if($basket['invArray'][$i+1]["digital"]==1){
	
			
	
				$digitalProduct['cart_order_id'] = $db->mySQLSafe($cart_order_id);
	
				$digitalProduct['customerId'] = $db->mySQLSafe(0);
	
				$digitalProduct['expire'] = $db->mySQLSafe(time()+$config['dnLoadExpire']);
	
				$digitalProduct['productId'] = $db->mySQLSafe($basket['invArray'][$i+1]["productId"]);
	
				$digitalProduct['accessKey'] = $db->mySQLSafe(randomPass());
	
				$insert = $db->insert($glob['dbprefix']."CubeCart_Downloads", $digitalProduct);
	
				
	
			}
	
		
	
		}
	
		
	
		if($insert==FALSE) {
	
			echo "An error building the order inventory was encountered. Please inform a member of staff.";
	
			exit;
	
		}
	
		
		
	
		// insert order summary
	
		
		$cust["title"] = $db->mySQLSafe($basket['invInf']['title']);
		$cust["firstName"] = $db->mySQLSafe($basket['invInf']['firstName']);
		$cust["lastName"] = $db->mySQLSafe($basket['invInf']['lastName']);
		$cust["add_1"] = $db->mySQLSafe($basket['invInf']['add_1']);
		$cust["add_2"] = $db->mySQLSafe($basket['invInf']['add_2']);
		$cust["town"] = $db->mySQLSafe($basket['invInf']['town']);
		$cust["county"] = $db->mySQLSafe($basket['invInf']['county']);
		$cust["postcode"] = $db->mySQLSafe($basket['invInf']['postcode']);
		$cust["phone"] = $db->mySQLSafe($basket['invInf']['telephone']);
		$cust["mobile"] = $db->mySQLSafe($basket['invInf']['mobile']);
		$cust["country"] = $db->mySQLSafe($basket['invInf']['country']);
		$cust["email"] = $db->mySQLSafe($basket['invInf']['email']);
		//Set the customer type in the database to 2 so that we know that this is customer who purchased without registering
		$cust["type"] = $db->mySQLSafe(2);
		
		$insert = $db->insert($glob['dbprefix']."CubeCart_customer", $cust);
		$cust_insert_id = mysql_insert_id();
		
	
		//////////////////
	
		// Invoice info
	
		/////
	
		$orderSum['cart_order_id'] = $db->mySQLSafe($cart_order_id);
	
		$orderSum['customer_id'] = $db->mySQLSafe($cust_insert_id);
	
		$orderSum['email'] = $db->mySQLSafe($basket['invInf']['email']);
	
		$orderSum['name'] = $db->mySQLSafe($basket['invInf']['title']." ".$basket['invInf']['firstName']." ".$basket['invInf']['lastName']); 
	
		$orderSum['add_1'] = $db->mySQLSafe($basket['invInf']['add_1']);
	
		$orderSum['add_2'] = $db->mySQLSafe($basket['invInf']['add_2']);
	
		$orderSum['town'] = $db->mySQLSafe($basket['invInf']['town']);
	
		$orderSum['county'] = $db->mySQLSafe($basket['invInf']['county']);
	
		$orderSum['postcode'] = $db->mySQLSafe($basket['invInf']['postcode']);
	
		$orderSum['country'] = $db->mySQLSafe(countryName($basket['invInf']['country']));
	
		$orderSum['phone'] = $db->mySQLSafe($basket['invInf']['phone']);
	
		$orderSum['mobile'] = $db->mySQLSafe($basket['invInf']['mobile']);
	
		if($basket['couponCode']){
			$orderSum['couponCode'] = $db->mySQLSafe($basket['couponCode']); 
		}
		
		if($basket['discountTot']){
			$orderSum['discountTot'] = $db->mySQLSafe($basket['discountTot']); 
			$discountTotal = $basket['discountTot'];
		}else{
			$discountTotal = "0.00";
		}
	
		$currency = $db->select("SELECT currency FROM ".$glob['dbprefix']."CubeCart_sessions WHERE sessId = ".$db->mySQLSafe($_SESSION['ccUser']));
	
		
	
		if($currency == TRUE){
	
			$orderSum['currency'] = $db->mySQLSafe($currency[0]['currency']);
	
		} else {
	
			$orderSum['currency'] = $config['defaultCurrency'];
	
		}
	
		//////////////////
	
		// Delivery info
	
		/////
	
		$orderSum['name_d'] = $db->mySQLSafe($basket['delInf']['title']." ".$basket['delInf']['firstName']." ".$basket['delInf']['lastName']); 
	
		$orderSum['add_1_d'] = $db->mySQLSafe($basket['delInf']['add_1']);
	
		$orderSum['add_2_d'] = $db->mySQLSafe($basket['delInf']['add_2']);
	
		$orderSum['town_d'] = $db->mySQLSafe($basket['delInf']['town']);
	
		$orderSum['county_d'] = $db->mySQLSafe($basket['delInf']['county']);
	
		$orderSum['postcode_d'] = $db->mySQLSafe($basket['delInf']['postcode']);
	
		$orderSum['country_d'] = $db->mySQLSafe(countryName($basket['delInf']['country']));
	
		
	
		//////////////////
	
		// Summary
	
		/////
	
		$orderSum['subtotal'] = $db->mySQLSafe($basket['subTotal']);
	
		$orderSum['total_ship'] = $db->mySQLSafe($basket['shipCost']);
	
		$orderSum['total_tax'] = $db->mySQLSafe($basket['tax']);
	
		$orderSum['prod_total'] = $db->mySQLSafe($basket['grandTotal']);
	
		$orderSum['shipMethod'] = $db->mySQLSafe($basket['shipMethod']); 
	
		
	
		if(!empty($sec_order_id)){
	
			$orderSum['sec_order_id'] = $db->mySQLSafe($sec_order_id);
	
		}
	
		
	
		$orderSum['ip'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
	
		$orderSum['time'] = $db->mySQLSafe(time());
	
		$orderSum['customer_comments'] = $db->mySQLSafe($_POST['customer_comments']);
	
		
	
		$cart->setVar($_POST['customer_comments'],"customer_comments");
	
		
	
		$orderSum['gateway'] = $db->mySQLSafe($_POST['gateway']);
	
		
	
		$insert = $db->insert($glob['dbprefix']."CubeCart_order_sum", $orderSum);
	
		
	
		if($insert==FALSE) {
	
			echo "An error building the order summary was encountered. Please inform a member of staff.";
	
			exit;
	
		}
	
		
	
		##################################################################################
	
		## Admin E-Mail Fix by Sir William -- http://www.swscripts.com/
	
		// notify shop owner of new order
	
		
	
		if($basket['mailSent']==0){ // send only if not sent already for current order number
	
			
	
			include_once("classes/htmlMimeMail.php");
	
			$mail = new htmlMimeMail();
	
		
	
			if($basket['shipCost']>0){
	
				
	
				$emailShipCost = $basket['shipCost'];
	
			
	
			} else {
	
			
	
				$emailShipCost = "0.00";
	
			
	
			}
	
		
	
			$text = sprintf($lang['front']['gateway']['admin_email_body_1'],
	
						$cart_order_id,
	
						formatTime(time()),
	
						$basket['invInf']['title']." ".$basket['invInf']['firstName']." ".$basket['invInf']['lastName'],
	
						$basket['invInf']['email'],
	
						priceFormat($basket['subTotal']),
						
						priceFormat($discountTotal),
	
						priceFormat($emailShipCost),
	
						priceFormat($basket['tax']),
	
						priceFormat($basket['grandTotal']),
	
						$basket['invInf']['title']." ".$basket['invInf']['firstName']." ".$basket['invInf']['lastName'],
	
						$basket['invInf']['add_1'],
	
						$basket['invInf']['add_2'],
	
						$basket['invInf']['town'],
	
						$basket['invInf']['county'],
	
						$basket['invInf']['postcode'],
	
						countryName($basket['invInf']['country']),
	
						$basket['delInf']['title']." ".$basket['delInf']['firstName']." ".$basket['delInf']['lastName'],
	
						$basket['delInf']['add_1'],
	
						$basket['delInf']['add_2'],
	
						$basket['delInf']['town'],
	
						$basket['delInf']['county'],
	
						$basket['delInf']['postcode'],
	
						countryName($basket['delInf']['country']),
	
						str_replace("_"," ",$_POST['gateway']),
	
						str_replace("_"," ",$basket['shipMethod']));
	
		
	
			if(!empty($_POST['customer_comments'])){
	
				$text .= sprintf($lang['front']['gateway']['admin_email_body_2'],
	
							$_POST['customer_comments']);
	
			}
	
		
	
			$text .= $lang['front']['gateway']['admin_email_body_3'];
	
		
	
			$text .= $prodtext;
	
		
	
			$mail->setText($text);
	
			// uncomment the following two lines to have the order message come from customer
	
			// $mail->setReturnPath($ccUserData[0]['email']);
	
			// $mail->setFrom($ccUserData[0]['firstName']." ".$ccUserData[0]['lastName'].' <'.$ccUserData[0]['email'].'>');
	
		
	
			// uncomment the following two lines to have the order message come from store admin
	
			$mail->setReturnPath($config['masterEmail']);
	
			$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
	
		
	
			$mail->setSubject($lang['front']['gateway']['newOrderSubj'].$cart_order_id);
	
			$mail->setHeader('X-Mailer', 'CubeCart Mailer');
	
			$send = $mail->send(array($config['masterEmail']), $config['mailMethod']);
	
		}

		##################################################################################
		// start mod: Customer Order Confirmation Email - http://cubecart.expandingbrain.com
		$coce_mod = fetchDbConfig("Customer_Order_Conf_Email");
		if ($coce_mod && $coce_mod['status'] && ($basket['mailSent']==0 || $basket['mailResend']==1)) { 
			include("includes/estelles_mod_store/customer_order_conf_email.gateway2.inc.php");
		}
		// end mod: Customer Order Confirmation Email - by Estelle

		if ($transfer == "manual"){
			$gateway->assign("LANG_FORM_TITLE",$lang['front']['gateway']['fill_out_below']);
			include("modules/gateway/".$_POST['gateway']."/form.inc.php");
			$gateway->assign("FORM_TEMPLATE",$formTemplate);
			$gateway->parse("gateway.cart_true.transfer.manual_submit");

		} else {
			$gateway->assign("LANG_TRANSFERRING",$lang['front']['gateway']['transferring']);
			$gateway->parse("gateway.cart_true.transfer.auto_submit");
		}

		$gateway->assign("VAL_FORM_ACTION",$formAction);
		$gateway->assign("VAL_FORM_METHOD",$formMethod);
		$gateway->assign("VAL_TARGET",$formTarget);

		// add fixed vars
		$transVars .= fixedVars();
		$gateway->assign("FORM_PARAMETERS",$transVars);
		$gateway->assign("LANG_CHECKOUT",$lang['front']['gateway']['go_now']);
		$gateway->parse("gateway.cart_true.transfer");
		$gateway->parse("gateway.cart_true");
		
	} elseif($basket==TRUE && !isset($_POST['gateway'])) {
	
		$gateway->assign("CONTINUE_SHOPPING_BUTTON", "<p><div><a href='/' class='txtUpdate'>".$lang['front']['step1']['cont_shopping']."</a></div></p>");
		$gateway->assign("CONTINUE_SHOPPING_HEIGHT", "50px");
		$gateway->assign("VAL_FORM_ACTION","cart.php?act=step5");
		$gateway->assign("VAL_FORM_METHOD","post");
		$gateway->assign("VAL_TARGET","_self");
		$gateway->assign("LANG_CHECKOUT",$lang['front']['gateway']['continue']);
		$gateway->assign("LANG_CHOOSE_GATEWAY",$lang['front']['gateway']['choose_method']);
		
		$gatewayModules = $db->select("SELECT folder, `default` FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='gateway' AND status = 1");
		
		if ($gatewayModules == TRUE) {

			$gateway->assign("LANG_COMMENTS",$lang['front']['gateway']['your_comments']);

			for ($i=0; $i<count($gatewayModules); $i++) {
				$gateway->assign("TD_CART_CLASS",cellColor($i, $tdEven="tdcartEven", $tdOdd="tdcartOdd"));
				$module = fetchDbConfig($gatewayModules[$i]['folder']);
				$gateway->assign("VAL_GATEWAY_DESC",$module['desc']);
				$gateway->assign("VAL_GATEWAY_FOLDER",$gatewayModules[$i]['folder']);

				if ($gatewayModules[$i]['default'] == 1) {
					$gateway->assign("VAL_CHECKED","checked='checked'");
				} else {
					$gateway->assign("VAL_CHECKED","");
				}

				$gateway->parse("gateway.cart_true.choose_gate.gateways_true");

			}
	
			if (isset($basket['customer_comments'])) {
				$gateway->assign("VAL_CUSTOMER_COMMENTS",$basket['customer_comments']);
			}
			
			$gateway->parse("gateway.cart_true.choose_gate");
			
		} else {
			$gateway->assign("LANG_GATEWAYS_FALSE",$lang['front']['gateway']['none_configured']);
			$gateway->parse("gateway.cart_true.choose_gate.gateways_flase");
			$gateway->parse("gateway.cart_true.choose_gate");
		}

		$gateway->parse("gateway.cart_true");

	} else {
		$gateway->assign("CONTINUE_SHOPPING_BUTTON", "<p><div><a href='/' class='txtUpdate'>".$lang['front']['step1']['cont_shopping']."</a></div></p>");
		$gateway->assign("CONTINUE_SHOPPING_HEIGHT", "50px");
		$gateway->assign("LANG_CART_EMPTY","Your shopping cart is currently empty.");
		$gateway->parse("gateway.cart_false");
	} 

	$gateway->parse("gateway");
	$page_content = $gateway->text("gateway");
}

?>
	
