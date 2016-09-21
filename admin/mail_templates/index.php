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
|	index.php
|   ========================================
|	Manage Mail Templates
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

$delete = $db->delete($glob['dbprefix']."CubeCart_mail_templates", $where, ""); 

	if($delete == TRUE){
		$msg = "<p class='infoText'>".$lang['admin']['docs']['delete_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['docs']['delete_fail']."</p>";
	}
} elseif(isset($_POST['id']) && $_POST['id']>0 && !isset($_POST['save_as'])){

$record["name"] = $db->mySQLSafe($_POST['name']);	
$record["mail"] = $db->mySQLSafe($_POST['FCKeditor']);
							
$where = "id = ".$db->mySQLSafe($_POST['id']);

$update =$db->update($glob['dbprefix']."CubeCart_mail_templates", $record, $where);
			
	if($update == TRUE){
		$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['docs']['update_success']."</p>"; 
	} else {
		$msg = "<p class='warnText'>'".$_POST['name']."' ".$lang['admin']['docs']['update_fail']."</p>"; 
	}

} elseif(isset($_POST['id']) && (empty($_POST['id']) || isset($_POST['save_as']))){

$record["name"] = $db->mySQLSafe($_POST['name']);		
$record["mail"] = $db->mySQLSafe($_POST['FCKeditor']);	

$insert = $db->insert($glob['dbprefix']."CubeCart_mail_templates", $record);

	if($insert == TRUE){
		$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['docs']['add_success']."</p>";
	} else {
		$msg = "<p class='infoText'>".$lang['admin']['docs']['add_fail']."</p>";
	}
}

// retrieve current documents
if(!isset($_GET['mode'])){
	
	// make sql query
	if(isset($_GET['edit']) && $_GET['edit']>0){
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_mail_templates WHERE id = %s", $db->mySQLSafe($_GET['edit'])); 
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_mail_templates ORDER BY name ASC";
	} 
	
	// query database
	$results = $db->select($query);
} // end if mode is not new
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Mail Templates</p></td>
    <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a <?php if(permission("documents","write")==TRUE){?>href="?mode=new" class="txtLink"<?php } else { echo $link401; } ?>><img src="../images/buttons/new.gif" alt="" hspace="4" border="0" title="" /><?php echo $lang['admin']['add_new']; ?></a></td><?php } ?>
  </tr>
</table>
<?php if((isset($_GET['edit']) && $_GET['edit']>0 && permission("documents","edit")==TRUE) || (isset($_GET['mode']) && $_GET['mode']=="new" && permission("documents","write")==TRUE)){ ?>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/mail_templates/index.php" target="_self" method="post" language="javascript">
<p class="copyText">Here you can create and manage mail templates which can then be used when emailing customers.</p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
  	<td class="tdTitle">Mail Template</td>
  </tr>
  <tr>
    <td class="tdRichText"><span class="copyText"><strong>Template Name:</strong></span> <input name="name" class="textbox" value="<?php if(isset($results[0]['name'])) echo $results[0]['name']; ?>" type="text" maxlength="255" /></td>
  </tr>
  <tr>
    <td class="tdRichText">
<?php
$oFCKeditor = new FCKeditor('FCKeditor') ;
$oFCKeditor->BasePath = $GLOBALS['rootRel'].'admin/includes/rte/';
if(isset($results[0]['mail'])){ 
$oFCKeditor->Value = $results[0]['mail'];
} else {
$oFCKeditor->Value = "";
}
$oFCKeditor->Create();
?></td>
  </tr>
  <tr>
    <td class="tdRichText">

	<input type="hidden" value="<?php if(isset($_GET['edit'])) echo $_GET['edit']; ?>" name="id" />
	<input name="submit" type="submit" id="submit" class="submit" <?php if(isset($results) && $results == TRUE){ ?>value="Update Template"<?php } else { echo "value=\"Save Template\""; } ?> />
	  
      <?php if(isset($results) && $results == TRUE){ ?><input type="submit" name="save_as" id="save_as" value="Save As" class="submit" /><? } ?>
      
	</td>
  </tr>
</table>
</form>
<?php } else {
if(isset($msg)){
 echo stripslashes($msg);
} else { 
?>
<p class="copyText">Below is a list of all the current email templates. These can be edited and/or deleted at any time.</p>
<?php } ?>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
    <td class="tdTitle" width="80%">Mail Template Name</td>
    <td class="tdTitle" colspan="2" align="center" width="20%"><?php echo $lang['admin']['docs']['action']; ?></td>
  </tr>
  <?php 
  if($results == TRUE){
  	for ($i=0; $i<count($results); $i++){ 
  	
	$cellColor = "";
	$cellColor = cellColor($i);
	
  ?>
  <tr>
    <td width="80%" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['name']; ?></span></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a <?php if(permission("documents","edit")==TRUE){ ?>href="?edit=<?php echo $results[$i]['id']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><?php echo $lang['admin']['edit']; ?></a></td>
    <td align="center" width="5%" class="<?php echo $cellColor; ?>"><a <?php if(permission("documents","delete")==TRUE){ ?>href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>?','?delete=<?php echo $results[$i]['id']; ?>');" class="txtLink" <?php } else { echo $link401; } ?>><?php echo $lang['admin']['delete']; ?></a></td>
  </tr>
  <?php } // end loop
  } else { ?>
   <tr>
    <td colspan="4" class="tdText">No mail templates available.</td>
  </tr>
  <?php } ?>
</table>

<?php } ?>
<?php include("../includes/footer.inc.php"); ?>
