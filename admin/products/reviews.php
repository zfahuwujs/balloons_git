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
|	siteDocs.php
|   ========================================
|	Manage Site Docs
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
if(permission("documents","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}
include("../includes/header.inc.php");

// delete document
if(isset($_GET['delete']) && $_GET['delete']>0){

$where = "id = ".$db->mySQLSafe($_GET['delete']);

$delete = $db->delete($glob['dbprefix']."CubeCart_store_ratings", $where, ""); 

	if($delete == TRUE){
		$msg = "<p class='infoText'>Review successfully deleted</p>";
	} else {
		$msg = "<p class='warnText'>Review failed to delete</p>";
	}
} elseif(isset($_POST['id']) && $_POST['id']>0){

$record["cust_name"] = $db->mySQLSafe($_POST['cust_name']);
$record["product_id"] = $db->mySQLSafe($_POST['product_id']);
$record["product_name"] = $db->mySQLSafe($_POST['product_name']);
$record["stars"] = $db->mySQLSafe($_POST['stars']);
$record["status"] = $db->mySQLSafe($_POST['status']);
$record["comments"] = $db->mySQLSafe($_POST['FCKeditor']);
							
$where = "id = ".$db->mySQLSafe($_POST['id']);

$update =$db->update($glob['dbprefix']."CubeCart_store_ratings", $record, $where);
			
	if($update == TRUE){
		$msg = "<p class='infoText'>Review has been updated</p>"; 
	} else {
		$msg = "<p class='warnText'>Review failed tp update</p>"; 
	}

} elseif(isset($_POST['id']) && empty($_POST['id'])){


$record["cust_name"] = $db->mySQLSafe($_POST['cust_name']);
$record["product_id"] = $db->mySQLSafe($_POST['product_id']);
$record["product_name"] = $db->mySQLSafe($_POST['product_name']);
$record["stars"] = $db->mySQLSafe($_POST['stars']);
$record["status"] = $db->mySQLSafe($_POST['status']);
$record["date"] = $db->mySQLSafe($_POST['date']);
$record["ip_address"] = $db->mySQLSafe($_POST['ip_address']);
$record["comments"] = $db->mySQLSafe($_POST['FCKeditor']);

$insert = $db->insert($glob['dbprefix']."CubeCart_store_ratings", $record);

	if($insert == TRUE){
		$msg = "<p class='infoText'>Review successfully added</p>";
	} else {
		$msg = "<p class='infoText'>Review failed to add</p>";
	}
}

// retrieve current documents
if(!isset($_GET['mode'])){
	
	// make sql query
	if(isset($_GET['edit']) && $_GET['edit']>0){
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_store_ratings WHERE id = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_store_ratings ORDER BY status ASC, date DESC";
	} 
	
	// query database
	$results = $db->select($query);
} // end if mode is not new
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Product Reviews</p></td>
    <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a <?php if(permission("documents","write")==TRUE){?>href="?mode=new" class="addNew"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['add_new']; ?></a></td><?php } ?>
  </tr>
</table>
<?php if((isset($_GET['edit']) && $_GET['edit']>0 && permission("documents","edit")==TRUE) || (isset($_GET['mode']) && $_GET['mode']=="new" && permission("documents","write")==TRUE)){ ?>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/products/reviews.php" target="_self" method="post" language="javascript">
<input name="id" class="textbox" value="<?php if(isset($results[0]['id'])) echo $results[0]['id']; ?>" type="hidden"  />
<p class="copyText"><?php echo $lang['admin']['docs']['use_rich_text'];?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td colspan="2" class="tdTitle">Product Review</td>
  </tr>
   <tr>
    <td width="15%" class="tdRichText"><span class="copyText"><strong>Customer Name:</strong></span></td>
	 <td class="tdRichText"><input name="cust_name" class="textbox" value="<?php if(isset($results[0]['cust_name'])) echo $results[0]['cust_name']; ?>" type="text" maxlength="255" /></td>
  </tr>
  <tr>
    <td width="15%" class="tdRichText"><span class="copyText"><strong>Product ID:</strong></span></td>
	 <td class="tdRichText"><input name="product_id" class="textbox" value="<?php if(isset($results[0]['product_id'])) echo $results[0]['product_id']; ?>" type="text" maxlength="255" /></td>
  </tr>
  <tr>
  	<td class="tdRichText"><span class="copyText"><strong>Product Name:</strong></span></td>
	 <td class="tdRichText"><input name="product_name" class="textbox" value="<?php if(isset($results[0]['product_name'])) echo $results[0]['product_name']; ?>" type="text"  size="50" maxlength="255" /></td>
  </tr>
  <tr>
    <td colspan="2" class="tdRichText"><strong>Comment:</strong></td>
	</tr>
<tr>
    <td colspan="2" class="tdRichText">
<?php
$oFCKeditor = new FCKeditor('FCKeditor') ;
$oFCKeditor->BasePath = $GLOBALS['rootRel'].'admin/includes/rte/';
if(isset($results[0]['comments'])){ 
$oFCKeditor->Value = $results[0]['comments'];
} else {
$oFCKeditor->Value = "";
}
$oFCKeditor->Create();
?></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Stars:</strong></td>
	<td class="tdText"><select name="stars" class="textbox">
			<option value="0" <?php if($results[0]['stars'] == 0) echo "selected"; ?>>1 Star</option>
			<option value="1" <?php if($results[0]['stars'] == 1) echo "selected"; ?>>2 Stars</option>
			<option value="2" <?php if($results[0]['stars'] == 2) echo "selected"; ?>>3 Stars</option>
			<option value="3" <?php if($results[0]['stars'] == 3) echo "selected"; ?>>4 Stars</option>
			<option value="4" <?php if($results[0]['stars'] == 4) echo "selected"; ?>>5 Stars</option>
		</select>
	</td>
  </tr>
   <tr>
    <td width="15%" class="tdRichText"><span class="copyText"><strong>Date:</strong></span></td>
	 <td class="tdRichText"><input name="date" class="textbox" value="<?php if(isset($results[0]['date'])) echo $results[0]['date']; ?>" <?php if(isset($results[0]['date'])) echo "disabled"; ?> type="text" maxlength="255" /></td>
  </tr>
   <tr>
    <td width="15%" class="tdRichText"><span class="copyText"><strong>IP Address:</strong></span></td>
	 <td class="tdRichText"><input name="ipAddress" class="textbox" value="<?php if(isset($results[0]['ip_address'])) echo $results[0]['ip_address']; ?>" <?php if(isset($results[0]['ip_address'])) echo "disabled"; ?> type="text" maxlength="255" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong>Status:</strong></td>
	<td class="tdText"><select name="status" class="textbox">
			<option value="0" <?php if($results[0]['status'] == 0) echo "selected"; ?>>Hide</option>
			<option value="1" <?php if($results[0]['status'] == 1) echo "selected"; ?>>Show</option>
		</select>
	</td>
  </tr>
  <tr>
    <td class="tdRichText">



	<input type="hidden" value="<?php if(isset($_GET['edit'])) echo $_GET['edit']; ?>" name="id" />
	<input name="submit" type="submit" id="submit" class="submit" <?php if(isset($results) && $results == TRUE){ ?>value="<?php echo $lang['admin']['docs']['update_doc'];?>"<?php } else { echo "value=\"".$lang['admin']['docs']['save_doc']."\""; } ?> /></td>
  </tr>
</table>
</form>
<?php } else {
if(isset($msg)){
 echo stripslashes($msg);
} else { 
?>
<?php } ?>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
    <td class="tdTitle" width="20%">Product Name</td>
	<td class="tdTitle" width="20%">Stars</td>
	<td class="tdTitle" width="20%">Date</td>
	<td class="tdTitle" width="20%">Status</td>
    <td class="tdTitle" colspan="3" align="center" width="20%"><?php echo $lang['admin']['docs']['action']; ?></td>
  </tr>
  <?php 
  if($results == TRUE){
  	for ($i=0; $i<count($results); $i++){ 
  	
	$cellColor = "";
	$cellColor = cellColor($i);
	
  ?>
  <tr>
    <td width="20%" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['product_name']; ?></span></td>
	<td width="20%" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['stars']+1; ?></span></td>
	<td width="20%" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['date']; ?></span></td>
	<td width="20%" class="<?php echo $cellColor; ?>"><span class="copyText"><?php if($results[$i]['status'] == 0) echo "Unauthorised"; else echo "Authorised"; ?></span></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a <?php if(permission("documents","edit")==TRUE){ ?>href="?edit=<?php echo $results[$i]['id']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><?php echo $lang['admin']['edit']; ?></a></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a <?php if(permission("documents","delete")==TRUE){ ?>href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>?','?delete=<?php echo $results[$i]['id']; ?>');" class="txtLink" <?php } else { echo $link401; } ?>><?php echo $lang['admin']['delete']; ?></a></td>
  </tr>
  <?php } // end loop
  } else { ?>
   <tr>
    <td colspan="4" class="tdText"><?php echo $lang['admin']['docs']['no_docs']; ?></td>
  </tr>
  <?php } ?>
</table>

<?php } ?>
<?php include("../includes/footer.inc.php"); ?>
