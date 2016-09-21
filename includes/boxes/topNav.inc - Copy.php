<?php

phpExtension();

$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/topNav.tpl");
	
$results = $db->select("
SELECT catId AS id, catName AS name, NULL AS doc, menuOrder as menuOrder, NULL AS cat FROM CubeCart_docCats WHERE menuShow = 1 
UNION ALL SELECT doc_id AS id, doc_name AS name, doc_id AS doc, menuOrder, NULL AS cat FROM CubeCart_docs WHERE menuShow = 1 
UNION ALL SELECT cat_id AS id, cat_name AS name, NULL AS doc, menuOrder, cat_id AS cat FROM CubeCart_category WHERE menuShow = 1 
ORDER BY menuOrder ASC");

$currentSub=0;
$current=0;
if($results == TRUE){
	for ($i=0; $i<count($results); $i++){
 		if(isset($results[$i]['doc'])){
			if($results[$i]['id']==7){
				$box_content->assign("NAVIGATION_ITEM", "<a href='/'>".$results[$i]['name']."</a>");
			}else{
				$box_content->assign("NAVIGATION_ITEM", "<a href='/index.php?act=viewDoc&amp;docId=".$results[$i]['id']."'>".$results[$i]['name']."</a>");
			}
			
			if((isset($_GET['docId']) && $_GET['docId']==$results[$i]['id']) || (!isset($_GET['act']) && $results[$i]['id']==7) && $current!=1){
				$box_content->assign("CURRENT", "topNavCurrent");
				$current=1;
			}else{
				$box_content->assign("CURRENT", "");
			}
			
		}elseif(isset($results[$i]['cat'])){
			$box_content->assign("NAVIGATION_ITEM", "<a href='/index.php?act=viewCat&amp;catId=".$results[$i]['id']."'>".$results[$i]['name']."</a>");
			if(evoHideBol(100)==false){
				$subs = $db->select("SELECT * FROM CubeCart_category WHERE cat_father_id = ".$results[$i]['id']." ORDER BY menuOrder ASC");

				if($subs == TRUE){
					for ($s=0; $s<count($subs); $s++){
						$box_content->assign("SUB_NAVIGATION_ITEM", "<a href='/index.php?act=viewCat&amp;catId=".$subs[$s]['cat_id']."'>".$subs[$s]['cat_name']."</a>");
						$box_content->parse("top_nav.top_nav_item.drop_down.sub_top_nav_item");
						if(isset($_GET['catId']) && $_GET['catId']==$subs[$s]['cat_id']){
							$currentSub=1;
						}
					}
					$box_content->parse("top_nav.top_nav_item.drop_down");
				}
			}
			if((isset($_GET['catId']) && $_GET['catId']==$results[$i]['id'] && $current!=1) || ($currentSub==1 && $current!=1)){
				$box_content->assign("CURRENT", "topNavCurrent");
				$current=1;
				$currentSub=0;
			}else{
				$box_content->assign("CURRENT", "");
			}
		}else{
			//comment out the line below to remove default links for doc categories
			$defaultLink = $db->select("SELECT * FROM CubeCart_docs WHERE catId = ".$results[$i]['id']." ORDER BY menuOrder ASC LIMIT 1");
			if($defaultLink==true){
				$box_content->assign("NAVIGATION_ITEM", "<a href='/index.php?act=viewDoc&amp;docId=".$defaultLink[0]['doc_id']."'>".$results[$i]['name']."</a>");
			}else{
				$box_content->assign("NAVIGATION_ITEM", "<a href='/'>".$results[$i]['name']."</a>");
			}
			if(evoHideBol(100)==false){
				$subs = $db->select("SELECT * FROM CubeCart_docs WHERE catId = ".$results[$i]['id']." ORDER BY menuOrder ASC");
				if($subs == TRUE){
					for ($s=0; $s<count($subs); $s++){
						$box_content->assign("SUB_NAVIGATION_ITEM", "<a href='/index.php?act=viewDoc&amp;docId=".$subs[$s]['doc_id']."'>".$subs[$s]['doc_name']."</a>");
						$box_content->parse("top_nav.top_nav_item.drop_down.sub_top_nav_item");
						if(isset($_GET['docId']) && $_GET['docId']==$subs[$s]['doc_id']){
							$currentSub=1;
						}
					}
					$box_content->parse("top_nav.top_nav_item.drop_down");
				}
			}
			if($currentSub==1 && $current!=1){
				$box_content->assign("CURRENT", "topNavCurrent");
				$current=1;
				$currentSub=0;
			}else{
				$box_content->assign("CURRENT", "");
			}
		}
		
		if ($i == 0) {
		$box_content->assign("FIRSTNAV", "firstnav");
		} else {
		$box_content->assign("FIRSTNAV", "");	
		}
		
		$box_content->parse("top_nav.top_nav_item");
	}
	
	
	if($ccUserData[0]['customer_id']>0){
			$box_content->assign("LANG_WELCOME_BACK",$lang['front']['boxes']['welcome_back']);
			$box_content->assign("TXT_USERNAME",$ccUserData[0]['firstName']." ".$ccUserData[0]['lastName']);
			$box_content->assign("LANG_LOGOUT",$lang['front']['boxes']['logout']);
			$box_content->assign("LANG_YOUR_ACCOUNT",$lang['front']['boxes']['your_account']);
			$box_content->parse("top_nav.session_true");
	
	} else {
	
			$box_content->assign("LANG_WELCOME_GUEST",$lang['front']['boxes']['welcome_guest']);
			$box_content->assign("VAL_SELF",base64_encode(currentPage()));
			$box_content->assign("LANG_LOGIN",$lang['front']['boxes']['login']);
			$box_content->assign("LANG_REGISTER",$lang['front']['boxes']['register']);
			$box_content->parse("top_nav.session_false");
	
	}

	$box_content->parse("top_nav");
	$box_content = $box_content->text("top_nav");
}else{
	$box_content->parse("no_top_nav");
	$box_content = $box_content->text("no_top_nav");
}

?>