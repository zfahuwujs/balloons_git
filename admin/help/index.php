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



include("../../language/".$config['defaultLang']."/lang.inc.php");

$enableSSl = 1;

include("../../includes/sslSwitch.inc.php");

include("../includes/rte/fckeditor.php");

include("../includes/functions.inc.php");

include("../includes/auth.inc.php");

include("../includes/header.inc.php");

// delete document
if(isset($_GET['delete']) && $_GET['delete']>0){

$where = "help_id = ".$db->mySQLSafe($_GET['delete']);

$delete = $db->delete($glob['dbprefix']."CubeCart_help_doc", $where, ""); 

	if($delete == TRUE){
		$msg = "<p class='infoText'>".$lang['admin']['docs']['delete_success']."</p>";
		
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['docs']['delete_fail']."</p>";
	}
} elseif(isset($_POST['docId']) && $_POST['docId']>0){

$record["menuShow"] = $db->mySQLSafe($_POST['menuShow']);
if($_POST['menuShow'] == 0)
		$record["menuOrder"] = $db->mySQLSafe(99);
$record["help_name"] = $db->mySQLSafe($_POST['help_name']);	
$record["help_pos"] = $db->mySQLSafe($_POST['help_pos']);		
$record["help_content"] = $db->mySQLSafe($_POST['FCKeditor']);
/* <rf> search engine friendly url mod */
if($config['seftags']) {
	$record["help_metatitle"] = $db->mySQLSafe($_POST['help_metatitle']);
	$record["doc_metadesc"] = $db->mySQLSafe($_POST['doc_metadesc']);
	$record["help_metakeywords"] = $db->mySQLSafe($_POST['help_metakeywords']);
	if($config['sefcustomurl'] == 1) $record["help_sefur"] = $db->mySQLSafe($_POST['help_sefur']);	
}
/* <rf> end mod */
							
$where = "help_id = ".$db->mySQLSafe($_POST['docId']);

$update =$db->update($glob['dbprefix']."CubeCart_help_doc", $record, $where);
			
	if($update == TRUE){
		$msg = "<p class='infoText'>'".$_POST['help_name']."' ".$lang['admin']['docs']['update_success']."</p>"; 
	} else {
		$msg = "<p class='warnText'>'".$_POST['help_name']."' ".$lang['admin']['docs']['update_fail']."</p>"; 
	}

} elseif(isset($_POST['docId']) && empty($_POST['docId'])){
/// Robz Sitemap Addition Part #4
$record["lastModified"] = $db->mySQLSafe(time());
/// END Part #4
$record["help_name"] = $db->mySQLSafe($_POST['help_name']);		
$record["help_content"] = $db->mySQLSafe($_POST['FCKeditor']);	
/* <rf> search engine friendly url mod */
if($config['seftags']) {
	$record["help_metatitle"] = $db->mySQLSafe($_POST['help_metatitle']);
	$record["doc_metadesc"] = $db->mySQLSafe($_POST['doc_metadesc']);
	$record["help_metakeywords"] = $db->mySQLSafe($_POST['help_metakeywords']);
	if($config['sefcustomurl'] == 1) $record["help_sefur"] = $db->mySQLSafe($_POST['help_sefur']);		
}
/* <rf> end mod */

$insert = $db->insert($glob['dbprefix']."CubeCart_help_doc", $record);

	if($insert == TRUE){
		$msg = "<p class='infoText'>'".$_POST['help_name']."' ".$lang['admin']['docs']['add_success']."</p>";
	} else {
		$msg = "<p class='infoText'>".$lang['admin']['docs']['add_fail']."</p>";
	}
}


// retrieve current documents
if(!isset($_GET['mode'])){
	
	// make sql query
	if(isset($_GET['id']) && $_GET['id']>0){
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_help_doc WHERE help_id = %s", $db->mySQLSafe($_GET['id'])); 
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_help_doc ORDER BY help_pos ASC";
	} 
	
	// query database
	$results = $db->select($query);
	
} // end if mode is not new
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Evo Ecommerce User Guide</p></td>
  </tr>
</table>

<?php if(isset($_GET['id']) && $_GET['id']>0){ ?>

<p class="copyText"><?php echo $lang['admin']['docs']['use_rich_text'];?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable" style="background-color:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size: 12px;">
    <tr>
    	<td><?php echo $results[0]['help_content'];?></td>
    </tr>
</table>

<?php } else {
if(isset($msg)){
 echo stripslashes($msg);
} else { 
?>
<p class="copyText"><?php echo $lang['admin']['docs']['current_doc_list']; ?></p>
<?php } ?>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
    <td class="tdTitle">Help Document Name</td>
  </tr>
  <?php 
  if($results == TRUE){
  	for ($i=0; $i<count($results); $i++){ 
  	
	$cellColor = "";
	$cellColor = cellColor($i);
	
  ?>
  <tr>
    <td width="80%" class="<?php echo $cellColor; ?>"><a href="<?php echo $GLOBALS['rootRel']."admin/help/index.php?id=".$results[$i]['help_id']; ?>" target="_blank" class="txtLink"><?php echo $results[$i]['help_name']; ?></a></td>
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
