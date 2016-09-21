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


if(isset($_GET['moveup'])) {
	$query = "SELECT imgName, menuOrder FROM ".$glob['dbprefix']."CubeCart_images WHERE imgId = ".$db->mySQLSafe($_GET['moveup']);
	$results = $db->select($query);
	$currentname = $results[0]['imgName'];
	$currentposition = $results[0]['menuOrder'];

	$query = "SELECT imgName, menuOrder FROM ".$glob['dbprefix']."CubeCart_images WHERE imgId = ".$db->mySQLSafe($_GET['displace']);
	$results = $db->select($query);
	$displacedname = $results[0]['imgName'];
	$displacedposition = $results[0]['menuOrder'];

	// Verify that they are indeed consecutive.
	if($currentposition - $displacedposition == 1){
		// Update the two records and swap positions.
		$record["menuOrder"] = $db->mySQLSafe($displacedposition);
		$where = "imgId=".$db->mySQLSafe($_GET['moveup']);
		$update = $db->update($glob['dbprefix']."CubeCart_images", $record, $where);
		
		$record["menuOrder"] = $db->mySQLSafe($currentposition);
		$where = "imgId=".$db->mySQLSafe($_GET['displace']);
		$update = $db->update($glob['dbprefix']."CubeCart_images", $record, $where);
		
	  $msg = "<i>".$currentname."</i>".$lang['admin']['categories']['cdo_moved_up']."<i>".$displacedname."</i>.";
	}
}

if(isset($_GET['movedown'])) {
	$query = "SELECT imgName, menuOrder FROM ".$glob['dbprefix']."CubeCart_images WHERE imgId = ".$db->mySQLSafe($_GET['movedown']);
	$results = $db->select($query);
	$currentname = $results[0]['imgName'];
	$currentposition = $results[0]['menuOrder'];

	$query = "SELECT imgName, menuOrder FROM ".$glob['dbprefix']."CubeCart_images WHERE imgId = ".$db->mySQLSafe($_GET['displace']);
	$results = $db->select($query);
	$displacedname = $results[0]['imgName'];
	$displacedposition = $results[0]['menuOrder'];

	// Verify that they are indeed consecutive.
	if($displacedposition - $currentposition == 1){
		// Update the two records and swap positions.
		$record["menuOrder"] = $db->mySQLSafe($displacedposition);
		$where = "imgId=".$db->mySQLSafe($_GET['movedown']);
		$update = $db->update($glob['dbprefix']."CubeCart_images", $record, $where);

		$record["menuOrder"] = $db->mySQLSafe($currentposition);
		$where = "imgId=".$db->mySQLSafe($_GET['displace']);
		$update = $db->update($glob['dbprefix']."CubeCart_images", $record, $where);
		
	  $msg = "<i>".$currentname."</i>".$lang['admin']['categories']['cdo_moved_down']."<i>".$displacedname."</i>.";
	}
}

//assign menuOrder
$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_images AS img INNER JOIN ".$glob['dbprefix']."CubeCart_imgcat_idx AS idx ON img.imgId=idx.imgId WHERE idx.catId=9 ORDER BY img.menuOrder ASC";
$images = $db->select($query);
if($images==true){
	for($i=0; $i<count($images); $i++){
		$record["menuOrder"] = $db->mySQLSafe($i+1);
		$where = "imgId=".$db->mySQLSafe($images[$i]['imgId']);
		$update = $db->update($glob['dbprefix']."CubeCart_images", $record, $where);
	}
}

//get images
$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_images AS img INNER JOIN ".$glob['dbprefix']."CubeCart_imgcat_idx AS idx ON img.imgId=idx.imgId WHERE idx.catId=9 ORDER BY img.menuOrder ASC";
$images = $db->select($query);
$numrows = $db->numrows($query);
?>
<p class="pageTitle">Gallery Order</p>
<table width="100%"  border="0" cellspacing="0" cellpadding="3" class="mainTable">
    <tr>
        <td class="tdTitle" align="center">Image</td>
        <td class="tdTitle">Image Name</td>
        <td class="tdTitle" align="center">Order</td>
    </tr>
<?
if($images==true){
	for($i=0; $i<count($images); $i++){
		$cellColor = "";
		$cellColor = cellColor($i);
	?>
    <tr>
       	<td class="copyText <?php echo $cellColor; ?>" align="center"><img src="<?php echo $images[$i]['thumbLoc'] ?>" alt="<?php echo $images[$i]['filename'] ?>" height="50" /></td>
        <td class="copyText <?php echo $cellColor; ?>"><?php echo $images[$i]['imgName'] ?></td>
        <td class="copyText <?php echo $cellColor; ?>" align="center">
        <a
        <?php 
		if($i==0){ 
			echo "href='javascript:alert(\"".$lang['admin']['categories']['cdo_noup']."\")' class='txtNullLink'"; 
		}else{ 
			echo 'href="?moveup='.$images[$i]['imgId'].'&amp;displace='.$images[$i-1]['imgId'].'" class=\"txtLink\"'; 
		}
		?>><img src="/admin/images/evo/sitemap_up.png" alt="Up" title="Up" border="0" /></a> 
		&nbsp; 
		<a
		<?php 
	
			if($i+1==$numrows){ 
				echo "href='javascript:alert(\"".$lang['admin']['categories']['cdo_nodown']."\")' class='txtNullLink'"; 
			}else{
				echo 'href="?movedown='.$images[$i]['imgId'].'&amp;displace='.$images[$i+1]['imgId'].'" class=\"txtLink\"'; 
			}
	
		?>><img src="/admin/images/evo/sitemap_down.png" alt="Down" title="Down" border="0" /></a>
        
        </td>
    </tr>
    
    <?php
	}
}
?>
</table>



<?php include("../includes/footer.inc.php"); ?>
