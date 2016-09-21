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

$where = "catId = ".$db->mySQLSafe($_GET['delete']);

$delete = $db->delete($glob['dbprefix']."CubeCart_docCats", $where, ""); 

	if($delete == TRUE){
		$msg = "<p class='infoText'>".$lang['admin']['docs']['delete_success']."</p>";
		
		/// Robz Sitemap Addition Part #1
		$delRec["type"] = $db->mySQLSafe("Document");
		$delRec["aspect_id"] = $db->mySQLSafe($_GET['delete']);
		$delRec["action"] = $db->mySQLSafe("Delete");
		
		$insert = $db->insert($glob['dbprefix']."CubeCart_mods", $delRec);
		/// END Part #1
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['docs']['delete_fail']."</p>";
	}
} elseif(isset($_POST['docId']) && $_POST['docId']>0){
	
$record["catName"] = $db->mySQLSafe($_POST['catName']);		
$record["menuShow"] = $db->mySQLSafe($_POST['menuShow']);
							
$where = "catId = ".$db->mySQLSafe($_POST['docId']);

$update =$db->update($glob['dbprefix']."CubeCart_docCats", $record, $where);
			
	if($update == TRUE){
		$msg = "<p class='infoText'>'".$_POST['doc_name']."' ".$lang['admin']['docs']['update_success']."</p>"; 
		
		/// Robz Sitemap Addition Part #2
		$updRec["type"] = $db->mySQLSafe("Document");
		$updRec["aspect_id"] = $db->mySQLSafe($_POST['docId']);
		$updRec["action"] = $db->mySQLSafe("Update");
		
		$insert = $db->insert($glob['dbprefix']."CubeCart_mods", $updRec);
		/// END Part #2
		
		
	} else {
		$msg = "<p class='warnText'> ".$lang['admin']['docs']['update_fail']."</p>"; 
	}

} elseif(isset($_POST['docId']) && empty($_POST['docId'])){
$record["catName"] = $db->mySQLSafe($_POST['catName']);		
$record["menuShow"] = $db->mySQLSafe($_POST['menuShow']);

$insert = $db->insert($glob['dbprefix']."CubeCart_docCats", $record);

	if($insert == TRUE){
		$msg = "<p class='infoText'> ".$lang['admin']['docs']['add_success']."</p>";
		
		/// Robz Sitemap Addition Part #3
		$insRec["type"] = $db->mySQLSafe("Document");
		$insRec["aspect_id"] = $db->mySQLSafe(mysql_insert_id());
		$insRec["action"] = $db->mySQLSafe("Insert");
		
		$insert = $db->insert($glob['dbprefix']."CubeCart_mods", $insRec);
		/// END Part #3
	} else {
		$msg = "<p class='infoText'>".$lang['admin']['docs']['add_fail']."</p>";
	}
}

// retrieve current documents
if(!isset($_GET['mode'])){
	
	// make sql query
	if(isset($_GET['edit']) && $_GET['edit']>0){
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_docCats WHERE catId = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_docCats ORDER BY catId ASC";
	} 
	
	// query database
	$results = $db->select($query);
} // end if mode is not new
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Document Categories</p></td>
    <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a class="addNew" <?php if(permission("documents","write")==TRUE){?>href="?mode=new" <?php } else { echo $link401; } ?>><?php echo $lang['admin']['add_new']; ?></a></td><?php } ?>
  </tr>
</table>
<?php if((isset($_GET['edit']) && $_GET['edit']>0 && permission("documents","edit")==TRUE) || (isset($_GET['mode']) && $_GET['mode']=="new" && permission("documents","write")==TRUE)){ ?>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/categories/addDocCat.php?" target="_self" method="post" language="javascript">
<p class="copyText"><?php echo $lang['admin']['docs']['use_rich_text'];?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td class="tdTitle"><?php echo $lang['admin']['docs']['site_doc']; ?></td>
  </tr>
  <tr>
    <td class="tdRichText"><span class="copyText"><strong><?php echo 'Category Name';?></strong></span> <input name="catName" class="textbox" value="<?php if(isset($results[0]['catName'])) echo $results[0]['catName']; ?>" type="text" maxlength="255" /></td>
  </tr>
  <tr <?php evoHide(23); ?>>
    <td class="tdText"><strong>Include In Top Navigation:</strong>
    	<select name="menuShow" class="textbox">
			<option value="0" <?php if($results[0]['menuShow'] == 0) echo "selected"; ?>>No</option>
			<option value="1" <?php if($results[0]['menuShow'] == 1) echo "selected"; ?>>Yes</option>
		</select>
	</td>
  </tr>
  <tr>
    <td class="tdRichText">

	
	<input type="hidden" value="<?php if(isset($_GET['edit'])) echo $_GET['edit']; ?>" name="docId" />
	<input name="submit" type="submit" id="submit" class="submit" <?php if(isset($results) && $results == TRUE){ ?>value="<?php echo $lang['admin']['docs']['update_doc'];?>"<?php } else { echo "value=\"".$lang['admin']['docs']['save_doc']."\""; } ?> /></td>
  </tr>
</table>
</form>
<?php } else {
if(isset($msg)){
 echo stripslashes($msg);
} else { 
?>
<p class="copyText"><?php echo $lang['admin']['docs']['current_doc_list']; ?></p>
<?php } ?>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
    <td class="tdTitle" width="80%">Category</td>
    <td class="tdTitle" align="center" width="10%">Edit</td>
    <td class="tdTitle" align="center" width="10%">Delete</td>
  </tr>
  <?php 
  if($results == TRUE){
  	for ($i=0; $i<count($results); $i++){ 
  	
	$cellColor = "";
	$cellColor = cellColor($i);
	
  ?>
  <tr>
    <td width="40%" class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['catName']; ?></td>

    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a <?php if(permission("documents","edit")==TRUE){ ?>href="?edit=<?php echo $results[$i]['catId']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><img src="/admin/images/edit.gif" alt="Edit" title="Edit" width="16" border="0" /></a></td>
    <td align="center" width="5%" class="<?php echo $cellColor; ?>">
    <?php if($results[$i]['locked'] != 1){ ?>
    <a <?php if(permission("documents","delete")==TRUE){ ?>href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>?','?delete=<?php echo $results[$i]['catId']; ?>');" class="txtLink" <?php } else { echo $link401; } ?>><img src="/admin/images/del.gif" alt="Delete" title="Delete" border="0" /></a>
    <?php } ?>
    </td>
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
