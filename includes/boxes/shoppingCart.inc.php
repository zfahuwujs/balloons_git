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
|	shoppingCart.inc.php
|   ========================================
|	Shopping Cart Box	
+--------------------------------------------------------------------------
*/

phpExtension();

/* <rf> search engine friendly mods */
if(user_is_search_engine() == false || $config['sef'] == 0) {

$box_content=new XTemplate("skins/".$config['skinDir']."/styleTemplates/boxes/shoppingCart.tpl");

/*if(!evo()){
$box_content->assign("LANG_SHOPPING_CART_TITLE",$lang['front']['boxes']['shopping_cart']);
$box_content->assign("VAL_SKIN",$config['skinDir']);

require_once("classes/cart.php");
$cart = new cart();
$basket = $cart->cartContents($ccUserData[0]['basket']);

if(isset($_POST['add']) && $_POST['add']>0) {

if(!isset($_POST['productOptions'])){
	// check product options are selected if they are required
	$prodOpts = $db->select("SELECT count(product) as noOpts FROM ".$glob['dbprefix']."CubeCart_options_bot WHERE product=".$db->mySQLSafe($_POST['add']));
	// if they are required redirect to product view page
	if($prodOpts[0]['noOpts'] >0){

	header("Location: /index.php?act=viewProd&productId=".$_POST['add']);
	exit;
	
	}

}

// add product to the cart
	if($_POST['quan']>0){
		$quantity = $_POST['quan'];
	} else {
		$quantity = 1;
	}
	
	if(isset($_POST['productOptions'])){
	
		$basket = $cart->add($_POST['add'],$quantity,$_POST['productOptions']);
	
	} else {
	
		$basket = $cart->add($_POST['add'],$quantity,"");
	
	}
	
	// start mod: Redirect to Basket, by Estelle - http://cubecart.expandingbrain.com
	if ($ccUserData[0]['customer_id'] > 0) {
		header("Location: ".$glob['rootRel']."cart.php?act=step2");
		exit;
	} else {
		header("Location: ".$glob['rootRel']."cart.php?act=cart");
		exit;
	}
	// end mod: Redirect to Basket

	// prevents refresh adding extras to the basket
	header("/Location: index.php?act=viewProd&productId=".$_POST['add']);
	exit;
	
}

$cartTotal = "";

if(is_array($basket['conts']) && !empty($basket['conts'])) {
	
	foreach($basket['conts'] as $key => $value){
		
		$productId = $cart->getProductId($key);
		
		// get product details
		$product = $db->select("SELECT name, price, sale_price, tradePrice, tradeSale, productId FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId=".$db->mySQLSafe($productId));
		
		if(($val = prodAltLang($product[0]['productId'])) == TRUE){
			
			$product[0]['name'] = $val['name'];
		
		}
		
		// build the product options
		$optionKeys = $cart->getOptions($key);
		
		$optionsCost = 0;
		
		if(!empty($optionKeys)){
		
			$options = explode("|",$optionKeys);
			
			
			
			foreach($options as $value)
			{
				// look up options in database
				$option = $db->select("SELECT ".$glob['dbprefix']."CubeCart_options_bot.option_id, ".$glob['dbprefix']."CubeCart_options_bot.value_id, option_price, option_symbol, assign_id FROM `".$glob['dbprefix']."CubeCart_options_bot` INNER JOIN `".$glob['dbprefix']."CubeCart_options_mid` ON ".$glob['dbprefix']."CubeCart_options_mid.value_id = ".$glob['dbprefix']."CubeCart_options_bot.value_id INNER JOIN `".$glob['dbprefix']."CubeCart_options_top` ON ".$glob['dbprefix']."CubeCart_options_bot.option_id = ".$glob['dbprefix']."CubeCart_options_top.option_id WHERE assign_id = ".$value);
				
				if($option[0]['option_price']>0){ 
					
					if($option[0]['option_symbol']=="+"){
				
						$optionsCost = $optionsCost + $option[0]['option_price'];
			
					} elseif($option[0]['option_symbol']=="-"){
			
						$optionsCost = $optionsCost - $option[0]['option_price'];
			
					}
					
				}

			}
			
		}
		
		
		if(salePrice($product[0]['price'], $product[0]['sale_price'])==FALSE){
			//Trade Customer check
			if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
				$price = $product[0]['tradePrice'];
			}else{
				$price = $product[0]['price'];
			}
		} else {
			//Trade Customer check
			if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
				$price = salePrice($product[0]['tradePrice'], $product[0]['tradeSale']);
			}else{
				$price = salePrice($product[0]['price'], $product[0]['sale_price']);
			}
		
		}
		
		$price = $price + ($optionsCost);
		
		$box_content->assign("PRODUCT_PRICE",priceFormat($price));
		$box_content->assign("VAL_NO_PRODUCT",$cart->cartArray['conts'][$key]["quantity"]);
		$box_content->assign("PRODUCT_ID",$productId);
		
		// chop name if too long
		if(strlen($product[0]['name']) > 15){
			$product[0]['name'] = substr($product[0]['name'],0,15)."..";
		}
		
		$box_content->assign("VAL_PRODUCT_NAME",validHTML($product[0]['name']));
		$box_content->parse("shopping_cart.contents_true");
		$cartTotal = $cartTotal + ($price * $cart->cartArray['conts'][$key]["quantity"]);
	}
	
} else {

	$box_content->assign("LANG_CART_EMPTY",$lang['front']['boxes']['basket_empty']);
	$box_content->parse("shopping_cart.contents_false");
	
}
$box_content->assign("VAL_CART_ITEMS",$cart->noItems());
$box_content->assign("LANG_ITEMS_IN_CART",$lang['front']['boxes']['items_in_cart']);
if(isset($cartTotal) && $cartTotal>0){
	$box_content->assign("VAL_CART_TOTAL",priceFormat($cartTotal));
} else {
	$box_content->assign("VAL_CART_TOTAL",priceFormat(0.00, TRUE));
}
$box_content->assign("LANG_TOTAL_CART_PRICE",$lang['front']['boxes']['total']);
$box_content->assign("LANG_VIEW_CART",$lang['front']['boxes']['view_basket']);

if($ccUserData[0]['customer_id']>0){
	$box_content->assign("CART_STEP","step2");
} else {
	$box_content->assign("CART_STEP","cart");
}

// correct plural
if($cart->noItems() == 0 || $cart->noItems() > 1){
	$box_content->assign("ITEMS","Items");
}else if($cart->noItems()  ){
	$box_content->assign("ITEMS","Item");
}

}else{*/
	//one page cart
	
	$box_content->assign("LANG_SHOPPING_CART_TITLE",$lang['front']['boxes']['shopping_cart']);
$box_content->assign("VAL_SKIN",$config['skinDir']);

require_once("classes/newCart.php");
$cart = new basket();
$basket = $cart->getContents();

if(isset($_POST['add']) && $_POST['add']>0){
	if(isset($_POST['optyQty'])){
		$optionKeys = array_keys($_POST['optyQty']);
		if(is_array($optionKeys)){
			for($i = 0; $i < count($optionKeys); $i++){
				if($_POST['optyQty'][$optionKeys[$i]] > 0){
					$basket = $cart->add($_POST['add'],$_POST['optyQty'][$optionKeys[$i]],$optionKeys[$i]);
				}
			}
		}
	}else{
		if(!isset($_POST['productOptions'])){
			// check product options are selected if they are required
			$prodOpts = $db->select("SELECT count(product) as noOpts FROM ".$glob['dbprefix']."CubeCart_options_bot WHERE product=".$db->mySQLSafe($_POST['add']));
			// if they are required redirect to product view page
			if($prodOpts[0]['noOpts'] >0){
				header("Location: /index.php?act=viewProd&productId=".$_POST['add']);
				exit;
			}
		}
	
		$basket = $cart->add($_POST['add'],$_POST['quan'],$_POST['productOptions']);
	}
	//header("location: /index.php?act=viewProd&productId=".$_POST['add']."&added=true");
	//exit;
	$box_content->assign('JQUERYSTUFF',"
	<script>
	$(document).ready(function(){
		$('.itemAdded').show('slow');
		$('.itemAdded').delay(10000).hide('slow');
	});
	function closeNotification(){
		$('.itemAdded').hide();
	}
	</script>
	");
}else{
	$box_content->assign('STYLE','style="display:none"');
}
$box_content->assign('SESSIONID','&amp;ccUser='.$ccUserData[0]['sessId']);
$cartTotal = "";

if($cart->noItems() != 1){
	$box_content->assign("S",'s');
}else{
	$box_content->assign("S",null);
}
$box_content->assign("VAL_CART_ITEMS",$cart->noItems());
$box_content->assign("LANG_ITEMS_IN_CART",$lang['front']['boxes']['items_in_cart']);
$cartTotal = $cart->productTotal();
if(isset($cartTotal) && $cartTotal>0){
	$box_content->assign("VAL_CART_TOTAL",priceFormat($cartTotal));
} else {
	$box_content->assign("VAL_CART_TOTAL",priceFormat(0.00, TRUE));
}
$box_content->assign("LANG_TOTAL_CART_PRICE",$lang['front']['boxes']['total']);
$box_content->assign("LANG_VIEW_CART",$lang['front']['boxes']['view_basket']);

if($ccUserData[0]['customer_id']>0){
	$box_content->assign("CART_STEP","step2");
} else {
	$box_content->assign("CART_STEP","cart");
}
if(preg_match('/cart.php/i',$_SERVER['PHP_SELF'])){
	$box_content->assign('LINK','<a href="/"><img src="/skins/Pullumsports/styleImages/backgrounds/conShopping.png" alt="Checkout" width="139" height="30" border="0" /></a>');
}else{
	$box_content->assign('LINK','<a href="/cart.php?act=onePage"><img src="/skins/Pullumsports/styleImages/backgrounds/checkout.png" alt="Checkout" width="139" height="30" border="0" /></a>');
	$cart_order_id = $cart->getOrderId();
	if((int)$cart_order_id > 0 && basename($_SERVER['PHP_SELF'])!='confirmed.php'){
		$status = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_order_sum WHERE cart_order_id = '.$db->mySQLSafe($cart_order_id));
		if($status==true){
			if($status[0]['status'] > 1){
				$cart->emptyCart();
			}
		}
	}
}
	

$box_content->parse("shopping_cart");

$box_content = $box_content->text("shopping_cart");

} else {
	$box_content = null;
}
/* <rf> end mod */

?>
