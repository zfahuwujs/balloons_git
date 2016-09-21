<?php
$web = substr(pathinfo(__FILE__, PATHINFO_DIRNAME), 0, -5);
include($web."includes/ini.inc.php");
include($web."includes/global.inc.php");
require_once($web."classes/db.inc.php");
$db = new db();
include_once($web."includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once($web."language/".$config['defaultLang']."/lang.inc.php");
include($web."classes/gd.inc.php");
include($web."includes/currencyVars.inc.php");

ini_set("memory_limit", "512M");
$timeout = 1;


$query = "SELECT inv.productCode, inv.productId, inv.name, inv.description, inv.prodWeight, inv.condition, inv.brand, manufacturerCode, productCodeAsc FROM ".$glob['dbprefix']."CubeCart_inventory inv WHERE showProd = 1 ORDER BY name ASC";

$results = $db->select($query);
if($results==TRUE) {
	$googleBaseContent = "id\tlink\ttitle\tdescription\timage_link\tprice\tweight\tcondition\tBrand\tMPN\tGTIN\tshipping\r\n";
		
	for($g=0; $g<count($results); $g++){
		$bronzePrice = $db->select("
			SELECT bronze 
			FROM ".$glob['dbprefix']."CubeCart_currentPrices
			WHERE productCode = ".$db->mySQLSafe($results[$g]['productCode'])."
			LIMIT 1
		");
		
		$price = $bronzePrice[0]['bronze'];
		
		$shippingModules = $db->select("SELECT folder FROM ".$glob['dbprefix']."CubeCart_Modules WHERE module='shipping' AND status = 1");
		$noItems = 1;
		$sum = 0;
		$shippingPrice = 0;
		$shipRegion = 0;
		$shippingRate = null;
		if($shippingModules == TRUE){
			for($s=0; $s<count($shippingModules); $s++){
				$shipKey++;
				include($web."modules/shipping/".$shippingModules[$s]['folder']."/calc.php"); 
				//var_dump($methodData[1]["sumMethod"]);
				$displacer = 0;
				while(empty($methodData[$displacer]["sumMethod"]) || !is_numeric($methodData[$displacer]["sumMethod"])){
					$displacer++;
				}
				$shippingPrice = $methodData[$displacer]["sumMethod"];
				$shipRegion = $basket['delInf']['country'];
				$shippingRate = $basket['delInf']['country'].':::'.$shippingPrice;
			}
		}
		
			
		$name = str_replace(array("&nbsp;","\t","\r","\n","\0","\x0B","
		"),"",strip_tags($results[$g]['name']));
		$name = str_replace("  ","",$name);
		$desc = str_replace(array("&nbsp;","\t","\r","\n","\0","\x0B","
		"),"",strip_tags($results[$g]['description']));
		$desc = str_replace("  ","",$desc);
		// SEO friendly mod
		if($config['sef'] == 0) {
		$googleBaseContent .= $results[$g]['productId']."\t".$glob['storeURL']."/index.php?act=viewProd&productId=".$results[$g]['productId']."\t".$name."\t".$desc;
		} else {
		include_once($web."includes/sef_urls.inc.php");
		$googleBaseContent .= $results[$g]['productId']."\t".$glob['storeURL']."/".sef_get_base_url(). generateProductUrl($results[$g]['productId'])."\t".$name."\t".$desc;
		}
		// SEO friendly mod
			
			
		if($results[$g]['image'])
			$googleBaseContent .= "\t".$glob['storeURL']."/images/uploads/".$results[$g]['image'];
		else
			$googleBaseContent .= "\t".$glob['storeURL']."/skins/".$config['skinDir']."/styleImages/nophoto.gif";
			
		$googleBaseContent .= "\t".$price."\t".$results[$g]['prodWeight']."\t".$results[$g]['condition']."\t".$results[$g]['brand']."\t".$results[$g]['manufacturerCode']."\t".$results[$g]['productCodeAsc']."\t".$shippingRate."\r\n";
		
		set_time_limit ($timeout);
	}
		
	$filename = $web."GoogleBaseFeed.txt";
	$contentLength = strlen($googleBaseContent);
	$content = $googleBaseContent;
	
	if(file_exists($filename)){
		$fileHandle = fopen($filename, 'w') or die("");
	}else{
		$fileHandle = fopen($filename, 'w+') or die("");
	}
		
	fwrite($fileHandle, $content);
	fclose($fileHandle);
	exit;
}
?>
