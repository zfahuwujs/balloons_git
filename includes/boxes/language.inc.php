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

|	language.inc.php

|   ========================================

|	Language Jump Box	

+--------------------------------------------------------------------------

*/



phpExtension();

$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/language.tpl");
$box_content->assign("LANG_LANGUAGE_TITLE",$lang['front']['boxes']['language']);
if(evoHideBol(102)==false){
	$box_content->parse("language.google");
}else{
	$path = "language";
		if ($dir = opendir($path)) {
			$returnPage = "";
			$returnPage = urlencode(currentPage());
			while (false !== ($folder = readdir($dir))) {
				if(!eregi("[.]", $folder)){
				include($path."/".$folder."/config.inc.php");
					if($lang_folder==$folder){
						$box_content->assign("LANG_SELECTED","selected='selected'");
					} else {
						$box_content->assign("LANG_SELECTED","");
					}
					$box_content->assign("LANG_NAME",$langName);
					$box_content->assign("LANG_VAL",$folder);
					$box_content->assign("VAL_CURRENT_PAGE",$returnPage);
					$box_content->parse("language.cubie.option");
				}
			} 
		}
	// $lang_folder is defined in /index.php
	$box_content->assign("ICON_FLAG",$lang_folder."/flag.gif");
	$box_content->parse("language.cubie");
}

$box_content->parse("language");

$box_content = $box_content->text("language");

?>