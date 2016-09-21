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

|	Switch between secure and insecure pages	

+--------------------------------------------------------------------------

*/



phpExtension();



if(isset($_GET['act'])){



	$_GET['act'] = treatGet($_GET['act']);

	$currentPage = $_GET['act'];



} else {



	$currentPage = "";



}



if((isset($enableSSl) && $enableSSl==1) OR (isset($sslPages[$currentPage]) && $sslPages[$currentPage]==1)) { 



	$enableSSl = 1; 



} else { 



	$enableSSl = ""; 



}



$currentPageDir = $_SERVER['PHP_SELF'];

	

	$storeURL = str_replace("http://","",$glob['storeURL']);

	$storeURL_SSL = str_replace("https://","",$config['storeURL_SSL']);

	

	if (isset($_SERVER['QUERY_STRING'])) {

		

		$currentPageDir .= "?" . htmlentities($_SERVER['QUERY_STRING']);

		

		if($config['ssl'] == 1 && $storeURL!==$storeURL_SSL){

	

			$currentPageDir .= "&ccUser=" . $_SESSION['ccUser'];

	

		}

		

	} elseif($config['ssl'] == 1 && $storeURL!==$storeURL_SSL){

		

		$currentPageDir .= "?ccUser=" . $_SESSION['ccUser'];

	

	}

	

	$currentPageDir = str_replace("&amp;","&",$currentPageDir);

	

// switch into ssl mode

if(detectSSL()==TRUE && $enableSSl == 0 && $config['ssl'] == 1){



	$page = $glob['storeURL'].str_replace($config['rootRel_SSL'],"/",$currentPageDir);

	header("Location: ".$page);

	exit;



}

// switch out of ssl mode 

elseif(detectSSL()==FALSE && $enableSSl == 1 && $config['ssl'] == 1) {

	

	$page = $config['storeURL_SSL'].str_replace($glob['rootRel'],"/",$currentPageDir);

	header("Location: ".$page);

	exit;

	

}



// get paths and dirs

if(detectSSL()==TRUE){

	

	$GLOBALS['rootDir'] = $glob['rootDir'];

	$GLOBALS['storeURL'] = $config['storeURL_SSL'];

	$GLOBALS['rootRel'] = $config['rootRel_SSL'];



} else {

	

	$GLOBALS['rootDir'] = $glob['rootDir'];

	$GLOBALS['storeURL'] = $glob['storeURL'];

	$GLOBALS['rootRel'] = $glob['rootRel'];

	

}

?>