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
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

if(isset($_POST['update_gruop']) && $_POST['group_name']!=''){
	$where = 'groupId = '.$db->mySQLSafe($_GET['edit']);
	$record['groupName'] = $db->mySQLSafe($_POST['group_name']);
	$updateGroup = $db->update($glob['dbprefix'].'CubeCart_mail_group',$record,$where);
	if($updateGroup==true){
		header('location: /admin/mail_groups/');	
	}
}
if(isset($_POST['add_gruop'])){
	$record['groupName'] = $db->mySQLSafe($_POST['group_name']);
	$insertGroup = $db->insert($glob['dbprefix'].'CubeCart_mail_group',$record);
	if($insertGroup==true){
		header('location: /admin/mail_groups/');	
	}
}
if(isset($_GET['delete'])){
	if($_GET['members']==0){
		$where = "groupId = ".$db->mySQLSafe($_GET['delete']);
		$delete = $db->delete($glob['dbprefix']."CubeCart_mail_group", $where, ""); 
	}else{
		header('location: /admin/mail_groups/?confirmdelete='.$_GET['delete'].'&members='.$_GET['members']);	
	}
}
if(isset($_POST['move_delete']) && $_POST['new_group'] > 0){
	$moveWhere = 'groupId = '.$_GET['confirmdelete'];
	$moveTo['groupId'] = $db->mySQLSafe($_POST['new_group']);
	$move = $db->update($glob['dbprefix'].'CubeCart_customer',$moveTo,$moveWhere);
	if($move==true){
		header('location: /admin/mail_groups/?delete='.$_GET['confirmdelete'].'&members=0');	
	}
}
if(isset($_POST['upload_add'])){
	if(!empty($_FILES["filename"])){
		if($_FILES["filename"]["type"] === "application/vnd.ms-excel"){
			$handle = fopen($_FILES["filename"]["tmp_name"], "r");

			while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
				
				$emailExists = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_customer WHERE email = '.$db->mySQLSafe($data[0]));
				if($emailExists==true){
					$addWhere = 'email = '.$db->mySQLSafe($data[0]);
					$addRecord['optIn1st'] = $db->mySQLSafe(1);
					$addRecord['groupId'] = $db->mySQLSafe($_POST['new_group']);
					$addUpdate = $db->update($glob['dbprefix'].'CubeCart_customer',$addRecord,$addWhere);
				}else{
					$newSubscriber['optIn1st'] = $db->mySQLSafe(1);
					$newSubscriber['type'] = $db->mySQLSafe(0);
					$newSubscriber['email'] = $db->mySQLSafe($data[0]);
					$newSubscriber['groupId'] = $db->mySQLSafe($_POST['new_group']);
					$insertEmails = $db->insert($glob['dbprefix'].'CubeCart_customer',$newSubscriber);
				}

			}
		}
	}
}
if(isset($_POST['upload_remove'])){
	if(!empty($_FILES["filename"])){
		if($_FILES["filename"]["type"] === "application/vnd.ms-excel"){
			$handle = fopen($_FILES["filename"]["tmp_name"], "r");

			while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
				//var_dump($data[0]);
				$emailExists = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_customer WHERE email = '.$db->mySQLSafe($data[0]));
				if($emailExists==true){
					if($emailExists[0]['type']==0){
						$addWhere = 'email = '.$db->mySQLSafe($data[0]);
						$addUpdate = $db->delete($glob['dbprefix'].'CubeCart_customer',$addWhere);
					}else{
						$addWhere = 'email = '.$db->mySQLSafe($data[0]);
						$addRecord['optIn1st'] = $db->mySQLSafe(0);
						$addUpdate = $db->update($glob['dbprefix'].'CubeCart_customer',$addRecord,$addWhere);
					}
				}

			}
		}
	}
}
include("../includes/header.inc.php"); ?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Mail Groups</p></td>
    <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a <?php if(permission("documents","write")==TRUE){?>href="?mode=new" class="txtLink"<?php } else { echo $link401; } ?>><img src="../images/buttons/new.gif" alt="" hspace="4" border="0" title="" /><?php echo $lang['admin']['add_new']; ?></a></td><?php } ?>
  </tr>
</table>
<?php
if(isset($_GET['edit']) && $_GET['edit'] > 0){ 

$currentGroup = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_mail_group WHERE groupId = '.$db->mySQLSafe($_GET['edit']));

?>
<form name="form1" method="post" action="<?=curPageURL(); ?>" class="copyText mainTable" style="padding:4px;">
  <label for="group_name">Group Name:</label>
  <input type="text" name="group_name" id="group_name" value="<?=$currentGroup[0]['groupName']; ?>">
  <input type="submit" name="update_gruop" id="update_gruop" value="Update" class="submit">
</form>
<? }elseif($_GET['mode']=='new'){ ?>
<form name="form1" method="post" action="<?=curPageURL(); ?>" class="copyText mainTable" style="padding:4px;">
  <label for="group_name">Group Name:</label>
  <input type="text" name="group_name" id="group_name" value="<?=$currentGroup[0]['groupName']; ?>">
  <input type="submit" name="add_gruop" id="add_gruop" value="Add Group" class="submit">
</form>
<? }elseif($_GET['confirmdelete']){ ?>
<form name="form2" method="post" action="<?=curPageURL(); ?>">
  <table border="0" align="center" cellpadding="4" cellspacing="0" class="copyText mainTable">
    <tr>
      <td colspan="2" class="tdTitle">Group you are about to delete contains <?=$_GET['members'] ?> members</td>
    </tr>
    <tr>
      <td align="right">Move members to:</td>
      <td align="left"><label for="new_gruoop"></label>
        <label for="new_group"></label>
        <select name="new_group" id="new_group">
        <?php
		$allGroups = $db->select('SELECT * FROM CubeCart_mail_group WHERE groupId!='.$db->mySQLSafe($_GET['confirmdelete']));
		if($allGroups==true){
			for($i = 0; $i < count($allGroups); $i++){
				echo '<option value="'.$allGroups[$i]['groupId'].'">'.$allGroups[$i]['groupName'].'</option>';
			}
		}
		?>
      </select></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" name="move_delete" id="move_delete" value="Move and Delete" class="submit"></td>
    </tr>
  </table>
</form>
<? }else{
$groups = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_mail_group ORDER BY groupName');
if($groups==true){ ?><table width="100%" border="0" cellpadding="4" cellspacing="0" class="mainTable">
  <tr>
    <td width="20" class="tdTitle">ID</td>
    <td class="tdTitle">Group name</td>
    <td width="110" align="center" class="tdTitle">Group Members</td>
    <td colspan="2" align="center" class="tdTitle" width="120">Actions</td>
  </tr>
<?	for($i = 0; $i < count($groups); $i++){	 ?>
<?php $memberCount = $db->numrows('SELECT email FROM '.$glob['dbprefix'].'CubeCart_customer WHERE groupId = '.$db->mySQLSafe($groups[$i]['groupId']).' AND optIn1st = 1'); ?>
  <tr>
    <td class="<?=cellColor($i); ?> copyText"><?=$groups[$i]['groupId']; ?></td>
    <td class="<?=cellColor($i); ?> copyText"><?=$groups[$i]['groupName']; ?></td>
    <td align="center" class="<?=cellColor($i); ?> copyText"><?=$memberCount ?></td>
    <td width="60" align="center" class="<?=cellColor($i); ?> copyText"><a href="?edit=<?=$groups[$i]['groupId']; ?>" class="txtLink">Edit</a></td>
    <td width="60" align="center" class="<?=cellColor($i); ?> copyText"><?php if($groups[$i]['groupId']!=1){ ?><a href="?delete=<?=$groups[$i]['groupId']; ?>&amp;members=<?=$memberCount ?>" class="txtLink">Delete</a><? } ?></td>
  </tr>
<?	} ?>
</table>
<? } ?>
<table width="100%"  border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td nowrap='nowrap' class="copyText">
    <form method="post" action="<?=curPageURL(); ?>" name="addNew" enctype="multipart/form-data">
    <table border="0" cellpadding="4" cellspacing="0" class="mainTable">
    <tr>
      <td colspan="2" align="center" class="tdTitle">
        <strong>Import to a group (Add emails)</strong>      </td>
      </tr>
    <tr>
      <td><strong>Group:</strong></td>
      <td>
    <label for="new_gruoop"></label>
        <label for="new_group"></label>
        <select name="new_group" id="new_group">
        <?php
		$allGroups = $db->select('SELECT * FROM CubeCart_mail_group WHERE groupId!='.$db->mySQLSafe($_GET['confirmdelete']));
		if($allGroups==true){
			for($i = 0; $i < count($allGroups); $i++){
				echo '<option value="'.$allGroups[$i]['groupId'].'">'.$allGroups[$i]['groupName'].'</option>';
			}
		}
		?>
      </select>
	</td></tr>
    <tr>
      <td><strong>File:</strong></td>
      <td>
    <input type="file" name="filename" />
	</td></tr>
    <tr>
      <td>&nbsp;</td>
      <td>
	<input type="submit" name="upload_add" value="Upload File"  class="submit" />
    </td></tr>
    </table>
	</form></td>
  </tr>
  <tr>
    <td nowrap='nowrap' class="copyText">
    <form method="post" action="<?=curPageURL(); ?>" name="remove" enctype="multipart/form-data">
    <table border="0" cellpadding="4" cellspacing="0" class="mainTable">
    <tr>
      <td colspan="2" align="center" class="tdTitle">
        <strong>Import to remove from a group:</strong>      </td>
      </tr>
    <tr>
      <td><strong>File:</strong></td>
      <td>
    <input type="file" name="filename" />
    </td></tr>
    <tr>
      <td>&nbsp;</td>
      <td>
	<input type="submit" name="upload_remove" value="Upload File"  class="submit" />
    </td></tr></table>
	</form>
    </td>
  </tr>
</table>
<? } ?>
<?php include("../includes/footer.inc.php"); ?>
