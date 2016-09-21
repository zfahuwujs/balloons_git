<?php
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
if(permission("brands","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

$brandsPerPage = 25;

if(isset($_GET["delete"]) && $_GET["delete"]>0){
		
		$where = "id=".$db->mySQLSafe($_GET["delete"]);
		$delete = $db->delete($glob['dbprefix']."CubeCart_brands", $where);
		
		if($delete == TRUE){
			$msg = "<p class='infoText'>".$lang['admin']['categories']['delete_success']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['categories']['delete_failed']."</p>";
		}

} elseif(isset($_POST['id'])) {

	$record["brandName"] = $db->mySQLSafe($_POST['brandName']);
	$record["brandImage"] = $db->mySQLSafe(preg_replace('/thumb_/i', '', $_POST['image']));

	if($_POST['id']>0) {
 		
		$where = "id=".$db->mySQLSafe($_POST['id']);
		$update = $db->update($glob['dbprefix']."CubeCart_brands", $record, $where);
		
		if($update == TRUE){
			
			$msg = "<p class='infoText'>'".$_POST['brandName']."' ".$lang['admin']['categories']['update_success']."</p>";
		
		} else {
			
			$msg = "<p class='warnText'>".$lang['admin']['categories']['update_fail']."</p>";
		
		}
 		
	} else {
	 	
		$insert = $db->insert($glob['dbprefix']."CubeCart_brands", $record);

		if($insert == TRUE){
			
			$msg = "<p class='infoText'>'".$_POST['brandName']."' ".$lang['admin']['categories']['add_success']."</p>";
		
		} else {
			
			$msg = "<p class='warnText'>".$lang['admin']['categories']['add_failed']."</p>";
		
		}
	}
}

if(!isset($_GET['mode'])){
	
	// make sql query
	if(isset($_GET['edit']) && $_GET['edit']>0){
		
		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_brands WHERE id = %s", $db->mySQLSafe($_GET['edit'])); 
	
	} else {
	
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_brands ORDER BY id ASC";
	} 
	
	if(isset($_GET['page'])){
		$page = $_GET['page'];
	} else {
		$page = 0;
	}
	
	// query database
	$results = $db->select($query, $brandsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = $db->paginate($numrows, $brandsPerPage, $page, "page");
}



include("../includes/header.inc.php"); 
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle">Brands Manager</p></td>
     <?php if(!isset($_GET["mode"])){ ?><td align="right" valign="middle"><a <?php if(permission("brands","write")==TRUE){?>href="?mode=new" <?php } else { echo $link401; } ?> class="addNew"><?php echo $lang['admin']['add_new'];?></a></td><?php } ?>
  </tr>
</table>
<?php if(isset($msg)){ echo stripslashes($msg); }?>
<?php
if(!isset($_GET['mode']) && !isset($_GET['edit'])){
?>
<p class="copyText"><?php echo 'Below is a list of all the current brands in the database.'; ?></p>
<p class="copyText"><?php echo $pagination; ?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="mainTable">
  <tr>
    <td width="10%" nowrap='nowrap' class="tdTitle">Brand Name</td>
    <td align="center" class="tdTitle">Image</td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['categories']['no_products']; ?></td>
    <td class="tdTitle" align="center" width="10%">Edit</td>
    <td class="tdTitle" align="center" width="10%">Delete</td>
  </tr>
  <?php 
  if($results == TRUE){
  
  	for ($i=0; $i<count($results); $i++){ 
	
	$cellColor = "";
	$cellColor = cellColor($i);
	?>
  <tr>
    <td nowrap='nowrap' width="10%" class="<?php echo $cellColor; ?>"><?php echo $results[$i]['brandName']; ?></td>
    <td align="center" valign="middle" nowrap='nowrap'  class="<?php echo $cellColor; ?>">
	<?php 
	if(file_exists($GLOBALS['rootDir']."/images/uploads/".$results[$i]['brandImage']) && !empty($results[$i]['brandImage'])){
	$imgSize = getimagesize($GLOBALS['rootDir']."/images/uploads/".$results[$i]['brandImage']); 
	?>
	<img src="<?php echo $glob['rootRel'];?>images/uploads/<?php echo $results[$i]['brandImage']; ?>" alt="<?php echo $results[$i]['brandImage']; ?>" title="" <?php if($imgSize['0']>49){ ?>height="50"<?php } // end if image exists ?> />
		<?php } else { echo "&nbsp;"; }// end if image exists ?></td>
        
    <td align="center" valign="middle" nowrap='nowrap'  class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['noProducts']; ?></span></td>
    <td align="center" width="10%" class="<?php echo $cellColor; ?>"><a 
	<?php if(permission("brands","edit")==TRUE){ ?>
	href="?edit=<?php echo $results[$i]['id']; ?>" class="txtLink"
	<?php } else { echo $link401; } ?>
	><img src="/admin/images/edit.gif" alt="Edit" title="Edit" width="16" border="0" /></a>
   </td>
    
    <td align="center" width="5%" class="<?php echo $cellColor; ?>">
    <a <?php if(permission("brands","delete")==TRUE && $results[$i]['noProducts']<=0){ ?>
	href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>','?delete=<?php echo $results[$i]['id']; ?>');" class="txtLink" <?php } elseif(permission("brands","delete")==TRUE && $results[$i]['noProducts']>0) {	echo "href='javascript:alert(\"".$lang['admin']['categories']['cannot_del']."\")' class='txtNullLink'"; } else { echo $link401; } ?>><img src="/admin/images/del.gif" alt="Delete" title="Delete" border="0" /></a>
    </td>
   
  </tr>
  <?php } // end loop
  } else { ?>
   <tr>
    <td colspan="7" class="tdText"><?php echo 'No brands exist'; ?></td>
  </tr>
  <?php } ?>
</table>
<p class="copyText"><?php echo $pagination; ?></p>

<?php 
} elseif($_GET["mode"]=="new" OR $_GET["edit"]>0){  

if(isset($_GET["edit"]) && $_GET["edit"]>0){ $modeTxt = $lang['admin']['edit']; } else { $modeTxt = $lang['admin']['add']; } 
?>
<p class="copyText"><?php echo $lang['admin']['categories']['add_desc'];?></p>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/brands/index.php" method="post" enctype="multipart/form-data" name="form1">
<table border="0" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php if(isset($_GET["edit"]) && $_GET["edit"]>0){ echo $modeTxt; } else { echo $modeTxt;  } ?> Brand</td>
  </tr>
  <tr>
    <td class="tdText">Brand Name:</td>
    <td>
      <input name="brandName" type="text" class="textbox" value="<?php if(isset($results[0]['brandName'])) echo validHTML($results[0]['brandName']); ?>" maxlength="255" />
    </td>
  </tr>
<tr <?php evoHide(44); ?>>
        <td align="left" valign="top" class="tdText"><?php echo $lang['admin']['categories']['image_optional'];?></td>
      <td valign="top"><div id="selectedImage">
          <?php if(!empty($results[0]['brandImage'])){ ?>
          <img src="<?php echo $GLOBALS['rootRel']."images/uploads/".$results[0]['brandImage']; ?>" alt="<?php echo $results[0]['brandImage']; ?>" title="" />
          <div  style="padding: 3px;">
            <input type="button" class="submit" src="../images/remove.gif" name="remove" value="<?php echo $lang['admin']['remove']; ?>" onclick="addImage('','')" />
          </div>
          <?php } ?>
        </div>
        <div id="imageControls">
          <table border="0" cellspacing="0" cellpadding="3">
            <tr>
              <td><a href="/admin/filemanager/upload.php" class="submit" target="_blank"><?php echo $lang['admin']['categories']['upload_new_image']; ?></a></td>
            </tr>
            <tr>
              <td><input name="browse" class="submit" type="button" id="browse" onclick="openPopUp('../filemanager/browse.php?custom=1&amp;cat=6','filemanager',450,500)" value="Browse Images" /></td>
            </tr>
          </table>
        </div>
        <input type="hidden" name="image" id="imageName" value="<?php echo $results[0]['brandImage']; ?>" />
      </td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
	<input type="hidden" name="noProducts" value="<?php echo $results[0]['noProducts']; ?>" />
	<input type="hidden" name="id" value="<?php echo $results[0]['id']; ?>" />
	<input type="submit" name="Submit" class="submit" value="<?php if(isset($_GET["edit"]) && $_GET["edit"]>0){ echo $modeTxt; } else { echo $modeTxt;  } ?>" /></td>
  </tr>
</table>
</form>
<?php } ?>
<?php include("../includes/footer.inc.php"); ?>
