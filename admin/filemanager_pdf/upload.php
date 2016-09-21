<?php

/*

 * FCKeditor - The text editor for internet

 * Copyright (C) 2003-2004 Frederico Caldeira Knabben

 *

 * Licensed under the terms of the GNU Lesser General Public License

 * (http://www.opensource.org/licenses/lgpl-license.php)

 *

 * For further information go to http://www.fredck.com/admin/includes/rte/ 

 * or contact fckeditor@fredck.com.

 *

 * upload.php: Basic file upload manager for the editor. You have

 *   to have set a directory called "userimages" in the root folder

 *   of your web site.

 *

 * Authors:

 *   Frederic TYNDIUK (http://www.ftls.org/ - tyndiuk[at]ftls.org)

 * Modded by:

 *	Alistair Brookbanks Devellion Limited

 */

// Init var :

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
/*
if(permission("filemanager","write")==FALSE){

	header("Location: ".$GLOBALS['rootRel']."admin/401.php");

	exit;

}
*/
$BaseDir='/';

// End int var

?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >

<html>

	<head>

		<title><?php echo $lang['admin']['filemanager']['file_uploader']; ?></title>

		<link rel="stylesheet" type="text/css" href="../includes/rte/css/fck_dialog.css">

		<link rel="stylesheet" type="text/css" href="../styles/style.css">

	</head>

	<body>

	<p class="pageTitle">Upload File</p>

	<?php if($_GET["custom"]==1){
	
	$productId=mysql_real_escape_string($_GET['pid']);
	
	$targetfield = $_GET['targetfield']; $targetdiv = $_GET['targetdiv']; $imagecontrols = $_GET['imagecontrols']; ?>

	<form action="<?php echo $GLOBALS['rootRel'];?>admin/filemanager_pdf/upload.php" method="post" enctype="multipart/form-data" target="_self">

	<table align="center" border="0" cellpadding="3" cellspacing="0" class="mainTable">

    <tr>

    <td class="tdTitle">Please browse for your file: </td>

  </tr>

  <tr>

    <td><input name="FCKeditor_File" type="file" class="" /></td>

  </tr>

  <tr>

    <td align="center"><input name="submit" type="submit" class="submit" value="Upload File" /></td>

  </tr>

</table>

	<input type="hidden" name="productId" value="<?PHP echo $productId; ?>" />
	<input type="hidden" name="targetfield" value="<?PHP echo $targetfield; ?>" />
    <input type="hidden" name="targetdiv" value="<?PHP echo $targetdiv; ?>" />
    <input type="hidden" name="imagecontrols" value="<?PHP echo $imagecontrols;?>" />

	<?php if(isset($_GET['redir'])){ ?>

	<input type="hidden" value="0" name="redir" />

	<?php } ?>

	<input type="hidden" value="1" name="custom" />

	</form>

	<?php } else { ?>

	<form>

		<TABLE>

			<tr>

				<td>


<span class="copyText">

<?php

$uploadFileName = str_replace(array(" ","%20"),"_",$_FILES['FCKeditor_File']['name']);

// Enter max filesize in bytes (Currently 6.5MB)
//$UploadSize=$config['maxImageUploadSize'];
$UploadSize=652428800;

if($_FILES['FCKeditor_File']['size'] > $UploadSize) { 

	echo sprintf($lang['admin']['filemanager']['file_too_big'],$uploadFileName,format_size($config['maxImageUploadSize'])); 

	unlink($_FILES['FCKeditor_File']['tmp_name']);

} elseif (file_exists($GLOBALS['rootDir'].$BaseDir."pdf/".$uploadFileName)) {

	echo sprintf($lang['admin']['filemanager']['img_already_exists'],$uploadFileName);

} elseif (!is_uploaded_file($_FILES['FCKeditor_File']['tmp_name'])) { 

	echo $lang['admin']['filemanager']['upload_too_large'];

/*

    * text: readable text data text/rfc822 [RFC822]; text/plain [RFC2646]; text/html [RFC2854] .
    * image: binary data representing digital images: image/jpeg; image/gif; image/png.
    * audio: digital sound data: audio/basic; audio/wav
    * video: video data: video/mpeg
    * application: Other binary data: application/octet-stream; application/pdf 

*/

} elseif ($_FILES['FCKeditor_File']['type'] != "application/pdf" && $_FILES['FCKeditor_File']['type'] != "application/octet-stream"){

	echo "Error: {$uploadFileName} is not a valid file type.";

	unlink($_FILES['FCKeditor_File']['tmp_name']); 

} else {

	if (is_uploaded_file($_FILES['FCKeditor_File']['tmp_name'])) {

		$savefile = $GLOBALS['rootDir'].$BaseDir."pdf/".$uploadFileName;

	if (move_uploaded_file($_FILES['FCKeditor_File']['tmp_name'], $savefile)) {

		@chmod($savefile, 0644);

		$imageFormat = strtoupper(ereg_replace(".*\.(.*)$","\\1",$uploadFileName));		

		echo sprintf($lang['admin']['filemanager']['image_upload_success'],$uploadFileName);

		$productId=$_POST['productId'];

/*
			if(!isset($_POST['redir'])){

?>

		<script language=javascript>

		<?php if($_POST["custom"]==1){ ?>

		window.opener.addImage('<?php echo $uploadFileName; ?>', '<?php echo $GLOBALS['rootRel'].$BaseDir."pdf/".$uploadFileName; ?>', '<?PHP echo $_POST['targetfield']; ?>', '<?PHP echo $_POST['targetdiv']; ?>', '<?PHP echo $_POST['imagecontrols']; ?>') ;

		<?php } else { ?>

		window.opener.setImage('<?php echo $GLOBALS['rootRel'].$BaseDir."pdf/".$uploadFileName; ?>') ;

		<?php } ?>

		window.close();

		</script>

		<?php

			} // end if not set redir
*/
		

		}

	}



	

} ?></span>

				</td>

			</tr>

		</table>

	</form>

	<?php }  ?>

	<p align="center"><?php if(isset($_POST['submit'])){ ?><input type="button" class="submit" value="<?php echo $lang['admin']['filemanager']['back']; ?>" onClick="history.back()" /><?php } ?> 

	<input type="button" class="submit" value="<?php echo $lang['admin']['filemanager']['close_window']; ?>"  onclick="<?php if(isset($_POST['redir'])) { echo "window.opener.location.reload();"; } ?> javascript:window.close();" /></p>

	</body>

</html>