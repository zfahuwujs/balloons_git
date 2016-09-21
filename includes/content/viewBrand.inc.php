<?php

phpExtension();
if(isset($config['prodSortOrder'])) { $prodSortOrder = $config['prodSortOrder']; }
if(isset($_GET['sortOrder'])) { $prodSortOrder = treatGet($_GET['sortOrder']); }
if($_GET['sortOrder'] == "default") { $prodSortOrder = $config['prodSortOrder']; }
if(isset($prodSortOrder)) { $prodSortMethod = " ORDER BY ".$glob['dbprefix']."CubeCart_inventory.".$prodSortOrder; }

if(isset($_GET['page'])){
	
	$page = treatGet($_GET['page']);

} else {
	
	$page = 0;

}

$view_brand = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/viewBrand.tpl");

$sortOptions = "<option value=\"default\">Default</option>";
for ($i=0; $i<count($sortType); $i++) 
{
	$sortOptions .= "<option value=\"".$sortType[$i]['method']."\"";
	if($prodSortOrder == $sortType[$i]['method'])
	{
		$sortOptions .= " selected";
	}
	$sortOptions .= ">".$sortType[$i]['name']."</option>";
}

if ( isset ( $_GET['brandId'] ) && $_GET['brandId'] > 0 ) {

	$query = "SELECT * FROM " .$glob['dbprefix']."CubeCart_inventory, " . $glob['dbprefix'] . "CubeCart_brands WHERE showProd = 1 AND productBrand = " . $db->mySQLSafe ( $_GET['brandId'] ) . " AND " . $glob['dbprefix'] . "CubeCart_inventory.productBrand = " .$glob['dbprefix'] . "CubeCart_brands.id".$prodSortMethod;
	$results = $db->select ( $query );
	$totalNoProducts = $db->numrows( $query );

	if ( $results == TRUE )
	{

		for ( $i = 0; $i < count( $results ); $i++ )
		{
		
			$view_brand->assign("CLASS",cellColor($i, $tdEven="tdEven", $tdOdd="tdOdd"));
			if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$results[$i]['image'])){
				$view_brand->assign("SRC_PROD_THUMB",$GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$results[$i]['image']);
			} else {
				$view_brand->assign("SRC_PROD_THUMB",$GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif");
			}
			$view_brand->assign("TXT_TITLE",validHTML($results[$i]['name']));		
			$view_brand->assign("TXT_DESC",substr(strip_tags($results[$i]['description']),0,$config['productPrecis'])."&hellip;");
			if(salePrice($results[$i]['price'], $results[$i]['sale_price'])==FALSE){
				$view_brand->assign("TXT_PRICE",priceFormat($results[$i]['price']));
			} else {
				$view_brand->assign("TXT_PRICE","<span class='txtOldPrice'>".priceFormat($results[$i]['price'])."</span>");
			}
			$salePrice = salePrice($results[$i]['price'], $results[$i]['sale_price']);
			$view_brand->assign("TXT_SALE_PRICE", priceFormat($salePrice));
			if(isset($_GET['add']) && isset($_GET['quan'])){
				$view_brand->assign("CURRENT_URL",str_replace(array("&amp;add=".$_GET['add'],"&amp;quan=".$_GET['quan']),"",currentPage()));
			} else {
				$view_brand->assign("CURRENT_URL",currentPage());
			}
			if($config['outofstockPurchase']==1){
				
				$view_brand->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
				$view_brand->assign("PRODUCT_ID",$results[$i]['productId']);
				$view_brand->parse("view_brand.productTable.products.buy_btn");
			
			} elseif($results[$i]['useStockLevel']==1 && $results[$i]['stock_level']>0){
				
				$view_brand->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
				$view_brand->assign("PRODUCT_ID",$results[$i]['productId']);
				$view_brand->parse("view_brand.productTable.products.buy_btn");
			
			} elseif($results[$i]['useStockLevel']==0){
			
				$view_brand->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
				$view_brand->assign("PRODUCT_ID",$results[$i]['productId']);
				$view_brand->parse("view_brand.productTable.products.buy_btn");
			
			}
	
			$view_brand->assign("BTN_MORE",$lang['front']['viewCat']['more']);
			$view_brand->assign("PRODUCT_ID",$results[$i]['productId']);
	
			if($results[$i]['stock_level']<1 && $results[$i]['useStockLevel']==1 && $results[$i]['digital']==0)
			{
				$view_brand->assign("TXT_OUTOFSTOCK",$lang['front']['viewCat']['out_of_stock']);
			} else {
				$view_brand->assign("TXT_OUTOFSTOCK","In Stock");
			}
			$view_brand->parse("view_brand.productTable.products");
		}
		$view_brand->parse("view_brand.productTable");
	
	} elseif ( isset ( $_GET['searchStr'] ) ) {
		$view_brand->assign("TXT_NO_PRODUCTS",$lang['front']['viewCat']['no_products_match']." ".treatGet($_GET['searchStr']));
		$view_brand->parse("view_brand.noProducts");
	} else {
		$view_brand->assign("TXT_NO_PRODUCTS",$lang['front']['viewCat']['no_prods_in_cat']);
		$view_brand->parse("view_brand.noProducts");
	}
	
}


$view_brand->assign("LANG_CURRENT_DIR",$lang['front']['viewCat']['products_in']);
$view_brand->assign("CURRENT_DIR",validHTML($results[0]['brandName']));
$view_brand->assign("SORT_BY_TEXT",$lang['admin']['categories']['prodsort_sordorder']);
$view_brand->assign("SORT_BY_OPTIONS",$sortOptions);
$view_brand->assign("LINK_BRANDID",$_GET['brandId']);
$view_brand->parse("view_brand.prod_sort");

$view_brand->assign("TXT_CAT_TITLE", validHTML($results[0]['brandName']));

$view_brand->assign("PAGINATION",$db->paginate($totalNoProducts, $config['productPages'], $page, "page"));

$view_brand->parse("view_brand");
$page_content = $view_brand->text("view_brand");
?>