<?php
include_once("includes/ini.inc.php");
include_once("includes/global.inc.php");
include_once("classes/db.inc.php");
$db = new db();
include_once("includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("includes/sessionStart.inc.php");
include_once("includes/sef_urls.inc.php");	
include_once("includes/sslSwitch.inc.php");
include_once("includes/session.inc.php");
include_once("includes/currencyVars.inc.php");
$lang_folder = "";
if(empty($ccUserData[0]['lang'])){
	$lang_folder = $config['defaultLang'];
} else {
	$lang_folder = $ccUserData[0]['lang'];
}
require_once("classes/newCart.php");

$cart = new basket();
$basket = $cart->getContents();

$totalWeight = $cart->getTotalWeight();

if(isset($_GET['country'])){
	$delCountry = $_GET['country'];
}
$shippingModules = $db->select("SELECT folder FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='shipping' AND status = 1");
$noItems = $cart->noItems();
$sum = 0;
if($shippingModules == TRUE){
	$basketKey = $cart->getVar('shipKey');
	
	if (isset($_GET['s']) && $_GET['s'] == 1) {
		$cart->setVar(1,"shipKey");
	} elseif (isset($_POST['shipping']) && $_POST['shipping'] > 0) {
		$cart->setVar($_POST['shipping'], "shipKey");
	} elseif (!isset($basketKey) || empty($basketKey)) {
		$cart->setVar(1, "shipKey");
	}

	for ($i = 0; $i < count($shippingModules); $i++) {
		$shipKey++;
		include("modules/shipping/".$shippingModules[$i]['folder']."/calc.php"); 
	}
	if($shippingAvailable==true){
		echo $shippingPrice;
	}else{
		if($overWeight==true){
			$cart->removeShipping();
			echo 'overWeight';
		}else{
			$cart->removeShipping();
			echo 'No shipping available';
		}
	}
}
?>