<?php
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
require_once("classes/cart.php");
$cart = new cart();
$basket = $cart->cartContents($ccUserData[0]['basket']);

if(isset($basket['shipCost']) && $basket['shipCost'] > 0){
?>
<?php echo priceFormat($basket['shipCost']); ?>
<input name="shipPrice" id="shipPrice" type="hidden" value="<?php echo $basket['shipCost']; ?>">
<?php
}else{
	echo 'n/a';
}

?>