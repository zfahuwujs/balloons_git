<?php
include_once("includes/ini.inc.php");
include_once("includes/global.inc.php");
$enableSSl = 1;
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

if(isset($_REQUEST)){
$prodId = $_REQUEST['prodId'];
$prodOpt = $_REQUEST['prodOpt'];
$result = array();
$prodOptString = substr($prodOpt,0,-1);

$hasOptions = $db->select("SELECT stock FROM ".$glob['dbprefix'] ."CubeCart_options_stock WHERE prodId = ".$db->mySQLSafe($prodId));


//get price
if($hasOptions){
	$record['email'] = $db->mySQLSafe($ccUserData[0]['email']);
	$record['productId'] = $db->mySQLSafe($prodId);
	$record['prodOptions'] = $db->mySQLSafe($prodOptString);
	$insert = $db->insert($glob['dbprefix']."CubeCart_stock_notify", $record);
	if($insert){
		$result['emailMeMsg'] = 'We will email you when this item is back in stock';
		$result['emailMe'] = '2';
	}else{
		$result['emailMeMsg'] = 'Email me when in stock';
		$result['emailMe'] = '1';
	}
}else{
	$record['email'] = $db->mySQLSafe($ccUserData[0]['email']);
	$record['productId'] = $db->mySQLSafe($prodId);
	$insert = $db->insert("CubeCart_stock_notify", $record);
	if($insert){
		$result['emailMeMsg'] = 'We will email you when this item is back in stock';
		$result['emailMe'] = '2';
	}else{
		$result['emailMeMsg'] = 'Email me when in stock';
		$result['emailMe'] = '1';
	}
}
echo json_encode($result);
}


////
?>