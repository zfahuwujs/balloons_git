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
|	siteDocs.inc.php
|   ========================================
|	Build Links to Site Docs	
+--------------------------------------------------------------------------
*/

phpExtension();

// query database
$results = $db->select("SELECT doc_id, doc_name FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_pos > 0 ORDER BY doc_pos , doc_id ASC");$resultsForeign = $db->select("SELECT doc_master_id as doc_id, doc_name FROM ".$glob['dbprefix']."CubeCart_docs_lang WHERE doc_lang = '".$lang_folder."'"); 

$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/siteDocsLeft.tpl");


$box_content->assign("LANG_CATEGORY_TITLE",$lang['front']['boxes']['site_docs_left']);

// build attributes
if($results == TRUE){

	// start loop
	for ($i=0; $i<count($results); $i++){
 	
		if($i<count($results)-1){
			$box_content->parse("site_docs.li.sep");
		}
		
		
		if(is_array($resultsForeign)){
			
			for ($k=0; $k<count($resultsForeign); $k++){
		
				if($resultsForeign[$k]['doc_id'] == $results[$i]['doc_id']){
				
					$results[$i]['doc_name'] = $resultsForeign[$k]['doc_name'];
				
				}
				
			}
		
		}
		
		$results[$i]['doc_name'] = validHTML($results[$i]['doc_name']);
		$box_content->assign("DATA",$results[$i]);
		$box_content->parse("site_docs.li");
	
	} // end loop 
}
$box_content->parse("site_docs");
$box_content = $box_content->text("site_docs");
?>