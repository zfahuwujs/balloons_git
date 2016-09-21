<?php

$web = substr(pathinfo(__FILE__, PATHINFO_DIRNAME), 0, -5);
$website = "http://www.".substr($web, 16, -9);

include($web."includes/ini.inc.php");
include($web."includes/global.inc.php");
require_once($web."classes/db.inc.php");
$db = new db();
include_once($web."includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once($web."language/".$config['defaultLang']."/lang.inc.php");
include($web."classes/gd.inc.php");
include($web."includes/currencyVars.inc.php");
include($web."includes/sef_urls.inc.php");

$existing = $db->numrows("SELECT * FROM ".$glob['dbprefix']."CubeCart_mods");

if($existing > 0){
	$delete = $db->misc("TRUNCATE ".$glob['dbprefix']."CubeCart_mods");
	
	require_once($web."admin/sitemapXMLgenerator.php");
	
	$encoder = urlencode($website."sitemap.xml");
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "www.google.com/webmasters/tools/ping?sitemap=".$encoder);
	//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);
	curl_close($ch); 
	
	//echo $output;
}