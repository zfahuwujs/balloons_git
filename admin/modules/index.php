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

|	List Modules

+--------------------------------------------------------------------------

*/

include("../../../includes/ini.inc.php");

include("../../../includes/global.inc.php");

require_once("../../../classes/db.inc.php");

$db = new db();

include_once("../../../includes/functions.inc.php");

$config = fetchDbConfig("config");



include_once("../../../language/".$config['defaultLang']."/lang.inc.php");

$enableSSl = 1;

include_once("../../../includes/sslSwitch.inc.php");

include("../../includes/auth.inc.php");

include("../../includes/functions.inc.php");



if(permission("settings","read")==FALSE){

	header("Location: ".$GLOBALS['rootRel']."admin/401.php");

	exit;

}



include("../../includes/header.inc.php");


if(ucfirst($module)=='Shipping'){
	echo '<p class="pageTitle">Delivery Set-Up</p>';
}elseif(ucfirst($module)=='Gateway'){
	echo '<p class="pageTitle">Gateway Set-Up</p>';
}
?>



<?php
	if(ucfirst($module) == "Gateway"){
		echo '<p class="copyText">Click <a href="/admin/modules/gateway/Gateway_Options.pdf" target="_blank">here</a> to view our recommended gateway providers.</p>';
	}
?>

<table border="0" cellspacing="0" cellpadding="4" align="center" class="mainTable">

	<tr>

		<td class="tdTitle"><?php echo $lang['admin']['customers']['name']; ?></td>

	    <td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers']['action']; ?></td>

	    <td align="center" class="tdTitle"><?php echo $lang['admin']['orders']['status']; ?></td>

	</tr>

<?php 

	$path = $GLOBALS['rootDir']."/admin/modules/".$module;

	$dirArray = listModules($path);

	if(is_array($dirArray)){



		$i = 0;

		

		foreach($dirArray as $folder) {

		$i++;

		

		$cellColor = "";

		$cellColor = cellColor($i);

?>

	<tr>

		<td align="left" valign="top" class="<?php echo $cellColor; ?>">

			<?php if(file_exists($path."/".$folder."/logo.gif")){ ?>

			<a href="<?php echo $folder; ?>/index.php?module=<?php echo $module; ?>&amp;folder=<?php echo $folder; ?>"><img src="<?php echo $folder; ?>/logo.gif" alt="<?php echo $folder; ?>" border="0" title="" /></a>

			<?php } else { ?> 

			<span class="copyText"><?php echo str_replace("_"," ",$folder); ?></span>

			<?php } ?>		</td>

        <td align="center" valign="middle" class="<?php echo $cellColor; ?>"><a href="<?php echo $folder; ?>/index.php?module=<?php echo $module; ?>&amp;folder=<?php echo $folder; ?>" class="txtLink">Configure</a></td>

	    <td align="center" valign="middle" class="<?php echo $cellColor; ?>">

		<?php

		$moduleStatus = fetchDbConfig($folder);



		if($moduleStatus['status']==1){

		?>

			<img src="../../images/1.gif" width="10" height="10" alt="" title="" />

		<?php

		} else {

		?>

			<img src="../../images/0.gif" width="10" height="10" alt="" title="" />

		<?php 

		} 

		unset($moduleStatus);

		?>

		

		</td>

	</tr>

<?php

	}

}

?>

</table>

<?php include("../../includes/footer.inc.php"); ?>