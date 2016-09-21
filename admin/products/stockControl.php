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
include("../includes/auth.inc.php");

if(isset($_GET['catId'])){
	$results = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE cat_id = ".$db->mySQLSafe($_GET['catId'])." ORDER BY productId");
}

//update db on submit
if($results == TRUE && isset($_POST['submit'])){
	$error=0;
	for($i=0; $i<count($results); $i++) {
		$id=$results[$i]['productId'];
		$write=0;
		//check for change on useStcokLevel
		if(isset($_POST['useStockLevel_'.$id]) && $_POST['useStockLevel_'.$id]!=$results[$i]['useStockLevel']){
			$record["useStockLevel"] = $db->mySQLSafe($_POST['useStockLevel_'.$id]);
			$write=1;
		}
		//check for change in stock
		if(isset($_POST['stock_level_'.$id]) && $_POST['stock_level_'.$id]!=$_POST['old_stock_level_'.$id]){
			//compensate for change in stock while editing page
			$newStock=$_POST['stock_level_'.$id];
			if($_POST['old_stock_level_'.$id]>$results[$i]['stock_level']){
				$stockDiff=$_POST['old_stock_level_'.$id]-$results[$i]['stock_level'];
				$newStock=$_POST['stock_level_'.$id]-$stockDiff;
			}
			$record["stock_level"] = $db->mySQLSafe($newStock);
			$write=1;
		}
		//check for change to selling price
		if(isset($_POST['price_'.$id]) && $_POST['price_'.$id]!=$results[$i]['price']){
			$record["price"] = $db->mySQLSafe($_POST['price_'.$id]);
			$write=1;
		}
		//check for change to sale price
		if(isset($_POST['sale_'.$id]) && $_POST['sale_'.$id]!=$results[$i]['sale_price']){
			$record["sale_price"] = $db->mySQLSafe($_POST['sale_'.$id]);
			$write=1;
		}
		//update db if this product was changed
		if($write==1){
			$where = "productId = ".$id;
			$update =$db->update($glob['dbprefix']."CubeCart_inventory", $record, $where);
			if($update==true && $error!=1){
				$error=2;
			}else{
				$error=1;
			}
		}
	}
	header("Location: ".$GLOBALS['rootRel']."admin/products/stockControl.php?upd=".$error."&catId=".$_GET['catId']);
	exit;
}

include("../includes/header.inc.php");
?>

<p class="pageTitle">Stock Control</p>
<p class="copyText">You can use this section to increase, decrease and trun off your stock level. Updating your stock on this page will also take into account any stock that has been sold while editing this page.</p>
<?php if(isset($_GET['upd'])){
		if($_GET['upd']==1){
			echo "<p class='warnText'>Update Failed</p>";
		}elseif($_GET['upd']==2){
			echo "<p class='infoText'>Updated Sucessfully</p>";
		}
	} 
?>
<br />
<form action="<?php echo $GLOBALS['rootRel'];?>admin/products/stockControl.php<?php if(isset($_GET['catId'])){echo '?catId='.$_GET['catId'];} ?>" target="_self" method="get">
<select name="catId" class="textbox">
<?php
	$skip[0]=0;
	$cats = $db->select("SELECT cat_id, cat_name FROM ".$glob['dbprefix']."CubeCart_category ORDER BY cat_name");
	if($cats==true){
		for($i=0; $i<count($cats); $i++){
			echo'<option value="'.$cats[$i]['cat_id'].'">'.$cats[$i]['cat_name'].'</option>';
		}
	}
	
?>
</select>
<input type="submit" value="Get Products" class="submit" />
</form>
<br />
<form action="<?php echo $GLOBALS['rootRel'];?>admin/products/stockControl.php<?php if(isset($_GET['catId'])){echo '?catId='.$_GET['catId'];} ?>" target="_self" method="post" language="javascript">
<table width="100%"  border="0" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
    <td align="center" class="tdTitle">Use Stock</td>
    <td align="center" class="tdTitle">Stock Level</td>
  	<td align="center" class="tdTitle">Selling Price</td>
    <td align="center" class="tdTitle">Sale Price</td>
    <td class="tdTitle">Product Code</td>
    <td class="tdTitle">Product Name</td>
  </tr>

<?php
if($results == TRUE){
	for($i=0; $i<count($results); $i++) {
		$cellColor = "";
		$cellColor = cellColor($i);
?>

  <tr>
    <td align="center" class="<?php echo $cellColor; ?> copyText"><?php
		echo'
		<select name="useStockLevel_'.$results[$i]['productId'].'">
			<option value="1">On</option>
			<option value="0"'; if($results[$i]['useStockLevel']==0){echo'selected="selected"';} echo'>Off</option>
		</select> 
		';
		if($results[$i]['useStockLevel']==0){
        	echo' <img src="../images/0.gif" alt="" title="Hide" />';
		}else{
			echo' <img src="../images/1.gif" alt="" title="Show" />';
		}
		?>
    </td>
    <td align="center" class="<?php echo $cellColor; ?> copyText stockChange"><?php
		echo'
			<a class="stockDown txtLink" href="'.$results[$i]['productId'].'"><img src="/admin/images/minus.png" alt="Lower Stock" title="Lower Stock" border="0" width="12" /></a> 
			<input name="stock_level_'.$results[$i]['productId'].'" id="stock_level_'.$results[$i]['productId'].'" value="'.$results[$i]['stock_level'].'" type="text" class="textbox" style="width:40px !important;" />
			<a class="stockUp txtLink" href="'.$results[$i]['productId'].'"><img src="/admin/images/add.png" alt="Raise Stock" title="Raise Stock" border="0" width="12" /></a>
			<input name="old_stock_level_'.$results[$i]['productId'].'" value="'.$results[$i]['stock_level'].'" type="hidden" />
		';
	?></td>
    <td align="center" class="<?php echo $cellColor; ?> copyText"><?php
    	echo'
			<input name="price_'.$results[$i]['productId'].'" value="'.$results[$i]['price'].'" type="text" class="textbox" style="width:60px !important;" />
		';
	?></td>
    <td align="center" class="<?php echo $cellColor; ?> copyText"><?php
    	echo'
			<input name="sale_'.$results[$i]['productId'].'" value="'.$results[$i]['sale_price'].'" type="text" class="textbox" style="width:60px !important;" />
		';
		if($results[$i]['sale_price']>0){echo' <img src="../images/1.gif" alt="" title="Show" />';}
		else{echo' <img src="../images/0.gif" alt="" title="Hide" />';}
	?></td>
  	<td class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['productCode']; ?></td>
  	<td class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['name']; ?></td>
  </tr>
  
<?php } 
}
?>
  <tr>
    <td></td>
	<td align="center"><input name="submit" type="submit" id="submit" class="submit" value="Update" /></td>
    <td></td>
    <td></td>
  </tr>
</table>
</form>
<script>
	$('.stockChange .stockUp').click(function(e) {
		//disable link
		e.preventDefault();
		//get info
		var prodId = $(this).attr('href');
		var oldStock = parseInt($('#stock_level_'+prodId).attr('value'));
		//add stock and output
		var newStock = oldStock + 1;
		$('#stock_level_'+prodId).attr('value',newStock);
	});
	$('.stockChange .stockDown').click(function(e) {
		e.preventDefault();
		var prodId = $(this).attr('href');
		var oldStock = parseInt($('#stock_level_'+prodId).attr('value'));
		var newStock = oldStock - 1;
		$('#stock_level_'+prodId).attr('value',newStock);
	});
</script>
<?php include("../includes/footer.inc.php"); ?>