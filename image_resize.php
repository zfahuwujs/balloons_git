<?php
if ((!isset($_GET['im']) || trim($_GET['im']) == ''))
{
	die('Missing record ID!');
}
$img_path = $_GET['im'];
$x_size = $_GET['size'];
$y_size = $_GET['y_size'];


/*if(mime_content_type($_GET['im'])=='image/jpeg'){
	$src = imagecreatefromjpeg($img_path);
}elseif(mime_content_type($_GET['im'])=='image/png'){
	$src = imagecreatefrompng($img_path);
}elseif(mime_content_type($_GET['im'])=='image/gif'){
	$src = imagecreatefromgif($img_path);
}
*/


$imageType = mime_content_type($_GET['im']);

	switch($imageType) {
		case "image/gif":
			$src=imagecreatefromgif($img_path); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$src=imagecreatefromjpeg($img_path); 
			break;
	    case "image/png":
		case "image/x-png":
			$src=imagecreatefrompng($img_path); 
			break;
  	}

$width = imagesx($src);
$height = imagesy($src);
if($x_size!=''){
	
	$x = $x_size; 
	if($y_size!='')
	{
		$y = $y_size;
	}else{
		$y = $height * ($x/$width);
	}
	
}else{
	$y = $y_size;
	$x = $width * ($y/$height);
}


$dst = imagecreatetruecolor($x,$y);
imagealphablending($dst,false);
imagesavealpha($dst,true);
$transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
imagefilledrectangle($dst, 0, 0, $x, $y, $transparent);
	
	
imagecopyresampled($dst,$src,0,0,0,0,$x,$y,$width,$height);
header('Content-Type: '.$imageType);

switch($imageType) {
	case "image/gif":
		imagegif($dst);
		break;
	case "image/pjpeg":
	case "image/jpeg":
	case "image/jpg":
		imagejpeg($dst);
		break;
	case "image/png":
	case "image/x-png":
		imagepng($dst);
		break;
}
?>