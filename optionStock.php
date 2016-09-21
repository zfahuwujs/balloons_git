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
	if($config['sef'] == 0 && preg_match('#'.$glob['rootRel'].$sefroot.'#i', $_SERVER['PHP_SELF'])) {
		Header("Location: ".$glob['rootRel']."index.php");
	}
	include_once("includes/sslSwitch.inc.php");
	include_once("includes/session.inc.php");
	include_once("includes/currencyVars.inc.php");
	
	$children='';
	if(isset($_POST)){
		$stock = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_option_stock WHERE options='.$db->mySQLSafe($_POST['options']));
		if($stock && $stock[0]['stock']>$_POST['quan']){
			$msg='inStock';
		}elseif($stock && $stock[0]['stock']>0){
			$msg=$stock[0]['stock'];
		}else{
			$msg='noStock';
		}
	}
	echo $msg;
?>