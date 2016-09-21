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
if(isset($_POST['save_country'])){
	$where = 'id = '.$db->mySQLSafe($_GET['edit']);
	$record['iso'] = $db->mySQLSafe($_POST['iso']);
	$record['printable_name'] = $db->mySQLSafe($_POST['name']);
	$record['iso3'] = $db->mySQLSafe($_POST['iso_3']);
	$record['numcode'] = $db->mySQLSafe($_POST['numcode']);
	$update = $db->update($glob['dbprefix'].'CubeCart_iso_countries',$record,$where);
	header('location: /admin/customers/countries.php');
}
if(isset($_POST['add_country'])){
	$record['iso'] = $db->mySQLSafe($_POST['iso']);
	$record['printable_name'] = $db->mySQLSafe($_POST['name']);
	$record['iso3'] = $db->mySQLSafe($_POST['iso_3']);
	$record['numcode'] = $db->mySQLSafe($_POST['numcode']);
	$update = $db->insert($glob['dbprefix'].'CubeCart_iso_countries',$record);
	header('location: /admin/customers/countries.php');
}
if(isset($_GET['delete'])){
	$where = 'id = '.$db->mySQLSafe($_GET['delete']);
	$delete = $db->delete($glob['dbprefix'].'CubeCart_iso_countries',$where,1);
	header('location: /admin/customers/countries.php');
}
include("../includes/header.inc.php"); ?>
<p class="pageTitle">Country List</p>
<form id="form1" name="form1" method="post" action="">
  <table border="0" cellspacing="0" cellpadding="4" width="100%" class="mainTable">
    <tr>
      <td align="center" class="tdTitle">Id</td>
      <td align="center" class="tdTitle">Iso Code</td>
      <td class="tdTitle">Name</td>
      <td align="center" class="tdTitle">Iso 3 Code</td>
      <td align="center" class="tdTitle">Numeric Code</td>
      <td colspan="2" align="center" class="tdTitle">Actions</td>
    </tr>
<?php $countries = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_iso_countries ORDER BY printable_name');
if($countries==true){
	for($i = 0; $i < count($countries); $i++){ ?>
    <?php if(isset($_GET['edit']) && $_GET['edit']==$countries[$i]['id']){ ?>
    <tr>
      <td align="center" class="<?=cellColor($i); ?> copyText"><?=$countries[$i]['id']; ?></td>
      <td align="center" class="<?=cellColor($i); ?> copyText">
        <label for="iso"></label>
      <input name="iso" type="text" id="iso" size="3" value="<?=$countries[$i]['iso']; ?>" /></td>
      <td class="<?=cellColor($i); ?> copyText"><label for="name"></label>
      <input type="text" name="name" id="name" value="<?=$countries[$i]['printable_name']; ?>" /></td>
      <td align="center" class="<?=cellColor($i); ?> copyText"><label for="iso_3"></label>
      <input name="iso_3" type="text" id="iso_3" size="3" value="<?=$countries[$i]['iso3']; ?>" /></td>
      <td align="center" class="<?=cellColor($i); ?> copyText"><label for="numcode"></label>
      <input name="numcode" type="text" id="numcode" size="6" value="<?=$countries[$i]['numcode']; ?>" /></td>
      <td colspan="2" align="center" class="<?=cellColor($i); ?> copyText"><input type="submit" name="save_country" id="save_country" value="Save Country" /></td>
    </tr>
    <? }else{ ?>
    <tr>
      <td align="center" class="<?=cellColor($i); ?> copyText"><?=$countries[$i]['id']; ?></td>
      <td align="center" class="<?=cellColor($i); ?> copyText"><?=$countries[$i]['iso']; ?></td>
      <td class="<?=cellColor($i); ?> copyText"><?=$countries[$i]['printable_name']; ?></td>
      <td align="center" class="<?=cellColor($i); ?> copyText"><?=$countries[$i]['iso3']; ?></td>
      <td align="center" class="<?=cellColor($i); ?> copyText"><?=$countries[$i]['numcode']; ?></td>
      <td align="center" class="<?=cellColor($i); ?> copyText"><a href="?edit=<?=$countries[$i]['id']; ?>" class="txtLink">Edit</a></td>
      <td align="center" class="<?=cellColor($i); ?> copyText"><a href="?delete=<?=$countries[$i]['id']; ?>" class="txtLink">Delete</a></td>
    </tr>
    <? } ?>
<? 	}
}
?>
<?php if(!isset($_GET['edit'])){ ?>
    <tr>
      <td align="center" class="<?=cellColor($i); ?> copyText">Add:</td>
      <td align="center" class="<?=cellColor($i); ?> copyText">
        <label for="iso"></label>
      <input name="iso" type="text" id="iso" size="3" /></td>
      <td class="<?=cellColor($i); ?> copyText"><label for="name"></label>
      <input type="text" name="name" id="name" /></td>
      <td align="center" class="<?=cellColor($i); ?> copyText"><label for="iso_3"></label>
      <input name="iso_3" type="text" id="iso_3" size="3" /></td>
      <td align="center" class="<?=cellColor($i); ?> copyText"><label for="numcode"></label>
      <input name="numcode" type="text" id="numcode" size="6" /></td>
      <td colspan="2" align="center" class="<?=cellColor($i); ?> copyText"><input type="submit" name="add_country" id="add_country" value="Add Country" /></td>
    </tr>
<? } ?>
  </table>
</form>
<?php include("../includes/footer.inc.php"); ?>
