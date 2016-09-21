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

|	email.php

|   ========================================

|	Email Customers

+--------------------------------------------------------------------------

*/

include("../../includes/ini.inc.php");

include("../../includes/global.inc.php");

require_once("../../classes/db.inc.php");

$db = new db();

include_once("../../includes/functions.inc.php");

$config = fetchDbConfig("config");



include_once("../../language/".$config['defaultLang']."/lang.inc.php");

$enableSSl = 1;

include_once("../../includes/sslSwitch.inc.php");

include("../includes/auth.inc.php");



if(permission("customers","write")==FALSE){

	header("Location: ".$GLOBALS['rootRel']."admin/401.php");

	exit;

}



if(isset($_GET['action']) && $_GET['action']=="download"){


	$query = "SELECT title, email, firstName, lastName, type FROM ".$glob['dbprefix']."CubeCart_customer WHERE optIn1st = 1";

	$results = $db->select($query);



	if($results==TRUE) {



		$emailList = "";

		

		for($i=0; $i<count($results); $i++){



			if($_POST['incName']==1 && $results[$i]['type']==1){

				$emailList .=  $results[$i]['title']." ".$results[$i]['firstName']." ".$results[$i]['lastName']." <".$results[$i]['email'].">";

			} else {

				$emailList .=  $results[$i]['email'];

			}

		

			$emailList .=  "\r\n";

		}



	$filename="CustomerEmails_".date("dMy").".txt";

	header('Pragma: private');

	header('Cache-control: private, must-revalidate');

	header("Content-Disposition: attachment; filename=".$filename);

	header("Content-type: text/plain");

	header("Content-type: application/octet-stream");

	header("Content-length: ".strlen($emailList));

	header("Content-Transfer-Encoding: binary");

	echo $emailList;

	exit;

		

	} else {

		$msg = $lang['admin']['customers']['no_download_email'];

	}

exit;

}



include("../includes/header.inc.php");

?>
<script language="javascript" src="../../js/jquery-1.3.2.min.js" type="text/javascript"></script>
<p class="pageTitle">Mail Customers</p>
<?php if(isset($_GET['action']) && $_GET['action']=="send"){ ?>
<script type="text/javascript">
	
	function validateFields(){
		if($('#subject').val() == ""){
			alert('Please fill in the subject of the email');
			return false;
		}else if($('#fromName').val() == ""){
			alert('Please fill in the senders name of the email.  For example: Store Name Newsletter.');
			return false;
		}else if($('#fromEmail').val() == ""){
			alert('Please fill in the email address you wish to send the email from.');
			return false;
		}else if($('#returnPath').val() == ""){
			alert('Please fill in the return email address for bounced emails.  This is to allow you to check customers who invalid email addresses entered.');
			return false;
		}else if($('#test').attr('checked') == true){
			if($('#testEmail').val() == ""){
				alert('Please fill in the test email address you wish to send the email to.');
				return false;
			}
		}else{
			return true;
		}
	}
	

</script>

<form id="form1" name="form1" method="post" onsubmit="return validateFields()" action="<?php echo $GLOBALS['rootRel'];?>admin/customers/send.php" target="_self" enctype="multipart/form-data">
  <table width="100%"  border="0" cellspacing="0" cellpadding="3" class="mainTable">
    <tr>
      <td colspan="2" class="tdTitle"><?php echo $lang['admin']['customers']['create_email']; ?></td>
    </tr>
    <tr>
      <td colspan="2" class="tdRichText"><?php

	include("../includes/rte/fckeditor.php");

	$oFCKeditor = new FCKeditor('FCKeditor') ;

	$oFCKeditor->BasePath = $GLOBALS['rootRel'].'admin/includes/rte/';

		

		
		if(isset($_POST['FCKeditor'])){

			$oFCKeditor->Value = stripslashes($_POST['FCKeditor']);

		}else{

			if(isset($_POST['Template']) && $_POST['Template']!='No Template'){
				$Newsletter=$db->select("SELECT mail FROM " . $glob['dbprefix'] . "CubeCart_mail_templates WHERE id = {$_POST['Template']}");
				$oFCKeditor->Value = $Newsletter[0]['mail'];
			}else{
				$oFCKeditor->Value = '';
			}

		}

	

	$oFCKeditor->Create();

	?>
      </td>
    </tr>
    <tr>
      <td class="tdRichText"><span class="tdText"><em><strong><?php echo $lang['admin']['customers']['hint']; ?></strong> </em></span></td>
      <td class="tdRichText"><span class="tdText"><em><?php echo $lang['admin']['customers']['click_source']; ?></em></span></td>
    </tr>
    <tr>
      <td valign="top" class="tdRichText"><span class="tdText"><em><strong><?php echo $lang['admin']['customers']['important']; ?></strong></em></span></td>
      <td class="tdRichText"><span class="tdText"><em><?php echo $lang['admin']['customers']['absolute_links']; ?></em></span>
        <input name="unsubscribe" type="text" class="textbox" value="<?php echo $GLOBALS['storeURL']."/index.php?act=unsubscribe"; ?>" size="30" />
        <br /><span class="tdText"><em>Please also ensure you complete all fields before sending the email.  Please note:  If you have 'Send Test Email?' set no you do not have to enter in a 'Test Email Recipient'.</em></span>
      </td>
    </tr>
    <? if(evoHideBol(101)==false){ ?>
    <tr>
      <td class="tdText"><strong>Groups to email:</strong></td>
      <td class="tdText">
      <?php $emailGroups = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_mail_group ORDER BY groupName'); 
	  if($emailGroups==true){
		  for($i = 0; $i < count($emailGroups); $i++){ ?>
	    <input name="mailgroup[]" type="checkbox" id="mailgroup[]" value="<?php echo $emailGroups[$i]['groupId']; ?>" />
			  <label for="mailgroup[]"><?php echo $emailGroups[$i]['groupName']; ?></label>
<?  }
	  }
	  ?>
      </td>
    </tr>
    <?php } ?>
    <tr>
      <td width="110" class="tdText"><strong><?php echo $lang['admin']['customers']['email_subject']; ?></strong> </td>
      <td class="tdText"><input name="subject" type="text" id="subject" class="textbox" value="<?php if(isset($_POST['subject'])) echo stripslashes($_POST['subject']); ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['customers']['senders_name']; ?></strong></td>
      <td class="tdText"><input name="fromName" type="text" class="textbox" id="fromName" value="<?php if(isset($_POST['fromName'])) echo 

	stripslashes($_POST['fromName']); ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['customers']['senders_email']; ?></strong></td>
      <td class="tdText"><input name="fromEmail" type="text" class="textbox" id="fromEmail" value="<?php if(isset($_POST['fromEmail'])) echo $_POST['fromEmail']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['customers']['return_path']; ?></strong></td>
      <td class="tdText"><input name="returnPath" type="text" class="textbox" id="returnPath" value="<?php if(isset($_POST['returnPath'])) echo $_POST['returnPath']; ?>" />
        <?php echo $lang['admin']['customers']['bounce_desc']; ?></td>
    </tr>
    <tr>
      <td width="110" class="tdText"><strong><?php echo $lang['admin']['customers']['send_test']; ?></strong></td>
      <td class="tdText"><?php echo $lang['admin']['yes']; ?>
        <input id="test" name="test" type="radio" value="1" <?php if(isset($_POST['test']) && $_POST['test']=="1") echo "checked='checked'"; elseif(!isset($_POST['test'])) echo "checked='checked'"; ?> />
        <?php echo $lang['admin']['no']; ?>
        <input name="test" type="radio" value="0" <?php if(isset($_POST['test']) && $_POST['test'] =="0") echo "checked='checked'"; ?> />
        <strong><?php echo $lang['admin']['customers']['test_email_recip']; ?></strong>
        <input name="testEmail" type="text" id="testEmail" value="<?php if(isset($_POST['testEmail'])) echo $_POST['testEmail']; else echo $config['masterEmail']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText">&nbsp;</td>
      <td class="tdText"><input type="submit" value="<?php echo $lang['admin']['customers']['send_email']; ?>" class="submit" /></td>
    </tr>
  </table>
</form>
<?php } else { ?>
<?php if(isset($msg)) { ?>
<p class="warnText"><?php echo stripslashes($msg); ?></p>
<?php } ?>
<p class="copyText"><?php echo $lang['admin']['customers']['download_or_send']; ?></p>
<table width="450" border="0" align="center" cellpadding="4" cellspacing="0" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php echo $lang['admin']['customers']['please_choose']; ?></td>
  </tr>
  <tr>
    <td width="50%" valign="top" class="copyText"><?php echo $lang['admin']['customers']['used_to_download']; ?></td>
    <td width="50%" valign="top" class="copyText"><?php echo $lang['admin']['customers']['bulk_to_subscribed']; ?></td>
  </tr>
  <tr align="center">
    <td valign="bottom" class="copyText"><form name="download" method="post" action="<?php echo $GLOBALS['rootRel'];?>admin/customers/email.php?action=download">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td><?php echo $lang['admin']['customers']['include_name']; ?></td>
            <td><?php echo $lang['admin']['yes']; ?>
              <input name="incName" type="radio" value="1" checked='checked' />
              <?php echo $lang['admin']['no']; ?>
              <input name="incName" type="radio" value="0" /></td>
          </tr>
          <tr align="center">
            <td height="30" colspan="2"><input name="download" type="submit" id="download" value="<?php echo $lang['admin']['customers']['download_email']; ?>" class="submit" /></td>
          </tr>
        </table>
      </form></td>
    <td valign="bottom" class="copyText"><form name="download" method="post" action="<?php echo $GLOBALS['rootRel'];?>admin/customers/email.php?action=send" enctype="multipart/form-data">
        <?php

			$Templates=$db->select("SELECT * FROM " . $glob['dbprefix'] . "CubeCart_mail_templates");
		
			if($Templates){
				echo '<div style="height:35px;">Template: <select name="Template" id="Template"><option value="No Template">No Template</option>';
				for($x=0;$x<count($Templates);$x++){
					echo '<option value="'.$Templates[$x]['id'].'">'.$Templates[$x]['name'].'</option>';
				}
				echo '</select></div>';
			}
		
		?>
        <input name="send" type="submit" id="send" value="<?php echo $lang['admin']['customers']['send_email'];?>" class="submit" />
      </form></td>
  </tr>
</table>
<table width="450" border="0" align="center" cellpadding="4" cellspacing="0" class="mainTable">
  <tr>
    <td align="center" colspan="2" class="tdTitle">NOTICE</td>
  </tr>
  <tr>
    <td valign="center" align="center" class="copyText">Please note that this mailer is not designed for bulk emails.  If you need to send an email / newsletter to more than 1000 customers at once then please contact your account manager and we will be happy to assist.  Alternatively you can download your entire list to use in a bulk emailer program of your choice.</td>
  </tr>
</table>
<?php } ?>
<?php include("../includes/footer.inc.php"); ?>
