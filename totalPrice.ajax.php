<?php
$enableSSl = 1;
include_once("includes/ini.inc.php");
include_once("includes/global.inc.php");
include_once("classes/db.inc.php");
$db = new db();
include_once("includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("includes/sessionStart.inc.php");
include_once("includes/sef_urls.inc.php");	
$sefroot = sef_script_name();
include_once("includes/sslSwitch.inc.php");
include_once("includes/session.inc.php");
include_once("includes/currencyVars.inc.php");

require_once("classes/newCart.php");
$cart = new basket();
$basket = $cart->getContents();

$total = priceFormat($cart->grandTotal());
echo $total;
?>