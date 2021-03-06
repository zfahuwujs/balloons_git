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
|	confirmed.php
|   ========================================
|	Order Confirmation
+--------------------------------------------------------------------------
*/
if(!isset($_POST['x_invoice_num']) && !isset($_POST['x_amount'])){
	echo "<html>\r\n<head>\r\n<title>Forbidden 403</title>\r\n</head>\r\n<body><h3>Forbidden 403</h3>\r\nThe document you are requesting is forbidden.\r\n</body>\r\n</html>";
	exit;
}

	include_once("../../../includes/ini.inc.php");
	
	// INCLUDE CORE VARIABLES & FUNCTIONS
	include_once("../../../includes/global.inc.php");
	$enableSSl = 1;
	
	// initiate db class
	include_once("../../../classes/db.inc.php");
	$db = new db();
	
	include_once("../../../includes/functions.inc.php");
	$config = fetchDbConfig("config");
	
	include_once("../../../includes/sessionStart.inc.php");
	
	include_once("../../../includes/sslSwitch.inc.php");
	
	include_once("../../../includes/session.inc.php");
	// get exchange rates etc
	include_once("../../../includes/currencyVars.inc.php");
	
	include_once("../../../language/".$config['defaultLang']."/lang.inc.php");

// WORK OUT IS THE ORDER WAS SUCCESSFULL OR NOT ;)

// 1. Include gateway file

include("transfer.inc.php");

// 2. Include function which returns ture or false

$success = successFirst();
	
	if($success == TRUE){
		
		//$cart_order_id = $_POST['x_invoice_num'];
		//include_once("../../../includes/orderSuccess.inc.php");
		$result = "?pg=".base64_encode("Authorize");
		
	} else {
		
		$result = "?f=1&amp;pg=".base64_encode("Authorize");
		
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charsetIso;?>" />
<title>Redirecting...</title>
<meta http-equiv="refresh" content="0;URL=<?php echo $GLOBALS['storeURL']."/confirmed.php".$result; ?>" />
</head>
<body>
<p align="center"><a href="<?php echo $GLOBALS['storeURL']."/confirmed.php".$result; ?>"><?php echo $GLOBALS['storeURL']; ?></a></p>
<?php 

if($success == TRUE){
		
	// add affilate tracking code/module
	$affiliateModule = $db->select("SELECT folder, `default` FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='affiliate' AND status = 1");

	if($affiliateModule == TRUE) {
	
		for($i=0; $i<count($affiliateModule); $i++){
			
			include("../../../modules/affiliate/".$affiliateModule[$i]['folder']."/tracker.inc.php");
			// VARS AVAILABLE
			// Order Id Number $basket['cart_order_id']
			// Order Total $order[0]['prod_total']
			$basket['cart_order_id'] = $_POST['x_invoice_num'];
			$order[0]['prod_total'] = $_POST['x_amount'];
			echo $affCode;
		
		}
	
	}
		
}

?>
</body>
</html>
