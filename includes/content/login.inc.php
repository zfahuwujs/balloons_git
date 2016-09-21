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
|	login.inc.php
|   ========================================
|	Start the session	
+--------------------------------------------------------------------------
*/
phpExtension();

$_GET['act'] = treatGet($_GET['act']); 

if($_GET['act']=="login" && isset($_POST['username']) && isset($_POST['password'])){
	$_POST['username'] = treatGet($_POST['username']);
	$_POST['password'] = treatGet($_POST['password']);

	if($config['usernameType']==1){
		$usernameType='nickname';
	}else{
		$usernameType='email';
	}
	$query = "SELECT customer_id FROM ".$glob['dbprefix']."CubeCart_customer WHERE ".$usernameType."=".$db->mySQLSafe($_POST['username'])." AND password = ".$db->mySQLSafe(md5($_POST['password']))." AND type>0";
	$customer = $db->select($query);
	$evoAccountBlock=0;
	if(($customer[0]['customer_id']==3 || $customer[0]['customer_id']==4) && evo==false){
		$evoAccountBlock=1;
	}
	if($customer==FALSE) {

		if($db->blocker($_POST['username'],$ini['bfattempts'],$ini['bftime'],FALSE,"f")==TRUE)
		{
			$blocked = TRUE; 	
		}
	} elseif($customer[0]['customer_id']>0 && $evoAccountBlock==0) {
		if($db->blocker($_POST['username'],$ini['bfattempts'],$ini['bftime'],TRUE,"f")==TRUE){
			$blocked = TRUE; 
		}else{
			$customerData["customer_id"] = $customer[0]['customer_id'];
			$update = $db->update($glob['dbprefix']."CubeCart_sessions", $customerData,"sessId=".$db->mySQLSafe($_SESSION['ccUser']));
			$_POST['remember'] = treatGet($_POST['remember']);
			if($_POST['remember']==1){
				setcookie("ccRemember","1",time()+$config['sqlSessionExpiry'], $GLOBALS['rootRel']);
			}
			//Set cookie to remember username
			$timeAppend = 60*60*24*365;
			$cookieExpiry = time()+$timeAppend;
			
			$customerName = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_customer WHERE customer_id = ".$db->mySQLSafe($customer[0]['customer_id']));
			if ($customerName[0]['firstName'] != '' && $customerName[0]['firstName'] != NULL) {
				if ($customerName[0]['lastName'] != '' && $customerName[0]['lastName'] != NULL) {
					$name = $customerName[0]['firstName'].' '.$customerName[0]['lastName'];
				} else $name = $customerName[0]['firstName'];
			} else if ($customerName[0]['lastName'] != '' && $customerName[0]['lastName'] != NULL) {
				if ($customerName[0]['title'] != '' && $customerName[0]['title'] != NULL) {
					$name = $customerName[0]['title'].' '.$customerName[0]['lastName'];
				}
			} else $name = $customerName[0]['email'];
			
			setcookie('returnVisitorName', $name, $cookieExpiry, '/');
			// redirect
			// "login","reg","unsubscribe","forgotPass"
			if(isset($_GET['redir']) && !empty($_GET['redir']) && !eregi("logout|login|forgotPass|changePass",base64_decode($_GET['redir']))){
				header("Location: ".str_replace("amp;","",treatGet(base64_decode($_GET['redir']))));
				exit;
			} else {
				header("Location: ".$GLOBALS['rootRel']."index.php");
				exit;
			}
		}
	}elseif(eregi("step1",base64_decode($_GET['redir']))){
		header("Location: ".$GLOBALS['rootRel']."cart.php?act=step1");
		exit;	
	} 
}


$login = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/login.tpl");


$login->assign("LANG_LOGIN_TITLE",$lang['front']['login']['login']);
$login->assign("VAL_SELF",treatGet($_GET['redir']));
if($config['usernameType']==1){
	$login->assign("LANG_USERNAME","Username:");
}else{
	$login->assign("LANG_USERNAME","Email:");
}

if(isset($_POST['username'])){
	$login->assign("VAL_USERNAME",$_POST['username']);
}

$login->assign("LANG_PASSWORD",$lang['front']['login']['password']);
$login->assign("LANG_REMEMBER",$lang['front']['login']['remember_me']);
$login->assign("TXT_LOGIN",$lang['front']['login']['login']);
$login->assign("LANG_FORGOT_PASS",$lang['front']['login']['forgot_pass']);

if(isset($_POST['remember']) && $_POST['remember']==1) $login->assign("CHECKBOX_STATUS","checked='checked'");

if($ccUserData[0]['customer_id'] > 0 &&  isset($_POST['submit'])){
	$login->assign("LOGIN_STATUS",$lang['front']['login']['login_success']);
} elseif($ccUserData[0]['customer_id'] > 0 &&  !isset($_POST['submit'])) {
	$login->assign("LOGIN_STATUS",$lang['front']['login']['already_logged_in']);
} elseif($ccUserData[0]['customer_id'] == 0 && isset($_POST['submit'])) {
	if($blocked == TRUE){
		$login->assign("LOGIN_STATUS",sprintf($lang['front']['login']['blocked'],sprintf("%.0f",$ini['bftime']/60)));
	}else{
		$login->assign("LOGIN_STATUS",$lang['front']['login']['login_failed']);
	}
	$login->parse("login.form");
} else {
	$login->assign("LOGIN_STATUS",$lang['front']['login']['login_below']);
	$login->parse("login.form");
}


$login->parse("login");
$page_content = $login->text("login");
?>