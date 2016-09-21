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

$delete = $db->delete($glob['dbprefix']."CubeCart_slider", $where, ""); 

	if($delete == TRUE){
		$msg = "<p class='infoText2'>".$lang['admin']['docs']['delete_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['docs']['delete_fail']."</p>";
	}
} elseif(isset($_POST['docId']) && $_POST['docId']>0){


$record["image"] = $db->mySQLSafe(preg_replace('/thumb_/i', '', $_POST['image']));	
$record["text"] = $db->mySQLSafe($_POST['text']);
$record["title"] = $db->mySQLSafe($_POST['title']);
$record["link"] = $db->mySQLSafe($_POST['link']);
$record["slide_pos"] = $db->mySQLSafe($_POST['slide_pos']);

							
$where = "id = ".$db->mySQLSafe($_POST['docId']);

$update =$db->update($glob['dbprefix']."CubeCart_slider", $record, $where);
			
	if($update == TRUE){
		$msg = "<p class='infoText'>'".$_POST['title']."' ".$lang['admin']['docs']['update_success']."</p>"; 
	} else {
		$msg = "<p class='warnText'>'".$_POST['title']."' ".$lang['admin']['docs']['update_fail']."</p>"; 
	}

} elseif(isset($_POST['docId']) && empty($_POST['docId'])){


$record["image"] = $db->mySQLSafe(preg_replace('/thumb_/i', '', $_POST['image']));	
$record["text"] = $db->mySQLSafe($_POST['text']);
$record["title"] = $db->mySQLSafe($_POST['title']);
$record["link"] = $db->mySQLSafe($_POST['link']);
$record["slide_pos"] = $db->mySQLSafe($_POST['slide_pos']);

$insert = $db->insert($glob['dbprefix']."CubeCart_slider", $record);

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
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_slider WHERE id = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_slider ORDER BY slide_pos ASC";
	} 
	
	// query database
	$results = $db->select($query);
} // end if mode is not new
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Slider Manager</p></td>
    <?php
		if(!isset($_GET["mode"])){ ?>
        	<td align="right" valign="middle"><a class="addNew" <?php if(permission("documents","write")==TRUE){?>href="?mode=new"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['add_new']; ?></a></td>
		<?php } 
	?>
  </tr>
</table>
<?php if((isset($_GET['edit']) && $_GET['edit']>0 && permission("documents","edit")==TRUE) || (isset($_GET['mode']) && $_GET['mode']=="new" && permission("documents","write")==TRUE)){ ?>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/slider/index.php" target="_self" method="post" language="javascript">
<p class="copyText"><?php echo $lang['admin']['docs']['use_rich_text'];?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td class="tdTitle" colspan="2">Slider Content</td>
  </tr>
  <tr <?php evoHide(22); ?>>

    <td width="25%" align="left" valign="top" class="tdText"><strong>Slide Title:</strong></td>

    <td valign="top"><input type="text" name="title" id="title" class="textbox" value="<?php if(isset($results[0]['title'])) echo $results[0]['title']; ?>" /></td>

  </tr>
  <tr>

    <td width="25%" align="left" valign="top" class="tdText"><strong>Slide Order:</strong><br />(Enter 0 to hide)</td>

    <td valign="top"><input type="text" name="slide_pos" id="slide_pos" class="textbox" value="<?php if(isset($results[0]['slide_pos'])) echo $results[0]['slide_pos']; ?>" /></td>

  </tr>
  <tr <?php evoHide(21); ?>>

    <td width="25%" class="tdText"><strong>Slide Description:</strong> <br /></td>

    <td><textarea name="text" type="text" class="textarea" rows="4" cols="50"><?php if(isset($results[0]['text'])) echo $results[0]['text']; ?></textarea></td>

  </tr>
    <tr <?php evoHide(19); ?>>

    <td width="25%" align="left" valign="top" class="tdText"><strong>Slide Link:</strong></td>

    <td valign="top"><input type="text=" name="link" id="link" class="textbox" value="<?php if(isset($results[0]['link'])) echo $results[0]['link']; ?>" /></td>

  </tr>
  
  <tr <?php evoHide(44); ?>>
        <td align="left" valign="top" class="tdText"><strong>Image: </strong></td>
      <td valign="top"><div id="selectedImage">
          <?php if(!empty($results[0]['image'])){ ?>
          <img src="<?php echo $GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$results[0]['image']; ?>" alt="<?php echo $results[0]['image']; ?>" title="" />
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
              <td><input name="browse" class="submit" type="button" id="browse" onclick="openPopUp('../filemanager/browse.php?custom=1&amp;cat=3','filemanager',450,500)" value="Browse Images" /></td>
            </tr>
          </table>
        </div>
        <input type="hidden" name="image" id="imageName" value="<?php echo $results[0]['image']; ?>" />
      </td>
    </tr>

  <tr>
    <td class="tdRichText">

	<input type="hidden" value="<?php if(isset($_GET['edit'])) echo $_GET['edit']; ?>" name="docId" />
	<input name="submit" type="submit" id="submit" class="submit" <?php if(isset($results) && $results == TRUE){ ?>value="Update"<?php } else { echo "value=\"".$lang['admin']['docs']['save_doc']."\""; } ?> /></td>
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
  	<td class="tdTitle" width="60%">Slide</td>
    <td class="tdTitle" width="20%">Order</td>
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
  	<td width="60%" class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['title']; ?></td>
    <td width="20%" class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['slide_pos']; ?></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?> copyText"><a <?php if(permission("documents","edit")==TRUE){ ?>href="?edit=<?php echo $results[$i]['id']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><img src="/admin/images/edit.gif" alt="Edit" title="Edit" width="16" border="0" /></a> 
	</td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?> copyText">
	<?php if(permission("documents","delete")==TRUE){ ?><a href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>?','?delete=<?php echo $results[$i]['id']; ?>');" ><img src="/admin/images/del.gif" alt="Delete" title="Delete" border="0" /></a><?php } ?></td>
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