<?php

phpExtension();

//Get Product
$_GET['productId'] = treatGet($_GET['productId']) ;
$result = $db->select("SELECT name, productCode FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = ".$db->mySQLSafe($_GET['productId'])); 
$sent=0;
if($result==true){
	$quote=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/quote.tpl");
	
	$quote->assign("PRODUCT_ID",$_GET['productId']);
	$quote->assign("PRODUCT_NAME",$result[0]['name']);
	$quote->assign("PRODUCT_CODE",$result[0]['productCode']);
	$quote->assign("RAND_NUM",rand(0,9999));
	
	if(empty($_POST['message'])){
		$quote->assign("VAL_MESSAGE","I would like to request a quotation for your product ".$result[0]['name']." (".$result[0]['productCode'].")");
	}
	
	// send email on submit
	if(isset($_POST['submit'])){
			$name=stripslashes($_REQUEST['name']);
			$from=stripslashes($_REQUEST['from']);
			$message=stripslashes($_REQUEST['message']);
			$verif_box = treatGet($_REQUEST["verif_box"]);
			// start validation
			$errors=0;
			if($name=='' || $from=='' || $message==''){
				$errors=1;
			}
			if(md5($verif_box).'a4xn' == $_COOKIE['tntcon'] && $errors==0){
				mail($config['masterEmail'], $config['masterName'].': Quotation Form', "Name: ".$name."\nEmail: ".$from."\nIP: ".$_SERVER['REMOTE_ADDR']."\n\n".$message, "From: $from");
				setcookie('tntcon','');
				$sent=1;
			}else{
				$quote->parse("quote.not_sent.wrong_code");
				if($errors==1){
					$quote->assign("ERROR","Please fill out all fileds");
					$quote->parse("quote.not_sent.errors");
				}
				$quote->assign("VAL_NAME",$name);
				$quote->assign("VAL_EMAIL",$from);
				$quote->assign("VAL_MESSAGE",$message);
			}
	}
}
if($sent==1){
	$quote->parse("quote.sent");
}else{
	$quote->parse("quote.not_sent");
}
$quote->parse("quote");
$page_content = $quote->text("quote");

?>