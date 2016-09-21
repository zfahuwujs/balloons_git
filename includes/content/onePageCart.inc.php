<?php
require_once("classes/newCart.php");
$cart = new basket();
$basket = $cart->getContents();

if(isset($_GET['removePromo'])){
	$basket = $cart->removeDiscount();
	header('location: /cart.php?act=onePage');
	exit;
}
if (isset($_POST['couponCode']) && $_POST['couponCode'] !="") {
	
	$basket = $cart->setDiscount($_POST['couponCode']);
	
	//header('location: /cart.php?act=onePage');
	//exit;
}


if(isset($_POST['gateway'])){
	$cart->setGateway($_POST['gateway']);
	//required fields array
	$fieldsDescrArray = array('firstName'=>'First Name','lastName'=>'Last Name','email'=>'Email Address','country'=>'Country','add_1'=>'Street Address 1','town'=>'City','postcode'=>'Postcode');
	//validate
	$invKeys = array_keys($_POST['delInf']);
	if(is_array($invKeys)){
		for($i = 0; $i < count($invKeys); $i++){
			if($invKeys[$i]!='phone'){
				if($invKeys[$i]=='email'){
					if(!validateEmail($_POST['invInf'][$invKeys[$i]])){
						$error[] = 'Please enter valid email address';
					}
				}else{
					if(in_array($invKeys[$i],array_keys($fieldsDescrArray))){
						if(empty($_POST['invInf'][$invKeys[$i]]) || !preg_match('/[a-z0-9]/i',$_POST['invInf'][$invKeys[$i]])){
							$error[] = 'Please enter your '.strtolower($fieldsDescrArray[$invKeys[$i]]).' in billing address';
						}
					}
				}
			}
		}
	}
	$delKeys = array_keys($_POST['delInf']);
	if(is_array($invKeys)){
		for($i = 0; $i < count($delKeys); $i++){
			if($delKeys[$i]!='phone'){
				if($delKeys[$i]=='email'){
					if(!validateEmail($_POST['delInf'][$delKeys[$i]])){
						$error[] = 'Please enter valid email address';
					}
				}else{
					if(in_array($delKeys[$i],array_keys($fieldsDescrArray))){
						if(empty($_POST['delInf'][$delKeys[$i]]) || !preg_match('/[a-z0-9]/i',$_POST['delInf'][$delKeys[$i]])){
							$error[] = 'Please enter your '.strtolower($fieldsDescrArray[$delKeys[$i]]).' in delivery address';
						}
					}
				}
			}
		}
	}
	
	if(isset($_POST['invInf']['password']) && !empty($_POST['invInf']['password'])){
		$password = $_POST['invInf']['password'];
		$confirmPassword = $_POST['confirmPassword'];
		if($password != $confirmPassword){
			$error[] = 'Password must match';
			
		}else{
			//header('location: /');
			$record["email"] = $db->mySQLSafe($_POST['invInf']['email']);
			$record["password"] = $db->mySQLSafe(md5($_POST['invInf']['password']));
			//$record["title"] = $db->mySQLSafe($_POST['title']);
			$record["firstName"] = $db->mySQLSafe($_POST['invInf']['firstName']);
			$record["lastName"] = $db->mySQLSafe($_POST['invInf']['lastName']);
			$record["add_1"] = $db->mySQLSafe($_POST['invInf']['add_1']);
			$record["add_2"] = $db->mySQLSafe($_POST['invInf']['add_2']);
			$record["town"] = $db->mySQLSafe($_POST['invInf']['town']);
			$record["county"] = $db->mySQLSafe($_POST['invInf']['county']);
			$record["postcode"] = $db->mySQLSafe($_POST['invInf']['postcode']);
			//$record["country"] = $db->mySQLSafe($countryID[0]['id']);
			$record["country"] = $db->mySQLSafe($_POST['invInf']['country']);
			$record["phone"] = $db->mySQLSafe($_POST['invInf']['phone']);
			$record["mobile"] = $db->mySQLSafe($_POST['delInf']['mobile']);	
			//$record["company"] = $db->mySQLSafe($_POST['company']);
			$record["regTime"] = $db->mySQLSafe(time());
			$record["ipAddress"] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
			$record["type"] = $db->mySQLSafe('1');
			
			
			// look up users zone

			$zoneId = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_iso_counties WHERE (abbrev LIKE ".$db->mySQLSafe($_POST['invInf']['county']).
																										" OR name LIKE ".$db->mySQLSafe($_POST['invInf']['county']).")");
			if($zoneId[0]['id']>0){
				$record["zoneId"] = $zoneId[0]['id'];
			}
			//check if that email exist
			$emailResult = $db->select("SELECT customer_id FROM ".$glob['dbprefix']."CubeCart_customer WHERE email =".$db->mySQLSafe($_POST['invInf']['email']));
			if(!empty($emailResult)){
			// update
				//var_dump(__LINE__);
				$error[] = 'User with that email already exist';
			} else {

				$insert = $db->insert($glob['dbprefix']."CubeCart_customer", $record);
				if($insert){
					$sessData['customer_id'] = $db->insertid();
				}
			// Seans mod to email admin about new user registration (controllable via general settings)
				if($insert && $config['notifyAdminRegistration']){
					//Trade Customer
					if(isset($_POST['tradeAccount']) && $_POST['tradeAccount'] == 1){
						$trade = "\n\nThis customer has requested a trade account.  Please log into your back office to grant access or not.";
					}
	
					include_once('classes/htmlMimeMail.php');
	
					$mail = new htmlMimeMail();
					$mail->setSubject('New Registration');
					$mail->setHeader('X-Mailer', 'Mailer');
					$mail->setFrom($config['masterEmail']);
					$mail->setReturnPath($returnPath);
					$mail->setText("A new user has registered with the following details:\n\nName: {$_POST['invInf']['firstName']} {$_POST['invInf']['lastName']}\nEmail: {$_POST['invInf']['email']}\nSignup IP: {$_SERVER['REMOTE_ADDR']} $trade");
					$result = $mail->send(array($config['masterEmail']), $config['mailMethod']);
				  }

			}

			$update = $db->update($glob['dbprefix']."CubeCart_sessions", $sessData,"sessId=".$db->mySQLSafe($_SESSION['ccUser']));
			
		}
	}
	
	if(isset($_POST['delInf'])) {		
		$basket = $cart->setDelAddr($_POST['delInf']);
	}
	if(isset($_POST['invInf'])) {		
		$basket = $cart->setInvAddr($_POST['invInf']);
	}
	if(isset($_POST['customer_comments'])){
		$cart->setVar($_POST['customer_comments'],'customer_comments');
	}

	$productList = $cart->productList();
	if(isset($productList) && is_array($productList) && count($productList) > 0){
		for($i = 0; $i < count($productList); $i++){
			if(!empty($productList[$i]['productOptions'])){
				$optionsArray = explode('|',$productList[$i]['productOptions']);
			}
		}
		//shipping modules
		//$shipCost = $cart->getShipping();
		/*if($shipCost==0){
			$shippingModules = $db->select("SELECT folder FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='shipping' AND status = 1");
			$noItems = $cart->noItems();
			$sum = 0;
			if($shippingModules == TRUE){
				$shipKey = $cart->getVar('shipKey');
				if (isset($_GET['s']) && $_GET['s'] == 1) {
					$basket = $cart->setVar(1,"shipKey");
				} elseif (isset($_POST['shipping']) && $_POST['shipping'] > 0) {
					$basket = $cart->setVar($_POST['shipping'], "shipKey");
				} elseif (!isset($shipKey)) {
					$basket = $cart->setVar(1, "shipKey");
				}
				$delCountry = $_POST['delInf']['country'];
				for ($i = 0; $i < count($shippingModules); $i++) {
					$shipKey++;
					include("modules/shipping/".$shippingModules[$i]['folder']."/calc.php"); 
				}
				if($shippingAvailable==false){
					$cart->removeShipping();
					header('location: /cart.php?act=noShip');
					exit;
				}
			}
		}*/
	}		
	
	
	if(!isset($error)){
		header('location: /cart.php?act=step5');
		exit;
	}
}else{

	$shippingModules = $db->select("SELECT folder FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='shipping' AND status = 1");
	$noItems = $cart->noItems();
	$sum = 0;
	if($shippingModules == TRUE){
		$shipKey = $cart->getVar('shipKey');
		if (isset($_GET['s']) && $_GET['s'] == 1) {
			$basket = $cart->setVar(1,"shipKey");
		} elseif (isset($_POST['shipping']) && $_POST['shipping'] > 0) {
			$basket = $cart->setVar($_POST['shipping'], "shipKey");
		} elseif (!isset($shipKey)) {
			$basket = $cart->setVar(1, "shipKey");
		}
		$delCountry = 225;
		for ($i = 0; $i < count($shippingModules); $i++) {
			$shipKey++;
			include("modules/shipping/".$shippingModules[$i]['folder']."/calc.php"); 
		}
	}
}



if (isset($_GET['remove'])) {
	$basket = $cart->removeItem($_GET['remove']);
	header('location: /cart.php?act=onePage');
	exit;
} elseif (isset($_POST['qty'])) {
	foreach ($_POST['qty'] as $key => $value) {
		$basket = $cart->update($key,$value);
	}
	header('location: /cart.php?act=onePage');
	exit;
} elseif (isset($_GET['mode']) && $_GET['mode'] == "emptyCart") {
	$basket = $cart->emptyCart();
	header('location: /cart.php?act=onePage');
	exit;
}
if(isset($_POST['username'])){

	$_POST['username'] = treatGet($_POST['username']);
	$_POST['password'] = treatGet($_POST['password']);

	if($config['usernameType']==1){
		$usernameType='nickname';
	}else{
		$usernameType='email';
	}
	$query = "SELECT customer_id FROM ".$glob['dbprefix']."CubeCart_customer WHERE ".$usernameType."=".$db->mySQLSafe($_POST['username'])." AND password = ".$db->mySQLSafe(md5($_POST['password']))." AND type>0";
	$customer = $db->select($query);
	$evoAccountBlock=0;
	if(($customer[0]['customer_id']==3 || $customer[0]['customer_id']==4) && evo==false){
		$evoAccountBlock=1;
	}
	if($customer==FALSE) {

		if($db->blocker($_POST['username'],$ini['bfattempts'],$ini['bftime'],FALSE,"f")==TRUE)
		{
			$blocked = TRUE; 	
		}
	} elseif($customer[0]['customer_id']>0 && $evoAccountBlock==0) {
		if($db->blocker($_POST['username'],$ini['bfattempts'],$ini['bftime'],TRUE,"f")==TRUE){
			$blocked = TRUE; 
		}else{
			$customerData["customer_id"] = $customer[0]['customer_id'];
			$update = $db->update($glob['dbprefix']."CubeCart_sessions", $customerData,"sessId=".$db->mySQLSafe($_SESSION['ccUser']));
			$_POST['remember'] = treatGet($_POST['remember']);
			if($_POST['remember']==1){
				setcookie("ccRemember","1",time()+$config['sqlSessionExpiry'], $GLOBALS['rootRel']);
			}
			//Set cookie to remember username
			$timeAppend = 60*60*24*365;
			$cookieExpiry = time()+$timeAppend;
			
			$customerName = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_customer WHERE customer_id = ".$db->mySQLSafe($customer[0]['customer_id']));
			if ($customerName[0]['firstName'] != '' && $customerName[0]['firstName'] != NULL) {
				if ($customerName[0]['lastName'] != '' && $customerName[0]['lastName'] != NULL) {
					$name = $customerName[0]['firstName'].' '.$customerName[0]['lastName'];
				} else $name = $customerName[0]['firstName'];
			} else if ($customerName[0]['lastName'] != '' && $customerName[0]['lastName'] != NULL) {
				if ($customerName[0]['title'] != '' && $customerName[0]['title'] != NULL) {
					$name = $customerName[0]['title'].' '.$customerName[0]['lastName'];
				}
			} else $name = $customerName[0]['email'];
			
			setcookie('returnVisitorName', $name, $cookieExpiry, '/');
			// redirect
			// "login","reg","unsubscribe","forgotPass"
			header('location: /cart.php?act=onePage');
			exit;
			
		}
	}elseif(eregi("step1",base64_decode($_GET['redir']))){
		header("Location: ".$GLOBALS['rootRel']."cart.php?act=step1");
		exit;	
	}
}
$view_cart = new XTemplate("skins/".$config['skinDir']."/styleTemplates/content/onePageCart.tpl");

$view_cart->assign("SHIP_PRICE",priceFormat($firstPrice,true));

if(isset($error) && is_array($error)){
	$errorHtml = implode('<br/>',$error);
	$view_cart->assign("REGERROR",$errorHtml);
}

$view_cart->assign("SESSIONID",$ccUserData[0]['sessId']);
	$invAddr = $cart->getInvAddr();
	if (isset($invAddr['invInf'])) {
		$view_cart->assign("VAL_INF_TITLE",stripslashes($invAddr['invInf']['title']));
		$view_cart->assign("VAL_INF_FIRST_NAME",stripslashes($invAddr['invInf']['firstName']));
		$view_cart->assign("VAL_INF_LAST_NAME",stripslashes($invAddr['invInf']['lastName']));
		$view_cart->assign("VAL_INF_ADD_1",stripslashes($invAddr['invInf']['add_1']));
		$view_cart->assign("VAL_INF_ADD_2",stripslashes($invAddr['invInf']['add_2']));
		$view_cart->assign("VAL_INF_TOWN",stripslashes($invAddr['invInf']['town']));
		$view_cart->assign("VAL_INF_COUNTY",stripslashes($invAddr['invInf']['county']));
		$view_cart->assign("VAL_INF_POSTCODE",stripslashes($invAddr['invInf']['postcode']));
		$view_cart->assign("VAL_INF_COUNTRY",stripslashes(countryName($invAddr['invInf']['country'])));
		$view_cart->assign("VAL_INF_TELEPHONE",stripslashes($invAddr['invInf']['telephone']));
		$view_cart->assign("VAL_INF_MOBILE",stripslashes($invAddr['delInf']['mobile']));
		$view_cart->assign("VAL_INF_EMAIL",stripslashes($invAddr['invInf']['email']));
	} elseif ($ccUserData[0]['customer_id'] > 0) {
		$view_cart->assign("VAL_INF_EMAIL",stripslashes($ccUserData[0]['email']));
		$view_cart->assign("VAL_INF_TITLE",stripslashes($ccUserData[0]['title']));
		$view_cart->assign("VAL_INF_FIRST_NAME",stripslashes($ccUserData[0]['firstName']));
		$view_cart->assign("VAL_INF_LAST_NAME",stripslashes($ccUserData[0]['lastName']));
		$view_cart->assign("VAL_INF_ADD_1",stripslashes($ccUserData[0]['add_1']));
		$view_cart->assign("VAL_INF_ADD_2",stripslashes($ccUserData[0]['add_2']));
		$view_cart->assign("VAL_INF_TOWN",stripslashes($ccUserData[0]['town']));
		$view_cart->assign("VAL_INF_COUNTY",stripslashes($ccUserData[0]['county']));
		$view_cart->assign("VAL_INF_POSTCODE",stripslashes($ccUserData[0]['postcode']));
		$view_cart->assign("VAL_INF_COUNTRY",stripslashes(countryName($ccUserData[0]['country'])));
		$view_cart->assign("VAL_INF_TELEPHONE",stripslashes($ccUserData[0]['phone']));
	}
		$delAddr = $cart->getDelAddr();
		$view_cart->assign("VAL_DEL_TITLE",stripslashes($delAddr['delInf']['title']));
		$view_cart->assign("VAL_DEL_FIRST_NAME",stripslashes($delAddr['delInf']['firstName']));
		$view_cart->assign("VAL_DEL_LAST_NAME",stripslashes($delAddr['delInf']['lastName']));
		$view_cart->assign("VAL_DEL_ADD_1",stripslashes($delAddr['delInf']['add_1']));
		$view_cart->assign("VAL_DEL_ADD_2",stripslashes($delAddr['delInf']['add_2']));
		$view_cart->assign("VAL_DEL_TOWN",stripslashes($delAddr['delInf']['town']));
		$view_cart->assign("VAL_DEL_COUNTY",stripslashes($delAddr['delInf']['county']));
		$view_cart->assign("VAL_DEL_POSTCODE",stripslashes($delAddr['delInf']['postcode']));
		$view_cart->assign("VAL_DEL_COUNTRY",stripslashes(countryName($delAddr['delInf']['country'])));

	
	$uk = $db->select("SELECT id, printable_name FROM ".$glob['dbprefix']."CubeCart_iso_countries WHERE id IN (225,240,241,242,243)");
	if($uk==true){
		for ($i=0; $i<count($uk); $i++) {
			if (($uk[$i]['id'] == $delAddr['delInf']['country']) || ($uk[$i]['id']==$ccUserData[0]['country'] && !isset($delAddr['delInf']['country']))) {
				$view_cart->assign("DEL_COUNTRY_SELECTED","selected='selected'");
			} else {
				$view_cart->assign("DEL_COUNTRY_SELECTED","");
			}
			if (($uk[$i]['id'] == $invAddr['invInf']['country']) || ($uk[$i]['id']==$ccUserData[0]['country'] && !isset($invAddr['invInf']['country']))) {
				$view_cart->assign("INV_COUNTRY_SELECTED","selected='selected'");
			} else {
				$view_cart->assign("INV_COUNTRY_SELECTED","");
			}
			$view_cart->assign("VAL_DEL_COUNTRY_ID",$uk[$i]['id']);
			$view_cart->assign("VAL_INV_COUNTRY_ID",$uk[$i]['id']);
			$countryName = "";
			$countryName = $uk[$i]['printable_name'];
			/*if (strlen($countryName) > 20) {
				$countryName = substr($countryName,0,20)."&hellip;";
			}*/
			$view_cart->assign("VAL_DEL_COUNTRY_NAME",$countryName);
			$view_cart->assign("VAL_INV_COUNTRY_NAME",$countryName);
			$view_cart->parse("view_cart.displayUK1.uk_opts1");
			$view_cart->parse("view_cart.displayUK2.uk_opts2");
		}
		$view_cart->parse("view_cart.displayUK1");
		$view_cart->parse("view_cart.displayUK2");
	}
	
	$countries = $db->select("SELECT id, printable_name FROM ".$glob['dbprefix']."CubeCart_iso_countries WHERE id NOT IN (225,240,241,242,243) ORDER BY printable_name"); 
	for ($i=0; $i<count($countries); $i++) {
		if (($countries[$i]['id'] == $delAddr['delInf']['country']) || ($countries[$i]['id']==$ccUserData[0]['country'] && !isset($delAddr['delInf']['country']))) {
			$view_cart->assign("DEL_COUNTRY_SELECTED","selected='selected'");
		} else {
			$view_cart->assign("DEL_COUNTRY_SELECTED","");
		}
		if (($countries[$i]['id'] == $invAddr['invInf']['country']) || ($countries[$i]['id']==$ccUserData[0]['country'] && !isset($invAddr['invInf']['country']))) {
			$view_cart->assign("INV_COUNTRY_SELECTED","selected='selected'");
		} else {
			$view_cart->assign("INV_COUNTRY_SELECTED","");
		}
		$view_cart->assign("VAL_DEL_COUNTRY_ID",$countries[$i]['id']);
		$view_cart->assign("VAL_INV_COUNTRY_ID",$countries[$i]['id']);
		$countryName = "";
		$countryName = $countries[$i]['printable_name'];
		/*if (strlen($countryName) > 20) {
			$countryName = substr($countryName,0,20)."&hellip;";
		}*/
		$view_cart->assign("VAL_DEL_COUNTRY_NAME",$countryName);
		$view_cart->assign("VAL_INV_COUNTRY_NAME",$countryName);
		$view_cart->parse("view_cart.country_opts1");
		$view_cart->parse("view_cart.country_opts2");
	}


if($ccUserData[0]['customer_id'] > 0 &&  isset($_POST['submit'])){
	$view_cart->assign("LOGIN_STATUS",$lang['front']['login']['login_success']);
} elseif($ccUserData[0]['customer_id'] > 0 &&  !isset($_POST['submit'])) {
	$view_cart->assign("LOGIN_STATUS",$lang['front']['login']['already_logged_in']);
} elseif($ccUserData[0]['customer_id'] == 0 && isset($_POST['submit'])) {
	if($blocked == TRUE){
		$view_cart->assign("LOGIN_STATUS",sprintf($lang['front']['login']['blocked'],sprintf("%.0f",$ini['bftime']/60)));
	}else{
		$view_cart->assign("LOGIN_STATUS",$lang['front']['login']['login_failed']);
	}
}

$view_cart->assign('PAGE_ACTION',curPageURL());

if($ccUserData[0]['customer_id'] < 1){
	$view_cart->parse("view_cart.login");
	$view_cart->parse("view_cart.createaccount");
}

//contents and calculate totals
$productList = $cart->productList();
if(isset($productList) && is_array($productList) && count($productList) > 0){
	for($i = 0; $i < count($productList); $i++){
		$view_cart->assign("KEY",$productList[$i]['id']);
		if(!empty($productList[$i]['productOptions'])){
			$optionsArray = explode('|',$productList[$i]['productOptions']);
			if(is_array($optionsArray) && !empty($optionsArray)){
				$view_cart->assign("DISPLAY_OPTIONS",$cart->displayOptions($productList[$i]['productId'],$optionsArray));
			}
		}
		/*if($cart->productData($productList[$i]['productId'],'maxOrder') > 0 && $cart->productData($productList[$i]['productId'],'maxOrder') < $productList[$i]['productQty']){
			$cart->update($productList[$i]['id'],$cart->productData($productList[$i]['productId'],'maxOrder'));
			$view_cart->assign("VAL_STOCK_WARN","ALL ITEMS ARE LIMITED TO 2 PER CUSTOMER UNLESS STATED OTHERWISE.");
			$view_cart->assign("QTY",$cart->productData($productList[$i]['productId'],'maxOrder'));
			$view_cart->assign("VAL_INSTOCK",$cart->getStock($productList[$i]['productId'],$optionsArray));
			$view_cart->parse("view_cart.order_summary.item.stock_warn");
		}else*/
		if($cart->getStock($productList[$i]['productId'],$optionsArray) < $productList[$i]['productQty'] && $config['outofstockPurchase']==1 && $cart->productData($productList[$i]['productId'],'useStockLevel')==1){
			$view_cart->assign("VAL_INSTOCK",$cart->getStock($productList[$i]['productId'],$optionsArray));
			$view_cart->assign("VAL_STOCK_WARN",$lang['front']['cart']['amount_capped']." ".$cart->getStock($productList[$i]['productId'],$optionsArray).".");
			$cart->update($productList[$i]['id'],$cart->getStock($productList[$i]['productId'],$optionsArray));
			$view_cart->parse("view_cart.order_summary.item.stock_warn");
			$view_cart->assign("QTY",$cart->getStock($productList[$i]['productId'],$optionsArray));
		}else{
			$view_cart->assign("VAL_INSTOCK","&infin;");
			$view_cart->assign("QTY",$productList[$i]['productQty']);
		}
		$view_cart->assign("PRODUCT_NAME",$cart->productData($productList[$i]['productId'],'name'));
		$view_cart->assign("PRODUCT_ID",$productList[$i]['productId']);
		
		
		if($ccUserData[0]['tax'] == 1){
			if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 1){
				$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
				$discount = $tradeDiscount[0]['discount']/100;
				$productList[$i]['productPrice'] = $productList[$i]['productPrice'] - ($productList[$i]['productPrice'] * $discount);
				$productList[$i]['totalPrice'] = $productList[$i]['totalPrice'] - ($productList[$i]['totalPrice'] * $discount);
				$view_cart->assign("PRICE",priceFormat($productList[$i]['productPrice']));
				$view_cart->assign("PRICE_TOTAL",priceFormat($productList[$i]['totalPrice']));
			}else{
				$view_cart->assign("PRICE",priceFormat($productList[$i]['productPrice']));
				$view_cart->assign("PRICE_TOTAL",priceFormat($productList[$i]['totalPrice']));
			}
			
		}
		if($ccUserData[0]['tax'] == 2){
			$vatProdPrice = getProductPriceWithTax($productList[$i]['productId'], $productList[$i]['productPrice']);
			$vatProdTotalPrice = getProductPriceWithTax($productList[$i]['productId'], $productList[$i]['totalPrice']);
			if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 1){
				$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
				$discount = $tradeDiscount[0]['discount']/100;
				$vatProdPrice = $vatProdPrice - ($vatProdPrice * $discount);
				$vatProdTotalPrice = $vatProdTotalPrice - ($vatProdTotalPrice * $discount);
				$view_cart->assign("PRICE_TOTAL",priceFormat($vatProdTotalPrice));
				$view_cart->assign("PRICE",priceFormat($vatProdPrice));
			}else{
				$view_cart->assign("PRICE_TOTAL",priceFormat($vatProdTotalPrice));
				$view_cart->assign("PRICE",priceFormat($vatProdPrice));
			}
			
			
			
			
			
		}
		
		
		
		
		
		
		
		
		$image = $cart->productData($productList[$i]['productId'],'image');
		if(!empty($image) && file_exists($_SERVER['DOCUMENT_ROOT'].'/images/uploads/'.$image)){
			$imgPath = 'images/uploads/'.$image;
		}else{
			$imgPath = 'skins/FixedSize/styleImages/nophoto.gif';
		}
		$view_cart->assign("SRC_PROD_THUMB",$imgPath);
		unset($image,$imgPath);
		$view_cart->parse("view_cart.order_summary.item");
	}
		
	if($cart->discountTotal() > 0){
		$view_cart->assign('SHOW_DISCOUNT_CODE',$cart->getDiscountCode());
		$view_cart->assign('DISCOUNT_AMOUNT',priceFormat($cart->discountTotal()));
		$view_cart->parse("view_cart.order_summary.discount");
	}
	
	$view_cart->assign('CART_TOTAL',priceFormat($cart->productTotal()));
	$view_cart->assign('TOTAL_UNFORMATED',$cart->productTotal());
	$view_cart->assign('GRAND_TOTAL',priceFormat($cart->grandTotal()));
	$view_cart->parse("view_cart.order_summary");
	//shipping modules
	$shippingModules = $db->select("SELECT folder FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='shipping' AND status = 1");
	$noItems = $cart->noItems();
	$sum = 0;
	if($shippingModules == TRUE){
		$shipKey = $cart->getVar('shipKey');
		if (isset($_GET['s']) && $_GET['s'] == 1) {
			$basket = $cart->setVar(1,"shipKey");
		} elseif (isset($_POST['shipping']) && $_POST['shipping'] > 0) {
			$basket = $cart->setVar($_POST['shipping'], "shipKey");
		} elseif (!isset($shipKey)) {
			$basket = $cart->setVar(1, "shipKey");
		}

		for ($i = 0; $i < count($shippingModules); $i++) {
			$shipKey++;
			include("modules/shipping/".$shippingModules[$i]['folder']."/calc.php"); 
		}
		$view_cart->assign("VAL_SHIPPING",$shippingPrice);
		$shipCost = $cart->getShipping();
		if(isset($shipCost)){
			$view_cart->assign("CURRENT_SHIPPING",'changeShipping(\''.$shipCost.'\');');
		}
	}
	
	//payment modules
	$gatewayModules = $db->select("SELECT folder, `default` FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='gateway' AND status = 1");
	if ($gatewayModules == TRUE) {
		$view_cart->assign("LANG_CHOOSE_GATEWAY",$lang['front']['gateway']['choose_method']);
		$view_cart->assign("LANG_COMMENTS",$lang['front']['gateway']['your_comments']);

		for ($i=0; $i<count($gatewayModules); $i++) {

			$view_cart->assign("TD_CART_CLASS",cellColor($i, $tdEven="tdcartEven", $tdOdd="tdcartOdd"));
			$module = fetchDbConfig($gatewayModules[$i]['folder']);
			$view_cart->assign("VAL_GATEWAY_DESC",$module['desc']);
			$view_cart->assign("VAL_GATEWAY_FOLDER",$gatewayModules[$i]['folder']);
			
			if ($gatewayModules[$i]['default'] == 1) {
				$view_cart->assign("VAL_CHECKED","checked='checked'");
			} else {
				$view_cart->assign("VAL_CHECKED","");
			}
			$view_cart->parse("view_cart.choose_gate.gateways_true");
		}
		$comments = $cart->getVar('customer_comments');
		if (isset($comments)) {
			$view_cart->assign("VAL_CUSTOMER_COMMENTS",$comments);
		} 

		$view_cart->parse("view_cart.choose_gate");
	}
	$view_cart->parse("view_cart.discount_Manager");
	
}else{
	$view_cart->parse("view_cart.empty");
}
$checkOutPages = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_docs WHERE inCHeckout = 1 ORDER BY doc_pos');
if($checkOutPages==true){
	for($i = 0; $i < count($checkOutPages); $i++){
		$view_cart->assign("TXT_TITLE",$checkOutPages[$i]['doc_name']);
		$view_cart->assign("TXT_DESCRIPTION",$checkOutPages[$i]['doc_content']);
		$view_cart->parse("view_cart.tabs.title");
		$view_cart->parse("view_cart.tabs.contents");
	}
	$view_cart->parse("view_cart.tabs");
}
$view_cart->parse("view_cart");
$page_content = $view_cart->text("view_cart");
?>