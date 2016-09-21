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

|	requestPass.inc.php

|   ========================================

|	Request Admin Password

+--------------------------------------------------------------------------

*/

include("../includes/ini.inc.php");

include("../includes/global.inc.php");

include_once("../classes/db.inc.php");

$db = new db();

include_once("../includes/functions.inc.php");

$config = fetchDbConfig("config");

include_once("../language/".$config['defaultLang']."/lang.inc.php");

$enableSSl = 1;

include_once("../includes/sslSwitch.inc.php");



if (isset($_POST['email'])){

	

	$query = sprintf("SELECT adminId, username, name FROM ".$glob['dbprefix']."CubeCart_admin_users WHERE email = %s", $db->mySQLSafe($_POST['email']));

 

	$result = $db->select($query);

	

	

	if($result == TRUE) {

	

		$newPass = randomPass();

		$data['password'] = $db->mySQLSafe(md5($newPass));

		$update = $db->update($glob['dbprefix']."CubeCart_admin_users",$data,"adminId=".$result[0]['adminId']);

		

		// make email

		include("../classes/htmlMimeMail.php");

		

		$mail = new htmlMimeMail();

        

		$text = sprintf($lang['admin']['other']['reset_pass_email'],$result[0]['name'],$result[0]['username'],$newPass,$GLOBALS['storeURL'],$_SERVER['REMOTE_ADDR']);

		

		$mail->setText($text);

		$mail->setReturnPath($_POST['email']);

		$mail->setFrom('Evo Mailer <'.$config['masterEmail'].'>');

		$mail->setSubject('Evo Admin Password');

		$mail->setHeader('X-Mailer', 'Evo Mailer');

		$result = $mail->send(array($_POST['email']), $config['mailMethod']);

		

		header("Location: login.php?email=".urlencode($_POST['email']));

		

	} else {

		$msg = "<p class='warnText'>".$lang['admin']['other']['pass_reset_failed']."</p>";

	}

}

?>

<?php include("includes/header.inc.php"); ?>

<?php if(isset($msg)){ echo stripslashes($msg); } ?>



<form action="<?php echo $GLOBALS['rootRel'];?>admin/requestPass.php" method="post" enctype="multipart/form-data" name="login" target="_self">

<div style="margin: auto; width: 310px; padding-bottom: 10px;"><a href="index.php"><img src="<?php echo $GLOBALS['rootRel']; ?>admin/images/evo/adminLogo.png" alt=""  width="177" height="96" border="0" title="" /></a></div>

<table border="0" align="center" width="284" cellpadding="4" cellspacing="0" class="mainTable">

  <tr>

    <td colspan="2" class="tdTitle"><?php echo $lang['admin']['other']['enter_email_below'];?></td>

    </tr>

  <tr>

    <td class="tdText"><?php echo $lang['admin']['other']['email_address'];?></td>

    <td><input name="email" type="text" id="email" class="textbox" /></td>

  </tr>

  <tr>

    <td>&nbsp;</td>

    <td><input name="login" type="submit" id="login" value="<?php echo $lang['admin']['other']['send_pass'];?>" class="submit" /></td>

  </tr>

</table>

</form>

<?php include("includes/footer.inc.php"); ?>