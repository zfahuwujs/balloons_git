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

|	extraCats.php

|   ========================================

|	Add/Edit/Delete Products in Multiple Categories	

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

include("../includes/auth.inc.php");

function ThisPageURL() {
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

	// add

	if(isset($_GET['add']) && $_GET['add']>0) {

	

		$record['cat_id'] = $db->mySQLSafe($_GET['add']);

		$record['productId'] = $db->mySQLSafe($_GET['productId']);  

		

		$insert = $db->insert($glob['dbprefix']."CubeCart_cats_idx", $record);

		unset($record);



		if($insert == TRUE){

			$msg = "<p class='infoText'>".$lang['admin']['products']['prod_added_to_cat']."</p>";

			

			// set category +1

			$db->categoryNos($_GET['add'], "+");

			

		} else {

			$msg = "<p class='warnText'>".$lang['admin']['products']['prod_not_added_to_cat']."</p>";

		}

	

	} elseif(isset($_GET['remove']) && $_GET['remove']>0){

	

	$where = "cat_id=".$db->mySQLSafe($_GET['remove'])." AND productId=".$db->mySQLSafe($_GET["productId"]);

	$delete = $db->delete($glob['dbprefix']."CubeCart_cats_idx", $where);

		

		if($delete == TRUE){

			$msg = "<p class='infoText'>".$lang['admin']['products']['prod_removed_from_cat']."</p>";

			

			// set category - 1

			$db->categoryNos($_GET['remove'], "-");

		

		} else {

			$msg = "<p class='warnText'>".$lang['admin']['products']['prod_not_removed_from_cat']."</p>";

		}

	

	}

	
/* start mod: Prevent duplicate products in categories - by Estelle */
	if ($insert || $delete) {
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit;
	}
	/* delete any duplicates - only have to run this once but doesn't hurt to leave it here */
	$db->misc("DELETE T1 FROM ".$glob['dbprefix']."CubeCart_cats_idx AS T1, ".$glob['dbprefix']."CubeCart_cats_idx AS T2 WHERE T1.cat_id = T2.cat_id AND T1.productId = T2.productId AND T1.id > T2.id");
	/* end mod: Prevent duplicate products in categories - by Estelle */

	

	// get array of existing categories product relation ships

	$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_cats_idx WHERE productId= %s", $db->mySQLSafe($_GET['productId']));

	$assocArray = $db->select($query);

	

	for ($i=0; $i<count($assocArray); $i++){

		$catKey = $assocArray[$i]['cat_id'];

		$catIndex[$catKey] = $assocArray[$i]['cat_id'];

	} 



	// make sql query

	$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id <> %s", $db->mySQLSafe($_GET['cat_id'])); 

	// query database

	$results = $db->select($query, 15, $_GET['page']);

	$pagination = $db->paginate($db->numrows($query), 15, $_GET['page'], "page");

	

	// rip out add and remove vars

	$currentPage = str_replace(array("&amp;add=".$_GET['add'],"&amp;remove=".$_GET['remove']),"",currentPage());

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >

<html>

	<head>

		<title><?php echo $lang['admin']['products']['title_extraCats'];?></title>

		<link rel="stylesheet" type="text/css" href="../styles/style.css">

	</head>

	<body>

	<p class="pageTitle"><?php echo $lang['admin']['products']['manage_cats'];?> - <?php echo $_GET['name']; ?></p>

	<p class="copyText"><strong><?php echo $lang['admin']['products']['master_cat'];?></strong> <span class="txtDir"><?php echo getCatDir(treatGet(urldecode($_GET['cat_name'])),treatGet($_GET['cat_father_id']), treatGet($_GET['cat_id']));?></span></p>

	<?php if(isset($msg)){ echo stripslashes($msg); }?>

	<p align="right" class="copyText"><?php echo $pagination; ?></p>

	<table border="0" width="100%" cellspacing="0" cellpadding="3" class="mainTable">

      <tr>

        <td class="tdTitle">Category</td>

        <td align="center" class="tdTitle">Action</td>

      </tr>

        <?php 

  	if($results == TRUE){

  

  		for ($i=0; $i<count($results); $i++){ 

	

		$cellColor = "";

		$cellColor = cellColor($i);

	?>

	  <tr>

        <td class="<?php echo $cellColor; ?>"><span class="txtDir"><?php echo getCatDir($results[$i]['cat_name'],$results[$i]['cat_father_id'], $results[$i]['cat_id']);?></span></td>

        <td align="center" class="<?php echo $cellColor; ?>">

		

		<?php 

		$currentCat = $results[$i]['cat_id'];

		if(isset($catIndex[$currentCat])){ ?>

		<a href="<?php echo ThisPageURL(); ?>&amp;remove=<?php echo $results[$i]['cat_id']; ?>" class="txtLink">Remove</a>

		<?php } else { ?>

		<a href="<?php echo ThisPageURL(); ?>&amp;add=<?php echo $results[$i]['cat_id']; ?>" class="txtLink">Add</a>

		<?php } ?>

		</td>

      </tr>

	  <?php }

	 }

	 ?>

    </table>

	<p align="right" class="copyText"><?php echo $pagination; ?></p>

	<p align="center"><a href="javascript:window.close();" class="txtLink"><?php echo $lang['admin']['products']['close_window'];?></a></p>

	</body>

</html>

