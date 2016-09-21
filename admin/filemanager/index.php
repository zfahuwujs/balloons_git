<?php

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
if(permission("filemanager","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}
include("../includes/header.inc.php");

//unassign images from cat
if(isset($_GET['fmact']) && $_GET['fmact']=='unassign'){
	$where = "imgId = ".$db->mySQLSafe($_GET['imgId'])." AND catId = ".$db->mySQLSafe($_GET['catId']);
	$delete = $db->delete($glob['dbprefix']."CubeCart_imgcat_idx", $where, ""); 
}
//delete cat
if(isset($_GET['fmact']) && $_GET['fmact']=='delCat'){
	//ulink images before delete
	$where = "catId = ".$db->mySQLSafe($_GET['catId']);
	$delete = $db->delete($glob['dbprefix']."CubeCart_imgcat_idx", $where, ""); 
	
	$where = "catId = ".$db->mySQLSafe($_GET['catId']);
	$delete = $db->delete($glob['dbprefix']."CubeCart_images_cat", $where, ""); 
}
//add new cat
if(isset($_POST['newCat']) && $_POST['newCat']!=''){
	$record["catName"] = $db->mySQLSafe($_POST['newCat']);
	$record["catFather"] = $db->mySQLSafe($_POST['catFather']);
	$insert = $db->insert($glob['dbprefix']."CubeCart_images_cat", $record);
	if($insert == TRUE){
		$msg = "<p class='infoText'>'".$_POST['newCat']."' ".$lang['admin']['docs']['add_success']."</p>";
	} else {
		$msg = "<p class='infoText'>".$lang['admin']['docs']['add_fail']."</p>";
	}
}
//update cat
if(isset($_POST['catId0'])){
	$i=0;
	while(isset($_POST['catId'.$i])){
		$record["catName"] = $db->mySQLSafe($_POST['catName'.$i]);
		$record["catFather"] = $_POST['catFather'.$i];
		$record["topNav"] = $db->mySQLSafe($_POST['topNav'.$i]);
		$record["siteDoc"] = $db->mySQLSafe($_POST['siteDoc'.$i]);
		$where = "catId = ".$db->mySQLSafe($_POST['catId'.$i]);
		$update =$db->update($glob['dbprefix']."CubeCart_images_cat", $record, $where);
		$i++;
	}
}
//update image name
if(isset($_POST['imgName0'])){
	$i=0;
	while(isset($_POST['imgName'.$i]) && $_POST['imgName'.$i]!='' && $_POST['imgName'.$i]!='Name'){
		
		$record["imgName"] = $db->mySQLSafe($_POST['imgName'.$i]);
		$where = "imgId = ".$db->mySQLSafe($_POST['imgId'.$i]);
		$update =$db->update($glob['dbprefix']."CubeCart_images", $record, $where);
		$i++;
	}
}
//assign image to cat
if(isset($_POST['assign'])){
	$imgcat = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_imgcat_idx");
	if($imgcat==true){
		$flag=0;
		for($i=0; $i<count($imgcat); $i++){
			if($imgcat[$i]['imgId']==$_POST['imgId'] && $imgcat[$i]['catId']==$_POST['catId']){
				$flag=1;
			}
		}
	}
	if($flag==0){
		$record["imgId"] = $db->mySQLSafe($_POST['imgId']);
		$record["catId"] = $db->mySQLSafe($_POST['catId']);
		$insert = $db->insert($glob['dbprefix']."CubeCart_imgcat_idx", $record);
	}
}




//delete image
if(isset($_GET['unlink']) && !empty($_GET['unlink'])){
	// check for dependancies	
	$file = treatGet(urldecode($_GET['unlink'])); 
	$imageName = str_replace(array($GLOBALS['rootRel']."images/uploads/thumbs/thumb_",$GLOBALS['rootRel']."images/uploads/"),"",$file);
	$query = "SELECT ".$glob['dbprefix']."CubeCart_inventory.image, ".$glob['dbprefix']."CubeCart_category.cat_image FROM ".$glob['dbprefix']."CubeCart_inventory, ".$glob['dbprefix']."CubeCart_category WHERE image = '".$imageName."' OR cat_image = '".$imageName."'";
	$results = $db->select($query);
	$query = "SELECT doc_id FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_content LIKE '%".$imageName."%'";
	$siteDocs = $db->select($query);
	/*$filename = $GLOBALS['rootDir']."/includes/static/home.htm";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	$homepage = ereg($imageName,$contents);
	*/
	$path = $GLOBALS['rootDir']."/language";
	$homepage = "";
	if ($dir = opendir($path)) {
		while (false !== ($folder = readdir($dir))) {
			if(!eregi($folder,array(".",".."))){
			include($path."/".$folder."/home.inc.php");
				if(ereg($imageName,$home['copy'])){
					$homepage = TRUE;
				}
			}
		} 
	}
	$query = "SELECT img FROM ".$glob['dbprefix']."CubeCart_img_idx WHERE img = '".$imageName."'";
	$extraImg = $db->select($query);
	if($results == TRUE && !isset($_GET['confirmed'])){
		$msg = "<p class='warnText'>".sprintf($lang['admin']['filemanager']['prod_cat_use_img'],$file)." <a href=\"javascript:decision('".$lang['admin']['delete_q']."','index.php?unlink=".$file."&confirmed=1');\" class='txtRed'>".$lang['admin']['filemanager']['continue_q']."</a></p>";
	} elseif($siteDocs == TRUE && !isset($_GET['confirmed'])) {
		$msg = "<p class='warnText'>".sprintf($lang['admin']['filemanager']['site_doc_use_img'],$file)." <a href=\"javascript:decision('Are you sure you want to delete this?','index.php?unlink=".$file."&amp;confirmed=1');\" class='txtRed'>".$lang['admin']['filemanager']['continue_q']."</a></p>";
	} elseif($homepage == TRUE && !isset($_GET['confirmed'])) {
		$msg = "<p class='warnText'>".sprintf($lang['admin']['filemanager']['home_use_img'],$file)." <a href=\"javascript:decision('".$lang['admin']['delete_q']."','index.php?unlink=".$file."&amp;confirmed=1');\" class='txtRed'>".$lang['admin']['filemanager']['continue_q']."</a></p>";
	} elseif($extraImg == TRUE && !isset($_GET['confirmed'])) {
		$msg = "<p class='warnText'>".sprintf($lang['admin']['filemanager']['gallery_use_img'],$file)." <a href=\"javascript:decision('".$lang['admin']['delete_q']."','index.php?unlink=".$file."&amp;confirmed=1&amp;idx=1');\" class='txtRed'>".$lang['admin']['filemanager']['continue_q']."</a></p>";
	} else {
		if(file_exists($GLOBALS['rootDir']."/images/uploads/".$imageName) && unlink($GLOBALS['rootDir']."/images/uploads/".$imageName)){
			// delete thumbnail if it exists
			if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$imageName)){
				unlink($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$imageName);
			}
			
			//ulink image from cats before delete
			$image = $db->select("SELECT imgId FROM ".$glob['dbprefix']."CubeCart_images WHERE imgLoc = ".$db->mySQLSafe($_GET['unlink']));
			$where = "imgId = ".$image[0]['imgId'];
			$delete = $db->delete($glob['dbprefix']."CubeCart_imgcat_idx", $where, ""); 
			
			//delete image from db
			$where = "imgLoc = ".$db->mySQLSafe($_GET['unlink']);
			$delete = $db->delete($glob['dbprefix']."CubeCart_images", $where, ""); 
			
			
			if(isset($_GET['idx'])){
				$delete = "img = '".$imageName."'";
				$delete = $db->delete($glob['dbprefix']."CubeCart_img_idx",$delete);
			}
			$msg = "<p class='infoText'>".$lang['admin']['filemanager']['image_deleted']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['filemanager']['delete_failed']."</p>";
		}
	}
}
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle"><?php echo $lang['admin']['filemanager']['image_manager']; ?></p></td>
    <td align="right" valign="middle">
    <a href="<?php echo $GLOBALS['rootRel']; ?>admin/filemanager/upload.php" class="addNew" >Upload Image</a></td>
  </tr>
</table>
<?php 
if(isset($msg)){ 
	echo stripslashes($msg); 
} else { ?>
<p class="copyText"><?php echo $lang['admin']['filemanager']['delete_from_server']; ?></p>
<?php } ?>

<?php
	//filter image by category
	if(isset($_GET['allCat']) && $_GET['allCat']=='all'){
		$imgFilter = '';
		$assFilter = '';
	}elseif(isset($_GET['allCat'])){
		$imgFilter = "INNER JOIN ".$glob['dbprefix']."CubeCart_imgcat_idx ON ".$glob['dbprefix']."CubeCart_imgcat_idx.imgId=".$glob['dbprefix']."CubeCart_images.imgId WHERE ".$glob['dbprefix']."CubeCart_imgcat_idx.catId =".$_GET['allCat'];
		$assFilter = "WHERE ".$glob['dbprefix']."CubeCart_imgcat_idx.catId=".$_GET['allCat'];
	} else {
		$imgFilter = '';
		$assFilter = '';
	}
	
	//query and paginate assigned images
	if(isset($_GET['assignedPage'])){
		$assPage = $_GET['assignedPage'];
	} else {
		$assPage = 0;
	}
	$assignedPerPage = 50;
	$assignedQuery="SELECT * FROM ".$glob['dbprefix']."CubeCart_images INNER JOIN ".$glob['dbprefix']."CubeCart_imgcat_idx ON ".$glob['dbprefix']."CubeCart_images.imgId = ".$glob['dbprefix']."CubeCart_imgcat_idx.imgId INNER JOIN ".$glob['dbprefix']."CubeCart_images_cat ON ".$glob['dbprefix']."CubeCart_images_cat.catId = ".$glob['dbprefix']."CubeCart_imgcat_idx.catId ".$assFilter." ORDER BY ".$glob['dbprefix']."CubeCart_images_cat.catName";
	$assigned = $db->select($assignedQuery, $assignedPerPage, $assPage);
	$numrowsAssigned = $db->numrows($assignedQuery);
	$paginationAssigned = $db->paginate($numrowsAssigned, $assignedPerPage, $assPage, "assignedPage");
	
	//query and paginate image view
	if(isset($_GET['imgPage'])){
		$imgPage = $_GET['imgPage'];
	} else {
		$imgPage = 0;
	}
	$imagesPerPage = 50;
	$imagesQuery = "SELECT * FROM ".$glob['dbprefix']."CubeCart_images ".$imgFilter." ORDER BY imgCatId, imgName";
	$images = $db->select($imagesQuery, $imagesPerPage, $imgPage);
	$imagesAssign = $db->select($imagesQuery);
	$numrowsImg = $db->numrows($imagesQuery);
	$paginationImages = $db->paginate($numrowsImg, $imagesPerPage, $imgPage, "imgPage");
	
	//query image categories
	$categories = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images_cat ORDER BY CatId");
	
	//set variables for current page
	$pageVars='';
	$pageVarsAlt='';
	if(isset($_GET['allCat'])){$pageVars='?allCat='.$_GET['allCat'];}
	if(isset($_GET['imgPage']) && $pageVars==''){$pageVars='?imgPage='.$_GET['imgPage'];}elseif(isset($_GET['imgPage'])){$pageVars.='&amp;imgPage='.$_GET['imgPage'];}
	if(isset($_GET['assignedPage']) && $pageVars==''){$pageVars.='?assignedPage='.$_GET['assignedPage'];}elseif(isset($_GET['assignedPage'])){$pageVars.='&amp;assignedPage='.$_GET['assignedPage'];}
	if($pageVars!=''){$pageVarsAlt=preg_replace('/\?/i', '&amp;', $pageVars);}
?>


<table border="0" cellspacing="0" cellpadding="3">
	<?php /*?><form action="/admin/filemanager/index.php" method="get" name="search" id="search">
	<tr>
		<td class="tdText"><?php echo $lang['admin']['filemanager']['search']; ?></td>
		<td class="tdText"><input type="text" name="searchStr" id="searchStr" class="textbox" /></td>
		<td class="tdText"><input type="submit" name="submit" id="submit" class="submit" value="Search" /></td>
		<td class="tdText"><a href="/admin/filemanager/index.php" class="submit"><?php echo $lang['admin']['filemanager']['show_all']; ?></a></td>
	</tr>
    </form><?php */?>
    
    <form action="/admin/filemanager/index.php" method="get" name="category">
	<tr>
		<td class="tdText">Filter Images by Category</td>
        <td class="tdText">
        	<select name="allCat" class="textbox">
            	<option value="all">All Images</option>
            <?php
				if($categories==true){
					for($i=0; $i<count($categories); $i++){
            			echo '<option value="'.$categories[$i]['catId'].'" '; if(isset($_GET['allCat']) && $_GET['allCat']==$categories[$i]['catId']){echo 'selected="selected"';} echo'>'.$categories[$i]['catName'].'</option>';
					}
				}
			?>
        	</select>
        </td>
        <td class="tdText"><input type="submit" name="" id="submit" value="Filter Images"  class="submit"/></td>
	</tr>
	</form>
	
    <?php /*?><form action="/admin/filemanager/index.php<?php echo $pageVars; ?>" method="post" name="category">
	<tr>
		<td class="tdText">Add New Category</td>
		<td class="tdText"><input type="text" name="newCat" id="newCat" class="textbox" /></td>
        <!--<td class="tdText">
        	<select name="catFather" class="textbox">
            	<option value="0">Top Level</option>
            <?php
				if($categories==true){
					for($i=0; $i<count($categories); $i++){
            			echo '<option value="'.$categories[$i]['catId'].'">'.$categories[$i]['catName'].'</option>';
					}
				}
			?>
        	</select>
        </td>-->
        <td class="tdText"><input type="submit" name="addCat" id="submit" value="Add Category"  class="submit"/></td>
	</tr>
	</form><?php */?>

	<form action="/admin/filemanager/index.php<?php echo $pageVars; ?>" method="post" name="assignCat">
	<tr>
		<td class="tdText">Assign Image to Category</td>
		<td class="tdText">
        	<select name="imgId" class="textbox">
            <?php
				if($imagesAssign==true){
					for($i=0; $i<count($imagesAssign); $i++){
            			echo '<option value="'.$imagesAssign[$i]['imgId'].'">'.$imagesAssign[$i]['imgName'].'</option>';
					}
				}
			?>
        	</select>
        </td>
        <td class="tdText">
        	<select name="catId" class="textbox">
            <?php
				if($categories==true){
					for($i=0; $i<count($categories); $i++){
            			echo '<option value="'.$categories[$i]['catId'].'">'.$categories[$i]['catName'].'</option>';
					}
				}
			?>
        	</select>
        </td>
        <td class="tdText"><input type="submit" name="assign" id="submit" value="Assign" class="submit" /></td>
	</tr>
    </form>
</table>
<br />
<form action="/admin/filemanager/index.php<?php echo $pageVars; ?>" method="post" name="updateCat">
<table width="100%"  border="0" cellspacing="0" cellpadding="3" class="mainTable">
<tr>
  	<td class="tdTitle">Delete</td>
    <td class="tdTitle">Category Name</td>
    <td class="tdTitle"><!--Sub-Category Name--></td>
    <td class="tdTitle"><!--Top Navigation Link--></td>
    <td class="tdTitle"><!--Site Document Navigation Link--></td>
  </tr>
<?php
	$cats = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images_cat");
	if($cats==true){
		for($c=0; $c<count($cats); $c++){	
	echo'
	<tr>
		<td class="tdText">
		'; if($cats[$c]['locked']==1){echo'<img src="/admin/images/padlock.png" alt="locked" title="locked" width="12" border="0" />';}else{ echo'
			<a href="/admin/filemanager/index.php?fmact=delCat&amp;catId='.$cats[$c]['catId'].'"><img src="/admin/images/del.gif" alt="delete" title="delete" border="0" /></a>
			<input type="hidden" name="catId'.$c.'" value="'.$cats[$c]['catId'].'" />
			'; } echo'
		</td>
		<td class="tdText">
			<input type="text" class="textbox" name="catName'.$c.'" value="'.$cats[$c]['catName'].'" />
        </td>
		<td class="tdText">
			<!--<select name="catFather'.$c.'">';
				$catFather = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images_cat WHERE catId = ".$cats[$c]['catFather']);
				if($catFather==true && $cats[$c]['catFather']>0){
					echo '<option value="0">Top Level</option>';
				}else{
					echo '<option value="0" selected="selected">Top Level</option>';
				}
				for($f=0; $f<count($cats); $f++){	
					if($cats[$f]['catId']!=$cats[$c]['catId']){
						echo'<option value="'.$cats[$f]['catId'].'" ';if($catFather==true && $catFather[0]['catId']==$cats[$f]['catId']){echo'selected="selected"';} echo'>'.$cats[$f]['catName'].'</option>';
					}
				}
				echo'
			</select>-->
        </td>
		<td class="tdText">
			<!--<select name="topNav'.$c.'">
				<option value="0" ';if($cats[$c]['topNav']==0){echo'selected="selected"';} echo'>No</option>
				<option value="1" ';if($cats[$c]['topNav']==1){echo'selected="selected"';} echo'>Yes</option>
			</select>-->
        </td>
		<td class="tdText">
			<!--<select name="siteDoc'.$c.'">
				<option value="0" ';if($cats[$c]['siteDoc']==0){echo'selected="selected"';} echo'>No</option>
				<option value="1" ';if($cats[$c]['siteDoc']==1){echo'selected="selected"';} echo'>Yes</option>
			</select>-->
        </td>
	</tr>
	';
		}
	}
?>
	<tr>
    	<td class="tdText" colspan="5" align="center"><input type="submit" name="submit" id="submit" class="submit" value="Update" /></td>
    </tr>
</table>
</form>
<br />
<p class="copyText"><?php echo $paginationAssigned; ?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="3" class="mainTable">
<tr>
  	<td class="tdTitle">Unassign</td>
    <td class="tdTitle">Image Name</td>
    <td class="tdTitle">Image Location</td>
    <td class="tdTitle">Assigned To Category</td>
  </tr>
<?php
	if($assigned==true){
		for($a=0; $a<count($assigned); $a++){	
	echo'
	<tr>
		<td class="tdText"><a href="/admin/filemanager/index.php?fmact=unassign&amp;imgId='.$assigned[$a]['imgId'].'&amp;catId='.$assigned[$a]['catId'].$pageVarsAlt.'"><img src="/admin/images/del.gif" alt="delete" title="delete" border="0" /></a></td>
		<td class="tdText">
		';
			echo $assigned[$a]['imgName'];
		echo'
        </td>
		<td class="tdText">
		';
			echo $assigned[$a]['imgLoc'];
		echo'
        </td>
        <td class="tdText">
		';
			echo $assigned[$a]['catName'];
		echo'
        </td>
	</tr>
	';
		}
	}
?>
</table>
<br />
<p class="copyText"><?php echo $paginationImages; ?></p>
<form action="/admin/filemanager/index.php<?php echo $pageVars; ?>" method="post" name="imageNames" id="search">
<table width="100%"  border="0" cellspacing="0" cellpadding="3" class="mainTable">
<tr>
  	<td class="tdTitle">Images</td>
  </tr>
  <tr>
  	<td>

<?php
	$dirArray = walk_dir($GLOBALS['rootDir']."/images/uploads");
	$i = 0;
	if (isset($_GET['searchStr'])) {
		$resultsArray = array();
		for ($j=0; $j<count($dirArray); $j++) {
			$image = $dirArray[$j];
			$splitImage = explode('/', $image);
			if(!eregi("thumb_",$image)){
				$imageName = explode('.', array_pop($splitImage));
				$searchResult = strpos(strtolower($imageName[0]), strtolower($_GET['searchStr']));
				if($searchResult !== false){
					array_push($resultsArray, $dirArray[$j]);
				}
			}
		}
		$dirArray = $resultsArray;
	}
	if($images==true){
		for($i=0; $i<count($images); $i++){	
		
			if(file_exists($GLOBALS['rootDir'].$images[$i]['imgLoc'])) {		
	?>
	
              <!--<div style="float:left; width:100px; height:130px;">-->
                    <table width="100px" height="130px" border="0" cellspacing="0" cellpadding="2" class="mainTable" style="float: left; margin: 1px; max-width:100px;">
                        <tr style="width:100px;">
                            <td rowspan="2"class="copyText" align="center" width="50px" height="40px"><a href="<?php echo $images[$i]['imgLoc']; ?>" class="galleryImg" title="<?php echo $images[$i]['imgName']; ?>"><img src="<?php echo $images[$i]['imgLoc']; ?>" style="width:50px; max-height:50px; border:none;" /></a></td>
                            <td class="copyText" width="12px;" valign="top"><a  style="font-size:10px;" <?php if(permission("filemanager","delete")==TRUE){ ?>href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>','index.php?unlink=<?php echo urlencode($images[$i]['imgLoc']).$pageVarsAlt; ?>');" class="txtLink" <?php } else { echo $link401; } ?>><img src="/admin/images/del.gif" alt="delete" title="delete" border="0" /></a>
                            </td>
                        </tr>
                        <tr style="width:100px;">
                            <td class="copyText" valign="bottom">
                            <a href="<?php echo $images[$i]['thumbLoc']; ?>" class="galleryImg" title="Thumbnail: <?php echo $images[$i]['imgName']; ?>"><img src="/admin/images/evo/magnifying.png" alt="Thumbnail" title="Thumbnail" border="0" /></a>
                            </td>
                        </tr>
                        <tr style="width:100px;">
                            <td class="copyText" colspan="2" align="center"><input typye="text" name="imgName<?php echo $i ?>" value="<?php echo $images[$i]['imgName']; ?>" class="textbox" style="width:80px;" onfocus="if(this.value=='Name'){ this.value=''; }" onblur="if(this.value==''){ this.value='Name'; }" />
                            <input type="hidden" name="imgId<?php echo $i ?>" value="<?php echo $images[$i]['imgId']; ?>" />
                            </td>
                        </tr>
                        <tr style="width:100px;">
                        <td class="copyText" colspan="2" align="center" width="50px" style="font-size:8px;">
                        <input typye="text" name="imgLoc" value="<?php echo $images[$i]['imgLoc']; ?>" class="textbox" style="width:80px;" />
                        </td>
                        </tr>
                    </table>
              <!-- </div> -->
	
	
	<?php 
			} else {
				$where = "imgId = ".$images[$i]['imgId'];	
				$imgDel = $db->delete($glob['dbprefix']."CubeCart_images", $where);
				$imgCatDel = $db->delete($glob['dbprefix']."CubeCart_imgcat_idx", $where);
			}
		}
	} 
	?>
        <br clear="all" />
        </td>
		</tr>
        <?php
		if($i==0) {
	
		?>
	
		<tr>
	
		<td colspan="3" class="tdText"><?php echo $lang['admin']['filemanager']['no_images_added'];?></td>
	
		</tr>
	
	<?php } ?>
	<tr>
    	<td class="tdText" align="center"><input type="submit" name="submit" id="submit" class="submit" value="Update" /></td>
    </tr>
</table>
</form>

<script>
jQuery(function() {
	var classSwitch = 0;
	jQuery('.mainTable tr').each(function (i) { 
		if(jQuery(this).children('td').is('.tdTitle')){
			classSwitch = 0;
		}
		if(classSwitch == 0){
			jQuery(this).children('.tdText').addClass('tdEven');
			classSwitch = 1;
		}else{
		 	jQuery(this).children('.tdText').addClass('tdOdd');
			classSwitch = 0;
		}
	});
});
</script>
<?php include("../includes/footer.inc.php"); ?>

