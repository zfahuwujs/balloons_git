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

$exportData = "Category Name&&&Category ID\r\n";
	
$cat = $db->select("SELECT * FROM ".$glob["dbprefix"]."CubeCart_category");
	
if($cat == TRUE){
	
	for($j=0; $j<count($cat); $j++){
		$row = "";
		$row .= $cat[$j]["cat_name"]."&&&".$cat[$j]["cat_id"]."\r\n";
		$exportData .= $row;
	}
	
	$exportData = str_replace(',', '&#44;',$exportData);
	$exportData = str_replace("&&&",",",$exportData);
	
	$filename = "CSV_Categories_Export_".date("dMy").".csv";
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
	echo '<p class="pageTitle">Export Categories</p>';
	echo "<p class='warnText'>No Fields Found</p>";
	include("../includes/footer.inc.php");
}

?>