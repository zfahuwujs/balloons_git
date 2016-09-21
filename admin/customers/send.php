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

|	send.php

|   ========================================

|	Send Bulk Email

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



include("../includes/header.inc.php");



// number of email recipients per page

$perPage = 20;



if($_POST['test']==0){
	if(evoHideBol(101)==false){
		if(isset($_POST['mailgroup'])){
			$groupWhere = '';
			$formFields = '';
			for($g = 0; $g < count($_POST['mailgroup']); $g++){
				if($_POST['mailgroup'][$g]!=''){
					$groupWhere .= $db->mySQLSafe($_POST['mailgroup'][$g]).',';
					$formFields .= '<input name="mailgroup[]" type="hidden" value="'.$_POST['mailgroup'][$g].'" />';
				}
			}
			$groupWhere = ' AND groupId IN ('.substr($groupWhere, 0, -1).')';
		}
	}else{
		$groupWhere='';
	}

	$query = "SELECT email, firstName, lastName, htmlEmail FROM ".$glob['dbprefix']."CubeCart_customer WHERE optIn1st = 1".$groupWhere;

	$emailList = $db->select($query, $perPage, $_GET['page']);

}

?>



<div id="sending"  class="pageTitle">

<?php echo $lang['admin']['customers']['sending']; ?> <img src="../images/progress.gif" alt="" width="9" height="15" title="" /></div>

<div id="sent" class="pageTitle" style="visibility:hidden;"><?php echo $lang['admin']['customers']['sending_complete']; ?></div>

<?php

// start email

include("../../classes/htmlMimeMail.php");

		

		// subsequent pages are encoded so we need to decode

		$html = stripslashes($_POST['FCKeditor']);
		if($_POST['test']==0){
			$mailRecord['mailHtml'] = $db->mySQLSafe($html);
			$mailRecord['mailTime'] = $db->mySQLSafe(time());
			$insertMail = $db->insert($glob['dbprefix'].'CubeCart_mail_archive',$mailRecord);
			$newsID = $db->insertid();
			
			unset($mailRecord,$insertMail);
		}
		$subject = $_POST['subject'];

		$fromName = $_POST['fromName'];

		$fromEmail = $_POST['fromEmail'];

		$returnPath = $_POST['returnPath'];
		
		if($_POST['test']==0){
			if(isset($html)){
				if(preg_match('/\*\|ARCHIVE\|\*/i',$html) && isset($newsID)){
					$html = preg_replace('/\*\|ARCHIVE\|\*/i', $glob['storeURL'].'/newsletter.php?newsId='.$newsID, $html);
					$mailRecord['mailHtml'] = $db->mySQLSafe($html);
					$mailWhere = 'mailId = '.$db->mySQLSafe($newsID);
					$insertMail = $db->update($glob['dbprefix'].'CubeCart_mail_archive',$mailRecord,$mailWhere);
					unset($mailRecord,$insertMail);
				}
			}
		}

		$text = "";

		$text = str_replace("<br />","\r\n",$html);

		$text = str_replace("<p>","",$html);

		$text = str_replace("</p>","\r\n\r\n",$html);

		$text = strip_tags($html);

		

		if($_POST['test']==1){

		

			$mail = new htmlMimeMail();

			$mail->setSubject($subject);

			$mail->setHeader('X-Mailer', 'CubeCart Bulk Mailer');

			$mail->setFrom($fromName." <".$fromEmail.">");

			$mail->setReturnPath($returnPath);

			$mail->setHtml($html);

			$result = $mail->send(array($_POST['testEmail']), $config['mailMethod']);

			

			/*$mail = new htmlMimeMail();

			$mail->setSubject($subject);

			$mail->setHeader('X-Mailer', 'CubeCart Bulk Mailer');

			$mail->setFrom($fromName." <".$fromEmail.">");

			$mail->setReturnPath($returnPath);

			$mail->setText($text);*/

			//$result = $mail->send(array($_POST['testEmail']), $config['mailMethod']);

			

			echo "<p class='copyText'><strong>".$lang['admin']['customers']['recipient']."</strong> ".$_POST['testEmail']."</p>";

			?>

			<img src="../images/progress.gif" alt="" width="1" height="1" title="" onload="MM_showHideLayers('sending','','hide','sent','','show');" />

			<form method="post" action="<?php echo $GLOBALS['rootRel'];?>admin/customers/email.php?action=send" enctype="multipart/form-data">

			<?php

			// recover post vars

			echo recoverPostVars($_POST,"FCKeditor");

			?>

			<input name="submit" type="submit" id="submit" value="<?php echo $lang['admin']['customers']['prev_page']; ?>" class="submit" />

			</form>

			<?php

			



		} else {

		

			$i = 0;

			

			if(isset($_GET['startTime'])) $startTime = $_GET['startTime']; else $startTime = $_GET['startTime'] = time();

			

			if($emailList == TRUE){

			

				echo "<table border='0' cellspacing='0' cellpadding='3' class='mainTable'>";

				print "<tr><td class='tdTitle' colspan='3'>".$lang['admin']['customers']['page']." ".($_GET['page']+1)."</td></tr>";

			

				for ($i=0; $i<count($emailList); $i++){

					

					$cellColor = "";

					$cellColor = cellColor($i);

					

						

					$mail = new htmlMimeMail();

					$mail->setSubject($subject);

					$mail->setHeader('X-Mailer', 'CubeCart Bulk Mailer');

					$mail->setFrom($fromName." <".$fromEmail.">");

					$mail->setReturnPath($returnPath);

						

						if($emailList[$i]['htmlEmail']==1) {

							

							$mail->setHtml($html);

						

						} else {

							

							$mail->setText($text);

						

						}

					

					$result = $mail->send(array($emailList[$i]['email']), $config['mailMethod']);

					

					$recipNo = $_GET['emailed']+($i+1);

					

					echo "<tr><td class='".$cellColor."'><span class='copyText'>".($recipNo).".</span></td><td class='".$cellColor."'><span class='copyText'>".$emailList[$i]['firstName']." ".$emailList[$i]['lastName']."</span></td><td class='".$cellColor."'><span class='copyText'> &lt;".$emailList[$i]['email']."&gt;</span></td></tr>\r\n";

				}

			

		echo "</table>"; 

		

		?>

		<form method="post" name="autoSubmitForm" action="<?php echo $GLOBALS['rootRel'];?>admin/customers/send.php?page=<?php echo $_GET['page']+1; ?>&amp;startTime=<?php echo $startTime; ?>&amp;emailed=<?php echo $recipNo;?>" enctype="multipart/form-data">

		<?php
		


		echo recoverPostVars($_POST,"FCKeditor");
		if(evoHideBol(101)==false){
			if(isset($_POST['mailgroup'])){
				$formFields = '';
				for($g = 0; $g < count($_POST['mailgroup']); $g++){
					if($_POST['mailgroup'][$g]!=''){
						echo '<input name="mailgroup[]" type="hidden" value="'.$_POST['mailgroup'][$g].'" />';
					}
				}
			}
		}
		
		?>

		<img src="../images/px.gif" alt="" width="1" height="1" title="" onload="submitDoc('autoSubmitForm');" />

		</form>

		<?php

		} else {

		?>

		<p class="infoText"><?php echo $lang['admin']['customers']['bulk_finished'];?></p>

	

		<img src="../images/px.gif" alt="" width="1" height="1" title="" onload="MM_showHideLayers('sending','','hide','sent','','show');" />

		<?php

		} // else



?>

<p class="copyText">

	<strong><?php echo $lang['admin']['customers']['time_taken'];?></strong> <?php echo readableSeconds(time() - $startTime); ?><br/>

	<strong><?php echo $lang['admin']['customers']['recipients'];?></strong> <?php echo $_GET['emailed'] + $i; ?>

</p>

<?php

}

?>



<?php include("../includes/footer.inc.php"); ?>