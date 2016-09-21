<?php

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

if(permission("customers","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

if(isset($_POST['discountAmount']) && $_POST['discountAmount'] != "" && isset($_POST['discountCode']) && $_POST['discountCode'] != ""){
	$rec["customerId"] = $db->mySQLSafe(0);
	$rec["discountType"] = $db->mySQLSafe(1);
	$rec["discountAmount"] = $db->mySQLSafe($_POST['discountAmount']);
	$rec["discountFormat"] = $db->mySQLSafe($_POST['discountFormat']);
	$rec["discountCode"] = $db->mySQLSafe($_POST['discountCode']);
	$date = explode('/',$_POST['expDate']);
	$expDate = mktime(23,59,59,$date[1],$date[0],$date[2]);
	$rec["expDate"] = $db->mySQLSafe($expDate);
	//var_dump(date('H:i:s d/m/Y',$expDate));
	$insert = $db->insert($glob['dbprefix']."CubeCart_discount", $rec);
	
	if($insert == TRUE){
		$msg = "<p class='infoText'>Coupon was successfully updated.</p>";
	}else{
		$msg = "<p class='warnText'>Coupon failed to update or no changes have been made.</p>";
	}
}elseif(isset($_POST['discountAmount']) && $_POST['discountAmount'] == "" && isset($_POST['discountCode']) && $_POST['discountCode'] == ""){
	$msg = "<p class='warnText'>Coupon was not updated. Incorrect or missing information.</p>";
}elseif(isset($_GET["delete"]) && $_GET["delete"] > 0){
	$delete = $db->delete($glob['dbprefix']."CubeCart_discount", "id = ".$db->mySQLSafe($_GET['delete']));

	if($delete == TRUE){
		$msg = "<p class='infoText'>Coupon was successfully deleted.</p>";
	}else{
		$msg = "<p class='warnText'>Coupon failed to delete.</p>";
	}
}

$genDiscount = $db->select("
SELECT * 
FROM ".$glob['dbprefix']."CubeCart_discount
WHERE customerId = 0
AND discountType = 1");

include("../includes/header.inc.php"); 

?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Coupon management</p></td>
   </tr>
   <tr>
    <td nowrap='nowrap'>
    <a href="coupons.php" class="txtLink"><strong>Customer General Coupons</strong></a>
    &nbsp;-&nbsp;
    <a href="couponsCategory.php" class="txtLink">Category Specific Coupons</a>
    &nbsp;-&nbsp;
    <a href="couponsProduct.php" class="txtLink">Product Specific Coupons</a>
    </td>
  </tr>
</table>

<p></p>

<?php

if(isset($msg)){ echo stripslashes($msg); }

?>

<form action="<?php echo $GLOBALS['rootRel'];?>admin/customers/coupons.php" target="_self" method="post" language="javascript">

<table width="60%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td width="60%" class="tdTitle" colspan="2">Customer General Coupons</td>
  </tr>
  <tr>
    <td class="tdRichText" width="20%"><span class="copyText"><strong>Coupon Format:</strong></span></td>
	<td width="40%">
    <select name="discountFormat" class="textbox">
    	<option value="1">Amount Off</option>
        <option value="2">Percentage Off</option>
    </select></td>
  </tr>
   <tr>
    <td class="tdRichText" width="20%"><span class="copyText"><strong>Coupon Discount:</strong></span></td>
	<td width="40%"><input name="discountAmount" class="textbox" value="" type="text" maxlength="255" /></td>
  </tr>
   <tr>
    <td class="tdRichText" width="20%"><span class="copyText"><strong>Coupon Code:</strong></span></td>
		<td width="40%"><input name="discountCode" class="textbox" value="" type="text" maxlength="255" /></td>
  </tr>
  <tr>
  	<td class="tdRichText" width="20%"><span class="copyText"><strong>Expiry date: example 27/04/2014</strong></span></td>
		<td width="40%"><input name="expDate" class="textbox" value="" type="text" maxlength="255" /></td>
  </tr>
  <tr>
    <td class="tdRichText" width="20%">&nbsp;</td>
	<td width="40%"><input type="submit" name="submit" class="submit" value="Add" /></td>
  </tr>
</table>

<p></p>

<table width="50%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td width="20%" class="tdTitle">Coupon Code</td>
  	<td width="20%" class="tdTitle">Coupon Discount</td>
    <td width="20%" class="tdTitle">Expiry date</td>
  	<td width="10%" class="tdTitle">Action</td>
  </tr>
  
<?php
		$existing = $db->select("
		SELECT *
		FROM ".$glob['dbprefix']."CubeCart_discount
		WHERE customerId = 0 
		AND discountType = 1");
		
		if($existing == TRUE){
			for($i=0; $i<count($existing); $i++){
?>
         	<tr>
            	<td class="tdRichText" width="20%"><span class="copyText"><strong><?php echo $existing[$i]['discountCode']; ?></strong></span></td>
                <td class="tdRichText" width="20%"><span class="copyText"><strong><?php if($existing[$i]['discountFormat'] == 1){ echo "&pound;".$existing[$i]['discountAmount']; }else{ echo $existing[$i]['discountAmount']."%"; } ?></strong></span></td>
                <td class="tdRichText" width="20%"><span class="copyText"><strong><?php echo date('d/m/Y',$existing[$i]['expDate'])?></strong></span></td>
                <td class="tdRichText" width="10%"><a <?php if(permission("customers","delete")==TRUE){ ?>href="javascript:decision('<?php echo $lang['admin']['delete_q'];?>','coupons.php?delete=<?php echo $existing[$i]['id']; ?>');" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['delete'];?></a></td>
            </tr>
<?php
			}
		}else{
?>
			<tr>
            	<td class="tdRichText" colspan="3"><span class="copyText">No coupons selected.</span></td>
            </tr>
<?php	
		}
?>
</table>

<?php include("../includes/footer.inc.php"); ?>
