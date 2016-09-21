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
|	401.php
|   ========================================
|	Admin Access Denied Page	
+--------------------------------------------------------------------------
*/
include("../includes/ini.inc.php");
include("../includes/global.inc.php");
include_once("../classes/db.inc.php");
$db = new db();
include_once("../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include_once("../includes/sslSwitch.inc.php");
include("includes/auth.inc.php");
include("includes/header.inc.php"); ?>
<p class="warnText"><?php echo $lang['admin']['other']['401']; ?></p>
<p align="center"><img src="images/largeWarning.gif" alt="" width="220" height="192" title="" /></p>
<?php include("includes/footer.inc.php"); ?>