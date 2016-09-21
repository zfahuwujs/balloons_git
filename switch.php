<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.15
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2005 - 2006. All rights reserved.
|   Devellion Limited,
|   22 Thomas Heskin Court,
|   Station Road,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 3EE
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Thursday, 4th January 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	switch.php
|   ========================================
|	Switch between language and currency vars	
+--------------------------------------------------------------------------
*/
	include_once("includes/ini.inc.php");
	
	// INCLUDE CORE VARIABLES & FUNCTIONS
	include_once("includes/global.inc.php");
	include_once("includes/functions.inc.php");

	// initiate db class
	include_once("classes/db.inc.php");
	$db = new db();
	include_once("includes/functions.inc.php");
	$config = fetchDbConfig("config");

	// get session data
	include_once("includes/sessionStart.inc.php");
	include_once("includes/session.inc.php");
	
	// change language if necessary making sure it is cleaned against cross site scripting!!! Or else there'd be truble!!
	if( (isset($_GET['lang'])) && (!empty($_GET['lang'])) && (isset($_SESSION['ccUser'])) ){
		$sessData['lang'] = "'".preg_replace('/[^a-zA-Z0-9_\-\+]/', '',$_GET['lang'])."'";
		$update = $db->update($glob['dbprefix']."CubeCart_sessions", $sessData,"sessId=".$db->mySQLSafe($_SESSION['ccUser']));
		
		// detect possible spoofing URL's
		if(!eregi("http://",$_GET['r']) && !eregi("ftp://",$_GET['r']) && !eregi("https://",$_GET['r'])){
			header("Location: ".str_replace("amp;","",treatGet($_GET['r'])));
		} else {
			header("Location: index.php");
		}
		exit;
		
	} elseif((isset($_GET['currency'])) && !empty($_GET['currency']) && (isset($_SESSION['ccUser']))){
	
		$sessData['currency'] = "'".preg_replace('/[^a-zA-Z0-9_\-\+]/', '',$_GET['currency'])."'";
		$update = $db->update($glob['dbprefix']."CubeCart_sessions", $sessData,"sessId=".$db->mySQLSafe($_SESSION['ccUser']));
		
		// detect possible spoofing URL's
		if(!eregi("http://",$_GET['r']) && !eregi("ftp://",$_GET['r']) && !eregi("https://",$_GET['r'])){
			header("Location: ".str_replace("amp;","",treatGet($_GET['r'])));
		} else {
			header("Location: index.php");
		}
		exit;
	
	}	elseif((isset($_GET['tax'])) && !empty($_GET['tax']) && (isset($_SESSION['ccUser']))){
	
		$sessData['tax'] = $db->mySQLSafe($_GET['tax']);
		$update = $db->update($glob['dbprefix']."CubeCart_sessions", $sessData,"sessId=".$db->mySQLSafe($_SESSION['ccUser']));
		
		// detect possible spoofing URL's
		if(!eregi("http://",$_GET['r']) && !eregi("ftp://",$_GET['r']) && !eregi("https://",$_GET['r'])){
			header("Location: ".str_replace("amp;","",treatGet($_GET['r'])));
		} else {
			header("Location: index.php");
		}
		exit;
		
		} else {
		header("Location: index.php");
		exit;
	}
?>