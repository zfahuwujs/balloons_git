<?php 

phpExtension();

$box_content = new XTemplate("skins/".$config['skinDir']."/styleTemplates/boxes/searchForm.tpl");
$box_content->assign("LANG_SEARCH_FOR", $lang['front']['boxes']['search_for']);

// categories
$searchCategories = $db->select("SELECT cat_id, cat_name, cat_father_id, noProducts
	FROM ".$glob['dbprefix']."CubeCart_category ORDER BY cat_name ASC");

if ($searchCategories) {
	for ($i = 0; $i < count($searchCategories); $i++) {
		
		// Count the number of products listed under this category
		$queryCount = "SELECT COUNT(productId) FROM ".$glob['dbprefix']."CubeCart_inventory
			WHERE cat_id = ".$db->mySQLSafe($searchCategories[$i]['cat_id']);
		$resultCount = $db->select($queryCount);
		$resultCount = $resultCount[0][0];
		
		// is this a master category?
		if ($resultCount > 0) {
			$box_content->assign("CATEGORY_ID", $searchCategories[$i]['cat_id']);
			$box_content->assign("CATEGORY_NAME", $searchCategories[$i]['cat_name']);
			if (isset($_GET['searchCategory']) && $_GET['searchCategory'] == $searchCategories[$i]['cat_id']) {
				$box_content->assign("CATEGORY_SELECTED", 'selected="selected"');
			} else {
				$box_content->assign("CATEGORY_SELECTED", '');
			}
			$box_content->parse("search_form.categories");
		}
	}
}

if (isset($_GET['searchStr'])) {
	$box_content->assign("SEARCHSTR", treatGet($_GET['searchStr']));
} else {
	$box_content->assign("SEARCHSTR", "");
}

$box_content->assign("LANG_GO", $lang['front']['boxes']['go']);

if (evoHideBol(89) == false) {
	// data for autocomplete
	$products = $db->select("SELECT name, prod_metakeywords FROM ".$glob['dbprefix']."CubeCart_inventory ORDER BY name ASC");
	//output data
	if ($products == true) {
		for ($i = 0; $i < count($products); $i++) {
			$bank = array();
			// run through product names
			if ($products[$i]['name'] != '') {
				if (!in_array($products[$i]['name'], $bank)) {
					$bank[]=$products[$i]['name'];
				}
			}
			// run through product keywords
			if ($products[$i]['prod_metakeywords'] != '') {
				// keywords with comma and space
				$keywords = explode(",", $products[$i]['prod_metakeywords']);
				for ($j = 0; $j < count($keywords); $j++) {
					$keywords[$j] = trim($keywords[$j]);
					if (!in_array($keywords[$j], $bank)) {
						echo $keywords[$j];
						$bank[] = $keywords[$j];
					}
				}
			}
			// send word bank to tpl
			for ($k = 0; $k < count($bank); $k++) {
				$box_content->assign("AUTO_COMPLETE", $bank[$k]);
				$box_content->parse("search_form.autocomplete.search_data");	
			}
		}
	}
	$box_content->parse("search_form.autocomplete");
}
		
$box_content->parse("search_form");
$box_content = $box_content->text("search_form");
?>