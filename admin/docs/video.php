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

// delete video
if(isset($_GET['delete']) && $_GET['delete']>0){
	$where = "id = ".$db->mySQLSafe($_GET['delete']);
	$delete = $db->delete($glob['dbprefix']."CubeCart_video", $where, ""); 
	if($delete == TRUE){
		$msg = "<p class='infoText2'>".$lang['admin']['docs']['delete_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['docs']['delete_fail']."</p>";
	}
}elseif(isset($_POST['docId'])){
	//form fields
	$record["title"] = $db->mySQLSafe($_POST['title']);
	$record["text"] = $db->mySQLSafe($_POST['text']);
	$record["embed"] = $db->mySQLSafe(stripslashes($_POST['embed']));
	$record["featured"] = $db->mySQLSafe($_POST['featured']);
	
	//update video
	if($_POST['docId']>0){
		$where = "id = ".$db->mySQLSafe($_POST['docId']);
		$update =$db->update($glob['dbprefix']."CubeCart_video", $record, $where);
		if($update == TRUE){
			$msg = "<p class='infoText'>'".$_POST['title']."' ".$lang['admin']['docs']['update_success']."</p>"; 
		} else {
			$msg = "<p class='warnText'>'".$_POST['title']."' ".$lang['admin']['docs']['update_fail']."</p>"; 
		}
	}elseif(empty($_POST['docId'])){
	//add new video
		$insert = $db->insert($glob['dbprefix']."CubeCart_video", $record);
		if($insert == TRUE){
			$msg = "<p class='infoText'>'".$_POST['doc_name']."' ".$lang['admin']['docs']['add_success']."</p>";
		} else {
			$msg = "<p class='infoText'>".$lang['admin']['docs']['add_fail']."</p>";
		}
	}
}

// Get videos
if(!isset($_GET['mode'])){
	if(isset($_GET['edit']) && $_GET['edit']>0){
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_video WHERE id = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_video ORDER BY title ASC";
	} 
	$results = $db->select($query);
}

//====================================================================================================================================
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Video</p></td>
    <?php
		if(!isset($_GET["mode"])){ ?>
        	<td align="right" valign="middle"><a class="addNew" <?php if(permission("documents","write")==TRUE){?>href="?mode=new"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['add_new']; ?></a></td>
		<?php } 
	?>
  </tr>
</table>
<?php if((isset($_GET['edit']) && $_GET['edit']>0 && permission("documents","edit")==TRUE) || (isset($_GET['mode']) && $_GET['mode']=="new" && permission("documents","write")==TRUE)){ ?>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/docs/video.php" target="_self" method="post" language="javascript">
<p class="copyText">Here you can embed videos from sites such as YouTube</p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td class="tdTitle" colspan="2">Add a Video</td>
  </tr>
  <tr>

    <td width="25%" align="left" valign="top" class="tdText"><strong>Title:</strong></td>

    <td valign="top"><input type="text=" name="title" id="title" style="width: 335px" value="<?php if(isset($results[0]['title'])) echo $results[0]['title']; ?>" /></td>

  </tr>
  <tr>

    <td width="25%" class="tdText"><strong>Description:</strong> <br /></td>

    <td><textarea name="text" type="text" class="textbox" rows="4" cols="50"><?php if(isset($results[0]['text'])) echo $results[0]['text']; ?></textarea></td>

  </tr>
    <tr>

    <td width="25%" align="left" valign="top" class="tdText"><strong>Embed Code:</strong></td>

    <td valign="top"><textarea name="embed" type="text" class="textbox" rows="4" cols="50"><?php if(isset($results[0]['embed'])) echo stripslashes($results[0]['embed']); ?></textarea></td>

  </tr>
  <tr>
      <td class="tdText"><strong>Featured: </strong></td>
      <td class="tdText"><select name="featured" class="textbox">
      	  <option value="0" <?php if(isset($results[0]['featured']) && $results[0]['featured']==0) echo "selected='selected'"; ?>>No</option>
          <option value="1" <?php if(isset($results[0]['featured']) && $results[0]['featured']==1) echo "selected='selected'"; ?>>Yes</option>
        </select>
      </td>
    </tr
  ><tr>
  	<td class="tdText"></td>
    <td class="tdText">

	<input type="hidden" value="<?php if(isset($_GET['edit'])) echo $_GET['edit']; ?>" name="docId" />
	<input name="submit" type="submit" id="submit" class="submit" <?php if(isset($results) && $results == TRUE){ ?>value="Update Video"<?php } else { echo 'value="Add Video"'; } ?> /></td>
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
    <td class="tdTitle" width="80%">Video Title</td>
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
    <td width="80%" class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['title']; ?></td>
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