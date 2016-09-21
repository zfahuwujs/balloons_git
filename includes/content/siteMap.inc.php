<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.6
|   ========================================
|   by Alistair Brookbanks
|   CubeCart is a Trade Mark of Devellion Limited
|   (c) 2005 Devellion Limited
|   Devellion Limited,
|   Westfield Lodge,
|   Westland Green,
|   Little Hadham,
|   Nr Ware, HERTS.
|   SG11 2AL
|   UNITED KINGDOM
|   http://www.devellion.com
|   UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Tuesday, 13th December 2005
|   Email: info (at) cubecart (dot) com
|   License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|   siteMap.inc.php
|   Version 1.1
|   ========================================
|   Displays a HTML site map of all categories	
+--------------------------------------------------------------------------
*/

// Admin Options
// Set these to 1 or 0 to enable/disable each option
// $show_counts will display each category's product count next to it
// $hide_zeros will simply not show the count if there aren't any products
// $show_products will display each category's products
// $show_products will show each product's price IF $show_products is also set
$show_counts = 1;
$hide_zeros = 1;
$show_products = 0;
$show_prices = 0;

if(!isset($config)){
	echo "<html>\r\n<head>\r\n<title>Forbidden 403</title>\r\n</head>\r\n<body><h3>Forbidden 403</h3>\r\nThe document you are requesting is forbidden.\r\n</body>\r\n</html>";
	exit;
}

$view_map=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/siteMap.tpl");

// call the first iteration of drillDown() with an argument of "0" -- this will get all root categories
drillDown(0);

$view_map->assign("CAT_LIST",$catMap);
$view_map->parse("view_map.cat_list");

// pull in and display all the site docs
$results = $db->select("SELECT doc_id, doc_name FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_pos != 0 ORDER BY doc_name ASC");
if($results == TRUE) {
	for ($i=0; $i<count($results); $i++){
		// uncomment the following line for use with 3.0.5 or higher
		// $results[$i]['doc_name'] = validHTML($results[$i]['doc_name']);
			if($results[$i]['doc_id']!=5){
				$view_map->assign("DATA",$results[$i]);
				$view_map->parse("view_map.doc_list");
			}
		} // end for loop
	} // end if
	
// put it all together and send to index.php
$view_map->parse("view_map");
$page_content = $view_map->text("view_map");

// that's it!  We're done here.  Nothing left but the recursive function

function drillDown($thisCat) {
	global $db, $glob, $show_counts, $hide_zeros, $show_products, $catMap;

	$query = "SELECT cat_name, cat_id, cat_father_id, noProducts FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_father_id = '".$thisCat."' ORDER BY cat_name ASC";
	$results = $db->select($query);
	$numrows = $db->numrows($query);

	if($numrows > 0){
		$catMap .= "<ul class=\"sitemap\">\n";
		for ($i=0; $i<count($results); $i++){ 
			// determine whether we're showing product count and if so, what it should be.
			if($show_counts==1){
			  if($hide_zeros==1 && $results[$i]['noProducts']==0){
					unset($count);
					} else {
					$count = "- (".$results[$i]['noProducts'].")";
					} // end if $hide_zeros
				} // end if $show_counts

			// add the current category to the list
			$catMap .= "<li class=\"sitemap\"><a href=\"index.php?act=viewCat&amp;catId=".$results[$i]['cat_id']."\" class=\"txtDefault\">".$results[$i]['cat_name']."</a> ".$count."</li>\n";
			// this version is for 3.0.5 and up
			// $catMap .= "<li class=\"sitemap\"><a href=\"index.php?act=viewCat&amp;catId=".$results[$i]['cat_id']."\" class=\"txtDefault\">".validHTML($results[$i]['cat_name'])."</a> ".$count."</li>\n";
			
			if($show_products==1 && $results[$i]['noProducts'] > 0){
				displayProducts($results[$i]['cat_id']);
			}

			// now display any sub-categories of this category .... rinse and repeat
			drillDown($results[$i]['cat_id']);

			} // end for loop
		$catMap .= "</ul>\n";
		} // end if
	return;
} // end function

function displayProducts($thisCat) {
	global $db, $glob, $config, $show_prices, $catMap;

	// this will use the sort order specified in my Category Sort mod if installed
	if(isset($config['prodSortOrder'])) { $prodSortMethod = " ORDER BY ".$glob['dbprefix']."CubeCart_inventory.".$config['prodSortOrder']; }

	$productListQuery = "SELECT ".$glob['dbprefix']."CubeCart_cats_idx.cat_id, ".$glob['dbprefix']."CubeCart_cats_idx.productId, productCode, price, name, sale_price FROM ".$glob['dbprefix']."CubeCart_cats_idx INNER JOIN ".$glob['dbprefix']."CubeCart_inventory ON ".$glob['dbprefix']."CubeCart_cats_idx.productId = ".$glob['dbprefix']."CubeCart_inventory.productId WHERE ".$glob['dbprefix']."CubeCart_cats_idx.cat_id = ".$db->mySQLSafe($thisCat).$prodSortMethod;
	$results = $db->select($productListQuery);
	$numrows = $db->numrows($productListQuery);

	if($numrows > 0){
		$catMap .= "<ul class=\"sitemap\">\n";
		for ($i=0; $i<count($results); $i++){ 
		
			$catMap .= "<li class=\"sitemap\"><i><a href=\"index.php?act=viewProd&amp;productId=".$results[$i]['productId']."\" class=\"txtDefault\">".$results[$i]['name']."</a> ";
	
			if($show_prices==1){
				if(salePrice($results[$i]['price'], $results[$i]['sale_price'])==FALSE){
					if($results[$i]['price'] == 0) {
						$catMap .= " -- FREE!";
					} else {
						$catMap .= " -- ".priceFormat($results[$i]['price']);
					}
				} else {
					$catMap .= " -- <span class='txtOldPrice'>".priceFormat($results[$i]['price'])."</span> ";
				}
				$salePrice = salePrice($results[$i]['price'], $results[$i]['sale_price']);
				
				$catMap .= "<span class='txtSale'>".priceFormat($salePrice)."</span></i>";
			} //end if $show_prices
			$catMap .= "</li>\n";
		} // end for loop
		$catMap .= "</ul>\n";
	} // end if
} // end function
?>