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
$files = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images AS img WHERE imgCatId=".$db->mySQLSafe($_GET['cat'])." ORDER BY imgName");
$files2 = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images AS img INNER JOIN ".$glob['dbprefix']."CubeCart_imgcat_idx AS idx ON img.imgId=idx.imgId WHERE idx.catId =".$db->mySQLSafe($_GET['cat']). " OR img.imgCatId = '0' GROUP BY img.imgId ORDER BY imgName");


$files = array_merge($files, $files2);

/*echo "<pre>";
var_dump(($files));
die;*/

if($files==true){
	$html_img_lst = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
	for ($i = 0; $i < count($files); $i++) {
		//var_dump($files[$i]["filename"]);
		$html_img_lst .= "<tr><td><a href=\"javascript:getImage('thumb_".$files[$i]['filename']."');\">thumb_".$files[$i]['filename']."</a></td><td align='right'>".format_size($files[$i]['size'])."</td></tr>\n";
	}
	$html_img_lst .= "</table>"; 
} else { // end if is array
	$empty = 1;
}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
<head>
    <title><?php echo $lang['admin']['filemanager']['image_browser'];?></title>
    <link rel="stylesheet" type="text/css" href="../includes/rte/editor/css/fck_dialog.css">
    <script language="javascript">
    var sImagesPath  = "<?php echo $GLOBALS['rootRel']."images/uploads/thumbs/"; ?>";
    var sActiveImage = "" ;
    var fileName = "" ;

    <? if(isset($_GET['targetfield'])){ ?>
        var targetfield = "<?PHP echo $_GET['targetfield']; ?>";
        var targetdiv = "<?PHP echo $_GET['targetdiv']; ?>";
        var imagecontrols = "<?PHP echo $_GET['imagecontrols']; ?>";
    <?php }else{ ?>
		var targetfield = "imageName";
        var targetdiv = "selectedImage";
        var imagecontrols = "imageControls";
	<?php } ?>
	

    function getImage(imageName){
        if(imageName){
            sActiveImage = sImagesPath + imageName ;
            fileName = imageName;
        } else {
            sActiveImage = sImagesPath + 'noPreview.gif' ;
        }
        imgPreview.src = sActiveImage ;
    }

    <?php if($_GET['custom']==1){ ?>
    function ok(){	
		if(targetfield=='imageName2'){
			window.opener.addImage2(fileName, sActiveImage, targetfield, targetdiv, imagecontrols);
			window.close();
		}else{
			window.opener.addImage(fileName, sActiveImage, targetfield, targetdiv, imagecontrols);
			window.close();
		}
	}
    <?php } else { ?>
    function ok(){	
        window.setImage(sActiveImage);
        window.close() ;
    }
    <?php } ?>
    </script>
</head>
<body bottommargin="5" leftmargin="5" topmargin="5" rightmargin="5">
    <table cellspacing="1" cellpadding="1" border="0" class="dlg" height="100%">
        <tr height="100%">
            <td>
                <table cellspacing="0" cellpadding="0" border="0" height="100%">
                    <tr>
                        <td valign="top">
                            <table cellpadding="0" cellspacing="0" height="100%" width="220">
                                <tr>
                                    <td><?php echo $lang['admin']['filemanager']['file']; ?></td>
                                </tr>
                                <tr height="100%">
                                    <td>
                                        <div class="ImagePreviewArea">
                                        <?php 
                                        if($empty==1){ 
                                            echo $lang['admin']['filemanager']['no_imgs']; 
                                        } else { 
                                            echo $html_img_lst; 
                                        } 
                                        ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                      </td>
                        <td width="5">&nbsp;</td>
                        <td>
                            <table cellpadding="0" cellspacing="0" height="100%" width="220">
                                <tr>
                                    <td><?php echo $lang['admin']['filemanager']['preview']; ?></td>
                                </tr>
                                <tr>
                                    <td height="100%" align="center" valign="middle">
                                        <?php if($empty==1){ ?>&nbsp;<?php } else { ?><div class="ImagePreviewArea"><IMG src="<?php echo $glob['rootRel'];?>images/general/px.gif" border="0" id="imgPreview" title="" alt="" /></div><?php } ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center">
                <form name="changCat" action="/admin/filemanager/browse.php" method="get">
                <input type="hidden" name="custom" value="1" />
                <?php
                if(isset($_GET['targetfield'])){
                    echo'
						<input type="hidden" name="targetfield" value="'.$_GET['targetfield'].'" />
						<input type="hidden" name="targetdiv" value="'.$_GET['targetdiv'].'" />
						<input type="hidden" name="imagecontrols" value="'.$_GET['imagecontrols'].'" />
					';
                }
                ?>
                <select name="cat">
                                        <?php
                    $imgCats=$db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_images_cat");
                    if($imgCats){
                        foreach($imgCats as $cat){
                            echo '<option value="'.$cat['catId'].'">'.$cat['catName'].'</option>';
                        }
                    }
                ?>
                </select>
                <input name="update" type="submit" value="Change Category" />
                </form>
            </td>
        </tr>
        <tr>
            <td align="center">
                <input style="width: 80px" type="button" value="<?php echo $lang['admin']['filemanager']['ok']; ?>" onClick="ok();" />  
                <input style="width: 80px" type="button" value="<?php echo $lang['admin']['filemanager']['cancel']; ?>" onClick="window.close();" />
            </td>
        </tr>
    </table>
</body>
</html>

