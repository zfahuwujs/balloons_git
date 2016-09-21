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
|	changePass.php
|   ========================================
|	Change Admin Password
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

if (isset($_POST['oldPass']) && isset($_POST['newPass']) && isset($_POST['confirmPass'])){
	
	$query = sprintf("SELECT adminId FROM ".$glob['dbprefix']."CubeCart_admin_users WHERE password = %s AND adminId = %s", 
						$db->mySQLSafe(md5($_POST['oldPass'])),
						$db->mySQLSafe($_SESSION['ccAdmin']));
 
	$result = $db->select($query);
	
	
	if($result == TRUE) {
	
		$data['password'] = $db->mySQLSafe(md5($_POST['newPass']));
		$update = $db->update($glob['dbprefix']."CubeCart_admin_users",$data,"adminId=".$result[0]['adminId']);
		
		$msg = "<p class='infoText'>".$lang['admin']['adminusers']['pass_updated']."</p>";
		
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['adminusers']['pass_not_updated']."</p>";
	}
}
?>
<?php include("../includes/header.inc.php"); ?>
<?php if(isset($msg)){ echo stripslashes($msg); } ?>

<form action="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/changePass.php" method="post" enctype="multipart/form-data" name="login" target="_self">
<table border="0" align="center" cellpadding="4" cellspacing="0" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php echo $lang['admin']['adminusers']['change_pass_below']; ?></td>
    </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['adminusers']['old_pass']; ?></td>
    <td><input name="oldPass" type="password" id="oldPass" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['adminusers']['new_pass']; ?></td>
    <td><input name="newPass" type="password" id="newPass" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['adminusers']['confirm_pass']; ?></td>
    <td><input name="confirmPass" type="password" id="confirmPass" class="textbox" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input name="login" type="submit" id="login" value="Update Password" class="submit" /></td>
  </tr>
</table>
</form>
<?php include("../includes/footer.inc.php"); ?>