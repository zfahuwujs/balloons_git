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
if(permission("documents","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}
include("../includes/header.inc.php");

// delete document
if(isset($_GET['delete']) && $_GET['delete']>0){

$where = "id = ".$db->mySQLSafe($_GET['delete']);

$delete = $db->delete($glob['dbprefix']."CubeCart_trade_accounts", $where, ""); 

} elseif(isset($_POST['id']) && $_POST['id']>0){

$record["cust_name"] = $db->mySQLSafe($_POST['cust_name']);
							
$where = "id = ".$db->mySQLSafe($_POST['id']);

$update =$db->update($glob['dbprefix']."CubeCart_trade_accounts", $record, $where);
			

} elseif(isset($_POST['id']) && empty($_POST['id'])){


$record["cust_name"] = $db->mySQLSafe($_POST['cust_name']);

$insert = $db->insert($glob['dbprefix']."CubeCart_trade_accounts", $record);

}

// retrieve current documents

$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_trade_accounts ORDER BY discount ASC";
	
	// query database
$results = $db->select($query);
 // end if mode is not new
if($results == TRUE){
?>
<table width="230"  border="0" cellspacing="0" cellpadding="0" style="background-color:#FFF;">
<tr>
	<th width="30" align="left">ID</th>
  <th width="100" align="left">Name</th>
  <th width="100" align="left">Discount in %</th>
</tr>
<?php 
foreach($results as $account){
?>
<tr>
	<td><?php echo $account['tradeAccId']?></td>
	<td><?php echo $account['name']?></td>
  <td><?php echo $account['discount']?></td>
</tr>
<?php
}
?>
<form method="post" target="">
<tr>
	<td><input type="submit" value="Add new"></td>
  <td><input type="text" name="name" width="100"></td>
  <td><input type="text" name="discount" width="100"></td>
</tr>
</form>
</table>

<?php
}else{
	echo 'No trade account set yet';
}

include("../includes/footer.inc.php"); ?>
