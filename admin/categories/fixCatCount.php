<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.2
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   (c) 2005 Devellion Limited
|   Devellion Limited,
|   Westfield Lodge,
|   Westland Green,
|   Little Hadham,
|   Nr Ware, HERTS.
|   SG11 2AL
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Friday, 12 August 2005
|   Email: info (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	categoryOrder.php
|   ========================================
|	Specify Category Display Order
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

$maxdepth = 4;

if(isset($_GET['page'])){
	$page = $_GET['page'];
} else {
	$page = 0;
}

// query database
$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_category WHERE 1";
$results = $db->select($query, $catsPerPage, $page);
$numrows = $db->numrows($query);

include("../includes/header.inc.php"); 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Category Product Count</p></td>
  </tr>
</table>
<p class="copyText">Now correcting category product count...</p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
    <td nowrap='nowrap' class="tdTitle"><?php echo $lang['admin']['categories']['cat_name']; ?></td>
    <td class="tdTitle"><?php echo $lang['admin']['categories']['dir']; ?></td>
    <td class="tdTitle" align="center">Former Count</td>
    <td class="tdTitle" align="center">Direct Count</td>
    <td class="tdTitle" align="center">Total Count</td>
  </tr>
  <?php 
  if($results == TRUE){
  
	for ($i=0; $i<count($results); $i++){ 
		$thiscat = $results[$i]['cat_id'];
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_cats_idx WHERE cat_id = '$thiscat'";
		$qtytoadd = $db->numrows($query);
		$quantity[$thiscat] += $qtytoadd;
		$fathercat = $results[$i]['cat_father_id'];
		
		while($fathercat != 0){
			$query2 = "SELECT cat_father_id FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = '$thiscat'";
			$results2 = $db->select($query2);
			$fathercat = $results2[0]['cat_father_id'];
			if($fathercat != 0){
				$quantity[$fathercat] += $qtytoadd;
				}
			$thiscat = $fathercat;
			}
		}

 	for ($i=0; $i<count($results); $i++){ 

	$thiscat = $results[$i]['cat_id'];
	$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_cats_idx WHERE cat_id = '$thiscat'";
	$directcount = $db->numrows($query);
	$cellColor = "";
	$cellColor = cellColor($i);

	$record["noProducts"] = $db->mySQLSafe($quantity[$thiscat]);
	$where = "cat_id=".$db->mySQLSafe($thiscat);
	$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);

	?>
  <tr>
    <td nowrap="nowrap" class="<?php echo $cellColor."\"><span class=\"copyText\">".$results[$i]['cat_name']."</span>"; ?></td>
    <td nowrap="nowrap" class="<?php echo $cellColor; ?>"><span class="txtDir"><?php echo getCatDir($results[$i]['cat_name'],$results[$i]['cat_father_id'], $results[$i]['cat_id']);?></span></td>
    <td align="center" valign="middle" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['noProducts']; ?></span></td>
    <td align="center" valign="middle" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $directcount; ?></span></td>
    <td align="center" valign="middle" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $quantity[$thiscat]; ?></span></td>
  </tr>
  <?php } // end loop
  } else { ?>
   <tr>
    <td colspan="6" class="tdText"><?php echo $lang['admin']['categories']['no_cats_exist'];?></td>
  </tr>
  <?php } ?>
</table>
<p>

<?php include("../includes/footer.inc.php"); ?>
