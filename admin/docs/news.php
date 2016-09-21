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

$delete = $db->delete($glob['dbprefix']."CubeCart_news", $where, ""); 

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

$record["menuShow"] = $db->mySQLSafe($_POST['menuShow']);

if($_POST['menuShow'] == 0)
	$record["menuOrder"] = $db->mySQLSafe(99);
	
$record["doc_name"] = $db->mySQLSafe($_POST['doc_name']);	
$record["doc_pos"] = $db->mySQLSafe($_POST['doc_pos']);	
$record["featured"] = $db->mySQLSafe($_POST['featured']);
$record["isNews"] = 1;	
$record["date"] = $db->mySQLSafe($_POST['date']);	
$record["image"] = $db->mySQLSafe($_POST['imageName']);	
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

$update =$db->update($glob['dbprefix']."CubeCart_news", $record, $where);
			
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
		
		$update = $db->update($glob['dbprefix']."CubeCart_news", $modRec, $where);
		/// END Part #5
	} else {
		$msg = "<p class='warnText'>'".$_POST['doc_name']."' ".$lang['admin']['docs']['update_fail']."</p>"; 
	}

} elseif(isset($_POST['docId']) && empty($_POST['docId'])){
/// Robz Sitemap Addition Part #4
$record["lastModified"] = $db->mySQLSafe(time());
/// END Part #4

$record["menuShow"] = $db->mySQLSafe($_POST['menuShow']);

$record["doc_name"] = $db->mySQLSafe($_POST['doc_name']);
$record["catId"] = $db->mySQLSafe($_POST['catId']);
$record["featured"] = $db->mySQLSafe($_POST['featured']);	
$record["isNews"] = 1;		
$record["date"] = time();
$record["image"] = $db->mySQLSafe($_POST['imageName']);	
$record["doc_content"] = $db->mySQLSafe($_POST['FCKeditor']);	
/* <rf> search engine friendly url mod */
if($config['seftags']) {
	$record["doc_metatitle"] = $db->mySQLSafe($_POST['doc_metatitle']);
	$record["doc_metadesc"] = $db->mySQLSafe($_POST['doc_metadesc']);
	$record["doc_metakeywords"] = $db->mySQLSafe($_POST['doc_metakeywords']);
	if($config['sefcustomurl'] == 1) $record["doc_sefurl"] = $db->mySQLSafe($_POST['doc_sefurl']);		
}
/* <rf> end mod */

$insert = $db->insert($glob['dbprefix']."CubeCart_news", $record);

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
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_news WHERE doc_id != 5 AND isNews = 1 AND doc_id = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_news WHERE doc_id != 5 AND isNews = 1 ORDER BY date ASC";
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
    <td nowrap='nowrap'><p class="pageTitle">News</p></td>
    <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a <?php if(permission("documents","write")==TRUE){?>href="?mode=new" class="addNew"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['add_new']; ?></a></td><?php } ?>
  </tr>
</table>
<?php if((isset($_GET['edit']) && $_GET['edit']>0 && permission("documents","edit")==TRUE) || (isset($_GET['mode']) && $_GET['mode']=="new" && permission("documents","write")==TRUE)){ ?>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/docs/news.php" target="_self" method="post" language="javascript">
<p class="copyText"><?php echo $lang['admin']['docs']['use_rich_text'];?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td class="tdTitle" colspan="2"><?php echo $lang['admin']['docs']['site_doc']; ?></td>
  </tr>
  <tr>
    <td class="tdRichText"><span class="copyText"><strong><?php echo $lang['admin']['docs']['doc_name'];?></strong></span></td><td class="tdText"> <input name="doc_name" class="textbox" value="<?php if(isset($results[0]['doc_name'])) echo $results[0]['doc_name']; ?>" type="text" maxlength="255" /></td>
  </tr>
  <tr>
    <td class="tdText">Featured News Item:</td><td class="tdText"><select name="featured" class="textbox">
			<option value="0" <?php if($results[0]['featured'] == 0) echo "selected"; ?>>No</option>
			<option value="1" <?php if($results[0]['featured'] == 1) echo "selected"; ?>>Yes</option>
		</select>
	</td>
  </tr>
 <?php /*?> <tr>
    <td class="tdText">Include In Navigation:</td><td class="tdText"><select name="menuShow" class="textbox">
			<option value="0" <?php if($results[0]['menuShow'] == 0) echo "selected"; ?>>No</option>
			<option value="1" <?php if($results[0]['menuShow'] == 1) echo "selected"; ?>>Yes</option>
		</select>
	</td>
  </tr><?php */?>
  <tr>
  	<td class="tdRichText"><span class="copyText"><strong>Date/Time</strong></span></td><td class="tdText">
    <?php if(isset($results[0]['date'])){echo date("j F Y, H:i", $results[0]['date']);}else{echo date("j F Y, H:i", time());} ?>
<input name="date" value="<?php if(isset($results[0]['date'])) echo $results[0]['date']; ?>" type="hidden" />
</td>
  </tr>

  
  
  <tr>
    <td class="tdRichText" colspan="2">
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
  
  <?php /*?><tr <?php evoHide(44); ?>>
        <td align="left" valign="top" class="tdText"><?php echo $lang['admin']['categories']['image_optional'];?></td>
      <td valign="top"><div id="selectedImage">
          <?php if(!empty($results[0]['image'])){ ?>
          <img src="<?php echo $GLOBALS['rootRel']."images/uploads/".$results[0]['image']; ?>" alt="<?php echo $results[0]['image']; ?>" title="" />
          <div  style="padding: 3px;">
            <input type="button" class="submit" src="../images/remove.gif" name="remove" value="<?php echo $lang['admin']['remove']; ?>" onclick="addImage('','')" />
          </div>
          <?php } ?>
        </div>
        <div id="imageControls">
          <table border="0" cellspacing="0" cellpadding="3">
            <tr>
              <td><a href="/admin/filemanager/upload.php" class="submit" target="_blank"><?php echo $lang['admin']['categories']['upload_new_image']; ?></a></td>
            </tr>
            <tr>
              <td><input name="browse" class="submit" type="button" id="browse" onclick="openPopUp('../filemanager/browse.php?custom=1&amp;cat=7','filemanager',450,500)" value="Browse Images" /></td>
            </tr>
          </table>
        </div>
        <input type="hidden" name="image" id="imageName" value="<?php echo $results[0]['image']; ?>" />
      </td>
    </tr>
  <tr><?php */?>
    <td class="tdRichText" colspan="2">

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
<p class="copyText">Below is a list of all the current news items. These can be edited and/or deleted at any time.</p>
<?php } ?>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
    <td class="tdTitle" width="60%"><?php echo $lang['admin']['docs']['doc_name2']; ?></td>
    <td class="tdTitle" width="15%">Added</td>
    <td class="tdTitle" width="5%">Featured</td>
    <td class="tdTitle <?php evoHideAlt(102); ?>" align="center" width="10%">Language</td>
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
    <td width="60%" class="<?php echo $cellColor; ?>"><a href="<?php echo $GLOBALS['rootRel']."index.php?act=viewDoc&amp;docId=".$results[$i]['doc_id']; ?>" target="_blank" class="txtLink"><?php echo $results[$i]['doc_name']; ?></a></td>
    <td width="15%" class="<?php echo $cellColor; ?>"><?php echo "<span class='tdText'>".date("j F Y, H:i", $results[$i]['date'])."</span>"; ?></td>
    <td width="5%" class="<?php echo $cellColor; ?>"><?php if($results[$i]['featured']==1){echo "<span class='tdText'>Yes</span>";} ?></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?> <?php evoHideAlt(102); ?>"><a <?php if(permission("documents","edit")==TRUE){ ?>href="languagesNews.php?doc_master_id=<?php echo $results[$i]['doc_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><?php echo $lang['admin']['docs']['languages']; ?></a></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a <?php if(permission("documents","edit")==TRUE){ ?>href="?edit=<?php echo $results[$i]['doc_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><img src="/admin/images/edit.gif" alt="Edit" title="Edit" width="16" border="0" /></a></td>
    <td align="center" width="5%" class="<?php echo $cellColor; ?>"><a <?php if(permission("documents","delete")==TRUE && $results[$i]['doc_id'] != 3 && $results[$i]['doc_id'] != 4 && $results[$i]['doc_id'] != 5){ ?>href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>?','?delete=<?php echo $results[$i]['doc_id']; ?>');" class="txtLink" <?php } else { echo $link401; } ?>><img src="/admin/images/del.gif" alt="Delete" title="Delete" border="0" /></a></td>
  </tr>
  <?php } // end loop
  } else { ?>
   <tr>
    <td colspan="4" class="tdText">There is no news in the database.</td>
  </tr>
  <?php } ?>
</table>

<?php } ?>
<?php include("../includes/footer.inc.php"); ?>
