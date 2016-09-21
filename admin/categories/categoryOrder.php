<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.6
|   ========================================
|   by Alistair Brookbanks
|   CubeCart is a Trade Mark of Devellion Limited
|   (c) 2005 Devellion Limited
|   Devellion Limited,
|   Westfield Lodge,
|   Westland Green,
|   Little Hadham,
|   Nr Ware, HERTS.
|   SG11 2AL
|   UNITED KINGDOM
|   http://www.devellion.com
|   UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Monday, December 26th, 2005
|   Email: info (at) cubecart (dot) com
|   License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|   categoryOrder.php
|   ========================================
|   Specify Category Display Order
|   Customized by Sir William Copyright 2005
|   http://www.swscripts.com/
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
include("../../includes/sslSwitch.inc.php");
include("../includes/auth.inc.php");
include("../includes/functions.inc.php");
if(permission("categories","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

checkDBexist();

if(isset($_POST['config'])){
	$config = fetchDbConfig("config");
	$msg = writeDbConf($_POST['config'],"config", $config, "config");
}
$config = fetchDbConfig("config");

$whichCat = $_GET['catId'];

if(isset($_GET['moveup'])) {
	// Get info on the item we're moving.
	$query = "SELECT cat_name, cat_father_id, disp_order FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($_GET['moveup']);
	$results = $db->select($query);
	$currentname = $results[0]['cat_name'];
	$currentposition = $results[0]['disp_order'];
	$whichCat = $results[0]['cat_father_id'];

	// Get info on the item it's displacing.
	$query = "SELECT cat_name, disp_order FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($_GET['displace']);
	$results = $db->select($query);
	$displacedname = $results[0]['cat_name'];
	$displacedposition = $results[0]['disp_order'];

	// Verify that they are indeed consecutive.
	if($currentposition - $displacedposition == 1){
		// Update the two records and swap positions.
		$record["disp_order"] = $db->mySQLSafe($displacedposition);
		$where = "cat_id=".$db->mySQLSafe($_GET['moveup']);
		$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);

		$record["disp_order"] = $db->mySQLSafe($currentposition);
		$where = "cat_id=".$db->mySQLSafe($_GET['displace']);
		$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
	  $msg = "<i>".$currentname."</i>".$lang['admin']['categories']['cdo_moved_up']."<i>".$displacedname."</i>.";
	}
}

if(isset($_GET['movedown'])) {
	// Get info on the item we're moving.
	$query = "SELECT cat_name, cat_father_id, disp_order FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($_GET['movedown']);
	$results = $db->select($query);
	$currentname = $results[0]['cat_name'];
	$currentposition = $results[0]['disp_order'];
	$whichCat = $results[0]['cat_father_id'];

	// Get info on the item it's displacing.
	$query = "SELECT cat_name, disp_order FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($_GET['displace']);
	$results = $db->select($query);
	$displacedname = $results[0]['cat_name'];
	$displacedposition = $results[0]['disp_order'];

	// Verify that they are indeed consecutive.
	if($displacedposition - $currentposition == 1){
		// Update the two records and swap positions.
		$record["disp_order"] = $db->mySQLSafe($displacedposition);
		$where = "cat_id=".$db->mySQLSafe($_GET['movedown']);
		$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);

		$record["disp_order"] = $db->mySQLSafe($currentposition);
		$where = "cat_id=".$db->mySQLSafe($_GET['displace']);
		$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
	  $msg = "<i>".$currentname."</i>".$lang['admin']['categories']['cdo_moved_down']."<i>".$displacedname."</i>.";
	}
}


// make sql query
if($whichCat > 0){
	
  $query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = ".$db->mySQLSafe($whichCat)." ORDER BY disp_order ASC";
	while($whichCat > 0){
	  $query2 = "SELECT cat_name, cat_father_id FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($whichCat);
		$results2 = $db->select($query2);
		$breadcrumb = " > <a href=\"?catId=".$whichCat."\" class=\"breadcrumb\">".$results2[0]['cat_name']."</a>".$breadcrumb;
		$whichCat = $results2[0]['cat_father_id'];
	}
} else {

	$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = '0' ORDER BY disp_order ASC";
} 

if(isset($_GET['page'])){
	$page = $_GET['page'];
} else {
	$page = 0;
}

// query database
$results = $db->select($query);
$numrows = $db->numrows($query);

include("../includes/header.inc.php"); 
?>

<p class="pageTitle"><?php echo "Reorder Categories</p><p class=\"breadcrumb\"> ";
    
		if(isset($breadcrumb)) { echo $breadcrumb; }?></p>

<?php if(isset($msg)){ echo "<p><div class=\"infoText\">".$msg."</div>"; }?>
<?php
if(!isset($_GET['mode']) && !isset($_GET['edit'])){
?>
<p class="copyText"><?php echo $lang['admin']['categories']['cdo_desc']; ?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
    <td nowrap='nowrap' class="tdTitle"><?php echo $lang['admin']['categories']['cat_name']; ?></td>
    <td class="tdTitle"><?php echo $lang['admin']['categories']['dir']; ?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['categories']['cdo_col_title']; ?></td>
    <td class="tdTitle" align="center" width="10%"><?php echo $lang['admin']['categories']['action']; ?></td>
  </tr>
  <?php 
  if($results == TRUE){
  
  	for ($i=0; $i<count($results); $i++){ 
	
	$cellColor = "";
	$cellColor = cellColor($i);
	// Process any newly added or moved/edited categories and update their display order appropriately.
	if($results[$i]['disp_order'] == 9999) {
		$record["disp_order"] = $db->mySQLSafe($i+1);
		$where = "cat_id=".$db->mySQLSafe($results[$i]['cat_id']);
		$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
		$results[$i]['disp_order'] = $i+1;
  }

	// Remove any gaps from display order due to deleted or moved categories.
	if($results[$i]['disp_order'] != $i+1) {
		$record["disp_order"] = $db->mySQLSafe($i+1);
		$where = "cat_id=".$db->mySQLSafe($results[$i]['cat_id']);
		$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
		$results[$i]['disp_order'] = $i+1;
  }

	$query = "SELECT cat_father_id FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = '".$results[$i]['cat_id']."' ORDER BY disp_order ASC";
	$numsubs = $db->numrows($query);

	?>
  <tr>
    <td nowrap='nowrap' class="<?php echo $cellColor; ?>">
    <?php 
			if($numsubs > 0){
				?><a href="<?php echo "?catId=".$results[$i]['cat_id']; ?>" class="txtLink"><?php echo $results[$i]['cat_name']; ?></a> <?php echo "(".$numsubs.")";
			} else {
				echo "<span class=\"copyText\">".$results[$i]['cat_name']." (".$numsubs.")</span>";
			}?>
		</td>
    <td nowrap='nowrap'  class="<?php echo $cellColor; ?>"><span class="txtDir"><?php echo getCatDir($results[$i]['cat_name'],$results[$i]['cat_father_id'], $results[$i]['cat_id']);?></span></td>
    <td align="center" valign="middle" nowrap='nowrap' class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['disp_order']; ?></span></td>
    <td align="center" nowrap='nowrap' class="<?php echo $cellColor; ?>"><a 
	<?php if(permission("categories","edit")==TRUE){
	if($i==0){ echo "href='javascript:alert(\"".$lang['admin']['categories']['cdo_noup']."\")' class='txtNullLink'"; }
	else { echo "href=\"?moveup=".$results[$i]['cat_id']."&amp;displace=".$results[$i-1]['cat_id']."\" class=\"txtLink\""; }
	} else { echo $link401; } ?>
	><img src="/admin/images/evo/icon_move_up.png" alt="Up" title="Up" border="0" /></a> &nbsp; <a
	<?php if(permission("categories","edit")==TRUE){
	if($i+1==$numrows){ echo "href='javascript:alert(\"".$lang['admin']['categories']['cdo_nodown']."\")' class='txtNullLink'"; }
	else { echo "href=\"?movedown=".$results[$i]['cat_id']."&amp;displace=".$results[$i+1]['cat_id']."\" class=\"txtLink\""; }
	} else { echo $link401; } ?>
	><img src="/admin/images/evo/icon_move_down.png" alt="Down" title="Down" border="0" /></a>
	</td>
  </tr>
  <?php } // end loop
  } else { ?>
   <tr>
    <td colspan="6" class="tdText"><?php echo $lang['admin']['categories']['no_cats_exist'];?></td>
  </tr>
  <?php } ?>
</table>
<p>
<form name="updateSort" method="post" target="_self">
<table width="60%" border="0" cellspacing="0" cellpadding="4" class="mainTable" align="center">
<tr>
  <td class="tdTitle" colspan="2" align="center"><?php echo $lang['admin']['categories']['prodsort_boxtitle']; ?></td>
</tr>
<tr>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_nameasc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="name ASC"<?php if($config['prodSortOrder']=="name ASC") echo " checked"; ?>></span></td>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_namedesc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="name DESC"<?php if($config['prodSortOrder']=="name DESC") echo " checked"; ?>></span></td>
</tr>
<tr>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_priceasc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="price ASC"<?php if($config['prodSortOrder']=="price ASC") echo " checked"; ?>></span></td>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_pricedesc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="price DESC"<?php if($config['prodSortOrder']=="price DESC") echo " checked"; ?>></span></td>
</tr>
<tr>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_prodcodeasc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="productCode ASC"<?php if($config['prodSortOrder']=="productCode ASC") echo " checked"; ?>></span></td>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_prodcodedesc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="productCode DESC"<?php if($config['prodSortOrder']=="productCode DESC") echo " checked"; ?>></span></td>
</tr>
<tr>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_itemasc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="productId ASC"<?php if($config['prodSortOrder']=="productId ASC") echo " checked"; ?>></span></td>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_itemdesc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="productId DESC"<?php if($config['prodSortOrder']=="productId DESC") echo " checked"; ?>></span></td>
</tr>
<tr>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_stocklevelasc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="stock_level ASC"<?php if($config['prodSortOrder']=="stock_level ASC") echo " checked"; ?>></span></td>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_stockleveldesc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="stock_level DESC"<?php if($config['prodSortOrder']=="stock_level DESC") echo " checked"; ?>></span></td>
</tr>
<tr>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_popularasc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="popularity ASC"<?php if($config['prodSortOrder']=="popularity ASC") echo " checked"; ?>></span></td>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_populardesc']; ?>
  <input type="radio" name="config[prodSortOrder]" value="popularity DESC"<?php if($config['prodSortOrder']=="popularity DESC") echo " checked"; ?>></span></td>
</tr>
<tr>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_random']; ?>
  <input type="radio" name="config[prodSortOrder]" value="digital ASC"<?php if($config['prodSortOrder']=="digital ASC") echo " checked"; ?>></span></td>
  <td align="right"><span class="copyText"><?php echo $lang['admin']['categories']['prodsort_full_random']; ?>
  <input type="radio" name="config[prodSortOrder]" value="digital DESC"<?php if($config['prodSortOrder']=="digital DESC") echo " checked"; ?>></span></td>
</tr>
<tr>
  <td colspan="2" align="center"><input type="submit" name="submit" class="submit" value="Update Sort Order"></td>
</tr>
</table>
</form>
<p>
<?php
	include("../includes/footer.inc.php");
}

function checkDBexist() {
	global $db, $glob;
	$query = "SELECT disp_order FROM ".$glob['dbprefix']."CubeCart_category";
	if(mysql_query($query) != TRUE) {
		$db->misc("ALTER TABLE ".$glob['dbprefix']."CubeCart_category ADD disp_order INT(16) NOT NULL DEFAULT 9999");
	}
}
?>