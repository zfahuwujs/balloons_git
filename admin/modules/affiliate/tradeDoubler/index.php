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
|	index.php
|   ========================================
|	Configure tradeDoubler
+--------------------------------------------------------------------------
*/
include("../../../../includes/ini.inc.php");
include("../../../../includes/global.inc.php");
require_once("../../../../classes/db.inc.php");
$db = new db();
include_once("../../../../includes/functions.inc.php");
$config = fetchDbConfig("config");

include_once("../../../../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include_once("../../../../includes/sslSwitch.inc.php");
include("../../../includes/auth.inc.php");
include("../../../includes/header.inc.php");

if(permission("settings","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

if(isset($_POST['module'])){
	include("../../status.php");
	include("../../../includes/functions.inc.php");
	$module = fetchDbConfig($_GET['folder']);
	$msg = writeDbConf($_POST['module'], $_GET['folder'], $module);
	
}
$module = fetchDbConfig($_GET['folder']);
?>

<p><a href="http://www.tradedoubler.com/"><img src="logo.gif" alt="TradeDoubler" border="0" title="" /></a></p>
<?php 
if(isset($msg)){ 
	echo stripslashes($msg); 
} 
?>
<p class="copyText">&quot;TradeDoubler - Improving Online Business.&quot;<br />
<a href="http://www.tradedoubler.com/" target="_blank" class="txtLink">http://www.tradedoubler.com/</a></p>
<form action="http://www.tradedoubler.com" method="post" enctype="multipart/form-data"  name="frm_Login" target="_blank" id="frm_Login">
<table border="0" cellspacing="0" cellpadding="3" class="mainTable">
	<tr>
		<td class="tdTitle">Login to TradeDoubler (Opens in new window) </td>
	</tr>
	<tr>
		<td align="center" class="tdText"><a href="http://www.tradedoubler.com" class="txtLink" target="_blank">Login to Admin</a></td>
	</tr>
</table>
</form>
<br />
<form action="<?php echo $GLOBALS['rootRel'];?>admin/modules/<?php echo $_GET['module']; ?>/<?php echo $_GET['folder']; ?>/index.php?module=<?php echo $_GET['module']; ?>&amp;folder=<?php echo $_GET['folder']; ?>" method="post" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Configuration Settings </td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>Status:</strong></td>
    <td class="tdText">
	<select name="module[status]">
		<option value="1" <?php if($module['status']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($module['status']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>
	</td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdText"><strong>TradeDoubler A/C No: </strong></td>
    <td class="tdText"><input type="text" name="module[acNo]" value="<?php echo $module['acNo']; ?>" class="textbox" size="40" />
	</td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdText"><strong>Test Mode: </strong></td>
    <td class="tdText">
	<select name="module[testMode]">
      <option value="1" <?php if($module['testMode']==1) echo "selected='selected'"; ?>>On</option>
      <option value="0" <?php if($module['testMode']==0) echo "selected='selected'"; ?>>Off</option>
    </select></td>
  </tr>
  <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>
<?php include("../../../includes/footer.inc.php"); ?>