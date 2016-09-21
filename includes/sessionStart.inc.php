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
//$sessionDomain = substr($GLOBALS['rootRel'],0, strlen($GLOBALS['rootRel'])-1);

$sessionDomain = '/';

if($glob['rootRel']=="/"){
	$sessionName = "ccSID";
} else {
	$sessionName = "ccSID".md5($glob['rootRel']);
}

session_name($sessionName);
@ini_set("session.cookie_path",$sessionDomain);
// session_start();  <rf> search engine friendly url mod
search_friendly_session_start();

/* <rf> search engine friendly url mod */
function search_friendly_session_start() {
	global $config;

	if(user_is_search_engine() == false || $config['sef'] == 0) {
session_start();
	}
}

function user_is_search_engine() {
	global $glob;

	$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$spider_flag = false;

	if (($user_agent != '') && (strtolower($user_agent) != 'null') && (strlen(trim($user_agent)) > 0)) {
    		$spiders = file($glob['rootDir'].'/spiders.txt');
          	foreach ($spiders as $spider) {
            	if (($spider != '') && (strtolower($spider) != 'null') && (strlen(trim($spider)) > 0)) {
              		if (strpos($user_agent, trim($spider)) !== false) {
                			$spider_flag = true;
                			break;
				}
              	}
		}
	}

	return $spider_flag;
}

/* <rf> end of mod */

?>
