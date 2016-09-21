<?PHP

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
// add
if(isset($_GET['add']) && !empty($_GET['add']))
{
	$record['img'] = $db->mySQLSafe(urldecode($_GET['add']));
	$record['productId'] = $db->mySQLSafe($_GET['productId']);  
	$insert = $db->insert($glob['dbprefix']."CubeCart_img_idx", $record);
	unset($record);
	
	if($insert == TRUE)
	{
		$msg = "<p class='infoText'>".$lang['admin']['products']['img_added_to_prod']."</p>";
	}
	else
	{
		$msg = "<p class='warnText'>".$lang['admin']['products']['img_not_added_to_prod']."</p>";
	}
	
	$count['noImages'] = "noImages + 1";
	$db->update($glob['dbprefix']."CubeCart_inventory", $count, "productId = ".$db->mySQLSafe($_GET['productId']));
	
}
elseif(isset($_GET['remove']) && !empty($_GET['remove']))
{
	
	$where = "img=".$db->mySQLSafe(urldecode($_GET['remove']))." AND productId=".$db->mySQLSafe($_GET["productId"]);
	$delete = $db->delete($glob['dbprefix']."CubeCart_img_idx", $where);
	
	if($delete == TRUE){
		$msg = "<p class='infoText'>".$lang['admin']['products']['img_removed']."</p>";
	}
	else
	{
		$msg = "<p class='warnText'>".$lang['admin']['products']['img_not_removed']."</p>";
	}
	
	$count['noImages'] = "noImages - 1";
	$db->update($glob['dbprefix']."CubeCart_inventory", $count, "productId = ".$db->mySQLSafe($_GET['productId']));
	
}
	

$imgArray = $db->select("SELECT img FROM ".$glob['dbprefix']."CubeCart_img_idx WHERE productId=".$db->mySQLsafe($_GET['productId']));
$currentPage = $_SERVER['PHP_SELF']."?productId=".$_GET['productId']."&amp;img=".$_GET['img']."&amp;cat=".$_GET['cat'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
<head>
<title><?php echo $lang['admin']['products']['image_management'];?></title>
<link rel="stylesheet" type="text/css" href="../styles/style.css">
</head>
<body>
<p class="pageTitle"><?php echo $lang['admin']['products']['manage_images'];?></p>
<?php if(isset($msg)){ echo stripslashes($msg); }?>
<table border="0" width="100%" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle"><?php echo $lang['admin']['products']['image'];?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['products']['action'];?></td>
  </tr>
  <?php
	if($imgArray==true){
		for($i=0; $i<count($imgArray); $i++){
	?>
   
  <tr>
    <td align="center" class="<?php echo $cellColor; ?>"><img src="<?php echo $GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$imgArray[$i]['img']; ?>" alt="" title="" /></td>
    <td align="center" class="<?php echo $cellColor; ?>"><a href="<?php echo $currentPage; ?>&amp;remove=<?php echo $imgArray[$i]['img']; ?>" class="txtLink"><?php echo $lang['admin']['remove'];?></a></td>
  </tr>
  <?PHP
  		}
	}
  ?>
</table>
<br /><br />
<form action="<?PHP echo $currentPage; ?>" method="post">
  <table border="0" width="100%" cellspacing="0" cellpadding="3" class="mainTable">
    <tr>
      <td class="tdTitle" colspan="2">Search</td>
    </tr>
    <tr>
      <td><input type="text" name="search" id="search" /></td>
      <td><input type="submit" name="submit" id="submit" value="Search" /></td>
    </tr>
  </table>
</form>
<?PHP
#########################################################

if ( isset ( $_POST['submit'] ) ) {
	?>
	<br /><br />
    
<table border="0" width="100%" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle"><?php echo $lang['admin']['products']['image'];?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['products']['action'];?></td>
  </tr>
    <?PHP
		$files = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images AS img INNER JOIN ".$glob['dbprefix']."CubeCart_imgcat_idx AS idx ON img.imgId=idx.imgId WHERE idx.catId=".$db->mySQLSafe($_GET['cat'])." AND imgName LIKE ".$db->mySQLSafe("%".$_POST['search']."%")." GROUP BY img.imgId ORDER BY imgName");
		if($files==true){
			for($i=0; $i<count($files); $i++){
	?>
  <tr>
  	<td align="center" class="<?php echo $cellColor; ?>"><img src="<?php echo $GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$files[$i]['filename']; ?>" alt="" title="" /></td>
    <td align="center" class="<?php echo $cellColor; ?>"><a href="<?php echo $currentPage; ?>&amp;add=<?php echo $files[$i]['filename']; ?>" class="txtLink"><?php echo $lang['admin']['add'];?></a></td>
  </tr>
  <?PHP
			}
	 	 }
  ?>
</table>
	<?php
}

###################
?>
<p align="center"><a href="javascript:window.close();" class="txtLink"><?php echo $lang['admin']['products']['close_window'];?></a></p>
</body>
</html>