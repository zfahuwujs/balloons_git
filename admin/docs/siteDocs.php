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

$where = "doc_id = ".$db->mySQLSafe($_GET['delete']);

$delete = $db->delete($glob['dbprefix']."CubeCart_docs", $where, ""); 

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

if($_POST['doc_pos'] == 0 || $_POST['doc_pos'] == ''){
	$record["doc_pos"] = 0;
}else{
	$record["doc_pos"] = $db->mySQLSafe($_POST['doc_pos']);	
	
}
	
$record["doc_name"] = $db->mySQLSafe($_POST['doc_name']);	
$record["menuShow"] = $db->mySQLSafe($_POST['menuShow']);
$record["sub_heading"] = $db->mySQLSafe($_POST['sub_heading']);
$record["catId"] = $db->mySQLSafe($_POST['catId']);
$record["doc_content"] = $db->mySQLSafe($_POST['FCKeditor']);
/* <rf> search engine friendly url mod */
if($config['seftags']) {
	$record["doc_metatitle"] = $db->mySQLSafe($_POST['doc_metatitle']);
	$record["doc_metadesc"] = $db->mySQLSafe($_POST['doc_metadesc']);
	$record["doc_metakeywords"] = $db->mySQLSafe($_POST['doc_metakeywords']);
	if($config['sefcustomurl'] == 1) $record["doc_sefurl"] = $db->mySQLSafe($_POST['doc_sefurl']);	
}
/* <rf> end mod */
							
$where = "doc_id = ".$db->mySQLSafe($_POST['docId']);

$update =$db->update($glob['dbprefix']."CubeCart_docs", $record, $where);
			
	if($update == TRUE){
		$msg = "<p class='infoText'>'".$_POST['doc_name']."' ".$lang['admin']['docs']['update_success']."</p>"; 
		
		/// Robz Sitemap Addition Part #2
		$updRec["type"] = $db->mySQLSafe("Document");
		$updRec["aspect_id"] = $db->mySQLSafe($_POST['docId']);
		$updRec["action"] = $db->mySQLSafe("Update");
		
		$insert = $db->insert($glob['dbprefix']."CubeCart_mods", $updRec);
		/// END Part #2
		
		/// Robz Sitemap Addition Part #5
		$modRec["lastModified"] = $db->mySQLSafe(time());
		
		$update = $db->update($glob['dbprefix']."CubeCart_docs", $modRec, $where);
		/// END Part #5
	} else {
		$msg = "<p class='warnText'>'".$_POST['doc_name']."' ".$lang['admin']['docs']['update_fail']."</p>"; 
	}

} elseif(isset($_POST['docId']) && empty($_POST['docId'])){
/// Robz Sitemap Addition Part #4
$record["lastModified"] = $db->mySQLSafe(time());
/// END Part #4

if($_POST['doc_pos'] == 0 || $_POST['doc_pos'] == ''){
	$record["doc_pos"] = 0;
}else{
	$record["doc_pos"] = $db->mySQLSafe($_POST['doc_pos']);	
	
}
$record["menuShow"] = $db->mySQLSafe($_POST['menuShow']);
$record["doc_name"] = $db->mySQLSafe($_POST['doc_name']);
$record["sub_heading"] = $db->mySQLSafe($_POST['sub_heading']);
$record["catId"] = $db->mySQLSafe($_POST['catId']);			
$record["doc_content"] = $db->mySQLSafe($_POST['FCKeditor']);	
/* <rf> search engine friendly url mod */
if($config['seftags']) {
	$record["doc_metatitle"] = $db->mySQLSafe($_POST['doc_metatitle']);
	$record["doc_metadesc"] = $db->mySQLSafe($_POST['doc_metadesc']);
	$record["doc_metakeywords"] = $db->mySQLSafe($_POST['doc_metakeywords']);
	if($config['sefcustomurl'] == 1) $record["doc_sefurl"] = $db->mySQLSafe($_POST['doc_sefurl']);		
}
/* <rf> end mod */

$insert = $db->insert($glob['dbprefix']."CubeCart_docs", $record);

	if($insert == TRUE){
		$msg = "<p class='infoText'>'".$_POST['doc_name']."' ".$lang['admin']['docs']['add_success']."</p>";
		
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
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		if(evoHideBol(14)==false){
			$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_docs AS docs LEFT JOIN ".$glob['dbprefix']."CubeCart_docCats AS cats ON docs.catId=cats.catId ORDER BY cats.catName, docs.doc_name ASC";
		}else{
			$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_docs ORDER BY doc_name ASC";
		}
	} 
	
	// query database
	$results = $db->select($query);
} // end if mode is not new


// FIND CATEGORIES
	$catQuery = "SELECT * FROM ".$glob['dbprefix']."CubeCart_docCats ORDER BY catName ASC";
	$catResults = $db->select($catQuery);
	

?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle"><?php echo $lang['admin']['docs']['site_docs']; ?></p></td>
    <?php 
	//limit number of pages, set this in general settings (404 page is not included in the limit but is used to account for the homepage
	if(count($results)<($config['pageLimit']-1) || $config['pageLimit']==0 || $config['pageLimit']==''){
		if(!isset($_GET["mode"])){ ?>
    		<td align="right" valign="middle"><a <?php if(permission("documents","write")==TRUE){?>href="?mode=new" class="addNew"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['add_new']; ?></a></td>
	<?php 
		}
	} ?>
  </tr>
</table>
<?php if((isset($_GET['edit']) && $_GET['edit']>0 && permission("documents","edit")==TRUE) || (isset($_GET['mode']) && $_GET['mode']=="new" && permission("documents","write")==TRUE)){ ?>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/docs/siteDocs.php" target="_self" method="post" language="javascript">
<p class="copyText"><?php echo $lang['admin']['docs']['use_rich_text'];?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td class="tdTitle"><?php echo $lang['admin']['docs']['site_doc']; ?></td>
  </tr>
  <tr>
    <td class="tdRichText"><span class="copyText"><strong><?php echo $lang['admin']['docs']['doc_name'];?></strong></span> <input name="doc_name" class="textbox" value="<?php if(isset($results[0]['doc_name'])) echo $results[0]['doc_name']; ?>" type="text" maxlength="255" /></td>
  </tr>
  <tr <?php if(evoHideBol(17)==true || $results[0]['doc_id']!=7){echo 'class="elementHide"';} ?>>
    <td class="tdRichText"><span class="copyText"><strong>Sub-Heading</strong></span> <input name="sub_heading" class="textbox" value="<?php if(isset($results[0]['sub_heading'])) echo $results[0]['sub_heading']; ?>" type="text" maxlength="255" /></td>
  </tr>
  <tr>
  	<td class="tdRichText"><span class="copyText"><strong>Doc position in Site doc menu (0 to hide)</strong></span>
<input name="doc_pos" class="textbox" value="<?php if(isset($results[0]['doc_pos'])) echo $results[0]['doc_pos']; ?>" type="text" size="4" maxlength="10" />
</td>
  </tr>
  
   <tr <?php evoHide(14); ?>>
    <?php if ($catResults) { ?>
    <td class="tdRichText"><span class="copyText"><strong><?php echo 'Category';?></strong></span> 
		<select name="catId" class="textbox">
			<option value="0">No Category</option>
			<?php for ($x=0; $x<count($catResults); $x++) { 
			
					if ($catResults[$x]['catId'] > 0) { ?>
			
			<option value="<?php echo $catResults[$x]['catId']; ?>" <?php if($results[0]['catId'] == $catResults[$x]['catId']) echo "selected"; ?>><?php echo $catResults[$x]['catName']; ?></option>
			
			<?php }
				} ?>
			
		</select>
		
	</td>
	<?php } else {?>
	<td class="tdRichText"><span class="copyText"><strong><?php echo 'No Categories Have Been Created';?></strong></span><input type="hidden" value="0" name="cat" /></td>
	<?php } ?>
  </tr>
  <tr <?php evoHide(23); ?>>
    <td class="tdText"><strong>Include In Navigation:</strong>
    	<select name="menuShow" class="textbox">
			<option value="0" <?php if($results[0]['menuShow'] == 0) echo "selected"; ?>>No</option>
			<option value="1" <?php if($results[0]['menuShow'] == 1) echo "selected"; ?>>Yes</option>
		</select>
	</td>
  </tr> 
  
  <tr>
    <td class="tdRichText">
<?php
$oFCKeditor = new FCKeditor('FCKeditor') ;
$oFCKeditor->BasePath = $GLOBALS['rootRel'].'admin/includes/rte/';
if(isset($results[0]['doc_content'])){ 
$oFCKeditor->Value = $results[0]['doc_content'];
} else {
$oFCKeditor->Value = "";
}
$oFCKeditor->Create();
?></td>
  </tr>
  <tr>
    <td class="tdRichText">

<!-- <rf> search engine friendly mod -->
<?php if($config['seftags']) { ?>
		<p>
                <table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
                  <tr> 
                    <td colspan="2" class="tdTitle"><strong>DO NOT EDIT anything in the section below without consulting your account manager first</strong></td>
                  </tr>
                  <tr> 
                    <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['browser_title']; ?></strong></td>
                    <td align="left"><input name="doc_metatitle" type="text" size="35" class="textbox" value="<?php if(isset($results[0]['doc_metatitle'])) echo $results[0]['doc_metatitle']; ?>" /></td>
                  </tr>
                  <tr> 
                    <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['settings']['meta_desc'];?></strong></td>
                    <td align="left"><textarea name="doc_metadesc" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['doc_metadesc'])) echo $results[0]['doc_metadesc']; ?></textarea></td>
                  </tr>
                  <tr> 
                    <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['settings']['meta_keywords'];?></strong> <?php echo $lang['admin']['settings']['comma_separated'];?></td>
                    <td align="left"><textarea name="doc_metakeywords" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['doc_metakeywords'])) echo $results[0]['doc_metakeywords']; ?></textarea></td>
                  </tr>
<?php if($config['sefcustomurl'] == 1) { ?>				  
                  <tr> 
                    <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo 'Custom URL:';?></strong></td>
                    <td align="left">http://ccroot/<input name="doc_sefurl" type="text" size="20" class="textbox" value="<?php if(isset($results[0]['doc_sefurl'])) echo $results[0]['doc_sefurl']; ?>" />/i_xx.html</td>
                  </tr>
<?php } ?>				  				  
                </table>
		</p>
<?php } ?>
<!-- <rf> end mod -->

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
    <td class="tdTitle" width="50%"><?php echo $lang['admin']['docs']['doc_name2']; ?></td>
    <td class="tdTitle <?php evoHideAlt(14); ?>" width="20%">Document Category</td>
    <td class="tdTitle <?php evoHideAlt(102); ?>" align="center" width="10%">Language</td>
    <td class="tdTitle" align="center" width="10%">Edit</td>
    <td class="tdTitle" align="center" width="10%">Delete</td>
  </tr>
<?php /*?>  <tr>
    <td width="80%" class="tdOdd"><a href="<?php echo $GLOBALS['rootRel']."index.php"; ?>" target="_blank" class="txtLink">Homepage</a></td>
    <td align="center" width="10%" class="tdOdd"><a <?php if(permission("documents","edit")==TRUE){ ?>href="/admin/docs/home.php" class="txtLink"<?php } else { echo $link401; } ?> ><img src="/admin/images/edit.gif" alt="Edit" title="Edit" border="0" width="16" /></a></td>
    <td align="center" width="10%" class="tdOdd"><img src="/admin/images/padlock.png" alt="Locked" title="Locked" width="12" border="0" /></td>
  </tr><?php */?>
  <?php 
  if($results == TRUE){
  	for ($i=0; $i<count($results); $i++){ 
  	
	$cellColor = "";
	$cellColor = cellColor($i);
	
  ?>
  <tr>
    <td width="50%" class="<?php echo $cellColor; ?>"><a href="<?php echo $GLOBALS['rootRel']."index.php?act=viewDoc&amp;docId=".$results[$i]['doc_id']; ?>" target="_blank" class="txtLink"><?php echo $results[$i]['doc_name']; ?></a></td>
    <td width="20%" class="<?php echo $cellColor; ?> tdText  <?php evoHideAlt(14); ?>"><?php echo $results[$i]['catName']; ?></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?> <?php evoHideAlt(102); ?>"><a <?php if(permission("documents","edit")==TRUE){ ?>href="languages.php?doc_master_id=<?php echo $results[$i]['doc_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><?php echo $lang['admin']['docs']['languages']; ?></a></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a <?php if(permission("documents","edit")==TRUE){ ?>href="?edit=<?php echo $results[$i]['doc_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><img src="/admin/images/edit.gif" alt="Edit" title="Edit" width="16" border="0" /></a></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>">
    <?php if(permission("documents","delete")==TRUE && $results[$i]['locked'] != 1){ ?>
    <a href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>?','?delete=<?php echo $results[$i]['doc_id']; ?>');"><img src="/admin/images/del.gif" alt="Delete" title="Delete" border="0" /></a>
    <?php }else{ echo'<img src="/admin/images/padlock.png" alt="Locked" title="Locked" width="12" border="0" />';} ?>
    
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
