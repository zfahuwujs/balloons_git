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

|	currency.inc.php

|   ========================================

|	Currency Jump Box	

+--------------------------------------------------------------------------

*/

phpExtension();



$currencies = $db->select("SELECT name, code FROM ".$glob['dbprefix']."CubeCart_currencies WHERE active = 1 ORDER BY name ASC");



if($currencies == TRUE) {



$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/currency.tpl");



$box_content->assign("LANG_CURRENCY_TITLE",$lang['front']['boxes']['currency']);

			

			

			for($i=0; $i<count($currencies); $i++){

				

				if($ccUserData[0]['currency']==$currencies[$i]['code']){

					$box_content->assign("CURRENCY_SELECTED","selected='selected'");

				} elseif(($config['defaultCurrency']==$currencies[$i]['code']) && empty($ccUserData[0]['currency'])) {

					$box_content->assign("CURRENCY_SELECTED","selected='selected'");

				} else {

					$box_content->assign("CURRENCY_SELECTED","");

				}

				

				

				

				if(strlen($currencies[$i]['name'])>20) {

					$currencyName = substr($currencies[$i]['name'],0,18)."&hellip;";

				} else {

					$currencyName = $currencies[$i]['name'];

				}

				

					$box_content->assign("VAL_CURRENCY",$currencies[$i]['code']);

					$box_content->assign("CURRENCY_NAME",$currencyName);

					$box_content->assign("VAL_CURRENT_PAGE",$returnPage);

					$box_content->parse("currency.option");

				

				}



		

		

$box_content->parse("currency");

$box_content = $box_content->text("currency");



} else {



	$box_content = "";



}

?>