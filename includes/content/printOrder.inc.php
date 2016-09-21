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
	$view_order=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/printOrder.tpl");
	$view_order->assign("LANG_YOUR_VIEW_ORDER",$lang['front']['viewOrder']['order_no']." ".treatGet($_GET['cart_order_id']));
	$order = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_order_sum INNER JOIN ".$glob['dbprefix']."CubeCart_customer ON ".$glob['dbprefix']."CubeCart_order_sum.customer_id = ".$glob['dbprefix']."CubeCart_customer.customer_id WHERE ".$glob['dbprefix']."CubeCart_order_sum.cart_order_id = ".$db->mySQLSafe($_GET['cart_order_id'])." AND ".$glob['dbprefix']."CubeCart_order_sum.customer_id=".$db->mySQLsafe($ccUserData[0]['customer_id']));
	
	if($order == TRUE){
		
/*		$view_order->assign('VAL_TRACKING', 'Tracking ID: ' .$order[0]['tracking_id']);
*/
		$view_order->assign("ORDID",treatGet($_GET['cart_order_id']));
		
		$view_order->assign("LANG_CUSTOMER_INFO",$lang['front']['viewOrder']['customer_info']);
		
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
			
			if($products[$i]['price'] == "0.00")
				$view_order->assign("VAL_IND_PRICE","FREE<sup>Freebie/BOGOF</sup>");
			else 
				$view_order->assign("VAL_IND_PRICE",priceFormat($products[$i]['price']));
			$view_order->parse("view_order.session_true.order_true.repeat_products");
			
			// Count BOGOFs
			/*$getCatId=$db->select("SELECT cat_id FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = '{$products[$i]['productId']}'");
			//echo var_dump($getCatId);
			$CatBogof=$db->select("SELECT bogof_amount, bogof_product FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = {$getCatId[0]['cat_id']} AND bogof_amount <> 0 AND bogof_product <> ''");
		
			if($CatBogof!=FALSE && count($CatBogof)>0){
				$Bogof[$getCatId[0]['cat_id']]+=$products[$i]['quantity'];
			}*/
		}
		
		/*$subTotal=$order[0]['subtotal'];
		$query="SELECT * FROM CubeCart_inventory WHERE cat_id = 23 AND freebie_price <> 0.00 AND freebie_price < $subTotal"; //toQualify < $subTotal");
		$Freebies=$db->select($query);
		if($Freebies!=FALSE && count($Freebies)>0){
		
		for($x=0;$x<count($Freebies);$x++){
			$view_order->assign("TD_CLASS","");
			$view_order->assign("VAL_PRODUCT",$Freebies[$x]['name']);
			$view_order->assign("VAL_PRODUCT_OPTS","");
			$view_order->assign("VAL_IND_QUANTITY",1);
			$view_order->assign("VAL_IND_PROD_CODE",$Freebies[$i]['productCode']);
			$view_order->assign("VAL_IND_PRICE","<span class='copyText'>FREE<sup>Freebie</sup></span>");
			$view_order->parse("view_order.session_true.order_true.repeat_products");
		}

		
				
			if(count($Bogof)>0){
		
				foreach($Bogof as $CatId => $Amount){
				
					$Cat=$db->select("SELECT * FROM CubeCart_category WHERE cat_id = $CatId");
				
					if($Cat[0]['bogof_amount']<=($Amount+1)){
						// Qualifies
						$CLASS=cellColor(($J+1), $tdEven="tdcartEven", $tdOdd="tdcartOdd");
			
						$ProdInf=$db->select("SELECT * FROM CubeCart_inventory WHERE productCode = '{$Cat[0]['bogof_product']}'");
						
						if(empty($ProdInf[0]["image"])){
							$IMG="skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif";
						} else {
							$IMG="images/uploads/thumbs/thumb_".$ProdInf[0]["image"];
						}
							
						if($ProdInf!=FALSE){
							if($Cat[0]['bogof_amount']<=$Amount){
								$view_order->assign("TD_CLASS","");
								$view_order->assign("VAL_PRODUCT",$ProdInf[0]['name']);
								$view_order->assign("VAL_PRODUCT_OPTS","");
								$view_order->assign("VAL_IND_QUANTITY",1);
								$view_order->assign("VAL_IND_PROD_CODE",$ProdInf[0]['productCode']);
								$view_order->assign("VAL_IND_PRICE","<span class='copyText'>FREE<sup>BOGOF</sup></span>");
								$view_order->parse("view_order.session_true.order_true.repeat_products");
							}	
						}
					}
				}
			
			}
		}*/
		
		$view_order->assign("LANG_ORDER_LIST",$lang['front']['viewOrder']['review_below']); 
		
		$view_order->assign("LANG_ORDER_TIME",$lang['front']['viewOrder']['order_date_time']);
		$view_order->assign("VAL_ORDER_TIME",formatTime($order[0]['time']));
		
		$view_order->assign("LANG_GATEWAY",$lang['front']['viewOrder']['payment_method']);
		$view_order->assign("VAL_GATEWAY",str_replace("_"," ",$order[0]['gateway']));
		
		$view_order->assign("LANG_SHIP_METHOD",str_replace("_"," ",$lang['front']['viewOrder']['ship_method']));
		$view_order->assign("VAL_SHIP_METHOD",$order[0]['shipMethod']);
		
		$view_order->assign("LANG_SUBTOTAL",$lang['front']['viewOrder']['subtotal']);
		$view_order->assign("VAL_SUBTOTAL",priceFormat($order[0]['subtotal']));
//.: adg_coupon_mod http://www.cubecartmodder.com/ :.
		$view_order->assign("LANG_COUPON",$lang['front']['cart']['coupon']."<br>".$order[0]['coupon_code']);
		$view_order->assign("VAL_COUPON",priceFormat($order[0]['coupon_savings']));
		//.: adg_coupon_mod :.
		
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