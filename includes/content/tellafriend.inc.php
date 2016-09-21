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
|	tellafriend.inc.php
|   ========================================
|	Tell a friend about a product	
+--------------------------------------------------------------------------
*/
if (preg_match ("/.inc.php/i",$HTTP_SERVER_VARS['PHP_SELF']) || preg_match ("/.inc.php/i",$_SERVER['PHP_SELF'])) {
	echo "<html>\r\n<head>\r\n<title>Forbidden 403</title>\r\n</head>\r\n<body><h3>Forbidden 403</h3>\r\nThe document you are requesting is forbidden.\r\n</body>\r\n</html>";
	exit;
}

// query database
$_GET['productId'] = treatGet($_GET['productId']) ;
$result = $db->select("SELECT name FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = ".$db->mySQLSafe($_GET['productId'])); 

	if($lang_folder !== $config['defaultLang']){
	
		$foreignVal = $db->select("SELECT name FROM ".$glob['dbprefix']."CubeCart_inv_lang WHERE prod_master_id = ".$db->mySQLSafe($_GET['productId'])." AND prod_lang=".$db->mySQLSafe($lang_folder));
		
		if($foreignVal==TRUE){
		
			$result[0]['name'] = $foreignVal[0]['name'];
			
		}
	
	}
$errorMsg='';
// send email if form is submit
if(isset($_POST['submit'])){

		//$spamCode = fetchSpamCode($_POST['ESC'],TRUE);
		$verif_box = treatGet($_REQUEST["verif_box"]);
		
		// start validation
		//if((!isset($_POST['spamcode']) || ($spamCode['SpamCode']!==$_POST['spamcode']) || ($_SERVER['REMOTE_ADDR']!==$spamCode['userIp'])) AND ($config['floodControl']==1))
		if(!isset($_POST['verif_box']) || $_POST['verif_box']=='' || md5($verif_box).'a4xn' != $_COOKIE['tntcon'])
		{
			$errorMsg = $lang['front']['tellafriend']['error_code'];

		}
		elseif(empty($_POST['senderName']) || empty($_POST['recipName']) )
		{
			$errorMsg = $lang['front']['tellafriend']['error_name'];

		} 
		elseif(validateEmail($_POST['senderEmail'])==FALSE || validateEmail($_POST['recipEmail'])==FALSE)
		{
			$errorMsg = $lang['front']['tellafriend']['error_email'];
		}
		
		if(md5($verif_box).'a4xn' == $_COOKIE['tntcon'] && $errorMsg=='')
		{

			// make email
			include("classes/htmlMimeMail.php");
			
			$mail = new htmlMimeMail();
			
			$text = sprintf($lang['front']['tellafriend']['email_body'],treatGet($_POST['recipName']),stripslashes(treatGet($_POST['message'])),$GLOBALS['storeURL'],treatGet($_GET['productId']),$GLOBALS['storeURL'],$_SERVER['REMOTE_ADDR']);
			
			$mail->setText($text);
			$mail->setReturnPath($_POST['senderEmail']);
			$mail->setFrom($_POST['senderName'].' <'.$_POST['senderEmail'].'>');
			$mail->setSubject(sprintf($lang['front']['tellafriend']['email_subject'],$_POST['senderName']));
			$mail->setHeader('X-Mailer', 'CubeCart Mailer');
			$send = $mail->send(array($_POST['recipEmail']), $config['mailMethod']);
			setcookie('tntcon','');
		}

}

$tellafriend=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/tellafriend.tpl");

	$tellafriend->assign("PRODUCT_ID",$_GET['productId']);
	
	$tellafriend->assign("TAF_TITLE",$lang['front']['tellafriend']['tellafriend']);
	
	if(isset($_POST['submit']) && $errorMsg=='')
	{
	
		$tellafriend->assign("TAF_DESC",sprintf($lang['front']['tellafriend']['message_sent'],$_POST['recipName'],$result[0]['name']));
	
	} 
	elseif(isset($_POST['submit']) && $errorMsg!='') 
	{
	
		$tellafriend->assign("TAF_DESC",sprintf($lang['front']['tellafriend']['fill_out_below'],$result[0]['name']));
	
		if(isset($errorMsg))
		{

			$tellafriend->assign("VAL_ERROR",$errorMsg);
			$tellafriend->parse("tellafriend.error");
	
		}
	
		
	
	}
	
	$tellafriend->assign("TXT_RECIP_NAME",$lang['front']['tellafriend']['friends_name']);
	
	$tellafriend->assign("TXT_RECIP_EMAIL",$lang['front']['tellafriend']['friends_email']);
	
	
	$tellafriend->assign("TXT_SENDER_NAME",$lang['front']['tellafriend']['your_name']);
	
	if(isset($_POST['senderName'])){
		$tellafriend->assign("VAL_SENDER_NAME",$_POST['senderName']);
	}
	
	$tellafriend->assign("TXT_SENDER_EMAIL",$lang['front']['tellafriend']['your_email']);
	
	if(isset($_POST['senderName'])){
		$tellafriend->assign("VAL_SENDER_EMAIL",$_POST['senderEmail']);
	}
	
	$tellafriend->assign("TXT_MESSAGE",$lang['front']['tellafriend']['message']);
	
	if(isset($_POST['message'])){
		$tellafriend->assign("VAL_MESSAGE",stripslashes($_POST['message']));
	} else {
		$tellafriend->assign("VAL_MESSAGE",sprintf($lang['front']['tellafriend']['default_message'],$result[0]['name']));
	}
	
	$tellafriend->assign("TXT_SUBMIT",$lang['front']['tellafriend']['send']);
	
	// Start Spam Bot Control
	//if($config['floodControl']==1) {
		
		$spamCode = strtoupper(randomPass(5));
		$ESC = createSpamCode($spamCode);
		
		$imgSpambot = imgSpambot($ESC);
		
		$tellafriend->assign("VAL_ESC",$ESC);
		$tellafriend->assign("TXT_SPAMBOT",$lang['front']['tellafriend']['spambot']);
		$tellafriend->assign("IMG_SPAMBOT",$imgSpambot);
		$tellafriend->assign("RAND_NUM",rand(0,9999));
		$tellafriend->parse("tellafriend.spambot");
	//}


$tellafriend->parse("tellafriend");
$page_content = $tellafriend->text("tellafriend");
?>