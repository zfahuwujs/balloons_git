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

|	Manage Orders

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

include("../includes/rte/fckeditor.php");

include("../includes/auth.inc.php");

include("../../includes/currencyVars.inc.php");

if(permission("orders","read")==FALSE){

	header("Location: ".$GLOBALS['rootRel']."admin/401.php");

	exit;

}

include("../includes/header.inc.php");



// delete document

if(isset($_GET['delete']) && $_GET['delete']>0){



$record['noOrders'] = "noOrders - 1";

$where = "customer_id = ".$_GET['customer_id'];

$update = $db->update($glob['dbprefix']."CubeCart_customer", $record, $where);



$where = "cart_order_id = ".$db->mySQLSafe($_GET['delete']);



$delete = $db->delete($glob['dbprefix']."CubeCart_order_sum", $where, "");

$delete = $db->delete($glob['dbprefix']."CubeCart_order_inv", $where, ""); 



	if($delete == TRUE){

		$msg = "<p class='infoText'>".$lang['admin']['orders']['delete_success']."</p>";

	} else {

		$msg = "<p class='infoText'>".$lang['admin']['orders']['delete_fail']."</p>";

	}

}





$sqlQuery = "";

if(isset($_GET['oid']) && !isset($_GET['delete'])){

	$sqlQuery = "WHERE cart_order_id = ".$db->mySQLsafe($_GET['oid']);

} elseif(isset($_GET['customer_id']) && $_GET['customer_id']>0){

	$sqlQuery = "WHERE customer_id = ".$db->mySQLsafe($_GET['customer_id']);

}

 
// start mod: Order Status Filter, by Estelle
	$status = $_GET['status'];
	if (!empty($status) && empty($sqlQuery)) {
		$sqlQuery .= "WHERE status = ".$db->mySQLSafe($status);
	} elseif (!empty($status) && !empty($sqlQuery)) {
		$sqlQuery .= " AND status = ".$db->mySQLSafe($status);
	}
	$state_options = $lang['orderState'];
	ksort($state_options);
	// end mod: Order Status Filter, by Estelle



	// query database

	if(isset($_GET['page'])){

		$page = $_GET['page'];

	} else {

		$page = 0;

	}

	

	$ordersPerPage = 25;

	

	$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_order_sum ".$sqlQuery." ORDER BY time DESC";

	

	$results = $db->select($query, $ordersPerPage, $page);

	$numrows = $db->numrows($query);

	$pagination = $db->paginate($numrows, $ordersPerPage, $page, "page");



?>

<p class="pageTitle">Order Manager</p>



<?php

if(isset($msg)){

 echo stripslashes($msg);

}

?>

<!-- start mod: Order Status Filter, by Estelle -->
<form name="filter" method="get" action="<?php echo $GLOBALS['rootRel'];?>admin/orders/index.php">
<?php if (!empty($_GET['customer_id'])) { ?> <input type="hidden" name="customer_id" value="<?php echo $_GET['customer_id']; ?>" /> <?php } ?>
<p class="copyText"> Show: <select name="status" class="textbox"><option value="" <?php if ($status==(string)$key) echo "selected"; ?>> All Orders </option>
<?php foreach ($state_options as $key => $value) { ?>
    <option value="<?php echo $key; ?>" <?php if ($status==(string)$key) echo "selected"; ?>><?php echo $value." Orders"; ?></option>
<?php } ?>
</select> <input type="submit" value="Filter" class="submit" /></p></form>
<!-- end mod: Order Status Filter, by Estelle -->


<p class="copyText"><?php echo $lang['admin']['orders']['all_orders']; ?></p>

<p class="copyText"><?php echo $pagination; ?></p>

<table border="0" width="100%" cellspacing="0" cellpadding="4" class="mainTable">

  <tr>

    <td class="tdTitle"><?php echo $lang['admin']['orders']['order_no']; ?></td>

    <td class="tdTitle"><?php echo $lang['admin']['orders']['status']; ?></td>

    <td class="tdTitle"><?php echo $lang['admin']['orders']['date_time']; ?></td>

    <td class="tdTitle"><?php echo $lang['admin']['orders']['customer']; ?></td>

    <td class="tdTitle"><?php echo $lang['admin']['orders']['ip_address']; ?></td>

    <td class="tdTitle"><?php echo $lang['admin']['orders']['cart_total']; ?></td>

    <td class="tdTitle" align="center">Delete</td>

  </tr>

  <?php 

  if($results == TRUE){

  	for ($i=0; $i<count($results); $i++){ 

  	

	$cellColor = "";

	$cellColor = cellColor($i);

	

  ?>

  <tr>

    <td class="<?php echo $cellColor; ?>"><a href="order.php?cart_order_id=<?php echo $results[$i]['cart_order_id']; ?>" class="txtLink"><?php echo $results[$i]['cart_order_id']; ?></a></td>

    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php 

	$state = $results[$i]['status'];

	echo $lang['orderState'][$state];

	?></span></td>

    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo formatTime($results[$i]['time']); ?></span></td>

    <td class="<?php echo $cellColor; ?>"><a href="../customers/index.php?searchStr=<?php echo urlencode($results[$i]['email']); ?>" class="txtLink"><?php echo $results[$i]['name']; ?></a></td>

    <td class="<?php echo $cellColor; ?>"><a href="javascript:;" class="txtLink" onclick="openPopUp('../misc/lookupip.php?ip=<?php echo $results[$i]['ip']; ?>','misc',300,120)"><?php echo $results[$i]['ip']; ?></a></td>

    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo priceFormat($results[$i]['prod_total']); ?></span></td>

    <td align="center" class="<?php echo $cellColor; ?>"><a <?php if(permission("orders","delete")==TRUE){ ?>href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>','?delete=<?php echo $results[$i]['cart_order_id']; ?>&customer_id=<?php echo $results[$i]['customer_id']; ?>');" class="txtLink" <?php } else { echo $link401; } ?>><img src="/admin/images/del.gif" alt="Delete" title="Delete" border="0" /></a></td>

  </tr>

  <?php } // end loop

  } else { ?>

   <tr>

    <td colspan="7" class="tdText"><?php echo $lang['admin']['orders']['no_orders_in_db']; ?></td>

  </tr>

  <?php } ?>

</table>

<p class="copyText"><?php echo $pagination; ?></p>

<?php include("../includes/footer.inc.php"); ?>