<?

$newlang['admin']['categories'] = array (

'category_disp_order' => "Category Display Order and Product Display Options",

'cdo_desc' => "Below is the current category display order.",

'cdo_col_title' => "Display Order",

'cdo_master' => "Main Store",

'cdo_moveup' => "Up",

'cdo_movedown' => "Down",

'cdo_noup' => "You cannot move the first item up.",

'cdo_nodown' => "You cannot move the last item down.",

'cdo_moved_up' => " has been moved up to replace ",

'cdo_moved_down' => " has been moved down to replace ",

'prodsort_boxtitle' => "Select Default Product Sort Order",

'prodsort_sordorder' => "Sort Order: ",

'prodsort_boxsubmit' => "Update Sort Preference",

'prodsort_nameasc' => "Name Ascending",

'prodsort_namedesc' => "Name Descending",

'prodsort_priceasc' => "Price Lowest",

'prodsort_pricedesc' => "Price Highest",

'prodsort_itemasc' => "DB ID Ascending (Oldest)",

'prodsort_itemdesc' => "DB ID Descending (Newest)",

'prodsort_prodcodeasc' => " Product Code Ascending",

'prodsort_prodcodedesc' => "Product Code Descending",

'prodsort_stocklevelasc' => "Stock Level Ascending",

'prodsort_stockleveldesc' => "Stock Level Descending",

'prodsort_popularasc' => "Popularity Ascending",

'prodsort_populardesc' => "Popularity Descending",

'prodsort_random' => "Random Order - By Session",

'prodsort_full_random' => "Random Order - Full Random",

);

$lang['admin']['categories'] = array_merge($lang['admin']['categories'], $newlang['admin']['categories']);

$newlang['admin']['nav'] = array(

'reorder_categories' => "Reorder Categories",

);

$lang['admin']['nav'] = array_merge($lang['admin']['nav'], $newlang['admin']['nav']);

// The remaining lines will define what types of sorts your customers can do.
// Simply comment out any method(s) you don't want them to have available.
// You can of course rename them as you see fit by changing the 'name' assignments.
$sortType = array();
$sortType[] = array('method' => 'name ASC', 'name' => 'Name Ascending');
$sortType[] = array('method' => 'name DESC', 'name' => 'Name Descending');
$sortType[] = array('method' => 'price ASC', 'name' => 'Price Lowest');
$sortType[] = array('method' => 'price DESC', 'name' => 'Price Highest');
//$sortType[] = array('method' => 'productCode ASC', 'name' => 'Product Code Ascending');
//$sortType[] = array('method' => 'productCode DESC', 'name' => 'Product Code Descending');
//$sortType[] = array('method' => 'popularity ASC', 'name' => 'Popularity Ascending');
//$sortType[] = array('method' => 'popularity DESC', 'name' => 'Popularity Descending');
$sortType[] = array('method' => 'productId ASC', 'name' => 'Oldest');
$sortType[] = array('method' => 'productId DESC', 'name' => 'Newest');
$sortType[] = array('method' => 'digital ASC', 'name' => 'Random');

?>
