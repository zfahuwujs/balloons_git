<?php
/*-----------------------------------------------------------------------------
 * Customer Order Confirmation Email
 *-----------------------------------------------------------------------------
 * index.php
 *-----------------------------------------------------------------------------
 * Author:   Estelle Winterflood
 * Email:    cubecart@expandingbrain.com
 * Store:    http://cubecart.expandingbrain.com
 *
 * Date:     January 23, 2007
 * Updated:  November 01, 2007
 * For CubeCart Version:  3.0.x
 *-----------------------------------------------------------------------------
 * DISCLAIMER:
 * The modification is provided on an "AS IS" basis, without warranty of
 * any kind, including without limitation the warranties of merchantability,
 * fitness for a particular purpose and non-infringement. The entire risk
 * as to the quality and performance of the Software is borne by you.
 * Should the modification prove defective, you and not the author assume 
 * the entire cost of any service and repair. 
 *-----------------------------------------------------------------------------
 */

$config_name = "Customer_Order_Conf_Email";
$mod_title = "Customer Order Confirmation Email";
$version = "1.2";

$_GET['module'] = "3rdparty";
$_GET['folder'] = $config_name;


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
include("../../../includes/rte/fckeditor.php");
include("../../../includes/currencyVars.inc.php");


if(permission("settings","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}


//---- UPDATE CONFIG ----//

$msg = "";

$module = fetchDbConfig($config_name);
if ($module==FALSE)
{
	// setup default values for initial install
	$module['licence_key']='';
	$module['status']=0;
	$module['gateway_1']='';
	$module['gateway_2']='';
	$module['gateway_3']='';
	include("../../status.php");
	include("../../../includes/functions.inc.php");
	writeDbConf($module, $config_name, $module);
}
else if(isset($_POST['module']))
{
	include("../../status.php");
	include("../../../includes/functions.inc.php");
	$msg = writeDbConf($_POST['module'], $config_name, $module);
	$module = fetchDbConfig($config_name);
}

//---- DISPLAY PAGE ----//

$page = $GLOBALS['rootRel']."admin/modules/".$_GET['module']."/".$_GET['folder']."/index.php?module=".$_GET['module']."&folder=".$_GET['folder'];

include("../../../includes/header.inc.php");

echo '<p class="pageTitle">'.$mod_title." v".$version.'</p>';

if (isset($msg)) { echo stripslashes($msg); }

if ($module['status']==0) {
	echo "<p class='copyText' style='color: red; border: 1px solid red; padding: 0.5em;'>This mod is currently deactivated and will have no effect in your store. To enable this mod set the status below to \"Enabled\".</p>";
}

$path = $GLOBALS['rootDir']."/admin/modules/gateway";
$dirArray = listModules($path);

?>
	<!-- CONFIG SETTINGS -->

	<p class="copyText">Select one or more gateways for which you want an order confirmation email sent to the customer following new orders.</p>
	<p class="copyText"><strong>Note:</strong> For many online payment gateways (e.g. PayPal), the store will already send an order confirmation email upon successful payment. This mod is designed to be used for gateways that require manual processing and do not send an order confirmation email (e.g. Print Order Form).</p>

	<form action="<?php echo $page; ?>" method="post" enctype="multipart/form-data" name="settings">
	<table border="0" cellspacing="0" cellpadding="3" class="mainTable">
	<tr>
		<td colspan="2" class="tdTitle">Configuration Settings</td>
	</tr>
	<tr>
		<td class="tdText">
			<strong>Status:</strong>
		</td>
		<td class="tdText">
			<select name="module[status]" class="textbox">
			<option value="0" <?php if ($module['status']==0) echo "selected"; ?>>Disabled</option>
			<option value="1" <?php if ($module['status']!=0) echo "selected"; ?>>Enabled</option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<td class="tdText">
			<strong>Gateway&nbsp;1:</strong>
		</td>
		<td class="tdText">
<?php if (is_array($dirArray)) { ?>
			<select name="module[gateway_1]" class="textbox">
				<option value="">-- n/a --</option>
<?php foreach ($dirArray as $folder) { ?>
				<option value="<?php echo $folder; ?>" <?php if ($folder==$module['gateway_1']) echo "selected"; ?>><?php echo str_replace("_"," ",$folder); ?></option>
<?php } ?>
			</select>
<?php } else { echo "Error: No gateways found"; } ?>
			<br/>
		</td>
	</tr>
	<tr valign="middle">
		<td colspan="2" class="tdText">
			<strong>Gateway 1 Payment Instructions:</strong>
		</td>
	</tr>
	<tr valign="middle">
		<td class="tdText">&nbsp;</td>
		<td class="tdText">
			<?php if (empty($lang['front']['gateway']['order_email_gateway_1_instructions'])) { ?>
				<em>(NOTE: Not yet setup. If you wish to add payment instructions to the email when a customer uses this gateway, you will need to edit: language/XX/lang_customer_order_conf_email.inc.php)</em>
			<?php } else { ?>
				<em>(NOTE: Shown in default language only.<br/>
				To modify, edit: language/XX/lang_customer_order_conf_email.inc.php)</em>
				<br/><?php echo "<div style='border: 1px solid #A5ACB2; margin: 0.5em 0; padding: 0.5em;'>".nl2br($lang['front']['gateway']['order_email_gateway_1_instructions'])."</div>"; ?>
			<?php } ?>
		</td>
	</tr>
	<tr valign="top">
		<td class="tdText">
			<strong>Gateway&nbsp;2:</strong>
		</td>
		<td class="tdText">
<?php if (is_array($dirArray)) { ?>
			<select name="module[gateway_2]" class="textbox">
				<option value="">-- n/a --</option>
<?php foreach ($dirArray as $folder) { ?>
				<option value="<?php echo $folder; ?>" <?php if ($folder==$module['gateway_2']) echo "selected"; ?>><?php echo str_replace("_"," ",$folder); ?></option>
<?php } ?>
			</select>
<?php } else { echo "Error: No gateways found"; } ?>
			<br/>
		</td>
	</tr>
	<tr valign="middle">
		<td colspan="2" class="tdText">
			<strong>Gateway 2 Payment Instructions:</strong>
		</td>
	</tr>
	<tr valign="middle">
		<td class="tdText">&nbsp;</td>
		<td class="tdText">
			<?php if (empty($lang['front']['gateway']['order_email_gateway_2_instructions'])) { ?>
				<em>(NOTE: Not yet setup. If you wish to add payment instructions to the email when a customer uses this gateway, you will need to edit: language/XX/lang_customer_order_conf_email.inc.php)</em>
			<?php } else { ?>
				<em>(NOTE: Shown in default language only.<br/>
				To modify, edit: language/XX/lang_customer_order_conf_email.inc.php)</em>
				<br/><?php echo "<div style='border: 1px solid #A5ACB2; margin: 0.5em 0; padding: 0.5em;'>".nl2br($lang['front']['gateway']['order_email_gateway_2_instructions'])."</div>"; ?>
			<?php } ?>
		</td>
	</tr>
	<tr valign="top">
		<td class="tdText">
			<strong>Gateway&nbsp;3:</strong>
		</td>
		<td class="tdText">
<?php if (is_array($dirArray)) { ?>
			<select name="module[gateway_3]" class="textbox">
				<option value="">-- n/a --</option>
<?php foreach ($dirArray as $folder) { ?>
				<option value="<?php echo $folder; ?>" <?php if ($folder==$module['gateway_3']) echo "selected"; ?>><?php echo str_replace("_"," ",$folder); ?></option>
<?php } ?>
			</select>
<?php } else { echo "Error: No gateways found"; } ?>
			<br/>
		</td>
	</tr>
	<tr valign="middle">
		<td colspan="2" class="tdText">
			<strong>Gateway 3 Payment Instructions:</strong>
		</td>
	</tr>
	<tr valign="middle">
		<td class="tdText">&nbsp;</td>
		<td class="tdText">
			<?php if (empty($lang['front']['gateway']['order_email_gateway_3_instructions'])) { ?>
				<em>(NOTE: Not yet setup. If you wish to add payment instructions to the email when a customer uses this gateway, you will need to edit: language/XX/lang_customer_order_conf_email.inc.php)</em>
			<?php } else { ?>
				<em>(NOTE: Shown in default language only.<br/>
				To modify, edit: language/XX/lang_customer_order_conf_email.inc.php)</em>
				<br/><?php echo "<div style='border: 1px solid #A5ACB2; margin: 0.5em 0; padding: 0.5em;'>".nl2br($lang['front']['gateway']['order_email_gateway_3_instructions'])."</div>"; ?>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td class="tdText">&nbsp;</td>
		<td class="tdText">
			<input type="submit" class="submit" value="Update" />
		</td>
	</tr>
	</table>
	</form>

	<br/>

<?php

include("../../../includes/footer.inc.php");

?>
