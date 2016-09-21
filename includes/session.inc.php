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

|	session.php

|   ========================================

|	Core Session Management	

+--------------------------------------------------------------------------

*/



phpExtension();

//if(evo() == false) { header("Location: index.html"); exit; }


if(($config['offLine']==1 && isset($_SESSION['ccAdmin']) && $config['offLineAllowAdmin']==0) || ($config['offLine']==1 && !isset($_SESSION['ccAdmin']))) {

	header("Location: offLine.php");

	exit;

}



$sessData["location"] = $db->mySQLSafe(currentPage());

$lkParsed = "PC9ib2R5Pg==";



if( !isset($_SESSION['ccUser']) && (isset($_COOKIE['ccUser']) || isset($_GET['ccUser'])) ){



	if(isset($_COOKIE['ccUser'])){



		$_COOKIE['ccUser'] = treatGet($_COOKIE['ccUser']);

		$sessId = base64_decode($_COOKIE['ccUser']);

	

	} elseif(isset($_GET['ccUser'])){



		$_GET['ccUser'] = treatGet($_GET['ccUser']);

		$sessId = $_GET['ccUser'];

	

	}

	

	// see if session is still in db

	$query = "SELECT sessId FROM ".$glob['dbprefix']."CubeCart_sessions WHERE sessId=".$db->mySQLSafe($sessId);

	$results = $db->select($query);

	

	if($results == TRUE){



	

		$sessData["timeLast"] = $db->mySQLSafe(time());

		

		if(!isset($_COOKIE['ccRemember'])) { $sessData["customer_id"] = 0; }

		

		$update = $db->update($glob['dbprefix']."CubeCart_sessions", $sessData,"sessId=".$db->mySQLSafe($results[0]['sessId']));

		

		$_SESSION['ccUser'] = $results[0]['sessId'];

		// set cookie to extend expire time meaning if the visitor visits regularly they stay logged in

		setcookie("ccUser", base64_encode($sessId),time()+$config['sqlSessionExpiry'], $sessionDomain);

	

	}

	

}



if(!isset($_SESSION['ccUser']) && $results == FALSE) {

	

	

	$sessId = makeSessId();

	$_SESSION['ccUser'] = $sessId;

	

	// insert sessionId into db

	

	$sessData["sessId"] = $db->mySQLSafe($_SESSION['ccUser']);		

	$timeNow = $db->mySQLSafe(time());

	$sessData["timeStart"] = $timeNow;	

	$sessData["timeLast"] = $timeNow;

	$sessData["customer_id"] = 0;



	$insert = $db->insert($glob['dbprefix']."CubeCart_sessions", $sessData);

	

	// set cookie

	setcookie("ccUser", base64_encode($sessId),time()+$config['sqlSessionExpiry'], $sessionDomain);

	

	// delete sessions older than time set in config file

	// delete sessions older than time set in config file

	$expiredSessTime = time() - $config['sqlSessionExpiry'];
	$expiredSessions = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_sessions WHERE timeLast < '.$db->mySQLSafe($expiredSessTime));
	if($expiredSessions==true){
		for($i = 0; $i < count($expiredSessions); $i++){
			$cartDelete = $db->delete($glob['dbprefix']."CubeCart_basket", "sessId = ".$db->mySQLSafe($expiredSessions[$i]['sessId']));
		}
	}
	$delete = $db->delete($glob['dbprefix']."CubeCart_sessions", "timeLast<".$expiredSessTime);



} else {

	

	$sessData["timeLast"] = $db->mySQLSafe(time());



	$update = $db->update($glob['dbprefix']."CubeCart_sessions", $sessData,"sessId=".$db->mySQLSafe($_SESSION['ccUser']));



}



$uniKey = "PGRpdiBjbGFzcz0ndHh0Q29weXJpZ2h0Jz5Qb3dlcmVkIGJ5IDxhIGhyZWY9J2h0dHA6Ly93d3cuY3ViZWNhcnQuY29tJyBjbGFzcz0ndHh0Q29weXJpZ2h0JyB0YXJnZXQ9J19ibGFuayc+Q3ViZUNhcnQ8L2E+JnRyYWRlOzxiciAvPkNvcHlyaWdodCA8YSBocmVmPSdodHRwOi8vd3d3LmRldmVsbGlvbi5jb20nIGNsYXNzPSd0eHRDb3B5cmlnaHQnIHRhcmdldD0nX2JsYW5rJz5EZXZlbGxpb24gTGltaXRlZDwvYT4gMjAwNi4gQWxsIHJpZ2h0cyByZXNlcnZlZC48L2Rpdj48L2JvZHk+";

$uniKey2 = "TG9jYXRpb246IGh0dHA6Ly93d3cuY3ViZWNhcnQuY29tL3NpdGUvcHVyY2hhc2Uv";





// get userdata

$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_sessions LEFT JOIN ".$glob['dbprefix']."CubeCart_customer ON ".$glob['dbprefix']."CubeCart_sessions.customer_id = ".$glob['dbprefix']."CubeCart_customer.customer_id WHERE sessId = ".$db->mySQLSafe($_SESSION['ccUser']);

$ccUserData = $db->select($query);



// We have a session issue :-/ (e.g. session but no matching DB value)

if($ccUserData==FALSE)

{

	// reset session and reload current page

	unset($_SESSION['ccUser'],$_COOKIE['ccUser'],$_COOKIE['ccRemember']);

	header("Location: ".str_replace("&amp;","&",currentPage()));

	exit;

}

?>