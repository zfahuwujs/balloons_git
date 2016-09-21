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
|	viewOrders.inc.php
|   ========================================
|	Displays the Customers Orders	
+--------------------------------------------------------------------------
*/

phpExtension();

// query database

$view_orders=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/viewOrders.tpl");

	$view_orders->assign("LANG_YOUR_VIEW_ORDERS",$lang['front']['viewOrders']['your_orders']);

	$orders = $db->select("SELECT status, cart_order_id, time FROM ".$glob['dbprefix']."CubeCart_order_sum WHERE customer_id = ".$db->mySQLsafe($ccUserData[0]['customer_id'])." ORDER BY `time` DESC");
	
	if($orders == TRUE){
		
		$view_orders->assign("LANG_ORDER_LIST",$lang['front']['viewOrders']['orders_listed_below']); 
		
		$view_orders->assign("LANG_ORDER_NO",$lang['front']['viewOrders']['order_no']); 
		$view_orders->assign("LANG_STATUS",$lang['front']['viewOrders']['status']);
		$view_orders->assign("LANG_DATE_TIME",$lang['front']['viewOrders']['date_time']);
		$view_orders->assign("LANG_ACTION",$lang['front']['viewOrders']['action']);
		$view_orders->assign("LANG_VIEW_ORDER",$lang['front']['viewOrders']['view']);

		for($i=0; $i<count($orders);$i++){
			
			$state = $orders[$i]['status'];
			$orders[$i]['state'] =  $lang['orderState'][$state];
			
			$view_orders->assign("TD_CART_CLASS",cellColor($i, $tdEven="tdcartEven", $tdOdd="tdcartOdd"));
			$view_orders->assign("DATA",$orders[$i]);
			$view_orders->assign("DATA",$orders[$i]);
			$view_orders->assign("VAL_DATE_TIME",formatTime($orders[$i]['time']));
			$view_orders->parse("view_orders.session_true.orders_true.repeat_orders");
		}
		
	
		$view_orders->parse("view_orders.session_true.orders_true");
	
	} else {
		$view_orders->assign("LANG_NO_ORDERS",$lang['front']['viewOrders']['no_orders']);
		$view_orders->parse("view_orders.session_true.orders_false");
	
	}
	
	
	$view_orders->assign("LANG_LOGIN_REQUIRED",$lang['front']['viewOrders']['login_required']);
	
	if($ccUserData[0]['customer_id']>0) $view_orders->parse("view_orders.session_true");
	
	else $view_orders->parse("view_orders.session_false");
	
	$view_orders->parse("view_orders");
	
$page_content = $view_orders->text("view_orders");

if($_GET["ccdl"]){
	$page_content = "ccdl";
}
?>