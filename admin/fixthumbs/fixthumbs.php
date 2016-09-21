<?php
/*
+--------------------------------------------------------------------------
|	Thumb Fix v 1.4
|	CubeCart Mod by PHPRocket -> http://www.phprocket.com
|    Updated for support for subfolders
+--------------------------------------------------------------------------
|	fixthumbs.php
|   ========================================
|     CubeCart Mod by PhpRocket -> http://www.phprocket.com
|     Last Modified 16 JAN 2007
|     support@phprocket.com
+--------------------------------------------------------------------------
*/
	//The max amount of thumbs this script will generate at one time.
	$maxgen = 1000;


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

	if(isset($_POST['config']))
	{
		$config = fetchDbConfig("config");
		$msg = writeDbConf($_POST['config'],"config", $config, "config");
	}
	$config = fetchDbConfig("config");

	//-----------------------------------------------------------------------------
	function MakeThumbnail($source, $destination, $maxsize)
	{
		//@public
		//generates thumbnail from picture located in $source
		//saves it to $destination folder using th_$source as filename
		//$maxsize is maximum dimension of thumb to make
		//thumbs are proportional to original image
		//returns false on failure and array with details on success

		$size = @GetImageSize($source);
		if (!$size)
			return false;

		$kw = ($size[0]/$maxsize);
		$kh = ($size[1]/$maxsize);
		$k = max ($kw,$kh);
		$w_ = intval($size[0]/$k);
		$h_ = intval($size[1]/$k);

		$im = ImageCreateFromCustom ($size[2],$source);

		if (!$im)
		{
			return false;
		}

		$ne = ImageCreateTrueColor($w_,$h_);

		imagepalettecopy ($ne,$im);
		
		imagealphablending($ne,false);
			
		imagesavealpha($ne,true);
		
		$transparent = imagecolorallocatealpha($ne, 255, 255, 255, 127);
		
		imagefilledrectangle($ne, 0, 0, $w_, $h_, $transparent);
		
		imagecopyresampled ($ne, $im, 0, 0, 0,0, $w_, $h_, $size[0], $size[1]);

		$saveto = $destination."/thumb_".basename($source);
		$orginal = basename($source);

		if (!ImageResult ($size[2],$ne,$saveto,100))
			return false;
		else
			return array ($orginal,basename($saveto),$w_,$h_);
		}

	function ResizeOrginal($source, $destination, $maxsize)
	{
		//@public
		//generates thumbnail from picture located in $source
		//saves it to $destination folder using th_$source as filename
		//$maxsize is maximum dimension of thumb to make
		//thumbs are proportional to original image
		//returns false on failure and array with details on success

		$size = @GetImageSize($source);

		if (!$size)
		{
			return false;
		}

		$kw = ($size[0]/$maxsize);
		$kh = ($size[1]/$maxsize);
		$k = max ($kw,$kh);
		$w_ = intval($size[0]/$k);
		$h_ = intval($size[1]/$k);

		$im = ImageCreateFromCustom ($size[2],$source);

		if (!$im)
		{
			return false;
		}

		$ne = ImageCreateTrueColor($w_,$h_);

		imagepalettecopy ($ne,$im);
		imagecopyresampled ($ne, $im, 0, 0, 0,0, $w_, $h_, $size[0], $size[1]);

		$saveto = $destination."/".basename($source);
		$orginal = basename($source);

		if (!ImageResult ($size[2],$ne,$saveto,100))
			return false;
		else
			return array ($orginal,basename($saveto),$w_,$h_);
		}
	//-----------------------------------------------------------------------------
	function ImageResult($type,$im,$filename,$quality)
	{
		//@private
		$res = null;
		switch ($type)
		{
			case 1:
				$res = ImageGIF($im,$filename);
				break;
			case 2:
				$res = ImageJPEG($im,$filename,$quality);
				break;
			case 3:
				$res = ImagePNG($im,$filename);
				break;
		}
		return $res;
	}
	//-----------------------------------------------------------------------------
	function ImageCreateFromCustom($type,$filename)
	{
		//@private
		$im = null;
		switch ($type)
		{
			case 1:
				$im = ImageCreateFromGif($filename);
				break;
			case 2:
				$im = ImageCreateFromJpeg($filename);
				break;
			case 3:
				$im = ImageCreateFromPNG($filename);
				break;
		}
		return $im;
	}
	//-----------------------------------------------------------------------------
	function checkDbImageExists($filename,$db)
	{
		//@private
		$image = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images WHERE filename=".$db->mySQLSafe($filename));
		if($image==true){
			return true;
		}else{
			return false;
		}
	}
	//-----------------------------------------------------------------------------
	function addImagetoDb($filename,$db)
	{
		//@private
		$record["filename"] = $db->mySQLSafe($filename);
		$record["imgLoc"] = $db->mySQLSafe("/images/uploads/".$filename);
		$record["thumbLoc"] = $db->mySQLSafe("/images/uploads/thumbs/thumb_".$filename);
		$record["imgName"] = $db->mySQLSafe($filename);
		$insert = $db->insert($glob['dbprefix']."CubeCart_images", $record);
		unset($record);
		if($insert==true){
			return true;
		}else{
			return false;
		}
	}
	//-----------------------------------------------------------------------------
	function assignImagetoCat($filename,$db,$catId)
	{
		//@private
		$image = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images WHERE filename=".$db->mySQLSafe($filename));
		if($image){
			$record["imgId"] = $db->mySQLSafe($image[0]['imgId']);
			$record["catId"] = $db->mySQLSafe($catId);
			$insert = $db->insert($glob['dbprefix']."CubeCart_imgcat_idx", $record);
		}
		
		if($insert==true){
			return true;
		}else{
			return false;
		}
	}
	//-----------------------------------------------------------------------------
	include("../includes/header.inc.php");


	//This is a lame way to check for gd but it works!
	if(function_exists('imagecreate') == false)
	{
		die('GD 2 is required but was not detected!<br /><br /> Possible Reasons :<ul><li>Simply GD 2 was not installed (Normally this is already loaded with php unless you have an older version)</li><li>Installed but NOT enabled.. check php.ini settings</li><li>Your hosting provider may have disabled use of certain GD2 functions!');
	}

	//Get the dirs if any and store them in this var
	$listdirs = findImages($glob['rootDir'].'/images/uploads');

	function findImages( $path, $level = 0 )
	{
		$returns ='';

    		$ignore = array( 'cgi-bin', '.', '..','.svn','thumbs' );
    		// Directories to ignore when listing output. Many hosts

    		$dh = @opendir( $path );
    		// Open the directory to the handle $dh


    		while( false !== ( $file = readdir( $dh ) ) )
    		{
    			global $returns;

    			// Loop through the directory

        		if( !in_array( $file, $ignore ) )
        		{
        			// Check that this file is not to be ignored

            		if( is_dir($path.'/'.$file) )
            		{
            			//check if thumbs directory exits
            			if (is_dir($path.'/'.$file.'/thumbs')==false)
            			{
            				//create the dir
            				$makedir = mkdir($path.'/'.$file.'/thumbs',0777);

            				if ($makedir==false)
            				{
            					echo '<p>Error could not create thumbs dir <br />'.$path.'/'.$file.'/thumbs</p>';
            				}
            				else
            				{
            					echo '<p>Thumbs folder created <br />'.$path.'/'.$file.'/thumbs</p>';
            				}

            			}
            			// Its a directory, so we need to keep reading down...
                		findImages( "$path/$file", ($level+1) );
                		// Re-call this same function but on a new directory.
                		// this is what makes function recursive.

            		}
            		elseif(strlen($file) > 4 && substr($file, strlen($file) - 4) === '.jpg' && !is_dir($file) OR strlen($file) > 4 && substr($file, strlen($file) - 4) === '.gif' && !is_dir($file) OR strlen($file) > 4 && substr($file, strlen($file) - 4) === '.png' && !is_dir($file))
            		{
					$returns[]=$path."/".$file;
            		}

        		}

    		}
    		closedir( $dh );
    		// Close the directory handle
    		return $returns;

	}//end function

	echo "Thumbnail Fix ";
	echo "Menu : <a href='?do=del'>Delete All Thumbs</a> | <a href='?do=gen'>Regenerate Thumbs</a> | <a href='?do=stats'>View Stats</a> <hr noshadow size=1>";

	if(isset($_GET['do']) && $_GET['do'] =='gen')
	{
		//clear the counts
		$tcount=0;
		$tnew=0;

		if (is_array($listdirs))
		{
			while(list($imgkey,$imgvalue) = each($listdirs))
			{
				$file = substr($imgvalue,(strrpos($imgvalue,'/')+1),((strlen($imgvalue))-strrpos($imgvalue,'/')));
				$path = substr($imgvalue,0,strrpos($imgvalue,'/'));
				$image_url = $glob['storeURL'].(str_replace($glob['rootDir'],'',$path));

				if(strlen($file) > 4 && substr($file, strlen($file) - 4) === '.jpg' && !is_dir($file) OR strlen($file) > 4 && substr($file, strlen($file) - 4) === '.gif' && !is_dir($file) OR strlen($file) > 4 && substr($file, strlen($file) - 4) === '.png' && !is_dir($file))
				{
 					if (!is_file($path."/thumbs/thumb_".$file))
 					{
						//check and add image to db
						if(checkDbImageExists($file,$db)==false){
							addImagetoDb($file,$db);
						}
						//uncomment to assign image to a category
						//assignImagetoCat($file,$db,1);
 						$makefile ='';
 						$makefile = MakeThumbnail($path."/".$file, $path."/thumbs/", $config['gdthumbSize']);
 						echo "<p><a href='".$image_url."/".$file."' target='".$file."'><br /><img src='".$image_url."/thumbs/thumb_".$file."' border=0></a> $file was updated!</p>";
						$tnew++;
 					}//endif
				}//endif

				$tcount++;

				if ($tnew >= $maxgen)
				{
					break;
				}//endif
			}//end while
		}//endif

		if ($tnew >0 AND $maxgen > $tnew)
		{
			echo "<p>".$tnew." thumbs were generated <br>Your thumbs images are now currently up to date!</p>";

		}
		elseif ($tnew >0 AND $maxgen <= $tnew)
		{
			echo "<p>".$tnew." new thumbs images was generated <br />The script has reached it's configurable max limit on image regeneration. Please <a href='?do=gen'>click here to continue</a> this process.<br /> </p>";
		}
		else
		{
			echo "<p>Your thumbs images are currently up to date! No Changes were made.</p>";
		}
	}
	elseif(isset($_GET['do']) && $_GET['do'] =='del')
	{
		if (is_array($listdirs))
		{
			//count thumbs deleted!
			$thumbs_deleted =0;

			while(list($imgkey,$imgvalue) = each($listdirs))
			{
				$file = substr($imgvalue,(strrpos($imgvalue,'/')+1),((strlen($imgvalue))-strrpos($imgvalue,'/')));
				$path = substr($imgvalue,0,strrpos($imgvalue,'/'));
				$image_url = $glob['storeURL'].(str_replace($glob['rootDir'],'',$path));

				if(strlen($file) > 4 && substr($file, strlen($file) - 4) === '.jpg' && !is_dir($file) OR strlen($file) > 4 && substr($file, strlen($file) - 4) === '.gif' && !is_dir($file) OR strlen($file) > 4 && substr($file, strlen($file) - 4) === '.png' && !is_dir($file))
				{
 					if (is_file($path."/thumbs/thumb_".$file))
 					{
						echo "Delete this ".$path."/thumbs/thumb_".$file."<br />";
						$deleteit = unlink($path."/thumbs/thumb_".$file);

						if ($deleteit == true)
						{
							$thumbs_deleted++;
						}
						else
						{
							echo "<hr>Failed to delete thumbs from this folder <br /><b>".$path."/thumbs/</b> <br /><br />Please make sure your folder and file permissions have rights to delete these images.. (hint chomd 0777)<br/>";
							break;
						}
 					}//endif
				}//endif

			}//end while

			echo "<hr>Thumbs was deleted: <b>$thumbs_deleted </b><br /> To regenerate the thumbs <a href='?do=gen'>click here</a><br /><br /><br />";
		}//endif
	}
	elseif (isset($_GET['do']) && $_GET['do'] =='stats')
	{
		$count_missing_thumbs =0;

		$count_images_found= 0;

		if (is_array($listdirs))
		{
			while(list($imgkey,$imgvalue) = each($listdirs))
			{
				$file = substr($imgvalue,(strrpos($imgvalue,'/')+1),((strlen($imgvalue))-strrpos($imgvalue,'/')));
				$path = substr($imgvalue,0,strrpos($imgvalue,'/'));
				$image_url = $glob['storeURL'].(str_replace($glob['rootDir'],'',$path));

				if(strlen($file) > 4 && substr($file, strlen($file) - 4) === '.jpg' && !is_dir($file) OR strlen($file) > 4 && substr($file, strlen($file) - 4) === '.gif' && !is_dir($file) OR strlen($file) > 4 && substr($file, strlen($file) - 4) === '.png' && !is_dir($file))
				{
 					if (!is_file($path."/thumbs/thumb_".$file))
 					{
						$count_missing_thumbs++;
						$tnew++;
 					}//endif
 					else
 					{
 						$count_images_found++;
 					}
				}//endif

				$tcount++;

				if ($tnew >= $maxgen)
				{
					break;
				}//endif
			}//end while
		}//endif

		echo "<p>$count_missing_thumbs Missing Thumbs<br />$count_images_found Thumbs Counted <br />";

	}
	else
	{
		echo "Ready....";
	}



	include("../includes/footer.inc.php");
?>