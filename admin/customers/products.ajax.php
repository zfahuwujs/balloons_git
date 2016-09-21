<?php

include_once('../../includes/ini.inc.php');
include_once('../../includes/global.inc.php');
include_once('../../classes/db.inc.php');
$db = new db();
include_once('../../includes/functions.inc.php');

if(isset($_POST['catId'])){
	$prods = $db->select("
	SELECT * 
	FROM ".$glob['dbprefix']."CubeCart_inventory
	WHERE cat_id = ".$db->mySQLSafe($_POST['catId'])."
	ORDER BY name");
	
	if($prods == TRUE){
		for ($i=0; $i<count($prods); $i++){
			echo '<option value="'.$prods[$i]['productId'].'">'.$prods[$i]['name'].' (#'.$prods[$i]['productId'].')</option>';
		}
	}
}