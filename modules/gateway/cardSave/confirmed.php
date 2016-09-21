<?php

include_once("../../../includes/ini.inc.php");

include_once("../../../includes/global.inc.php");

$enableSSl = 1;

include_once("../../../classes/db.inc.php");

$db = new db();

include_once("../../../includes/functions.inc.php");

$config = fetchDbConfig("config");

include_once("../../../includes/sessionStart.inc.php"); 

include_once("../../../includes/sslSwitch.inc.php");

include_once("../../../includes/session.inc.php");

include_once("../../../includes/currencyVars.inc.php");

include_once("../../../language/".$config['defaultLang']."/lang.inc.php");

include("transfer.inc.php");

$success = successConfirm();

if($success == TRUE){
	$cart_order_id = treatGet($_POST["OrderID"]);
	include_once("../../../includes/orderSuccess.inc.php");
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Redirecting...</title>
	</head>

	<body onload="javascript: document.getElementById('callback').submit();">
		<form method="post" action="https://mms.cardsaveonlinepayments.com/Pages/PublicPages/PaymentForm.aspx" name="callback" id="callback">
			<input type="hidden" name="StatusCode" value="<?php echo $_POST["StatusCode"]; ?>" />
			<input type="hidden" name="Message" value="Results received OK" />
			<input type="hidden" name="OrderID" value="<?php echo $_POST["OrderID"]; ?>" />
		</form>
	</body>
</html>

