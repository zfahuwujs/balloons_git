<?php
phpExtension();

$_GET['docId'] = treatGet($_GET['docId']);
if(isset($_GET['page'])){
	$page = treatGet($_GET['page']);
} else {
	$page = 0;
}

if($lang_folder !== $config['defaultLang']){

$result = $db->select("SELECT doc_master_id AS doc_id, doc_name, doc_content FROM ".$glob['dbprefix']."CubeCart_news_lang WHERE doc_master_id = ".$db->mySQLSafe($_GET['docId'])." AND doc_lang=".$db->mySQLSafe($lang_folder));

	/* <rf> search engine friendly mod */
	if($config['seftags']) {
		// get metas for the docs
		$sefresult = $db->select("SELECT doc_metatitle, doc_metadesc, doc_metakeywords FROM ".$glob['dbprefix']."CubeCart_news WHERE doc_id = ".$db->mySQLSafe($_GET['docId'])); 
		$result['sefSiteTitle'] = $sefresult['sefSiteTitle'];
		$result['sefSiteDesc'] = $sefresult['sefSiteDesc'];
		$result['sefSiteKeywords'] = $sefresult['sefSiteKeywords'];
	} 
	/* <rf> end mod */
}

if(!isset($result) || $result==FALSE) {
/* <rf> search engine friendly mod */
if($_GET['docId']=='all') {
	$newsList = "SELECT * FROM ".$glob['dbprefix']."CubeCart_news ORDER BY date DESC"; 
	$result = $db->select($newsList, $config['productPages'], $page);
	$totalNoProducts = $db->numrows($newsList);
} else {
	$result = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_news WHERE doc_id = ".$db->mySQLSafe($_GET['docId']));
}

/* <rf> end mod */
}

$view_news=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/viewNews.tpl");

if(isset($result) && $result == TRUE && $_GET['docId']=='all'){
		$view_news->assign("PAGINATION",$db->paginate($totalNoProducts, $config['productPages'], $page, "page"));
	for($i=0; $i<count($result); $i++){
		if($i==0){
			$view_news->assign("DOC_NAME",validHTML($result[$i]['doc_name']));
			$view_news->assign("DOC_CONTENT",$result[$i]['doc_content']);
			$view_news->assign("NEWS_ID",$result[$i]['doc_id']);
			$view_news->assign("ADDED",date("j F Y, H:i", $result[$i]['date']));
			if(isset($result[0]['image'])){
				$view_news->assign("IMAGE",$result[0]['image']);
				$view_news->parse("view_news.all.all_one.image");
			}
			$view_news->parse("view_news.all.all_one");
		}
		$view_news->assign("DOC_NAME",validHTML($result[$i]['doc_name']));
		$view_news->assign("DOC_CONTENT",$result[$i]['doc_content']);
		$view_news->assign("NEWS_ID",$result[$i]['doc_id']);
		$view_news->assign("ADDED",date("j F Y, H:i", $result[$i]['date']));
		$view_news->parse("view_news.all.item");
	}	
	$view_news->parse("view_news.all");
	
}elseif(isset($result) && $result == TRUE){
	
	
	$view_news->assign("DOC_NAME",validHTML($result[0]['doc_name']));
	$view_news->assign("DOC_CONTENT",$result[0]['doc_content']);
	$view_news->assign("ADDED",date("j F Y, H:i", $result[0]['date']));
	if(isset($result[0]['image'])){
		$view_news->assign("IMAGE",$result[0]['image']);
		$view_news->parse("view_news.one.image");
	}

	/* <rf> search engine friendly mod */
	if($config['seftags']) {
			$robz = trim(strip_tags(str_replace("\r\n", "", $result[0]['doc_content'])));
			$count = 0;
			$output = "";
			
			for($i=0; $i<strlen($robz); $i++){
				$temp = substr($robz, $i, 1);
				
				if($temp == " "){
					if($count == 0){
						$output .= $temp;
						$count++;
					}
				}else{
					$output .= $temp;
					$count = 0;
				}
			}
			
		$meta['metaDescription'] = substr($output, 0, 156);
		$meta['siteTitle'] = $result[0]['doc_name'];		
		//$meta['metaDescription'] = substr(strip_tags($result[0]['doc_content']),0,155);
		$meta['sefSiteTitle'] = $result[0]['doc_metatitle']; 
		$meta['sefSiteDesc'] = $result[0]['doc_metadesc'];
		$meta['sefSiteKeywords'] = $result[0]['doc_metakeywords'];
	} else {
		$meta['siteTitle'] = $config['siteTitle']." - ".$result[0]['doc_name'];
		$robz = trim(strip_tags(str_replace("\r\n", "", $result[0]['doc_content'])));
			$count = 0;
			$output = "";
			
			for($i=0; $i<strlen($robz); $i++){
				$temp = substr($robz, $i, 1);
				
				if($temp == " "){
					if($count == 0){
						$output .= $temp;
						$count++;
					}
				}else{
					$output .= $temp;
					$count = 0;
				}
			}
			
		$meta['metaDescription'] = substr($output, 0, 156);
	}
	/* <rf> end mod */	
	$view_news->parse("view_news.one");
} else {
	
	$view_news->assign("DOC_NAME",$lang['front']['viewDoc']['error']);
	$view_news->assign("DOC_CONTENT",$lang['front']['viewDoc']['does_not_exist']);
	$view_news->parse("view_news.one");
}

$view_news->parse("view_news");
$page_content = $view_news->text("view_news");
?>