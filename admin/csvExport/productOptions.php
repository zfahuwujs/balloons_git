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

$exportData = "Product Option&&&Option Values\r\n";
	
$options = $db->select("SELECT top.option_id, top.option_name, mid.value_name FROM ".$glob["dbprefix"]."CubeCart_options_top top INNER JOIN ".$glob["dbprefix"]."CubeCart_options_mid mid ON top.option_id = mid.father_id ORDER BY top.option_name, mid.value_name");
	
if($options == TRUE){
	$currentOption = 0;
	$optionsBlock = "";
	$valuesBlock = "";
				
	for($j=0; $j<count($options); $j++){
		if($currentOption == 0 || $currentOption != $options[$j]["option_id"]){
			if($currentOption != 0){
				$exportData .= $optionsBlock."&&&".$valuesBlock."\r\n";
				$optionsBlock = "";
				$valuesBlock = "";
			}
			
			$currentOption = $options[$j]["option_id"];
			$optionsBlock .= $options[$j]['option_name'];
			$valuesBlock .= "[".$options[$j]['value_name']."]";
		}else
			$valuesBlock .= "[".$options[$j]['value_name']."]";
	}
	
	$exportData .= $optionsBlock."&&&".$valuesBlock."\r\n";
	
	$exportData = str_replace(',', '&#44;',$exportData);
	$exportData = str_replace("&&&",",",$exportData);
	
	$filename = "CSV_Product_Options_Export_".date("dMy").".csv";
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