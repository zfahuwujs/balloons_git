<?php
/*
+--------------------------------------------------------------------------
|   Contact Form
|   ========================================
|   by Sean
|   Aw yeah!
+--------------------------------------------------------------------------
|	contact.inc.php
|   ========================================
|	Displays a contact form	
+--------------------------------------------------------------------------
*/
// query database

phpExtension();

$to=$config['masterEmail'];

$contact=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/contact.tpl");

if(!isset($_POST['Submit'])){

	$ContactForm=$db->select("SELECT * FROM CubeCart_contact WHERE type = 1");

	$contact->assign("RAND_NUM",rand(0,9999));
	$contact->assign("DOC_NAME",$ContactForm[0]['doc_name']);
	$contact->assign("DOC_CONTENT",$ContactForm[0]['doc_content']);
	$contact->parse("contact_form.the_form");

}else{

	$contact->assign("RAND_NUM",rand(0,9999));

	$ContactForm=$db->select("SELECT * FROM CubeCart_contact WHERE type = 2");
	$contact->assign("DOC_NAME",$ContactForm[0]['doc_name']);
	$contact->assign("DOC_CONTENT",$ContactForm[0]['doc_content']);

	// load the variables form address bar
	$subject = treatGet($_POST["subject"]);
	$message = treatGet($_REQUEST["message"]);
	$from = treatGet($_REQUEST["from"]);
	$verif_box = treatGet($_REQUEST["verif_box"]);

	// remove the backslashes that normally appears when entering " or '
	$message = stripslashes($message); 
	$subject = stripslashes($subject); 
	$from = stripslashes($from); 
	
	// check to see if verificaton code was correct
	if(md5($verif_box).'a4xn' == $_COOKIE['tntcon']){
		// if verification code was correct send the message and show this page
		mail($to, 'Online Form: '.$subject, $_SERVER['REMOTE_ADDR']."\n\n".$message, "From: $from");
		// delete the cookie so it cannot sent again by refreshing this page
		setcookie('tntcon','');
	} else {
		// if verification code was incorrect then return to contact page and show error

		$contact->parse("contact_form.the_form.wrong_code");
		$contact->assign("VAL_SUBJECT",$subject);
		$contact->assign("VAL_EMAIL",$from);
		$contact->assign("VAL_MESSAGE",$message);
		$contact->parse("contact_form.the_form");

	}

}

$contact->parse("contact_form");
$page_content = $contact->text("contact_form");

?>