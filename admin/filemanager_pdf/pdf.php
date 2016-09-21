<?PHP

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

if(permission("filemanager","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}else{
?>
<style type="text/css">
body {
	font:12px Verdana, Arial, Helvetica, sans-serif;
	text-indent:10;
}
p {
	line-height:2;
}
.eof {
	color:#FFF;
	background-color:#494949;
	text-transform:uppercase;
}
</style>
<?php
	function readDirectory ( $dir = '.', $sort = FALSE )
	{
		if ( $handle = opendir ( $dir ) )
		{
			while ( $file = readdir ( $handle ) )
			{
				if ( $file != '.' && $file != '..' )
				{
					$items[] = $file;
				}
			}
			closedir ( $handle );
		} else {
			echo "<p class='eof'>unable to open directory</p>";
		}
		if ( $sort == TRUE )
		{
			sort ( $items );
		}
		return ( !empty ( $items ) ) ? $items : '<p>No Files</p>' ;
	}
	
	if ( isset ( $_POST['submit'] ) )
	{
		if ( isset ( $_FILES ) ) {
			if ( $_FILES['pdf_cat']['size'] > 0 && $_FILES['pdf_cat']['error'] == 0 ) {
				$uploadFileName = str_replace(array(" ","%20"),"_",$_FILES['pdf_cat']['name']);
				$target_path = $glob['rootDir'] . '/PDF/' . $uploadFileName;
				echo $target_path . "<br />";
				move_uploaded_file( $_FILES['pdf_cat']['tmp_name'], $target_path );
				chmod($target_path,0775);
			}
		}
	}
	
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>PDF Upload Facility</title>
	<link rel="stylesheet" type="text/css" href="../includes/rte/css/fck_dialog.css">
	<link rel="stylesheet" type="text/css" href="../styles/style.css">
	</head>
	<body>
	<p class="pageTitle">PDF Upload Facility</p>
	<?PHP
	if ( isset ( $_GET['act'] ) && $_GET['act'] == 'read' )
	{
		$contents = readDirectory ( $glob['rootDir'] . '/PDF' );
		if ( is_array ( $contents ) )
		{
		?>
		<ul>
		<?PHP
		foreach ( $contents as $value )
		{
		?>
		<li><a href="<?PHP echo $glob['storeURL'] . '/PDF/' . $value; ?>"><?PHP echo $glob['storeURL'] . '/PDF/' . $value; ?></a></li>
		<?PHP
		}
		echo "<p class='eof'>End of File</p>";
		} else {
		echo "<p>$contents</p>";
		}
	}
	if ( isset ( $_GET['act'] ) && $_GET['act'] == 'upload' )
	{
	?>
	<form action="<?PHP echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
	  <table class="mainTable" align="center" border="0" cellpadding="3" cellspacing="0">
		<tbody>
		  <tr>
			<td class="tdTitle">Please browse for your PDF:</td>
		  </tr>
		  <tr>
			<td><input name="pdf_cat" class="textbox" type="file"></td>
		  </tr>
		  <tr>
			<td align="center"><input name="submit" class="submit" value="Upload PDF" type="submit"></td>
		  </tr>
		</tbody>
	  </table>
	</form>
	<p align="center">
	  <input class="submit" value="Close Window" onClick=" javascript:window.close();" type="button">
	</p>
	<?PHP
	}
	?>
	</body>
	</html>
<?php
}
?>