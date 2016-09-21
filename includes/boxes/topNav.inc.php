<?php
$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/topNav.tpl");

if(isset($_GET['searchStr'])){
	$box_content->assign("SEARCHSTR",treatGet($_GET['searchStr']));
} else {
	$box_content->assign("SEARCHSTR","Search site");
}

if(preg_match('/ipad/i',$_SERVER['HTTP_USER_AGENT']) || preg_match('/iphone/i',$_SERVER['HTTP_USER_AGENT'])){
	$box_content->assign("CSSCLASSADJUST",'isearchBox');
}else{
	$box_content->assign("CSSCLASSADJUST",'searchBox');
}

$results = $db->select("
SELECT catId, catName AS name, NULL AS doc, menuOrder as menuOrder, NULL AS cat , NULL as disp_order FROM CubeCart_docCats WHERE menuShow = 1 
UNION ALL SELECT doc_id AS catId, doc_name AS name, doc_id AS doc, menuOrder, NULL AS cat , NULL as disp_order FROM CubeCart_docs WHERE menuShow = 1 
UNION ALL SELECT cat_id AS catId, cat_name AS name, NULL AS doc, menuOrder, cat_id AS cat, disp_order as disp_order FROM CubeCart_category WHERE menuShow = 1 
ORDER BY menuOrder asc;");
$currentSub=0;
$current=0;




/*echo "<pre>";
var_dump($results);
echo "</pre>";*/


if($results == TRUE){
	for ($i=0; $i<count($results); $i++){
		$additionalClass = null;
 		if(isset($results[$i]['doc'])){
			if($results[$i]['catId']==7){
				//$additionalClass = 'homeButton ';
				$box_content->assign("NAVIGATION_ITEM", "<a href='/' class=\"homeButton\">Home</a>");
			}else{
				
				$box_content->assign("NAVIGATION_ITEM", "<a class='navLink' href='/index.php?act=viewDoc&amp;docId=".$results[$i]['catId']."'>".$results[$i]['name']."</a>");
			}
			
			if((isset($_GET['docId']) && $_GET['docId']==$results[$i]['id']) || (!isset($_GET['act']) && $results[$i]['isHome']==1) && $current!=1){
				$box_content->assign("CURRENT", $additionalClass."topNavCurrent");
				$current=1;
			}elseif(!isset($_GET['act']) && $results[$i]['id']==7){
				$box_content->assign("CURRENT", $additionalClass."topNavCurrent");
				$current=1;
			}else{
				$box_content->assign("CURRENT", $additionalClass."");
			}
			
		}elseif(isset($results[$i]['catId'])){
			
				// ISSUE CAUSER : Was pulling though docCats all the time, customer wanted prodcats, this line does not know the difference between doccats and prodcats.
				$subs = $db->select("SELECT * FROM CubeCart_category WHERE cat_father_id = ".$results[$i]['catId']." ORDER BY disp_order ASC");

	

				if($subs == TRUE){
					$box_content->assign("NAVIGATION_ITEM", "<a href='/index.php?act=viewCat&amp;catId=".$results[$i]['catId']."' class=\"dropMenu\" id=\"megaanchor_".$results[$i]['catId']."\">".$results[$i]['name']."</a>");
					
					$menuJs .= 'jkmegamenu.definemenu("megaanchor_'.$results[$i]['catId'].'", "megamenu_'.$results[$i]['catId'].'", "mouseover");'."\r\n";
					
					$menu = '<div id="megamenu_'.$results[$i]['catId'].'" class="megamenu">';
					//$menu .= '<h3>I\'m looking for:</h3>';
					$perColumn = 10;
					$prineted = 0;
						$menu .= '<div class="column"><ul>';
						for ($s=0; $s<count($subs); $s++){
							$menu .= '<li><a href="/index.php?act=viewCat&amp;catId='.$subs[$s]['cat_id'].'">'.$subs[$s]['cat_name'].'</a></li>';
							$prineted++;
							if(isset($subs[($s+1)]['doc_id']) && $prineted > ($perColumn-1)){
								$prineted = 0;
								$menu .= '</ul></div>';
								$menu .= '<div class="column"><ul>';
							}
						}
						if($results[$i]['catId']==104){
							$menu .= '<li><a href="/index.php?act=gallery">Gallery</a></li>';
						}
						$menu .= '</ul></div>';
					$menu .= '</div>';
					$box_content->assign("MEGA",$menu);
				}else{
					$box_content->assign("NAVIGATION_ITEM", "<a href='/index.php?act=viewCat&amp;catId=".$results[$i]['catId']."'>".$results[$i]['name']."</a>");
					$box_content->assign("MEGA",null);
				}
			//var_dump(topCat($_GET['catId']));
			if((isset($_GET['catId']) && topCat($_GET['catId'])==$results[$i]['id'] && $current!=1) || ($currentSub==1 && $current!=1)){
				
				$box_content->assign("CURRENT", "topNavCurrent");
				$current=1;
				$currentSub=0;
			}else{
				$box_content->assign("CURRENT", "");
			}
		}else{
			//comment out the line below to remove default links for doc categories
			$defaultLink = $db->select("SELECT * FROM CubeCart_docs WHERE catId = ".$results[$i]['catId']." ORDER BY menuOrder ASC LIMIT 1");
			if($defaultLink==true){
				$box_content->assign("NAVIGATION_ITEM", "<a href='/index.php?act=viewDoc&amp;docId=".$defaultLink[0]['doc_id']."'>".$results[$i]['name']."</a>");
			}else{
				$box_content->assign("NAVIGATION_ITEM", "<a href='/'>".$results[$i]['name']."</a>");
			}

				$subs = $db->select("SELECT * FROM CubeCart_docs WHERE catId = ".$results[$i]['catId']." ORDER BY menuOrder ASC");
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

			if($currentSub==1 && $current!=1){
				$box_content->assign("CURRENT", "topNavCurrent");
				$current=1;
				$currentSub=0;
			}else{
				$box_content->assign("CURRENT", "");
			}
		}
		$box_content->parse("top_nav.top_nav_item");
	}

	$box_content->parse("top_nav");
	$box_content = $box_content->text("top_nav");
}else{
	$box_content->parse("no_top_nav");
	$box_content = $box_content->text("no_top_nav");
}

?>