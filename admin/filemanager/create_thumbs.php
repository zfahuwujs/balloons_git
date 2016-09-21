<?php
/*
				All changes done by Artur Pasnikowski - NOT FINISHED DONE TO 266 LINE AND DELETE NOT CHECKED
*/
include("../../includes/global.inc.php");
require_once("../../classes/db.inc.php");
$db = new db();
include_once("../../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include_once("../../includes/sslSwitch.inc.php");
include("../../classes/gd.inc.php");
include("../includes/auth.inc.php");
if(permission("filemanager","write")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}
$error = array();
//			SETTINGS
$upload_dir = $GLOBALS['rootDir']."/images/"; 							// The directory for the images to be saved in
$upload_path = $upload_dir."uploads/";									// The path to where the image will be saved
$thumb_image_prefix = "thumbs/thumb_";									// The prefix name to the thumb image
$max_file = "2"; 															// Maximum file size in MB
$max_width = $config['gdmaxImgSize'];									// Max width allowed for the large image (last check was 1080)
$allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
$allowed_image_ext = array_unique($allowed_image_types); // do not change this
######################################################################################################

//			SET WIDTH HEIGHT	
if(isset($_POST['width']) && isset($_POST['height'])){
	$thumb_width=$_POST['width'];
	$thumb_height=$_POST['height'];
}elseif(!empty($_GET['width']) && !empty($_GET['height'])){
	$thumb_width=$_GET['width'];
	$thumb_height=$_GET['height'];
}else{
	$thumb_width=100;
	$thumb_height=100;
}

		
if (isset($_POST["upload"])) {
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
		
		//			UPLOADED FILE DATA
		$userfile_name = $_FILES['image']['name'];
		$userfile_tmp = $_FILES['image']['tmp_name'];
		$userfile_size = $_FILES['image']['size'];
		$userfile_type = $_FILES['image']['type'];
		
		
	
		//			SET FILENAME AND FILE_EXT
		if($_POST["name"]!=''){
			$file = basename($_FILES['image']['name'],".".$file_ext);
			$file_ext = strtolower(substr($file, strrpos($file, '.') + 1));
			$post_name = str_replace(array(" ","%20"),"_",preg_replace("/[^a-zA-Z0-9._-\s]/", "", $_POST["name"]));
			$filename = $post_name.'.'.$file_ext;
		}else{
			$error[] = "Please enter a name for your image.";
		}
		
		//			VALIDATE FILE EXT
		$valid_ext = false;
		foreach ($allowed_image_ext as $mime_type => $ext) {
			if(strtoupper($ext)==strtoupper($file_ext)){
				$valid_ext = true;
				break;
			}
		}
		if($valid_ext==false){
			$error[] = "Wrong type of file.";
		}
		
		//			CHECK FILE SIZE
		if ($userfile_size > ($max_file*1048576)) {
			$error[] = "Images must be under ".$max_file."MB in size";
		}
		
		//			CHECK IF FILE WITH POSTED NAME DOES NOT EXIST	
		$image = $db->select("SELECT imgName FROM ".$glob['dbprefix']."CubeCart_images WHERE imgName = ".$db->mySQLSafe($post_name));
		if($image==true){
			$error[] = "There is already an image with that name. Please try a different name for your image";
		}
	
	}else{
		$error[] ="Select an image for upload";
	}
	//			Create the upload directory with the right permissions if it doesn't exist
	if(!is_dir($upload_dir)){ 
		mkdir($upload_dir, 0777);
		chmod($upload_dir, 0777);
	}
	//			Image Locations
	$large_image_location = $upload_path.$filename;
	//$thumb_image_location = $upload_path.$thumb_image_prefix.$filename;

	//Check to see if any images with the same name already exist
	if (file_exists($large_image_location)){
		$error[] = "Image with that name: $filename already exist.";
	}
	if(file_exists($thumb_image_location)){
		$error[] = "Thumb image with that name: $filename already exist.";
	}
	
	if(empty($error)){//			ALL GOOD
		//			MOVE MAIN IMAGE FILE
		move_uploaded_file($userfile_tmp, $large_image_location);
		chmod($large_image_location, 0777);
		//			SAVE IMAGE TO DB
		$record["filename"] = $db->mySQLSafe($filename);
		$record["imgLoc"] = $db->mySQLSafe("/images/uploads/".$filename);
		$record["thumbLoc"] = $db->mySQLSafe("/images/uploads/".$thumb_image_prefix.$filename);
		$record["imgName"] = $db->mySQLSafe($post_name);
		
		$record['imgCatId'] = $db->mySQLSafe($_POST['category']);
		
		$insert = $db->insert($glob['dbprefix']."CubeCart_images", $record);
		
		//			SAVE IMAGE CATEGORY
		$cat_record['imgId'] = $db->mySQLSafe($db->insertid());
		$cat_record['catId'] = $db->mySQLSafe($_POST['category']);
		$insert = $db->insert($glob['dbprefix']."CubeCart_imgcat_idx", $cat_record);
		
		$width = getWidth($large_image_location);
		$height = getHeight($large_image_location);
		//	Scale the image if it is greater than the width set above
		if ($width > $max_width){
			$scale = $max_width/$width;
			$uploaded = resizeImage($large_image_location,$width,$height,$scale);
		}else{
			$scale = 1;
			$uploaded = resizeImage($large_image_location,$width,$height,$scale);
		}
	}
}


if (isset($_POST["upload_thumbnail"])) { 
	//Get the new coordinates to crop the image.
	$x1 = $_POST["x1"];
	$y1 = $_POST["y1"];
	$x2 = $_POST["x2"];
	$y2 = $_POST["y2"];
	$w = $_POST["w"];
	$h = $_POST["h"];
	$filename = $_POST['filename'];
	$scale = $_POST['scale'];
	$large_image_location = $upload_path.$filename;
 	$thumb_image_location = $upload_path.$thumb_image_prefix.$filename;
	$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
	//Reload the page again to view the thumbnail
	//header("location:".$_SERVER["PHP_SELF"]."?width=".$thumb_width."&height=".$thumb_height);
	//exit();
}


if ($_GET['a']=="delete" && strlen($_GET['t'])>0){
//get the file locations 
	$large_image_location = $upload_path.$large_image_prefix.$_GET['t'];
	$thumb_image_location = $upload_path.$thumb_image_prefix.$_GET['t'];
	if (file_exists($large_image_location)) {
		unlink($large_image_location);
	}
	if (file_exists($thumb_image_location)) {
		unlink($thumb_image_location);
	}
	header("location:".$_SERVER["PHP_SELF"]."?width=".$thumb_width."&height=".$thumb_height);
	exit(); 
}

if (file_exists($large_image_location)){
	$large_photo_exists = 1;
}
if(file_exists($thumb_image_location)){
	$thumb_photo_exists = 1;
}


include("../includes/header.inc.php");
?>

<?php

//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
	$current_large_image_width = getWidth($large_image_location);
	$current_large_image_height = getHeight($large_image_location);
	?>
<script type="text/javascript">
function preview(img, selection) { 
	var scaleX = <?php echo $thumb_width;?> / selection.width; 
	var scaleY = <?php echo $thumb_height;?> / selection.height; 
	
	$('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 

$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("You must make a selection first");
			return false;
		}else{
			return true;
		}
	});
}); 

$(window).load(function () { 
	$('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', onSelectChange: preview }); 
});

</script>
<?php }?>


<?php
//Display error message if there are any
if(!empty($error)){
	foreach($error as $e){
		echo "<p class='error'>$e</p>";
	}
}
if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){
	echo "<img src='/images/uploads/thumbs/thumb_$filename' />";
	echo "<p><a href=\"".$_SERVER["PHP_SELF"]."\" class='submit'>Upload another</a>";
	echo ' <a href="'.$GLOBALS['rootRel'].'admin/filemanager/index.php" class="submit">Image Manager</a></p>';
	
}else{
		if(strlen($large_photo_exists)>0){?>
			<p class="pageTitle">Create Thumbnail</p>
			<p class="copyTxt">Drag a box over the image on the left to create the thumbnail</p>
			<div align="center" style="width:100%; text-align:center;">
				<img src="/images/uploads/<?php echo $filename?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
				<div style="border:1px #e5e5e5 solid; float:left; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
					<img src="/images/uploads/<?php echo $filename?>" style="position: relative;" alt="Thumbnail Preview" />
				</div>
				<br style="clear:both;"/>
				<form name="thumbnail" action="<?php echo $_SERVER["PHP_SELF"]."?width=".$thumb_width."&height=".$thumb_height;;?>" method="post">
					<input type="hidden" name="x1" value="" id="x1" />
					<input type="hidden" name="y1" value="" id="y1" />
					<input type="hidden" name="x2" value="" id="x2" />
					<input type="hidden" name="y2" value="" id="y2" />
					<input type="hidden" name="w" value="" id="w" />
					<input type="hidden" name="h" value="" id="h" />
					<input type="hidden" name="filename" value="<?php echo $filename ?>" id="filename" />
					<input type="hidden" name="scale" value="<?php echo $scale ?>" id="scale" />
					<input type="submit" name="upload_thumbnail" value="Save Thumbnail" id="save_thumb" class="submit" />
				</form>
			</div>
<?php
		}else{ ?>
    	<p class="pageTitle">Upload Image</p>
			<p class="copyText">Your image must be one of the follwing formats: jpg, gif, png</p>
			<form name="photo" enctype="multipart/form-data" id="formLocation" action="<?php echo $_SERVER["PHP_SELF"]."?width=".$thumb_width."&height=".$thumb_height;?>" method="post">
				<input name="actionHolder" id="actionHolder" type="hidden" value="<?php echo $_SERVER["PHP_SELF"]."?width=".$thumb_width."&height=".$thumb_height;?>" />
			<table width="100%" border="0" cellpadding="4" cellspacing="0" class="mainTable">
    <tr>
        <td>
        	<table width="100%" border="0" cellpadding="4" cellspacing="0" class="">
            	<tr>
                	<td class="copyText">Image File: </td><td class="copyText"><input type="file" name="image" size="30" class="" /></td>
                </tr>
                <tr>
                	<td class="copyText">Image Name: </td><td class="copyText"><input type="text" name="name" size="30" class="textbox" /></td>
                </tr>
                <tr>
                	<td class="copyText">
    <?php
		$cats = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images_cat");
        if($cats==true){
	        echo 'Category: </td><td class="copyText"><select name="category" class="textbox" id="imgCat">';
            //<option class="image" value="0" id="emptyCat">Select a Category</option>';
            for($q=0; $q<count($cats); $q++){
    	        echo '<option class="image" value="'.$cats[$q]['catId'].'" '; 
							if($cats[$q]['catId']==1){echo'id="prodCat"';}
							elseif($cats[$q]['catId']==3){echo'id="slideCat"';}
							elseif($cats[$q]['catId']==5){echo'id="catCat"';}
							elseif($cats[$q]['catId']==6){echo'id="brandCat"';}
							elseif($cats[$q]['catId']==7){echo'id="newsCat"';}
							elseif($cats[$q]['catId']==9){echo'id="galCat"';}
							echo'>'.$cats[$q]['catName'].'</option>';
            }
            echo'</select><br />';
        }else{
          	echo'<input type="hidden" name="category" value="0" />';
        }
	?>
    				</td>
                </tr>
                <tr>
                	<td class="copyText">Crop Width: </td><td class="copyText"><input name="width" value="" type="text" class="textbox" id="cropWidth" /></td>
                </tr>
                <tr>
                	<td class="copyText">Crop Height: </td><td class="copyText"><input name="height" value="" type="text" class="textbox" id="cropHeight" /></td>
                </tr>
                <tr>
                	<td class="copyText">Use Image Cropper: </td><td class="copyText">
                    	<select name="useCrop" id="useCrop" class="textbox">
                        	<option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
        <td>
        	<table width="100%" border="0" cellpadding="4" cellspacing="0" class="">
            	<tr>
                	<td class="copyText"><strong>Recommended Crop Settings:</strong></td><td class="copyText"></td>
                </tr>
                <tr <?php evoHide(20); ?>>
                	<td class="copyText">Slider: </td>
                    <td class="copyText"><span id="slideWid"><?php echo $config['slideImgWid'] ?></span> x <span id="slideHei"><?php echo $config['slideImgHei'] ?></span></td>
                </tr>
                <tr <?php evoHide(35); ?>>
                	<td class="copyText">Product: </td>
                    <td class="copyText"><span id="prodWid"><?php echo $config['prodImgWid'] ?></span> x <span id="prodHei"><?php echo $config['prodImgHei'] ?></span></td>
                </tr>
                <tr <?php evoHide(34); ?>>
                	<td class="copyText">Category: </td>
                    <td class="copyText"><span id="catWid"><?php echo $config['catImgWid'] ?></span> x <span id="catHei"><?php echo $config['catImgHei'] ?></span></td>
                </tr>
                <tr <?php evoHide(25); ?>>
                	<td class="copyText">Brand: </td>
                    <td class="copyText"><span id="brandWid"><?php echo $config['brandImgWid'] ?></span> x <span id="brandHei"><?php echo $config['brandImgHei'] ?></span></td>
                </tr>
                <tr <?php evoHide(61); ?>>
                	<td class="copyText">News: </td>
                    <td class="copyText"><span id="newsWid"><?php echo $config['newsImgWid'] ?></span> x <span id="newsHei"><?php echo $config['newsImgHei'] ?></span></td>
                </tr>
                <tr <?php evoHide(80); ?>>
                	<td class="copyText">Gallery: </td>
                    <td class="copyText"><span id="GalWid"><?php echo $config['galImgWid'] ?></span> x <span id="GalHei"><?php echo $config['galImgHei'] ?></span></td>
                </tr>
                <tr <?php evoHide(80); ?>>
                	<td class="copyText">Quick Upload: (dont use cropper) </td><td class="copyText" id="GalHei"><?php echo $config['gdthumbSize'] ?> x <?php echo $config['gdthumbSize'] ?></td>
                </tr>
            </table>
        </td>
    </tr>
    </table>
    <script>
		//auto add crop dimensions on category selection				### NEEDS TO BE CHECKED DONT THINK THAT WORK
		$('#imgCat option').click(function() {
		  if($(this).attr('id')=='prodCat'){
			  $('#cropWidth').val($('#prodWid').text());
			  $('#cropHeight').val($('#prodHei').text());
		  }else if($(this).attr('id')=='slideCat'){
			  $('#cropWidth').val($('#slideWid').text());
			  $('#cropHeight').val($('#slideHei').text());
		  }else if($(this).attr('id')=='catCat'){
			  $('#cropWidth').val($('#catWid').text());
			  $('#cropHeight').val($('#catHei').text());
		  }else if($(this).attr('id')=='brandCat'){
			  $('#cropWidth').val($('#brandWid').text());
			  $('#cropHeight').val($('#brandHei').text());
		  }else if($(this).attr('id')=='newsCat'){
			  $('#cropWidth').val($('#newsWid').text());
			  $('#cropHeight').val($('#newsHei').text());
		  }else if($(this).attr('id')=='galCat'){
			  $('#cropWidth').val($('#GalWid').text());
			  $('#cropHeight').val($('#GalHei').text());
		  }else if($(this).attr('id')=='emptyCat'){
			  $('#cropWidth').val('');
			  $('#cropHeight').val('');
		  }
		});
		
		//change form action for upload type (use image cropper)
		$('#useCrop option').click(function() {
			if($(this).val()==1){
				$('#formLocation').attr('action', $('#actionHolder').val());
			}else if($(this).val()==0){
				$('#formLocation').attr('action', '/admin/filemanager/upload2.php');
			}
		});
	</script>
    <input type="submit" name="upload" value="Upload" class="submit" />

	</form>
<?php }  }?>



<?php include("../includes/footer.inc.php"); 











##########################################################################################################
# IMAGE FUNCTIONS																						 #
# You do not need to alter these functions																 #
##########################################################################################################
function resizeImage($image,$width,$height,$scale) {
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagealphablending($newImage,false);
	imagesavealpha($newImage,true);
	$transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
	imagefilledrectangle($newImage, 0, 0, $newImageWidth, $newImageHeight, $transparent);
	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$image); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$image,100); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$image);  
			break;
    }
	
	chmod($image, 0777);
	return $image;
}
//You do not need to alter these functions
function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagealphablending($newImage,false);
	imagesavealpha($newImage,true);
	$transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
	imagefilledrectangle($newImage, 0, 0, $newImageWidth, $newImageHeight, $transparent);
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$thumb_image_name); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$thumb_image_name,100); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$thumb_image_name);  
			break;
    }
	chmod($thumb_image_name, 0777);
	return $thumb_image_name;
}
//You do not need to alter these functions
function getHeight($image) {
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
}
//You do not need to alter these functions
function getWidth($image) {
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
}


?>