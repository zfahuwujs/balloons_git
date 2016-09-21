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

|	viewOrder.inc.php

|   ========================================

|	Displays the Customers Specific Order

+--------------------------------------------------------------------------

*/



phpExtension();



	$view_order=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/viewOrder.tpl");



	$view_order->assign("LANG_YOUR_VIEW_ORDER",$lang['front']['viewOrder']['order_no']." ".treatGet($_GET['cart_order_id']));



	$order = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_order_sum INNER JOIN ".$glob['dbprefix']."CubeCart_customer ON ".$glob['dbprefix']."CubeCart_order_sum.customer_id = ".$glob['dbprefix']."CubeCart_customer.customer_id WHERE ".$glob['dbprefix']."CubeCart_order_sum.cart_order_id = ".$db->mySQLSafe($_GET['cart_order_id'])." AND ".$glob['dbprefix']."CubeCart_order_sum.customer_id=".$db->mySQLsafe($ccUserData[0]['customer_id']));

	

	if($order == TRUE){
		
		if(isset($_GET['addToBasket']) && $_GET['addToBasket'] == 1){
			require_once("classes/newCart.php");
			$cart = new basket();
			$products = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_order_inv WHERE cart_order_id = ".$db->mySQLSafe($_GET['cart_order_id']));
			foreach($products as $product){
				//check if its still available
				
				$prodCheck = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = ".$db->mySQLSafe($product['productId']));
				if($prodCheck == TRUE){
					//var_dump($prodCheck);
					if($prodCheck[0]['showProd']==1){
						
						if($product['optionKeys']!=''){
							$stock = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_options_stock WHERE options='.$db->mySQLSafe($product['optionKeys']));
							if($stock && $stock[0]['stock'] >= $product['quantity']){
								$cart->add($product['productId'],$product['quantity'], $product['optionKeys']);	
							}elseif($stock && $stock[0]['stock'] > 0){
								$cart->add($product['productId'],$stock[0]['stock'], $product['optionKeys']);	
							}
						}else{
							if($prodCheck[0]['useStockLevel']==1){
								if($prodCheck[0]['stock_level'] >= $product['quantity']){
									$cart->add($product['productId'],$product['quantity']);	
								}
							}else{
								$cart->add($product['productId'],$product['quantity']);	
							}
						}
					}
				
				}
			}
		}
		
		

		$view_order->assign("LANG_CUSTOMER_INFO",$lang['front']['viewOrder']['customer_info']);

		$view_order->assign("ORDID",$order[0]['cart_order_id']);

		$view_order->assign("LANG_INVOICE_ADDRESS",$lang['front']['viewOrder']['invoice_address']);

		$view_order->assign("VAL_INVOICE_NAME",$order[0]['name']);

	  	$view_order->assign("VAL_INVOICE_ADD_1",$order[0]['add_1']);

	  	$view_order->assign("VAL_INVOICE_ADD_2",$order[0]['add_2']);

	  	$view_order->assign("VAL_INVOICE_TOWN",$order[0]['town']);

	 	$view_order->assign("VAL_INVOICE_POSTCODE",$order[0]['postcode']);

	  	$view_order->assign("VAL_INVOICE_COUNTRY",countryName($order[0]['country']));

		

		

		$view_order->assign("LANG_DELIVERY_ADDRESS",$lang['front']['viewOrder']['delivery_address']);

		$view_order->assign("VAL_DELIVERY_NAME",$order[0]['name_d']);

	  	$view_order->assign("VAL_DELIVERY_ADD_1",$order[0]['add_1_d']);

	  	$view_order->assign("VAL_DELIVERY_ADD_2",$order[0]['add_2_d']);

	  	$view_order->assign("VAL_DELIVERY_TOWN",$order[0]['town_d']);

	 	$view_order->assign("VAL_DELIVERY_POSTCODE",$order[0]['postcode_d']);

	  	$view_order->assign("VAL_DELIVERY_COUNTRY",$order[0]['country_d']);

		

		

		if(empty($order[0]['customer_comments'])){

			$view_order->assign("VAL_CUSTOMER_COMMENTS",$lang['front']['viewOrder']['na']);

		} else {

			$view_order->assign("VAL_CUSTOMER_COMMENTS",$order[0]['customer_comments']);

		}

		$view_order->assign("LANG_CUSTOMER_COMMENTS",$lang['front']['viewOrder']['customer_comments']);

		$view_order->assign("LANG_ORDER_SUMMARY",$lang['front']['viewOrder']['order_summary']);

		

		$view_order->assign("LANG_PRODUCT",$lang['front']['viewOrder']['product']);

		$view_order->assign("LANG_PRODUCT_CODE",$lang['front']['viewOrder']['product_code']);

		$view_order->assign("LANG_QUANTITY",$lang['front']['viewOrder']['quantity']);

		$view_order->assign("LANG_PRICE",$lang['front']['viewOrder']['price']);

		

		$products = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_order_inv WHERE cart_order_id = ".$db->mySQLSafe($_GET['cart_order_id']));

		

		for($i=0;$i<count($products); $i++){

		

		

			if($products[$i]['digital']==1 && ($order[0]['status']>=2 && $order[0]['status']<=3) ){

				// get digital info 

				$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_Downloads INNER JOIN ".$glob['dbprefix']."CubeCart_inventory ON ".$glob['dbprefix']."CubeCart_Downloads.productId =  ".$glob['dbprefix']."CubeCart_inventory.productId WHERE cart_order_id = ".$db->mySQLSafe($_GET['cart_order_id'])." AND ".$glob['dbprefix']."CubeCart_Downloads.productId = ".$db->mySQLSafe($products[$i]['productId']);

			

				$download = $db->select($query);

				$view_order->assign("VAL_DOWNLOAD_LINK",$glob['storeURL']."/download.php?pid=".$download[0]['productId']."&oid=".base64_encode($_GET['cart_order_id'])."&ak=".$download[0]['accessKey']);

				$view_order->assign("LANG_DOWNLOAD_LINK",$lang['front']['viewOrder']['download_here']);

				$view_order->parse("view_order.session_true.order_true.repeat_products.digital_link");

		

			}



		

			$view_order->assign("TD_CLASS","");

			$view_order->assign("VAL_PRODUCT",$products[$i]['name']);

			$view_order->assign("VAL_PRODUCT_OPTS",$products[$i]['product_options']);

			$view_order->assign("VAL_IND_QUANTITY",$products[$i]['quantity']);

			$view_order->assign("VAL_IND_PROD_CODE",$products[$i]['productCode']);

			$view_order->assign("VAL_IND_PRICE",priceFormat($products[$i]['price']));

			$view_order->parse("view_order.session_true.order_true.repeat_products");

			

		}

		

		

		$view_order->assign("LANG_ORDER_LIST",$lang['front']['viewOrder']['review_below']); 

		

		$view_order->assign("LANG_ORDER_TIME",$lang['front']['viewOrder']['order_date_time']);

		$view_order->assign("VAL_ORDER_TIME",formatTime($order[0]['time']));

		

		$view_order->assign("LANG_GATEWAY",$lang['front']['viewOrder']['payment_method']);

		$view_order->assign("VAL_GATEWAY",str_replace("_"," ",$order[0]['gateway']));

		

		$view_order->assign("LANG_SHIP_METHOD",str_replace("_"," ",$lang['front']['viewOrder']['ship_method']));

		$view_order->assign("VAL_SHIP_METHOD",$order[0]['shipMethod']);

		

		$view_order->assign("LANG_SUBTOTAL",$lang['front']['viewOrder']['subtotal']);

		$view_order->assign("VAL_SUBTOTAL",priceFormat($order[0]['subtotal']));

		
		$view_order->assign("LANG_COUPON","Discount:");

		$view_order->assign("VAL_COUPON",priceFormat($order[0]['discountTot']));
		

		$view_order->assign("LANG_TOTAL_TAX",$lang['front']['viewOrder']['total_tax']);

		$view_order->assign("VAL_TOTAL_TAX",priceFormat($order[0]['total_tax']));

		

		$view_order->assign("LANG_TOTAL_SHIP",$lang['front']['viewOrder']['shipping']);

		$view_order->assign("VAL_TOTAL_SHIP",priceFormat($order[0]['total_ship']));

		

		$view_order->assign("LANG_GRAND_TOTAL",$lang['front']['viewOrder']['grand_total']);

		$view_order->assign("VAL_GRAND_TOTAL",priceFormat($order[0]['prod_total']));

	

		$view_order->parse("view_order.session_true.order_true");

	

	} else {

		$view_order->assign("LANG_NO_ORDERS",$lang['front']['viewOrder']['order_not_found']);

		$view_order->parse("view_order.session_true.order_false");

	

	}

	

	

	$view_order->assign("LANG_LOGIN_REQUIRED",$lang['front']['viewOrder']['login_required']);

	

	if($ccUserData[0]['customer_id']>0) $view_order->parse("view_order.session_true");

	

	else $view_order->parse("view_order.session_false");

	

	$view_order->parse("view_order");

	

$page_content = $view_order->text("view_order");

?>