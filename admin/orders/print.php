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

|	print.php

|   ========================================

|	Print Packing Slip	

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

include("../../includes/currencyVars.inc.php");

if(permission("orders","read")==FALSE){

	header("Location: ".$GLOBALS['rootRel']."admin/401.php");

	exit;

}



$result = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_order_sum WHERE cart_order_id = ".$db->mySQLSafe($_GET['cart_order_id']));

$record['time']=time();
$record['username']="'".$ccAdminData[0]['username']."'";
$record['ipAddress']="'".$_SERVER['REMOTE_ADDR']."'";
$record['cart_order_id']="'".$_GET['cart_order_id']."'";
$record['type']='\'Print Packing Slip\'';

$db->insert("CubeCart_admin_sessions_new",$record);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link href="<?php echo $GLOBALS['rootRel']; ?>admin/styles/style.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['rootRel']; ?>admin/js/jslibrary.js"></script>

<body onload="window.print();">

<p align="left" class="copyText">

<span style="font-size: 16px; font-weight: bold;"><?php echo $config['storeName']; ?></span>

<br />

<?php echo $GLOBALS['storeURL']; ?></p>

<table border="0" width="100%" cellspacing="0" cellpadding="4">

    <tr>

      <td align="left" valign="top" class="copyText">

	  <strong><?php echo $lang['admin']['orders']['paid_by'];?></strong>

      <div style="padding: 10px;"><?php echo $result[0]['name']; ?><br/>

	  <?php echo $result[0]['add_1']; ?><br/>

	  <?php if(!empty($result[0]['add_2'])) echo $result[0]['add_2']."<br/>"; ?>

	  <?php echo $result[0]['town']; ?>, <?php echo $result[0]['county']; ?><br/>

	  <?php echo $result[0]['postcode']; ?><br/>

	  <?php echo countryName($result[0]['country']); ?></div>

	  </td>

      <td align="left" valign="top" class="copyText">

	  <strong><?php echo $lang['admin']['orders']['delivered_to'];?></strong>

      <div style="padding: 10px;"><?php echo $result[0]['name_d']; ?><br/>

	  <?php echo $result[0]['add_1_d']; ?><br/>

	  <?php if(!empty($result[0]['add_2_d'])) { echo $result[0]['add_2_d']."<br />"; } ?>

	  <?php echo $result[0]['town_d']; ?>, <?php echo $result[0]['county_d']; ?><br/>

	  <?php echo $result[0]['postcode_d']; ?><br/>

	  <?php echo $result[0]['country_d']; ?></div>

	  </td>

    </tr>

  <tr>

    <td class="copyText" align="left" valign="top">

	<strong><?php echo $lang['admin']['orders']['invoice_reciept_for'];?></strong><br />

	<?php echo $lang['admin']['orders']['order_of_time'];?> <?php echo formatTime($result[0]['time']);?>

	</td>

    <td class="copyText" align="left" valign="top"><strong><?php echo $lang['admin']['orders']['order_id'];?></strong> <?php echo $_GET['cart_order_id']; ?></td>

  </tr>

  <?php 

  if($result == TRUE){	

  ?>

  <tr>

    <td colspan="2" align="left" valign="bottom" style="padding: 0px;">

	<?php

	$results = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_order_inv WHERE cart_order_id = ".$db->mySQLSafe($_GET['cart_order_id']));

	?>

	<table width="100%" border="0" cellpadding="7" cellspacing="0" style="padding: 7px;">

      <tr>

        <td class="subHead"><strong><?php echo $lang['admin']['orders']['product'];?></strong></td>

        <td class="subHead"><strong><?php echo $lang['admin']['orders']['code'];?></strong></td>

        <td class="subHead"><strong><?php echo $lang['admin']['orders']['quantity'];?></strong></td>

        <td align="right" class="subHead"><strong><?php echo $lang['admin']['orders']['price'];?></strong></td>

      </tr>

	 <?php 

	 for ($i=0; $i<count($results);$i++){

	 ?>

	  <tr>

        <td>

		<span class="copyText"><?php echo $results[$i]['name']; ?><br />

		<?php echo nl2br($results[$i]['product_options']); ?>

		</span>

		</td>

        <td><span class="copyText"><?php echo $results[$i]['productCode']; ?></span></td>

        <td><span class="copyText"><?php echo $results[$i]['quantity']; ?></span></td>

        <td align="right"><span class="copyText"><?php echo priceFormat($results[$i]['price']); ?></span></td>

      </tr>

	  <?php } ?>

	  <tr>

	    <td rowspan="4" align="left" valign="top" class="btmSubNav"><span class="copyText"><?php echo $lang['admin']['orders']['payment_method'];?> <?php echo str_replace("_"," ",$result[0]['gateway']);?><br />

		<?php echo $lang['admin']['orders']['ship_method'];?> <?php echo str_replace("_"," ",$result[0]['shipMethod']);?>

		</span></td>

	    <td rowspan="4" class="btmSubNav">&nbsp;</td>

	    <td class="btmSubNav"><span class="copyText"><?php echo $lang['admin']['orders']['subtotal'];?></span></td>

	    <td align="right" class="btmSubNav">

		<span class="copyText"><?php echo priceFormat($result[0]['subtotal']);?></span>

		</td>

	  </tr>
      <tr>
	  <td><span class="copyText">Discount:</span><font color="red"><b><?php echo $result[0]['couponCode'] != "" ? " <br />[Coupon: ".$result[0]['couponCode']."]" : "";?></b></font></td>
	    <td align="right">
		<span class="copyText"><font color="red">-<?php echo priceFormat($result[0]['discountTot']);?></font></span>
		</td>
	    </tr>

	  <tr>

	    <td><span class="copyText"><?php echo $lang['admin']['orders']['total_tax'];?></span></td>

	    <td align="right">

		<span class="copyText"><?php echo priceFormat($result[0]['total_tax']);?></span>

		</td>

	    </tr>

	  <tr>

	    <td><span class="copyText"><?php echo $lang['admin']['orders']['shipping'];?></span></td>

	    <td align="right">

		<span class="copyText"><?php echo priceFormat($result[0]['total_ship']);?></span>

		</td>

	    </tr>

	  <tr>
		<td style="border-top: 1px solid #000000;">&nbsp;</td>
        <td style="border-top: 1px solid #000000;">&nbsp;</td>
	    <td style="border-top: 1px solid #000000;"><span class="copyText"><strong><?php echo $lang['admin']['orders']['grand_total'];?></strong></span></td>

	    <td align="right" class="btmSubNav" style="border-top: 1px solid #000000;">

		<span class="copyText"><strong><?php echo priceFormat($result[0]['prod_total']);?></strong></span></td>

	    </tr>

	  

    </table></td>

  </tr>

        <tr>

        <td colspan="2" class="tdText"><?php echo $lang['admin']['orders']['digi_goods_desc'];?></td>

      </tr>

      	<?php

  		} else { 

  		?>

   <tr>

    <td colspan="2" class="tdText"><?php echo sprintf($lang['admin']['orders']['order_notfound'],$_GET['cart_order_id']);?></td>

  </tr>

  <?php } ?>

</table>

<p align="center" class="copyText" style="font-size: 16px;"><?php echo $lang['admin']['orders']['thank_you'];?></p>

<p align="center" class="copyText"><?php echo $config['storeAddress']; ?></p>

</body>

</html>