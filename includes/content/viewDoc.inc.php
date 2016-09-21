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
|	viewDoc.inc.php
|   ========================================
|	Displays a site document	
+--------------------------------------------------------------------------
*/
// query database

phpExtension();

$_GET['docId'] = treatGet($_GET['docId']);

if($_GET['docId']==7){
	header("Location: /");
	exit;
}


if($lang_folder !== $config['defaultLang']){

$result = $db->select("SELECT doc_master_id AS doc_id, doc_name, doc_content FROM ".$glob['dbprefix']."CubeCart_docs_lang WHERE doc_master_id = ".$db->mySQLSafe($_GET['docId'])." AND doc_lang=".$db->mySQLSafe($lang_folder));

	/* <rf> search engine friendly mod */
	if($config['seftags']) {
		// get metas for the docs
		$sefresult = $db->select("SELECT doc_metatitle, doc_metadesc, doc_metakeywords FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = ".$db->mySQLSafe($_GET['docId'])); 
		$result['sefSiteTitle'] = $sefresult['sefSiteTitle'];
		$result['sefSiteDesc'] = $sefresult['sefSiteDesc'];
		$result['sefSiteKeywords'] = $sefresult['sefSiteKeywords'];
	} 
	/* <rf> end mod */
}

if(!isset($result) || $result==FALSE) {
/* <rf> search engine friendly mod */
if($config['seftags']) {
	$result = $db->select("SELECT doc_id, doc_metatitle, doc_metadesc, doc_metakeywords, doc_name, doc_content FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = ".$db->mySQLSafe($_GET['docId'])); 
} else {
	$result = $db->select("SELECT doc_id, doc_name, doc_content FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = ".$db->mySQLSafe($_GET['docId'])); 
}
/* <rf> end mod */
}

$view_doc=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/viewDoc.tpl");

if(isset($result) && $result == TRUE){
	///////////
	///404 test
	///////////
	
	if($config['seftags'] && $_SERVER['REQUEST_URI'] != $glob["rootRel"].generateDocumentUrl($result[0]["doc_id"])){
			header("Location: ".$glob["rootRel"].generateDocumentUrl($result[0]["doc_id"]));
			exit;
	}
	
	$view_doc->assign("DOC_NAME",validHTML($result[0]['doc_name']));
	
	$view_doc->assign("DOC_CONTENT",$result[0]['doc_content']);
	
	if($_GET['docId'] != 2){
		$view_doc->parse("view_doc.content");
	}
	
	/* <rf> search engine friendly mod */
	if($config['seftags']) {
			$robz = trim(strip_tags(str_replace("\r\n", "", $result[0]['doc_content'])));
			$count = 0;
			$output = "";
			
			for($i=0; $i<strlen($robz); $i++){
				$temp = substr($robz, $i, 1);
				
				if($temp == " "){
					if($count == 0){
						$output .= $temp;
						$count++;
					}
				}else{
					$output .= $temp;
					$count = 0;
				}
			}
			
		$meta['metaDescription'] = substr($output, 0, 156);
		$meta['siteTitle'] = $result[0]['doc_name'];		
		//$meta['metaDescription'] = substr(strip_tags($result[0]['doc_content']),0,155);
		$meta['sefSiteTitle'] = $result[0]['doc_metatitle']; 
		$meta['sefSiteDesc'] = $result[0]['doc_metadesc'];
		$meta['sefSiteKeywords'] = $result[0]['doc_metakeywords'];
	} else {
		$meta['siteTitle'] = $config['siteTitle']." - ".$result[0]['doc_name'];
		$robz = trim(strip_tags(str_replace("\r\n", "", $result[0]['doc_content'])));
			$count = 0;
			$output = "";
			
			for($i=0; $i<strlen($robz); $i++){
				$temp = substr($robz, $i, 1);
				
				if($temp == " "){
					if($count == 0){
						$output .= $temp;
						$count++;
					}
				}else{
					$output .= $temp;
					$count = 0;
				}
			}
			
		$meta['metaDescription'] = substr($output, 0, 156);
	}
	/* <rf> end mod */	
} else {
	
	$meta['sefSiteTitle'] = " "; 
	$meta['sefSiteDesc'] = " "; 
	$meta['sefSiteKeywords'] = " "; 
	
	header("Location: /notfound");
	exit;
	
	$view_doc->assign("DOC_NAME",$lang['front']['viewDoc']['error']);
	$view_doc->assign("DOC_CONTENT",$lang['front']['viewDoc']['does_not_exist']);

}

if($_GET['docId'] == 2){
	
	$view_doc->assign("FB_LINK",$config['facebook']);
	$view_doc->assign("TWITER_LINK",$config['twitter']);
	
	if(evoHideBol(105)==false){
		for($d=1; $d<=$config['numOfDeps']; $d++){
			$view_doc->assign("DEP_NAME",$config['contactName'.$d]);
			$view_doc->assign("DEP_EMAIL",$config['contactEmail'.$d]);
			$view_doc->parse("view_doc.the_form.department.departments");
		}
		$view_doc->parse("view_doc.the_form.department");
	}

	if(!isset($_POST['Submit'])){

		$ContactForm=$db->select("SELECT * FROM CubeCart_docs WHERE doc_id = 2");
		
		$view_doc->assign("RAND_NUM",rand(0,9999));
		$view_doc->assign("DOC_NAME",$ContactForm[0]['doc_name']);
		$view_doc->assign("DOC_CONTENT",$ContactForm[0]['doc_content']);
		$view_doc->parse("view_doc.the_form");
	
	}else{
	
		$view_doc->assign("RAND_NUM",rand(0,9999));
	
		// load the variables form address bar
		$subject = treatGet($_POST["subject"]);
		$message = treatGet($_REQUEST["message"]);
		$from = treatGet($_REQUEST["from"]);
		$verif_box = treatGet($_REQUEST["verif_box"]);
		$name = treatGet($_REQUEST["name"]);
		$phone = treatGet($_REQUEST["phone"]);
		
		if(evoHideBol(105)==false){
			$department = treatGet($_REQUEST["department"]);
			$department = stripslashes($department);
		}else{
			$department='empty';
		}
	
		// remove the backslashes that normally appears when entering " or '
		$message = stripslashes($message); 
		$subject = stripslashes($subject); 
		$from = stripslashes($from); 
		$name = stripslashes($name);
		$phone = stripslashes($phone);
		
		$error='';
		if($from=='' || $name=='' || $subject=='' || $message=='' || $department==''){
			$error.='<p class="txtError">Please fill in all fields</p>';
		}elseif(validateEmail($from)==FALSE){
			$error.='<p class="txtError">Invalid email address entered</p>';
		}
		
		// check to see if verificaton code was correct
		if(md5($verif_box).'a4xn' == $_COOKIE['tntcon'] && $error==''){
			// if verification code was correct send the message and show this page
			
			$ContactForm=$db->select("SELECT * FROM CubeCart_docs WHERE doc_id = 6");
			$view_doc->assign("DOC_NAME",$ContactForm[0]['doc_name']);
			$view_doc->assign("DOC_CONTENT",$ContactForm[0]['doc_content']);
			
			//mail($config['masterEmail'], $config['masterName'].' Online Form: '.$subject, "\n Name: ".$name."\n Email: ".$from."\n IP: ".$_SERVER['REMOTE_ADDR']."\n\n".$message, "From: $from");
			// delete the cookie so it cannot sent again by refreshing this page
			include_once("classes/htmlMimeMail.php");
			$adminmail = new htmlMimeMail();
			$customermail = new htmlMimeMail();
			
			$adminmail->setHtml(nl2br("<b>Name:</b> ".$name."<br /><b>Email:</b> ".$from."<br /><b>Contact Number:</b> ".$phone."<br /><br />".$message."<hr/>".$_SERVER['REMOTE_ADDR']));
			$adminmail->setReturnPath($from);
			$adminmail->setFrom($from.' <'.$from.'>');
			$adminmail->setSubject($subject);
			$adminmail->setHeader('X-Mailer', 'CubeCart Mailer');
			if(evoHideBol(105)==false){
				$adminsend = $adminmail->send(array($department), $config['mailMethod']);
			}else{
				$adminsend = $adminmail->send(array($config['masterEmail']), $config['mailMethod']);
			}
			
			if($config['contactFormConfirmation']==1){
				$customermail->setHtml(nl2br("Your message to ".$config['storeName']." has been sent successfully. \n\n".$message."<hr/> Your IP Address: ".$_SERVER['REMOTE_ADDR']));
				if(evoHideBol(105)==false){
					$customermail->setReturnPath($department);
					$customermail->setFrom($config['masterName'].' <'.$department.'>');
				}else{
					$customermail->setReturnPath($config['masterEmail']);
					$customermail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
				}
				$customermail->setSubject($subject);
				$customermail->setHeader('X-Mailer', 'CubeCart Mailer');
				$customermailsend = $customermail->send(array($from), $config['mailMethod']);
			}

			setcookie('tntcon','');
		} else {
			// if verification code was incorrect then return to contact page and show error
			
			$ContactForm=$db->select("SELECT * FROM CubeCart_docs WHERE doc_id = 2");
			$view_doc->assign("DOC_NAME",$ContactForm[0]['doc_name']);
			$view_doc->assign("DOC_CONTENT",$ContactForm[0]['doc_content']);
	
			$view_doc->parse("view_doc.the_form.wrong_code");
			$view_doc->assign("ERROR",$error);
			$view_doc->assign("VAL_SUBJECT",$subject);
			$view_doc->assign("VAL_EMAIL",$from);
			$view_doc->assign("VAL_MESSAGE",$message);
			$view_doc->assign("VAL_NAME",$name);
			$view_doc->assign("VAL_PHONE",$phone);
			$view_doc->parse("view_doc.the_form");
		}

	}
}

if($_GET['docId'] == 5){
	$view_doc->assign("404",
		'<script type="text/javascript">
  			var GOOG_FIXURL_LANG = "en-GB";
  			var GOOG_FIXURL_SITE = "'.$glob['storeURL'].'";
		</script>
		<script type="text/javascript" src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js">
</script>');
}

$view_doc->parse("view_doc");
$page_content = $view_doc->text("view_doc");
?>