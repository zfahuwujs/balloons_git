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

$exportData = "Customer ID&&&Title&&&Firsr Name&&&Last Name&&&Email&&&Address Line 1&&&Address Line 2&&&Town&&&County&&&Postcode&&&Country&&&Phone&&&Mobile&&&Business Name\r\n";
	
$customers = $db->select("SELECT * FROM ".$glob["dbprefix"]."CubeCart_customer");
	
if($customers == TRUE){
	for($j=0; $j<count($customers); $j++){
		$row = "";
		$row .= $customers[$j]["customer_id"]."&&&".$customers[$j]["title"]."&&&".$customers[$j]["firstName"]."&&&".$customers[$j]["lastName"]."&&&".$customers[$j]["email"]."&&&".$customers[$j]["add_1"]."&&&".$customers[$j]["add_2"]."&&&".$customers[$j]["town"]."&&&".$customers[$j]["county"]."&&&".$customers[$j]["postcode"]."&&&".countryName($customers[$j]["country"])."&&&".$customers[$j]["phone"]."&&&".$customers[$j]["mobile"]."&&&".$customers[$j]["company"]."\r\n";
		$exportData .= $row;
	}
	
	$exportData = str_replace(',', '&#44;',$exportData);
	$exportData = str_replace("&&&",",",$exportData);
	
	$filename = "CSV_Customers_Export_".date("dMy").".csv";
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
	echo '<p class="pageTitle">Export Customers</p>';
	echo "<p class='warnText'>No Customers Found</p>";
	include("../includes/footer.inc.php");
}

?>