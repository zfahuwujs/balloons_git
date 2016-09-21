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

if(isset($_POST['discountAmount']) && $_POST['discountAmount'] != ""){
	if(isset($_POST['discountId']) && $_POST['discountId'] > 0){
		$rec["customerId"] = $db->mySQLSafe($_GET['customerId']);
		$rec["discountType"] = $db->mySQLSafe(3);
		$rec["discountAmount"] = $db->mySQLSafe($_POST['discountAmount']);
		$rec["discountFormat"] = $db->mySQLSafe($_POST['discountFormat']);
		$rec["discountId"] = $db->mySQLSafe($_POST['discountId']);
	$rec["discountCode"] = $db->mySQLSafe($_POST['discountCode']);
		
		$exist = $db->numrows("
		SELECT * 
		FROM ".$glob['dbprefix']."CubeCart_discount
		WHERE customerId = ".$db->mySQLSafe($_GET['customerId'])."
		AND discountType = 3
		AND discountId = ".$db->mySQLSafe($_POST['discountId']));
		
		if($exist == 0){
			$update = $db->insert($glob['dbprefix']."CubeCart_discount", $rec);
		}else{
			$update = $db->update($glob['dbprefix']."CubeCart_discount", $rec, "customerId = 0 AND discountType = 3 AND discountId = ".$db->mySQLSafe($_POST['discountId']));
		}
		
		if($update == TRUE){
			$msg = "<p class='infoText'>Coupon was successfully updated.</p>";
		}else{
			$msg = "<p class='warnText'>Coupon failed to update or no changes have been made.</p>";
		}
	}else{
		$msg = "<p class='warnText'>Coupon failed to update. No product was selected.</p>";
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

$fatherDetails = $db->select("
SELECT * 
FROM ".$glob['dbprefix']."CubeCart_customer
WHERE customer_id = ".$db->mySQLSafe($_GET['customerId']));

include("../includes/header.inc.php"); 

?>

<script>
	$(document).ready(function(){
		$("#catId").change(function(){
			$.ajax({
				type: 'post',
				url: 'products.ajax.php',
				data: 'catId=' + $("#catId").val(),
				success: function(msg) {
					$('#discountId').html(msg);
				}
			});
		});
	});
</script>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Coupons management</p></td>
   </tr>
   <tr>
    <td nowrap='nowrap'>
    <a href="coupons.php" class="txtLink">Customer General Coupons</a>
    &nbsp;-&nbsp;
    <a href="couponsCategory.php" class="txtLink">Category Specific Coupons</a>
    &nbsp;-&nbsp;
    <a href="couponsProduct.php" class="txtLink"><strong>Product Specific Coupons</strong></a>
    </td>
  </tr>
</table>

<p></p>

<?php

if(isset($msg)){ echo stripslashes($msg); }

?>

<form action="<?php echo $GLOBALS['rootRel'];?>admin/customers/couponsProduct.php" target="_self" method="post" language="javascript">

<table width="60%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td width="60%" class="tdTitle" colspan="2">Customer Product Coupons</td>
  </tr>
   <tr>
    <td class="tdRichText" width="20%"><span class="copyText"><strong>Category:</strong></span></td>
	<td width="40%">
    <select name="catId" id="catId" class="textbox">
<?php
		$cats = $db->select("
		SELECT cat_id, cat_name, cat_father_id 
		FROM ".$glob['dbprefix']."CubeCart_category 
		ORDER BY cat_father_id, cat_name");
		
		if($cats == TRUE){
			$starter = $cats[0]["cat_id"];
			
			for($i=0; $i<count($cats); $i++){
?>
    			<option value="<?php echo $cats[$i]['cat_id']; ?>"><?php echo getCatDir($cats[$i]['cat_name'],$cats[$i]['cat_father_id'], $cats[$i]['cat_id']); ?></option>
<?php
			}
		}
?>
    </select></td>
  </tr>
   <tr>
    <td class="tdRichText" width="20%"><span class="copyText"><strong>Product:</strong></span></td>
	<td width="40%">
    <select name="discountId" id="discountId" class="textbox">
<?php
		$prods = $db->select("
		SELECT productId, name 
		FROM ".$glob['dbprefix']."CubeCart_inventory
		WHERE cat_id = ".$db->mySQLSafe($starter)."
		ORDER BY name");
		
		if($prods == TRUE){
			for($i=0; $i<count($prods); $i++){
?>
    			<option value="<?php echo $prods[$i]['productId']; ?>"><?php echo $prods[$i]['name']." (#".$prods[$i]['productId'].")"; ?></option>
<?php
			}
		}
?>
    </select></td>
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
    <td class="tdRichText" width="20%">&nbsp;</td>
	<td width="40%"><input type="submit" name="submit" class="submit" value="Update" /></td>
  </tr>
</table>

<p></p>

<table width="90%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td width="20%" class="tdTitle">Coupon Code</td>
  	<td width="20%" class="tdTitle">Product</td>
  	<td width="20%" class="tdTitle">Category</td>
  	<td width="20%" class="tdTitle">Discount</td>
  	<td width="10%" class="tdTitle">Action</td>
  </tr>
  
<?php
		$existing = $db->select("
		SELECT *
		FROM ".$glob['dbprefix']."CubeCart_discount dis
		INNER JOIN ".$glob['dbprefix']."CubeCart_inventory prod
		ON dis.discountId = prod.productId
		INNER JOIN ".$glob['dbprefix']."CubeCart_category cat
		ON prod.cat_id = cat.cat_id
		WHERE dis.customerId = 0
		AND dis.discountType = 3
		ORDER BY prod.name");
		
		if($existing == TRUE){
			for($i=0; $i<count($existing); $i++){
?>
         	<tr>
            	<td class="tdRichText" width="20%"><span class="copyText"><strong><?php echo $existing[$i]['discountCode']; ?></strong></span></td>
            	<td class="tdRichText" width="20%"><span class="copyText"><strong><?php echo $existing[$i]['name']; ?></strong></span></td>
            	<td class="tdRichText" width="20%"><span class="copyText"><strong><?php echo getCatDir($existing[$i]['cat_name'],$existing[$i]['cat_father_id'], $existing[$i]['cat_id']); ?></strong></span></td>
                <td class="tdRichText" width="20%"><span class="copyText"><strong><?php if($existing[$i]['discountFormat'] == 1){ echo "&pound;".$existing[$i]['discountAmount']; }else{ echo $existing[$i]['discountAmount']."%"; } ?></strong></span></td>
                <td class="tdRichText" width="10%"><a <?php if(permission("customers","delete")==TRUE){ ?>href="javascript:decision('<?php echo $lang['admin']['delete_q'];?>','couponsProduct.php?delete=<?php echo $existing[$i]['id']; ?>');" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['delete'];?></a></td>
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
