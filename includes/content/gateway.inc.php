<?php
require_once("classes/newCart.php");
$cart = new basket();
$basket = $cart->getContents();

//$cart->setVar(0,'mailSent');

if($cart->validateBasket()!=true){
	echo 'Error Creating Order!';
	echo '<hr/>';
	echo '<a href="/cart.php?act=onePage">Please try again</a>';
	ob_start();
	echo 'Error Creating Order!';
	echo '<pre>';
	var_dump($basket);
	echo '</pre>';
	$data = ob_get_contents();
	ob_end_clean();
	$data .= "\r\n".'-----------------------------------------------------------------------------------------------------------'."\r\n";
	$line = "\r\n".'-----------------------------------------------------------------------------------------------------------'."\r\n";
	$data .= $_SERVER['HTTP_USER_AGENT']."\r\n";
	$data .= $_SERVER['REMOTE_ADDR']."\r\n";
	$fileName = $_SERVER['DOCUMENT_ROOT'].'/order_error/data-'.time().'.txt';
	$fp = fopen($fileName, 'w+');
	fwrite($fp,$data);
	fclose($fp);
	exit;
}else{

	$gateway = $cart->getGateway();
	
	if(isset($gateway) && !empty($gateway)){
		$_POST['gateway'] = $gateway;
	}
	
	$gateway = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/gateway.tpl");
	$gateway->assign("VAL_SKIN",$config['skinDir']);
	$gateway->assign("LANG_PAYMENT",$lang['front']['gateway']['payment']);
	$gateway->assign("LANG_CART",$lang['front']['gateway']['cart']);
	$gateway->assign("LANG_ADDRESS",$lang['front']['gateway']['address']);
	$gateway->assign("LANG_PAYMENT",$lang['front']['gateway']['payment']);
	$gateway->assign("LANG_COMPLETE",$lang['front']['gateway']['complete']);
	
	// sanitise gateway variable
	if ($basket == TRUE && isset($_POST['gateway']) && !preg_match("[^0-9a-z_-]",$_POST['gateway'])) {
		$gateway->assign("CONTINUE_SHOPPING_BUTTON", "");
		$gateway->assign("CONTINUE_SHOPPING_HEIGHT", "25px");	
		$cart->setGateway($_POST['gateway']);
		// build order number
		$cart_order_id = $cart->getOrderId();
		if (!isset($cart_order_id) || (isset($cart_order_id) && empty($cart_order_id))){
			$cart_order_id = date("ymd-His-").rand(1000,9999);
			$cart->setOrderId($cart_order_id,"cart_order_id");
			$cart->setVar(0,"mailSent");
			$record['noOrders'] = "noOrders + 1";
			if ($ccUserData[0]['customer_id'] > 0) {
				$where = "customer_id = ".$ccUserData[0]['customer_id'];
				$update = $db->update($glob['dbprefix']."CubeCart_customer", $record, $where);
			}
			
		} else {
			$cart_order_id = $cart->getOrderId();
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
		$productList = $cart->productList();
		for ($i=0; $i<count($productList); $i++) {
			$orderInv['cart_order_id'] = $db->mySQLSafe($cart_order_id);
			$orderInv['productId'] = $db->mySQLSafe($productList[$i]['productId']);
			$orderInv['name'] = $db->mySQLSafe($cart->productData($productList[$i]['productId'],'name'));
			$orderInv['price'] = $db->mySQLSafe($productList[$i]['totalPrice']);
			$orderInv['quantity'] = $db->mySQLSafe($productList[$i]['productQty']);
			if(!empty($productList[$i]['productOptions'])){
				$optionsArray = explode('|',$productList[$i]['productOptions']);
				$options = $cart->displayOptions($productList[$i]['productId'],$optionsArray);
			}else{
				unset($options,$optionsArray);
			}
			$orderInv['product_options'] = $db->mySQLSafe($options);
			$orderInv['productCode'] = $db->mySQLSafe($cart->productData($productList[$i]['productId'],'productCode'));
			$orderInv['digital'] = $db->mySQLSafe($cart->productData($productList[$i]['productId'],'digital'));
			$orderInv['optionKeys'] = $db->mySQLSafe($productList[$i]['productOptions']);

			$insert = $db->insert($glob['dbprefix']."CubeCart_order_inv", $orderInv);
			
			// start mod: Customer Order Confirmation Email - http://cubecart.expandingbrain.com
			include("includes/estelles_mod_store/customer_order_conf_email.gateway1.inc.php");
			// end mod: Customer Order Confirmation Email - by Estelle
	
			##################################################################################
			## Admin E-Mail Fix by Sir William -- http://www.swscripts.com/
			// build admin email product list
	
			//if($cart->getVar('mailSent')==0){ // send only if not sent already for current order number
				$prodtext .= sprintf($lang['front']['gateway']['admin_email_body_4'],
							$cart->productData($productList[$i]['productId'],'name'));
	
				if(!empty($productList[$i]['productOptions'])){
				$prodtext .= sprintf($lang['front']['gateway']['admin_email_body_5'],
							str_replace(array("\r","\n")," ",$options));
				}
				$prodtext .= sprintf($lang['front']['gateway']['admin_email_body_6'],
	
							$productList[$i]['productQty'],
							$cart->productData($productList[$i]['productId'],'productCode'),
							priceFormat($productList[$i]['totalPrice']));
	
			//}
	
			##################################################################################
	
			foreach ($orderInv as $key => $value){
				$orderInv[$key] = str_replace("'","",$value);
			}
	
			$transVars .= repeatVars();
	
			if($cart->productData($productList[$i]['productId'],'digital')==1){
				$digitalProduct['cart_order_id'] = $db->mySQLSafe($cart_order_id);
				$digitalProduct['customerId'] = $db->mySQLSafe($ccUserData[0]['customer_id']);
				$digitalProduct['expire'] = $db->mySQLSafe(time()+$config['dnLoadExpire']);
				$digitalProduct['productId'] = $db->mySQLSafe($productList[$i]['productId']);
				$digitalProduct['accessKey'] = $db->mySQLSafe(randomPass());
				$insert = $db->insert($glob['dbprefix']."CubeCart_Downloads", $digitalProduct);
			}
			unset($orderInv);
		}
	
		if($insert==FALSE) {
			echo "An error building the order inventory was encountered. Please inform a member of staff.";
			exit;
		}
	
		// insert order summary
	
		//////////////////
		// Invoice info
		/////
		$invoiceAddr = $cart->getInvAddr();
		$orderSum['cart_order_id'] = $db->mySQLSafe($cart_order_id);
		if($ccUserData[0]['customer_id'] > 0){
			$orderSum['customer_id'] = $db->mySQLSafe($ccUserData[0]['customer_id']);
		}else{
			$orderSum['customer_id'] = $db->mySQLSafe(0);
		}
		$orderSum['email'] = $db->mySQLSafe($invoiceAddr['invInf']['email']);
		$orderSum['name'] = $db->mySQLSafe($invoiceAddr['invInf']['title']." ".$invoiceAddr['invInf']['firstName']." ".$invoiceAddr['invInf']['lastName']); 
		$orderSum['add_1'] = $db->mySQLSafe($invoiceAddr['invInf']['add_1']);
		$orderSum['add_2'] = $db->mySQLSafe($invoiceAddr['invInf']['add_2']);
		$orderSum['town'] = $db->mySQLSafe($invoiceAddr['invInf']['town']);
		$orderSum['county'] = $db->mySQLSafe($invoiceAddr['invInf']['county']);
		$orderSum['postcode'] = $db->mySQLSafe($invoiceAddr['invInf']['postcode']);
		$orderSum['country'] = $db->mySQLSafe(countryName($invoiceAddr['invInf']['country']));
		$orderSum['phone'] = $db->mySQLSafe($invoiceAddr['invInf']['phone']);
		$orderSum['mobile'] = $db->mySQLSafe($invoiceAddr['delInf']['mobile']);
		$orderSum['clientBrowser'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$couponCode = $cart->getDiscountCode();
		if($couponCode){
			$orderSum['couponCode'] = $db->mySQLSafe($couponCode); 
		}
		$discountTotal = $cart->discountTotal();
		if($discountTotal){
			$orderSum['discountTot'] = $db->mySQLSafe($discountTotal); 
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
		$delInf = $cart->getDelAddr();
		$orderSum['name_d'] = $db->mySQLSafe($delInf['delInf']['title']." ".$delInf['delInf']['firstName']." ".$delInf['delInf']['lastName']); 
		$orderSum['add_1_d'] = $db->mySQLSafe($delInf['delInf']['add_1']);
		$orderSum['add_2_d'] = $db->mySQLSafe($delInf['delInf']['add_2']);
		$orderSum['town_d'] = $db->mySQLSafe($delInf['delInf']['town']);
		$orderSum['county_d'] = $db->mySQLSafe($delInf['delInf']['county']);
		$orderSum['postcode_d'] = $db->mySQLSafe($delInf['delInf']['postcode']);
		$orderSum['country_d'] = $db->mySQLSafe(countryName($delInf['delInf']['country']));
	
		//////////////////
		// Summary
		/////
	
		$orderSum['subtotal'] = $db->mySQLSafe($cart->productTotal());
		$orderSum['total_ship'] = $db->mySQLSafe($cart->getShipping());	
		$orderSum['total_tax'] = $db->mySQLSafe($cart->taxTotal());
		$orderSum['prod_total'] = $db->mySQLSafe($cart->grandTotal());
		$orderSum['shipMethod'] = $db->mySQLSafe($cart->getShipMethod()); 
		if(!empty($sec_order_id)){
			$orderSum['sec_order_id'] = $db->mySQLSafe($sec_order_id);
		}
		$orderSum['ip'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
		$orderSum['time'] = $db->mySQLSafe(time());
		$orderSum['customer_comments'] = $db->mySQLSafe($cart->getVar('customer_comments'));
		
		$cart->setVar($_POST['customer_comments'],"customer_comments");
	
		$orderSum['gateway'] = $db->mySQLSafe($_POST['gateway']);
	
		$insert = $db->insert($glob['dbprefix']."CubeCart_order_sum", $orderSum);
	
		if($insert==FALSE) {
	
			echo "An error building the order summary was encountered. Please inform a member of staff.";
	
			exit;
	
		}elseif(empty($invoiceAddr['invInf']['title']) && empty($invoiceAddr['invInf']['firstName']) && empty($invoiceAddr['invInf']['lastName']) && empty($invoiceAddr['invInf']['add_1']) && empty($invoiceAddr['invInf']['email']) && empty($invoiceAddr['invInf']['country']) && empty($invoiceAddr['invInf']['town'])){
			if(!empty($cart_order_id)){
				echo "An error building the order summary was encountered. Please inform a member of staff.<br/>Your order id is: ".$cart_order_id;
			}else{
				echo "An error building the order summary was encountered. Please inform a member of staff.";
			}
	
			exit;
		}
	
		unset($orderSum);
	
		##################################################################################
	
		## Admin E-Mail Fix by Sir William -- http://www.swscripts.com/
	
		// notify shop owner of new order
	
		
		//$basket['mailSent'] = 0;		IF YOU WANT TO SEND IT FEW TIMES TO SAME ADDRESS
		
		
		if($cart->getVar('mailSent')==0){ // send only if not sent already for current order number
			include_once("classes/htmlMimeMail.php");
			$mail = new htmlMimeMail();
			if($cart->getShipping()>0){
				$emailShipCost = $cart->getShipping();
			} else {
				$emailShipCost = "0.00";
			}
			$text = sprintf($lang['front']['gateway']['admin_email_body_1'],
						$cart_order_id,
						formatTime(time()),
						$invoiceAddr['invInf']['title']." ".$invoiceAddr['invInf']['firstName']." ".$invoiceAddr['invInf']['lastName'],
						$invoiceAddr['invInf']['email'],
						priceFormat($cart->productTotal(),true),
						priceFormat($cart->discountTotal(),true),
						priceFormat($emailShipCost,true),
						priceFormat($cart->taxTotal(),true),
						priceFormat($cart->grandTotal(),true),
						$invoiceAddr['invInf']['title']." ".$invoiceAddr['invInf']['firstName']." ".$invoiceAddr['invInf']['lastName'],
						$invoiceAddr['invInf']['add_1'],
						$invoiceAddr['invInf']['add_2'],
						$invoiceAddr['invInf']['town'],
						$invoiceAddr['invInf']['county'],
						$invoiceAddr['invInf']['postcode'],
						countryName($ccUserData[0]['country']),
						$invoiceAddr['invInf']['phone'],
						$delInf['delInf']['title']." ".$delInf['delInf']['firstName']." ".$delInf['delInf']['lastName'],
						$delInf['delInf']['add_1'],
						$delInf['delInf']['add_2'],
						$delInf['delInf']['town'],
						$delInf['delInf']['county'],
						$delInf['delInf']['postcode'],
						countryName($delInf['delInf']['country']),
						$delInf['delInf']['phone'],
						str_replace("_"," ",$_POST['gateway']),
						str_replace("_"," ",$cart->getShipMethod()));
			if(!empty($_POST['customer_comments'])){
				$text .= sprintf($lang['front']['gateway']['admin_email_body_2'],
							$_POST['customer_comments']);
			}
			$text .= $lang['front']['gateway']['admin_email_body_3'];
			$text .= $prodtext;
			$mail->setHtml(nl2br($text));
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
				$gateway->assign("VAL_CUSTOMER_COMMENTS",$cart->getVar('customer_comments'));
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
	
