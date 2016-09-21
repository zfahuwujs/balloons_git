<?php
$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/footer.tpl");
$footDocsCats = $db->select('SELECT * FROM CubeCart_docCats WHERE inFooter = 1 ORDER BY menuOrder ASC');
if($footDocsCats==true){
	$box_content->assign('COLWIDTH', round(100/count($footDocsCats)).'%;');
	for($i = 0; $i < count($footDocsCats); $i++){
		$box_content->assign('CATNAME',$footDocsCats[$i]['catName']);
		if($i > 0){
			$box_content->assign('COLUMNCALSS','footColumn');
		}else{
			$box_content->assign('COLUMNCALSS','firstfootColumn');
		}
		$docs = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_docs WHERE catId = '.$db->mySQLSafe($footDocsCats[$i]['catId']).' AND doc_pos > 0 ORDER BY doc_pos');
		if($docs==true){
			for($z = 0; $z < count($docs); $z++){
				$box_content->assign('FOOTITEM','<a style="font-size:11px" href="/index.php?act=viewDoc&amp;docId='.$docs[$z]['doc_id'].'">'.$docs[$z]['doc_name'].'</a>');
				$box_content->parse('footer.column.item');
			}
		}
		$box_content->parse('footer.column');
	}
}
$box_content->parse('footer');
$box_content = $box_content->text("footer");
?>