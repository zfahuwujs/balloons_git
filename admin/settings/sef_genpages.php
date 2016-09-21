<?php

/* <rf> search engine friendly url mods */

/////////////////////////////////////////////
// CONFIGURATION ITEMS
// CHANGE THESE TO YOUR OWN SETTINGS
// WARNING!! YOU MUST READ THE README.TXT FILE 
// BEFORE YOU CHANGE ANY OF THESE SETTINGS!!!
$ftp_server = "";              // your FTP server e.g. ftp.mydomain.com
$ftp_username = "";            // your FTP username
$ftp_password = "";	       // your FTP password
$ftp_passive = FALSE;          // Do you need to login to FTP in passive mode if so make this TRUE. Try this if your having troubles.
$ftp_cube_cart_root_dir = "";  // dir to cubecart root from the dir the FTP server starts you at (see README.TXT) e.g. public_html/
// END OF CONFIGURATION ITEMS
/////////////////////////////////////////////

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
include("../includes/functions.inc.php");

if(permission("settings","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

include_once("../../includes/sef_urls.inc.php");

include("../includes/header.inc.php"); 

set_time_limit(0);

// connect to FTP server
$conn_id = FTPConnect($ftp_server, $ftp_username, $ftp_password);
if($conn_id == FALSE)
{
	echo "<b>Cannot connect to your FTP server. Check your FTP settings, see README.TXT for info</b>";
	include("../includes/footer.inc.php"); 
	return;
}

// ok time to generate the product pages
$exist = $db->select("SELECT productId FROM ".$glob['dbprefix']."CubeCart_inventory order by productId");
if( $exist ) 
{
	echo "<b>CREATING PRODUCT PAGES NOW....<br></b>";
	for($x = 0; $x < count($exist); $x++) 
	{
		$prodId = $exist[$x]['productId'];
		$producturl = generateProductUrl($prodId);
		$tempfilename = explode("?", basename($producturl));
		$prodfilename = $tempfilename[0];		
		$proddirectory = dirname($producturl) . "/";
		generatePage($ftp_cube_cart_root_dir, $proddirectory, $prodfilename, $prodId, "prod");
		echo "Created ".$ftp_cube_cart_root_dir.$proddirectory.$prodfilename." <br>";
	}
	echo "<p>";
}

// ok time to generate the tellafriend pages
$exist = $db->select("SELECT productId FROM ".$glob['dbprefix']."CubeCart_inventory order by productId");
if( $exist ) 
{
	echo "<b>CREATING TELLAFRIEND PAGES NOW....<br></b>";
	for($x = 0; $x < count($exist); $x++) 
	{
		$prodId = $exist[$x]['productId'];
		$producturl = generateTellFriendUrl($prodId);
		$tempfilename = explode("?", basename($producturl));
		$prodfilename = $tempfilename[0];	 		
		$proddirectory = dirname($producturl) . "/";
		generatePage($ftp_cube_cart_root_dir, $proddirectory, $prodfilename, $prodId, "taf");
		echo "Created ".$ftp_cube_cart_root_dir.$proddirectory.$prodfilename." <br>";
	}
	echo "<p>";
}

// ok time to generate the category pages
$exist = $db->select("SELECT cat_id FROM ".$glob['dbprefix']."CubeCart_category order by cat_id");
if( $exist ) 
{
	echo "<b>CREATING CATEGORY PAGES NOW....<br></b>";
	for($x = 0; $x < count($exist); $x++) 
	{
		$catId = $exist[$x]['cat_id'];
		$categoryurl = generateCategoryUrl($catId);
		$tempfilename = explode("?", basename($categoryurl));
		$catfilename = $tempfilename[0];	 		
		$catdirectory = dirname($categoryurl) . "/";
		generatePage($ftp_cube_cart_root_dir, $catdirectory, $catfilename, $catId, "cat");
		echo "Created ".$ftp_cube_cart_root_dir.$catdirectory.$catfilename." <br>";
	}
	// handle the sale item category page as a special case
	$catId = "saleItems";
	$categoryurl = generateCategoryUrl($catId);
	$tempfilename = explode("?", basename($categoryurl));
	$catfilename = $tempfilename[0];		
	$catdirectory = "";
	generatePage($ftp_cube_cart_root_dir, $catdirectory, $catfilename, $catId, "cat");
	echo "Created ".$ftp_cube_cart_root_dir.$catdirectory.$catfilename." <br>";	
	echo "<p>";
}

// ok time to generate the document pages
$exist = $db->select("SELECT doc_id FROM ".$glob['dbprefix']."CubeCart_docs order by doc_id");
if( $exist ) 
{
	echo "<b>CREATING DOCUMENT PAGES NOW....<br></b>";
	for($x = 0; $x < count($exist); $x++) 
	{
		$docId = $exist[$x]['doc_id'];
		$documenturl = generateDocumentUrl($docId);
		$tempfilename = explode("?", basename($documenturl));
		$docfilename = $tempfilename[0];			
		$docdirectory = dirname($documenturl) . "/";
		generatePage($ftp_cube_cart_root_dir, $docdirectory, $docfilename, $docId, "doc");
		echo "Created ".$ftp_cube_cart_root_dir.$docdirectory.$docfilename." <br>";
	}
	echo "<p>";
}

// disconnect from FTP server
FTPDisconnect($conn_id);

echo "<b>DONE!...<p></b>";

include("../includes/footer.inc.php"); 

// END OF SCRIPT!!






/**************** functions *****************************/

function generatePage($cubecartdir, $dir, $filename, $Id, $pagetype)
{
	global $conn_id, $ftp_server, $ftp_username, $ftp_password, $ftp_cube_cart_root_dir;

	// create directory structure
	FTPMkDir($conn_id, "/".$ftp_cube_cart_root_dir.$dir);

	// count directory deep levels
	$path=split("/", $dir);
	$deep = count($path);
	$homedir = "./";
	for ($i=1;$i<$deep;$i++) $homedir = $homedir . "../";

	// open file for writing
	// most likely it already exists need to delete it then so we can update it
	@ftp_delete($conn_id, '/'.$ftp_cube_cart_root_dir.$dir.$filename);
	$handle = fopen('../../images/uploads/seotmp.tmp', "w");

	// generate correct page contents
	if(strcmp($pagetype, "prod") == 0)
	{		
		$pagecontents = "<?php\n\n// This is an automatic SEO Mod generated file. Do not modify!\n\nchdir('$homedir');\n".'$_GET[\'act\']=\'viewProd\';'."\n".'$_GET[\'productId\']'."='$Id';\ninclude('shop.php');\n\n?>\n";
	}
	else if(strcmp($pagetype, "cat") == 0)
	{		
		$pagecontents = "<?php\n\n// This is an automatic SEO Mod generated file. Do not modify!\n\nchdir('$homedir');\n".'$_GET[\'act\']=\'viewCat\';'."\n".'$_GET[\'catId\']'."='$Id';\ninclude('shop.php');\n\n?>\n";
	}
	else if(strcmp($pagetype, "taf") == 0)
	{		
		$pagecontents = "<?php\n\n// This is an automatic SEO Mod generated file. Do not modify!\n\nchdir('$homedir');\n".'$_GET[\'act\']=\'taf\';'."\n".'$_GET[\'productId\']'."='$Id';\ninclude('shop.php');\n\n?>\n";
	}
	else if(strcmp($pagetype, "doc") == 0)
	{		
		$pagecontents = "<?php\n\n// This is an automatic SEO Mod generated file. Do not modify!\n\nchdir('$homedir');\n".'$_GET[\'act\']=\'viewDoc\';'."\n".'$_GET[\'docId\']'."='$Id';\ninclude('shop.php');\n\n?>\n";
	}
	
	// write and close file
	fwrite($handle, $pagecontents);
	fclose($handle);
	$handle = fopen('../../images/uploads/seotmp.tmp', "r");
	@ftp_fput($conn_id, '/'.$ftp_cube_cart_root_dir.$dir.$filename, $handle, FTP_ASCII);
	fclose($handle);	

	// lets change permissions to 755
	for($i=0;$i<5;$i++) // damn command can execute asychronous to above file creation meaning file might not be ready yet, simplest hack
	{
		if(@ftp_site($conn_id, 'CHMOD 0755 ' . '/'.$ftp_cube_cart_root_dir.$dir.$filename) == TRUE)
			break;  // success!
		sleep(1);
	}

	// ok for some reason I can only assume the asychronous behaviour between the FTP library and fopen we get a filesize of 0
	// extremely rare but a pain in the neck nevertheless. If this happens redo this function. Simple hack
	if(@ftp_size($conn_id, '/'.$ftp_cube_cart_root_dir.$dir.$filename) == 0)
	{
		generatePage($cubecartdir, $dir, $filename, $Id, $pagetype);
	}
}

/***************** FTP functions ************************/

function FTPConnect($server, $username, $password)
{
	$conn_id = @ftp_connect($server);
	if($conn_id != FALSE) 
	{
		if(@ftp_login($conn_id, $username, $password) == FALSE) 
		{
			@ftp_close($conn_id);
			$conn_id = FALSE; // wrong details
		}	
		else
		{
			if($ftp_passive == TRUE)
			{
				// turn on passive mode
				@ftp_pasv($conn_id, TRUE);
			}
		}
	}
	return $conn_id;
}

function FTPDisconnect($conn_id)
{
	ftp_close($conn_id);
}

function FTPMkDir($conn_id, $path)
{
	$dir=split("/", $path);
	$path="";
	$ret = true;

	for ($i=1;$i<count($dir);$i++)
	{
		$path.="/".$dir[$i];
		if(!@ftp_chdir($conn_id,$path))
		{
			@ftp_chdir($conn_id,"/");
			if(!@ftp_mkdir($conn_id,$path))
			{
				$ret=false;
				break;
			}
			ftp_site($conn_id, 'CHMOD 0755 '.$path);
		} 
	}
	return $ret;
} 

/* <rf> end mods */

?>
