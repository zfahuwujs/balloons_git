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

|	Configure Flat Rate Shipping

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



<p class="pageTitle">Flat Rate</p>

<?php 

if(isset($msg)){ 

	echo stripslashes($msg); 

} 

?>

<p class="copyText">This shipping method is used to give a flat module rate to all orders.</p>



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

  <td align="left" class="tdText"><strong>Shipping Cost:</strong></td>

    <td class="tdText"><input type="text" name="module[cost]" value="<?php echo $module['cost']; ?>" class="textbox" size="10" /></td>

  </tr>

  <td align="left" class="tdText"><strong>Tax Class:</strong></td>

    <td class="tdText">

	<?php

	$tax = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_taxes");

	?>

	<select name="module[tax]">

	<?php for($i=0; $i<count($tax); $i++){ ?>

	<option value="<?php echo $tax[$i]['id']; ?>" <?php if($module['tax'] == $tax[$i]['id']) echo "selected='selected'"; ?>>

	<?php echo $tax[$i]['taxName']; ?>

	</option>

	<?php } ?>

	</select>

	</td>

  </tr>

  <tr>

    <td align="right" class="tdText">&nbsp;</td>

    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>

  </tr>

</table>

</form>

<?php include("../../../includes/footer.inc.php"); ?>