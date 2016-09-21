<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.15
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2005 - 2006. All rights reserved.
|   Devellion Limited,
|   22 Thomas Heskin Court,
|   Station Road,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 3EE
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Thursday, 4th January 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	preview.php
|   ========================================
|	Preview Image
+--------------------------------------------------------------------------
*/
include("../../includes/ini.inc.php");
include("../../includes/global.inc.php");
require_once("../../classes/db.inc.php");
$db = new db();
include_once("../../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../../language/".$config['defaultLang']."/lang.inc.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $lang['admin']['filemanager']['prev_file'];?></title>
<link href="../styles/style.css" rel="stylesheet" type="text/css" />
</head>

<body class="greyBg">
<?php if(isset($_GET['file'])){ ?>
<div class="imgPreview" align="center" style="width:<?php echo treatGet($_GET['x']);?>px; height:<?php echo treatGet($_GET['y']);?>px;"><a href="javascript:window.close();"><img src="<?php echo treatGet($_GET['file']); ?>" alt="<?php echo $lang['admin']['filemanager']['close_window'];?>" title="<?php echo $lang['admin']['filemanager']['close_window'];?>" border="0" /></a></div>
<?php } else { ?>
<span class="copyText"><?php echo $lang['admin']['filemanager']['no_image_selected'];?></span>
<?php } ?>
</body>
</html>
