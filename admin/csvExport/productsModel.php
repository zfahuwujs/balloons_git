<?php

include("../../includes/ini.inc.php");
include("../../includes/global.inc.php");
require_once("../../classes/db.inc.php");
$db = new db();
include_once("../../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include_once("../../includes/sslSwitch.inc.php");
include("../includes/auth.inc.php");
if(permission("csv","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

$exportData = "";
	
$fields = $db->select("DESCRIBE ".$glob["dbprefix"]."CubeCart_inventory");
	
if($fields == TRUE){
	for($i=0; $i<count($fields); $i++){
		if($fields[$i]["Field"] != "prod_metatitle" && $fields[$i]["Field"] != "prod_metadesc" && $fields[$i]["Field"] != "prod_metakeywords" && $fields[$i]["Field"] != "prod_sefurl")
		$exportData .= $fields[$i]["Field"]."&&&";
	}
		
	$noOptions = $db->select("SELECT count(option_id) AS total FROM ".$glob["dbprefix"]."CubeCart_options_top");

	if($noOptions == TRUE && $noOptions[0]["total"] > 0){
		$exportData .= "Product Options&&&";
		
		for($i=1; $i<=$noOptions[0]["total"]; $i++){
			$exportData .= "Product Variation ".$i."&&&Product Supplement Price ".$i."&&&Product Supplement Symbol ".$i."&&&";
		}
	}
	
	$exportData .= "\r\n";
	$exportData = str_replace(",","",$exportData);
	$exportData = str_replace("&&&",",",$exportData);
		
	$filename = "CSV_Products_Import_Model_".date("dMy").".csv";
	$contentLength = strlen($exportData);	
	$content = $exportData;	
	header('Pragma: private');
	header('Cache-control: private, must-revalidate');
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-type: text/plain");
	header("Content-type: application/octet-stream");
	header("Content-length: ".$contentLength);
	header("Content-Transfer-Encoding: binary");
	echo $content;
}else{
	include("../includes/header.inc.php");
	echo '<p class="pageTitle">Import Products Model</p>';
	echo "<p class='warnText'>No Fields Found</p>";
	include("../includes/footer.inc.php");
}

?>