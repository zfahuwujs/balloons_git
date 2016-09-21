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
if(permission("csv","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}
$msg='';
if(isset($_GET['moveup'])) {
	if($_GET['upType'] == 1){
	// Get info on the item we're moving.
		$query = "SELECT doc_name AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = ".$db->mySQLSafe($_GET['moveup']);
		$results = $db->select($query);
	}elseif($_GET['upType'] == 2){
		$query = "SELECT cat_name AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($_GET['moveup']);
		$results = $db->select($query);
	}else{
		$query = "SELECT catName AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_docCats WHERE catId = ".$db->mySQLSafe($_GET['moveup']);
		$results = $db->select($query);
	}
	$currentname = $results[0]['name'];
	$currentposition = $results[0]['menuOrder'];

	if($_GET['disType'] == 1){
	// Get info on the item it's displacing.
		$query = "SELECT doc_name AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = ".$db->mySQLSafe($_GET['displace']);
		$results = $db->select($query);
	}elseif($_GET['disType'] == 2){
		$query = "SELECT cat_name AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($_GET['displace']);
		$results = $db->select($query);
	}else{
		$query = "SELECT catName AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_docCats WHERE catId = ".$db->mySQLSafe($_GET['displace']);
		$results = $db->select($query);
	}
	$displacedname = $results[0]['name'];
	$displacedposition = $results[0]['menuOrder'];

	// Verify that they are indeed consecutive.
	//if($currentposition - $displacedposition == 1){
		// Update the two records and swap positions.
		$record["menuOrder"] = $db->mySQLSafe($displacedposition);
		
		if($_GET['upType'] == 1){
			$where = "doc_id=".$db->mySQLSafe($_GET['moveup']);
			$update = $db->update($glob['dbprefix']."CubeCart_docs", $record, $where);
		}elseif($_GET['upType'] == 2){
			$where = "cat_id=".$db->mySQLSafe($_GET['moveup']);
			$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
		}else{
			$where = "catId=".$db->mySQLSafe($_GET['moveup']);
			$update = $db->update($glob['dbprefix']."CubeCart_docCats", $record, $where);
		}
		
		$record["menuOrder"] = $db->mySQLSafe($currentposition);
		
		if($_GET['disType'] == 1){
			$where = "doc_id=".$db->mySQLSafe($_GET['displace']);
			$update = $db->update($glob['dbprefix']."CubeCart_docs", $record, $where);
		}elseif($_GET['disType'] == 2){
			$where = "cat_id=".$db->mySQLSafe($_GET['displace']);
			$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
		}else{
			$where = "catId=".$db->mySQLSafe($_GET['displace']);
			$update = $db->update($glob['dbprefix']."CubeCart_docCats", $record, $where);
		}
		
	  $msg = "<i>".$currentname."</i>".$lang['admin']['categories']['cdo_moved_up']."<i>".$displacedname."</i>.";
	//}
}

if(isset($_GET['movedown'])) {
	if($_GET['downType'] == 1){
	// Get info on the item we're moving.
		$query = "SELECT doc_name AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = ".$db->mySQLSafe($_GET['movedown']);
		$results = $db->select($query);
	}elseif($_GET['downType'] == 2){
		$query = "SELECT cat_name AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($_GET['movedown']);
		$results = $db->select($query);
	}else{
		$query = "SELECT catName AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_docCats WHERE catId = ".$db->mySQLSafe($_GET['movedown']);
		$results = $db->select($query);
	}
	$currentname = $results[0]['name'];
	$currentposition = $results[0]['menuOrder'];

	if($_GET['disType'] == 1){
	// Get info on the item it's displacing.
		$query = "SELECT doc_name AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id = ".$db->mySQLSafe($_GET['displace']);
		$results = $db->select($query);
	}elseif($_GET['disType'] == 2){
		$query = "SELECT cat_name AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id = ".$db->mySQLSafe($_GET['displace']);
		$results = $db->select($query);
	}else{
		$query = "SELECT catName AS name, menuOrder FROM ".$glob['dbprefix']."CubeCart_docCats WHERE catId = ".$db->mySQLSafe($_GET['displace']);
		$results = $db->select($query);
	}
	$displacedname = $results[0]['name'];
	$displacedposition = $results[0]['menuOrder'];

	// Verify that they are indeed consecutive.
	//if($displacedposition - $currentposition == 1){
		// Update the two records and swap positions.
		$record["menuOrder"] = $db->mySQLSafe($displacedposition);
		
		if($_GET['downType'] == 1){
			$where = "doc_id=".$db->mySQLSafe($_GET['movedown']);
			$update = $db->update($glob['dbprefix']."CubeCart_docs", $record, $where);
		}elseif($_GET['downType'] == 2){
			$where = "cat_id=".$db->mySQLSafe($_GET['movedown']);
			$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
		}else{
			$where = "catId=".$db->mySQLSafe($_GET['movedown']);
			$update = $db->update($glob['dbprefix']."CubeCart_docCats", $record, $where);
		}

		$record["menuOrder"] = $db->mySQLSafe($currentposition);
		
		if($_GET['disType'] == 1){
			$where = "doc_id=".$db->mySQLSafe($_GET['displace']);
			$update = $db->update($glob['dbprefix']."CubeCart_docs", $record, $where);
		}elseif($_GET['disType'] == 2){
			$where = "cat_id=".$db->mySQLSafe($_GET['displace']);
			$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
		}else{
			$where = "catId=".$db->mySQLSafe($_GET['displace']);
			$update = $db->update($glob['dbprefix']."CubeCart_docCats", $record, $where);
		}
		
	  $msg = "<i>".$currentname."</i>".$lang['admin']['categories']['cdo_moved_down']."<i>".$displacedname."</i>.";
	//}
}

$query = "
SELECT catId AS id, catName AS name, NULL AS doc, menuOrder as menuOrder, NULL AS cat FROM CubeCart_docCats WHERE menuShow = 1 
UNION ALL SELECT doc_id AS id, doc_name AS name, doc_id AS doc, menuOrder, NULL AS cat FROM CubeCart_docs WHERE menuShow = 1 
UNION ALL SELECT cat_id AS id, cat_name AS name, NULL AS doc, menuOrder, cat_id AS cat FROM CubeCart_category WHERE menuShow = 1 
ORDER BY menuOrder ASC";
$results = $db->select($query);
$numrows = $db->numrows($query);

include("../includes/header.inc.php"); 
?>

<p class="pageTitle">Top Navigation Order</p>
<p class="copyText"><?php //echo $msg; ?></p>
  <?php 
  if($results == TRUE){
  ?>
	  <table width="70%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
	  <tr>
		<td nowrap='nowrap' class="tdTitle">Name</td>
		<td class="tdTitle">Document/Category</td>
		<td class="tdTitle">Order Number</td>
		<td class="tdTitle" align="center" width="10%"><?php echo $lang['admin']['categories']['action']; ?></td>
	  </tr>
  <?php
  	for ($i=0; $i<count($results); $i++){ 
	
	$cellColor = "";
	$cellColor = cellColor($i);
	// Process any newly added or moved/edited categories and update their display order appropriately.
	if($results[$i]['menuOrder'] == 0) {
		$record["menuOrder"] = $db->mySQLSafe($i+1);
		
		if(isset($results[$i]['doc'])){
			$where = "doc_id=".$db->mySQLSafe($results[$i]['id']);
			$update = $db->update($glob['dbprefix']."CubeCart_docs", $record, $where);
		}else{
			$where = "cat_id=".$db->mySQLSafe($results[$i]['id']);
			$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
		}
		$results[$i]['menuOrder'] = $i+1;
  	}

	// Remove any gaps from display order due to deleted or moved categories.
	if($results[$i]['menuOrder'] != $i+1) {
		$record["menuOrder"] = $db->mySQLSafe($i+1);
		
		if(isset($results[$i]['doc'])){
			$where = "doc_id=".$db->mySQLSafe($results[$i]['id']);
			$update = $db->update($glob['dbprefix']."CubeCart_docs", $record, $where);
		}else{
			$where = "cat_id=".$db->mySQLSafe($results[$i]['id']);
			$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where);
		}
		$results[$i]['menuOrder'] = $i+1;
 	}
	?>
  <tr>
    <td nowrap='nowrap' class="copyText <?php echo $cellColor; ?>"><? echo $results[$i]['name']; ?></td>
    <td nowrap='nowrap'  class="copyText <?php echo $cellColor; ?>"><? if(isset($results[$i]['doc'])){echo "Document";}elseif(isset($results[$i]['cat'])){echo "Category";}else{echo "Document Category";} ?></td>
    <td nowrap='nowrap'  class="copyText <?php echo $cellColor; ?>"><? echo $results[$i]['menuOrder']; ?></td>
    <td align="center" nowrap='nowrap' class="copyText <?php echo $cellColor; ?>"><a 
	<?php 
	if(permission("csv","edit")==TRUE){
		if($i==0){ 
			echo "href='javascript:alert(\"".$lang['admin']['categories']['cdo_noup']."\")' class='txtNullLink'"; 
		}else{ 
			if(isset($results[$i]['doc'])){
				if(isset($results[$i+1]['doc'])){
					echo "href=\"?moveup=".$results[$i]['id']."&amp;displace=".$results[$i-1]['id']."&amp;upType=1&amp;disType=1\" class=\"txtLink\""; 
				}elseif(isset($results[$i+1]['cat'])){
					echo "href=\"?moveup=".$results[$i]['id']."&amp;displace=".$results[$i-1]['id']."&amp;upType=1&amp;disType=2\" class=\"txtLink\""; 
				}else{
					echo "href=\"?moveup=".$results[$i]['id']."&amp;displace=".$results[$i-1]['id']."&amp;upType=1&amp;disType=3\" class=\"txtLink\""; 
				}
			}elseif(isset($results[$i]['cat'])){
				if(isset($results[$i+1]['doc'])){
					echo "href=\"?moveup=".$results[$i]['id']."&amp;displace=".$results[$i-1]['id']."&amp;upType=2&amp;disType=1\" class=\"txtLink\""; 
				}elseif(isset($results[$i+1]['cat'])){
					echo "href=\"?moveup=".$results[$i]['id']."&amp;displace=".$results[$i-1]['id']."&amp;upType=2&amp;disType=2\" class=\"txtLink\""; 
				}else{
					echo "href=\"?moveup=".$results[$i]['id']."&amp;displace=".$results[$i-1]['id']."&amp;upType=2&amp;disType=3\" class=\"txtLink\""; 
				}
			}else{
				if(isset($results[$i+1]['doc'])){
					echo "href=\"?moveup=".$results[$i]['id']."&amp;displace=".$results[$i-1]['id']."&amp;upType=3&amp;disType=1\" class=\"txtLink\""; 
				}elseif(isset($results[$i+1]['cat'])){
					echo "href=\"?moveup=".$results[$i]['id']."&amp;displace=".$results[$i-1]['id']."&amp;upType=3&amp;disType=2\" class=\"txtLink\""; 
				}else{
					echo "href=\"?moveup=".$results[$i]['id']."&amp;displace=".$results[$i-1]['id']."&amp;upType=3&amp;disType=3\" class=\"txtLink\""; 
				}
			}
		}
	}else{ 
		echo $link401; 
	} 
	?>><img src="/admin/images/evo/sitemap_up.png" alt="Up" title="Up" border="0" /></a> 
	&nbsp; 
	<a
	<?php 
	if(permission("csv","edit")==TRUE){
		if($i+1==$numrows){ 
			echo "href='javascript:alert(\"".$lang['admin']['categories']['cdo_nodown']."\")' class='txtNullLink'"; 
		}else{
			if(isset($results[$i]['doc'])){
				if(isset($results[$i+1]['doc'])){
					echo "href=\"?movedown=".$results[$i]['id']."&amp;displace=".$results[$i+1]['id']."&amp;downType=1&amp;disType=1\" class=\"txtLink\""; 
				}elseif(isset($results[$i+1]['cat'])){
					echo "href=\"?movedown=".$results[$i]['id']."&amp;displace=".$results[$i+1]['id']."&amp;downType=1&amp;disType=2\" class=\"txtLink\""; 
				}else{
					echo "href=\"?movedown=".$results[$i]['id']."&amp;displace=".$results[$i+1]['id']."&amp;downType=1&amp;disType=3\" class=\"txtLink\""; 
				}
			}elseif(isset($results[$i]['cat'])){
				if(isset($results[$i+1]['doc'])){
					echo "href=\"?movedown=".$results[$i]['id']."&amp;displace=".$results[$i+1]['id']."&amp;downType=2&amp;disType=1\" class=\"txtLink\""; 
				}elseif(isset($results[$i+1]['cat'])){
					echo "href=\"?movedown=".$results[$i]['id']."&amp;displace=".$results[$i+1]['id']."&amp;downType=2&amp;disType=2\" class=\"txtLink\""; 
				}else{
					echo "href=\"?movedown=".$results[$i]['id']."&amp;displace=".$results[$i+1]['id']."&amp;downType=2&amp;disType=3\" class=\"txtLink\""; 
				}
			}else{
				if(isset($results[$i+1]['doc'])){
					echo "href=\"?movedown=".$results[$i]['id']."&amp;displace=".$results[$i+1]['id']."&amp;downType=3&amp;disType=1\" class=\"txtLink\""; 
				}elseif(isset($results[$i+1]['cat'])){
					echo "href=\"?movedown=".$results[$i]['id']."&amp;displace=".$results[$i+1]['id']."&amp;downType=3&amp;disType=2\" class=\"txtLink\""; 
				}else{
					echo "href=\"?movedown=".$results[$i]['id']."&amp;displace=".$results[$i+1]['id']."&amp;downType=3&amp;disType=3\" class=\"txtLink\""; 
				}
			}
		}
	}else{ 
		echo $link401; 
	} 
	?>><img src="/admin/images/evo/sitemap_down.png" alt="Down" title="Down" border="0" /></a>
	<?php
	}
	?>
	</td>
  </tr>
  </table>
  <?php 
}else
	echo "<p class='infoText'>No items found.</p>";
  

include("../includes/footer.inc.php");

?>