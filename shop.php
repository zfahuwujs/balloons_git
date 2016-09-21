<?php

/* <rf> search engine friendly url mods */

include_once("includes/ini.inc.php");
	
// INCLUDE CORE VARIABLES & FUNCTIONS
include_once("includes/global.inc.php");

// some servers do not include the full directory information, if so fill it ourselves
if(strpos($_SERVER['PHP_SELF'], $glob['rootRel']."shop.php") !== 0)
{
	$_SERVER['PHP_SELF'] = $glob['rootRel'] . "shop.php" . $_SERVER['PHP_SELF'];	
}

$path = $_SERVER['PHP_SELF'];

if(preg_match("/.*(c|cat)_(.*)\.(html|php)/i", $path, $matches)) {
	$_GET['act'] = 'viewCat'; 
	$_GET['catId'] = "$matches[2]";	
}
else if(preg_match("/.*(p|prod)_(.*)\.(html|php)/i", $path, $matches)) {
	$_GET['act'] = 'viewProd'; 
	$_GET['productId'] = "$matches[2]";
}
else if(preg_match("/.*(i|info)_(.*)\.(html|php)/i", $path, $matches)) {
	$_GET['act'] = 'viewDoc'; 
	$_GET['docId'] = "$matches[2]";
}
else if(preg_match("/.*(t|tell)_(.*)\.(html|php)/i", $path, $matches)) {
	$_GET['act'] = 'taf'; 
	$_GET['productId'] = "$matches[2]";
}

include("index.php");   

/* <rf> end mods */

?>
