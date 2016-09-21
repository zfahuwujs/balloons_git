<?php



$box_content=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/subCatWithImages.tpl");
$categiries = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_category WHERE cat_father_id = '.$db->mySQLSafe($_GET['catId']).' order by disp_order asc LIMIT 3000');
if($categiries){
	$i = 1;
	foreach($categiries as $cat){
				
		if($i %1 == 0) { $a = 1;}
		if($i %2 == 0) { $a = 2;}
		if($i %3 == 0) { $a = 3;}
		if($i %4 == 0) { $a = 4;}
		if($i %5 == 0) { $a =5;}
				
		if ($i == 5){ $i = 0;} 
		
			if ($cat["cat_image"] == ""){
		$box_content->assign("CAT_IMG_PATH", '/skins/FixedSize/styleImages/thumb_nophoto.gif');
	}else{
		$box_content->assign("CAT_IMG_PATH", 'images/uploads/thumbs/thumb_'.$cat['cat_image']);
	}
		
		$box_content->assign('CAT_LINK', '/index.php?act=viewCat&amp;catId='.$cat['cat_id']);
		$box_content->assign('CAT_NAME', $cat['cat_name']);
		$box_content->assign('CAT_NUMBER', $a);
		$box_content->parse("catWithImages.cat");	
		$i++;
	}
		
	$box_content->parse("catWithImages");	
	$box_content = $box_content->text("catWithImages");
}