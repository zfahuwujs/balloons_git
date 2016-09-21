<?php

$urlLimit = 50000;
$currentUrl = 1;
$sitemapIndex = array();

$exportData = '<?xml version="1.0" encoding="UTF-8"?>';
$exportData .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

if(file_exists($web."language/en/home.inc.php")){
	$modifyTime = filemtime($web."language/en/home.inc.php");
	
	$exportData .= '<url><loc>'.substr($website, 0, -1).'</loc><priority>1.0</priority><lastmod>'.date("Y-m-d", $modifyTime).'</lastmod></url>';
}else{
	$exportData .= '<url><loc>'.substr($website, 0, -1).'</loc><priority>1.0</priority><lastmod>'.date("Y-m-d", time()).'</lastmod></url>';
}


if(!isset($config)){
	echo "<html>\r\n<head>\r\n<title>Forbidden 403</title>\r\n</head>\r\n<body><h3>Forbidden 403</h3>\r\nThe document you are requesting is forbidden.\r\n</body>\r\n</html>";
	exit;
}

drillDown(0);

$results = $db->select("SELECT doc_id, doc_name, lastModified FROM ".$glob['dbprefix']."CubeCart_docs ORDER BY doc_name ASC");
if($results == TRUE) {
	for ($i=0; $i<count($results); $i++){
		if($currentUrl == $urlLimit){
			updateSitemap();
		}else{
			$currentUrl++;
		}
		
		$exportData .= "<url><loc>".$website.generateDocumentUrl($results[$i]['doc_id'])."</loc><lastmod>".date('Y-m-d', $results[$i]['lastModified'])."</lastmod><priority>0.7</priority></url>";
	} 
}

updateSitemap();

$filename = $web."sitemap.xml";

$exportData = '<?xml version="1.0" encoding="UTF-8"?>';
$exportData .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

for($i=0; $i<count($sitemapIndex); $i++){
	$exportData .= '<sitemap>';
	$exportData .= '<loc>'.$website.'sitemap-'.($i+1).'.xml</loc>';
	$exportData .= '<lastmod>'.date("Y-m-d", time()).'</lastmod>';
	$exportData .= '</sitemap>';
}

$exportData .= '</sitemapindex>';

$content = $exportData;
$contentLength = strlen($exportData);
	
if(file_exists($filename)){
	$fileHandle = fopen($filename, 'w') or die("");
}else{
	$fileHandle = fopen($filename, 'w+') or die("");
}
		
fwrite($fileHandle, $content);
fclose($fileHandle);

for($i=0; $i<count($sitemapIndex); $i++){
	$filename = $web."sitemap-".($i+1).".xml";
	$content = $sitemapIndex[$i];
	$contentLength = strlen($content);
		
	if(file_exists($filename)){
		$fileHandle = fopen($filename, 'w') or die("");
	}else{
		$fileHandle = fopen($filename, 'w+') or die("");
	}
			
	fwrite($fileHandle, $content);
	fclose($fileHandle);
}

exit;


function updateSitemap(){
	global $exportData, $sitemapIndex, $currentUrl, $urlLimit;
	
	$exportData .= "</urlset>";
	
	array_push($sitemapIndex, $exportData);
	
	$exportData = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	
	$currentUrl = 1;
	
	return;
}

function drillDown($thisCat) {
	global $db, $glob, $show_counts, $hide_zeros, $show_products, $exportData, $website, $currentUrl, $urlLimit;

	$query = "SELECT cat_name, cat_id, cat_father_id, noProducts, lastModified FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = '".$thisCat."' ORDER BY cat_name ASC";
	$results = $db->select($query);
	$numrows = $db->numrows($query);

	if($numrows > 0){
		for ($i=0; $i<count($results); $i++){ 
			if($currentUrl == $urlLimit){
				updateSitemap();
			}else{
				$currentUrl++;
			}
		
			$exportData .= "<url><loc>".$website.generateCategoryUrl($results[$i]['cat_id'])."</loc><lastmod>".date('Y-m-d', $results[$i]['lastModified'])."</lastmod><priority>0.9</priority></url>";	
			
			if($results[$i]['noProducts'] > 0){
				displayProducts($results[$i]['cat_id']);
			}

			drillDown($results[$i]['cat_id']);
		}
	}
	return;
}

function displayProducts($thisCat) {
	global $db, $glob, $config, $show_prices, $exportData, $website, $currentUrl, $urlLimit;

	if(isset($config['prodSortOrder'])) { $prodSortMethod = " ORDER BY ".$glob['dbprefix']."CubeCart_inventory.".$config['prodSortOrder']; }

	$productListQuery = "
	SELECT ".$glob['dbprefix']."CubeCart_cats_idx.cat_id, ".$glob['dbprefix']."CubeCart_cats_idx.productId, productCode, price, name, sale_price, lastModified
	FROM ".$glob['dbprefix']."CubeCart_cats_idx 
	INNER JOIN ".$glob['dbprefix']."CubeCart_inventory 
	ON ".$glob['dbprefix']."CubeCart_cats_idx.productId = ".$glob['dbprefix']."CubeCart_inventory.productId 
	WHERE ".$glob['dbprefix']."CubeCart_cats_idx.cat_id = ".$db->mySQLSafe($thisCat).$prodSortMethod;
	$results = $db->select($productListQuery);
	$numrows = $db->numrows($productListQuery);

	if($numrows > 0){
		for ($i=0; $i<count($results); $i++){
			if($currentUrl == $urlLimit){
				updateSitemap();
			}else{
				$currentUrl++;
			}
		 
			$exportData .= "<url><loc>".$website.generateProductUrl($results[$i]['productId'])."</loc><lastmod>".date('Y-m-d', $results[$i]['lastModified'])."</lastmod><priority>0.8</priority></url>";
		} 
	}
} 
?>