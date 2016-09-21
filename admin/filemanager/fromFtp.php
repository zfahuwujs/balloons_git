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

function resize_image_smart($orig, $dest_width=null, $dest_height=null)
{
  $orig_width = imagesx($orig);
  $orig_height = imagesy($orig);
  $vertical_offset = 0;
  $horizontal_offset = 0;
  if($dest_width == null)
  {
    if($dest_height == null)
    {
      die('$dest_width and $dest_height cant both be null!');
    }
    // height is locked
    $dest_width = $dest_height * $orig_width / $orig_height;
  } else {
    if($dest_height == null)
    {
      // width is locked
      $dest_height = $dest_width * $orig_height / $orig_width;
    } else {
      // both dimensions are locked
      $vertical_offset = $dest_height - ($orig_height * $dest_width) / $orig_width;
      $horizontal_offset = $dest_width - ($dest_height * $orig_width) / $orig_height;
      if($vertical_offset < 0) $vertical_offset = 0;
      if($horizontal_offset < 0) $horizontal_offset = 0;
    }
  }
  $img = imagecreatetruecolor($dest_width, $dest_height);
  imagesavealpha($img, true);
  imagealphablending($img, false);
  $transparent = imagecolorallocatealpha($img, 255, 255, 255, 127);
  imagefill($img, 0, 0, $transparent);
  imagecopyresampled($img, $orig, round($horizontal_offset / 2),
                                  round($vertical_offset / 2),
                                  0,
                                  0,
                                  round($dest_width - $horizontal_offset),
                                  round($dest_height - $vertical_offset),
                                  $orig_width,
                                  $orig_height);
  return $img;
}
if(isset($_POST['saveImages'])){
	if(!empty($_POST['width']) && $_POST['width']!=0 && !empty($_POST['height']) && $_POST['height']!=0)
	$dir = new DirectoryIterator("../../images/ftp/");
	$count = 0;
	 foreach ($dir as $fileinfo) {
		 if($fileinfo->getFilename()!='.' && $fileinfo->getFilename()!='..'){
			 $thumbName = "thumb_".$fileinfo->getFilename();
			 $count++;
			 $ext = pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION);
			 $imageName = $fileinfo->getBasename('.' .$ext);
			 if($ext=='jpg' || $ext =='jpeg'){
					$orgImg = imagecreatefromjpeg($_SERVER['DOCUMENT_ROOT']."/images/ftp/".$fileinfo->getFilename());
					$image = resize_image_smart($orgImg,$_POST['width'],$_POST['height']);
					imagejpeg($image,$_SERVER['DOCUMENT_ROOT']."/images/uploads/thumbs/".$thumbName,100);
					rename($_SERVER['DOCUMENT_ROOT']."/images/ftp/".$fileinfo->getFilename(),$_SERVER['DOCUMENT_ROOT']."/images/uploads/".$fileinfo->getFilename());
				}
				if($ext=='gif' || $ext =='png'){
					$orgImg = imagecreatefrompng($_SERVER['DOCUMENT_ROOT']."/images/ftp/".$fileinfo->getFilename());
					$image = resize_image_smart($orgImg,$_POST['width'],$_POST['height']);
					imagepng($image,$_SERVER['DOCUMENT_ROOT']."/images/uploads/thumbs/".$thumbName);
					rename($_SERVER['DOCUMENT_ROOT']."/images/ftp/".$fileinfo->getFilename(),$_SERVER['DOCUMENT_ROOT']."/images/uploads/".$fileinfo->getFilename());
				}
			 	$record['imgCatId'] = $db->mySQLSafe($_POST['imgCatId']);
				$record['imgName'] = $db->mySQLSafe($imageName);
				$record['imgLoc'] = $db->mySQLSafe("/images/uploads/".$fileinfo->getFilename());
				$record['thumbLoc'] = $db->mySQLSafe("/images/uploads/thumbs/".$thumbName);
				$record['filename'] = $db->mySQLSafe($fileinfo->getFilename());
				$insert = $db->insert('CubeCart_images',$record);
				if($insert==TRUE){
					echo 'New image added: '.$fileinfo->getFilename().'<br>';
				}
		 }
	 }
	 echo 'All '. $count.' added.';
}

?>

<h1>Copy files from ftp</h1>
<p>Please make sure all files were uploaded in to folder /images/ftp</p>
<form action="" method="post">
<p>Please select type of the pictures</p>
<select name="imgCatId">
	<option class="image" value="1" id="prodCat">Product Images</option>
  <option class="image" value="3" id="slideCat">Slider Images</option>
  <option class="image" value="5" id="catCat">Category Images</option>
</select>
<p>Please enter THUMB width and height (best practice for products put image size in product page)</p>
<p>Width: <input type="text" name="width"></p>
<p>Height: <input type="text" name="height"></p>
<p><input type="submit" value="Save images" name="saveImages">
</form>



<?php include("../includes/footer.inc.php"); ?>

