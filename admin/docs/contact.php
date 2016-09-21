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

if(isset($_POST['docId']) && $_POST['docId']>0){
	
	$record["doc_name"] = $db->mySQLSafe($_POST['doc_name']);	
			
	$record["doc_content"] = $db->mySQLSafe($_POST['FCKeditor']);
								
	$where = "type = ".$db->mySQLSafe($_POST['docId']);
	
	$update =$db->update($glob['dbprefix']."CubeCart_contact", $record, $where);
			
	if($update == TRUE){
		$msg = "<p class='infoText'>'".$_POST['doc_name']."' ".$lang['admin']['docs']['update_success']."</p>"; 
		
		/// Robz Sitemap Addition Part #2
		$updRec["type"] = $db->mySQLSafe("Document");
		$updRec["aspect_id"] = $db->mySQLSafe($_POST['docId']);
		$updRec["action"] = $db->mySQLSafe("Update");
		
		$insert = $db->insert($glob['dbprefix']."CubeCart_mods", $updRec);
		/// END Part #2
		
		
	} else {
		$msg = "<p class='warnText'>'".$_POST['doc_name']."' ".$lang['admin']['docs']['update_fail']."</p>"; 
	}

}
// retrieve current documents
if(!isset($_GET['mode'])){
	
	// make sql query
	if(isset($_GET['edit']) && $_GET['edit']>0){
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_contact WHERE type = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_contact ORDER BY type ASC";
	} 
	
	// query database
	$results = $db->select($query);
} // end if mode is not new
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle"><?php echo $lang['admin']['docs']['contact']; ?></p></td>
  </tr>
</table>
<?php if((isset($_GET['edit']) && $_GET['edit']>0 && permission("documents","edit")==TRUE) || (isset($_GET['mode']) && $_GET['mode']=="new" && permission("documents","write")==TRUE)){ ?>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/docs/contact.php" target="_self" method="post" language="javascript">
<p class="copyText"><?php echo $lang['admin']['docs']['use_rich_text'];?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td class="tdTitle"><?php echo $lang['admin']['docs']['contact']; ?></td>
  </tr>
  <tr>
    <td class="tdRichText"><span class="copyText"><strong><?php echo $lang['admin']['docs']['doc_name'];?></strong></span> <input name="doc_name" class="textbox" value="<?php if(isset($results[0]['doc_name'])) echo $results[0]['doc_name']; ?>" type="text" maxlength="255" /></td>
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
    <td class="tdTitle" width="80%"><?php echo $lang['admin']['docs']['doc_name2']; ?></td>
    <td class="tdTitle" colspan="3" align="center" width="20%"><?php echo $lang['admin']['docs']['action']; ?></td>
  </tr>
  <?php 
  if($results == TRUE){
  	for ($i=0; $i<count($results); $i++){ 
  	
	$cellColor = "";
	$cellColor = cellColor($i);
	
  ?>
  <tr>
    <td width="80%" class="tdText <?php echo $cellColor; ?>"><?php echo $results[$i]['doc_name']; ?></td>
    <td align="center" width="20%" class="<?php echo $cellColor; ?>"><a <?php if(permission("documents","edit")==TRUE){ ?>href="?edit=<?php echo $results[$i]['type']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><?php echo $lang['admin']['edit']; ?></a></td>
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
