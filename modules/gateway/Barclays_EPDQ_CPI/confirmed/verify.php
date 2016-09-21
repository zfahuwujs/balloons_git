<?php

include("../../../../includes/ini.inc.php");
include("../../../../includes/global.inc.php");
require_once("../../../../classes/db.inc.php");
$db = new db();
include_once("../../../../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../../../../language/".$config['defaultLang']."/lang.inc.php");
include("../../../../includes/currencyVars.inc.php");

if($_POST['oid'] && $_POST['transactionstatus']=="Success"){
	$record['status'] = 2;
	$where = "cart_order_id = ".$db->mySQLSafe($_POST['oid']);
	$update = $db->update($glob['dbprefix']."CubeCart_order_sum", $record, $where);
}

?>