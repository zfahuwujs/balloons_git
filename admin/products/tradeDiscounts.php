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

if(isset($_GET['update'])){
	if($_GET['update'] == 'true'){
		$msg = "<p class='infoText'>Trade account was successfully updated.</p>";
	}else{
		$msg = "<p class='warnText'>Trade account  failed to update or no changes have been made.</p>";
	}
}elseif(isset($_GET["delete"]) && $_GET["delete"] > 0){
	$delete = $db->delete($glob['dbprefix']."CubeCart_trade_accounts", "tradeAccId = ".$db->mySQLSafe($_GET['delete']));

	if($delete == TRUE){
		$msg = "<p class='infoText'>Trade account was successfully deleted.</p>";
	}else{
		$msg = "<p class='warnText'>Trade account failed to delete.</p>";
	}
}elseif(isset($_POST['addNew'])){
	$rec["name"] = $db->mySQLSafe($_POST['name']);
	$rec["discount"] = $db->mySQLSafe($_POST['discount']);
	$insert = $db->insert($glob['dbprefix']."CubeCart_trade_accounts", $rec);
	if($insert == TRUE){
		$msg = "<p class='infoText'>Trade account was successfully added.</p>";
	}else{
		$msg = "<p class='warnText'>Trade account failed to add new.</p>";
	}
}

$tradeDiscount = $db->select("
SELECT * 
FROM ".$glob['dbprefix']."CubeCart_trade_accounts");

include("../includes/header.inc.php"); 

if(isset($msg)){ echo stripslashes($msg); }

?>

<form action="<?php echo $GLOBALS['rootRel'];?>admin/products/tradeDiscounts.php" target="_self" method="post" language="javascript">

<table width="60%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td width="40%"><span class="copyText">Name</span></td>
    <td width="40%"><span class="copyText">Discount(%)</span></td>
		<td width="10%"><span class="copyText">Edit</span></td>
  	<td width="10%"><span class="copyText">Delete</span></td>
  </tr>
  <?php 
	foreach($tradeDiscount as $account){
	?>
  <tr>
  	<td><?php echo $account['name']?></td>
    <td><?php echo $account['discount']?></td>
    <td><a href="<?php echo $GLOBALS['rootRel'];?>admin/products/tradeDiscountsEdit.php?edit=<?php echo $account['tradeAccId']?>"><img src="/admin/images/edit.gif" alt="Edit" title="Edit" width="16" border="0" /></a></td>
    <td><a href="?delete=<?php echo $account['tradeAccId']?>"><img src="/admin/images/del.gif" alt="Delete" title="Delete" border="0" /></a></td>
  </tr>
  
  <?php
	}
	?>
  <tr>
  	<td><input type="text" name="name" width="255"></td>
    <td><input type="text" name="discount" width="255"></td>
    <td colspan="2"><input type="submit" name="addNew" value="Add new"></td>
  </tr>
</table>
</form>
<?php include("../includes/footer.inc.php"); ?>
