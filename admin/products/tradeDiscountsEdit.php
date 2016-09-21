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

if(isset($_POST['update'])){
	$rec["name"] = $db->mySQLSafe($_POST['name']);
	$rec["discount"] = $db->mySQLSafe($_POST['discount']);
	$where = "tradeAccId = ".$db->mySQLSafe($_POST['tradeAccId']);
	$update = $db->update($glob['dbprefix']."CubeCart_trade_accounts", $rec, $where);
	
	if($update == TRUE){
		header("Location: ".$GLOBALS['rootRel']."admin/customers/tradeDiscounts.php?update=true");
		exit;
	}else{
		header("Location: ".$GLOBALS['rootRel']."admin/customers/tradeDiscounts.php?update=false");
		exit;
	}
}

if(isset($_GET['edit']) && $_GET['edit'] > 0){
	$tradeDiscount = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($_GET['edit']));

include("../includes/header.inc.php"); 
?>

<form action="<?php echo $GLOBALS['rootRel'];?>admin/customers/tradeDiscountsEdit.php" target="_self" method="post" language="javascript">

<table width="60%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td width="40%"><span class="copyText">Name</span></td>
    <td width="40%"><span class="copyText">Discount(%)</span></td>
		<td width="10%"><span class="copyText">Edit</span></td>
  </tr>
  <?php 
	foreach($tradeDiscount as $account){
	?>
  <tr>
  	<td><input name="name" class="textbox" value="<?php echo $account['name']?>" type="text" maxlength="255" /></td>
    <td><input name="discount" class="textbox" value="<?php echo $account['discount']?>" type="text" maxlength="255" /></td>
    <td><input type="submit" name="update" value="Submit"></td>
    <input name="tradeAccId" value="<?php echo $account['tradeAccId']?>" type="hidden"/>
  </tr>
  
  <?php
	}
	?>
  
</table>
</form>
<?php include("../includes/footer.inc.php"); 
}
?>
