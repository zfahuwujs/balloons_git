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

	$noOptions = $db->select("SELECT count(DISTINCT bot.product, bot.option_id) AS totals FROM ".$glob["dbprefix"]."CubeCart_options_bot bot INNER JOIN ".$glob["dbprefix"]."CubeCart_options_top top ON bot.option_id = top.option_id GROUP BY bot.product ORDER BY totals DESC");

	if($noOptions == TRUE){
		$options = $noOptions[0]['totals'];	

		if($options > 0){
			$option_headers = "Product Options&&&";

			for($i=1; $i<=$options; $i++){
				$option_headers .= "Product Variation ".$i."&&&Product Supplement Price ".$i."&&&Product Supplement Symbol ".$i."&&&";
			}

			$exportData .= $option_headers;
		}

		$exportData = substr($exportData, 0, -3);
	}
	$exportData .= "\r\n";

	$inv = $db->select("SELECT * FROM ".$glob["dbprefix"]."CubeCart_inventory ORDER BY productId");

	if($inv == TRUE){
		for($j=0; $j<count($inv); $j++){
			$row = "";
			for($i=0; $i<count($fields); $i++){
				if($fields[$i]["Field"] != "prod_metatitle" && $fields[$i]["Field"] != "prod_metadesc" && $fields[$i]["Field"] != "prod_metakeywords" && $fields[$i]["Field"] != "prod_sefurl")
				$row .= htmlentities($inv[$j][$fields[$i]["Field"]])."&&&";
				// The 'htmlentities()' function is used to avoid any errors caused by html tags in descriptions and any special characters in names.
				// When the data will be imported back to the database, the 'html_entity_decode()' will need to be used.
				// by Robz
			}

			$inventoryOptions = $db->select("SELECT bot.product, bot.option_symbol, bot.option_price, top.option_id, top.option_name, mid.value_id, mid.value_name FROM ".$glob["dbprefix"]."CubeCart_options_bot bot INNER JOIN ".$glob["dbprefix"]."CubeCart_options_top top ON bot.option_id = top.option_id INNER JOIN ".$glob["dbprefix"]."CubeCart_options_mid mid ON bot.value_id = mid.value_id WHERE bot.product = ".$inv[$j]['productId']." ORDER BY bot.product, top.option_name, mid.value_name");
				
			if($inventoryOptions == TRUE){
				$currentOption = 0;
				$optionNo = 0;
				$optionsBlock = "";
				$valuesBlock = "";
				$pricesBlock = "";
				$symbolsBlock = "";
				$rowBlock = "";

				for($k=0; $k<count($inventoryOptions); $k++){
					if($currentOption == 0 || $currentOption != $inventoryOptions[$k]["option_id"]){
						$rowBlock .= $valuesBlock.$pricesBlock.$symbolsBlock;
						$currentOption = $inventoryOptions[$k]["option_id"];
						$optionsBlock .= "[".$inventoryOptions[$k]['option_name']."]";
						$optionNo++;

						$valuesBlock = "&&&[".$inventoryOptions[$k]['value_name']."]";
						$pricesBlock = "&&&[".$inventoryOptions[$k]['option_price']."]";
						$symbolsBlock = "&&&[".$inventoryOptions[$k]['option_symbol']."]";
					}else{
						$valuesBlock .= "[".$inventoryOptions[$k]['value_name']."]";
						$pricesBlock .= "[".$inventoryOptions[$k]['option_price']."]";
						$symbolsBlock .= "[".$inventoryOptions[$k]['option_symbol']."]";
					}
				}
				
				//$valuesBlock = substr($valuesBlock, 3);
				$row .= $optionsBlock.$rowBlock.$valuesBlock.$pricesBlock.$symbolsBlock;
					
				for($k=0; $k<($options-$optionNo); $k++){
					$row .= "&&&";
				}
			}else{
				for($k=0; $k<=$options; $k++){
					$row .= "&&&";
				}
			}

			//$row = substr($row, 0, -3);
			$row = str_replace("\r\n","",$row);
			$exportData .= $row."\r\n";
		}
	}
		
	$exportData = str_replace(',', '&#44;',$exportData);
	$exportData = str_replace("&&&",",",$exportData);
		
	$filename = "CSV_Products_Export_".date("dMy").".csv";
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
	echo '<p class="pageTitle">Export Products</p>';
	echo "<p class='warnText'>No Fields Found</p>";
	include("../includes/footer.inc.php");
}

?>